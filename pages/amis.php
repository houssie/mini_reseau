<?php
include('../inc/functions.php');
if (!isset($_SESSION['id_membres'])) {
    header("Location: index.php");
    exit();
}
$id_utilisateur = $_SESSION['id_membres'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accepter'])) {
        $id_invitation = $_POST['id_invitation'];
        global $bdd;
        $query_accepter = "UPDATE amis SET statut = 'accepte' WHERE id_amis = '$id_invitation'";
        mysqli_query($bdd, $query_accepter);
    } elseif (isset($_POST['refuser'])) {
        $id_invitation = $_POST['id_invitation'];
        global $bdd;
        $query_refuser = "DELETE FROM amis WHERE id_amis = '$id_invitation'";
        mysqli_query($bdd, $query_refuser);
    }
}

$invitations = get_received_invitations($id_utilisateur);
$amis = get_friends_list($id_utilisateur);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des amis</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
        <h1>Bienvenue sur mon réseau social</h1>
    </header>

    <div class="container">
    <h2>Gestion des amis</h2>

    <h3>Invitations reçues :</h3>
    <?php if (mysqli_num_rows($invitations) > 0) { ?>
        <ul>
            <?php while ($invitation = mysqli_fetch_assoc($invitations)) { ?>
                <li>
                    Vous avez reçu une invitation de <strong><?php echo $invitation['nom']; ?></strong> (<?php echo $invitation['email']; ?>).
                    <form method="post" action="amis.php" style="display:inline;">
                        <input type="hidden" name="id_invitation" value="<?php echo $invitation['id_amis']; ?>">
                        <button type="submit" name="accepter">Accepter</button>
                        <button type="submit" name="refuser">Refuser</button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Aucune invitation reçue.</p>
    <?php } ?>

    <h3>Liste des amis :</h3>
    <?php if (mysqli_num_rows($amis) > 0) { ?>
        <ul>
            <?php while ($ami = mysqli_fetch_assoc($amis)) { ?>
                <li>
                    <strong><?php echo $ami['nom']; ?></strong> (<?php echo $ami['email']; ?>)
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Vous n'avez aucun ami pour le moment.</p>
    <?php } ?>

    <p><a href="publication.php" class="link-more">Retour aux publications</a></p>
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