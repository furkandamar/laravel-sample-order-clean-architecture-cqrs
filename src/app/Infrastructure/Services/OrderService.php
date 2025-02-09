<?php

namespace App\Infrastructure\Services;

use App\Exceptions\ApiException;
use App\Infrastructure\Abstraction\Service\IOrderService;
use App\Models\CustomerModel;
use App\Models\DiscountModel;
use App\Models\OrderDiscountModel;
use App\Models\OrderModel;
use App\Models\OrderPackageModel;
use App\Models\ProductModel;
use App\Models\Response\OrderDiscountResponse;
use App\Models\Response\OrderProductItemResponse;
use App\Models\Response\OrderPackageResponse;
use DiscountTypeConstant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService implements IOrderService
{

    private function getCustomer($customerId)
    {
        $customer = CustomerModel::find($customerId);
        if (!$customer) {
            throw new ApiException("Müşteri bulunamadı");
        }
        return $customer;
    }

    public function createOrder($customerId, $payload)
    {
        try {
            DB::beginTransaction();

            $customer = $this->getCustomer($customerId);

            $productIds = collect($payload)->pluck('product_id');
            $products = ProductModel::find($productIds)->keyBy('id');

            if ($products->count() == 0) {
                throw new ApiException("Ürün bulunamadı");
            }

            $quantities = collect($payload)
                ->groupBy('product_id')
                ->map(fn($group) => $group->sum('amount'));

            $packageTotal = 0.0;

            $orderPackage = OrderPackageModel::create([
                'total' => $packageTotal,
                'customer_id' => $customerId,
            ]);

            $orderDto = $this->fillOrderAndUpdateStock($quantities, $products, $orderPackage);

            $totalDiscount = $this->checkAndApproveDiscount($orderDto);

            $packageTotal = collect($orderDto)->sum('total') - $totalDiscount;

            if ($customer->balance < $packageTotal) {
                throw new ApiException("Bakiye yetersiz");
            }

            $orderPackage->discount = $totalDiscount;
            $orderPackage->total = $packageTotal;
            $orderPackage->save();

            $customer->balance -= $packageTotal;
            $customer->save();

            $this->clearCache();

            DB::commit();

            return new OrderPackageResponse(
                $orderPackage->id,
                count($orderDto),
                $totalDiscount,
                $orderPackage->total,
                $orderPackage->created_at->timestamp
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException($e->getMessage());
        }
    }

    public function updateOrderPackage($customerId, $orderPackageId, $payload)
    {
        try {
            DB::beginTransaction();

            $customer = $this->getCustomer($customerId);

            $orderPackage = OrderPackageModel::where('customer_id', $customerId)
                ->where('id', $orderPackageId)
                ->where('is_deleted', false)
                ->first();

            if (!$orderPackage) {
                throw new ApiException("Sipariş paketi bulunamadı");
            }


            //Önce siparişi iadet et ve stokları geri al
            $orders = OrderModel::where('order_package_id', $orderPackageId)->get();

            foreach ($orders as $order) {
                $product = ProductModel::find($order->product_id);
                $product->stock += $order->quantity;
                $product->save();

                OrderDiscountModel::where('order_id', $order->id)->delete();
                $order->delete();
            }

            $customer->balance += $orderPackage->total;

            //Sipariş içeriğini yeniden oluştur
            $productIds = collect($payload)->pluck('product_id');
            $products = ProductModel::find($productIds)->keyBy('id');

            if ($products->count() == 0) {
                throw new ApiException("Ürün bulunamadı");
            }

            $quantities = collect($payload)
                ->groupBy('product_id')
                ->map(fn($group) => $group->sum('amount'));

            $packageTotal = 0.0;

            $orderDto = $this->fillOrderAndUpdateStock($quantities, $products, $orderPackage);

            $totalDiscount = $this->checkAndApproveDiscount($orderDto);

            $packageTotal = collect($orderDto)->sum('total') - $totalDiscount;

            if ($customer->balance < $packageTotal) {
                throw new ApiException("Bakiye yetersiz");
            }

            $orderPackage->discount = $totalDiscount;
            $orderPackage->total = $packageTotal;
            $orderPackage->save();

            $customer->balance -= $packageTotal;
            $customer->save();

            $this->clearCache();

            DB::commit();

            return new OrderPackageResponse(
                $orderPackage->id,
                count($orderDto),
                $totalDiscount,
                $orderPackage->total,
                $orderPackage->created_at->timestamp
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException($e->getMessage());
        }
    }

    protected function clearCache()
    {
        Cache::forget("order_package:*");
        Cache::forget("products_all:*");
        Cache::forget("products_all:*");
    }

    protected function fillOrderAndUpdateStock($quantities, $products, $orderPackage)
    {
        $orderDto = [];

        foreach ($quantities as $productId => $quantity) {
            $product = $products[$productId];

            if ($quantity > $product->stock) {
                throw new ApiException("Stok yetersiz");
            }

            $orderTotal = $quantity * $product->price;

            $order = OrderModel::create([
                'order_package_id' => $orderPackage->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'total' => $orderTotal
            ]);

            ProductModel::where('id', $productId)->update([
                'stock' => $product->stock - $quantity
            ]);

            $orderDto[] = new OrderProductItemResponse(
                $order->id,
                $productId,
                $order->product->name,
                $quantity,
                $product->price,
                $orderTotal,
            );
        }

        return $orderDto;
    }

    protected function checkAndApproveDiscount($orders)
    {
        for ($k = 0; $k < count($orders); $k++) {
            $productDiscount = DiscountModel::where('product_id', $orders[$k]->productId)
                ->where('min_limit_quantity', '<=', $orders[$k]->quantity)
                ->orderBy('min_limit_quantity', 'desc')
                ->first();

            //Başta ürün bazında indirim koşulu kontrol ediliyor
            if ($productDiscount) {
                return $this->scanDiscount(
                    $productDiscount,
                    $orders[$k],
                    $orders
                );
                //İndirim uygulanmış sipariş verisi güncelleniyor
                $orders[$k] = $updatedOrder;
            } else {
                //Ürün bazında indirim yoksa kategori bazında indirim kontrol ediliyor
                $product = ProductModel::find($orders[$k]->productId);

                $sameCategoryProducts = ProductModel::where('category_id', $product->category_id)->pluck('id')->toArray();

                $productsSameCategoryQuantity = collect($orders)
                    ->whereIn('productId', $sameCategoryProducts, true)
                    ->sum('quantity');


                $categoryDiscount = DiscountModel::where('category_id', $product->category_id)
                    ->where('min_limit_quantity', '<=', $productsSameCategoryQuantity)
                    ->orderBy('min_limit_quantity', 'desc')
                    ->first();

                if ($categoryDiscount) {
                    return $this->scanDiscount(
                        $categoryDiscount,
                        $orders[$k],
                        $orders
                    );
                    //İndirim uygulanmış sipariş verisi güncelleniyor
                    $orders[$k] = $updatedOrder;
                } else {
                    //Kategori bazında indirim yoksa genel indirim kontrol ediliyor
                    $generalDiscount = DiscountModel::where('category_id', null)
                        ->where('product_id', null)
                        ->where('min_limit_quantity', '<=', collect($orders)->sum('total'))
                        ->orderBy('min_limit_quantity', 'desc')
                        ->first();

                    if ($generalDiscount) {
                        return $this->scanDiscount(
                            $generalDiscount,
                            $orders[$k],
                            $orders
                        );
                        //İndirim uygulanmış sipariş verisi güncelleniyor
                        $orders[$k] = $updatedOrder;
                    }
                }
            }
        }
        return 0.00; //İndirim dahil edilmediyse defaul değer döner
    }

    protected function scanDiscount($discountModel, $order, $allOrder)
    {
        $totalDiscount = 0.0;
        $orderTotal = $order->total;

        if ($discountModel->discount_type == DiscountTypeConstant::TOTAL_AMOUNT_GREATER_PERCENTAGE_DISCOUNT) {
            //Toplam adet belirlenen miktar ve üzeri ise, uygulanacak toplam tutarda yüzdelik indirimi
            $amount = $order->unitPrice * $order->quantity;
            $totalDiscount = $amount * $discountModel->discount_value / 100.0;
        } else if ($discountModel->discount_type == DiscountTypeConstant::MANY_GET_ONE_FREE) {
            //Belirlenen miktardan fazla alımda 1 bedava verir
            $getAvailableOrder = collect($allOrder)
                ->where('quantity', '>=', $discountModel->min_limit_quantity)
                ->first();
            if ($getAvailableOrder) {
                $totalDiscount = $getAvailableOrder->unitPrice;
            }
        } else if ($discountModel->discount_type == DiscountTypeConstant::TOTAL_AMOUNT_GREATER_PERCENTAGE_CHEAPEST_PRODUCT) {
            //En ucuz ürüne yüzdelik indirim yapılıyor
            $cheapestProduct = collect($allOrder)->sortBy('unitPrice')->first();
            $totalDiscount = $cheapestProduct->unitPrice * $discountModel->discount_value / 100.0;
        } else if ($discountModel->discount_type == DiscountTypeConstant::TOTAL_QUANTITY_GREATER_PERCENTAGE_DISCOUNT) {
            //Toplam tutar belirlenen tutar ve üzeri ise, uygulanacak toplam tutarda yüzdelik indirimi
            $totalAmount = collect($allOrder)->sum('total');
            $totalDiscount = $totalAmount * $discountModel->discount_value / 100.0;
        } else {
            return $totalDiscount;
        }

        OrderDiscountModel::create([
            'order_id' => $order->orderId,
            'discount_id' => $discountModel->id,
            'discount_amount' => $totalDiscount,
            'sub_total' => $orderTotal
        ]);

        return $totalDiscount;
    }

    public function getOrders($customerId, $orderPackageId = null)
    {
        $cacheKey = sprintf(
            "order_package:%s",
            $orderPackageId ?? 'all'
        );

        if ($orderPackageId) {
            $orderPackage = OrderPackageModel::where('customer_id', $customerId)
                ->where('id', $orderPackageId)
                ->where('is_deleted', false)
                ->first();

            if (!$orderPackage) {
                throw new ApiException("Sipariş paketi bulunamadı");
            }
        }

        return Cache::remember($cacheKey, 60, function () use ($orderPackageId, $customerId) {
            if ($orderPackageId) {
                $entity =  OrderModel::where('order_package_id', $orderPackageId)->get();
                return $entity->map(fn($item) => new OrderProductItemResponse(
                    $item->order_package_id,
                    $item->product_id,
                    $item->product->name,
                    $item->quantity,
                    $item->unit_price,
                    $item->total
                ));
            }
            $entity = OrderPackageModel::where('customer_id', $customerId)->where('is_deleted', false)->get();
            return $entity->map(fn($item) => new OrderPackageResponse(
                $item->id,
                $item->productCount(),
                $item->discount,
                $item->total,
                $item->created_at->timestamp,
            ));
        });
    }

    public function cancelOrderPackage($customerId, $orderPackageId)
    {
        try {
            DB::beginTransaction();

            $orderPackage = OrderPackageModel::where('customer_id', $customerId)
                ->where('id', $orderPackageId)
                ->where('is_deleted', false)
                ->first();

            if (!$orderPackage) {
                throw new ApiException("Sipariş paketi bulunamadı");
            }

            $orders = OrderModel::where('order_package_id', $orderPackageId)->get();

            foreach ($orders as $order) {
                $product = ProductModel::find($order->product_id);
                $product->stock += $order->quantity;
                $product->save();

                OrderDiscountModel::where('order_id', $order->id)->delete();
                $order->delete();
            }

            $orderPackage->is_deleted = true;
            $orderPackage->save();

            CustomerModel::where('id', $customerId)->increment('balance', $orderPackage->total);

            $this->clearCache();

            DB::commit();
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }


    public function getDiscount($customerId, $orderPackageId)
    {
        $cacheKey = sprintf(
            "order_discount:%s",
            $orderPackageId
        );
        return Cache::remember($cacheKey, 60, function () use ($orderPackageId) {
            $orders = OrderModel::where('order_package_id', $orderPackageId)->pluck('id')->toArray();
            $entity = OrderDiscountModel::where('order_id',  $orders)->get();
            return $entity->map(fn($item) => new OrderDiscountResponse(
                $item->discount->discount_type,
                $item->order_id,
                $item->order->product_id,
                $item->order->product->name,
                $item->discount_amount,
                $item->sub_total,
                $item->created_at->timestamp
            ));
        });
    }
}
