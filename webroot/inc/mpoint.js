
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

function selectCard(obj, id)
{
	obj.className += ' selected';
	var _select = function()
	{
		var img = document.getElementById('card-'+ id +'-image').src;
		var name = document.getElementById('card-'+ id +'-name').innerHTML;

		document.getElementById('card-'+ id +'-image').src = document.getElementById('selected-card-image').src;
		document.getElementById('card-'+ id +'-image').setAttribute('id', 'card-'+ document.getElementById('cardid').value +'-image');
		document.getElementById('card-'+ id +'-name').innerHTML = document.getElementById('selected-card-name').innerHTML;
		document.getElementById('card-'+ id +'-name').setAttribute('id', 'card-'+ document.getElementById('cardid').value +'-name');
		obj.setAttribute('onclick', 'javascript:obj_Menu.select(this, \'selected\'); selectCard(this, '+ document.getElementById('cardid').value +');');

		document.getElementById('selected-card-image').src = img;
		document.getElementById('selected-card-name').innerHTML = name;
		document.getElementById('cardid').value = id;
		
		obj.className = obj.className.replace(' selected', '');
		obj.className = obj.className.replace('selected', '');
	}
	setTimeout(_select, 200);
}