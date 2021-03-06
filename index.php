<?php

include "vendor/autoload.php";
include "Helpers/XML2Array.php";
include "Helpers/class.Connection.php";
include "Helpers/class.RegexHelper.php";


for ($x = 1; $x <= 39; $x++) {
    $connection = new Connection("https://kvlrap.wordpress.com/feed/atom/?post_type=post&paged=");
    $body = $connection->getBodyWithPage($x);
    try {
        $xmlToArray = new XML2Array(simplexml_load_string($body));
        $arr = $xmlToArray->xmlToArray();
        foreach ($arr["feed"]["entry"] as $value) {
            $title = $value["title"]["$"];
            if (!is_dir(__DIR__ . "/datas/page_" . $x)) {
                mkdir(__DIR__ . "/datas/page_" . $x);
            }
            $fileName = __DIR__ . "/datas/page_" . $x . "/" . $title . ".json";
            touch($fileName);
            file_put_contents($fileName, json_encode($value, JSON_PRETTY_PRINT));
            echo $title . " - yazdırıldı \n";
        }
    } catch (Exception $e) {
        echo $e;
    }
}

$datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store', "images"));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store", "images"));
    foreach ($files as $file) {
        array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
    }
}

echo "Comment time! \n";
foreach ($filesArray as $fileName) {
    $file = json_decode(file_get_contents($fileName), true);
    $justId = explode("=", $file["id"])[1];
    if ($justId == null or $justId == 0) {
        echo "error For debug";
        return;
    }
    $justUrl = explode("=", $file["id"])[0];
    $connection = new Connection($justUrl . "=");
    $pageBody = $connection->getBodyWithPage($justId);
    $myRegexHelper = new RegexHelper($pageBody);
    $normalizedComment = $myRegexHelper->normalizeComment();

    $file["comments"] = $normalizedComment;
    file_put_contents($fileName, json_encode($file, JSON_PRETTY_PRINT));
    echo "comment yazdırıldı. -> " . $fileName . "\n";
    $forDebug = $normalizedComment;
}
