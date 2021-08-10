<?php

namespace PHPSerializer;

use SplFileObject;

class SerializedArray implements \Iterator, \Countable
{
    protected  $file;
    protected $itemsCount;
    protected $current;
    protected $currentItemStartPosition;
    protected $currentKey;
    protected $end = false;
    protected $itterated = 0;

    public function __construct(SplFileObject $file)
    {
        $this->file = $file;

        $this->rewind();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        if ($this->itterated === 0) {
            $this->next();
        }
        return unserialize($this->current);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $file = $this->file;

        // Store the starting position in memory in case we need it
        $this->currentItemStartPosition = $file->ftell();

        // Get the item key
        $key = '';
        while (($char = $file->fgetc()) !== ';') {
            if ($char === false) {
                // End of file
                $this->current = null;
                $this->currentKey = false;
                return $this->end = true;
            }
            $key .= $char;
        }
        $this->currentKey = ($key . ';');

        // Get the item value
        $buildString = '';
        $stop = false;
        $levels = 0;
        while ($stop === false) {
            $char = $file->fgetc();

            if ($levels === 0 && $char === ';') {
                $stop = true;
            }

            if ($char === '{') {
                $levels++;
            }

            if ($levels === 1 && $char === '}') {
                $stop = true;
            }

            if ($char === '}') {
                $levels--;
            }

            $buildString .= $char;

        }

        $this->current = $buildString;
        $this->itterated++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if ($this->itterated === 0) {
            $this->next();
        }

        return unserialize($this->currentKey);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->end === false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $file = $this->file;

        // Move past the decleration
        $file->rewind();
        if (($char = $file->fgetc()) !== ($exp = 'a') || ($char = $file->fgetc()) !== ($exp = ':')) {
            throw new \Exception('Stream contents is corrupt');
        }

        // Move past the array count and remember it
        $index = '';
        while (($char = $file->fgetc()) !== ':') {
            $index .= $char;
        }
        $this->itemsCount = (int) $index;

        // Move into the array
        if ($file->fgetc() !== '{') {
            throw new \Exception('Stream contents is corrupt');
        }

        $this->itterated = 0;
        $this->end = ($this->count() === 0);
    }

    /**
     * Remove the current() item
     */
    public function remove()
    {
        // Get the current item
        $this->current();

        // Calculate the file pointers we're going to need
        // "Element" refers to the element we're going to remove
        $endOfElement = $this->file->ftell();
        $startOfElement = $this->currentItemStartPosition;
        $sizeOfElement = $endOfElement - $startOfElement;

        // Override the element with data from the rest of the file
        $restOfFile = $this->file->fgets();
        $this->file->fseek($startOfElement);
        $this->file->fwrite($restOfFile, strlen($restOfFile));

        // Get the total file size
        $this->file->fseek(0, SEEK_END);
        $totalSize = $this->file->ftell();

        // Remove the number of bytes that were overridden fom the end of the file
        $this->file->ftruncate($totalSize - $sizeOfElement);

        // Update the total items count
        $this->updateArrayCount($this->itemsCount - 1);

        // Now move the pointer back to where it was
        $this->file->fseek($startOfElement, SEEK_SET);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return (int) $this->itemsCount;
    }

    public function append($item)
    {
        // Count current items
        $this->file->fseek(2, SEEK_SET);
        $oldCount = '';
        while (($char = $this->file->fgetc()) !== ':') {
            $oldCount .= $char;
        }
        $this->updateArrayCount((int) $oldCount + 1);


        // Generate a random key for the new item.
        // We need to ensure our new key doesn't collide with an existing key.
        // This is not a problem when iterating through the array with next() as long as you don't
        // need to rely on the keys being unique. The biggest issue with collisions is when exporting
        // the array with all(), or if you try to unserialize the array outside of SerializedArray
        $newKey = rand(1000000, 2000000);

        // Append the new item
        $this->file->fseek(-1, SEEK_END);
        $this->file->fwrite('i:' . $newKey . ';' . serialize($item) . '}');

        $this->rewind();
    }

    protected function updateArrayCount($count)
    {
        // Get the old count
        $this->file->fseek(2, SEEK_SET);
        $oldDefinition = '';
        while (($char = $this->file->fgetc()) !== ':') {
            $oldDefinition .= $char;
        }

        $newDefinition = $count;

        $this->file->rewind();

        if (strlen($newDefinition) == strlen($oldDefinition)) {
            // New definition is the same length as the old one, so just overwrite those chars
            $this->file->fseek(2, SEEK_SET);
            $this->file->fwrite($newDefinition, strlen($newDefinition));
        } else {
            // New definition is longer than the old. Re-write the line to "push" all characters back one
            $this->file->fseek(2 + strlen($oldDefinition), SEEK_SET);
            $restOfLine = $this->file->fgets();
            $this->file->fseek(2, SEEK_SET);
            $this->file->fwrite($newDefinition . $restOfLine);
            $this->file->rewind();
        }

        $this->itemsCount = $count;
    }

    public function all()
    {
        $this->file->fseek(0, SEEK_SET);
        $this->rewind();

        $items = [];
        while ($this->valid()) {
            $items[$this->key()] = $this->current();
            $this->next();
        }

        return $items;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        $this->rewind();
        return $this->valid() ? $this->current() : null;
    }

    public function override(array $array)
    {
        $this->file->rewind();
        $this->file->ftruncate(0);
        $this->file->fwrite(serialize($array));
        $this->rewind();
    }

    public static function createFromArray(array $array)
    {
        return self::createFromString(serialize($array));
    }

    public static function createFromString($serializedString)
    {
        $file = new \SplTempFileObject();
        $file->fwrite($serializedString);
        return new SerializedArray($file);
    }
}
