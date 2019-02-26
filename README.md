## VerusPriceApi

 - Contributors: J Oliver Westbrook
 - Tags: verus, vrsc, api, price, fiat, btc, exchange, exchanges
 - Stable Tag: 0.1.1
 - Copyright: Copyright (c) 2019, John Oliver Westbrook 
 - Version: 0.1.1

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
Simple script for getting Verus, volume-weighted, average last price across all active exchanges

This can be hosted on your own site, or accessed for free via https://veruspay.io/api/

The purpose of this api script is to provide an average last price for Verus Coin (VRSC) from all exchanges with 24 hour base (btc) weighting and on a slight delay so data is able to be accessed immediately despite delays and slow connections from various exchanges.  My Api at VerusPay.io above is on a 1 min update interval and the default fiat I've set is USD.

### Installation

> Best practice: rename getlastprice.php to an obscure name.

1. Clone this repo into the folder where you'll run your api from
2. Rename getlastprice.php to something obscure
3. Create a cron job to run the script at the interval you desire. For example: `*/1 * * * * /usr/bin/php /var/www/yourdomain.com/apifolder/getlastprice.php` will run every 1 minute.
4. Within the interval you set in cron, the first run will generate the price data as the index.php file within `lastprice` folder within your script folder.

### Use

1. For simple VRSC-Fiat price data, simply request the currency of your choice (must be supported by BitPay) with`?currency=` - for example: `https://veruspay.io/api/?currency=GBP` to return simply the fiat price based on latest average exchange data and bitpay BTC pricing.
2. For access to the latest raw price data, for example to access via a curl request, call `rawpricedata.php` which returns the json formatted avg price data, includes date stamp of when the price was retrieved. 

### Features

This is an early release, more features are being developed. 

- Exchange support for DigitalPrice, AACoin, STEX, and Crypto-Bridge. 
- Fiat prices calculated from bitpay.  
- Script can accept GET or POST for specific exchange last price and for currency.
- Date stamp recorded on each new price output.

### Changelog

### 2019.02.26 - version 0.1.1

- 24-hour volume-weighted price data (no volume has no impact on price, all others have percentage weight on average price based on percentage of volume) *Thanks to https://github.com/miketout for this suggestion!
- Fix bug with USD default price data
- Add support for future features with more price and volume data
- Simplified CURL calls and functions

### 2019.02.18 - version 0.1.0

- Initial release with base features
