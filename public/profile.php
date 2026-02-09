<?php
// Data loaded by PageController (session, user info, $total_unread)
$user = [
    'id'    => $_SESSION['user_id'],
    'name'  => $_SESSION['user_name'] ?? '',
    'email' => $_SESSION['user_email'] ?? '',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profil - <?php echo htmlspecialchars($user['name']); ?></title>
  <link rel="stylesheet" href="./assets/main-QD_VOj1Y.css">
</head>
<body class="p-4">
  <div class="container">
    <div class="card p-4" style="max-width:720px;">
      <h3>Profil utilisateur</h3>
      <dl class="row">
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9"><?php echo htmlspecialchars($user['id']); ?></dd>

        <dt class="col-sm-3">Nom</dt>
        <dd class="col-sm-9"><?php echo htmlspecialchars($user['name']); ?></dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>
      </dl>

      <a href="/index" class="btn btn-primary">Retour au tableau de bord</a>
      <a href="/logout.php" class="btn btn-outline-secondary ms-2">Se déconnecter</a>
    </div>
  </div>
</body>
</html>
