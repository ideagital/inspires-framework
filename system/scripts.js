function goBack()
  {
  window.history.back()
  }
function ShowHide(show,hide){ // ฟังก์ชั่นสำหรับ แสดง (Show) ส่งค่า id ของ DIV หรือ Table TD TR
	document.getElementById(show).style.display = ''; 
	document.getElementById(hide).style.display = 'none'; 
}
function ShowShow(show1,show2){
	document.getElementById(show1).style.display = ''; 
	document.getElementById(show2).style.display = '';
}
function HideHide(hide1,hide2){ 
	document.getElementById(hide1).style.display = 'none'; 
	document.getElementById(hide2).style.display = 'none'; 
}

jQuery(function($) {
	$("#signup_username").change(function() { // triggred click
        
        /************** start: email exist function and etc. **************/
        $("#result").html("Loading");
		
		username=jQuery.trim($("#signup_username").val());

        var datastring = 'process=signup_username_exist&username='+ username; // get data in the form manual
        //var datastring = $('form#mainform').serialize(); // or use serialize
        $.ajax({
            type: "POST", // type
            url: "ajax", // request file the 'check_email.php'
            data: datastring, // post the data
            success: function(responseText) { // get the response
                        if(responseText == 1) { // if the response is 1
                            $("#result").html("<i class='glyphicon glyphicon-ban-circle'></i>");
							document.getElementById("username_check_status").className = "form-group has-error has-feedback";
							this.setCustomValidity("Username are already exist.");   
							return false;
                        } else if(responseText == 0) {
							$("#result").html("<i class='glyphicon glyphicon-ok'></i>");
							document.getElementById("username_check_status").className = "form-group has-success has-feedback";
							$("#username_check_status").setCustomValidity("Username is OK.");   
                        }else{
							$("#result").html(responseText);
						}
                    } // end success
        }); // ajax end
        /************** end: email exist function and etc. **************/
		
    }); // click end
	
	$(".comment_text").keypress(function(e) { // triggred click
        /************** start: email exist function and etc. **************/
		if(e.which == 13) {
			$("#result").html("Loading");
			
			comment_text=jQuery.trim($(this).val());
			post_id=jQuery.trim($(this).data('post'));

			var datastring = 'action=comment_add&comment_text='+ comment_text + '&post_id=' + post_id; // get data in the form manual
			
			//var datastring = $('form#mainform').serialize(); // or use serialize
			$.ajax({
                    type: "POST", // type
                    url: "ajax", // request file the 'check_email.php'
                    data: datastring, // post the data
                    success: function(responseText) { // get the response
                        if(responseText == 1) { // if the response is 1
                            $("#add_comment_text_" + post_id).html(comment_text);
							$("#add_comment_" + post_id).toggleClass("hidden");
							$("#frm_add_comment_" + post_id).toggleClass("hidden");
                        } else if(responseText == 0) {
							$("#result").html("<i class='glyphicon glyphicon-ok'></i>");
							document.getElementById("username_check_status").className = "form-group has-success has-feedback";
							$("#username_check_status").setCustomValidity("Username is OK.");   
                        }else{
							$("#result").html(responseText);
						}
                    } // end success
			}); // ajax end
		}
        /************** end: email exist function and etc. **************/
		
    }); // click end
	
	$(".like_post").click(function() { // triggred click
			$("#result").html("Loading");
			post_id=jQuery.trim($(this).data('post'));
			likes=$(this).data('likes');
			likes=likes+1;
			var datastring = 'action=like_post_add&post_id=' + post_id; // get data in the form manual			
			$.ajax({
                    type: "POST", // type
                    url: "ajax", // request file the 'check_email.php'
                    data: datastring, // post the data
                    success: function(responseText) { // get the response
                        if(responseText == 1) { // if the response is 1
                            $("#like_post_" + post_id).attr("class","btn btn-sm btn-primary like_post");
                            $("#post_likes_" + post_id).html(likes);
                        }else if(responseText == 0) {
						
                        }else{
							$("#result").html(responseText);
						}
                    } // end success
			}); // ajax end
    }); // click end
	
	$(".btn_add_post_text").click(function() { 
		$(".add_post_text").attr("style","");
		$(".add_post_photo").addClass("hidden");
		$(".add_post_link").addClass("hidden");
		$(".add_post_video").addClass("hidden");
		
		$(".btn_add_post_text").addClass("active");
		$(".btn_add_post_photo").removeClass("active");
		$(".btn_add_post_link").removeClass("active");
		$(".btn_add_post_video").removeClass("active");
    });
	$(".btn_add_post_photo").click(function() { 
		$(".add_post_text").attr("style","height:40px;margin-top:5px;");
		$(".add_post_photo").removeClass("hidden");
		$(".add_post_link").addClass("hidden");
		$(".add_post_video").addClass("hidden");
		
		$(".btn_add_post_text").removeClass("active");
		$(".btn_add_post_photo").addClass("active");
		$(".btn_add_post_link").removeClass("active");
		$(".btn_add_post_video").removeClass("active");
    });
	$(".btn_add_post_link").click(function() { 
		$(".add_post_text").attr("style","height:60px;margin-top:5px;");
		$(".add_post_photo").addClass("hidden");
		$(".add_post_link").removeClass("hidden");
		$(".add_post_video").addClass("hidden");
		
		$(".btn_add_post_text").removeClass("active");
		$(".btn_add_post_photo").removeClass("active");
		$(".btn_add_post_link").addClass("active");
		$(".btn_add_post_video").removeClass("active");
    });
	$(".btn_add_post_video").click(function() { 
		$(".add_post_text").attr("style","height:60px;margin-top:5px;");
		$(".add_post_photo").addClass("hidden");
		$(".add_post_link").addClass("hidden");
		$(".add_post_video").removeClass("hidden");
		
		$(".btn_add_post_text").removeClass("active");
		$(".btn_add_post_photo").removeClass("active");
		$(".btn_add_post_link").removeClass("active");
		$(".btn_add_post_video").addClass("active");
    });
	
	$("#signup_password_confirm").blur(function() { // triggred click
        
		password=jQuery.trim($("#signup_password").val());
		password_confirm=jQuery.trim($("#signup_password_confirm").val());
		
		if(password == password_confirm) { // if the response is 1
			$("#result").html("<i class='glyphicon glyphicon-ok'></i>");
			document.getElementById("password_check").className = "form-group has-success has-feedback";
			document.getElementById("password_confirm").className = "form-group has-success has-feedback";
		}else{
			alert("Password not match");
			document.getElementById("password_check").className = "form-group has-error has-feedback";
			document.getElementById("password_confirm").className = "form-group has-error has-feedback";
			$("#result").html(responseText);
		}
    }); // click end
}); // jquery end