<?php
/*
 * Copyright (c) 2020 hieudmg.
 */

namespace PaywaySDK\Data;

class MerchantInformation {
	/**
	 * @var string
	 */
	protected $merchantId;
	/**
	 * @var string
	 */
	protected $publicKey;
	/**
	 * @var string
	 */
	protected $privateKey;

	/**
	 * @return string
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}

	/**
	 * @param string $publicKey
	 *
	 * @return MerchantInformation
	 */
	public function setPublicKey( $publicKey ) {
		$this->publicKey = $publicKey;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrivateKey() {
		return $this->privateKey;
	}

	/**
	 * @param string $privateKey
	 *
	 * @return MerchantInformation
	 */
	public function setPrivateKey( $privateKey ) {
		$this->privateKey = $privateKey;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMerchantId() {
		return $this->merchantId;
	}

	/**
	 * @param string $merchantId
	 *
	 * @return MerchantInformation
	 */
	public function setMerchantId( $merchantId ) {
		$this->merchantId = $merchantId;

		return $this;
	}
}
