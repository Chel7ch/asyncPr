<?php

namespace Client;

use Parser\SpiderGroup;
use Proxy\CookingProxy;

trait ErrResp
{
    public function errResp($response, $url, $proxy='')
    {
        if ($response != 200) {
            if ($response == 0 and CookingProxy::$firstPage != 0) {
                CookingProxy::replace($proxy);

                $errReq = array($response, $url);
                file_exists(PROJECT_DIR . '/logs') ?: mkdir(PROJECT_DIR . '/logs');
                $fd = fopen(ERR_RESP_FILE, 'a');
                fputcsv($fd, $errReq);
                fclose($fd);
            }
            if ($response > 0) {

            }
        }
    }
}
