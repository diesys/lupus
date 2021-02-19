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
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Londrina+Solid:wght@100;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php $seed = rand(0,1);
    if($seed == 0) { ?>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } else { ?>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } ?>
</head>

<body class="login <?php $color = rand(0,4); echo("clr-".$color); ?>">
    <div id="bg" style="background-image: url('assets/img/bg/<?php if($seed == 0) {echo("space/");} else {echo("classic/");} echo(rand(0, 5)); ?>.jpg')"></div>
    <header>
        <h2 class="title">
            <a href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
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