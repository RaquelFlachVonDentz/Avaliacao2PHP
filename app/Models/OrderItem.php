<?php

namespace App\Models;

class OrderItem
{
    public ?int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $unit_price;

    public function __construct(?int $id, int $order_id, int $product_id, int $quantity, float $unit_price)
    {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->unit_price = $unit_price;
    }
}



