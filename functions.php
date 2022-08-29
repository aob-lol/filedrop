<?php

include_once("settings.php");


//
//
//
// Functions
//
//
//

function isAdmin($userEmail) {
    if ( in_array( strtolower($userEmail), $GLOBALS["FD_adminUsers"]) ) {
        return true;
    }
    else {
        return false;
    }
}


function isUpload($userEmail) {
    if ( 
        in_array( strtolower($userEmail), $GLOBALS["FD_adminUsers"])
        || in_array( strtolower($userEmail), $GLOBALS["FD_uploadUsers"])) {
        return true;
    }
    else {
        return false;
    }
}


?>