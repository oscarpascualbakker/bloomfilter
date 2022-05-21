<?php

require __DIR__.'/vendor/autoload.php';


use src\BloomFilter\BloomFilter;
use src\Storage\Storage;


$maxElements              = 100;
$falsePositiveProbability = 0.001;  // 0,1%

$storage = new Storage('Redis');


// This array will be used later
$arrayTest = [
    'key1' => 'value1',
    'key2' => 'value2'
];


// UNCOMMENT the storage code if you need to save the filter.


// First, let's check if we have the filter already stored.
// if (!$bloomfilter = $storage->get('bloom_filter')) {

    // We don't, so let's create the filter...
    $bloomfilter = new BloomFilter($maxElements, $falsePositiveProbability, $storage);

    // ...and add 25 elements to it.
    $bloomfilter->add('Text 1');
    $bloomfilter->add('This is a test');
    $bloomfilter->add('Testing Bloom filter');
    $bloomfilter->add('What about this text?');
    $bloomfilter->add('This is another text');
    $bloomfilter->add('Still writing');
    $bloomfilter->add('One more');
    $bloomfilter->add('OK.  Enough! Search for some other texts!');
    $bloomfilter->add('scrambled it to make a type specimen book. It has survived not only five centuries');
    $bloomfilter->add('with the release of Letraset');

    $bloomfilter->add('Lorem Ipsum is simply dummy text of the printing');
    $bloomfilter->add('Contrary to popular belief');
    $bloomfilter->add('over 2000 years old');
    $bloomfilter->add('It was popularised');
    $bloomfilter->add('page editors');
    $bloomfilter->add('There are many variations');
    $bloomfilter->add('Please email us with details');
    $bloomfilter->add('Translations: Can you help translate this site into a foreign language ?');
    $bloomfilter->add('accompanied by English');
    $bloomfilter->add('The Extremes of Good and Evil');

    $bloomfilter->add('actual teachings of the great explorer');
    $bloomfilter->add('with a man who chooses');
    $bloomfilter->add('deleniti atque corrupti quos dolores');
    $bloomfilter->add('unde omnis iste natus error sit voluptatem accusantium');
    $bloomfilter->add('I dont understand why');
    $bloomfilter->add(27074);
    $bloomfilter->add(true);

    $bloomfilter->add($arrayTest);

    // Save it for next execution
    // $bloomfilter->save();
// }


// Print the filter information
$bloomfilter->getInfo();
echo "<br><br>";


// Print the bitarray information
$bloomfilter->print();
echo "<br><br>";
$bloomfilter->print_segments();


// Inclusion tests
function inclusionTest($element)
{
    global $bloomfilter, $falsePositiveProbability;
    if ($bloomfilter->check($element)) {
        echo "The element is in the filter (" . 100 - round($falsePositiveProbability*100, 2). "% sure).<br>";
    } else {
        echo "The element is NOT in the filter (100% sure).<br>";
    }
}

inclusionTest('This is another text');
inclusionTest('Testing Bloom filter');
inclusionTest('This text is not here');
inclusionTest('I dont understand why');
inclusionTest('There are many variationsa');

inclusionTest(27074);
inclusionTest(27075);
inclusionTest(true);
inclusionTest(false);
inclusionTest(0.3444);

inclusionTest($arrayTest);

?>