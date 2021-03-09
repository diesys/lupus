<?php
    include 'assets/lupus.php';

    // MAIN ///////////
    $error = "";

    if (isset($_GET) and isset($_GET['v'])) {
        $village = get_village($_GET['v'], $villages);
    }

    // AGGIUNGI/RIMUOVI EVENTO
    if($_SESSION['logged_in'] == TRUE and isset($village) and isset($_POST)) {
        //// NUOVO EVENTO
        if(isset($_POST['type'])) {
            // NUOVA NOTTE e calcolo giorno
            if($_POST['type'] == "notte") {
                $day = count($village['giorni']);
                array_push($village['giorni'], array());
            } else {
                if(!isset($_POST['day'])) { $day = count($village['giorni'])-1; } 
                else { $day = $_POST['day']; }
                // nuovo evento da creare (se != notte)
                $new_event = array('tipo' => $_POST['type']);

                // uccide il giocatore selezioneto (giorno o notte)
                if(($_POST['type'] == "assassinato" or $_POST['type'] == "giustiziato")) {
                    $village['giocatori'] = kill($_POST['player'], $village);
                }

                // popola il nuovo evento
                if(isset($_POST['player'])) { $new_event['giocatore'] = $_POST['player']; }
                if(isset($_POST['description'])) { $new_event['descrizione'] = $_POST['description']; } 
                if(isset($_POST['poll_url'])) { $new_event['sondaggio'] = $_POST['poll_url']; }

                // aggiunge l'evento al calendario
                array_push($village['giorni'][$day], $new_event);
            }
            write_village($village);
        }
    }

    //// elimina evento
    if(array_key_exists('id_evento',$_POST)) {
        // as: " $indice_giorno # $indice_evento "
        $id = explode('#', $_POST['id_evento']);
        if(count($id) > 1) { // giustiziato o assassinato
            // elimina evento
            $village['giorni'] = remove_event($village, $id[0], $id[1]);
        }
        // notte come si fa? si puo' anche ignorare... unico problema coi voti e ballottaggio?
    }

    //// concludi partita
    if(array_key_exists('conclusa', $_POST)) {
        $village['conclusa'] = $_POST['conclusa'];
        write_village($village);
    }   

    header("Location: edit.php?v=".$village['id']);
?>