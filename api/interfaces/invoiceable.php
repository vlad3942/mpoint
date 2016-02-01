<?php
interface Invoiceable
{
	public function invoice($sMsg = "" ,$iAmount = -1);
}