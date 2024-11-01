/*
 * 
 * All the $(document).ready() functions
 * 
 */ 
jQuery(document).ready(function() 
    { 
		// Add Tablesorter to the pages Programma's, Actiecodes and Categorieën
		jQuery("table.tablesorter").each(function (index) {  
			jQuery("#sortTable").tablesorter(); 
		});
		
        // Tooltip for the page Programma's
		jQuery("#tooltip").each(function (index) {  
			jQuery("#tooltip").tipTip({maxWidth: "auto", edgeOffset: 10});
		});
    } 
);  

/*
 *  
 * Show loading icon
 * 
 */
function loadingDiv() {
   document.getElementById("loadingDiv").style.display = "block";
}

function loadingDiv2() {
   document.getElementById("loadingDiv2").style.display = "block";
}

/*
 * 
 * Select all function for clicking on shorttags in Programma's, Actiecodes and Categorieën
 * 
 */
function select_all(el) {
	if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
		var range = document.createRange();
		range.selectNodeContents(el);
		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(range);
	} else if (typeof document.selection != "undefined" && typeof document.body.createTextRange != "undefined") {
		var textRange = document.body.createTextRange();
		textRange.moveToElementText(el);
		textRange.select();
	}
}

/*
 * 
 * Warning messages
 * 
 */
function confirmProduct() { 
	if (confirm("Weet je zeker dat je alle producten van de programma wilt ophalen? Dit kan namelijk enkele uren duren!")) {
		return true; 
	}else{
		return false; 
	}
} 

function confirmWarning() { 
	if (confirm("!! Let op: weet je zeker dat je terug wil naar de begininstellingen. Reeds ingestelde instellingen kunnen verloren gaan. !!")) {
		return true; 
	} else {
		return false; 
	}
}

function confirmDelete() {
	if (confirm("Weet je zeker dat je de stylesheet wilt verwijderen?")) {
		return true; 
	} else {
		return false; 
	}
}

function confirmResetStylesheets() {
	if (confirm("Weet je zeker dat je terug wilt gaan naar de begininstellingen? Alle stylesheets die je zelf hebt aangepast zullen verloren gaan.")) {
		return true; 
	} else {
		return false; 
	}
}

/*
 * 
 *  Switch between "Handmatig producten genereren" and "Automatisch producten genereren" tabs 
 * 
 */
jQuery("#productOne").click(function(){
	view = "one";
	jQuery("#viewOne").show();
	jQuery("#viewTwo").hide();
	jQuery("#productOne").css("background","#CCCCCC");
	jQuery("#productOne").css("color","#000000");

	jQuery("#productTwo").css("background","#DDDDDD");
	jQuery("#productTwo").css("color","#FFFFFF");

}); 
jQuery("#productTwo").click(function(){
	view = "two";
	jQuery("#viewOne").hide();
	jQuery("#viewTwo").show();
	jQuery("#productTwo").css("background","#CCCCCC");
	jQuery("#productOne").css("background","#DDDDDD");
		
	jQuery("#productOne").css("color","#FFFFFF");
	jQuery("#productTwo").css("color","#000000");
});
