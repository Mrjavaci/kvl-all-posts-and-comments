<?php

require_once "vendor/autoload.php";
require_once "FlatFileDatabaseHelper.php";

$isAddData = false;


$datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store', "images"));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store", "images"));
    foreach ($files as $file) {
        array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
    }
}
$flatHelper = new FlatFileDatabaseHelper();
if ($isAddData) {
    foreach ($filesArray as $fileName) {
        $file = json_decode(file_get_contents($fileName), true);

        $title = $file["title"]["$"];
        $updated = $file["updated"];
        $published = $file["published"];

        $flatHelper->addNewRow(array(
            "title" => $title,
            "updated" => strtotime($updated),
            "published" => strtotime($published),
        ));


    }
}


$rows = $flatHelper->getAllRows();
//sort için, flatbase sort işlemi çalışmıyor.
$castArr = (array)$rows;
usort($castArr, function ($a, $b) {
    return $a[0]['published'] <=> $b[0]['published'];
});

$rows = $castArr;
$i = 1;
foreach ($rows as $row) {
    $publishedDate = \Carbon\Carbon::parse($rows[$i][0]["published"]);
    $lastPublishedDate =  \Carbon\Carbon::parse($rows[($i - 1)][0]["published"]);
    echo $row[0]["title"] . " bir önceki posttan " .$publishedDate->locale("tr_TR")->diffForHumans($lastPublishedDate). " yayınlandı \n";
    $i++;
}