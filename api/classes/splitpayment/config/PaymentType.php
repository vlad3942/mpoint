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
    private bool $isClubbable;

    public function __construct(int $id, int $index, bool $isClubbable = FALSE)
    {
        $this->id = $id;
        $this->index = $index;
        $this->isClubbable = $isClubbable;
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
        return "<payment_type><id>$this->id</id><index>$this->index</index><is_clubbable>". ($this->isClubbable ? 'true' : 'false') ."</is_clubbable></payment_type>";
    }

    /**
     * @return bool
     */
    public function isClubbable(): bool
    {
        return $this->isClubbable;
    }

    /**
     * @param bool $isClubbable
     */
    public function setIsClubbable(bool $isClubbable): void
    {
        $this->isClubbable = $isClubbable;
    }
}