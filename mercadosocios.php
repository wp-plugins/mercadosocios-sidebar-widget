<?php
/*
Plugin Name: MercadoSocios widget
Plugin URI: 
Description: Displays a gallery of items related to specific keyword/category of MercadoLibre related to your Mercadosocios account
Author: Comersus, Rodrigo Alhadeff
Version: 1.0
Author URI: http://
License: GPL2    

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
    
*/

class Mercado_Socios_Widget extends WP_Widget {
	
	function Mercado_Socios_Widget() {
		$widget_ops = array('classname' => 'widget_mercadosocios', 'description' => __('Get MercadoSocios Gallery'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('mercadosocios', __('MercadoSocios'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		
		$keyword=$instance['keyword'];
		$keyword=urlencode($keyword);
		$afiliado=$instance['afiliado'];
		$categoria=$instance['categoria'];
		$categoria=urlencode($categoria);
		$pais=$instance['pais'];
		$pais=urlencode($pais);
		
		
		$instance['text']= "<? readfile(\"http://www.comersus.com/wordpress/servers/mercadosocios/?keyWords=".$keyword."&cat=".$categoria."&pais=".$pais."&afiliado=".$afiliado."\"); ?>";
		$text = apply_filters( 'widget_mercadosocios', $instance['text'], $instance );
		echo $before_widget;
		//echo $keyword;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
			ob_start();
			eval('?>'.$text);
			$text = ob_get_contents();			
			ob_end_clean();
			?>			
			<div class="mercadosocioswidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;		
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['afiliado'] = strip_tags($new_instance['afiliado']);
		$instance['categoria'] = strip_tags($new_instance['categoria']);
		$instance['keyword'] = strip_tags($new_instance['keyword']);								
		$instance['pais'] = strip_tags($new_instance['pais']);								
			
		$keyword=$instance['keyword'];
		$keyword=urlencode($keyword);
		$afiliado=$instance['afiliado'];
		$categoria=$instance['categoria'];
		$categoria=urlencode($categoria);
		$pais=$instance['pais'];
		$pais=urlencode($pais);
						
		$instance['text']= "<? readfile(\"http://www.comersus.com/wordpress/servers/mercadosocios?keyWords=".$keyword."&cat=".$categoria."&pais=".$pais."&afiliado=".$afiliado."\"); ?>";
		$new_instance['text'] = "";
		
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];			
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( $new_instance['text'] ) );
			
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$afiliado=strip_tags($instance['afiliado']);
		$categoria=strip_tags($instance['categoria']);
		$keyword=strip_tags($instance['keyword']);
		$pais=strip_tags($instance['pais']);
				
		$text= "";
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>	
		
		<p><label for="<?php echo $this->get_field_id('afiliado'); ?>"><?php _e('Afiliado:'); ?></label>
		<input id="<?php echo $this->get_field_id('afiliado'); ?>" name="<?php echo $this->get_field_name('afiliado'); ?>" type="text" value="<?php echo esc_attr($afiliado); ?>" size=20 /></p>
		
		<p><label for="<?php echo $this->get_field_id('categoria'); ?>"><?php _e('ID Categoria:'); ?></label>
		<input id="<?php echo $this->get_field_id('categoria'); ?>" name="<?php echo $this->get_field_name('categoria'); ?>" type="text" value="<?php echo esc_attr($categoria); ?>" size=20 /></p>
		
		<p><label for="<?php echo $this->get_field_id('keyword'); ?>"><?php _e('Palabras filtro:'); ?></label>
		<input id="<?php echo $this->get_field_id('keyword'); ?>" name="<?php echo $this->get_field_name('keyword'); ?>" type="text" value="<?php echo esc_attr($keyword); ?>" size=20 /></p>
		
		<p><label for="<?php echo $this->get_field_id('pais'); ?>"><?php _e('Pais (MLA para Argentina):'); ?></label>
		<input id="<?php echo $this->get_field_id('pais'); ?>" name="<?php echo $this->get_field_name('pais'); ?>" type="text" value="<?php echo esc_attr($pais); ?>" size=20 /></p>
		
		<p><input type=hidden id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Detalle de opciones en readme.txt.'); ?></label></p>
		
		
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Mercado_Socios_Widget");'));

// donate link on manage plugin page
add_filter('plugin_row_meta', 'mercadosocios_donate_link', 10, 2);
function mercadosocios_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40comersus%2ecom">Donate</a>';
		$links[] = $donate_link;
	}
	return $links;
}