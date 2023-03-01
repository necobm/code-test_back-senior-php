<?php

namespace App\Repository;

class BaseRepository
{
    protected string $resourcesDir = __DIR__;

    protected function isValidResourcesDir(): bool
    {
        return $this->resourcesDir !== __DIR__;
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
    }

    public function findOneById(int $id): ?array
    {
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
        $records = $this->findAll();
        $records[$id] = $data;
        $this->persist($records);
    }


}