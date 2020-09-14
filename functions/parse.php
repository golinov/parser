<?php

function parse($html,$filter)
{
    $page = getPage($html);
    $parsedData = parsePage($page,$filter);
    return $parsedData;
}
