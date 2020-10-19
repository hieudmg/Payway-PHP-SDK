<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK\Response;

class PaywayResponse {
	/**
	 * @var bool
	 */
	protected $success;
	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var string
	 */
	protected $idempotencyKey;
	/**
	 * @var array
	 */
	protected $rawResponse;

	/**
	 * PaywayResponse constructor.
	 *
	 * @param array $rawResponse
	 */
	public function __construct( $rawResponse ) {
	    if (!is_array($rawResponse)) {
	        $rawResponse = [];
        }
		$this->rawResponse    = $rawResponse;
		$this->idempotencyKey = isset( $rawResponse['idempotencyKey'] ) ? $rawResponse['idempotencyKey'] : null;
		$this->success        = true;
        if (is_array($this->getResponseData('data'))) {
            foreach ($this->getResponseData('data') as $data) {
                if (isset($data['message'])) {
                    $this->message = $data['message'];
                    break;
                }
            }
        }
	}

	public function getResponseData( $key, $default = null ) {
		if ( array_key_exists( $key, $this->rawResponse ) ) {
			return $this->rawResponse[ $key ];
		}

		return $default;
	}

	/**
	 * @return bool
	 */
	public function isSuccess() {
		return $this->success;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getIdempotencyKey() {
		return $this->idempotencyKey;
	}

	/**
	 * @return array
	 */
	public function getRawResponse() {
		return $this->rawResponse;
	}
}
