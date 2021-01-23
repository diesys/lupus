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
    $_SESSION['password'] = "";
    $password = "supul";
    $_SESSION['logged_in'] = FALSE;
    
    if(isset($_POST['password'])) {
        if($_POST['password'] == $password) {
            // $_SESSION['password'] = $pass;
            $_SESSION['logged_in'] = TRUE;
        }
        else {
            // $_SESSION['password'] = null;
            $_SESSION['logged_in'] = FALSE;
            if($error == "") {
                $error="Incorrect Password";
            }
        }
    }
 
    // crea partita
    if(isset($_POST['new_name'])) {
        $data = array(
            'nome' => $_POST['new_name'],
            'eventi' => array(),
            'id' => generateRandomString()
        );
        new_village($data, $_POST['new_name']);
    }

    // logout
    if(isset($_POST['page_logout'])) {
        $_SESSION['logged_in'] = FALSE;
        // unset($_SESSION['password']);
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
</head>
<body>

<?php   
   if ($_SESSION['logged_in']) { 
    //    $_SESSION['password'] = $password;
?> 

    <center>
        <form action="" method="post">
            <label for="new_name">Nuovo</label>
            <input name="new_name" type="text" pattern="[A-Za-z0-9]{3-24}" required />
            <button type="submit" formmethod="post">Crea</button>
        </form>

        <form action="" method="post">
            <label for="villaggi">Scegli</label>
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