<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK\Response;

require_once 'PaywayResponse.php';

use DateTime;

class TransactionResponse extends PaywayResponse
{
    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $receiptNumber;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var DateTime
     */
    protected $transactionTime;

    /**
     * TransactionResponse constructor.
     * @param array $rawResponse
     */
    public function __construct($rawResponse)
    {
        parent::__construct($rawResponse);

        $approvedStatuses = ['approved', 'approved*'];

        $this->success = in_array($this->getResponseData('status'), $approvedStatuses);
        $message = $this->getResponseData('responseText', $this->getResponseData('message', ''));
        if (!empty($message)) {
            $this->message = $message;
        }
        $this->transactionId = $this->getResponseData('transactionId', '');
        $this->receiptNumber = $this->getResponseData('receiptNumber', '');
        $this->amount = $this->getResponseData('amount', 0.00);
        $this->transactionTime = $this->getResponseData('transactionDateTime', 0.00);
        $this->transactionTime = DateTime::createFromFormat('d M Y H:i T',
            $this->getResponseData('transactionDateTime'));
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getTransactionTime()
    {
        return $this->transactionTime;
    }

    /**
     * @return string
     */
    public function getReceiptNumber()
    {
        return $this->receiptNumber;
    }
}
