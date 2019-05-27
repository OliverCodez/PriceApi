jQuery( function( $ ) {
    $.getScript('https://veruspay.io/price/js/scripts.js', function() {
        var e = '#_verus_api_ticker';
        var c = $(e).attr('coin');
        $(e).html('<style>.coin_container{display:block;position:relative;float:left;background-color:#dddddd;border-radius:14px;max-width:380px;padding:10px 20px;width:100%;transition:background-color 0.5s ease;}.coin_container:nth-child(2){float:right;}.coin_container:last-child{float:none;margin:auto;}.sub_container{display:inline-block;padding:05px; font-family: arial;font-weight: bold;font-size: 20px;height: 60px;line-height: 60px;}._veruspriceapi_price {float: right;padding: 0px 14px;}.coinimage {display: block;float: left;height: 60px;width: 30px;margin: 0 10px;background-position: center;background-repeat: no-repeat;background-size: contain;}#vrsc .coinimage {background-image: url(https://veruspay.io/price/img/wc-verus-icon-128x128.png);}#kmd .coinimage {background-image: url(https://veruspay.io/price/img/wc-komodo-icon-128x128.png);}#arrr .coinimage {background-image: url(https://veruspay.io/price/img/wc-pirate-icon-128x128.png);}select.currency_select {max-width: 80px;font-size: 20px;border: none;}@media (max-width:1279px){.coin_container {max-width: 240px;padding: 10px 2px;height: 80px;}.sub_container {height: 40px;}.coinimage {margin-left: 0;}.sub_container:nth-child(2){float: right;width: 70px;line-height: 40px;}}@media (max-width:767px) {.coin_container {float:none;width: 100%;margin:20px auto;padding: 10px 20px;max-width: 330px;}.coin_container:first-child{margin:0 auto;}.coin_container:nth-child(2){float:none;}.sub_container:nth-child(2) {line-height: 80px;}.coinimage {height:80px;}}@media (max-width:414px){.coin_container {max-width: 270px;padding: 10px 5px;height: 80px;}.sub_container {position: relative;display: block;float: left;height: 40px;width: 100%;}.sub_container:nth-child(2){float:right;width: 70px;line-height: 40px;margin-right: 24px;}.coinimage {height:60px;}}</style><div id="'+c+'" class="coin_container" style="background-color: rgb(221, 221, 221);"><div class="sub_container"><span class="coinimage"></span>VRSC: <span id="'+c+'_price" class="_veruspriceapi_price" data-currency="USD" data-coin="'+c+'" style="">0.00</span></div><div class="sub_container"><select id="'+c+'_select" class="currency_select" data-coin="'+c+'"><option value="USD">USD</option><option value="BTC">BTC</option><option value="CAD">CAD</option><option value="EUR">EUR</option><option value="GBP">GBP</option><option value="CNY">CNY</option></select></div></div>');
    });
});