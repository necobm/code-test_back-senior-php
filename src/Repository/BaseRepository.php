<?php

namespace App\Repository;

class BaseRepository
{
    protected string $resourcesDir = __DIR__;

    protected function isValidResourcesDir(): bool
    {
        return ($this->resourcesDir !== __DIR__ && file_exists($this->resourcesDir));
    }

    /**
     * @throws \Exception
     */
    public function findAll(): ?array
    {
        if( !$this->isValidResourcesDir() ){
            throw new \Exception("Wrong data file");
        }

        $data = file_get_contents($this->resourcesDir);
        if($data !== false){
            return json_decode($data, true);
        }
        return null;
    }

    public function findOneById(int $id): ?array
    {
        if( !$this->isValidResourcesDir() ){
            throw new \Exception("Wrong data file");
        }

        $data = $this->findAll();
        if( empty($data) || !isset($data[$id]) ){
            return null;
        }
        return $data[$id];

    }

    /**
     * @throws \Exception
     */
    public function persist(array $records): void
    {
        if( !$this->isValidResourcesDir() ){
            throw new \Exception("Wrong data file");
        }

        if(empty($records)){
            throw new \Exception("You cannot persist empty data, you may loose all your data by accident");
        }
        $fileHandler = fopen($this->resourcesDir, 'w');

        fwrite($fileHandler, json_encode($records));
        fclose($fileHandler);
    }

    /**
     * @throws \Exception
     */
    public function update(int $id, array $data): void
    {
        if( !$this->isValidResourcesDir() ){
            throw new \Exception("Wrong data file");
        }

        if(empty($data)){
            throw new \Exception("WARNING, you are updating a record with empty data");
        }
        $records = $this->findAll();

        if(!array_key_exists(strval($id), $records)){
            throw new \Exception("The given id doesn't exist");
        }

        $records[$id] = $data;
        $this->persist($records);
    }
}