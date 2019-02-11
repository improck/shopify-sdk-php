# PHP Shopify SDK

PHP Shopify is a simple SDK implementation of Shopify API. It helps accessing the API in an object oriented way. 

## Installation
Install with Composer
```shell
composer require improck/shopify-sdk-php
```

## Usage

You can use PHP Shopify in a pretty simple object oriented way. 

##### Generate install url

```php
\Improck\Shopify\Shopify::generateInstallUrl($myshopifyDomain, $apiKey, $scopes, $redirectUrl)
```

##### Get the Shopify SDK Object

```php
$shopify = new \Improck\Shopify\Shopify($myshopifyDomain, $accessToken);
```

##### Get the access token when redirected back to the $redirectUrl after app authorization.

```php
$shopify = \Improck\Shopify\Shopify::authorize($myshopifyDomain, $code, $apiKey, $secretKey);
```

##### Making API calls

```php
$products = $shopify->get("products.json");

echo $products->count();

$products->each(function($product) {
    
    echo $product['title'];
    
});

$firstProduct = $products->first();

$result = $shopify->post("webhooks.json", ["webhook" => []]);

$result = $shopify->put("webhooks/4759306.json", ["webhook" => []]]);

$deletedStatus = $shopify->delete("products/5616516.json");
```
##### Collect methods

All methods can be viewed on the library wiki: https://laravel.com/docs/5.7/collections#available-methods