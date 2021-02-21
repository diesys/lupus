<?php
    include 'assets/lupus.php';
    
    if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'] == TRUE) {
        header("Location: admin.php");
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Login | Lupus</title>
    
    <?php echo(headerImport("space")); ?>
</head>

<body class="login <?php $color = rand(0,4); echo("clr-".$color); ?>">
    <div id="bg" style="background-image: url('assets/img/bg/space/<?php echo(rand(0, 5)); ?>.jpg')"></div>
    <header>
        <h2 class="title">
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Lupus login
        </h2>
    </header>

    <center>
        <form action="admin.php" method="post" class="flex">
            <input autofocus type="password" name="password" />
            <button type="submit">entra*</button>
        </form>
        <p>
            Oppure <a href=".">torna alla home</a>
        </p>

    </center>

    <footer>
        <p class="legend">
           * Effettuando il login La password verrà salvata in un cookie solo per evitare di doverla inserire ad ogni operazione e non viene salvato nulla a insaputa dell'utente, cliccando su "entra" si accetta il suo utilizzo.
        </p>
    </footer>

</body>
</html>