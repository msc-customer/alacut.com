<?php
session_start();
include('config/config1.php');
include('ThumbImage.php');
if($_SESSION['login_user_id']=="")
{
header("Location: ".SITE_URL);exit();
}	
$member_ID = $_SESSION['login_user_id'];

$selectPwdQry = mysql_query("SELECT `password` FROM `alacut_member` WHERE  member_id = '".$member_ID."' ");
		$selectPwdRes = mysql_fetch_assoc($selectPwdQry); 
		$currentPwdDB = $selectPwdRes['password'];

if($_POST['btnDeleteUser'] == 'DELETE')
{	
	if($currentPwdDB == $_POST['currentPwdToDel'])
	{	
		mysql_query("UPDATE `alacut_member` SET `active` = '2',`deleted_by`='".$member_ID."' WHERE  member_id = '".$member_ID."' "); 
		header("location: index.php");exit();
	}else 
		{
			echo "Password doesn't match";
		}
}

if($_POST['btnUserType'] == 'Confirm')
{	
	if($currentPwdDB == $_POST['currentPwdUserType'])
	{	
		mysql_query("UPDATE `alacut_member` SET `handle` = 'E' WHERE  member_id = '".$member_ID."' "); 
		header("location: readUserInfo.php");
		exit();
	}else 
		{
			echo "Password doesn't match";
		}
}

if($_POST['btnCreateUserInfo'] == 'Save Your Profile')  
{
	//Upload Photo
	$profilePhoto = ' ';
	if($_FILES['txtProfilePhoto']['name'] != '' && $_FILES['txtProfilePhoto']['size'] > 0)
	{
		$fileName = @md5($empCode).basename( str_replace(' ', '', $_FILES['txtProfilePhoto']['name']));
		$target_path = $_SERVER["DOCUMENT_ROOT"].'/upload/profilePhotos/'. $fileName;
		if ( move_uploaded_file($_FILES['txtProfilePhoto']['tmp_name'], $target_path))
		{
			CreateThumb_40($target_path,$fileName,$_SERVER["DOCUMENT_ROOT"].'/upload/profilePhotos/thumb/t_40/');
			CreateThumb_90($target_path,$fileName,$_SERVER["DOCUMENT_ROOT"].'/upload/profilePhotos/thumb/t_90/');
			CreateProfileThumb($target_path,$fileName,$_SERVER["DOCUMENT_ROOT"].'/upload/profilePhotos/');
			$profilePhoto = " , `profile_photo_name` = '$fileName'";
		}
	}
	//Password Change
	if($currentPwdDB == $_POST['currentPwd'])
	{	
		if($_POST['newPwd1'] == $_POST['newPwd2'])
		{	
			mysql_query("UPDATE `alacut_member` SET `password` = '".$_POST['newPwd1']."' WHERE  member_id = '".$member_ID."' "); 
		}else 
			{
				echo "New Password doesn't match";
			}
	}else
		{
			echo "Old Password doesn't match";	
		}
	//Update user detail	                                        
	$updateUserInfoSql = "UPDATE `alacut_member` SET `full_name` = '".$_POST['txtName']."', `job` = '".$_POST['txtJob']."', `education` = '".$_POST['txtEdu']."', `address` = '".$_POST['txtLoc']."', `relationship` = '".$_POST['ddMStatus']."', `married_to` = '".$_POST['txtMarried']."', `handle` = '".$_POST['userType']."', `url` = '".$_POST['txtUrl']."' $profilePhoto 
						  WHERE member_id = '".$member_ID."' AND active = '1'";
 	//echo $updateUserInfoSql;exit();
	@mysql_query($updateUserInfoSql);
	
	header("location: readUserInfo.php");
	exit();
}

$selectUserInfoSql = "SELECT `member_id`,`full_name`,`job`,`education`,`address`,`relationship`,`married_to`,`url`,
`handle`,`profile_photo_name`,`profile_photo_name` FROM `alacut_member` WHERE member_id = '".$member_ID."' 
AND active = '1'";		
$selectUserInfoRes = mysql_query($selectUserInfoSql);		
$selectUserInfo = mysql_fetch_array($selectUserInfoRes); 	

$totalPostsQry = "SELECT COUNT(`video_id`) AS count FROM `upload_video` WHERE `video_upload_by`='".$member_ID."' AND `status` != '2'";
$totalPostsRes = mysql_query($totalPostsQry);
$totalPostsInfo = mysql_fetch_array($totalPostsRes);
?>
<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8" />

<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if lte IE 6]>
<link rel="stylesheet" href="http://universal-ie6-css.googlecode.com/files/ie6.1.1.css" media="screen, projection">
<![endif]-->
<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<link type="text/css" rel="stylesheet" href="css/colorbox.css" />
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
<script>
$(document).ready(function () {
	$("#btnSearch").click(function(){
		$(".popup").colorbox({scrolling: false, overlayClose: false, escKey: false, opacity: 0.8});
		$("#btnSearch").attr("href","Search.php?search="+$("#txtFriendSearch").val())	
		});	
	$('#lnk_ff').click(function(){
		$('#dv_usrinfo').hide();
		$('#dv_frnds').show();
	});
	$('.cancel').click(function(){
		$('#dv_usrinfo').show();
		$('#dv_frnds').hide();
	});
$('#no').click(function(){
	$('.delete_acc').slideUp();
});
	  $(".change_password").hide();
	  $("#div_Change_Pwd").click(function () { 
		  $(".change_password").slideToggle("fast");
		  $(".delete_acc").hide();
		  $(".entity").hide();
		  $(".upload_img").hide();
				});
			
	  $(".delete_acc").hide();
	  $("#div_delete").click(function () {
		  $(".delete_acc").slideToggle("fast");
		  $(".entity").hide();
		  $(".change_password").hide();
		  $(".upload_img").hide();
				});
		
	  $("#div_Photo").click(function () {
			$('#txtProfilePhoto').click();
			$(".delete_acc").hide();
			$(".entity").hide();
			$(".change_password").hide();
				});
	  
	  $(".div_Current_Pwd").hide();
	  $(".delYes").click(function () {
		  $(".div_Current_Pwd").slideDown("fast");
				});
	  $(".delNo").click(function () {
		  $(".div_Current_Pwd").slideUp("fast");
				});  
	  
	  $(".entity").hide();
	  $("#div_Entity").click(function () {
		  $(".entity").slideDown("fast");
		  $(".upload_img").hide();
		  $(".delete_acc").hide();
		  $(".change_password").hide();
				});
	  $("#div_Indivisual").click(function () {
		  $(".entity").hide();
		  $(".upload_img").hide();
		  $(".delete_acc").hide();
		  $(".change_password").hide();
				});	
	  $('#btnUserInfo').click(function(){
	  	
	  	if(validateSubmit())
	  	{
	  		$('#btnCreateUserInfo').click();
	  	}
	  });
	  $(".currentEntityPwd").hide();
	  $(".yes").click(function () {
		  $(".currentEntityPwd").slideDown("fast");
				});
	  $(".no").click(function () {
		  $(".currentEntityPwd").slideUp("fast");
				});
			
	$(".single").click(function () {
		$("#txtMarried").hide();
			});
	$(".engaged").click(function () {
		$("#txtMarried").hide();
			});
	$(".marriedTo").click(function () {
		$("#txtMarried").show();
			});
	$('#txtProfilePhoto').change(function(){
		checkfile(this);
	});
	
});
function validatPasswordToDelete()
{
	var name = $("#currentPwdToDel").val();
	var namelen = name.length;
	if (name == null || name == ""){
		alert("Please Enter Current Password");
		return false;
	}
}
function validatPwdToEntity()
{
	var name = $("#currentPwdUserType").val();
	var namelen = name.length;
	if (name == null || name == ""){
		alert("Please Enter Current Password");
		return false;
	}
}

function validateSubmit()
{
	var name = $("#txtName").val();
	var namelen = name.length;
			
	if (name == null || name == ""){
		alert("Please enter Full Name");
		return false;
	}else if(/^[a-zA-Z ]*$/.test(name)== false)
	{  
		alert('Full Name:Please enter alphabets only ');
		return false;
	}else if (namelen > 16){
		alert("Full Name :Please enter 16 characters only");
		return false;
	}else if ($('#hdnfile').val()=='0'){
		alert("Please Upload Valid File!!");
		return false;
	}
	
	return true;
}
function checkfile(input)
{
	var img=$(input).val();
	var srcimg=$('#img_org').attr('src');
	if(img!='')
	{
		var ext=img.split('.');
		if(ext.length==1)
		{
			$('#hdnfile').val('0');
			$('#img_profile').attr('src',srcimg);
			alert("Invalid Image File!!");
			return false;
		}
		else if(ext[ext.length-1]!='jpg' && ext[ext.length-1]!='jpeg' && ext[ext.length-1]!='png' )
		{
			$('#hdnfile').val('0');
			$('#img_profile').attr('src',srcimg);
			alert("Invalid Image File!!");
			return false;
		}
		else
		{
			
				file = input.files[0];
			    if(file.size>10485760 )
			    {
			    	alert("File is Too Large !!\nFile Size Must Be Less Than 10MB");
			    	$('#img_profile').attr('src',srcimg);
			    	$('#hdnfile').val('0');
			    	return false;
			    }else if(file.size<102400)
			    {
			    	alert("File is Too Small !!\nFile Size Must Be More Than 100Kb and Less Than 1MB");
			    	$('#img_profile').attr('src',srcimg);
			    	$('#hdnfile').val('0');
			    	return false;
			    }
			    else
			    {
			
			    var reader = new FileReader();
			
			    reader.onerror = function (e) {
			        alert('Error code: ' + e.target.error);
			    };
			
			    // Create a closure to capture the file information. 
			    reader.onload = (function (aFile) {
			        return function (evt) {
						$('#img_profile').attr('src',evt.target.result);
						$('#hdnfile').val('1');
					
			        }
			    })(file);
			    // Read in the image file as a data url. 
			    reader.readAsDataURL(file);
			    }
			    return true;
			}
		}
	
}
</script>
</head>
<body>
<form id="frmUserInfo" name="frmUserInfo" method="POST" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data">
<div class="wrapper">
  <?php include("header.php");?>
  <section class="inner">
	<section class="inner_left">
		<div id="dv_usrinfo">
		<header class="head">
          <h1>Edit Your Profile</h1>
          
        </header>
		
    	<section class="left_body">
    	
      	<section class="user_info">
        
        <section class="mid">
          <article class="img">
          	<?php if($selectUserInfo['profile_photo_name'] != ''){
                	echo '<img id="img_profile" src="upload/profilePhotos/'.$selectUserInfo['profile_photo_name'].'" alt="" width="300" height="300"/>';
                }else{
                    echo '<img  id="img_profile" src="images/no-image.jpg" alt="" width="300" height="300"/>'; 
            }?>
          </article>
         
          <section class="cont">
            <div class="head">Name</div>
            <div class="field">
              <label for="textfield"></label>
              <input type="text" name="txtName" id="txtName" value="<?php echo $selectUserInfo['full_name']?>"/>
            </div>
            <div class="head">Job</div>
            <div class="field">
              <label for="textfield6"></label>
              <input type="text" name="txtJob" id="txtJob" value="<?php echo $selectUserInfo['job']?>"/>
            </div>
            <div class="head">Education</div>
            <div class="field">
              <label for="textfield7"></label>
              <input type="text" name="txtEdu" id="txtEdu" value="<?php echo $selectUserInfo['education']?>"/>
            </div>
            <div class="head">Location</div>
            <div class="field">
              <label for="textfield8"></label>
              <input type="text" name="txtLoc" id="txtLoc" value="<?php echo $selectUserInfo['address']?>"/>
            </div>
            <div class="head">Marital</div>
            <div class="field">
              <select name="ddMStatus" id="ddMStatus" style="width: 84px; height: 18px;">
                <option class="single" value="S" selected="selected">Single</option>
                <option class="engaged" value="E" <?php if($selectUserInfo['relationship'] == 'E') echo 'selected="selected"';?>>Engaged</option>
                <option class="marriedTo" value="M" <?php if($selectUserInfo['relationship'] == 'M') echo 'selected="selected"';?>>Married to</option>
              </select>
              <input type="text" name="txtMarried" id="txtMarried" value="<?php echo $selectUserInfo['married_to']?>" style="width: 150px;display:<?php if($selectUserInfo['relationship'] == 'M') echo "block"; else echo "none;"; ?> margin-left: 10px" />
            </div>
            <div class="head">URL</div>
            <div class="field">
            	<div style="width: 50px; line-height: 15px;">alacut.com/</div>
            	<label for="textfield10"></label>
              <input type="text" name="txtUrl" id="txtUrl" value="<?php echo $selectUserInfo['url']?>" style="width: 184px; margin-left: 10px"/>
            </div>
            <div class="head">Handle</div>
            <div class="field">
              <select name="userType" id="userType" style="width: 84px; height: 18px;">
              	<option value="I" id="div_Indivisual" selected="selected">Individial</option>
                <option value="E" id="div_Entity" <?php if($selectUserInfo['handle'] == 'E') echo 'selected="selected"';?>>Entity</option>
              </select>
            </div>
          </section>

        </section>
        <section class="bot">
          <nav class="one">
            <button name="" id="div_Photo" type="button">New&nbsp;Profile&nbsp;Picture</button>
          </nav>
          <nav class="two" id="div_delete">
            <button type="button">Delete&nbsp;Account</button>
          </nav>
          <nav class="three" id="div_Change_Pwd">
            <button name="" type="button">Change&nbsp;Password</button>
        </nav>
          <nav class="four">
          	<button class="button"  id="btnUserInfo" type="button" value="Save Your Profile">Save Your Profile</button>
            <input style="display:none;" class="button" type="submit" id="btnCreateUserInfo" name="btnCreateUserInfo" value="Save Your Profile" />
      </nav>
        </section>
        <div class="upload_img" style="display: none;"><input type="file" id="txtProfilePhoto" name="txtProfilePhoto" style="width:300px;" /></div>
        <section class="change_password">
          <div class="head">Current Password</div>
          <div class="cont"><span class="field">
            <input type="password" name="currentPwd" id="currentPwd" />
          </span></div>
          <div class="head">New Password</div>
          <div class="cont"><span class="field">
            <input type="password" name="newPwd1" id="newPwd1" />
          </span></div>
          <div class="head">Re-enter New Password</div>
          <div class="cont"><span class="field">
            <input type="password" name="newPwd2" id="newPwd2" />
          </span></div>
        </section>
        <section class="delete_acc">
          <div class="warning">Are you sure want to delete your account? You can not undo this.</div>
          <div class="cont">
            <nav class="yes delYes" id="yes"><a href="javascript:void(0);">Yes</a></nav>
            <nav class="no delNo" id="no"><a href="javascript:void(0);">No</a></nav>
          </div>
          <div class="div_Current_Pwd">
            <div class="head">Enter your passsword and press delete button:</div>
            <div class="cont"><span class="field">
              <input type="password" name="currentPwdToDel" id="currentPwdToDel" />
            </span></div>
            <div class="head">&nbsp;</div>
            <div class="cont"><span class="field">
              <div class="del">
                <input type="submit" id="btnDeleteUser" name="btnDeleteUser" onclick="return validatPasswordToDelete();" value="DELETE"/>
              </div>
            </span></div>
          </div>
      </section>
        <section class="entity">
          <div class="head">
            <div class="warning">Are you sure want to change your handle to entity?</div>
          </div>
          <div class="cont">
            <nav class="yes"><a href="javascript:void(0);">Yes</a></nav>
            <nav class="no"><a href="javascript:void(0);">No</a></nav>
          </div>
          <div class="currentEntityPwd">
            <div class="head">Enter your passsword and press delete button:</div>
            <div class="cont"><span class="field">
              <input type="password" name="currentPwdUserType" id="currentPwdUserType" />
            </span></div>
            <div class="head">&nbsp;</div>
            <div class="cont"><span class="field">
              <div class="del">
                <input type="submit" id="btnUserType" name="btnUserType" onclick="return validatPwdToEntity();" value="Confirm"/>
              </div>
            </span></div>
          </div>
        </section>
        </section>
     	</form>
   	 </section>
		</div>
		<div id="dv_frnds" style="display:none;">
		<header class="head">
        <h1>Find your friends on alacut!</h1>
        
      </header>
      <section class="left_body_box">
      <section class="serch_friend">
      	<nav class="button"><a href="javascript:void(0);" class="cancel">Cancel</a></nav>
      <article class="sfriend_box">
        <div class="sfriend_menu"><img src="images/hotmail.jpg" width="150" height="40"></div>
        <div class="sfriend_menu_unsel"><img src="images/gmail.jpg" width="87" height="40"></div>  
        <div class="sfriend_menu_unsel"><img src="images/yahoomail.jpg" width="150" height="40"></div>
        <div class="sfriend_menu_unsel"><img src="images/facebook.jpg" width="150" height="40"></div>
      </article>
        <article class="sfriend">
        	<h1>Check your Hotmail Address Book</h1>
             <article>&nbsp;</article>
             <article>&nbsp;</article>
        </article>
      </section>
  </section>

	</div>

 	 </section>
</form>	
  	<aside class="inner_right">
      <section class="user_img">
        <article class="pic">
			<?php if($selectUserInfo['profile_photo_name'] != ''){
                echo '<img id="img_org" src="upload/profilePhotos/'.$selectUserInfo['profile_photo_name'].'" alt="" width="300" height="300"/>';
                }else{
                    echo '<img id="img_org" src="images/no-image.jpg" alt="" width="300" height="300"/>';
            }?>
        </article>
        <section class="detail_box">
          <h1><?php echo $selectUserInfo['full_name']?></h1>
          <div class="detail"><strong><img src="images/work.jpg" width="15" height="11" />Works at:</strong><?php echo "   "; echo $selectUserInfo['job']?></div>
          <div class="detail"><strong><img src="images/sudies.jpg" alt="" width="15" height="11" />Studied:</strong><?php echo "   "; echo $selectUserInfo['education']?></div>
          <div class="detail"><strong><img src="images/livesin.jpg" alt="" width="15" height="11" />Lives in:</strong><?php echo "   "; echo $selectUserInfo['address']?></div>
          <div class="detail"><strong><img src="images/married.jpg" alt="" width="15" height="11" />Married to:</strong><?php echo "   "; echo $selectUserInfo['married_to']?></div>
        </section>
      </section>
      <section class="user_btn">
        <nav class="button"><a id="lnk_ff" href="javascript:void(0);">Find&nbsp;Friends</a></nav>
        <nav class="button"><a href="myFriends.php">Friends</a></nav>
        <nav class="button"><a href="allPosts.php?id=<?php echo $selectUserInfo['member_id']?>"><?php echo $totalPostsInfo['count']?>&nbsp;&nbsp;Posts</a></nav>
        <nav class="button"><a href="editUserInfo.php" style="margin: 0;">Edit&nbsp;Info</a></nav>
    </section>
  </aside>
  </section>
  <?php include("footer.php");?>
</div>
<input type="hidden" id="hdnfile" value='0'/>
</body>
</html>
            