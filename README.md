# GPWebpay SDK package for Laravel.

This package provides a simple way to integrate GPWebpay payment gateway into Laravel applications.

### Requirements for the package
- PHP 8.2 or higher
- Laravel 10 or higher

## Installation

### Install the package via composer

```bash
    composer require websystem/gp-webpay-sdk
```

### Publish the configuration file

```bash
    php artisan vendor:publish --tag=webpay-config
```

### Define environment variables
Save the following environment variables to the `.env` file in the root directory of the project.
 Locate your keys and certificates in the `storage` directory.


```php
GPWEBPAY_PRIVATE_KEY_PATH= # Storage path to the private key
GPWEBPAY_PRIVATE_KEY_PASSWORD= # Password for the private key
GPWEBPAY_PUBLIC_KEY_PATH= # Storage path to the public key
GPWEBPAY_MERCHANT_NUMBER= # Merchant number
GPWEBPAY_URL=https://test.3dsecure.gpwebpay.com/pgw/order.do
GPWEBPAY_ADD_INFO_SCHEMA= # Storage path to the additional info schema
```

### Available methods

#### Create payment
```php
$paymentData = [
    'orderNumber' => 1001, # Unique order number
    'amount' => 150.50, # Float value
    'currency' => Currency::EUR,
    'depositFlag' => 1, 
    'returnUrl' => 'https://example.com/callback',
    'addInfo' => [
        'name' => 'John Doe',
        'email' => 'doe@invelity.com',
    ],
    'addInfoSchemaPath' => config('gpwebpay.add_info_schema'),
];

    $requestUrl = Gpwebpay::createPaymentRequestUrl($paymentData);

    return redirect($requestUrl);
```
