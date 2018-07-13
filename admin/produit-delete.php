<?php
require_once __DIR__.'/../include/init.php';
adminSecurity();

$query = 'SELECT phoito FROM produit WHERE id='.(int)$_GET['id'];

$stmt = $pdo -> query($query);
$photo = $stmt-> fetchColumn();

//on supprime la photo du produit s'il en a une
if (!empty($photo)){
    unlink(PHOTO_DIR . $photo);
}

$query = 'DELETE FROM produit WHERE id='.(int)$_GET['id'];

$pdo->exec($query);
setFlashMessage('Le produit a été supprimé');
header('Location: produits.php');
//termine l'excution du script