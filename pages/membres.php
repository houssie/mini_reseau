<?php
include('../inc/functions.php');
if (!isset($_SESSION['id_membres'])) {
    header("Location: index.php");
    exit();
}

$id_utilisateur = $_SESSION['id_membres'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_membre'])) {
    $id_membre_a_ajouter = $_POST['id_membre'];
    if (send_friend_request($id_utilisateur, $id_membre_a_ajouter)) {
        echo "<p>Invitation envoyée avec succès !</p>";
    } else {
        echo "<p>Une invitation a déjà été envoyée à cet utilisateur.</p>";
    }
}

global $bdd;
$query_membres = "SELECT id_membres, nom, email FROM reseaux WHERE id_membres != '$id_utilisateur'";
$result_membres = mysqli_query($bdd, $query_membres);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membres</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <h1>Bienvenue sur mon réseau social</h1>
        <a href="recherche.php">recherche</a>
    </header>

    <div class="container">
    <h2>Liste des membres</h2>
    <?php if (mysqli_num_rows($result_membres) > 0) { ?>
        <ul>
            <?php while ($membre = mysqli_fetch_assoc($result_membres)) { ?>
                <li>
                    <strong><?php echo $membre['nom']; ?></strong> (<?php echo $membre['email']; ?>)
                    <form method="post" action="membres.php" style="display:inline;">
                        <input type="hidden" name="id_membre" value="<?php echo $membre['id_membres']; ?>">
                        <button type="submit">Ajouter potes </button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Aucun autre membre trouvé.</p>
    <?php } ?>

    <p><a href="publication.php"class="link-more">Retour aux publications</a></p>
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