<?php

namespace Client;

use Parser\SpiderGroup;
use Proxy\CookingProxy;

trait ErrResp
{
    public static $zeroUrl = array();

    public function errResp($response, $url, $proxy = '')
    {
        if ($response != 200) {
            if ($response == 0 and CookingProxy::$firstPage != 0) {
                CookingProxy::replace($proxy);

                if (!array_search($url, self::$zeroUrl)) {
                    self::$zeroUrl[] = $url;

                    $errReq = array($url);
                    $this->writeErrResp($errReq, ZERO_ERR_RESP_FILE);

                }
            } else {
                $errReq = array($response, $url);
                $this->writeErrResp($errReq, ERR_RESP_FILE);
            }
        }
    }

    public function writeErrResp($errReq, $nameFile)
    {
        file_exists(PROJECT_DIR . '/logs') ?: mkdir(PROJECT_DIR . '/logs');
        $fd = fopen($nameFile, 'a');
        fputcsv($fd, $errReq);
        fclose($fd);
    }

    public function readErrorURL($nameFile)//!!!!!!!!!!!!!
    {
        $list =array();
        if (file_exists($nameFile) && ($fp = fopen($nameFile, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str = htmlentities(fgets($fp));
                if ($str != '') $list[] = strstr($str, 'http');
            }
            fclose($fp);
//            file_put_contents($nameFile, '');

//            $list = str_replace(array("\r","\n"),"",$list);
            $list = array_values(array_unique(array_diff($list, array('', 0, null))));

            return $list;
        }
    }
}
