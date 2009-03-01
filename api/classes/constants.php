<?php
/**
 * The Constants package contains system wide constants that are used for various purposes, including:
 * 	- Logging
 * 	- Validation
 * 	- Types
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Constants
 * @version 1.01
 */

/**
 * Data class for holding all defined Constants
 *
 */
abstract class Constants
{
	/**
	 * Defines the ID of the State that should be logged when the Recipient's Mobile Network Operator isn't Supported
	 *
	 */
	const iUNSUPPORTED_OPERATOR = 44;
	/**
	 * Defines the ID of the State that should be logged when the Client Input has been successfully validate
	 *
	 */
	const iINPUT_VALID_STATE = 1001;
	/**
	 * Defines the ID of the State that is used for providing easy access to the product data provided by the Client
	 *
	 */
	const iPRODUCTS_STATE = 1002;
	/**
	 * Defines the ID of the State that is used for providing easy access to any custom variables provided by the Client
	 *
	 */
	const iCLIENT_VARS_STATE = 1003;
	/**
	 * Defines the ID of the State that is used for providing easy access to the Delivery Information for a Customer's Order
	 *
	 */
	const iDELIVERY_INFO_STATE = 1004;
	/**
	 * Defines the ID of the State that is used for providing easy access to the Shipping Information for a Customer's Order
	 *
	 */
	const iSHIPPING_INFO_STATE = 1005;
	/**
	 * Defines the ID of the State that should be logged if a message is accepted by GoMobile
	 *
	 */
	const iMSG_ACCEPTED_BY_GM_STATE = 1010;
	/**
	 * Defines the ID of the State that should be logged if mPoint is unable to establish a connection to GoMobile
	 *
	 */
	const iGM_CONN_FAILED_STATE = 1011;
	/**
	 * Defines the ID of the State that should be logged if a message is rejected by GoMobile
	 *
	 */
	const iMSG_REJECTED_BY_GM_STATE = 1012;
	/**
	 * Defines the ID of the State that should be logged when the payment link has been constructed
	 *
	 */
	const iCONST_LINK_STATE = 1020;
	/**
	 * Defines the ID of the State that indicates the mPoint link has been activated by the customer
	 *
	 */
	const iACTIVATE_LINK_STATE = 1021;
	/**
	 * Defines the ID of the State that should be logged if tan E-Mail is accepted by the local SMTP Server
	 *
	 */
	const iEMAIL_ACCEPTED_STATE = 1030;
	/**
	 * Defines the ID of the State that should be logged if an E-Mail is rejected by the local SMTP Server
	 *
	 */
	const iEMAIL_REJECTED_START = 1031;
	/**
	 * Defines the ID of the State that indicates that mPoint's Callback request has been accepted
	 *
	 */
	const iCB_ACCEPTED_STATE = 1990;
	/**
	 * Defines the ID of the State that indicates that the callback request to the Client has been constructed
	 *
	 */
	const iCB_CONSTRUCTED_STATE = 1991;
	/**
	 * Defines the ID of the State that mPoint has connected to the Client's server to perform a Callback
	 *
	 */
	const iCB_CONNECTED_STATE = 1992;
	/**
	 * Defines the ID of the State that indicates that the connection to the Client's server failed while mPoint was doing a Callback
	 *
	 */
	const iCB_CONN_FAILED_STATE = 1993;
	/**
	 * Defines the ID of the State that indicates that the Callback request to the Client while mPoint was transmitting the data
	 *
	 */
	const iCB_SEND_FAILED_STATE = 1994;
	/**
	 * Defines the ID of the State that indicates that mPoint's Callback request has been rejected
	 *
	 */
	const iCB_REJECTED_STATE = 1995;
	/**
	 * Defines the ID of the State that indicates that the payment has been successfully cleared by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_ACCEPTED_STATE = 2000;
	/**
	 * Defines the ID of the State that indicates that the payment has been successfully captured by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_CAPTURED_STATE = 2001;
	/**
	 * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_REJECTED_STATE = 2010;

	/**
	 * Defines GoMobile's type identifier for an MT-SMS message
	 *
	 */
	const iMT_SMS_TYPE = 2;
	/**
	 * Defines GoMobile's type identifier for an MT-WAP Push message
	 *
	 */
	const iMT_WAP_PUSH_TYPE = 3;
	/**
	 * Defines Price Point that all messages sent to the customer is charged at
	 *
	 */
	const iMT_PRICE = 0;

	/**
	 * Defines Type ID for a purchase initiated by a Call Centre
	 *
	 */
	const iCALL_CENTRE_PURCHASE_TYPE = 10;
	/**
	 * Defines Type ID for a subscription initiated by a Call Centre
	 *
	 */
	const iCALL_CENTRE_SUBSCR_TYPE = 11;
	/**
	 * Defines Type ID for a purchase initiated via SMS
	 *
	 */
	const iSMS_PURCHASE_TYPE = 20;
	/**
	 * Defines Type ID for a subscription initiated via SMS
	 *
	 */
	const iSMS_SUBSCR_TYPE = 21;
	/**
	 * Defines Type ID for a purchase initiated from a Mobile Site or Application
	 *
	 */
	const iWEB_PURCHASE_TYPE = 30;
	/**
	 * Defines Type ID for a subscription initiated from a Mobile Site or Application
	 *
	 */
	const iWEB_SUBSCR_TYPE = 31;

	/**
	 * Defines the min length for all authentication data:
	 * 	- Username
	 * 	- Password
	 * 	- E-Mail
	 * 	- Name
	 *
	 */
	const iAUTH_MIN_LENGTH = 4;
	/**
	 * Defines the max length for all authentication data:
	 * 	- Username
	 * 	- Password
	 * 	- E-Mail
	 *
	 */
	const iAUTH_MAX_LENGTH = 50;
	/**
	 * Unique ID for the Payment Service Provider: Cellpoint Mobile
	 *
	 */
	const iCPM_PSP = 1;
	/**
	 * Unique ID for the Payment Service Provider: DIBS
	 *
	 */
	const iDIBS_PSP = 2;
	/**
	 * Unique ID for the Payment Service Provider: IHI
	 *
	 */
	const iIHI_PSP = 3;

	/**
	 * Unique ID for the Electronic Payment Flow
	 *
	 */
	const iELECTRONIC_FLOW = 1;
	/**
	 * Unique ID for the Physical Payment Flow
	 *
	 */
	const iPHYSICAL_FLOW = 2;
}
?>