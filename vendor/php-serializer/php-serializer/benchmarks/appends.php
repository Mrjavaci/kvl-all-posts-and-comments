<?php

require __DIR__ . '/../vendor/autoload.php';

$limitAppends = 2000;
$controlSize = 5000;
function getControlData()
{
    global $controlSize;
    $a = [];
    for ($i=0; $i<=$controlSize; $i++) {
        $a[] = $i;
    }
    return serialize($a);
}

echo "Building control data\n\n";
file_put_contents(__DIR__ . '/data/appends_data.serialized', getControlData());

// Oldskool PHP
echo "Benchmarking appends by unserialize(), array_push(), and serialize()\n";
$bench = new Ubench;
$bench->start();

$data = getControlData();
for ($i=0; $i<=$limitAppends; $i++) {
    $un = unserialize($data);
    array_push($un, $i);
    $data = serialize($un);
}

$bench->end();
echo "- Counting items in array: " . count(unserialize($data)) . " items\n";
unset($data);
unset($un);
echo "- Performed " . $limitAppends . " appends to a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";



// Oldskool PHP - From file
echo "Benchmarking file based appends by unserialize(), array_push(), and serialize()\n";
file_put_contents(__DIR__ . '/data/appends_data-2.serialized', file_get_contents(__DIR__ . '/data/appends_data.serialized'));
$bench = new Ubench;
$bench->start();

$file = new SplFileObject(__DIR__ . '/data/appends_data-2.serialized');
for ($i=0; $i<=$limitAppends; $i++) {
    $file->rewind();
    $un = unserialize($file->fgets());
    array_push($un, $i);
    $file->ftruncate(0);
    $file->fwrite(serialize($un));
}

$bench->end();
$file->rewind();
echo "- Counting items in array: " . count(unserialize($file->fgets())) . " items\n";
unset($data);
unset($un);
unset($file);
echo "- Performed " . $limitAppends . " appends to a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";

echo "\n";

// PHPSerializer
echo "Benchmarking appends PHPSerializer\\SerializeArray::append()\n";
$bench = new Ubench;
$bench->start();

$array = \PHPSerializer\SerializedArray::createFromString(getControlData());
for ($i=0; $i<=$limitAppends; $i++) {
    $array->append($i);
}

$bench->end();
echo "- Counting items in array: " . $array->count() . " items\n";
unset($array);
echo "- Performed " . $limitAppends . " appends to a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";


// PHPSerializer
echo "Benchmarking file based appends PHPSerializer\\SerializeArray::append()\n";
file_put_contents(__DIR__ . '/data/appends_data-3.serialized', file_get_contents(__DIR__ . '/data/appends_data.serialized'));
$bench = new Ubench;
$bench->start();

$array = new \PHPSerializer\SerializedArray($file = new SplFileObject(__DIR__ . '/data/appends_data-3.serialized'));
for ($i=0; $i<=$limitAppends; $i++) {
    $array->append($i);
}

$bench->end();
echo "- Counting items in array: " . $array->count() . " items\n";
echo "- Performed " . $limitAppends . " appends to a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";

echo "\n";