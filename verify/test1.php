<?php

session_start();

if( empty($_SESSION['myverifytoken']) || empty($_POST['onlyAuthentication']) ){
    exit;
}
if( $_SESSION['myverifytoken'] == $_POST['onlyAuthentication'] ){
    echo 'chenggong';
    unset($_SESSION['myverifytoken']);
}