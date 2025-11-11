<?php

namespace App\Models;

class Client
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $phone;
    public string $city;
    public string $state;
    public string $created_at;

    public function __construct(?int $id, string $name, string $email, string $phone, string $city, string $state, string $created_at = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->city = $city;
        $this->state = $state;
        $this->created_at = $created_at;
    }
}
