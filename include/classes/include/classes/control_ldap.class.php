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

  function auth_user($user,$pwd){
    $this->con = ldap_connect(SERVER,PORT);
    return ldap_bind($this->con,$user,$pwd);
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

  function get_all_users(){
    return [
      [
        "Neumann, Steffen",
        "sneumann",
        "+49 345 5582 1470",
        "sneumann@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Peters, Kristian",
        "kpeters",
        "+49 345 5582 1473",
        "Kristian.Peters@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Duchrow, Oliver",
        "oduchrow",
        "+49 345 5582 1472",
        "Oliver.Duchrow@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Chimmiri, Anusha",
        "achimmir",
        "+49 345 5582 1472",
        "Anusha.Chimmiri@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Meier, Rene",
        "rmeier",
        "+49 345 5582 1473",
        "Rene.Meier@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Scharfenberg, Sarah",
        "sscharfe",
        "+49 345 5582 1474",
        "Sarah.Scharfenberg@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Marr, Susanne",
        "smarr",
        "+49 345 5582 1472",
        "Susanne.Marr@ipb-halle.de",
        "BASDA,BASDA_EN"
      ],
      [
        "Burwig, Alexandra",
        "burwig",
        "+49 345 5582 0",
        "Alexandra.Burwig@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Langlhofer, Maike",
        "mlanglho",
        "+49 345 5582 1622",
        "Maike.Langlhofer@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Plato, Philipp",
        "pplato",
        "+49 345 5582 1654",
        "Philipp.Plato@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Voigt, Sabine",
        "svoigt",
        "+49 345 5582 1654",
        "Sabine.Voigt@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Warkus, Eberhard",
        "ewarkus",
        "+49 345 5582 1632",
        "Eberhard.Warkus@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Bartz, Holger",
        "hbartz",
        "+49 345 5582 1642",
        "Holger.Bartz@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Scheller, Ronald",
        "rschelle",
        "+49 345 5582 1641",
        "Ronald.Scheller@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Kolodziej, Kilian",
        "kkolodzi",
        "+49 345 5582 1653",
        "Kilian.Kolodziej@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Timpel, Catrin",
        "ctimpel",
        "+49 345 5582 1636",
        "Catrin.Timpel@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Nin Brauer, Martin Claudio",
        "mbrauer",
        "+49 345 5582 1618",
        "MartinClaudio.NinBrauer@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Walter, Andrea",
        "awalter",
        "+49 345 5582 1615",
        "Andrea.Walter@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "K\\u00f6rner, Tino",
        "tkoerner",
        "+49 345 5582 1640",
        "Tino.Koerner@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Rasch, Melanie",
        "mrasch",
        "+49 345 5582 1617",
        "Melanie.Rasch@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Maier, Hendrik",
        "hmaier",
        "+49 345 5582 1643",
        "Hendrik.Maier@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Gierlich, Angela",
        "agierlich",
        "+49 345 5582 1112",
        "Angela.Gierlich@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Klaus, Alina",
        "aklaus",
        "+49 345 5582 1626",
        "Alina.Klaus@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Stolzenbach, Caroline",
        "cstolzen",
        "+49 345 5582 1601",
        "Caroline.Stolzenbach@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Kraege, Michael",
        "mkraege",
        "+49 345 5582 1631",
        "Michael.Kraege@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Koth, Carsten",
        "ckoth",
        "+49 345 5582 1632",
        "Carsten.Koth@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Piskol, Andrea",
        "apiskol",
        "+49 345 5582 1621",
        "Andrea.Piskol@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Jansen, Petra",
        "pjansen",
        "+49 345 5582 1654",
        "Petra.Jansen@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Z\\u00f6hler, Leon",
        "lzoehler",
        "+49 345 5582 1607",
        "Leon.Zoehler@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Noack, Frank",
        "fnoack",
        "+49 345 5582 1653",
        "Frank.Noack@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Lechowski, Franz",
        "flechows",
        "+49 345 5582 1610",
        "Franz.Lechowski@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Pieplow, Sylvia",
        "spieplow",
        "+49 345 5582 1110",
        "Sylvia.Pieplow@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "G\\u00fcnther, Vanessa",
        "vguenthe",
        "+49 345 5582 1653",
        "Vanessa.Guenther@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Stra\\u00dfburg, Sabrina",
        "sstrassb",
        "+49 345 5582 1653",
        "Sabrina.Strassburg@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Abe, Tobias",
        "tabe",
        "+49 345 5582 1644",
        "Tobias.Abe@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Wolf, Barbara",
        "bwolf",
        "+49 345 5582 1614",
        "Barbara.Wolf@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Hellmuth, Antje",
        "ahellmut",
        "+49 345 5582 1113",
        "Antje.Hellmuth@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Haferung, Claudia",
        "chaferun",
        "+49 345 5582 1612",
        "Claudia.Haferung@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "\\u00d6lke, Felix",
        "foelke",
        "+49 345 5582 1632",
        "Felix.Oelke@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Mueller, Christian",
        "cmueller",
        "+49 345 5582 1653",
        "Christian.Mueller@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Balkenhohl, Kerstin",
        "balkenhohl",
        "+49 345 5582 1610",
        "Kerstin.Balkenhohl@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Boehm, Heike",
        "hboehm",
        "+49 345 5582 1630",
        "Heike.Boehm@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Schade, Janine",
        "jschade",
        "+49 345 5582 1611",
        "Janine.Schade@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Pareis, Tanja",
        "tpareis",
        "+49 345 5582 1616",
        "Tanja.Pareis@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Zedler, Sophia",
        "szedler",
        "+49 345 5582 1610",
        "Sophia.Zedler@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Scherze, Christopher",
        "cscherze",
        "+49 345 5582 1653",
        "Christopher.Scherze@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Zuber, Peter",
        "pzuber",
        "+49 345 5582 1600",
        "Peter.Zuber@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Schinke, Clemens",
        "cschinke",
        "+49 345 5582 1613",
        "Clemens.Schinke@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Schubert, Ines",
        "ischubert",
        "+49 345 5582 1634",
        "Ines.Schubert@ipb-halle.de",
        "AdmIN,AdmiN_EN"
      ],
      [
        "Grimpe, Christoph",
        "cgrimpe",
        "+49 345 5582 1631",
        "Christoph.Grimpe@ipb-halle.de",
        "AdmIN,AdmIN_EN"
      ],
      [
        "Sandmann, Alexander",
        "asandman",
        "+4934555821701",
        "Alexander.Sandmann@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Voiniciuc, Catalin",
        "cvoinici",
        "+49 345 5582 1720",
        "Catalin.Voiniciuc@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Yang, Bo",
        "byang",
        "+49 345 5582 1483",
        "Bo.Yang@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "M\\u00fcnch, Judith",
        "jmuench",
        "+49 345 5582 1702",
        "Judith.Muench@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Lee, Soo Yun",
        "slee",
        "+49 345 5582 1721",
        "SooYun.Lee@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Hillmer, Lennart",
        "lhillmer",
        "+49 345 5582 1701",
        "Lennart.Hillmer@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Wolf, Yannick",
        "ywolf",
        "+49 345 5582 1702",
        "Yannick.Wolf@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "K\\u00f6rner, Saskia",
        "skoerner",
        "+49 345 5582 1366",
        "Saskia.Koerner@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Puellmann, Pascal",
        "ppuellma",
        "+49 345 5582 1701",
        "Pascal.Puellmann@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Robert, Madalen",
        "mrobert",
        "+49 345 5582 1703",
        "Madalen.Robert@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Klassen, Robert",
        "rklassen",
        "+49 345 5582 1702",
        "Robert.Klassen@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Weissenborn, Martin",
        "mweissen",
        "+49 345 5582 1700",
        "Martin.Weissenborn@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Clau\\u00df, Stefanie",
        "sclauss",
        "+49 345 5582 1703",
        "Stefanie.Clauss@ipb-halle.de",
        "IRG,IRG_EN"
      ],
      [
        "Calderon Villalobos, Luz Irina",
        "lcaldero",
        "+49 345 5582 1232",
        "LuzIrina.CalderonVillalobos@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Figueroa Parra, Jhonny Oscar",
        "jfiguero",
        "+49 345 5582 1212",
        "JhonnyOscar.FigueroaParra@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Gago Zachert, Selma Persida",
        "sgago",
        "+49 345 5582 1220",
        "SelmaPersida.GagoZachert@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "R\\u00e4de, Antonia",
        "araede",
        "+49 345 5582 1231",
        "Antonia.Raede@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Herrmann, Alexandra",
        "aherrman",
        "+49 345 5582 1201",
        "Alexandra.Herrmann@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Kaschig, Katja",
        "kkaschig",
        "+49 345 5582 1224",
        "Katja.Kaschig@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Heidecker, Michel",
        "mheideck",
        "+49 345 5582 1224",
        "Michel.Heidecker@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Meena, Mukesh Kumar",
        "mmeena",
        "+49 345 5582 1222",
        "MukeshKumar.Meena@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Quegwer, Jakob",
        "jquegwer",
        "+49 345 5582 1241",
        "Jakob.Quegwer@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Stamm, Gina",
        "gstamm",
        "+49 345 5582 1231",
        "Gina.Stamm@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Schenke, Andreas",
        "aschenke",
        "+49 345 5582 1222",
        "Andreas.Schenke@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Bade, Patrizia Josefine",
        "pbade",
        "+49 345 5582 1212",
        "PatriziaJosefine.Bade@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "B\\u00fcrstenbinder, Katharina",
        "kbuerste",
        "+49 345 5582 1226",
        "Katharina.Buerstenbinder@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Zimmer, Marlene",
        "mzimmer",
        "+49 345 5582 1221",
        "Marlene.Zimmer@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Ziegler, Joerg",
        "jziegler",
        "+49 345 5582 1225",
        "Joerg.Ziegler@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Wagner, Tobias",
        "twagner",
        "+49 345 5582 1211",
        "Tobias.Wagner@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Gasperini, Debora",
        "dgasperi",
        "+49 345 5582 1230",
        "Debora.Gasperini@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Engelmann, Susanne",
        "shoepfne",
        "+49 345 5582 1224",
        "Susanne.Engelmann@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Naumann, Christin",
        "cnaumann",
        "+49 345 5582 1220",
        "Christin.Naumann@ipb-halle.de",
        "MSV,MSV_EN"
      ],
      [
        "Ma, Yunjing",
        "yma",
        "+49 345 5582 1221",
        "Yunjing.Ma@ipb-halle.de",
        "MSV,MSV_EN"
      ]
    ];
  }

}