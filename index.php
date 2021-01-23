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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupus</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php
    if (isset($_GET) and isset($_GET['v'])) {
        $json = file_get_contents('v/_all.json');
        $db = json_decode($json, true);
        
        // searching village json by id
        if(array_key_exists($_GET['v'], $db)) {
            $selected = $db[$_GET['v']];
            $village = read_village($selected);
            var_dump($village);
        } else { ?>
            <center><h2 style="color:yellow;">"Village not present!"</h2></center>
        <?php }
    } else {
?>
    <!-- NO GET => HOME -->
    <center>
        <h2>Benvenut, su MasterLupus!</h2>
        <p><a href="login.php">login?</a></p>
    </center>
<?php } ?>

</body>
</html>