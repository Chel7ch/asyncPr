<?php

namespace Client;

use Config\Config;
use Proxy\CookingProxy;


/**
 * Writes and reads URLs of rejected pages
 */
trait LogErrorResponse
{
    public static $zeroUrl = array();

    /**
     * @param int $response
     * @param $url
     * @param string $proxy bad proxy
     */
    public function errorResponse($response, $url, $proxy = '')
    {
        if ($response != 200) {
            if ($response == 0 and CookingProxy::$firstPage > 1) {
                CookingProxy::replace($proxy);

                if (!array_search($url, self::$zeroUrl)) {
                    self::$zeroUrl[] = $url;

                    $errReq = array($url);
                    $this->writeErrResp($errReq, $url, Config::get('zeroErrRespFile'));
                }
            } elseif ($response != 0) {
                $errReq = array($response, $url);
                $this->writeErrResp($errReq, $url, Config::get('errRespFile'));
            }
        }
    }

    /**
     * @param int $errReq
     * @param string $url
     * @param string $nameFile
     */
    public function writeErrResp($errReq, $url, $nameFile)
    {
        file_exists(Config::get('logErrRespDir')) ?: mkdir(Config::get('logErrRespDir'));
        $row = array($errReq, $url);
        $fd = fopen($nameFile, 'a');
        fputcsv($fd, $row);
        fclose($fd);
    }

    /**
     * @param string $nameFile
     * @return array
     */
    public function readErrorURL($nameFile)
    {
        $list = array();

        if (file_exists($nameFile) && ($fp = fopen($nameFile, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str = htmlentities(fgets($fp));
                if ($str != '') $list[] = strstr($str, 'http');
            }
            fclose($fp);

            file_put_contents($nameFile, '');

            if ($nameFile == Config::get('zeroErrRespFile')) self::$zeroUrl = array();

            $list = str_replace(array("\r", "\n"), "", $list);
            $list = array_values(array_unique(array_diff($list, array('', 0, null))));
        }
        
        return $list;
    }
}