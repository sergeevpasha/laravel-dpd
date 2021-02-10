[![Maintainability](https://api.codeclimate.com/v1/badges/52ea85ccfbc7d77dee10/maintainability)](https://codeclimate.com/github/sergeevpasha/laravel-dpd/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/52ea85ccfbc7d77dee10/test_coverage)](https://codeclimate.com/github/sergeevpasha/laravel-dpd/test_coverage)
[![CodeFactor](https://www.codefactor.io/repository/github/sergeevpasha/laravel-dpd/badge)](https://www.codefactor.io/repository/github/sergeevpasha/laravel-dpd)
[![Generic badge](https://img.shields.io/badge/PHP-^7.4.*-blue.svg)](https://www.php.net)
[![Generic badge](https://img.shields.io/badge/Laravel-^8.0-red.svg)](https://laravel.com)

# Laravel DPD API Wrapper
Allows you to:
* Find a City by query string
* Find a Street by City ID and query string
* Find a City that has Receive Points / Terminals
* Find Receive Points / Terminals
* Find Terminals
* Calculate a delivery

## No Database required

If you did research of DPD API you must know, that they suppose you to fetch data files with cities, terminals and streets and manage all of it by your own. However, with this package, there is no need for that.

## Pre-requirements
You need to get DPD API key, user, login and password.
Key can be obtained in your cabinet at https://www.dpd.ru/ols/order/personal/integrationKey.do2

## Installation
<pre>composer require sergeevpasha/laravel-dpd</pre>

## Configuration
This package has a few configuration values:
<pre>
'key'        => env('DPD_KEY', null),
'user'       => env('DPD_USER', null),
'login'      => env('DPD_LOGIN', null),
'password'   => env('DPD_PASSWORD', null),
'prefix'     => 'dpd',
'middleware' => ['web']
</pre>
If you only need to use DPDClient, you may completely skip this configuration. Otherwise, you can use default options and specify some data in .env file:
* DPD_KEY
* DPD_USER
* DPD_LOGIN
* DPD_PASSWORD

To make full use of predefined routes, you will need to publish the config:
<pre>
php artisan vendor:publish --provider="SergeevPasha\DPD\Providers\DPDServiceProvider" --tag="config"
</pre>
Now you can change routes prefix and middleware to whatever you need

### Use Case #1
After installing, you may just import the client
<pre>use SergeevPasha\DPD\Libraries\DPDClient;</pre>
Firstly let's initialize and get a session. Session is required for a few methods.
<pre>
/* 
    You may find your User ID by entering DPD Cabinet.
    Here we are initializing the client.
*/
$client = new DPDClient('user', 'key');
/* 
    Please make sure you understand the diff between login and User.
    We need to get a session, so we are authorizing with login and password
*/
$session = client->authorize('login', 'password);
</pre>
Now we can use these methods:
<pre>
$client->findCity(string $query, string $country)
$client->findCityStreet(int $city, string $query, string $session)
$client->findReceivePointCity(string $query)
$client->getReceivePoints(string $bounds, string $city)
$client->getTerminals(string $bounds, string $city)
/* This one requires a Delivery Object, see next to see how to build it */
$client->getPrice(Delivery $delivery)
</pre>
## Delivery Object
To build a Delivery object you will need to pass an array to fromArray() method just like that:<br>
<pre>
Delivery::fromArray([
    arrival_city_id     => 123456, // Arrival City ID from findCity() method
    derival_city_id     => 123456, // Derival City ID from findCity() method
    arrival_terminal    => 1, // Set 1 if you are delivering to terminal
    derival_terminal    => 0, // Set 1 if you send from terminal
    parcel_total_weight => 20, // Total parcel weight, KG
    parcel_total_volume => 0.5, // Total parcel volume, M<sup>3</sup>
    parcel_total_value  => 1000500.50, // Total parcel volume, RUB
    pickup_date         => 2020-10-10, // YYYY-MM-DD Format, when your parcel should be picked up for delivery
    max_delivery_days   => 15, // Show only options that can be delivered for that or less amount of days
    max_delivery_price  => 1000.10, // Show only options that costs that or less price
    services => [
        'ECU',
        'CUR'
    ], // List of available services
])
</pre>

## Available countries
If you need to specify a country you need to use one of these codes:
<pre>
RU - Russia
KZ - Kazakhstan
AM - Armenia
BY - Belarus
KG - Kyrgyzstan
</pre>

## Available services
If you need to specify a service you need to use one of these codes:
<pre>
    BZP = '18:00';
    ECN - ECONOMY
    ECU - ECONOMY CU
    CUR - CLASSIC
    NDY - EXPRESS
    CSM - Online Express
    PCL - OPTIMUM
    PUP - SHOP
    DPI - CLASSIC international IMPORT
    DPE - CLASSIC international EXPORT
    MAX - MAX domestic
    MXO - Online Max
</pre>

### Use Case #2

There are some predefined routes, that will be merged with your routes as well. You may check it by using
<pre>php artisan routes:list</pre>
It actually exposes the same methods to the routes, so it should be pretty clear on how to use it.
For more information on how to use it, please check out `src/` folder.
