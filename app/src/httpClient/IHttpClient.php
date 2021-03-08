<?php
namespace Client;


interface IHttpClient{
    public function getPage($page);
    public function postPage($page,$postData);
}

