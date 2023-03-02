<?php

namespace App\Repository;

final class UsersIpRepository extends BaseRepository
{
    public function __construct(string $resourceDir = "")
    {
        $this->resourcesDir = __DIR__ . '/../../resources/db/users_ip.json';
        if(!empty($resourceDir)){
            $this->resourcesDir = $resourceDir;
        }
    }

}