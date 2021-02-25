
function $(id){return document.getElementById(id);}
function $$ (class_name) {return document.getElementsByClassName(class_name);}

var params;
var ajaxObj;
var action;
var url;
var output;
var json_response;


var send_btn;
var full_name;
var phone;
var email;
var department;
var status_input;
var radio_buttons;
var lang;
var img;
var selected_room;
var calender;
var start_date;
var end_date;
var room_selection;
var room_number;
var room_month_year;
var user_search;
var uid;
var user_data;

function init(){
  send_btn = $$("send_btn");
  full_name = $("name-input");
  phone = $("phone-input");
  email = $("email-input");
  department = $("department-input");
  output = $("output");
  calender = $("calender");
  start_date = $("start_date");
  end_date = $("end_date");
  // console.log(start_date.value);
  // console.log(end_date.value);
  room_selection = $("room_selection");
  room_month_year = $("room_month_year");
  user_search = $("user_search");
  // action = "get-lang";
  // send_info("action="+action);
  initEventListeners();
}

function validate(input,pattern){
  let reg = new RegExp(pattern);
  return reg.test(input.value);
}


function initEventListeners(){

  user_search.addEventListener("input", e =>{
    action = "user_search";
    uid = user_search.value;
    params = "action="+action+"&uid="+uid;
    send_info(params);
    //console.log(uid);
    //console.log(params);
    })
  // user_search.addEventListener("keyup", e =>{
  //   if(e.key === "Enter") {
  //
  //   }
  // })


  send_btn[0].addEventListener("click", function (){
    action = "form-data";
    department = $("department-input").value;
    room_number = room_selection.options[room_selection.selectedIndex].value;
    radio_buttons = document.getElementsByName("status");
    for(let i = 0;i<radio_buttons.length;i++){if(radio_buttons[i].checked){status_input = radio_buttons[i].value;}}

    params =
      "action="+action+
      "&full_name="+name.value+
      //"&surname="+surname.value+
      "&phone="+phone.value+
      "&email="+email.value+
      "&department="+department+
      "&status="+status_input+
      "&start_date="+start_date.value+
      "&end_date="+end_date.value+
      "&room_number="+room_number;

    //****************************
    //SENDING OUT THE RESERVATION
    //****************************

    send_info(params);
    console.log("form data send to php");
    console.log(params);
    })


  td_listener();
  room_selection_listener();
  month_buttons_listener();


  //checking the input of the firstname on keyup
  // $("name-input").addEventListener("keyup",function (){
  //   //name
  //   if(validate(firstname,/^[A-Za-z]{2,}$/)){
  //     $("img1").style.display = "flex";
  //     }else{
  //     $("img1").style.display = "none";
  //   }
  // })

  //checking the input of the surname on keyup
  // $("surname-input").addEventListener("keyup",function () {
  //   //name
  //   if(validate(surname,/^[A-Za-z]{2,}$/)){
  //     $("img2").style.display = "flex";
  //   }else{
  //     $("img2").style.display = "none";
  //   }
  // })

 //checking the input of the phone number
 //  $("phone-input").addEventListener("keyup", function(){
 //    //phone
 //    if(validate(phone,/^\d{4,}$/)){
 //      $("img3").style.display = "flex";
 //    }else{
 //      $("img3").style.display = "none";
 //    }
 //  })

  //checking the email input on keyup
  // $("email-input").addEventListener("keyup",function (){
  //   //email
  //   if(validate(email,/(\w+|\w+.\w+){3,}@(ipb-halle.de)/)){
  //     $("img4").style.display = "flex";
  //   }else{
  //     $("img4").style.display = "none";
  //   }
  // })

}

function td_listener(){ // EVENT LISTENER FOR SINGLE TABLE CELLS
  for(var i = 0;i<$$("current_month").length;i++){ //current_month -> table cells this month
    $$("current_month")[i].addEventListener("click", set_dates)
  }
}
function room_selection_listener(){
  $("room_selection").addEventListener("click", e => {
    selected_room = $("room_selection").value;
    action = "room_select";
    params ="action="+action+"&room="+selected_room;
    send_info(params);
  })
}
function month_buttons_listener(){
  $("prev_month").addEventListener("click",e => {
    action = "prev_month";
    params = "action="+action;
    send_info(params);
    //console.log("prev_month pressed")
  })

  $("next_month").addEventListener("click",e => {
    action = "next_month";
    params = "action="+action;
    send_info(params);
    //console.log("next_month pressed")
  })

 /* $("current_month").addEventListener("click",e => {
    action = "current_month";
    params = "action="+action;
    send_info(params);
    //console.log("current_month pressed")
  })*/

  room_selection_listener();
}
function load_calender_listeners(){
  td_listener();
}


function set_dates(e){
  // console.log(start_date);
  // console.log(end_date);
  if( start_date.value !== "" &&  end_date.value !== ""){ //when both vars are defined
    start_date.value = "";
    end_date.value = "";
  }
  if(start_date.value === ""){
    start_date.value = e.target.id;
  }else{
    end_date.value = e.target.id;
  }
  action = "submit_dates";
  params = "action="+action+"&start_date="+start_date.value+"&end_date="+end_date.value;
  send_info(params);//is start_date > end_date?
}

function reset_input_values(){
  img = $$("img");
  for(let i = 0;i<img.length;i++){img[i].style.display = "none";} // disappearing green checks
  firstname.value = null;
  surname.value = null;
  phone.value = null;
  email.value = "@ipb-halle.de";
  //resetting
  start_date = "";
  end_date = "";
  $("start_date").innerHTML = "";
  $("end_date").innerHTML = "";
}



function getAjaxObject() {
  //creating an ajax object
  if (window.ActiveXObject)
    return new ActiveXObject("Microsoft.XMLHTTP");
  else if (window.XMLHttpRequest)
    return new XMLHttpRequest();
  else {
    return null;
  }
}
function send_info(parameters) {
  //sending parameters(data) to url(index.php)
  ajaxObj = getAjaxObject();
  if (ajaxObj !== null) {
    ajaxObj.open("POST", "index.php", true);
    ajaxObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxObj.onreadystatechange = setOutput;
    ajaxObj.send(parameters);
  }
  else {
    console.log("Kein Ajax Objekt.");
    return false;
  }
}

function setOutput() {
  //receiving output form php as json
  if (ajaxObj.readyState === 4) {
    //console.log(ajaxObj.responseText);
    if (ajaxObj.status === 200) {
      try {
        json_response = JSON.parse(ajaxObj.responseText);
      } catch (e) {
        console.log("Ungültige Daten. Kein JSON String!");
        console.log(ajaxObj.responseText);
        return false;
      }
      switch (action) {
        case "form-data":
          //console.log("form-data received from php")
          console.log(json_response);
          action = "";
          if(json_response[1] === false){// validation found errors
            output.style.color = "#DF171E";
            $("output").innerHTML = json_response[0];
          }else{                        // validation found no errors
            reset_input_values();
            output.style.color = "#277e34";
            $("output").innerHTML = json_response[0];
            calender.innerHTML = json_response[2];
            load_calender_listeners();
            room_selection_listener();
          }

          output.style.display = "block";
          break;
        case "get-lang":
          //lang = json_response;
          //console.log(lang);
          break;
        case "room_select":
          action = "";
          room_month_year.innerHTML = json_response[0]
          calender.innerHTML = json_response[1]; // the DIV surrounding the calender
          load_calender_listeners();
          room_selection_listener();
          break;
        case "prev_month":
          action = "";
          room_month_year.innerHTML = json_response[0];
          calender.innerHTML = json_response[1]; // the DIV surrounding the calender
          load_calender_listeners();
          break;
       /* case "current_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          load_calender_listeners();
          break;*/
        case "next_month":
          action = "";
          room_month_year.innerHTML = json_response[0];
          calender.innerHTML = json_response[1]; // the DIV surrounding the calender
          load_calender_listeners();
          break;
        case "submit_dates":
          action = "";
          //calender.innerHTML = json_response[0];
          if(typeof json_response[0] !== typeof null){
            start_date.value = "";
            end_date.value = "";
          }
          //load_calender_listeners();
          break;
        case "user_search":
          //$("suggestions").innerHTML = json_response[0]
          user_data = json_response;
          console.log(json_response)
          // full_name.value = user_data[0];
          // phone.value = user_data[2];
          // email.value = user_data[3];
          // department.value = user_data[4];

            full_name.value = user_data[0];
            phone.value = user_data[2];
            email.value = user_data[3];
            department.value = user_data[4];

            // full_name.value = "";
            // phone.value =  "";
            // email.value =  "@ipb-halle.de";
            // department.value =  "";

          break;
        default:
          output.innerHTML = "INVALID ACTION";
          break;
      }

    }

  }
}