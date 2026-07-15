<?php

// Lightweight, dependency-free health check for Render / load balancers.
// Intentionally does not touch the database or session so it stays fast
// and reliable even if the DB is briefly unavailable.
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';
