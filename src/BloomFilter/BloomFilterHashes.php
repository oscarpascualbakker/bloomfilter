<?php

namespace src\BloomFilter;


/**
 * The idea is to obtain a hash string of the element, which is basically a
 * hexadecimal string, convert that hex string to decimal, and then perform
 * the modulus with the number of bits of the filter (the limit).
 *
 * PHP cannot handle big numbers.  Therefore I use the BC Math extension.
 * https://www.php.net/manual/en/ref.bc.php
 *
 * The bcmod function requires both parameters to be strings.
 * https://www.php.net/manual/en/function.bcmod.php
 */
class BloomFilterHashes
{

    public function md5_hash($string, $limit)
    {
        $hash      = hexdec(hash('md5', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function sha256_hash($string, $limit)
    {
        $hash      = hexdec(hash('sha256', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function ripemd160_hash($string, $limit)
    {
        $hash      = hexdec(hash('ripemd160', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function gost_hash($string, $limit)
    {
        $hash      = hexdec(hash('gost', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function whirlpool_hash($string, $limit)
    {
        $hash      = hexdec(hash('whirlpool', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function snefru_hash($string, $limit)
    {
        $hash      = hexdec(hash('snefru', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }


    public function haval2563_hash($string, $limit)
    {
        $hash      = hexdec(hash('haval256,3', $string));
        $hash_text = number_format($hash, 0, '', '');

        return bcmod($hash_text, strval($limit));
    }

    // Here we should add some more (and better) hashing functions.

}
?>