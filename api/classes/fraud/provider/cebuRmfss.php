<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author GAURAV PAWAR
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * * @version 1.00
 */


/**
 * Model Class containing all the Business Logic for the Payment Service Provider: CEBU RMFSS
 *
 */
class CebuRmfssFSP extends CPMFRAUD
{
    public function getFSPID() { return Constants::iCEBU_RMFSS_FSP; }
}
