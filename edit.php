<?php

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
    
    function get_alive($village) {
        $alive = 0;
        $dead = 0;
        foreach($village['giocatori'] as $giocatore) {
            if($giocatore['in_vita']) {
                $alive += 1;
            } else {
                $dead += 1;
            }
        } return [$alive, $dead];
    }

    function get_events($village) {
        $events = array();
        foreach($village['eventi'] as $event) {
            array_push($events, $event);
        } return $events;
    }

    //////////////////////
    session_start();
    $error = "";
    
    if ($_SESSION['logged_in'] == TRUE and isset($_GET['v'])) {
        $json = file_get_contents('v/_all.json');
        $db = json_decode($json, true);
        
        // searching village json by id
        if(array_key_exists($_GET['v'], $db)) {
            $selected = $db[$_GET['v']];
            $village = read_village($selected);
            // $alive = get_alive($village);
            // $events = get_events($village);
                // var_dump($village);
        } else {
            $error = "Village not present!";
        }
    }

    //// aggiungi evento
    if(isset($village) and isset($_POST) and isset($_POST['date']) and isset($_POST['description'])) {
        // prevent double submits
        if(isset($_SESSION['last_action'])) {
            if($_POST['description'] != $_SESSION['last_action']) {
                array_push($village['eventi'], array('data' => $_POST['date'], 'descrizione' => $_POST['description']));
                write_village($village, $selected);
                $_SESSION['last_action'] = $_POST['description'];
            }
        } else {
            array_push($village['eventi'], array('data' => $_POST['date'], 'descrizione' => $_POST['description']));
            write_village($village, $selected);
            $_SESSION['last_action'] = $_POST['description'];
        }
    }
    $alive = get_alive($village);
    $events = get_events($village);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit · masterus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    
<?php if(isset($village)) { ?>
    <header>
        <h2>
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Modifica <?php echo($village['nome']);?>
        </h2>

        <ul>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="./?v=<?php echo($village['id']);?>">Bacheca</a></li>
        </ul>
        <br>
        <ul>
            <li><a href="#alive">Giocatori</a></li>
            <li><a href="#events">Calendario</a></li>
        </ul>
    </header>

    <center>
    <span id="alive">
        <h4>Vivi: <?php echo($alive[0]);?></h4>
        <h4>Morti: <?php echo($alive[1]);?></h4>
    </span>
    <table id="players">
        <?php foreach($village['giocatori'] as $giocatore) { ?>
        <tr>
            <td><?php echo($giocatore['username']); ?></td>
            <td><?php echo($giocatore['ruolo']); ?></td>
            <td><?php echo($giocatore['in_vita']); ?></td>                    
        </tr>
        <?php } ?>
    </table>
       

        <div id="events">
            <?php foreach($events as $event) { ?>
                <span class="event">
                    <?php 
                        echo($event['data']);
                        echo($event['descrizione']);
                    ?>
                </span>
            <?php } ?>
        </div>
        
        <form action="" method="post">
            <h4>Nuova elezione</h4>
            <input type="date" name="date" id="date" required />
            <textarea name="description" placeholder="descrizione" required></textarea>
            <p class="legend">legenda</p>

            <button type="submit" formmethod="post">crea</button>

            <?php // var_dump($village); ?>
        </form>

        <form action="" method="post">
            <h4>Nuova morte</h4>
            <select name="giocatore">
                <?php
                    foreach ($village['giocatori'] as $player) {
                        echo("<option value='".$player['username']."'>".$player['username']."</option>");
                    }
                ?>
            </select>
            
            <p class="legend">legenda</p>

            <button type="submit" formmethod="post">uccidi</button>
        </form>   

<!-- ERRORI -->
<?php } if ($error != "") { ?>
    <h2 style="color:yellow;"><?php echo $error;?></h2>
<?php } ?>

    </center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>