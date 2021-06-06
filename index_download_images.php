<?php

include "vendor/autoload.php";
include "XML2Array.php";
include "class.Connection.php";
include "class.RegexHelper.php";

echo "Images Time! \n";

$datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store'));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store"));
    foreach ($files as $file) {
        array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
    }
}

foreach ($filesArray as $fileName) {
    $file = json_decode(file_get_contents($fileName), true);
    $content = $file["content"]["$"];
    $regexHelper = new RegexHelper($content);
    $imagesArray = $regexHelper->getImagesArray();
    $connection = new Connection(null);
    $connection->downloadAllImagesWithFolders($imagesArray);
    $forDebug = "";
}