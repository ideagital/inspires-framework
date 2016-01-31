<?php
$session_id=session_id();
isset($_REQUEST['action'])?$action=$_REQUEST['action'] : $action=null;
isset($_REQUEST['goto'])?$goto=$_REQUEST['goto'] : $goto=null;
isset($_SESSION['me_login'])?$me_login=$_SESSION['me_login'] : $me_login=null;
isset($_SESSION['sponsor']) ? $sponsor=$_SESSION['sponsor'] : $sponsor=null;

/* Register */
if($process=="member_signup"){
	isset($_REQUEST['signup_name']) ? $member_name=$_REQUEST['signup_name'] : $member_name=null;
	isset($_REQUEST['signup_surname']) ? $member_surname=$_REQUEST['signup_surname'] : $member_surname=null;
	isset($_REQUEST['signup_city']) ? $member_city=$_REQUEST['signup_city'] : $member_city=null;
	isset($_REQUEST['signup_country']) ? $member_country=$_REQUEST['signup_country'] : $member_country=null;
	isset($_REQUEST['signup_currency']) ? $member_currency=$_REQUEST['signup_currency'] : $member_currency=null;
	isset($_REQUEST['signup_zipcode']) ? $member_zipcode=$_REQUEST['signup_zipcode'] : $member_zipcode=null;
	isset($_REQUEST['signup_email']) ? $member_email=$_REQUEST['signup_email'] : $member_email=null;
	isset($_REQUEST['signup_phone']) ? $member_phone=$_REQUEST['signup_phone'] : $member_phone=null;
	isset($_REQUEST['signup_username']) ? $member_username=$_REQUEST['signup_username'] : $member_username=null;
	isset($_REQUEST['signup_password']) ? $member_password=encode($_REQUEST['signup_password'],$private_key) : $member_password=null;
	isset($_REQUEST['signup_national_id']) ? $member_national_id=$_REQUEST['signup_national_id'] : $member_national_id=null;
	isset($_REQUEST['signup_passport']) ? $member_passport=$_REQUEST['signup_passport'] : $member_passport=null;
	
	isset($_REQUEST['package']) ? $signup_package=$_REQUEST['package'] : $signup_package=0;
	isset($_REQUEST['product_set']) ? $product_set=$_REQUEST['product_set'] : $product_set=null;
	isset($_REQUEST['sponsor']) ? $sponsor=$_REQUEST['sponsor'] : $sponsor=null;
	isset($_REQUEST['upline']) ? $upline=$_REQUEST['upline'] : $upline=null;
	isset($_REQUEST['placement']) ? $placement=$_REQUEST['placement'] : $placement=null;
	isset($_REQUEST['signup_franchiser']) ? $franchiser=$_REQUEST['signup_franchiser'] : $franchiser=null;
	isset($_REQUEST['payment_method']) ? $payment_method=$_REQUEST['payment_method'] : $payment_method=null;
	
	$member_avatar="avatar-".rand(1,12).".png";
	
	$signup_sql="INSERT INTO members	(member_name,member_surname,member_city,member_country,member_zipcode,member_avatar,member_email,member_phone,member_username,member_password,member_national_id,member_passport,member_registered,package_id,sponsor,upline,placement,franchiser)
	VALUES	
	('$member_name','$member_surname','$member_city','$member_country','$member_zipcode','$member_avatar','$member_email','$member_phone','$member_username','$member_password','$member_national_id','$member_passport',now(),'$signup_package','$sponsor','$upline','$placement','$franchiser')";
	$signup_query=mysqli_query($connect,$signup_sql);
	if($signup_query){
		//Defined member username , Find member id
		$member_id=mysqli_fetch_array(mysqli_query($connect,"SELECT member_id FROM members WHERE member_username='$member_username' AND member_password='$member_password' ")); 
		$member_id=$member_id['member_id'];
		
		mysqli_query($connect,"INSERT INTO members_settings VALUES(NULL,$member_id,'country_code','$member_country')");
		mysqli_query($connect,"INSERT INTO members_settings VALUES(NULL,$member_id,'currency_code','$member_currency')");
		
		$package=mysqli_fetch_array(mysqli_query($connect,"SELECT package_price,package_pv FROM packages WHERE package_id=$signup_package"));
		$package_price=$package['package_price'];
		$package_pv=$package['package_pv'];
		
		
		// Set complete infomation
		$_SESSION['signup_username']=$member_username;
		$_SESSION['signup_password']=$member_password;
		$_SESSION['signup_avatar']=$member_avatar;
		$return="signup/complete";
		
		
		// Check product select
		if($product_set=="neo-medical"){
			$basket[82]=1;
		}elseif($product_set=="glu-gold"){
			$basket[80]=1;
		}elseif($product_set=="facial-massage"){
			$basket[81]=1;
		}
		
		
		// Calculate price , pv
		$order_total=0;
		$order_discount=0;
		foreach($basket as $product_id => $product_qty){
			$product=mysqli_fetch_array(mysqli_query($connect,"SELECT product_price_member FROM products WHERE product_id=$product_id"));
			$order_total+=$basket["$product_id"]*$product['product_price_member'];	
		}
		
		
		if($order_total>$package_price)	$order_discount=$order_total-$package_price+0;
		$order_total=$order_total-$order_discount;
		
		$order_add=mysqli_query($connect,"
		INSERT INTO orders(member_id,order_total,order_discount,payment_method,order_comment,order_status,shipping_costs,session_id,shipping_method,order_date) 
		VALUES('$member_id','$order_total','$order_discount','$payment_method','signup_package_from_jSystem','paid','0','$session_id','at-shop',now())
		;");
		if($order_add){
			$order_result=mysqli_fetch_array(mysqli_query($connect,"SELECT order_id FROM orders WHERE session_id='$session_id'"));
			$order_id=$order_result['order_id'];
			foreach($basket as $product_id => $item_qty){
				$items_add=mysqli_query($connect,"
				INSERT INTO order_items(order_id,product_id,item_qty) VALUES($order_id,$product_id,$item_qty)
				;");
			}
			mysqli_query($connect,"UPDATE orders SET session_id='' WHERE session_id='$session_id';");
			unset($_SESSION['basket']);
		}
	
		
		pay($payment_method,$package_price); // Update jWallet or rMoney
	
	
		//Direct bonus
		if($signup_package==0) $bonus_direct=3; else $bonus_direct=$package_pv*0.15; //direct bonus get 10% of package pv		
		bonus_direct($member_id,$bonus_direct);	
	}
}elseif($process=="member_settings"){
	$currency_code=$_REQUEST['currency_code'];
	$language_code=$_REQUEST['language_code'];
	$country_code=$_REQUEST['country_code'];
	
	if($currency_code!=""){
		$currency_code_check=mysqli_num_rows(mysqli_query($connect,"SELECT id FROM members_settings WHERE setting='currency_code' AND member_id=$me_login;"));
		if($currency_code_check==0){
			mysqli_query($connect,"INSERT INTO members_settings(member_id,setting,value) VALUES($me_login,'currency_code','$currency_code')");
		}else{
			mysqli_query($connect,"UPDATE members_settings SET value='$currency_code' WHERE setting='currency_code' AND member_id=$me_login;");
		}
	}
	
	if($country_code!=""){
		$country_code_check=mysqli_num_rows(mysqli_query($connect,"SELECT id FROM members_settings WHERE setting='country_code' AND member_id=$me_login;"));
		if($country_code_check==0){
			mysqli_query($connect,"INSERT INTO members_settings(member_id,setting,value) VALUES($me_login,'country_code','$country_code')");
		}else{
			mysqli_query($connect,"UPDATE members_settings SET value='$country_code' WHERE setting='country_code' AND member_id=$me_login;");
		}
	}
	
	if($language_code!=""){
		$language_code_check=mysqli_num_rows(mysqli_query($connect,"SELECT id FROM members_settings WHERE setting='language_code' AND member_id=$me_login;"));
		if($language_code_check==0){
			mysqli_query($connect,"INSERT INTO members_settings(member_id,setting,value) VALUES($me_login,'language_code','$language_code')");
		}else{
			mysqli_query($connect,"UPDATE members_settings SET value='$language_code' WHERE setting='language_code' AND member_id=$me_login;");
		}
	}
}elseif($process=="member_avatar"){
	$file=$_FILES['member_avatar'];
	$file_name = $file['name'];
	$file_file = $file['tmp_name'];
	$file_type = end(explode('.',$file_name));
	$file=$me_login.".".$file_type; 
	mysqli_query($connect,"UPDATE members SET member_avatar='$file' WHERE member_id=$me_login");
	copy($file_file,"../gallery/avatar/".$file);
}

//goto
if(isset($goto)) header("Location: $goto");

//return
if(isset($return)) header("Location: $return");
?>	
	
