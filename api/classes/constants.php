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
 * @version 1.02
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
	 * Defines the ID of the State that should be logged for the payment request sent to the Payment Service Provider (PSP) to
	 * authorize the payment transaction.
	 * Any sensitive data according to PCI DSS that is included in the request must be removed before the log entry is made.
	 * Typical data includes:
	 * 	- Card Number (PAN)
	 * 	- CVC / CVS
	 *
	 */
	const iPSP_PAYMENT_REQUEST_STATE = 1007;
	/**
	 * Defines the ID of the State that should be logged for the payment response from the Payment Service Provider (PSP) when
	 * a payment transaction is authorized.
	 *
	 */
	const iPSP_PAYMENT_RESPONSE_STATE = 1008;
	/**
	 * Defines the ID of the State that should be logged when the payment transaction has been successfully initialized with the
	 * Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_INIT_WITH_PSP_STATE = 1009;
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
	 * Defines the ID of the State that indicates that SurePay has resent the payment link embedded in an MT-SMS
	 *
	 */
	const iPAYMENT_LINK_RESENT_AS_EMBEDDED_STATE = 1022;
	/**
	 * Defines the ID of the State that indicates that SurePay has resent the payment link as an MT-WAP Push
	 *
	 */
	const iPAYMENT_LINK_RESENT_AS_WAPPUSH_STATE = 1023;
	/**
	 * Defines the ID of the State that indicates that SurePay has automatically notified the
	 * Client's Customer Service team that the payment link hasn't been activated
	 *
	 */
	const iCUSTOMER_SERVICE_NOTIFIED_STATE = 1029;
	/**
	 * Defines the ID of the State that should be logged if tan E-Mail is accepted by the local SMTP Server
	 *
	 */
	const iEMAIL_ACCEPTED_STATE = 1030;
	/**
	 * Defines the ID of the State that should be logged if an E-Mail is rejected by the local SMTP Server
	 *
	 */
	const iEMAIL_REJECTED_STATE = 1031;
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
	 * Defines the ID of the State that indicates that mPoint's Callback request is being retried
	 *
	 */
	const iCB_RETRIED_STATE = 1999;
	/**
	 * Defines the ID of the State that indicates that the payment has been successfully cleared by the Payment Service Provider (PSP)
	 * during the Authorisation
	 *
	 */
	const iPAYMENT_ACCEPTED_STATE = 2000;
	/**
	 * Defines the ID of the State that indicates that the payment has been successfully captured by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_CAPTURED_STATE = 2001;
	/**
	 * Defines the ID of the State that indicates that a previously authorized payment has been successfully cancelled by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_CANCELLED_STATE = 2002;
	/**
	 * Defines the ID of the State that indicates that previously captured payment has been successfully refunded by the Payment Service Provider (PSP)
	 *
	 */
	const iPAYMENT_REFUNDED_STATE = 2003;

	/**
	 * Defines the ID of the State that indicates that payment is being using the End-User's account
	 *
	 */
	const iPAYMENT_WITH_ACCOUNT_STATE = 2008;
	/**
	 * Defines the ID of the State that indicates that a new Ticket has been created using pre-authorization
	 *
	 */
	const iTICKET_CREATED_STATE = 2009;
	/**
	 * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
	 * when doing an Authorisation
	 *
	 */
	const iPAYMENT_REJECTED_STATE = 2010;
	/**
	 * Defines the ID of the State that indicates that the payment was declined by the Payment Service Provider (PSP)
	 * when doing a Capture
	 *
	 */
	const iPAYMENT_DECLINED_STATE = 2011;
	/**
	 * Defines the ID of the State that indicates that payment has accidentally been duplicated by DIBS
	 *
	 */
	const iPAYMENT_DUPLICATED_STATE = 2019;

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
	const iPURCHASE_VIA_CALL_CENTRE = 10;
	/**
	 * Defines Type ID for a subscription initiated by a Call Centre
	 *
	 */
	const iCALL_CENTRE_SUBSCR_TYPE = 11;
	/**
	 * Defines Type ID for a purchase initiated via SMS
	 *
	 */
	const iPURCHASE_VIA_SMS = 20;
	/**
	 * Defines Type ID for a subscription initiated via SMS
	 *
	 */
	const iSMS_SUBSCR_TYPE = 21;
	/**
	 * Defines Type ID for a purchase initiated from a Mobile Site or Mobile App. using an embedded WebView
	 *
	 */
	const iPURCHASE_VIA_WEB = 30;
	/**
	 * Defines Type ID for a subscription initiated from a Mobile Site or Mobile App. using an embedded WebView
	 *
	 */
	const iWEB_SUBSCR_TYPE = 31;
	/**
	 * Defines Type ID for a purchase initiated from a Mobile App. using native controls
	 *
	 */
	const iPURCHASE_VIA_APP = 40;
	/**
	 * Defines Type ID for a subscription initiated from a Mobile App. using native controls
	 *
	 */
	const iAPP_SUBSCR_TYPE = 41;
	/**
	 * Defines Type ID for an e-money top-up of an End-User's prepaid account
	 *
	 */
	const iPURCHASE_OF_EMONEY = 100;
	/**
	 * Defines Type ID for a subscription for automatically topping an End-User's prepaid account up
	 *
	 */
	const iTOPUP_SUBSCR_TYPE = 101;
	/**
	 * Defines Type ID for purchasing points to the End-User's loyalty account
	 *
	 */
	const iPURCHASE_OF_POINTS = 102;
	/**
	 * Defines Type ID for a Top-Up of the End-User's prepaid account
	 *
	 */
	const iTOPUP_OF_EMONEY = 1000;
	/**
	 * Defines Type ID for an E-Money based purchase made using the End-User's prepaid account
	 *
	 */
	const iPURCHASE_USING_EMONEY = 1001;
	/**
	 * Defines Type ID for an E-Money based transfer between 2 End-Users' prepaid accounts
	 *
	 */
	const iTRANSFER_OF_EMONEY = 1002;
	/**
	 * Defines Type ID for a Withdrawal from the End-User's prepaid account
	 *
	 */
	const iEMONEY_WITHDRAWAL_TYPE = 1003;
	/**
	 * Defines Type ID for a Top-Up of the End-User's loyalty account
	 *
	 */
	const iTOPUP_OF_POINTS = 1004;
	/**
	 * Defines Type ID for a Points based purchase made using the End-User's loyalty account
	 *
	 */
	const iPURCHASE_USING_POINTS = 1005;
	/**
	 * Defines Type ID for a Points based transfer between 2 End-Users' loyalty accounts
	 *
	 */
	const iTRANSFER_OF_POINTS = 1006;
	/**
	 * Defines Type ID for rewarding points to an end-user for completing a purchase
	 *
	 */
	const iREWARD_OF_POINTS = 1007;
	/**
	 * Defines Type ID for a Card / Premium SMS based purchase that should be associated with the End-User's prepaid account.
	 * Please note that the "amount" for this type of transaction MUST be 0.
	 *
	 */
	const iCARD_PURCHASE_TYPE = 1009;

	/**
	 * Defines the min length for all authentication data:
	 * 	- Username
	 * 	- Password
	 * 	- E-Mail
	 * 	- Name
	 *
	 */
	const iAUTH_MIN_LENGTH = 6;
	/**
	 * Defines the max length for all authentication data:
	 * 	- Username
	 * 	- Password
	 * 	- E-Mail
	 *
	 */
	const iAUTH_MAX_LENGTH = 50;
	/**
	 * Number of invalid login attempts before the End-User account is disabled
	 *
	 */
	const iMAX_LOGIN_ATTEMPTS = 3;
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
	 * Unique ID for the Payment Service Provider: WorldPay
	 *
	 */
	const iWORLDPAY_PSP = 4;
	/**
	 * Unique ID for the Payment Service Provider: PayEx
	 *
	 */
	const iPAYEX_PSP = 5;
	/**
	 * Unique ID for the Payment Service Provider: Authorize.Net
	 *
	 */
	const iANET_PSP = 6;
	/**
	 * Unique ID for the Payment Service Provider: WannaFind
	 *
	 */
	const iWANNAFIND_PSP = 7;
	/**
	 * Unique ID for the Payment Service Provider: NetAxept
	 *
	 */
	const iNETAXEPT_PSP = 8;
	/**
	 * Unique ID for the Payment Service Provider: Emirates
	 *
	 */
	const iCPG_PSP = 9;
	/**
	 * Unique ID for the Payment Service Provider: Stripe
	 *
	 */
	const iSTRIPE_PSP = 10;
	/**
	 * Unique ID for the Alternative Payment Method: MobilePay
	 *
	 */
	const iMOBILEPAY_PSP = 11;
	/**
	 * Unique ID for the Payment Service Provider: Adyen
	 *
	 */
	const iADYEN_PSP = 12;	
	/**
	 * Unique ID for the Payment Service Provider: VISA Checkout
	 *
	 */
	const iVISA_CHECKOUT_PSP = 13;
	/**
	 * Unique ID for the Payment Service Provider: Apple Pay
	 *
	 */
	const iAPPLE_PAY_PSP = 14;
	/**
	 * Unique ID for the Payment Service Provider: Master Pass
	 *
	 */
	const iMASTER_PASS_PSP = 15;

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

	/**
	 * Default amount that is added to an End-User's account when a new account is created and the client is
	 * running in test mode.
	 *
	 */
	const iEMONEY_GRANT = 100000;
	
	/**
	 * Unique Card ID for transaction's made using American Express
	 *
	 */
	const iAMEX_CARD = 1;
	/**
	 * Unique Card ID for transaction's made using Dankort
	 *
	 */
	const iDANKORT_CARD = 2;
	/**
	 * Unique Card ID for transaction's made using Diners Club
	 *
	 */
	const iDINERS_CLUB_CARD = 3;
	/**
	 * Unique Card ID for transaction's made using EuroCard
	 *
	 */
	const iEUROCARD = 4;
	/**
	 * Unique Card ID for transaction's made using JCB
	 *
	 */
	const iJCB_CARD = 5;
	/**
	 * Unique Card ID for transaction's made using Maestro
	 *
	 */
	const iMAESTRO_CARD = 6;
	/**
	 * Unique Card ID for transaction's made using MasterCard
	 *
	 */
	const iMASTERCARD = 7;
	/**
	 * Unique Card ID for transaction's made using VISA
	 *
	 */
	const iVISA_CARD = 8;
	/**
	 * Unique Card ID for transaction's made using VISA Electron
	 *
	 */
	const iVISA_ELECTRON_CARD = 9;
	/**
	 * Unique Card ID for transaction's made using Premium SMS
	 *
	 */
	const iPREMIUM_SMS = 10;
	/**
	 * Unique Card ID for transaction's made using mPoint's built-in Wallet
	 *
	 */
	const iWALLET = 11;
	/**
	 * Unique Card ID for transaction's made using Switch
	 *
	 */
	const iSWITCH_CARD = 12;
	/**
	 * Unique Card ID for transaction's made using Solo
	 *
	 */
	const iSOLO_CARD = 13;
	/**
	 * Unique Card ID for transaction's made using Delta
	 *
	 */
	const iDELTA_CARD = 14;
	/**
	 * Unique Card ID for transaction's made using Apple Pay
	 *
	 */
	const iAPPLE_PAY = 15;
	/**
	 * Unique Card ID for transaction's made using the VISA Checkout Wallet
	 *
	 */
	const iVISA_CHECKOUT_WALLET = 16;
	/**
	 * Unique Card ID for transaction's made using Danske Bank MobilePay
	 *
	 */
	const iMOBILEPAY = 17;
	/**
	 * Unique Card ID for transaction's made using Cartebleue
	 *
	 */
	const iCARTE_BLEUE_CARD = 18;
	/**
	 * Unique Card ID for transaction's made using Postepay VISA
	 *
	 */
	const iPOSTEPAY_VISA_CARD = 19;
	/**
	 * Unique Card ID for transaction's made using Postepay Mastercard
	 *
	 */
	const iPOSTEPAY_MASTERCARD = 20;
	/**
	 * Unique Card ID for transaction's made using UATP
	 *
	 */
	const iUATP_CARD = 21;
	/**
	 * Unique Card ID for transaction's made using Discover
	 *
	 */
	const iDISCOVER_CARD = 22;
	/**
	 * Unique Card ID for transaction's made using Master Pass
	 *
	 */
	const iMASTER_PASS_WALLET = 23;

	/**
	 * Unique Fee Type ID for Top-Ups
	 *
	 */
	const iTOPUP_FEE = 1;
	/**
	 * Unique Fee Type ID for Transfers
	 *
	 */
	const iTRANSFER_FEE = 2;
	/**
	 * Unique Fee Type ID for Withdrawals
	 *
	 */
	const iWITHDRAWAL_FEE = 3;

	const iTRANSACTION_COMPLETED_STATE = 1800;
	const iTRANSFER_PENDING_STATE = 1808;
	const iTRANSFER_CANCELLED_STATE = 1809;

	const iOPERATION_CARD_SAVED = 1;
	const iOPERATION_CARD_DELETED = 2;
	const iOPERATION_LOGGED_IN = 3;

	const iURL_TYPE_GET_TRANSACTION_STATUS = 3;
	const iURL_TYPE_CALLBACK = 4;

	/**
	 * Unique ID for the Information Type: PSP Message
	 * 
	 */
	const iPSP_MESSAGE_INFO = 1;
	
	/*
	 * mConsole Enterprise URL endpoint.
	 */
	const sMCONSOLE_SINGLE_SIGN_ON_PATH = '/mconsole/single-sign-on';	
	
	/*
	 * Value of the Payment Data retrieve call from the third party endpoint
	 * To fetch the card details with out the PSP details
	 */
	const sPAYMENT_DATA_SUMMARY = "summary";
	
	/*
	 * Value of the Payment Data retrieve call from the third party endpoint
	 * To fetch the card details by passing the auth toke to the wallet instance.
	 */
	const sPAYMENT_DATA_FULL = "full";
}
?>
