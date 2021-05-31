<?php

include "vendor/autoload.php";
include "XML2Array.php";
include "class.Connection.php";
include "class.RegexHelper.php";


for ($x = 1; $x <= 39; $x++) {
    $connection = new Connection("https://kvlrap.wordpress.com/feed/atom/?post_type=post&paged=");
    $body = $connection->getBodyWithPage($x);
    try {
        $arr = xmlToArray(simplexml_load_string($body));
        foreach ($arr["feed"]["entry"] as $value) {
            $title = $value["title"]["$"];
            if (!is_dir(__DIR__."/datas/page_".$x)){
                mkdir(__DIR__."/datas/page_".$x);
            }
            $fileName = __DIR__."/datas/page_".$x."/" . $title . ".json";
            touch($fileName);
            file_put_contents($fileName, json_encode($value));
            echo $title." - yazdırıldı \n";
        }
    } catch (Exception $e) {
        echo $e;
    }
}

$datasFolder = array_diff(scandir(__DIR__."/datas"), array('.', '..', '.DS_Store'));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__."/datas/" . $folder), array('.', '..', ".DS_Store"));
    foreach ($files as $file) {
        array_push($filesArray,__DIR__. "/datas/" . $folder . "/" . $file);
    }
}

echo "Comment time! \n";
foreach ($filesArray as $fileName) {
    $file = json_decode(file_get_contents($fileName), true);
    $justId = explode("=", $file["id"])[1];
    $justUrl = explode("=", $file["id"])[0];
    $connection = new Connection($justUrl . "=");
    $pageBody = $connection->getBodyWithPage($justId);
    $myRegexHelper = new RegexHelper($pageBody);
    $normalizedComment = $myRegexHelper->normalizeComment();

    $file["comments"] = $normalizedComment;
    file_put_contents($fileName, json_encode($file));
    echo "comment yazdırıldı. -> " . $fileName . "\n";


    $forDebug = $normalizedComment;
}
