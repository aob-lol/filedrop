<?php

function renderHeader($siteTitle) {
echo '
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs   -->
  <meta charset="utf-8">
  <title>'. $siteTitle .'</title>
  <meta name="description" content="new media artist">
  <meta name="author" content="ashley ona bott">

  <!-- Mobile Specific Metas   -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS  -->
  <link rel="stylesheet" href="css/OperatorMono.css" />
  <link rel="stylesheet" href="css/normalize.css" />
  <link rel="stylesheet" href="css/skeleton.css" />
  <link rel="stylesheet" href="css/filedrop.css" />

  <!-- Favicon  -->
  <link rel="icon" type="image/png" href="images/favicon.png" />
  
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="twelve columns">
                <center><h2 style="margin-top: 15%; font-weight:400; font-style:italic">'. $siteTitle .'</h2></center>
            </div>
        </div>
';
}

?>