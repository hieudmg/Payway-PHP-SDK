<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK\Data;

class CreditCardInfo
{
    /**
     * @var string
     */
    protected $cardNumber;
    /**
     * @var string
     */
    protected $expiryMonth;
    /**
     * @var string
     */
    protected $expiryYear;
    /**
     * @var string
     */
    protected $cardScheme;
    /**
     * @var string
     */
    protected $lastFour;

    public function __construct($creditCardArray)
    {
        $this->cardNumber = isset($creditCardArray['cardNumber']) ? $creditCardArray['cardNumber'] : '';
        $this->expiryMonth = isset($creditCardArray['expiryDateMonth']) ? $creditCardArray['expiryDateMonth'] : '';
        $this->expiryYear = isset($creditCardArray['expiryDateYear']) ? $creditCardArray['expiryDateYear'] : '';
        $this->cardScheme = isset($creditCardArray['cardScheme']) ? $creditCardArray['cardScheme'] : '';
        $cardNumberExploded = explode('...', $this->cardNumber);
        $this->lastFour = $this->cardNumber ? end($cardNumberExploded) : '';
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @return string
     */
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }

    /**
     * @return string
     */
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }

    /**
     * @return string
     */
    public function getCardScheme()
    {
        return $this->cardScheme;
    }

    /**
     * @return string
     */
    public function getLastFour()
    {
        return $this->lastFour;
    }
}