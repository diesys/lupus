<?php
    include 'assets/lupus.php';

    // MAIN ///////////
    $error = "";

    if ($_SESSION['logged_in'] == TRUE and isset($_GET) and isset($_GET['v']) and isset($villages)) {
        if (array_key_exists($_GET['v'], $villages)) {
            $village = get_village($_GET['v'], $villages);
            $alive = get_alive($village);
            $days = get_events($village);
        } else {
            $error = "Villaggio non trovato!";
        }
    } else {
        $error = "Sicuro di essere un <a href='login.php'>master</a> o che il villaggio <a href='admin.php'>esista</a>?";
    }

    // update giocatori da populate.php$_POST['id_evento']
    // le chiavi sono della forma: key#n , con n ordinale
    if($_SESSION['logged_in'] == TRUE and (isset($_POST) and isset($_POST['username#0']) and !isset($_POST['new_name']))) {
        $giocatori = array(array());
        foreach($_POST as $key => $value) {
            $chiave = explode('#', $key)[0];
            $giocatore_n = explode('#', $key)[1];
            $giocatori[$giocatore_n][$chiave] = $value;
            // aggiorna le fazioni
            if($chiave == "ruolo") {
                $giocatori[$giocatore_n]['fazione'] = $roles[$village['variante']][$value];
            }
            // $giocatori[explode('#', $key)[1]][explode('#', $key)[0]] = $value;
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
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
    <title>Gestisci <?php echo($village['nome']);?> | Lupus</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if($village['variante'] == "space") { ?>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } elseif($village['variante'] == "classic") { ?>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } ?>

    <script>
        function fixSelect() { // da fare meglio magari..
            tipo = document.querySelector('#type_select');
            if(tipo.options[tipo.selectedIndex].value == "notte") {
                document.querySelector('#day_select').setAttribute('disabled', '1');
                document.querySelector('#player_select').setAttribute('disabled', '1');
                document.querySelector('#description').setAttribute('disabled', '1');
                document.querySelector('#poll_url').setAttribute('disabled', '1');
            } else if(tipo.options[tipo.selectedIndex].value == "assassinato") {
                document.querySelector('#day_select').removeAttribute('disabled');
                document.querySelector('#player_select').removeAttribute('disabled');
                document.querySelector('#description').removeAttribute('disabled');
                document.querySelector('#poll_url').setAttribute('disabled', '1');
            } else if(tipo.options[tipo.selectedIndex].value == "giustiziato") {
                document.querySelector('#day_select').removeAttribute('disabled');
                document.querySelector('#player_select').removeAttribute('disabled');
                document.querySelector('#description').removeAttribute('disabled');
                document.querySelector('#poll_url').removeAttribute('disabled');
            } else if(tipo.options[tipo.selectedIndex].value == "comunicazione") {
                document.querySelector('#day_select').removeAttribute('disabled');
                document.querySelector('#player_select').setAttribute('disabled', '1');
                document.querySelector('#description').removeAttribute('disabled');
                document.querySelector('#poll_url').removeAttribute('disabled');
            }       
        }
    </script>
</head>

<body style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')">
    <header>
        <h2>
            <?php if(isset($village['nome'])) echo($village['nome']);?>
        </h2>
        
        <ul>
            <li><a href="#" class="logo"><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a></li>
            <li><a href="admin.php">
                <img src="assets/img/icons/view_list-24px.svg" alt="·" height="28" width="28">
                Villaggi</a>
            </li>
        <?php if(isset($village['id']) and isset($village['nome'])) { ?>
            <li><a href="./?v=<?php echo($village['id']); ?>">
                <img src="assets/img/icons/public-24px.svg" alt="·" height="28" width="28">    
                Bacheca</a>
            </li>
            <li><a href="v/<?php echo($village['nome']); ?>.json" download>
                <img src="assets/img/icons/download-24px.svg" alt="·" height="28" width="28">
                Download</a>
            </li>
            <!-- <li><a href="v/<?php// echo($village['nome']);?>.json" download>Carica</a></li> -->
            <li><a href="assets/logout.php">
                <img src="assets/img/icons/logout-24px.svg" alt="·" height="28" width="28">
                Logout</a>
            </li>
            <!-- <li><a href="#players">Giocatori</a></li>
            <li><a href="#events">Calendario</a></li> -->
        <?php } ?>
        </ul>
    </header>

    <center>
    
    <?php if(isset($village) and $error == "") { ?>
        <form action="update.php?v=<?php echo($village['id']); ?>" method="post">
            <h4 class="full-width">Aggiungi al calendario</h4>
            <span>
                <label for="type">Tipo</label>
                <select onchange="fixSelect();" name="type" id="type_select" required>
                    <option value="comunicazione">Comunicazione</option>
                    <option value="notte">Nuovo giorno</option>
                    <option value="assassinato">Assassinio notturno</option>
                    <option value="giustiziato">Condanna diurna</option>
                    <!-- <option value="votazione">Votazione</option> -->
                </select>
            </span>
            <span>
                <label for="day">Giorno</label>
                <select name="day" id="day_select" required>
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
            <textarea id="description" class="half-width" name="description" placeholder="descrizione (opzionale)" rows="2"></textarea>
            <input type="text" id="poll_url" name="poll_url" value="" placeholder="URL poll">
            
            <button type="submit" formmethod="post">aggiungi</button>
        </form>

        <form action="update.php?v=<?php echo($village['id']); ?>" method="post">
            <h4 class="full-width">Rimuovi dal calendario</h4>
            <select name="id_evento">
                <?php
                foreach ($days as $n => $day) {
                    foreach ($day as $i => $event) {
                        // echo("<option value='".$n."#".$event['tipo']."#".$event['giocatore']."'>".intval($n+1).") ".$event['giocatore']." (".$event['tipo'].")</option>");;
                        echo("<option value='".$n."#".$i."'>".intval($n+1).".".intval($i+1)." ".$event['tipo']."</option>");
                    }
                } 
                ?>
            </select>

            <button type="submit" formmethod="post">elimina</button>
        </form>


        <span class="full-width" id="players">
            <h2><a href="populate.php?v=<?php echo($village['id']); ?>">Giocatori</a></h2>
            <!-- <p><a href="populate.php?v=<?php echo($village['id']); ?>">modifica</a></p> -->
            <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
        </span>
        <p class="legend full-width">
        <?php if($village['variante'] == "space") { ?>
            <span class="dot colonia">colonia</span>
            <span class="dot ribelli">ribelli</span>
            <span class="dot software">software</span>
            <span class="dot simbionti">simbionti</span>
            <span class="dot programmatori">programmatori</span>
        <?php } else { ?>
            <span class="dot umani">umani</span>
            <span class="dot lupi">lupi</span>
            <span class="dot criceti">criceti</span>
        <?php } ?>
        </p>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
            <span class="player <?php echo($giocatore['ruolo']." ".$giocatore['fazione']); if($giocatore['in_vita'] != "true") {echo(" dead");} ?>">
                <a class="username" target="_blank" href="https://t.me/<?php echo($giocatore['username']); ?>">@<?php echo($giocatore['username']); ?></a>
                <small class="role">(<?php echo($giocatore['ruolo']); ?>)</small>
            </span>
            <?php } ?>
        </div>
       
        <span id="events">
            <h2 class="full-width">Calendario</h2>
        </span>
        <p class="legend full-width">
            <span class="dot assassinato">assassati di notte</span> · <span class="dot giustiziato">giustiziati di giorno</span>
        </p>
        <div id="events_list">
            <?php $n_days = count($days); 
                foreach (array_reverse($days) as $i => $day) { //if(count($day) > 0) { ?>
                <span class="day">
                    <span class="date">
                        Giorno <?php echo(intval($n_days - $i));?>
                    </span>

                <?php foreach ($day as $j => $event) { if($event) {?>
                    <span class="event <?php echo($event['tipo']);?>" data-type="<?php echo("id: ".intval($n_days - $i).".".intval($j+1)." (".$event['tipo'].")");?>">
                        <span class="description">
                        <?php if(isset($event['giocatore'])) { ?>
                            <a target="_blank" href="https://t.me/<?php echo($event['giocatore']); ?>">@<?php echo($event['giocatore']); }?></a>
                            <?php echo(" ".$event['descrizione']);?>
                            <?php if(isset($event['sondaggio']) and $event['sondaggio'] != "") { 
                                echo("<small>(<a target='_blank' href='".$event['sondaggio']."'>voti</a>)</small>"); 
                            }?>
                        </span> 
                    </span>
                <?php }} ?>
                </span>
            <?php }//} ?>
        </div>

<!-- ERRORI -->
<?php } elseif ($error != "") { ?>
    <h2 class="error"><?php echo $error;?></h2>
<?php } ?>

    </center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>