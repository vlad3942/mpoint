<?php

interface Redeemable
{
	public function redeem($iVoucherID, $iAmount = -1, $sessionToken=null);
}