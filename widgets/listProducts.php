<?php 
/* Daisycon affiliate marketing plugin
 * File: listProducts.php
 * 
 * Products widget, to view a list of products based on a keyword
 * 
 */

class Products extends WP_Widget
{
  function Products()
  {
    $widget_ops = array('classname' => 'Products', 'description' => __('Deze widget toont producten aan de hand van de opgegeven zoekterm. Je kan zelf bepalen hoeveel producten je toont.','DaisyconPlugin') );
    $this->WP_Widget('Products', __('Daisycon producten','DaisyconPlugin'), $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'product' => '', 'aantal' => '5', 'chars' => '10', 'size' => '50') );
    $title = $instance['title'];
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>">
	<?php _e('Titel','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" /></label></p>
	
	<strong><?php _e('Product','DaisyconPlugin') ?></strong>
	<p><label for="<?php echo $this->get_field_id('product'); ?>">
	<?php _e('Product','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('product'); ?>" name="<?php echo $this->get_field_name('product'); ?>" type="text" value="<?php echo esc_attr($instance['product']); ?>" /></label></p>
    <p><label for="<?php echo $this->get_field_id('aantal'); ?>">
    <?php _e('Aantal','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('aantal'); ?>" name="<?php echo $this->get_field_name('aantal'); ?>" type="text" value="<?php echo esc_attr($instance['aantal']); ?>" /></label></p>
	
	<strong><?php _e('Product afmetingen in pixels','DaisyconPlugin') ?></strong>
	<p><label for="<?php echo $this->get_field_id('size'); ?>">
	<?php _e('Grootte','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo esc_attr($instance['size']); ?>" /></label></p>
	
	<strong><?php _e('Productnaam','DaisyconPlugin') ?></strong>
	<p><label for="<?php echo $this->get_field_id('chars'); ?>">
	<?php _e('Aantal characters','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo esc_attr($instance['chars']); ?>" /></label></p>
  	<p><label for="<?php echo $this->get_field_id('text'); ?>">
  	<?php _e('Zichtbaar','DaisyconPlugin') ?>: <input id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['text'] ); ?> /></label></p>
 
 	<strong><?php _e('Prijs','DaisyconPlugin') ?></strong>
  	<p><label for="<?php echo $this->get_field_id('price'); ?>">
  	<?php _e('Zichtbaar','DaisyconPlugin') ?>: <input id="<?php echo $this->get_field_id('price'); ?>" name="<?php echo $this->get_field_name('price'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['price'] ); ?> /></label></p>

<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['product'] = $new_instance['product'];
    $instance['aantal'] = $new_instance['aantal'];
    $instance['size'] = $new_instance['size'];
	$instance['title'] = $new_instance['title'];
    $instance['chars'] = $new_instance['chars'];
    $instance['text'] = $new_instance['text'];
    $instance['price'] = $new_instance['price'];
    return $instance;
  }
 
  public function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 	global $post,$wpdb;
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 	
    if (!empty($title))
      echo $before_title . $title . $after_title;;
      
      $explode = explode(" ", $instance['product']);
      
	for($i=0; $i<count($explode); $i++){
      	$breed = explode('*', $explode[$i]);
	      if($i != 0){
	      	if(count($breed) > 1){
	      		$tDatabase .= " AND (title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' OR description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
	      	}else{
	      		$tDatabase .= " AND (title LIKE '%".$explode[$i]."%' OR description LIKE '%".$explode[$i]."%')";
	      	}
	      }else{
	      	if(count($breed) > 1){
	     		$tDatabase = "(title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' OR description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
	      	}else{
	      		$tDatabase = "(title LIKE '%".$explode[$i]."%' OR description LIKE '%".$explode[$i]."%')";
	      }
      } 
	}

	$products = $wpdb->get_results("SELECT * FROM productfeed WHERE ".$tDatabase." AND image != '' ORDER BY RAND() LIMIT ".$instance['aantal']." ");
	    
	$publisher = $wpdb->get_row("SELECT * FROM publisher");
	$wi = explode('/media/', $publisher->feed);
	$wi = explode('/', $wi[1]);
	echo '	<script type="text/javascript">					
				function insertStats1(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=widget_producten&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
	
	if(count($products) > 0){
		foreach ($products AS $product){
			echo'<div class="widgetproduct"><div class="widgetproductimage" style="width:'.$instance['size'].'px">';
	
			
			echo '<a href="'.$product->link.'" target="_blank" rel="nofollow" onclick="insertStats1()">';
			
			echo'
					<img src="'.$product->image.'" style="height:'.$instance['size'].'px" />
				';
			
			echo '</a></div>';
			
			
			if($instance['text'] == 1){
			echo	'
					
					<div class="widgetproducttitle" style="height:'.$instance['size'].'px; line-height:'.$instance['size'].'px">
						'.substr($product->title, 0, $instance['chars']).'
					</div>
				';
			}
			
			if($instance['price'] == 1){
			echo	'
					<div class="widgetproductprice" style="height:'.$instance['size'].'px; line-height:'.$instance['size'].'px">
						&euro; '.$product->price.'
					</div>
					';
			}
			echo	'
					<div style="clear:both;"></div>
				</div>
				';
		}
	  }
  }
 
}

?>