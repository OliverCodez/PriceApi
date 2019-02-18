<?php
/**
 * Verus Price Api Data
 *
 * @category Cryptocurrency
 * @package  VerusPriceApi
 * @author   John Oliver Westbrook <johnwestbrook@pm.me>
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
$fiatexchange = "https://bitpay.com/api/rates";
$rawpricedata = json_decode( file_get_contents( dirname(__FILE__) . '/rawpricedata.php' ), true);
$btcprice = $rawpricedata['data']['avg_btc'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currency = $_GET[ 'currency' ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency = $_POST[ 'currency' ];
}
if ( ! isset( $currency ) ) {
    $currency = 'USD';
}
echo fiatPrice( $currency, $fiatexchange, $btcprice );

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
