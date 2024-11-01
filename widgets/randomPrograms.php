<?php 
/* Daisycon affiliate marketing plugin
 * File: randomPrograms.php
 * 
 * Random programs widget, to view a list of random programs
 * 
 */

class RandomVifeWidget extends WP_Widget
{
  function RandomVifeWidget()
  {
    $widget_ops = array('classname' => 'RandomVifeWidget', 'description' => __('Deze widget toont willekeurige programma&rsquo;s. Iedere keer als de pagina wordt vernieuwd worden andere programma&rsquo;s getoond. Je kan zelf bepalen hoeveel programma&rsquo;s je toont.','DaisyconPlugin')  );
    $this->WP_Widget('RadomVifeWidget', __('Daisycon random','DaisyconPlugin'), $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'aantal' => '', 'image' => '', 'image_size' => '', 'image_float' => '', 'text' => '', 'chars' => '10' ) );
    $title = $instance['title'];
?>

  <p><label for="<?php echo $this->get_field_id('title'); ?>">
  <?php _e('Titel','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('aantal'); ?>">
  <?php _e('Aantal programma&rsquo;s','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('aantal'); ?>" name="<?php echo $this->get_field_name('aantal'); ?>" type="text" value="<?php echo esc_attr($instance['aantal']); ?>" /></label></p>
  
  <strong><?php _e('Titel','DaisyconPlugin') ?></strong>
  <p><label for="<?php echo $this->get_field_id('chars'); ?>">
  <?php _e('Aantal characters','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo esc_attr($instance['chars']); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('text'); ?>">
  <?php _e('Zichtbaar','DaisyconPlugin') ?>: <input id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['text'] ); ?> /></label></p>
 
  <strong><?php _e('Plaatje','DaisyconPlugin') ?></strong>
  <p><label for="<?php echo $this->get_field_id('image_size'); ?>">
  <?php _e('Grootte','DaisyconPlugin') ?>: <input class="widefat" id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>" type="text" value="<?php echo esc_attr($instance['image_size']); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('image'); ?>">
  <?php _e('Zichtbaar','DaisyconPlugin') ?>: <input id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['image'] ); ?> /></label></p>
  <p><label for="<?php echo $this->get_field_id('image_float'); ?>"><input id="<?php echo $this->get_field_id('image_float'); ?>" name="<?php echo $this->get_field_name('image_float'); ?>" type="radio" value="left" <?php if($instance['image_float'] == 'left'){ echo ' checked';} ?> /> 
  <?php _e('Links','DaisyconPlugin') ?>
  <input id="<?php echo $this->get_field_id('image_float'); ?>" name="<?php echo $this->get_field_name('image_float'); ?>" type="radio" value="right" <?php if($instance['image_float'] == 'right'){ echo ' checked';} ?> /> 
  <?php _e('Rechts','DaisyconPlugin') ?></label></p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['aantal'] = $new_instance['aantal'];
    $instance['text'] = $new_instance['text'];
    $instance['image'] = $new_instance['image'];
    $instance['chars'] = $new_instance['chars'];
    $instance['image_size'] = $new_instance['image_size'];
    $instance['image_float'] = $new_instance['image_float'];
    return $instance;
  }
 
  public function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 	global $post,$wpdb;
 	
 	$publisher = $wpdb->get_row("SELECT * FROM publisher");
	$wi = explode('/media/', $publisher->feed);
	$wi = explode('/', $wi[1]);
	echo '	<script type="text/javascript">
				function insertStats3(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=widget_randomprograms&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
 	
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 	
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
	$results = $wpdb->get_results("SELECT 	programs.name,
											programs.image,
											programs.url FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.visible = '1' AND categories.visible = '1' ORDER BY RAND() LIMIT ".$instance['aantal']."");

	echo '<ul>';
	$number = 0;
	foreach($results as $array){
		$number++;
		echo	'
			<div class="WidgetTop">
				<div class="Number"'; if (isset($instance['image_size'])){echo'style="line-height:'.$instance['image_size'].'px;"';}echo'>
					'.$number.'
				</div>
				';
		if ($instance['image'] == 1){
			echo	'
					<a href="'.$array->url.'" target="_blank" rel="nofollow" onclick="insertStats3()">
						<img src="'.$array->image.'" class="Img" '; if (isset($instance['image_size'])){echo 'style ="width:'.$instance['image_size'].'px; height:'.$instance['image_size'].'px; float:'.$instance['image_float'].';"';} echo' />
					</a>
					';
		}
		if ($instance['text'] == 1){
		echo	'
				<div class="Text"'; if (isset($instance['image_size'])){echo'style="line-height:'.$instance['image_size'].'px;"';}echo'>
					<a href="'.$array->url.'" target="_blank" rel="nofollow" onclick="insertStats3()"> 
						'.substr($array->name, 0, $instance['chars']).'
					</a>
				</div>
				';
		}
		echo	'
		
			</div>
			<div style="clear:both;"></div>
			<div class="underline"></div>
				';	
	}
    echo '</ul>';

  }
 
}

?>