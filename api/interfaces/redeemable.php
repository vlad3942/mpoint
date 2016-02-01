<?php

interface Redeemable
{
	public function redeem($iVoucherID, $iAmount = -1);
}