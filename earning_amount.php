<?php
function readMail() {
    $dns = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
    //$email = "anuj@jasapp.com";
    //$password = "9785914691";
	$email = 'bnbstories@gmail.com';
	$password = 'hostbetter';

    $openmail = imap_open($dns,$email,$password ) or die("Cannot Connect ".imap_last_error());
    if ($openmail) {

        for($i=1; $i <= imap_num_msg($openmail); $i++) {

            $header = imap_header($openmail,$i);
			echo $subject = trim($header->Subject);
			    if($subject=="=?UTF-8?Q?Fwd:_Versement_de_731=E2=82=AC_envoy=C3=A9?=")
				{
					$subject = explode("_",$subject);
					
					if(in_array("Versement", $subject)){
					 	$msgBody 						= imap_fetchbody ($openmail, $i, 2);
						$msgBodycontaining_text		 	= strafter($msgBody,'Montant');
						$msgBodycontaining_text			= strbefore($msgBodycontaining_text,'=E2=82=AC')."<br />";
						$output[$i]['earnAmount'] 		= trim(strafter($msgBodycontaining_text,'Montmartre'));
						$msgBodycontaining_text			= quoted_printable_decode ($msgBodycontaining_text);
						echo $msgBodycontaining_text;
						$msgBodycontaining_text			= explode("-",$msgBodycontaining_text);
						print_r($msgBodycontaining_text);
						
					}
				}
        }
 /*
        $msg = imap_fetchbody($openmail,1,"","FT_PEEK");

       
        $msgBody = imap_fetchbody ($openmail, $i, "2.1");
        if ($msgBody == "") {
           $portNo = "2.1";
           $msgBody = imap_fetchbody ($openmail, $i, $portNo);
        }

        $msgBody = trim(substr(quoted_printable_decode($msgBody), 0, 200));

        
        echo $msg;*/
        imap_close($openmail);
        
    } else {

        echo "Failed reading messages!!";

    }

}
readMail();

function strafter($string, $substring) {
  $pos = strpos($string, $substring);
  if ($pos === false)
   return $string;
  else  
   return(substr($string, $pos+strlen($substring)));
}

function strbefore($string, $substring) {
  $pos = strpos($string, $substring);
  if ($pos === false)
   return $string;
  else  
   return(substr($string, 0, $pos));
} 
?>