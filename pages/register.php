<?php
include('../inc/connection.php');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];
    $date_naissance = $_POST['date_naissance'];

    // Vérifier si l'email existe déjà
    $query_check = "SELECT * FROM reseaux WHERE email = '$email'";
    $result_check = mysqli_query($bdd, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Insérer le nouvel utilisateur
        $query_insert = "INSERT INTO reseaux (nom, email, motdepasse, date_naissance) VALUES ('$nom', '$email', '$motdepasse', '$date_naissance')";
        if (mysqli_query($bdd, $query_insert)) {
            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
     <!-- En-tête -->
     <header>
        <h1>Bienvenue sur mon réseau social</h1>
    </header>

    <div class="container">
    <h2>Inscription</h2>
    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>
    <?php if (isset($success)) { ?>
        <p class="success"><?php echo $success; ?></p>
    <?php } ?>

    <form method="post" action="register.php">
        <div>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="motdepasse">Mot de passe:</label>
            <input type="password" id="motdepasse" name="motdepasse" required>
        </div>
        <div>
            <label for="date_naissance">Date de naissance:</label>
            <input type="date" id="date_naissance" name="date_naissance" required>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    </div>

<!-- Pied de page -->
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