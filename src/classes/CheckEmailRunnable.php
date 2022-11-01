<?php

namespace classes;

use Jenner\SimpleFork\Runnable;
use Psr\Log\LoggerInterface;

class CheckEmailRunnable implements Runnable
{
    private $logger;
    private $email;

    public function __construct(LoggerInterface $logger, string $email)
    {
        $this->logger = $logger;
        $this->email = $email;
    }

    private function saveResult($email, $result)
    {
        $db = new \PDO("mysql:dbname=karma8;host=mysql", 'root', '1234');
        $sth = $db->prepare('UPDATE `emails` SET `valid` = :valid, `checked` = 1 WHERE `email` = :email');
        $sth->execute(['valid' => $result ? 1 : 0, ':email' => $email]);
    }

    public function run()
    {
        try {
            $this->logger->info("check_email " . $this->email);
            $result = \functions\check_mail_logs($this->email);
            if ($result) {
                $result = \functions\check_mx_records(\functions\get_email_domain($this->email));
            } else {
                $this->logger->info("check_mail_logs failed for " . $this->email);
            }
            if ($result) {
                $result = \functions\check_smtp_server(\functions\get_email_domain($this->email));
            } else {
                $this->logger->info("check_mx_records failed for " . $this->email);
            }
            if ($result) {
                $result = \functions\check_email($this->email);
            } else {
                $this->logger->info("check_smtp_server failed for " . $this->email);
            }
            if (!$result) {
                $this->logger->info("check_email failed for " . $this->email);
            }
            $this->saveResult($this->email, $result);
            $this->logger->info("finish check_email " . $this->email);
        } catch (\Exception $e) {
            $this->logger->error("error check_email #" . $e->getCode() . " " . $e->getMessage());
        }
    }
}
