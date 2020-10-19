<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK;

require_once('data/MerchantInformation.php');
require_once('response/TransactionResponse.php');
require_once('response/CustomerResponse.php');

use PaywaySDK\Data\MerchantInformation;
use PaywaySDK\Response\CustomerResponse;
use PaywaySDK\Response\TransactionResponse;

abstract class AbstractPaywayApiWrapper
{
    const BASE_URL = 'https://api.payway.com.au/rest/v1/';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    const CURRENCY_AUD = 'aud';

    /**
     * Take payment
     * https://www.payway.com.au/docs/rest.html#take-payment
     *
     * @param string $token
     * @param string $customerNumber
     * @param float $amount
     * @param string $customerIp
     * @param string|null $idempotencyKey
     * @param string $currency
     * @param string|null $orderNumber
     *
     * @return TransactionResponse
     */
    public function takePayment(
        $token,
        $customerNumber,
        $amount,
        $customerIp,
        $idempotencyKey = null,
        $currency = self::CURRENCY_AUD,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $merchantInformation = $this->getMerchantInformation();
        $customerNumber = $this->makeUnique($customerNumber);

        $data = [
            'singleUseTokenId' => $token,
            'transactionType' => 'payment',
            'principalAmount' => (float)$amount,
            'currency' => $currency,
            'merchantId' => $merchantInformation->getMerchantId(),
            'customerIpAddress' => $customerIp,
            'customerNumber' => $customerNumber,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * @param string ...$parts
     *
     * @return string
     */
    public static function buildUrl(...$parts)
    {
        $parts = array_map(function ($part) {
            return trim($part, "\\/\t\r\n\0\x0B");
        }, $parts);

        return join('/', $parts);
    }

    /**
     * Generate UUID to use as Idempotency key
     * https://www.payway.com.au/docs/rest.html#avoiding-duplicate-posts
     *
     * @return string
     */
    public function generateUuid()
    {
        $hash = md5($this->getStoreUid() . microtime(true));

        return sprintf('%s-%s-%s-%s-%s', substr($hash, 0, 8), substr($hash, 8, 4), substr($hash, 12, 4),
            substr($hash, 16, 4), substr($hash, 20));
    }

    /**
     * Get store unique identifier
     *
     * @return string
     */
    protected abstract function getStoreUid();

    /**
     * Get public api key
     *
     * @return MerchantInformation
     */
    protected abstract function getMerchantInformation();

    /**
     * Make params unique to store code
     *
     * @param string $string Parameter.
     *
     * @return string
     */
    protected function makeUnique($string)
    {
        if (substr($string, 0, strlen($this->getStoreUid())) !== $this->getStoreUid()) {
            return $this->getStoreUid() . '_' . $string;
        }

        return $string;
    }

    /**
     * Make a request using framework's implementation. Must add IdempotencyKey to response array
     *
     * @param string $url
     * @param string $method
     * @param string $idempotencyKey
     * @param array|string|null $data
     * @param array|null $additionalHeaders
     *
     * @return array
     */
    protected abstract function makeRequest(
        $url,
        $method,
        $idempotencyKey = '',
        $data = null,
        $additionalHeaders = null
    );

    /**
     * Take payment using stored card
     * https://www.payway.com.au/docs/rest.html#take-payment-using-stored-card-or-account
     *
     * @param string $customerNumber
     * @param float $amount
     * @param string $customerIp
     * @param null $idempotencyKey
     * @param string $currency
     * @param null $orderNumber
     *
     * @return TransactionResponse
     */
    public function takePaymentUsingStoredCard(
        $customerNumber,
        $amount,
        $customerIp,
        $idempotencyKey = null,
        $currency = self::CURRENCY_AUD,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $data = [
            'transactionType' => 'payment',
            'principalAmount' => (float)$amount,
            'currency' => $currency,
            'customerIpAddress' => $customerIp,
            'customerNumber' => $customerNumber,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * Process pre-authorisation
     * https://www.payway.com.au/docs/rest.html#process-pre-authorisation
     *
     * @param string $token
     * @param string $customerNumber
     * @param float $amount
     * @param string $customerIp
     * @param string|null $idempotencyKey
     * @param string $currency
     * @param string|null $orderNumber
     *
     * @return TransactionResponse
     */
    public function processPreAuthorization(
        $token,
        $customerNumber,
        $amount,
        $customerIp,
        $idempotencyKey = null,
        $currency = self::CURRENCY_AUD,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $merchantInformation = $this->getMerchantInformation();
        $customerNumber = $this->makeUnique($customerNumber);

        $data = [
            'singleUseTokenId' => $token,
            'transactionType' => 'preAuth',
            'principalAmount' => $amount,
            'currency' => $currency,
            'merchantId' => $merchantInformation->getMerchantId(),
            'customerIpAddress' => $customerIp,
            'customerNumber' => $customerNumber,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * Process pre-authorisation using stored credit card
     * https://www.payway.com.au/docs/rest.html#process-pre-authorisation-using-stored-credit-card
     *
     * @param string $customerNumber
     * @param float $amount
     * @param string $customerIp
     * @param string|null $idempotencyKey
     * @param string $currency
     * @param string|null $orderNumber
     *
     * @return TransactionResponse
     */
    public function processPreAuthorizationUsingStoredCard(
        $customerNumber,
        $amount,
        $customerIp,
        $idempotencyKey = null,
        $currency = self::CURRENCY_AUD,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $data = [
            'customerNumber' => $customerNumber,
            'transactionType' => 'preAuth',
            'principalAmount' => $amount,
            'currency' => $currency,
            'customerIpAddress' => $customerIp,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * Capture a pre-authorisation
     * https://www.payway.com.au/docs/rest.html#capture-a-pre-authorisation
     *
     * @param float $amount
     * @param string $parentTransactionId
     * @param string $customerIp
     * @param string|null $idempotencyKey
     * @param string|null $orderNumber
     *
     * @return TransactionResponse
     */
    public function capturePreAuthorization(
        $amount,
        $parentTransactionId,
        $customerIp,
        $idempotencyKey = null,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $data = [
            'transactionType' => 'capture',
            'parentTransactionId' => $parentTransactionId,
            'principalAmount' => $amount,
            'customerIpAddress' => $customerIp,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * Refund a payment
     * https://www.payway.com.au/docs/rest.html#refund-a-payment
     *
     * @param float $amount
     * @param string $parentTransactionId
     * @param string $customerIp
     * @param string|null $idempotencyKey
     * @param string|null $orderNumber
     *
     * @return TransactionResponse
     */
    public function refundPayment(
        $amount,
        $parentTransactionId,
        $customerIp,
        $idempotencyKey = null,
        $orderNumber = null
    )
    {
        $url = self::buildUrl(self::BASE_URL, 'transactions');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $data = [
            'transactionType' => 'refund',
            'parentTransactionId' => $parentTransactionId,
            'principalAmount' => $amount,
            'customerIpAddress' => $customerIp,
        ];

        if ($orderNumber) {
            $data['orderNumber'] = $this->makeUnique($orderNumber);
        }

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new TransactionResponse($rawResponse);
    }

    /**
     * Create a new customer and store the tokenized card
     * https://www.payway.com.au/docs/rest.html#store-credit-card-for-new-customer
     *
     * @param string $token
     * @param null $idempotencyKey
     *
     * @return CustomerResponse
     */
    public function storeCreditCard($token, $idempotencyKey = null)
    {
        $url = self::buildUrl(self::BASE_URL, 'customers');

        if (!$idempotencyKey) {
            $idempotencyKey = $this->generateUuid();
        }

        $data = [
            'singleUseTokenId' => $token,
            'merchantId' => $this->getMerchantInformation()->getMerchantId(),
        ];

        $rawResponse = $this->makeRequest($url, self::METHOD_POST, $idempotencyKey, $data);

        return new CustomerResponse($rawResponse);
    }

    /**
     * Delete stored credit card
     * https://www.payway.com.au/docs/rest.html#delete-customer
     *
     * @param $customerNumber
     *
     * @return CustomerResponse
     */
    public function deleteStoredCreditCard($customerNumber)
    {
        $url = self::buildUrl(self::BASE_URL, "customers/$customerNumber");

        $rawResponse = $this->makeRequest($url, self::METHOD_DELETE);

        return new CustomerResponse($rawResponse);
    }
}
