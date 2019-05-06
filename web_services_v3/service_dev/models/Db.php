<?php
class Db{

private $host = "localhost";
	private $username = "userecodev";
	private $password = "RtB76ioZs";
	private $database = "ecocompare_dev";
	
	private $bots=array('Googlebot', 'bingbot','ScoutJet','DotBot','Yahoo! Slurp','MJ12bot','Baiduspider/2.0','Twitmunin Crawler','msnbot-media','hotpage');
	
	function __construct(){
		mysql_connect($this->host, $this->username, $this->password);
		mysql_select_db($this->database);
		mysql_query("SET NAMES 'utf8'");
	}

	function resultsToArray($results){
		$array = array();
		while ($result = mysql_fetch_assoc($results)){
			$array[] = $result;
		}
		return $array;
	}
	function arrayToPairs($array){
		$str = '';
		foreach($array as $key=>$value){
			$str .= $key."='".addslashes($value)."', ";
		}
		if ($str != ''){
			$str = substr($str, 0, strlen($str) - 2);
		}
		return $str;
	}
	
	
	function logProductStat($productId)
	{
		
		//parcours des ligns bots, si trouvé on ne loggue pas
		foreach ($this->bots as $key=>$ligne) {
	
			$ligne=str_replace('/','\/',$ligne);
			//echo strtolower($ligne)."::".strtolower($_SERVER['HTTP_USER_AGENT'] )."::".preg_match("/$ligne/i",$_SERVER['HTTP_USER_AGENT'])."<br/>";
			
			if (preg_match("/$ligne/i",$_SERVER['HTTP_USER_AGENT']) > 0) return;
		 
					
			
		}
		
		$query="insert into productstats(productid,date,ip,useragent) values ($productId,now(),'".$_SERVER['REMOTE_ADDR']."','".$_SERVER['HTTP_USER_AGENT']."')";

		 $results = mysql_query($query);
     echo mysql_error();
	}
	
	
	function getMaxEanToComplete()
        {
      
        $query="SELECT count(*) as max FROM  iphone_eans_desc A where  A.description=''  and length(A.ean)=13 and A.noresult=0  ";
       $results = mysql_query($query);
			 $result = mysql_fetch_assoc($results);
       echo mysql_error();
             
		   return 	$result['max'];
        }
        
        function getLastScan()
        {
      
        $query="SELECT * FROM  iphone_query A,iphone_eans_desc B,iphone_users C where  A.user_id=C.id and A.longitude!='' and B.description!=''  and length(A.ean)=13 and B.noresult=0  and A.ean=B.ean order by A.date desc limit 1";
        $results = mysql_query($query);
				$scans = mysql_fetch_assoc($results);
				$scans['name']=str_replace('"','',$scans['name']);
				$scans['name']=trim(addslashes($scans['name']));
				$scans['description']=str_replace('"','',$scans['description']);
				$scans['description']=trim(addslashes($scans['description']));
				$scans['marque']=str_replace('"','',$scans['marque']);
			
				
				return $scans;
		   
        }
        
		function getEanToComplete()
        {
      
        $query="SELECT DISTINCT A.id,A.ean,DATE_FORMAT(B.date,'%Y-%m-%d') as date_request,A.description,A.marque,A.produitvert,A.noresult FROM  iphone_eans_desc A,iphone_query B where A.ean=B.ean and A.ean and A.description=''  and length(A.ean)=13 and A.noresult=0   order by B.date desc,marque asc  limit 20";
        $results = mysql_query($query);

        echo mysql_error();
             
        return $this->resultsToarray($results);
        }
        
        
	//iPhone
//scanning week

		function getRandomTestimony() {
			$query = "SELECT * from testimony";
			$results = mysql_query($query);
			   
			$r = rand(0,mysql_num_rows($results) - 1); // On tire une ligne au hasard
			$query = "SELECT * from testimony LIMIT $r, 1";
			$results = mysql_query($query);
			return mysql_fetch_assoc($results);
			 
		}
		
		function getSWUsers($dpt)
        {
        if ($dpt=="")
        $query="SELECT A.user_id,email,name,tel,total from iphone_query_SW A, iphone_users B where A.user_id=B.id   order by total desc, name asc limit 50";
        else
        $query="SELECT A.user_id,email,name,tel,total from iphone_query_SW A, iphone_users B where A.user_id=B.id and A.dpt in ($dpt) order by total desc, name asc limit 50";


                $results = mysql_query($query);
                echo mysql_error();
             
                return $this->resultsToarray($results);
        }
        
function getMaxSWUsers($dpt)
        {
        if ($dpt=="")
        $query="SELECT count(*) as max from iphone_query_SW A, iphone_users B where A.user_id=B.id  ";
        else
        $query="SELECT count(*) as max from iphone_query_SW A, iphone_users B where A.user_id=B.id and A.dpt in ($dpt) ";
        $results = mysql_query($query);
			 	$result = mysql_fetch_assoc($results);
       	echo mysql_error();
				return 	$result['max'];
 }
        
    function getScanCategory($cat) {
    	  $query="SELECT *  FROM `iphone_eans_desc` WHERE `description` LIKE '%".$cat."%' AND `produitvert` = 1 order by hit desc, ecocompare desc";
        $results = mysql_query($query);
        echo mysql_error();
        return $this->resultsToarray($results);
    	}
    	
    function getCountScanCategory($cat,$flag) {
    	 if ($flag=='scancount')  $query="SELECT count(*) as nombre  FROM `iphone_eans_desc` WHERE `description` LIKE '%".$cat."%' AND `produitvert` = 1 and ecocompare=0 ";
    	 if ($flag=='ecoscancount') $query="SELECT count(*) as nombre  FROM `iphone_eans_desc` WHERE `description` LIKE '%".$cat."%' AND `produitvert` = 1 and ecocompare!=0 ";
    	 if ($flag=='userscancount') $query="SELECT count(*) as nombre FROM iphone_eans_desc A,iphone_query B WHERE A.description LIKE '%".$cat."%' AND A.produitvert = 1 and A.ean=B.ean"; 
        $results = mysql_query($query);
			 	$result = mysql_fetch_assoc($results);
       	echo mysql_error();
				return 	$result['nombre'];
    	}
    	

        
		function getSWEans($dpt)
        {
            if ($dpt=="")
               $query="SELECT *,B.description as description from iphone_eans_SW A,iphone_eans_desc B left join products on products.id=B.ecocompare where A.ean=B.ean  order by total desc,A.ean desc limit 50";
               else   
        				$query="SELECT *,B.description as description from iphone_eans_SW A,iphone_eans_desc B left join products on products.id=B.ecocompare where A.ean=B.ean  and A.dpt in ($dpt)  order by total desc,A.ean desc limit 50";
                $results = mysql_query($query);
                echo mysql_error();
               
                return $this->resultsToarray($results);
        }
        
         function getMaxSWEans($dpt)
        {
            if ($dpt=="")
               $query="SELECT count(*) as max from iphone_eans_SW A,iphone_eans_desc B left join products on products.id=B.ecocompare where A.ean=B.ean  ";
               else   
        				$query="SELECT count(*) as max from iphone_eans_SW A,iphone_eans_desc B left join products on products.id=B.ecocompare where A.ean=B.ean  and A.dpt in ($dpt) ";
                $results = mysql_query($query);
				 $result = mysql_fetch_assoc($results);
                echo mysql_error();
               
                return 	$result['max'];
        }
        
	
	
	
        function getEcoactors($month,$year,$limit)
        {
        if ($year=="") $year=date("Y");
        $query="SELECT B.id as id,email,name,tel,total,nbwin,totalproduitvert,month,year,win_max,B.prevsort,win_maxgreen,useragent from iphone_query_month A, iphone_users B where A.user_id=B.id and month='$month' and year='$year' order by sort asc, totalproduitvert desc, name asc limit $limit";
                $results = mysql_query($query);
                echo mysql_error();
             
                return $this->resultsToarray($results);
        }
         
         function getMaxGreenProduct($month,$year)
        {
        if ($year=="") $year=date("Y");
        $query="SELECT max(totalproduitvert) as max  from iphone_query_month A, iphone_users B where A.user_id=B.id and month='$month' and year='$year'order by total desc, totalproduitvert desc";
                $results = mysql_query($query);
                $result = mysql_fetch_assoc($results);
                echo mysql_error();
                return 	$result['max'];
        }
        
 function getScanRegion() {
	$query="select count(*) as nombre ,googleAdministrativeAreaName  as region from iphone_query A, wgs84_codes_postaux B  where A.codeinsee=B.codeinsee and A.codeinsee!=0 and googleAdministrativeAreaName !='' group by googleAdministrativeAreaName  order by count(*) desc";
	 $results = mysql_query($query);
    echo mysql_error();
   return $this->resultsToarray($results);
}

 	function getIphoneBrands($limit=0)
        {
        $query="Select C.libelle as marque, C.action as action, count(A.ean) as nombre from iphone_query A,iphone_eans_desc B, iphone_marqueverte C where  C.libelle=B.marque and A.ean=B.ean group by C.libelle desc ORDER BY count(A.ean) DESC, C.libelle asc";
        if ($limit!=0) $query.=" limit $limit";
      
                $results = mysql_query($query);
                echo mysql_error();
                return $this->resultsToarray($results);
        }
        
	  function getIphoneEans($month,$year,$limit=15)
        {
        if ($year=="") $year=date("Y");
        if ($month=="") $year=date("m");
        
        //$query="SELECT *,B.description as description,B.hit as hit from iphone_eans A,iphone_eans_desc B left join products on products.id=B.ecocompare where A.month='$month' and A.year='$year' and A.ean=B.ean  order by total desc,A.ean desc";
        $query="SELECT * from iphone_eans_desc where produitvert=1 and ecocompare=0 order by hit desc,ean desc limit $limit";

                $results = mysql_query($query);
                echo mysql_error();
               
                return $this->resultsToarray($results);
        }
        
        

	// stats
	function getStat($cle){
		$query = "select valeur from statistics where cle='$cle'";
		$results=mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result['valeur'];
	}
	
	function getStatMonth($month="",$year=""){
		//si pas de mois en param, prendre le mois en cours
		if ($month=="") $month=date("m");
		if ($year=="") $year=date("Y");
		$query = "select * from iphone_recap_mois where month='$month' and  year='$year'";
		$results=mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	
	
function getBrands() {
		$query = "select distinct C.userid as userid,A.title from companies A,users B,products C where C.userid=B.id and A.id=B.companyid order by title asc ";
		
		$results = mysql_query($query);
		return $this->resultsToArray($results);
}	
	
		// stats
	function getType($typeId){
		 $query = "select * from type where id=$typeId order by name asc";
		
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	
		function getTypes(){
	  $query = "select * from type  order by name asc";
		$results = mysql_query($query);
		return $this->resultsToArray($results);
	}
	
	function getTypeDetail($typeId,$lang=1){
		$query = "select typescore.id as id, m_theme.name as theme, m_lifecycle.name as lifecycle, scoreideal  from typescore left join m_lifecycle on lifecycle_id=m_lifecycle.id and m_lifecycle.lang=$lang,m_theme where m_theme.lang=$lang and m_theme.id=theme_id and type_id=$typeId order by theme_id asc,lifecycle_id asc";
			$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}
	
	
	
		function duplicateProduct($id) {
		
		$query="insert into products(userid,typeid,equitable_sud,refid,state,image,name,brand,EAN,description,tags,rating1comment,rating2comment,rating3comment,rating4comment,rating5comment,rating6comment,price,facts,findit,related,created,pubdate,published,selec,contact,co2,majdate,majuserid,AE,rating1,rating2,rating3,score1,score2,score3,score4,globalcomment) ";
		$query.=" select userid,typeid,equitable_sud,refid,state,image,name,brand,EAN,description,tags,rating1comment,rating2comment,rating3comment,rating4comment,rating5comment,rating6comment,price,facts,findit,related,".time().",0,'saved',selec,contact,co2,0,majuserid,AE,rating1,rating2,rating3,score1,score2,score3,score4,globalcomment from products where id=$id";
		//echo $query."<br/>";
		$results = mysql_query($query);
		echo mysql_error();
		
		$new_id = mysql_insert_id();
		
		$query="insert into productscore(product_id,theme_id,lifecycle_id,score,note) select $new_id,theme_id,lifecycle_id,score,note from productscore where product_id=$id";
		$results = mysql_query($query);
		echo mysql_error();
		
		$query=" insert into productsubratings (productid,subratingid,comment,feedback,islabel) select $new_id,subratingid,comment,feedback,islabel from productsubratings where productid=$id";
		$results = mysql_query($query);
		echo mysql_error();
		
			$query=" insert into productlabels (productid,labelid) select $new_id,labelid from productlabels where productid=$id";
		$results = mysql_query($query);
	
		$query=" insert into productcategories (productid,categoryid) select $new_id,categoryid from productcategories where productid=$id";
		$results = mysql_query($query);
	
		$query=" insert into productindicator (productid,indicatorid,count) select $new_id,indocatorid,count from productindicator where productid=$id";
		$results = mysql_query($query);
	
		return $new_id;
	}
	
	
	
	//Product

	function createProduct($params){
		$query = "INSERT INTO products SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();

		$query = "SELECT MAX(id) AS maxid FROM products";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}
	function oldgetProducts(){
		$query = "SELECT * from products";
		$results = mysql_query($query);
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$products[] = $product;
		}
		return $products;
	}
	
	function getProductsSelection($condition){
		$query = "SELECT * from products WHERE $condition AND published='true' GROUP BY brand ORDER BY pubdate DESC LIMIT 16 OFFSET 0";
		$results = mysql_query($query);
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['commentscount'] = $this->countComments($product['id']);
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$products[] = $product;
		}
		shuffle($products);
		$products = array_slice($products, 0, 4);
		
		return $products;
	}

	function getAllProducts(){
		$query = "SELECT * from products";
		$results = mysql_query($query);
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$products[] = $product;
		}
		return $products;
	}

function getBrandPartnerByCompany($companyId){
		$query = "SELECT partner,url FROM partnerbrandlink,companies  WHERE brandid=ecosapiens_id and companies.id=".intval($companyId)."";
		$results = mysql_query($query);
		$partners=array();

		echo mysql_error();
		while ($partner = mysql_fetch_assoc($results)){
			$partners[] = $partner;
		}
		return $partners;
}

function getProductPartnerByProduct($productId,$partner){
		$query = "SELECT url,price,partnerid FROM partnerproductlink  WHERE partner='$partner' and productid=".intval($productId)." order by price asc limit 1";
		$results = mysql_query($query);

		echo mysql_error();
		$partners=array();

		while ($partner = mysql_fetch_assoc($results)){
			$partners[] = $partner;
		}
		return $partners;
}

	
	function getProductsByCompany($companyId, $published = 'true'){
		$query = "SELECT id FROM users WHERE companyid='".intval($companyId)."'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		if ($result == null){
			return array();
		}else{
			$query = "SELECT * FROM products WHERE published='$published' AND userid='".$result['id']."' order by pubdate desc";
			$results = mysql_query($query);
			echo mysql_error();
			$products = array();		
			while ($product = mysql_fetch_assoc($results)){
				$product['commentscount'] = $this->countComments($product['id']);
				$product['labels'] = $this->getProductLabels($product['id']);
				$products[] = $product;
			}
			return $products;
		}
	}

	function getProducts($categoryId = "", $orderBy = "", $published = 'true', $offset = 0, $limit = 10, $userId = 0){
		if ($categoryId == ""){
			$query = "SELECT * from products WHERE published='$published'";
		}else{
			$query = "SELECT * from products, productcategories WHERE products.id=productcategories.productid AND categoryid='".addSlashes($categoryId)."' AND published='$published'";
		}
		if ($orderBy == ""){
			$orderStatement = " ORDER BY name ASC";
		}else{
			$orderStatement = " ORDER BY $orderBy";
		}
		if ($userId != 0){
			$query .= " AND userid='$userId'";
		}
		$results = mysql_query($query.$orderStatement." LIMIT $limit OFFSET $offset");
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['commentscount'] = $this->countComments($product['id']);
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$product['referer'] = $this->getReferer($product['id']);
			$products[] = $product;
		}
		return $products;
	}

	function getUserProducts($userId){
		$query = "SELECT * from products WHERE userid='$userId' AND published <> 'false' ORDER BY name ASC";
		$results = mysql_query($query);
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['commentscount'] = $this->countComments($product['id']);
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$product['referer'] = $this->getReferer($product['id']);
			$products[] = $product;
		}
		return $products;
	}

	function getReferer($id){
		$query = "SELECT * from products WHERE refid='$id'";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result;
	}

	/*function advancedGetProducts($categoryId = 0, $orderBy = "", $label = 0, $brand = "", $limit = 0, $offset = 0){
		
		$from = array();
		$from[] = "products";
		$where = array();
	
		if ($categoryId != 0){
		
	
			$from[] = "productcategories";

			$tempWhere = "(categoryid = '$categoryId'";
			$subcategories = $this->getCategoriesFlattened($categoryId);
			foreach ($subcategories as $key => $subcategory){
				$tempWhere .= " OR categoryId='".$subcategory['id']."'";
			}
			$tempWhere .= ')';
			$where[] = $tempWhere;

			$where[] = "productcategories.productid = products.id";
		}
		if ($label != ""){
			$from[] = "productlabels";
			$where[] = "labelid = $label";
			$where[] = "productlabels.productid = products.id";
		}
		if ($brand != ""){
			$where[] = "brand = '$brand'";
		}
		
		$where[] = "published = 'true'";
		
		$query = "SELECT * FROM ";
		foreach ($from as $key => $value){
			if ($key != 0){
				$query .= ", ";
			} 
			$query .= $value;
		}
		foreach ($where as $key => $value){
			if ($key == 0){
				$query .= " WHERE ";
			}else{
				$query .= " AND ";
			} 
			$query .= $value;
		}
		
		if ($orderBy == ""){
			$query .= " ORDER BY name ASC";
		}else{
			$query .= " ORDER BY $orderBy";
		}
		
		if ($limit != 0){			
			$query .= " LIMIT $limit OFFSET $offset";
		}
		
		$results = mysql_query($query);
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$products[] = $product;
		}
		return $products;
	}

	function searchProducts($keywords = '', $categoryId = 0, $orderBy = "", $label = 0, $brand = "", $limit = 0, $offset = 0){
		
		$keywords = addslashes($keywords);
		$categoryId = intval($categoryId);
		
		$from = array();
		$from[] = "products";
		$where = array();
	
		if ($keywords != ''){
			$where[] = "MATCH (name, description, tags, brand) AGAINST ('$keywords*' IN BOOLEAN MODE)";
		}
	
		if ($categoryId != 0){
		
	
			$from[] = "productcategories";

			$tempWhere = "(categoryid = '$categoryId'";
			$subcategories = $this->getCategoriesFlattened($categoryId);
			foreach ($subcategories as $key => $subcategory){
				$tempWhere .= " OR categoryId='".$subcategory['id']."'";
			}
			$tempWhere .= ')';
			$where[] = $tempWhere;

			$where[] = "productcategories.productid = products.id";
		}
		if ($label != ""){
			$from[] = "productlabels";
			$where[] = "labelid = $label";
			$where[] = "productlabels.productid = products.id";
		}
		if ($brand != ""){
			$where[] = "brand = '$brand'";
		}
		
		$query = "SELECT * FROM ";
		foreach ($from as $key => $value){
			if ($key != 0){
				$query .= ", ";
			} 
			$query .= $value;
		}
		foreach ($where as $key => $value){
			if ($key == 0){
				$query .= " WHERE ";
			}else{
				$query .= " AND ";
			} 
			$query .= $value;
		}
		
		if ($orderBy == ""){
			$query .= " ORDER BY name ASC";
		}else{
			$query .= " ORDER BY $orderBy";
		}
		
		if ($limit != 0){			
			$query .= " LIMIT $limit OFFSET $offset";
		}
		
		$results = mysql_query($query);
		echo mysql_error();
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$products[] = $product;
		}
		return $products;
	}*/

	function searchProducts2($keywords = '', $categoryId = 'new', $orderBy = "", $label = 0, $brand = "", $limit = 0, $offset = 0, $justCount = false, $personnal=''){
		
		$keywordsArray = explode(' ', $keywords);
		
		$keywords = '';
		foreach($keywordsArray as $key=>$keyword){
			$keywords .= "+$keyword ";
		}
		
		if ($personnal !='') {
		list($choix_score1, $choix_score2, $choix_score3) =
    preg_split("/_/", $personnal, 3);
		}
		
		$keywords = mysql_real_escape_string($keywords);
		
		$from = array();
		$from[] = "products";
		$where = array();
	
		if ($keywords != '+ '){
			$where[] = "MATCH (name, description, tags, brand, EAN) AGAINST ('".$keywords."' IN BOOLEAN MODE)";
		}
		
		if ($categoryId == "selection"){
			$where[] = "score4 >= 2";
		}else if ($categoryId != "new") {
			$categoryId = intval($categoryId);
			$from[] = "productcategories";

			$tempWhere = "(categoryid = '$categoryId'";
			$subcategories = $this->getCategoriesFlattened($categoryId);
			foreach ($subcategories as $key => $subcategory){
				$tempWhere .= " OR categoryId='".$subcategory['id']."'";
			}
			$tempWhere .= ')';
			$where[] = $tempWhere;

			$where[] = "productcategories.productid = products.id";
		}

		if ($label != ""){
			$from[] = "productlabels";
			$where[] = "labelid = $label";
			$where[] = "productlabels.productid = products.id";
		}
		if ($brand != ""){
			$where[] = "brand = '$brand'";
		}
		$where[] = "published = 'true'";
		
		if ($justCount == false){
			$query = "SELECT * FROM ";
		}else{
			$query = "SELECT count(DISTINCT id) as count FROM ";
		}	
			
		foreach ($from as $key => $value){
			if ($key != 0){
				$query .= ", ";
			} 
			$query .= $value;
		}
		foreach ($where as $key => $value){
			if ($key == 0){
				$query .= " WHERE ";
			}else{
				$query .= " AND ";
			} 
			$query .= $value;
		}
		if ($justCount == false){
			$query .= " GROUP BY id";
		}
		
		if ($personnal !='') { 
		$query .= " ORDER BY ((score1*$choix_score1)+(score2*$choix_score2)+(score3*$choix_score3)) DESC "; }
		else  {
		
			if ($orderBy == ""){
				$query .= " ORDER BY name ASC";
			}else{
				$query .= " ORDER BY $orderBy";
			}
		
	}
		
		if ($limit != 0){			
			$query .= " LIMIT $limit OFFSET $offset";
		}
		
		//echo "$query";
	
		$results = mysql_query($query);
		
		if ($justCount == true){
			$result = mysql_fetch_assoc($results);
			return $result['count'];
		}
		
		$products = array();		
		while ($product = mysql_fetch_assoc($results)){
			$product['commentscount'] = $this->countComments($product['id']);
			$product['categories'] = $this->getProductCategories($product['id']);
			$product['labels'] = $this->getProductLabels($product['id']);
			$products[] = $product;
		}
		return $products;
	}

function getMaxScore($typeId,$themeId,$lifecycleId=0){
		$query = "SELECT scoreideal from  typescore where type_id=$typeId and theme_id=$themeId and lifecycle_id=$lifecycleId";
		$results = mysql_query($query);
		echo mysql_error();
		$score = mysql_fetch_assoc($results);
			
		return $score['scoreideal'];
	}
	
function getProductNote($productId=0,$themeId=0,$lifecycleId=0){
		$query = "SELECT note from  productscore where theme_id=$themeId and lifecycle_id=$lifecycleId and product_id=$productId";
		$results = mysql_query($query);
		//echo mysql_error();
		$score = mysql_fetch_assoc($results);
			
		return $score['note'];
	}

	
	function getProduct($id){
		$query = "SELECT products.* from products left join type on products.typeid=type.id where products.id=".addSlashes($id);
		$results = mysql_query($query);

		echo mysql_error();
	
		$product = mysql_fetch_assoc($results);
		$product['categories'] = $this->getProductCategories($id);
		$product['labels'] = $this->getProductLabels($id);
		$product['subratings'] = $this->getProductSubratings($id);
		$product['subratingsdetail'] = $this->getProductSubratings($id, true,false,false);
		$product['subratingsfeedback'] = $this->getProductSubratings($id, false,true,false);
		$product['subratingslabel'] = $this->getProductSubratingsComments($id);
		$product['indicators']=$this->getProductIndicator($id);
		
		return $product;
	}
	
	function saveProductIndicator($productId) {
		
		$query="delete from productindicator where productid=$productId";
		$results = mysql_query($query);
		
		$query="insert into productindicator(productid,indicatorid,count) select $productId, indicator_id, count(*) from m_crit_indic, productsubratings where criteria_id=subratingid and productid=$productId group by indicator_id order by count(*) desc LIMIT 3";
		$results = mysql_query($query);
		
		return;
	}
	function getProductIndicator($productId,$lang=1) {
		$query="select  indicatorid, name, count from productindicator left join m_indicator on m_indicator.id=indicatorid where productid=$productId and m_indicator.lang=$lang";
		$results = mysql_query($query);
		
		echo mysql_error();
	
		return $this->resultsToArray($results);
		
	}
	
	function getSubratingsComment($productId,$subratingId) {
			$query = "SELECT comment from productsubratingscomments WHERE productid=$productId and subratingid=$subratingId";
			$results = mysql_query($query);
			$partners=array();

			echo mysql_error();
	
		return $this->resultsToArray($results);
	
	} 
	
	
	//on recherche les commentaires de labels pour chaque critère d'un produit
	function getProductSubratingsComments($productId){
		$query = "SELECT distinct subratingid from productsubratingscomments WHERE productid=$productId";
		echo mysql_error();
		$results = mysql_query($query);
		$rv = array();
		while ($result = mysql_fetch_assoc($results)){
	
		$rv[$result['subratingid']]=$this->getSubratingsComment($productId,$result['subratingid']);
		
		//$query2 = "SELECT comment from productsubratingscomments WHERE productid=$productId and subratingid=".$result['subratingid'];
		//echo mysql_error();
		//$results2 = mysql_query($query2);
		//while ($result2 = mysql_fetch_assoc($results2)){
		
			//	$rv[$result['subratingid']][] = stripslashes($result2['comment']);
			
			//}
			
		}
		
		return $rv;
	}
	
	function getProductFeedback($id){
		$query = "SELECT count(*) as nombre from productsubratings  where feedback!='' and productid=".addSlashes($id);
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['nombre'];
	}
	
	
	function getProductCategories($id){
		$query = "SELECT categories.id, categories.name FROM productcategories, categories WHERE categories.id=categoryid AND productid='".addSlashes($id)."'";
		$results = mysql_query($query);
		$categories = $this->resultsToArray($results);
		echo mysql_error();
		return $categories;
		
	}
	function getProductLabels($id){
		$query = "SELECT labels.id, labels.name,labels.image,labels.description FROM productlabels, labels WHERE labels.id=labelid AND productid='".addSlashes($id)."'";
		$results = mysql_query($query);
		$categories = $this->resultsToArray($results);
		echo mysql_error();
		return $categories;
		
	}
	
	//rajoute le label si un des critères est sélectionné (fabrication en France par exemple)
	function syncLabelsWithSubratings($productId){
		$query = "SELECT * FROM labels WHERE subratingid <> 0";
		$results = mysql_query($query);
		while ($label = mysql_fetch_assoc($results)){
			$query = "SELECT * FROM productsubratings WHERE productid='$productId' AND subratingid = '".$label['subratingid']."'";
		
			$results2 = mysql_query($query);
			$result = mysql_fetch_assoc($results2);

			$query = "DELETE FROM productlabels WHERE productid='$productId' AND labelid = '".$label['id']."'";
			mysql_query($query);
			
			
			if ($result != null){
				$query = "INSERT INTO productlabels SET productid='$productId', labelid = '".$label['id']."'";
		
				mysql_query($query);

			}
		}
	}
	
	function updateEan($ean,$description,$marque,$produitvert,$introuvable){

		$query = "UPDATE iphone_eans_desc set description='$description', marque='$marque',produitvert=$produitvert, noresult=$introuvable  where ean='$ean'";

		mysql_query($query);
		echo mysql_error();
	}
	
	function updateProduct($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE products set $pairs where id='$id'";

		mysql_query($query);
		echo mysql_error();
	}
	function updateProductCategories($id, $categories){
		$query = "DELETE FROM productcategories WHERE productid='$id'";
		mysql_query($query);		
		foreach($categories as $key=>$categoryId){
			$query = "INSERT INTO productcategories SET productid='$id', categoryid='$categoryId'";
			mysql_query($query);
			echo mysql_error();
		}
	}
	function deleteProduct($id,$userid){
		
		//si pas admin on check que c'est bien le produit du compte utilisateur
		if (! admin()) {
				$query2 = "SELECT userid from products where id=$id";
				$results2 = mysql_query($query2);
				echo mysql_error();
				$result2 = mysql_fetch_assoc($results2);
				$product_userid= $result2['userid'];
		  	if ($userid != $product_userid) {echo "Compte utilisateur interdit";
		  		exit;
		  	return;}
  	}
		$query = "DELETE FROM products WHERE id='$id' ";
		mysql_query($query);	
		echo mysql_error();	
		$query = "DELETE FROM productcategories WHERE productid='$id' ";
		mysql_query($query);	
		echo mysql_error();	
		$query = "DELETE FROM productlabels WHERE productid='$id'";
		mysql_query($query);	
		echo mysql_error();	
		$query = "DELETE FROM productsubratings WHERE productid='$id'";
		mysql_query($query);	
		echo mysql_error();	
	}

	//Label

	function getLabels($labelId=0,$includeCriteria=false,$includeType=false){
		$query = "SELECT * from labels ";
		if ($labelId!=0) $query.=" where id=$labelId";
		$query.=" order by name asc";
		$results = mysql_query($query);
		$labels = array();		

		$labels = mysql_fetch_assoc($results);
		if ($includeCriteria) {$labels['criterias'] = $this->getCriteriaLabel($labelId);}
		if ($includeType) {$labels['types'] = $this->getTypeLabel($labelId);}

		if ($labelId==0) return $this->resultsToArray($results); else return $labels;

} 

	
		function getLabelName($id){
		$query = "SELECT * from labels where id=$id";
		$results = mysql_query($query);
		echo mysql_error();
		
		$label = mysql_fetch_assoc($results);
		return $label['name']; 
		
	
	}
	
		function getTypelabel($labelId){
		$query = "SELECT type.id,type.name as name from type, typelabel where type.id=typeid and labelid=$labelId";
		$results = mysql_query($query);
		echo mysql_error();
	//	echo $query;
		return $this->resultsToArray($results);
		return $array;
	} 
	
		function getCriteriaLabel($labelId,$lang=1){
		$query = "SELECT  m_criteria_label.id as id,m_criteria.name as name,m_theme.name as theme,m_lifecycle.name as lifecycle,value from m_criteria_label,m_criteria left join m_theme on m_theme.id=theme_id and m_theme.lang=$lang left join m_lifecycle on m_lifecycle.id=lifecycle_id and m_lifecycle.lang=$lang where label_id=$labelId and criteria_id=m_criteria.id and m_criteria.lang=$lang order by theme_id,lifecycle_id, m_criteria.id asc ";
		$results = mysql_query($query);
		echo mysql_error();
		//echo $query;
		return $this->resultsToArray($results);
		return $array;
	}
	
	
	function getLabelsType($typeid){
		$query = "SELECT * from labels,typelabel where typeid='$typeid' and labelid=id order by name asc";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToArray($results);
		return $array;
	}
	
	
	//on enregistre les labels affectés au produit ainsi que les critères associés au label
	function updateProductLabels($id, $labels){
		$query = "DELETE FROM productlabels WHERE productid='$id'";
		mysql_query($query);		
		
		//on détermine si produit équitable sud
		$query = "SELECT equitable_sud from products WHERE id='$id'";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		$equitable_sud= $result['equitable_sud'];
		$sql_equitable="0";
		
		if ($result['equitable_sud'] ==1) $not_include="7"; else $not_include="6";
						
		$query = "DELETE FROM productsubratings WHERE productid='$id' and islabel=1";
		mysql_query($query);		
		
		$query = "DELETE FROM productsubratingscomments WHERE productid='$id'" ;
		mysql_query($query);		
		
		
		 //insertion des labels affectés à certains critères (made in france, pas de tests animaux, ...)
		 $query="insert into productlabels (productid,labelid) select $id,A.id from labels A,productsubratings B where A.subratingid=B.subratingid and B.productid=$id";
		 	mysql_query($query);	
		 	
		foreach($labels as $key=>$labelId){
	
			$query = "INSERT INTO productlabels SET productid='$id', labelid='$labelId'";
			mysql_query($query);
			
			//V2 on check les labels associés pour rechercher les critères liés
			//on insere une entrée critere
			$query2="insert into productsubratings(productid,subratingid,islabel) select $id,m_criteria.id,1 from m_criteria_label,m_criteria where m_criteria.theme_id != $not_include and m_criteria.id=criteria_id and m_criteria.lang=1 and label_id='$labelId' and m_criteria.id not in (select subratingid from productsubratings where productid='$id' and islabel=1)";
			$results2=mysql_query($query2);		
			//echo $query2."<br/>";
			//on insère les justifications de label
			$query2="insert into productsubratingscomments(productid,subratingid,labelid,comment) select $id,m_criteria.id,label_id,CONCAT('Garanti par le label ',labels.name) from m_criteria_label left join labels on labels.id=label_id,m_criteria where m_criteria.theme_id != $not_include and m_criteria.id=criteria_id and m_criteria.lang=1 and label_id='$labelId' ";
			$results2=mysql_query($query2);		
			//echo $query2."<br/>";
			echo mysql_error();

			
			
		}

	}
	
	// Comments
	
	function addComment($params){
		$paramsString = $this->arrayToPairs($params);
		$time = time();
		$query = "INSERT INTO comments SET $paramsString, date='$time'";
		mysql_query($query);
		echo mysql_error();
		
	}
	
	function updateComment($id, $params){
		$paramsString = $this->arrayToPairs($params);
		$time = time();
		$query = "UPDATE comments SET $paramsString WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
		
	}
	
	function getPendingComments(){
		$query = "SELECT * from comments WHERE state='pending' ORDER BY date";
		$results = mysql_query($query);
		$rv = array();
		while ($result = mysql_fetch_assoc($results)){
			if ($result['type'] == 'product'){
				$result['product'] = $this->getProduct($result['productid']);
			}else if ($result['type'] == 'article'){
				$result['article'] = $this->getArticle($result['productid']);
			}else if ($result['type'] == 'match'){
				$result['match'] = $this->getMatch($result['productid']);
			}
			$rv[] = $result;
		}
		return $rv;
		echo mysql_error();
		
	}
	
	function countComments($id){
		$query = "SELECT COUNT(*) as count from comments WHERE state='published' AND productid='$id'";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		echo mysql_error();
		return $result['count'];
	}
	
	function getComments($type, $id){
		$query = "SELECT * from comments WHERE state='published' AND productid='".addSlashes($id)."' AND type='$type' ORDER BY date";
		$results = mysql_query($query);
		return $this->resultsToArray($results);
		echo mysql_error();
		
	}
	
	function getComment($id){
		$query = "SELECT * from comments WHERE id='$id'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	
	// Categories
	
	function getCategories($parentId = 0){
		$query = "SELECT * from categories WHERE parentid='$parentId' ORDER BY name";
		$results = mysql_query($query);
		$array = array();
		while ($result = mysql_fetch_assoc($results)){
			$result['subcategories'] = $this->getCategories($result['id']);
			$result['productscount'] = count($this->getProducts($result['id'])); // Optimisable
			$array[] = $result;
		}
		return $array;
		echo mysql_error();
		
	}
	
	function getCategoriesFlattened($parentId = 0){
		$query = "SELECT * from categories WHERE parentid='$parentId'";
		$results = mysql_query($query);
		echo mysql_error();
		$array = array();
		while ($result = mysql_fetch_assoc($results)){
			$array[] = $result;
			$deepArray = $this->getCategoriesFlattened($result['id']);
			foreach ($deepArray as $key=>$category){
				$array[] = $category;
			}
		}
		return $array;
	}
	
	function createCategory($params){
		$query = "INSERT INTO categories SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}
	function getCategory($id){
		if ($id>0) {
		$query = "SELECT * from categories WHERE id='$id'";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		$result['subcategories'] = $this->getCategories($result['id']);
		$result['productscount'] = count($this->getProducts($result['id'])); // Optimisable
		echo mysql_error();
				return $result;
} else return;
		
	}
	function updateCategory($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE categories set $pairs where id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	function deleteCategory($id){
		$query = "DELETE FROM categories WHERE id='$id'";
		mysql_query($query);	
		echo mysql_error();
		$query = "DELETE FROM productcategories WHERE categoryid='$id'";
		mysql_query($query);	
		echo mysql_error();
	}
	
	// Matches
	
	function getMatches(){
		$query = "SELECT * from matches ORDER BY id DESC";
		$results = mysql_query($query);
		echo mysql_error();
		$matches = array();
		while($result = mysql_fetch_assoc($results)){
			$query = "SELECT * from matchproducts WHERE matchid='".$result['id']."'";
			$results2 = mysql_query($query);
			echo mysql_error();
			$productids = array();
			while ($result2 = mysql_fetch_assoc($results2)){
				$productids[] = $result2['productid'];
			}
			$result['productids'] = $productids;
			$matches[] = $result;
		}
		return $matches;
	}

	function getMatch($id){
		$query = "SELECT * from matches WHERE id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		$query = "SELECT * from matchproducts WHERE matchid='".$result['id']."'";
		$results2 = mysql_query($query);
		echo mysql_error();
		$productids = array();
		while ($result2 = mysql_fetch_assoc($results2)){
			$productids[] = $result2['productid'];
		}
		$result['productids'] = $productids;
		return $result;
	}

	function setMatchProducts($id, $productIds){
		$query = "DELETE FROM matchproducts WHERE matchid='$id'";
		mysql_query($query);
		echo mysql_error();
		foreach($productIds as $key=>$productId){
			$query = "INSERT INTO matchproducts SET matchid='$id', productid='".$productId."'";
			mysql_query($query);
			echo mysql_error();
		}
	}

	function addMatch($title){
		$query = "INSERT INTO matches SET title='$title'";
		mysql_query($query);
		echo mysql_error();
		
		$query = "SELECT MAX(id) as maxid FROM matches";
		$results = mysql_query($query);;
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
 		return $result['maxid'];
	}
	
	function updateMatch($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE matches SET $pairs WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	
	function deleteMatch($id){
		$query = "DELETE FROM matches WHERE id='$id'";
		echo $query;
		$results = mysql_query($query);;
		echo mysql_error();
	}
	
	// Article
	
	function getArticles($limit = 0){
		$query = "SELECT * from articles where enable=true ORDER BY created DESC";
		if ($limit != 0){
			$query .= " LIMIT $limit";
		}
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
		
	}
	function getArticle($id){
		$query = "SELECT * from articles WHERE id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	function updateArticle($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE articles set $pairs where id='".addSlashes($id)."'";
		mysql_query($query);
		echo mysql_error();
	}
	function addArticle($params){
		$pairs = $this->arrayToPairs($params);
		$query = "INSERT INTO articles SET $pairs";
		mysql_query($query);
		echo mysql_error();
		$query = "SELECT MAX(id) AS maxid FROM articles";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}
	function deleteArticle($id){
		$query = "DELETE FROM articles WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	
		function createArticleImage($params){
		$query = "INSERT INTO articleimages SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
		$query = "SELECT MAX(id) AS maxid FROM articleimages";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}
	
	// news
	function getNews($limit = 0){
		$query = "SELECT * from news ORDER BY created DESC";
		if ($limit != 0){
			$query .= " LIMIT $limit";
		}
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
		
	}
	function getNew($id){
		$query = "SELECT * from news WHERE id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	function updateNew($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE news set $pairs where id='".addSlashes($id)."'";
		mysql_query($query);
		echo mysql_error();
	}
	function addNew($params){
		
		$pairs = $this->arrayToPairs($params);
		$query = "INSERT INTO news SET $pairs";
		mysql_query($query);
		echo mysql_error();
		$query = "SELECT MAX(id) AS maxid FROM news";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}
	function deleteNew($id){
		$query = "DELETE FROM news WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	


	// Company
	
	function getCompanies($limit = 0, $orderBy = "title ASC", $userId = 0, $includeBlank = false, $backoffice=false){
		$query = "SELECT * from companies where 1=1  ";
		if ($backoffice == false){
			$query .= " and enable=1";
		}
		
		if ($userId != 0){
			$query .= " and userid='$userId'";
		}
		if ($includeBlank == false){
			$query .= " and text<>''";
		}
		
		$query .= " ORDER BY $orderBy";
		
		if ($limit != 0){
			$query .= " LIMIT $limit";
		}
		$results = mysql_query($query);
	
		echo mysql_error();
		return $this->resultsToarray($results);
		
	}
	function getCompany($id){
		$query = "SELECT * from companies WHERE id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);

		return $result;
	}
	
	function getCompanyByUser($userId){
		$user = $this->getUser($userId);
		if ($user == null || $user['companyid'] == 0){
			return null;
		}else{
			return $this->getCompany($user['companyid']);
		}
	}
	
	function updateCompany($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE companies set $pairs where id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	function addCompany($params){
		$pairs = $this->arrayToPairs($params);
		$query = "INSERT INTO companies SET $pairs";
		mysql_query($query);
		echo mysql_error();
		$query = "SELECT MAX(id) AS maxid FROM companies";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}
	function deleteCompany($id){
		$query = "DELETE FROM companies WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	
	function createCompanyImage($params){
		$query = "INSERT INTO companyimages SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
		$query = "SELECT MAX(id) AS maxid FROM companyimages";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result['maxid'];
	}

	
	// FreeText
	
	function getFreeTexts(){
		$query = "SELECT * from freetexts ORDER BY id";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}
	function getFreeText($id){
		$id = addSlashes($id);
		$query = "SELECT * from freetexts WHERE id='$id'";
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;
	}
	function updateFreeText($id, $text){
		$id = addSlashes($id);
		$text = addSlashes($text);		
		$query = "UPDATE freetexts set text = '$text' where id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	
	// Subratings
	
	// recherche des critère attachés à un produit selonun type 0: commentaire libr, 1 preuve ou 2 déclaration honneur
	function getProductSubratingProof($productId,$askproof=0,$lang=1) {
		$query="select name,proof_exist,proof_new from m_criteria,productsubratings where subratingid=m_criteria.id and productid=$productId and ask_proof=$askproof and islabel=0 and m_criteria.lang=$lang order by m_criteria.id asc";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}
	
	// recherche des critère attachés à un produit selonun type 0: commentaire libr, 1 preuve ou 2 déclaration honneur
	function countProductSubratingProof($userId,$askproof=0,$lang=1) {
		$query="select count(*) as count from m_criteria,productsubratings,products where subratingid=m_criteria.id and products.id=productid and userid=$userId and ask_proof=$askproof and islabel=0 and m_criteria.lang=$lang ";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result['count'];
	}
	
	
	//detail d'un critere (ratingId ou tous)
	function getNewSubratings($ratingId = 0,$themeId=1,$lang=1){
		if ($ratingId == 0){
			$query = "SELECT m_criteria.id as id, m_criteria.name as name,m_theme.name as theme, m_heading.name as heading,m_lifecycle.name as lifecycle,points from m_criteria left join m_lifecycle on m_criteria.lifecycle_id =m_lifecycle.id and m_lifecycle.lang=$lang left join m_heading on m_criteria.heading_id=m_heading.id and m_heading.lang=$lang left join m_theme on m_theme.id=theme_id and m_theme.lang=$lang where m_criteria.lang=$lang";
			if ($themeId!=0) $query.=" and m_criteria.theme_id=$themeId ";
			$query.=" ORDER BY m_criteria.theme_id,m_criteria.lifecycle_id,heading_id,m_criteria.id";
		}else{
			$query = "SELECT m_criteria.id as id, m_criteria.name as name,m_heading.name as heading,m_lifecycle.name as lifecycle,points from m_criteria left join m_lifecycle on m_criteria.lifecycle_id =m_lifecycle.id and m_lifecycle.lang=$lang left join m_heading on m_criteria.heading_id=m_heading.id and m_heading.lang=$lang where m_criteria.id=$ratingId and m_criteria.lang=$lang";
		if ($themeId!=0) $query.=" and m_criteria.theme_id=$themeId ";
		
		}
		
		$results = mysql_query($query);
		//echo $query;
		$criterias = array();		
		while ($criteria = mysql_fetch_assoc($results)){
			$criteria['impact'] = $this->getImpact($criteria['id'],$lang);
			$criterias[] = $criteria;
		}
		return $criterias;
		echo mysql_error();
}
	
	function getImpact($criteriaId,$lang=1){
		
		$query = "SELECT A.id,A.name from m_indicator A, m_crit_indic B where A.id=indicator_id and criteria_id=$criteriaId and A.lang=$lang";
		$results = mysql_query($query);

		return $this->resultsToArray($results);
		echo mysql_error();
		
	}
	
	function getSubratings($ratingId = 0){
		if ($ratingId == 0){
			$query = "SELECT * from subratings2 ORDER BY ratingid ASC, points DESC, name ASC";
		}else{
			$query = "SELECT * from subratings2 WHERE ratingid=$ratingId ORDER BY ratingid ASC, points DESC, name ASC";
		}
		$results = mysql_query($query);

		return $this->resultsToArray($results);
		echo mysql_error();
		
	}
	
	function getSubratings3($themeId = 0,$lang=1){
		if ($themeId == 0){
			$query = "SELECT * from m_criteria where m_criteria.lang=$lang ORDER BY id ASC, points DESC, name ASC";
		}else{
			$query = "SELECT * from m_criteria WHERE m_criteria.theme_id=$themeId and m_criteria.lang=$lang ORDER BY id ASC, points DESC, name ASC";
		}
		$results = mysql_query($query);

		return $this->resultsToArray($results);
		echo mysql_error();
		
	}
	
		function getSubratings2($type_id,$themeId,$lifecycleId=0,$headingId=0,$lang=1){
		
		if ($type_id !=0) $query = "SELECT m_criteria.id as id, m_criteria.name as name,ask_proof, points, m_heading.name as heading,example,question,proof_exist,proof_new from m_criteria left join m_heading on heading_id=m_heading.id and m_heading.lang=$lang,typesubrating where  subrating_id=m_criteria.id and type_id=$type_id and m_criteria.lang=$lang ";
		else $query = "SELECT  m_criteria.id as id, m_criteria.name as name,ask_proof, points, m_heading.name as heading,example,question,proof_exist,proof_new from m_criteria left join m_heading on heading_id=m_heading.id and m_heading.lang=$lang where m_criteria.lang=$lang ";
		if ($type_id !=0) $query.= "and type_id=$type_id ";
		if ($themeId != 0) $query.= "and m_criteria.theme_id=$themeId";
		if ($lifecycleId != 0) $query.= " and m_criteria.lifecycle_id=$lifecycleId";
		if ($headingId != 0) $query.= " and heading_id=$headingId";
	
		$query.=" ORDER BY lifecycle_id asc,  m_criteria.id asc";
		$results = mysql_query($query);
		echo mysql_error();
	//echo $query;

		return $this->resultsToArray($results);
		echo mysql_error();
		
	}
	
	function getRatingProductDetail($productId,$themeId,$lifecycleId=0,$lang=1) {
		$query="select m_criteria.id as id,name,points,comment,ask_proof,islabel from m_criteria,productsubratings where productid=$productId and subratingid=m_criteria.id and m_criteria.lang=$lang ";
		if ($lifecycleId!=0) $query.=" and lifecycle_id=$lifecycleId";
		if ($themeId!=0) $query.=" and theme_id=$themeId";
		$results = mysql_query($query);
    $ratings=array();
    
	while ($rating = mysql_fetch_assoc($results)){
		
		$rating['subratingslabel']=$this->getSubratingsComment($productId,$rating['id']);
		
		//$query2="select comment from productsubratingscomments  where ratingid=".$result['id']." and productid=$productId";
		//$results2 = mysql_query($query2);
	$ratings[]=$rating;	
	}
	
	
		echo mysql_error();
	//	var_dump($ratings);
		return $ratings;
	}




		
	function createSubrating($params){
		$query = "INSERT INTO subratings2 SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}
	function getSubrating($id){
		$query = "SELECT * from subratings2 WHERE id='$id'";
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		echo mysql_error();
		return $result;
	}
	
	// METHODO 2012
	function getThemes($themeId=0,$lang=1) {
		$query = "SELECT * from m_theme where lang=$lang ";
		if ($themeId!=0) $query.=" and id=$themeId";
		$query.=" order by id asc";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
	function getLabelCriteria($criteriaId,$lang=1) {
		$query="select name,image,value,referentiel from labels,m_criteria_label where label_id=labels.id and criteria_id=$criteriaId  order by name asc";
		$results = mysql_query($query);
		echo mysql_error();
		
		return $this->resultsToArray($results);
	}
	
	function getTypesCriteria($criteriaId){
		$query = "SELECT name,name_en from type,typesubrating where type_id=type.id and subrating_id=$criteriaId order by name asc ";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
		function getHeadings($themeId=0,$typeId=0,$lifecycleId=0,$lang=1) {
		if ($typeId!=0) $query=" SELECT distinct m_heading.id, m_heading.name from m_heading,m_criteria,typesubrating where  subrating_id=m_criteria.id and heading_id=m_heading.id and m_heading.lang=$lang and type_id=$typeId and m_criteria.lang=$lang ";
		else  $query="SELECT distinct m_heading.id, m_heading.name from m_heading,m_criteria where  heading_id=m_heading.id and m_heading.lang=$lang and m_criteria.lang=$lang ";
		
		if ($themeId!=0) $query.=" and theme_id=$themeId";
		if ($lifecycleId!=0) $query.=" and lifecycle_id=$lifecycleId";
		$results = mysql_query($query);
		
		//echo $query;
		echo mysql_error();
	
		return $this->resultsToArray($results); 
	}
	
	
	function getLifecycles($productId=0,$lang=1) {
		if ($productId==0) {
		$query = "SELECT * from m_lifecycle where m_lifecycle.lang=$lang order by id";
	}
	
	else {$query = "SELECT m_lifecycle.id as lifecycle_id, m_lifecycle.name as name,score,name from m_lifecycle,productscore  where lifecycle_id=m_lifecycle.id and m_lifecycle.lang=$lang and product_id=$productId order by m_lifecycle.id asc";
	}
		$results = mysql_query($query);
				echo mysql_error();
				
			
				
		return $this->resultsToArray($results);
	}
	
		function getThemeScore($productId=0,$themeList,$lang=1) {
		$query = "SELECT * from productscore,m_theme  where theme_id=m_theme.id and m_theme.lang=$lang and product_id=$productId and theme_id in ($themeList) order by m_theme.id asc";

		$results = mysql_query($query);
				echo mysql_error();
				
				//echo "$productId::$query";
				
		return $this->resultsToArray($results);
	}
	
	function getNewSubrating($id,$lang=1){
		$query = "SELECT * from m_criteria WHERE id='$id' and m_criteria.lang=$lang";
		$results = mysql_query($query);
		$criterias = mysql_fetch_assoc($results);	
		$criterias['impact'] = $this->getImpact($criterias['id'],$lang);
		//echo $query;
		return $criterias;
		echo mysql_error();
	}
	
	function updateSubrating($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE subratings2 set $pairs where id='$id'";
		mysql_query($query);
		echo mysql_error();

	}

	
	// V2 Pmontier
	function updateNewSubrating($id, $params,$lang=1){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE m_criteria set $pairs where id='$id' and m_criteria.lang=$lang";
		mysql_query($query);
	//	echo $query;
		echo mysql_error();
	}
	
	function updateNewLabel($id, $params){
		$pairs = $this->arrayToPairs($params);
		$query = "UPDATE labels set $pairs where id='$id'";
		mysql_query($query);
	//	echo $query;
		echo mysql_error();
	}
	
	
	function isfeedback($themeId,$productId,$lang=1) {
		
	$query="select count(*) as count from productsubratings A, m_criteria B where B.id=A.subratingid and A.feedback!='' and B.theme_id=$themeId and A.productid=$productId and B.lang=$lang";
	$results = mysql_query($query);	
	echo mysql_error();
	$result = mysql_fetch_assoc($results);
	//echo $query;
	
	if ( $result['count']>0 ) return true; else return false;
	
	 
	}
	
	function getProductSubratings($productId, $includeComments = false, $includeFeedback = false,$includeLabel=false){
		$query = "SELECT * from productsubratings WHERE productid=$productId";
		
		
		echo mysql_error();
		$results = mysql_query($query);
		$rv = array();
		while ($result = mysql_fetch_assoc($results)){
			if ($includeComments){
				$rv[$result['subratingid']] = stripslashes($result['comment']);
				}
			else if ($includeFeedback) {	$rv[$result['subratingid']] = stripslashes($result['feedback']);}
			//else if ($includeLabel) {	$rv[$result['subratingid']] = stripslashes($result['labeljustify']);}
			
			else{
				$rv[] = arrondir($result['subratingid']);
			}
		}
	
		return $rv;
	}

// on enregistre tous les critères cliqués (et donc justifiés)
	function updateProductSubratings($id, $subratings){

		$query = "DELETE FROM productsubratings WHERE productid='$id' and islabel=0";
		mysql_query($query);
	
		foreach($subratings as $key=>$subratingId){
			$query = "INSERT INTO productsubratings SET productid='$id', subratingid='$subratingId'";
			mysql_query($query);
			echo mysql_error();

		}
		
		$this->syncLabelsWithSubratings($id);
	}

	

	
// on enregistre tous les critères cliqués (et donc justifiés)
	function insertLabelComment($productId, $subratingId,$comment) {

		$query = "UPDATE productsubratings SET comment='".addslashes($comment)."' WHERE productid='$productId' AND subratingid='$subratingId'";
		mysql_query($query);
		echo mysql_error();
		
		
	}
	
		//on met à jour le commentaire du critère déjà saisi
	function updateProductSubratingComment($productId, $subratingId, $comment){
		$query = "UPDATE productsubratings SET comment='".addslashes($comment)."' WHERE productid='$productId' AND subratingid='$subratingId'";
		mysql_query($query);
		
		echo mysql_error();
	}
	
	function updateProductSubratingFeedback($productId, $subratingId, $feedback){
		$query = "UPDATE productsubratings SET feedback='".addslashes($feedback)."' WHERE productid='$productId' AND subratingid='$subratingId'";
		mysql_query($query);
		
	
		echo mysql_error();
	}
	
	function recalculateAffectedProducts($subratingId){
		$query = "SELECT * from productsubratings WHERE subratingid=$subratingId";
		echo mysql_error();
		$results = mysql_query($query);
		$report = '';
 		while ($result = mysql_fetch_assoc($results)){
 			if ($this->recalculate($result['productid']) == true){
 				$report .= $result['productid']."\n";
 			}
		}
		return $report;
	}
	
		function getRandomEcoacteur(){
		$query = "SELECT A.user_id as user_id,A.nbscans as nbscans,B.name,A.month,A.year  from iphone_winners A ,iphone_users B where A.user_id=B.id order by rand() limit 2";
		echo mysql_error();
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
		
	}
	
	function getFiveProducts(){
		$query = "SELECT * from iphone_5products";
		echo mysql_error();
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
		
	}
	
	
	
	function getAffectedProductsCount($subratingId){
		$query = "SELECT COUNT(*) as count from productsubratings WHERE subratingid=$subratingId";
		echo mysql_error();
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result['count'];
	}
	
	function getAffectedLabelsCount($labelId){
		$query = "SELECT COUNT(*) as count from productlabels WHERE labelid=$labelId";
		echo mysql_error();
		$results = mysql_query($query);
		$result = mysql_fetch_assoc($results);
		return $result['count'];
	}
	
	
	function recalculate($productId){


		$query = "SELECT * from products, typescore where products.typeid=typescore.type_id and products.id=$productId";
	//	echo $query;
		echo mysql_error();
		$results = mysql_query($query);
		
		//on détermine si produit équitable NORD/SUD (type id 6)
		$equitable_sud=0;
			
		$maxscore=array();
		while ($product = mysql_fetch_assoc($results)){
		$type=$product['typeid'];
		$equitable_sud=$product['equitable_sud'];
		$maxscore[$product['theme_id']][$product['lifecycle_id']]=$product['scoreideal'];
		
		if ($maxscore[$product['theme_id']][$product['lifecycle_id']]==0) { echo "Erreur, MAXSCORE de zero pour produit=$productId, type=$type et theme=".$product['theme_id']." et lifecycle=".$product['lifecycle_id'];
		}
	  }
	
	//recherche de la note environnement idéale (somme des sous notes idéales) pour les 12 sections
	$query="select sum(scoreideal) as max from typescore, products where products.typeid=typescore.type_id and theme_id=1 and products.id=$productId";
  $results = mysql_query($query);
  $result = mysql_fetch_assoc($results);
  $maxscore[1][0]=$result['max']; // ici on force l'indice à zero car les indices 1-12 sont les sous-détails

	
	//recherche des points sur le couple produit / notation
	$query = "SELECT * from productsubratings, m_criteria WHERE subratingid=m_criteria.id AND productid=$productId and m_criteria.lang=1";
		//echo $query;
		echo mysql_error();
		$results = mysql_query($query);
		$score=array();
	 				
 		while ($result = mysql_fetch_assoc($results)){
 		$theme_id=$result['theme_id'];
 		$lifecycle_id=$result['lifecycle_id'];
 		$score[$theme_id][$lifecycle_id]+= $result['points'];
 		
		}

		//on rajoute +5 à note santé
		$score[2][0]+= 5;
 	
		//on supprime les calculs précédents
		$query="delete from productscore where product_id=$productId";
		$results = mysql_query($query);

		
		//on calcule les scores pour environement, lifecycle
		$total = 0;
		$totalscore = array();
		$score1=0;
		$score2=0;
		$score3=0;
		$score4=0;
		
		
		$rating=array();
		
		for ($i = 1; $i <= 12; $i++){
			$note=0;
			if (isset($score[1][$i] )) { 
		
					//on cumule chaque sous note
					
					if ($score[1][$i] > $maxscore[1][$i]) $score[1][$i]= $maxscore[1][$i]; else $score[1][$i]=$score[1][$i];
					
					$totalscore[1]+=$score[1][$i];
					$score1+=$score[1][$i]*5/$maxscore[1][$i];
										
					if ($maxscore[1][$i]==0)  echo "Erreur, MAXSCORE de zero pour produit=$productId, type=$type et theme=1 et lifecycle=".$i;
					
					$query="insert into productscore (product_id,theme_id,lifecycle_id,score,note) values ($productId,1,$i,".$score[1][$i].",".$note*5 / $maxscore[1][$i].");";
					$results = mysql_query($query);
					if (mysql_errno()) 	echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n$query\n<br>";
	 	  
			}
		}
		//on divise le score1 par les 11 critères pour obtenir la moyenne
		$score1=$score1/11;
		
		//maintenant Societal : qualité, social, éthique, équitable Nord, équitable sud
		for ($i=2;$i<=7;$i++) {
		$note=0;
		if (isset($score[$i][0] )) { 
		$totalscore[$i]+=$score[$i][0];
		
		
		$rating[$i][0]=$score[$i][0]*5 / $maxscore[$i][0];
		if ($score[$i][0] > $maxscore[$i][0]) $note= $maxscore[$i][0]; else $note=$score[$i][0];
		$query="insert into productscore (product_id,theme_id,lifecycle_id,score,note) values ($productId,$i,0,".$score[$i][0].",".$note*5 / $maxscore[$i][0].");";
		$results = mysql_query($query);
		//echo mysql_error();
		//echo $query;
	}
		}
		
		
	
		// on regroupe les scores
		//rating1 : environnement
		//rating2 : social : ethique + qualité + social
		//rating3 : santé
		//Rating4 : general

		$notemax2=0;
		$notemax3=0;
		 
		//On met à jour environnement
		$query="update products set rating1=".($totalscore[1]+0).",score1=".$score1." where id=$productId";
		$results = mysql_query($query);
		if (mysql_errno()) { 
 	 	echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n$query\n<br>";
 	  }

	// on force pour insérer note globale environnement dans productscore
		$query="insert into productscore (product_id,theme_id,lifecycle_id,score,note) values ($productId,1,0,".$totalscore[1].",".$score1.");";
		$results = mysql_query($query);
		//echo mysql_error();
		
			
		// SOCIETAL : Social+ ethique + qualité + equitable
		$notemax3=($totalscore[3] > $maxscore[3][0]) ? $maxscore[3][0] : $totalscore[3];
		$notemax4=($totalscore[4] > $maxscore[4][0]) ? $maxscore[4][0] : $totalscore[4];
		$notemax5=($totalscore[5] > $maxscore[5][0]) ? $maxscore[5][0] : $totalscore[5];
	
		
		//si équitable NORDSUD
		if ($equitable_sud==1) 
		{
			$notemax6=($totalscore[6] > $maxscore[6][0]) ? $maxscore[6][0] : $totalscore[6];
				
		
				$score2=(($notemax3*5/$maxscore[3][0]) + ($notemax4*5/$maxscore[4][0]) +($notemax5*5/$maxscore[5][0]) + ($notemax6*5/$maxscore[6][0])) / 4;
				$query="update products set rating2=".($totalscore[3]+$totalscore[4]+$totalscore[5]+$totalscore[6]).",score2=".$score2." where id=$productId";

		} else
		{
		$notemax7=($totalscore[7] > $maxscore[7][0]) ? $maxscore[7][0] : $totalscore[7];		
		$score2=(($notemax3*5/$maxscore[3][0]) +($notemax4*5/$maxscore[4][0]) +($notemax5*5/$maxscore[5][0]) +($notemax7*5/$maxscore[7][0])) / 4 ;
		$query="update products set rating2=".($totalscore[3]+$totalscore[4]+$totalscore[5]+$totalscore[7]).",score2=".$score2." where id=$productId";
			//	echo "maxscore[7][0]:".$maxscore[7][0]. ":::notemax7=".$notemax7;
			
		}
	

		$results = mysql_query($query);
		if (mysql_errno()) { 
 	 	echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n$query\n<br>";
 	  }
	
		//santé, on part de 5 en note minimum
		$notemax2=($totalscore[2] > $maxscore[2][0]) ? $maxscore[2][0] : $totalscore[2];
		$score3=$notemax2*5 / $maxscore[2][0];
		$query="update products set rating3=".($totalscore[2]+0).",score3=".$score3." where id=$productId";
		$results = mysql_query($query);
		if (mysql_errno()) { 
 	 	echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n$query\n<br>";
 	  }
		

 

//	echo "$score1 : $score2 : $score3 ";
	//calcul des impact
	$this->saveProductIndicator($productId);
	
	  //et maintenant la moyenne globale !
	  
	$query="update products set score4=".round((($score1+$score2+$score3)/3),1, PHP_ROUND_HALF_UP)." where id=$productId";
		$results = mysql_query($query);
	if (mysql_errno()) { 
 	 	echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n$query\n<br>";
 	  }
	

	
	
	}
	
	function deleteSubrating($id){
		$query = "DELETE FROM m_criteria WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
		
		$query = "DELETE FROM productsubratings WHERE subratingid='$id'";
		mysql_query($query);
		echo mysql_error();
		
	}
	
	function deleteSubratingLabel($id){
		$query = "DELETE FROM m_criteria_label WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
	}
	
	function addSubratingLabel($labelId,$subratingId,$value){
		$query = "insert into m_criteria_label (label_id,criteria_id,value) values ($labelId,$subratingId,'".$value."');";
		mysql_query($query);
		echo mysql_error();
	}
	
	
	
		function deleteLabel($id){
		$query = "DELETE FROM label WHERE id='$id'";
		mysql_query($query);
		echo mysql_error();
		
	
		
	}
	
	/*function deleteCategory($id){
		$query = "DELETE FROM categories WHERE id='$id'";
		mysql_query($query);	
		echo mysql_error();
		$query = "DELETE FROM productcategories WHERE categoryid='$id'";
		mysql_query($query);	
		echo mysql_error();
	}*/
	
	// Users
	
	function getUsers(){
		$query = "SELECT * from users ORDER BY username";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}

	function getUser($id){
		$query = "SELECT * from users WHERE id='$id'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	function getUserByEmail($email){
		$query = "SELECT * from users WHERE email='$email'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}
	
	function createUser($params){
		$query = "INSERT INTO users SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}

	function updateUser($id, $params){
		$query = "UPDATE users SET ".$this->arrayToPairs($params)." WHERE id='".addslashes($id). "'";
		mysql_query($query);
		echo mysql_error();
	}

	function deleteUser($id){
		$query = "DELETE FROM users WHERE id='".addslashes($id). "'";
		mysql_query($query);
		echo mysql_error();
	}
	
	function checkUser($email, $password){
		$query = "SELECT * FROM users WHERE email='".addSlashes($email)."' AND password=SHA('".addSlashes($password)."')";
		
		$results = mysql_query($query);
		echo mysql_error();
		$result = mysql_fetch_assoc($results);
		return $result;		
	}

	/* DEBUT MOBILE */
	function getMobileUser($id){
		$query = "SELECT * from iphone_users WHERE id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	function getMobileUserByEmail($email){
		$query = "SELECT * from iphone_users WHERE email='".addSlashes($email)."'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	function checkMobileUser($email, $pwd){
		$query = "SELECT * from iphone_users WHERE email='".addSlashes($email)."' AND pwd='".sha1(addSlashes($pwd))."' ";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	function checkMobileUserToken($token){
		$query = "SELECT * from iphone_users WHERE token='".addSlashes($token)."'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	function createMobileUser($params){
		$query = "INSERT INTO iphone_users SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}

	function updateMobileUser($id, $params){
		$query = "UPDATE iphone_users SET ".$this->arrayToPairs($params)." WHERE id='".addslashes($id). "'";
		mysql_query($query);
		echo mysql_error();
	}
	function createMobileUserScan($params){
		$query = "INSERT INTO iphone_query SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}

	function getUserScans($id){
		$query = "SELECT product_id,a.ean,description,marque,produitvert,noresult,date FROM iphone_query a, iphone_eans_desc b WHERE a.ean=b.ean AND user_id='$id' ORDER BY date Desc LIMIT 15";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}
	
	function getGeneralRating(){
		$query = "SELECT user_id,name,sort,prevsort,total, totalproduitvert FROM iphone_query_month a, iphone_users b WHERE a.user_id = b.id and month=MONTH(NOW()) and year=YEAR(NOW()) ORDER BY sort ASC LIMIT 15";
		$results = mysql_query($query);
		echo mysql_error();
		return $this->resultsToarray($results);
	}
	
	function createUserStats($params){
		$query = "INSERT INTO iphone_users_stat SET ".$this->arrayToPairs($params);
		mysql_query($query);
		echo mysql_error();
	}

	function getStatUser($id){
		$query = "SELECT * from iphone_users_stat WHERE user_id = ".addSlashes($id)."";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}

	/* FIN MOBILE */
	
	/* DEBUT BADGE */
		//insert l'avis concernant le badge
		function insertOpinionBadge($idpdt, $avis, $numQuestion){
			$query = "INSERT INTO badge_opinion SET product_id='$idpdt', opinion='$avis', num_question='$numQuestion' ";
			mysql_query($query);
		}
		
	/* FIN BADGE */
	/*DEBUT COMPTE ECOACTEUR */
	

	function getUserByIdFB($id){
		$query = "SELECT * from iphone_users WHERE FB_id='".addSlashes($id)."'";
		$results = mysql_query($query);
		echo mysql_error();
		return mysql_fetch_assoc($results);
	}
	
	
	/*FIN COMPTE ECOACTEUR */	
	
	//Ajouts IdNext
	
	// Cloud Tags - Footer
	function getTags($occurence){
		$query ="SELECT libelle, nombre FROM tags  WHERE nombre > ".$occurence." ;";
		$results = mysql_query($query);

		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
	function getAllProductsParentsCategories($parentCategoryId, $limit){
		$query = "SELECT DISTINCT products.id AS id, products.name AS name, products.created ";
		$query .= "FROM products, productcategories, categories ";
		$query .= "WHERE products.id = productcategories.productid ";
		$query .= "AND productcategories.categoryid = categories.id ";
		$query .= "AND categories.parentid =".$parentCategoryId." ";
		$query .= "AND products.published = \"true\" ";
		$query .= "ORDER BY products.created DESC ";
		$query .= "LIMIT ".$limit." ;";
		$results = mysql_query($query);

		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
	function getCategoriesParent(){
		$query = "SELECT categories.id AS id, categories.name AS name FROM categories WHERE parentid = 0";
		$results = mysql_query($query);

		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
	function getRelatedProducts($productId, $categoryId, $limit){
		$query = "SELECT products.id, products.name ";
		$query .= "FROM products, productcategories, categories ";
		$query .= "WHERE products.id = productcategories.productid ";
		$query .= "AND productcategories.categoryid = categories.id ";
		$query .= "AND categories.id =".$categoryId[0]['id']." ";
		$query .= "AND products.id <> ".$productId." ";
		$query .= "AND products.published = 'true' ";
		$query .= "ORDER BY rand() ";
		$query .= "LIMIT ".$limit.";";
		$results = mysql_query($query);


		echo mysql_error();
		return $this->resultsToArray($results);
	}
	
	function saveIphoneQuery($iphone_id, $email, $name, $tel, $product_id, $ean, $lon, $lat, $alt) {
  
  }

function getTypeMax($typeId,$themeId,$lifecycleId=0) {
	if ($themeId==1 && $lifecycleId==0) 
	$query="select sum(scoreideal) as scoreideal from typescore where type_id=$typeId and theme_id=$themeId ";
	else
	$query="select scoreideal from typescore where type_id=$typeId and theme_id=$themeId and lifecycle_id=$lifecycleId";
	$results = mysql_query($query);
	$result = mysql_fetch_assoc($results);
  echo mysql_error();
   //echo $query;
	return 	round($result['scoreideal']);
	
}
function getMonth($month)
{
	switch ($month) {
    case 1:
        return "Janvier";
        break;
		case 2:
        return "Février";
        break;
    case 3:
        return "Mars";
        break;
    case 4:
        return "Avril";
        break;
    case 5:
        return "Mai";
        break;
    case 6:
        return "Juin";
        break;
    case 7:
        return "Juillet";
        break;
    case 8:
 			  return "Aout";
        break;
   case 9:
			  return "Septembre";
        break;
   case 10:
			  return "Octobre";
        break;
   case 11:
			  return "Novembre";
        break;
   case 12:
			  return "Décembre";
        break;
}}
}

function checkLink($chaine,$ratingId) {
		$offset=0;
		$term="";
		$nombre=0;
	
	while (strpos($chaine,"[[") >0 && strpos($chaine,"]]") >0) 
		 
		{
		$debut=strpos($chaine,"[[",$offset);	
		$fin=strpos($chaine,"]]",$offset);	
	
		if ($debut >0 && $fin >0) {
		$nombre++;
			$debut+=2;
			$term=substr($chaine,$debut,$fin-$debut);
			//echo ">>".$term."<<<";
			$offset=$fin;
			
			//recherche dans dico
			$query="select nom,description from dico where term like '".$term."';";
		//echo $query;
		
			$description="";
			$nom="";
			$results = mysql_query($query);
			$tag = mysql_fetch_assoc($results);
			//echo mysql_error();
			$description=$tag['description'];
			$nom=$tag['nom'];
		
			$chaine=substr($chaine,0,$debut-2)."<a href=\"#\" id=\"link$ratingId$nombre\"><b>$term</b></a><label for=\"link$ratingId$nombre\"><b>$term</b> - $nom<br/>".$description."</label>".substr($chaine,$fin+2,strlen($chaine)-$fin+2);

		
		} // fin if



		}// fin while
		return nl2br2($chaine);
	}


function nl2br2($ligne) {
	return preg_replace("/\n/", "<br/>", $ligne);
	
}
?>
