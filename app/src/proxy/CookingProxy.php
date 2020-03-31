<?php

namespace Proxy;

use Parser\ParserGroupPage;

class CookingProxy
{
    const ATTEMPT = 5;
    static public $attemptWork;
    static public $listProxy = array();
    static public $listP = array();
    static public $badProxy;
    static public $workProxy = array();
    static public $multiRequest;
    static public $firstPage;

    public static function cook($listProxy, $count = MULTI_REQUEST)
    {
        self::$attemptWork = 0;
        self::$badProxy = array();

        self::$listProxy = self::$listP = $listProxy;
        self::$listProxy = array_diff($listProxy, self::$badProxy);

        if (count(self::$listProxy) >= $count) self::$multiRequest = $count;
        else self::$multiRequest = count(self::$listProxy);

        self::$workProxy = array_splice(self::$listProxy, 0, self::$multiRequest);

        echo '__________cook___________________________________________<br>';
        print_r(self::$workProxy); echo '__________self::$workProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        print_r(self::$listProxy); echo '__________self::$listProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        print_r(self::$badProxy); echo '__________self::$badProxy';
        echo '<br>'. self::$multiRequest.'__________self::$multiRequest';
    }

    /**
     * @param string $badPr
     */
    public static function replace($badPr)
    {

        self::$badProxy[] = $badPr;

        $new = array_shift(self::$listProxy);
        $key = array_search($badPr, self::$workProxy);

        echo '________________________________replace___________________<br>';
        print_r(self::$workProxy[$key]);echo '__________self::$workProxy[$key]<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        print_r($new);echo '__________$new<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//        print_r(self::$listProxy);echo '__________self::$listProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//        print_r(self::$badProxy);echo '__________self::$badProxy';
        echo '<br>' . self::$attemptWork . '__________self::$attemptWork';


        if ($new) {
            self::$workProxy[$key] = $new;

        } elseif (count(self::$workProxy) > 1) {
            unset(self::$workProxy[$key]);
            self::$workProxy = array_values(self::$workProxy);
            self::$multiRequest--;
        } elseif (count(self::$workProxy) == 1 and self::$attemptWork < self::ATTEMPT) {
            self::$listProxy = self::$listP;
            self::$badProxy = array();
            self::$workProxy = array_splice(self::$listProxy, 0, self::$multiRequest);
            self::$attemptWork++;
        } elseif (count(self::$workProxy) == 1) {
            self::$workProxy = (string)'';
        }
    }
}