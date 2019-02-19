<?php
/**
 * Verus Price Api Data
 *
 * @category Cryptocurrency
 * @package  VerusPriceApi
 * @author   J Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusPriceApi
 * 
 * This application allows the getting of average Verus market price from included exchanges and outputting to a file for remote access. Basic version.
 * ====================
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2019 John Oliver Westbrook
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * ====================
 */
$curl_requests = 0;

$fiatexchange = "https://bitpay.com/api/rates";
$exch_data = array(
    'digitalprice' => array(
        'url' => 'https://digitalprice.io/api/markets?baseMarket=BTC',
        'top' => 'data',
        'base' => null,
        'price' => 'priceLast',
        'code' => 'url',
        'market' => 'vrsc-btc',
    ),
    'aacoin' => array(
        'url' => 'https://api.aacoin.com/market/tickers',
        'top' => 'data',
        'base' => 'ticker',
        'price' => 'last',
        'code' => 'symbol',
        'market' => 'VRSC_BTC',
    ),
    'stex' => array(
        'url' => 'https://app.stex.com/api2/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'code' => 'market_name',
        'market' => 'VRSC_BTC',
    ),
    'cryptobridge' => array(
        'url' => 'https://api.crypto-bridge.org/api/v1/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'code' => 'id',
        'market' => 'VRSC_BTC',
    )
);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currency = strtoupper($_GET[ 'currency' ]);
    $exch_name = $_GET[ 'name' ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency = strtoupper($_POST[ 'currency' ]);
    $exch_name = $_GET[ 'name' ];
}

if ( ! isset( $currency ) ) {
    $currency = 'USD';
}

// Build array of prices
$exch_prices = array();
foreach ( $exch_data as $exch_item ) {
    $exch_prices[] = btcPrice( $exch_item );
}
$avg_btc = number_format( array_sum($exch_prices) / count($exch_prices), 8 );
$avg_fiat = fiatPrice( $currency, $fiatexchange, $avg_btc );
$sel_btc = btcPrice( $exch_data[$exch_name] );
$sel_fiat = fiatPrice( $currency, $fiatexchange, $sel_btc );
$price_results = array(
    'date' => time(),
    'data' => array(
        'avg_btc' => $avg_btc,
        'avg_fiat' => $avg_fiat,
        'sel_name' => $exch_name,
        'sel_btc' => $sel_btc,
        'sel_fiat' => $sel_fiat,
    ),
);
file_put_contents( dirname(__FILE__) . '/rawpricedata.php', json_encode( $price_results, true ) );

function btcPrice( $exchange ) {
    $results = json_decode( curlRequest( $exchange['url'], curl_init(), null ), true );
    if ( $exchange['top'] != null ) {
        if ( $exchange['base'] != null ) {
            $data = $results[ $exchange['top'] ][ $exchange['base'] ];
        }
        else {
            $data = $results[ $exchange['top'] ];
        }
    }
    else {
        $data = $results;
    }
    return array_column( $data, $exchange['price'], $exchange['code'] )[ $exchange['market'] ];
}

function fiatPrice( $currency, $fiatexchange, $btcprice ) {
    $fiatrates = json_decode( curlRequest( $fiatexchange, curl_init(), null ), true );
    $fiatrates = array_column( $fiatrates, 'rate', 'code' );
    $rate = $fiatrates[$currency];
    if ( empty(  $rate ) ) {
        $fiatExchRate = 0;
    }
    else {
        $rate = number_format( ( $btcprice * $rate ), 8 );
        $fiatrate = number_format( ( $rate ), 4 );
        $fiatExchRate = $fiatrate;
    }
    return $fiatExchRate;
}

function curlRequest( $url, $curl_handle, $fail_on_error = false ) {
    global $curl_requests;

    if ( $curl_handle === false ) {
        return false;
    }
    if ( $fail_on_error === true ) {
        curl_setopt( $curl_handle, CURLOPT_FAILONERROR, true );
    }
    curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl_handle, CURLOPT_USERAGENT, 'Verus Price API' );
    curl_setopt( $curl_handle, CURLOPT_URL, $url );
    $curl_requests++;
    return curl_exec( $curl_handle );
}

?>
