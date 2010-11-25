<?php
require_once dirname(__FILE__) . '/include/template.php';
class CONFIG{
  public static function db_info(){
    return array(
      'host' => 'localhost',
      'user' => 'softeng17',
      'pass' => 'vtpeer',
      'db'   => 'softeng17'
    );
  }
  public static function basedir(){
    return dirname(__FILE__) . '/';
  }

  public static function incdir(){
    return self::basedir() . '/include';
  }

  public static function inc($file){
    return self::incdir() . '/' . $file;
  }

  public static function fixquotes($array){
    if(get_magic_quotes_gpc()){
      foreach($array as $key => $value){
        if(is_array($value)){
          self::fixquotes($array);
        }else{
          stripslashes($value);
        }
      }
    }
  }

  public static function connect(){
    $i = self::db_info();
    if($x = mysql_connect($i['host'], $i['user'], $i['pass'])){
      mysql_select_db($i['db']);
      return $x;
    }
    return false;
  }
  public static function query($query, $params=false){
    #print_r($query);
    #print_r($params);
    if($params){
      foreach ($params as &$v){    # Escaping parameters
        $v = mysql_real_escape_string($v);
      }
      # str_replace - replacing ? -> %s. %s is ugly in raw sql query
      # vsprintf - replacing all %s to parameters
      $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
      return mysql_query($sql_query);    # Perfoming escaped query
    }
    return mysql_query($query);    # If no params...
  }
  public static function query_one_value($query, $params=false){
    $value_row = mysql_fetch_array(CONFIG::query($query, $params));
    return $value_row[0];
  }

  public static function redirect($url){
    header("Location: $url");
    exit(0);
  }

  public static function session_set($key, $value){
    $_SESSION["$key"] = $value;
  }

  public static function start_session($username, $access_role){
    session_start();
    self::session_set('username', $username);
    self::session_set('access_role', $access_role);
  }

  public static function continue_session($access_level_required='user'){
    session_start();
    if(!self::session_is_set('username')){
      self::redirect('login.php');
    }
    if('user' !== $access_level_required){
      if(self::session_get('access_role') !== 'admin'){
        self::redirect('student_home.php');
      }
    }
  }

  public static function end_session(){
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if(ini_get("session.use_cookies")){
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );
    }

    // Finally, destroy the session.
    session_destroy();
  }

  public static function session_is_set($var){
    return isset($_SESSION[$var]);
  }

  public static function session_get($key){
    return $_SESSION["$key"];
  }

  public static function index($array, $key){
    return $array[$key];
  }
}


  CONFIG::fixquotes($_GET);
  CONFIG::fixquotes($_POST);
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
?>
