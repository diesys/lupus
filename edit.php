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

    function write_village($data, $file_name) {
        $json = json_encode($data);
        file_put_contents('v/'.$file_name.'.json', $json);
    }

    //////////////////////
    session_start();
    $error = "";
    
    if ($_SESSION['logged_in'] == TRUE and isset($_GET['v'])) {
        $json = file_get_contents('v/_all.json');
        $db = json_decode($json, true);
        
        // searching village json by id
        if(array_key_exists($_GET['v'], $db)) {
            $selected = $db[$_GET['v']];
            $village = read_village($selected);
                // var_dump($village);
        } else {
            $error = "Village not present!";
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit · masterus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    
<?php if(isset($village)) { ?>
    <header>
        <h2>
            <img height="40" width="40" src="assets/img/amarok.png" alt="logo">
            Modifica <?php echo($village['nome']);?>
        </h2>

        <ul>
            <li><a href="admin.php">Pannello admin</a></li>
            <li><a href="./?v=<?php echo($village['id']);?>">Bacheca pubblica</a></li>
        </ul>
    </header>

    <center>
    <!-- <form action="" method="post"> -->
        <?php var_dump($village); ?>
    <!-- </form> -->    

<!-- ERRORI -->
<?php } if ($error != "") { ?>
    <h2 style="color:yellow;"><?php echo $error;?></h2>
<?php } ?>

    </center>
    
    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>