# Package for implementing Ogone payment services.

## In development
Please keep in mind that this is my first draft for this package. Feel free to contribute.

The current package contains the following features:

*	Initiate payments
*	Signature data verification
*	Subscription Manager (Recurring payments)
*	Postsale callback (payment accepted, declined, rejected etc)

## Installing

Currently only available as download or clone from Github. Like any other package it must be put in its own 'ogone' dir in the packages dir and added to your app/config/config.php as an always loaded package.

## Usage

Make sure you set your credentials and your SHA-IN signature in the configuration file.

```php
Ogone::order(1)
 ->currency('EUR')
 ->amount(100)
 ->method('CreditCard')
 ->contact(array('name' => 'John Doe', 'email' => 'john@doe.com'))
 ->build();
```

Will return plain HTML containing the Ogone payment form.

### Recurring payments

To create a recurring payment profile (subscription) you can add the following method (example):

```php
subscription(array('id' => 1, 'amount' => 100, 'comment' => 'Subscription for Magazine',
	'period_unit' => 'm', 'period_number' => 1, 'period_moment' => 1,
	'startdate' => date('Y-m-d', strtotime("+1 month")), 'enddate' => date('Y-m-d', strtotime("+12 months")),
	'status' => 1, 'merc_comment' => 'Testing'))

// So e.g: Ogone::order(1)->subscription(..)->amount(100)->build();
```

### Extra parameters like theming

You can also add some extra optional parameters, like theme and styling of the payment page:

```php
extra(array('TITLE' => 'Foobar Payment Portal')) // Look for all parameters in the ogone.php class $extra_params

// So e.g: Ogone::order(1)->extra(..)->amount(100)->build();
```

Handle postsale callback like this for example:

```php
try 
{
	$postsale = Ogone::handlePostsale();

	/**
	 * Will return array like so:
	 *
	 * 'order_id' => 1,
	 * 'status' => 9,
	 * 'status_txt' => 'The payment has been accepted.',
	 * 'date' => current
	 *
	 * To output raw data pass true as function parameter
	 */

	Log::debug($postsale);

} catch (Exception $e) {
	
	die($e);
}
```

## Links

* [Fuel Framework](http://fuelphp.com/)
* [Ogone Payment Services](http://ogone.com/)
* [Michel Hiemstra](http://michelhiemstra.nl/)

For documentation on Ogone check the [Downloads](https://github.com/mdahiemstra/Fuel-Ogone-Payments/downloads) section


## LICENSE: 

Copyright (c) 2011 Michel Hiemstra

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.