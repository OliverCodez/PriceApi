<?php
$coins = array(
    'vrsc',
    'kmd',
    'arrr',
);
?>

<!doctype html>
<html lang="en-US">

<head>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>

<title>Verus Price API</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="Keywords" content="VRSC,Verus,VerusCoin,Cryptocurrency,Ticker,Exchange,Crypto,Blockchain,Bitcoin,Komodo,BTC,KMD">
<meta name="Description" content="Verus VRSC price ticker">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link href="https://fonts.googleapis.com/css?family=Source Code Pro" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

</head>
<body>
<header><span>VerusPay API Ticker</span><span>Developed with love for VRSC by <a href="https://github.com/joliverwestbrook" target="_BLANK">Oliver Westbrook</a></span></header>
<main>
    <?php
        foreach ( $coins as $item ) {
            echo '<div id="'.$item.'" class="coin_container">
                    <div class="sub_container">
                        <span class="coinimage"></span>'.strtoupper($item).' Price: <span id="'.$item.'_price" class="_veruspriceapi_price" data-currency="USD" data-coin="'.$item.'">0.00</span>
                    </div>
                    <div class="sub_container">
                        <select id="'.$item.'_select" class="currency_select" data-coin="'.$item.'">
                            <option value="USD">USD</option>
                            <option value="BTC">BTC</option>
                            <option value="CAD">CAD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="CNY">CNY</option>
                        </select>
                    </div>
                </div>';
        }
    ?>
</main>
<footer><a href="https://veruscoin.io" target="_BLANK">Learn about Verus Coin VRSC Community Project</a></footer>
</body>
</html>
