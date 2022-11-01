<?php

namespace functions;

function check_mx_records($domain)
{
    // dig $domain MX +short

    return \rand(0, 2) <= 1;
}
