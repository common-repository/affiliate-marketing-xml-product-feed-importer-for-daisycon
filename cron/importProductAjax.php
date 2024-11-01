<?php
include('../../../../wp-config.php');
ini_set('memory_limit', '100M');
?>
<?php 
class importProducts{
	
	public function importProducts(){
		
		$deleteProducts = array();
		
		global $wpdb;

		if(isset($_GET['product'])){
			$program = $this->selectProgram($_GET['product']);
			
		}else{
			$program = $this->selectProgram($_POST['program']);
		}
		
		if(isset($_POST['start']) && isset($_POST['end'])){
			$xml = $this->xml($program->productfeed.'&start='.$_POST['start'].'&limiet='.$_POST['end']);
		}	
		
		$i = 0;
				
		$ids = "";
		foreach($xml->item as $value) {

			$product = $wpdb->get_row("SELECT * FROM productfeed WHERE product_id = '".$value->daisycon_unique_id."'");
			
			if(count($product) == 0){
				
				if (isset($value->city_of_destination)){
					$cat = $value->city_of_destination;
				}
				elseif(isset($value->brand)){
					$cat = $value->brand;
				}
				else{
					$cat = '';
				}

				$add = $wpdb->get_row("SELECT * FROM publisher");	
	
				$subid = $add->subid;
				
				if(isset($value->img_medium)){
					$imageProduct = $value->img_medium;
				}elseif(isset($value->img_small)){
					$imageProduct = $value->img_small;
				}else{
					$imageProduct = $value->img_large;
				}

				$wpdb->query("INSERT INTO productfeed (product_id, title, description, program_id, image, price, link, category, sub_category) VALUES ('".$value->daisycon_unique_id."', '".$value->title."', '".$value->description."', '".$program->program_id."', '".$imageProduct."', '".$value->minimum_price."', '".$value->link."dai_wp_', '".$value->category."', '".$cat."')");

				$wpdb->query("UPDATE programs SET productfeed_date = '".date("Y-m-d h:m:s")."' WHERE program_id = '".$program->program_id."'");
			}else{

				if ($product->price != $value->minimum_price){
					$wpdb->update('products', array('price' => $value->minimum_price), array('product_id' => $value->daisycon_unique_id));
					$wpdb->query("UPDATE programs SET productfeed_date = '".date("Y-m-d h:m:s")."' WHERE program_id = '".$program->program_id."'");
				}
				if ($product->image != $value->img_medium){
					$wpdb->update('products', array('image' => $value->img_medium), array('product_id' => $value->daisycon_unique_id));
					$wpdb->query("UPDATE programs SET productfeed_date = '".date("Y-m-d h:m:s")."' WHERE program_id = '".$program->program_id."'");
				}
			}
			
			if((count($xml->item) - 1) > $i){
				$ids .= ''.$value->daisycon_unique_id.', ';
			}else{
				$ids .= ''.$value->daisycon_unique_id;
			}				
			
			$i++;
		}
		
		echo $ids;		
	}
	
	public function allProduct($program_id){
		global $wpdb;
		
		$products = $wpdb->get_results("SELECT product_id FROM productfeed WHERE productfeed.program_id = '".$program_id."'");
		
		if($products){
			foreach($products AS $product){
				$aProducts[] = $product->product_id;
			}
			
			return($aProducts);
		}
	}

	public function selectProgram($program_id){
		global $wpdb;
		
		$program = $wpdb->get_row("SELECT * FROM programs WHERE programs.program_id = '".$program_id."'");
		
		
		return($program);
	}
	
	public function xml($url){
		
		if(function_exists('curl_init')){	 
			
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    //curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");
		    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    //curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		    $xml = curl_exec($ch);
		    $xml = simplexml_load_string($xml);
		    curl_close($ch);	

		}elseif( ini_get('allow_url_fopen') ) {
			
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