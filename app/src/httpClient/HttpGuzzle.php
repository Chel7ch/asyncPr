<?php

namespace Client;

use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Getting  page and pulling out all references from it and benefit data
 */
class HttpGuzzle implements IHttpClient
{
    /**plug-in traits */
    use ErrResp, SaveHTMLPage;

    /** @var string URL
     * @var array header of request
     * @var object client of Guzzle
     */
    public $url;
    public $header;
    private $client;

    /** Create a new object of client Guzzle
     * @param string $url
     * @param array $header
     */
    public function __construct($url, $header)
    {
        $this->url = $url;
        $this->client = new Client([
            'verify' => false,
            'timeout' => 120.0,
            'cookies' => true,
            'headers' => $header
        ]);
    }

    /** Get HTML of page
     * @param string $page URL of HTML page
     * @return Document
     * @uses ErrResp, CleanLinks, CleanLinks traits
     */
    public function getPage($page)
    {
        $document = '';
        try {
            $acceptDoc = $this->client->request('GET', $page);

            /** if response > 399 writing in the error file */
            $this->errResp(http_response_code(), $page);

            $content = $acceptDoc->getBody()->getContents();
        } catch (RequestException $e) {
        }

        if (isset($content)) {
            $document = new Document($content);
            $this->saveHTMLPage($document, $page);
        }

        return $document;
    }


    public function postPage($page, $postData)
    {

    }

    public function close()
    {

    }

}