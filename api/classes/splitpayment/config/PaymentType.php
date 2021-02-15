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
    private int $index;

    public function __construct(int $id, int $index)
    {
        $this->id = $id;
        $this->index = $index;
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
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function toXML(): string
    {
        return "<paymentType><id>$this->id</id><index>$this->index</index></paymentType>";
    }
}