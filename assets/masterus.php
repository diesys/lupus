<?php 
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

function new_village($file_name, $hash) {
    $data = array(
        'nome' => $_POST['new_name'],
        'telegram' => "",
        'giorni' => array(array()),
        'giocatori' => array_fill(0, $_POST['players'], array("username" => "", "ruolo" => "", "in_vita" => TRUE)),
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

function kill($username, $village) {
    $giocatori = array();
    foreach ($village['giocatori'] as $player) {
        if ($player['username'] == $username) {
            $in_vita = false;
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

// sessione
if(!isset($_SESSION)){
    session_start();
    $_SESSION['key'] = generateRandomString(6);
}

// DB
$db_path = 'v/_all.json';
$password = "supul";

if(file_exists($db_path)) {
    $db = file_get_contents('v/_all.json');
    $villages = json_decode($db, true);
}

?>