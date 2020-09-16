<?php

define('PROCESSES_NUM', 150);

require_once __DIR__ . '/vendor/autoload.php';

$error = fopen('logs/errors.txt', 'a'); // open the log file
$success = fopen('logs/successes.txt', 'a'); // open the log file

if (!getRedis()->keys('*')) {
    $result = parse(startURL[1], filter['link']);
    if ($result) {
        getRedis()->lpush('firstPage', $result);
    } else {
        print("Error, for details look at logs/errors.txt\n");
        exit;
    }
}

while (true) {
    $childPids = [];
    for ($i = 1; $i < PROCESSES_NUM; $i++) {

        $newPid = pcntl_fork();

        if ($newPid == -1) {
            die('Can\'t fork process');
        } elseif ($newPid) {

            $childPids[] = $newPid;
//            echo 'Main process have created subprocess ' . $newPid . PHP_EOL;

            if ($i == (PROCESSES_NUM - 1)) {
//                echo 'Main process is waiting for all subprocesses' . PHP_EOL;
                foreach ($childPids as $childPid) {
                    pcntl_waitpid($childPid, $status);
//                    echo 'OK. Subprocess ' . $childPid . ' is ready' . PHP_EOL;

                }
//                echo 'OK. All subprocesses are ready' . PHP_EOL;
            }

        } else {

            $myPid = getmypid();
//            echo 'I am forked process with pid ' . $myPid. PHP_EOL;
            $data = getRedis()->brpop(['fourthPage','thirdPage', 'secondPage', 'firstPage'], 2);
            switch ($data[0]) {
                case 'firstPage':
                    $result = parse($data[1], filter['link']);
                    $result ? getRedis()->lpush('secondPage', $result) : getRedis()->lpush('firstPage', $data[1]);
                    break;
                case 'secondPage':
                    $result = parse($data[1], filter['questionLink']);
                    $result ? getRedis()->lpush('thirdPage', $result) : getRedis()->lpush('secondPage', $data[1]);
                    break;
                case 'thirdPage':
                    $result = parse($data[1], filter['text']);
                    $result ? $db = writeToDb($result) : getRedis()->lpush('thirdPage', $data[1]);
                    while(true) {
                        $db ? true : writeToDb($result);
                        if(true)
                        {
                            break;
                        }
                    }
                    break;
            }
//            echo 'I am already done ' . $myPid . PHP_EOL;

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