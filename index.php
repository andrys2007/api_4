<? 
define('URL_API4_TOKEN',"https://services-test.open.ru/anketa_credit/api/partner/v1/auth/token");  
define('URL_API4_SHORT',"https://services-test.open.ru/anketa_credit/api/partner/v1/credit/short/card/application");  
define('URL_API4_FULL',"https://services-test.open.ru/anketa_credit/api/partner/v1/credit/full/cash/application");  


define('PASSWORD',"test_password");  
define('USERNAME',"test_partner");  


$Body=array("grant_type"=>"password",
   			"username"=>"test_partner",
			"password"=>"test_password");

function RestAPI($URL,$Body,$Token)
{
   $headers = array(
	 "Accept: application/json",
	 "Content-Type: application/json" );				   
	 
  if ( !empty($Token) )  $headers[]="Authorization: Bearer ".$Token; 	
	
  $ch = curl_init($URL);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Body) );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $LAST_REZ = json_decode(curl_exec($ch),true);
  if ( curl_errno($ch) ) 
     {
  	   $CODE=curl_getinfo($ch,CURLINFO_HTTP_CODE);
       echo 'ERROR!!! CODE=>'.$CODE."<BR>";
     }
  $CODE=curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  return array("REZ"=>$LAST_REZ,"CODE"=>$CODE);			
}

/* Функция  разворачиваем и показывает дерево-массив. Нужна что бы показать все сообщения об ошибках */   
function ShowAllErrors($name,$NameVar,$Level)
    {
		$BR='<BR>';
        ob_start();
        foreach( $name as $key => $value )
        {
            if ( (gettype($value)=='array' || gettype($value)=='object' || gettype($value)=='Array')  ) ShowAllErrors($value,$key,$Level+1);
            else
            {
                if (  gettype($value)=='object' ) echo  str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$Level)." ".$key." => ".get_class($value).$BR;
                else echo  str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$Level)." ".$key." => ".$value.$BR;
            }
        }
        $Page=ob_get_contents();
        ob_end_clean();

        $space=str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$Level);
        echo $space.'#'.$NameVar.$BR;
        echo $Page;
    }	
	
	
// Получаем токен ...
$R=RestAPI(URL_API4_TOKEN.'?grant_type=password&username='.USERNAME.'&password='.PASSWORD,array(),"");

if ( $R["CODE"]!=200 )  // Ошибка
   {
	    echo "Error...($CODE)<BR>";
	    echo $REZ["error"]."<BR>";
	    echo $REZ["error_description"]."<BR>";
	    die();
	 
	 return ;
   }

$Token=$R["REZ"]["access_token"];				 



$BodyShort=array(
	"meta_info"=> array(
		"utm_campaign"=>"Reklama", 
		"utm_source"=> "Reklama",
		"utm_term"=> "Reklama",
		"utm_content"=> "Reklama",
		"utm_medium"=>"Reklama"
		),
	"personal_info"=> array(
		"first_name"=> "Валентина",
		"last_name"=> "Петрова",
		"middle_name"=> "Михайловна",
		"gender"=> "Ж",
		"mobile_phone"=> "78011423456",
		"is_citizen"=> true,
		"is_accepted"=> true
		),
	"passport_info"=> array(
		"current_passport"=> "4340-716495",
		"current_passport_issuer"=> "Отделом внутренних дел России по г. Химки",
		"current_passport_issuer_code"=> "220-397",
		"date_of_birth"=> "1990-03-11",
		"current_passport_issue_date"=> "2019-08-24",
		"place_of_birth"=> "Москва",
		"SNILS"=> "08040402613",
		"is_passport_changed"=> true,
		"previous_passport"=> "1234-567890",
		"is_full_name_changed"=> true,
		"previous_first_name"=> "Валентина",
		"previous_last_name"=> "Петрова",
		"previous_middle_name"=> "Михайловна",
		"year_of_change_full_name"=> 2015,
		"registration_address"=> array(
			"custom_address"=> array(
				"postal_code"=> "123456",
				"region"=> "Московская область",
				"settlement"=> "Москва",
				"street"=> "Цандера",
				"house"=> "11"
				),
			"have_custom_address"=> true
			),
		"registration_date"=> "1999-06-26",
		"is_addresses_equals"=> false,
		"living_address"=> array(
			"custom_address"=> array(
				"postal_code"=> "123456",
				"region"=> "Московская область",
				"settlement"=> "Москва",
				"street"=> "Цандера",
				"house"=> "11"
				),
			"have_custom_address"=> true
			)
		),
		"additional_info"=> array(
			"amount_income"=> 70000,
			"confirmation_document_type_id"=> 10,
			"pensioner_state_id"=> 10,
			"education_id"=> 10,
			"city_id"=> "7700000000000"
			)
	);
$R=RestAPI(URL_API4_SHORT,$BodyShort,$Token);


if ( $R["CODE"]!=200 )  //400
   {
	 /* Так как сообщения разные и разные структуры то просто аккуратно разворачиваем дерево-массив ...*/   
	 ShowAllErrors($R["REZ"],'ERROR INFO',0);
	 die();
   }
   
echo "OK<BR>";
echo "partner_id=".$R["REZ"]["partner_id"];
