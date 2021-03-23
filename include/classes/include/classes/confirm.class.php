<?php


class confirm extends control_db
{
  function html_res_list(){
    $res_string = "";
    $num_tr = $this->count_num_tr();
    $index = 0;
    $dates = $this->get_occupied_dates();
    for($room_num=1; $room_num<=$num_tr;$room_num++){

      foreach($dates[$room_num-1] as $res){
        if($res[4] == 0 && $res[2] !== 0 && $res[3] !== 0){// if the reservation is not confirmed
          //error_log(json_encode($res));
          $res_string .= "<p> [ROOM-NUM: $room_num] <span class='user_id_db'>[USER ID: $res[1]]</span> $res[2] - $res[3]</p>";
        }

      }
    }

    return $res_string;
  }


}
