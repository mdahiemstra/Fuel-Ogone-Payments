<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @subpackage Ogone
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

Autoloader::add_core_namespace('Ogone');

Autoloader::add_classes(array(
	'Ogone\\Ogone'	=> __DIR__.'/classes/ogone.php',
));


/* End of file bootstrap.php */