<?php
namespace Parser;

trait RepeatErrResp{

    public function readErrorURL()
    {
        $list =array();
        if (file_exists(ERR_RESP_FILE) && ($fp = fopen(ERR_RESP_FILE, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str = htmlentities(fgets($fp));
                if ($str != '') $list[] = strstr($str, 'http');
            }
            fclose($fp);
            file_put_contents(ERR_RESP_FILE, '');
            $list = array_values(array_unique($list));

            return $list;
        }
    }
}
