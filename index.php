<?php

define('PROCESSES_NUM', 100);

require_once __DIR__ . '/vendor/autoload.php';

$fp = fopen('logs/log.txt', 'a'); // open the log file
ftruncate($fp, 0); // Clear the log file

while (true) {
    $data = getRedis()->brpop('fourthPage', 'thirdPage', 'secondPage', 'firstPage', 10);
    if (empty($data)) {
        parse(startURL[1],filter['link']);
    } else
        var_dump($data);
    for ($i = 1; $i <= PROCESSES_NUM; ++$i) {
        $pid = pcntl_fork();
        if (!$pid) {
            sleep(1);
            switch ($data[0]) {
                case 'startUrl':
                    $result = parse($data[1], filter['link']);
                    getRedis()->lpush('firstPage', $result);
                    break;
                case 'firstPage':
                    $result = parse($data[1], filter['link']);
                    getRedis()->lpush('secondPage', $result);
                    break;
                case 'secondPage':
                    $result = parse($data[1], filter['questionLink']);
                    getRedis()->lpush('thirdPage', $result);
                    break;
                case 'thirdPage':
                case 'fourthPage':
            }
            exit($i);
        }
    }
    while (pcntl_waitpid(0, $status) != -1) {
        $status = pcntl_wexitstatus($status);
    }
}

fclose($fp); // Close the log file