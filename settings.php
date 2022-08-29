<?php

// reCaptcha secrets, acquire at https://www.google.com/recaptcha/about/
include_once("secrets.php");

// or define inline:
// $_GLOBALS['reCaptchaSite'] = "";
// $_GLOBALS['reCaptchaSecret'] = "";
// $FD_adminUsers = ["user@site.com"];
// $FD_uploadUsers = ["user2@site.com"];






// Site Settings
//
//
//
// Used in mailer
$FD_siteURL = "fs.aob.lol";

// Title at top of site
$FD_siteTitle = "ashley ona bott.";

// Login page verbage
$FD_splashLanding = "this filedrop issues one-time passwords for login.";

// Universal page footer
$FD_footer = "privacy policy <br /><br />" . PHP_EOL .
"email addresses and ip addresses retained to prevent abuse." . PHP_EOL .
" i will never contact emails for marketing purposes.<br /><br />" . PHP_EOL .
"&lt;3 aob";

?>