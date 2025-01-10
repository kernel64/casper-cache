<?php

use Casper\CasperServer;

require_once "vendor/autoload.php";
/**
 * default IP and port
 * 127.0.0.1
 * 100824
 */
$server = new CasperServer();
$server->start();