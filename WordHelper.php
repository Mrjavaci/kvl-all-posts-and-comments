<?php

class WordHelper
{
    private $includeComments;
    public $allWords;
    public $illegalChars = array("\xc2\xa0", "\xe2\x80\x93", "\n", "\r", "\"", "”", "“", "&nbsp;", " ", "/", "2", ";", ")", "(");

    public function __construct($includeComments = false)
    {
        $this->includeComments = $includeComments;
        $this->allWords = $this->getAllWords();
    }

    public function getAllWords()
    {
        $filePaths = $this->getAllFilePaths();
        $retArray = array();
        $i = 0;
        foreach ($filePaths as $fileName) {
            $json = json_decode(file_get_contents($fileName), true);
            $content = $json["content"]["$"];
            if ($this->includeComments) {
                foreach ($json["comments"]["comments"] as $comment) {
                    $content .= $comment["commentBody"];
                }
            }
            $content = $this->removeHtmlTags($content);
            $ex = explode(" ", $content);
            foreach ($ex as $str) {
                $str = mb_strtolower($str);
                $exForDots = explode(".", $str);
                $exForComma = explode(",", $str);
                $count = count($exForDots);
                if (count($exForDots) > 1) {
                    foreach ($exForDots as $dots) {
                        $dots = str_replace($this->illegalChars, '', $dots);
                        if ($dots != "" or $dots != null) {
                            array_push($retArray, $dots);
                            $i++;

                        }
                    }
                } else if (count($exForComma) > 1) {
                    foreach ($exForComma as $commas) {
                        $commas = str_replace($this->illegalChars, '', $commas);
                        if ($commas != "" or $commas != null or $commas != " ") {
                            array_push($retArray, $commas);
                            $i++;
                        }
                    }
                } else {
                    $str = str_replace($this->illegalChars, '', $str);
                    if ($str != "" or $str != null or $str != " " or !ctype_space($str)) {
                        array_push($retArray, $str);
                        $i++;

                    }
                }
            }
        }
        $retArray = $this->normalizeArray($retArray);
        $this->saveData($retArray);
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

    private function normalizeArray(array $retArray): array
    {
        $arr = array();
        foreach ($retArray as $value) {
            if ($value != "") {
                array_push($arr, $value);
            }
        }
        return $arr;
    }

    private function saveData(array $retArray)
    {
        if (SaveData) {
            file_put_contents("AllWords.json", json_encode($retArray));
        }
    }

    public function getMostUsedWords()
    {
        $array_count_values = array_count_values($this->allWords);
        arsort($array_count_values);
        return $array_count_values;
    }
}

//292424
//109 ms
//501 ms