# Package for implementing Ogone payment services.

## In development
Please keep in mind that this is my first draft for this package, Im working on _Data and origin verification_, _transaction feedback_ and _SHA Signing_. Feel free to contribute.

## Installing

Currently only available as download or clone from Github. Like any other package it must be put in its own 'ogone' dir in the packages dir and added to your app/config/config.php as an always loaded package.

## Usage

Make sure you set your credentials and shasign in the configuration file.

```php
Ogone::order(1)
 ->currency('USD')
 ->amount(100)
 ->method('CreditCard')
 ->contact(array('name' => 'John Doe', 'email' => 'john@doe.com'))
 ->build();
```

Will return plain HTML containing the Ogone payment form.


## LICENSE: 

Copyright (c) 2011 Michel Hiemstra

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.