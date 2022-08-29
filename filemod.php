<?php

include_once("settings.php");
include("functions.php");

// Module for uploading and deleting files

// Is user logged in?
    //yes:
        //is user admin?
            //yes:
                // do the file operation
            //no:
                // log user out
    //no:
        //log user out


session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    if ( isAdmin( strtolower($_SESSION['email'])) || isUpload($_SESSION['email']) ) {
        if (isset($_FILES['file'])) {
            
            
            // Write files to disk
            
            $uuid = uniqid();
            $tmp_fn =  "files/" . $uuid . "_" . $_FILES['file']['name'];
            
            $fileobj = array(
                "user" => $_SESSION['email'],
                "uuid" => $uuid,
                "timedate" => date("Ymd-His"),
                "filename" => $tmp_fn,
                "md5" => md5($tmp_fn),
                "size" => filesize($_FILES['file']['tmp_name'])
            );
    
            move_uploaded_file($_FILES['file']['tmp_name'], $fileobj['filename']);
    
            // copy($_FILES['file']['tmp_name'], $fileobj['filename']);

            // write metadata file
            $stringData = serialize($fileobj);
            file_put_contents( "files/" . md5($tmp_fn) . ".txt", $stringData);

        }
        
        else if (isset($_POST['delete'])) {

            // Delete files from disk

            // Open metadata file to get filename

            $filedata = unserialize(file_get_contents("files/" . $_POST['delete'] . ".txt"));

            // Delete file
            unlink($filedata['filename']);

            // Delete metadata file
            unlink("files/" . $_POST['delete'] . ".txt");

        }
        
        else if (isset($_POST['tags']) && isset($_POST['md5'])) {
            // Check if metadata file exists

            $filepath = "files/" . $_POST['md5'] . ".txt";

            if (file_exists($filepath)) {
                // Get contents of metadata file
                $metadata_file = file_get_contents($filepath);
                // Parse metadata file as object
                $metadata_object = unserialize($metadata_file);

                // If tags key does not exist, add key
                if (!key_exists("tags", $metadata_object)) {
                    $metadata_object['tags'] = [];
                }
                
                // split tags. comma and space separated. 
                $tags = $_POST['tags'];
                $tags = explode(",", $tags);
                
                // add tags
                $metadata_object['tags'] = $tags;

                // put contents back in file
                file_put_contents($filepath, serialize($metadata_object));

                // exit ok.
                header('Location:index.php', true);
            
            }
            else {
                echo "File not found.";
                exit();
            }

            
        }

        
        header('Location:index.php', true);
    } 
}


?>