
<?php
if (isset($_POST['username'], $_POST['useremail'], $_POST['usernumber'])) {

$name = $_POST['username'];
$email = $_POST['useremail'];
$number = $_POST['usernumber'];
$title = "New lead from Nad Al Sheba for Evernest Premium Properties.";

#evernest@12301
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farhana4_enredb"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO ps_campaign_data (`page_tile`, `email`, `mobile`, `lead_name`) VALUES ('$title','$email','$number','$name')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
      // the message
    $msg = "Enquiry from ".$title."\n";
    $body = "Name : ".$name."\n";
    $body .= "Email : ".$email."\n";
    $body .= "Number : ".$number."\n";

    $msg2 = wordwrap($msg,20);
    // rafid@evernestre.ae 
    // send email
    // mail("idrees@evernestre.ae",$msg2 ,$body);


    //sms to Idrees
    // mail("idrees@evernestre.ae",$msg2 ,$body);
   
    $sms=$title."\n".$body;
    
    
    // Sms to Rafid    971528182664

    $url = 'https://myinboxmedia.in/api/mim/SendSMS?userid=MIM2300164&pwd=33E5$4A8_C0B&mobile=971522406449&sender=EVERNEST&msg='.urlencode($sms).'&msgtype=16';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    
    //second sms on : 55 597 3042
    // $url = "https://myinboxmedia.in/api/mim/SendSMS?userid=MIM2300164&pwd=33E5$4A8_C0B&mobile=971555973042&sender=EVERNEST&msg=".urlencode($sms);
    // $ch = curl_init($url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $curl_scraped_page = curl_exec($ch);
    // curl_close($ch);
    // print_r($curl_scraped_page);
    

} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

} else {
    echo "Required POST data is missing.";
}

?>