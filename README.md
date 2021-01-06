# Omnipay: Tranzila

**Tranzila driver for the Omnipay PHP payment processing library**

![Build](https://github.com/futureecom/omnipay-tranzila/workflows/Build/badge.svg?branch=master) 
[![Latest Stable Version](https://poser.pugx.org/futureecom/omnipay-tranzila/version.png)](https://packagist.org/packages/futureecom/omnipay-tranzila)
[![Total Downloads](https://poser.pugx.org/futureecom/omnipay-tranzila/d/total.png)](https://packagist.org/packages/futureecom/omnipay-tranzila)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Tranzila support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `futureecom/omnipay-tranzila` with Composer:

```
composer require league/omnipay futureecom/omnipay-tranzila
```

## Basic Usage

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

The following gateways are supported by this package:
* Authorize
* Capture
* Purchase
* Refund
* Void

### Tranzila Documentation

* [Hebrew](http://doctr6.interspace.net/)
* [English](http://tranzila:express2017!secret@doctren.interspace.net/?type=1)

We are not the authors of the Tranzila API! 
Please direct any questions about Tranzila to Tranzila Support.

### Supported currencies

Tranzila supports only four currencies:
- EUR
- GBP
- ILS
- USD

If you will use an unsupported currency, you'll  receive an `InvalidRequestException`.

## Test Mode

The test Tranzila account can be created only by Tranzila Support. Please contact them to create your 'testing terminal'.

You cannot perform authorization on your test account and purchases can be only made up to 10 ILS. When you will try to do either, your request will be refused.


# Authorize

Payment authorization can be done in two ways.

First is by transferring card data (not recommended if the site does not meet PCI standards).
Second is to redirect customer to secure payment page (iframe).

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->authorize([
    'amount' => '10.00',
    'currency' => 'ILS',
    'myid' => '12345678',
    'card' => [
        'ccno' => '4444333322221111',
        'expdate' => '1225',
        'mycvv' => '1234',
    ],
])->send();
```

To generate a redirect link, send the above request skipping the `card` part. In response you will receive an url to make the payment.

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->authorize([
    'amount' => '10.00',
    'currency' => 'ILS',
    'myid' => '12345678',
])->send();

if ($response->isRedirect()) {
    echo $response->getRedirectUrl(); // https://direct.tranzila.com/terminal_name/iframe.php?tranmode=V&currency=1&sum=1.00
}
```

A link will be generated that can be used to redirect the customer or display in the iframe.

# Capture

To capture a payment, you must provide transaction reference. 
It is built of two elements that we receive in response to payment authorization - 
Index and AuthNr separated by `-` for example: `22-000000`.

Warning: If redirect URL was used to authorize the payment, `transaction_reference` from notify url will must be used.

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->capture([
    'amount' => '1',
    'transaction_reference' => '22-000000',
])->send();
```

# Purchase

Purchase is carried out in the same way as authorization.

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->purchase([
    'amount' => '10.00',
    'currency' => 'ILS',
    'myid' => '12345678',
    'card' => [
        'ccno' => '4444333322221111',
        'expdate' => '1225',
        'mycvv' => '1234',
    ],
])->send();
```

# Refund

Tranzila also supports returns. However, you can only do return on the payment once. Therefore, partial refunds are not supported.
It works in the same way as authorization and purchase. While doing refund, you can use TranzilaTK (from notify url) instead of credit card details.

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->refund([
    'amount' => '5.00',
    'currency' => 'ILS',
    'transaction_reference' => '22-000000',
])->send();
```

# Void

Cancelling a transaction is also supported by Tranzila. To do so, all we have to send is a reference number.

```php
<?php

use Futureecom\OmnipayTranzila\TranzilaGateway;

/** @var TranzilaGateway $gateway */
$response = $gateway->void([
    'transaction_reference' => '22-000000',
    'TranzilaTK' => 'DdyniRvcUGHBj9xO', // TranzilaToken
])->send();
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/futureecom/omnipay-tranzila/issues),
or better yet, fork the library and submit a pull request.
