<?php

namespace App\Repository;

final class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->resourcesDir = __DIR__ . '/../../resources/db/users.json';
    }

    /**
     * @throws \Exception
     */
    public function getFieldValueFromAllUsers(string $fieldName): ?array
    {
        if( !$this->isValidResourcesDir() ){
            throw new \Exception("Wrong data file");
        }

        $userData = $this->findAll();
        if( empty($userData) ){
            return null;
        }

        return array_map(function ($item) use ($fieldName){
            return $item[$fieldName] ?? null;
        }, $userData);
    }

}