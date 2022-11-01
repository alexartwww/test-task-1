<?php
define('ROOT', implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));

require implode(DIRECTORY_SEPARATOR, [ROOT, 'vendor', 'autoload.php']);

use Jenner\SimpleFork\Process;
use Jenner\SimpleFork\Pool;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$n = 1000;

$logger = new Logger('check_email');
$logger->pushHandler(new StreamHandler('php://output', Logger::DEBUG));

$db = new \PDO("mysql:dbname=karma8;host=mysql", 'root', '1234');

$logger->info('Insert new emails');
$sth = $db->prepare('INSERT IGNORE INTO `emails` (`email`, `checked`, `valid`) SELECT `email`, 0, 0 FROM `users`');
$sth->execute([]);
$logger->info('Mark checked and valid if it is confirmed');
$sth = $db->prepare('INSERT INTO `emails` (`email`, `checked`, `valid`) SELECT `email`, 1, 1 FROM `users` WHERE `confirmed` = 1 ON DUPLICATE KEY UPDATE `checked` = VALUES(`checked`), `valid` = VALUES(`valid`)');
$sth->execute([]);

$sth = $db->prepare('
SELECT
    E.`email`
FROM
    `emails` E
WHERE
    E.`checked` = 0
');
$sth->execute([]);
$pool = new Pool();
while ($email = $sth->fetch(PDO::FETCH_ASSOC)) {
    $pool->execute(
        new Process(
            new \classes\CheckEmailRunnable(
                $logger,
                $email['email']
            )
        )
    );
    if ($pool->count() >= $n) {
        $pool->removeExitedProcess();
        $pool->wait();
    }
}
$pool->wait();
