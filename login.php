<?php

include("settings.php");
include_once("otpmailer.php");
include_once("headers.php");
include_once("footers.php");
include_once("functions.php");

function renderLogin($siteTitle, $splashLanding, $siteFooter, $reCaptchaSite, $failedCaptcha=false) {

    renderHeader($siteTitle);

    // recaptcha v3 reqs
    echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
    echo '<script> function onSubmit(token) { ' .
        'document.getElementById("userlogin").submit();' . 
        ' } </script>';
    
    // build form
    echo '<div class="row">';
    echo '<div class="twelve columns">';
    echo '<p class="u-full-width" style="text-align:center">' . $splashLanding . '</p>'; 
    echo '<p class="u-full-width" style="text-align:center">please provide your email address.</p>';
    if ($failedCaptcha) {
        echo '<p class="u-full-width" style="text-align:center; color:#F00;">failed captcha. please retry.</p>';
    }
    echo '</div>';

    echo '<div class="six columns offset-by-three" style="text-align:center">';
    echo '<form method="post" action="login.php" id="userlogin" class="u-full-width">';
    echo '<input type="text" placeholder="ex., user@site.org" name="email" class="two-thirds column" />';

    // Embed code for recaptcha
    echo '<button class="g-recaptcha one-third column" data-sitekey="' . $reCaptchaSite . '" data-callback=\'onSubmit\' data-action=\'submit\' id="loginButton">Login</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    renderFooter($siteFooter);

};

function renderOTPLogin($siteTitle, $failedOTP=false) {

    renderHeader($siteTitle);

    // build form
    echo '<div class="row">';
    echo '<div class="twelve columns">';
    echo '<p class="u-full-width" style="text-align:center">a <b>one-time-password</b> has been sent to your email. please enter it below.</p>';
    if ($failedOTP) {
        echo '<p class="u-full-width" style="text-align:center;color:#F00">incorrect otp, please try again.</p>';
    }
    echo '</div>';

    echo '<div class="six columns offset-by-three" style="text-align:center">';
    echo '<form method="post" action="login.php" id="userlogin" class="u-full-width">';
    echo '<input type="text" placeholder="ex., 123ABC" name="otp" class="two-thirds column" />';
    echo '<input type="submit">';
    echo '</form>';
    echo "</div>";

    echo '<div class="six columns offset-by-three" style="text-align:center">';
    echo '<form method="post" action="login.php" id="userLogout" class="u-full-width">';
    echo '<input type="hidden" name="logout" value="true" class="two-thirds column" />';
    echo '<input type="submit" value="< back">';
    echo '</form>';
    echo "</div>";

    echo "</div>";

    renderFooter();

};



// MAIN LOGIC
//
//
// check session. no email? new session.

session_start();
// session_unset();
// session_destroy();
// logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location:login.php", true);
}

// first landing
if ( !isset($_SESSION['loginstep'])) {

    // New session. Initialize session
    $_SESSION['email'] = "";
    $_SESSION['otp'] = renderOTP();
    $_SESSION['verified'] = false;
    $_SESSION['loggedin'] = false;
    $_SESSION['loginstep'] = 0;

}

// var_dump($_SESSION);

// User is at some stage of login.

switch($_SESSION['loginstep']) {

    case 0: // preverified
    
        $_SESSION['loginstep'] = 1;
        renderLogin($FD_siteTitle, $FD_splashLanding, $FD_footer, $_GLOBALS['reCaptchaSite']);
        break;
    
    case 1: // verification
        // Run verification

        if (isset($_POST['email'])) {
            // Did user pass captcha?
            if (isset($_POST['g-recaptcha-response'])) {
                $reCaptchaResponse = $_POST['g-recaptcha-response'];
    
                $url = 'https://www.google.com/recaptcha/api/siteverify';
                $data = array(
                    'secret' => $_GLOBALS['reCaptchaSecret'], 
                    'response' => $reCaptchaResponse
                );
                
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    )
                );
                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                $result = json_decode($result, $assoc = false);
    
                if ($result->success) {
                    // assign session vars
                    $_SESSION["email"] = $_POST['email'];
                    $_SESSION["verified"] = true;
                    $_SESSION["loginstep"] = 2;
                    header('Location: login.php');
                }
                else {
                    renderLogin($FD_siteTitle, $FD_splashLanding, $FD_footer, $_GLOBALS['reCaptchaSite'], true);
                }
            } 
        } else {
            // email not set, rebuild session
            session_unset();
            session_destroy();
            header('Location: login.php', true);
        }
        
        break;

    case 2: // send otp

        $_SESSION['loginstep'] = 3;

        if (!isset($_SESSION['otpmailed']) && $_SESSION['otpmailed'] == false ){
            // Email has not yet been sent
            sendOTP($_SESSION['email'], $_SESSION['otp']);
            $_SESSION['otpmailed'] = true;
            renderOTPLogin($FD_siteTitle);
        } else {
            // Prevent too much mail, do not resend email
            // 
            renderOTPLogin($FD_siteTitle, true);
        }     

        break;

    case 3: // verify otp

        if ($_SESSION['otp'] == strtoupper($_POST['otp'])){
            $_SESSION['loggedin'] = true;
            // write user to logfile
            if ( !isAdmin($_SESSION['email']) ) {
                file_put_contents("users.tsv", $_SESSION["email"] . "\t" . date("m-d-y") . PHP_EOL, FILE_APPEND);
            }
            
            // redirect to index;
            header('Location: index.php', true);
        } else {
            $_SESSION['loginstep'] = 2;

            // redirect to login
            header('Location: login.php', true);
        }
        break;
    
    default: // we're in limbo, destroy session and restart
        session_unset();
        session_destroy();
        break;

}


?>