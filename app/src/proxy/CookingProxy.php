<?php

namespace Proxy;

use Parser\ParserGroupPage;

class CookingProxy
{
    static public $listProxy = array();
    static public $badProxy = array();
    static public $workProxy = array();
    static public $multiRequest;
    static public $firstPage;
    private $conn;

    public static function cook($listProxy, $count = MULTI_REQUEST)
    {
        self::$listProxy = array_diff($listProxy, self::$badProxy);

        if (count(self::$listProxy) >= $count) self::$multiRequest = $count;
        else self::$multiRequest = count(self::$listProxy);

        self::$workProxy = array_splice(self::$listProxy, 0, self::$multiRequest);

    }

    /**
     * @param string $badPr
     */
    public static function replace($badPr)
    {
        self::$badProxy[] = $badPr;
        $new = array_shift(self::$listProxy);
        $key = array_search($badPr, self::$workProxy);

//        echo $badPr . '$badPr<br>';
//        print_r(self::$badProxy);
//        echo '$badProxy<br>';
//        print_r($new);
//        echo '$new<br>';
//        print_r(self::$listProxy);
//        echo '$listProxy<br>';
//        print_r($key);
//        echo 'key++++++++<br>';

        if ($new) {
            self::$workProxy[$key] = $new;

        } elseif (count(self::$workProxy) > 1) {
            unset(self::$workProxy[$key]);
            self::$workProxy = array_values(self::$workProxy);
            self::$multiRequest--;
        } elseif (count(self::$workProxy) == 1) {
            self::$workProxy = (string)'';
        }
    }
}