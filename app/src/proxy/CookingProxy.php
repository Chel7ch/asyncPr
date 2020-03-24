<?php

namespace Proxy;

use Parser\ParserGroupPage;

class CookingProxy
{
    static public $listProxy = array();
    static public $badProxy = array();
    static public $workProxy = array();
    static  public $multiRequest;
    private $conn;

    public function cook($listProxy, $count = MULTI_REQUEST)
    {
        self::$listProxy = $listProxy;

        if (count(self::$listProxy) >= $count) self::$multiRequest = $count;
        else self::$multiRequest = count(self::$listProxy);

        self::$workProxy = array_splice(self::$listProxy, 0, self::$multiRequest);

//        return self::$workProxy;
    }

    public function replace($oldProxy, $badPr)
    {
        self::$badProxy[] = $badPr;
        $new = array_shift(self::$listProxy);
        $key = array_search($badPr, $oldProxy);

        if ($new) {
            $oldProxy[$key] = $new;
        } else {
            $listProxy = $this->selectProxy();
            self::$listProxy = array_diff($listProxy, self::$badProxy, $oldProxy);

            // если менять плохой прокси нечем, то уменьшаем группу прокси на 1
            if (empty(self::$listProxy)) {
                unset($oldProxy[$key]);
                $oldProxy = array_values($oldProxy);
                self::$multiRequest--;
            }
            // если проксей не осталось , то отключаем прокси
            if (count($oldProxy) == 0) {
                $oldProxy = (string)'';
                self::$multiRequest = 1;
            }
        }

        return $oldProxy;
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** SelectDB
     * @return array
     */
    public function selectProxy()
    {
        $query = 'SELECT  field1 FROM  check_proxy';
        return $this->conn->execSelect($query);
    }

}