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

$success = ""; // Message de succès
$error = "";   // Message d'erreur

global $bdd;

// Gestion des commentaires
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['texte_commentaire']) && isset($_POST['id_publication'])) {
    $texte_commentaire = $_POST['texte_commentaire'];
    $id_publication = $_POST['id_publication'];
    $id_membres = $_SESSION['id_membres'];

    if (add_comment($texte_commentaire, $id_publication, $id_membres)) {
        $success = "Commentaire ajouté avec succès!";
    } else {
        $error = "Erreur lors de l'ajout du commentaire.";
    }
}


// Gestion de la publication
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['texte_publication'])) {
    $texte_publication = $_POST['texte_publication'];
    $id_membres = $_SESSION['id_membres'];

    $query = "INSERT INTO publications (texte_publication, id_membres) VALUES ('$texte_publication', '$id_membres')";
    if (mysqli_query($bdd, $query)) {
        $success = "Publication réussie!";
    } else {
        $error = "Erreur lors de la publication.";
    }
}

// Récupérer toutes les publications de l'utilisateur connecté
$id_membres = $_SESSION['id_membres'];
$result= get_user_publications($id_membres);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publications</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <h1>Bienvenue sur mon réseau social</h1>
    </header>

    <div class="container">
    <h2>Bienvenue, <?php echo $_SESSION['nom']; ?>!</h2>
    <p>Publiez quelque chose :</p>

    <?php if (!empty($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>
    <?php if (!empty($success)) { ?>
        <p class="success"><?php echo $success; ?></p>
    <?php } ?>

    <!-- Formulaire pour publier une nouvelle publication -->
    <form method="post" action="publication.php" class="comment-form">
        <div>
            <textarea name="texte_publication" rows="4" cols="50" placeholder="Écrivez votre publication ici..." required></textarea>
        </div>
        <button type="submit">Publier</button>

    </form>

    <h3>Vos publications :</h3>
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <ul class="comment-list">
            <?php while ($publication = mysqli_fetch_assoc($result)) { ?>
                <li>
                    <strong><?php echo $publication['nom']; ?>:</strong>
                    <?php echo $publication['texte_publication']; ?>
                    <br>
                    <small>Publié le <?php echo $publication['dateheure_publication']; ?></small>

                    <!-- Section des commentaires -->
                    <div>
                        <h4>Commentaires :</h4>

                        <!-- Formulaire pour ajouter un commentaire -->
                        <form method="post" action="publication.php" style="display:inline;">
                            <input type="hidden" name="id_publication" value="<?php echo $publication['id_publication']; ?>">
                            <textarea name="texte_commentaire" rows="2" cols="40" placeholder="Ajoutez un commentaire..." required></textarea>
                            <button type="submit">Commenter</button>
                        </form>

                        <!-- Afficher les 3 premiers commentaires -->
                        <?php
                        $id_publication = $publication['id_publication'];
                        $result_commentaires = get_comments($id_publication, 3); // Limiter à 3 commentaires initialement
                        ?>
                        <ul class="comment-list">
                            <?php while ($commentaire = mysqli_fetch_assoc($result_commentaires)) { ?>
                                <li> 
                                    <strong><?php echo $commentaire['nom']; ?>:</strong>
                                    <?php echo $commentaire['texte_commentaire']; ?>
                                    <br>
                                    <small><?php echo $commentaire['dateheure_commentaire']; ?></small>
                                </li>
                            <?php } ?>
                        </ul>

                        <!-- Lien "Voir plus" -->
                        <a href="voir_commentaire.php?id_publication=<?php echo $publication['id_publication']; ?>" class="link-more">Voir plus</a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Vous n'avez aucune publication.</p>
    <?php } ?>

    
    <p><a href="amis.php" class="link-more">AMIS</a></p>
    <p><a href="membres.php" class="link-more">MEMBRES</a></p>
    
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