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
	
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
	//UPDATE STATUS Q
	$getQno = $mysql->query("SELECT MIN(q_no)  as qNO FROM add_q where status ='wait'");
    $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo="No Q";
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['qNO'];
    }
  }
$mysql->query("UPDATE `heroku_9899d38b5c56894`.`add_q` SET `status` ='complete'  WHERE q_no='$qNo'");
$replyText["text"] = "คิวถัดไปคือ $qNo";

}elseif ($text== 'CURRENT_Q') {
	$replyText["text"] = "หมายเลขคิวปัจุบัน";


//SELECT AND UPDATE STATUS
$getQno = $mysql->query("SELECT MAX(q_no)  as qNO FROM add_q where status ='complete'");
  $getNum = $getQno->num_rows;
  if ( $getNum == "0"){
      $qNo="No Q";
  } else {
    while($row = $getQno->fetch_assoc()){
      $qNo = $row['qNO'];
    }
  }

$API_URL = 'https://api.line.me/v2/bot/message';
$ACCESS_TOKEN = 'yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU='; 
$channelSecret = 'f9629f9dedd8637ddd1ff39c02ca9ae1';


$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

$jsonFlex = [
    "type" => "flex",
    "altText" => "Hello Flex Message",
    "contents" => [
      "type" => "bubble",
      "direction" => "ltr",
      "header" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => "หมายเลขคิวปัจจุบัน",
            "size" => "lg",
            "align" => "start",
            "weight" => "bold",
            "color" => "#009813"
          ],
          [
            "type" => "text",
            "text" => "        ".$qNo,
            "size" => "4xl",
            "weight" => "bold",
            "color" => "#000000"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "size" => "lg",
            "weight" => "bold",
            "color" => "#000000"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "size" => "xs",
            "color" => "#B2B2B2"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "margin" => "lg",
            "size" => "lg",
            "color" => "#000000"
          ]
        ]
      ],
      "body" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "separator",
            "color" => "#C3C3C3"
          ],
          [
            "type" => "box",
            "layout" => "baseline",
            "margin" => "lg",
            "contents" => [
              [
                "type" => "text",
                "text" => "พื้นที่โฆษณา",
                "align" => "start",
                "color" => "#C3C3C3"
              ],
              [
                "type" => "text",
                "text" => "TEST",
                "align" => "end",
                "color" => "#000000"
              ]
            ]
          ],
          [
            "type" => "box",
            "layout" => "baseline",
            "margin" => "lg",
            "contents" => [
              [
                "type" => "text",
                "text" => "พื้นที่โฆษณา",
                "color" => "#C3C3C3"
              ],
              [
                "type" => "text",
                "text" => "TEST",
                "align" => "end"
              ]
            ]
          ],
          [
            "type" => "separator",
            "margin" => "lg",
            "color" => "#C3C3C3"
          ]
        ]
      ],
      "footer" => [
        "type" => "box",
        "layout" => "horizontal",
        "contents" => [
          [
            "type" => "text",
            "text" => "ลิ้งพื้นที่โฆษณา",
            "size" => "lg",
            "align" => "start",
            "color" => "#0084B6",
            "action" => [
              "type" => "uri",
              "label" => "View Details",
              "uri" => "https://google.co.th/"
            ]
          ]
        ]
      ]
    ]
  ];



if ( sizeof($request_array['events']) > 0 ) {
    foreach ($request_array['events'] as $event) {
        error_log(json_encode($event));
        $reply_message = '';
        $reply_token = $event['replyToken'];


        $data = [
            'replyToken' => $reply_token,
            'messages' => [$jsonFlex]
        ];

        print_r($data);

        $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $send_result = send_reply_message($API_URL.'/reply', $POST_HEADER, $post_body);

        echo "Result: ".$send_result."\r\n";
        
    }
}




}else{
	$mysql->query("DELETE FROM `heroku_9899d38b5c56894`.`add_q`  WHERE u_id=''");
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






$API_URL = 'https://api.line.me/v2/bot/message';
$ACCESS_TOKEN = 'yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU='; 
$channelSecret = 'f9629f9dedd8637ddd1ff39c02ca9ae1';


$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

$jsonFlex = [
    "type" => "flex",
    "altText" => "Hello Flex Message",
    "contents" => [
      "type" => "bubble",
      "direction" => "ltr",
      "header" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "text",
            "text" => "เลขคิวของคุณ $text",
            "size" => "lg",
            "align" => "start",
            "weight" => "bold",
            "color" => "#009813"
          ],
          [
            "type" => "text",
            "text" => "        ".$qNo,
            "size" => "4xl",
            "weight" => "bold",
            "color" => "#000000"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "size" => "lg",
            "weight" => "bold",
            "color" => "#000000"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "size" => "xs",
            "color" => "#B2B2B2"
          ],
          [
            "type" => "text",
            "text" => "พื้นที่โฆษณา",
            "margin" => "lg",
            "size" => "lg",
            "color" => "#000000"
          ]
        ]
      ],
      "body" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
          [
            "type" => "separator",
            "color" => "#C3C3C3"
          ],
          [
            "type" => "box",
            "layout" => "baseline",
            "margin" => "lg",
            "contents" => [
              [
                "type" => "text",
                "text" => "พื้นที่โฆษณา",
                "align" => "start",
                "color" => "#C3C3C3"
              ],
              [
                "type" => "text",
                "text" => "TEST",
                "align" => "end",
                "color" => "#000000"
              ]
            ]
          ],
          [
            "type" => "box",
            "layout" => "baseline",
            "margin" => "lg",
            "contents" => [
              [
                "type" => "text",
                "text" => "พื้นที่โฆษณา",
                "color" => "#C3C3C3"
              ],
              [
                "type" => "text",
                "text" => "TEST",
                "align" => "end"
              ]
            ]
          ],
          [
            "type" => "separator",
            "margin" => "lg",
            "color" => "#C3C3C3"
          ]
        ]
      ],
      "footer" => [
        "type" => "box",
        "layout" => "horizontal",
        "contents" => [
          [
            "type" => "text",
            "text" => "ลิ้งพื้นที่โฆษณา",
            "size" => "lg",
            "align" => "start",
            "color" => "#0084B6",
            "action" => [
              "type" => "uri",
              "label" => "View Details",
              "uri" => "https://google.co.th/"
            ]
          ]
        ]
      ]
    ]
  ];



if ( sizeof($request_array['events']) > 0 ) {
    foreach ($request_array['events'] as $event) {
        error_log(json_encode($event));
        $reply_message = '';
        $reply_token = $event['replyToken'];


        $data = [
            'replyToken' => $reply_token,
            'messages' => [$jsonFlex]
        ];

        print_r($data);

        $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $send_result = send_reply_message($API_URL.'/reply', $POST_HEADER, $post_body);

        echo "Result: ".$send_result."\r\n";
        
    }
}









  }
  $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
  $lineData['AccessToken'] = "yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU=";

  $replyJson["replyToken"] = $replyToken;
  $replyJson["messages"][0] = $replyText;

  $encodeJson = json_encode($replyJson);

  $results = sendMessage($encodeJson,$lineData);
  echo $results;
  http_response_code(200);