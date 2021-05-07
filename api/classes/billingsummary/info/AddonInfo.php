<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Parikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.billingsummary.info
 * File Name: BillingSummaryAddonInfo
 */


namespace api\classes\billingsummary\info;


class AddonInfo extends BillingSummaryAbstract
{

    /**
     * Default Constructor
     */
    public function __construct($id, $ref, $type, $typeId, $desc, $amt, $curr, $pseq, $tripTag, $tripSeq, $pCode, $pCategory, $pItem) {
        parent::__construct($id, $ref, $type, $typeId, $desc, $amt, $curr, $pseq, $tripTag, $tripSeq, $pCode, $pCategory, $pItem);
    }

    public static function produceConfig(\RDB $oDB, $id) : ?AddonInfo {
        $sql = "SELECT id, journey_ref, bill_type, type, description, amount, currency, created, modified, profile_seq, trip_tag,  trip_seq, product_code, product_category, product_item
					FROM log" . sSCHEMA_POSTFIX . ".billing_summary_tbl WHERE id=" . $id;
        // echo $sql ."\n";
        $RS = $oDB->getName ( $sql );
        if (is_array ( $RS ) === true && count ( $RS ) > 0) {
            return new AddonInfo( $RS ["ID"], $RS ["JOURNEY_REF"], $RS ["BILL_TYPE"], $RS ["TYPE"], $RS ["DESCRIPTION"], $RS ["AMOUNT"], $RS ["CURRENCY"], $RS ["PROFILE_SEQ"], $RS ["TRIP_TAG"], $RS ["TRIP_SEQ"], $RS ["PRODUCT_CODE"], $RS ["PRODUCT_CATEGORY"], $RS["PRODUCT_ITEM"]);
        } else {
            trigger_error('Unable to create Add on info object', E_USER_NOTICE);
            return null;
        }
    }
    public static function produceConfigurations(\RDB $oDB, $fid) {
        $sql = "SELECT id
				FROM Log" . sSCHEMA_POSTFIX . ".billing_summary_tbl
				WHERE order_id = " . intval ( $fid ) . " and bill_type='Add-on'";
        // echo $sql ."\n";
        $aConfigurations = array ();
        $res = $oDB->query ( $sql );
        while ( $RS = $oDB->fetchName ( $res ) ) {
            $aConfigurations [] = self::produceConfig ( $oDB, $RS ["ID"] );
        }
        return $aConfigurations;
    }

    public function toXML() : string
    {
        $xml = '';
        $xml .= '<add-on>';
        $xml .= '<profile-seq>' . $this->getProfileSeqence() . '</profile-seq>';
        $xml .= '<trip-tag>' . $this->getTripTag() . '</trip-tag>';
        $xml .= '<trip-seq>' . $this->getTripSeq() . '</trip-seq>';
        $xml .= '<description>' . $this->getDescription() . '</description>';
        $xml .= '<currency>' . $this->getCurrency() . '</currency>';
        $xml .= '<amount>' . $this->getAmount() . '</amount>';
        $xml .= '<product-code>' . $this->getProductCode() . '</product-code>';
        $xml .= '<product-category>' . $this->getProductCategory() . '</product-category>';
        $xml .= '<product-item>' . $this->getProductItem() . '</product-item>';
        $xml .= '</add-on>';
        return $xml;
    }
}