<?php

namespace src\Storage;


use src\BloomFilter\StorageInterface;


class RedisAdapter implements StorageInterface
{

    private $connection;


    public function __construct()
    {
        //Connecting to Redis server on localhost
        $this->connection = new \Redis();
        $this->connection->connect('redis', 6379);
    }


    public function put($key, $value): bool
    {
        return $this->connection->set($key, serialize($value));
    }


    public function get($key)
    {
        return unserialize($this->connection->get($key), ['allowed_classes' => true]);
    }

}

?>