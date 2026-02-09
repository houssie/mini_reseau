<?php
include('../inc/functions.php');


$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    global $bdd;
    $query = "SELECT id_membres, nom, motdepasse FROM reseaux WHERE email = '$email'";
    $result = mysqli_query($bdd, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($motdepasse === $user['motdepasse']) {
            $_SESSION['id_membres'] = $user['id_membres'];
            $_SESSION['nom'] = $user['nom'];
            header("Location: publication.php");
            exit();
        } else {
            $error = "Mot de passe incorrect";
        }
    } else {
        $error = "Email non trouvé";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
        <h1>Bienvenue sur mon réseau social</h1>
    </header>

    <div class="container">
    <h2>Connexion</h2>
    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="post" action="index.php">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="motdepasse">Mot de passe:</label>
            <input type="password" id="motdepasse" name="motdepasse" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit? <a href="register.php">Créer un compte</a></p>
    </div>

    <footer>
        <nav>
            <ul>
                <li><a href="index.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            </ul>
        </nav>
    </footer>
</body>
</html>
</body>
</html>