<?php
    include 'assets/lupus.php';

    if(isset($_GET) and isset($_GET['v'])) {
        if($_GET['v'] == "space") {
            $content = file_get_contents('regolamenti/lupusinspace.html');
        } elseif ($_GET['v'] == "classic") {
            $content = file_get_contents('regolamenti/classic.html');
        } $variant = $_GET['v'];
    } else { $variant = "space";}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <?php if($variant == "space") { echo(headerImport("space")); ?>
        <title>Regolamento | Lupus in Space</title>
    <?php } elseif($variant == "classic") { echo(headerImport("classic")); ?>
        <title>Regolamento | Classico</title>
    <?php } else { echo(headerImport("space")); ?>
        <title>Regolamenti</title>
    <?php } ?>

</head>

<body class="clr-<?php if(isset($_SESSION) and isset($_SESSION['color']) and intval($_SESSION['color']) != -1) {
        echo($_SESSION['color']);
    } else {
        $color = rand(0,4); echo($color); 
    }
    ?>">
<?php themeSelector('regolamento.php?'.$_SERVER['QUERY_STRING']); ?>

    <div id="bg" style="background-image: url('assets/img/bg/<?php 
        if($variant != "") {
            echo($variant.'/');
        } else {
            echo("space/");
        }
        if(isset($_SESSION) and isset($_SESSION['image']) and intval($_SESSION['image']) != -1) {
            echo($_SESSION['image']);
        } else {
            echo(rand(0, 5)); 
        } ?>.jpg')"></div>

    <header>
        <ul>
            <li>
                <a class="logo" href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
                <h2 class="title-header no-shadow no-padd-marg">Regolamento</h2>
            </li>
            <li>
                <?php if($variant == "space") { ?>
                    <a href="regolamenti/Regolamento_LupusInSpace.pdf" download="">
                        <img src="assets/img/icons/download-24px.svg" alt="Scarica" height="32" width="32">
                        PDF
                    </a>
                <?php } elseif($variant == "classic") { ?>
                    <a href="regolamenti/Regolamento_Classic.pdf" download="">
                        <img src="assets/img/icons/download-24px.svg" alt="Scarica" height="32" width="32">
                        PDF
                    </a>
            </li>
            <?php } ?>
        </ul>
        
    </header>


    <center class="regolamento">
        <?php 
            if($variant != "") {echo($content);} 
            else { ?>
                <p>Vedi la nuovissima variante <a href="regolamento.php?v=space">Lupus in Space</a> o la <a href="regolamento.php?v=classic">Classica</a> (al momento non presente)</p>
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