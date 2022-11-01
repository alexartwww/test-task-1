<?php

namespace functions;

function check_mail_logs($email)
{
    // parse Postfix logs -> update db -> check db

    return \rand(0, 2) <= 1;
}
