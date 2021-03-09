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
        'conclusa' => 0,
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

function headerImport($variante = "space") {
    $head = "<meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' href='assets/img/favicon.ico' />
    <link rel='preconnect' href='https://fonts.gstatic.com'>    
    <link href='https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='assets/css/style.css'>
    <script type='text/javascript' defer src='assets/js/lupus.js'></script>

    <meta property='og:title' content='Lupus in Space'>
    <meta property='og:description' content='Un semplice assistente per giocare a distanza a Lupus, con la una variante inedita con cyborg, replicanti, IA, CEO...'>
    <meta property='og:image' content='assets/img/amarok.png'>
    <meta property='og:url' content='https://flowin.space/lupus'>
    <meta name='twitter:card' content='summary_large_image'>

    <meta property='og:site_name' content='Lupus in Space'>
    <meta name='twitter:image:alt' content='Lupus in Space'>";
    
    if($variante == "space") {
        $head .= "\n<link rel='stylesheet' href='assets/css/space.css'>";
    } elseif ($variante == "classic") {
        $head .= "\n<link rel='stylesheet' href='assets/css/classic.css'>";
    }
    return $head;
}

function themeSelector($redir_url) {
    echo("<span onclick='document.querySelector(\"#theme_selector\").classList.toggle(\"active\")' class='link' id='theme_selector_toggle'><img src='assets/img/icons/palette-24px.svg' alt='·' height='32' width='32'></span>");
    echo("<div id='theme_selector'>");
    echo("  <ul id='colors_list'>");
    if(isset($_SESSION) and isset($_SESSION['color']) and intval($_SESSION['color']) == -1) {
        $random = "selected";
    } else {
        $random = "";
    } echo("    <li onclick='updateColor(-1);' class='selector_entry se-random $random'><img src='assets/img/icons/shuffle-24px.svg' alt='·' height='26' width='26'></span></li>");
    for($i=0; $i<5; $i++) {
        if(isset($_SESSION) and isset($_SESSION['color']) and intval($_SESSION['color']) == $i) {
            $selected = "selected";
        } else {
            $selected = "";
        } echo("    <li onclick='updateColor($i);' class='selector_entry se-$i $selected'><span class='primary'></span><span class='secondary'></li>");
    }
    echo("  </ul>");
    echo("  <ul id='bgs_list'>");
    if(isset($_SESSION) and isset($_SESSION['image']) and intval($_SESSION['image']) == -1) {
        $random = "selected";
    } else {
        $random = "";
    } echo("    <li onclick='updateImage(-1);' class='selector_entry se-random $random'><img src='assets/img/icons/shuffle-24px.svg' alt='·' height='26' width='26'></span></li>");
    for($i=0; $i<5; $i++) {
        if(isset($_SESSION) and isset($_SESSION['image']) and intval($_SESSION['image']) == $i){
            $selected = "selected";
        } else {
            $selected = "";
        } echo("    <li onclick='updateImage($i);' class='selector_entry se-$i $selected'></span></li>");
    }
    echo("  </ul>");
    echo("<button onclick='document.querySelector(\"#theme_form\").submit()' type='submit' value='salva'>salva</button>");
    echo("</div>");
    
    // form
    echo("<form action='assets/updateSession.php' id='theme_form' method='post' style='display: none;'>");
    echo("<input id='theme_color' type='number' name='color' value='' hidden>");
    echo("<input id='theme_image' type='number' name='image' value='' hidden>");
    echo("<input id='theme_parent_url' name='parent_url' value='../$redir_url' hidden required>");
    echo("</form>");

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
            'manovale' => 'colonia',
            'coroner' => 'colonia',
            'cyborg' => 'colonia',
            'hacker' => 'colonia',
            'ingegnere' => 'colonia',
            'panopticon' => 'colonia',
            'paranoico' => 'colonia',
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
            'traditore' => 'replicanti',
            
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