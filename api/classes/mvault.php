<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: mvault.php
 */

/* ==================== mVault Exception Classes Start ==================== */
/**
 * Super class for all mVault Exceptions
 */
class MVaultException extends CallbackException { }
/* ==================== mVault Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: mVault
 *
 */

class MVault extends CPMPSP
{
    public function capture($iAmount=-1) { throw new MVaultException("Method: capture is not supported by mVault"); }
    public function refund($iAmount=-1) { throw new MVaultException("Method: refund is not supported by mVault"); }
    public function void($iAmount=-1) { throw new MVaultException("Method: void is not supported by mVault"); }
    public function cancel() { throw new MVaultException("Method: cancel is not supported by mVault"); }
    public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new MVaultException("Method: authTicket is not supported by mVault"); }
    public function status() { throw new MVaultException("Method: status is not supported by mVault"); }

    public function getPSPID() { return Constants::iMVault_PSP; }


}