<?php
    include 'assets/lupus.php';
    $error = "";

    // crea partita
    if(isset($_POST) and isset($_POST['new_name']) and isset($_POST['players']) and isset($_GET) and isset($_GET['v']) and isset($_POST['variant'])) {
        new_village($_POST['new_name'], $_GET['v'], $_POST['variant']);
    }

    // il DB esiste?
    if(file_exists('v/_all.json')) {
        $json = file_get_contents('v/_all.json');
    } else {
        $json = "{}";
    }
    $villages = json_decode($json, true);
    if(isset($_GET) and isset($_GET['v']) and (array_key_exists($_GET['v'], $villages))) {
        $village = read_village($villages[$_GET['v']]);
    } else {
        $village = NULL;
        $error = "Villaggio non trovato!";
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Popola <?php if(isset($village['nome'])) {echo($village['nome']);} ?> | Lupus</title>
    
    <?php if($village != NULL and $village['variante'] == "classic") { 
        echo(headerImport("classic"));
    } else { 
        echo(headerImport("space"));
    } ?>
</head>

<body class="<?php $color = rand(0,4); echo("clr-".$color); ?>">
<?php if(isset($village)) { ?>    
    <div id="bg" style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')"></div>
<?php } else { ?>
    <div id="bg" style="background-image: url('assets/img/bg/space-<?php echo(rand(0, 5)); ?>.jpg')"></div>
<?php } ?>

<header>
    <ul>
        <li>
            <a class="logo" href="admin.php"><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
        </li>
        <li><a href="admin.php">
            <img src="assets/img/icons/view_list-24px.svg" alt="·" height="28" width="28">
            Villaggi</a>
        </li>
    <?php if(isset($village['id'])) { ?>
        <li><a href="./?v=<?php echo($village['id']); ?>">
            <img src="assets/img/icons/public-24px.svg" alt="·" height="28" width="28">
            Bacheca</a>
        </li>
    <?php } ?>
    <?php if(!isset($_POST['new_name']) and isset($village['nome'])) { ?>
        <li><a href="v/<?php echo($village['nome']); ?>.json" download>
            <img src="assets/img/icons/download-24px.svg" alt="·" height="28" width="28">
            Scarica</a>
        </li>
    <?php } ?>
        <!-- <li><?php// echo($village['nome']);?> Carica</li> -->
    </ul>
</header>

<center>
    <h2 class="title">
        <?php if(isset($village['nome'])) {echo($village['nome']);} ?>
    </h2>

<?php if ($village != NULL and $error == "" and isset($_SESSION['logged_in']) and $_SESSION['logged_in'] == TRUE) { ?>
    <form action="edit.php?v=<?php echo($village['id']); ?>" method="post" name="populate_form" class="flex-column">

        <?php if(isset($_POST['new_name'])) { ?>
            <input style="display: none;" id="first_run" name="first_run" value="1" required>
        <?php } ?>


        <div class='player_input'>
            <label for="master">Master</label>
            <input type="text" placeholder="master" name="master" pattern="[A-Za-z0-9_]{5,}" placeholder="master" value="<?php if(isset($village['master'])) {echo($village['master']);} ?>" required>
        </div>
        <h3>Giocatori</h3>
    <?php $i=0; foreach ($village['giocatori'] as $player) { ?>
        <span class='player_input'>
            <input type="text" placeholder="username" pattern="[A-Za-z0-9_]{5,}" name="username#<?php echo($i); ?>" value="<?php echo($player['username']); ?>" required>
            <select name="ruolo#<?php echo($i); ?>" required>
                <?php foreach ($roles[$village['variante']] as $role => $faction) {
                    if(isset($player['ruolo']) and $player['ruolo'] == $role) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    echo("<option value='".$role."'".$selected.">".$role." (".$faction.")</option>");
                } ?>
            </select>
            <select name="in_vita#<?php echo($i); ?>" required>
            <?php if($player['in_vita'] == "true") { ?>
                <option value="true" selected>vivo</option>
                <option value="false">morto</option>
            <?php } else { ?>
                <option value="true">vivo</option>
                <option value="false" selected>morto</option>
            <?php } ?>
            </select>
        </span>
    <?php $i++;} ?>
        <br>
        <button class='full-width' formmethod='post' type='submit'>salva</button>
    </form>
<?php } else {
    if ($error != "") { ?>
        <h2 class="error"><?php echo($error);?></h2>
        <p>Torna alla pagina di <a href="admin.php">admin</a></p>
    <?php }} ?>

</center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>