var my_client_id = "457098491051872"; // app_id
var my_secret = "fd5adea1ca8d7ccf004f63ab60559273"; // app_secret 
var my_redirect_uri = "http://www.facebook.com/connect/login_success.html";
var my_type ="user_agent";
var my_display = "touch";
var facebook_token = "fbToken";

var Facebook = {           		
	/**
	 * init
	 * @author idnext
	 * @description Call facebook login view, if user try to connect a new url is catched by FacebookLocChanged
	 */
	init:function(){
            		 
        // Begin Authorization
        var authorize_url = "https://graph.facebook.com/oauth/authorize?";
        authorize_url += "client_id=" + my_client_id;
        authorize_url += "&redirect_uri=" + my_redirect_uri;
        authorize_url += "&display=" + my_display;
        authorize_url += "&scope=email"
            		 			
        // Open InAppBrowser and ask for permissions
        ref = window.open(authorize_url, '_self', 'location=no');
        ref.addEventListener('loadstart', function(event){
            Facebook.facebookLocChanged(event.url);
            console.log('Connected to facebook');
        });
    }, 
    /**
     * facebookLocChanged
     * @author idnext
     * @param {String} loc New url
     * @description Test if loc contains "code"
     */
    facebookLocChanged:function(loc){
    	if(loc.indexOf("code=") >= 1 ) {
            //close inappbrowser
            ref.close();

            //connexion
            var codeUrl = 'https://graph.facebook.com/oauth/access_token?client_id='+my_client_id+'&client_secret='+my_secret+'&redirect_uri='+my_redirect_uri+'&code='+loc.split("=")[1]+'';
            				   
            $.ajax({
            	url: codeUrl,
            	data: {},
            	type: 'POST',
                async: true,
            	cache: false,
            	success: function(data, status){
            		var facebook_token =  data.split('=')[1].split('&')[0];
            		window.localStorage.setItem('facebook_token', facebook_token);
            		console.log(facebook_token);
            		ref.close();
            		Facebook.getUserProfil();
            	},
            	error: function(){
            		console.log('error');
            		ref.close();
            	}
            });
    	}
    },
    /**
     * getUserProfil
     * @author idnext
     * @description get the user information
     */
    getUserProfil:function(){
    	// Our Base URL which is composed of our request type and our localStorage facebook_token
        var url = 'https://graph.facebook.com/me?access_token='+window.localStorage.getItem('facebook_token')+'';
        $.ajax({
        	url:url,
        	data:{},
        	type: 'GET',
        	contentType:'application/json',
        	dataType: 'json',
        	success: function(data, status){
        		 console.log('From facebook /me/');
        		 console.log(data.id);
        		 console.log(data.name);
        		 console.log(data.email);
        		 console.log(data.gender);
        		 //Info is get now, we can auth by server side
        		 Facebook.checkFacebook(data.id, data.name, data.email, data.gender);
        	},
        	error: function(error) {
        		console.log('Error');
        	}
        });
    },
    /**
     * checkFacebook
     * @author idnext
     * @param {int} id
     * @param {String} name
     * @param {String} email
     * @param {String} gender
     * @description Check if user is a new or current user
     */
    checkFacebook:function(id, name, email, gender){
    	var url = base + 'mobile/checkUserFacebook.php';
    	$.ajax({
        	url:url,
        	data:{id: id, name: name, email: email, gender: gender,},
        	type: 'GET',
        	contentType:'application/json',
        	dataType: 'json',
        	success: function(data){
        		console.log('Success facebook check');
        		 if(data.id){
        			 window.localStorage.setItem('idUser', data.id);
            	     window.localStorage.setItem('fb', true);
            	     window.localStorage.setItem('validUser', data.isValid);
            	     window.location.href='account.html';
        		 }
        	},
        	error: function(error){
        		console.log('Error check facebook user');
        	}
        });
   },
};