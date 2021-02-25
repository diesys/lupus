<?php
    include 'lupus.php';
    
    if(!isset($_SESSION)) {
        session_start();
    }

    if(isset($_POST) and isset($_POST['parent_url']) and isset($_POST['color']) and isset($_POST['image'])) {
        if($_POST['color'] != "") {
            $_SESSION['color'] = intval($_POST['color']);
        }
        if($_POST['image'] != "") {
            $_SESSION['image'] = intval($_POST['image']);
        }
       
        header("Location: ".$_POST['parent_url']);
    }
    // var_dump($_POST);
    // echo("++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
    // var_dump($_SESSION);

?>