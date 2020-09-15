<?php

define('PROCESSES_NUM', 500);

require_once __DIR__ . '/vendor/autoload.php';

$fp = fopen('logs/log.txt', 'a'); // open the log file
//ftruncate($fp, 0); // Clear the log file

$data = getRedis()->brpop(['thirdPage', 'secondPage', 'firstPage'], 1);
if (empty($data)) {
    $result = parse(startURL[1],filter['link']);
    getRedis()->lpush('firstPage', $result);
}

while (true) {
    print("on queue =  $data[1] \n");
    sleep(5);
    for ($i = 1; $i < PROCESSES_NUM; $i++) {
        switch ($pid = pcntl_fork()) {
            case -1:
                // @fail
                die('Fork failed');

            case 0:
                // @child: Include() misbehaving code here
                $data = getRedis()->brpop( ['thirdPage', 'secondPage', 'firstPage'], 1);
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
    }
    if (empty($data)) {
        break;
    }
}

print('Completed.');

fclose($fp); // Close the log file