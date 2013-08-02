<?php
/*
Plugin Name: Show Stock Quotes
Plugin URI: http://kylebenkapps.com/wordpress-plugins/
Description: Show stock quotes updated in real-time.
Version: 1.0
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
	
	function kjb_show_stocks(){
		$widget_ops = array( 'classname' => 'kjb_show_stocks', 'description' => 'Display stock data in real-time.' );
		
		$this->options = array(
			array(
				'name'  => 'title', 'label' => 'Title',
				'type'	=> 'text', 	'default' => 'Stocks'	),
			array(
				'name'	=> 'stock_1',	'label'	=> 'Stock Tickers',
				'type'	=> 'text',	'default' => 'AAPL'			),
			array(
				'name'	=> 'stock_2',	'label'	=> '',
				'type'	=> 'text',	'default' => 'GOOG'			),
			array(
				'name'	=> 'stock_3',	'label'	=> '',
				'type'	=> 'text',	'default' => 'IBM'			),
			array(
				'name'	=> 'stock_4',	'label'	=> '',
				'type'	=> 'text',	'default' => 'ORCL'			),
			array(
				'name'	=> 'stock_5',	'label'	=> '',
				'type'	=> 'text',	'default' => 'HPQ'			)
		);
		
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
		
		$contents = $this->kjb_get_stock_data($instance);
		
		//Display all stock data
		?>
		<table id = 'table'>
		  <col width='75'>
		  <col width='75'>
		  <col width='100'>
		<?php
		foreach($contents as $item)
		{
			if (count($item) == 4) {
				?> <tr> <?php
				if ($item['change'] > 0){
					?> <td id="ticker"> <?php echo $item['ticker']; ?> </td> 
					   <td id="quote"> <?php echo '$'.$item['quote']; ?> </td>
					   <td style = 'color:green'> <?php echo $item['change']; ?> </td> <?php
				}else{
					?> <td id="ticker"> <?php echo $item['ticker']; ?> </td> 
					   <td id="quote"> <?php echo '$'.$item['quote']; ?> </td> 
					   <td style = 'color:red'> <?php echo $item['change']; ?> </td> <?php
				}
				?> </tr> <?php
			}
		}
		?></table><?php
		
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		
		foreach ($this->options as $val) {
			$instance[$val['name']] = strip_tags($new_instance[$val['name']]);
		}
		
        return $instance;
    }
    
	/** @see WP_Widget::form */
    function form($instance) {
    	if (isset($instance)){
	    	$title = $instance[ 'title' ];
    	}else{
	    	$title = __( 'New title', 'text_domain' );    	
	    }
	    
	   
    	?>
    	<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<br /><br />
		<label>Stocks Tickers</label>
		<?php
		for ($i = 1; $i < 6; $i++) {
			$stock = $instance['stock_'.$i];
			?><input class="widefat" id="<?php echo $this->get_field_id( 'stock_'.$i ); ?>" name="<?php echo $this->get_field_name( 'stock_'.$i); ?>" type="text" value="<?php echo esc_attr( $stock ); ?>" />
			<?php
		}
		?>
		</p>
		<?php 
	}
	
	
	protected function kjb_get_stock_data($ticker_info){
		$out = get_transient('kjb_stockdata_transient');
		
		if (false === $out){
			for ($i = 1; $i < 6; $i++) {
				$ticker = $ticker_info['stock_'.$i];
				if ($ticker != '') {
					//Get stock data
					$contents = str_getcsv(file_get_contents('http://download.finance.yahoo.com/d/quotes.csv?s='.$ticker.'&f=sl1c1c0&e=.csv'),',');
					if ($contents[1] != '0.00') {
						$temp = array(
							'ticker' => $contents[0],
							'quote' => $contents[1],
							'change' => $contents[2],
							'change_precent' => $contents[3]
						);
					}else{
						$temp = 'Invalid ticker';
					}
					$out[] = $temp;
				}
			}
			
			set_transient('kjb_stockdata_transient',$out,60);	
		}
		
		return $out;
	}
}

add_action( 'widgets_init', function(){
     register_widget( 'kjb_Show_Stocks' );
});
?>