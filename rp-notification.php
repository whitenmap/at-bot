<?php
  require_once('twitteroauth-master/autoload.php');
  require_once('key.php');
  use Abraham\TwitterOAuth\TwitterOAuth;

  $connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

  $json = file_get_contents("https://kenkoooo.com/atcoder/resources/sums.json");
  $array = json_decode($json, true);

  define("SUM_FILE","sum.dat");
  define("LAST_RP_FILE","last-rp.dat");
  define("DATE_FILE","last-date.dat");

  //逃げるな対象のユーザ(Screen Name)リスト
  $key_arr = [
    "Twitter ID 1" => "AtCoder ID 1",
    "Twitter ID 2" => "AtCoder ID 2"
  ];

  if(!file_exists("users")) {
    mkdir("users", 0755, true);
  }

  foreach ($array as $a) {
    if($key = array_search($a["user_id"], $key_arr)) {
      $imaSumRp = 0.0;
      $maeSumRp = 0.0;
      $imaRp = 0.0;
      $maeRp = 0.0;
      $nigeruna = "";

      if(!file_exists("users/".$a["user_id"])) {
        mkdir("users/".$a["user_id"], 0755, true);
      }

      if(file_exists("users/".$a["user_id"]."/".LAST_RP_FILE))  {
        $imaSumRp = $a["point_sum"];
        $maeSumRp = load_file("users/".$a["user_id"]."/".SUM_FILE);
        $imaRp = $imaSumRp - $maeSumRp;
        $maeRp = load_file("users/".$a["user_id"]."/".LAST_RP_FILE);

        save_file("users/".$a["user_id"]."/".SUM_FILE, $imaSumRp);
        save_file("users/".$a["user_id"]."/".LAST_RP_FILE, $imaRp);

        if($imaRp == 0) {
          $nigeruna = "最後にRated Pointが変動した日: ".load_file("users/".$a["user_id"]."/".DATE_FILE)."\nAtCoderから逃げるな。";
        } else {
          save_file("users/".$a["user_id"]."/".DATE_FILE, date('Y/m/d', strtotime('-1 day')));
        }
      } else if(file_exists("users/".$a["user_id"]."/".SUM_FILE)) {
        $imaSumRp = $a["point_sum"];
        $maeSumRp = load_file("users/".$a["user_id"]."/".SUM_FILE);
        $imaRp = $imaSumRp - $maeSumRp;
        $maeRp = "記録なし";

        save_file("users/".$a["user_id"]."/".SUM_FILE, $imaSumRp);
        save_file("users/".$a["user_id"]."/".LAST_RP_FILE, $imaRp);

        if($imaRp == 0) {
          $nigeruna = "最後にRated Pointが変動した日: ".load_file("users/".$a["user_id"]."/".DATE_FILE)."\nAtCoderから逃げるな。";
        } else {
          save_file("users/".$a["user_id"]."/".DATE_FILE, date('Y/m/d', strtotime('-1 day')));
        }
      } else {
        $imaSumRp = $a["point_sum"];
        $imaRp = "記録なし";
        $maeRp = "記録なし";

        save_file("users/".$a["user_id"]."/".SUM_FILE, $imaSumRp);
      }

      $statues = $connection->post("statuses/update", ["status" => "@".$key."\n本日のRated Point: ".$imaRp."\n前日のRated Point: ".$maeRp."\nこれまでの合計Rated Point: ".$imaSumRp."\n".$nigeruna]);
    }
   }

  //ファイル読み込み
  function load_file($filename) {
    $fp = fopen($filename,"r");
    $buf = fgets($fp);
    fclose($fp);
    return $buf;
  }

  //ファイル書き込み
  function save_file($filename,$buf) {
    $fp = fopen($filename,"w");
    fwrite($fp,$buf);
    fclose($fp);
  }

?>