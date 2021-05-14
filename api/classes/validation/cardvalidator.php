<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:CardValidator.php
 */

class CardValidator extends ValidateBase
{
    private $card = NULL;

    public function __construct(card $card)
    {
        $this->card = $card;
    }

    /**
     * Performs validation of the CVC to determine whether it is valid.
     * The method will return the following status codes:
     * 7XX - 7 - Card Validation
     * 71X - 1 - CVC Validation
     * 710 - Valid
     * 711 - Length is small than configuration
     * 712 - Length is large than configuration
     * 714 - Undefined
     *
     * @return integer
     */
    public function validateCVC()
    {
        if ($this->getCard() !== NULL) {
            $useCvcLength = strlen($this->getCard()->getCvc());
            if ($useCvcLength === $this->getCard()->getCvcLength()) {
                return 710;
            } elseif ($useCvcLength < $this->getCard()->getCvcLength()) {
                return 711;
            } elseif ($useCvcLength > $this->getCard()->getCvcLength()) {
                return 712;
            }
        }
    }

    /**
     * @return \card|null
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Performs validation of the Card number to determine whether it is valid.
     * This validation is developed on https://en.wikipedia.org/wiki/Luhn_algorithm.
     * The method will return the following status codes:
     *     7XX - 7 - Card Validation
     *     72X - 1 - Card Number Validation
     *     720 - given number is valid card number
     *     721 - number is Undefined
     *     722 - number is too small
     *     723 - number is too large
     *     724 - number is not valid card
     *
     *
     * @param \RDB|null $oDB
     *
     * @return integer
     */
    public function valCardNumber(RDB &$oDB = NULL)
    {
        $code = 0;
        $number = $this->getCard()->getCardNumber();

        if (empty($number) === TRUE) { //Validation is valid if card number is present
            $code = 720;
        } else {
            $number = preg_replace('/[^0-9]/', '', $number);
            $cardNumberLength = strlen($number);
            if ($cardNumberLength < $this->getCard()->getMinCardLength($oDB)) {
                $code = 722;
            } elseif ($cardNumberLength > $this->getCard()->getMaxCardLength($oDB)) {
                $code = 723;
            } else {
                $checksum = 0;
                for ($i = (2 - (strlen($number) % 2)); $i <= $cardNumberLength; $i += 2) {
                    $checksum += (int)($number[$i - 1]);
                }

                for ($i = (strlen($number) % 2) + 1; $i < $cardNumberLength; $i += 2) {
                    $digit = (int)($number[$i - 1]) * 2;
                    if ($digit < 10) {
                        $checksum += $digit;
                    } else {
                        $checksum += ($digit - 9);
                    }
                }

                if (($checksum % 10) === 0) {
                    $code = 720;
                } else {
                    $code = 724;
                }
            }
        }

        return $code;
    }


    /**
     * Performs validation of the Card Holder name to determine whether it is valid.
     * The method will return the following status codes:
     * 7XX - 7 - Card Validation
     * 73X - 3 - Card holder name Validation
     * 730 - Valid
     * 731 - Invalid
     *
     * @return integer
     */
    public function valCardFullName()
    {
        if (preg_match('/^[a-zA-Z ]+$/', $this->getCard()->getCardHolderName()) == FALSE) {
            $code = 731;
        } else {
            $code = 730;
        }

        return $code;
    }
    /**
     * Performs validation of the card expiry date
     * The method will return the following status codes:
     * 740 - Valid
     * 741 - Invalid Card expiry date
     * 742 - Card is expired
     *
     * @return integer
     */
    public function validateExpiry(): int
    {
        $code = 740;
        if ($this->getCard() !== NULL) {
            $cardExpiry = $this->getCard()->getExpiry();
            if(preg_match('/^\\d{2}\\/\\d{2}$/', $cardExpiry) == 0) {
                $code= 741;
            }else{
                $expiry     = explode(substr($cardExpiry, 2, 1),$cardExpiry);
                $expiryDate = \DateTime::createFromFormat('my', $expiry[0].$expiry[1]);
                $today      = new \DateTime('midnight');
                if ($expiryDate < $today) {
                    $code= 742;
                }
            }
        }
        return $code;
    }
}