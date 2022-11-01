<?php

namespace classes;

use Jenner\SimpleFork\Runnable;
use Psr\Log\LoggerInterface;

class SendEmailRunnable implements Runnable
{
    private $logger;
    private $email;
    private $from;
    private $to;
    private $subj;
    private $body;

    public function __construct(LoggerInterface $logger, string $email, string $from, string $to, string $subj, string $body)
    {
        $this->logger = $logger;
        $this->email = $email;
        $this->from = $from;
        $this->to = $to;
        $this->subj = $subj;
        $this->body = $body;
    }

    public function run()
    {
        try {
            $this->logger->info("send_email " . $this->email);
            \functions\send_email($this->email, $this->from, $this->to, $this->subj, $this->body);
            $this->logger->info("finish send_email " . $this->email);
        } catch (\Exception $e) {
            $this->logger->error("error send_email #" . $e->getCode() . " " . $e->getMessage());
        }
    }
}
