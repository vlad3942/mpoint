<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.core
 * File Name:TranslateText.php
 */

namespace api\classes\core;

class TranslateText
{

    public function __construct(array $p, $bp, $m=0, $cs="ISO-8859-15", array $a=null)
    {
    }

    public function _($txt)
    {
        return $txt;
    }
    public function loadConstants(array $a=null)
    {

    }
}