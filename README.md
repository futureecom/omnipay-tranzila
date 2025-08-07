# Omnipay Tranzila

Tranzila gateway for Omnipay payment processing library.

**Note:** This package uses the modern Tranzila API. Redirect functionality has been removed in favor of the handshake-based iframe integration for better PCI compliance. For advanced integration details, see the official Tranzila documentation.

## Documentation

- Official Tranzila API docs: [https://docs.tranzila.com/](https://docs.tranzila.com/)
- Omnipay general usage: [https://github.com/thephpleague/omnipay](https://github.com/thephpleague/omnipay)

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it to your `composer.json` file:

```json
{
    "require": {
        "futureecom/omnipay-tranzila": "~3.0"
    }
}
```

And run composer to update your dependencies:

```bash
$ composer update
```

## Basic Usage

The following gateways are provided by this package:

* Tranzila

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

### Authorization

Authorization operations support two methods:

#### Method 1: Credit Card Details
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->authorize([
    'amount' => '10.00',
    'currency' => 'ILS',
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123'
    ]
])->send();

if ($response->isSuccessful()) {
    // authorization was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // authorization failed: display message to customer
    echo $response->getMessage();
}
```

#### Method 2: Token + Expiry
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->authorize([
    'amount' => '10.00',
    'currency' => 'ILS',
    'token' => 'U99e9abcd81c2ca4444',
    'expiryMonth' => 12,
    'expiryYear' => 2025
])->send();

if ($response->isSuccessful()) {
    // authorization was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // authorization failed: display message to customer
    echo $response->getMessage();
}
```

### Capture

Capture operations require all 4 pieces of information:

#### Method 1: Transaction Reference + Authorization Number + Token + Expiry
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->capture([
    'amount' => '10.00',
    'currency' => 'ILS',
    'transactionReference' => '12345', // The transaction reference from the authorization
    'authorizationNumber' => 'AUTH123', // Authorization number from the original transaction
    'token' => 'U99e9abcd81c2ca4444', // Token from the original transaction
    'expiryMonth' => 12,
    'expiryYear' => 2025
])->send();

if ($response->isSuccessful()) {
    // capture was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // capture failed: display message to customer
    echo $response->getMessage();
}
```

#### Method 2: Transaction Reference + Authorization Number + Credit Card Details
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->capture([
    'amount' => '10.00',
    'currency' => 'ILS',
    'transactionReference' => '12345', // The transaction reference from the authorization
    'authorizationNumber' => 'AUTH123', // Authorization number from the original transaction
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123'
    ]
])->send();

if ($response->isSuccessful()) {
    // capture was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // capture failed: display message to customer
    echo $response->getMessage();
}
```

### Void

Void operations require both transaction reference and authorization number:

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->void([
    'transactionReference' => '12345', // The transaction reference to void
    'authorizationNumber' => 'AUTH123' // Authorization number from the original transaction
])->send();

if ($response->isSuccessful()) {
    // void was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // void failed: display message to customer
    echo $response->getMessage();
}
```

> **Note:** Void operations may not work on Tranzila test accounts. This is a limitation of the Tranzila sandbox environment. For more details, see the [Tranzila API documentation](https://docs.tranzila.com/).

> **Note:** Void responses have a simpler format than other transactions. A successful void returns `{"error_code":0,"message":"Success"}` without a `transaction_result` object.

### Reversal

Reversal operations require transaction reference, authorization number, and either token+expiry or card details:

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->reversal([
    'amount' => '1.00',
    'currency' => 'ILS',
    'transactionReference' => '12345', // The transaction reference to reverse
    'authorizationNumber' => 'AUTH123', // Authorization number from the original transaction
    'token' => 's0fc7b9882e722b0691', // Token from the original transaction
    'expiryMonth' => 11,
    'expiryYear' => 2028
])->send();

if ($response->isSuccessful()) {
    // reversal was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // reversal failed: display message to customer
    echo $response->getMessage();
}
```

**Alternative: Using Card Details**
```php
$response = $gateway->reversal([
    'amount' => '1.00',
    'currency' => 'ILS',
    'transactionReference' => '12345',
    'authorizationNumber' => 'AUTH123',
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '11',
        'expiryYear' => '2028',
        'cvv' => '123'
    ]
])->send();
```

> **Note:** Reversal operations require the original transaction reference, authorization number, and either the token with expiry or complete card details from the original transaction.

### Verification

Verification operations support two methods:

#### Method 1: Credit Card Details
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->verify([
    'amount' => '10.00',
    'currency' => 'ILS',
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123'
    ]
])->send();

if ($response->isSuccessful()) {
    // verification was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // verification failed: display message to customer
    echo $response->getMessage();
}
```

#### Method 2: Token + Expiry
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->verify([
    'amount' => '10.00',
    'currency' => 'ILS',
    'token' => 'U99e9abcd81c2ca4444',
    'expiryMonth' => 12,
    'expiryYear' => 2025
])->send();

if ($response->isSuccessful()) {
    // verification was successful: update database
    $transactionReference = $response->getTransactionReference();
    print_r($response);
} else {
    // verification failed: display message to customer
    echo $response->getMessage();
}
```

### Purchase

Purchase operations support two methods:

#### Method 1: Credit Card Details
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->purchase([
    'amount' => '10.00',
    'currency' => 'ILS',
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123'
    ]
])->send();

if ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```

#### Method 2: Token + Expiry
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->purchase([
    'amount' => '10.00',
    'currency' => 'ILS',
    'token' => 'U99e9abcd81c2ca4444',
    'expiryMonth' => 12,
    'expiryYear' => 2025
])->send();

if ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```

### Refund

Refund operations require authorization number + transaction reference + either token+expiry OR card details:

#### Method 1: Authorization Number + Transaction Reference + Token + Expiry
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->refund([
    'amount' => '10.00',
    'currency' => 'ILS',
    'transactionReference' => '12345',
    'authorizationNumber' => 'AUTH123',
    'token' => 'U99e9abcd81c2ca4444',
    'expiryMonth' => 12,
    'expiryYear' => 2025
])->send();

if ($response->isSuccessful()) {
    // refund was successful: update database
    print_r($response);
} else {
    // refund failed: display message to customer
    echo $response->getMessage();
}
```

#### Method 2: Authorization Number + Transaction Reference + Credit Card Details
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setAppKey('your_app_key');
$gateway->setSecret('your_secret');
$gateway->setTerminalName('your_terminal_name');

$response = $gateway->refund([
    'amount' => '10.00',
    'currency' => 'ILS',
    'transactionReference' => '12345',
    'authorizationNumber' => 'AUTH123',
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '12',
        'expiryYear' => '2025',
        'cvv' => '123'
    ]
])->send();

if ($response->isSuccessful()) {
    // refund was successful: update database
    print_r($response);
} else {
    // refund failed: display message to customer
    echo $response->getMessage();
}
```

## Setting the Terminal Password

Some Tranzila API operations (such as the handshake/iframe token request) require both the terminal name and the terminal password. You can set the terminal password using:

```php
$gateway->setTerminalPassword('your_terminal_password');
```

- For most payment operations (authorize, purchase, capture, refund, void, verify), only the terminal name is required.
- For the handshake (iframe token) operation, **both** terminal name and terminal password are required.

### Example: Handshake with Terminal Password

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setTerminalName('your_terminal_name');
$gateway->setTerminalPassword('your_terminal_password');

$response = $gateway->handshake([
    'amount' => '10.00',
])->send();

if ($response->isSuccessful()) {
    $handshakeToken = $response->getHandshakeToken();
    print_r($response->getResponseData());
} else {
    echo $response->getMessage();
}
```

### Handshake (Iframe Token)

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Tranzila');
$gateway->setTerminalName('your_terminal_name');
$gateway->setTerminalPassword('your_terminal_password');

$response = $gateway->handshake([
    'amount' => '10.00',
])->send();

if ($response->isSuccessful()) {
    // Get the handshake token for iframe usage
    $handshakeToken = $response->getHandshakeToken();
    // Or get a structured array with all details
    $data = $response->getResponseData();
    print_r($data);
} else {
    // handshake failed: display message to customer
    echo $response->getMessage();
}
```

## Supported Methods

This gateway supports the following payment methods:

- `authorize()` - Authorize a payment (supports token+expiry or card details)
- `capture()` - Capture a previously authorized payment (requires transaction reference, authorization number, and either token+expiry or card details)
- `purchase()` - Authorize and capture a payment in one step (supports token+expiry or card details)
- `refund()` - Refund a payment (requires authorization number+transaction reference+token+expiry OR authorization number+transaction reference+card details)
- `void()` - Void a payment (requires transaction reference and authorization number)
- `reversal()` - Reverse a payment (requires transaction reference, authorization number, and either token+expiry or card details)
- `verify()` - Verify a card without charging (supports token+expiry or card details)
- `handshake()` - Get iframe token for secure payment form

## Supported Currencies

The gateway supports the following currencies:
- ILS (Israeli Shekel)
- USD (US Dollar)
- EUR (Euro)
- GBP (British Pound)

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements and discuss the project
in general, please join the [mailing list](https://groups.google.com/forum/#!forum/omnipay) and [follow us on Twitter](https://twitter.com/thephpleague).

## Security

If you discover any security related issues, please email security@omnipay.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
