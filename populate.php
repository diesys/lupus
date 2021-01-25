<?php
function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function read_village($file_name) {
    if(file_exists('v/'.$file_name.'.json')) {
        $json = file_get_contents('v/'.$file_name.'.json');
        $data = json_decode($json, true);
        return $data;
    } else {
        return FALSE;
    }
}

function write_village($data, $file_name) {
    $json = json_encode($data);
    file_put_contents('v/'.$file_name.'.json', $json);
}

function new_village($file_name) {
    $data = array(
        'nome' => $_POST['new_name'],
        'telegram' => "",
        'giorni' => array(array(array())),
        'giocatori' => array_fill(0, $_POST['players'], array("username" => "", "ruolo" => "", "in_vita" => TRUE)),
        'id' => generateRandomString()
    );

    $new_json = 'v/'.$file_name.'.json';
    $new_village = json_encode($data);
    
    if (!file_exists('v/_all.json')) {
        file_put_contents('v/_all.json', json_encode(array()));
    }
    $db = file_get_contents('v/_all.json');
    $all = json_decode($db, true);

    if (!file_exists($new_json)) {
        if(!array_key_exists($data['id'], $all)) {
            $all[$data['id']] = $data['nome'];
        } else {
            $data['id'] = substr($data['id'], 0, -2);
            $all[$data['id']] = $data['nome'];
        }
        file_put_contents($new_json, $new_village);
        file_put_contents('v/_all.json', json_encode($all));
    } else {
        $error = "Il file esiste!";
    }
}

    //////////////
    // crea partita
    if(isset($_POST['new_name']) and isset($_POST['players'])) {
        new_village($_POST['new_name']);
    }
    $village = read_village($_POST['new_name']);
    
    session_start();
    $error = "";
    if(file_exists('v/_all.json')) {
        $json = file_get_contents('v/_all.json');
    } else {
        $json = "{}";
    }
    $db = json_decode($json, true);

    if(isset($_POST)) {
        //;
    }

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popola | Masterus</title>
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
        <li><a href="v/<?php echo($village['nome']);?>.json" download>Scarica</a></li>
        <!-- <li><a href="v/<?php// echo($village['nome']);?>.json" download>Carica</a></li> -->
    </ul>
</header>

<center>

<?php if ($_SESSION['logged_in'] == TRUE and isset($_POST['new_name'])) { ?>
        <form action="" method="post" name="populate_form">
        <?php foreach ($village['giocatori'] as $player) { ?>
            <span class='player_input'>
                <input type="text" placeholder="username" value="<?php echo($player['username']); ?>" required>
                <input type="text" placeholder="ruolo" required>
                <select name="in_vita" required>
                    <option value="true" selected>vivo</option>
                    <option value="false">morto</option>
                </select>
            </span>
        <?php } ?>
            <button class='full-width' formmethod='post' type='submit'>salva</button>
        </form>
    <?php } ?>
            <a href="edit.php?v=<?php echo($village['id']);?>" class="full-width">Gestione villaggio</a>
    </center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>