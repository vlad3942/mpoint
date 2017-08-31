<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:tokenizable.php
 */

interface Tokenizable
{
    public function tokenize($obj_Card, ClientInfo $obj_ClientInfo);
}