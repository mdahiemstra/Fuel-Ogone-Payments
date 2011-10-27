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

	public static function currency($currency) {
		
		self::$currency = $currency;

		return static::instance();
	}

	public static function order($order_id) {
		
		if (!is_numeric($order_id))
			throw new \Fuel_Exception('$order_id is not a number');

		self::$order_id = $order_id;

		return static::instance();
	}

	public static function amount($amount) {
		
		if (!is_numeric($amount))
			throw new \Fuel_Exception('$amount is not a number');

		self::$amount = $amount;

		return static::instance();
	}

	public static function method($method) {

		self::$method = $method;

		return static::instance();
	}

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
	

	public static function build() 
	{
		$post_url = \Config::get('ogone.environment') == 'development' ? self::$post_test_url : $self::$post_prod_url;
		$field_type = \Config::get('ogone.environment') == 'development' ? 'text' : 'hidden';

		$html = '<form method="post" action="'.$post_url.'" id="ogone_payment_form" name="ogone_payment_form">'
			  . '<!-- general parameters: see General Payment Parameters -->'
			  . '<input type="'.$field_type.'" name="PSPID" value="'.self::$psp_id.'">'
			  . '<input type="'.$field_type.'" name="ORDERID" value="'.self::$order_id.'">'
			  . '<input type="'.$field_type.'" name="AMOUNT" value="'.self::$amount.'">'
			  . '<input type="'.$field_type.'" name="CURRENCY" value="'.self::$currency.'">'
			  . '<input type="'.$field_type.'" name="LANGUAGE" value="'.self::$language.'">'
			  . '<!-- optional customer details, highly recommended for fraud prevention -->'
			  . '<input type="'.$field_type.'" name="CN" value="'.self::$contact['name'].'">'
			  . '<input type="'.$field_type.'" name="EMAIL" value="'.self::$contact['email'].'">'
			  . '<input type="'.$field_type.'" name="OWNERZIP" value="'.self::$contact['zipcode'].'">'
			  . '<input type="'.$field_type.'" name="OWNERADDRESS" value="'.self::$contact['address'].'">'
			  . '<input type="'.$field_type.'" name="OWNERCTY" value="'.self::$contact['country'].'">'
			  . '<input type="'.$field_type.'" name="OWNERTOWN" value="'.self::$contact['city'].'">'
			  . '<input type="'.$field_type.'" name="OWNERTELNO" value="'.self::$contact['phone'].'">'
			  . '<input type="'.$field_type.'" name="COM" value="">'
			  . '<!-- check before the payment: see SHA-1-IN signature -->'
			  . '<input type="'.$field_type.'" name="SHASIGN" value="'.self::$shasign.'">'
			  . '<!-- layout information: see Look & Feel of the Payment Page -->'
			  . '<input type="'.$field_type.'" name="TITLE" value="">'
			  . '<input type="'.$field_type.'" name="BGCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="TXTCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="TBLBGCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="TBLTXTCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="BUTTONBGCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="BUTTONTXTCOLOR" value="">'
			  . '<input type="'.$field_type.'" name="LOGO" value="">'
			  . '<input type="'.$field_type.'" name="FONTTYPE" value="">'
			  . '<!-- dynamic template page: see Look & Feel of the Payment Page -->'
			  . '<input type="'.$field_type.'" name="TP" value="">'
			  . '<!-- payment methods/page specifics: see Payment method and payment page specifics -->'
			  . '<input type="'.$field_type.'" name="PM" value="">'
			  . '<input type="'.$field_type.'" name="BRAND" value="">'
			  . '<input type="'.$field_type.'" name="WIN3DS" value="">'
			  . '<input type="'.$field_type.'" name="PMLIST" value="">'
			  . '<input type="'.$field_type.'" name="PMLISTTYPE" value="">'
			  . '<!-- link to your website: see Default reaction -->'
			  . '<input type="'.$field_type.'" name="HOMEURL" value="">'
			  . '<input type="'.$field_type.'" name="CATALOGURL" value="">'
			  . '<!-- post payment parameters -> '
			  . '<input type="'.$field_type.'" name="COMPLUS" value="">'
			  . '<input type="'.$field_type.'" name="PARAMPLUS" value="">'
			  . '<!-- post payment parameters: see Direct feedback requests (Post-payment) -->'
			  . '<input type="'.$field_type.'" name="PARAMVAR" value="">'
			  . '<!-- post payment redirection: see Redirection depending on the payment result -->'
			  . '<input type="'.$field_type.'" name="ACCEPTURL" value="'.self::$accept_url.'">'
			  . '<input type="'.$field_type.'" name="DECLINEURL" value="'.self::$decline_url.'">'
			  . '<input type="'.$field_type.'" name="EXCEPTIONURL" value="'.self::$exception_url.'">'
			  . '<input type="'.$field_type.'" name="CANCELURL" value="'.self::$cancel_url.'">'
			  . '<!-- optional operation field: see Operation -->'
			  . '<input type="'.$field_type.'" name="OPERATION" value="">'
			  . '<!-- optional extra login detail field: see User field -->'
			  . '<input type="'.$field_type.'" name="USERID" value="">'
			  . '<!-- Alias details: see Alias Management documentation -->'
			  . '<input type="'.$field_type.'" name="ALIAS" value="">'
			  . '<input type="'.$field_type.'" name="ALIASUSAGE" value="">'
			  . '<input type="'.$field_type.'" name="ALIASOPERATION" value="">'
			  . '<input type="submit" value="Pay" id="ogoneSubmit" name="ogoneSubmit">'
			  . '</form>';

		return $html;
	}
	
}
