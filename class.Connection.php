<?php


class Connection
{
    private $url;
    private $client;

    public function __construct($url)
    {
        $this->url = $url;
        $this->client = new GuzzleHttp\Client();
    }

    public function getBodyWithPage($page)
    {
        $res = $this->client->request('GET', $this->url . $page);
        return (string)$res->getBody();
    }

    public function downloadAllImagesWithFolders($imagesArray)
    {
        foreach ($imagesArray as $imageUrl) {
            $explodedArray = explode("/", $imageUrl);
            print_r($explodedArray);
            array_shift($explodedArray);
            array_shift($explodedArray);
            array_shift($explodedArray);
            if ($explodedArray[0] != null or $explodedArray[1] != null or $explodedArray[2] != null) {


                $this->ifNotFolderCreateFolder(__DIR__ . "/datas/images/" . $explodedArray[0]);
                $this->ifNotFolderCreateFolder(__DIR__ . "/datas/images/" . $explodedArray[0] . "/" . $explodedArray[1]);
                $this->client->request('GET', $imageUrl, ['sink' => __DIR__ . "/datas/images/" . $explodedArray[0] . "/" . $explodedArray[1] . "/" . $explodedArray[2]]);
            }else{
                echo "catch -> ". $imageUrl;
            }
        }

    }


    public
    function ifNotFolderCreateFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

}