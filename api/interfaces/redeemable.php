<?php

interface Redeemable
{
	public function redeem(string $iVoucherID, float $iAmount = -1, array $additionalData = array());
}