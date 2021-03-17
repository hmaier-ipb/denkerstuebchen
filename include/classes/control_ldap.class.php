<?php

define("SERVER", "ipb-ad1.ipb-halle.de");
define("PORT", 389);
define("USER", "ldapreader@ipb-halle.de");
define("PWD", "password");
define("ORG", "dc=ipb-halle, dc=de");

class control_ldap
{
  /**
   * @var false|resource
   */
  protected $con;

  function connect(){
    $this->con = ldap_connect(SERVER,PORT)//connecting to ldap server
    or die(error_log("ldap connection error"));
    //error_log("connect");
    ldap_set_option($this->con, LDAP_OPT_PROTOCOL_VERSION, 3);
    return ldap_bind($this->con,USER,PWD);//logging in into ldap server
  }

  function get_ad_data($user_id){//querys to "user" and "nwc", to merge them into one array
    $suggestions = [];
    $attributes = ["name","uid","telephonenumber","mail","department"];
    if($this->connect()){
      $response_user = $this->user_search($user_id,"user",$attributes);
      $suggestions[] = $this->process_response($response_user);
      $response_nwc = $this->user_search($user_id,"nwc",$attributes);
      $nwc_array = $this->process_response($response_nwc);
      foreach ($nwc_array as $item){//adding the nwc response to suggestions
        $suggestions[0][] = $item;
      }
      return $suggestions[0];
    }else{
      return ["connection_failed"];
    }

    //error_log(json_encode($suggestions));;
  }

  function user_search($user_id,$node,$attributes){
    $base_dsn = "OU=".$node.",".ORG;//cn=node,dc=ipb-halle, dc=de
    $filter = "(uid=$user_id*)";
    $search_users = ldap_search($this->con,$base_dsn,$filter,$attributes);//asterisks for getting all occurrences
    $response = ldap_get_entries($this->con,$search_users);
    //error_log(json_encode($response));
    return $response; // all attributes of a person
  }

  function process_response($response){
    $length = $response["count"];
    $response_array = [];
    if($length){
    for($i=0;$i<$length-1;$i++){
      $item = $response[$i];
      if(isset($item["telephonenumber"][0])&&isset($item["department"][0])&&isset($item["mail"][0])){
        $name = $item["name"][0];
        $uid = $item["uid"][0];
        $phone = $item["telephonenumber"][0];
        $email = $item["mail"][0];
        $department = $item["department"][0];
        $response_array[] = [$name,$uid,$phone,$email,$department];
        }
      }
    }
    return $response_array;
  }

  function suggestion_html($response){
    $suggestion_string = "";
    foreach ($response as $index){
      $suggestion_string .= "<option id='$index[1]' value='$index[1]'>";
      $suggestion_string .= "</option>";
    }
    return $suggestion_string;
  }

}