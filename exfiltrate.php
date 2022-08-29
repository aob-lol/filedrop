<?php

session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

    // allow download

    // get file request from GET

    $file = "";
    $filedata = "";

    if (isset($_GET['file'])) {
        $file = $_GET['file'];
        
    } else {
        header('HTTP/1.0 403 Forbidden');
        echo 'Bad request.'; 
        exit();
    }

    // open metadata file if it exists
    
    if (file_exists("files/" . $file . ".txt")) {
        
        $filehandler = fopen("files/" . $file . ".txt", "r");
        $filedata = unserialize(fgets($filehandler));
        fclose($filehandler);

        $filedata['friendlyname'] = basename($filedata['filename']);
        $filedata['friendlyname'] = explode("_", $filedata['friendlyname'], 2)[1];

    }
    else {
        header('HTTP/1.0 403 Forbidden');
        echo 'Metadata file does not exist for given file.';
        exit();
    }

    // Push file as download
    header("X-Sendfile:" . $filedata['filename']);
    header('Content-Type: application/octet-stream');
    // add quotes around filename to avoid error in chrome when commas appear in filename
    $quotedFilename = '"' . $filedata['friendlyname'] . '"';
    header('Content-Disposition: attachment; filename='. $quotedFilename);

} else {

    header('HTTP/1.0 403 Forbidden');
    echo 'Unauthorized access forbidden';

}


?>