


<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * The interface that represents HTTP client of LINE Messaging API.
 *
 * If you want to switch using HTTP client, please implement this.
 *
 * @package LINE\LINEBot
 */
interface HTTPClient
{
    /**
     * Sends GET request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data URL parameters.
     * @param array $headers
     * @return Response Response of API request.
     */
    public function get($url, array $data = [], array $headers = []);

    /**
     * Sends POST request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data Request body.
     * @param array|null $headers Request headers.
     * @return Response Response of API request.
     */
    public function post($url, array $data, array $headers = null);
    
    /**
     * Sends DELETE request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     */
    public function delete($url);
}
?>
<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\LINEBot;

/**
 * A class represents API response.
 *
 * @package LINE\LINEBot
 */
class Response
{
    /** @var int */
    private $httpStatus;
    /** @var string */
    private $body;
    /** @var string[] */
    private $headers;

    /**
     * Response constructor.
     *
     * @param int $httpStatus HTTP status code of response.
     * @param string $body Request body.
     * @param string[] $headers
     */
    public function __construct($httpStatus, $body, $headers = [])
    {
        $this->httpStatus = $httpStatus;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Returns HTTP status code of response.
     *
     * @return int HTTP status code of response.
     */
    public function getHTTPStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Returns request is succeeded or not.
     *
     * @return bool Request is succeeded or not.
     */
    public function isSucceeded()
    {
        return $this->httpStatus === 200;
    }

    /**
     * Returns raw response body.
     *
     * @return string Raw request body.
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * Returns response body as array (it means, returns JSON decoded body).
     *
     * @return array Request body that is JSON decoded.
     */
    public function getJSONDecodedBody()
    {
        return json_decode($this->body, true);
    }

    /**
     * Returns the value of the specified response header.
     *
     * @param string $name A String specifying the header name.
     * @return string|null A response header string, or null if the response does not have a header of that name.
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return null;
    }

    /**
     * Returns all of response headers.
     *
     * @return string[] All of the response headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
?>
<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

use LINE\LINEBot\Constant\Meta;
use LINE\LINEBot\Exception\CurlExecutionException;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\Response;

/**
 * Class CurlHTTPClient.
 *
 * A HTTPClient that uses cURL.
 *
 * @package LINE\LINEBot\HTTPClient
 */
class CurlHTTPClient implements HTTPClient
{
    /** @var array */
    private $authHeaders;
    /** @var array */
    private $userAgentHeader;

    /**
     * CurlHTTPClient constructor.
     *
     * @param string $channelToken Access token of your channel.
     */
    public function __construct($channelToken)
    {
        $this->authHeaders = [
            "Authorization: Bearer $channelToken",
        ];
        $this->userAgentHeader = [
            'User-Agent: LINE-BotSDK-PHP/' . Meta::VERSION,
        ];
    }

    /**
     * Sends GET request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data Request body
     * @param array $headers Request headers.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function get($url, array $data = [], array $headers = [])
    {
        if ($data) {
            $url .= '?' . http_build_query($data);
        }
        return $this->sendRequest('GET', $url, $headers);
    }

    /**
     * Sends POST request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array|null $headers Request headers.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function post($url, array $data, array $headers = null)
    {
        $headers = is_null($headers) ? ['Content-Type: application/json; charset=utf-8'] : $headers;
        return $this->sendRequest('POST', $url, $headers, $data);
    }

    /**
     * Sends DELETE request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     * @throws CurlExecutionException
     */
    public function delete($url)
    {
        return $this->sendRequest('DELETE', $url, [], []);
    }

    /**
     * @param string $method
     * @param array $headers
     * @param string|null $reqBody
     * @return array cUrl options
     */
    private function getOptions($method, $headers, $reqBody)
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HEADER => true,
        ];
        if ($method === 'POST') {
            if (is_null($reqBody)) {
                // Rel: https://github.com/line/line-bot-sdk-php/issues/35
                $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            } else {
                if (isset($reqBody['__file']) && isset($reqBody['__type'])) {
                    $options[CURLOPT_PUT] = true;
                    $options[CURLOPT_INFILE] = fopen($reqBody['__file'], 'r');
                    $options[CURLOPT_INFILESIZE] = filesize($reqBody['__file']);
                } elseif (in_array('Content-Type: application/x-www-form-urlencoded', $headers)) {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = http_build_query($reqBody);
                } elseif (!empty($reqBody)) {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = json_encode($reqBody);
                } else {
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = $reqBody;
                }
            }
        }
        return $options;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $additionalHeader
     * @param string|null $reqBody
     * @return Response
     * @throws CurlExecutionException
     */
    private function sendRequest($method, $url, array $additionalHeader, $reqBody = null)
    {
        $curl = new Curl($url);

        $headers = array_merge($this->authHeaders, $this->userAgentHeader, $additionalHeader);

        $options = $this->getOptions($method, $headers, $reqBody);
        $curl->setoptArray($options);

        $result = $curl->exec();

        if ($curl->errno()) {
            throw new CurlExecutionException($curl->error());
        }

        $info = $curl->getinfo();
        $httpStatus = $info['http_code'];

        $responseHeaderSize = $info['header_size'];

        $responseHeaderStr = substr($result, 0, $responseHeaderSize);
        $responseHeaders = [];
        foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
            $kv = explode(':', $responseHeader, 2);
            if (count($kv) === 2) {
                $responseHeaders[$kv[0]] = trim($kv[1]);
            }
        }

        $body = substr($result, $responseHeaderSize);

        if (isset($options[CURLOPT_INFILE])) {
            fclose($options[CURLOPT_INFILE]);
        }

        return new Response($httpStatus, $body, $responseHeaders);
    }
}
?>
<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * cURL session manager
 *
 * @package LINE\LINEBot\HTTPClient
 */
class Curl
{
    /** @var resource */
    private $ch;

    /**
     * Initialize a cURL session
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->ch = curl_init($url);
    }

    /**
     * Set multiple options for a cURL transfer
     *
     * @param array $options Returns TRUE if all options were successfully set. If an option could not be
     * successfully set, FALSE is immediately returned, ignoring any future options in the options array.
     * @return bool
     */
    public function setoptArray(array $options)
    {
        return curl_setopt_array($this->ch, $options);
    }

    /**
     * Perform a cURL session
     *
     * @return bool Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER
     * option is set, it will return the result on success, FALSE on failure.
     */
    public function exec()
    {
        return curl_exec($this->ch);
    }

    /**
     * Gets information about the last transfer.
     *
     * @return array
     */
    public function getinfo()
    {
        return curl_getinfo($this->ch);
    }

    /**
     * @return int Returns the error number or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return curl_errno($this->ch);
    }

    /**
     * @return string Returns the error message or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return curl_error($this->ch);
    }

    /**
     * Closes a cURL session and frees all resources. The cURL handle, ch, is also deleted.
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }
}
?>
<?php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// include composer autoload
//require_once '../vendor/autoload.php';
 
// การตั้งเกี่ยวกับ bot
/// การตั้งค่าเกี่ยวกับ bot ใน LINE Messaging API
define('LINE_MESSAGE_CHANNEL_ID','1649637522');
define('LINE_MESSAGE_CHANNEL_SECRET','f9629f9dedd8637ddd1ff39c02ca9ae1');
define('LINE_MESSAGE_ACCESS_TOKEN','yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU=');
 
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
 
 
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
 
// คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');
 
// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);
if(!is_null($events)){
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $userID = $events['events'][0]['source']['userId'];
    $sourceType = $events['events'][0]['source']['type'];
    $is_postback = NULL;
    $is_message = NULL;
    if(isset($events['events'][0]) && array_key_exists('message',$events['events'][0])){
        $is_message = true;
        $typeMessage = $events['events'][0]['message']['type'];
        $userMessage = $events['events'][0]['message']['text'];     
        $idMessage = $events['events'][0]['message']['id']; 
    }
    if(isset($events['events'][0]) && array_key_exists('postback',$events['events'][0])){
        $is_postback = true;
        $dataPostback = NULL;
        parse_str($events['events'][0]['postback']['data'],$dataPostback);;
        $paramPostback = NULL;
        if(array_key_exists('params',$events['events'][0]['postback'])){
            if(array_key_exists('date',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['date'];
            }
            if(array_key_exists('time',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['time'];
            }
            if(array_key_exists('datetime',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['datetime'];
            }                       
        }
    }   
    if(!is_null($is_postback)){
        $textReplyMessage = "ข้อความจาก Postback Event Data = ";
        if(is_array($dataPostback)){
            $textReplyMessage.= json_encode($dataPostback);
        }
        if(!is_null($paramPostback)){
            $textReplyMessage.= " \r\nParams = ".$paramPostback;
        }
        $replyData = new TextMessageBuilder($textReplyMessage);     
    }
    if(!is_null($is_message)){
        switch ($typeMessage){
            case 'text':
                $userMessage = strtolower($userMessage); // แปลงเป็นตัวเล็ก สำหรับทดสอบ
                switch ($userMessage) {
                    case "p":
                        // เรียกดูข้อมูลโพรไฟล์ของ Line user โดยส่งค่า userID ของผู้ใช้ LINE ไปดึงข้อมูล
                        $response = $bot->getProfile($userID);
                        if ($response->isSucceeded()) {
                            // ดึงค่ามาแบบเป็น JSON String โดยใช้คำสั่ง getRawBody() กรณีเป้นข้อความ text
                            $textReplyMessage = $response->getRawBody(); // return string            
                            $replyData = new TextMessageBuilder($textReplyMessage);         
                            break;              
                        }
                        // กรณีไม่สามารถดึงข้อมูลได้ ให้แสดงสถานะ และข้อมูลแจ้ง ถ้าไม่ต้องการแจ้งก็ปิดส่วนนี้ไปก็ได้
                        $failMessage = json_encode($response->getHTTPStatus() . ' ' . $response->getRawBody());
                        $replyData = new TextMessageBuilder($failMessage);
                        break;              
                    case "สวัสดี":
                        // เรียกดูข้อมูลโพรไฟล์ของ Line user โดยส่งค่า userID ของผู้ใช้ LINE ไปดึงข้อมูล
                        $response = $bot->getProfile($userID);
                        if ($response->isSucceeded()) {
                            // ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array 
                            $userData = $response->getJSONDecodedBody(); // return array     
                            // $userData['userId']
                            // $userData['displayName']
                            // $userData['pictureUrl']
                            // $userData['statusMessage']
                            $textReplyMessage = 'สวัสดีครับ คุณ '.$userData['displayName'];             
                            $replyData = new TextMessageBuilder($textReplyMessage);         
                            break;              
                        }
                        // กรณีไม่สามารถดึงข้อมูลได้ ให้แสดงสถานะ และข้อมูลแจ้ง ถ้าไม่ต้องการแจ้งก็ปิดส่วนนี้ไปก็ได้
                        $failMessage = json_encode($response->getHTTPStatus() . ' ' . $response->getRawBody());
                        $replyData = new TextMessageBuilder($failMessage);
                        break;                                                                                                                                                                                                                                          
                    default:
                        $textReplyMessage = " คุณไม่ได้พิมพ์ ค่า ตามที่กำหนด";
                        $replyData = new TextMessageBuilder($textReplyMessage);         
                        break;                                      
                }
                break;      
            case (preg_match('/image|audio|video/',$typeMessage) ? true : false) :
                $response = $bot->getMessageContent($idMessage);
                if ($response->isSucceeded()) {
                    // คำสั่ง getRawBody() ในกรณีนี้ จะได้ข้อมูลส่งกลับมาเป็น binary 
                    // เราสามารถเอาข้อมูลไปบันทึกเป็นไฟล์ได้
                    $dataBinary = $response->getRawBody(); // return binary
                    // ดึงข้อมูลประเภทของไฟล์ จาก header
                    $fileType = $response->getHeader('Content-Type');    
                    switch ($fileType){
                        case (preg_match('/^image/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $ext = ($ext=='jpeg' || $ext=='jpg')?"jpg":$ext;
                            $fileNameSave = time().".".$ext;
                            break;
                        case (preg_match('/^audio/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $fileNameSave = time().".".$ext;                        
                            break;
                        case (preg_match('/^video/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $fileNameSave = time().".".$ext;                                
                            break;                                                      
                    }
                    $botDataFolder = 'botdata/'; // โฟลเดอร์หลักที่จะบันทึกไฟล์
                    $botDataUserFolder = $botDataFolder.$userID; // มีโฟลเดอร์ด้านในเป็น userId อีกขั้น
                    if(!file_exists($botDataUserFolder)) { // ตรวจสอบถ้ายังไม่มีให้สร้างโฟลเดอร์ userId
                        mkdir($botDataUserFolder, 0777, true);
                    }   
                    // กำหนด path ของไฟล์ที่จะบันทึก
                    $fileFullSavePath = $botDataUserFolder.'/'.$fileNameSave;
                    file_put_contents($fileFullSavePath,$dataBinary); // ทำการบันทึกไฟล์
                    $textReplyMessage = "บันทึกไฟล์เรียบร้อยแล้ว $fileNameSave";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
                }
                $failMessage = json_encode($idMessage.' '.$response->getHTTPStatus() . ' ' . $response->getRawBody());
                $replyData = new TextMessageBuilder($failMessage);  
                break;                                                      
            default:
                $textReplyMessage = json_encode($events);
                $replyData = new TextMessageBuilder($textReplyMessage);         
                break;  
        }
    }
}
$response = $bot->replyMessage($replyToken,$replyData);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}
 
// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

echo 'Succeeded!';
?>

