<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Badave
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:fraud_result.php
 */

class FraudResult
{
    /**
     * Hold is fraud attempted or not
     *
     * @var bool
     */
    private $_isFraudCheckAttempted;

    /**
     * Hold is fraud check accepted or not
     *
     * @var bool
     */
    private $_isFraudCheckAccepted;

    public function __construct( ) { $this->_isFraudCheckAccepted = $this->_isFraudCheckAttempted = false; }
    public function isFraudCheckAccepted() { return $this->_isFraudCheckAccepted; }
    public function isFraudCheckAttempted() { return $this->_isFraudCheckAttempted; }

    public function setFraudCheckAttempted($isFraudCheckAttempted){ $this->_isFraudCheckAttempted = $isFraudCheckAttempted; }
    public function setFraudCheckResult($isFraudCheckAccepted){ $this->_isFraudCheckAccepted = $isFraudCheckAccepted; }
}