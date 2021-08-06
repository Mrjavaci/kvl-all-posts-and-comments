<html lang='tr'>
<body>

<?php
require_once "../vendor/autoload.php";
$datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store', "images"));
$filesArray = array();
foreach ($datasFolder as $folder) {
    $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store", "images"));
    foreach ($files as $file) {
        array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
    }
}
$isChangeImageFolder = false;
$enlilArray = array();

foreach ($filesArray as $fileName) {
    $re = '/Bölüm-(.*?)\).json/m';
    preg_match_all($re, $fileName, $matches, PREG_SET_ORDER, 0);
    if (@$matches[0][1] != null) {
        array_push($enlilArray, array("name" => $fileName, "number" => $matches[0][1]));
    }
}

usort($enlilArray, function ($a, $b) {
    return $a["number"] <=> $b["number"];
});

//print_r($enlilArray);
$contents = array();
foreach ($enlilArray as $value) {
    $json = file_get_contents($value["name"]);
    $json = json_decode($json, true);
    if ($isChangeImageFolder) {
        $content = $json["content"]["$"];
        preg_match_all('/src=\"(.*?)\"/m', $content, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            $exp = explode("/", $match[1]);
            $exp2 = explode("?", $exp[(count($exp) - 3)] . "/" . $exp[(count($exp) - 2)] . "/" . $exp[(count($exp) - 1)]);
            $replace = __DIR__ . "/datas/images/" . $exp2[0];
            $content = str_replace($match[1], $replace, $content);
        }
        $json["content"]["$"] = $content;
        file_put_contents($value["name"], json_encode($json));
    } else {
        echo "<hr>";
        echo $json["title"]["$"] . "<br>\n";
        echo $json["content"]["$"];
        array_push($contents, $json["content"]["$"]);
        echo "<hr>";
    }

}

$html = implode("<hr>",$contents);
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
//$dompdf->render();

// Output the generated PDF to Browser
//$dompdf->stream();
//file_put_contents("./pdf.pdf", $dompdf->output());

?>
</body>
</html>
