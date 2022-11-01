<?php

namespace functions;

function check_smtp_server($domain)
{
    // nmap -Pn25 $domain

    return \rand(0, 2) <= 1;
}
