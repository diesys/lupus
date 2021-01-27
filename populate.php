<?php
    include 'assets/masterus.php';

    // crea partita
    if(isset($_POST['new_name']) and isset($_POST['players'])) {
        new_village($_POST['new_name'], $_GET['v']);
    }

    // il DB esiste?
    if(file_exists('v/_all.json')) {
        $json = file_get_contents('v/_all.json');
    } else {
        $json = "{}";
    }
    $villages = json_decode($json, true);
    $village = read_village($villages[$_GET['v']]);

    // update ruoli
    if(isset($_POST) and isset($_POST['username#0']) and !isset($_POST['new_name'])) {
        $giocatori = array(array());
        foreach($_POST as $key => $value) {
            $giocatori[explode('#', $key)[1]][explode('#', $key)[0]] = $value;
        }
        $village['giocatori'] = $giocatori;
        write_village($village);
    }

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popola <?php echo($village['nome']);?> | Masterus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h2>
        <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
        Popola <?php echo($village['nome']);?>
    </h2>

    <ul>
        <li><a href="admin.php">Admin</a></li>
        <li><a href="./?v=<?php echo($village['id']);?>">Bacheca</a></li>
    <?php if(!isset($_POST['new_name'])) { ?>
        <li><a href="v/<?php echo($village['nome']);?>.json" download>Scarica</a></li>
    <?php } ?>
        <!-- <li><a href="v/<?php// echo($village['nome']);?>.json" download>Carica</a></li> -->
    </ul>
</header>

<center>

<?php if ($_SESSION['logged_in'] == TRUE) { ?>
    <form action="" method="post" name="populate_form">
    <?php $i=0; foreach ($village['giocatori'] as $player) { ?>
        <span class='player_input'>
            <input type="text" placeholder="username" name="username#<?php echo($i); ?>" value="<?php echo($player['username']); ?>" required>
            <input type="text" placeholder="ruolo" name="ruolo#<?php echo($i); ?>" value="<?php echo($player['ruolo']); ?>" required>
            <select name="in_vita#<?php echo($i); ?>" required>
                <option value="true" <?php if($player['in_vita'] == true) { ?> selected <?php } ?>>vivo</option>
                <option value="false" <?php if($player['in_vita'] == false) { ?> selected <?php } ?>>morto</option>
            </select>
        </span>
    <?php $i++;} ?>
        <button class='full-width' formmethod='post' type='submit'>salva</button>
    </form>
<?php } ?>

<p>Salvato i giocatori? Vai alla <a href="edit.php?v=<?php echo($village['id']);?>" class="full-width">pagina di gestione</a> dove potrai aggiungere eventi al calendario ed avere una panoramica da master sul nuovo villaggio!</p>

</center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>