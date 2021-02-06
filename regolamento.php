<?php
    if(isset($_GET) and isset($_GET['v'])) {
        if($_GET['v'] == "space") {
            $content = file_get_contents('regolamenti/lupusinspace.html');
        } elseif ($_GET['v'] == "classic") {
            $content = file_get_contents('regolamenti/classic.html');
        } $variant = $_GET['v'];
    } else { $variant = "";}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
    <?php if($variant == "space") { ?>
        <title>Regolamento | Lupus in Space</title>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } elseif($variant == "classic") { ?>
        <title>Regolamento | Classico</title>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } else { ?>
        <title>Regolamenti</title>
    <?php } ?>
    
    <!-- general css -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="background-image: url('assets/img/bg/<?php echo rand(0, 5) ?>.jpg')">
    <header>
        <h2>
            <a href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
            <?php if($variant == "space") { ?>
                Regolamento <i>Lupus in Space</i> <br> 
                <small><a href="docs/Regolamento_LupusInSpace.pdf" download="">scarica PDF</a></small>
            <?php } elseif($variant == "classic") { ?>
                Regolamento <i>Classico</i> <br> 
                <small><a href="docs/Regolamento_Classic.pdf" download="">scarica PDF</a></small>
            <?php } else { ?>
                Regolamenti 
            <?php } ?>
        </h2>
    </header>


    <center class="regolamento">
        <?php 
            if($variant != "") {echo($content);} 
            else { ?>
                <p>Vedi la nuovissima variante <a href="regolamento.php?v=space">Lupus in Space</a> o la <a href="regolamento.php?v=classic">Classica</a></p>
        <?php } ?>
    </center>

<footer>
    <p class="legend">
        Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti):
        informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le
        pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo
        possiede.
    </p>
</footer>
</body>

</html>