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
        $days = array();
        foreach($village['giorni'] as $day) {
            array_push($days, $day);
        } return $days;
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
        } else {
            $error = "Village not present!";
        }
    }

    //// aggiungi evento
    if(isset($village) and isset($_POST) and isset($_POST['day']) and isset($_POST['description']) and isset($_POST['type']) and isset($_POST['player'])) {
        // NUOVA NOTTE
        if($_POST['type'] == "notte") {
            $_POST['day'] = intval($_POST['day']+1);
            array_push($village['giorni'], array());
        }
        // prevent double submits # 1/2
        if(isset($_SESSION['last_action'])) {
            if(substr($_POST['description'], 0, 20)."#".$_POST['day']."#".$_POST['type'] != $_SESSION['last_action']) {
                array_push($village['giorni'][$_POST['day']], array(
                                                'tipo' => $_POST['type'], 
                                                'descrizione' => $_POST['description'],
                                                'giocatore' => $_POST['player']));
                write_village($village, $selected);
                // prevent double submits #2/2
                $_SESSION['last_action'] = substr($_POST['description'], 0, 20)."#".$_POST['day']."#".$_POST['type'];
            }
        } else {
            array_push($village['giorni'][$_POST['day']], array(
                'tipo' => $_POST['type'], 
                'descrizione' => $_POST['description'],
                'giocatore' => $_POST['player']));
            write_village($village, $selected);
            $_SESSION['last_action'] = $_POST['description'];
        }
    }
    $alive = get_alive($village);
    $days = get_events($village);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica | Masterus</title>
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
            <li><a href="v/<?php echo($village['nome']);?>.json" download>Scarica</a></li>
            <!-- <li><a href="v/<?php// echo($village['nome']);?>.json" download>Carica</a></li> -->
            <li><a href="#players">Giocatori</a></li>
            <li><a href="#events">Calendario</a></li>
        </ul>
    </header>

    <center>
        <form action="" method="post">
            <h4 class="full-width">Aggiungi al calendario</h4>
            <select name="day" required>
                <?php foreach ($days as $j => $day) {
                    echo("<option value='".$j."'>Giorno ".intval($j+1)."</option>");
                } ?>
            </select>
            <select name="type" id="type" required>
                <option value="notte">Nuovo giorno</option>
                <option value="assassinato">Assassinio notturno</option>
                <option value="giustiziato">Condanna diurna</option>
                <option value="votazione">Votazione</option>
            </select>
            <select name="player" required>
                <option value=" " selected>Nessuno</option>
                <?php foreach ($village['giocatori'] as $player) {
                   echo("<option value='".$player['username']."'>".$player['username']."</option>");
                } ?>
            </select>
            <textarea class="full-width" name="description" placeholder="descrizione" col="8" required></textarea>

            <button type="submit" formmethod="post">aggiungi</button>
        </form>

        <!-- <form action="" method="post">
            <h4 class="full-width">Rimuovi dal calendario</h4>
            <select name="id_evento">
                <?php
                // foreach ($days as $day) {
                //     foreach ($day as $number => $event) {
                //         echo("<option value='".$number." ".$event['tipo']."'>".intval($number+1).") ".$event['tipo']."</option>");;
                //     }
                // } 
                ?>
            </select>

            <button type="submit" formmethod="post">elimina</button>
        </form> -->


        <span class="full-width" id="players">
            <h2>Giocatori</h2>
            <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
        </span>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
            <span class="player <?php if($giocatore['in_vita'] == "false") {echo("dead");} ?>">
                <span><?php echo($giocatore['username']); ?></span>
                <span>(<?php echo($giocatore['ruolo']); ?>)</span>
            </span>
            <?php } ?>
        </div>
       
        <span id="events">
            <h2 class="full-width">Calendario</h2>
        </span>
        <div id="events_list">
            <?php foreach ($days as $i => $day) { ?>
                <span class="day">
                    <span class="date">
                        Giorno <?php echo(intval($i+1));?>
                    </span>

                <?php foreach ($day as $event) { if($event) {?>
                    <span class="event <?php echo($event['tipo']);?>">
                        <span class="description">
                            <?php echo($event['descrizione']);?>
                        </span> 
                    </span>
                <?php }} ?>
                </span>
            <?php } ?>
        </div>

<!-- ERRORI -->
<?php } if ($error != "") { ?>
    <h2 style="color:yellow;"><?php echo $error;?></h2>
<?php } ?>


        <p class="legend">
            <span class="dot assassinato">assassinati dai lupi</span> - <span class="dot giustiziato">giustiziati dal villaggio</span>
        </p>
    </center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>