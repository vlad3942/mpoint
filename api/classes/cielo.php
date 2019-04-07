<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: Cielo
 * File Name:cielo.php
 */

class Cielo extends CPMACQUIRER
{

    public function getPSPID()
    {
        return Constants::iCielo_ACQUIRER;
    }
}