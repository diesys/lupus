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
        $json = file_get_contents('v/'.$file_name.'.json');
        $data = json_decode($json, true);
    }

    function write_village($data, $file_name) {
        $json = json_encode($data);
        file_put_contents('v/'.$file_name.'.json', $json);
    }

    function new_village($data, $file_name) {
        $path = 'v/'.$file_name.'.json';
        $json = json_encode($data);
        if (!file_exists($path)) {
            file_put_contents($path, $json);
        } else {
            $error = "filename exists!";
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
    if($_SESSION['logged_in']) {
        $_SESSION['logged_in'] = TRUE;
    }
    if(isset($_POST['password']) and $_POST['password'] == $password) {
        $_SESSION['logged_in'] = TRUE;
    } else {
        $error="Incorrect Password";
        $_SESSION['logged_in'] = FALSE;
    }

    // logout
    if(isset($_POST['page_logout'])) {
        $_SESSION['logged_in'] = FALSE;
    }    
 
    // crea partita
    if(isset($_POST['new_name']) and isset($_POST['players'])) {
        $data = array(
            'nome' => $_POST['new_name'],
            'eventi' => array(),
            'giocatori' => array_fill(0, $_POST['players'], ""),
            'id' => generateRandomString()
        );
        new_village($data, $_POST['new_name']);
        header("./admin.php");
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
            <h4>Nuovo villaggio</h4>
            <input name="new_name" placeholder="Nome*" type="text" pattern="[A-Za-z0-9]{4-24}" required />
            <input name="players" type="number" placeholder="Giocatori**" min="4" max="30" range="1" required />
            <button type="submit" formmethod="post">Crea</button>
            <p class="legend">
                <small>* 3-24 caratteri alfanumerici, senza spazi</small>
                <br>
                <small>** da 4 a 30 giocatori</small>
            </p>
        </form>

        <form action="" method="post">
            <h4>Seleziona villaggio</h4>
            <select name="villaggi" id="lista_villaggi">
                <?php 
                foreach (glob("v/*.json") as $filename) {
                    // removes dir and format
                    $village = substr($filename, 2, -5);
                    echo("<option value='".$village."'>".$village."</option>");
                }
                ?>
            </select>
        </form>
    </center>

<?php } if ($error != "") { ?>
    <center><h3 style="color:red;"><?php echo $error;?></h3></center>
<?php } ?>

</body>
</html>