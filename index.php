<?php

define('PROCESSES_NUM', 100);

require_once __DIR__ . '/vendor/autoload.php';

$fp = fopen('logs/log.txt', 'a'); // open the log file
//ftruncate($fp, 0); // Clear the log file

$data = getRedis()->brpop('fourthPage', 'thirdPage', 'secondPage', 'firstPage', 1);
if (empty($data)) {
    $result = parse(startURL[1],filter['link']);
    getRedis()->lpush('firstPage', $result);
}

while (true) {
    $data = getRedis()->brpop('fourthPage', 'thirdPage', 'secondPage', 'firstPage', 10);
    if (empty($data)) {
        break;
    } else
        var_dump($data);
//    for ($x = 1; $x < 3; $x++) {
        switch ($pid = pcntl_fork()) {
            case -1:
                // @fail
                die('Fork failed');

            case 0:
                // @child: Include() misbehaving code here
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
                        $result = parse($data[1], filter['text']);
                        print('written to db'."\n");
                        break;
                }
                break;

            default:
                // @parent
                print("wait child pid = $pid \n");
                pcntl_waitpid($pid, $status);
                break;
        }
//    }
    print "Completed first iteration \n";
}

print('Completed.');

fclose($fp); // Close the log file