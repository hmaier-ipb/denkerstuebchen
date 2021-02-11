
function $(id){return document.getElementById(id);}
function $$ (class_name) {return document.getElementsByClassName(class_name);}

var params;
var ajaxObj;
var action;
var url;
var output;


var send_btn;
var firstname;
var surname;
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


function init(){
  send_btn = $$("send_btn")[0];
  firstname = $("name-input");
  surname = $("surname-input");
  phone = $("phone-input");
  email = $("email-input");
  output = $("output");
  calender = $("calender");
  output2 = $("output2");
  action = "get-lang";
  send_info("action="+action);
  initEventListeners();
}

function validate(input,pattern){
  let reg = new RegExp(pattern);
  return reg.test(input.value);
}


function initEventListeners(){

  send_btn.addEventListener("click", function (){
    action = "form-data";
    department = $("department-input").value;
    radio_buttons = document.getElementsByName("status");
    //console.log(radio_buttons);
    for(let i = 0;i<radio_buttons.length;i++){if(radio_buttons[i].checked){status_input = radio_buttons[i].value;}}
    params =
      "action="+action+
      "&name="+firstname.value+
      "&surname="+surname.value+
      "&phone="+phone.value+
      "&email="+email.value+
      "&department="+department+
      "&status="+status_input+
      "&start_date="+start_date+
      "&end_date="+end_date;

    //****************************
    //SENDING OUT THE RESERVATION
    //****************************
    send_info(params);
    console.log("form data send to php");
    })


  //checking the input of the firstname on keyup
  $("name-input").addEventListener("keyup",function (){
    //name
    if(validate(firstname,/^[A-Za-z]{2,}$/)){
      $("img1").style.display = "flex";
      }else{
      $("img1").style.display = "none";
    }
  })

  //checking the input of the surname on keyup
  $("surname-input").addEventListener("keyup",function () {
    //name
    if(validate(surname,/^[A-Za-z]{3,}$/)){
      $("img2").style.display = "flex";
    }else{
      $("img2").style.display = "none";
    }
  })

 //checking the input of the phone number
  $("phone-input").addEventListener("keyup", function(){
    //phone
    if(validate(phone,/^\d{4,}$/g)){
      $("img3").style.display = "flex";
    }else{
      $("img3").style.display = "none";
    }
  })

  //checking the email input on keyup
  $("email-input").addEventListener("keyup",function (){
    //email
    if(validate(email,/(\w+|\w+.\w+){3,}@(ipb-halle.de)/)){
      $("img4").style.display = "flex";
    }else{
      $("img4").style.display = "none";
    }
  })

  //set the cursor at position 0 when focusing the email input
  $("email-input").addEventListener("focus", function (c){
    var input = $("email-input");
    if (input.setSelectionRange){
      input.focus();
      input.setSelectionRange(0,0);
    }
  })

  $("select_room").addEventListener("click", e => {
    selected_room = $("rooms").value;
    action = "room_select";
    params ="action="+action+"&room="+selected_room;
    send_info(params);
  })

  $("prev_month").addEventListener("click",e => {
    action = "prev_month";
    params = "action="+action;
    send_info(params);
  })

  $("current_month").addEventListener("click",e => {
    action = "current_month";
    params = "action="+action;
    send_info(params);
  })

  $("next_month").addEventListener("click",e => {
    action = "next_month";
    params = "action="+action;
    send_info(params);
  })
  td_listener();
}

function td_listener(){ // EVENT LISTENER FOR SINGLE TABLE CELLS
  for(var i = 0;i<$$("current_month").length;i++){ //current_month -> table cells this month
    $$("current_month")[i].addEventListener("click", set_dates)
  }
}

function set_dates(e){

  if(typeof start_date !== typeof undefined && typeof end_date !== typeof undefined){ //when both vars are defined
    start_date = undefined;
    end_date = undefined;
  }

  if(typeof start_date === typeof undefined){
    start_date = e.target.id;
    switch (lang){
      case "de":
        $("start_date").innerHTML = "Startdatum: ";
        break;
      default:
        $("start_date").innerHTML = "Start Date: ";
    }

    $("date_error").innerHTML = "";
    $("start_date").innerHTML += start_date;
    $("end_date").innerHTML = "";
  }else{
    end_date = e.target.id;
    switch (lang){
      case "de":
        $("end_date").innerHTML = "Enddatum: ";
        break;
      default:
        $("end_date").innerHTML = "End Date: ";
    }
    $("end_date").innerHTML += end_date;
    action = "submit_dates";
    params = "action="+action+"&start_date="+start_date+"&end_date="+end_date;
    send_info(params);
  }
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
    console.log(ajaxObj.responseText);
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

          console.log("form-data received from php")
          action = "";
          $("output").innerText = json_response;
          output.style.color = "#277e34";
          img = $$("img");
          for(let i = 0;i<img.length;i++){img[i].style.display = "none";} // disappearing green checks
          output.style.display = "block";
          //resetting form values
          firstname.value = null;
          surname.value = null;
          phone.value = null;
          email.value = "@ipb-halle.de";
          //resetting
          start_date = "";
          end_date = "";
          $("start_date").innerHTML = "";
          $("end_date").innerHTML = "";


          break;
        case "get-lang":
          lang = json_response;
          //console.log(lang);
          break;
        case "room_select":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          break;
        case "prev_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          td_listener();
          break;
        case "current_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          td_listener();
          break;
        case "next_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          td_listener();
          break;
        case "submit_dates":
          action = "";

          if(typeof json_response !== typeof null){
            $("start_date").innerHTML = "";
            $("end_date").innerHTML = "";
            $("date_error").innerHTML = json_response;
          }
          break;
        default:
          output.innerHTML = "INVALID ACTION";
          break;
      }

    }

  }
}