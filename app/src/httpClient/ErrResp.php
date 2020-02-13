<?php
namespace Client;

trait ErrResp
{
    public function errResp($response, $page)
    {
        if ($response > 399) {
            $errReq = array($response, $page);
            file_exists(PROJECT_DIR. '/logs')? :mkdir(PROJECT_DIR. '/logs');
            $fd = fopen(ERR_RESP_FILE, 'a');
            fputcsv($fd, $errReq);
            fclose($fd);
        }
    }
}