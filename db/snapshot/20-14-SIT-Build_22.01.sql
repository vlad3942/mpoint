--CMP-4191
update client.additionalproperty_tbl set value='orderid ::= (transaction.orderid)
attempt ::= (@attempt)
date ::= {date.ymd}
invoiceid ::= (psp-config/@id)=="24"="NM"<orderid><date><attempt>' where key='invoiceidrules' and externalid=10020;