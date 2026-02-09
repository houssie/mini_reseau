<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
include('../inc/connection.php');

// Fonction pour récupérer les détails d'un utilisateur
function get_user_details($id_membres) {
    global $bdd;
    $query = "SELECT * FROM reseaux WHERE id_membres = '$id_membres'";
    $result = mysqli_query($bdd, $query);
    return mysqli_fetch_assoc($result);
}

// Fonction pour envoyer une invitation d'amitié
function send_friend_request($id_membre1, $id_membre2) {
    global $bdd;
    $query_check = "SELECT * FROM amis WHERE id_membre1 = '$id_membre1' AND id_membre2 = '$id_membre2'";
    $result_check = mysqli_query($bdd, $query_check);

    if (mysqli_num_rows($result_check) === 0) {
        $query_insert = "INSERT INTO amis (id_membre1, id_membre2, statut) VALUES ('$id_membre1', '$id_membre2', 'en_attente')";
        return mysqli_query($bdd, $query_insert);
    }
    return false;
}

// Fonction pour récupérer les invitations reçues
function get_received_invitations($id_membre2) {
    global $bdd;
    $query = "SELECT a.id_amis, r.nom, r.email 
              FROM amis a
              JOIN reseaux r ON a.id_membre1 = r.id_membres
              WHERE a.id_membre2 = '$id_membre2' AND a.statut = 'en_attente'";
    return mysqli_query($bdd, $query);
}

// Fonction pour récupérer la liste des amis
function get_friends_list($id_membre) {
    global $bdd;
    $query = "SELECT r.nom, r.email 
              FROM amis a
              JOIN reseaux r ON a.id_membre1 = r.id_membres
              WHERE a.id_membre2 = '$id_membre' AND a.statut = 'accepte'
              UNION
              SELECT r.nom, r.email 
              FROM amis a
              JOIN reseaux r ON a.id_membre2 = r.id_membres
              WHERE a.id_membre1 = '$id_membre' AND a.statut = 'accepte'";
    return mysqli_query($bdd, $query);
}

// Fonction pour ajouter un commentaire
function add_comment($texte_commentaire, $id_publication, $id_membres) {
    global $bdd;
    $query = "INSERT INTO commentaires (texte_commentaire, id_membres, id_publication) 
              VALUES ('$texte_commentaire', '$id_membres', '$id_publication')";
    return mysqli_query($bdd, $query);
}

// Fonction pour récupérer les publications d'un utilisateur
function get_user_publications($id_membres) {
    global $bdd;
    $query = "SELECT * FROM publications WHERE id_membres = '$id_membres' ORDER BY dateheure_publication DESC";
    return mysqli_query($bdd, $query);
}

// Fonction pour récupérer les commentaires d'une publication
function get_comments($id_publication, $limit = null) {
    global $bdd;
    $query = "SELECT c.texte_commentaire, c.dateheure_commentaire, r.nom 
              FROM commentaires c
              JOIN reseaux r ON c.id_membres = r.id_membres
              WHERE c.id_publication = '$id_publication'
              ORDER BY c.dateheure_commentaire ASC";
    if ($limit !== null) {
        $query .= " LIMIT $limit";
    }
    return mysqli_query($bdd, $query);
}
function get_all_publications($id_membres) {
    global $bdd;
    $query = "SELECT p.id_publication, p.texte_publication, p.dateheure_publication, m.nom
              FROM publications p
              JOIN membres m ON p.id_membres = m.id_membres 
              WHERE p.id_membres = '$id_membres'
              ORDER BY p.dateheure_publication DESC";
    return mysqli_query($bdd, $query);
}
?>