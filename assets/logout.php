<?php
    include 'lupus.php';
    
    if(isset($_SESSION['logged_in'])) {
        $_SESSION['logged_in'] = FALSE;
    }
    unset($_SESSION);
    header("Location: ../");
?>