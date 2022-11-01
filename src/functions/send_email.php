<?php

namespace functions;

function send_email($email, $from, $to, $subj, $body)
{
    \usleep(\rand(1000000, 10000000));
    return true;
}
