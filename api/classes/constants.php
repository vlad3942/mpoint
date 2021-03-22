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
	 * Defines the ID of the State that should be logged when the Transaction is created by mPoint
	 * This state define that transaction is created and in order to data for futuristic authorization
	 *
	 */
	const iTRANSACTION_CREATED = 1000;

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
	 * Defines the ID of the State that indicates that 3D Secure has been activated for the payment
	 *
	 */
	const i3D_SECURE_ACTIVATED_STATE = 1100;
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
	 * Defines the ID of the State that indicates that the callback request to the Client has been constructed
	 *
	 */
	const iCB_ACCEPTED_TIME_OUT_STATE = 19909;
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
	 * Defines the ID of the State that indicates that mPoint's Acknowledgement to Foreign Exchange request has been accepted
	 *
	 */
	const iACKFX_ACCEPTED_STATE = 1980;
	/**
	 * Defines the ID of the State that indicates that the Acknowledgement to Foreign Exchange  request to the Client has been constructed
	 *
	 */
	const iACKFX_CONSTRUCTED_STATE = 1981;
	/**
	 * Defines the ID of the State that indicates that the connection to the Client's server failed while mPoint was doing a Acknowledgement
	 *
	 */
	const iACKFX_CONN_FAILED_STATE = 1983;
	/**
	 * Defines the ID of the State that indicates that the Acknowledgement to Foreign Exchange request to the Client while mPoint was transmitting the data
	 *
	 */
	const iACKFX_SEND_FAILED_STATE = 1984;
	/**
	 * Defines the ID of the State that indicates that mPoint's Acknowledgement to Foreign Exchange request has been rejected
	 *
	 */
	const iACKFX_REJECTED_STATE = 1985;
	/**
	 * Defines the ID of the State that indicates that the transaction has been done for Account Validation and authorisation was successful.
	 *
	 */
	const iPAYMENT_ACCOUNT_VALIDATED = 1998;
	/**
	 * Defines the ID of the State that indicates that the transaction has been done for Account Validation and it failed authorisation.
	 *
	 */
	const iPAYMENT_ACCOUNT_VALIDATION_FAILED = 1997;
	/**
	 * Defines the ID of the State that indicates that the transaction has been done for Account Validation(successful) and has been cancelled now.
	 *
	 */
	const iPAYMENT_ACCOUNT_VALIDATED_CANCELLED = 19980;
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
	 * Defines the ID of the State that indicates that Card is not enrolled for 3DS by Issuer
	 *
	 */
	const iPAYMENT_3DS_CARD_NOT_ENROLLED = 2004;
    /**
     * Defines the ID of the State that indicates that payment requires 3d verification
     *
     */
    const iPAYMENT_3DS_VERIFICATION_STATE = 2005;
    /**
     * Defines the ID of the State that indicates that payment has successfully completed 3DS authentication
     *
     */
    const iPAYMENT_3DS_SUCCESS_STATE = 2006;

	/**
	 * Defines the ID of the State that indicates that payment is being using a voucher
	 *
	 */
	const iPAYMENT_WITH_VOUCHER_STATE = 2007;
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
     * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
     * when doing an Authorisation due to incorrect payment information
     *
     */
    const iPAYMENT_REJECTED_INCORRECT_INFO_STATE = 20101;
    /**
     * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
     * when doing an Authorisation
     *
     */
    const iPAYMENT_REJECTED_PSP_UNAVAILABLE_STATE = 20102;
    /**
     * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
     * when doing an Authorisation
     *
     */
    const iPAYMENT_REJECTED_3D_SECURE_FAILURE_STATE = 20103;

    /**
     * Defines the ID of the State that indicates that the payment was rejected by the Payment Service Provider (PSP)
     * when doing an Authorisation as fraud check failed.
     *
     */
    const iPAYMENT_REJECTED_TXN_UNDER_REVIEW_STATE = 20104;

    /**
     * Defines the ID of the State that indicates that the payment was not complete due to time out error
     * from PSP or Issuer and system does not have the final status of payment yet
     *  
     */
    const iPAYMENT_TIME_OUT_STATE = 20109;
    /**
     * Defines the ID of the State that indicates that the payment was not complete due to time out error
     * from PSP or Issuer and system does not have the final status of payment yet
     *
     */
    const iPSP_TIME_OUT_STATE = 20108;
	/**
	 * Defines the ID of the State that indicates that the payment was declined by the Payment Service Provider (PSP)
	 * when doing a Capture
	 *
	 */
	const iPAYMENT_DECLINED_STATE = 2011;
    /**
     * Defines the ID of the State that indicates that payment has successfully completed 3DS authentication
     *
     */
    const iPAYMENT_3DS_FAILURE_STATE = 2016;
	/**
	 * Defines the ID of the State that indicates that 3DS authentication successfully completed and authorization not attempted
	 * due to rule matched
	 */
	const iPAYMENT_3DS_SUCCESS_AUTH_NOT_ATTEMPTED_STATE = 2017;
	/**
	 * Defines the ID of the State that indicates that payment has accidentally been duplicated by DIBS
	 *
	 */
	const iPAYMENT_DUPLICATED_STATE = 2019;

	/**
	 * Defines the ID of the State that indicates that payment has been settled
	 *
	 */
	const iPAYMENT_SETTLED_STATE = 2020;

    /**
     * Defines the ID of the State that indicates that payment transaction has been tokenized
     *
     */
    const iPAYMENT_TOKENIZATION_COMPLETE_STATE = 2030;

    /**
     * Defines the ID of the State that indicates that payment transaction has failed tokenized
     *
     */
    const iPAYMENT_TOKENIZATION_FAILURE_STATE = 2031;

    /**
     * Defines the ID of the State that indicates that payment transaction accepted by fraud check
     *
     */
    const iPAYMENT_FRAUD_CHECK_COMPLETE_STATE = 2040;

    /**
     * Defines the ID of the State that indicates that payment transaction rejected by fraud check
     *
     */
    const iPAYMENT_FRAUD_CHECK_FAILURE_STATE = 2041;

    /**
     * Defines the ID of the State that indicates that Tokenization request successfully completed
     *
     */
    const iCARD_TOKENIZE_SUCCESS = 2100;

    /**
     * Defines the ID of the State that indicates that Tokenization request failed
     *
     */
    const iCARD_TOKENIZE_FAILED = 2101;


    /**
     * Defines the ID of the State that indicates that Session is created
     *
     */
    const iSESSION_CREATED = 4001;


    /**
     * Defines the ID of the State that indicates that Session is expired
     *
     */

    const iSESSION_EXPIRED = 4010;

    /**
     * Defines the ID of the State that indicates that Session failed
     *
     */
    const iSESSION_FAILED = 4020;

    /**
     * Defines the ID of the State that indicates that Session failed due to maximum transaction attempts
     *
     */
    const iSESSION_FAILED_MAXIMUM_ATTEMPTS = 4021;
    
    /**
     * Defines the ID of the State that indicates that Session complete failed
     *
     */
    const iSESSION_COMPLETED = 4030;

    /**
     * Defines the ID of the State that indicates that Session Partially Completed
     *
     */
    const iSESSION_PARTIALLY_COMPLETED = 4031;

    /**
	 * Defines the ID of the State that indicates that the payment has been initiated for captured.
	 *
	 */
	const iPAYMENT_CAPTURE_INITIATED_STATE = 20012;

	/**
	 * Defines the ID of the State that indicates that a previously authorized payment has been initiated for cancel.
	 *
	 */
	const iPAYMENT_CANCEL_INITIATED_STATE = 20022;

	/**
	 * Defines the ID of the State that indicates that previously captured payment has been initiated for refund.
	 *
	 */
	const iPAYMENT_REFUND_INITIATED_STATE = 20032;

	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Initiated
	 *
	 */
	const iPRE_FRAUD_CHECK_INITIATED_STATE = 3010;

	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Result ACCEPTED
	 *
	 */
	const iPRE_FRAUD_CHECK_ACCEPTED_STATE = 3011;

	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Service Unavailable
	 *
	 */
	const iPRE_FRAUD_CHECK_UNAVAILABLE_STATE = 3012;

	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Result Unknown
	 *
	 */
	const iPRE_FRAUD_CHECK_UNKNOWN_STATE = 3013;

	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Result Review
	 *
	 */
	const iPRE_FRAUD_CHECK_REVIEW_STATE = 3014;
	/**
	 * Defines the ID of the State that indicates that Pre-Auth Fraud Result Rejected
	 *
	 */
	const iPRE_FRAUD_CHECK_REJECTED_STATE = 3015;

	/**
	 * Defines the ID of the State that indicates that PRE-Auth Fraud Connection Failed
	 *
	 */
	const iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE = 3016;

    /**
     * Defines the ID of the State that indicates that PRE-Auth Review Success
     *
     */
    const iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE = 3017;

    /**
     * Defines the ID of the State that indicates that PRE-Auth Review Fail
     *
     */
    const iPRE_FRAUD_CHECK_REVIEW_FAIL_STATE = 3018;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Initiated
	 *
	 */
	const iPOST_FRAUD_CHECK_INITIATED_STATE = 3110;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Result ACCEPTED
	 *
	 */
	const iPOST_FRAUD_CHECK_ACCEPTED_STATE = 3111;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Service Unavailable
	 *
	 */
	const iPOST_FRAUD_CHECK_UNAVAILABLE_STATE = 3112;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Result Unknown
	 *
	 */
	const iPOST_FRAUD_CHECK_UNKNOWN_STATE = 3113;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Result Review
	 *
	 */
	const iPOST_FRAUD_CHECK_REVIEW_STATE = 3114;


	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Result Rejected
	 *
	 */
	const iPOST_FRAUD_CHECK_REJECTED_STATE = 3115;

	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Connection Failed
	 *
	 */
	const iPOST_FRAUD_CHECK_CONNECTION_FAILED_STATE = 3116;
	/**
	 * Defines the ID of the State that indicates that POST-Auth Fraud Check Skipped due to rule matched
	 *
	 */
	const iPOST_FRAUD_CHECK_SKIP_RULE_MATCHED_STATE = 3117;

    /**
     * Defines the ID of the State that indicates that POST-Auth Review Success
     *
     */
    const iPOST_FRAUD_CHECK_REVIEW_SUCCESS_STATE = 3118;

    /**
     * Defines the ID of the State that indicates that POST-Auth Review Fail
     *
     */
    const iPOST_FRAUD_CHECK_REVIEW_FAIL_STATE = 3119;


    /**
     * Defines the ID of the State that indicates that Payment retried using dynamic routing
     *
     */
	const iPAYMENT_RETRIED_USING_DR_STATE = 7010;

	/**
     * Defines the ProductType for the ticket
     *
     */
    const iPrimaryProdTypeBase = 100;

    /**
     * Defines the ProductType for the Ancillary
     *
     */
    const iAncillaryProdTypeBase = 200;

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
	 * Defined Type ID for a New Card which are not stored card and not associated with End-User's prepaid account at mPoint side.
	 * 
	 */	
	const iNEW_CARD_PURCHASE_TYPE = 10091;

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
	 * Unique ID for the Payment Service Provider: AMEX Express Checkout
	 *
	 */
	const iAMEX_EXPRESS_CHECKOUT_PSP = 16;
	
	/**
	 * Unique ID for the Payment Service Provider: Data Cash
	 *
	 */
	const iDATA_CASH_PSP = 17;
	
	/**
	 * Unique ID for the Payment Service Provider: Wire Card
	 *
	 */
	const iWIRE_CARD_PSP = 18;
	
	/**
	 * Unique ID for the Payment Service Provider: DSB
	 *
	 */
	const iDSB_PSP = 19;
	
	/**
	 * Unique ID for the Payment Service Provider: GlobalCollect
	 *
	 */
	const iGLOBAL_COLLECT_PSP = 21;

	/**
	 * Unique ID for the Payment Service Provider: GlobalCollect
	 *
	 */
	const iSECURE_TRADING_PSP = 22;
	
	/**
	 * Unique ID for the Payment Service Provider: PayFort
	 *
	 */
	const iPAYFORT_PSP = 23;
	
	/**
	 * Unique ID for the Payment Service Provider: PayPal
	 *
	 */
	const iPAYPAL_PSP = 24;
	
	/**
	 * Unique ID for the Payment Service Provider: CCAvenue
	 *
	 */
	const iCCAVENUE_PSP = 25;
	
	/**
	 * Unique ID for the Payment Service Provider: 2C2P
	 *
	 */
	const i2C2P_PSP = 26;
	
	/**
	 * Unique ID for the Payment Service Provider: MayBank
	 *
	 */
	const iMAYBANK_PSP = 27;
	
	/**
	 * Unique ID for the Payment Service Provider: Public Bank
	 *
	 */
	const iPUBLIC_BANK_PSP = 28;
	
	/**
	 * Unique ID for the Payment Service Provider: AliPay
	 *
	 */
	const iALIPAY_PSP = 30;

	/**
	 * Unique ID for the Payment Service Provider: Qiwi
	 *
	 */
	const iQIWI_PSP = 31;
	
	/**
	 * Unique ID for the Payment Service Provider: POLi
	 *
	 */
	const iPOLI_PSP = 32;
	
	/**
	 * Unique ID for the Payment Service Provider: MobilePay Online
	 *
	 */
	const iMOBILEPAY_ONLINE_PSP = 33;


    /**
     * Unique ID for the Acquirer: NETS
     *
     */
    const iNETS_ACQUIRER = 35;

	/**
	 * Unique ID for the Payment Service Provider: Klarna
	 *
	 */
	const iKLARNA_PSP = 37;

	/**
	 * Unique ID for the Payment Service Provider: Trustly
	 *
	 */
	const iTRUSTLY_PSP = 39;
	
	
    /**
     * Unique ID for the Payment Service Provider: mVault
     *
     */
    const iMVAULT_PSP = 36;

    /**
     * Unique ID for the Payment Service Provider: Pay Tabs
     *
     */
    const iPAY_TABS_PSP = 38;
    
	/**
	 * Unique ID for the Payment Service Provider: Android Pay
	 *
	 */
	const iANDROID_PAY_PSP = 20;
	
	/**
	 * Unique ID for the Payment Service Provider: 2c2p-Alc
	 *
	 */
	const i2C2P_ALC_PSP = 40;


    /**
    * Unique ID for the MPI : Nets
    *
    */
    const iNETS_MPI = 42;


    /**
     * Unique ID for the Payment Service Provider: Citcon - WeChat Pay
     *
     */
    const iCITCON_PSP = 41;

    /**
     * Unique ID for the Payment Service Provider: AliPay Chinese
     *
     */
    const iALIPAY_CHINESE_PSP = 43;


    /**
     * Unique ID for the Payment Service Provider: Google Pay
     *
     */
    const iGOOGLE_PAY_PSP = 44;

    /**
     * Unique ID for the Payment Service Provider: PPRO
     *
     */
    const iPPRO_GATEWAY = 46;

    /**
     * Unique ID for the MPI : Modirum
     *
     */
    const iMODIRUM_MPI = 47;

    /**
     * Unique ID for the PSP : CHUBB
     *
     */
    const iCHUBB_PSP = 48;


    /**
     * Unique ID for the Acquirer: UATP
     *
     */
    const iUATP_ACQUIRER = 49;

    /**
     * Unique ID for the Card Account Service: UATP
     *
     */
    const iUATP_CARD_ACCOUNT = 50;

    /**
     * Unique ID for the Net banking aggregator : eGHL
     *
     */
    const iEGHL_PSP = 51;

	/**

    /**
     * Unique ID for the Acquirer: Amex
     *
     */
    const iAMEX_ACQUIRER = 45;

    /**
     * Unique ID for the Acquirer: Chase Payment
     *
     */
    const iCHASE_ACQUIRER = 52;

    /**
     * Unique ID for the Payment Service Provider: PayU
     *
     */
    const iPAYU_PSP = 53;

    /**
     * Unique ID for the Payment Service Provider: Cielo
     *
     */
    const iCielo_ACQUIRER = 54;

    /**
     * Unique ID for the Payment Service Provider: Global Payments
     *
     */
    const iGlobal_Payments_PSP = 56;


    /**
     * Unique ID for the Payment Service Provider: MADA MPGS
     *
     */
    const iMADA_MPGS_PSP = 57;
    /**
     * Unique ID for the Payment Service Provider: VERITRANS4G
     *
     */
    const iVeriTrans4G_PSP = 59;
    /**
     * Unique ID for the Payment Service Provider: MADA MPGS
     *
     */
    const iCellulant_PSP = 58;
    /**
     * Unique ID for the Payment Service Provider: EZY
     *
     */
    const iEZY_PSP = 60;
	/**
	 * Unique ID for the Payment Service Provider: First-Data
	 *
	 */
	const iFirstData_PSP = 62;
    /**
     * Unique ID for the Payment aggregator: DRAGONPAY
     *
     */
	const iDragonPay_AGGREGATOR = 61;

	/**
	 * Unique ID for the Payment aggregator: Cybersource
	 *
	 */
	const iCyberSource_PSP = 63;

	/**
	 * Unique ID for the Cyber Source Fraud Service Provider
	 *
	 */
	const iCYBER_SOURCE_FSP = 64;
	/**
	 * Unique ID for the Cyber Source Fraud Service Provider
	 *
	 */
	const iCEBU_RMFSS_FSP = 65;
	/**
	 * Unique ID for the Payment APM: SWISH
	 *
	 */
	const iSWISH_APM = 66;
    /**
     * Unique ID for the Payment Service Provider: GrabPay
	 *
	 */
	const iGRAB_PAY_PSP = 67;
	/**
	 * Unique ID for transaction's made using paymaya
	 *
	 */
	const iPAYMAYA_WALLET = 68;

	/**
	 * Unique PSP ID for transaction's made using CEBU Payment Center
	 *
	 */
	const iCEBUPAYMENTCENTER_APM = 69;
	/**
	 * Unique ID for the Net banking aggregator : SAFETYPAY
	 *
	 */
	const iSAFETYPAY_AGGREGATOR = 70;

	/**
	 * Unique PSP ID for transaction's made using CEBU Travel Fund
	 *
	 */
	const iTRAVELFUND_VOUCHER = 71;

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
	 * Unique Card ID for transaction's made using Invoice paymnet
	 *
	 */
	const iINVOICE = 24;
	/**
	 * Unique Card ID for transaction's made using AMEX Express Checkout Wallet
	 *
	 */
	const iAMEX_EXPRESS_CHECKOUT_WALLET = 25;
	/**
	 * Unique Card ID for transaction's made using Vouchers
	 *
	 */
	const iVOUCHER_CARD = 26;
	/**
	 * Unique Card ID for transaction's made using Android Pay
	 *
	 */
	const iANDROID_PAY_WALLET = 27;
	/**
	 * Unique Card ID for transaction's made using Android Pay
	 *
	 */
	const iPAYPAL_PAY_WALLET = 28;
	/**
	 * Unique Card ID for transaction's made using MobilePay Online
	 *
	 */
	const iMOBILEPAY_ONLINE = 30;
	/**
	 * Unique Card ID for transaction's made using SADAD
	 *
	 */
	const iSADAD = 31;
	/**
	 * Unique Card ID for transaction's made using AliPay
	 *
	 */
	const iALIPAY_WALLET = 32;
	/**
	 * Unique Card ID for transaction's made using POLi
	 *
	 */
	const iPOLI_WALLET = 34;
	/**
	 * Unique Card ID for transaction's made using Qiwi
	 *
	 */
	const iQIWI_WALLET = 33;	
	/**
	 * Unique Card ID for transaction's made using Klarna
	 *
	 */
	const iKLARNA_PAY = 36;
	/**
	 * Unique Card ID for transaction's made using Trustly
	 *
	 */
	const iTRUSTLY_PAY = 38;
	/**
	 * Unique Card ID for transaction's made using the co-branded VISA / Dankort
	 *
	 */
	const iVISA_DANKORT_CARD = 37;
	
    /**
     * Unique Card ID for transaction's made using mVault
     *
     */
    const iMVAULT_WALLET = 35;


    /**
     * Unique Card ID for transaction's made using Citcon WeChat Pay
     *
     */
    const iCITCON_WECHAT_WALLET = 39;

    /**
     * Unique Card ID for transaction's made using Alipay Chinese
     *
     */
    const iALIPAY_CHINESE_WALLET = 40;

    /**
     * Unique Card ID for transaction's made using Google Pay
     *
     */
    const iGOOGLE_PAY_WALLET = 41;

    /**
     * Unique Card ID for transaction's made using PPRO
     *
     */
    const iPPRO_PAY = 42;

    /**
     * Unique Card ID for transaction's made using AFFIN_BANK
     *
     */
    const iAFFIN_BANK = 43;

    /**
     * Unique Card ID for transaction's made using AMBANK
     *
     */
    const iAMBANK = 44;

    /**
     * Unique Card ID for transaction's made using BANCONTACT
     *
     */
    const iBANCONTACT = 45;

    /**
     * Unique Card ID for transaction's made using CIMB_CLICKS
     *
     */
    const iCIMB_CLICKS = 46;

    /**
     * Unique Card ID for transaction's made using DRAGONPAY
     *
     */
    const iDRAGONPAY = 47;

    /**
     * Unique Card ID for transaction's made using ENETS
     *
     */
    const iENETS = 48;

    /**
     * Unique Card ID for transaction's made using ENTERCASH
     *
     */
    const iENTERCASH = 49;

    /**
     * Unique Card ID for transaction's made using EPS
     *
     */
    const iEPS = 50;

    /**
     * Unique Card ID for transaction's made using ESTONIAN_BANKS
     *
     */
    const iESTONIAN_BANKS = 51;

    /**
     * Unique Card ID for transaction's made using GIROPAY
     *
     */
    const iGIROPAY = 52;

    /**
     * Unique Card ID for transaction's made using IDEAL
     *
     */
    const iIDEAL = 53;

    /**
     * Unique Card ID for transaction's made using LATVIAN_BANKS
     *
     */
    const iLATVIAN_BANKS = 54;

    /**
     * Unique Card ID for transaction's made using LITHUANIAN_BANKS
     *
     */
    const iLITHUANIAN_BANKS = 55;

    /**
     * Unique Card ID for transaction's made using MAYBANK2U
     *
     */
    const iMAYBANK2U = 56;

    /**
     * Unique Card ID for transaction's made using MULTIBANCO
     *
     */
    const iMULTIBANCO = 57;

    /**
     * Unique Card ID for transaction's made using MYCLEAR_FPX
     *
     */
    const iMYCLEAR_FPX = 58;

    /**
     * Unique Card ID for transaction's made using PAYSBUY
     *
     */
    const iPAYSBUY = 59;

    /**
     * Unique Card ID for transaction's made using PAYU
     *
     */
    const iPAYU = 60;

    /**
     * Unique Card ID for transaction's made using PRZELEWY24
     *
     */
    const iPRZELEWY24  = 61;

    /**
     * Unique Card ID for transaction's made using RHB BANK
     *
     */
    const iRHB_BANK = 62;

    /**
     * Unique Card ID for transaction's made using SAFETYPAY
     *
     */
    const iSAFETYPAY = 63;

    /**
     * Unique Card ID for transaction's made using SEPA
     *
     */
    const iSEPA = 64;

    /**
     * Unique Card ID for transaction's made using SINGPOST
     *
     */
    const iSINGPOST = 65;

    /**
     * Unique Card ID for transaction's made using SOFORT BANKING
     *
     */
    const iSOFORT_BANKING = 66;

    /**
     * Unique Card ID for transaction's made using UNIONPAY
     *
     */
    const iUNIONPAY = 67;

    /**
     * Unique Card ID for transaction's made using VERKKOPANKKI
     *
     */
    const iVERKKOPANKKI = 68;

    /**
     * Unique Card ID for transaction's made using KNET
     *
     */
    const iKNET = 69;

    /**
     * Unique Card ID for transaction's made using BENEFIT
     *
     */
    const iBENEFIT = 70;

    /**
     * Unique Card ID for transaction's made using MADA
     *
     */
    const iMADA = 71;

    /**
     * Unique Card ID for transaction's made using SADAD v2
     *
     */
    const iSADADV2 = 72;

    /**
     * Unique Card ID for transaction's made using FPX
     *
     */
    const iFPX = 73;

    /**
     * Unique Card ID for transaction's made using CELLULANT
     *
     */
    const iCELLULANT = 86;

	/**
	 * Unique Card ID for transaction's made using OMANNET
	 *
	 */
	const iOMANNET = 87;
	
	/**
	 * Unique Card ID for transaction's made using DRAGONPAY OFFLINE
	 *
	 */
	const iDRAGONPAYOFFLINE = 88;
	/**
	 * Unique Card ID for transaction's made using Grab Pay
	 *
	 */
	const iGRAB_PAY = 94;
	
	/**
	 * Unique Card ID for transaction's made using SWISH
	 *
	 */
	const iSWISH = 92;
	
	/**
	 * Unique Card ID for transaction's made using paymaya wallet
	 *
	 */
	const iPAYMAYA = 95;

	/**
	 * Unique Card ID for transaction's made using CEBU Payment Center Offline
	 *
	 */
	const iCEBUPAYMENTCENTEROFFLINE = 96;
	/**
	 * Unique Card ID for transaction's made using PSE
	 *
	 */
	const iPSE = 97;
	/**
	 * Unique Card ID for transaction's made using BOLETO for offline
	 *
	 */
	const iBOLETO = 98;/**
	* Unique Card ID for transaction's made using Efecty for offline
	*
	*/
	const iEFECTY = 99;/**
	* Unique Card ID for transaction's made using BancoDe Bogata for offline
	*
	*/
	const iBANCODEBOGATA  = 100;
	
	

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

	/**
	 * mPoint card states as also listed in System.CardState_Tbl
	 */
	const iCARD_ENABLED_STATE = 1;
	const iCARD_DISABLED_BY_MERCHANT_STATE = 2;
	const iCARD_DISABLED_BY_PSP_STATE = 3;
	const iCARD_PREREQUISITE_NOT_MET_STATE = 4;
	const iCARD_TEMPORARILY_UNAVAILABLE_STATE = 5;

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

	/*
	 * Failed Transaction API modes
	 * 1. Get All Failed Transaction.
	 * 2. Get All Initialization Failed Txn
	 * 3. Get All Txns that have failed authorization with PSP
	 *
	 * */
	const iFAILED_TXNS_ALL = 1;
	const iFAILED_TXNS_FAILED_INIT = 2;
	const iFAILED_TXNS_FAILED_AUTH = 3;

	/*
	 * Payment Processor types in mPoint
	 * */
	const iPROCESSOR_TYPE_PSP = 1;
	const iPROCESSOR_TYPE_ACQUIRER = 2;
	const iPROCESSOR_TYPE_WALLET = 3;
	const iPROCESSOR_TYPE_APM = 4;
	const iPROCESSOR_TYPE_VIRTUAL = 5;
	const iPROCESSOR_TYPE_MPI = 6;
	const iPROCESSOR_TYPE_GATEWAY = 7;
	const iPROCESSOR_TYPE_TOKENIZATION = 8;
	const iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY = 9;
	const iPROCESSOR_TYPE_POST_FRAUD_GATEWAY = 10;
	const iPROCESSOR_TYPE_VOUCHER = 11;



	const iRETRIAL_TYPE_TIMEBASED = 1;
	const iRETRIAL_TYPE_RESPONSEBASED = 2;
	const iRETRIAL_TYPE_MAXATTEMPTBASED = 3;

	/*
	 * Settlement File status
	 */
	const sSETTLEMENT_REQUEST_ACTIVE = "active";
	const sSETTLEMENT_REQUEST_WAITING = "waiting";
	const sSETTLEMENT_REQUEST_ERROR = "error";
	const sSETTLEMENT_REQUEST_FAIL = "fail";
	const sSETTLEMENT_REQUEST_ACCEPETED = "accepted";
	const sSETTLEMENT_REQUEST_PARTIALLY_ACCEPTED = "accepted with error";
	const sSETTLEMENT_REQUEST_OK = "OK";
	const sSETTLEMENT_REQUEST_PROCESSING = "PROCESSING";
	const sFileExpireThreshold = 'FILE_EXPIRY';

	/*
	 * Additional Property Scope
	 */
	const iInternalProperty = 0;
	const iPrivateProperty = 1;
	const iPublicProperty = 2;

    /*
	 * Unique Id for payment captured and callback sent
	 */
    const iPAYMENT_CAPTURED_AND_CALLBACK_SENT = 1002;

	/*
	 * Passbook Operation Codes
	 */
    const iAuthorizeRequested = 5010;
	const iCaptureRequested = 5011;
	const iCancelRequested = 5012;
	const iRefundRequested = 5013;
	const iInitializeRequested = 5014;
	const iVoidRequested = 5015;

	/*
	 * Passbook Error Code
	 */
	const iInvalidOperation = 6100;
	const iOperationNotAllowed = 6200;
	const iAmountIsHigher = 6201;

	/*
	 * Passbook Error Code
	 */
	const sPassbookStatusPending = 'pending';
	const sPassbookStatusInProgress = 'inprogress';
	const sPassbookStatusDone = 'done';
	const sPassbookStatusInvalid = 'invalid';
	const sPassbookStatusError = 'error';

	/*
	 * External Systems ENUM
	 */
	const iForeignExchange = 1;

    /*
     * Default Profile Expiry (in days) for profiles created in mProfile for Velocity transactions
     */
    const iProfileExpiry = 365;

    /*
     * Default mProfile APIs for Velocity to create profile
     */
    const sSaveProfileEndPoint = "/mprofile/save-profile";

    /*
     * Default mProfile APIs for Velocity to retrieve profile
     */
    const sGetProfileEndPoint = "/mprofile/get-profile";

    /*
	 * Card payment types in mPoint
	 * */
    const iPAYMENT_TYPE_CARD = 1;
    const iPAYMENT_TYPE_VOUCHER = 2;
    const iPAYMENT_TYPE_WALLET = 3;
    const iPAYMENT_TYPE_APM = 4;
    const iPAYMENT_TYPE_CARD_TOKEN = 5;
    const iPAYMENT_TYPE_VIRTUAL = 6;
    const iPAYMENT_TYPE_ONLINE_BANKING = 7;
	const iPAYMENT_TYPE_OFFLINE = 8;

    /*
	 * Alternate routes to authorize payment  if primary fails during authorize
	 * */
    const iPRIMARY_ROUTE = 1;
    const iSECOND_ALTERNATE_ROUTE = 2;
    const iTHIRD_ALTERNATE_ROUTE = 3;

    /*
	 * Transaction types in mPoint
	 * */
    const iTRANSACTION_TYPE_SHOPPING_ONLINE = 1;
    const iTRANSACTION_TYPE_SHOPPING_OFFLINE = 2;
    const iTRANSACTION_TYPE_SELF_SERVICE_ONLINE = 3;
    const iTRANSACTION_TYPE_SELF_SERVICE_OFFLINE = 4;

    /*
     * Defines unique ID of the State that indicates payment soft declined
     */
    const iPAYMENT_SOFT_DECLINED_STATE = 20103;

    /*
     * Define upper and lower limit for soft decline status sub code
     */
    const iSOFT_DECLINED_SUB_CODE_LOWER_LIMIT = 2010300;
    const iSOFT_DECLINED_SUB_CODE_UPPER_LIMIT = 2010399;

   /*
    * Defines unique ID of the State that indicates payment decline due to
    * authentication failed
    */
    const iAUTHENTICATION_DECLINED_SUB_CODE = 2010406;

	/*
     * Define substatus code of 2004 status code
     */
	const iPAYMENT_3DS_CARD_NOT_ENROLLED_SUB_STATE = 2004002;
	const iPAYMENT_3DS_CARD_NOT_ENROLLED_CACHE_SUB_STATE = 2004003;
	const iPAYMENT_3DS_CARD_NOT_ENROLLED_NO_DIR_FOUND_SUB_STATE = 2004095;
	const iPAYMENT_3DS_CARD_NOT_ENROLLED_NO_VER2_DIR_FOUND_SUB_STATE = 2004096;

	/*
     * Define substatus code of 2005 status code
     */
	const iPAYMENT_3DS_VERIFICATION_CARD_ENROLLED_ATTEMPT_V1_SUB_STATE = 2005001;
	const iPAYMENT_3DS_VERIFICATION_CARD_ENROLLED_ATTEMPT_V2_SUB_STATE = 2005002;
	const iPAYMENT_3DS_VERIFICATION_UNKNOWN_HTML_FORMAT_SUB_STATE = 2005003;

	/*
     * Define substatus code of 2006 status code
     */
	const iPAYMENT_3DS_SUCCESS_FULLY_AUTHENTICATED_SUB_STATE = 2006001;
	const iPAYMENT_3DS_SUCCESS_ATTEMPTED_SUB_STATE = 2006004;
	/*
     * Define substatus code of 2016 status code
     */
	const iPAYMENT_3DS_FAILURE_NOT_AUTHENTICATED_SUB_STATE = 2016000;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_GRAY_AREA_SUB_STATE = 2016005;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_ERROR_SUB_STATE = 2016006;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_NETWORK_ERROR_SUB_STATE = 2016091;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_TIMEOUT_ERROR_SUB_STATE = 2016092;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_CONFIGURATION_ERROR_SUB_STATE = 2016093;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_INPUT_ERROR_SUB_STATE = 2016094;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_TXN_NOT_FOUND_SUB_STATE = 2016097;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_SYSTEM_ERROR_SUB_STATE = 2016099;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_NOT_APPLICABLE_SUB_STATE = 2016998;
	const iPAYMENT_3DS_FAILURE_AUTHENTICATED_UNKNOWN_ERROR_SUB_STATE = 2016999;



}

abstract class AutoCaptureType
{
	/*
   * Auto-Capture flag for mPoint to do not perform auto-capture
   */
	const eRunTimeAutoCapt = 1;
	/*
   * Auto-Capture flag for mPoint to perform PSP level auto-capture
   */
	const ePSPLevelAutoCapt = 2;
	/*
   * Auto-Capture flag for mPoint to perform Merchant level auto-capture
   */
	const eMerchantLevelAutoCapt = 3;
	/*
   * Auto-Capture flag for mPoint to perform batch-capture
   */
	const eBatchCapt = 4;
}
?>
