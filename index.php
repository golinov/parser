<?php

define('PROCESSES_NUM', 500);

require_once __DIR__ . '/vendor/autoload.php';

$error = fopen('logs/errors.txt', 'a'); // open the log file
$success = fopen('logs/successes.txt', 'a'); // open the log file

if (!getRedis()->keys('*'))
{
    $result = parse(startURL[1], filter['link']);
    getRedis()->lpush('firstPage', $result);
}

while (true) {
    $childPids = [];
    for ($i = 1; $i < PROCESSES_NUM; $i++) {

        $newPid = pcntl_fork();

        if ($newPid == -1) {
            die('Can\'t fork process');
        } elseif ($newPid) {

            $childPids[] = $newPid;
            echo 'Main process have created subprocess ' . $newPid . PHP_EOL;

            if ($i == (PROCESSES_NUM-1)) {
                echo 'Main process is waiting for all subprocesses' . PHP_EOL;
                foreach ($childPids as $childPid) {
                    pcntl_waitpid($childPid, $status);
                    echo 'OK. Subprocess ' . $childPid . ' is ready' . PHP_EOL;

                }
                echo 'OK. All subprocesses are ready' . PHP_EOL;
            }

        } else {

            $myPid = getmypid();
            echo 'I am forked process with pid ' . $myPid. PHP_EOL;
            $data = getRedis()->brpop(['thirdPage', 'secondPage', 'firstPage'], 10);
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
                    writeToDb($result);
                    print('written to db' . "\n");
                    break;
            }
            echo 'I am already done ' . $myPid . PHP_EOL;

            die(0);

        }
    }
    if (!getRedis()->keys('*')) {
        break;
    }
}

print("Completed.\n");

fclose($error); // Close the log file
fclose($success); // Close the log file