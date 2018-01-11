<?php
/**
 * Class provides object structure for Communication Channels for a client.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Client Config
 * @subpackage Communication Channel Config
 * @version 1.00
 */

Class ClientCommunicationChannelsConfig extends BasicConfig
{
     /**
     * Param value as configured with the client
     *
     * @var integer
     */
    private $_iValue;

    const iSMS_CHANNEL_ENABLED = 1;
    const iPUSH_CHANNEL_ENABLED = 3;
    const iEMAIL_CHANNEL_ENABLED = 5;

    /**
     * Default Constructor
     *
     * @param 	integer $id 			The unique ID for the client
     * @param 	string $name 			Client name
     * @param 	string $value   	 	Values representing the enabled communication channels for the client.
     */
    public function __construct($id, $name, $value)
    {
        parent::__construct($id, $name);

        $this->_iValue = intval($value);
    }
    public function getValue() { return $this->_iValue; }

    public function toXML()
    {
        $iChannelsVal = intval($this->getValue());
        $xml = '<communication-channels>';
        if( ($iChannelsVal - self::iEMAIL_CHANNEL_ENABLED ) >= 0 )
        {
            $xml .= '<channel type = "'.self::iEMAIL_CHANNEL_ENABLED.'" />';
            $iChannelsVal -= self::iEMAIL_CHANNEL_ENABLED;
        }
        if( ($iChannelsVal - self::iPUSH_CHANNEL_ENABLED ) >= 0 )
        {
            $xml .= '<channel type = "'.self::iPUSH_CHANNEL_ENABLED.'" />';
            $iChannelsVal -= self::iPUSH_CHANNEL_ENABLED;
        }
        if( ($iChannelsVal - self::iSMS_CHANNEL_ENABLED ) >= 0 )
        {
            $xml .= '<channel type = "'.self::iSMS_CHANNEL_ENABLED.'" />';
        }
        $xml .= '</communication-channels>';
        return $xml;
    }

    public static function produceConfig(RDB $oDB, $id)
    {
        $sql = "SELECT CL.id, CL.name, CL.communicationchannels AS channels
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL
				WHERE CL.id = ". intval($id) ." AND CL.enabled = '1'";
        //echo $sql ."\n";
        $RS = $oDB->getName($sql);
        if(is_array($RS) === true && count($RS) > 0)
        {
            return new ClientCommunicationChannelsConfig($RS["ID"], $RS["NAME"], $RS["CHANNELS"]
            );
        }
        else { return null; }
    }
}
