<?php

namespace App\Models;

class Order
{
    public ?int $id;
    public int $client_id;
    public string $order_date;
    public string $status;

    public function __construct(?int $id, int $client_id, string $order_date, string $status)
    {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->order_date = $order_date;
        $this->status = $status;
    }
}

