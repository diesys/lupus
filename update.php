<?php
    include 'assets/cyberlupus.php';

    // MAIN ///////////
    $error = "";

    if (isset($_GET) and isset($_GET['v'])) {
        $village = get_village($_GET['v'], $villages);
        // $alive = get_alive($village);
        // $days = get_events($village);
    }

    // AGGIUNGI RIMUOVI EVENTO
    if($_SESSION['logged_in'] == TRUE and isset($village) and isset($_POST)) {

    //// AGGIUNGI EVENTO
        if(isset($_POST['type'])) {
            // NUOVA NOTTE
            if($_POST['type'] == "notte") {
                $day = count($village['giorni']);
                array_push($village['giorni'], array());
            } else {
                if(!isset($_POST['day'])) {
                    $day = count($village['giorni'])-1;
                } else {
                    $day = $_POST['day'];
                }

                if(($_POST['type'] == "assassinato" or $_POST['type'] == "giustiziato")) {
                    // aggiorna il giocatore morto
                    $village['giocatori'] = kill($_POST['player'], $village);
                } elseif($_POST['type'] == "comunicazione" and isset($_POST['description'])) {
                    array_push($village['giorni'][$day], array(
                        'tipo' => $_POST['type'],
                        'descrizione' => $_POST['description']));
                }
            }

            // NUOVA UCCISIONE
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
    }

    //// elimina evento
    if(array_key_exists('id_evento',$_POST)) {
        // as: $giorno # $tipo # $username
        $id = explode('#', $_POST['id_evento']);
        if(count($id) > 2) { // giustiziato o assassinato
            // elimina evento
            $village['giorni'][$id[0]] = remove_event($village, $id[0], $id[1], $id[2]);
            // resuscita giocatore
            $village['giocatori'] = kill($id[2], $village, TRUE);
            write_village($village);
        }
        //} // else { // notte? }    
        // notte come si fa? si puo' anche ignorare... unico problema coi voti e ballottaggio?
    }
        

    header("Location: edit.php?v=".$village['id']);
?>