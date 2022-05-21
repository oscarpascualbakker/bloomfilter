<?php

namespace src\BloomFilter;


use src\BloomFilter\BloomFilterHashes;
use src\BloomFilter\BloomFilterInterface;
use src\BloomFilter\StorageInterface;
use Oscarpb\Bitarray\BitArray;


final class BloomFilter implements BloomFilterInterface
{

    /**
     * The total number of bits we need for this filter.
     *
     * @var integer
     */
    private int $numBits = 0;


    /**
     * The filter itself, in the form of a bit array.
     *
     * @var array
     */
    private BitArray $bitArray;


    /**
     * The hashing functions.  They come from another class, so this one is
     * agnostic about what hashing functions are used.
     *
     * @var class
     */
    private BloomFilterHashes $hashes;


    /**
     * The number of elements in the univers.
     *
     * @var integer
     */
    private int $maxElements = 0;


    /**
     * The probability of false positive we can accept.  Some examples:
     *    0.01  => 1%      1 in a hunderd
     *    0.001 => 0.1%    1 in a thousand
     *
     * @var float
     */
    private float $falsePositiveProbability;


    /**
     * Just a counter to know the number of elements in the filter.
     *
     * @var integer
     */
    private int $currentElements = 0;


    /**
     * We need to store the filter somewhere.  This implementation does not know
     * how the storage is implemented.  It just "stores" the filter... :-)
     *
     * @var mixed
     */
    private StorageInterface $storage;


    /**
     * The number of hashing functions (k) we need to obtain the desired false probability.
     *
     * @var integer
     */
    private int $hashesNeeded;


    /**
     * The number of hashing functions available in this package.
     *
     * @var integer
     */
    private int $hashesAvailable;


    /**
     * The construct function will perform the following operations:
     *
     *    -Calculate the number of bits needed to satisfy population and
     *     false positive probability.
     *    -Initialize the filter (all values to zero).
     *    -Import the hashing functions
     *
     * There are several ways to calculate the size of the filter.
     * Here are some useful sites to check:
     *    -https://hur.st/bloomfilter
     *    -https://krisives.github.io/bloom-calculator/
     *    -https://toolslick.com/programming/data-structure/bloom-filter-calculator
     *
     * Here are a couple of formulas to obtain the filter size:
     *    1) (int) round((($maxElements * log($falsePositiveProbability)) / pow(log(2), 2)) * -1)
     *    2) (int) ceil(($maxElements * log($falsePositiveProbability)) / log(1 / pow(2, log(2))))
     *
     * The second one is twice as fast as the first one, but don't worry because this is only done once.
     *
     * @param integer $maxElements
     * @param float $falsePositiveProbability
     * @param StorageInterface $storage
     */
    public function __construct(int $maxElements, float $falsePositiveProbability, StorageInterface $storage)
    {
        $this->numBits      = (int) ceil(($maxElements * log($falsePositiveProbability)) / log(1 / pow(2, log(2))));
        $this->hashesNeeded = round(($this->numBits / $maxElements) * log(2));

        $this->bitArray = new BitArray($this->numBits);

        $this->hashes          = new BloomFilterHashes;
        $this->hashesAvailable = count(get_class_methods($this->hashes));

        $this->maxElements              = $maxElements;
        $this->falsePositiveProbability = $falsePositiveProbability;

        // We use a specific storage, but this implementation doesn't know anything about it.
        $this->storage = $storage;
    }


    /**
     * Hash the element to add with the available hashing functions
     * and set the correspondant bit in the filter.
     *
     * @param mixed $element
     * @return void
     */
    public function add($element)
    {
        // Get all the available hashing functions
        $hashfunctions = get_class_methods($this->hashes);

        // Serialize element if different from string
        if (!is_string($element)) {
            $element = serialize($element);
        }

        // Execute them all and increment result bit
        $i = 0;
        foreach ($hashfunctions as $function) {
            $hash = $this->hashes->$function($element, $this->numBits);
            $this->bitArray->setBit($hash);
            $i++;
            if ($i >= $this->hashesNeeded) {
                break;
            }
        }

        $this->currentElements++;
    }


    /**
     * Check the existence of an element in the filter.  The process is more
     * or less the same as adding: hash the element and check if the bit is
     * activated.  If not, we can safely return 'false', the element is not present.
     *
     * The check function only returns 'true' if all bits are activated.
     *
     * @param mixed $element
     * @return boolean
     */
    public function check($element): bool
    {
        // Get all the available hashing functions
        $hashfunctions = get_class_methods($this->hashes);

        // Serialize element if different from string
        if (!is_string($element)) {
            $element = serialize($element);
        }

        foreach ($hashfunctions as $function) {
            $hash = $this->hashes->$function($element, $this->numBits);
            if (!$this->bitArray->getBit($hash)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Stores the filter into the defined storage.
     * Because we have the storage contract (the interface), we know that
     * we must provide a key and a value.
     *
     * @return void
     */
    public function save()
    {
        $this->storage->put('bloom_filter', $this);
    }


    /**
     * Just a method to show what's going on inside the filter
     *
     * @return void
     */
    public function getInfo()
    {
        $stringArray = array();
        $j = 0;
        for ($i = 0; $i < $this->numBits; $i++) {

            if ($this->bitArray->getBit($i) != 0) {
                $stringArray[] = $i;
                $j++;
            }
        }

        echo "<table border='1'>";
        echo "<tr><td>Max. elements</td><td width='300px' style='text-align: right;'>".$this->maxElements."</td></tr>";
        echo "<tr><td>Current elements</td><td style='text-align: right;'>".$this->currentElements."</td></tr>";
        echo "<tr><td>False positive %</td><td style='text-align: right;'>".($this->falsePositiveProbability*100)."%</td></tr>";
        echo "<tr><td>Bits in filter</td><td style='text-align: right;'>".$this->numBits."</td></tr>";
        echo "<tr><td>Hashing functions needed</td><td style='text-align: right;'>".$this->hashesNeeded."</td></tr>";
        echo "<tr><td>Hashing functions available</td><td style='text-align: right;'>".$this->hashesAvailable."</td></tr>";
        echo "<tr><td>Used bytes</td><td style='text-align: right;'>".ceil($this->numBits/8)."</td></tr>";
        echo "<tr><td>Activated bits<br>(".$j.")</td><td>".implode(", ", $stringArray)."</td></tr>";
        echo "</table>";
    }


    public function print()
    {
        echo $this->bitArray->print();
    }


    public function print_segments()
    {
        echo $this->bitArray->print_segments();
    }


}
?>