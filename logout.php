<?php
    include 'assets/cyberlupus.php';
    
    if(isset($_SESSION['logged_in'])) {
        $_SESSION['logged_in'] = FALSE;
    }
    unset($_SESSION);
    header("Location: .");
?>