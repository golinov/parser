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
    global $error;
    global $success;
    $proxy = file('proxy_list.txt');
    $i = 0;
    $client = new Client();
    while (true) {
        try {
            $result = $client->request('GET', $url, [
                'connect_timeout' => 2,
                'proxy' => [
                    'http' => $proxy[array_rand($proxy)]
                ]
            ]);
            $result = $result->getBody()->getContents();
            fwrite($success,"$url successful received \n");
            return $result ?? false;
        } catch (ConnectException $e) {
            fwrite($error,'$url ' . $e->getMessage(). "\n");
            $i++;
            if ($i === count($proxy)) {
                throw $e;
            }
        }
    }
}