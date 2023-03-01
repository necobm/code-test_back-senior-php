<?php

namespace App\Repository;

final class UsersIpRepository extends BaseRepository
{
    public function __construct()
    {
        $this->resourcesDir = __DIR__ . '/../../resources/db/users_ip.json';
    }

}