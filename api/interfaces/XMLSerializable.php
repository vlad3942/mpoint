<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.interfaces
 * File Name:XMLSerializable.php
 */

namespace api\interfaces;

use DOMDocument;

interface XMLSerializable
{
    public function xmlSerialize ();
}