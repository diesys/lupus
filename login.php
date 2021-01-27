<?php
    include 'assets/masterus.php';
    
    if($_SESSION['logged_in'] == TRUE) {
        header("Location: admin.php");
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master lupus login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h2>
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Masterus
        </h2>
    </header>

    <center>
        <form action="admin.php" method="post" class="flex-column">
            <input autofocus type="password" name="password" />
            <button type="submit">entra*</button>
            <p>
                <a href=".">Torna alla home</a>
            </p>
        </form>

    </center>

    <footer>
        <p class="legend">
           * Effettuando il login La password verrà salvata in un cookie solo per evitare di doverla inserire ad ogni operazione e non viene salvato nulla a insaputa dell'utente, cliccando su "entra" si accetta il suo utilizzo.
        </p>
    </footer>

</body>
</html>