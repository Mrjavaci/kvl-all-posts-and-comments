<?php
const SaveData = false;

$rustart = getrusage();

include "vendor/autoload.php";
include "WordHelper.php";

$myHelper = new WordHelper();
$mostUsedWords = $myHelper->getMostUsedWords();
$mostUsedWords = array_slice($mostUsedWords, 0, 500);
$table = "";
$i = 0;
foreach ($mostUsedWords as $key => $value) {
    $i++;
    $table .= "<tr><td class=\"tg-0lax\">" . $i . "</td><td class=\"tg-0lax\">" . $key . "</td><td class=\"tg-0lax\">" . $value . "</td></tr>";

}


function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
        - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

$ru = getrusage();
$time = " Bu çıktı " . rutime($ru, $rustart, "utime") . " ms sürdü.\n";

?>

<html lang="tr">
<head>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
    <title></title>
</head>
<body>
<?= $time ?>

<style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .tg td {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg th {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg .tg-baqh {
        text-align: center;
        vertical-align: top
    }

    .tg .tg-0lax {
        text-align: left;
        vertical-align: top
    }
</style>
<table class="tg" style="undefined;table-layout: fixed; width: 574px">
    <colgroup>
        <col style="width: 211px">
        <col style="width: 219px">
        <col style="width: 144px">
    </colgroup>
    <thead>
    <tr>
        <th class="tg-baqh">Order</th>
        <th class="tg-0lax">Word</th>
        <th class="tg-0lax">Count</th>
    </tr>
    </thead>
    <tbody>
    <?= $table ?>
    </tbody>
</table>

</body>
</html>


