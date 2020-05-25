<?php
/**
 * Fraud service provider(FSP) handles fraud related operation
 * 
 * @author SAGAR BADAVE
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * * @version 1.00
 */


/**
 * Model Class containing all the Business Logic for the Fraud Service Provider: Cyber source
 *
 */
class CyberSourceFSP extends CPMFRAUD
{
    public function getFSPID() { return Constants::iCYBER_SOURCE_FSP; }
}
