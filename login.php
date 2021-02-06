<?php
    include 'assets/lupus.php';
    
    if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'] == TRUE) {
        header("Location: admin.php");
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
    <title>Login | Lupus</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if(rand(0,1)%2 == 0) { ?>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } else { ?>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } ?>
</head>

<body style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')">
    <header>
        <h2>
            <a href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
            Lupus login
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