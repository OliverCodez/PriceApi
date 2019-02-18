# VerusPriceApi
Simple script for getting Verus average last price across all exchanges

This can be hosted or used via https://veruspay.io/api/lastprice/

The purpose of this api script is to provide an average last price for Verus Coin (VRSC) from all exchanges and on a slight delay so data is able to be accessed immediately despite delays and slow connections from various exchanges.  My Api at VerusPay.io above is on a 1 min update interval and the default fiat I've set is USD.

### Installation and Use

> Best practice: rename getlastprice.php to an obscure name.

1. Clone this repo into the folder where you'll run your api from
2. Rename getlastprice.php to something obscure
3. Create a cron job to run the script at the interval you desire. For example: `*/1 * * * * /usr/bin/php /var/www/yourdomain.com/apifolder/getlastprice.php` will run every 1 minute.
4. Within the interval you set in cron, the first run will generate the price data as the index.php file within `lastprice` folder within your script folder.

### Features

This is an early release, more features are being developed. 

- Exchange support for DigitalPrice, AACoin, STEX, and Crypto-Bridge. 
- Fiat prices calculated from bitpay.  
- Script can accept GET or POST for specific exchange last price and for currency.
- Date stamp recorded on each new price output.
