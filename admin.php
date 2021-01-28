<?php
    include 'assets/masterus.php';

    $error = "";

    // LOGIN
    if((isset($_POST['password']) and $_POST['password'] == $password) or (isset($_SESSION['logged_in']) and $_SESSION['logged_in'])) {
        $logged = TRUE;
    } else {
        $error="Password sbagliata!";
        $logged = FALSE;
    }
    $_SESSION['logged_in'] = $logged

    // logout
    // if(isset($_POST['page_logout'])) {
    //     $_SESSION['logged_in'] = FALSE;
    // }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Masterus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h2><img height="40" width="40" src="assets/img/amarok.png" alt="logo">Masterus</h2>
</header>

<center>
<?php if ($logged == TRUE) { ?> 
        <form action="edit.php" method="get">
            <h4 class="full-width">Villaggio</h4>
            <select class="full-width" name="v" id="lista_villaggi" required>
                <option value="" disabled selected></option>
                <?php
                    foreach ($villages as $hash => $name) {
                        echo("<option value='".$hash."'>".$name."</option>");
                    }
                ?>
            </select>
            <button type="submit" formmethod="get">vai</button>
        </form>

        <form action="populate.php?v=<?php echo(generateRandomString()); ?>" method="post">
            <h4 class="full-width">Nuovo villaggio cazzo</h4>
            <input class="half-width" name="new_name" placeholdxer="Nome¹" type="text" pattern="[A-Za-z0-9]{4-24}" required />
            <input class="half-width" name="players" type="number" placeholder="Giocatori²" min="2" max="40" range="1" required />
            <p class="legend">¹ alfanumerico senza spazi · ² 4-30 giocatori</p>
            
            <button type="submit" formmethod="post">Crea</button>
        </form>  

<?php } if ($error != "") { ?>
    <h2 style="color:yellow;"><?php echo($error);?></h2>
<?php } ?>
</center>

<footer>
    <p class="legend">
        Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
    </p>
</footer>
</body>
</html>