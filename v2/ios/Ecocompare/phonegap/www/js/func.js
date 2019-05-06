/*
 * func.js
 * Keep "other" functions
 *
 * checkDigitEan13(ean)
 * Take an ean in param.
 * Check if the Ean is ok or not
 * For further information :
 * http://fr.wikipedia.org/wiki/EAN_13
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

/*
 * refreshNew()
 * Called for refresh products on index view
 */
function refreshNew(){			
	window.localStorage.removeItem('products');   			
	window.location.href='index.html';
}

/*
 * isConnected()
 * Check if a user is connected
 */
function isConnected(){
	if (window.localStorage.getItem('idUser')){
		return true;
	}
	else{
		return false;
	}
}

/*
 * isValid()
 * Check if current user has a valid account
 */
function isValid(){
	if(window.localStorage.getItem('validUser')==1){
		return true;
	}
	else{
		return false;
	}
}


/*
 * disconnect()
 * This function disconnect the current User and redirect him on connect view
 * If the user is connect with Fb, we call an url for disconnect the user of Facebook
 * CALL THE URL IN FB APP PARAMS : http://projets2.idnext.net
 */
function disconnect(){
	window.localStorage.removeItem('idUser');
	window.localStorage.removeItem('validUser');
	if(window.localStorage.getItem('facebook_token')){

		 var revoke_url = "http://m.facebook.com/logout.php?next=http://projets2.idnext.net";
		 revoke_url += "&access_token=" + window.localStorage.getItem('facebook_token');
         
		 // Open InAppBrowser
		 var browser = window.open(encodeURI(revoke_url), '_blank', 'location=no');
         alert('Vous allez être déconnecté!'); //Some hacks here for close the inAppbrowser if not alert the inAppBrowser will not close
		 window.close();
		 window.localStorage.removeItem('facebook_token');
	}
	window.location.href='connect.html';
}

/*
 * sendAlert(message, callback, title, buttonName, beep, vibrate)
 * This function init a new native alert
 * Beep and vibrate depends on device
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

/*
 * resendValidationMail()
 * Call checkUser with resend at true
 * Resend allow to resend an email to the user
 * User is disconnected for spamming reason.
 */
function resendValidationMail(){
	if(isConnected()&&!isValid()){
		var id = localStorage.getItem('idUser');
		$.ajax({
	    	url: 'http://ecocompare.com/mobile/checkUser.php',
	    	data:{id: id,resend: true},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	type:'GET',
	    	success: function(){
	    		sendAlert('Un mail de validation vous a été envoyé. Veuillez vous reconnecter pour accéder à votre profil.',null,'Information', 'Fermer', null, null);
	    		disconnect();
	    	},
	    	error: function(){
               console.log('Error');
	    	}
	    });
	}
}

/*
 * checkConnection()
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

/*
 * displatLeavesImg(score)
 * Display the icons leaves matched by score
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

/*
 * wordsLimit(text, limit)
 * Return a troncate text for responsive issues
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

/*
 * mois(i)
 * Receive an int and return the matched month in French
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
/*
 * formatDate(since70)
 * Receive PHP TimeStamp in milliseconds, adapt for JS in seconds and return date in "day month year" format
 */
function formatDate(since70){
	var d = new Date();
	d.setTime(since70*1000); //milliseconds * 1000 = result in seconds
	return (d.getDate()+" "+mois(d.getMonth())+" "+d.getFullYear());
}

/*
 * parseUrl(key)
 * Return the value of the key
 * Used for get the url params
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

/*
 * scan()
 * Call the scanner
 * If success go to the result
 */
function scan(){
	window.plugins.barcodeScanner.scan(function(result){
		if(result.cancelled===true){
            window.location.href='index.html';
		}else{
			window.location.href='product.html?ean='+result.text;
		}
		},function(error) {
			console.log("Scanning failed: " + error);
	});
}

/*
 * string_to_slug(str)
 * Called for call the right product img
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
/*
 * goToNote(id,score, typeNote)
 * Redirect the user to the right note details
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