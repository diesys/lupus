<?php
    include 'assets/lupus.php';

    // MAIN ///////////
    $error = "";

    if (isset($_GET) and isset($_GET['v']) and array_key_exists($_GET['v'], $villages)) {
        $village = get_village($_GET['v'], $villages);
        $alive = get_alive($village);
        $days = get_events($village);
    } elseif(isset($_GET['v'])) {
        $village = NULL;
        $error = "Villaggio non trovato!";
    } else {
        $village = NULL;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
    <title><?php if($village != NULL) {echo("Villaggio ".$village['nome']." | ");}?>Lupus</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if($village != NULL and $village['variante'] == "space") { ?>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } elseif($village != NULL and $village['variante'] == "classic") { ?>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } elseif(rand(0,1)%2 == 0) { ?>
        <link rel="stylesheet" href="assets/css/space.css">
    <?php } else { ?>
        <link rel="stylesheet" href="assets/css/classic.css">
    <?php } ?>
</head>

<body style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')">
    <header>
        <h2>
            <a href="#"><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
            <?php if($village != NULL) {echo("Villaggio ".$village['nome']);} else { echo("Lupus");}?>
        </h2>
    <?php if($village != NULL) { ?>
        <ul>
            <li><a href="#players">Giocatori</a></li>
            <li><a href="#events">Calendario</a></li>
        <?php if($village['variante'] == "space") { ?>
            <li><a href="regolamento.php?v=space">Regolamento</a></li>
        <?php } else { ?>
            <li><a href="regolamento.php?v=classic">Regolamento</a></li>
        <?php } ?>
        </ul>
    <?php } ?>
    </header>

    <center>
    <?php if($village != NULL) { ?>
        <span id="players">
            <h2>Giocatori</h2>
            <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
        </span>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
                <a target="_blank" href="https://t.me/<?php echo($giocatore['username']); ?>" class="player username <?php if($giocatore['in_vita'] != "true") {echo("dead");} ?>">
                    @<?php echo($giocatore['username']); ?>
                </a>
            <?php } ?>
        </div>

        <span id="events">
            <h2 class="full-width">Calendario</h2>
        </span>
        <div id="events_list">
            <p class="legend full-width">
                <span class="dot assassinato">assassati di notte</span> - <span class="dot giustiziato">giustiziati di giorno</span>
            </p>

            <?php $n_days = count($days);
                 foreach (array_reverse($days) as $i => $day) { ?>
                <span class="day">
                    <span class="date">Giorno <?php echo(intval($n_days - $i));?></span>
                <?php foreach ($day as $event) { if($event) {?>
                    <span class="event <?php echo($event['tipo']);?>" data-type="<?php echo(" ".$event['tipo']);?>">
                        <span class="description">
                        <?php if(isset($event['giocatore'])) { ?>
                            <a target="_blank" href="https://t.me/<?php echo($event['giocatore']); ?>">@<?php echo($event['giocatore']); }?></a>
                            <?php echo(" ".$event['descrizione']);?>
                            <?php if(isset($event['sondaggio']) and $event['sondaggio'] != "") { 
                                echo("<small>voti: <a target='_blank' href='".$event['sondaggio']."'>".$event['sondaggio']."</a></small>"); 
                            }?>
                        </span> 
                    </span>
                <?php }} ?>
                </span>
            <?php } ?>
        </div>
        
    <?php } else { 

        // ERRORE
        if($error != "") { ?>
            <h2 class="error"><?php echo($error); ?></h2>
        <?php } ?> 

        <!-- HOMEPAGE -->
        <p>
            In costruzione... leggi i <a href="credits.html">credits</a> (ancora più in costruzione) <br><br>
            Sei un <a href="login.php">master</a>?
        </p>
        <p>Vedi la nuovissima variante <a href="regolamento.php?v=space">Lupus in Space</a> o la <a href="regolamento.php?v=classic">Classica</a></p>
    <?php } ?> 
    </center>

    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>