<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * @param $url string url
 * @param $proxy array of proxies
 * @return string page
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function getPage($url)
{
    $proxy = file('proxy_list.txt');
    $i = 0;
    global $fp;
    $client = new Client();
    while (true) {
        try {
            $result = $client->request('GET', $url, [
                'connect_timeout' => 4,
                'debug' => $fp,
                'proxy' => [
                    'http' => $proxy[array_rand($proxy)]
                ]
            ]);
            $result = $result->getBody()->getContents();
            return $result;
        } catch (ConnectException $e) {
            echo $e->getMessage();
            $i++;
            var_dump($i);
            if ($i === count($proxy)) {
                throw $e;
            }
        }
    }
}