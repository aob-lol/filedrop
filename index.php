<?php

include_once("settings.php");
include_once("headers.php");
include_once("footers.php");
include_once("functions.php");

function renderPage($isadmin = false, $isupload=false) {
    
    // logout area
    echo '        <div class="row">'. PHP_EOL;
    echo '            <div class="six columns offset-by-three" style="text-align:center">'. PHP_EOL;
    echo '                <form method="post" action="login.php" class="u-full-width" style="padding-top: 12px">'. PHP_EOL;
    echo '                    <input type="hidden" name="logout" value="true" />'. PHP_EOL;
    echo '                    <input type="submit" class="u-full-width" value="logout" />'. PHP_EOL;
    echo '                </form>'. PHP_EOL;
    echo '            </div>'. PHP_EOL;
    echo '        </div>'. PHP_EOL;

    // file drop area
    if ($isadmin || $isupload) {
        echo '        <div class="row" style="margin-bottom:100px;>"'. PHP_EOL;

        echo '          <div class="five columns" id="drop-area" style="margin-bottom: 35px">'. PHP_EOL;
        echo '            <form class="uploadform five columns" action="filemod.php" method="post">'. PHP_EOL;
        echo '                  <label class="button u-full-width button-primary" for="fileElem">Select file(s)</label>'. PHP_EOL;
        echo '                  <p class="u-full-width"><small>tap above or drop files here to upload. don\'t upload abusive shit. assume all uploaded files are public and unencrypted. again: use the honor system.</small></p>'. PHP_EOL;
        echo '                  <input type="file" class="u-full-width" id="fileElem" multiple onchange="handleFiles(this.files)">'. PHP_EOL;
        echo '                  <progress id="progress-bar" class="u-full-width" max=100 value=0></progress>'. PHP_EOL;
        echo '            </form>'. PHP_EOL;
        
        
        if ($isadmin) {
        $contents = file("users.tsv");
        $contents = array_reverse($contents);
    
        echo '            <pre class="seven columns fdUserArea">'. PHP_EOL; 
        echo 'latest users'. PHP_EOL . PHP_EOL;
        foreach ($contents as $c) {
            echo $c . PHP_EOL;
        }
    
        echo '            </pre>'. PHP_EOL;
    };
        echo '          </div>'. PHP_EOL;
        echo '      </div>' . PHP_EOL;
        echo '    </div>' . PHP_EOL;
    }


        // tag cloud
        renderTagCloud($isadmin, $isupload);
    
    echo '      <div class="row">' . PHP_EOL;
    echo '          <div class="eleven columns">' . PHP_EOL;
    // file list
    renderFileList($isadmin, $isupload);
    echo '          </div>' . PHP_EOL;
}

function getHumanReadableSize($bytes) {
    if ($bytes > 0) {
      $base = floor(log($bytes) / log(1024));
      $units = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"); //units of measurement
      return number_format(($bytes / pow(1024, floor($base))), 2) . " $units[$base]";
    } else return "0 bytes";
  }

function renderTagCloud(){
    $all_files = [];
    $all_tags = [];

    // Iteratively open every txt file
    foreach(glob("files/*.txt") as $filename) {
        $tmp_file = unserialize(file_get_contents($filename));
        array_push($all_files, $tmp_file);
    }


    foreach($all_files as $filedata) {
        foreach($filedata['tags'] as $tag) {
            array_push($all_tags, $tag);
        }
    }

    $all_tags = array_unique($all_tags);

    echo '<ul id="tagcloud" class="eight columns offset-by-two">' . PHP_EOL;

    foreach($all_tags as $tag) {
        echo '<a href="?tag=' . $tag . '"><li>' . $tag . "</li></a>";
    }

    echo "</ul>" . PHP_EOL;
}

function renderFileList($isadmin = false, $isupload=false) {

    // Check for tag in $_GET
    $tagmode = false;
    $tag = "";

    if (isset($_GET['tag'])){ 
        $tagmode = true;
        $tag = $_GET['tag'];
    }




    echo '              <table class="eleven columns offset-by-one">' . PHP_EOL;
    echo '                  <thead style="font-size: 11px; font-weight: 900;">' . PHP_EOL;
    echo '                      <tr>' . PHP_EOL;
    echo '                          <td style="text-align:center;">file</td>' . PHP_EOL;
    echo '                          <td style="text-align:center;">attribs</td>' . PHP_EOL;
    if ($isadmin) { 
        echo '                          <td style="text-align:center;">rm</td>' . PHP_EOL;
    }
    echo '                      </tr>' . PHP_EOL;
    echo '                  </thead>' . PHP_EOL;
    echo '                  <tbody>' . PHP_EOL;

    // Sort files by date in metadata files

    // Declare master filelist
    $all_files = [];

    // Iteratively open every txt file
    foreach(glob("files/*.txt") as $filename) {
        $tmp_file = unserialize(file_get_contents($filename));
        array_push($all_files, $tmp_file);
    }

    // Sort array by date 
    // Get column
    $date = array_column($all_files, "timedate");
    // array_column($all_files, "timedate");

    // Sort array
    array_multisort($date, SORT_DESC, $all_files);

    // Filter by tag
    if ($tagmode) {

        $filtered_files = [];

        foreach ($all_files as $filedata) {

            if (in_array($tag, $filedata['tags'])) {
                array_push($filtered_files, $filedata);
            }
        }

        $all_files = $filtered_files;
    }

    foreach ($all_files as $filedata) {
        echo '                      <tr class="u-full-width">' . PHP_EOL;

        $filedata['friendlyname'] = basename($filedata['filename']);
        $filedata['friendlyname'] = explode("_", $filedata['friendlyname'], 2)[1];
        $filemd5 = $filedata['md5'];
        $filetags = $filedata['tags'];
        $filetagshuman = "";


        if ($isadmin || $isupload) {
            // Format tags for form
            for ($i = 0; $i < sizeof($filetags) - 1; $i++) {
                $filetagshuman = $filetagshuman . $filetags[$i] . ",";
            }
            $filetagshuman = $filetagshuman . end($filetags);

        } else {
            // Format tags for user interaction

            for ($i = 0; $i < sizeof($filetags) - 1; $i++) {
                $filetagshuman = $filetagshuman . '<a href="?tag=' . $filetags[$i] . '">' . $filetags[$i] . "</a>, ";
            }
            $filetagshuman = $filetagshuman . '<a href="?tag=' . end($filetags) . '">' . end($filetags) . "</a>";
        }
        

    


        // File download button
        echo '                          <th class="u-full-width">' . PHP_EOL;
        echo '                              <a href="exfiltrate.php?file=' . $filedata['md5'] . '" target="_blank">';
        echo '                                  <button class="button button-primary u-full-width">' . $filedata['friendlyname'] . '</button>';
        echo '                              </a>' . PHP_EOL;
        echo '                          </th>' . PHP_EOL;

        // File info area
        echo '                          <th>' . PHP_EOL;
        echo '                              <pre class="fdFileInfo">date ' . 
                                                $filedata['timedate'] . 
                                                PHP_EOL . 
                                                'size ' . 
                                                getHumanReadableSize($filedata['size']) . 
                                                '</pre>' . PHP_EOL;
        echo '                          </th>' . PHP_EOL;

        if ($isadmin) {
            echo '                          <th>' . PHP_EOL;
            echo '                              <form action="filemod.php" method="post">' . PHP_EOL;
            echo '                                  <input type="hidden" name="delete" value="' . $filemd5 . '" />' . PHP_EOL;
            echo '                                  <input type="submit" value="del" style="margin-top:5px" />' . PHP_EOL;
            echo '                              </form>' . PHP_EOL;
            echo '                          </th>' . PHP_EOL;
        }
        
        echo '                      </tr>' . PHP_EOL;

        if ($isadmin || $isupload) {

            echo '                      <tr>' . PHP_EOL;
            echo '                          <th class="u-full-width">' . PHP_EOL;
            echo '                              <form action="filemod.php" method="post">' . PHP_EOL;
            echo '                                  <input name="tags" value="' . $filetagshuman . '" class="nine columns tags" />';
            echo '                                  <input type="hidden" name="md5" value="' . $filemd5 . '" />' . PHP_EOL;
            echo '                                  <div class="one column">&nbsp;</div>' . PHP_EOL;
            echo '                                  <input type="submit"  />' . PHP_EOL;
            echo '                              </form>' . PHP_EOL;
            echo '                          </th>' . PHP_EOL;
            echo '                      </tr>' . PHP_EOL;
        } else {
            echo '                      <tr>' . PHP_EOL;
            echo '                          <th>' . PHP_EOL;
            echo '                              <div class="tags" class="nine columns">' . $filetagshuman . "</pre>" . PHP_EOL;
            echo '                          </th>' . PHP_EOL;
            echo '                      </tr>' . PHP_EOL;
        }

    }

    echo '                  </tbody>' . PHP_EOL;
    echo '              </table>' . PHP_EOL;
    echo '          </div>' . PHP_EOL;
}

// Is user logged in? 
    // yes:
        // Is user admin user?
            // yes: 
                // render all controls
            // no:
                // render limited controls
    // no:
        // send user to login

session_start();

if ( isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true ) {
    if ( isAdmin($_SESSION['email'])) {
        // render all controls
        renderHeader($FD_siteTitle);
        renderPage(true);
        renderFooter($FD_footer, true);
    }
    else if ( isUpload($_SESSION['email'])) {
        renderHeader($FD_siteTitle);
        renderPage(false, true);
        renderFooter($FD_footer, true);
    }
    else {
        // render user controls
        renderHeader($FD_siteTitle);
        renderPage();
        renderFooter($FD_footer);
    }
} else {
    // 200 redirect to login.php
    header('Location:login.php', true);
}

?>