<?php 
ini_set('memory_limit', '100M');

class importProducts{
	
	public function importProducts(){
		
		$deleteProducts = array();
		
		global $wpdb;

		if(isset($_GET['product'])){
			$program = $this->selectProgram($_GET['product']);
			
		}else{
			$program = $this->selectProgram($_POST['program']);
		}
		
		$explode = explode("normal", $program->productfeed);
		$xmlurl = $explode[0].'delete'.$explode[1].'&start_date='.$program->productfeed_date;
		
		$xml = $this->xml($xmlurl);	
		
		foreach($xml->item as $value) {
			$wpdb->query("DELETE FROM productfeed WHERE product_id = '".$value->daisycon_unique_id."'");
		}
		
		$wpdb->update('programs', array('productfeed_date' => date('Y').'-'.date('m').'-'.date('d')), array('program_id' => $program->program_id));	
		
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