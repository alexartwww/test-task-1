<?php
define('ROOT', implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));

require implode(DIRECTORY_SEPARATOR, [ROOT, 'vendor', 'autoload.php']);

use Jenner\SimpleFork\Process;
use Jenner\SimpleFork\Pool;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('send_email');
$logger->pushHandler(new StreamHandler('php://output', Logger::DEBUG));

$period = 86400;
$start = date('Y-m-d H:i:s', time() + (3 * 86400));
$finish = date('Y-m-d H:i:s', time() + (3 * 86400 + $period));
$db = new PDO("mysql:dbname=karma8;host=mysql", 'root', '1234');
$sth = $db->prepare('
SELECT
    U.`username`,
    U.`email`,
    U.`validts`,
    U.`confirmed`
FROM
    `users` U
    LEFT JOIN `emails` E ON U.`email` = E.`email`
WHERE
    (U.`validts` >=  :start)
    AND (U.`validts` <  :finish)
    AND (U.`confirmed` = 1 OR E.`email` IS NULL OR E.`checked` = 0 OR (E.`checked` = 1 AND E.`valid` = 1))
');
$sth->execute([':start' => $start, ':finish' => $finish]);
$n = 1000;
$pool = new Pool();
while ($user = $sth->fetch(PDO::FETCH_ASSOC)) {
    $pool->execute(
        new Process(
            new classes\SendEmailRunnable(
                $logger,
                $user['email'],
                'karma8',
                $user['username'],
                'karma8 subscription reminder',
                $user['username'] . ', your subscription is expiring soon')
        )
    );
    if ($pool->count() >= $n) {
        $pool->removeExitedProcess();
        $pool->wait();
    }
}
$pool->wait();


/*
$arr = [];
foreach (range(1, 1000000) as $i) {
    $username = functions\random_string(10, 'abcdefghijklmnopqrstuvwxyz');
    $servers = ['gmail.com', 'yandex.ru', 'example.com', 'asdasd.com'];
    $email = $username . '@' . $servers[rand(0, count($servers) - 1)];
    $confirmed = rand(0, 1);
    $validts = date('Y-m-d H:i:s', time() + rand(0, 86400 * 30));
    $arr[] = "('". ucfirst($username) ." ". ucfirst($username) ."', '". $email ."', " . "'".$validts."'" . ", ". $confirmed .")";
    if (count($arr) >= 10000) {
        echo "INSERT INTO `users` (`username`, `email`, `validts`, `confirmed`) VALUES ".implode(',', $arr).";" . PHP_EOL;
        $arr = [];
    }
}
if (count($arr) > 0) {
    echo "INSERT INTO `users` (`username`, `email`, `validts`, `confirmed`) VALUES (".implode(',', $arr).");" . PHP_EOL;
    $arr = [];
}
exit();
*/
