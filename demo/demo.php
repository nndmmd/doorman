<?php

// Doorman
require_once('../Doorman.php');
$doorman = new \Nndmmd\Doorman\Doorman;
$doorman->setRoles(require './roles.cfg.php');
$doorman->setPermissions(require './permissions.cfg.php');

if(!$doorman->can(['admin'],parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),$roles)) {
    echo 'Access denied.<br/>';
    var_dump($roles);
} else {
    echo 'Wellcome.<br/>';
    var_dump($roles);
}

