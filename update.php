<?php
    include 'assets/masterus.php';

    // MAIN ///////////
    $error = "";

    if (isset($_GET) and isset($_GET['v'])) {
        $village = get_village($_GET['v'], $villages);
        $alive = get_alive($village);
        $days = get_events($village);
    }

    //// aggiungi evento
    if($_SESSION['logged_in'] == TRUE and isset($village) and isset($_POST) and isset($_POST['type'])) {
        // NUOVA NOTTE
        if($_POST['type'] == "notte") {
            $day = count($village['giorni']);
            array_push($village['giorni'], array());
        } elseif(($_POST['type'] == "assassinato" or $_POST['type'] == "giustiziato")) {
            if(!isset($_POST['day'])) {
                $day = count($village['giorni'])-1;
            } else {
                $day = $_POST['day'];
            }
            // aggiorna il giocatore morto
            $village['giocatori'] = kill($_POST['player'], $village);
        }

        if(isset($_POST['description']) and isset($_POST['player'])) {
            if($_POST['type'] != "notte") {
                // mettere qui aggiorna giocatore morto
                array_push($village['giorni'][$day], array(
                    'tipo' => $_POST['type'], 
                    'descrizione' => $_POST['description'],
                    'giocatore' => $_POST['player']));
            }
        }        
        write_village($village);
    }

    header("Location: edit.php?v=".$village['id']);

?>