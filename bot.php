<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


$strAccessToken = "nYv+i5TmedYy/LPS5Hopb9uf3VFKTyzXQ2gepzduwqQuohDhsKYL5lQbOxoPXPepXow/uO3qYy7+8gpqx/dWUBaAK/hXpZDlbiOiWzh3LlB4bf1Vj4huu/n7yW63qtyYPVVCvXma/3z/xnE+MPtBBQdB04t89/1O/w1cDnyilFU=";
//$strAccessToken = 'QyDCRLjPHuON1WvMIQwGxwAft8ejqSjmsLRqrICPjfWqwYJG+H9bLQmj7iwXe1ac+u0nqKInTo/8+E06TXKZ0uMIJMwhSA3xgIg6te4VYSxj61PSbxAnt2852xKDrKb3H0i9E1I6Gne7rWvbBo6JBgdB04t89/1O/w1cDnyilFU=';
//'izcgE06I9iuCDy2ZmpIJv/VnxXucgBSyGp1qlOD/MDB57rF16QmQG8WXf+/0hrAfPnnasXsBT8CrW80bk4H55JdCfzFRtV0PzauHLfBs/JUt+Yjrp47ruhUuaxYHH3J0XyRKeCvYd+Aaz5M5XMTdCAdB04t89/1O/w1cDnyilFU=';

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$replyToken = $arrJson['events'][0]['replyToken'];

if(isset($_msg)) {
  $_msg = $_msg;
} else {
  $_msg = $_GET['msg'];
}

$FIREBASE = "https://samuiaksorn-f0b31.firebaseio.com/";
$length = 15;
$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

                date_default_timezone_set('Asia/Bangkok');
                $time = time();
$date = date('h:i:s a m/d/Y', $time);

$mId = $arrJson['events'][0]['message']['id'];

if($arrJson['events'][0]['source']['type'] == 'user') {
  $fromId = $arrJson['events'][0]['source']['userId'];
  $type = 'user';
} else {
  $fromId = $arrJson['events'][0]['source']['userId'];
  $type = 'group';
}

if($arrJson['events'][0]['message']["type"] == 'text') {
  $mtype = "text";
} else if($arrJson['events'][0]['message']["type"] == 'image') {
  $mtype = "image";
  getMessageContent($arrHeader,$mId);
} else {
  $mtype = "other";
}


// $NODE_PUT = $fromId . ".json";

//                 $data = array(
//                   "messageId" => $mId,
//                   "messageType" => $mtype,
//                   "fromId" => $fromId,
//                   "type" => $type,
//                     "text" => $_msg,
//                     "time" => $date,
//                     "timestamp" => $time,
//                     "msg" => $arrJson['events'][0]['message'],
//                     "src" => $arrJson['events'][0]['source'],
//                     "obj" => $arrJson,
//                 );
//                     // JSON encoded
//                 $json = json_encode($data);

// $curl = curl_init();
//             //Create
//                  curl_setopt( $curl, CURLOPT_URL, $FIREBASE . $NODE_PUT );
//                  curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" );
//                 curl_setopt( $curl, CURLOPT_POSTFIELDS, $json);
//                // Get return value
//                 curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
//                 // Make request
//                 // Close connection
//                 $response = curl_exec( $curl );
//                 curl_close( $curl );


// $arrHeader2 = array();
// $arrHeader2[] = "Content-Type: application/json";
$query = '{"question":"'.$_msg.'"}';

$api_key="";
$mLab_url = 'https://api.mlab.com/api/1/databases/chatbot/collections/linebot?apiKey=q6QelAg9EiIAp60ySeabhVUxA9XSJPlT';

$json = file_get_contents($mLab_url . "&q=" . rawurlencode($query));
$data = json_decode($json);
$isData=sizeof($data);

//$isData = 0;

if (strpos($_msg, 'สอนเป็ด') !== false) {
  if (strpos($_msg, 'สอนเป็ด') !== false) {
    $x_tra = str_replace("สอนเป็ด","", $_msg);
    $pieces = explode("|", $x_tra);
    $_question=str_replace("[","",$pieces[0]);
    $_answer=str_replace("]","",$pieces[1]);
    //Post New Data
    $newData = json_encode(
      array(
        'question' => $_question,
        'answer'=> $_answer
      )
    );
    $opts = array(
      'http' => array(
          'method' => "POST",
          'header' => "Content-type: application/json",
          'content' => $newData
       )
    );
    $context = stream_context_create($opts);
    $returnValue = file_get_contents($mLab_url,false,$context);
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = 'ขอบคุณที่สอนเป็ด';
        $arrPostData['messages'][] = [
                    'type' => 'sticker',
                    'packageId' => 1,
                    'stickerId' => 4,
                ];
  }
}else{
  if($isData >0){
   foreach($data as $rec){
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $rec->answer;
   }
  }else{
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";

    $res = "";

    if(strlen($_msg) == 5 && is_numeric($_msg)) {
      $job_no = $_msg;
      $url = "http://erpsamuiaksorn.com/stat/2013/check/bot_check.php?job_no=" . $job_no;
      $bot_check_json = file_get_contents($url);
      $arr = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $bot_check_json), true );

      //print_r($arr);

      if($arr) {
        $the_data = $arr['item'];
        //$res .= "Job No: " . $the_data['job_no'] . " \n";
        $res .= "ชื่องาน: ". $the_data['print_name'] ." \n";
        $res .= "ลูกค้า: ". $the_data['p_name'] ." \n";
        $res .= "สถานะปัจจุบัน: ". $the_data['s_name'] . " (" . $the_data['probability'] . "%)" ." \n";
        $res .= "วันนัดรับงาน: ".$the_data['date_deadline']." \n";
        $res .= "\n";
        $res .= "อัพโหลดไฟล์งาน หรือ ดูรายละเอียดเพิ่มเติมได้ที่ \n";
        $res .= "http://erpsamuiaksorn.com/uploader/?job_no=".$_msg." ";
      } else {
        $res .= "ไม่พบ Job No: " . $job_no;
      }

      $arrPostData['messages'][0]['text'] = $res;
    } else {
      $holder_name = $_msg;
      $url = "http://erpsamuiaksorn.com/stat/2013/check/_holders.php";
      $holder_json = file_get_contents($url);
      $holders = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $holder_json), true );
      $arr = array();
      foreach($holders['items'] as $holder) {
        $arr[] = $holder['name'];
        $arr[] = $holder['login'];
      }
      //print_r($arr);
      if(in_array($holder_name,$arr)) {
        $url = "http://erpsamuiaksorn.com/stat/2013/check/_icheck.php?job_no=&state=open&stage=&holder=".$holder_name."&partner=";
        $bot_check_json = file_get_contents($url);
        $jobs = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $bot_check_json), true );
        if(count($jobs['items']) != 0) {
          $res .= "ทั้งหมด " . count($jobs['items']) . " งาน \n++++++++++\n";
          $res2 = "";
          $i = 0;
          foreach($jobs['items'] as $job) {
            if($i < 10) {
              $res .= "Job No: ". $job['job_no'] ." \n";
              $res .= "ชื่องาน: ". $job['fax'] ." \n";
              $res .= "ลูกค้า: ". $job['p_name'] ." \n";
              $res .= "วันนัดรับงาน: ".$job['date_deadline']." \n";
              $res .= ".\n";
            } else if($i < 20) {
              $res2 .= "Job No: ". $job['job_no'] ." \n";
              $res2 .= "ชื่องาน: ". $job['fax'] ." \n";
              $res2 .= "ลูกค้า: ". $job['p_name'] ." \n";
              $res2 .= "วันนัดรับงาน: ".$job['date_deadline']." \n";
              $res2 .= ".\n";
            }
            // else {
            //   $res2 .= ".\n";
            // }
            $i++;
          }
        } else {
          $res .= $holder_name . " ไม่มีงานค้างในระบบ";
        }

      }

      $arrPostData['messages'][0]['text'] = $res;

      if($res2 != "") {
        $arrPostData['messages'][1]['type'] = "text";
        $arrPostData['messages'][1]['text'] = $res2;
      }

      print_r($arrPostData);


    }

    // $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    // $arrPostData['messages'][0]['type'] = "text";
    // $arrPostData['messages'][0]['text'] = 'ก๊าบบ คุณสามารถสอนให้ฉลาดได้เพียงพิมพ์: สอนเป็ด[คำถาม|คำตอบ]';

  }
}

function get($job_no) {
  $url = "http://erpsamuiaksorn.com/stat/2013/check/_ocheck.php?job_no=" . $job_no;
  $channel = curl_init();
  curl_setopt($channel, CURLOPT_URL,$url);
  curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($channel, CURLOPT_TIMEOUT, 3);
  $result = trim(curl_exec($channel));
  curl_close ($channel);
  return $result;
}


$channel = curl_init();
curl_setopt($channel, CURLOPT_URL,$strUrl);
curl_setopt($channel, CURLOPT_HEADER, false);
curl_setopt($channel, CURLOPT_POST, true);
curl_setopt($channel, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($channel);
curl_close ($channel);

function getMessageContent($arrHeader,$messageId) {
  $channel = curl_init();
  $url = "https://api.line.me/v2/bot/message/".$messageId."/content";
  curl_setopt($channel, CURLOPT_URL,$url);
  curl_setopt($channel, CURLOPT_HEADER, false);
  curl_setopt($channel, CURLOPT_POST, false);
  curl_setopt($channel, CURLOPT_HTTPHEADER, $arrHeader);
  //curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($arrPostData));
  curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
      curl_setopt($channel, CURLOPT_BINARYTRANSFER,1);
  curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($channel, CURLOPT_ENCODING , "");
  $raw = curl_exec($channel);
  //$saveto = "/var/www/candychat.net/line/images/".$messageId.".jpg";
  if(file_exists($saveto)){
        unlink($saveto);
    }
  $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);

  curl_close ($channel);
  //return $result;
}

//getMessageContent($arrHeader,"6604250582008");

// header('Content-Type: image/jpeg;');

// $data = pack('H*',$data);

// $im = imagecreatefromstring($data);

// imagejpeg($im);




echo "OK";
//header("Content-type:application/json");
//echo print_r($arrPostData,1);
