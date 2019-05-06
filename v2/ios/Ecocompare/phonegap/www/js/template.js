function products(data, page){
	$.each(data, function(k, v){
		$(".list").append("<div class='list-row'>" +
						  "<a class='link-area' href='product.html?id="+v.id+"'>"+
						  "<div class='thumbnail'><img class='mini'src='"+base+"comparateur-produit-eco-"+string_to_slug(v.name)+"-"+v.id+".jpg'/></div>" +
						  "<div class='info'>"+wordsLimit(v.name,23)+
						  "<div class='marque'>"+v.brand+"</div>"+
						  "</div>"+
						  "<div class='more'>"+displayLeavesImg(v.score4)+"</div></a>"+
						  "</div>");
	});
	waitingOff();
	if(page=='index'){
		if(window.localStorage.getItem('products')==null){
			$(".list-row").each(function(idx) {   
				$(this).delay(idx * 150).fadeIn('fast');
			});
		}
		else
		{
			$('.list-row').css('display','block');
		}		
	}
	
	if(page=='search'){
		if(window.localStorage.getItem('search')){
			$('.list-row').css('display','block');	
		}
		else
		{
			$(".list-row").each(function(idx) {   
				$(this).delay(idx * 150).fadeIn('fast');
			});
		}
		$("<div class='result-row'>"+data.length+" élement(s) trouvé(s)</div>").insertBefore($('.list-row:first-child'));
	}
}

function product(data){
	if(data.name){
	$(".box").append(
			"<div class='box-head'>"+
        		"<div class='thumbnail'><img src='"+base+"comparateur-produit-eco-"+string_to_slug(data.name)+"-"+data.id+".jpg'/></div>"+
	        	"<div class='info'><b>"+
	        		data.name+
	        		"</b><div class='marque'>"+data.brand+"</div>"+
	        		"<div class='date'>Ajouté le "+formatDate(data.pubdate)+"</div>"+
	        	"</div>"+
        	"</div>"+
        	"<div class='box-price'>Prix : ");
			if(data.price != ""){
				$(".box-price").append(data.price);
			}
			else{
				$(".box-price").append("N/C");
			}
			$(".box").append("</b></div>");
			if (data.labels.length > 0){
				$(".box").append("<div class='box-label'>");
				$.each(data.labels,function(k,v){
					var desc = v.description.replace(/\r\n/gmi," ").replace(/'/gmi,"\\'");
					$(".box-label").append('<img onClick="sendAlert(\''+desc+'\',null,\''+v.name+'\')" src="'+base+'images/labels_16/'+v.image+'" />');
				});
	        	$(".box").append("</div>");
			}
        	$(".box").append("<div class='box-notation'>"+
        		"Environnement<span class='notationPoints' onClick='goToNote("+data.id+","+data.score1+",0)' style='float:right;'>"+data.score1+"/5</span>"+
        		"<div id='progressBar' class='1' onClick='goToNote("+data.id+","+data.score1+",0)'><span class='value'></span></div>"+
        		"Sociétal<span class='notationPoints' onClick='goToNote("+data.id+","+data.score2+",1)' style='float:right;'>"+data.score2+"/5</span>"+
        		"<div id='progressBar' class='2' onClick='goToNote("+data.id+","+data.score2+",1)'><span class='value'></span></div>"+
        		"Santé<span class='notationPoints' onClick='goToNote("+data.id+","+data.score3+",2)' style='float:right;'>"+data.score3+"/5</span>"+
        		"<div id='progressBar' class='3' onClick='goToNote("+data.id+","+data.score3+",2)'><span class='value'></span></div>"+
        		"Total<span class='notationPoints' style='float:right;color:#65c7ff;'>"+data.score4+"/5</span>"+
        		"<div id='progressBar' class='4'><span class='value' style='background-color:#65c7ff;'></span></div>"+
        	"</div>");
        	if(data.description.length > 0){
        		var description=data.description;
        		description.replace(/\[\[/gmi,"<b>").replace(/\]\]/gmi,"</b>");
        		$(".box").append("<div class='box-findit'><div class='title-mini-box'>Informations</div><div class='text-mini-box'>"+description+"</div></div>");
        	}
        	if (data.findit.length > 0){
        		$(".box").append("<div class='box-findit'><div class='title-mini-box'>Où le trouver ?</div><div class='text-mini-box'>"+data.findit+"</div></div>");
        	}
        	
			var score1 = Math.round(data.score1*100)/5;
			var score2 = Math.round(data.score2*100)/5;
			var score3 = Math.round(data.score3*100)/5;
			var score4 = Math.round(data.score4*100)/5;
	        $("#progressBar.1").find(".value").animate({width:""+score1+"%"},500);
			$("#progressBar.2").find(".value").animate({width:""+score2+"%"},500);
			$("#progressBar.3").find(".value").animate({width:""+score3+"%"},500);
			$("#progressBar.4").find(".value").animate({width:""+score4+"%"},500);
	}else{
		if(data!=false){
			$(".box").append('<div class="list"><p><b>'+data+'</b><br><br>Nous ne disposons pas encore d\'informations de la part de la marque pour ce produit. Votre demande a été prise en compte et sera traitée dès que possible.</p></div>');
		}
		else{
			$(".box").append('<div class="list"><p>Ce produit n\'existe pas encore dans notre base de données. Votre demande a été prise en compte et sera traitée dès que possible.</p></div>');
		}
	}
}

function productNote(data, score, typeNote){
	//Afficher la note
	$('.scroll').append("<div class='bigNote'>"+score+"/5</div>");
	$('.scroll').append("<div class='progressBorder'></div>");
	
	//Display Environnement and Societal
	if(typeNote==0||typeNote==1){
		if(typeNote==0){
			$('.titleBar').append('Environnement');
		}
		if(typeNote==1){
			$('.titleBar').append('Sociétal');
		}
		if(data.length){
			for(var i=0;i<data.length;i++){
				var v = data[i];
				var txt = '<div class="box-container" onClick="showNote('+i+')">'+v.name+'<img id="'+i+'" class="noteMore"  src="img/icons/plus.png"/>';
				txt += '<div id="'+i+'" class="row-container">';
				for(var j=0;j<v.subrating.length;j++){
					var v2 = v.subrating[j];
					txt += '<div class="note-container"><strong>'+v2.name+'</strong><br>';
					if(v2.islabel=="1"){
						for(var k=0; k<v2.subratingslabel.length;k++){
							var v3 = v2.subratingslabel[k];
							txt += v3.comment+'<br>';
						}
						txt += '</div>';
					}
					else{
						switch(v2.ask_proof){
						case "0":
							var comment=v2.comment.replace(/\[\[/gmi,"<b>").replace(/\]\]/gmi,"</b>");
							txt += '<i>'+comment+'</i></div>';
						break;
						case "1":
							txt += '<i>Photo ou preuve fournie</i></div>';
						break;
						case "2":
							txt += '<i>Attestation sur l\'honneur fournie</i></div>';
						break;
						}
					}
				}
				txt += "</div></div>";
				$('.scroll').append(txt);
			}
		}
		else
		{
			$('.scroll').append('<div class="box"><p>Désolé.<br>Nous n\'avons pas d\'informations supplémentaires concernant cette note.</p></div>');
		}
	}
	//Display Sante
	if(typeNote==2){
		$('.titleBar').append('Santé');
		if(data.length){
			var txt = '<div class="box-container"><div class="row-container" id="'+i+'">';
			for(var i=0;i<data.length;i++){
				var v = data[i];
				txt += '<div class="note-container"><strong>'+v.name+'</strong><br>';
				if(v.islabel=="1"){
					for(var j=0; j<v.subratingslabel.length;j++){
						var v2 = v.subratingslabel[j];
						txt += v2.comment;
					}
					txt += '</div>';
				}
				else{
					switch(v.ask_proof){
					case "0":
						var comment=v.comment.replace(/\[\[/gmi,"<b>").replace(/\]\]/gmi,"</b>");
						txt += '<i>'+comment+'</i></div>';
					break;
					case "1":
						txt += '<i>Photo ou preuve fournie</i></div>';
					break;
					case "2":
						txt += '<i>Attestation sur l\'honneur fournie</i></div>';
					break;
					}
				}
			}
			txt += "</div></div>";
			$('.scroll').append(txt);
			$(".row-container").css('display','block');
		}
		else{
			$('.scroll').append('<div class="box"><p>Désolé.<br>Nous n\'avons pas d\'informations supplémentaires concernant cette note.</p></div>');
		}
		
	}
	
	var scoreValue = Math.round(score*100)/5;
	$('.progressBorder').animate({width:""+scoreValue+"%"},500);
}

function userInfo(data){
	if(isValid()==1){
		 $('#account').append("<img id='photo' onClick='showPopup2()' src='http://ecocompare.com/ecoacteur-photo-"+data.id+"' />"+
		    //"<div class='label'>Nom et prénom</div>"+
	     	"<div><input class='grouped' id='name' placeholder='Nom Prénom' required type='text'/></div>"+
	     	//"<div class='label'>Genre</div>"+
	     	"<div class='select-input required'>"+
	     		"<select id='gender'>"+
	     			"<option>Homme</option>"+
	     			"<option>Femme</option>"+
	     		"</select>"+	
	     	"</div>"+
	     	//"<div class='label'>Année de naissance</div>"+
	     	"<div><input class='grouped' id='birthyear' placeholder='Année de naissance' required type='text'/></div>"+
	     	//"<div class='label'>Code postal</div>"+
	     	"<div><input class='grouped' id='postalcode' placeholder='Code postal' required type='text'/></div>"+
	     	//"<div class='label'>Ville</div>"+
	     	"<div><input class='grouped' id='ville' placeholder='Ville' type='text'/></div>"+
	     	//"<div class='label'>Adresse</div>"+
	     	"<div><input class='grouped' id='address' placeholder='Adresse' type='text'/></div>"+
	     	//"<div class='label'>N° Téléphone</div>"+
	     	"<div><input class='grouped' id='tel' placeholder='N° de téléphone' type='tel'/></div>"+
	     	//"<div class='label'>Activité</div>"+
	     	"<div class='select-input'>"+
	     		"<select id='job'>"+
	     			"<option>Agriculteur</option>"+
	     			"<option>Artisan</option>"+
	     			"<option>Cadre</option>"+
	     			"<option>Chef d'entreprise</option>"+
	     			"<option>Enseignant / Santé / Social</option>"+
	     			"<option>Employé</option>"+
	     			"<option>Etudiant</option>"+
	     			"<option>Ouvrier</option>"+
	     			"<option>Profession libérale</option>"+
	     			"<option>Retraité</option>"+
	     			"<option>Sans emploi</option>"+
	     			"<option>Autre</option>"+
	     		"</select>"+	
	     	"</div>"+
	     	//"<div class='label'>Nombre d'enfants</div>"+
	     	"<div class='select-input'>"+
	     		"<select id='child'>"+
	     			"<option>0</option>"+
	     			"<option>1</option>"+
	     			"<option>2</option>"+
	     			"<option>3</option>"+
	     			"<option>4</option>"+
	     			"<option>5</option>"+
	     		"</select>"+
	     	"</div>"+/*
	     	"<div class='label'>Email de mon parrain</div>"+
        	"<div class='input'><input id='parrain' type='email' /></div>"+*/
	     	"<div><input class='btn-large green' type='submit' value='Confirmer'/></div>");
		
		$('#name').val(data.name);
		$('#gender').val(data.gender);
		if(data.tel=='NULL'){
			$('#tel').val('');
		}else{
			$('#tel').val(data.tel);
		}
		$('#birthyear').val(data.birthyear);
		$('#job').val(data.job);
		$('#ville').val(data.ville);
		$('#postalcode').val(data.postalcode);
		$('#address').val(data.address);
		$('#child').val(data.nb_child);
		/*
		if(data.parrain!=''){
			$('#parrain').val(data.parrain);
			$('#parrain').attr('readonly','readonly');
		}
		*/
		$("img").error(function () {
			  $(this).unbind("error").attr("src", "img/icons/user-256-2.png");
		});
	}
	else{
		//Mail non valide
		$('.box').html('');//vide la page
		$('.box').append("<div class='box-notif error'>Afin d'utiliser les fonctionnalités éco-acteur, vous devez valider votre adresse email. Si vous n'avez pas reçu d'email, vous pouvez toujours vous en renvoyer un depuis le bouton ci-dessous.</div>"+
		"<div class='btn-large green' onClick='resendValidationMail()'>Renvoyez-moi un mail</div>");
	}
}