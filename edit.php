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
    if(isset($village) and isset($_POST) and isset($_POST['date']) and isset($_POST['description']) and isset($_POST['time']) and isset($_POST['type']) and isset($_POST['player'])) {
        // prevent double submits # 1/2
        if(isset($_SESSION['last_action'])) {
            if(substr($_POST['description'], 0, 20).$_POST['date'].$_POST['time'] != $_SESSION['last_action']) {
                array_push($village['eventi'], array(
                                                'data' => $_POST['date'], 
                                                'ora' => $_POST['time'],
                                                'tipo' => $_POST['type'], 
                                                'descrizione' => $_POST['description'],
                                                'giocatore' => $_POST['player']));
                write_village($village, $selected);
                // prevent double submits #2/2
                $_SESSION['last_action'] = substr($_POST['description'], 0, 20).$_POST['date'].$_POST['time'];
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
            <li><a href="#players">Giocatori</a></li>
            <li><a href="#events">Calendario</a></li>
        </ul>
    </header>

    <center>

        <form action="" method="post">
            <h4 class="full-width">Aggiungi al calendario</h4>
            <input type="date" name="date" id="date" min="2020-01-01" max="2025-01-01" required />
            <input type="time" name="time" id="time" required />
            <select name="type" id="type" required>
                <option value="ucciso_notte">Assassinio notturno</option>
                <option value="ucciso_giorno">Condanna diurna</option>
                <option value="votazione">Esito votazione</option>
                <option value="notte">Notte</option>
            </select>
            <select name="player" required>
                <option value="_" selected>Nessuno</option>
                <?php foreach ($village['giocatori'] as $player) {
                   echo("<option value='".$player['username']."'>".$player['username']."</option>");
                } ?>
            </select>
            <textarea class="full-width" name="description" placeholder="descrizione" col="8" required></textarea>

            <button type="submit" formmethod="post">crea</button>
        </form>

        <form action="" method="post">
            <h4 class="full-width">Rimuovi dal calendario</h4>
            <select name="id_evento">
                <option value="" disabled selected>data e ora</option>
                <?php foreach ($village['eventi'] as $evento) {
                   echo("<option value='".$evento['data']."_".$evento['ora']."'>".$evento['data']." | ".$evento['ora']."</option>");
                } ?>
            </select>

            <button type="submit" formmethod="post">elimina</button>
        </form>


        <span id="players">
            <h2>Giocatori</h2>
            <span>Vivi: <?php echo($alive[0]);?> - Morti: <?php echo($alive[1]);?></span>
        </span>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
            <span class="player <?php if(!$giocatore['in_vita']) {echo("dead");} ?>">
                <span><?php echo($giocatore['username']); ?></span>
                <span>(<?php echo($giocatore['ruolo']); ?>)</span>
            </span>
            <?php } ?>
        </div>
       
        <span id="events">
            <h2>Calendario</h2>
            <!-- <span>oggi: <?php //echo($alive[0]);?></span> -->
        </span>
        <div id="events_list">
            <?php foreach($events as $event) { ?>
                <span class="event <?php echo($event['tipo']);?>">
                    <span class="date">
                        <?php echo($event['data']." - ".$event['ora']);?>
                    </span>
                    <span class="description">
                        <?php echo($event['descrizione']);?>
                    </span>
                </span>
            <?php } ?>
        </div>
         

        <!-- <form action="" method="post">
            <h4 class="full-width">Giocatori in vita</h4>
            <select name="giocatore">
                <?php// foreach ($village['giocatori'] as $player) {
                       // echo("<option value='".$player['username']."'>".$player['username']."</option>");
                   // } ?>
            </select>
            <button type="submit" class="alivebtn hidden" formmethod="post">resuscita</button>
            <button type="submit" class="alivebtn" formmethod="post">uccidi</button>
        </form>    -->

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