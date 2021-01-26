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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masterus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h2>
        <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
        Masterus
    </h2>
<?php if(isset($_GET) and isset($_GET['v'])) { ?>
    <ul>
        <li><a href="#players">Giocatori</a></li>
        <li><a href="#days">Calendario</a></li>
    </ul>
<?php } ?>
</header>

<center>

<?php
    if (isset($_GET) and isset($_GET['v'])) {
        $json = file_get_contents('v/_all.json');
        $db = json_decode($json, true);
        
        // searching village json by id
        if(array_key_exists($_GET['v'], $db)) {
            $selected = $db[$_GET['v']];
            $village = read_village($selected);
            $alive = get_alive($village);
            $days = get_events($village);
            // print_r($village); 
?>

    <span id="players">
        <h2>Giocatori</h2>
        <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
    </span>
    <div id="players_list">
        <?php foreach($village['giocatori'] as $giocatore) { ?>
            <span class="player <?php if($giocatore['in_vita'] == "false") {echo("dead");} ?>">
                <?php echo($giocatore['username']); ?>
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
    
<?php
    } else { ?>
        <h2 style="color:yellow;">Villaggio non presente!</h2>
<?php }} else { ?> 
    <p> <!-- NO GET => HOME -->
        In costruzione... leggi i <a href="credits.html">credits</a> (ancora più in costruzione) <br><br>
        Sei un <a href="login.php">master</a>?
    </p>
<?php } ?> 
</center>

    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>