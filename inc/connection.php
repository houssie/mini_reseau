<?php
if ($bdd = mysqli_connect('localhost', 'root', '', 'reseau')){
  echo "Vous êtes connecté!";
}else {
    echo "Connexion échoué!";
}

?>