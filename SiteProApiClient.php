<?php

namespace Profis\SitePro;

use ErrorException;

class SiteProApiClient {
	
	/** @var string */
	protected $apiUrl;
	protected $apiUser;
	protected $apiPass;
	
	public function __construct($apiUrl, $apiUsername, $apiPassword) {
		$this->apiUrl = $apiUrl;
		$this->apiUser = $apiUsername;
		$this->apiPass = $apiPassword;
	}
	
	public function remoteCall($method, $params) {
		$url = $this->apiUrl.$method;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Site.pro API Client/1.0.1 (PHP '.phpversion().')');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Connection: Close',
			'Content-Type: application/json',
		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->apiUser.':'.$this->apiPass);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$r = curl_exec($ch);
		$errNo = curl_errno($ch);
		$errMsg = curl_error($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($errNo) {
			$res = null;
			throw new ErrorException('Curl Error ('.$errNo.'): '.$errMsg);
		}
		
		if ($status != 200) {
			$res = json_decode($r);
			if (!$res) {
				$res = null;
				throw new ErrorException('Response Code ('.$status.')');
			}
		} else {
			$res = json_decode($r);
		}
		
		return $res;
	}
	
}
