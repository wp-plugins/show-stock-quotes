<?php
/*
Plugin Name: Show Stock Quotes
Plugin URI: http://kylebenkapps.com/wordpress-plugins/
Description: Show stock quotes updated in real-time.
Version: 1.3.1
Author: Kyle Benk
Author URI: http://kylebenkapps.com
License: GPL2
*/

/*	Copyright 2013  Kyle Benk

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
class kjb_Show_Stocks extends WP_Widget {
	
	function kjb_Show_Stocks(){
		$widget_ops = array( 'classname' => 'kjb_show_stocks', 'description' => 'Display stock data in real-time.' );
		
		$this->options[] = array(
			'name'  => 'title', 'label' => 'Title',
			'type'	=> 'text', 	'default' => 'Stocks'	
		);
		
		for ($i = 1; $i < 21; $i++) {
			$this->options[] = array(
				'name'	=> 'stock_' . $i,	'label'	=> 'Stock Tickers',
				'type'	=> 'text',	'default' => ''			
			);
		}
		
		parent::WP_Widget(false, 'Show Stock Data', $widget_ops);	
	}
	
	
	/** @see WP_Widget::widget */
    function widget($args, $instance) {
		extract( $args );
		$title = $instance['title'];
		
		echo $before_widget;
		
		if ( $title != '') {
			echo $before_title . $title . $after_title;
		}else {
			echo 'Make sure settings are saved.';
		}
		
		$tickers = $this->kjb_get_stock_data($instance);
		
		//Display all stock data
		?>
		<table class="kjb_show_stock_quotes_table">
		  <col width='20%'>
		  <col width='40%'>
		  <col width='40%'>
		<?php
		foreach($tickers as $ticker) { 
			
			$new_ticker = str_replace('^', '-', $ticker);
			$new_ticker = str_replace('.', '_', $new_ticker);
		?>
			<tr style="border:none;"> 
				<td class="kjb_show_stock_quotes_ticker" style="border: none;"> <?php echo $ticker; ?> </td> 
				<td class="kjb_show_stock_quotes_quote_<?php echo $new_ticker; ?> kjb_show_stock_quotes_error"></td>
				<td class="kjb_show_stock_quotes_change_<?php echo $new_ticker; ?> kjb_show_stock_quotes_error"></td>
			</tr>
		<?php }
		?></table><?php
		
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		
		foreach ($this->options as $val) {
			$instance[$val['name']] = strip_tags(isset($new_instance[$val['name']]) ? $new_instance[$val['name']] : '');
		}
		
        return $instance;
    }
    
	/** @see WP_Widget::form */
    function form($instance) {
    	
    	if (isset($instance['title'])){
	    	$title = $instance['title'];
    	}else{
	    	$title = __('New title');    	
	    }
	    
	   
    	?>
    	<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<br /><br />
		<label>Stocks Tickers</label>
		<ol>
		
		<?php
		for ($i = 1; $i < 21; $i++) {
			$stock = isset($instance['stock_'.$i]) ? $instance['stock_'.$i] : '';
			?>
			<li><input class="widefat" id="<?php echo $this->get_field_id( 'stock_'.$i ); ?>" name="<?php echo $this->get_field_name( 'stock_'.$i); ?>" type="text" value="<?php echo esc_attr( $stock ); ?>" /></li>
			<?php
		}
		?>
		</ol>
		</p>
		<?php 
	}
	
	
	protected function kjb_get_stock_data($ticker_info) {
	
		wp_register_script('kjb_quotes_js_src', plugins_url('include/js/kjb_quotes.js', __FILE__));
		wp_enqueue_script('kjb_quotes_js_src');
		
		wp_register_style('kjb_quotes_css_src', plugins_url('include/css/kjb_quotes.css', __FILE__));
		wp_enqueue_style('kjb_quotes_css_src');
		
		wp_enqueue_script('kjb_jquery', "//code.jquery.com/jquery-1.10.2.js");
		
		//$out = get_transient('kjb_stockdata_transient');
		
		//if (false === $out){
		for ($i = 1; $i < 21; $i++) {
			$ticker = $ticker_info['stock_'.$i];
			if ($ticker != '') {
				//Get stock data
				//$contents = str_getcsv(file_get_contents('http://download.finance.yahoo.com/d/quotes.csv?s='.$ticker.'&f=sl1c1c0&e=.csv'),',');
				/*
if ($contents[1] != '0.00') {
					$temp = array(
					'ticker' => $contents[],
					'quote' => $contents[],
					'change' => $contents[],
					'change_precent' => $contents[]
				);
				}else{
					$temp = 'Invalid ticker';
				}
*/					
				$out[] = $ticker;
			}
		}
		
		wp_localize_script('kjb_quotes_js_src', 'stock_array', $out);
			
			
			//set_transient('kjb_stockdata_transient',$out,60);	
		//}
		
		return $out;
	}
}

add_action( 'widgets_init', function(){
     register_widget( 'kjb_Show_Stocks' );
});
?>
