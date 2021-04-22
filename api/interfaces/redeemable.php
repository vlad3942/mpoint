<?php

interface Redeemable
{
	public function redeem(string $iVoucherID = null, float $iAmount = -1);
}