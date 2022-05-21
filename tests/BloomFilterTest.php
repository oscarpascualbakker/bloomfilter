<?php

use \PHPUnit\Framework\TestCase;
use src\BloomFilter\BloomFilter;
use src\Storage\Storage;


/**
 * Just to check that the filter is instanciated, it can add elements, and check them.
 */
class BloomFilterTest extends TestCase
{

    /**
     * Create the Bloom Filter object, the storage, and add some elements.
     *
     * This test will work with a single hash.  If double hashing is applied it won't work.
     *
     * @covers MerkleTree
     * @return void
     */
    public function test_bloom_filter_is_ok()
    {
        $maxElements              = 100;
        $falsePositiveProbability = 0.001;  // 0,1%

        $storage = new Storage('Redis');

        $bloomfilter = new BloomFilter($maxElements, $falsePositiveProbability, $storage);
        $this->assertInstanceOf(BloomFilter::class, $bloomfilter);

        $bloomfilter->add('abc');  // Bits: 1426,  868, 1350
        $bloomfilter->add('def');  // Bits:  806,  628, 1104
        $bloomfilter->add('ghi');  // Bits:  172, 1066,  690
        $bloomfilter->add('jkl');  // Bits:  878,  476,  832

        $this->assertTrue($bloomfilter->check('abc'));
        $this->assertTrue($bloomfilter->check('def'));
        $this->assertTrue($bloomfilter->check('ghi'));
        $this->assertTrue($bloomfilter->check('jkl'));
        $this->assertFalse($bloomfilter->check('adgj'));
        $this->assertFalse($bloomfilter->check('behk'));
        $this->assertFalse($bloomfilter->check('cfil'));
    }

}