<?php

namespace App\Factory;

class UserFactory
{
    public function generate(): array
    {
        return [
            1 => ["id" => 1, "name" => "Sergio Palma", "ip" => "188.223.227.125"],
            2 => ["id" => 2, "name" => "Manolo Engracia", "ip" => "194.191.232.168"],
            3 => ["id" => 3, "name" => "Fito Cabrales", "ip" => "77.162.109.160"]
        ];
    }

}