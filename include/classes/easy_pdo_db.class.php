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

  function get_row_by_id($table,$search_id){// fetch row by a certain id
    $query = "SELECT * FROM $table WHERE id = :id";
    $num_rows = $this->count_rows($table);
    if ($num_rows !== 0){
      try{
        $statement = $this->pdo->prepare($query);
        //error_log(json_encode($statement));
        $statement->bindValue(":id",$search_id);
        //error_log(json_encode($statement));
        $statement->execute();
        //error_log(json_encode($statement));
        $result = $statement->fetch();
      }catch (\Exception $e){
        error_log(json_encode($e));
      }
      return $result;
    }else{
      return null;
    }
    //error_log(json_encode($result));

  }

  function get_columns($table,$cols_string){// certain cols of a table
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

  function count_rows($table){// count rows of a table
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

  function get_tables(){
      $table = function ($table){return "[$table]";};
      $query = "SHOW TABLES";
      try{
          $statement = $this->pdo->prepare($query);
          $statement->execute();
          $result = $statement->fetchAll(PDO::FETCH_FUNC,$table);
      }catch (\Exception $e){
          error_log(json_encode($e));
      }
      //error_log(json_encode($result));
      return $result;
  }

  function update_row($table,$columns_array,$vars_array,$id){
    $columns = "";
    $num_rows = $this->count_rows($table);
    if($num_rows !== 0){
      foreach($columns_array as $col){$columns .= "$col=?,";}
      $columns = substr($columns, 0, -1);
      $query = "UPDATE $table SET $columns WHERE id=$id";
      try{
        $stmt= $this->pdo->prepare($query);
        $stmt->execute($vars_array);
      }catch (\Exception $e){
        error_log(json_encode($e));
      }
    }
  }

  function delete_row($table,$id){
    $query = "DELETE FROM $table WHERE id=:id";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->bindValue(":id",$id);
      $statement->execute();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
  }

  function where_col_equals_var($table,$col_name,$search_var){// get row where col equals var

    $query = "SELECT * FROM $table WHERE $col_name=:search_var";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->bindValue(":search_var",$search_var);
      $statement->execute();
      $result = $statement->fetchAll();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
    return $result;
  }

  function set_autoincrement($table){
    $rows_num = $this->count_rows($table);
    $x = $rows_num+1;
    $query = "ALTER TABLE $table AUTO_INCREMENT=$x";
    try{
      $statement = $this->pdo->prepare($query);
      $statement->execute();
    }catch (\Exception $e){
      error_log(json_encode($e));
    }
  }


}