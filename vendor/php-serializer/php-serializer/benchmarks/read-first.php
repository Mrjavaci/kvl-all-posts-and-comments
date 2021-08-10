<?php

require __DIR__ . '/../vendor/autoload.php';

$limit = 1;
$controlSize = 3000000;
function getControlData()
{
    global $controlSize;
    $a = [];
    for ($i=0; $i<=$controlSize; $i++) {
        $a[] = [
            md5($i),
        ];
    }
    return serialize($a);
}

echo "Building control data\n\n";
$string = getControlData();

// Oldskool PHP
echo "Benchmarking reads using PHPSerializer::first()\n";
$bench = new Ubench;
$bench->start();

$array = \PHPSerializer\SerializedArray::createFromString($string);
$item = $array->first();

$bench->end();

echo "- Read the first item from a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";

echo "\n";

// PHPSerializer
echo "Benchmarking reads using unserialize[] \n";
$bench = new Ubench;
$bench->start();

$array = unserialize($string);
$item = $array[0];

$bench->end();
echo "- Read the first item from a data set of " . $controlSize . " in " . $bench->getTime() . ", with a memory peack of " . $bench->getMemoryPeak() . "\n";

echo "\n";