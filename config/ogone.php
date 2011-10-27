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

return array(
	'environment' => 'development',
	'psp_id' => 'pspidhere',
	'shasign' => ')i8YKLod$j4&AOu3pfy', // Single quotes
	'language' => 'en_US', // en_US, nl_NL, fr_FR, ...
	'currency' => 'USD', // ISO currency code
	'accept_url' => \Uri::create(\Uri::current() . '/status/accepted'),
	'decline_url' => \Uri::create(\Uri::current() . '/status/declined'),
	'exception_url' => \Uri::create(\Uri::current() . '/status/exception'),
	'cancel_url' => \Uri::create(\Uri::current() . '/status/canceled'),
	'post_test_url' => 'https://secure.ogone.com/ncol/test/orderstandard.asp',
	'post_prod_url' => 'https://secure.ogone.com/ncol/prod/orderstandard.asp'
);