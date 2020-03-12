<?php

namespace Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;

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
     * @param string $proxy
     * @return Document
     * @uses ErrResp, CleanLinks, CleanLinks traits
     */
    public function getPage($page, $proxy = '')
    {
        $content = '';
        try {
//            $acceptDoc = $this->client->request('GET', $page, ['proxy' => '151.253.165.70:8080']);
            $acceptDoc = $this->client->request('GET', $page);


            /** if response > 399 writing in the error file */
            $this->errResp(http_response_code(), $page);
            echo http_response_code() . ' __http_response_code<br>';//!!!!!!!!!!!!!!

            $content = $acceptDoc->getBody()->getContents();
        } catch (RequestException $e) {
        }

        $this->saveHTMLPage($content, $page);
        if (HTTP_INFO == 1) HTTPInfo::Info($page, $content);

        return $content;
    }

    public function asy($page)
    {
        $promise = $this->client->requestAsync('GET', $page);
        $promise->then(function ($response) {
            echo 'Got a response! ' . $response->getStatusCode();
        });
//        $promise = $this->client->requestAsync('GET', $page);
        $response = $promise->wait();
        $content = $response->getBody()->getContents();

        $results = Promise\settle($promise)->wait();
print_r($content);
//        $promise = $pool->promise();
//        $promise->wait();

//        if (HTTP_INFO == 1) HTTPInfo::Info($page, $content);
//        return $acceptDoc;
    }

    public function asyncGetPages($page)
    {

        $requests = function ($total) {
            $uris = [
                'http://razrabotkaweb.ru/ip.php',
                'https://httpbin.org/delay/1',
                'https://httpbin.org/delay/2',
                'https://httpbin.org/status/500',
            ];
            for ($i = 0; $i < count($uris); $i++) {
                yield new Request('GET', $uris[$i]);
            }
        };

        $pool = new Pool($this->client, $requests(8), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) {
                // this is delivered each successful response
                print_r($index . "fulfilled\n");
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
                print_r($index . "rejected\n");
            },
        ]);
// Initiate the transfers and create a promise
//        $promise = $pool->promise();
// Force the pool of requests to complete.
//        $promise->wait();


        $promises = [
            'success' => $this->client->getAsync('https://httpbin.org/get')->then(
                function (ResponseInterface $res) {
                    echo $res->getStatusCode() . "\n";
                },
                function (ResponseInterface $res) {
                    echo $e->getMessage() . "\n";
                    echo $e->getRequest()->getMethod();
                }
            )
            ,
            'success' => $this->client->getAsync('https://httpbin.org/delay/1')->then(
                function (ResponseInterface $res) {
                    echo $res->getStatusCode() . "\n";
                },
                function (RequestException $e) {
                    echo $e->getMessage() . "\n";
                    echo $e->getRequest()->getMethod();
                }
            ),
            'failconnecttimeout' => $this->client->getAsync('https://httpbin.org/delay/2')->then(
                function (ResponseInterface $res) {
                    echo $res->getStatusCode() . "\n";
                },
                function (RequestException $e) {
                    echo $e->getMessage() . "\n";
                    echo $e->getRequest()->getMethod();
                }
            ),
            'fail500' => $this->client->getAsync('https://httpbin.org/status/500')->then(
                function (ResponseInterface $res) {
                    echo $res->getStatusCode() . "\n";
                },
                function (RequestException $e) {
                    echo $e->getMessage() . "\n";
                    echo "getMethod()\n";
                }
            ),
        ];

        $results = Promise\settle($promises)->wait();

        $promise = $pool->promise();
        $promise->wait();
    }


    public function postPage($page, $postData)
    {

    }


}