var base = "http://ecocompare.com/"; //Main directory
var url = base + "mobile/"; //Web Service directory
/*
 * getProducts()
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
		url+='getProducts.php';
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

/*
 * getProduct(int)
 * This function take a product_id in argument
 * Template called : 'product'
 */
function getProduct(id){
	waitingOn();
	url +='getProduct.php';
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

/*
 * getProductEean(int)
 * This function take an Ean code in argument
 * If the ean code match with a product_id we call the template 'product'
 * If the ean code exist but doesn't match, data are get in Table 'iphone_eans_desc'
 * If not, the function display an alert message
 * Template called : 'product'
 */
function getProductEan(ean){
	waitingOn();
	url +='getProductEan.php';
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

/*
 * getSearch()
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
		url += 'getSearch.php';
		if(document.getElementById('keywords').value.length >= 4){
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
	    				alert('Pas de resultat trouvé');
	    			}
	    		},
	    		error: function(){
	    			console.log('Error');
	    		}
	    	});
		}
		else
		{
			alert('Veuillez entrez au moins 4 caractères!');
		}
		return false;
    });
}

/*
 * getNoteDetails(int, int, int)
 * This function get a specific note for a specific product
 * TypeNote: 0 = Environnement
 * 		   : 1 = Societal
 *         : 2 = Santé
 * Score is in arg for display the general score of TypeNote
 * Template called : 'productNote'
 */
function getNoteDetails(id, score, typeNote){
	//$(".waiting").css("display", "block");
	waitingOn();
	url += 'getNotation.php';
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

/*
 * getUserInfo()
 * This function get personnal information of the current connected user in localStorage
 * It also get isValid for check if user as a valid email or not
 * Templated called : 'userInfo'
 */
function getUserInfo(){
	var id = localStorage.getItem('idUser');
	waitingOn();
	url+='getUserInfo.php';
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

/*
 * setuserInfo()
 * Update information for the current connected User
 * Required : name
 * 			: gender
 * 			: postalcode
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
    //var parrain = document.getElementById('parrain').value;
    
    //Check value info before ajax send
    var errors = [];
    var checkMailParrain= new RegExp("^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$","g");
    var checkName= new RegExp("^[a-zâäàéèùêëîïôöçñA-Z0-9]+[ \-']?[[a-zâäàéèùêëîïôöçñA-Z0-9]+[ \-']?]*[a-zâäàéèùêëîïôöçñA-Z0-9]+$","g");
    //var checkAge = new RegExp("^[1-9][0-9]?$","g");
    var checkTel = new RegExp("^0[1-9]((\\s|\.|-)?[0-9]{2}){4}$","g");
    var checkPostalCode = new RegExp("^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3}$","g");
    
	if(name!=''){
		if(!checkName.test(name)){
			errors.push('Veuillez saisir un prénom et/ou nom valide');
		}
	}else{
		errors.push('Veuillez remplir le champ Nom Prénom');
	}
   	if(birthyear!=''){
   		//test année en cours
   		var date = new Date();
   		if((birthyear<1900)||(birthyear>date.getFullYear())){
   			errors.push('Veuillez saisir une année de naissance cohérente');
   		}
   	}else{
   		errors.push('Veuillez remplir le champ Année de naissance');
   	}
   	if(tel!=''){
   		if(!checkTel.test(tel)){
   			errors.push('Veuillez saisir un numéro de téléphone valide');
   		}
   	}
   	/*
   	if(parrain!=''){
   		if(!checkMailParrain.test(parrain)){
   			errors.push('Veuillez saisir l\'adresse exact de votre parrain');
   		}
   	}*/
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
            console.log(errors[i]);
   		}
   		txt += '</div>';
   		$('.box-notif.information').before(txt);
   		$('.box-notif.information').css('display','none');
   	}
    else
    {
	    $.ajax({
	    	url: 'http://ecocompare.com/mobile/setUserInfo.php',
	    	data:{id: id,name: name,gender: gender, tel: tel, birthyear: birthyear, postalcode: postalcode, ville: ville, address: address, job: job, child: child,},
	    	contentType:'application/json',
	    	dataType: 'json',
	    	type:'GET',
	    	success: function(data){
	    		if(data!=false){
	    			sendAlert('Données enregistrées avec succés!',null,'Information');
	    		}
	    	},
	    	error: function(){
	    		console.log('Error');
	    	},
	    	complete: function(){
	    		location.reload(); //Recharger la page quoi qu'il arrive
	    	}
	    });
   	}
}

/*
 * setUserPassword()
 * Update user password for the current connected User
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
   		
   	}else{
		url += 'setUserPassword.php';
		$.ajax({
		    url: url,
		    data:{id: id,pwd: pwdnew,},
		    contentType:'application/json',
		    dataType: 'json',
		    type:'GET',
		    success: function(data){
		    	if(data!=false){
		    		sendAlert('Votre mot de passe a été changé avec succés.',null,'Bravo', 'Fermer', null, null);
		    	}
		    },
		    error: function(){
		    	console.log('Error');
		    }
		 });
	}
}
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
   	}
	else{
		 url += 'forgotPassword.php';
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
/*
 * setScanQuery(int)
 * This function take an Ean code in arg
 * If user is connected, with a valid email we set his geolocation(if active) and his id for this Ean scan
 * If user is undefined, we set a new scan with ean or product info but no user information
 */
function setScan(ean){
	
	
	if(isConnected()&&isValid()){
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
		$.ajax({
	    	url: 'http://ecocompare.com/mobile/setScan.php',
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
		$.ajax({
	    	url: 'http://ecocompare.com/mobile/setScan.php',
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