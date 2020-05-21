<?php
//mysql://b79cc14ad249eb:76b0ba67@us-cdbr-iron-east-01.cleardb.net/heroku_9899d38b5c56894?reconnect=true
//$branchNo = $_GET['id'];//Get ID Branch https://firstbitlinebot.herokuapp.com/?id=1
$branchNo = '111';
 $servername = "us-cdbr-iron-east-01.cleardb.net";
  $username = "b79cc14ad249eb";
  $password = "76b0ba67";
  $dbname = "heroku_9899d38b5c56894";
  $mysql = new mysqli($servername, $username, $password, $dbname);
  mysqli_set_charset($mysql, "utf8");

  if ($mysql->connect_error){
  $errorcode = $mysql->connect_error;
  print("MySQL(Connection)> ".$errorcode);
  }
  $getBranch = $mysql->query("SELECT ID,name,accessToken,admin_code,branch_code FROM  branch WHERE branch_code='$branchNo'");
  $getNum = $getBranch->num_rows;
  if ( $getNum == "0"){
      $access_token='';
      $admin_code='';
  } else {
    while($row =  $getBranch->fetch_assoc()){
      $access_token = $row['accessToken'];
      $admin_code=$row['admin_code'];
    }
  }


$access_token = $access_token;
  $LINEData = file_get_contents('php://input');
  $LINEDatas['token'] = $access_token;
  $jsonData = json_decode($LINEData,true);

  $replyToken = $jsonData["events"][0]["replyToken"];
  $userID = $jsonData["events"][0]["source"]["userId"];
  $text = $jsonData["events"][0]["message"]["text"];
  $timestamp = $jsonData["events"][0]["timestamp"]; //$results['response']
  $mID = $jsonData["events"][0]["message"]["id"];
  $mType =$jsonData["events"][0]["message"]["type"];
  $qNo=1;
  $qStatus="wait";
  function sendMessage($replyJson, $sendInfo){
          $ch = curl_init($sendInfo["URL"]);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLINFO_HEADER_OUT, true);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Authorization: Bearer ' . $sendInfo["AccessToken"])
              );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $replyJson);
          $result = curl_exec($ch);
          curl_close($ch);
    return $result;
  }
  function getContent($datas)
{
	$datasReturn = [];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.line.me/v2/bot/message/" . $datas['messageId'] . "/content",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer " . $datas['token'],
			"cache-control: no-cache"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
		$datasReturn['result'] = 'E';
		$datasReturn['message'] = $err;
	} else {
		$datasReturn['result'] = 'S';
		$datasReturn['message'] = 'Success';
		$datasReturn['response'] = $response;
	}

	return $datasReturn;
}
function _sendOut($access_token, $replyToken, $messagesX)
{

	$url = 'https://api.line.me/v2/bot/message/reply';

	$data = [

		'replyToken' => $replyToken,

		'messages' => $messagesX,

	];



	$post = json_encode($data);

	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);




	// $deCode = json_decode($post, true);
	// file_put_contents('log2.txt', implode("", $data) , FILE_APPEND);


	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$result = curl_exec($ch);

	curl_close($ch);



	echo $result . "\r\n";
}
function send_reply_message($url, $post_header, $post_body)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
function pushMsg($arrayHeader,$arrayPostData){
      $strUrl = "https://api.line.me/v2/bot/message/push";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$strUrl);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $result = curl_exec($ch);
      curl_close ($ch);
   }
    function getLINEProfile($datas)
{
   $datasReturn = [];
   $curl = curl_init();
   curl_setopt_array($curl, array(
     CURLOPT_URL => $datas['url'],
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_ENCODING => "",
     CURLOPT_MAXREDIRS => 10,
     CURLOPT_TIMEOUT => 30,
     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
     CURLOPT_CUSTOMREQUEST => "GET",
     CURLOPT_HTTPHEADER => array(
       "Authorization: Bearer ".$datas['token'],
       "cache-control: no-cache"
     ),
   ));
   $response = curl_exec($curl);
   $err = curl_error($curl);
   curl_close($curl);
   if($err){
      $datasReturn['result'] = 'E';
      $datasReturn['message'] = $err;
   }else{
      if($response == "{}"){
          $datasReturn['result'] = 'S';
          $datasReturn['message'] = 'Success';
      }else{
          $datasReturn['result'] = 'E';
          $datasReturn['message'] = $response;
      }
   }
   return $datasReturn;
}
 //ADD_Q
$replyText["type"] = "text";
$isUsed='T';
///
$IsAddAdmin='F';
 $getBranch = $mysql->query("SELECT ID,name,accessToken,admin_code,branch_code FROM  branch WHERE admin_code='$text'");
  $getNum = $getBranch->num_rows;
  if ( $getNum == "0"){
      
  } else {
    while($row =  $getBranch->fetch_assoc()){
      $a_branch_code=$row['branch_code'];
      $IsAddAdmin='T';
    }
  }
///SETUP ADMIN //////////////

if ($IsAddAdmin=='T') {
     //Update permisstion
	$mysql->query("UPDATE `user_profiles` SET `permission` ='admin' ,`branch_no` ='$a_branch_code'   WHERE u_id='$userID' ");
    $replyText["text"] = "ระบบได้เพิ่มคุณเป็น Admin ร้านเรียบร้อยค่ะ";
}
///// ADD PERMISSTION
$isRegister='F';
$cus_name='';
$permission='user';
$getQno = $mysql->query("select u_id,branch_no,permission,name,tel from user_profiles where u_id='$userID'");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      //ยังไม่เคยลงทะเบียน
  	$arrTxt=explode(" ",  $text);
    $name=$arrTxt[0]; 
    $tel=$arrTxt[1]; 
  	  $isUsed='F';
      
      if(strlen($tel)<>10)
      {
      	 $replyText["text"] = "กรุณากรอกข้อมูลก่อนเข้ารับบริการ ชื่อ เว้นวรรค ตามด้วยเบอร์โทรด้วยค่ะ";
      }else
      {
      	$LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userID;
$LINEDatas['token'] = $access_token;
$results = getLINEProfile($LINEDatas);
$profileText = implode("", $results);
$str_arr = explode (",", $profileText); 
$x1=explode (":", $str_arr[1]); 
$x2=explode (":", $str_arr[2]); 
$permission='user';
$displayName=str_replace("\"", "", $x1[1]);
$pictureUrl=str_replace("\"", "", $x2[1]);
$statusMessage=$results['statusMessage'];
$email=$results["E"][0]["displayName"];
  	//Insert User Profile
$mysql->query("INSERT INTO `user_profiles`(`u_id`,`branch_no`,`displayName`,`pictureUrl`,`statusMessage`,`email`,`permission`,`name`,`tel`,`lang`)VALUES('$userID','$branchNo','$displayName','$pictureUrl','$profileText','$email','$permission','$name','$tel','THI')");
$action='ADD_USER';
$mysql->query("INSERT INTO `user_action`(`u_id`,`action`)VALUES('$userID','$action')");
		$cus_name=$name;
      	$replyText["text"] = "คุณ $name หมายเลขโทรศัพท์ $tel ลงทะเบียนเรียบร้อยค่ะ";
      }

  } else {
    while($row = $getQno->fetch_assoc()){
      $isUsed='T';
      $permission=$row['permission'];
      $name=$row['name']; 
      $tel=$row['tel']; 
      $isRegister='T';
      //$START_Q=$row['START_Q'];
    }
  }
    $arrTxt=explode(" ",  $text);
    if(permission=='user')
    {
    $text=$arrTxt[0]; 
    }
    $branch_code=$arrTxt[1];  


    if($isRegister=='T')
    {
    ////SET Branch NO
    // ตรวจสอบ ACTION ก่อนหน้า /////
 
		$isUsed='F';
        if($permission=='user')
        {

          if ($branch_code=='') {
          	//ตรวจสอบ TEXT 
           		 if($text== 'ADD_Q')
            		{
            			$mysql->query("UPDATE `user_action` SET `action` ='ADD_Q'  WHERE u_id='$userID' ");
            			$replyText["text"] = "ป้อนรหัสร้านที่ต้องการจองคิวค่ะ";
            		}else if($text== '1')
            	{
            			//CANCEL Q
            			$mysql->query("UPDATE `user_action` SET `action` ='CANCEL_Q'  WHERE u_id='$userID' ");
            			$replyText["text"] = "ป้อนรหัสร้านที่ต้องการยกเลิกคิวค่ะ";
            	}else if($text== '2')
            	{
            		  $mysql->query("UPDATE `user_profiles` SET `lang` ='THI'  WHERE u_id='$userID' ");
                      $replyText["text"] = "LANG = THI แสดงข้อความภาษาไทย";
            	}else if($text== '3')
            	{
            		  $mysql->query("UPDATE `user_profiles` SET `lang` ='ENG'  WHERE u_id='$userID' ");
            		  $replyText["text"] = "LANG = ENG แสดงข้อความภาษาอังกฤษ";
            	}
            	else if ($text== 'CURRENT_Q') {
            		$isUsed='T';
            	}else if($text== 'OPTION')
            	        {
                             $replyText["text"] = "กด 1 ยกเลิกคิว, กด 2 ภาษาไทย, กด 3 English";
            	        }
            			else{
            				//ตรวจสอบ BRANCH_CODE
            				
							$getAcc= $mysql->query("SELECT action FROM user_action where u_id='$userID'");
  							$getNum = $getAcc->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getAcc->fetch_assoc()){
      									$user_action = $row['action'];
    									}
  								}

            			
                            $replyText["text"]="ป้อนรหัสร้านไม่ถูกต้องกรุณาป้อนใหม่ค่ะ";
            				$getBranch = $mysql->query("SELECT ID,name,accessToken,branch_code FROM  branch WHERE branch_code='$text'");
  							$getNum = $getBranch->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getBranch->fetch_assoc()){
      									$branch_code = $row['branch_code'];
      									$text=$user_action; //Set User Action ใหม่
      									$isUsed='T';
    									}
  								}

            			}
          }else
          {
          	$isUsed='T';
          }

        }else
        {
        	//ADMIN
        	$isUsed='F';
        	//Get Branch_code
        	$getBranch = $mysql->query("SELECT branch_no FROM  user_profiles WHERE u_id='$userID'");
  							$getNum = $getBranch->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getBranch->fetch_assoc()){
      									$branch_code = $row['branch_no'];
    									}
  								}
  		    if($text== 'ADD_Q')
  		    {
  		    	$mysql->query("UPDATE `user_action` SET `action` ='ADD_Q'  WHERE u_id='$userID' ");
            			$replyText["text"] = "กรุณากรอกชื่อ เว้นวรรค ตามด้วยเบอร์โทรลูกค้าด้วยค่ะ";
  		    }else if($text== 'OPTION')
  		    {
 
                 $replyText["text"] = "กด 1 ยกเลิกคิว, กด 2 ภาษาไทย, กด 3 English";           	        
  		    }else if($text== '1')
  		    {
  		    	//// ไม่ทำงาน
  		    }else if($text== '2')
            	{
            		  $mysql->query("UPDATE `user_profiles` SET `lang` ='THI'  WHERE u_id='$userID' ");
                      $replyText["text"] = "LANG = THI แสดงข้อความภาษาไทย";
            	}else if($text== '3')
            	{
            		  $mysql->query("UPDATE `user_profiles` SET `lang` ='ENG'  WHERE u_id='$userID' ");
            		  $replyText["text"] = "LANG = ENG แสดงข้อความภาษาอังกฤษ";
            	}else if($text== 'CURRENT_Q')
            	{


					$getAcc= $mysql->query("select IFNULL(max(q_no),0) AS q_no from add_q  where status='complete'  and branch_code='$branch_code'");
  							$getNum = $getAcc->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getAcc->fetch_assoc()){
      									$CurrentQ = $row['q_no'];
    									}
  								}

$getAcc= $mysql->query("select IFNULL(max(q_no),0) AS q_no from add_q  where status='wait'  and branch_code='$branch_code'");
  							$getNum = $getAcc->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getAcc->fetch_assoc()){
      									$LastQ = $row['q_no'];
    									}
  								}

$getQ1 = $mysql->query("SELECT count(*) as cancleQ FROM add_q where status ='cancel' and branch_code='$branch_code' and q_no>$CurrentQ  and q_no<$LastQ");
  $get1 = $getQ1->num_rows;
  if ( $get1 == "0"){
      //
  } else {
    while($row = $getQ1->fetch_assoc()){
    	    $cancelQ = $row['cancleQ'];
    }
  }
  
  $allWaitQ=$LastQ-$CurrentQ-$cancelQ;

 $replyText["text"] = "หมายเลขคิวปัจจุบันคือ $CurrentQ หมายเลขคิวสุดท้ายคือ $LastQ รออยู่ $allWaitQ คิว";


            	}else if($text== 'NEXT_Q')
            	{


///////////BEGIN NEXT Q
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
	//UPDATE STATUS Q
	$getQno = $mysql->query("SELECT IFNULL(MIN(q_no),0) as qNO FROM add_q where status ='wait' and branch_code='$branch_code'");
    $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo="No Q";
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['qNO'];
    }
  }
$mysql->query("UPDATE `heroku_9899d38b5c56894`.`add_q` SET `status` ='complete'  WHERE q_no='$qNo' and branch_code='$branch_code'");

$getAcc= $mysql->query("select name,name_t,tel from add_q   WHERE q_no='$qNo' and branch_code='$branch_code'");
  							$getNum = $getAcc->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getAcc->fetch_assoc()){
      									$Aname = $row['name'];
      									$Aname_t = $row['name_t'];
      									$Atel = $row['tel'];
    									}
  								}

$replyText["text"] = "คิวที่ $qNo จาก $Aname ชื่อ $Aname_t เบอร์โทร $Atel";

//ตรวจสอบถ้าเป็นคิว Admin ผู้ ADD จะไม่ push message แจ้งคิว wait 
//Push Message Queue
$accessToken = $access_token;//copy ข้อความ Channel access token ตอนที่ตั้งค่า
   $content = file_get_contents('php://input');
   $arrayJson = json_decode($content, true);
   $arrayHeader = array();
   $arrayHeader[] = "Content-Type: application/json";
   $arrayHeader[] = "Authorization: Bearer {$accessToken}";
   //Display Current Q
$getMsg = $mysql->query("SELECT u_id,name,q_no,reply_token FROM add_q where  branch_no='$branch_code' and q_no='$qNo'");
$getNum = $getMsg->num_rows;
  if ( $getNum == "0"){
      //$qNo="No Q";
  } else {
    while($row = $getMsg->fetch_assoc()){
      //$qNo = $row['qNO'];
    	//รับ id ของผู้ใช้
    	    $name = $row['name'];
    	    $userQ= $row['q_no'];
    	    $waitQ=$userQ-$qNo;
    	    $textMsg="ถึงคิวที่ $userQ ของคุณแล้ว";
     		$id = $row['u_id'];
         	$arrayPostData['to'] = $id;
          	$arrayPostData['messages'][0]['type'] = "text";
          	$arrayPostData['messages'][0]['text'] = $textMsg;
          	if($id<>$userID)
          	{
          	pushMsg($arrayHeader,$arrayPostData);
            }
    }
  }
$getMsg = $mysql->query("SELECT u_id,name,q_no,reply_token FROM add_q where status='wait' and branch_no='$branch_code' Order By q_no LIMIT 3");
  $getNum = $getMsg->num_rows;
  if ( $getNum == "0"){
      //$qNo="No Q";
  } else {
    while($row = $getMsg->fetch_assoc()){
      //$qNo = $row['qNO'];
    	//รับ id ของผู้ใช้
    	    $name = $row['name'];
    	    $userQ= $row['q_no'];
    	    $waitQ=$userQ-$qNo;
    	    $textMsg="คิวล่าสุดคือ $qNo รออีก $waitQ คิว";
     		$id = $row['u_id'];
         	$arrayPostData['to'] = $id;
          	$arrayPostData['messages'][0]['type'] = "text";
          	$arrayPostData['messages'][0]['text'] = $textMsg;
          	if($id<>$userID)
          	{
          	pushMsg($arrayHeader,$arrayPostData);
            }
    }
  }

///// END NEXT Q
            	}else if($IsAddAdmin=='T')
            	{
            		///กรณี Add Admin ด้วย CODE Branch สำเร็จ
            	}
  		    else   {
  		    			$getAcc= $mysql->query("SELECT action FROM user_action where u_id='$userID'");
  							$getNum = $getAcc->num_rows;
  							if ( $getNum == "0"){
     						         //ป้อน CODE ร้านไม่ถูกต้อง
  								} else {
    									while($row =  $getAcc->fetch_assoc()){
      									$user_action = $row['action'];
    									}
  								}
  								if($user_action=='ADD_Q')
  								{
  									$arrTxt=explode(" ",  $text);
   								    $name=$arrTxt[0]; 
                                    $tel=$arrTxt[1]; 
      
                                  if(strlen($tel)<>10)
                                    {
      	                                $replyText["text"] = "กรุณากรอกชื่อ เว้นวรรค ตามด้วยเบอร์โทรลูกค้าด้วยค่ะ";
                                    }else{
                                    	//// ADD_Q ลุกค้า โดย Admin
                                    	  ////Get Branch Code
  	$getQno = $mysql->query("select START_Q from branch where branch_code='$branch_code'");
    $getNum = $getQno->num_rows;
    if ( $getNum == "0"){

           $START_Q=0;
           //$replyText["text"] = "ไม่พบ branch_code ไม่สามารถระบุร้านได้";
    } else {
    while($row = $getQno->fetch_assoc()){
      $START_Q=$row['START_Q'];
    }
  }

  if($START_Q==1)
  {
	//ตรวจสอบต้องเป็น User ADD ใหม่ หรือ คิว Complete ไปแล้ว
   //$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`");  
$LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userID;
$LINEDatas['token'] = $access_token;
$results = getLINEProfile($LINEDatas);
$profileText = implode("", $results);
$str_arr = explode (",", $profileText); 
$x1=explode (":", $str_arr[1]);  
//$displayName=$x1[1];
$displayName=str_replace("\"", "", $x1[1]);
$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
  //select Max AddQ
  $getQno = $mysql->query("select MAX(q_no) As q_no from add_q  WHERE  branch_code='$branch_code'");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo=1;
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['q_no'];
    }
    $qNo =$qNo +1;
  }

    $mysql->query("INSERT INTO `add_q`(`u_id`, `branch_no`, `name`,`q_no`,`reply_token`,`status`,`branch_code`,`name_t`,`tel`) VALUES ('$userID','$branch_code','$displayName','$qNo','$replyToken','$qStatus','$branch_code','$name','$tel')");
     //$replyText["text"] = "หมายเลขคิวของคุณ $text คือ $qNo ค่ะ";
     $replyText["text"] = "หมายเลขคิวของคุณ $name คือ $qNo ค่ะ";
  	//
}elseif ($START_Q==2) {
 	//PAUSE_Q
 	$replyText["text"] = "ขออภัย ร้านหยุดรับคิวชั่วคราว";
 }elseif ($START_Q==3) {
 	//STOP_Q
 	$replyText["text"] = "ขออภัย ร้านยังไม่เปิดรับคิวในเวลานี้";
 } ////END $START_Q
                                    }
  								} //// END IF ADD_Q

  		    		}


        }



///////////////////////////////////////
    //$START_Q   1=เปิดรับ Q , 2 =PAUSE_Q ,3 = STOP_Q 
  if($isUsed=='T')
  {

if($text== 'ADD_Q' && $permission=='user')
{
	////// ทำการเลือก Branch 
    //$replyText["text"] = "คุณ $text หมายเลขโทรศัพท์ $tel ลงทะเบียนเรียบร้อยค่ะ 555";
	if($branch_code<>'')
	{

  ////Get Branch Code
  	$getQno = $mysql->query("select START_Q from branch where branch_code='$branch_code'");
    $getNum = $getQno->num_rows;
    if ( $getNum == "0"){

           $START_Q=0;
           //$replyText["text"] = "ไม่พบ branch_code ไม่สามารถระบุร้านได้";
    } else {
    while($row = $getQno->fetch_assoc()){
      $START_Q=$row['START_Q'];
    }
  }
      
  if($START_Q==1)
  {
	//ตรวจสอบต้องเป็น User ADD ใหม่ หรือ คิว Complete ไปแล้ว
	$addNewQ='F';
    $getQno = $mysql->query("SELECT u_id,name FROM add_q where branch_code='$branch_code' AND u_id='$userID' and status='wait' AND q_no >(select IFNULL(max(q_no),0) AS q_no from add_q  where status='complete'  and branch_code='$branch_code')");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $addNewQ='T';
  } else {
    $addNewQ='F';
  }
if($addNewQ=='T')
{
   //$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`");  
$LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userID;
$LINEDatas['token'] = $access_token;
$results = getLINEProfile($LINEDatas);
$profileText = implode("", $results);
$str_arr = explode (",", $profileText); 
$x1=explode (":", $str_arr[1]);  
//$displayName=$x1[1];
$displayName=str_replace("\"", "", $x1[1]);
$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
  //select Max AddQ
  $getQno = $mysql->query("select MAX(q_no) As q_no from add_q  WHERE  branch_code='$branch_code'");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo=1;
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['q_no'];
    }
    $qNo =$qNo +1;
  }

	$mysql->query("INSERT INTO `LOG`(`UserID`, `Text`, `Timestamp`,`image`) VALUES ('$userID','$text','$timestamp','$image')");
    $mysql->query("INSERT INTO `add_q`(`u_id`, `branch_no`, `name`,`q_no`,`reply_token`,`status`,`branch_code`,`name_t`,`tel`) VALUES ('$userID','$branch_code','$displayName','$qNo','$replyToken','$qStatus','$branch_code','$name','$tel')");
     //$replyText["text"] = "หมายเลขคิวของคุณ $text คือ $qNo ค่ะ";
     $replyText["text"] = "หมายเลขคิวของคุณ $name คือ $qNo ค่ะ";
  	//
 }else{
 	////// รอคิว
 	  $qNo = 0;
      $name = '';
 	$getQno = $mysql->query("SELECT u_id,name,q_no FROM add_q where branch_no='$branch_code' AND u_id='$userID' and status ='wait'");
    $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      //
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['q_no'];
      //$name = $row['name'];
    }
  }
 	$replyText["text"] = "หมายเลขคิวของคุณ $name คือ $qNo หากต้องการเพิ่มคิวใหม่ กรุณากดปุ่มตัวเลือก และยกเลิกคิวก่อนนะค่ะ";
 }
 }elseif ($START_Q==2) {
 	//PAUSE_Q
 	$replyText["text"] = "ขออภัย ร้านหยุดรับคิวชั่วคราว";
 }elseif ($START_Q==3) {
 	//STOP_Q
 	$replyText["text"] = "ขออภัย ร้านยังไม่เปิดรับคิวในเวลานี้";
 } ////END $START_Q



}////////////// END ADD_Q



}elseif (($text== 'CLEAR_Q') && $permission =='admin') {
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q` where branch_no=$branchNo");
	$replyText["text"] = "เครียร์คิวเรียบร้อยค่ะ";
}elseif ($text== 'NEXT_Q') {


//SELECT AND UPDATE STATUS 
    if($permission=='admin')
    {
	///////////BEGIN NEXT Q
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
	//UPDATE STATUS Q
	$getQno = $mysql->query("SELECT MIN(q_no)  as qNO FROM add_q where status ='wait' and branch_no=$branchNo");
    $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo="No Q";
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['qNO'];
    }
  }
$mysql->query("UPDATE `heroku_9899d38b5c56894`.`add_q` SET `status` ='complete'  WHERE q_no='$qNo' and branch_no=$branchNo");
$replyText["text"] = "คิวถัดไปคือ $qNo";

//ตรวจสอบถ้าเป็นคิว Admin ผู้ ADD จะไม่ push message แจ้งคิว wait 
//Push Message Queue
$accessToken = $access_token;//copy ข้อความ Channel access token ตอนที่ตั้งค่า
   $content = file_get_contents('php://input');
   $arrayJson = json_decode($content, true);
   $arrayHeader = array();
   $arrayHeader[] = "Content-Type: application/json";
   $arrayHeader[] = "Authorization: Bearer {$accessToken}";
   //Display Current Q
$getMsg = $mysql->query("SELECT u_id,name,q_no,reply_token FROM add_q where  branch_no=$branchNo and q_no='$qNo'");
$getNum = $getMsg->num_rows;
  if ( $getNum == "0"){
      //$qNo="No Q";
  } else {
    while($row = $getMsg->fetch_assoc()){
      //$qNo = $row['qNO'];
    	//รับ id ของผู้ใช้
    	    $name = $row['name'];
    	    $userQ= $row['q_no'];
    	    $waitQ=$userQ-$qNo;
    	    $textMsg="ถึงคิวของคุณ $name แล้วนะคะ";
     		$id = $row['u_id'];
         	$arrayPostData['to'] = $id;
          	$arrayPostData['messages'][0]['type'] = "text";
          	$arrayPostData['messages'][0]['text'] = $textMsg;
          	pushMsg($arrayHeader,$arrayPostData);
    }
  }
$getMsg = $mysql->query("SELECT u_id,name,q_no,reply_token FROM add_q where status='wait' and branch_no=$branchNo Order By q_no LIMIT 3");
  $getNum = $getMsg->num_rows;
  if ( $getNum == "0"){
      //$qNo="No Q";
  } else {
    while($row = $getMsg->fetch_assoc()){
      //$qNo = $row['qNO'];
    	//รับ id ของผู้ใช้
    	    $name = $row['name'];
    	    $userQ= $row['q_no'];
    	    $waitQ=$userQ-$qNo;
    	    $textMsg="คิวล่าสุดคือ $qNo คิวของคุณ $name คือ $userQ รออีก $waitQ ค่ะ";
     		$id = $row['u_id'];
         	$arrayPostData['to'] = $id;
          	$arrayPostData['messages'][0]['type'] = "text";
          	$arrayPostData['messages'][0]['text'] = $textMsg;
          	pushMsg($arrayHeader,$arrayPostData);
    }
  }

///// END NEXT Q
}

}elseif ($text== 'CURRENT_Q') {
$tempTxt='';
   $accessToken = $access_token;//copy ข้อความ Channel access token ตอนที่ตั้งค่า
   $content = file_get_contents('php://input');
   $arrayJson = json_decode($content, true);
   $arrayHeader = array();
   $arrayHeader[] = "Content-Type: application/json";
   $arrayHeader[] = "Authorization: Bearer {$accessToken}";
$get = $mysql->query("SELECT b.name,a.branch_code,name_t,q_no,a.u_id FROM add_q a inner join branch b on a.branch_code=b.branch_code where u_id='$userID'  AND status='wait'");
  $getNum1 = $get->num_rows;
  if ( $getNum1 == "0"){
      $tempTxt="ไม่มีคิวที่จะแสดง";
  } else {
    while($row = $get->fetch_assoc()){
      //$qNo = $row['qNO'];
    	//SELECT AND UPDATE STATUS
    	$branch_name=$row['name'];
    	$b_code=$row['branch_code'];
    	$name_t=$row['name_t'];
    	$a_Qno=$row['q_no'];
    	$a_id=$row['u_id'];
	$getQno = $mysql->query("SELECT MAX(q_no)  as qNO FROM add_q where status ='complete' and branch_code='$b_code'");
  	$getNum = $getQno->num_rows;
  	$qNo=0;
  	if ( $getNum == "0"){
      	$qNo="No Q";
      	$tempTxt=$tempTxt."ไม่มีคิวที่จะแสดง";
  		} else {
    			while($row = $getQno->fetch_assoc()){
      			$qNo = $row['qNO'];
                //$tempTxt=$tempTxt."หมายเลขคิวปัจุบัน ร้าน $branch_name คือ $qNo หมายเลขคิวของคุณ $name_t คือ $a_Qno รออีก [X] คิว ";
                ///////// หา Q ststus cancel 
if($qNo=='')
  {
  	$qNo=0;
  }
             $getQ1 = $mysql->query("SELECT count(*) as cancleQ FROM add_q where status ='cancel' and branch_code='$b_code' and q_no>$qNo  and q_no<$a_Qno");
  $get1 = $getQ1->num_rows;
  if ( $get1 == "0"){
      //
  } else {
    while($row = $getQ1->fetch_assoc()){
    	    $cancelQ = $row['cancleQ'];
    }
  }
  
  $allWaitQ=$a_Qno-$cancelQ-$qNo;
               /////////////////////////////////////////
            $textMsg="หมายเลขคิวปัจุบัน ร้าน $branch_name คือ $qNo หมายเลขคิวของคุณ $name_t คือ $a_Qno รออีก $allWaitQ คิว ";
     		$id = $row['u_id'];
         	$arrayPostData['to'] = $a_id;
          	$arrayPostData['messages'][0]['type'] = "text";
          	$arrayPostData['messages'][0]['text'] = $textMsg;
          	pushMsg($arrayHeader,$arrayPostData);
    			}
  				}
    	}
  }


//$replyText["text"] = $tempTxt;//"หมายเลขคิวปัจุบัน $qNo";
   ///// END CURRENT Q
}else if($text== 'ADD_USER')
  {
  	//$userID
$LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userID;
$LINEDatas['token'] = $access_token;
$results = getLINEProfile($LINEDatas);
$profileText = implode("", $results);
$str_arr = explode (",", $profileText); 
$x1=explode (":", $str_arr[1]); 
$x2=explode (":", $str_arr[2]); 
$permission='user';
$displayName=str_replace("\"", "", $x1[1]);
$pictureUrl=str_replace("\"", "", $x2[1]);
$statusMessage=$results['statusMessage'];
$email=$results["E"][0]["displayName"];
  	//Insert User Profile
$mysql->query("INSERT INTO `user_profiles`(`u_id`,`branch_no`,`displayName`,`pictureUrl`,`statusMessage`,`email`,`permission`)VALUES('$userID','$branchNo','$displayName','$pictureUrl','$profileText','$email','$permission')");


  }else if($text== 'ADD_Q' && $permission =='admin'){
	
	$replyText["text"] = "กรุณากรอกข้อมูลก่อนเข้ารับบริการ ชื่อ เว้นวรรค ตามด้วยเบอร์โทรด้วยค่ะ";


  }else if($text <> '' && $permission =='admin' &&( ($text <> 'ADD_Q')&&($text <> 'CURRENT_Q')&&($text <> 'CANCEL_Q') && ($text <> 'NEXT_Q')&& ($text <> 'CLEAR_Q') )){
	//ADDMIN ADD_Q
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
  //select Max AddQ
  $getQno = $mysql->query("select MAX(q_no) As q_no from add_q  WHERE  branch_no=$branchNo");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo=1;
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['q_no'];
    }
    $qNo =$qNo +1;
  }

	$mysql->query("INSERT INTO `LOG`(`UserID`, `Text`, `Timestamp`,`image`) VALUES ('$userID','$text','$timestamp','$image')");
    $mysql->query("INSERT INTO `add_q`(`u_id`, `branch_no`, `name`,`q_no`,`reply_token`,`status`) VALUES ('$userID','$branchNo','$text','$qNo','$replyToken','$qStatus')");
     $replyText["text"] = "หมายเลขคิวของคุณลูกค้า $text คือ $qNo ค่ะ";
  	//
  }else if(($text== 'CANCEL_Q') && ($permission =='user'))
  {
  	//ยกเลิกคิวที่มี status wait
  	$mysql->query("UPDATE `heroku_9899d38b5c56894`.`add_q` SET `status` ='cancel'  WHERE u_id='$userID' AND branch_code=$branch_code and status ='wait'");
  	$replyText["text"] = "ยกเลิกคิวเรียบร้อยแล้วค่ะ ขอบคุณที่ใช้บริการ";
  }//Else $text
}///////// END  IF Isused
}///////////END IF isRegister

  $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
  $lineData['AccessToken'] = $access_token;

  $replyJson["replyToken"] = $replyToken;
  $replyJson["messages"][0] = $replyText;

  $encodeJson = json_encode($replyJson);

  $results = sendMessage($encodeJson,$lineData);
  echo $results;
  http_response_code(200);


 