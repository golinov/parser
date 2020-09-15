<?php

use Symfony\Component\DomCrawler\Crawler;

/**
 * @param $html html page
 * @param $filter parsing params
 * @return array of parsed data
 */
function parsePage($html, $filter)
{
    $crawler = new Crawler($html);
    if ($filter['type'] === 'link' || $filter['type'] === 'questionLink') {
        $result = $crawler = $crawler->filter($filter['filter'])->each(function (Crawler $node, $i) {
            return $node->link()->getUri();
        });
        return array_unique($result);
    } else {
        $question = $crawler->filter($filter['filter']['question'])->text();
        $answer = $crawler->filter($filter['filter']['answer'])->each(function (Crawler $node, $i) {
            return $node->text();
        });
        $length = $crawler->filter($filter['filter']['length'])->each(function (Crawler $node, $i) {
            return $node->text();
        });
        return [
            'question' => $question,
            'answer' => $answer,
            'length' => $length
        ];
    }

}