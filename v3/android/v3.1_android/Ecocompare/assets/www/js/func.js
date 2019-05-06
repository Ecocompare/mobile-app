/*
 * func.js
 * regroup "other" usefull functions
 */


 /**
 * checkDigitEan13
 * @author idnext
 * @param {int} ean
 * @returns {boolean}
 * @description Check if the Ean is ok or not
 * @see http://fr.wikipedia.org/wiki/EAN_13
 */
function checkDigitEan13(ean){
	var digits = ean.split('');
	var total = 0
	var result = 0;
	
	for(var i=0; i<=11; i++){
		if((i & 1)==1){
			total = total + parseInt(digits[i])*3;
		}
		else{
			total = total + parseInt(digits[i]);
		}
	}
	
	result = total%10;
	
	if(result==0){
		return 0==digits[12];
	}else{
		return 10-result==digits[12];
	}
}

/**
 * eanSearch
 * @author idnext
 * @description check ean field after a submit
 * @returns {boolean}
 */
function eanSearch(){
	$('#formEan').submit(function(){
		var checkEan = new RegExp("^[0-9]{13}$","g");
		var ean = document.getElementById('ean').value;
		if(!checkEan.test(ean)){
			sendAlert('Veuillez entrer un code à 13 chiffres', null, 'Erreur', 'Fermer', null, null);
		}else{
			if(checkDigitEan13(ean)==true){
				window.location.href='product.html?ean='+ean+'';
			}
			else{
				sendAlert('Le code ne semble pas valide. Veuillez réessayer', null, 'Erreur', 'Fermer', null, null);
			}
		
		}
    	return false;
	});
}

/**
 * greenPercentage
 * @author idnext
 * @param {int} total
 * @param {int} totalgreen
 * @description Calculate % of green products
 * @returns {String}
 */
function greenPercentage(total, totalgreen){
	var percent = Math.round((100*totalgreen)/total);
	return totalgreen+' ('+percent+'%)';
}

/**
 * setUpDown
 * @author idnext
 * @param {int} prevsort
 * @param {int} sort
 * @description Set and up or down icon
 * @returns {String}
 */
function setUpDown(prevsort, sort){
	
    var actual = Number(sort);
    var prev = Number(prevsort);
    
	if(actual < prev){
		return "<img class='updown' style='margin-left:10px' src='img/icons/up.png' />";
	}
	else if(actual > prev){
		return "<img class='updown' style='margin-left:10px' src='img/icons/down.png' />";
	}
}

/**
 * refreshNew
 * @author idnext
 * @description Called for refresh products on index view
 */
function refreshNew(){			
	window.localStorage.removeItem('products');   			
	window.location.href='index.html';
}

/**
 * isConnected
 * @author idnext
 * @description Check if a user is connected
 * @returns {boolean}
 */
function isConnected(){
	if (window.localStorage.getItem('idUser')){
		return true;
	}
	else{
		return false;
	}
}

/**
 * isValid
 * @author idnext
 * @description Check if current user has a valid account
 * @returns {boolean}
 */
function isValid(){
	if(window.localStorage.getItem('validUser')==1){
		return true;
	}
	else{
		return false;
	}
}


/**
 * disconnect
 * @author idnext
 * This function disconnect the current User and send him on connect view
 * If the user is connected with Fb, we call url for disconnect the user of Facebook
 * CALL THE URL IN FB APP PARAMS : http://projets2.idnext.net
 */
function disconnect(){
	window.localStorage.removeItem('idUser');
	window.localStorage.removeItem('validUser');
	if(window.localStorage.getItem('facebook_token')){
		console.log('Déconnexion facebook');
		var revoke_url = "http://m.facebook.com/logout.php?next=http://projets2.idnext.net";
		 revoke_url += "&access_token=" + window.localStorage.getItem('facebook_token');
		 // Open InAppBrowser s
		 console.log(revoke_url);
		 ref = window.open(revoke_url, '_self', 'location=no');
	     ref.close();
		 window.localStorage.removeItem('facebook_token');
	}
	window.location.href='connect.html';
}

/**
 * sendAlert
 * @author idnext
 * @param {String} message
 * @param {String} callback
 * @param {String} title
 * @param {String} buttonName
 * @param {Bool} beep
 * @param {Bool} vibrate
 * @description Init a new native alert. Beep and vibrate depends on device
 */
function sendAlert(message, callback, title, buttonName, beep, vibrate) {
	document.addEventListener("deviceready", function(){	
		navigator.notification.alert(message, callback, title, buttonName);
		if(beep){
			navigator.notification.beep(1);
		}
		if(vibrate){
			navigator.notification.vibrate(1000);
		}
	}, false);
}
function onConfirm(button){
	if(button==1){
		scan();
	}else{
		showPopup();
	}
}
function retryScan(){
	document.addEventListener("deviceready", function(){
		navigator.notification.confirm(
	            'Le code scanné ne semble pas valide. Voulez-vous réessayer ?',  // message
	            onConfirm,              // callback to invoke with index of button pressed
	            'Erreur',            // title
	            'Oui,Non'          // buttonLabels
	    );
	}, false);
}

/**
 * resendValidationMail
 * @author idnext
 * Call checkUser with resend at true
 * Resend allow to resend an email to the user
 * User is disconnected for spamming reason.
 */
function resendValidationMail(){
	if(isConnected()&&!isValid()){
		var id = localStorage.getItem('idUser');
		var url = base + '/mobile/checkUser.php';
		$.ajax({
	    	url: url,
	    	data:{id: id,resend: true},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	type:'GET',
	    	success: function(){
	    		sendAlert('Un mail de validation vous a été envoyé. Vous allez maintenant être déconnecté.',null,'Information', 'Fermer', null, null);
	    		disconnect();
	    	},
	    	error: function(){
	    		console.log('Error');
	    	}
	    });
	}
}

/**
 * checkConnection
 * @author idnext
 * Check if user is connected to a network
 * If not we call a 'broken network' page
 * Template called : networkState.html
 */
function checkConnection() {
	var networkState = navigator.network.connection.type;
	var states = {};
	states[Connection.NONE]     = true;
	if (networkState == Connection.NONE){
	    window.location.href='networkState.html';
	}
}

/**
 * displatLeavesImg
 * @author idnext
 * @param {Float} score
 * @description Display the icons leaves matched by score
 * @returns {String}
 */
function displayLeavesImg(score){
	if(score == 0){
		return ("<img src='img/leaves/leaves0.png'/>");
	}
	else if(score > 0 && score <= 0.5){
		return ("<img src='img/leaves/leaves05.png'/>");
	}
	else if(score > 0.5 && score <= 1){
		return ("<img src='img/leaves/leaves1.png'/>");
	}
	else if(score > 1 && score <= 1.5){
		return ("<img src='img/leaves/leaves15.png'/>");
	}
	else if(score > 1.5 && score <= 2){
		return ("<img src='img/leaves/leaves2.png'/>");
	}
	else if(score > 2 && score <= 2.5){
		return ("<img src='img/leaves/leaves25.png'/>");
	}
	else if(score > 2.5 && score <= 3){
		return ("<img src='img/leaves/leaves3.png'/>");
	}
	else if(score > 3 && score <= 3.5){
		return ("<img src='img/leaves/leaves35.png'/>");
	}
	else if(score > 3.5 && score <= 4){
		return ("<img src='img/leaves/leaves4.png'/>");
	}
	else if(score > 4 && score <= 4.5){
		return ("<img src='img/leaves/leaves45.png'/>");
	}
	else if(score > 4.5 && score <= 5){
		return ("<img src='img/leaves/leaves5.png'/>");
	}
}

/**
 * wordsLimit
 * @author idnext
 * @param {String} text
 * @param {int} limit
 * @description Return a troncate text for responsive issues
 * @deprecated Function is now deprecated since v3
 */
function wordsLimit(text, limit){
	if(text.length > limit){
		var result ="";
		for(var i=0;i<limit;i++){
			result += text[i];
		}
		return(result+='...');
	}
	else{
		return(text);
	}
}

/**
 * mois
 * @author idnext
 * @param {int} i
 * @description Receive an int and return the matched month in French
 * @returns {String}
 */
function mois(i){
	switch (i) {
	 case 0:
	 return "Janvier";
	 break;
	 case 1:
	 return "Février";
	 break;
	 case 2:
	 return "Mars";
	 break;
	 case 3:
	 return "Avril";
	 break;
	 case 4:
	 return "Mai";
	 break;
	 case 5:
	 return "Juin";
	 break;
	 case 6:
	 return "Juillet";
	 break;
	 case 7:
	 return "Août";
	 break;
	 case 8:
	 return "Septembre";
	 break;
	 case 9:
	 return "Octobre";
	 break;
	 case 10:
	 return "Novembre";
	 break;
	 case 11:
	 return "Décembre";
	 break;
	}
}

/**
 * formatDate
 * @author idnext
 * @param {int} since70
 * @description Receive PHP TimeStamp in milliseconds, adapt for JS in seconds and return date in "day month year" format
 * @returns {String}
 */
function formatDate(since70){
	var d = new Date();
	d.setTime(since70*1000); //milliseconds * 1000 = result in seconds
	return (d.getDate()+" "+mois(d.getMonth())+" "+d.getFullYear());
}

/**
 * dateDiff
 * @author idnext
 * @param {String} date1
 * @param {String} date2
 * @description create an array with the difference between 2 dates
 * @returns {String}[]
 */
function dateDiff(date1, date2){
    var diff = {};                          // Initialisation du retour
    var tmp = date2 - date1;
 
    tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
    diff.sec = tmp % 60;                    // Extraction du nombre de secondes
 
    tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
    diff.min = tmp % 60;                    // Extraction du nombre de minutes
 
    tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
    diff.hour = tmp % 24;                   // Extraction du nombre d'heures
     
    tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours
    diff.day = tmp;
     
    return diff;
}

/**
 * timeSpent
 * @author idnext
 * @param {String} date
 * @description Display the time since scan query
 * @returns {String}
 */
function timeSpent(date){

	var now = new Date(); //Current Date
	now.toLocaleString();
	var dateparse = date.replace(/-/gi, '/');
	var d = new Date(dateparse); //Scan Date

	diff = dateDiff(d, now);
	
	if(diff.day > 0){
		return 'Il y a '+diff.day+' j';
	}
	else if(diff.hour > 0){
		return 'Il y a '+diff.hour+' h';
	}
	else if(diff.min > 0){
		return 'Il y a '+diff.min+' min';
	}
	else{
		return 'Il y a moins d\'une min';
	}
}

/**
 * parseUrl
 * @author idnext
 * @param {String} key
 * @description Return the value of the key. Used for get the url params
 * @return {String}
 */
function parseUrl(key){
	search = (location.search)
	search = search.substr(1); //Delete the "?"
	var tab = search.split('&'); //Get params in tab cells
	for(var i=0; i<tab.length; i++){ //Create key,value couple
		var pairs = tab[i].split("=");
		if (pairs[0] == key)
		{
			return(pairs[1]);
		}
	}
}

/**
 * scan
 * @author
 * @description Call the scanner plugin
 */
function scan(){
	window.plugins.barcodeScanner.scan(function(result){
		if(result.cancelled===true){
			navigator.app.backHistory();
		}else{
			if(checkDigitEan13(result.text)==true){
				window.location.href='product.html?ean='+result.text;
			}else{
				retryScan();
			}
		}
		},function(error) {
			console.log("Scanning failed: " + error);
	});	
}

/**
 * string_to_slug
 * @author idnext
 * @param {String} str
 * @description Make a sluged url from param. Called for get the product img from url
 * @returns {String}
 */
function string_to_slug(str) {
	  str = str.replace(/^\s+|\s+$/g, ''); // trim  
	  // remove accents etc
	  var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
	  var to   = "aaaaeeeeiiiioooouuuunc------";
	  for (var i=0, l=from.length ; i<l ; i++) {
	    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	  }

	  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
	    .replace(/\s+/g, '-') // collapse whitespace and replace by -
	    .replace(/-+/g, '-'); // collapse dashes

	  return str;
}

/**
 * goToNote
 * @author idnext
 * @param {int} id
 * @param {float} score
 * @param {int} typeNote
 * @description Redirect the user to the right note details
 */
function goToNote(id, score, typeNote){
	window.location.href='note.html?id='+id+'&score='+score+'&typeNote='+typeNote+'';
}


/*
 * Here is some Jquery functions for Hide/Show items
 */
function showEanForm(){
	if ($("#formEan").is(':visible')){
		$("#formEan").slideUp(500);
	}
	else
	{
		$("#formEan").slideDown(500);
	}
}
function showPopup(){
	if ($(".popup").is(':visible')){
		$(".popup").slideUp(500);
	}
	else
	{
		$(".popup").slideDown(500);
	}
}
function showPopup2(){
	if ($(".popup2").is(':visible')){
		$(".popup2").slideUp(500);
	}
	else
	{
		$(".popup2").slideDown(500);
	}
}
function showNotation(){
	if ($(".detailsNotation").is(':visible')){
		$(".detailsNotation").slideUp(500);
	}
	else
	{
		$(".detailsNotation").slideDown(500);
	}
}
function showDetails(){
	if($(".description").is(':visible')){
		$(".description").slideUp(500);
		$(".btn-desc").slideDown(500);
	}
	else
	{
		$(".description").slideDown(500);
		$(".btn-desc").slideUp(500);
	}
}
function showLabels(){
	if($(".box-label-sub").is(':visible')){
		$(".box-label-sub").slideUp(500);
	}
	else
	{
		$(".box-label-sub").slideDown(500);
	}
}
function showNote(id){
	if($("#"+id+".row-container").is(':visible')){
		$("#"+id+".noteMore").attr('src','img/icons/plus.png');
		$("#"+id+".row-container").slideUp(500);
	}
	else
	{
		$("#"+id+".noteMore").attr('src','img/icons/minus.png');
		$("#"+id+".row-container").slideDown(500);
	}
}
function waitingOn(){
	$(".titleBar").css("display", "none");
	$(".subTitle").css("display", "none");
	$(".footer").css("display", "none");
	$(".box").css("display", "none");
	$(".list").css("display", "none");
	$(".nav").css("display", "none");
	$(".waiting").css("display", "block");
	
}
function waitingOff(){
	$(".waiting").css("display", "none");
	$(".titleBar").css("display", "block");
	$(".subTitle").css("display", "block");
	$(".list").css("display", "block");
	$(".box").css("display", "block");
	$(".footer").css("display", "block");
}