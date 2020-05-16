<?php
//mysql://b79cc14ad249eb:76b0ba67@us-cdbr-iron-east-01.cleardb.net/heroku_9899d38b5c56894?reconnect=true
$branchNo = $_GET['id'];//Get ID Branch https://firstbitlinebot.herokuapp.com/?id=1
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
  $getBranch = $mysql->query("SELECT ID,name,accessToken FROM  branch WHERE ID=$branchNo");
  $getNum = $getBranch->num_rows;
  if ( $getNum == "0"){
      $access_token='';
  } else {
    while($row =  $getBranch->fetch_assoc()){
      $access_token = $row['accessToken'];
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
///// ADD PERMISSTION
$permission='user';
$getQno = $mysql->query("select u_id,branch_no from user_profiles where u_id='$userID' and permission='admin'");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      //user
  } else {
    while($row = $getQno->fetch_assoc()){
      $permission='admin';
    }
  }

///////////////////////////////////////
if($text== 'ADD_Q' && $permission=='user')
{
	//ตรวจสอบต้องเป็น User ADD ใหม่ หรือ คิว Complete ไปแล้ว
	$addNewQ='F';
    $getQno = $mysql->query("SELECT u_id,name FROM add_q where branch_no=$branchNo AND u_id='$userID' AND q_no >(select IFNULL(max(q_no),0) AS q_no from add_q  where status='complete'  and branch_no=$branchNo)");
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
    $mysql->query("INSERT INTO `add_q`(`u_id`, `branch_no`, `name`,`q_no`,`reply_token`,`status`) VALUES ('$userID','$branchNo','$displayName','$qNo','$replyToken','$qStatus')");
     //$replyText["text"] = "หมายเลขคิวของคุณ $text คือ $qNo ค่ะ";
     $replyText["text"] = "หมายเลขคิวของคุณ $displayName คือ $qNo ค่ะ";
  	//
 }else{
 	////// รอคิว
 	  $qNo = 0;
      $name = '';
 	$getQno = $mysql->query("SELECT u_id,name,q_no FROM add_q where branch_no=$branchNo AND u_id='$userID' and status ='wait'");
    $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      //
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['q_no'];
      $name = $row['name'];
    }
  }
 	$replyText["text"] = "คุณ $name ได้เพิ่มคิวไปแล้วก่อนหน้าคิวเลขที่ $qNo หากต้องการเพิ่มคิวใหม่ กรุณากดยกเลิกคิวก่อนนะค่ะ";
 }

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
	


//SELECT AND UPDATE STATUS
$getQno = $mysql->query("SELECT MAX(q_no)  as qNO FROM add_q where status ='complete' and branch_no=$branchNo");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo="No Q";
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['qNO'];
    }
  }
$replyText["text"] = "หมายเลขคิวปัจุบัน $qNo";

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


  }else if($text== 'B_ADD_Q' && $permission =='admin'){
	
	$replyText["text"] = "ป้อนชื่อ และ นามสกุลด้วยนะคะ";
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
  }else if(($text== 'CANCEL_Q ') && ($permission =='user'))
  {
  	//ยกเลิกคิวที่มี status wait
  	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id='$userID' AND branch_no=$branchNo and status ='wait'");
  	$replyText["text"] = "ยกเลิกคิวเรียบร้อยแล้วค่ะ ขอบคุณที่ใช้บริการ";
  }//Else $text
  $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
  $lineData['AccessToken'] = $access_token;

  $replyJson["replyToken"] = $replyToken;
  $replyJson["messages"][0] = $replyText;

  $encodeJson = json_encode($replyJson);

  $results = sendMessage($encodeJson,$lineData);
  echo $results;
  http_response_code(200);


 