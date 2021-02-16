<?php

/**
 * this class provides many operations used on a database
 * mysqli lib is needed for this class
 * activate mysqli in the php.ini by uncommenting under DYNAMIC EXTENSIONS
 * muss auf jeden fall mal überarbeitet werden... it's a complete chaos...
 */
namespace db_denkerstuebchen;
class db
{
  //database login credentials
  public string $host;
  public string $user;
  public string $pwd;
  public string $db_name;


  //calling this function in __construct opens a connection when the class is instantiated
  function connect()
  {
    return mysqli_connect($this->host,$this->user,$this->pwd,$this->db_name);
  }

  //sets autoincrement to number of row plus 1
  //useful when deleting something from a table
  public function set_autoinc($table)
  {
    $query = "SELECT * FROM ".$table;
    $all_data = $this->query_to_db($query);
    $rows_num = mysqli_num_rows($all_data);
    $set_auto_increment_query = "ALTER TABLE ".$table." AUTO_INCREMENT=".$rows_num+=1;
    query_to_db($set_auto_increment_query);
  }

  //used for sending queries to the db
  public function query_to_db($query)
  {
    $query = mysqli_real_escape_string($this->connect(),$query);
    return mysqli_query($this->connect(),$query);

  }

  //used for finding a certain variable in a certain table and column
  //if you want to search through ALL COLUMNS you have to address every column on its own
  public function check_value($var, $table, $column)
  {
    $query = "SELECT * FROM " . $table . " WHERE " . $column . "='" . $var . "'";
    $check = $this->query_to_db($query);
    //error_log(json_encode($check));
    return (mysqli_num_rows($check) == 0) ? 0 : 1; // 0 = not existing, 1 >= existing
  }

  //this function returns the id of a var inside a column in a table

  public function return_id($var,$table,$column)
  {
  $query = "SELECT * FROM ".$table." WHERE ".$column."='".$var."'";
  $result = mysqli_fetch_assoc($this->query_to_db($query));
  return $result["id"];
  }

  public function return_row($var,$table,$column)
  {
    $query = "SELECT * FROM ".$table." WHERE ".$column."='".$var."'";
    $result = mysqli_fetch_row($this->query_to_db($query));
    return $result;
  }

  //insert multiple variables by giving them into arrays
  //respect the sequence in the database
  public function insert_values($columns_array,$vars_array,$table)
  {
    //error_log(json_encode($columns_array));
    //error_log(json_encode($vars_array));
    $columns = "";
    $vars = "";
    foreach($columns_array as $column){
      $columns .= "$column,";
    }
    $columns = substr($columns, 0, -1);
    foreach($vars_array as $var){
      $vars .= "'$var',";
    }
    $vars = substr($vars, 0, -1);

    error_log(json_encode($columns));
    error_log(json_encode($vars));

    $query = "INSERT INTO ".$table."(".$columns.")VALUES(".$vars.")";
    error_log(json_encode($query));
    $this->query_to_db($query);
  }

  public function update_table($var,$table,$column,$id){
    $query = "UPDATE $table SET $column=$var WHERE id=$id ";
    $this->query_to_db($query);
  }

  //delete the last 100 values from a table
  public function delete_table($table){
    $query = "DELETE FROM ". $table ."WHERE id ORDER BY id DESC LIMIT 100";
    $this->query_to_db($query);
    $this->set_autoinc($table);
  }

  //changes a single value
  public function change_entry($newvar,$oldvar,$table,$column)
  {
    $query1 = "SELECT id FROM ".$table."WHERE".$column."=".$oldvar;
    $id_result = $this->query_to_db($query1);
    $oldvar_id = mysqli_fetch_row($id_result);
    $query2 = "UPDATE ".$table." SET ".$column."=".$newvar." WHERE id=".$oldvar_id;
    $this->query_to_db($query2);
  }

  //deleting last row
  public function delete_last_row($table)
  {
    $query = "DELETE FROM ".$table." WHERE id ORDER BY id DESC LIMIT 1";
    $this->query_to_db($query);
  }

  //deletes the last 100 rows from all tables in the current database
  public function clear_all_tables()
  {
    $result = $this->query_to_db("SHOW TABLES");
    $tables = mysqli_fetch_assoc($result);

    foreach ($tables as $table)
    {
      $query = "DELETE FROM ".$table." WHERE id ORDER BY id DESC LIMIT 100";
      $this->query_to_db($query);
    }
  }
  //returns mysqli_result from a table
  public function return_table($table)
  {
    $query = "SELECT * FROM $table";
    $table_result = $this->query_to_db($query);
    return $table_result;
  }

  public function remove_row($id,$table){
    $query = "DELETE FROM ".$table." WHERE id='$id' ";
    $this->query_to_db($query);
  }


}