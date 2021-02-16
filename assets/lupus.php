<?php

// LIB ////////////////////////////////////////////////////////////////
function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function read_village($file_name) {
    if(file_exists('v/'.$file_name.'.json')) {
        $json = file_get_contents('v/'.$file_name.'.json');
        $data = json_decode($json, true);
        return $data;
    } else {
        return FALSE;
    }
}

// searching village json by id
function get_village($hash, $villages) {
    if(array_key_exists($hash, $villages)) {
        $selected = $villages[$hash];
        return read_village($selected);
    } else {
        return NULL;
    }
}

function write_village($data) {
    $json = json_encode($data);
    file_put_contents('v/'.$data['nome'].'.json', $json);
}

function new_village($file_name, $hash, $variante) {
    $data = array(
        'nome' => $_POST['new_name'],
        // 'telegram' => "",
        'variante' => $variante,
        'giorni' => array(array()),
        'giocatori' => array_fill(0, $_POST['players'], array("username" => "", "ruolo" => "","fazione" => "", "in_vita" => TRUE)),
        'id' => $hash
    );

    $new_json = 'v/'.$file_name.'.json';
    $new_village = json_encode($data);
    
    // no db
    if (!file_exists('v/_all.json')) {
        file_put_contents('v/_all.json', json_encode(array()));
    }
    $new_db = file_get_contents('v/_all.json');
    $all = json_decode($new_db, true);
    
    // new file
    if (!file_exists($new_json)) {
        // not present in db
        if(!array_key_exists($data['id'], $all)) {
            $all[$data['id']] = $data['nome'];
        } else {
            $data['id'] = substr($data['id'], 0, -2);
            $all[$data['id']] = $data['nome'];
        }
        file_put_contents($new_json, $new_village);
        file_put_contents('v/_all.json', json_encode($all));
    } else {
        $error = "Il file esiste!";
    }
}

function get_alive($village) {
    $alive = 0;
    $dead = 0;
    foreach($village['giocatori'] as $giocatore) {
        if($giocatore['in_vita'] == "true") {
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

function remove_event($village, $day_n, $event_i) {
    if(array_key_exists('giocatore', $village['giorni'][$day_n][$event_i])) {
        $village['giocatori'] = kill($village['giorni'][$day_n][$event_i]['giocatore'], $village, TRUE);
    }
    unset($village['giorni'][$day_n][$event_i]);
    write_village($village);

    // per le notti in caso che si fa?
}

function kill($username, $village, $undo = FALSE) {
    $giocatori = array();
    foreach ($village['giocatori'] as $player) {
        if ($player['username'] == $username) {
            $in_vita = $undo;  // normalmente uccide, se metti parametro undo TRUE resuscita ?
        } else {
            $in_vita = $player['in_vita'];
        }
        array_push($giocatori, array(
                'username' => $player['username'],
                'ruolo' => $player['ruolo'],
                'in_vita' => $in_vita
            ));
    }
    return $giocatori;
}

// MAIN ////////////////////////////////////////////////////////////////

// GAME
$roles = array(
    'classic' => array(
            // umani
            //array('angelo dei villici' => 'umani'),
            //array('condannato' => 'umani'),
            'contadino' => 'umani',
            'guardia del corpo' => 'umani',
            'gufo' => 'umani',
            //array('maga' => 'umani'),
            'massone' => 'umani',
            'medium' => 'umani',
            'mitomane' => 'umani',
            'romeo (+giulietta)' => 'umani',
            'giulietta (+romeo)' => 'umani',
            'veggente' => 'umani',
            
            // lupi
            'lupo' => 'lupi',
            'indemoniato' => 'lupi',

            // criceto
            'criceto mannaro' => 'criceti'
    ), 
    
    'space' => array(
            // colonia
            'apotecario' => 'colonia',
            'archivista' => 'colonia',
            'bioconvertito' => 'colonia',
            'carceriere' => 'colonia',
            'cartello droghe' => 'colonia',
            'colono' => 'colonia',
            'coroner' => 'colonia',
            'cyborg' => 'colonia',
            'hacker' => 'colonia',
            'ingegnere' => 'colonia',
            'panopticon' => 'colonia',
            'tecnochirurgo' => 'colonia',
            'sentinelle' => 'colonia',
            // amministrazione
            'burocrate' => 'amministrazione',
            'capo partito' => 'amministrazione',
            'ceo' => 'amministrazione',
            'portavoce' => 'amministrazione',

            // replicanti
            'collaboratore' => 'replicanti',
            'I.A.' => 'replicanti',
            'virus' => 'replicanti',
            'replicante' => 'replicanti',
            
            // ribelli
            'cancellatore' => 'ribelli',
            'coordinatore' => 'ribelli',
            'informatore' => 'ribelli',
            'lurker' => 'ribelli',
            'man in the middle' => 'ribelli',
            'sosia' => 'ribelli',
            
            // programmatori
            // 'programmatore' => 'programmatori',
            
            // simbionti
            'simbionte' => 'simbionti',

            // software
            'backdoor' => 'software',
            'backup' => 'software',
            'malware' => 'software',
            'rookit' => 'software',
            'swapper' => 'software'
    )
);

// SESSION
session_start();

// DB
$db_path = 'v/_all.json';
$password = "supul";
if(file_exists($db_path)) {
    $db = file_get_contents('v/_all.json');
    $villages = json_decode($db, true);
}

?>