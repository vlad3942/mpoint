<?php
/*
** Adding dummy endpoint for checkout service.
*/


header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<Checkout>
	<AuthenticationOptions>
		<CAvv>test</CAvv>
		<EciFlag>12345</EciFlag>
		<Xid>test</Xid>
		<AuthenticateMethod>3DS</AuthenticateMethod>
	</AuthenticationOptions>
    <Card>
        <BrandId>master</BrandId>
        <BrandName>Mastercard</BrandName>
        <AccountNumber>5435579315709649</AccountNumber>
        <BillingAddress>
            <City>Anytown</City>
            <Country>US</Country>
            <Line1>100 Not A Real Street</Line1>
            <PostalCode>63011</PostalCode>
        </BillingAddress>
        <CardHolderName>Joe Test</CardHolderName>
        <ExpiryMonth>02</ExpiryMonth>
        <ExpiryYear>2016</ExpiryYear>
    </Card>
    <TransactionId>72525</TransactionId>
    <Contact>
        <FirstName>Joe</FirstName>
        <MiddleName>M</MiddleName>
        <LastName>Test</LastName>
        <Gender>M</Gender>
        <DateOfBirth>
            <Year>1975</Year>
            <Month>03</Month>
            <Day>28</Day>
        </DateOfBirth>
        <NationalID>30258374209</NationalID>
        <Country>US</Country>
        <EmailAddress>joe.test@email.com</EmailAddress>
        <PhoneNumber>1-9876543210</PhoneNumber>
    </Contact>
    <ShippingAddress>
        <City>O Fallon</City>
        <Country>US</Country>
        <CountrySubdivision>US-AK</CountrySubdivision>
        <Line1>1 main street</Line1>
        <PostalCode>63368</PostalCode>
        <RecipientName>JoeTest</RecipientName>
        <RecipientPhoneNumber>1-9876543210</RecipientPhoneNumber>
    </ShippingAddress>
    <WalletID>101</WalletID>
    <RewardProgram>
        <RewardNumber>123</RewardNumber>
        <RewardId>1234</RewardId>
        <RewardName>ABC Rewards</RewardName>
        <ExpiryMonth>02</ExpiryMonth>
        <ExpiryYear>2015</ExpiryYear>
    </RewardProgram>
</Checkout>';