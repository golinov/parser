<?php

const startURL = [
    'startUrl',
    'http://www.kreuzwort-raetsel.net/uebersicht.html'
];

const filter = [
    'link' => [
        'filter' => 'ul.dnrg > li > a',
        'type' => 'link'
    ],
    'questionLink' => [
        'filter' => 'td.Question > a',
        'type' => 'link'
    ],
    'text' => [
        'filter' => [
            'question' => 'div.Text > h1 > span',
            'answer' => 'td.Answer > a',
            'length' => 'td.Length'
        ],
        'type' => 'text'
    ]
];