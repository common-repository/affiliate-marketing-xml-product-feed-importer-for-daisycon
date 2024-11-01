<?php 
class importActiecodes{
	
	public function importActiecodes(){
		global $wpdb;
		$daisycon = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");
		$xml = $this->xml($daisycon->actiecodefeed);
		
		$i = 0;
		foreach($xml->record as $value) {
			$program = $this->selectActiecode($value[0]->program_name);
			
			if($program){
			
				$actiecode = $wpdb->get_row("SELECT * FROM actioncodes WHERE actioncode_title = '".$value[0]->promotioncode_name."'");
				if(count($actiecode) == 0){
					
					$wpdb->query("INSERT INTO actioncodes (	`program_id`,
															`actioncode_title`,
															`actioncode_description`,
															`actioncode`,
															`date_start`,
															`date_end`,
															`actioncode_link`,
															`actioncode_lan` ) VALUES (	'".$program->program_id."',
																						'".$value[0]->promotioncode_name."',
																						'".addslashes($value[0]->description)."',
																						'".$value[0]->promotioncode."',
																						'".self::setDate($value[0]->start_date)."',
																						'".self::setDate($value[0]->end_date)."',
																						'".$value[0]->link."',
																						'".$value[0]->locale."')");
				}
				$i++;
			}
		}
		
		if($daisycon->actiecodefeed == NULL){
			echo '
					<div id="message" class="Merror">
						<p>
							<b>'.__('Vul eerst de Actiecode Feed in.','DaisyconPlugin').'</b>
						</p>
					</div>
			';
		}else{
				echo 	'
					<div id="message" class="Msuccess">
						<p>
							<b>'.__('Actiecodes zijn opgehaald!','DaisyconPlugin').'</b>
						</p>
					</div>
					';
		}
	}
	
	public function setDate($date){
		$explode = explode ('-', $date);
		
		$date = $explode[0].'-'.$explode[1].'-'.substr($explode[2], 0, -9);
		
		return($date);	
	}

	public function selectActiecode($name){
		global $wpdb;
		
		$program = $wpdb->get_row("SELECT * FROM programs WHERE programs.name = '".addslashes($name)."'");
		
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

$importActiecodes = new importActiecodes;
?>