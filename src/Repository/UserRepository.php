<?php

namespace App\Repository;

class UserRepository
{
    private $resourcesDir = __DIR__ . '/../../resources/db/users.json';
    public function findAll(): ?array
    {
        $usersData = file_get_contents($this->resourcesDir);
        if($usersData !== false){
            return json_decode($usersData, true);
        }
    }

    public function findOneById(int $id): ?array
    {
        $userData = $this->findAll();
        if( !is_null($userData) && isset($userData[$id]) ){
            return $userData[$id];
        }
        return null;
    }

}