# PriceApi - Latest Price Data for VRSC, ARRR, ZEC

 - Contributors: J Oliver Westbrook
 - Copyright: Copyright (c) 2019, John Oliver Westbrook 
 - Version: 0.1.4

## The MIT License (MIT)
 
Copyright (c) 2019 John Oliver Westbrook

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

## Description
Simple script for getting volume-weighted average last price across all active exchanges (included in this script, more to come)

This can be hosted on your own site.

The purpose of this api script is to provide an average last price for Verus Coin (VRSC) from all exchanges with 24 hour base (btc) weighting and on a slight delay so data is able to be accessed immediately despite delays and slow connections from various exchanges.

## Installation

> Best practice: rename getlastprice.php to an obscure name.

1. Clone this repo into the folder where you'll run your api from
2. Rename getlastprice.php to something obscure
3. Create a cron job to run the script at the interval you desire. For example: `*/1 * * * * /usr/bin/php /var/www/yourdomain.com/apifolder/getlastprice.php` will run every 1 minute.
4. Within the interval you set in cron, the first run will generate the price data as the index.php file within `lastprice` folder within your script folder.

## Use

For simple VRSC-Fiat price data, simply request the fiat currency of your choice (must be supported by BitPay) with`?currency=` - for example: `https://veruspay.io/api/?currency=GBP` to return simply the fiat price based on latest average exchange data and bitpay BTC pricing.  The currency of `BTC` is also supported.

For access to the latest raw price data, for example to access via a curl request, call `rawpricedata.php` which returns the json formatted avg price data, includes date stamp of when the price was retrieved, along with individual exchange price and volume data.

### Options (values are case insensitive): 

* currency - BTC or Fiat code like USD or CAD
* ticker - ARRR, VRSC, KMD, and ZEC are supported
* data - volume or price - volume only relevant if exchange is defined
* exch - name of supported exchange, e.g. digitalprice - If no exchange, price is average of all supported for that coin.

If no options are set, the default is average price in USD fiat of VRSC.

### Examples:

https://veruspay.io/api/ - This get's the current price of VRSC in USD, weighted against 24hr volume across all exchanges. This is the default return.

https://veruspay.io/api/?exch=digitalprice&currency=cad - This will get the current price on digital price for VRSC and display in CAD fiat

https://veruspay.io/api/?currency=btc - This will get the average price of VRSC in BTC, weighted by 24 hr volume across both exchanges

https://veruspay.io/api/?currency=cad - This gets the current average price of VRSC in CAD, weighted by 24 hr volume across both exchanges

https://veruspay.io/api/?exch=cryptobridge&data=volume - This will get the 24 volume of VRSC on CryptoBridge in the default currency of USD

https://veruspay.io/api/?exch=cryptobridge&data=volume&currency=btc - This does the same but with BTC as the currency result

https://veruspay.io/api/?currency=cad&ticker=arrr - Gets the average price of ARRR, now added to the api!

## Changelog

### 2019.03.22 - version 0.1.4

- Support for KMD Komodo added
- Support for ZEC Zcash added
- Inclusion of binance, bittrex, and huobi
- Change formatting of exch data array and related functions

### 2019.03.07 - version 0.1.2

- Support for ARRR Pirate added
- Inclusion of individual exchange data in rawpricedata output
- Options added for specific api calls
- Improved connection timeout and error handling

### 2019.02.26 - version 0.1.1

- 24-hour volume-weighted price data (no volume has no impact on price, all others have percentage weight on average price based on percentage of volume) *Thanks to https://github.com/miketout for this suggestion!
- Fix bug with USD default price data
- Add support for future features with more price and volume data
- Simplified CURL calls and functions

### 2019.02.18 - version 0.1.0

- Initial release with base features
- Exchange support for DigitalPrice, AACoin, STEX, and Crypto-Bridge. 
- Fiat prices calculated from bitpay.  
- Script at index.php can accept GET or POST for specific exchange last price and for currency.
- Date stamp recorded on each new price output.
