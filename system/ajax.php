<?php
isset($_REQUEST['process'])?$process=$_REQUEST['process'] : $process=null;
isset($_REQUEST['action'])?$action=$_REQUEST['action'] : $action=null;
isset($_REQUEST['return'])?$return=$_REQUEST['return'] : $return=null;
isset($_SESSION['me_login'])?$me_login=$_SESSION['me_login'] : $me_login=null;
$session_id=session_id();

if($process=="member_edit"){
	$edit=$_REQUEST['edit'];
	$value=$_REQUEST['value'];
	mysqli_query($connect,"UPDATE members SET $edit='$value' WHERE member_id=$me_login;");
	echo "1";
}elseif($process=="member_username_check"){
	$value=$_REQUEST['value'];
	$check_query = mysqli_query($connect,"SELECT member_username FROM members WHERE member_username='".$value."'");
	$count=mysqli_num_rows($check_query);
	if($count==0){
		mysqli_query($connect,"UPDATE members SET member_username='$value' WHERE member_id=$me_login;");
	}
	echo $count;
}elseif($process=="member_username_check_only"){
	$value=$_REQUEST['value'];
	$check_query = mysqli_query($connect,"SELECT member_username FROM members WHERE member_username='".$value."'");
	$count=mysqli_num_rows($check_query);
	if($count==0){
		$_SESSION['signup_username']=$value;
	}
	echo $count;
}elseif($process=="signup_username_exist"){
	$username = $_REQUEST['username'];
	$query = "SELECT member_username FROM members WHERE member_username='$username'";
	$link = mysqli_query($connect,$query);
	if (!$link) {
		die('query error');
	}
	$num=mysqli_num_rows($link)+0;
	if ($num>0){
	//die('email already exists'); //email already taken   
		echo '1';
	 }else{	
		echo '0';	
	}
}elseif($process=="member_password_check"){
	$value=encode($_REQUEST['value'],$private_key);
	$check=mysqli_num_rows(mysqli_query($connect,"SELECT member_password FROM members WHERE member_id=$me_login AND member_password='$value'"));
	echo $check;
}elseif($process=="member_password_update"){
	$edit=$_REQUEST['edit'];
	$value=encode($_REQUEST['value'],$private_key);
	mysqli_query($connect,"UPDATE members SET $edit='$value' WHERE member_id=$me_login;");
	echo "1";
}

if($action=="comment_add"){
	isset($_REQUEST['comment_text']) ? $comment_text=$_REQUEST['comment_text'] : $comment_text=null;
	isset($_REQUEST['post_id']) ? $post_id=$_REQUEST['post_id'] : $post_id=null;
	
	if($comment_text!=null){
		$query=mysqli_query($connect,"INSERT INTO post_comments(comment_date,comment_text,member_id,post_id) VALUES(NOW(),'$comment_text',$me_login,$post_id) ");
		if($query){
			echo '1';
		}else{
			echo '0';
		}
	}else{
		echo '0';
	}
}elseif($action=="like_post_add"){
	isset($_REQUEST['post_id']) ? $post_id=$_REQUEST['post_id'] : $post_id=null;
	
	$post_result=mysqli_fetch_array(mysqli_query($connect,"SELECT post_likes FROM posts WHERE post_id=$post_id"));	
	$post_likes=$post_result['post_likes']+1;
	
	$query=mysqli_query($connect,"UPDATE posts SET post_likes='$post_likes' WHERE post_id=$post_id");
	if($query){
		echo '1';
	}else{
		echo '0';
	}
}

?>