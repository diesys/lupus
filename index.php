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
    <title><?php if($village != NULL) {echo($village['nome']." | ");}?>Lupus</title>
    
    <?php if($village != NULL and $village['variante'] == "classic") { 
        echo(headerImport("classic"));
    } elseif(rand(0,1) == 1) {
        echo(headerImport("classic"));
    } else { 
        echo(headerImport("space"));
    } ?>
</head>

<body class="<?php $color = rand(0,4); echo("clr-".$color); ?>">
<?php if(isset($village)) { ?>
    <div id="bg" style="background-image: url('assets/img/bg/<?php echo($village['variante']."/".rand(0, 5)); ?>.jpg')"></div>
<?php } else { ?>
    <div id="bg" style="background-image: url('assets/img/bg/<?php if($seed == 0) {echo("space/");} else {echo("classic/");} echo(rand(0, 5)); ?>.jpg')"></div>
<?php } ?>

    <header>
    <?php if($village != NULL) { ?>
        <ul>
            <li><a href="#" class="logo"><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a></li>
            <li><a href="#players">
                <img src="assets/img/icons/people-24px.svg" alt="·" height="28" width="28">
                    <?php 
                        echo($village['nome']);
                        // if($village['variante'] == "space") 
                            // {echo("Colonia");}
                        // elseif($village['variante'] == "classic") 
                            // {echo("Villaggio");}
                    ?>
                </a>
            </li>
            <li><a href="#events">
                <img src="assets/img/icons/event-24px.svg" alt="·" height="28" width="28">
                Calendario</a>
            </li>
            <li><a href="regolamento.php?v=<?php if($village['variante'] == "space") {echo($village['variante']);} ?>">
                <img src="assets/img/icons/menu_book-24px.svg" alt="·" height="28" width="28">
                Regolamento</a>
            </li>
            <li><a target="_blank" href="https://t.me/<?php if(isset($village['master'])) {echo($village['master']);} ?>">
                <img src="assets/img/icons/chat-24px.svg" alt="·" height="28" width="28">
                Master</a>
            </li>
        </ul>
    <?php } else { ?>
        <h2 class="title">
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Lupus
        </h2>
    <?php } ?>
    </header>

    
    <center>

    <?php if($village != NULL) { ?>
        <span id="players">
            <h2>
                <?php 
                    if($village['variante'] == "space") 
                        {echo("Colonia");}
                    elseif($village['variante'] == "classic") 
                        {echo("Villaggio");}
                ?>
            </h2>
            <span>Vivi: <?php echo($alive[0]."/".intval($alive[0]+$alive[1]));?></span>
        </span>
        <div id="players_list">
            <?php foreach($village['giocatori'] as $giocatore) { ?>
                <a target="_blank" href="https://t.me/<?php echo($giocatore['username']); ?>" class="player username <?php if($giocatore['in_vita'] != "true") {echo(" dead");} ?>">
                    @<?php echo($giocatore['username']); ?>
                </a>
            <?php } ?>
        </div>

        <span id="events">
            <h2 class="full-width">Calendario</h2>
        </span>
        <div id="events_list">
            <p class="legend full-width">
                <span class="dot assassinato">assassati di notte</span>
                <span class="dot giustiziato">giustiziati di giorno</span>
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
                                echo("<small><a target='_blank' href='".$event['sondaggio']."'>voti</a></small>"); 
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
            In costruzione... sei un <a href="login.php">master</a>?
        </p>
        <p>
            Vedi il regolamento della nuovissima variante <a href="regolamento.php?v=space">Lupus in Space</a> 
            <!-- o la <a href="regolamento.php?v=classic">Classica</a> -->
        </p>
    <?php } ?> 
    </center>

    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede. <br>
            Leggi i <a href="credits.html">credits</a> (in costruzione)
        </p>
    </footer>
</body>
</html>