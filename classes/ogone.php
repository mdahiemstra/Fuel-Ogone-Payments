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
 * 		Ogone::order(1)->currency('EUR')->amount(100)->method('CreditCard')->build();
 *
 * returns HTML containing the postform for Ogone payments.
 *
*/


namespace Ogone;

class Ogone
{

	// Forward declare credentials, endpoints, URLs, defaults etc
	protected static $psp_id = null;
	protected static $shasign = null;
	protected static $hash_method = null;
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
	protected static $subscription = array('id' => null, 'amount' => null, 'comment' => null, 'order_id' => null, 'period_unit' => null, 'period_number' => null, 'period_moment' => null, 'startdate' => null, 'enddate' => null, 'status' => null, 'merc_comment' => null);
	protected static $extra_params = array('TITLE' => null, 'BGCOLOR' => null, 'TXTCOLOR' => null, 'TBLBGCOLOR' => null, 'TBLTXTCOLOR' => null, 'BUTTONBGCOLOR' => null, 'BUTTONTXTCOLOR' => null, 'LOGO' => null, 'FONTTYPE' => null, 'TP' => null, 'PM' => null, 'BRAND' => null, 'WIN3DS' => null, 'PMLIST' => null, 'PMLISTTYPE' => null, 'HOMEURL' => null, 'CATALOGURL' => null, 'COMPLUS' => null, 'PARAMPLUS' => null, 'PARAMVAR' => null, 'OPERATION' => null, 'USERID' => null, 'ALIAS' => null, 'ALIASUSAGE' => null, 'ALIASOPERATION' => null);

	protected static $_instance = null;

	protected static $_fields = array();

	protected static $status_messages = array(5 => 'The authorization has been accepted.',
											  9 => 'The payment has been accepted.',
											  0 => 'Invalid or incomplete.',
											  2 => 'Authorization refused.',
											  52 => 'The authorization will be processed offline.',
											  91 => 'The data capture will be processed offline.',
											  52 => 'Authorization not known.',
											  92 => 'Payment uncertain.',
											  93 => 'Payment refused.');

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
		self::$hash_method = \Config::get('ogone.hash_method');
		self::$language = \Config::get('ogone.language');
		self::$currency = \Config::get('ogone.currency');
		self::$accept_url = \Config::get('ogone.accept_url');
		self::$decline_url = \Config::get('ogone.decline_url');
		self::$exception_url = \Config::get('ogone.exception_url');
		self::$cancel_url = \Config::get('ogone.cancel_url');
		self::$post_test_url = \Config::get('ogone.post_test_url');
		self::$post_prod_url = \Config::get('ogone.post_prod_url');
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

	public static function subscription($data) {

		$data['order_id'] = self::$order_id;

		foreach ($data as $key => $value) 
			
			if (array_key_exists($key, self::$subscription)) 
				self::$subscription[$key] = $value;
			else 
				throw new \Fuel_Exception("subscription $key not found");

		return static::instance();
	}

	public static function extra($data) {

		foreach ($data as $key => $value) 
			
			if (array_key_exists($key, self::$extra_params)) 
				self::$extra_params[$key] = $value;
			else 
				throw new \Fuel_Exception("extra_params $key not found");

		return static::instance();
	}

	private static function buildSHA() {

		// Alphabetical order to comply with Ogone's SHA building
		ksort(self::$_fields);
		
		// Forward declare the var we are using for the sha string
		$sha_string = null;

		foreach (self::$_fields as $name => $value) {

			if (!empty($value))
				$sha_string .= strtoupper($name) . '=' . $value . self::$shasign;
		}

		if (function_exists('hash'))
			$shaout = hash(self::$hash_method, $sha_string);
		elseif (function_exists('mhash'))
			$shaout = strtoupper(bin2hex(mhash(self::$hash_method, $sha_string)));
		else
			throw new \Fuel_Exception("No encryption method found");

		return $shaout;
	}
	
	/*
	* Build the ogone payment form
	*/
	public static function build($openForm = true, $submitButton = array('name' => 'ogoneSubmit', 'value' => 'Process Payment', 'attributes' => array())) {
		
		if ($openForm)
			$html  = \Form::open(array('action' => \Config::get('ogone.debug') ? self::$post_test_url : self::$post_prod_url, 'method' => 'post'));

		// Input type, text for debugging
		$input = \Config::get('ogone.debug') ? 'input' : 'hidden';

		// Assign values to the fields
		self::$_fields = array(
						/* General payment parameters */
						'PSPID' => self::$psp_id,
						'ORDERID' => self::$order_id,
						'AMOUNT' => self::$amount,
						'CURRENCY' => self::$currency,
						'LANGUAGE' => self::$language,
						/* Optional customer details, highly recommended for fraud prevention */
						'CN' => self::$contact['name'],
						'EMAIL' => self::$contact['email'],
						'OWNERZIP' => self::$contact['zipcode'],
						'OWNERADDRESS' => self::$contact['address'],
						'OWNERCTY' => self::$contact['country'],
						'OWNERTOWN' => self::$contact['city'],
						'OWNERTELNO' => self::$contact['phone'],
						/* SHA-1-IN signature */
						'SHASIGN' => '',
						/* Post payment redirection */
						'ACCEPTURL' => self::$accept_url,
						'DECLINEURL' => self::$decline_url,
						'EXCEPTIONURL' => self::$exception_url,
						'CANCELURL' => self::$cancel_url,
						/* Subscription Manager */
						'SUBSCRIPTION_ID' => self::$subscription['id'],
						'SUB_AMOUNT' => self::$subscription['amount'],
						'SUB_COM' => self::$subscription['comment'],
						'SUB_ORDERID' => self::$subscription['order_id'],
						'SUB_PERIOD_UNIT' => self::$subscription['period_unit'],
						'SUB_PERIOD_NUMBER' => self::$subscription['period_number'],
						'SUB_PERIOD_MOMENT' => self::$subscription['period_moment'],
						'SUB_STARTDATE' => self::$subscription['startdate'],
						'SUB_ENDDATE' => self::$subscription['enddate'],
						'SUB_STATUS' => self::$subscription['status'],
						'SUB_COMMENT' => self::$subscription['merc_comment']
						);

		// Add extra params
		foreach (self::$extra_params as $name => $value) {
			
			if (!empty($value))
				self::$_fields[$name] = $value;
		}
		
		self::$_fields['SHASIGN'] = self::buildSHA();

		// Iterate for each field
		foreach (self::$_fields as $name => $value) {
			
			if (!empty($value))
				$html .= \Form::$input($name, $value);
		}

		// Do we need to parse a submit button
		if ($submitButton)
			$html .= \Form::button($submitButton['name'], $submitButton['value'], $submitButton['attributes']);
		
		if ($openForm)
			$html .= \Form::close();

		return $html;
	}

	public static function handlePostsale($raw = false) {
		
		$post_data = file_get_contents('php://input');

		if (empty($post_data))
			throw new \Fuel_Exception("No postsale data found");

		// Parse string into array
		parse_str($post_data, $data);

		if ($raw)
			return $data;

		return array('order_id' => $data['orderID'],
					 'status' => $data['STATUS'],
					 'status_txt' => self::$status_messages[$data['STATUS']],
					 'date' => strtotime($data['TRXDATE']));
	}
	
}