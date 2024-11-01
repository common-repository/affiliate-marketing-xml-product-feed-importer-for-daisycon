<?php 
global $wpdb;

if(isset($_GET['product'])){
	$programs = $wpdb->get_results("SELECT * FROM programs WHERE program_id = '".$_GET['product']."'");
	$wpdb->query("UPDATE programs SET productfeed_date = '".date("Y-m-d h:m:s")."' WHERE program_id = '".$_GET['product']."'");
}else{
	$programs = $wpdb->get_results("SELECT * FROM programs WHERE product_count > 0 AND daisycon_program_id != '1'");
}

// Query to get details from Publisher
$daisycon = $wpdb->get_row("SELECT * FROM publisher LIMIT 1");

$dPrograms = array();
echo'
	<div id="black"></div>
	<div id="blackcontent">
	<div class="wrap">
		<h2>'.__( 'Producten ophalen','DaisyconPlugin').'</h2><img src="'.get_bloginfo( 'wpurl' ).'/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="40" id="loadingIcon" alt="'.__('Even geduld....','DaisyconPlugin').'" style="float: right;" />
	</div>
	'.__('De producten voor dit programma worden opgehaald. Afhankelijk van het aantal producten kan dit tot een half uur duren. In de tussentijd is het noodzakelijk om deze pagina open te laten staan.','DaisyconPlugin').'
	<table class="wp-list-table widefat fixed bookmarks" cellspacing="0" style="width:98%;">
		<thead>
			<tr>
				<th class="manage-column column-name sortable desc"  scope="col">
					<a href=""><span>'.__('Programma','DaisyconPlugin').'</span></a>
				</th>
				<th class="manage-column column-name sortable desc"  scope="col">
					<a href=""><span>'.__('Producten opgehaald/totaal aantal producten','DaisyconPlugin').'</span></a>
				</th>
			</tr>
		</thead>
	<tfoot></tfoot>
	<tbody>
	';
 
foreach($programs AS $program){
	$dPrograms[] = array($program->program_id, $program->product_count);
	echo'
		<tr id="'.$program->program_id.'">
			<td>
				'.$program->name.'
			</td>
			<td class="succes">
				 
			</td>
		</tr>
		';
}

echo'
	</tbody>
	</table>
	</div>
	';

?>

<script>
jQuery(document).ready(function(){
var counts = 500;
var productsProgram = new Array();
var productsTemp = new Array();
var deleteProducts = new Array();
var check = 0;
var deleteproductss = "";
var dPrograms = new Array(
<?php 
 //plaats die de php array in een jquery array.
$i=0; 
$max = count($dPrograms)-1;
foreach($dPrograms AS $dProgram){ 
	
	if($i < $max){
		echo '['.$dProgram[0].', '.$dProgram[1].'],';
	}else{
		echo '['.$dProgram[0].', '.$dProgram[1].']';
	} 
	
	$i++;
}
?>
);

jQuery("#black").click(function() {	
	jQuery("#black").hide();
	jQuery("#blackcontent").hide();
});

go(0, 0, 500);

//Ajax functie welke de producten van het programma ophaald.

function go(counter, start, end){

	if((end) > dPrograms[counter][1]){
		jQuery("#"+dPrograms[counter][0]+" .succes").html('<?php _e('Even geduld de productfeed wordt nu opgehaald.','DaisyconPlugin') ?><img width="15" height="15" src="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="40" alt="<?php __('Even geduld....','DaisyconPlugin') ?>" />');
		//alert("test");
		var dataString = "program="+dPrograms[counter][0]+"&start="+start+"&end="+end;		
		jQuery.ajax
		({
			type: "POST",
			url: "<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/cron/importProductAjax.php?",
			data: dataString,
			cache: false,
			success: function(html)
			{	
				var dataString = "program="+dPrograms[counter][0];
							
				jQuery.ajax
				({
					type: "POST",
					url: "<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/cron/deleteProducts.php?",
					data: dataString,
					cache: false,
					success: function(html)
					{	
						
					} 
				}); 

				//tabel update
				jQuery("#"+dPrograms[counter][0]+" .succes").html("<?php _e('De producten zijn succesvol opgehaald','DaisyconPlugin') ?>");

				//alert("test"+dataString);
							
				//counter voor de var dPrograms
				counts = 500;

				//counter optellen voor volgende programma
				counter ++;
					
				//functie opnieuw aanroepen voor het volgende programma
				if(counter < dPrograms.length){
					setTimeout(function() { go(counter, 0, 500); }, <?php echo $daisycon->feed_timeout;?>000);
				}else{
					jQuery("#loadingIcon").attr("src", "<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/green.png");
				}
			}
		});
	}else{
		jQuery("#"+dPrograms[counter][0]+" .succes").html('<?php _e("Even geduld de productfeed wordt nu opgehaald","DaisyconPlugin") ?><img width="15" height="15" src="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="40" alt="<?php __("Even geduld....","DaisyconPlugin") ?>" />');
		var dataString = "program="+dPrograms[counter][0]+"&start="+start+"&end="+end;
			
		jQuery.ajax
		({
			type: "POST",
			url: "<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/cron/importProductAjax.php?",
			data: dataString,
			cache: false,
			success: function(html)
			{	
				//alert("start:"+start+" End:"+end);
				//alert(html);
				//tabel update
				jQuery('#'+dPrograms[counter][0]+' .succes').html('<?php _e("Ophalen producten","DaisyconPlugin") ?>: '+counts+'/'+dPrograms[counter][1]+'<img width="15" height="15" src="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/loading.gif" height="40" alt="<?php __("Even geduld....","DaisyconPlugin") ?>" />');

				//alert(productsProgram.length);
				
				//alert(dataString);
				//counter voor de var dPrograms
				//counter ++;
				start = counts;
				counts = counts + 500;
				//functie opnieuw aanroepen voor het volgende programma
				if(counter < dPrograms.length){
					setTimeout(function() { go(counter, start, counts); }, <?php echo $daisycon->feed_timeout;?>000);
				}else{
					jQuery("#loadingIcon").attr("src", "<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/images/green.png");
				}
			
			} 
		
		});
	}
}

});
</script>