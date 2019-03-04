<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:login.php
 */

// Require Global Include File
require_once '../../../webroot/inc/include.php';
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

$password = $obj_XML->login->password;

if (strpos($password, 'success') !== false) {
    header('HTTP/1.1 200 OK');
    return;
}
if (strpos($password, 'fail') !== false) {
    header('HTTP/1.1 400 Bad Request');
    return;
}
if (strpos($password, 'timeout') !== false) {
    header('HTTP/1.1 504 Gateway Timeout');
    return;
}
header('HTTP/1.1 404 Not Found');