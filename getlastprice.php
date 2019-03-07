<?php
/**
 * Verus Price Api Data
 *
 * @category Cryptocurrency
 * @package  VerusPriceApi
 * @author   J Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @version 0.1.1
 * @link     https://github.com/joliverwestbrook/VerusPriceApi
 * 
 * This application allows the getting of average, volume weighted Verus market price from included exchanges and outputting to a file for remote access. 
 * Basic version includes average price data, exchange specific price data, and fiat prices
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
 * 
 * Future Exchange Support:
 * --
 * 
 * Past - Deprecated Exchange Support:
 * 
 *  'aacoin' => array(
 *          'url' => 'https://api.aacoin.com/market/tickers',
 *          'top' => 'data',
 *          'base' => 'ticker',
 *          'price' => 'last',
 *          'volume' => 'obv', // BTC volume
 *          'code' => 'symbol',
 *          'market' => '_BTC',
 *	        'mktcase' => 'upper',
 *	        'support' => array(
 *		        'vrsc' => 'vrsc',
 *		        ),
 *      ),
 * 
 */
global $connection_status;

$curl_requests = 0;
$connection_status = 1;

// API to use for fiat price conversions
$fiatexchange = "https://bitpay.com/api/rates";

// Build array of exchanges to include
$exch_data = array(
    'digitalprice' => array(
        'url' => 'https://digitalprice.io/api/markets?baseMarket=BTC',
        'top' => 'data',
        'base' => null,
        'price' => 'priceLast',
        'volume' => 'volumeBase', // BTC volume
        'code' => 'url',
        'market' => '-btc',
	'mktcase' => 'lower',
	'support' => array(
			'vrsc' => 'vrsc',
			'arrr' => 'arrr',
		     ),
    ),
    'stex' => array(
        'url' => 'https://app.stex.com/api2/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'volume' => 'vol_market', // BTC volume
        'code' => 'market_name',
        'market' => '_BTC',
	'mktcase' => 'upper',
	'support' => array(
			'vrsc' => 'vrsc',
		     ),
    ),
    'cryptobridge' => array(
        'url' => 'https://api.crypto-bridge.org/api/v1/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'volume' => 'volume', // BTC volume
        'code' => 'id',
        'market' => '_BTC',
	'mktcase' => 'upper',
	'support' => array(
			'vrsc' => 'vrsc',
			'arrr' => 'arrr',
		     ),
    )
);

// Check for get/post calls
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currency = strtoupper($_GET[ 'currency' ]);
    $exch_name = $_GET[ 'name' ];
    $ticker = strtolower( $_GET['ticker'] );
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency = strtoupper($_POST[ 'currency' ]);
    $exch_name = $_POST[ 'name' ];
    $ticker = strtolower( $_POST['ticker'] );
}

// If no currency set, default to USD; if no ticker, set to VRSC
if ( ! isset( $currency ) | empty( $currency ) ) {
    $currency = 'USD';
}
if ( ! isset( $ticker ) | empty( $ticker ) ) {
    $ticker = array(
	'vrsc',
	'arrr'
	);
}
foreach ( $ticker as $item ) {
    echo "<br>Going<br>";
	generatePriceData( $item, $currency, $exch_name, $fiatexchange, $exch_data );
}
// Function to output price data to rawpricedata_TICKER.php file
function generatePriceData( $ticker, $currency, $exch_name, $fiatexchange, $exch_data ) {
	global $connection_status;
	// Build array of exchange data
	$exch_results = array();
	foreach ( $exch_data as $exch_key => $exch_item ) {
	    if ( $exch_item['mktcase'] == 'lower' ) {
		$ticker = strtolower( $ticker );
	    }
	    if ( $exch_item['mktcase'] == 'upper' ) {
		$ticker = strtoupper( $ticker );
	    }
	    $is_supported = array_search( strtolower( $ticker ), $exch_item['support'] );
	    if ( $is_supported ) {
		$exch_results[$exch_key] = btcData( $ticker, $exch_item );
	    }
	}

	// Check for no data / broken connection
	if ( $connection_status === 0 ) {
	    die;
	}

    // Setup price results array data
    echo "<br>exchresults<br>";
    print_r($exch_results);
	$avg_btc = getAvg( $exch_results );
	$avg_fiat = fiatPrice( $currency, $fiatexchange, $avg_btc );

	// If get/post name set include specific exchange data
	if ( isset( $exch_name ) ) {
	    $sel_btc = $exch_results[$exch_name]['price'];
	    $sel_vol = $exch_results[$exch_name]['volume'];
	    $sel_fiat = fiatPrice( $currency, $fiatexchange, $sel_btc );
	}

	// Build price results array for output
	$price_results = array(
	    'date' => time(),
	    strtolower( $ticker ) => array(
	        'avg_btc' => $avg_btc,
	        'avg_fiat' => $avg_fiat,
	        'sel_name' => $exch_name,
	        'sel_btc' => $sel_btc,
	        'sel_vol' => $sel_vol,
	        'sel_fiat' => $sel_fiat,
	    ),
	);

	// Output results to file in json format
	$ticker = strtolower( $ticker );
	file_put_contents( dirname(__FILE__) . '/rawpricedata_' . $ticker . '.php', json_encode( $price_results, true ) );
}

// Function for getting data from exchange APIs
function btcData( $ticker, $exchange ) {
    global $connection_status;
    $results = json_decode( curlRequest( $exchange['url'], curl_init(), null ), true );
    // Check for json structure of input for construct
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
    if ( !isset( (array_column($data, $exchange['price'], $exchange['code'] )[ $ticker . $exchange['market'] ]) ) ) {
        $connection_status = 0;
        return;
    }
    else {
        // Return last price and 24 hour base volume
        return array(
            'price' => (array_column($data, $exchange['price'], $exchange['code'] )[ $ticker . $exchange['market'] ]),
            'volume' => (array_column($data, $exchange['volume'], $exchange['code'] )[ $ticker . $exchange['market'] ]),
        );
    }
}

// Function for processing exchange data and getting average base price, weighted by volume
function getAvg( $exch_results ) {
    // Get total volume
    $sum_volume = 0;
    foreach ($exch_results as $item) {
        $sum_volume += $item['volume'];
    }
    // Get each exchange volume weight
    $weighted_prices = array();
    foreach ( $exch_results as $key => $item ) {
        $weighted_prices[$key] = array(
            'weight' => number_format( number_format( ( $item['volume'] / $sum_volume ), 3 ) * 100, 1 ),
            'weight_as_price' => number_format( ( $item['price'] * floor( round( number_format( ( $item['volume'] / $sum_volume ), 3 ) * 1000 ) ) ), 8 ),
        );
    }
    // Get total price point for average
    $sum_price = 0;
    foreach ($weighted_prices as $item) {
        $sum_price += $item['weight_as_price'];
    }
    return number_format(($sum_price) / 1000, 8);
}

// Function to return Fiat price of given input data
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

// Curl function
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
