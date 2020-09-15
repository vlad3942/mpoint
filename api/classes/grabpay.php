<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Badave
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:GrabPay.php
 */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: grabPay
 *
 */
class GrabPay extends CPMPSP
{
    public function getPSPID() { return Constants::iGRAB_PAY_PSP; }
}