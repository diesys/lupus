<?php
    include 'assets/masterus.php';
    
    // sessione
    session_start();
    $error = "";

    if ($_SESSION['logged_in'] == TRUE and isset($_GET['v'])) {
        // searching village json by id
        if(array_key_exists($_GET['v'], $villages)) {
            $selected = $villages[$_GET['v']];
            $village = read_village($selected);
        } else {
            $error = "Village not present!";
        }
    }

    //// aggiungi evento
    if(isset($village) and isset($_POST) and isset($_POST['type'])) {  // and isset($_POST['day']) and isset($_POST['description']) and isset($_POST['player'])
        // NUOVA NOTTE
        // prevent double submits # 1/2
        if($_POST['type'] == "notte" and ($_SESSION['last_action'] != substr($_POST['description'], 0, 20)."#".$_POST['type'])) {
            $day = count($village['giorni']);
            array_push($village['giorni'], array());
            write_village($village);
            $_SESSION['last_action'] = substr($_POST['description'], 0, 20)."#".$day."#".$_POST['type'];
        } else {
            if(!isset($_POST['day'])) {
                $day = count($village['giorni'])-1;
            } else {
                $day = $_POST['day'];
            }
        }
        // prevent double submits # 1/2
        if(($_POST['type'] == "assassinato" or $_POST['type'] == "giustiziato") and $_SESSION['last_action'] != substr($_POST['description'], 0, 20)."#".$day."#".$_POST['type']."#".$_POST['player']) {
            // aggiorna il giocatore morto
            $village['giocatori'] = kill($_POST['player'], $village);
            write_village($village);
            $_SESSION['last_action'] = substr($_POST['description'], 0, 20)."#".$day."#".$_POST['type']."#".$_POST['player'];
        }

        // prevent double submits # 1/2
        if(isset($_SESSION['last_action'])) {
            if(substr($_POST['description'], 0, 20)."#".$day."#".$_POST['type'] != $_SESSION['last_action']) {
                if($_POST['type'] != "notte") {
                    // mettere qui aggiorna giocatore morto
                    array_push($village['giorni'][$day], array(
                        'tipo' => $_POST['type'], 
                        'descrizione' => $_POST['description'],
                        'giocatore' => $_POST['player']));
                }

                write_village($village);
                // prevent double submits #2/2
                $_SESSION['last_action'] = substr($_POST['description'], 0, 20)."#".$_POST['day']."#".$_POST['type'];
            }
        } else {
            if($_POST['type'] != "notte") {
                array_push($village['giorni'][$_POST['day']], array(
                    'tipo' => $_POST['type'], 
                    'descrizione' => $_POST['description'],
                    'giocatore' => $_POST['player']));
            }
            write_village($village);
            $_SESSION['last_action'] = substr($_POST['description'], 0, 20)."#".$_POST['day']."#".$_POST['type'];
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
    <title>Gestisci <?php echo($village['nome']);?> | Masterus</title>
    <link rel="stylesheet" href="assets/style.css">

    <script>
        function fixSelect() {
            tipo = document.querySelector('#type_select');
            if(tipo.options[tipo.selectedIndex].value == "notte") {
                document.querySelector('#player_select').setAttribute('disabled', '1');
                document.querySelector('#day_select').setAttribute('disabled', '1');
            } else {
                document.querySelector('#player_select').removeAttribute('disabled');
                document.querySelector('#day_select').removeAttribute('disabled');
            }
        }
    </script>

</head>
<body>
    
<?php if(isset($village)) { ?>
    <header>
        <h2>
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Gestisci <?php echo($village['nome']);?>
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
            <span>
                <label for="type">Tipo</label>
                <select onchange="fixSelect();" name="type" id="type_select" required>
                    <option value="notte">Nuovo giorno</option>
                    <option value="assassinato">Assassinio notturno</option>
                    <option value="giustiziato">Condanna diurna</option>
                    <!-- <option value="votazione">Votazione</option> -->
                </select>
            </span>
            <span>
                <label for="day">Giorno</label>
                <select name="day" id="day_select" required disabled>
                    <?php $n_days = count($days); 
                     foreach (array_reverse($days) as $j => $day) {
                        echo("<option value='".intval($n_days-$j-1)."'>Giorno ".intval($n_days-$j)."</option>");
                    } ?>
                </select>
            </span>
            <span>
                <label for="player">Gicatore</label>
                <select name="player" id="player_select" required disabled>
                    <?php foreach ($village['giocatori'] as $player) { if($player['in_vita'] == "true") {
                        echo("<option value='".$player['username']."'>".$player['username']."</option>");
                    }} ?>
                </select>
            </span>
            <textarea class="half-width" name="description" placeholder="descrizione (opzionale)" rows="2"></textarea>

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
            <p>
                <a href="populate.php?v=<?php echo($village['id']); ?>">modifica</a>
            </p>
            <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
        </span>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
            <span class="player <?php if($giocatore['in_vita'] != "true") {echo("dead");} ?>">
                <a target="_blank" href="https://t.me/<?php echo($giocatore['username']); ?>">@<?php echo($giocatore['username']); ?></a>
                <span>(<?php echo($giocatore['ruolo']); ?>)</span>
            </span>
            <?php } ?>
        </div>
       
        <span id="events">
            <h2 class="full-width">Calendario</h2>
        </span>
        <p class="legend full-width">
            <span class="dot assassinato">assassinati dai lupi</span> - <span class="dot giustiziato">giustiziati dal villaggio</span>
        </p>
        <div id="events_list">
            <?php $n_days = count($days); 
                foreach (array_reverse($days) as $i => $day) { //if(count($day) > 0) { ?>
                <span class="day">
                    <span class="date">
                        Giorno <?php echo(intval($n_days - $i));?>
                    </span>

                <?php foreach ($day as $event) { if($event) {?>
                    <span class="event <?php echo($event['tipo']);?>" data-type="<?php echo(" ".$event['tipo']);?>">
                        <span class="description">
                            <a target="_blank" href="https://t.me/<?php echo($event['giocatore']); ?>">@<?php echo($event['giocatore']); ?></a>
                            <?php echo(" ".$event['descrizione']);?>
                        </span> 
                    </span>
                <?php }} ?>
                </span>
            <?php }//} ?>
        </div>

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