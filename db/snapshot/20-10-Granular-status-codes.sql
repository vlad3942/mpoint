alter table log.state_tbl alter column name type character varying(120);

UPDATE log.state_tbl
SET name = 'The amount is invalid.', module = 'sub-code', func = ''
    WHERE id = 2010101;

UPDATE log.state_tbl
SET name = 'Invalid Access Credentials', module = 'sub-code', func = ''
WHERE id = 2010201;

UPDATE log.state_tbl
SET name = 'Internal error / general system error', module = 'sub-code', func = ''
WHERE id = 2010301;

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010102, 'Card Number is invalid.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010103, 'Installment field value is invalid', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010104, 'Invalid Order Number value', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010105, 'Missing Mandatory Fields / Data not present / invalid data field (general error code when any field is invalid)', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010106, 'Invalid MerchantID', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010107, 'Invalid TransactionID', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010108, 'Invalid Transaction date', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010109, 'Invalid CVC', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010110, 'Invalid Payment Type', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010112, 'Invalid Expiry Date', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010113, 'Invalid 3DS Secure values', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010114, 'Invalid Card type', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010115, 'Invalid Request version', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010116, 'Return URL is not set.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010117, 'Invalid currency code.', 'sub-code', '');


INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010202, 'Invalid PIN OR OTP', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010203, 'Insufficient funds / over credit limit', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010204, 'Expired Card', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010205, 'Unable to authorize', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010206, 'Exceeds withdrawal count limit OR Authentication requested', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010207, 'Do Not Honor', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010208, 'Transaction not permitted to user', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010209, 'Transaction Aborted by user / Card Holder Abandoned 3DS/Wallet', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010210, 'User Inactive or Session Expired', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010211, 'Only a partial amount was approved', 'sub-code', '');


INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010302, 'Parse error / invalid Request', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010303, 'Service not available.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010304, 'Time out', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010305, 'Payment is cancelled / Payment reversed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010306, 'Waiting for upstream response', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010307, 'No Routing Available', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010308, 'System DB Error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010309, 'Invalid Operation / Operation Rejected', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010310, 'Transaction already in progress /  Duplicate Transaction / Duplicate Order Number', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010311, 'Endpoint not supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010312, 'Transaction not permitted to terminal', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010313, 'Invalid merchant account / configuration / API permission missing', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010314, 'Transaction rejected by Issuer / Authorization failed /Transaction Failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010315, 'EMI not available', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010316, 'Void not supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010317, 'Already Captured', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010318, 'Retry limit exceeded', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010319, 'Invalid Capture attempted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010320, 'Transaction Not Posted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010321, 'Recurring Payment Not Supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010322, 'Stored card option is disabled.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010323, 'Request Authentication Failed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010324, 'Unable to decrypt request.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010325, 'Transaction ID / EP Generation Failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010326, 'Installment Payment is disabled.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010327, 'Ticket issue failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010328, 'China Union Pay sign failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010329, 'Card type is not allowed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010330, 'Issuing bank unavailable.	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010331, 'Transaction exceeds the approved limit	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010332, 'Cannot void as capture or credit is submitted	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010333, 'Cannot Refund / You requested a credit for a capture that was previously voided.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010334, 'Credit amount exceeds maximum allowed for your Merchant account.', 'sub-code', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010401, 'FRAUD Suspicion / Rejected', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010402, 'Address verification failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010403, 'Card Acceptor should contact accquirer', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010404, 'Security Voilation', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010405, 'Card is Blocked due to fraud', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010406, '3D Secure authentication failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010407, 'Fraud Stolen Card', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010408, 'Compliance ERROR', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010409, 'Transaction Previously declined', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010410, 'E-commerce declined', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010411, 'Card restricted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010412, 'Card Function Not Supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010413, 'Physical Card Error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010414, 'BIN check failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010415, 'Validation Check Failed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010416, 'CVN did not match	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010417, 'The customer matched an entry on the processor’s negative file.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010418, 'Strong customer authentication (SCA) is required for this transaction.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010419, 'authorization request was approved by the issuing bank but declined by Gateway/processor', 'sub-code', '');