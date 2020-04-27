<?php

namespace Proxy;

use Config\Config;

class CookingProxy
{
    const ATTEMPT = 5;
    static private $attemptWork;
    static private $listProxy = array();
    static public $listPr = array();
    static private $badProxy;
    static private $workProxy = array();
    static private $multiRequest;
    static public $firstPage;

    /**
     * preparing array workProxy and setup multiRequest
     * @param int $count
     */
    public static function cooking($count)
    {
        self::$attemptWork = 0;
        self::$badProxy = array();
        self::$listProxy = self::$listPr;

        if (count(self::$listProxy) >= $count) Config::set('multiRequest', $count);
        else Config::set('multiRequest', count(self::$listProxy));

        self::$multiRequest = Config::get('multiRequest');

        self::$workProxy = array_splice(self::$listProxy, 0, Config::get('multiRequest'));
        Config::set('workProxy', self::$workProxy);
    }

    /**
     * replace bad proxy
     * @param string $badProxy
     */
    public static function replace($badProxy)
    {
        if (empty($badProxy)) exit('CookingProxy: incoming data is invalid');

        self::$badProxy[] = $badProxy;

        $new = array_shift(self::$listProxy);
        $key = array_search($badProxy, self::$workProxy);

        if ($new) {
            self::$workProxy[$key] = $new;

        } elseif (count(self::$workProxy) > 1) {
            unset(self::$workProxy[$key]);
            self::$workProxy = array_values(self::$workProxy);
            Config::set('multiRequest', Config::get('multiRequest') - 1);

        } elseif (count(self::$workProxy) == 1 and self::$attemptWork < self::ATTEMPT) {
            self::$listProxy = self::$listPr;
            self::$badProxy = array();
            Config::set('multiRequest', self::$multiRequest);
            self::$workProxy = array_splice(self::$listProxy, 0, Config::get('multiRequest'));
            self::$attemptWork++;

        } elseif (count(self::$workProxy) == 1) {
            self::$workProxy = (string)'';
        }
        Config::set('workProxy', self::$workProxy);
    }

    /** get list workProxy from the file */
    static function getList()
    {
        if (file_exists(Config::get('goodProxyFile'))) {
            $list = file_get_contents(Config::get('goodProxyFile'));
            $list = explode(' ', str_replace(array("\r", "\n"), " ", $list));
            self::$listPr = array_diff($list, array('', 0, null));
        }
    }

}