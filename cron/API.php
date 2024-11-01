<?php 

class programs{
	
	var $statics;
	var $xml;
	var $category = array();
	var $products = array();
	var $check = array('delete' => 0, 'category' => 0);
	var $erros = 0;
	
	public function programs(){
		
		global $wpdb;
		
		$errors = $this->foldHandler();
		
		if ($errors == 0){
			$daisycon = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");
			if($daisycon->api == 1){
				$statics = $this->statics($daisycon);
			}
			$xml = $this->xml($daisycon->feed);
			$xml_products = $this->xml($daisycon->programsproductfeed);
			$count_products = $this->products();
			$products = $results = $wpdb->get_results("SELECT program_id FROM productfeed");
			
			if (count($xml) != $count_products){
				$check['delete'] = 1;
			}
			
			foreach($xml->record as $key => $value) {
				$ecpc = 0;
				
				if($daisycon->api == 1){
					for ($i = 0; $i < $statics['responseInfo']->totalResults; $i++){
						if($value->program_id == $statics['return'][$i]->program_id){
							
							// Calculate ECPC
							$ecpc = $this->ecpc($statics['return'][$i]->transaction_open_amount,
												$statics['return'][$i]->transaction_approved_amount,
												$statics['return'][$i]->click_unique,
												$value);
						}
					}
				}
	
				$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM programs WHERE program_id = ".$value->program_id.""));
				
				// If program does not exist, insert it
				if(count($result) != 1){
					$this->insertProduct($value, date('Y-m-d'), $ecpc, $value->url);
				}else{
					
				}
				
				// If ECPC is different, update it in database
				if($daisycon->api == 1 && $result->ecpc != $ecpc){
					$wpdb->update('programs', array('ecpc' => $ecpc), array('program_id' => $value->program_id));
				}

				// Create unique category array
				$category = $this->arrayCategory($category, $check, $value);
				
				// Create array for products to be removed
				$this->arrayProducts($products, $value, $count_products, $xml);
				
				// Add productfeeds to programs
				$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM programs WHERE program_id = ".$value->program_id."" ));
				
				if($result->productfeed == ""){
					$productfeed = self::getProductfeed($xml_products, $result->program_id);
					
					$wpdb->update('programs',array('productfeed' => $productfeed['productfeed'], 'product_count' => $productfeed['product_count']) ,array('program_id' => $value->program_id));
				}else{
					$cProductfeed = self::getProductfeed($xml_products, $result->program_id);
					
					if($cProductfeed == ""){
						$wpdb->update('programs',array('productfeed' => "") ,array('program_id' => $value->program_id));
					}
					elseif($cProductfeed != $result->productfeed){
						$wpdb->update('programs',array('productfeed' => $cProductfeed['productfeed']) ,array('program_id' => $value->program_id));
					}		
				}
			}
			
			// If category does not exist, insert it
			$this->insertCategory($category);
	
			// If product does not exist, delete it
			$this->deleteProducts($count_products, $xml, $products);
		
				echo 	'
					<div id="message" class="Msuccess">
						<p>
							<b>'.__('Programma&acute;s zijn opgehaald!','DaisyconPlugin').'</b>
						</p>
					</div>
					';
			
		}
	}
	
	public function foldHandler(){
		
		global $wpdb;
		
		$cFeeds = $wpdb->get_row("SELECT * FROM publisher");
		
		if(count($cFeeds) < 1){
			$errors = 1;
		   	echo 	'
		    		<div id="message" class="updated">
						<p>
							'.__('Vul eerst je gegevens in!','DaisyconPlugin').'
						</p>
					</div>
		    		';
		}
			
		$url = "http://api.daisycon.com/publisher/soap/statistics/wsdl/";
		
		$handle = curl_init($url);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = curl_exec($handle);
		
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			$errors = 1;
			echo 	'
		    		<div id="message" class="updated">
						<p>
							'.__('URL haalt hij niet (404)','DaisyconPlugin').'
						</p>
					</div>
		    		';
		}
		else if($httpCode == 500) {
			$errors = 1;
			echo 	'
		    		<div id="message" class="updated">
						<p>
							'.__('URL haalt hij niet (500)','DaisyconPlugin').'
						</p>
					</div>
		    		';
		}else{
			$errors= 0;
		}
		
		curl_close($handle);
		
		return($errors);
	}
	
	public function statics($daisycon){
		$username = $daisycon->username;
		$password = $daisycon->password;

		$wsdl = "http://api.daisycon.com/publisher/soap/statistics/wsdl/";
		$account = array(
    						'login'     => $username,
    						'password'  => $password,
    						'trace'     => 1
		);
		
		$filter = array(
	   						'selection_start' => date('Y-m-d', strtotime('-2 months')),
							'selection_end' => date('Y-m-d')
		);
 
		$soapClient = new SoapClient($wsdl, $account);
		$program_soap = $soapClient->getProgramToplist($filter);
		
		return($program_soap);
		
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
	
	public function products(){
		
		global $wpdb;
		
		$products = $wpdb->get_results("SELECT * FROM programs");
		$count_products = count($products);
		
		return($count_products);
	}
	
	public function ecpc($trans_open, $trans_app, $unique, $value){
		
		global $wpdb;
		$income = $trans_open + $trans_app;
		if ($unique == 0){
			$ecpc = 0;
		}else{
			$ecpc = $income / $unique;
		}
		return($ecpc);
	}
	
	public function insertCategory($category){
		
		global $wpdb;
		
		for($k=0; $k<count($category); $k++){
			$result_cat = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM categories WHERE name = '".addslashes($category[$k])."'" ));
			if(count($result_cat) != 1){
				$wpdb->insert('categories',array(	'name' => $category[$k],
													'rename' => $category[$k],
													'visible' => '1')
				);
			}
		}
	}
	
	public function deleteProducts($count_products, $xml, $products){
		global $wpdb;
		if($count_products > count($xml)){
			foreach ($products as $delete){
				if($products->daisycon_program_id !=""){
					$wpdb->query("DELETE FROM programs WHERE program_id = '".$delete->program_id."'");
				}
			}
		}
	}
	
	public function insertProduct($value, $date, $ecpc, $link){
		
		global $wpdb;
		

		preg_replace('/&/', '&amp;', $link);
		$des = '<a href="'.$link.'dai_wp" target="_blank" rel="nofollow">Naar '.$value->program_name.'</a>';
		$more = '<a href="'.$link.'dai_wp" target="_blank" rel="nofollow">Naar '.$value->program_name.'</a>';
		$wpdb->insert('programs',array(	'program_id' => $value->program_id,
										'name' => $value->program_name,
										'description' => $des,
										'category' => $value->category,
										'language' => $value->target_countries,
										'url' => $link. 'dai_wp',
										'more' => $more,
										'image' => $value->program_logo,
										'date' => $date,
										'ecpc' => $ecpc,
										'visible' => '1')
		);
	}
	
	public function arrayCategory($category, $check, $value){
		for($j=0; $j<count($category); $j++){
			$check['category'] = 0;
			
			if($category[$j] == (string)$value->category){
				$check['category'] = 1;
			}
		}
			
		if($check['category'] == 0){
			$category[] = (string)$value->category;
		}
		
		return($category);
	}
	
	public function arrayProducts($products, $value, $count_products, $xml){
		if($count_products > count($xml)){
			for($l=0; $l<$count_products; $l++){
				if($products[$l]->program_id == $value->program_id){
					unset($products[$l]->program_id);
				}
			}
		}
		return($products);
	}
	
	public function getProductfeed($xml, $programname){
		
		$productfeed = "";
		
		foreach($xml AS $program){
			if($program->program_id == $programname){
				$replace = str_replace("&amp;", "&", $program->feed_link_xml);
				$productfeed['productfeed'] = $replace;
				$productfeed['product_count'] = $program->product_count;
			}
		}
		
		return($productfeed);
		
	}

}

$ob = new programs;