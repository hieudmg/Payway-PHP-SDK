<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK\Response;

use PaywaySDK\Data\CreditCardInfo;

require_once 'PaywayResponse.php';
require_once 'data/CreditCardInfo.php';

class CustomerResponse extends PaywayResponse
{
    /**
     * @var string
     */
    protected $customerNumber;

    /**
     * @var CreditCardInfo
     */
    protected $creditCardInfo;

    public function __construct($rawResponse)
    {
        parent::__construct($rawResponse);
        $this->success = !(empty($this->getResponseData('customerNumber')) || empty($this->getResponseData('paymentSetup')));
        $this->customerNumber = $this->getResponseData('customerNumber');
        $creditCardArray = $this->getResponseData('paymentSetup');
        if (isset($creditCardArray['creditCard'])) {
            $creditCardArray = $creditCardArray['creditCard'];
        }
        if (!is_array($creditCardArray)) {
            $creditCardArray = [];
        }

        $this->creditCardInfo = new CreditCardInfo($creditCardArray);
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }

    /**
     * @return CreditCardInfo
     */
    public function getCreditCardInfo()
    {
        return $this->creditCardInfo;
    }
}
