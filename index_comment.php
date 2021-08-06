<?php

include "vendor/autoload.php";
include "Helpers/XML2Array.php";
include "Helpers/class.Connection.php";
include "Helpers/class.RegexHelper.php";

$datasFolder = array_diff(scandir("datas"), array('.', '..', '.DS_Store'));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir("datas/" . $folder), array('.', '..', ".DS_Store"));
    foreach ($files as $file) {
        array_push($filesArray, "datas/" . $folder . "/" . $file);
    }
}


foreach ($filesArray as $fileName) {
    $file = json_decode(file_get_contents($fileName), true);
    $justId = explode("=", $file["id"])[1];
    $justUrl = explode("=", $file["id"])[0];
    $connection = new Connection($justUrl . "=");
    $pageBody = $connection->getBodyWithPage($justId);
    $myRegexHelper = new RegexHelper($pageBody);
    $normalizedComment = $myRegexHelper->normalizeComment();

    if (!$normalizedComment["commentCount"] != null or !$normalizedComment != 0) {
        $file["comments"] = $normalizedComment;
        file_put_contents($fileName . "-comment", json_encode($file));
    }
    $forDebug = $normalizedComment;
}
