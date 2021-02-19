<?php
    include 'assets/lupus.php';

    $error = "";

    // LOGIN
    if((isset($_POST) and isset($_POST['password']) and $_POST['password'] == $password) or (isset($_SESSION) and isset($_SESSION['logged_in']) and $_SESSION['logged_in'])) {
        $logged = TRUE;
    } else {
        $error="Password errata o assente!";
        $logged = FALSE;
    }
    $_SESSION['logged_in'] = $logged
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
    <title>Lista partite | Lupus</title>
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

<body class="<?php $color = rand(0,4); echo("clr-".$color); ?>">
    <div id="bg" style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')"></div>
    <header>
        <h2 class="title">
            <a class="logo" href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
            Lupus
        </h2>
    </header>

    <center class="admin">
    <?php if ($logged == TRUE) { ?> 
        <h2>Benvenuto Master!</h2>

        <form action="populate.php?v=<?php echo(generateRandomString()); ?>" method="post">
            <h4 class="full-width">Crea partita</h4>

            <span class="half-flex">
                <label for="new_name">Nome</label>
                <input name="new_name" placeholdxer="Nome" type="text" pattern="[A-Za-z0-9]{4-24}" required />
            </span>
            
            <span class="half-flex">
                <label for="variant">Variante</label>
                <select name="variant" id="variant" required>
                    <option value="space" selected>Lupus in Space</option>
                    <option value="classic">Classico</option>
                </select>
            </span>

            <span class="half-flex">
                <label for="players">Giocatori</label>
                <input name="players" type="number" placeholder="Giocatori" min="2" max="40" range="1" required />
            </span>

            <!-- <p class="legend">¹ alfanumerico senza spazi · ² 4-30 giocatori</p> -->

            <span class="half-flex">
                <button class="half-width" type="submit" formmethod="post">Crea</button>
            </span>
        </form>  

        
        <form action="edit.php" id="select_village" method="get">
            <h4 class="half-flex">Modifica</h4>
            <!-- <label for="v">Villaggio</label> -->
            <select class="half-flex" name="v" id="lista_villaggi" onchange="document.querySelector('#select_village').submit()" required>
        z        <option value="" disabled selected></option>
                <?php foreach ($villages as $hash => $name) {
                    echo("<option value='".$hash."'>".$name."</option>");
                } ?>
            </select>
            <!-- <button type="submit" formmethod="get">vai</button> -->
        </form>

    <?php } if ($error != "") { ?>
        <h3 class="error"><?php echo($error);?>, torna alla pagina di <a href="login.php">login</a>?</h3>
    <?php } ?>
    </center>

    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>