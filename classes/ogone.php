<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * Package for implementing Ogone payment services.
 *
 * @package		Ogone
 * @version		1.0
 * @author		Michel Hiemstra <mhiemstra@php.net>
 * @license		MIT License
 * @copyright	2011 Michel Hiemstra
 * @link		http://fuelphp.com
 * @link		http://ogone.com/
 * @link		http://michelhiemstra.nl/
 */

/**
 * Basic usage:
 * 		Ogone::order(1)->currency('USD')->amount(100)->method('CreditCard')->build();
 *
 * returns plain HTML containing the postform for Ogone payments.
 *
*/


namespace Ogone;

class Ogone
{

	// Forward declare credentials, endpoints, URLs, defaults etc
	protected static $psp_id = null;
	protected static $shasign = null;
	protected static $language = null; 
	protected static $currency = null; 
	protected static $accept_url = null;
	protected static $decline_url = null;
	protected static $exception_url = null;
	protected static $cancel_url = null;
	protected static $post_test_url = null;
	protected static $post_prod_url = null;

	// Order specific variables
	protected static $order_id = null;
	protected static $amount = null;
	protected static $method = null;
	protected static $contact = array('name' => null, 'email' => null, 'country' => null, 'zipcode' => null, 'address' => null, 'city' => null, 'phone' => null);
	
	protected static $_instance = null;

	public static function instance()
	{
		if (static::$_instance == null)
		{
			static::$_instance = static::factory();
		}
		return static::$_instance;
	}

	/**
	 * Creates a new instance of the Ogone package
	 *
	 * @return  Ogone
	 */
	public static function factory()
	{
		return new Ogone();
	}

	/*
	* Called automatically when class is initiated
	*/
	public static function _init()
	{
		\Config::load('ogone', true);

		self::$psp_id = \Config::get('ogone.psp_id');
		self::$shasign = \Config::get('ogone.shasign');
		self::$language = \Config::get('ogone.language');
		self::$currency = \Config::get('ogone.currency');
		self::$accept_url = \Config::get('ogone.accept_url');
		self::$decline_url = \Config::get('ogone.decline_url');
		self::$exception_url = \Config::get('ogone.exception_url');
		self::$cancel_url = \Config::get('ogone.cancel_url');
		self::$post_test_url = \Config::get('ogone.post_test_url');
		self::$post_test_url = \Config::get('ogone.post_test_url');
	}

	/*
	* Set the default currency
	*/
	public static function currency($currency) {
		
		self::$currency = $currency;

		return static::instance();
	}

	/*
	* Set order ID, must be unique
	*/
	public static function order($order_id) {
		
		if (!is_numeric($order_id))
			throw new \Fuel_Exception('$order_id is not a number');

		self::$order_id = $order_id;

		return static::instance();
	}

	/*
	* Set amount in cents
	*/
	public static function amount($amount) {
		
		if (!is_numeric($amount))
			throw new \Fuel_Exception('$amount is not a number');

		self::$amount = $amount;

		return static::instance();
	}

	/*
	* Set the default payment method
	*/
	public static function method($method) {

		self::$method = $method;

		return static::instance();
	}

	/*
	* Set contact details to verify and for fraud protection
	*/
	public static function contact($contactData) {

		if (is_array($contactData)) {
			
			foreach ($contactData as $key => $value) 
				
				if (array_key_exists($key, self::$contact)) 
					self::$contact[$key] = $value;
				else 
					throw new \Fuel_Exception("contactData $key not found");
		} else {
			
			throw new \Fuel_Exception('Expected contactData to be an array');
		}

		return static::instance();
	}
	
	/*
	* Build the ogone payment form
	*/
	public static function build($openForm = true, $submitButton = array('name' => 'ogoneSubmit', 'value' => 'Process Payment', 'attributes' => array())) {
		
		if ($openForm)
			$html  = \Form::open(array('action' => \Config::get('ogone.debug') ? self::$post_test_url : self::$post_prod_url, 'method' => 'post'));

		// Input type, text for debugging
		$input = \Config::get('ogone.debug') ? 'input' : 'hidden';

		// General payment parameters
		$html .= \Form::$input('PSPID', self::$psp_id);
		$html .= \Form::$input('ORDERID', self::$order_id);
		$html .= \Form::$input('AMOUNT', self::$amount);
		$html .= \Form::$input('CURRENCY', self::$currency);
		$html .= \Form::$input('LANGUAGE', self::$language);

		// Optional customer details, highly recommended for fraud prevention
		$html .= \Form::$input('CN', self::$contact['name']);
		$html .= \Form::$input('EMAIL', self::$contact['email']);
		$html .= \Form::$input('OWNERZIP', self::$contact['zipcode']);
		$html .= \Form::$input('OWNERADDRESS', self::$contact['address']);
		$html .= \Form::$input('OWNERCTY', self::$contact['country']);
		$html .= \Form::$input('OWNERTOWN', self::$contact['city']);
		$html .= \Form::$input('OWNERTELNO', self::$contact['name']);
		$html .= \Form::$input('OWNERCTY', self::$contact['phone']);

		// SHA-1-IN signature
		$html .= \Form::$input('SHASIGN', self::$shasign);

		// Look & Feel of the Payment Page
		$html .= \Form::$input('TITLE', '');
		$html .= \Form::$input('BGCOLOR', '');
		$html .= \Form::$input('TXTCOLOR', '');
		$html .= \Form::$input('TBLBGCOLOR', '');
		$html .= \Form::$input('TBLTXTCOLOR', '');
		$html .= \Form::$input('BUTTONBGCOLOR', '');
		$html .= \Form::$input('BUTTONTXTCOLOR', '');
		$html .= \Form::$input('LOGO', '');
		$html .= \Form::$input('FONTTYPE', '');

		// Dynamic template page (url)
		$html .= \Form::$input('TP', '');
		
		// Payment method and payment page specifics
		$html .= \Form::$input('PM', '');
		$html .= \Form::$input('BRAND', '');
		$html .= \Form::$input('WIN3DS', '');
		$html .= \Form::$input('PMLIST', '');
		$html .= \Form::$input('PMLISTTYPE', '');

		// Link to webshop / cart
		$html .= \Form::$input('HOMEURL', '');
		$html .= \Form::$input('CATALOGURL', '');

		// Post payment parameters
		$html .= \Form::$input('COMPLUS', '');
		$html .= \Form::$input('PARAMPLUS', '');
		$html .= \Form::$input('PARAMVAR', '');

		// Post payment redirection
		$html .= \Form::$input('ACCEPTURL', self::$accept_url);
		$html .= \Form::$input('DECLINEURL', self::$decline_url);
		$html .= \Form::$input('EXCEPTIONURL', self::$exception_url);
		$html .= \Form::$input('CANCELURL', self::$cancel_url);

		// Optional operation field
		$html .= \Form::$input('OPERATION', '');

		// Optional extra login detail
		$html .= \Form::$input('USERID', '');

		// Alias Management Details
		$html .= \Form::$input('ALIAS', '');
		$html .= \Form::$input('ALIASUSAGE', '');
		$html .= \Form::$input('ALIASOPERATION', '');

		if ($submitButton)
			$html .= \Form::button($submitButton['name'], $submitButton['value'], $submitButton['attributes']);
		
		if ($openForm)
			$html .= \Form::close();

		return $html;
	}
	
}