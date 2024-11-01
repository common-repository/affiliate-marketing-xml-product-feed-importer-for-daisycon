<?php
ini_set('memory_limit', '100M');
?>
<?php 
class importProducts{
	
	public function importProducts(){
		global $wpdb;
		$program = $this->selectProgram($_GET['product']);
		
		$xml = $this->xml($program->productfeed);
		
		$i = 0;
		
		$wpdb->update('productfeed', array('productfeed_date' => date('Y').'-'.date('m').'-'.date('d')), array('program_id' => $program->program_id));	
		
		foreach($xml->item as $value) {
			$product = $wpdb->get_row("SELECT * FROM products WHERE product_id = '".$value[0]->daisycon_unique_id."'");
			if(count($product) == 0){
				
				if (isset($value[0]->city_of_destination)){
					$cat = $value[0]->city_of_destination;
				}
				elseif(isset($value[0]->brand)){
					$cat = $value[0]->brand;
				}
				else{
					$cat = '';
				}
				
			 $wpdb->insert('productfeed', array('product_id' => $value[0]->daisycon_unique_id, 
			 								'program_id' => $program->program_id, 
											'title' => $value[0]->title,
											'image' => $value[0]->img_medium,
											'price' => $value[0]->minimum_price,
											'link' => $value[0]->link,
											'category' => $value[0]->category,
											'sub_category' => $cat)); 
			}else{
				if ($product->price != $value[0]->minimum_price){
					$wpdb->update('productfeed', array('price' => $value[0]->minimum_price), array('product_id' => $value[0]->daisycon_unique_id));
				}
				if ($product->image != $value[0]->img_medium){
					$wpdb->update('productfeed', array('image' => $value[0]->img_medium), array('product_id' => $value[0]->daisycon_unique_id));
				}
			}
			$i++;
		}
	}

	public function selectProgram($program_id){
		global $wpdb;
		
		$program = $wpdb->get_row("SELECT * FROM programs WHERE programs.program_id = '".$program_id."'");
		
		return($program);
	}
	
	public function xml($url){
		
		if( ini_get('allow_url_fopen') ) {
			
		   	$content = file_get_contents($url);
			$xml = simplexml_load_string($content);
			
		}else{
			
			$page_data = wp_remote_get($url);
			$body = wp_remote_retrieve_body($page_data);
			$xml = new SimpleXMLElement($body);
			
		}

		return($xml);
	}
	
}

$importproducts = new importProducts;
?>