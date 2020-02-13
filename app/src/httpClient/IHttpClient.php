<?php
namespace Client;

interface IHttpClient{
    public function __construct($startPage, $options);
    public function getPage($page);
    public function postPage($page,$postData);
    public function close();
}

