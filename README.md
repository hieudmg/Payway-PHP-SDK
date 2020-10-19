# Payway PHP API wrapper

## Disclaimer

This is base package I use for developing Payway payment plugin.
It might be clumsy in there, but it just got the job done.
If you have any suggestion, please open an issue.

## Usage

You cannot use this package out of the box immediately.
You must implement some function in order to make it work.
That provide flexibility and framework-compatibility.

### Implement the abstract functions

You need to inherit AbstractPaywayApiWrapper class to use it.
There are currently three functions need to be implemented:
 
```php
protected abstract function getStoreUid();
protected abstract function getMerchantInformation();
protected abstract function makeRequest($url, $method, $idempotencyKey = '', $data = null, $additionalHeaders = null);
```
- `getStoreUid`: This function is used to get saved store unique identifier in your framework.
Customer-related functionality needs this identifier to make saved customer in payway unique for each store.
Think of using the plugin in two store with same merchant credentials, without this identifier a customer with id 11 can be missused by the other stores and cause confusion.
- `getMerchantInformation`: This function returns merchant information saved in the framework's database.
It needs to return `PaywaySDK\Data\MerchantInformation` type.
- `makeRequest`: This function doing the request using the framework's implementation.
Each framework requires a different way to make outbound requests.
E.g: Magento 2 uses `Magento\Framework\HTTP\Client\Curl`, Wordpress uses `wp_remote_request`.
The function must return an array - the decoded JSON response from Payway gateway.
Don't forget to add necessary API headers and add idempotency key in response.
An example of Wordpress implementation with retry on failure:
```php
protected function makeRequest( $url, $method, $idempotencyKey = '', $data = null, $additionalHeaders = null ) {
    if ( ! is_array( $additionalHeaders ) ) {
        $additionalHeaders = [];
    }
    $headers = array_merge( $additionalHeaders, array(
        'Idempotency-Key' => $idempotencyKey,
        'Authorization'   => 'Basic ' . base64_encode( $this->getMerchantInformation()->getPrivateKey() ),
        'Content-Type'    => 'application/x-www-form-urlencoded'
    ) );

    if ( $method == METHOD_GET ) {
        $data = null;
    } elseif ( is_array( $data ) ) {
        $data = http_build_query( $data );
    } elseif ( ! is_string( $data ) ) {
        $data = null;
    }

    $result = array();

    for ( $retries = 0; $retries < 4; $retries ++ ) {
        if ( $retries > 0 ) {
            sleep( 20 );
        }

        $result = wp_remote_request( $url, array(
            'method'  => $method,
            'headers' => $headers,
            'body'    => $data,
            'timeout' => 5
        ) );

        if ( $result instanceof WP_Error ) {
            continue;
        } elseif ( $result['response']['code'] == 429 || $result['response']['code'] == 503 ) {
            continue;
        } elseif ( $result['response']['code'] > 200 && $result['response']['code'] < 299 || $result['response']['code'] == 422 ) {
            $result                   = json_decode( $result['body'], 1 );
            $result['idempotencyKey'] = $idempotencyKey;
            break;
        } else {
            unset( $result['cookies'] );
            unset( $result['http_response'] );
            break;
        }
    }

    return $result;
}
```

### Using the wrapper

After implement abstract functions, you can use it. Example usage:
```php
$api = new PaywayApiWrapper();
/** @var \PaywaySDK\Response\TransactionResponse $paymentResult */
$paymentResult = $api->takePayment( $paywayToken, $customerId, $orderTotal, $customerIpAddress );
if ($paymentResult && $paymentResult->isSuccess()) {
    // Process order here
} else {
    // Something went wrong, handle error here
}
```

## Contribution

If you found any problem and/or have idea of improvement, please open an issue.

## Support me
<a href="https://www.buymeacoffee.com/hieudmg" target="_blank"><img width="146" height="40" src="https://cdn.buymeacoffee.com/buttons/v2/default-violet.png" alt="Buy Me A Coffee"></a>
