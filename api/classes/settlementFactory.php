<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:SettlementFactory.php
 */

class SettlementFactory
{
    public static function create($clientId, $pspId, $connectionInfo)
    {
        switch ($pspId)
        {
            case Constants::iAMEX_ACQUIRER:
                return new AmexSettlement($clientId, $pspId, $connectionInfo);
            default:
                NULL;
        }
    }
}