<?php

class User {
  function LoginForm($type="html") {
    // Note: this has to be changed
    // but it's good enough for alpha stage
    switch ($type) {
      case "html":
        $result  ="<form action='/login' method='post'>";
        $result .="<input name=email type=email>";
        $result .="<input name=password type=password>";
        $result .="<input type=submit></form>";
        return $result;
        break;
    }
  }
  function ProcessLogin(){
    global $pdo;
    if (isset($_COOKIE['ItPhilManagerSession'])) {
      echo "checking session for ".$_COOKIE['ItPhilManagerSession']."<br>";
      $sth = $pdo->prepare("SELECT user.user_id as user_id from user
                            JOIN link_session2user
                            JOIN session
                            WHERE session_hash = :session_hash");
      $sth->execute(array(":session_hash" => $_COOKIE['ItPhilManagerSession']));
      $user_id = $sth->fetchColumn();
      if ($user_id) {
        $_SESSION['user']=array();
        $_SESSION['user']['id']=$user_id;
        $sth = $pdo->prepare("SELECT capability_name FROM capability WHERE user_id = :user_id");
        $sth->execute(array(":user_id"=> $user_id));
        echo "ERRIR (udi $user_id <pre>" . var_export( $sth->errorInfo() , true ) . "</pre>";
        //$capabilities = $sth->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP); //not quite the desired result
        $capabilities = array();
        while ($capability=$sth->fetchColumn())$capabilities[]=$capability;
        $_SESSION['user']['capabilities'] = $capabilities;
        return;
      } else {
        setcookie(ItPhilManagerSession, "" , 1); //unsetting cookie
      }
      
    }

    $hasher = new PasswordHash();
    $sth = $pdo->prepare("SELECT user_pbkdf2, user.user_id as user_id
                   FROM user
                   JOIN link_email2user 
                   JOIN email 
                   WHERE email_address = :email");
     $sth->execute(array(":email" => $_POST['email']));
     $loginData = $sth->fetch();

     $dbHash = $loginData['user_pbkdf2'];
     $user_id = $loginData['user_id'];
//DEBUG
echo "<pre>" . var_export($loginData,true) . "</pre>";
//DEBUG     
     $validPassword = $hasher->validate_password($_POST['password'], $dbHash);
     if ($validPassword) {
       echo "password valid, creating session";
       $data = array();
       $data[":session_hash"]=sha1(mcrypt_create_iv(16));
       if (strstr($_SERVER['REMOTE_ADDR'],":")) {
         // Remote address is IPv6 or IPv4 in IPv6 notation
         $data[":session_ip_start"] =inet_pton($_SERVER['REMOTE_ADDR']);
       } else {
	// Remote address is IPv4 in IPv4 notation
	// Convert to IPv6 notation
	$data[":session_ip_start"] = inet_pton("::ffff:".$_SERVER['REMOTE_ADDR']);
       }
       $data[":session_useragent"] = $_SERVER['HTTP_USER_AGENT'];
       $sth = $pdo->prepare("INSERT INTO session (session_hash,session_ip_start,session_useragent) values (:session_hash,:session_ip_start,:session_useragent)");
       //$sth->execute($data);
       if(!$sth->execute($data)) {
         //todo: error handling
       } 
       setcookie("ItPhilManagerSession", $data[":session_hash"] , PHP_INT_MAX); // set cookie
       $data = array();
       $data[':session_id'] = $pdo->lastInsertId();
       $data[':user_id'] =  $user_id;
       $sth = $pdo->prepare("INSERT INTO link_session2user (session_id,user_id) Values (:session_id,:user_id)");
       if(!$sth->execute($data)) {
         //todo: error handling
       }
       
       
       
     } else {
      echo "Invalid PasswordQ";
     }
  }

}
?>
