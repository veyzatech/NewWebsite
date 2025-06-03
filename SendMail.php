<?php 
if (isset($_REQUEST['name'],$_REQUEST['contact-email'])) {
      
    $fullname = $_REQUEST['name'];
    $email = $_REQUEST['contact-email'];
    $phone = $_REQUEST['subject'];
    $enquiry = $_REQUEST['message'];
    $message = 'Fullname is : '.$fullname.'<br> Custom Email Id : '.$email.'<br>Subject : '.$phone.'<br>Enquiry message : '.$enquiry.'';
   
      if(empty($fullname))
      {
          echo "Please enter fullname";
      }
      if(empty($email))
      {
          echo "Please enter email";
      }
      if(empty($phone))
      {
          echo "Please enter phone number";
      }
      if(empty($message))
      {
          echo "Please enter message";
      }
    // Set your email address where you want to receive emails. 
    $to = 'info@veyza.in';
      
    $subject = 'Request For Demo From Veyza Website';
     $headers = "From: ".$fullname." <".$email."> \r\n";
	$headers.='Reply-To: info@veyza.in \r\n';
	$headers.='X-Mailer: PHP/' . phpversion().'\r\n';
	$headers.= 'MIME-Version: 1.0' . "\r\n";
	$headers.= 'Content-type: text/html; charset=iso-8859-1 \r\n';      
    $send_email = mail($to,$subject,$message,$headers);
      
    echo ($send_email) ? 'success' : 'error';
      
}


?>