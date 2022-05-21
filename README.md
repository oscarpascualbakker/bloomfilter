
# Bloom Filter in PHP

A Bloom Filter is a probabilistic data structure used to check whether an element is a member of a set.  In this structure, elements can be added to the set, but not removed (there is a chance of deleting elements, but I will do that in another post).

A query to a Bloom filter returns either "possibly in set" or "definitely not in set".

A Bloom filter is an array of _m_ bits, initially all set to 0.  There must also be _k_ different hash functions defined, each of which maps or hashes some set element to one of the _m_ bitarray positions, generating a uniform random distribution.  Typically, _k_ is a small constant which depends on the desired false error rate.

To add an element to the set, it's necessary to perform _k_ hash functions to the element, get the correspondant _k_ positions in the filter, and set these bits to 1.

To check whether an element is in the set, perform all hash functions to that element to get the positions in the filter.  If any of the bits at these positions is 0, the element is definitely not in the set.

If all positions are set to 1, then either the element is in the set, or the bits have been set to 1 during the insertion of other elements, resulting in a false positive.

[![Bloom Filter](https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Bloom_filter.svg/540px-Bloom_filter.svg.png)](https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Bloom_filter.svg/540px-Bloom_filter.svg.png)

*Source: [Wikipedia](https://en.wikipedia.org/wiki/Bloom_filter  "Bloom Filter - Wikipedia")*

In this image, element _w_ is not in the set, as one of the bits is 0.

It is not possible to delete elements in a simple Bloom filter, and there is no way to distinguish between a legitime and a false positive.  More advanced techniques can solve this problem.




## In this implementation...

In this implementation I do not use a normal PHP array, but a "bit array", created specifically for this data structure as PHP has some difficulties dealing with bits.  By doing so, ~98% of the space is saved in a 64 bit machine.

Also, I only use seven hashing functions, and that means that I don't use the calculated number of functions I really need.  To do this correctly I should create more hashing functions and use _(k)_ of them.

There is plenty of literature out there about how to calculate the number of bits and the number of hashing functions, but have a look at this site:
https://hur.st/bloomfilter/
Use the data in my example (1000 elements and a false probability of 0,001) and you will get the following result:
* 1438 bits
* 10 hashing functions


## How do the hashing functions work

I use the PHP _hash_ function and seven different algorithms: md5, sha256 and ripemd160

The results are hexadecimal strings, and that's quite useless, here.  Over that result I apply the _hexdec_ function, which returns a huge number (and PHP has a problem dealing with huge numbers).

I need the modulus of that huge number and the number of bits in the filter.  The only way to do this (easily) in PHP is using the BC Math library.

[NOTICE] Before you say anything about the decision of using the PHP hash function and the selected algorithms: "I know".  The distribution is not generating a good and uniform random distribution.  I will eliminate this notice as soon as I add good (and different) hashing functions.


## Installation and run

First, clone this repository:

```sh
$ git clone https://github.com/oscarpascualbakker/bloomfilter.git .
```

Then, run the command to build and run the Docker images (there are two: the filter and the storage):

```sh
$ docker compose up -d
```

Go to your browser and point to http://localhost.



### Cost analysis

In such a structure, the cost is very low: **O(_k_)**, where _k_ is the number of used hashing functions.

### Comments

Bloom filters are useful in cases where:
* the data to be searched is large
* the memory available on the system is limited/low

Hence, this algorithm has a lot of uses.  As it is a very fast system to respond wether an element is probably in a set or not, it is used to save time and space.
* Pre-caching systems
* Weak password detection
* Internet Cache Protocol
* Safe browsing in Google Chrome
* Wallet synchronization in Bitcoin (this is not more part of Bitcoin)
* Hash based IP Traceback
* Cyber security like virus scanning

And that's it!  As usual, don't hesitate to give me your feedback. I'm glad to improve this algorithm with your help.

### **Cheers!**