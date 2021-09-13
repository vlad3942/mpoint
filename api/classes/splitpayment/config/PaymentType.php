<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.splitpayment.config
 * File Name: PaymentType
 */

namespace api\classes\splitpayment\config;


class PaymentType
{
    private int $id;
    private int $sequence;

    public function __construct(int $id, int $sequence)
    {
        $this->id = $id;
        $this->sequence = $sequence;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence(int $sequence): void
    {
        $this->sequence = $sequence;
    }

    /**
     * @return string
     */
    public function toXML(): string
    {
        return "<payment_type><id>$this->id</id><sequence>$this->sequence</sequence></payment_type>";
    }
}