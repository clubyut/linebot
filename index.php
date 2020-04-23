<?php
//mysql://b79cc14ad249eb:76b0ba67@us-cdbr-iron-east-01.cleardb.net/heroku_9899d38b5c56894?reconnect=true
$access_token = 'yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU=';
  $LINEData = file_get_contents('php://input');
  $LINEDatas['token'] = $access_token;
  $jsonData = json_decode($LINEData,true);

  $replyToken = $jsonData["events"][0]["replyToken"];
  $userID = $jsonData["events"][0]["source"]["userId"];
  $text = $jsonData["events"][0]["message"]["text"];
  $timestamp = $jsonData["events"][0]["timestamp"]; //$results['response']
  $mID = $jsonData["events"][0]["message"]["id"];
  $mType =$jsonData["events"][0]["message"]["type"];
  $branchNo=1;
  $qNo=1;
  $qStatus="wait";
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
 //ADD_Q
$replyText["type"] = "text";
if($text== 'ADD_Q')
{
	
   //$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`");
   $replyText["text"] = "กรุณาป้อนชื่อด้วยค่ะ";

}elseif ($text== 'CLEAR_Q') {
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`");
	$replyText["text"] = "เครียร์คิวเรียบร้อยค่ะ";
}elseif ($text== 'NEXT_Q') {
	$replyText["text"] = "คิวถัดไปคือ";
}elseif ($text== 'CURRENT_Q') {
	$replyText["text"] = "หมายเลขคิวปัจุบัน";
}else{
  //select Max AddQ
$getQno = $mysql->query("select MAX(q_no) As q_no from add_q  WHERE status='wait'");
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
     $replyText["text"] = "หมายเลขคิวของคุณ $text คือ $qNo ค่ะ";
  }
  $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
  $lineData['AccessToken'] = "yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU=";

  $replyJson["replyToken"] = $replyToken;
  $replyJson["messages"][0] = $replyText;

  $encodeJson = json_encode($replyJson);

  $results = sendMessage($encodeJson,$lineData);
  echo $results;
  http_response_code(200);