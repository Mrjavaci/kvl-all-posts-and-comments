<?php
const SaveData = false;

$rustart = getrusage();

include "vendor/autoload.php";
include "WordHelper.php";

$myHelper = new WordHelper();
$mostUsedWords = $myHelper->getMostUsedWords();
print_r($mostUsedWords);

$ru = getrusage();
function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
        - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

echo " Bu çıktı " . rutime($ru, $rustart, "utime") .
    " ms sürdü.\n";

