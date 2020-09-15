<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * @param $url string url
 * @param $proxy array of proxies
 * @return string page
 * @throws \GuzzleHttp\Exception\ConnectException
 */
function getPage($url)
{
    global $fp;
    $proxy = file('proxy_list.txt');
    $i = 0;
    $client = new Client();
    while (true) {
        try {
            $result = $client->request('GET', $url, [
                'connect_timeout' => 5,
                'proxy' => [
                    'http' => $proxy[array_rand($proxy)]
                ]
            ]);
            $result = $result->getBody()->getContents();
            fwrite($fp,"$url successful received \n");
            return $result;
        } catch (ConnectException $e) {
            fwrite($fp,"$url $e->getMessage() \n");
            $i++;
            if ($i === count($proxy)) {
                throw $e;
            }
        }
    }
}