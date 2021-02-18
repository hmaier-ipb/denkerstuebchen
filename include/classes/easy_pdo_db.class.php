<?php


class easy_pdo_db
{
  public array $options;
  public \PDO $pdo;

  function __construct(){
    $this->options = [
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => false
    ];

    $dsn = "mysql:host=localhost;dbname=denkerstuebchen";
    $user = "www-data";
    $pwd = "";

    $this->pdo = new \PDO($dsn, $user, $pwd, $this->options);
  }


  function insert_into($table,$cols_string,$vars){
    $qm = "";
    foreach($vars as $var){$qm .= "?,";}
    $qm = substr($qm, 0, -1);
    $query = "INSERT INTO $table ($cols_string)VALUES($qm)";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->execute($vars);
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
  }

  function does_exist($table,$column,$search_var){
    $query = "SELECT * FROM $table WHERE $column = :var";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->bindValue(":var",$search_var);
      $statement->execute();
      $result = $statement->fetch();
      //error_log(json_encode($result));
      $result !== false ? $response = 1 : $response = 0; // 1=exist, 0=does_not_exist
      //error_log($response);
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $response;
  }

  function get_id($table,$column,$search_var){
    $query = "SELECT id FROM $table WHERE $column = :var";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->bindValue(":var",$search_var);
      $statement->execute();
      $result = $statement->fetch();
      //error_log(json_encode($result));
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $result["id"];
  }

  function get_row_by_id($table,$search_id){
    $query = "SELECT * FROM $table WHERE id = :id";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->bindValue(":id",$search_id);
      $statement->execute();
      $result = $statement->fetch();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $result;
  }

  function get_columns($table,$cols_string){
    $query = "SELECT $cols_string FROM $table";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->execute();
      $result = $statement->fetch();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $result; //$col_value =  $result["colum"]
  }

  function count_rows($table){
    $query = "SELECT COUNT(*) FROM $table";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->execute();
      $result = $statement->fetch();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $result["COUNT(*)"];
  }


}