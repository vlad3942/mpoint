<?php
/**
 * Created by IntelliJ IDEA.
 * User: Priya Alamwar
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes
 * File Name:GenericFSP.php
 */

namespace api\classes;

use RDB;
use TxnInfo;

class GenericFSP extends \CPMFRAUD
{
    private int $FSP_ID = -1;
    public function __construct(RDB $oDB, \api\classes\core\TranslateText $oTxt, TxnInfo $oTI, ?array $aConnInfo, int $fspId = -1)
    {
        $this->FSP_ID = $fspId;
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo[$fspId]);
    }

    public function getFSPID()
    {
        return $this->FSP_ID;
    }
}