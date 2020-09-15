<?php

function parse($html,$filter)
{
    $page = getPage($html);
    return $page ? parsePage($page,$filter) : false;
}
