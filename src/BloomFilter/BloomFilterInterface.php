<?php

namespace src\BloomFilter;


interface BloomFilterInterface
{

    public function add($element);
    public function check($element): bool;
    public function save();

}

?>