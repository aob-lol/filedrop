<?php

// Functions for rendering OTP and mailing it out

function renderOTP() {
    // Output [0-9A-Z] * 6
    //
    $charList = ['1','2','3','4','5','6','7','8','9','0'];
    $outString = "";

    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, count($charList) - 1);
        $outString .= $charList[$randNum];
    }

    return $outString;


}

function sendOTP($address, $otp) {
    // Send session OTP to address

    $to = "User <" . $address . ">";
    $subject = 'OTP for fs.aob.lol';
    $message = 'Your OTP for the filedrop is ' . $otp . PHP_EOL . PHP_EOL;
           
    $headers = 'From: "Ashley Bott" <ashleyonabott@gmail.com>' . PHP_EOL .
           'X-Mailer: PHP/' . phpversion();
          
    mail($to, $subject, $message, $headers);

}

?>