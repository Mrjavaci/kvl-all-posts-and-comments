<?php
const Kelime = "";

require_once "vendor/autoload.php";

$datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store', "images"));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store", "images"));
    foreach ($files as $file) {
        array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
    }
}


foreach ($filesArray as $fileName) {
    $json = json_decode(file_get_contents($fileName), true);
    $comments = $json["comments"]["comments"];
    foreach ($comments as $comment) {
        if (str_contains(mb_strtolower($comment["commenterName"]), Kelime)) {
            echo $comment["commenterName"] . " - " . $json["link"][0]["@href"] . "\n";
        }
    }
}
