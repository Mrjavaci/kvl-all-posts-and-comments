<?php


class RegexHelper
{
    public $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function getImagesArray()
    {
        $re = '/<img(.*)\/>/m';
        preg_match_all($re, $this->body, $matches, PREG_SET_ORDER, 0);
        $reArray = array();
        foreach ($matches as $match) {
            $regex = '/data-orig-file="(.*)"/U';
            preg_match_all($regex, $match[0], $matchesImagesUrls, PREG_SET_ORDER, 0);
            array_push($reArray, $matchesImagesUrls[0][1]);
        }
        return $reArray;
    }


    public function normalizeComment(): array
    {
        $returnArray = array();
        $returnArray["commentCount"] = $this->getCommentCount();
        $returnArray["comments"] = $this->getComments();
        return $returnArray;
    }

    private function getCommentCount()
    {
        $re = '/üzerine (.*?) yorum/m';
        preg_match_all($re, $this->body, $matches, PREG_SET_ORDER, 0);
        if (@$matches[0][1] != null) {
            return $matches[0][1];
        }
        return null;
    }

    private function getComments()
    {
        $re = '/<li\s*class="comment\s*(.*?)<\/li>/ms';
        preg_match_all($re, $this->body, $matches, PREG_SET_ORDER, 0);
        $comments = array();
        foreach ($matches as $match) {
            $tempArray = array();
            $realMatch = $match[0];

            $imgUrlRegex = "/src=\'(.*?)\'/ms";
            preg_match_all($imgUrlRegex, $realMatch, $matchesImgUrl, PREG_SET_ORDER, 0);
            $imgUrl = $matchesImgUrl[0][1];
            $tempArray["imgUrl"] = $imgUrl;

            $commenterNameRegex = '/<cite class="fn">(.*?)<\/cite>/ms';
            preg_match_all($commenterNameRegex, $realMatch, $matchesCommenterName, PREG_SET_ORDER, 0);
            if (strpos($matchesCommenterName[0][1], "<a") !== false) {
                $commenterNameWithaRegex = '/>(.*?)<\/a>/ms';
                preg_match_all($commenterNameWithaRegex, $matchesCommenterName[0][1], $matchesCommenterNameWithA, PREG_SET_ORDER, 0);
                $tempArray["commenterName"] = $matchesCommenterNameWithA[0][1];
            } else {
                $tempArray["commenterName"] = $matchesCommenterName[0][1];
            }

            $dateTimeRegex = '/datetime="(.*?)">/ms';
            preg_match_all($dateTimeRegex, $realMatch, $matchesDateTime, PREG_SET_ORDER, 0);
            $tempArray["dateTime"] = $matchesDateTime[0][1];

            $commetBodyRegex = '/<div class="comment-content"><p>(.*?)<\/p>/ms';
            preg_match_all($commetBodyRegex, $realMatch, $commentBodyMatches, PREG_SET_ORDER, 0);
            $tempArray["commentBody"] = $commentBodyMatches[0][1];


            $commentLikeCountRegex = '/data-like-count="(.*?)"/ms';
            preg_match_all($commentLikeCountRegex, $realMatch, $commentLikeCountRegex, PREG_SET_ORDER, 0);
            $tempArray["commentLikeCount"] = $commentLikeCountRegex[0][1];


            array_push($comments, $tempArray);
        }
        return $comments;
    }
}