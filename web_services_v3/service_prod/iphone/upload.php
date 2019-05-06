<?php
//userPicture Upload
print_r($_FILES);

move_uploaded_file($_FILES["file"]["tmp_name"], dirname(__FILE__)."/usersImages/".$_FILES["file"]["name"]);
?>