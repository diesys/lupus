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

    function new_village($file_name) {
        $data = array(
            'nome' => $_POST['new_name'],
            'eventi' => array(array("data" => "", "descrizione" => "Prima notte")),
            'giocatori' => array_fill(0, $_POST['players'], array("username" => "", "ruolo" => "", "in vita" => TRUE)),
            'id' => generateRandomString()
        );

        $new_json = 'v/'.$file_name.'.json';
        $new_village = json_encode($data);
        
        if (!file_exists('v/_all.json')) {
            file_put_contents('v/_all.json', json_encode(array()));
        }
        $db = file_get_contents('v/_all.json');
        $all = json_decode($db, true);

        if (!file_exists($new_json)) {
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

        /////////////////////!!!!!!!!# ######################################################################
        // da aggiungere il nuovo villaggio a un file "_all.json" come db
        // $data['id'];
        /////////////////////////////////////////////// #####################################################
    }

    //////////////////////
    session_start();
    $error = "";
    $password = "supul";
    
    // LOGIN
    if(isset($_POST['password']) and $_POST['password'] == $password) {
        $_SESSION['logged_in'] = TRUE;
    } else if($_SESSION['logged_in']) {
        $_SESSION['logged_in'] = TRUE;
    } else {
        $error="Password sbagliata!";
        $_SESSION['logged_in'] = FALSE;
    }

    // logout
    if(isset($_POST['page_logout'])) {
        $_SESSION['logged_in'] = FALSE;
    }    
 
    // crea partita
    if(isset($_POST['new_name']) and isset($_POST['players'])) {
        new_village($_POST['new_name']);
        // header("./admin.php");
    }

    // including the index.html
    // $index = file_get_contents('_index.html');
    // echo $index;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master lupus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php   
   if ($_SESSION['logged_in'] == TRUE) { 
    //    $_SESSION['password'] = $password;
?> 

    <center>
        <form action="" method="post">
            <h4>Seleziona villaggio</h4>
            <select name="villaggi" id="lista_villaggi">
                <?php 
                foreach (glob("v/*.json") as $filename) {
                    // removes dir and format
                    $village = substr($filename, 2, -5);
                    if($village != "_all") {
                        echo("<option value='".$village."'>".$village."</option>");
                    }
                }
                ?>
            </select>
        </form>

        <form action="" method="post">
            <h4>Nuovo villaggio</h4>
            <input name="new_name" placeholder="Nome¹" type="text" pattern="[A-Za-z0-9]{4-24}" required />
            <input name="players" type="number" placeholder="Giocatori²" min="4" max="30" range="1" required />
            <button type="submit" formmethod="post">Crea</button>
            <p class="legend">
                <small>¹ alfanumerico, senza spazi</small>
                <br>
                <small>² 4-30 giocatori</small>
            </p>
        </form>
    </center>

<?php } if ($error != "") { ?>
    <center><h2 style="color:yellow;"><?php echo($error);?></h2></center>
<?php } ?>

</body>
</html>