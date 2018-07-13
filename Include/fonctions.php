<?php

function dump($var){
    
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
function sanitizeValue(&$value)
{   
    //trim() supprime les tableaux en debut et fin de chaine
    //strip_tags supprime les balises HTML
    $value = trim(strip_tags($value));
}

function sanitizeArray(array &$array)
{    
   //applique la fonction sanitizeValur() sur tousnles elements du tableau 
   array_walk($array, 'sanitizeValue');

}

function sanitizePost()
{    
   //applique la fonction sanitizeValur() sur tousnles elements du tableau 
   sanitizeArray($_POST);

}

function setFlashMessage($message, $type='success')
{
 $_SESSION['flashMessage'] = [
   'message' => $message,
   'type' => $type 
 ];   
}


//affiche un message flash s'il y en a un en session
//puis le supprime
function displayFlashMessage(){
    if(isset($_SESSION['flashMessage'])){
       $message = $_SESSION['flashMessage']['message'];
       $type = $_SESSION['flashMessage']['type'] == 'error'? 'danger' :$_SESSION['flashMessage']['type'];
       
       echo '<div class="alert alert-'. $type.'">'
            .'<h5 class="alert-heading">'.$message.'</h5>'
            .'</div>';
       
       //suppresion du message dans la session
       //pour affichage unique
       unset($_SESSION['flashMessage']);
    }
}

function isUserConnected(){
    return isset($_SESSION['utilisateur']);
}

function getUserFullName(){
    if(isUserConnected()){
    return $_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom'];
    }
}

function isUserAdmin(){
    return isUserConnected()
    && $_SESSION['utilisateur']['role']=='admin';
}

function adminSecurity()
{
    if (!isuserAdmin()){
        if(!isUserConnected()){
            header('location: '. RACINE_WEB . 'connexion.php');
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo "Vous n'avez pas le droit d'acceder à cette page";
        }
        
        die;
    }
}

function prixFR($prix)
{
    return number_format($prix, 2, ',', ' '). ' €';
}

function ajouterPanier(array $produit, $quantite){
    // initialisation du panier
    if(!isset($_SESSION['panier'])){
        $_SESSION['panier'] = [];
    }
    // si le produit n'est pas encore dans le panier
    if (!isset($_SESSION['panier'][$produit['id']])){
        //ajout du produit dans le panier
        $_SESSION['panier'][$produit['id']] = [
            'nom' => $produit['nom'],
            'prix' => $produit['prix'],
            'quantite' => $quantite,
        ];
    } else {
        // si le produit est deja dans le panier
        // on met a jour la quantité
        $_SESSION['panier'][$produit['id']]['quantite'] += $quantite;
    }
    
}

function totalPanier(){
    $total = 0;
    
    if(isset($_SESSION['panier'])){
        foreach($_SESSION['panier'] as $produit) {
                 $total += $produit['prix']* $produit['quantite'];
        }
    }
    return $total;
}
function modifierQuantitePanier($produit_ID, $quantite){
    
    if(isset($_SESSION['panier'][$produit_ID])){
        if($quantite !=0){
            $_SESSION['panier'][$produit_ID]['quantite'] =$quantite ;
        }else {
            unset($_SESSION['panier'][$produit_ID]);
        }
    }
}

function datetimeFR($datetimeSQL){
    return date( "d/m/Y H:i", strtotime( $datetimeSQL) );
}