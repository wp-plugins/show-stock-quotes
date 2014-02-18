/*
 * Created by Kyle Benk
 * http://kylebenkapps.com
 *
 * Credit to http://jsfiddle.net/vlad_bezden/RU8Jq/
 */

jQuery(document).ready(function($) {
	
	if (typeof(stock_array) != "undefined" && stock_array !== null) {
		for (var i = 0; i < stock_array.length; i++) {
		
			var url = "http://query.yahooapis.com/v1/public/yql";
		    var data = encodeURIComponent("select * from yahoo.finance.quotes where symbol in ('" + stock_array[i] + "')");
		
		    $.getJSON(url, 'q=' + data + "&format=json&diagnostics=true&env=http%3A%2F%2Fdatatables.org%2Falltables.env")
		        .done(function (data) {
			        
			        if (typeof(data.query.results) != "undefined" && data.query.results !== null) {
				        if (data.query.results.quote.Change <= 0) {
					        $(".kjb_show_stock_quotes_quote_" + data.query.results.quote.Symbol).attr('style', 'border: none; color:red; text-align:right'); 
							$(".kjb_show_stock_quotes_change_" + data.query.results.quote.Symbol).attr('style', 'border: none; color:red; text-align:right');
				        }else{
					        $(".kjb_show_stock_quotes_quote_" + data.query.results.quote.Symbol).attr('style', 'border: none;color:green; text-align:right');			        
							$(".kjb_show_stock_quotes_change_" + data.query.results.quote.Symbol).attr('style', 'border: none;color:green; text-align:right');
				        }
				        
				        $(".kjb_show_stock_quotes_quote_" + data.query.results.quote.Symbol).text(data.query.results.quote.LastTradePriceOnly);
						$(".kjb_show_stock_quotes_change_" + data.query.results.quote.Symbol).text(data.query.results.quote.Change);
						
						if (data.query.results.quote.LastTradePriceOnly == 0) {
							$(".kjb_show_stock_quotes_quote_" + data.query.results.quote.Symbol).attr('style', 'border: none;color:red; text-align:right'); 
							$(".kjb_show_stock_quotes_change_" + data.query.results.quote.Symbol).attr('style', 'border: none;color:red; text-align:right');
							$(".kjb_show_stock_quotes_quote_" + data.query.results.quote.Symbol).text('Invalid');
							$(".kjb_show_stock_quotes_change_" + data.query.results.quote.Symbol).text('Invalid');
						}
			        } /*
else {
			        	console.log('fail');
			        	console.log('--'  + $(".kjb_show_stock_quotes_error").text() + '--');
			        	
			        	if ($(".kjb_show_stock_quotes_error").text() == null) {
				        	$(".kjb_show_stock_quotes_error").attr('style', 'border: none;color:red; text-align:right');
							$(".kjb_show_stock_quotes_error").text('Failed');
			        	}
						
			        }
*/
		    })
		        .fail(function (jqxhr, textStatus, error) {
		        	var err = textStatus + ", " + error;
		        	//console.log(err);
		    });
		}
	}
});