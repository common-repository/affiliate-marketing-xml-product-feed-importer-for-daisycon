<?php
define( 'SHORTINIT', true );
require_once('../../../wp-load.php' );

if (isset($_GET['action'])){
	global $wpdb;
	
	if($_GET['action'] == "updateGet"){
		if(isset($_POST['program'])){
			$wpdb->update('programs',array('daisycon_program_id' => $_POST['val']), 
										array('program_id' => $_POST['program']));
		}else{
			$wpdb->query("UPDATE `programs` SET  `daisycon_program_id` =  '".$_POST['val']."'");
		}
	}
	
	
	if($_GET['action'] == "updateStylesheet"){
		$check = $wpdb->get_row("SELECT * FROM stylesheets WHERE name = '".$_POST['name']."'");
			if(count($check) < 1){
				
				if(empty($_POST['name'])){
					return;
				}else{
				
				$wpdb->insert('stylesheets', array( 'name' => $_POST['name'], 
													'bordercolor' => $_POST['border'], 
													'backgroundcolor' => $_POST['background'], 
													'textcolor' => $_POST['textcolor'], 
													'align' => $_POST['align'], 
													'store' => $_POST['store'], 
													'price' => $_POST['price'], 
													'view' => $_POST['view'], 
													'size' => $_POST['size'],
													'width' => $_POST['width'],
													'height' => $_POST['height'],
													'store_before' => $_POST['before_store'],
													'price_before' => $_POST['before_price'],
													'button_store' => $_POST['button_store_'],
													'buttoncolor' => $_POST['buttoncolor'],
													'buttonbordercolor' => $_POST['buttonbordercolor'],
													'buttontextcolor' => $_POST['buttontextcolor'],
													'float' => $_POST['float'],
													'price_button' => $_POST['price_button'],
													'store_button_program' => $_POST['store_button_program'],
													'moreproducts_color' => $_POST['moreproducts_color'],
													'moreproducts_text' => $_POST['moreproducts_text'],
													'moreproducts_font' => $_POST['moreproducts_font']
												)
				);
			echo 	'
		    		<div id="message" class="Msuccess">
						<p>
							<b>Stylesheet is bijgewerkt!</b>
						</p>
					</div>
		    		';
				}
		
			}else{
				$wpdb->update('stylesheets', array( 'name' => $_POST['name'], 
													'bordercolor' => $_POST['border'], 
													'backgroundcolor' => $_POST['background'], 
													'textcolor' => $_POST['textcolor'], 
													'align' => $_POST['align'], 
													'store' => $_POST['store'], 
													'price' => $_POST['price'], 
													'view' => $_POST['view'], 
													'size' => $_POST['size'],
													'width' => $_POST['width'],
													'height' => $_POST['height'],
													'store_before' => $_POST['before-store'],
													'price_before' => $_POST['before-price'],
													'button_store' => $_POST['button-store'],
													'buttoncolor' => $_POST['buttoncolor'],
													'buttonbordercolor' => $_POST['buttonbordercolor'],
													'buttontextcolor' => $_POST['buttontextcolor'],
													'float' => $_POST['float'],
													'price_button' => $_POST['price_button'],
													'store_button_program' => $_POST['store_button_program'],
													'moreproducts_color' => $_POST['moreproducts_color'],
													'moreproducts_text' => $_POST['moreproducts_text'],
													'moreproducts_font' => $_POST['moreproducts_font']
												), array('name' => $_POST['name'])
				);	
				
			}
				
	}
	
	// Automatically create product lists
	// Menu: Daisycon -> Producten -> Automatisch Producten genereren
	if($_GET['action'] == "viewAutoProducts"){
		if($_POST['view'] == "auto"){
			
			// Get list of stores to use in the product list for query
			$stores = explode(',', $_POST['stores']);
			
			$dStores = '';
			
			for($i=0; $i<count($stores); $i++){
				if($i == (count($stores)-1)){
					$dStores .= "productfeed.program_id = '".$stores[$i]."'";
				}else{
					$dStores .= "productfeed.program_id = '".$stores[$i]."' OR ";
				}
			}
			
			// Sort product lists option for query
			if($_POST['order'] == "abc"){
				$dOrder = "productfeed.title";
			}
			elseif($_POST['order'] == "low"){
				$dOrder = "productfeed.price ASC";
			}
			elseif($_POST['order'] == "high"){
				$dOrder = "productfeed.price DESC";
			}else{
				$dOrder = "RAND()";
			}
			
			// Get search term for query
			$eSearch = explode(" ", $_POST['search']);
			
			for($i=0; $i<count($eSearch); $i++){
		      	$breed = explode('*', $eSearch[$i]);
			      if($i != 0){
			      	if(count($breed) > 1){
			      		$tDatabase .= " AND (productfeed.title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' 
			      								OR productfeed.description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
			      	}else{
			      		$tDatabase .= " AND (productfeed.title LIKE '%".$eSearch[$i]."%' 
			      								OR productfeed.description LIKE '%".$eSearch[$i]."%')";
			      	}
			      }else{
			      	if(count($breed) > 1){
			     		$tDatabase = "(productfeed.title REGEXP '[[:<:]]".$breed[1]."[[:>:]]' 
			     							OR productfeed.description REGEXP '[[:<:]]".$breed[1]."[[:>:]]')";
			      	}else{
			      		$tDatabase = "(productfeed.title LIKE '%".$eSearch[$i]."%' 
			      							OR productfeed.description LIKE '%".$eSearch[$i]."%')";
			      }
		      } 
			}
			
			if(isset($_POST['click'])){
				$limit = ($_POST['amount'] * $_POST['click']).",".$_POST['amount'];
			}else{
				$limit = $_POST['amount'];
			}
			
			// Get results from database with the query bits from above
			$products = $wpdb->get_results("SELECT productfeed.product_id FROM productfeed 
												INNER JOIN programs ON productfeed.program_id = programs.program_id 
												WHERE ".$tDatabase." AND (".$dStores.") 
												ORDER BY ".$dOrder." 
												LIMIT ".$limit."");
						
			foreach($products AS $product){
				$explode[] = $product->product_id;
			}
				
		}else{
			
			$explode = explode(',', $_POST['products']);
			
		}
		
		if(count($explode) > 0){
			
			$result = '';
			// Get stylesheet for preview
			$style = $wpdb->get_row("SELECT * FROM stylesheets WHERE stylesheet_id = '".$_POST['stylesheet']."' ");
			
			for($i=0; $i < count($explode); $i++){
				
				//Selected products
				$product = $wpdb->get_results("SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.name,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE productfeed.product_id = '".$explode[$i]."' ORDER BY RAND()");
				
				if(count($product) > 0){
				
					// Price of the products
					if($product[0]->price === "0.00"){
						$product[0]->price = "Gratis";
					}
					
					// Float of the products, only for view = 1 (tegelweergave)
					if($style->float == 1){
						$float = "left";
					}else{
						$float = "none";
					}

					// View 1 is tile view (tegelweergave)
					if($style->view == 1){
						
						$programwidth = $style->width / 4;
					
						if($style->store == 1){
						
							$programimage = '
											<div id="dcTile_programimage" style="position:absolute; right:10px; top:10px; padding:2px; background:#FFF;">
												<img height="'.$programwidth.'" src="'.$product[0]->program_image.'" style="height: '.$programwidth.'px;"/>
											</div> 
											';
						}else{
							$programimage = '';
						}
						
						// 
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
									<div id="dcTile_pricetext" style="margin-top:10px; '.$buttonstyle.'">
										'.$pricetext.'
									</div>
									';
						}else{
							$price ='';
						}
		
						// Create view example with all the details from above
						$result .='
							<div class="products" id="dcTile_products" style="border:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; float:'.$float.'; height:'.$style->height.'px; width:'.$style->width.'px; text-align:'.$style->align.'; color:#'.$style->textcolor.'; cursor:pointer;" onClick="window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
								<div id="dc_Tileproductimage" style="margin-top:20px; width:'.$style->width.'px; height:'.$style->size.'px; text-align:'.$style->align.'; overflow:hidden;max-width: 100%;">
									<img src="'.$product[0]->image.'" style="height: '.$style->size.'px;" height="'.$style->size.'"/>
								</div>
								<div id="dcTile_producttitle" style="margin-top:15px; width:'.$style->width.'px;">
									'.$product[0]->title.'
								</div>
								'.$programimage.'
								'.$store.'
								'.$price.'
							</div>&nbsp;
							';	
						
					}

					// View 0 is table vieuw (tabelweergave)
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
							$price = '';
						}
						
						$result .= '
							<div id="dcTable_products" class="dcTable_products" style="text-align:left; border-bottom:#'.$style->bordercolor.' 1px solid; background:#'.$style->backgroundcolor.'; height:'.$style->height.'px; width:'.$style->width.'px; color:#'.$style->textcolor.'; margin-top:-5px; cursor:pointer" onClick="window.open(&#39;'.$product[0]->link.'&#39;, &#39;_blank&#39;);">
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
					
					}
				}else{
					$result = '';
				}
			}
		}
	
		$result .='</div><div style="clear:both;"></div>';
		
		if($_POST['moreproducts'] == "1"){
				if($style->view == 0){
					$moreproducts_width = $style->width.'px';
				}else{
					$moreproducts_width = '90%';
				}
				
				$result .='<div id="dcmore" style="background-color:#'.$style->moreproducts_color.'; color:#'.$style->moreproducts_font.'; width: '.$moreproducts_width.'">'.$style->moreproducts_text.'</div>';
		}
				
		echo $result;
	}
	
	if($_GET['action'] == "deleteProduct"){
		global $wpdb;
		
		$wpdb->query("DELETE FROM productfeed WHERE program_id = '".$_POST['program']."' AND product_id = '".$_POST['product']."'");
		echo "DELETE FROM productfeed WHERE program_id = '".$_POST['program']."' AND product_id = '".$_POST['product']."' ";
	}
	 
	if($_GET['action'] == "getProductsArray"){
		$products = $wpdb->get_results("SELECT product_id FROM productfeed WHERE program_id = '".$_POST['program']."'");
		
		$i = 1;
		foreach($products AS $product){
			
			if(count($products) > $i){
				$ids .= ''.$product->product_id.', ';
			}else{
				$ids .= ''.$product->product_id.'';
			}
			
			$i++;
		}
		
		echo $ids;
	}
		
	if ($_GET['action'] == "result"){	
		$explode = explode(" ", $_POST['word']);
      
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
		
		if($_POST['program'] != "" && $_POST['category'] == "" && $_POST['subcategory'] == ""){
			$Aproducts = $wpdb->get_results("SELECT * FROM productfeed 
												WHERE productfeed.program_id = '".$_POST['program']."' 
												AND ".$tDatabase." ORDER BY RAND()");
		}
		elseif($_POST['category'] != "" && $_POST['subcategory'] == ""){
			$Aproducts = $wpdb->get_results("SELECT * FROM productfeed 
												WHERE productfeed.program_id = '".$_POST['program']."' 
												AND productfeed.category = '".$_POST['category']."' 
												AND ".$tDatabase." ORDER BY RAND()");
		}
		elseif($_POST['subcategory'] != ""){
			$Aproducts = $wpdb->get_results("SELECT * FROM productfeed 
												WHERE productfeed.program_id = '".$_POST['program']."' 
												AND productfeed.sub_category = '".$_POST['subcategory']."' 
												AND ".$tDatabase." ORDER BY RAND()");
		}		
		else{
			$Aproducts = $wpdb->get_results("SELECT * FROM productfeed 
												WHERE ".$tDatabase." ORDER BY RAND()");
		}
		
		echo 'Producten gevonden:';
		echo count($Aproducts) . '<br /><br />';
		if (count($Aproducts) > 1500){ // Limit of 1500 products to show in the example view
			_e('Wees specifieker in je zoekopdracht','DaisyconPlugin');

		}else{

		// Get all the different style options	
		$aSheet = $wpdb->get_row("SELECT * FROM stylesheets WHERE stylesheet_id = '".$_POST['stylesheet']."'");

		if(isset($_POST['size'])){$size = $_POST['size'];}else{$size = $aSheet->size;}
		if(isset($_POST['view'])){$view = $_POST['view'];}else{$view = $aSheet->view;}
		if(isset($_POST['border'])){$border = $_POST['border'];}else{$border = $aSheet->bordercolor;}
		if(isset($_POST['background'])){$background = $_POST['background'];}else{$background = $aSheet->backgroundcolor;}
		if(isset($_POST['align'])){$align = $_POST['align'];}else{$align = $aSheet->align;}
		if(isset($_POST['textcolor'])){$textcolor = $_POST['textcolor'];}else{$textcolor = $aSheet->textcolor;}
		if(isset($_POST['price'])){$price = $_POST['price'];}else{$price = $aSheet->price;}
		if(isset($_POST['store'])){$store = $_POST['store'];}else{$store = $aSheet->store;}
		if(isset($_POST['float'])){$float = $_POST['float'];}else{$float = $aSheet->float;}
		if(isset($_POST['height'])){$height = $_POST['height'];}else{$height = $aSheet->height;}
		if(isset($_POST['width'])){$width = $_POST['width'];}else{$width = $aSheet->width;}
		if(isset($_POST['before_price'])){$before_price = $_POST['before_price'];}else{$before_price = $aSheet->price_before;}
		if(isset($_POST['button_store_'])){$button_store = $_POST['button_store_'];}else{$button_store = $aSheet->button_store;}
		if(isset($_POST['button_color_'])){$button_color = $_POST['button_color_'];}else{$button_color = $aSheet->buttoncolor;}
		if(isset($_POST['button_border_color_'])){$button_border_color = $_POST['button_border_color_'];}else{$button_border_color = $aSheet->buttonbordercolor;}
		if(isset($_POST['button_text_color_'])){$button_text_color = $_POST['button_text_color_'];}else{$button_text_color = $aSheet->buttontextcolor;}
		if(isset($_POST['store_button_program_'])){$store_button_program = $_POST['store_button_program_'];}else{$store_button_program = $aSheet->store_button_program;}
		
			foreach($Aproducts AS $Rproducts){
				echo '<li id="'.$Rproducts->product_id.'" style="border-bottom:1px #000 solid; cursor:pointer;">
						<img src="'.$Rproducts->image.'" style="width:40px; float:left;" />
						
						<div style="float:left; margin-left:10px; width:200px; font-size:11px;">
							'.$Rproducts->title.'
						</div>';
					
					$Aprogram = $wpdb->get_row("SELECT * FROM programs 
													WHERE program_id = '".$Rproducts->program_id."' ");
	
					echo '<div style="float:left; margin-left:10px; width:50px;">
								<img src="'.$Aprogram->image.'" style="width:40px;"/>
							 </div>';
						
					echo '<div style="float:left; margin-left:10px; width:50px; font-size:11px; line-height:40px;">
								&euro;'.$Rproducts->price.'
							 </div>';
	
					echo '</li>';
			}
		}
	}
	
	if ($_GET['action'] == "subcategory"){
		$aSubcat = array();
		$aSubcat = $wpdb->get_results("SELECT Distinct productfeed.sub_category FROM productfeed WHERE productfeed.program_id = '".$_POST['program']."' AND productfeed.category = '".$_POST['category']."' ORDER BY productfeed.sub_category");		
		echo  '<option value="">Selecteer een subcategory...</option>';
		foreach($aSubcat AS $rSubcat){
		echo	'
				<option value="'.$rSubcat->sub_category.'">'.$rSubcat->sub_category.'</option>
				';
		}
		break;
	}
	
	if ($_GET['action'] == "category"){
		$aCat = array();
		$aCat = $wpdb->get_results("SELECT Distinct productfeed.category FROM productfeed 
										WHERE program_id = '".$_POST['program']."' 
										ORDER BY productfeed.category");
		
		echo '<option value="">Selecteer een categorie...</option>';
	
		foreach($aCat AS $rCat){
			echo '<option value="'.$rCat->category.'">'.$rCat->category.'</option>';
		}	 
	}
		
	if ($_GET['action'] == "widget"){
		
		if(isset($_POST['store'])){
			if($_POST['store'] == "all" || $_POST['store'] == ""){
				$products = $wpdb->get_results("SELECT * FROM (SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE productfeed.title LIKE '%".$_POST['device_store']."%' AND NOT productfeed.program_id = '7' AND
    															NOT productfeed.program_id = '21' AND
    															NOT productfeed.program_id = '24' AND
    															NOT productfeed.program_id = '36' AND
    															NOT productfeed.program_id = '27' AND
    															NOT productfeed.program_id = '40' AND
    															NOT productfeed.program_id = '44' ORDER BY RAND() LIMIT 10) temp_tbl ORDER BY price ASC ");
			}else{
				$products = $wpdb->get_results("SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE productfeed.title LIKE '%".$_POST['device_store']."%' AND productfeed.program_id = '".$_POST['store']."' ORDER BY RAND() LIMIT 10");
			}
		}
		
		if(isset($_POST['provider'])){
			if($_POST['provider'] == "all" || $_POST['provider'] == ""){
				$products = $wpdb->get_results("SELECT * FROM (SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE 	productfeed.title LIKE '%".$_POST['device']."%' AND(
																																							productfeed.program_id = '7' OR
																							    															productfeed.program_id = '21' OR
																							    															productfeed.program_id = '24' OR
																							    															productfeed.program_id = '36' OR
																							    															productfeed.program_id = '27' OR
																							    															productfeed.program_id = '40' OR
																							    															productfeed.program_id = '44') ORDER BY RAND() LIMIT 10) temp_tbl ORDER BY price ASC ");
			}else{
				$products = $wpdb->get_results("SELECT 	productfeed.image,
														productfeed.title,
														productfeed.price,
														programs.image AS program_image,
														productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id WHERE 	productfeed.title LIKE '%".$_POST['device']."%' AND 
																																							productfeed.program_id = '".$_POST['provider']."' ORDER BY RAND() LIMIT 10");
			}
		}
		
		echo	'
				<h1 class="title" style="margin-top:15px;">'.$_POST['device'].'</h1>
				';
		
		if ($products){
			foreach($products AS $product){
				
				if($product->price == "0.00"){
					$price = "Gratis!";
				}else{
					$price = '&euro; '.$product->price;
				}
				
				echo '
						<div class="dcTable_products">
							<div class="img"> 
								<center>
								<a href="'.$product->link.'" target="_blank">
									<img src="'.$product->image.'"/> 
								</a>
								</center>
							</div>
							<div class="title">
								<a href="'.$product->link.'" target="_blank">
									'.$product->title.'
								</a>
							</div>
							<div class="program">
								<a href="'.$product->link.'" target="_blank">
									<img src="'.$product->program_image.'" />
								</a>
							</div>
							<div class="price">
								<a href="'.$product->link.'" target="_blank">
									'.$price.'
								</a>
							</div>
							<div style="clear:both;"></div> 
						</div>
					';
			}
		}else{
			echo 	'
					<div id="handler">
						Helaas zijn er geen producten gevonden!
					</div>
					';
		}
	}
	
	// Styleresult is for creating a preview of the stylesheet
	// Menu: Daisycon -> Stylesheets
	if ($_GET['action'] == "styleresult"){	
		$products = $wpdb->get_results("SELECT 	productfeed.image,
												productfeed.title,
												productfeed.price,
												programs.name,
												programs.image AS program_image,
												productfeed.link FROM productfeed INNER JOIN programs ON productfeed.program_id = programs.program_id 
												ORDER BY RAND() LIMIT 3");
		
		if($products){
			foreach($products AS $product){	
				
				// Price of the products
				if($product->price == "0.00"){
					$price = "Gratis!";
				}else{
					$price = $product->price;
				}
				
				// Float of the products, only for view = 1 (tegelweergave)
				if($_POST['float'] == 1){
					$float = "left";
				}else{
					$float = "none";
				}
				
				// View 1 is tile view (tegelweergave)
				if($_POST['view'] == 1){
					
					$programwidth = $_POST['width'] / 4;
					
					if($_POST['store'] == 1){
						$programimage = '
										<div style="position:absolute; right:10px; top:10px; padding:2px; background:#FFF;">
											<img height="'.$programwidth.'" src="'.$product->program_image.'" />
										</div>
										';
					}else{
						$programimage = '';
					}
					
					if($_POST['button_store'] == 1 || $_POST['store_button_program'] != "disabled"){
						
						if($_POST['store_button_program'] == "disabled"){
							$storetext = $_POST['before_store'];
						}
						else if($_POST['store_button_program'] == "after"){
							$storetext = $_POST['before_store'].' '.$product->name;
						}else{
							$storetext = $product->name.' '.$_POST['before_store'];
						}
						
						if($_POST['button_store'] == 1){
							$buttonstyle = 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$_POST['button_text_color'].'; border:#'.$_POST['button_border_color'].' solid 1px; background:#'.$_POST['button_color'].';';
						}else{
							$buttonstyle = "";
						}
						
						$store = '<div style="margin-top:10px; '.$buttonstyle.'">
									'.$storetext.'
								  </div>';
						
					}else{
						$store = '';
					}
					
					if($_POST['price'] == 1){
						
						if($_POST['button_price'] == 1){
							$buttonstyle = 'padding:5px; padding-top:2px; padding-bottom:2px; color:#'.$_POST['button_text_color'].'; border:#'.$_POST['button_border_color'].' solid 1px; background:#'.$_POST['button_color'].';';
						}else{
							$buttonstyle = "";
						}
						
						$pricetext = $_POST['before_price'].' '.$product->price;
						
						$price = '<div style="margin-top:10px; '.$buttonstyle.'">
									'.$pricetext.'
								  </div>';
					}else{
						$price = '';
					}
					
					echo '<div class="dcTable_products" style="border:#'.$_POST['border'].' 1px solid; background:#'.$_POST['background'].'; float:'.$float.'; height: '.$_POST['height'].'px; width:'.$_POST['width'].'px; text-align:'.$_POST['align'].'; color:#'.$_POST['textcolor'].';">
							<div style="margin-top:20px; width:'.$_POST['width'].'px; height:'.$_POST['size'].'px; text-align:'.$_POST['align'].'; overflow:hidden; max-width: 100%;">
								<img src="'.$product->image.'" height="'.$_POST['size'].'"/>
							</div>
							<div style="margin-top:15px; width:'.$_POST['width'].'px;">
								'.$product->title.'
							</div>
							'.$programimage.'
							'.$store.'
							'.$price.'
						  </div>';
					
				}else{
					
					$programwidth = $_POST['size'] / 2;
					
					if($_POST['store'] == 1){
					
						$programimage = '<div style="position:relative; float:left; padding:2px; background:#FFF;">
											<img height="'.$programwidth.'" src="'.$product->program_image.'" />
										 </div>';
					}else{
						$programimage = '';
					}
					
					if($_POST['price'] == 1){
						
						if($_POST['button_price'] == 1){
							$buttonstyle = 'color:#'.$_POST['button_text_color'].'; border:#'.$_POST['button_border_color'].' solid 1px; background:#'.$_POST['button_color'].';';
						}else{
							$buttonstyle = "";
						}
						
						$pricetext = $_POST['before_price'].' '.$product->price;
						
						$price = '<div style="float:left; margin-left:10px; padding:5px; padding-top:2px; padding-bottom:2px;  '.$buttonstyle.'">
									'.$pricetext.'
								  </div>';
						
					}else{
						$price = '';
					}
					
					echo '<div class="dcTable_products" style="text-align:left; border-bottom:#'.$_POST['border'].' 1px solid; background:#'.$_POST['background'].'; height:'.$_POST['height'].'px; width:'.$_POST['width'].'px; color:#'.$_POST['textcolor'].'; margin-top:-5px;">
							<div style="float:left; margin-right:10px; width:20%; height:'.$_POST['size'].'px; text-align:center; overflow:hidden;">
								<img src="'.$product->image.'" height="'.$_POST['size'].'"/>
							</div>
							<div style="float:left;  margin-right:10px; width:30%; text-align:'.$_POST['align'].'">
								'.$product->title.'
							</div>
							<div style="width:20%;  margin-right:10px; text-align:center; float:left;">'.$programimage.'</div>
							<div style="width:20%; text-align:center; float:left;">'.$price.'</div>
						 </div>';
				}

			}
		}
	}
}
?>