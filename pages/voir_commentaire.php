<?php
// Démarrer la session
session_start();

// Inclure les fonctions réutilisables
include('../inc/functions.php');

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id_membres'])) {
    header("Location: index.php");
    exit();
}

// Récupérer l'ID de la publication depuis la requête GET
if (!isset($_GET['id_publication']) || empty($_GET['id_publication'])) {
    die("ID de publication manquant.");
}
$id_publication = $_GET['id_publication'];

global $bdd;

// Récupérer les détails de la publication
$query_publication = "SELECT texte_publication, dateheure_publication 
                      FROM publications 
                      WHERE id_publication = '$id_publication'";
$result_publication = mysqli_query($bdd, $query_publication);

if (mysqli_num_rows($result_publication) === 0) {
    die("Publication non trouvée.");
}
$publication = mysqli_fetch_assoc($result_publication);

// Récupérer tous les commentaires de la publication
$query_commentaires = "SELECT c.texte_commentaire, c.dateheure_commentaire, r.nom 
                        FROM commentaires c
                        JOIN reseaux r ON c.id_membres = r.id_membres
                        WHERE c.id_publication = '$id_publication'
                        ORDER BY c.dateheure_commentaire ASC";
$result_commentaires = mysqli_query($bdd, $query_commentaires);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la publication</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
        <h1>Bienvenue sur mon réseau social</h1>
    </header>

    <div class="container">
    <h2>Détails de la publication</h2>
    <p><strong><?php echo $publication['texte_publication']; ?></strong></p>
    <p>Publié le <?php echo $publication['dateheure_publication']; ?></p>

    <h3>Commentaires :</h3>
    <?php if (mysqli_num_rows($result_commentaires) > 0) { ?>
        <ul>
            <?php while ($commentaire = mysqli_fetch_assoc($result_commentaires)) { ?>
                <li>
                    <strong><?php echo $commentaire['nom']; ?>:</strong>
                    <?php echo $commentaire['texte_commentaire']; ?>
                    <br>
                    <small><?php echo $commentaire['dateheure_commentaire']; ?></small>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Aucun commentaire pour cette publication.</p>
    <?php } ?>
<div class ="pagination">
    <p><a href="publication.php">Retour aux publications</a></p>
</div>
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