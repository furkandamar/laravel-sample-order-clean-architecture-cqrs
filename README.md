## Projeye Genel Bakış

Laravel projesinde; Ürün & kategorilerin listelenmesi, müşteri auth ve hesabım, kampanya veya indirim çeşitlerine göre ürün tutarlarında değişiklik, sipariş oluşturma gibi çeşitli servislerin REST mimarisine uygun biçimde, REST-API hizmeti olarak kullanılmasını sağlamak. Buradaki amaç; Laravel projelerinde CQRS, RabbitMQ, Event Sourcing, Redis gibi teknolohilerin nasıl kullanılabileceğine dair örneklenddirme.

- ✅ Clean Architecture
- ✅ CQRS & Singleton Pattern
- ✅ Event Sourcing
- ✅ Memory Cache (Redis)
- ✅ RabbitMQ
- ✅ Postgresql
- ✅ MongoDB

Laravel framework, hali hazırda MVC mimarisini kullanır. İhtiyaca göre MVC mimarisini geliştirip belkide dışına çıkıp farklı mimariler entegre edilebilir. Projede kullanılabilecek pattern ise oluşum aşamasında karar verilmelidir. Bu projede Singleton modeli uygulandı. 

Örnek kullanım & test için repo içersinde "PostmanSample.json" dosyası üzerinden postman koleksiyonu indirebilir veya http://localhost:8182/api/documentation üzerinden Swagger-ui testi yapabilirsiniz.

#### Veritabanı Şeması
![Veritabanı Şeması](https://raw.githubusercontent.com/furkandamar/laravel-sample-order-clean-architecture-cqrs/refs/heads/main/db-schema.jpg)


### CQRS Nedir?

Açılımı : Command Query Responsibility Segregation yani Türkçe karşılığı Komut Sorgu Sorumluluk Ayrımı olan, veri işleme ve okuma işlemlerini birbirinden ayıran desendir. Veri okuma ve yazma işlemlerini farklı modeller üzerinden gerçekleştirir.

- Command : Veri yazma veya veri üzerinde değişiklik yapan işlemler komut (Command) kapsamına girer. Örn; sipariş verme.
- Query : Sorgulama veya veri çağırma gibi işlemler query kapsamındadır. Örn; ürün listeleme.

**[Daha fazla](https://medium.com/sahibinden-technology/cqrs-design-pattern-nedir-neden-kullan%C4%B1l%C4%B1r-bir-cqrs-design-pattern-i%CC%87ncelemesi-ced6713abc6e)**

### Neden CQRS kullanılmalı?
Okuma ve yazma işlemleri farklı modeller üzerinden gerçekleştirildiği için, işlemler farklı veritabanına veya sunuculara dağıtılabilir, bu sayede ölçeklenebilirlik artar.
Bu projede okuma işlemleri in-memory database üzerinden gerçekleşir.

### RabbitMQ Nedir?

Servisler arasında asenkron iletişim kurmayı sağlayan kuyruk sistemidir. Mesajları üreticiden (producer), tüketiciye (consumer) organize bir şekilde iletilmesini sağlar.

**[Daha fazla](https://medium.com/@tlhashin/rabbitmq-nedir-203cd8fe7318)**

### Neden RabbitMQ kullanılmalı?

Yüksek trafikli web servislerde veya mikroservis projelerinde, verilerin kaybolmaması için gerekli olan yaklaşımdır.

### Event Sourcing Nedir?

Bir uygulamanın durumunu yönetmek için kullanılan bir mimari desendir. Uygulama durumunun bir dizi olay (event) olarak kaydedilmesi ve bu olayların biriktirilerek sistemin mevcut durumunun yeniden oluşturulabilmesidir.

**[Daha fazla](https://medium.com/bili%C5%9Fim-hareketi/event-sourcing-8ab339c0797e/)**

### Event Sourcing ve CQRS İlişkisi

Event Sourcing ile CQRS genelde ile birlikte kullanılır. geriye dönük raporlama ve karmaşık iş kuralları gerektiren sistemlerde tercih edilir.

### Redis Nedir?

NoSQL veritabanları kategorisinde yer alan, anahtar-değer (key-value) modelini kullanan bir bellek tabanlı veri deposudur. Verileri bellek üzerinde sakladığı için son derece hızlıdır ve genellikle performans kritik senaryolarda tercih edilir. Aynı zamanda verileri diske kaydederek kalıcılık sağlayabilir veya dağıtık sistemlerde replikasyon ile erişilebilirlik sunar.

 **[Daha fazla](https://devnot.com/2020/redis-nedir-temel-kullanim-alanlari-nelerdir/)**


## Kurulum
 Repo'yu klonlayın veya indirin. Docker uygulamasını çalıştırın. Eğer docker üzerinden kurmayıp sadece kaynak kodlara erişmek isterseniz, src klasörü içersinden ulaşabilirsiniz.
 ```bash
    cd project_path/
```
 ```bash
    docker-compose up -d
```

```bash
    docker-compose exec app composer install 
    docker-compose exec app php artisan migrate
    docker-compose exec app php artisan db:seed
```


## Proje Akışı
![akis](https://raw.githubusercontent.com/furkandamar/laravel-sample-order-clean-architecture-cqrs/refs/heads/main/process.jpg)

Proje akışı temelde şemadaki gibi işler. Kullanıcı isteği gönderir, controller kabul eder, CQRS handler den geçip servis katmanına erişir, servisde gerekli işlemler yapıldıktan sonra çıktı olarak döner (Response).

Burada ara katmanlar bulunuyor, CQRS işleme alınırken Event oluşturulması, In-memory cache databse üzerinden veri dönmesi gibi. 

Örneğin. Kullanıcı oturum açma isteğinde; Kullanıcı /login endpointine istek gönderdiğinde, AuthController bu isteği yakalar : 

```php
    //routes/api.php
    Route::post("/login", [AuthController::class, "login"]);

    // app/Http/Controllers/AuthController
    public function login(Request $request)
    {
        return ApiResponse::success(
            $this->handlerBus->handle(new LoginCommand($request->email, $request->password))
        );
    }
```

Bu bir auth işlemi gerçeklştiriyor ve Command handler pattern üzerinden ilgili servise ulaştırılıyor : 

```php

class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}

class LoginCommandHandler
{
    public function __construct(
        private IAuthService $authService
    ) {}

    public function handle(LoginCommand $command)
    {
        return $this->authService->login($command->email, $command->password);
    }
}

```

Proje içersinde Command veya Query handler'lar dinamik olabileceğinden, bir HandlerBus sınıfı oluşturup, dönüşümleri bu sınıc üzerinden gerçekleştiriyoruz : 
```php
use Illuminate\Support\Facades\App;
use ReflectionClass;

class HandlerBus
{
    public function handle($command)
    {
        $reflection = new ReflectionClass($command);
        $handlerName = $reflection->getShortName() . 'Handler';
        $handlerName = str_replace($reflection->getShortName(), $handlerName, $reflection->getName());
        $handler = App::make($handlerName);
        return $handler->handle($command);
    }
}
```


Dependency Injection tanımlamaları : 

```php
// app/Providers/AppServiceProvider.php

public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IProductService::class, ProductService::class);
        $this->app->bind(IOrderService::class, OrderService::class);
    }
```

## Rest-API Akışı
Kullanıcı giriş yapabilmesi için /login endpointine POST isteği yapar.

```bash
Payload
{
  "email": "turker@mail.com",
  "password": "abc123"
}
```

Başarılı bir istek ise : 
```bash
Response
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": "eyJ.......MU"
}
```

Kategoriler ve ürünleri çağırıken Bearer token göndermeye gerek yoktur.
(GET) /categories
```bash
Response 
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": [
        {
            "id": "9e2b8b76-5925-410e-b9a2-92038c0a49f8",
            "category_name": "Category 1",
            "created_at": "2025-02-09T10:56:45.000000Z",
            "updated_at": "2025-02-09T10:56:45.000000Z"
        },
        {
            "id": "9e2b8b76-5a13-47f4-8297-33d273210a2c",
            "category_name": "Category 2",
            "created_at": "2025-02-09T10:56:45.000000Z",
            "updated_at": "2025-02-09T10:56:45.000000Z"
        },
        {
            "id": "9e2b8b76-5aa0-4b5a-bb61-56667387d065",
            "category_name": "Category 3",
            "created_at": "2025-02-09T10:56:45.000000Z",
            "updated_at": "2025-02-09T10:56:45.000000Z"
        }
    ]
}
```

Ürünleri çağırıken queryParams olarak category_id gönderilirse, ilgili kategorinin ürünleri listelenir. 
(GET) /products?category_id=<OPTIONAL>

```bash
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": [
        {
            "productId": "9e2b2fdf-4f9c-4803-b24e-93ad13efaf01",
            "productName": "Product 3",
            "categoryId": "9e2b2fdf-345d-470e-a53d-47a3e0d1718d",
            "categoryName": "Category 3",
            "price": 14,
            "stock": 0
        },
        {
            "productId": "9e2b2fdf-54d2-4311-aed3-107a88a78e8e",
            "productName": "Product 8",
            "categoryId": "9e2b2fdf-345d-470e-a53d-47a3e0d1718d",
            "categoryName": "Category 3",
            "price": 12,
            "stock": 46
        }
          ]
}
```


Sipariş verme istekleri array olarak gönderilmektedir.
(POST) /orders
```bash
Payload
[
    {
        "product_id": "9e2b2fdf-4f9c-4803-b24e-93ad13efaf01",
        "amount": 2
    },
    {
        "product_id": "9e2b2fdf-54d2-4311-aed3-107a88a78e8e",
        "amount": 1
    },
    {
        "product_id": "9e2b2fdf-4f34-4ef7-92a7-f536b880c4b0",
        "amount": 1
    }
]
```
```bash
Response
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": {
        "orderPackageId": "9e2bc9c4-d753-4b24-bc12-3ddb89ec875e",
        "productCount": 3,
        "discount": 0.4,
        "totalPrice": 18.6,
        "createdAt": 1739109059
    }
}
```

Sipariş detayları (GET) /orders, eğer ilgili siparişin detayları listelemek istenirse parametre olarak order_package_id değerine siparişin uuid kodu eklenmelidir.
```bash
URL
orders/?order_package_id=<OPTIONAL>
```

```bash
Response (Orders History)
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": [
        {
            "orderPackageId": "9e2bc9c4-d753-4b24-bc12-3ddb89ec875e",
            "productCount": 3,
            "discount": 0.4,
            "totalPrice": 18.6,
            "createdAt": 1739109059
        }
    ]
}
```

```bash
Response (Order Package Detail)
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": [
        {
            "orderId": "9e2bc9c4-d753-4b24-bc12-3ddb89ec875e",
            "productId": "9e2b8b76-5d38-413c-9def-a502a431d1fb",
            "productName": "Product 1",
            "quantity": 2,
            "unitPrice": 3,
            "total": 6
        },
        {
            "orderId": "9e2bc9c4-d753-4b24-bc12-3ddb89ec875e",
            "productId": "9e2b8b76-5dc9-401e-98fa-8274b5874a05",
            "productName": "Product 2",
            "quantity": 1,
            "unitPrice": 11,
            "total": 11
        },
        {
            "orderId": "9e2bc9c4-d753-4b24-bc12-3ddb89ec875e",
            "productId": "9e2b8b76-5e00-4471-8aea-1abef05130ea",
            "productName": "Product 3",
            "quantity": 1,
            "unitPrice": 2,
            "total": 2
        }
    ]
}
```
İndirim yapılan siparişlerin detayına erişmek için : 
(GET) /discount/<ORDER_PACKAGE_UUID> 
```bash
Response
{
    "status": "success",
    "code": 200,
    "message": null,
    "data": [
        {
            "discountType": "TOTAL_QUANTITY_GREATER_PERCENTAGE_DISCOUNT",
            "orderId": "9e2b309a-8fd7-4a1e-aed3-855195b81fc6",
            "productId": "9e2b2fdf-4f9c-4803-b24e-93ad13efaf01",
            "productName": "Product 3",
            "discountAmount": 107.6,
            "subTotal": 406,
            "createdAt": 1739083362
        }
    ]
}
```
## Dış Kaynaklar

- Event Sourcing (https://spatie.be/docs/laravel-event-sourcing/v1/introduction)
- Tactician (https://tactician.thephpleague.com)

## Görüş, öneri ve sorularınız için

- [@website](https://furkandamar.net)
- [@linkedin](https://www.linkedin.com/in/furkan-damar-2b96ab99/)
- [@email](mailto:f.damar@hotmail.com)


