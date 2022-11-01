<?php

namespace functions;

function check_email($email)
{
    \usleep(rand(1000000, 60000000));
    return \rand(0, 1) == 1;
}

