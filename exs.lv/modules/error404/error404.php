<?php

$robotstag[] = 'noindex';

header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
header("Status: 404 Not Found");
http_response_code(404);

