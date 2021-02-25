<?php
    include 'assets/lupus.php';

    $error = "";
    $village = NULL;

    // LOGIN
    if((isset($_POST) and isset($_POST['password']) and $_POST['password'] == $password) or (isset($_SESSION) and isset($_SESSION['logged_in']) and $_SESSION['logged_in'])) {
        $logged = TRUE;
    } else {
        $error="Password errata";
        $logged = FALSE;
    }
    $_SESSION['logged_in'] = $logged
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Lista partite | Lupus</title>

    <?php echo(headerImport("space")); ?>

    <script>
        function uncollapse() {
            document.querySelectorAll('.collapsed').forEach(element => {
                element.classList.remove('collapsed');
            })
            document.querySelector('#titolo_crea').className = "full-width";
        }
    </script>
</head>

<body class="admin <?php
    if(isset($_SESSION) and isset($_SESSION['color']) and intval($_SESSION['color']) != -1) {
        $color = $_SESSION['color'];
    } else {
        $color = rand(0,4);
    }
    echo("clr-".$color." bs-clr-".$color); ?>">
<?php themeSelector('admin.php'); ?>

    <div id="bg" style="background-image: url('assets/img/bg/space/<?php 
        if(isset($_SESSION) and isset($_SESSION['image']) and intval($_SESSION['image']) != -1) {
            $image = $_SESSION['image'];
        } else {
            $image = rand(0, 5);
        } echo($image);?>.jpg')"></div>
    <header>
        <h2 class="title">
            <span>
                <a class="logo" href="."><img height="40" width="40" src="assets/img/amarok.png" alt="logo"></a>
                Lupus
            </span>
        </h2>
    </header>

    <center class="admin">
    <?php if ($logged == TRUE) { ?> 
        <h2>Benvenuto Master! <br> <a href="assets/logout.php">Logout?</a></h2>

        <form action="edit.php" id="select_village" method="get">
            <h4 class="half-flex">Modifica</h4>
            <!-- <label for="v">Villaggio</label> -->
            <select class="half-flex" name="v" id="lista_villaggi" onchange="document.querySelector('#select_village').submit()" required>
                <option value="" disabled selected></option>
                <?php foreach ($villages as $hash => $name) {
                    echo("<option value='".$hash."'>".$name."</option>");
                } ?>
            </select>
            <!-- <button type="submit" formmethod="get">vai</button> -->
        </form>

        <form action="populate.php?v=<?php echo(generateRandomString()); ?>" method="post">
            <h4 class="half-flex" id="titolo_crea">Crea partita</h4>
            
            <span class="half-flex">
                <label for="variant">Variante</label>
                <select onchange="uncollapse();" name="variant" id="variant" required>
                    <option value="" disabled selected></option>
                    <option value="space">Lupus in Space</option>
                    <option value="classic">Classico</option>
                </select>
            </span>
            
            <span class="half-flex collapsed">
                <label for="new_name">Nome</label>
                <input name="new_name" placeholdxer="Nome" type="text" pattern="[A-Za-z0-9]{4-24}" required />
            </span>
            
            <span class="half-flex collapsed">
                <label for="players">Giocatori</label>
                <input name="players" type="number" placeholder="Giocatori" min="4" max="40" range="1" required />
            </span>

            <!-- <p class="legend">¹ alfanumerico senza spazi · ² 4-30 giocatori</p> -->

            <span class="half-flex collapsed">
                <button class="half-width" type="submit" formmethod="post">Crea</button>
            </span>
        </form>  

    <?php } if ($error != "") { ?>
        <h3 class="error"><?php echo($error);?>, <a href="login.php">riprova</a></h3>
    <?php } ?>
    </center>

    <footer>
        <p class="legend">
            Questo sito conserva solamente i dati caricati dai master (chiedendone il consenso agli utenti): informazioni necessarie al fine del gioco (username/nome riconoscibile degli utenti) e le partite stesse. Le pagine pubbliche di consultazione utilizzano un link generato casualmente per essere visto solo da chi lo possiede.
        </p>
    </footer>
</body>
</html>