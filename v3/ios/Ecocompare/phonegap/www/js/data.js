window.base = "http://ecocompare.com/"; //Main directory

/**
 * getProducts
 * @author idnext
 * This function get the last 10 new products.
 * If localStorage item exist, the function bypass the ajax function and get data into localStorage
 * Template called : 'products' 
 */
function getProducts(){
	var data = window.localStorage.getItem('products');
	if(data != null){
		products(JSON.parse(data), 'index');
	}
	else{
		waitingOn();
		var url = base + 'mobile/getProducts.php';
		$.ajax({
			url: url,
			contentType:'application/json',
			dataType: 'json',
			timeout:10000,
			type:'POST',
			crossDomain:true,
			cache:false,
			success: function(data){
				products(data, 'index');
				window.localStorage.setItem('products', JSON.stringify(data));
			},
			error: function(){
				console.log('Error');
			}
		});
	}
}

/**
 * getProduct
 * @author idnext
 * @param {int} id
 * This function take a product_id in argument
 * Template called : 'product'
 */
function getProduct(id){
	waitingOn();
	var url = base + 'mobile/getProduct.php';
	$.ajax({
		url: url,
		data:{id: id,},
		contentType:'application/json',
		dataType: 'json',
		type:'GET',
		success: function(data){
			product(data);
			waitingOff();
		},
		error: function(){
			console.log('Error');
			waitingOff();
		}
	});
}

/**
 * getProductEan
 * @author idnext
 * @param {int} ean
 * This function take an Ean code in argument
 * If the ean code match with a product_id we call the template 'product'
 * If the ean code exist but doesn't match, data are get in Table 'iphone_eans_desc'
 * If not, the function display an alert message
 * Template called : 'product'
 */
function getProductEan(ean){
	waitingOn();
	var url = base + 'mobile/getProductEan.php';
	$.ajax({
		url: url,
		data:{ean: ean,},
		contentType:'application/json',
		dataType: 'json',
		type:'GET',
		success: function(data){		
			product(data);
			waitingOff();
		},
		error: function(){
			window.location.href='index.html';
		}
	});
}

/**
 * getSearch
 * @author idnext
 * This function get products matched with search input value.
 * If localStorage search exist, the function get data into localStorage
 * Template called : 'products' 
 */
function getSearch(){
	
	if(window.localStorage.getItem('search')){
		products(JSON.parse(window.localStorage.getItem('search')), 'search');
		document.getElementById('keywords').value = JSON.parse(window.localStorage.getItem('searchValue'));
	}
	
	//Event form launch
	$('#formSearch').submit(function(){
		if(document.getElementById('keywords').value.length >= 4){
			var url = base + 'mobile/getSearch.php';
    		var searchValue = document.getElementById('keywords').value;	
    	    var getData = $(this).serialize();
    	    $('.list').html('');
    	    $(".waiting").css("display", "block");
    	    $.ajax({
    	    	type: 'GET',
    	    	contentType:'application/json',
    	    	dataType: 'json',
    	    	data: getData,
    	    	url: url,
    	    	success: function(data){
    	    		if(data.length > 0){
    	    			window.localStorage.removeItem('search');
    	    			products(data, 'search');
    	    			window.localStorage.setItem('search',JSON.stringify(data));
    	    			window.localStorage.setItem('searchValue', JSON.stringify(searchValue));
    	    		}
    	    		else
    	    		{
    	    			$(".waiting").css("display", "none");
    	    			sendAlert('Oups! Pas de résultats pour votre recherche.', null, 'Information', 'Fermer', null, null);
    	    		}
    	    	},
    	    	error: function(){
    	    		console.error('Error : SEARCH FORM SUBMIT');
    	    	}
    	    });
    	}
    	else
    	{
    		sendAlert('Veuillez entrez au moins 4 caractères!', null, 'Information', 'Fermer', null, null);
    	}
		return false;
    });
}

/**
 * getCriteriaSearch
 * @author idnext
 * Personalize search with 3 criterias
 * Template called : products
 */
function getCriteriaSearch(){
	showPopup2();
	var env = $('#env').text();
	var societal = $('#societal').text();
	var sante = $('#sante').text();
	
	env = env.substr(0, env.length-1);
	societal = societal.substr(0, societal.length-1);
	sante = sante.substr(0, sante.length-1);
	
	var personnal = env + '_' + societal + '_' + sante;
	
	var url = base + 'mobile/getCriteriaSearch.php';

	
    $('.list').html('');
    $(".waiting").css("display", "block");
    $.ajax({
    	type: 'GET',
    	contentType:'application/json',
    	dataType: 'json',
    	data: {personnal: personnal},
    	url: url,
    	success: function(data){
    		if(data.length > 0){
    			window.localStorage.removeItem('search');
    			products(data, 'search');
    			window.localStorage.setItem('search',JSON.stringify(data));
    			window.localStorage.removeItem('searchValue');
    			document.getElementById('keywords').value='';
    		}
    		else
    		{
    			$(".waiting").css("display", "none");
    			sendAlert('Oups! Pas de résultats pour votre recherche.', null, 'Information', 'Fermer', null, null);
    		}
    	},
    	error: function(){
    		console.error('Error');
    	}
    });
    
}

/**
 * getNoteDetails
 * @author idnext
 * @param {int} id
 * @param {int} score
 * @param {int} typeNote
 * 
 * This function get a specific note for a specific product
 * TypeNote: 0 = Environnement
 * 		   : 1 = Societal
 *         : 2 = Santé
 * Score is here for display the general score of TypeNote
 * Template called : 'productNote'
 */
function getNoteDetails(id, score, typeNote){
	//$(".waiting").css("display", "block");
	waitingOn();
	var url = base + 'mobile/getNotation.php';
	$.ajax({
		type: 'GET',
		contentType:'application/json',
		dataType: 'json',
		data: {id: id,typeNote: typeNote},
		url: url,
		success: function(data){
			productNote(data,score,typeNote);
			//$(".waiting").css("display", "none");
			waitingOff();
		},
		error: function(){
			console.log('Error');
		}
	});
	
}

/**
 * checkUser
 * @author idnext
 * user connecting and registering flow
 * Send user on account.html
 */
function checkUser(){
	$('#account').submit(function(){
		
		//RFC 2822 Email regex
    	var checkMail= new RegExp("^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$","g");
        var email = document.getElementById('email').value;
        var pwd = document.getElementById('pwd').value;
        
		var errors=[];
		
		if(email!=''){
			if(!checkMail.test(email)){
				errors.push('Veuillez saisir une adresse email valide');
			}
		}else{
			errors.push('Veuillez remplir le champ email');
		}
       	if(pwd!=''){
       		if(pwd.length<4){
       			errors.push('Votre mot de passe doit contenir au moins 4 caractères');
       		}
       	}else{
       		errors.push('Veuillez remplir le champ mot de passe');
       	}
     
       	if(errors.length>0){
       		console.log(errors.length);
       		$('.box-notif.error').remove();
       		var txt = '<div class="box-notif error">';
       		for(var i=0; i<errors.length; i++){
       			txt += errors[i]+'<br>';
       		}
       		txt += '</div>';
       		$('.box-notif.information').before(txt);
       		$('.box-notif.information').css('display','none');
       		$('.scroll').animate({  
       	        scrollTop:$('body').offset().top 
       	    }, 'slow');  
       	    return false;
       		
       	}else{
       		var url = base + 'mobile/checkUser.php';
        	$.ajax({
        		url: url,
        		data:{email: email, pwd: pwd},
        		contentType:'application/json',
        		dataType: 'json',
        		type:'GET',
        		crossDomain:true,
        		success: function(data){
        			if(data.id){
        				window.location.href='account.html';
        				window.localStorage.setItem('idUser', data.id);
        				window.localStorage.setItem('validUser', data.isValid);
        			}
        			else{
        				sendAlert('Le mot de passe associé à cette adresse email n\'est pas valide.', null, 'Information', 'Fermer', null, null);
        			}
        		},
        		error: function(){
        			console.log('Error');
        		}
        	});	
       	}
       	return false;
       	location.reload();
    });
}

/**
 * getUserInfo
 * @author idnext
 * This function get personnal information of the current connected user in localStorage
 * It also get isValid for check if user as a valid email or not
 * Templated called : 'userInfo'
 */
function getUserInfo(){
	var id = localStorage.getItem('idUser');
	waitingOn();
	var url = base + 'mobile/getUserInfo.php';
	$.ajax({
    	url: url,
    	data:{id: id,},
    	contentType:'application/json',
    	dataType: 'json',
    	type:'GET',
    	success: function(data){
    		if(data!=null){
    			localStorage.setItem('validUser', data.isValid);
    			userInfo(data);
    			waitingOff();
    		}
    	},
    	error: function(){
    		console.log('Error');
    	},
    });
}

/**
 * setuserInfo
 * @author idnext
 * Update information for the current connected User
 * Required : name
 * 			: gender
 * 			: postalcode
 * 			: birthyear
 */
function setUserInfo(){
	var id = localStorage.getItem('idUser');
	var name = document.getElementById('name').value;
	var gender = document.getElementById('gender').value;
	var tel = document.getElementById('tel').value;
    var birthyear = document.getElementById('birthyear').value;
    var ville = document.getElementById('ville').value;
    var postalcode = document.getElementById('postalcode').value;
    var address = document.getElementById('address').value;
    var job = document.getElementById('job').value;
    var child = document.getElementById('child').value;

    
    var errors = [];
    var checkName= new RegExp("^[a-zâäàéèùêëîïôöçñA-Z0-9]+[ -']?[[a-zâäàéèùêëîïôöçñA-Z0-9-]+[ -']?]*[a-zâäàéèùêëîïôöçñA-Z0-9]+$","g");
    var checkAge = new RegExp("^[0-9]{4}$","g");
    var checkTel = new RegExp("^0[1-9]((\\s|\.|-)?[0-9]{2}){4}$","g");
    var checkPostalCode = new RegExp("^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3}$","g");
    
	if(name!=''){
		if(!checkName.test(name)){
			errors.push('Veuillez saisir une prénom et nom valide');
		}
	}else{
		errors.push('Veuillez remplir le champ Nom Prénom');
	}
   	if(birthyear!=''){
   		if(!checkAge.test(birthyear)){
   			errors.push('Veuillez saisir une année de naissance cohérente');
   		}else{
   			var date = new Date();
   	   		if((birthyear<1900)||(birthyear>date.getFullYear())){
   	   			errors.push('Veuillez saisir une année de naissance supérieur à 1900');
   	   		}
   		}	
   	}else{
   		errors.push('Veuillez remplir le champ Année de naissance');
   	}
   	if(tel!=''){
   		if(!checkTel.test(tel)){
   			errors.push('Veuillez saisir un numéro de téléphone valide');
   		}
   	}

   	if(postalcode!=''){
   		if(!checkPostalCode.test(postalcode)){
   			errors.push('Veuillez entrez un code postal valide');
   		}
   	}else{
   		errors.push('Veuillez remplir le champ Code postal');
   	}
    
   	if(errors.length>0){
   		$('.box-notif.error').remove();
   		var txt = '<div class="box-notif error">';
   		for(var i=0; i<errors.length; i++){
   			txt += errors[i]+'<br>';
   		}
   		txt += '</div>';
   		$('.box-notif.information').before(txt);
   		$('.box-notif.information').css('display','none');
   		$('.scroll').animate({  
   	        scrollTop:$('body').offset().top 
   	    }, 'slow');  
   	    return false; 
   	}else{
	    var url = base + 'mobile/setUserInfo.php';
	    $.ajax({
	    	url: url,
	    	data:{id: id,name: name,gender: gender, tel: tel, birthyear: birthyear, postalcode: postalcode, ville: ville, address: address, job: job, child: child,},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	type:'GET',
	    	success: function(data){
	    		if(data!=false){
	    			sendAlert('Données enregistrées avec succès!',null,'Information');
	    		}
	    	},
	    	error: function(){
	    		console.log('Error');
	    	},
	    	complete: function(){
	    		location.reload(); 
	    	}
	    });
   	}
}

/**
 * setUserPassword
 * @author idnext
 * @description Update user password for the current connected User
 */
function setUserPassword(){
	var id = localStorage.getItem('idUser');
	var pwdnew = document.getElementById('pwdnew').value;
	var pwdbis = document.getElementById('pwdbis').value;
	var errors=[];
	if(pwdnew!=''){
		if(pwdnew.length<4){
			errors.push('Le mot de passe doit au moins contenir 4 caractères.');
		}
	}
	else{
		errors.push('Veuillez remplir le champ Nouveau Mot de passe.');
	}
	if(pwdbis!=''){
		if(pwdbis.length<4){
			errors.push('Le mot de passe doit au moins contenir 4 caractères.');
		}
	}
	else{
		errors.push('Veuillez remplir le champ de Confirmation du Mot de passe.');
	}
	if(pwdbis!=pwdnew){
		errors.push('Les valeurs ne correspondent pas.');
	}
	
	if(errors.length>0){
   		$('.box-notif.error').remove();
   		var txt = '<div class="box-notif error">';
   		for(var i=0; i<errors.length; i++){
   			txt += errors[i]+'<br>';
   		}
   		txt += '</div>';
   		$('.box-notif.information').before(txt);
   		$('.box-notif.information').css('display','none');
   		$('.scroll').animate({  
   	        scrollTop:$('body').offset().top 
   	    }, 'slow');  
   	    return false; 
   	}else{
		var url = base+'mobile/setUserPassword.php';
		$.ajax({
		    url: url,
		    data:{id: id,pwd: pwdnew,},
		    contentType:'application/json',
		    dataType: 'json',
		    type:'GET',
		    success: function(data){
		    	if(data!=false){
		    		sendAlert('Votre mot de passe a été changé.',null,'Bravo', 'Fermer', null, null);
		    	}
		    },
		    error: function(){
		    	console.log('Error');
		    }
		 });
	}
}
/**
 * ResetUserPassword
 * @author idnext
 * @description Send a new password to user
 */
function resetUserPassword(){
	var errors=[];
	var checkMail= new RegExp("^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$","g");
	var email = document.getElementById('email').value;
	if(email!=''){
		if(!checkMail.test(email)){
			errors.push('Veuillez saisir une adresse email valide');
		}
	}else{
		errors.push('Veuillez remplir le champ email');
	}
	if(errors.length>0){
   		$('.box-notif.error').remove();
   		var txt = '<div class="box-notif error">';
   		for(var i=0; i<errors.length; i++){
   			txt += errors[i]+'<br>';
   		}
   		txt += '</div>';
   		$('.box-notif.information').before(txt);
   		$('.box-notif.information').css('display','none');
   		$('.scroll').animate({  
   	        scrollTop:$('body').offset().top 
   	    }, 'slow');  
   	    return false; 
   	}
	else{
		 var url = base + 'mobile/forgotPassword.php';
		    $.ajax({
		    	url: url,
		    	data:{email: email,},
		    	contentType:'application/json',
		    	dataType: 'json',
		    	type:'GET',
		    	success: function(data){
		    		if(data==true){
		    			sendAlert('Un mail de confirmation vous a été envoyé',null,'Information', 'Fermer', null, null);
		    			window.location.href='connect.html';
		    		}
		    		else{
		    			sendAlert('Cette adresse email ne correspond à aucun compte.', null, 'Erreur', 'Fermer', null, null);
		    		}
		    	},
		    	error: function(){
		    		console.log('Error');
		    	},
		    });
	}
}
/**
 * setScanQuery
 * @author idnext
 * @param {int} ean
 * This function take an Ean code in arg
 * If user is connected : with a valid email : we set his geolocation(if active) and his id for this Ean scan
 * If user is undefined, we set a new scan with ean or product info but not user_id 
 */
function setScan(ean){
	
	if(isConnected()&&(isValid()==true)){
		id = localStorage.getItem('idUser');
		console.log('Connecté');
	}else{
		id = null;
	}
	
	navigator.geolocation.getCurrentPosition(onSuccess, onError);
	
	function onSuccess(position){
		longitude = position.coords.longitude;
		latitude  = position.coords.latitude;
		altitude  = position.coords.altitude;
		var date = new Date();
		newdate = date.getMilliseconds();
		var url = base+'mobile/setScan.php';
		$.ajax({
	    	url: url,
	    	data:{id: id, ean: ean, longitude: longitude, latitude: latitude, altitude: altitude},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	cache:false,
	    	crossDomain:true,
	    	type:'GET',
	    	success: function(data){
	    		console.log('Success');
	    	},
	    	error: function(){
	    		console.error('Error');
	    	},
	    });
		console.log('Localisé');
		console.log('Ean :'+ean);
		console.log('IdUser :'+id);
		console.log('Longitude :'+longitude);
		console.log('Latitude  :'+latitude);
		console.log('altitude  :'+altitude);
	}
	
	function onError(error){
		longitude = 0;
		latitude  = 0;
		altitude  = 0;
		var date = new Date();
		newdate = date.getMilliseconds();
		var url = base+'mobile/setScan.php';
		$.ajax({
	    	url: url,
	    	data:{id: id, ean: ean, longitude: longitude, latitude: latitude, altitude: altitude},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	cache:false,
	    	crossDomain:true,
	    	type:'GET',
	    	success: function(data){
	    		console.log('Success');
	    	},
	    	error: function(xhr, ajaxOptions, thrownError){
	    		console.error(xhr.status);
	    	},
	    });
		console.log('Non localisé');
		console.log('Ean :'+ean);
		console.log('IdUser :'+id);
		console.log('Longitude :'+longitude);
		console.log('Latitude  :'+latitude);
		console.log('altitude  :'+altitude);
	}
}

/**
* getGeneralRating
* @author idnext
* Get the current month rating
* Template called : 'genetalRating'
*/
function getGeneralRating(){
	var url = base + 'mobile/getGeneralRating.php';
    waitingOn();
	$.ajax({
    	url: url,
    	data:{},
    	contentType:'application/json',
    	dataType: 'json',
    	type:'GET',
    	success: function(data){
    	    generalRating(data);
    	    console.log('Success');
    	},
    	error: function(){
    		console.log('Error');
    	},
    });
}

/**
* getUserScans
* @author idnext
* display a historic for 15 last user scans
* Template called : 'userScans'
*/
function getUserScans(){
	var id = window.localStorage.getItem('idUser');
	waitingOn();
	var url = base + 'mobile/getUserScans.php';
	$.ajax({
		url: url,
		data:{id:id},
		contentType:'application/json',
		dataType: 'json',
		type:'GET',
		success: function(data){
		    userScans(data); 
			console.log('Success');
		},
		error: function(){
			console.log('Error');
		},
	});
}

/**
 * getUserStats
 * @author idnext
 * get user stats and display medals
 * Template called : 'userStats'
 */
function getUserStats(){
	var id = window.localStorage.getItem('idUser');
	waitingOn();
	var url = base + 'mobile/getUserStats.php';
	$.ajax({
		url: url,
		data:{id: id},
		contentType:'application/json',
		dataType: 'json',
		type:'GET',
		success: function(data){
		    userStats(data);
			console.log('Success');
		},
		error: function(){
			console.log('Error');
		},
	});
}
