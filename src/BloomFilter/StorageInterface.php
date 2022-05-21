<?php

namespace src\BloomFilter;


interface StorageInterface
{

    public function put($key, $value): bool;
    public function get($key);

}

?>