<?php

interface Refundable
{
	public function refund($iAmount = -1);

	public function void($iAmount = -1);

}