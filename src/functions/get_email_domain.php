<?php

namespace functions;

function get_email_domain($email)
{
    $matches = [];
    if (\preg_match('/@(?P<domain>.*?)$/', $email, $matches)) {
        return $matches['domain'];
    }
    return '';
}
