<?php
	
	
	
	$notindomain_errorpage = "anatomy.html";
	$server_errorpage = "anatomy.html";
	$invalidaddress_errorpage = "anatomy.html";
	$successpage = "index.html";
	$recipient="ianheath2010@gmail.com";
	$subject="Web site feedback";
	
	// Set the server variables for older (PHP4,3 etc) systems
	if (!isset($_SERVER)){
		$_POST    = &$HTTP_POST_VARS;
		$_SERVER  = &$HTTP_SERVER_VARS;
	}
	
	
	$servername = $_SERVER['SERVER_NAME'];
	
   	if ($_SERVER['REQUEST_METHOD']=="POST") {
      if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])>7 || !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
		header( "Location: ".$notindomain_errorpage );
		exit;
      } else {
	      $msg="The following information was submitted from a form on ".$servername.":\n\n";
	      
	      foreach($_POST as $key => $val) {
	      	 //filter out any form items called send or reset
	      	 //image based submit and reset buttons will be in the format
	      	 //	send_x: 13
			 // send_y: 10
			 
	      	 $myKeySlice = substr("$key",0,4);
	     	 if ($myKeySlice != "send" && $myKeySlice != "rese"){
				
				if ($key == "subject" || $key == "email" || $key == "name"){
					//Prevent injection attacks by stripping tags and newlines from the data
					//Do this only on data that makes it into the e-mail header as newlines in a message body should still be valid
					$key = strip_tags($key);
					$val = strip_tags($val);
					if (eregi("\r",$key) || eregi("\n",$key)){
						header( "Location: ".$notindomain_errorpage );
						exit;
					}
					if (eregi("\r",$val) || eregi("\n",$val)){
						header( "Location: ".$notindomain_errorpage );
						exit;
					}
	      		}
	      		
	      		
	      		//replace any underscores in the input names (PHP puts these in!) with spaces
	     	 	$key = str_replace("_"," ",$key);
	     	 	
	     	 	//if the form item is called "subject" then set this as the subject line of the mail
	     	 	if ($key == "subject"){
	     	 		$subject=$val;
	     	 	} else {
					if (is_array($val)){
						$msg.="Item: $key\n";
						foreach($val as $v)
						   $msg.="ÊÊÊ$v\n";
					 } else {
						$msg.="$key: $val\n";
					 }
		        }
		        
		     }
	      }
	      
	      
	      
	      //set up the default headers
	      $headers = "";
	      
	      //get the senders name (if specified)
	      if ($_POST["Name"]) {
	     		$name = $_POST["Name"];
	      } else {
	      		$name = "";
	      }
	      
	      //get the senders email address (if specified)
	      if (isset($_POST["Email"])) {
				$email = $_POST["Email"];
	      		if (!preg_match('/^[a-zA-Z0-9_\.-]+@[a-zA-Z0-9-\.]+\.[a-zA-Z]+(\.[a-zA-Z]+)?$/', $email)){
	      			header( "Location: ".$invalidaddress_errorpage );
	      			exit;
	      		}
	      } else {
	      		
//the email is missing!
//strip the domain from the address
//www.domain.com -> domain.com
if (substr($servername,0,4) == "www."){
	$theaddress = substr($theaddress,4);
}
$email = "noreply@".$theaddress;
$msg.="\n\n------------------------------------------------------------------";
$msg.="\nPLEASE NOTE: This is a message from the ".$servername." web site";
$msg.="\nand has been sent from a machine and not a person.";
$msg.="\nPlease do not reply to this e-mail as it will bounce.";
$msg.="\n------------------------------------------------------------------";

	      		
	      }
	      
	      $headers .= "From: $name <$email>\r\n";
	      
	      //add the correct headers for plain text
	      //see: http://www.webmasterworld.com/php/3949990.htm
		  $headers .= "MIME-Version: 1.0\n";
		  $headers .= "Content-type: text/plain; charset=\"ISO-8859-1\"\n";
		  $headers .= "Content-transfer-encoding: 7bit\n";
	      
	      $headers .= "Reply-To: $email\r\n"."Return-Path: $email";
	      
	      
	      error_reporting(0);
	      if (mail($recipient, $subject, $msg, $headers)){//
	         header( "Location: ".$successpage );
	      } else {
	         header( "Location: ".$server_errorpage );
	      }
	 }
   	} else {
		header( "Location: ".$server_errorpage );
   	}
?>
