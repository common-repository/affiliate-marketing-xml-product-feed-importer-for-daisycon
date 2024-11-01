<?php 
/* Daisycon affiliate marketing plugin
 * File: core.php
 * 
 * Core to create all the pages in the menu. View and controllers separated
 * 
 */

class admin{
	
	public function adminProgram(){
		
		global $wpdb;
		
		
		if(isset($_POST['importallproducts'])){
			include('cron/importProducts.php');		
		}
		
		if (isset($_GET['action'])){
			
			if($_GET['action'] == 'importproducts'){
					include('cron/importProducts.php');
					$results = $wpdb->get_results("SELECT	programs.program_id,
															programs.daisycon_program_id,
															programs.name,
															programs.date,
															categories.rename,
															programs.product_count,
															categories.name AS categoryName,
															programs.productfeed,
															programs.productfeed_date,
															programs.program_id FROM `programs` INNER JOIN categories ON programs.category = categories.name ORDER BY programs.name ASC");
					$view = self::adminProgramView($results);
	
			}
			
			if ($_GET['action'] == "edit"){
				
				$view = self::adminProgramEdit();
				
				if (isset($_POST['save'])){
					
					if($_POST['visible'] == 'on'){
						$visible = 1;
					}else{
						$visible = 0;
					}
					$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
														
					$wpdb->update('programs',array(	'description' => stripslashes($_POST['description']),
													'category' => $_POST['category'],
													'url' => $_POST['url'],
													'more' => stripslashes($_POST['more']),
													'date' => $date,
													'visible' => $visible,
													'subid' => $_POST['subid'],
													'productfeed' => $_POST['productfeed']), array('program_id' => $_GET['product']));
					
					$rCategories = $wpdb->get_results("SELECT * FROM categories ORDER BY categories.rename ASC");
				
					$rprograms = $wpdb->get_results("SELECT programs.name,
															programs.date,
															programs.ecpc,
															programs.category,
															programs.url,
															programs.more,
															programs.productfeed,
															programs.image,
															programs.subid,
															programs.description,
															programs.visible,
															categories.rename,
															categories.name AS categoryName FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.program_id = '".$_GET['product']."'");
					
					$view = self::adminProgramEdit($rprograms, $rCategories);
				
					echo 	'
							<div id="message" class="Msuccess">
								<p>
									<b>'.__('Het programma is bijgewerkt!','DaisyconPlugin').'</b>
								</p>
							</div>
							';
				}
				elseif(isset($_GET['update'])){
					if($_GET['update'] == "true"){
						include('cron/importProducts.php');
					}
				}
				elseif(isset($_POST['products'])){
					
				if($_POST['visible'] == 'on'){
						$visible = 1;
					}else{
						$visible = 0;
					}
					$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
					
					$wpdb->update('programs',array(	'description' => stripslashes($_POST['description']),
													'category' => $_POST['category'],
													'url' => $_POST['url'],
													'more' => stripslashes($_POST['more']),
													'date' => $date,
													'visible' => $visible,
													'subid' => $_POST['subid'], 
													'productfeed' => $_POST['productfeed']), array('program_id' => $_GET['product']));
					$rCategories = $wpdb->get_results("SELECT * FROM categories ORDER BY categories.rename ASC");
				
					$rprograms = $wpdb->get_results("SELECT programs.name,
															programs.date,
															programs.ecpc,
															programs.category,
															programs.url,
															programs.more,
															programs.productfeed,
															programs.image,
															programs.description,
															programs.visible,
															programs.subid,
															categories.rename,
															categories.name AS categoryName FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.program_id = '".$_GET['product']."'");
					
					$view = self::adminProgramEdit($rprograms, $rCategories);
					
					include('cron/importProducts.php');
				}
			}
		}else{

			$publisher = $wpdb->get_row("SELECT * FROM publisher");
			$wi = explode('/media/', $publisher->feed);
			$wi = explode('/', $wi[1]);
			echo '	<script type="text/javascript">
						jQuery.ajax
						({
							url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=programma&jsoncallback=?",
							dataType: "jsonp",
							cache: false,
							success: function(html)
							{
							} 
						});
					</script>';
			
			if (isset($_POST['sCategory'])){
				$results = $wpdb->get_results("SELECT	programs.program_id,
														programs.daisycon_program_id,
														programs.name,
														programs.date,
														categories.rename,
														programs.product_count,
														categories.name AS categoryName,
														programs.productfeed,
														programs.productfeed_date,
														programs.program_id FROM `programs` INNER JOIN categories ON programs.category = categories.name WHERE categories.rename = '".$_POST['category']."' ORDER BY programs.name ASC");
				$view = self::adminProgramView($results);
			}
			else{
				$results = $wpdb->get_results("SELECT	programs.program_id,
														programs.daisycon_program_id,
														programs.name,
														programs.date,
														categories.rename,
														programs.product_count,
														categories.name AS categoryName,
														programs.productfeed,
														programs.productfeed_date,
														programs.program_id FROM `programs` INNER JOIN categories ON programs.category = categories.name ORDER BY programs.name ASC");
				$view = self::adminProgramView($results);
			}
		}
		
		
		echo $view;
	}
	
	public function adminProgramView($results){
		
		global $wpdb;
		
		//wp_enqueue_script('tablesorter', plugins_url('/files/js/jquery.tablesorter.min.js',__FILE__) );
		wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
		wp_enqueue_script('tipjquerymin', plugins_url('/files/js/jquery.tipTip.minified.js',__FILE__) );
		
		$output = 	'
		<script type="text/javascript">    
    jQuery(document).ready(function() 
	    { 	      	        
	        jQuery("#checkall").click(function(){
	        	
	       		if(jQuery(this).is(":checked")){
					jQuery(".updateGet").attr("checked", true);
				}else{
					jQuery(".updateGet").attr("checked", false);
				} 
				
				if(jQuery(this).is(":checked") == true){
					var get = 0;
				}else{
					var get = 1;
				} 
				
				var dataString = "&val=" + get;
																											
				jQuery.ajax
				({
					type: "POST",
					url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=updateGet",
					data: dataString,
					cache: false,
					success: function(html)
					{
						 
					} 
				});
	        	
	        });
	        
	        jQuery(".updateGet").click(function(){
	        	if(jQuery(this).is(":checked") == true){
					var get = 0;
				}else{
					var get = 1;
				} 
				
				var dataString = "&program="+ jQuery(this).attr("id") + "&val=" + get;
																											
				jQuery.ajax
				({
					type: "POST",
					url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=updateGet",
					data: dataString,
					cache: false,
					success: function(html)
					{
						
					} 
				});
	        });
	    } 
	); 
    							
    </script>
		
					<div class="wrap">
						<h2>'.__( 'Programma&lsquo;s','DaisyconPlugin' ).'</h2>
						'.__('Plak de shorttag(s) in een pagina of blogpost. Het programma wordt automatisch op deze pagina getoond. Je kan per programma kiezen tussen een shorttag met de lange of met een korte omschrijving. Die kan je zelf invullen door op het programma te klikken. Hier kan je ook links aanpassen, de categorie wijzigen of het programma uit het overzicht halen.<br /><br />Gebruik de "shorttag voor actiecode" om alle actiecodes van die adverteerder direct op je website te plaatsen.<br /><br /> Druk op "Producten ophalen" om de producten uit de productfeed van deze adverteerder op te halen. Hier kan je ook de individuele feeds updaten.  <br /><br />','DaisyconPlugin').'
					</div>
					<div class="screen-options">
						<form action="" method="post">
							<select name="category">
								<option>'.__('Alles tonen','DaisyconPlugin').'</option>
					';
		
		$rCategories = $wpdb->get_results("SELECT DISTINCT categories.rename FROM categories ORDER BY categories.rename ASC");
		foreach ($rCategories as $array){
			$output .=	'
								<option value="'.$array->rename.'">'.$array->rename.'</option>
						';
		}
		$output .=	'
							</select>
							<input type="submit" name="sCategory" value="'.__('Zoeken','DaisyconPlugin').'" class="button" />
							</form>
							';
		$Productfeed = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");
			
		if ($Productfeed->feed != NULL && $Productfeed->programsproductfeed != NULL && $rCategories != NULL){					
		
		$output .= '<div id="allprograms" style="float:right; margin-right:20px;">
								<form action="" name="testproducts" method="post" onsubmit="return confirmProduct();">
									<input type="hidden" value="'.$Productfeed->daisycon_id.'" name="id" />
									<input type="submit" class="button" name="importallproducts" value="'.__('Gekozen producten ophalen','DaisyconPlugin').'" class="btn btn-success"/ >
								</form>
							</div>
					';
		}

		$output .= '
					</div>
					<div id="loadingDiv"  style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="40" alt="Even geduld..." /></div>
					<div style="clear:both; height:10px;"></div>
					<table id="sortTable" class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
						<thead class="tableHeader">
						<tr>
							<th class="manage-column column-name sortable desc" scope="col">
								<span>'.__('Programma','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc" scope="col">
								<span>'.__('Shorttag voor korte omschrijving','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc" scope="col" style="width:22%;">
								<span>'.__('Shorttag voor Actiecode','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc" scope="col" style="width:19%;">
								<span>'.__('Datum producten opgehaald','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc" scope="col">
								<span>'.__('Producten ophalen','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc" scope="col" style="width:10%;">
								<span><input style="float:left; margin-top:30px;" type="checkbox" id="checkall" name="checkall" /><a href="" style="margin-top:20px;"  id="tooltip" title="'.__('Selecteer de programmas waarvan jij de producten wilt opnemen','DaisyconPlugin').'">[?]</a></span>
							<div id="tiptip_holder">
<div id="tiptip_content">
<div id="tiptip_arrow">
<div id="tiptip_arrow_inner"></div>
</div>
</div>
</div>
								</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
							
					';

		foreach ($results as $array) {
				$cActioncode = $wpdb->get_row("SELECT * FROM actioncodes WHERE program_id = '".$array->program_id."'");		
		
				$output.=	'
							<tr>
								<td>
									<a style="text-decoration:underline;" href="admin.php?page=programs&action=edit&product='.$array->program_id.'">
										'.$array->name.'
									</a>
								</td>
								<td>
									<div style="cursor:pointer;" onclick="select_all(this)">[moreProgram id="'.$array->program_id.'"]</div>
								</td>
								<td>
									<div style="cursor:pointer;" onclick="select_all(this)">'; if(count($cActioncode) > 0){ $output.= '[actioncodesProgram id="'.$array->program_id.'"]';} $output.= '</div>
								</td>
															';
			$dCount = '';
			$dCount = $wpdb->get_var("SELECT COUNT(*) FROM productfeed WHERE productfeed.program_id = '".$array->program_id."' ");		
			
			if($array->product_count > 0){
				
				if(count($dCount) > 0){
				$date = explode('-', $array->productfeed_date);
				
					if($array->productfeed_date != '0000-00-00'){
				$output .= '		<td>
										'.$date[2].'-'.$date[1].'-'.$date[0].'
									</td>
							';
					}else{
										$output .= '		<td>
										'.__('Niet opgehaald','DaisyconPlugin').'
									</td>	';
					}
				}
				
				else{
					$output .= '		<td>
										'.__('Niet opgehaald','DaisyconPlugin').'
									</td>
							';
				}
				
				
				$output .= '
									<td>
									<a href="admin.php?page=programs&action=importproducts&product='.$array->program_id.'" onclick="loadingDiv()">
							';
				
				if($dCount > 0){
					$output .= __('Producten updaten','DaisyconPlugin');
				}else{
					$output .= __('Producten ophalen','DaisyconPlugin');
				}
				$output .= '</a>
									
									';
									}else{
				$output .= '		<td><span style="font-size: 0.1px; color:#F9F9F9;">z</span></td>
									<td>
									'.__('Geen producten','DaisyconPlugin').'
									</td>
									
									
									';		
									}
				
				if($array->product_count > 0){
					$output .='<td><input class="updateGet" id="'.$array->program_id.'" type="checkbox" name="get"'; if($array->daisycon_program_id == 0){$output .= ' checked'; }
					$output .= 	'/></td>';
				}else{
					$output .= '<td>&nbsp;</td>'; 
				}
				
				$output .= '
							</tr>
						';
		}
		
				$output .= 	'
						</tbody>
					</table>
					';
		
		return($output);
		
	}

	
	public function adminProgramEdit(){
		global $wpdb;

		$rCategories = $wpdb->get_results("SELECT * FROM categories ORDER BY categories.rename ASC");	
		
		$result = $wpdb->get_results("SELECT 	programs.name,
												programs.daisycon_program_id,
												programs.date,
												programs.ecpc,
												programs.category,
												programs.url,
												programs.more,
												programs.productfeed,
												programs.image,
												programs.description,
												programs.visible,
												programs.subid,
												programs.productfeed_date,
												categories.rename,
												categories.name AS categoryName FROM programs INNER JOIN categories ON programs.category = categories.name WHERE programs.program_id = '".$_GET['product']."'");
		
		if (isset($_POST['save'])){
					
		if($_POST['visible'] == 'on'){
				$visible = 1;
			}else{
				$visible = 0;
		}
		
		if($_POST['get'] == 'on'){
				$get = 1;
			}else{
				$get = 0;
		}
		
		$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
																					
		$wpdb->update('programs',array(	'description' => stripslashes($_POST['description']),
													'category' => $_POST['category'],
													'url' => $_POST['url'],
													'more' => stripslashes($_POST['more']),
													'date' => $date,
													'visible' => $visible,
													'daisycon_program_id' => $get,
													'subid' => $_POST['subid'],
													'productfeed' => $_POST['productfeed']), array('program_id' => $_GET['product']));
		}
	
		foreach($result as $array){	
			if($array->productfeed_date != "0000-00-00"){
				$productfeed_date = explode('-', $array->productfeed_date);
				$productfeed_date = $productfeed_date[2].'-'.$productfeed_date[1].'-'.$productfeed_date[0];
			}else{
				$productfeed_date = __('Nog niet volledig ge&uuml;pdatet');
			}
			$products_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(product_id) FROM productfeed WHERE program_id = '".$_GET['product']."' "));
			  
			$output =	'
					<div class="wrap">
						<h2>'.__( 'Programma wijzigen','DaisyconPlugin' ).'</h2>
					</div>
					<div style="margin-top:5px; margin-bottom:10px; padding:5px; border-top:#ccc 1px dashed; border-bottom:#ccc 1px dashed;">
					<a href="admin.php?page=Daisycon">Daisycon</a> &gt; <a href="admin.php?page=programs">'.__('Programma&lsquo;s','DaisyconPlugin').'</a> &gt; '.$array->name.'
					</div>
					<form method="POST" action="">
					<div id="poststuff">
						<div id="post-body">
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Programmanaam','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<input type="text" size="50" value="'.$array->name.'" name="name" disabled />
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Informatie','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<div style="float:left">
											<img src="'.$array->image.'" />
										</div>
										<div style="float:left; margin-left:10px; line-height:20px;">
											'.$array->name.'<br />
											<a href="'.$array->url.'">'.__('Ga naar website','DaisyconPlugin').'</a><br /><br />
											';
			if(strlen($array->productfeed)>0){
				$output .=	'
											'.__('Aantal producten opgehaald:','DaisyconPlugin').' '.$products_count.'<br />
											'.__('Laatst producten opgehaald:','DaisyconPlugin').' '.$productfeed_date.' <input type="submit" name="products" id="products" class="button" value="'.__('Producten ophalen','DaisyconPlugin').'" style="float:right; margin-left:20px; margin-top:-21px;" />	
							';
			}else{
				$output .=	'				'.__('Niet mogelijk om producten te importeren','DaisyconPlugin').'
							';
			}
			$output .='		
										</div>
										<div style="clear:both;"></div>
									</div>
								</div>
							</div> 
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Korte omschrijving','DaisyconPlugin').'</label></h3>
									<div class="inside">
										'.__('Deze tekst komt op de categoriepagina. Je kan zelf de link aanpassen en bijvoorbeeld ook naar een pagina binnen je site linken.','DaisyconPlugin').'<br /><br />
										<textarea name="more" rows="10" cols="105">'.$array->more.'</textarea>
									</div>
								</div>
							</div> 
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Uitgebreide omschrijving','DaisyconPlugin').'</label></h3>
									<div class="inside">
									'.__('Deze tekst komt op de programmapagina','DaisyconPlugin').'<br /><br />
										<textarea name="description" rows="20" cols="105">'.$array->description.'</textarea>
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Categorie','DaisyconPlugin').'</label></h3>
									<div class="inside">
										'.__('Hier kan je dit programma in een andere categorie plaatsen.','DaisyconPlugin').' <br /><br />
										<select name="category">
					';
			foreach($rCategories as $aCategories){
		
				$output .=	'
											<option value="'.$aCategories->name.'" '; if($array->category == $aCategories->name){$output .= ' selected';}
				
				$output .= '>'.$aCategories->rename.'</option>';
			}
			$output .=	
					'					</select>
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Affiliatelink','DaisyconPlugin').'</label></h3>
									<div class="inside">
										'.__('Deze link wordt gebruikt om via het logo naar de adverteerder te linken. Je kan zelf een SubID toevoegen.','DaisyconPlugin').'<br /><br />
										<input type="text" size="100" value="'.$array->url.'" name="url" />
									</div>
								</div>
							</div>
							
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Productfeed link','DaisyconPlugin').'</label></h3>
									<div class="inside">
										'.__('De productfeed link wordt gebruikt om de producten op te halen en in de database te zetten. Vul indien gewenst in het tweede veld de SubID in.','DaisyconPlugin').'<br /><br />
										<input type="text" value="'.$array->productfeed.'" name="productfeed" size="100"/> <input type="text" value="'.$array->subid.'" name="subid" />
									</div>
								</div>
							</div>
							
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Datum','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<select name="day">
			';
			
			$date = explode("-", $array->date);
			for($i=1; $i<32; $i++){
				$output .=	'
											<option value="'.$i.'" '; if($i == $date[2]){$output .= ' selected';} $output .='>'.$i.'</option>
							';
			}
				$output .= 	'			</select>
										<select name="month">
			';
			
			for($j=1; $j<13; $j++){
				$output .=	'
											<option value="'.$j.'" '; if($j == $date[1]){$output .= ' selected';} $output .='>'.$j.'</option>
							';
			}
				$output .= 	'			</select>
										<select name="year">
			';
			
			for($k=date('Y')-10; $k<date('Y')+1; $k++){
				$output .=	'
											<option value="'.$k.'" '; if($k == $date[0]){$output .= ' selected';} $output .='>'.$k.'</option>
							';
			}
				$output .= 	'			</select>
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">eCPC</label></h3>
									<div class="inside">
									'.__('De eCPC wordt berekend op basis de omzet van de laatste twee maanden in jouw account.','DaisyconPlugin').'<br /><br />
										<input type="text" size="50" value="&euro; '.$array->ecpc.'" name="ecpc" disabled />
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Zichtbaar','DaisyconPlugin').'</label></h3>
									<div class="inside">
									'.__('Gebruik deze optie om een programma niet zichtbaar te maken. Indien deze optie is aangevinkt zal er een tekst weergegeven worden dat het programma niet meer beschikbaar is.','DaisyconPlugin').'
										<br/><input type="checkbox" name="visible"'; if($array->visible == 1){$output .= ' checked'; }
			$output .= 	'
								/>
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Producten ophalen','DaisyconPlugin').'</label></h3>
									<div class="inside">
									'.__('Gebruik deze optie om de producten van het programma op te halen als de actie "Alle producten van alle programmas" wordt gekozen.','DaisyconPlugin').'
										<br/><input type="checkbox" name="get"'; if($array->daisycon_program_id == 1){$output .= ' checked'; }
			$output .= 	'
								/>
									</div>
								</div>
							</div>
							<input type="submit" name="save" id="save" class="button" value="'.__('Opslaan','DaisyconPlugin').'"  />
							<input type="hidden" value="'.$array->name.'" name="name" />	
						</div>
					</div>
					</form>
					<div style="clear:both;"></div>
<p><a href="admin.php?page=programs"><-- '.__('Terug','DaisyconPlugin').'</a></p>
						';
		}
		
		return($output);
	}
	
	public function adminCategorie(){
		
		global $post,$wpdb;
		
		
		if (isset($_GET['action'])){
			if($_GET['action'] == "edit"){
				$view = self::adminCategoryEdit();
				
				if(isset($_POST['save'])){
			
					if($_POST['visible'] == 'on'){
						$visible = 1;
					}else{
						$visible = 0;
					}
					
					if($_POST['visible'] == 'on'){
						$visible = 1;
					}else{
						$visible = 0;
					}
					
					$wpdb->update('categories',array('rename' => stripslashes($_POST['rename']), 'visible' => $visible, 'visible' => $visible), array('category_id' => $_GET['category']));
					$view = self::adminCategoryEdit();
					echo '
							<div id="message" class="Msuccess">
								<p>
									<b>'.__('De categorie is bijgewerkt!','DaisyconPlugin').'</b>
								</p>
							</div>
						';
				}
			}
		}else{
			$view = self::adminCategoryView();
		}
		
		echo $view;
	}
	
	public function adminCategoryEdit(){
	
		global $wpdb;
		
		$result = $wpdb->get_results("SELECT * FROM categories WHERE category_id = '".$_GET['category']."'");
		
		foreach($result as $array){
		
		$output = 	'
					<div class="wrap">
						<h2>'.__( 'Categorie wijzigen','DaisyconPlugin').'</h2>
					</div>
					
					<form method="POST" action="">

					<div id="poststuff">
						<div id="post-body">
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Categorie','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<input type="text" size="50" value="'.$array->name.'" disabled />
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Categorie naam','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<input type="text" size="50" value="'.$array->rename.'" name="rename" />
									</div>
								</div>
							</div>
							<div id="post-body-content">
								<div class="stuffbox">
									<h3><label for="link_name">'.__('Zichtbaar','DaisyconPlugin').'</label></h3>
									<div class="inside">
										<input type="checkbox" name="visible" ';if($array->visible == 1){$output.=' checked';}$output.=' />
									</div>
								</div>
								
							</div>
							<div>
							<input type="submit" name="save" id="save" value="'.__('Opslaan','DaisyconPlugin').'" />
							</div>
							<div>
							<p><a href="admin.php?page=categorie"><-- '.__('Terug','DaisyconPlugin').'</a></p>
							</div>									
						</div>
					</div>
					</form>
					';
		}
		
		return($output);
	}

	public function adminCategoryView(){
		
		wp_enqueue_script('tablesorter', plugins_url('/files/js/jquery.tablesorter.min.js',__FILE__) );
		wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
		
		global $wpdb;
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		
		$output = ' 
		
		<script type="text/javascript">
			jQuery.ajax
			({
				url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=categorie&jsoncallback=?",
				dataType: "jsonp",
				cache: false,
				success: function(html)
				{
				} 
			});
		</script>
		<div class="wrap">
						<h2>'.__( 'Categorie&euml;n','DaisyconPlugin').'</h2>
						'.__('Plak onderstaande shorttag op een pagina of in een blogpost en de categorie wordt getoond. Vul achter &lsquo;amount&lsquo; tussen de aanhalingstekens het aantal programma&lsquo;s in dat je wil tonen. Vul je niets in, dan worden alle programma&lsquo;s getoond. De volgorde wordt bepaald obv eCPC (dit moet je wel aanzetten bij Instellingen).','DaisyconPlugin').'<br /><br />
					</div>
					<table id="sortTable" class="wp-list-table widefat fixed bookmarks tablesorter" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col">
								<a href=""><span>'.__('Categorie','DaisyconPlugin').'</span></a>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<a href=""><span>'.__('Shorttag','DaisyconPlugin').'</span></a>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<a href=""><span>'.__('Naam','DaisyconPlugin').'</span></a>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<a href=""><span>'.__('Programma&lsquo;s','DaisyconPlugin').'</span></a>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
					';
		
		global $post,$wpdb;
		
		$results = $wpdb->get_results("SELECT * FROM `categories` ORDER BY categories.rename ASC");
		
		$result = '<table>';
		
		foreach($results as $array){
			
			$results_prd = $wpdb->get_results("SELECT * FROM `programs` WHERE `category` = '".$array->name."'");
			$count = count($results_prd);
			
			$link = str_replace(" ", "_", $array->rename);
			$link = str_replace("&", "en", $link);
			$output .= '
							<tr>
								<td>
									<a href="admin.php?page=categorie&action=edit&category='.$array->category_id.'">
										'.$array->name.'
									</a>
								</td>
								<td>
									<div style="cursor:pointer;" onclick="select_all(this)">[category id="'.$array->category_id.'" amount=""]</div>
								</td>
								<td>
									'.$array->rename.'
								</td>
								<td>
									'.$count.'
								</td>
							</tr>';
		}	
		
		
		$output .=	'
						</tbody>
					</table>
				';
	
		return($output);
	}
	
	public function adminFeeds(){
			
		global $post,$wpdb;	
		
	if(isset($_POST['toevoegen'])){
    $daisycon = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");
		
	$url = 'http://publishers.daisycon.com/en/affiliatemarketing/feed/';		
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if( ini_get('allow_url_fopen') ){
		
		$args = array(
			'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ))
		);
		
		$result = wp_remote_request( $url, $args );
		$body = wp_remote_retrieve_body($result);
		
	}else{
		
		$context = stream_context_create(array(
		    'http' => array(
		        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		));
		
		$body = file_get_contents($url, false, $context);
		
	}	

	$jsonData = json_decode($body);

	if(!empty($jsonData) && !isset($jsonData->errors)){
	echo '
	<div style="border: 1px; width: 100%; height: 400px; margin-top: 325px; position: absolute; background-color: #ffffff;">
							<table class="wp-list-table widefat bookmarks" cellspacing="0" style="width:98%;">
							<thead>
							<tr>
								<th class="manage-column column-name sortable desc"  scope="col" colspan="5">
									<a href="#"><span>'.__('Stap 2: Website ophalen!','DaisyconPlugin').'</span></a>
								</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
							</table>
	
		<div style="width: 600px;">
	<p>'.__('Voor bovenstaand account heb je de volgende websites bij Daisycon aangemeld. Kies nu de website waarop je deze plugin hebt ge&iuml;nstalleerd, en klik op &#39;Toevoegen&#39;.','DaisyconPlugin').'</p>';	
	
	echo '<form action="" name="getfeeds" method="POST" id="getfeeds" />';		
	foreach ($jsonData as $publisherId => $feed)
	{
		echo '<select name="mediaId" id="mediaId">';
			
			foreach ($feed as $key => $value){
				echo '<option value="'. $key .'">'. $value .'</option>';
				}
				echo '</select>';
			break;
		}
		echo '
			<script type="text/javascript">
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertError&website='.base64_encode($_SERVER['SERVER_NAME']).'&email='.base64_encode($_POST['username']).'&error=pass&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
			</script>';	

		echo '<input type="hidden" name="publisher" value="'. $publisherId .'" />';
		echo '<input type="hidden" name="password" value="'. $_POST['password'] .'" />';
		echo '<input type="submit" name="getfeeds" value="'.__('Toevoegen!','DaisyconPlugin').'" class="buttonGreen" id="getfeeds" style="float: right; margin-right: 140px;" />';
		echo '</form></div></div>';	

		$wpdb->query("DELETE FROM programs");
		$wpdb->query("DELETE FROM categories");
		$wpdb->query("DELETE FROM productfeed");
		$wpdb->query("DELETE FROM actioncodes");	
		
	if (count($daisycon) == 0){
		$wpdb->insert('publisher', array('username' => $_POST['username'], 
								'password' => md5($_POST['password'])));
		}else{
		$wpdb->update('publisher', array('username' => $_POST['username'], 
								'password' => md5($_POST['password'])), array('daisycon_id' => 1));
	}

	}else{
	$rFeeds = $wpdb->get_row("SELECT * FROM publisher");

		if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
		echo '
			<div id="message" class="Mwarning">
				<p>
					<b>'.__('Vul je gegevens even opnieuw in.','DaisyconPlugin').'</b>
				</p>
			</div>';
		
		echo '
			<script type="text/javascript">
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertError&website='.base64_encode($_SERVER['SERVER_NAME']).'&email='.base64_encode($_POST['username']).'&error=opnieuw&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
			</script>';	
		}else{
			echo '
			<script type="text/javascript">
					jQuery.ajax
					({
						url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertError&website='.base64_encode($_SERVER['SERVER_NAME']).'&email='.base64_encode($_POST['username']).'&error=fout&jsoncallback=?",
						dataType: "jsonp",
						cache: false,
						success: function(html)
						{
						} 
					});
			</script>';	
			
		echo '
			<div id="message" class="Merror">
				<p>
					<b>'.__('Je moet even de (juiste) gegevens invullen.','DaisyconPlugin').'</b>
				</p>
			</div>';	
		}

		
	}
	
}elseif(isset($_POST['getfeeds'])){

		$cFeeds = $wpdb->get_row("SELECT * FROM publisher");

		$getPublisherId = $_POST['publisher'];
		
		$username = $cFeeds->username;
		$password = $_POST['password'];
		
		$url = 'http://publishers.daisycon.com/en/affiliatemarketing/feed/fetch/media/'. $_POST['mediaId'] .'/publisher/'. $getPublisherId .'';			

		echo '<br/>';
		
		if( ini_get('allow_url_fopen') ){
			
			$args = array(
				'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ))
			);
			
			$result = wp_remote_request( $url, $args );
			$body = wp_remote_retrieve_body($result);
			
		}else{
			
			$context = stream_context_create(array(
			    'http' => array(
			        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
			    )
			));
			
			$body = file_get_contents($url, false, $context);
			
		}		

		$jsonData = json_decode($body);
		
		$wpdb->update('publisher', array('actiecodefeed' => $jsonData->promotioncodes_export, 
											'feed' => $jsonData->program_export, 
											'programsproductfeed' => $jsonData->productfeeds_export,
											'actioncode_status' => 'delete',
											'api' => '0',
											'program_date' => '0000-00-00 00:00:00',
											'actioncode_date' => '0000-00-00 00:00:00'), array('daisycon_id' => 1));

		
	}
		
		if (isset($_POST['savehere'])){

			$wpdb->update('publisher', array(		'actioncode_status' => $_POST['actioncodestatus'], 
													'subid' => $_POST['subid'], 
													'api' => $_POST['api'],
													'feed_timeout' => $_POST['feed_timeout'],
													'subid' => $_POST['urlreplacer']), array('daisycon_id' => 1));
		
		echo ' 			
			<div id="message1" class="Msuccess">
					<p>
						<b>'.__('Gegevens zijn bijgewerkt!','DaisyconPlugin').'</b>
					</p>
			</div>
				';
		
		}	
	
		if (isset($_POST['save'])){
			$cFeeds = $wpdb->get_row("SELECT * FROM publisher");

			if (count($cFeeds) == 0){
				$wpdb->insert('publisher', array('actiecodefeed' => $_POST['actiecode'], 
														'actioncode_status' => $_POST['actioncodestatus'], 
														'feed' => $_POST['url'], 
														'programsproductfeed' => $_POST['prodfeed'], 
														'subid' => $_POST['subid'], 
														'username' => $_POST['username'], 
														'password' => md5($_POST['password']), 
														'api' => $_POST['api'],
														'feed_timeout' => $_POST['feed_timeout'], 
														'subid' => $_POST['urlreplacer']));
			}else{				
					if($cFeeds->password == $_POST['password']){
						$password = $cFeeds->password;
					}else{
						$password = md5($_POST['password']);
					}

					$wpdb->update('publisher', array(	'actiecodefeed' => $_POST['actiecode'], 
														'actioncode_status' => $_POST['actioncodestatus'], 
														'feed' => $_POST['url'], 
														'programsproductfeed' => $_POST['prodfeed'], 
														'subid' => $_POST['subid'], 
														'username' => $_POST['username'], 
														'password' => $password, 
														'api' => $_POST['api'],
														
														'feed_timeout' => $_POST['feed_timeout'],
														'subid' => $_POST['urlreplacer']), array('daisycon_id' => $_POST['id']));

			}
			
			echo ' 			
			<div id="message1" class="Msuccess">
					<p>
						<b>'.__('Gegevens zijn bijgewerkt!','DaisyconPlugin').'</b>
					</p>
			</div>
				';
			
			$view = self::adminFeedsView();
			
		}elseif(isset($_POST['update'])){
			$cFeeds = $wpdb->get_row("SELECT * FROM publisher");
			
			if (count($cFeeds) == 0){
				$wpdb->insert('publisher', array('feed' => $_POST['url']));
			}else{
				
					if($cFeeds->password == $_POST['password']){
						$password = $cFeeds->password;
					}else{
						$password = md5($_POST['password']);
					}

					$date = date("Y-m-d H:i:s");
					$wpdb->update('publisher', array('actioncode_status' => $_POST['actioncodestatus'], 'program_date' => $date), array('daisycon_id' => '1'));

			}
			
			
			include('cron/API.php');
		
		}elseif(isset($_POST['updateactiecodes'])){

		$date = date("Y-m-d H:i:s");

		$wpdb->update('publisher', array('actioncode_date' => $date), array('daisycon_id' => '1'));
				
		include('cron/importActioncodes.php');
						
		
		}elseif(isset($_POST['importallproducts'])){
			include('cron/importProducts.php');		
		}
					
		
		if($_GET['subpage'] == 'getfeeds'){
			$view = self::adminFeedsView();
			echo $view;
		}else{
			$view = self::adminGetFeedsView();
			echo $view;
		}
	}
	
	public function adminFeedsView(){

		global $post,$wpdb;
		
		wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
		wp_enqueue_script('tiptip', plugins_url('/files/js/jquery.tipTip.minified.js',__FILE__) );
		
		$rFeeds = $wpdb->get_row("SELECT * FROM publisher");
		$rPrograms = $wpdb->get_results("SELECT * FROM programs");	
		$rActioncodes = $wpdb->get_results("SELECT * FROM actioncodes");
		
				$output = '	
					<div id="viewMenu">
						<div class="choiseview" id="productOne" style="font-size:10px; background:#DDDDDD; -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888; margin-right: 20px;" onclick="window.location.href=&#39;admin.php?page=Daisycon&#39;"><h2><a style="color:#FFFFFF; text-decoration: none;" href="admin.php?page=Daisycon">'.__('Inloggen (automatisch feeds toevoegen)','DaisyconPlugin').'</a></h2></div>
						<div class="choiseview" id="productTwo" style="font-size:10px; background:#CCCCCC; -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888;" onclick="window.location.href=&#39;admin.php?page=Daisycon&subpage=getfeeds&#39;"><h2><a style="color:#000000; text-decoration: none;" href="admin.php?page=Daisycon&subpage=getfeeds">'.__('Handmatig feeds toevoegen','DaisyconPlugin').'</a></h2></div>
					</div>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
			<form action="" name="test" method="POST" id="update" />	
			
					<div class="wrap">
						<h2>'.__( 'Handmatig feeds toevoegen','DaisyconPlugin').'</h2>
					</div>
					<div style="margin-right: 20px;">
					'.__('Gebruik deze functie alleen als je wil filteren in de feeds die je gebruikt. Deze optie geeft jou als gebruiker de vrijheid om zelf in het Daisyconsysteem de feeds op te halen en te filteren. Wij raden alleen meer ervaren gebruikers aan deze optie toe te passen. Gebruik anders &#39;Automatisch feeds toevoegen&#39; in het vorige tabblad.','DaisyconPlugin').'<br />
					
					<br />					
					'.__('Om deze plugin te gebruiken moet je eerst aangemeld zijn als <a href="http://www.daisycon.com/nl/publishers/" target="_blank">publisher bij Daisycon</a>. Vul hieronder eerst je gegevens en vervolgens de feeds in. De feeds moet je uit het Daisycon systeem halen. Na het invullen van de gegevens en de feeds moet er eerst op Opslaan geklikt worden. Vervolgens moeten eerst de Programma&lsquo;s opgehaald worden en daarna de Actiecodes. De producten kan je in een keer allemaal ophalen, maar dit kan enkele uren duren. We raden je aan eerst de producten van de programma&lsquo;s die je wil gebruiken op te halen. Dit kan je doen bij "Programma&lsquo;s".<br /><br /> <a href="http://www.daisycon.com/nl/blog/wordpress_affiliate_plugin_productfeeds/" target="_blank">Klik hier voor een uitgebreide handleiding</a>.','DaisyconPlugin').'<br /><br />			
					</div>					
					<br /><br />
					<br /><br />
				

					<table class="wp-list-table widefat bookmarks" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col" colspan="5">
								<a href="#"><span>'.__('Stap 1: Daisycon gegevens','DaisyconPlugin').'</span></a>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
					';
				
			$output	.=	'
					<tr>
									<td>
										'.__('E-mailadres van publisheraccount','DaisyconPlugin').'
									</td>
									<td>
										<input type="text" class="check" value="'.$rFeeds->username.'" id="username" name="username"/>
									</td>
									<td>
									
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
								<tr>
								<td>
										'.__('Wachtwoord publisheraccount','DaisyconPlugin').'
									</td>
								<td> 
									<input class="check" type="password" name="password" id="password" value="'.$rFeeds->password.'" />
								</td>
									<td>
									
									</td>
									<td>
									</td>
									<td>
									</td>
					</tr>
			';
			if($rFeeds->username != NULL && count($rPrograms) == 0){
			$output	.=	'
					<div id="message" class="Msuccess">
						<p>
							<b>'.__('De publishergegevens zijn nu toegevoegd. Haal nu de programma&lsquo;s op om af te ronden en aan de slag te gaan.','DaisyconPlugin').'</b>
						</p>
					</div>
			';
			}
			 
			$output	.=	'
							<tr>
								<td>
										'.__('Programmalijst (feed met programma&#39;s)','DaisyconPlugin').'
								</td>
								<td>
									<input type="hidden" value="'.$rFeeds->daisycon_id.'" name="id" />
									<input class="check" type="text" value="'.$rFeeds->feed.'" name="url" style="width:200px;"/>
								</td>
								<td>
									<div id="loadingDiv"  style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
								</td>

							<tr>
								<td>
										'.__('Productfeedlijst (feed met beschikbare productfeeds)','DaisyconPlugin').'
								</td>
								<td>
									
									<input class="check" type="text" value="'.$rFeeds->programsproductfeed.'" name="prodfeed" style="width:200px;"/>
								</td>
									<td>
										
									</td>
									<td>
									</td>
							</tr>					
							<tr>
								<td>
									'.__('Actiecodelijst (feed met actiecodes)','DaisyconPlugin').'
								</td>
								<td>
									<input class="check" type="text" value="'.$rFeeds->actiecodefeed.'" name="actiecode" style="width:200px;"/>
								</td>
								<td>
								<div id="loadingDiv2" style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
								</td>
								
							</tr>
							<tr>
								<td>
										'.__('Verlopen actiecodes weergeven','DaisyconPlugin').'
									</td>
								<td>
									<select class="check" name="actioncodestatus" style="width:150px;">
										<option value="delete"';if($rFeeds->actioncode_status == "delete"){$output.= ' SELECTED';}$output.= '>'.__('Niet weergeven','DaisyconPlugin').'</option>
										<option value="alert"';if($rFeeds->actioncode_status == "alert"){$output.= ' SELECTED';}$output.= '>'.__('Wel weergeven, maar met melding','DaisyconPlugin').'</option>
									</select>
								</td>
								<td>
									
								</td>
								<td>
								</td>
							</tr>
							<tr>
									<td>
										'.__('eCPC voor programmalijst gebruiken JA / NEE','DaisyconPlugin').'
									</td>
									<td>
										<input class="check" type="checkbox" name="api" value="1"'; if ($rFeeds->api == "1"){ $output .= ' checked';} $output .=' />
									</td>
									<td>
										
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										'.__('Automatisch links omzetten naar Daisycon affiliatelinks','DaisyconPlugin').'
									</td>
									<td>
										<input class="check" type="checkbox" name="urlreplacer" value="1"'; if ($rFeeds->subid == "1"){ $output .= ' checked';} $output .=' style="margin-right: 10px;"><a href="http://www.daisycon.com/nl/blog/linkreplacer-wordpress-plugin" target="_blank">'.__('Lees info','DaisyconPlugin').'</a>

									</td>
									<td>
										
									</td>
									<td>
									</td>
								</tr>
								
								<tr>
									<td>
										'.__('Time-out producten ophalen','DaisyconPlugin').' <a href="#" id="tooltip" title="'.__('Tijd van de time-out tussen het producten ophalen. Standaard staat dit op 10 seconden. Het verlagen van deze tijd is op eigen risico.','DaisyconPlugin').'">[?]</a>
									</td>
									<td>
										<select name="feed_timeout">
											<option value="1"'; if ($rFeeds->feed_timeout == "1"){ $output .= 'selected';} $output .= '>1 '.__('seconde','DaisyconPlugin').'</option>
											<option value="2"'; if ($rFeeds->feed_timeout == "2"){ $output .= 'selected';} $output .= '>2 '.__('seconden','DaisyconPlugin').'</option>
											<option value="3"'; if ($rFeeds->feed_timeout == "3"){ $output .= 'selected';} $output .= '>3 '.__('seconden','DaisyconPlugin').'</option>
											<option value="4"'; if ($rFeeds->feed_timeout == "4"){ $output .= 'selected';} $output .= '>4 '.__('seconden','DaisyconPlugin').'</option>
											<option value="5"'; if ($rFeeds->feed_timeout == "5"){ $output .= 'selected';} $output .= '>5 '.__('seconden','DaisyconPlugin').'</option>
											<option value="6"'; if ($rFeeds->feed_timeout == "6"){ $output .= 'selected';} $output .= '>6 '.__('seconden','DaisyconPlugin').'</option>
											<option value="7"'; if ($rFeeds->feed_timeout == "7"){ $output .= 'selected';} $output .= '>7 '.__('seconden','DaisyconPlugin').'</option>
											<option value="8"'; if ($rFeeds->feed_timeout == "8"){ $output .= 'selected';} $output .= '>8 '.__('seconden','DaisyconPlugin').'</option>
											<option value="9"'; if ($rFeeds->feed_timeout == "9"){ $output .= 'selected';} $output .= '>9 '.__('seconden','DaisyconPlugin').'</option>
											<option value="10"'; if ($rFeeds->feed_timeout == "10"){ $output .= 'selected';} $output .= '>10 '.__('seconden (default)','DaisyconPlugin').'</option>
										</select>
											
									</td>
									
									<td>
										
									</td>
									<td>
									</td>
								</tr>
					
							<tr>
									<td>
										
									</td>
									<td>
										<input type="hidden" value="'.$rFeeds->subid.'" name="subid" maxlength="43" />
									</td>
									<td>
									
									</td>
									<td>
									</td>
							</tr>	
							<tr>
								<td colspan="2">
									<input type="submit" class="button" name="save" value="'.__('Opslaan','DaisyconPlugin').'" />
								</td>
							</tr>
						</tbody>
					</table>
					<br />
					';
		
		$check = $wpdb->get_row("SELECT * FROM publisher");
		if(count($check) > 0){
		
			$output	.=	'
						<table class="wp-list-table widefat bookmarks" cellspacing="0" style="width:98%;">
							<thead>
							<tr>
								<th class="manage-column column-name sortable desc"  scope="col" colspan="5">
									<a href="#"><span>'.__('Stap 2: Alles ophalen!','DaisyconPlugin').'</span></a>
								</th>
							</tr>
							</thead>
							<tfoot>
							</tfoot>
							<tbody>
								<tr>

									<td>
									
									';
			
							if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
							if($rFeeds->program_date == '0000-00-00 00:00:00' && count($rPrograms) == 0){
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#FF6600';} $output.= '" onclick="loadingDiv()"/> 
										
										</td>
									<td>
									<font color="#FF6600">
									'.__('Druk op de button om de programma&lsquo;s waarbij je bent aangemeld op te halen.','DaisyconPlugin').'
									</font> 
									
								';
							}elseif($rFeeds->program_date != '0000-00-00 00:00:00' && count($rPrograms) > 0){
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv()"/> 
									</td>
									<td>
									<font color="#006400">
									'.__('Laatste update','DaisyconPlugin').': '.self::makeDate($rFeeds->program_date).'
									</font>
									
								';
							}else{
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#000000';} $output.= '" onclick="loadingDiv()"/> 
									</td><td>
									'.__('Nog niet opgehaald.','DaisyconPlugin').'
									
								';
							}
				}else{
					$output	.=	__('Sla eerst je programmalijst en productlijst op!');
				}
						
				$output	.=	'
									 
									</td>
									<td>
									<div id="loadingDiv"  style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
									</td>
	
								</tr>
								<tr>

									<td>';
				if(strlen($rFeeds->actiecodefeed) > 0){
							if($rFeeds->program_date != '0000-00-00 00:00:00' && $rFeeds->actioncode_date == '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'
									
									<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#FF6600';} $output.= '" onclick="loadingDiv2()"/> 
									</td>
									<td>
									<font color="#FF6600">
									'.__('Druk op de button om de actiecodes waarbij je bent aangemeld op te halen. ','DaisyconPlugin').'
									</font>
									
								';
							}elseif($rFeeds->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) > 0){
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 
									</td>
									<td>
									<font color="#006400">
									'.__('Laatste update','DaisyconPlugin').': '.self::makeDate($rFeeds->actioncode_date).'
									</font>';
							}elseif($rFeeds->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 
									</td>
									<td>
									'.__('Geen actiecodes gevonden bij de opgehaalde programma&#39;s.','DaisyconPlugin').' ';
							}else{
		
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#000000';} $output.= '" onclick="loadingDiv2()" disabled /> 
									</td><td>
									<font color="#FF6600">
									'.__('Haal eerst je programma&#39;s op.','DaisyconPlugin').'
									</font>
									
								';
							}
				}else{
					$output .= __('Sla eerst je actiecodelijst op!');
				}
				
				$output	.=	'					
			
									</td>
									<td>
									<div id="loadingDiv2" style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
									</td>
	
								</tr>
								<tr>
	
									<td>
					</form>
					<form action="" method="post" onsubmit="return confirmProducts();">
									
							';

				if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
							if($rFeeds->program_date != '0000-00-00 00:00:00'){
								$output	.=	'
										<input type="submit" class="button" name="importallproducts" value="'.__('Alle producten ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= '';} $output.= '" />
									</td>
									<td>
									
									'.__('Druk op de button om alle producten van de programma&lsquo;s op te halen (Let op: dit kan enkele uren duren).','DaisyconPlugin').'
										';
							}else{
								$output	.=	'
										<input type="submit" class="button" name="importallproducts" value="'.__('Alle producten ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= '';} $output.= '" disabled />
									</td>
									<td>
									<font color="#FF6600">
									'.__('Haal eerst je programma&#39;s op.','DaisyconPlugin').'
									 </font>	';	
							}
								
				}else{
					$output	.=	__('Sla eerst je programmalijst en productlijst op!');
				}
				
			$output	.=	'
									</form>
									</td>
	
								</tr>
							</tbody>
							</thead>
						</table><p>&nbsp;</p>';
						
			}
		return($output);
	}
	
	public function makeDate($date){
		$explode = explode(" ", $date);
		$date = explode("-", $explode[0]);
		$time = explode(":", $explode[1]);
		
		if($explode[0] == date("Y-m-d")){
			$date = __('Vandaag','DaisyconPlugin');
		}
		elseif(date('Y-m-d', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -1 day")) == $explode[0]){
			$date = __('Gisteren','DaisyconPlugin');
		}
		elseif(date('Y-m-d', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " -2 day")) == $explode[0]){
			$date = __('Eergisteren','DaisyconPlugin');
		}
		else{
			$date = $date[2].'-'.$date[1].'-'.$date[0];
		}
		
		$time = __('om ','DaisyconPlugin') . $time[0] . ':' . $time[1] . __(' uur','DaisyconPlugin');
		
		return($date.' '.$time);
	}
	
	public function adminProduct(){

		if(!isset($_GET['action'])){
			$view = self::adminProductView();
			echo $view;
		}
		
		
	}
	
	public function adminProductView(){
		
		global $wpdb;
		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '<script type="text/javascript">
						jQuery.ajax
						({
							url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=producten&jsoncallback=?",
							dataType: "jsonp",
							cache: false,
							success: function(html)
							{
							} 
						});
				</script>';
		
		wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
		wp_enqueue_script('widget', includes_url('js/jquery/ui/jquery.ui.widget.min.js',__FILE__));
		wp_enqueue_script('mouse', includes_url('js/jquery/ui/jquery.ui.mouse.min.js',__FILE__));
		wp_enqueue_script('accor', includes_url('js/jquery/ui/jquery.ui.accordion.min.js',__FILE__));
		wp_enqueue_script('cor', includes_url('js/jquery/ui/jquery.ui.core.min.js',__FILE__));
		wp_enqueue_script('dragg', includes_url('js/jquery/ui/jquery.ui.draggable.min.js',__FILE__));
		wp_enqueue_script('droppa', includes_url('js/jquery/ui/jquery.ui.droppable.min.js',__FILE__));
		wp_enqueue_script('sorta', includes_url('js/jquery/ui/jquery.ui.sortable.min.js',__FILE__));
		
		$Aprograms = $wpdb->get_results("SELECT Distinct programs.name, programs.program_id FROM programs INNER JOIN productfeed ON programs.program_id = productfeed.program_id");
		$Asheets = $wpdb->get_results("SELECT * FROM stylesheets");
		
		$output =	'	
						

						<script type="text/javascript">
						var program = "";
						var category = "";
						var subcategory = "";
						var amount = "";
						var productlist = [];
						var stylesheet = "1";
						var word = "";
						var view = "one";
						
						jQuery(document).ready(function()
						{
							jQuery(".program").change(function()
							{
							 
							program = jQuery(this).val();
							category = "";
							subcategory = "";
							
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																											
								jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=category",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery(".category").html(html);
									} 
								});
								
								jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=subcategory",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery(".subcategory").html(html);
									} 
							});
								
						});

						
						//Shortag generen
						jQuery("#shorttag").click(function()
						{
							
							productlist = [];
						
							jQuery("#sortable2 li").each(function(index) {
    							productlist.push(jQuery(this).attr("id"));
							});
						
							if(stylesheet < 1){
								stylesheet = 1;
							}
							jQuery("#result_shorttag").html("[daisycon_products products=&quot;"+productlist+"&quot; stylesheet=&quot;"+stylesheet+"&quot;]");			
						}); 
						
						//Shorttag generen
						jQuery("#shorttagauto").click(function()
						{
							
							storelist = [];

							jQuery("#aSelectedstores option").each(function(index) {
    							storelist.push(jQuery(this).val());
							});
							
							stylesheet = jQuery("#stylesheet").val();
							
							if(stylesheet < 1){
								stylesheet = 1;
							}
													
							if(jQuery("input[name=aMoreproducts]").is(":checked") == true){
								moreproducts = 1;
							}else{
								moreproducts = 0;
							} 
							
							jQuery("#result_shorttagauto").html("[daisycon_products view=&quot;auto&quot; stores=&quot;"+storelist+"&quot; stylesheet=&quot;"+stylesheet+"&quot; search=&quot;"+jQuery("#aWord").val()+"&quot; amount=&quot;"+jQuery("#aAmount").val()+"&quot; order=&quot;"+jQuery("#aOrder").val()+"&quot; moreproducts=&quot;"+moreproducts+"&quot;]");			
						}); 
						
						//example
						jQuery("#examplebutton").click(function(){
							
							storelist = [];

							jQuery("#aSelectedstores option").each(function(index) {
    							storelist.push(jQuery(this).val());
							});
							
							stylesheet = jQuery("#stylesheet").val();
							
							if(stylesheet < 1){
								stylesheet = 1;
							}
							
							if(jQuery("input[name=aMoreproducts]").is(":checked") == true){
								moreproducts = 1;
							}else{
								moreproducts = 0;
							} 

							if(storelist.length == 0){
								jQuery("#result_example").html("'.__('Selecteer een programma om producten te tonen').'");
							}else{
								var dataString = "&view=auto&stores="+storelist+"&moreproducts="+moreproducts+"&stylesheet="+stylesheet+"&search="+jQuery("#aWord").val()+"&amount="+jQuery("#aAmount").val()+"&order="+jQuery("#aOrder").val()+"";
																								
								jQuery.ajax
									({
										type: "POST",
										url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=viewAutoProducts",
										data: dataString,
										cache: false,
										success: function(html)
										{
											jQuery("#result_example").html(html);
										} 
								});
							}
						});
						
						jQuery(".category").change(function()
						{
							category = jQuery(this).val();
							subcategory = "";
							program = products.elements["program"].value;
							
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																											
								jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=subcategory",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery(".subcategory").html(html);
									} 
							});
							
						});
						
						jQuery(document).keypress(function(event) {
	    					var keycode = (event.keyCode ? event.keyCode : event.which);
							if(keycode == 13){
								if(view == "one"){
									amount=jQuery(this).val();
									var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																										
									jQuery.ajax
									({
										type: "POST",
										url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=result",
										data: dataString,
										cache: false,
										success: function(html)
										{
											jQuery("#sortable1").html(html);
										} 
									});
								}
								if(view == "two"){
									storelist = [];

									jQuery("#aSelectedstores option").each(function(index) {
		    							storelist.push(jQuery(this).val());
									});
									
									
									stylesheet = jQuery("#stylesheet").val();
									
									if(stylesheet < 1){
										stylesheet = 1;
									}
									
							

									
									if(storelist.length == 0){
										jQuery("#result_example").html("Selecteer een programma om producten te tonen");
									}else{
										var dataString = "&view=auto&stores="+storelist+"&stylesheet="+stylesheet+"&search="+jQuery("#aWord").val()+"&amount="+jQuery("#aAmount").val()+"&order="+jQuery("#aOrder").val()+"";
																										
										jQuery.ajax
											({
												type: "POST",
												url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=viewAutoProducts",
												data: dataString,
												cache: false,
												success: function(html)
												{
													jQuery("#result_example").html(html);
												} 
										});
									}
								}
							}
						});

						jQuery("#generate").click(function()
							{
								amount=jQuery(this).val();
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																								
									jQuery.ajax
									({
										type: "POST",
										url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=result",
										data: dataString,
										cache: false,
										success: function(html)
										{
											jQuery("#sortable1").html(html);
										} 
								});
							});
						
						jQuery(".amount").change(function()
							{
								 
								amount=jQuery(this).val();
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																								
						});
							
						jQuery(".word").change(function()
							{
								 
								word=jQuery(this).val();
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																								
						});
						
						jQuery(".subcategory").change(function()
						{
							subcategory = jQuery(this).val();
							var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
														
						});
						
						jQuery(".stylesheet").change(function()
							{
								stylesheet=jQuery(this).val();
								var dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&stylesheet=" + stylesheet + "&word=" + word;
																
									jQuery.ajax
									({
										type: "POST",
										url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=result",
										data: dataString,
										cache: false,
										success: function(html)
										{
											jQuery("#sortable1").html(html);
										} 
								});
							});
						
						jQuery(function() {
							jQuery( "ul.droptrue" ).sortable({
								connectWith: "ul",
								stop: function(event, ui) { 
									var item = ui.item;
									var product_id = jQuery(item).attr("id");
																	
									productlist.push(product_id);
								
									jQuery("#demo").html() = "[products program_id="+products.elements["program"].value+" amount="+products.elements["amount"].value+" category="+products.elements["category"].value+" subcategory="+products.elements["subcategory"].value+" products="+productlist+"]";
									
        						}
							});
					
							jQuery( "ul.dropfalse" ).sortable({
								connectWith: "ul",
								dropOnEmpty: false,
								stop: function(event, ui) { 
									//weghalen van product
									var item = ui.item;
									var product_id = jQuery(item).attr("id");
									for(var i=0; i<productlist.length; i++){
										if (productlist[i] == product_id){
											productlist.splice([i], 1);
										}
									}
        						}
							});
					
							jQuery( "#sortable1, #sortable2" ).disableSelection();
							
							
						});

					});

					</script>
					';
		
		$output .=	'
					<div id="viewMenu">
						<div class="choiseview" id="productOne" style="font-size:10px; background:#CCCCCC; -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888;"><h2>'.__( 'Handmatig Producten genereren','DaisyconPlugin' ).'</h2></div>
						<div class="choiseview" id="productTwo" style="margin-left: 10px; font-size:10px; background:#DDDDDD; color:#FFFFFF;  -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888;"><h2>'.__( 'Automatisch Producten genereren','DaisyconPlugin' ).'</h2></div>
					</div>
					<div style="clear:both;"></div><br/>
					<div id="viewOne">
					<div class="wrap">
						'.__('Genereer een shorttag door middel van de onderstaande velden. De gegenereerde shorttag is vervolgens te gebruiken in een pagina of blogpost om een lijst met producten te tonen.','DaisyconPlugin').'<br /><br />
					</div>
					';
		
		$output .=	'
					<table class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col" colspan="3">
								<a href="#">'.__('Producten ophalen','DaisyconPlugin').'</a>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
								<tr>
								<td>
									'.__('Stylesheet','DaisyconPlugin').'
								</td>
									<td>
										<select name="stylesheet" class="stylesheet" style="width: 200px;" id="stylesheet">
											<option value="">'.__('Selecteer een stylesheet...','DaisyconPlugin').'</option>
					';
		
		foreach($Asheets as $Rsheets){
			$output .=	'
											<option value="'.$Rsheets->stylesheet_id.'">'.$Rsheets->name.'</option>
											
						';
		}
		
		$output .=	'
										</select>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										'.__('Programma','DaisyconPlugin').'
									</td>
									<td>
										<select name="program" class="program" style="width: 200px;">
											<option value="">'.__('Selecteer een programma...','DaisyconPlugin').'</option>
					';
		
		foreach($Aprograms as $Rprograms){
			$output .=	'
											<option value="'.$Rprograms->program_id.'">'.$Rprograms->name.'</option>
											
						';
		}
		
		$output .=	'
										</select>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										'.__('Categorie','DaisyconPlugin').'
									</td>
									<td>
										<select name="category" class="category" style="width: 200px;">
											<option value="">'.__('Selecteer een categorie...','DaisyconPlugin').'</option>
										</select>
									</td>
									<td>
									('.__('Optioneel','DaisyconPlugin').')
									</td>
								</tr>
								<tr>
									<td>
										'.__('Subcategorie','DaisyconPlugin').'
									</td>
									<td>
										<select name="subcategory" class="subcategory" style="width: 200px;">
											<option value="">'.__('Selecteer een subcategorie...','DaisyconPlugin').'</option>
										</select>
									</td>
									<td>
										('.__('Optioneel','DaisyconPlugin').')
									</td>
								</tr>
								<tr>
									<td>
										'.__('Zoeken','DaisyconPlugin').'
									</td>
									<td>
										<input type="text" name="word" class="word" style="width: 200px; border: 3px solid #33CC33;" />
									</td>
									<td>
										'.__('Zet * voor het woord om exact te zoeken (*voorbeeld) en spatie om op meerder woorden te zoeken (zoekwoord1 zoekwoord2).','DaisyconPlugin').'
										 
									</td>
								</tr>
								<tr>
									<td colspan="2">
									
<input type="button" id="generate" class="button" value="'.__('Producten zoeken','DaisyconPlugin').'" />
<input type="button" id="shorttag" class="button" value="'.__('Genereer shorttag','DaisyconPlugin').'" />
									
									</td>
								</tr>
					';
			
			$output .= '		
							
						</tbody>
					</table>
					
					<div style="clear:both; float:left;"></div>
					
					<div style="clear:both; float:left;"></div>
					<div id="result_shorttag" onclick="select_all(this)" style="cursor:pointer;">'.__('Zoek en sleep de producten die je op je site wil zetten in het rechtervlak. Klik daarna op "Genereer shorttag" om hier de shorttag te genereren die je in de HTML van je website kan plaatsen.','DaisyconPlugin').' </div>
						<ul id="sortable1" class="droptrue">'.__('Sleep de producten die je op je website wil tonen naar rechts','DaisyconPlugin').'</ul>
						<div style="overflow: auto; position: fixed; margin-left: 446px; z-index:1"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/arrow.png" alt="" style="width:50px;" /></div>
						<ul id="sortable2" class="dropfalse">'.__('Sleep hier de producten heen','DaisyconPlugin').'</ul>
					</div>
						';
			

			$output .=	'
					<div id="viewTwo" style="display:none;">
						<div class="wrap">
						'.__('In dit menu kunt u zoeken in productfeeds. U hoeft dus geen producten te selecteren, maar het zoekresultaat wordt direct op uw website getoond. 
						Genereer een shorttag door middel van de onderstaande velden. Plak de gegenereerde shorttag in de HTML van een pagina of blogpost.','DaisyconPlugin').'<br /><br />
					</div>
					<table class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col" colspan="4">
								<a href="#">'.__('Producten ophalen','DaisyconPlugin').'</a>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
							<form action="" method="post" name="products">
								<tr>
									<td>'.__('Zoekterm','DaisyconPlugin').'</td><td><input type="text" name="Aword" id="aWord" style="width: 200px; border: 3px solid #33CC33;"/></td><td colspan="2">'.__('Zet * voor het woord om exact te zoeken (*voorbeeld) en spatie om op meerder woorden te zoeken (zoekwoord1 zoekwoord2).','DaisyconPlugin').'</td>
								</tr>
								<tr>
									<td>'.__('Aantal producten tonen','DaisyconPlugin').'</td><td><input type="text" value="10" name="aAmount" id="aAmount" style="width: 200px;"/></td><td colspan="2"></td>
								</tr>
								<tr>
									<td>'.__('Meer producten tonen button','DaisyconPlugin').'</td><td><input type="checkbox" name="aMoreproducts" id="aMoreproducts" /></td><td colspan="2"></td>
								</tr>
								<tr>
								<td>
									'.__('Stylesheet','DaisyconPlugin').'
								</td>
									<td>
										<select name="stylesheet" class="stylesheet" id="stylesheet">
											<option value="">'.__('Selecteer een stylesheet...','DaisyconPlugin').'</option>
					';
		foreach($Asheets as $Rsheets){
			$output .=	'
											<option value="'.$Rsheets->stylesheet_id.'">'.$Rsheets->name.'</option>
											
						';
		}
		
		$output .=	'
										</select>
									</td>
									<td colspan="2">
									</td>
								</tr>
								<tr>
									<td>'.__('Selecteer programma&lsquo;s waarin u wil zoeken','DaisyconPlugin').'</td>
									<td>
										<select id="aStores" name="aStores" multiple>
					';
		
		foreach($Aprograms as $Rprograms){
			$output .=	'
											<option value="'.$Rprograms->program_id.'">'.$Rprograms->name.'</option>
											
						';
		}
		$output .=	'
										</select>
									</td>
									<td>
										<input type="button" id="src2TargetAll" name="src2TargetAll" value=">>"/><br/>
										<input type="button" id="src2Target" name="src2Target" value=">"/><br/>
										<input type="button" id="target2Src" name="target2Src" value="<"/><br/>
										<input type="button" id="target2SrcAll" name="target2SrcAll" value="<<"/><br/>
									</td>
									<td>
										<select id="aSelectedstores" name="aSelectedstores" multiple></select>
									</td>
								</tr>
								<tr>
									<td>
										'.__('Sorteren op','DaisyconPlugin').'
									</td>
									<td>
										<select id="aOrder" style="width: 200px;">
											<option value="abc">'.__('Alfabet','DaisyconPlugin').'</option>
											<option value="low">'.__('Prijs (laag > hoog)','DaisyconPlugin').'</option>
											<option value="high">'.__('Prijs (hoog > laag)','DaisyconPlugin').'</option>
											<option value="rand">'.__('Random','DaisyconPlugin').'</option>
										</select>
									</td>
									<td colspan="2">
									</td>
								</tr>
								<tr>
									<td>
										<input type="button" id="shorttagauto" class="button" value="'.__('Genereer shorttag','DaisyconPlugin').'" />
										<input type="button" id="examplebutton" class="button" value="'.__('Voorbeeld','DaisyconPlugin').'" />
										
									</td>
								</tr>
							</form>
						</tbody>
					</table>
					<div id="result_shorttagauto" onclick="select_all(this)" style="cursor:pointer;">'.__('Klik op voorbeeld om een voorbeeld te zien van de door jou gekozen instellingen. Klik daarna op "Genereer shorttag" om hier de shorttag te genereren die je in de HTML van je website kan plaatsen.','DaisyconPlugin').' </div>
					<div id="result_example"></div>
					</div>
<script type="text/javascript">

	function sureTransfer(from, to, all) {
		if ( from.getElementsByTagName && to.appendChild ) {
			while ( getCount(from, !all) > 0 ) {
				transfer(from, to, all);
			}
		}
	}


	function getCount(target, isSelected) {
		var options = target.getElementsByTagName("option");
		if ( !isSelected ) {
			return options.length;
		}
		var count = 0;
		for ( i = 0; i < options.length; i++ ) {
			if ( isSelected && options[i].selected ) {
				count++;
			}
		}
		return count;
	}
	
	function transfer(from, to, all) {
		if ( from.getElementsByTagName && to.appendChild ) {
			var options = from.getElementsByTagName("option");
			for ( i = 0; i < options.length; i++ ) {
				if ( all ) {
					to.appendChild(options[i]);
				} else {
					if ( options[i].selected ) {
						to.appendChild(options[i]);
					}
				}
			}
		}
	}
	
	window.onload = function() {
		document.getElementById("src2TargetAll").onclick = function() {
			sureTransfer(document.getElementById("aStores"), document.getElementById("aSelectedstores"), true);
		};
		document.getElementById("src2Target").onclick = function() {
			sureTransfer(document.getElementById("aStores"), document.getElementById("aSelectedstores"), false);
		};
		document.getElementById("target2SrcAll").onclick = function() {
			sureTransfer(document.getElementById("aSelectedstores"), document.getElementById("aStores"), true);
		};
		document.getElementById("target2Src").onclick = function() {
			sureTransfer(document.getElementById("aSelectedstores"), document.getElementById("aStores"), false);
		};
	}
</script>
						';
			
		return($output);
	}
	
public function adminStylesheets(){
		
		global $wpdb;
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		echo '<script type="text/javascript">
						jQuery.ajax
						({
							url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=stylesheet&jsoncallback=?",
							dataType: "jsonp",
							cache: false,
							success: function(html)
							{
							} 
						});
				</script>';
		
		wp_enqueue_script('color', plugins_url('/files/js/functions.js',__FILE__) );
		wp_enqueue_script('functions', plugins_url('/files/js/jscolor.js',__FILE__) );
		wp_enqueue_script('widget', includes_url('js/jquery/ui/jquery.ui.widget.min.js',__FILE__));
		wp_enqueue_script('mouse', includes_url('js/jquery/ui/jquery.ui.mouse.min.js',__FILE__));
		wp_enqueue_script('accor', includes_url('js/jquery/ui/jquery.ui.accordion.min.js',__FILE__));
		wp_enqueue_script('cor', includes_url('js/jquery/ui/jquery.ui.core.min.js',__FILE__));
		wp_enqueue_script('dragg', includes_url('js/jquery/ui/jquery.ui.draggable.min.js',__FILE__));
		wp_enqueue_script('droppa', includes_url('js/jquery/ui/jquery.ui.droppable.min.js',__FILE__));
		wp_enqueue_script('sorta', includes_url('js/jquery/ui/jquery.ui.sortable.min.js',__FILE__));
				
		if (isset($_POST['delete'])){
			$Asheet = $wpdb->query("DELETE FROM stylesheets WHERE stylesheet_id = '".$_POST['stylesheet_id']."'");
		} 

		if (isset($_POST['getdefault'])){
			$dropSheet = $wpdb->query("TRUNCATE TABLE stylesheets;");
						
			$Asheet = $wpdb->query("INSERT IGNORE INTO `stylesheets` (`stylesheet_id`, `name`, `bordercolor`, `backgroundcolor`, `textcolor`, `align`, `store`, `store_before`, `store_button_program`, `price`, `price_before`, `size`, `width`, `height`, `button_store`, `buttoncolor`, `buttonbordercolor`, `buttontextcolor`, `view`, `float`, `price_button`, `moreproducts_color`, `moreproducts_font`, `moreproducts_text`) VALUES
										(1, 'Tabelweergave (voorbeeld 1)', 'E3E3E3', 'FFFFFF', '000000', 'left', 1, '', 'before', 1, 'Prijs: ', 70, '400', '80', 0, 'E86400', 'FFFFFF', 'FFFFFF', '0', 0, 1, 000000, FFFFFF, 'Klik hier om meer producten te laden'),
										(2, 'Tegelweergave (voorbeeld 2)', 'CEC9D1', 'FFFFFF', '000000', 'center', 1, '', 'before', 1, '', 50, '100', '300', 0, 'FA8F02', '332B1F', 'FFFFFF', '1', 1, 1, 000000, FFFFFF, 'Klik hier om meer producten te laden');");
		
			
			echo 	'
		    		<div id="message" class="Msuccess">
						<p>
							<b>'.__('De voorbeeld stylesheets zijn gedownload! Deze zijn te vinden onder de namen Tabelweergave en Tegelweergave.','DaisyconPlugin').'</b>
						</p>
					</div>
		    		';
			
		}
		
		$view = self::adminStyleView();
		echo $view;
		
	}
	

	
	public function adminActiecodes(){

		global $wpdb;
			
		if(isset($_POST['updateactiecodes'])){	
			
			include('cron/importActioncodes.php');

			$date = date("Y-m-d H:i:s");
			$wpdb->update('publisher', array('actioncode_date' => $date), array('daisycon_id' => '1'));

		}

			$view = self::adminActiecodesView();
			echo $view;

	}

public function adminStyleView(){
		
		global $wpdb;		
		$publisher = $wpdb->get_row("SELECT * FROM publisher");
		$wi = explode('/media/', $publisher->feed);
		$wi = explode('/', $wi[1]);
		
		$height = "150"; 
		$width = "400"; 
		
		$name = ""; 
		$view = "0"; 
		$float = "1"; 
		$background = ""; 
		$border = ""; 
		$text = "";
		$size = "70"; 
		$align = "center"; 
		$store = ""; 
		$store_before = ""; 
		$button_store = "disabled"; 
		$button_color = ""; 
		$button_border_color = ""; 
		$button_text_color = ""; 
		$price = ""; 
		$price_button = ""; 
		$price_before = ""; 
		$store_button_program = "";
		$result = $wpdb->get_row("SELECT * FROM products ORDER BY RAND()");
		
		if(isset($_POST['save'])){
			$stylesheet_id = $_POST['stylesheet'];
			
			$name = $_POST['name']; 
			$bordercolor = $_POST['border']; 
			$backgroundcolor = $_POST['background']; 
			$textcolor = $_POST['textcolor']; 
			$align = $_POST['align']; 
			$store = $_POST['store']; 
			$price = $_POST['price']; 
			$view = $_POST['view']; 
			$size = $_POST['size'];
			$width = $_POST['width'];
			$height = $_POST['height'];
			$store_before = $_POST['before_store'];
			$price_before = $_POST['before-price'];
			$button_store = $_POST['button_store_'];
			$buttoncolor = $_POST['buttoncolor'];
			$buttonbordercolor = $_POST['buttonbordercolor'];
			$buttontextcolor = $_POST['buttontextcolor'];
			$float = $_POST['float'];
			$price_button = $_POST['price_button'];
			$store_button_program = $_POST['store_button_program'];
			$moreproducts_color = $_POST['moreproducts_color'];
			$moreproducts_text = $_POST['moreproducts_text'];
			$moreproducts_font = $_POST['moreproducts_font'];
			
			
		}elseif(isset($_POST['load'])){
			$stylesheet_id = $_POST['stylesheet'];
			
			if(!empty($stylesheet_id)){
			
				$Asheet = $wpdb->get_row("SELECT * FROM stylesheets WHERE stylesheet_id = '".$stylesheet_id."'");
				
				$name = $Asheet->name;
				$background = $Asheet->backgroundcolor;
				$border = $Asheet->bordercolor;
				$text = $Asheet->textcolor;
				$view = $Asheet->view;
				$float = $Asheet->float;
				$size = $Asheet->size;
				$width = $Asheet->width;
				$height = $Asheet->height;
				$align = $Asheet->align;
				$store = $Asheet->store;
				$store_before = $Asheet->store_before; 
				$price = $Asheet->price;
				$price_before = $Asheet->price_before;
				$price_button = $Asheet->price_button;
				$button_store = $Asheet->button_store;
				$button_color = $Asheet->buttoncolor;
				$button_border_color = $Asheet->buttonbordercolor;
				$button_text_color = $Asheet->buttontextcolor;
				$store_button_program = $Asheet->store_button_program;
				$moreproducts_text = $Asheet->moreproducts_text;
				$moreproducts_color = $Asheet->moreproducts_color;
				$moreproducts_font = $Asheet->moreproducts_font;
			}
		}
		
		
		if (isset($_POST['load'])){

			if($Asheet->width == NULL){
				$Asheet->width = 400;
			}
			
			if($Asheet->height == NULL){
				$Asheet->height = 150;
			}
			
			if($Asheet->size == NULL){
				$Asheet->size = 70;
			}			
		
			echo'
		
				<script type="text/javascript">				
				jQuery(document).ready(function(){
					var category = "'.$result->category.'";
					var program = "'.$result->program_id.'";
					var background = "'.$Asheet->backgroundcolor.'";
					var border = "'.$Asheet->bordercolor.'";
					var view = "'.$Asheet->view.'";
					var align = "'.$Asheet->align.'";
					var before_price = "'.$Asheet->price_before.'";
					var size = "'.$Asheet->size.'";
					var width = "'.$Asheet->width.'";;
					var height = "'.$Asheet->height.'";
					var textcolor = "'.$Asheet->textcolor.'";
					var button_color = "'.$Asheet->buttoncolor.'";
					var button_text_color = "'.$Asheet->buttontextcolor.'";
					var button_border_color = "'.$Asheet->buttonbordercolor.'";
					var before_store = "'.$Asheet->store_before.'";
					var store = "'.$Asheet->store.'";
					var store_button_program = "'.$Asheet->store_button_program.'";
					var float = "'.$Asheet->float.'";
					var button_store = "'.$Asheet->button_store.'";
					var button_price = "'.$Asheet->price_button.'";
					var price = "'.$Asheet->price.'";
					
					var dataString = "amount=3&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&border=" + border + "&background=" + background + "&size=" + size + "&height=" + height + "&width=" + width + "&align=" + align + "&textcolor=" + textcolor + "&price=" + price + "&store=" + store + "&before_store=" + before_store + "&before_price=" + before_price + "&button_store=" + button_store + "&button_color=" + button_color + "&button_border_color=" + button_border_color + "&button_text_color=" + button_text_color + "&view=" + view + "&button_price=" + button_price + "&float=" + float + "&store=" + store + "&store_button_program=" + store_button_program;
					jQuery("#sortable1").html("Stylesheet voorbeeld wordt geladen!");
					jQuery.ajax
						({
							type: "POST",
							url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
							data: dataString,
							cache: false,
							success: function(html)
							{
								jQuery("#example").html(html);
							} 
					});
				});
				</script>
				
				';
		}
		
		
		$output =	'	
						<script type="text/javascript">
						setTimeout(function() {
    						jQuery("#message1").fadeOut("fast");
						}, 2000); // <-- time in milliseconds
						
						var program = "'.$result->program_id.'";
						var category = "'.$result->category.'";
						var subcategory = "";
						var amount = "1";
						var productlist = [];
						var stylesheet ="";
						var background = "";
						var border = "";
						var view = "1";
						var align = "";
						var size = "";
						var width = "400";
						var height = "150";
						var textcolor = "";
						var before_store = "";
						var before_price = "";
						var button_price = "";
						var float = "";
						var button_store = "";
						var button_color = "";
						var button_border_color = "";
						var button_text_color = "";
						var store_button_program = "";
						var dataString = "";
						
						function setVars(){
							background = jQuery("#background").val();
							border = jQuery("#border").val();
							view = jQuery("#view").val();
							align = jQuery("#align").val();
							before_price = jQuery("#before-price").val();
							size = jQuery("#size").val();
							width = jQuery("#width").val();
							height = jQuery("#height").val();
							textcolor = jQuery("#textcolor").val();
							border = jQuery("#border").val();
							button_color = jQuery("#buttoncolor").val();
							button_text_color = jQuery("#buttontextcolor").val();
							button_border_color = jQuery("#buttonbordercolor").val();
							background = jQuery("#background").val();
							before_store = jQuery("#before-store").val();
							store_button_program = jQuery("#store_button_program").val();
							
							if(jQuery("input[name=float]").is(":checked") == true){
								float = 1;
							}else{
								float = 0;
							} 
							
							if(jQuery("input[name=button-store]").is(":checked") == true){
								button_store = 1;
							}else{
								button_store = 0;
							}
							if(jQuery("input[name=price]").is(":checked") == true){
								price = 1;
							}else{
								price = 0;
							}
							if(jQuery("input[name=store]").is(":checked") == true){
								store = 1;
							}else{
								store = 0;
							}
							if(jQuery("input[name=price_button]").is(":checked") == true){
								button_price = 1;
							}else{
								button_price = 0;
							}
							
							dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&border=" + border + "&background=" + background + "&size=" + size + "&align=" + align + "&textcolor=" + textcolor + "&price=" + price + "&store=" + store + "&before_store=" + before_store + "&before_price=" + before_price + "&button_store=" + button_store + "&button_price=" + button_price+ "&float=" + float + "&store_button_program=" + store_button_program + "&height=" + height + "&width=" + width + "&view=" + view+ "&align=" + align + "&button_color=" + button_color + "&button_border_color=" + button_border_color + "&button_text_color=" + button_text_color;
						}
						
						jQuery(document).ready(function()
						{
							
							jQuery(".program").change(function()
							{
								setVars();
							
																				
								jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=category",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery(".category").html(html);
									} 
							});
						});
						
						jQuery("#result").click(function()
						{
							
							var output = "[daisycon_products program_id=&quot;"+products.elements["program"].value+"&quot; amount=&quot;"+products.elements["amount"].value+"&quot; category=&quot;"+products.elements["category"].value+"&quot; subcategory=&quot;"+products.elements["subcategory"].value+"&quot; products=&quot;"+productlist+"&quot; stylesheet=&quot;"+products.elements["stylesheet"].value+"&quot;]";
							jQuery(this).html(output);
						});
						
						jQuery(".stylesheet").change(function()
						{

						});
						
						if(view == 1){
							float = 1;
							jQuery("#float").show();
						}else{
							float = 0;
							jQuery("#float").hide();
						}
						
						jQuery("#view").change(function()
						{
							setVars();
							
							if(view == 1){
								width = 150;
								height = 250;
								jQuery("#width").val(150);
								jQuery("#height").val(250);
								float = 1;
								jQuery("#float").show();
								jQuery("#button-store").show();
								jQuery("#store_button_program").show();
								jQuery("#before-store").show();
							}else{
								width = 500;
								height = 100;
								jQuery("#width").val(500);
								jQuery("#height").val(100);
								float = 0;
								jQuery("#float").hide();
								jQuery("#button-store").hide();
								jQuery("#store_button_program").hide();
								jQuery("#before-store").hide();
							}
								
							dataString = "amount="+ amount + "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&border=" + border + "&background=" + background + "&size=" + size + "&align=" + align + "&textcolor=" + textcolor + "&price=" + price + "&store=" + store + "&before_store=" + before_store + "&before_price=" + before_price + "&button_store=" + button_store + "&button_price=" + button_price+ "&float=" + float + "&store_button_program=" + store_button_program + "&height=" + height + "&width=" + width + "&view=" + view+ "&align=" + align + "&button_color=" + button_color + "&button_border_color=" + button_border_color + "&button_text_color=" + button_text_color;
							
							jQuery.ajax
								({
									type: "POST",
									url: "http://wordpress/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#textcolor").change(function()
						{
							setVars(); 
														
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#buttoncolor").change(function()
						{
							setVars();
														
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#buttontextcolor").change(function()
						{
							setVars();
							
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#buttonbordercolor").change(function()
						{
							setVars();
							
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#button-store").change(function()
						{
							setVars();
														
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#before-store").change(function()
						{
							setVars();
														
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#before-price").change(function()
						{
							setVars();
														
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#border").change(function()
						{
							setVars();
																					
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#store").change(function()
						{
							setVars();
																					
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#price").change(function()
						{
							setVars();
																					
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#background").change(function()
						{
							setVars();
																					
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#stylesheet").change(function()
						{
											
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#size").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#height").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#width").change(function()
						{
							setVars();
																	
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#align").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#float").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#store_button_program").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
						
						jQuery("#price_button").change(function()
						{
							setVars();
																						
							jQuery.ajax
								({
									type: "POST",
									url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=styleresult",
									data: dataString,
									cache: false,
									success: function(html)
									{
										jQuery("#example").html(html);
									} 
							});
						});
		});
						
						
						
										
					</script>
					
					<script type="text/javascript">
jQuery(document).ready(function(e) {
    jQuery(".stylesheet").change(function(){
        var textval = jQuery(":selected",this).val();
        jQuery("input[name=stylesheet_id]").val(textval);
    })
    
    var mail = document.getElementById("name");
    
	jQuery("#namestyle").keyup(function() {
	    mail.value = this.value;
	});

});					
					
function stylesheetUpdate(){
			name = jQuery("#name").val();
			background = jQuery("#background").val();
			border = jQuery("#border").val();
			view = jQuery("#view").val();
			align = jQuery("#align").val();
			before_price = jQuery("#before-price").val();
			size = jQuery("#size").val();
			width = jQuery("#width").val();
			height = jQuery("#height").val();
			textcolor = jQuery("#textcolor").val();
			border = jQuery("#border").val();
			button_color = jQuery("#buttoncolor").val();
			button_text_color = jQuery("#buttontextcolor").val();
			button_border_color = jQuery("#buttonbordercolor").val();
			before_store = jQuery("#before-store").val();
			store_button_program = jQuery("#store_button_program").val();
			moreproducts_color = jQuery("#moreproducts_color").val();
			moreproducts_font = jQuery("#moreproducts_font").val();
			moreproducts_text = jQuery("#moreproducts_text").val();
			

			if(jQuery("input[name=float]").is(":checked") == true){
				float = 1;
			}else{
				float = 0;
			} 

			if(jQuery("input[name=button-store]").is(":checked") == true){
				button_store = 1;
			}else{
				button_store = 0;
			}
			if(jQuery("input[name=price]").is(":checked") == true){
				price = 1;
			}else{
				price = 0;
			}
			if(jQuery("input[name=store]").is(":checked") == true){
				store = 1;
			}else{
				store = 0;
			}
			if(jQuery("input[name=price_button]").is(":checked") == true){
				button_price = 1;
			}else{
				button_price = 0;
			}

var dataString = "amount=" + amount + "&name=" + name +  "&category=" + category + "&program=" + program + "&subcategory=" + subcategory + "&border=" + border + "&background=" + background + "&size=" + size + "&align=" + align + "&textcolor=" + textcolor + "&price=" + price + "&store=" + store + "&before-store=" + before_store + "&before-price=" + before_price + "&button-store=" + button_store + "&price_button=" + button_price+ "&float=" + float + "&store_button_program=" + store_button_program + "&height=" + height + "&width=" + width + "&view=" + view+ "&align=" + align + "&buttoncolor=" + button_color + "&moreproducts_color=" + moreproducts_color + "&moreproducts_font=" + moreproducts_font + "&moreproducts_text=" + moreproducts_text + "&buttonbordercolor=" + button_border_color + "&buttontextcolor=" + button_text_color;
			
				jQuery.ajax
				({
					type: "POST",
					url: "'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/output.php?action=updateStylesheet",
					data: dataString,
					cache: false,
					success: function(html)
					{
					
						var input = jQuery("#name").val();
    						if(input != ""){
							alert("'.__('Succesvol opgeslagen.','DaisyconPlugin').'");
						}else{
						 	alert("'.__('Niet opgeslagen. Je moet de stylesheet nog een naam geven.','DaisyconPlugin').'");
						}
					} 
				});
				}
					
					</script>
					';
		$Asheets = $wpdb->get_results("SELECT * FROM stylesheets");
	
		$output .=	'
					<div class="wrap">
						<h2>'.( 'Stylesheets' ).'</h2>
						'.__('Door middel van Stylesheets is het mogelijk om de output van de shorttags aan te passen naar eigen voorkeur. Bij het maken en wijzigen van de stylesheets zullen de wijzigingen in de vorm van een preview zichtbaar worden.','DaisyconPlugin').'					</div>
					
					';
					
					
		$output .= '<form action="" method="post" name="form" onsubmit="return confirmResetStylesheets();">		
					'.__('Kom je er even niet meer uit? Klik op de button om alle stylesheets te verwijderen en de voorbeeld stylesheets opnieuw te laden:','DaisyconPlugin').' <input type="submit" value="'.__('Terug naar begininstellingen','DaisyconPlugin').'" id="getdefault" name="getdefault" /></form>
					
					<br/>
					'.__('We raden je aan om de voorbeeld stylesheets te gebruiken, en aan te passen naar je voorkeur.','DaisyconPlugin').'
					<br/><br/>
					<form action="" method="post" name="form">
					<table class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col" colspan="4">
								&nbsp;
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
							<tr>
								<td>
									'.__('Selecteer een stylesheet...','DaisyconPlugin').'
								</td>
								<td>
									<select name="stylesheet" class="stylesheet" id="stylesheet">
										<option value="">'.__('Selecteer een stylesheet...','DaisyconPlugin').'</option>
					';
		foreach($Asheets as $Rsheets){
			$output .=	'
										<option value="'.$Rsheets->stylesheet_id.'">'.$Rsheets->name.'</option>
											
						';
		}
		
		$output .=	'
									</select><input type="submit" value="'.__('Laden','DaisyconPlugin').'" id="load" name="load" /></form>
								</td>
								<td><form action="" method="post" name="form" onsubmit="return confirmDelete();"><input type="hidden" name="stylesheet_id" /><input type="submit" value="'.__('Verwijderen','DaisyconPlugin').'" id="delete" name="delete"/></form>
								</td>
								<td>
								<form action="" method="post" name="form"><input type="submit" value="'.__('Nieuwe stylesheet maken','DaisyconPlugin').'" id="load" name="load" />
								</td>
							</tr>
							</tbody>
							</table>';
		
if(isset($_POST['load'])){
			$output .= ' <input type="hidden" name="name" id="name" value="'.$name.'">
							<table class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
							<tbody>';
			if(empty($stylesheet_id)){
			$output .= '
							<tr>
								<td>
									'.__('Naam nieuwe stylesheet','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" name="namestyle" id="namestyle" value="'.$name.'">
								</td>
							</tr>';
			}else{
			$output .= '
							<tr>
								<td>
									'.__('Je bewerkt nu:','DaisyconPlugin').'
								</td>
								<td>
									<strong>'.$name.'</strong>
								</td>
							</tr>';
			}
			
			$output .= '
							<tr>
								<td>
									Design
								</td>
								<td>
									<select name="view" id="view">
										<option value="0"'; if ($view == "0"){ $output .= 'selected';} $output .= '>'.__('Tabelweergave','DaisyconPlugin').'</option>
										<option value="1"'; if ($view == "1"){ $output .= 'selected';} $output .= '>'.__('Tegelweergave','DaisyconPlugin').'</option>
									</select>
								</td>
							</tr>';
			$output .= '
								<tr>
								<td>
									'.__('Float (naast elkaar of onder elkaar)','DaisyconPlugin').'
								</td>
								<td>
									<input type="checkbox" name="float" id="float" value="1" '; if ($float == "1"){ $output .= 'checked';} $output .= ' />
								</td>
							</tr>';			
					
			$output .= '
							<tr>
								<td>
									'.__('Achtergrondkleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="background" name="background" value="'.$background.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Border','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="border" name="border" value="'.$border.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Tekstkleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="textcolor" name="textcolor" value="'.$text.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Hoogte in pixels (tabel)','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" id="height" name="height" value="'.$height.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Breedte in pixels (tabel)','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" id="width" name="width" value="'.$width.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Grootte product image in pixels','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" id="size" name="size" value="'.$size.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Uitlijnen / align','DaisyconPlugin').'
								</td>
								<td>
									<select name="align" id="align">
										<option value="left"'; if ($align == "left"){ $output .= 'selected';} $output .= '>'.__('Links','DaisyconPlugin').'</option>
										<option value="center"'; if ($align == "center"){ $output .= 'selected';} $output .= '>'.__('Midden','DaisyconPlugin').'</option>
										<option value="right"'; if ($align == "right"){ $output .= 'selected';} $output .= '>'.__('Rechts','DaisyconPlugin').'</option>
									</select>
								</td>
							<thead>
						<tr>
							<th class="manage-column column-name"  scope="col" colspan="3">
								<span>'.__('Instellingen','DaisyconPlugin').'</span>
							</th>
						</tr>
						</thead>
							<tr>
								<td>
									'.__('Adverteerder logo tonen','DaisyconPlugin').'
								</td>
								<td>
									<input type="checkbox" name="store" id="store" value="1" '; if ($store == "1"){ $output .= 'checked';} $output .= ' />
								</td>
							</tr>
							<tr>
								<td>
									'.__('Link tekst (leeglaten is geen tekst)','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" name="before-store" id="before-store" value="'.$store_before.'"  /> 
									<select name="store_button_program" id="store_button_program">
										<option value="before"'; if ($store_button_program == "before"){ $output .= 'selected';} $output .= '>'.__('Programmanaam voor tekst','DaisyconPlugin').'</option>
										<option value="after"'; if ($store_button_program == "after"){ $output .= 'selected';} $output .= '>'.__('Programmanaam na tekst','DaisyconPlugin').'</option>
										<option value="disabled"'; if ($store_button_program == "disabled"){ $output .= 'selected';} $output .= '>'.__('Programmanaam niet weergeven','DaisyconPlugin').'</option>
									</select>
								</td>
							</tr>
							<tr>
								<td> 
									'.__('Button om linktekst heen zetten','DaisyconPlugin').'
								</td>
								<td>
									<input type="checkbox" name="button-store" id="button-store" value="1" '; if ($button_store == "1"){ $output .= 'checked';} $output .= ' />
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button kleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="buttoncolor" name="buttoncolor" value="'.$button_color.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button borderkleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="buttonbordercolor" name="buttonbordercolor" value="'.$button_border_color.'">
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button tekstkleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" id="buttontextcolor" name="buttontextcolor" value="'.$button_text_color.'">
								</td>
							</tr>
							<thead>
						<tr>
							<th class="manage-column column-name"  scope="col" colspan="3">
								<span>'.__('Prijs','DaisyconPlugin').'</span>
							</th>
						</tr>
						</thead>
							<tr>
								<td>
									'.__('Prijs tonen (ja/nee)','DaisyconPlugin').'
								</td>
								<td>
									<input type="checkbox" name="price" id="price" value="1" '; if ($price == "1"){ $output .= 'checked';} $output .= ' />
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button om prijs heen zetten','DaisyconPlugin').'
								</td>
								<td>
									<input type="checkbox" name="price_button" id="price_button" value="1" '; if ($price_button == "1"){ $output .= 'checked';} $output .= ' />
								</td>
							</tr>
							<tr>
								<td>
									'.__('Tekst voor prijs','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" name="before-price" id="before-price" value="'.$price_before.'"  />
								</td>
							</tr>

						<thead>
						<tr>
							<th class="manage-column column-name"  scope="col" colspan="3">
								<span>'.__('Meer producten tonen button (optioneel)','DaisyconPlugin').'</span>
							</th>
						</tr>
						</thead>
							<tr>
								<td>
									'.__('Button kleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" name="moreproducts_color" id="moreproducts_color" value="'.$moreproducts_color.'"  />
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button testkleur','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" class="color" name="moreproducts_font" id="moreproducts_font" value="'.$moreproducts_font.'"  />	
								</td>
							</tr>
							<tr>
								<td>
									'.__('Button tekst','DaisyconPlugin').'
								</td>
								<td>
									<input type="textfield" name="moreproducts_text" id="moreproducts_text" value="'.$moreproducts_text.'"  />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="button" value="'.__('Stylesheet opslaan','DaisyconPlugin').'" name="updateStylesheet" onclick="stylesheetUpdate()">
								</td>
							</tr>
						</tbody>
					</table>
					</form>';
			
		if($Asheet->view == 0){
			$output .= '<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#float").hide();
				jQuery("#button-store").hide();
				jQuery("#store_button_program").hide();
				jQuery("#before-store").hide();
			});
			</script>';
			}
			
			$output .= '
					<div class="demo">
					    <ul id="example" class="dropfalse" style="width:100%; padding:10px; position: relative; float: left; width: relative; background:#F0F0F0; margin-bottom:10px; width:95%;">'.__('Hier komt het voorbeeld van de stylesheet','DaisyconPlugin').'</ul> <br/>
					</div>
					';
		}
			return($output);
	}
	
	public function adminActiecodesView(){
	
			global $wpdb;
			$publisher = $wpdb->get_row("SELECT * FROM publisher");
			$wi = explode('/media/', $publisher->feed);
			$wi = explode('/', $wi[1]);
			
			wp_enqueue_script('tablesorter', plugins_url('/files/js/jquery.tablesorter.min.js',__FILE__) );
			wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
			
			echo ' 	
					<div class="wrap">
						<h2>'.__('Actiecodes','DaisyconPlugin').'</h2>
						'.__('Plak de shorttag(s) in een pagina of blogpost. De actiecode wordt vervolgens automatisch op deze pagina getoond. Klik <a href="admin.php?page=Daisycon" alt="Daisycon gegevens">hier</a> om in te stellen wat er moet gebeuren als een actiecode verlopen is.','DaisyconPlugin').'<br/><br/>
					
						';

			$Actioncodefeed = $wpdb->get_row("SELECT * FROM publisher");
			
					$Actioncodefeed = $wpdb->get_row("SELECT * FROM publisher");	
					$rActioncodes = $wpdb->get_results("SELECT * FROM actioncodes");			
					if(strlen($Actioncodefeed->actiecodefeed) > 0){
						echo '<form action="" name="updateactiecodes" method="post">';
							if($Actioncodefeed->program_date != '0000-00-00 00:00:00' && $Actioncodefeed->actioncode_date == '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'

									<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($Actioncodefeed->actioncode_date != '0000-00-00'){$output.= 'color:#FF6600';} $output.= '" onclick="loadingDiv2()"/> 

									<span style="color:#FF6600;line-height: 24px;margin-left: 10px;">
									'.__('Druk op de button om de actiecodes waarbij je bent aangemeld op te halen.','DaisyconPlugin').'
									</span>

								';
							}elseif($Actioncodefeed->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) > 0){
								$output	.=	'

										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($Actioncodefeed->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 

									<span style="color:#006400;line-height: 24px;margin-left: 10px;">
									'.__('Laatste update','DaisyconPlugin').': '.self::makeDate($Actioncodefeed->actioncode_date).'
									</span>
';
							}elseif($Actioncodefeed->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($Actioncodefeed->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 
									<span style="line-height: 24px;margin-left: 10px;">
										'.__('Geen actiecodes gevonden bij de opgehaalde programma&#39;s.','DaisyconPlugin').'
									</span>

									';
								
							}else{
		
								$output	.=	'
									<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($Actioncodefeed->actioncode_date != '0000-00-00'){$output.= 'color:#000000';} $output.= '" onclick="loadingDiv2()" disabled /> 
									<span style="color:#FF6600;line-height: 24px;margin-left: 10px;">
									'.__('Haal eerst je programma&#39;s op.','DaisyconPlugin').'
									</span>

									
								';
							}
				}else{
					$output .= '<span style="line-height: 24px;margin-left: 10px;">'.__('Sla eerst je actiecodelijst op!','DaisyconPlugin').'</span>';
				}									
						echo $output;
						echo '<form>';
						
			$Aactiecodes = $wpdb->get_results("SELECT * FROM actioncodes INNER JOIN programs ON actioncodes.program_id = programs.program_id ORDER BY actioncodes.date_end DESC");
														
					echo '<br/><br/></div>		
					<table id="sortTable" class="wp-list-table widefat fixed bookmarks tablesorter" cellspacing="0" style="width:98%;">
						<thead class="tableHeader">
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col">
								<a href="#"><span>'.__('Adverteerder','DaisyconPlugin').'</span> </a>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<span>'.__('Omschrijving','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<span>'.__('Geldig van','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<span>'.__('Geldig tot','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<span>'.__('Actiecode','DaisyconPlugin').'</span>
							</th>
							<th class="manage-column column-name sortable desc"  scope="col">
								<span>'.__('Shorttag','DaisyconPlugin').'</span>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>

					<tbody>							
						';


				foreach($Aactiecodes as $Ractie){	
							
				if($Ractie->date_end == "0000-00-00"){
					$date_end = __('Geen einddatum');
				}else{
					$date_end = $Ractie->date_end;
				}
								
				$ex = explode('-', $Ractie->date_end);

				echo '
							<tr>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'>
									'.$Ractie->name.'';
				echo '
								</td>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'>
									'.$Ractie->actioncode_title.'
								</td>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'>
									'.$Ractie->date_start.'
								</td>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'> 
									'.$date_end.'
								</td>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'>
									'.$Ractie->actioncode.'
								</td>
								<td '; if(mktime(0,0,0,$ex[1],$ex[2],$ex[0]) < mktime(0,0,0,date("m"),date("d"),date("Y")) && $Ractie->date_end != "0000-00-00"){echo ' style="color:#CC0000"';} echo'>
									<div onclick="select_all(this)" style="cursor:pointer;">[daisycon_actioncode id='.$Ractie->actioncode_id.']</div>
								</td>						
								</tr>
								';		
						
								}
										
						echo '			
						</tbody>
					</table>
					<script type="text/javascript">
						jQuery.ajax
						({
							url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=actiecodes&jsoncallback=?",
							dataType: "jsonp",
							cache: false,
							success: function(html)
							{
							} 
						});
					</script>
					';
						
				if(isset($_POST['updateactiecodes'])){
				$date = date("Y-m-d H:i:s");
		
				$wpdb->update('publisher', array('actioncode_date' => $date), array('daisycon_id' => '1'));
						
				include('cron/importActioncodes.php');					
				}	
	}
	
	
	public function adminGetFeeds(){
		
			$view = self::adminGetFeedsView();
			
			echo $view;

	}

	public function adminAccountDetailsView(){
		global $wpdb;
		
		wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
		
		$rFeeds = $wpdb->get_row("SELECT * FROM publisher");
		$rPrograms = $wpdb->get_results("SELECT * FROM programs");	
		$rActioncodes = $wpdb->get_results("SELECT * FROM actioncodes");		
		
if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
		if (isset($_POST['toevoegen'])){
		echo '';
		}else{	
			
	$output = '	

	<form method="post" action="">
						<table class="wp-list-table widefat bookmarks" cellspacing="0" style="width:98%;">
							<thead>
							<tr>
								<th class="manage-column column-name sortable desc"  scope="col" colspan="5">
									<a href="#"><span>'.__('Stap 3: Alles ophalen!','DaisyconPlugin').'</span></a>
								</th>
							</tr>
							</thead>
							<tfoot>
							</tfoot>
							<tbody>
								<tr>
									<td>
							
									';
				if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
							if($rFeeds->program_date == '0000-00-00 00:00:00' && count($rPrograms) == 0){
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#FF6600';} $output.= '" onclick="loadingDiv()"/> 
										
										</td>
									<td>
									<font color="#FF6600">
									'.__('Druk op de button om de programma&lsquo;s waarbij je bent aangemeld op te halen.','DaisyconPlugin').'
									</font>
									
								';
							}elseif($rFeeds->program_date != '0000-00-00 00:00:00' && count($rPrograms) > 0){
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv()"/> 
									</td>
									<td>
									<font color="#006400">
									'.__('Laatste update:','DaisyconPlugin').' '.self::makeDate($rFeeds->program_date).'
									</font>
									
								';
							}else{
								$output	.=	'
									
										<input type="submit" class="button" name="update" value="'.__('Programma&lsquo;s ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->program_date != '0000-00-00'){$output.= 'color:#000000';} $output.= '" onclick="loadingDiv()"/> 
									</td><td>
									'.__('Nog niet opgehaald.','DaisyconPlugin').'
									
								';
							}
				}else{
					$output	.=	__('Sla eerst je programmalijst en productlijst op!');
				}
						
				$output	.=	'
									 
									</td>
									<td>
									<div id="loadingDiv"  style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
									</td>
	
								</tr>
								<tr>

									<td>';
				if(strlen($rFeeds->actiecodefeed) > 0){
							if($rFeeds->program_date != '0000-00-00 00:00:00' && $rFeeds->actioncode_date == '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'
									
									<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#FF6600';} $output.= '" onclick="loadingDiv2()"/> 
									</td>
									<td>
									<font color="#FF6600">
									'.__('Druk op de button om de actiecodes waarbij je bent aangemeld op te halen.','DaisyconPlugin').'
									</font>
									
								';
							}elseif($rFeeds->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) > 0){
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 
									</td>
									<td>
									<font color="#006400">
									'.__('Laatste update:','DaisyconPlugin').' '.self::makeDate($rFeeds->actioncode_date).'
									</font>
									
								';
								}elseif($rFeeds->actioncode_date != '0000-00-00 00:00:00' && count($rActioncodes) == 0){
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#006400';} $output.= '" onclick="loadingDiv2()" /> 
									</td>
									<td>
									'.__('Geen actiecodes gevonden bij de opgehaalde programma&#39;s.','DaisyconPlugin').'
									';
							}else{
	
								$output	.=	'
									
										<input type="submit" class="button" name="updateactiecodes" value="'.__('Actiecodes ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= 'color:#000000';} $output.= '" onclick="loadingDiv2()" disabled /> 
									</td><td>
									<font color="#FF6600">
									'.__('Haal eerst je programma&#39;s op.','DaisyconPlugin').'
									</font>
									
								';
							}
				}else{
					$output .= __('Sla eerst je actiecodelijst op!');
				}
				
				$output	.=	'					
			
									</td>
									<td>
									<div id="loadingDiv2" style="display:none;"><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="20" alt="Even geduld..." /></div>
									</td>
	
								</tr>
								<tr>
	
									<td>
					</form>
					<form action="" method="post" onsubmit="return confirmProduct();">
									
							';

				if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
							if($rFeeds->program_date != '0000-00-00 00:00:00'){
								$output	.=	'
										<input type="submit" class="button" name="importallproducts" value="'.__('Alle producten ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= '';} $output.= '" />
									</td>
									<td>
									
									'.__('Druk op de button om alle producten van de programma&lsquo;s op te halen (Let op: dit kan enkele uren duren).','DaisyconPlugin').'
										';
							}else{
								$output	.=	'
										<input type="submit" class="button" name="importallproducts" value="'.__('Alle producten ophalen','DaisyconPlugin').'" style="width: auto;';if($rFeeds->actioncode_date != '0000-00-00'){$output.= '';} $output.= '" disabled />
									</td>
									<td>
									<font color="#FF6600">
									'.__('Haal eerst je programma&#39;s op.','DaisyconPlugin').'
									</font>	';	
							}
								
				}else{
					$output	.=	__('Sla eerst je programmalijst en productlijst op!');
				}
				
			$output	.=	'
									</form>
									</td>
	
								</tr>
							</tbody>
							</thead>
						</table><p>&nbsp;</p>';
			
				$output .= '<form method="post" action="">	
							<table class="wp-list-table widefat bookmarks" cellspacing="0" style="width:98%;">
							<thead>
							<tr>
								<th class="manage-column column-name sortable desc"  scope="col" colspan="5">
									<a href="#"><span>'.__('Optionele instellingen','DaisyconPlugin').'</span></a>
								</th>
							</tr>
							</thead>
							<tfoot>
							</tfoot>
							<tbody>
							<tr>
								<td>
										'.__('Verlopen actiecodes weergeven','DaisyconPlugin').' <a href="#" id="" title="'.__('Dit punt heeft alleen betrekking tot het menu Actiecodes','DaisyconPlugin').'">[?]</a>
									</td>
								<td>
									<select class="check" name="actioncodestatus" style="width:150px;">
										<option value="delete"';if($rFeeds->actioncode_status == "delete"){$output.= ' SELECTED';}$output.= '>'.__('Niet weergeven','DaisyconPlugin').'</option>
										<option value="alert"';if($rFeeds->actioncode_status == "alert"){$output.= ' SELECTED';}$output.= '>'.__('Wel weergeven, maar met melding','DaisyconPlugin').'</option>
									</select>
								</td>
								<td>
									
								</td>
								<td>
								</td>
							</tr>
							<tr>
									<td>
										'.__('eCPC voor programmalijst gebruiken JA / NEE (Nadat je dit hebt aangevinkt moet je opnieuw de programma&#39;s ophalen.)','DaisyconPlugin').' <a href="#" id="" title="'.__('Dit punt heeft betrekking tot de volgorde waarop programmas in een categorie worden getoond op je site. eCPC wordt opnieuw uitgerekend wanneer je de programmas update.','DaisyconPlugin').'">[?]</a>
									</td>
									<td>
										<input class="check" type="checkbox" name="api" value="1"'; if ($rFeeds->api == "1"){ $output .= ' checked';} $output .=' />
									</td>
									<td>
										
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										'.__('Automatisch links omzetten naar Daisycon affiliatelinks','DaisyconPlugin').'									
										</td>
									<td>
										<input class="check" type="checkbox" name="urlreplacer" value="1"'; if ($rFeeds->subid == "1"){ $output .= ' checked';} $output .=' style="margin-right: 10px;"><a href="http://www.daisycon.com/nl/blog/linkreplacer-wordpress-plugin" target="_blank">'.__('Lees info','DaisyconPlugin').'</a>

									</td>
									<td>
										
									</td>
									<td>
									</td>
								</tr>
									<tr>
									<td>
										'.__('Sneller producten ophalen? Hier kunt u de time-out verkorten. Let op! Dit is alleen voor snelle servers en op eigen risico.','DaisyconPlugin').' <a href="#" id="" title="'.__('De time-out is de tijd die er zit tussen het ophalen van 500 producten. Standaard staat dit op 10 seconden, waardoor het ook werkt op trage servers.','DaisyconPlugin').'">[?]</a>
									</td>
									<td>

										<select name="feed_timeout">
											<option value="1"'; if ($rFeeds->feed_timeout == "1"){ $output .= 'selected';} $output .= '>1 '.__('seconde','DaisyconPlugin').'</option>
											<option value="2"'; if ($rFeeds->feed_timeout == "2"){ $output .= 'selected';} $output .= '>2 '.__('seconden','DaisyconPlugin').'</option>
											<option value="3"'; if ($rFeeds->feed_timeout == "3"){ $output .= 'selected';} $output .= '>3 '.__('seconden','DaisyconPlugin').'</option>
											<option value="4"'; if ($rFeeds->feed_timeout == "4"){ $output .= 'selected';} $output .= '>4 '.__('seconden','DaisyconPlugin').'</option>
											<option value="5"'; if ($rFeeds->feed_timeout == "5"){ $output .= 'selected';} $output .= '>5 '.__('seconden','DaisyconPlugin').'</option>
											<option value="6"'; if ($rFeeds->feed_timeout == "6"){ $output .= 'selected';} $output .= '>6 '.__('seconden','DaisyconPlugin').'</option>
											<option value="7"'; if ($rFeeds->feed_timeout == "7"){ $output .= 'selected';} $output .= '>7 '.__('seconden','DaisyconPlugin').'</option>
											<option value="8"'; if ($rFeeds->feed_timeout == "8"){ $output .= 'selected';} $output .= '>8 '.__('seconden','DaisyconPlugin').'</option>
											<option value="9"'; if ($rFeeds->feed_timeout == "9"){ $output .= 'selected';} $output .= '>9 '.__('seconden','DaisyconPlugin').'</option>
											<option value="10"'; if ($rFeeds->feed_timeout == "10"){ $output .= 'selected';} $output .= '>10 '.__('seconden (default)','DaisyconPlugin').'</option>
										</select>
											
									</td>
									
									<td>
										
									</td>
									<td>
									</td>
								</tr>
					
								<tr>
									<td>
									
									</td>
									<td>
									
										<input type="hidden" value="'.$rFeeds->subid.'" name="subid" maxlength="43" />
									</td>
									<td>
									
									</td>
									<td>
									</td>
							</tr>	
															<tr>
									<td>
									<input type="submit" class="button" name="savehere" id="savehere" value="'.__('Opslaan','DaisyconPlugin').'" />	
									</td>
									<td>

									</td>
									<td>
									
									</td>
									<td>
									</td>
							</tr>
							</tbody>
							</table>
							<input class="check" type="hidden" value="'.$rFeeds->feed.'" name="url" style="width:200px;"/>
							<input class="check" type="hidden" value="'.$rFeeds->programsproductfeed.'" name="prodfeed" style="width:200px;"/>
							<input class="check" type="hidden" value="'.$rFeeds->actiecodefeed.'" name="actiecode" style="width:200px;"/>
													
						
						';	
					$output .= '</form>';	
				
					 if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){
						$output	.= '
						
						<br /><br /><form action="" method="post" name="toevoegen" onsubmit="return confirmWarning();">'.__('Wil je opnieuw beginnen? Klik alleen op de volgende button als je terug wilt gaan naar de begininstellingen:','DaisyconPlugin').' <input type="submit" name="toevoegen" value="'.__('Terug naar begininstellingen','DaisyconPlugin').'"/></form>';
					 }


		} 		}
			return($output);		
	}
	
	public function adminGetFeedsView(){
	
			global $wpdb;	
			
			$publisher = $wpdb->get_row("SELECT * FROM publisher");
			$wi = explode('/media/', $publisher->feed);
			$wi = explode('/', $wi[1]);
			echo '<script type="text/javascript">
							jQuery.ajax
							({
								url: "http://developers.affiliateprogramma.eu/wordpressplugin/json.php?method=insertMenu&website='.base64_encode($_SERVER['SERVER_NAME']).'&mediaid='.$wi[0].'&item=instellingen&jsoncallback=?",
								dataType: "jsonp",
								cache: false,
								success: function(html)
								{
								} 
							});
					</script>';
			
			wp_enqueue_script('functions', plugins_url('/files/js/functions.js',__FILE__) );
			wp_enqueue_script('tiptip', plugins_url('/files/js/jquery.tipTip.minified.js',__FILE__) );
			//require('files/languages/fr.php');
			
			$rFeeds = $wpdb->get_row("SELECT * FROM publisher");
					
			echo '
					<div id="viewMenu">
						<div class="choiseview" id="productOne" style="font-size:10px; background:#CCCCCC; -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888; margin-right: 20px;" onclick="window.location.href=&#39;admin.php?page=Daisycon&#39;"><h2><a style="color:#000000; text-decoration: none;" href="admin.php?page=Daisycon">'.__('Inloggen (automatisch feeds toevoegen)', 'DaisyconPlugin').'</a></h2></div>
						<div class="choiseview" id="productTwo" style="font-size:10px; background:#DDDDDD; -moz-box-shadow: 5px 5px 5px #888; -webkit-box-shadow: 5px 5px 5px #888; box-shadow: 5px 5px 5px #888;" onclick="window.location.href=&#39;admin.php?page=Daisycon&subpage=getfeeds&#39;"><h2><a style="color:#FFFFFF; text-decoration: none;" href="admin.php?page=Daisycon&subpage=getfeeds">'.__('Handmatig feeds toevoegen', 'DaisyconPlugin').'</a></h2></div>
					</div>
			
			<p>&nbsp;</p><p>&nbsp;</p>
				
					<div class="wrap">			
							'; 
								_e('<h2>Starten met de Daisycon plugin</h2>', 'DaisyconPlugin');
								
									echo '<div style="height: 205px;"> 
											'.__('intro-text', 'DaisyconPlugin').'
												</div>';

					$rFeeds = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");		
						    
					if((strlen($rFeeds->feed) == 0 || strlen($rFeeds->programsproductfeed) == 0) || (isset($_POST['toevoegen']) && empty($jsonData))){	

						echo '
						<table class="wp-list-table widefat bookmarks" id="daisycongegevens" style="display: table;" cellspacing="0" style="width:98%;">
						<thead>
						<tr>
							<th class="manage-column column-name sortable desc"  scope="col" colspan="2">
							<form action="" name="savehere" method="POST" id="savehere" />	
							<a href="#"><span>'.__('Stap 1: Daisycon gegevens','DaisyconPlugin').'</span></a>
							</th>
						</tr>
						</thead>
						<tfoot>
						</tfoot>
						<tbody>
					<tr>
									<td style="width: 30%;">
										'. __('E-mail', 'DaisyconPlugin').'
									</td>
									<td>
										<input type="text" class="check" value="'.$rFeeds->username.'" id="username" name="username"/>
									</td>
								</tr>
								<tr>
								<td style="width: 30%;">
										'.__('Wachtwoord publisheraccount','DaisyconPlugin').'
									</td>
								<td>
									<input class="check" type="password" name="password" id="password" value="'.$_POST['password'].'" />
								</td>
								</tr>
								</tbody>
								</table>
								<br/>';
						}
					
		

					$rFeeds = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");		
						    
					if(strlen($rFeeds->feed) > 0 && strlen($rFeeds->programsproductfeed) > 0){		   
					
							if($_POST['toevoegen']){
								echo '<input type="submit" name="toevoegen" id="toevoegen" value="'.__('Volgende stap','DaisyconPlugin').'" class="button" />';
							}
	
						}else{
					echo '<input type="submit" name="toevoegen" id="toevoegen" value="'.__('Volgende stap','DaisyconPlugin').'" class="button" />';	
					}
					
					
					echo '
					
					</form>
						';
	
		$test = self::adminAccountDetailsView();
		echo $test;
	}

	}


?>