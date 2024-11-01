<?php 
/* Daisycon affiliate marketing plugin
 * File: general.php
 * 
 * View for the shorttags to be displayed on the website
 * 
 */

class general{

	public function newPrograms($array){
		
		global $post,$wpdb;
		
		if($array['amount'] < 1){
			$limit = '';
		}else{
			$limit = 'LIMIT '.$array['amount'];
		}
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats5(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=program_new&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$results = $wpdb->get_results("SELECT 	programs.name,
												programs.more,
												programs.image,
												programs.url,
												categories.rename FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.visible = '1' AND categories.visible = '1' ORDER BY programs.date DESC ".$limit."");
		
		foreach($results as $aPrograms){
		
			$subid = $wpdb->get_row("SELECT subid FROM publisher");
			if($subid != NULL){
			$links = $aPrograms->url . $subid->subid;
			}	
			
			
			$result .= '<div class="Rows">
							<div class="img">
								<a href="'.$links.'" rel="nofollow" target="_blank" onclick="insertStats5()">
									<img src="'.$aPrograms->image.'" alt="'.$aPrograms->name.'" title="'.$aPrograms->name.'" />
								</a>
							</div>
							<div class="text">
								<span class="title">'.$aPrograms->name.'</span><br />
								'.$aPrograms->more.'
							</div>
						</div>
						<div style="clear:both;"></div>
						<div class="underline"></div>';
		}
		$result .= '<div style="clear:both;"></div>';
		return($result);
	}

	//Take top 3 from programs (ecpc)
	public function topPrograms($array){
		
		global $post,$wpdb;
		
		if($array['amount'] < 1){
			$limit = '';
		}else{
			$limit = 'LIMIT '.$array['amount'];
		}
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats6(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=program_top&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$results = $wpdb->get_results("SELECT 	programs.name,
												programs.more,
												programs.image,
												programs.url,
												categories.rename FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.visible = '1' AND categories.visible = '1' ORDER BY programs.ecpc DESC ".$limit."");
		
		foreach($results as $aPrograms){
			
			$subid = $wpdb->get_row("SELECT subid FROM publisher");
			if($subid != NULL){
			$links = $aPrograms->url . $subid->subid;
			}	
			
			$result .= '<div class="Program">
							<div class="img">
								<a href="'.$links.'" rel="nofollow" target="_blank" onclick="insertStats6()">
									<img src="'.$aPrograms->image.'" alt="'.$aPrograms->name.'" title="'.$aPrograms->name.'" />
								</a>
							</div>
							<div class="text">
								<span class="title">'.$aPrograms->name.'</span><br />
								'.$aPrograms->more.'
							</div>
							<div style="clear:both;"></div>
						</div>
						';
		}
		return($result);
	}
	
	//Show all programmas in that category
	public function category($array){
		
		global $post,$wpdb;
		
		$rename = $wpdb->get_row("SELECT categories.rename FROM categories WHERE categories.category_id = '".$array['id']."'");
		
		if($array['amount'] == ""){
			$amount = $wpdb->get_results("SELECT * FROM programs INNER JOIN categories ON programs.category = categories.name WHERE categories.visible = '1' AND programs.visible = '1' AND categories.rename = '".$rename->rename."' ORDER BY programs.ecpc DESC");
			$amount = count($amount);
		}else{
			$amount = $array['amount'];
		}
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats7(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=category&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		if ($array['id'] != ""){
			$rPrograms = $wpdb->get_results("SELECT programs.name,
												programs.image,
												programs.description,
												programs.url,
												programs.more,
												programs.more FROM programs INNER JOIN categories ON programs.category = categories.name WHERE categories.visible = '1' AND programs.visible = '1' AND categories.rename='".$rename->rename."' ORDER BY programs.ECPC DESC LIMIT ".$amount."");
		}
		
		$result =	'';
		
		if($rPrograms){
			foreach($rPrograms as $aPrograms){
				
			$subid = $wpdb->get_row("SELECT subid FROM publisher");
			if($subid != NULL){
			$links = $aPrograms->url . $subid->subid;
			}				
				
				if (strlen($aPrograms->more) > 0){$link = $aPrograms->more;}else{$link = $aPrograms->url;}
				$result .=	'<div class="Program">
								<div class="img">
									<a href="'.$links.'" rel="nofollow" onclick="insertStats7()">
										<img src="'.$aPrograms->image.'" alt="'.$aPrograms->name.'" title="'.$aPrograms->name.'" width="150" height="150"/>
									</a>
								</div>
								<div class="text">
									<span class="title">'.$aPrograms->name.'</span><br /><br />
									'.$aPrograms->description.'
								</div>
								<div style="clear:both;"></div>
							</div>
							';
			}
		}
		$result .= '<div style="clear:both;"></div>';
		return($result);
	}
	
	//los program aanroepen
	public function program($array){
		global $wpdb;
		
		$program = $wpdb->get_row("SELECT * FROM programs WHERE program_id = '".$array['id']."' AND programs.visible = '1'");
		
		if(count($program) > 0){
			
		$subid = $wpdb->get_row("SELECT subid FROM publisher");
		if($subid != NULL){
		$links = $program->url . $subid->subid;
		}	
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats8(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=program&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
			$result =	'<div class="Program">
						<div class="img">
							<a href="'.$links.'" target="_blank" rel="nofollow" onclick="insertStats8()">
								<img src="'.$program->image.'" alt="'.$program->name.'" />
							</a>
						</div>
						<div class="text">
							<span class="title">'.$program->name.'</span><br />
							'.$program->description.'
						</div>
						<div style="clear:both;"></div>
					</div>
					';
		}else{
			$result = 	'
						
						';
		}
		return($result);
	}
	
	//los program aanroepen
	public function moreProgram($array){
		global $wpdb;
		
		$aProgram = $wpdb->get_row("SELECT 	programs.url,
											programs.image,
											programs.name,
											programs.more FROM programs WHERE programs.program_id = '".$array['id']."'  AND programs.visible = '1'");
		
		if(count($aProgram) > 0){
			
		$subid = $wpdb->get_row("SELECT subid FROM publisher");
		if($subid != NULL){
		$links = $aProgram->url . $subid->subid;
		}
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats9(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=program_more&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
			$result =	'<div class="Program">
							<div class="img">
								<a href="'.$links.'" rel="nofollow" target="_blank" onclick="insertStats9()">
									<img src="'.$aProgram->image.'" alt="'.$aProgram->name.'" title="'.$aProgram->name.'" />
								</a>
							</div>
							<div class="text">
								<span class="title">'.$aProgram->name.'</span><br />
								'.$aProgram->more.'
							</div>
							<div style="clear:both;"></div>
						</div>
						';
		}else{
			$result = 	'
						
						';
		}
	
		return($result);
	}
	
	public function products($array){
		global $wpdb;

		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		
		if($array['view'] == "auto"){
				
		echo '	<script type="text/javascript">					
			function insertStats10(){
				jQuery.ajax
				({
					url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=products_auto&jsoncallback=?",
					dataType: "jsonp",
					cache: false,
					success: function(html)
					{
					} 
				});
			}
		</script>';

			
			//Automatisch
			$result ='
				<script type="text/javascript">
				jQuery(document).ready(function(){
					var cclick = 0;
					var dataString = "";
					
					jQuery("#dcmore").click(function(){
					
						cclick++;
							
						dataString = "&click="+ cclick;
		
						if(jQuery("#dcmore").attr("data-order")){
							dataString += "&order="+ jQuery("#dcmore").attr("data-order");
						}
						if(jQuery("#dcmore").attr("data-amount")){
							dataString += "&amount="+ jQuery("#dcmore").attr("data-amount");
						}
						if(jQuery("#dcmore").attr("data-search")){
							dataString += "&search="+ jQuery("#dcmore").attr("data-search");
						}
						if(jQuery("#dcmore").attr("data-stores")){
							dataString += "&stores="+ jQuery("#dcmore").attr("data-stores");
						}
						if(jQuery("#dcmore").attr("data-style")){
							dataString += "&stylesheet="+ jQuery("#dcmore").attr("data-style");
						}
						if(jQuery("#dcmore").attr("data-view")){
							dataString += "&view="+ jQuery("#dcmore").attr("data-view");
						}
						
						jQuery.ajax
						({
							type: "POST",
							url: "'.get_bloginfo('wpurl').'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=viewAutoProducts",
							data: dataString,
							cache: false,
							success: function(html)
							{
								jQuery("#dcallproducts").html(jQuery("#dcallproducts").html() + html);
							} 
						});
					});
					
					if (jQuery("#dcallproducts").scrollTop + jQuery("#dcallproducts").clientHeight == jQuery("#dcallproducts").scrollHeight ){
					 alert("test");
					 }
				});
				</script>
				';
			
			$stores = explode(',', $array['stores']);
			
			for($i=0; $i<count($stores); $i++){
				if($i == (count($stores)-1)){
					$dStores .= "productfeed.program_id = '".$stores[$i]."'";
				}else{
					$dStores .= "productfeed.program_id = '".$stores[$i]."' OR ";
				}
			}
			
			if($array['order'] == "abc"){
				$dOrder = "productfeed.title";
			}
			elseif($array['order'] == "low"){
				$dOrder = "productfeed.price ASC";
			}
			elseif($array['order'] == "high"){
				$dOrder = "productfeed.price DESC";
			}else{
				$dOrder = "RAND()";
			}
			
			$eSearch = explode(" ", $array['search']);
			
			for($i=0; $i<count($eSearch); $i++){
		      	$breed = explode('*', $eSearch[$i]);
			      if($i != 0){
			      	if(count($breed) > 1){
			      		$tDatabase .= " AND (productfeed.title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' OR productfeed.description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
			      	}else{
			      		$tDatabase .= " AND (productfeed.title LIKE '%".$eSearch[$i]."%' OR productfeed.description LIKE '%".$eSearch[$i]."%')";
			      	}
			      }else{
			      	if(count($breed) > 1){
			     		$tDatabase = "(productfeed.title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' OR productfeed.description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
			      	}else{
			      		$tDatabase = "(productfeed.title LIKE '%".$eSearch[$i]."%' OR productfeed.description LIKE '%".$eSearch[$i]."%')";
			      }
		      } 
			}
			
			
			$products = $wpdb->get_results("SELECT productfeed.product_id FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE ".$tDatabase." AND (".$dStores.") ORDER BY ".$dOrder." LIMIT ".$array['amount']."");
						
			foreach($products AS $product){
				$explode[] = $product->product_id;
			}	
		}else{
			//Handmatig
			$explode = explode(',', $array['products']);
			echo '	<script type="text/javascript">					
			function insertStats10(){
				jQuery.ajax
				({
					url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=products_manual&jsoncallback=?",
					dataType: "jsonp",
					cache: false,
					success: function(html)
					{
					} 
				});
			}
			</script>';
		}
		
		if(count($explode) > 0){
			
			$style = $wpdb->get_row("SELECT * FROM stylesheets WHERE stylesheet_id = '".$array['stylesheet']."' ");
			
			$result .='<div id="dcallproducts">';
			for($i=0; $i < count($explode); $i++){
				
				//Selected products
				$product = $wpdb->get_results("SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.name,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE productfeed.product_id = '".$explode[$i]."' ORDER BY RAND()");
								
				if(count($product) > 0){
				
					if($product->price == "0.00"){
						$price = "Gratis!";
					}else{
						$price = $product->price;
					}
					
					if($style->float == 1){
						$float = "left";
					}else{
						$float = "none";
					}
						
				
					//View 1 is voor de tegelweergave
					if($style->view == 1){
						
						$programwidth = $style->width / 4;
					
						if($style->store == 1){
						
							$programimage = '
											<div class="dcTile_programimage" style="position:absolute; right:10px; top:10px; padding:2px; background:#FFF;">
												<img height="'.$programwidth.'" src="'.$product[0]->program_image.'" style="height: '.$programwidth.'px;"/>
											</div> 
											';
						}else{
							$programimage = '';
						}
						
						if($style->button_store == 1 || $style->store_button_program != "disabled"){
							
							if($style->store_button_program == "disabled"){
								$storetext = $style->store_before;
							}
							else if($style->store_button_program == "after"){
								$storetext = $style->store_before.' '.$product[0]->name;
							}else{
								$storetext = $product[0]->name.' '.$style->store_before;
							}
							
							if($style->button_store == 1){
								$buttonstyle= 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							$store = '
											<div style="margin-top:10px; '.$buttonstyle.'">
												'.$storetext.'
											</div>
											';
						}else{
							$store = '';
						}
						
						if($style->price == 1){
							
							if($style->price_button == 1){
								$buttonstyle= 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							$pricetext = $style->price_before.' '.$product[0]->price;
							
							$price ='
									<div class="dcTile_pricetext" style="margin-top:10px; '.$buttonstyle.'">
										'.$pricetext.'
									</div>
									';
						}else{
							$price ='';
						}
		
						$result .='
							<div class="products dcTile_products" style="border:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; float:'.$float.'; height:'.$style->height.'px; width:'.$style->width.'px; text-align:'.$style->align.'; color:#'.$style->textcolor.'; cursor:pointer;" onClick="insertStats10(); window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
								<div class="dc_Tileproductimage" style="margin-top:20px; width:'.$style->width.'px; height:'.$style->size.'px; text-align:'.$style->align.'; overflow:hidden;max-width: 100%;">
									<img src="'.$product[0]->image.'" style="height: '.$style->size.'px;" height="'.$style->size.'"/>
								</div>
								<div class="dcTile_producttitle" style="margin-top:15px; width:'.$style->width.'px;">
									'.$product[0]->title.'
								</div>
								'.$programimage.'
								'.$store.'
								'.$price.'
							</div>
							';	
						
					}
									
					elseif($style->view == 0){

						$programwidth = $style->size / 2;
					
						if($style->store == 1){
						
							$programimage = '
											<div id="dcTable_programimage" style="position:relative; float:left; padding:2px; background:#FFF;">
												<img height="'.$programwidth.'" src="'.$product[0]->program_image.'"  style="height: '.$programwidth.'px;" />
											</div>
											';
						}else{
							$programimage = '';
						}
						
						if($style->price == 1){
							
							if($style->price_button == 1){
								$buttonstyle= 'color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							$pricetext = $style->price_before.'  '.$product[0]->price;
							
							$price ='
									<div id="dcTable_pricetext" style="float:left; margin-left:10px; padding:5px; padding-top:2px; padding-bottom:2px;  '.$buttonstyle.'">
										'.$pricetext.'
									</div>
									';
						}else{
							$price ='';
						}
						
						$result .='
							<div id="dcTable_products" class="dcTable_products" style="text-align:left; border-bottom:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; height:'.$style->height.'px; width:'.$style->width.'px; color:#'.$style->textcolor.'; margin-top:-5px; cursor:pointer" onClick="insertStats10(); window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
								<div id="dcTable_productimage" style="float:left; margin-right:10px; width:20%; height:'.$style->size.'px; text-align:center; overflow:hidden;">
									<img src="'.$product[0]->image.'" style="height: '.$style->size.'px;" height="'.$style->size.'"/>
								</div>
								<div id="dcTable_producttitle" style="float:left;  margin-right:10px; width:30%; text-align:'.$style->align.'">
									'.$product[0]->title.'
								</div>
								<div style="width:20%; margin-right:10px; text-align:center; float:left;">'.$programimage.'</div>
								<div style="width:20%; text-align:center; float:left;">'.$price.'</div>
							</div>	
							';
						$result .='<div style="clear: both;"></div>';
					
					}
					
					
				}else{
					$result = '';
				}
			}
			
			$result .='<div style="clear: both;"></div></div>';
			
			if($array['moreproducts'] == 1){
				
					if($style->float == 1){
						$dcmore_width = "98%";
					}else{
						$dcmore_width = $style->width.'px';
					}
				
				$result .='<div id="dcmore" data-view="'.$array['view'].'" data-order="'.$array['order'].'" data-amount="'.$array['amount'].'" data-search="'.$array['search'].'" data-stores="'.$array['stores'].'" data-style="'.$array['stylesheet'].'" style="width:'.$dcmore_width.'; background-color:#'.$style->moreproducts_color.'; color:#'.$style->moreproducts_font.';">'.$style->moreproducts_text.'</div>';
			} 
		}
	
		
		
		return($result);
	
	}
	
	public function productlist($array){
		global $wpdb;
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats11(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=products_list&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$result = '<div>';
		
		$explode = explode(" ", $array['product']);
      
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

						
			$style = $wpdb->get_row("SELECT * FROM stylesheets WHERE stylesheet_id = '".$array['stylesheet']."' ");
			
			for($i=0; $i < count($explode); $i++){
				
				$product = $wpdb->get_results("SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.name,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id ORDER BY RAND() LIMIT 5");
				/*$product = $wpdb->get_results("SELECT * FROM productfeed WHERE ".$tDatabase." AND image != '' ORDER BY RAND() LIMIT 5 ");*/
				//var_dump($product);
				if(count($product) > 0){
				
					if($products->price == "0.00"){
						$price = "Gratis!";
					}else{
						$price = $products->price;
					}
					
					if($style->float == 1){
						$float = "left";
					}else{
						$float = "none";
					}
						
					//View 1 is voor de tegelweergave
					if($style->view == 1){
						
						$programwidth = $style->width / 4;
					
						if($style->store == 1){
						
							$programimage = '
											<div style="position:absolute; right:10px; top:10px; padding:2px; background:#FFF;">
												<img height="'.$programwidth.'" src="'.$product[0]->program_image.'" />
											</div>
											';
						}else{
							$programimage = '';
						}
						
						if($style->button_store == 1 || $style->store_button_program != "disabled"){
							
							if($style->store_button_program == "disabled"){
								$storetext = $style->before_store;
							}
							else if($style->store_button_program == "after"){
								$storetext = $style->store_before.' '.$product[0]->name;
							}else{
								$storetext = $product[0]->name.' '.$style->store_before;
							}
							
							if($style->button_store == 1){
								$buttonstyle= 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							$store = '
											<div style="margin-top:10px; '.$buttonstyle.'">
												'.$storetext.'
											</div>
											';
							
						}else{
							$store = '';
						}
						
						if($style->price == 1){
							
							if($style->price_button == 1){
								$buttonstyle= 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							$pricetext = $style->price_before.' '.$product[0]->price;
							
							$price ='
									<div style="margin-top:10px; '.$buttonstyle.'">
										'.$pricetext.'
									</div>
									';
						}else{
							$price ='';
						}
		
						$result .='
							<div class="products" style="border:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; float:'.$float.'; height:'.$style->height.'px; width:'.$style->width.'px; text-align:'.$style->align.'; color:#'.$style->textcolor.'; cursor:pointer;" onClick="insertStats11(); window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
								<div style="margin-top:20px; width:'.$style->width.'px; height:'.$style->size.'px; text-align:'.$style->align.'; overflow:hidden;">
									<img src="'.$product[0]->image.'" height="'.$style->size.'"/>
								</div>
								<div style="margin-top:15px; width:'.$style->width.'px;">
									'.$product[0]->title.'
								</div>
								'.$programimage.'
								'.$store.'
								'.$price.'
							</div>
							';	
						
					}
									
					elseif($style->view == 0){
						
					
						$programwidth = $style->size / 2;
					
						if($style->store == 1){
						
							$programimage = '
											<div style="position:relative; float:left; padding:2px; background:#FFF;">
												<img height="'.$programwidth.'" src="'.$product[0]->program_image.'" />
											</div>
											';
						}else{
							$programimage = '';
						}
						
						if($style->price == 1){
							
							if($style->price_button == 1){
								$buttonstyle= 'color:#'.$style->buttontextcolor.'; border:#'.$style->buttonbordercolor.' solid 1px; background:#'.$style->buttoncolor.';';
							}else{
								$buttonstyle= "";
							}
							
							
							
							$pricetext = $style->price_before.'  '.$product[0]->price;
							
							$price ='
									<div style="float:left; margin-left:10px; padding:5px; padding-top:2px; padding-bottom:2px;  '.$buttonstyle.'">
										'.$pricetext.'
									</div>
									';
						}else{
							$price ='';
						}
						
						$result .='
							<div class="products" style="text-align:left; border-bottom:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; height:'.$style->height.'px; width:'.$style->width.'px; color:#'.$style->textcolor.'; margin-top:-5px; cursor:pointer" onClick="insertStats11(); window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
								<div style="float:left; margin-right:10px; width:20%; height:'.$style->size.'px; text-align:center; overflow:hidden;">
									<img src="'.$product[0]->image.'" height="'.$style->size.'"/>
								</div>
								<div style="float:left;  margin-right:10px; width:30%; text-align:'.$style->align.'">
									'.$product[0]->title.'
								</div>
								<div style="width:20%; margin-right:10px; text-align:center; float:left;">'.$programimage.'</div>
								<div style="width:20%; text-align:center; float:left;">'.$price.'</div>
							</div>
							';
					
					}
				}else{
					$result = '';
				}
			}
			
	
		$result .='</div><div style="clear:both;"></div>';
		
		return($result);
	
	}
		
	public function actioncodes(){
		global $wpdb;
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		
		echo '	<script type="text/javascript">					
				function insertStats12(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=actiecode_los&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$aDaisycon = $wpdb->get_row("SELECT * FROM publisher");
		
		if($aDaisycon->actioncode_status == "delete"){
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id WHERE actioncodes.date_end > '".date('Y-m-d')."' OR actioncodes.date_end = 0000-00-00 ORDER BY date_end ");
		}else{
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id ORDER BY date_end ");
		}
		
		foreach($aActies as $rActies){
			
			if($rActies->date_end == '0000-00-00'){
				$end = 'Onbekend';
			}else{
				$explode = explode('-', $rActies->date_end);
				$end = $explode[2].''.$explode[1].''.$explode[0];
			}
				
			$result .=	'
						<div class="Acties" style="cursor: pointer;" onClick="insertStats12(); window.open(&#39;'.$rActies->url.'&#39;, &#39;_blank&#39;);">
							<span class="title"><a href="'.$rActies->url.'" target="_blank" rel="nofollow" onclick="insertStats12()">'.$rActies->actioncode_title.' ('.$rActies->name.')</a></span>
							<div class="date">Deze kortingscode is geldig t/m '.$end.'</div>
							<a href="'.$rActies->url.'" target="_blank" rel="nofollow" onclick="insertStats12()"><img src="'.$rActies->image.'" width="100" height="100" border="0" /></a>
							'.$rActies->actioncode_description.'<br /><br />
							Actiecode: '.$rActies->actioncode.'
						';
			if($aDaisycon->actioncode_status == "alert"){
				if($rActies->date_end < date('Y-m-d') && $rActies->date_end != "0000-00-00"){
					$result .= '<div class="alert">LET OP DEZE ACTIECODE IS VERLOPEN!</div>';
				}
			}
			$result.=	'		<div style="clear:both;"></div>
						</div>
						';	
		}
		
		return($result);
	}
	
	public function actioncodesProgram($array){
		global $wpdb;
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '	<script type="text/javascript">					
				function insertStats13(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=actiecode_programma&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$aDaisycon = $wpdb->get_row("SELECT * FROM publisher");
		
		if($aDaisycon->actioncode_status == "delete"){
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id WHERE (actioncodes.date_end > '".date('Y-m-d')."' OR actioncodes.date_end = '0000-00-00') AND actioncodes.program_id = '".$array['id']."' ORDER BY date_end");
		}else{
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id WHERE actioncodes.program_id = '".$array['id']."' ORDER BY date_end DESC");
		}

		$result = '';
		$i = 0;
		foreach($aActies as $rActies){
				
			$result .=	'
						<div class="Acties" style="cursor: pointer;" onClick="insertStats13(); window.open(&#39;'.$rActies->url.'&#39;, &#39;_blank&#39;);"
							<span class="title"><a href="'.$rActies->url.'" target="_blank" rel="nofollow" onClick="insertStats13();">'.$rActies->actioncode_title.' ('.$rActies->name.')</a></span>
							<div class="date">Deze kortingscode is geldig t/m ';
				if($rActies->date_end == "0000-00-00"){
					$result .= 'Onbekend';
				}else{
					$explode = explode('-', $rActies->date_end);
					$result .= $explode[2].'-'.$explode[1].'-'.$explode[0];
				}
				$result .= '</div>
							<a href="'.$rActies->url.'" target="_blank" rel="nofollow" onclick="insertStats13()"><img src="'.$rActies->image.'" width="100" height="100" border="0" /></a>
							'.$rActies->actioncode_description.'<br /><br />
							Actiecode: '.$rActies->actioncode.'
						';
								
			if($aDaisycon->actioncode_status == "alert"){
				if($rActies->date_end < date('Y-m-d') && $rActies->date_end != "0000-00-00"){
					$result .= '<div class="alert">LET OP DEZE ACTIECODE IS VERLOPEN!</div>';
				}
			}
			$result.=	'		<div style="clear:both;"></div>
						</div>
						';	
			

			
		}
		
		return($result);
	}
	
	public function actioncode($array){
		global $wpdb;

		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		
		echo '	<script type="text/javascript">					
				function insertStats14(){
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertClicks&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&object=actiecode_los&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
				}
			</script>';
		
		$aDaisycon = $wpdb->get_row("SELECT * FROM publisher");
		
		if($aDaisycon->actioncode_status == "delete"){
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id WHERE actioncodes.date_end > '".date('Y-m-d')."' AND actioncodes.actioncode_id = '".$array['id']."' ORDER BY date_end ");
		}else{
			$aActies = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id WHERE actioncodes.actioncode_id = '".$array['id']."' ORDER BY date_end ");
		}
		
		$i = 0;

		if($aActies){
			foreach($aActies as $rActies){

				$result .=	'
							<div class="Acties">
								<span class="title"><a href="'.$rActies->url.'" target="_blank" rel="nofollow" onclick="insertStats14()">'.$rActies->actioncode_title.' ('.$rActies->name.')</a></span>
								<div class="date">Deze kortingscode is geldig t/m ';
				if($rActies->date_end == "0000-00-00"){
					$result .= 'Onbekend';
				}else{
					$result .= $rActies->date_end;
				}
				$result .= '	</div>
								<a href="'.$rActies->url.'" target="_blank" rel="nofollow" onclick="insertStats14()"><img src="'.$rActies->image.'" width="100" height="100" border="0" /></a>
								'.$rActies->actioncode_description.'<br /><br />
								Actiecode: '.$rActies->actioncode.'
							';
				if($aDaisycon->actioncode_status == "alert"){
					if($rActies->date_end < date('Y-m-d') && $rActies->date_end != "0000-00-00"){
						$result .= '<div class="alert">LET OP DEZE ACTIECODE IS VERLOPEN!</div>';
					}
				}
				$result.=	'		<div style="clear:both;"></div>
							</div>
							';	
			}
		}
		
		return($result);
	}
	
	
	public function searchProvider($array){

		return('
		
    	<script type="text/javascript">
    	
    	jQuery(document).ready(function(){
    	
    		var provider = '.$array['program_id'].';
    		var device = "";
    	
    		jQuery("#program_search").change(function(){
    			device = jQuery("#program_search").val();
    		});
    		
  			jQuery("#products_provider").click(function(){
  				
  				var dataString = "provider="+provider+"&device="+device;															
  				
  				jQuery.ajax 
					({
						type: "POST",
						url: "'.get_bloginfo('wpurl').'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=widget",
						data: dataString,
						cache: false,
						success: function(html)
						{
							jQuery("#resultt").html(html);
						} 
					});
					
					
  			});
  			
  			jQuery("#program_search").keyup(function(e){
  				
  				if(event.keyCode == 13) {
				var dataString = "provider="+provider+"&device="+device;															
  				
  				jQuery.ajax 
					({
						type: "POST",
						url: "'.get_bloginfo('wpurl').'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=widget",
						data: dataString,
						cache: false,
						success: function(html)
						{
							jQuery("#resultt").html(html);
						} 
					});
					
				}
  			});
  			
  		});

  		</script>
	  
	    
	    <input type="text" name="search" id="program_search" style="float:left;" /> 
	    <div id="products_provider" class="show" style="float:left; margin-left:10px;">Zoeken</div>
	   
	    <div style="clear:both;"></div>
	    <div id="resultt">
	    </div>
    	');
	}
}
?>