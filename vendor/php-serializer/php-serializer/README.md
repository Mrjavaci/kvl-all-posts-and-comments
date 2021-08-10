# PHPSerializer

> This library is a work in progress. Test any builds before `phpunit` before use.

Library for very quick manipulation of large serialized strings,

PHPSerializer was built to improve the efficience of [Flatbase](https://github.com/adamnicholson/flatbase)

In PHP, if you have a serialized array and you wish to add an item to that array, you'd need to do something like this:

    $data = unserialize($serialized);
    array_push($data, 'some-new-item');
    $serialized = unserialize($data);

What's the problem with this? Well it's extremely inefficient to unserialize that entire array for such a simple operation.

If you're unserializing a small array or you're not doing it very often then this is probably fine, but if you're in a heavy load setup there must be a better way.

The idea behind PHPSerializer is essentially to parse and stream serialized strings in the most efficient way possible depending on the operation, rather than `unserialize()`'ing the entire string for every function.

The speed benefits can be outstanding. Here's some benchmarking results from early testing versions of PHPSerializer using this approach:

    > Beginning benchmarking test for appending new items to a serialized array
    > Benchmarking appends by unserialize(), array_push(), and serialize()
    > - Performed 2000 appends to a data set of 10000 in 7.004s, with a memory peack of 5.00Mb
    > Benchmarking appends PHPSerializer\SerializeArray::append()
    > - Performed 2000 appends to a data set of 10000 in 34ms, with a memory peack of 5.25Mb

To re-iterate those results; performing 2,000 array_push() operations on a serialized array with 10,000 items took `7.004s` with the traditional method, versus a measly `34ms` with PHPSerializer.

Another example where huge speed gains can be made is reading the first item from a  serialized array. Normally you would need to `unserialize()` the entire array regardless of how many array elements you're interested in. Let's see what the difference is

    > Building control data
    > Benchmarking reads using PHPSerializer::first()
    > - Read the first item from a data set of 3000000 in 89ms, with a memory peack of 2.23Gb
    > Benchmarking reads using unserialize[] 
    > - Read the first item from a data set of 3000000 in 1.118s, with a memory peack of 2.23Gb

That's `1250%` faster, dropping from `1.118s` to just `89ms`

Feature wishlist:
  - Be able to stream from files directly
  - Appending an item to a serialized array (`array_push()`)
  - Retreive a single item from a serialized array by its key
  - Search for an item in a serialized array by its value (`array_search()`)
  - Filter all items in a serialized array (`array_filter()`)
  - Strict serialized string validation; `unserialize('b:1;ThisWillNotThrowAnError')`
