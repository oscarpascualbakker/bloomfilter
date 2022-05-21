<?php

namespace src\Storage;


use src\Storage\RedisAdapter;
use src\BloomFilter\StorageInterface;


class Storage implements StorageInterface
{

    private $storage_type;


    public function __construct(string $storage_type)
    {
        if ($storage_type == 'Redis') {
            $this->storage_type = new RedisAdapter();
        }
    }


    public function put($key, $value): bool
    {
        return $this->storage_type->put($key, $value);
    }


    public function get($key)
    {
        return $this->storage_type->get($key);
    }

}

?>