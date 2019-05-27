function _veruspriceapi_Get_Price( currency, coin, price ) {
    var bkcolor = '#dddddd';
    var green = '#00770085';
    var red = '#ff000061';
    var newval = '';
    var container = '#'+coin;
    var lastval = jQuery( price ).text();
    jQuery( price ).data( 'currency', currency );
    jQuery.ajax({
        type: 'post',
        url: 'https://veruspay.io/api/',
        data: {currency: currency, ticker: coin},
        success: function(response){
            jQuery(price).hide();
            newval = response;
            if ( lastval > newval ) {
                var value = newval.toString();
                jQuery(price).html(value);
                jQuery( container ).css('background-color',red);
                jQuery(price).fadeIn(800).queue( function(next){
                    jQuery( container ).css( 'background-color', bkcolor ); 
                    next();
                });
            }
            if ( lastval < newval ) {
                var value = newval.toString();
                jQuery(price).html(value);
                jQuery( container ).css('background-color',green);
                jQuery(price).fadeIn(800).queue( function(next){
                    jQuery( container ).css( 'background-color', bkcolor ); 
                    next();
                });					
            }
            else {
                var value = newval.toString();
                jQuery(price).html(value);
                jQuery(price).fadeIn(800).queue( function(next){
                    jQuery( container ).css( 'background-color', bkcolor ); 
                    next();
                });				
            }						
        }
    });
}
jQuery( function( $ ) {
	$(document).ready(function() {

        $('.currency_select').on('change', function() {
            var theid = $( this ).data( 'coin' );
            var thevalue = $( this ).val();
            var theprice = '#'+theid+'_price';
            $( '#'+theid ).data('currency',thevalue);
            _veruspriceapi_Get_Price( thevalue, theid, theprice );
        });

        $( '._veruspriceapi_price' ).each(function(){
            var currency = $( this ).data( 'currency' );
            var coin = $( this ).data( 'coin' );
            var price = '#'+coin+'_price';
            _veruspriceapi_Get_Price( currency, coin, price );
        });

        setInterval( function() {
            $( '._veruspriceapi_price' ).each(function(){
                var currency = $( this ).data( 'currency' );
                var coin = $( this ).data( 'coin' );
                var price = '#'+coin+'_price';
                _veruspriceapi_Get_Price( currency, coin, price );
            });
        }, (10000));

    });
});

