<?php
/**
 * Verus Price Api Data
 *
 * @category Cryptocurrency
 * @package  VerusPriceApi
 * @author   J Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @version 0.1.5
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
global $master_results;
$master_results = array();

$curl_requests = 0;
$connection_status = 1;

// API to use for fiat price conversions
$fiatexchange = "https://bitpay.com/api/rates";

// Setup supported coins and default fiat
$currency = 'USD';
$ticker = array(
	'vrsc',
    'arrr',
    'kmd',
    'zec',
);

// Build array of exchanges to include
$exch_data = array(
    'digitalprice' => array(
        'url' => 'https://digitalprice.io/api/markets?baseMarket=BTC',
        'top' => 'data',
        'base' => null,
        'price' => 'priceLast',
        'volume' => 'volumeBase', // BTC volume
        'code' => 'url',
        'market' => array(
			'vrsc' => 'vrsc-btc',
			'arrr' => 'arrr-btc',
		),
    ),
    'stex' => array(
        'url' => 'https://app.stex.com/api2/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'volume' => 'vol_market', // BTC volume
        'code' => 'market_name',
        'market' => array(
			'vrsc' => 'VRSC_BTC',
		),
    ),
    'cryptobridge' => array(
        'url' => 'https://api.crypto-bridge.org/api/v1/ticker',
        'top' => null,
        'base' => null,
        'price' => 'last',
        'volume' => 'volume', // BTC volume
        'code' => 'id',
        'market' => array(
			'vrsc' => 'VRSC_BTC',
			'arrr' => 'ARRR_BTC',
		),
    ),
    'binance' => array(
        'url' => 'https://www.binance.com/api/v1/ticker/24hr',
        'top' => null,
        'base' => null,
        'price' => 'lastPrice',
        'volume' => 'volume', // BTC volume
        'code' => 'symbol',
        'market' => array(
            'kmd' => 'KMDBTC',
            'zec' => 'ZECBTC',
        ),
    ),
    'bittrex' => array(
        'url' => 'https://bittrex.com/api/v1.1/public/getmarketsummaries',
        'top' => 'result',
        'base' => null,
        'price' => 'Last',
        'volume' => 'BaseVolume', // BTC volume
        'code' => 'MarketName',
        'market' => array(
			'kmd' => 'BTC-KMD',
			'zec' => 'BTC-ZEC',
		),
    ),
    'huobi' => array(
        'url' => 'https://api.huobi.pro/market/tickers',
        'top' => 'data',
        'base' => null,
        'price' => 'close',
        'volume' => 'vol', // BTC volume
        'code' => 'symbol',
        'market' => array(
			'kmd' => 'kmdbtc',
			'zec' => 'zecbtc',
		),
    ),

);

// Generate the price data for all included coins
foreach ( $ticker as $item ) {
	generatePriceData( $item, $currency, $fiatexchange, $exch_data );
}
// Function to output price data to rawpricedata.php file
function generatePriceData( $ticker, $currency, $fiatexchange, $exch_data ) {
    global $connection_status;
    global $master_results;

	// Build array of exchange data
	$exch_results = array();
	foreach ( $exch_data as $exch_key => $exch_item ) {
	    if ( array_key_exists( strtolower( $ticker ), $exch_item['market'] ) ) {
            $exch_results[$exch_key] = btcData( $ticker, $exch_item, $exch_key );
	    }
	}

    // Setup price results array data
	$avg_btc = getAvg( $exch_results );
	$avg_fiat = fiatPrice( $currency, $fiatexchange, $avg_btc );

	// Build price results array for output
	$price_results = array(
        'avg_data' => array(
            'date' => time(),
            'avg_btc' => $avg_btc,
            'avg_fiat' => $avg_fiat,
        ),
        'exch_data' => $exch_results,
	);

	// Output results to file in json format
    $ticker = strtolower( $ticker );
    $master_results[$ticker] = $price_results;
	
}
// Write data to file
file_put_contents( dirname(__FILE__) . '/rawpricedata.php', json_encode( $master_results, true ) );

// Function for getting data from exchange APIs
function btcData( $ticker, $exchange, $exch_name ) {
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
    if ( !isset( (array_column($data, $exchange['price'], $exchange['code'] )[ $exchange['market'][$ticker] ]) ) ) {
        // Use previous data if no connection, set variable for later use
        $connection_status = 0;
        $previous_data = json_decode( file_get_contents( dirname(__FILE__) . '/rawpricedata.php' ), true)[strtolower( $ticker )];
        return array(
            'date' => $previous_data['exch_data'][$exch_name]['date'],
            'price' => $previous_data['exch_data'][$exch_name]['price'],
            'volume' => $previous_data['exch_data'][$exch_name]['volume'],
        );
    }
    else {
        // Return last price and 24 hour base volume
        return array(
            'date' => time(),
            'price' => (array_column($data, $exchange['price'], $exchange['code'] )[ $exchange['market'][$ticker] ]),
            'volume' => (array_column($data, $exchange['volume'], $exchange['code'] )[ $exchange['market'][$ticker] ]),
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