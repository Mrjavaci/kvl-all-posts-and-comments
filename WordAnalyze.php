<?php
const SaveData = false;
$rustart = getrusage();

include "vendor/autoload.php";
include "WordHelper.php";
$startMemory = memory_get_usage();
$myHelper = new WordHelper(true);
$mostUsedWords = $myHelper->getMostUsedWords();
$mostUsedWords = array_slice($mostUsedWords, 0, 999);
$table = "";
$i = 0;
foreach ($mostUsedWords as $key => $value) {
    $i++;
    $table .= "<tr><td>" . $i . "</td><td>" . $key . "</td><td>" . $value . "</td></tr>";

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
    table.darkTable {
        font-family: "Arial Black", Gadget, sans-serif;
        border: 2px solid #000000;
        background-color: #4A4A4A;
        width: 40%;
        text-align: center;
        border-collapse: collapse;
    }

    table.darkTable td, table.darkTable th {
        border: 1px solid #4A4A4A;
        padding: 3px 3px;
    }

    table.darkTable tbody td {
        font-size: 13px;
        color: #E6E6E6;
    }

    table.darkTable tr:nth-child(even) {
        background: #888888;
    }

    table.darkTable thead {
        background: #000000;
        border-bottom: 3px solid #000000;
    }

    table.darkTable thead th {
        font-size: 15px;
        color: #E6E6E6;
        text-align: center;
        border-left: 2px solid #4A4A4A;
    }

    table.darkTable thead th:first-child {
        border-left: none;
    }

    table.darkTable tfoot {
        font-size: 12px;
        color: #E6E6E6;
        background: #000000;
        background: -moz-linear-gradient(top, #404040 0%, #191919 66%, #000000 100%);
        background: -webkit-linear-gradient(top, #404040 0%, #191919 66%, #000000 100%);
        background: linear-gradient(to bottom, #404040 0%, #191919 66%, #000000 100%);
        57 rb
        border-top: 1px solid #4A4A4A;
    }

    table.darkTable tfoot td {
        font-size: 12px;
    }
</style>
<table class="darkTable">
    <thead>
    <tr>
        <th>Order</th>
        <th>Word</th>
        <th>Used Count</th>
    </tr>
    </thead>
    <tbody>
    <?= $table ?>
    </tbody>
</table>

</body>
</html>


