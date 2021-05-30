<?php


class Connection
{
    private $url;
    public function __construct($url)
    {
        $this->url = $url;
    }
    public function getBodyWithPage($page){
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $this->url . $page);
        return  (string)$res->getBody();

    }

}