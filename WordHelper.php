<?php

class WordHelper
{
    private $includeComments;

    public function __construct($includeComments = false)
    {
        $this->includeComments = $includeComments;
    }

    public function getAllWords()
    {
        $filePaths = $this->getAllFilePaths();
        $retArray = array();
        $i = 0;
        foreach ($filePaths as $fileName) {
            $json = json_decode(file_get_contents($fileName), true);
            $content = $json["content"]["$"];
            $content = $this->removeHtmlTags($content);
            $ex = explode(" ", $content);
            foreach ($ex as $str) {
                $exForDots = explode(".", $str);
                $exForComma = explode(",", $str);
                $count = count($exForDots);
                if ($i == 342671) {
                    echo "a";
                }
                if (count($exForDots) > 1) {
                    foreach ($exForDots as $dots) {
                        $dots = str_replace(array("\n", "\r", "\""), '', $dots);
                        if ($dots != "" or $dots != null or $dots != " " or !ctype_space($str)) {
                            //              $dots = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $dots);
                            array_push($retArray, $dots);
                            $i++;

                        }
                    }
                } else if (count($exForComma) > 1) {
                    foreach ($exForComma as $commas) {
                        $commas = str_replace(array("\n", "\r", "\"", "”"), '', $commas);
                        if ($commas != "" or $commas != null or $commas != " " or !ctype_space($str)) {
                            //            $commas = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $commas);
                            array_push($retArray, $commas);
                            $i++;

                        }
                    }
                } else {
                    $i++;

                    $str = str_replace(array("\n", "\r", "\"", "”"), '', $str);
                    //       $str = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $str);
                    $str = str_replace(array("\n", "\r"), '', $str);
                    if ($str != "" or $str != null or $str != " " or !ctype_space($str))
                        array_push($retArray, $str);
                }
            }
        }
        print_r($retArray);
        echo "\$i->>>" . $i . "\n";
        return $retArray;
    }

    private function getAllFilePaths(): array
    {
        $datasFolder = array_diff(scandir(__DIR__ . "/datas"), array('.', '..', '.DS_Store', "images"));
        $filesArray = array();
        foreach ($datasFolder as $folder) {
            $files = array_diff(scandir(__DIR__ . "/datas/" . $folder), array('.', '..', ".DS_Store", "images"));
            foreach ($files as $file) {
                array_push($filesArray, __DIR__ . "/datas/" . $folder . "/" . $file);
            }
        }
        return $filesArray;
    }

    private function removeHtmlTags(string $str): string
    {
        return strip_tags($str);
    }
}

//292424
//109 ms
//501 ms