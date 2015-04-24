<?php

interface Refundable
{
    public function refund($iAmount = -1);
}