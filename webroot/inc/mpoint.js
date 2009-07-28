
function convert(aExchangeRates, aCountries, aFees, srcid, tgtid, amount, er, la, fee)
{
	amount = Number(amount);
	// Calculate and Display Exchange Rate
	document.getElementById('exchange-rate').innerHTML = (1 / (aExchangeRates[aCountries[srcid] ] / aExchangeRates[aCountries[tgtid] ]) ).toFixed(2);
	
	// Calculate and Display Local Amount
	if (amount > 0)
	{
		document.getElementById('local-amount').innerHTML = aCountries[tgtid] +' '+ Number(aExchangeRates[aCountries[srcid] ] * amount / aExchangeRates[aCountries[tgtid] ]).toFixed(2);
	}
	else { document.getElementById('local-amount').innerHTML = aCountries[tgtid]; }
	
	// Calculate Transfer Fee
	var fee = -1;
	if (aFees[tgtid]['basefee'] + aFees[tgtid]['share'] * amount > aFees[tgtid]['minfee'])
	{
		fee = aFees[tgtid]['basefee'] + aFees[tgtid]['share'] * amount;
	}
	else { fee = aFees[tgtid]['minfee'] / 100; }
	
	// Display Transfer Fee and Total
	document.getElementById('fee').innerHTML = aCountries[srcid] +' '+ Number(fee).toFixed(2);
	document.getElementById('total').innerHTML = aCountries[srcid] +' '+ Number(amount + fee).toFixed(2);
}