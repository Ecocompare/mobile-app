var capturePhoto = function(){

	function onSuccess(imageURI){
		$('#photo').attr('src',imageURI);
		uploadPhoto(imageURI);
		
	}
    function onFail(message) {
        console.log('Camera Failed : ' + message);
    }
    
    navigator.camera.getPicture(onSuccess, onFail, {
        quality : 20,
        destinationType : Camera.DestinationType.FILE_URI
    });
}

var capturePhotoLibrary = function(){

	function onSuccess(imageURI){
		$('#photo').attr('src',imageURI);
		uploadPhoto(imageURI);
		document.location.reload();
	}
    function onFail(message) {
        console.log('Camera Failed : ' + message);
    }
    
    navigator.camera.getPicture(onSuccess, onFail, {
        quality : 20,
        sourceType: Camera.PictureSourceType.SAVEDPHOTOALBUM,
        destinationType : Camera.DestinationType.FILE_URI
    });
}
function uploadPhoto(imageURI){

	function onUploadError(error) {
	    console.log("An error has occurred: Code = " + error.code);
	    console.log("upload error source " + error.source);
	    console.log("upload error target " + error.target);
	}

	function onUploadSuccess(r) {
	    console.log("Code = " + r.responseCode);
	    console.log("Response = " + r.response);
	    console.log("Sent = " + r.bytesSent);
	}

	var options = new FileUploadOptions();
    options.fileKey="file";
    options.fileName= localStorage.getItem('idUser')+'.png';
    options.mimeType="image/png";

    var params = {};
    options.params = params;
    options.chunkedMode = false;

    var ft = new FileTransfer();

    //Upload on server side
    ft.upload(imageURI, "http://ecocompare.com/iphone/upload.php", onUploadSuccess, onUploadError, options);

}

