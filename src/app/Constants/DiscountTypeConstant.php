<?php

class DiscountTypeConstant
{
    public const MANY_GET_ONE_FREE = 'MANY_GET_ONE_FREE'; //Birden fazla alımda 1 bedava verir
    public const TOTAL_AMOUNT_GREATER_PERCENTAGE_CHEAPEST_PRODUCT = 'TOTAL_AMOUNT_GREATER_PERCENTAGE_CHEAPEST_PRODUCT'; //Toplam adet belirlenen miktar ve üzeri ise, en ucuz ürüne uygulanacak yüzdelik indirim
    public const TOTAL_AMOUNT_GREATER_PERCENTAGE_DISCOUNT = 'TOTAL_AMOUNT_GREATER_PERCENTAGE_DISCOUNT'; //Toplam adet belirlenen miktar ve üzeri ise, uygulanacak toplam tutarda yüzdelik indirimi
    public const TOTAL_QUANTITY_GREATER_PERCENTAGE_DISCOUNT = 'TOTAL_QUANTITY_GREATER_PERCENTAGE_DISCOUNT'; //Toplam tutar belirlenen tutar ve üzeri ise, uygulanacak toplam tutarda yüzdelik indirimi
}