<?php


class FlatFileDatabaseHelper
{
    private $flatBase;

    public function __construct()
    {
        $storage = new Flatbase\Storage\Filesystem(__DIR__ . "/db");
        $flatBase = new Flatbase\Flatbase($storage);
        $this->flatBase = $flatBase;
    }

    public function addNewRow($arr)
    {
        $this->flatBase->insert()->in('forAnalyze')->set([$arr])->execute();
    }


    public function getAllRows(): \Flatbase\Collection
    {
        return $this->flatBase->read()->in('forAnalyze')->get();
    }

    public function getAllRowsWithSort($sortField){
        return $this->flatBase->read()->in('forAnalyze')->sort($sortField)->get();
    }
}