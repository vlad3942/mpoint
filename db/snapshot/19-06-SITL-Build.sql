INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('invoiceidrules', 'orderid ::= (transaction.orderid)
transactionid ::= (transaction.@id)
attempt ::= (@attempt)
invoiceid ::= <orderid>"CPM"<transactionid><attempt>', true, 10007, 'client', 0);