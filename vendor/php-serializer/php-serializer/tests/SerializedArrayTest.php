<?php

namespace PHPSerializer;

use SplFileObject;
use SplTempFileObject;
use PHPUnit_Framework_Testcase;

class SerializedArrayTest extends PHPUnit_Framework_Testcase
{
    public function testInstance()
    {
        $stream = new SplFileObject(__DIR__ . '/data/simple-array-of-scalars.serialized', 'r');
        $array = new SerializedArray($stream);
        $this->assertTrue($array instanceof SerializedArray);
    }

    public function testInstanceWithInMemoryArray()
    {
        $stream = new SplTempFileObject();
        $stream->fwrite(serialize(['First Item', 2, true, 15.25]));
        $array = new SerializedArray($stream);
        $this->assertTrue($array instanceof SerializedArray);
    }

    public function testCreateFromString()
    {
        $array = SerializedArray::createFromString(serialize(['foo', 'bar']));
        $this->assertTrue($array instanceof SerializedArray);
    }

    public function testCreateFromArray()
    {
        $array = SerializedArray::createFromArray(['foo', 'bar']);
        $this->assertTrue($array instanceof SerializedArray);
    }

    public function testInstanceWithFlatAssociativeArray()
    {
        $array = SerializedArray::createFromArray(['foo' => 'bar', 'baz' => 'boz']);

        $this->assertTrue($array instanceof SerializedArray);

        $array->next();
        $this->assertEquals($array->key(), 'foo');
        $this->assertEquals($array->current(), 'bar');


        $array->next();
        $this->assertTrue($array->valid());
        $this->assertEquals($array->key(), 'baz');
        $this->assertEquals($array->current(), 'boz');

        $array->next();
        $this->assertFalse($array->valid());
    }

    public function testInstanceWithFlatIndexedArray()
    {
        $array = SerializedArray::createFromArray(['bar','boz']);

        $this->assertTrue($array instanceof SerializedArray);

        $array->next();
        $this->assertEquals($array->current(), 'bar');

        $array->next();
        $this->assertTrue($array->valid());
        $this->assertEquals($array->current(), 'boz');

        $array->next();
        $this->assertFalse($array->valid());
    }

    public function testFirstCallToNextReturnsFirstItem()
    {
        $stream = new SplTempFileObject();
        $stream->fwrite(serialize(['First Item', 2, true, 15.25]));
        $array = new SerializedArray($stream);
        $array->next();
        $this->assertEquals($array->key(), 0);
        $this->assertEquals($array->current(), 'First Item');
    }

    public function testValidTrueDuringItemsAndFalseAtEndOfArray()
    {
        $stream = new SplTempFileObject();
        $stream->fwrite(serialize(['First Item', 'There are only 2 items']));
        $array = new SerializedArray($stream);

        $array->next();
        $this->assertTrue($array->valid());

        $array->next();
        $this->assertTrue($array->valid());

        $array->next();
        $this->assertFalse($array->valid());
    }

    public function testArrayCanBeRebuilt()
    {
        $rawArray = ['foo', 'bar'];
        $array = SerializedArray::createFromArray($rawArray);
        $rebuiltArray = [];
        foreach ($array as $key => $value) {
            $rebuiltArray[$key] = $value;
        }
        $this->assertEquals($rawArray, $rebuiltArray);
    }

    public function testCountReturnsExpected()
    {
        $rawArray = ['foo', 'bar', 'foo', 5, 19.2, true, false, []];
        $array = SerializedArray::createFromArray($rawArray);
        $this->assertEquals($array->count(), 8);
    }

    public function testAllAlwaysReturnsAll()
    {
        $rawArray = ['foo', 'bar', 'foo', 5, 19.2, true, false, []];
        $array = SerializedArray::createFromArray($rawArray);
        $this->assertEquals($all = $array->all(), $rawArray);
        $this->assertEquals($array->all(), $all);
    }

    public function testAppend()
    {
        $rawArray = ['foo', 'bar'];
        $array = SerializedArray::createFromArray($rawArray);

        $array->append('baz');
        $this->assertEquals($array->count(), count($rawArray) + 1);

        // Iterate to the new item and make sure it is as expected
        $array->rewind();
        $array->next();
        $array->next();
        $array->next();
        $this->assertEquals($array->current(), 'baz');
    }

    public function testAppendWithNonLinearArrayKeys()
    {
        $rawArray = [3 => 'foo', 1 =>'bar'];
        $array = SerializedArray::createFromArray($rawArray);

        $array->append('baz');

        $all = $array->all();

        $this->assertEquals($array->count(), 3);
        $this->assertEquals($all[3], 'foo');
        $this->assertEquals($all[1], 'bar');
        $this->assertTrue(in_array('baz', $all));
    }

    public function testFirst()
    {
        $rawArray = ['foo', 'bar'];
        $array = SerializedArray::createFromArray($rawArray);
        $this->assertEquals($array->first(), 'foo');
    }

    public function testFirstReturnsNullifEmpty()
    {
        $array = SerializedArray::createFromArray([]);
        $this->assertEquals($array->first(), null);
    }

    /**
     * @param $rawArray
     * @param $timesToCallNext
     * @param $expected
     * @dataProvider testRemoveDataProvider
     */
    public function testRemove($rawArray, $timesToCallNext, $expected)
    {
        $array = SerializedArray::createFromArray($rawArray);

        for ($i=0; $i <= $timesToCallNext; $i++) {
            $array->next();
        }

        $array->remove();

        $this->assertEquals($array->count(), count($expected));

        $rebuild = [];
        $array->rewind();
        foreach ($array as $item) {
            $rebuild[] = $item;
        }

        $this->assertEquals($rebuild, $expected);
    }

    public function testRemoveDataProvider()
    {
        return [
            [
                ['foo', 'bar'],
                0,
                ['bar']
            ],
            [
                ['foo', 'bar'],
                1,
                ['foo']
            ],
            [
                ['foo', 'bar', 'baz'],
                1,
                ['foo', 'baz']
            ],
        ];
    }

    public function testRemovePreservesIteratorPosition()
    {
        $array = SerializedArray::createFromArray(['foo', 'bar', 'baz']);
        $array->remove();
        $array->next();
        $this->assertEquals($array->current(), 'bar');

        $array = SerializedArray::createFromArray(['foo', 'bar', 'baz']);
        $array->next();
        $array->next();
        $array->remove();
        $array->next();
        $this->assertEquals($array->current(), 'baz');
    }

    public function testRemoveUpdatesFile()
    {
        $path = __DIR__ . '/data/remove-test.serialized';
        $file = new SplFileObject($path, 'w+');
        $file->fwrite(serialize(['foo', 'bar', 'baz']));

        $array = new SerializedArray($file);
        $this->assertEquals($array->count(), 3);
        $array->remove();

        $array = new SerializedArray($file);
        $this->assertEquals($array->count(), 2);
    }

    public function testValidReturnsFalseWhenAllItemsRemovedWithRemove()
    {
        $array = SerializedArray::createFromArray(['foo', 'bar', 'baz']);
        $array->remove();
        $array->remove();
        $array->remove();

        $this->assertEquals($array->count(), 0);

        $array = SerializedArray::createFromArray(['foo', 'bar', 'baz']);
        foreach ($array as $item) {
            $array->remove();
        }

        $this->assertEquals($array->count(), 0);
    }

    public function testOverride()
    {
        $path = __DIR__ . '/data/override-test.serialized';
        $file = new SplFileObject($path, 'w+');
        $file->fwrite(serialize(['foo', 'bar', 'baz']));

        $array = new SerializedArray($file);
        $array->override(['newone', 'newtwo']);

        $this->assertEquals($array->count(), 2);
    }

    public function testCountZeroWhenAllItemsRemovedUsingOverride()
    {
        $array = SerializedArray::createFromArray(['foo', 'bar', 'baz']);
        $array->override([]);
        $this->assertEquals($array->count(), 0);
    }

    public function testValidReturnsFalseOnEmptyArray()
    {
        $array = SerializedArray::createFromArray([]);
        $this->assertEquals($array->valid(), false);
    }
}
