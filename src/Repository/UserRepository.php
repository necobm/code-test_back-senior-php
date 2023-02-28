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
        if( empty($userData) || !isset($userData[$id]) ){
            return null;
        }
        return $userData[$id];

    }

    public function getFieldValueFromAllUsers(string $fieldName): ?array
    {
        $userData = $this->findAll();
        if( empty($userData) ){
            return null;
        }

        return array_map(function ($item) use ($fieldName){
            return $item[$fieldName] ?? null;
        }, $userData);
    }

    /**
     * @throws \Exception
     */
    public function persist(array $users): void
    {
        if(empty($users)){
            throw new \Exception("You cannot persist empty data, you may loose all your data by accident");
        }
        $fileHandler = fopen($this->resourcesDir, 'w');

        fwrite($fileHandler, json_encode($users));
        fclose($fileHandler);
    }

}