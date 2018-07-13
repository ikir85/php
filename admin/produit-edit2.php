<?php
require_once __DIR__ . '/../include/init.php';
adminSecurity();

$errors = [];
$nom = '';
$description = '';
$reference = '';
$categorie ='';
$prix ='';

if (!empty($_POST)) { // si le formulaire a été soumis
    // nettoyage des données du formulaire (cf include/fonctions.php)
    // si on recoit un id dans l'url sans retour de POST pré-remplissage du formulaire a partir de la bdd
    // enregistrement avec UPDATE au lieu de INSERT
    //adpater le controle de l'unicité de la reference pour exculure la reference du produti aue l'on modie
    
    dump($_FILES);die;
    sanitizePost ();
    // crée des variables à partir d'un tableau
    // Les variables ont les noms des clés dans le tableau
    extract($_POST);
    
    // test de la saisie du champ nom
    if (empty($_POST['nom'])) {
        $errors[] = 'Le nom est obligatoire';
    } 
    
     if (empty($_POST['description'])) {
        $errors[] = 'Veuillez inscrire une description';
    } 
    
    if (empty($_POST['reference'])) {
        $errors[] = 'Veuillez inscrire une reference';
    } elseif (strlen($_POST['reference']) > 50) {
        $errors[] = 'La reference ne doit pas faire plus de 50 caractères';
    } else {
        $query = 'SELECT count(*) FROM produit WHERE reference = :reference';
        
        if(!empty($GET['id'])){
            $query .= ' AND id !=' . (int)$_GET['id'];
        }
        $stmt = $pdo->prepare($query);
        $stmt -> bindValue(':reference', $_POST['reference']);
        $stmt -> execute();
        $nb = $stmt->fetchColumn();
    }
    
    if (empty($_POST['prix'])) {
        $errors[] = 'Veuillez inscrire une description';
    } 
    
    if (empty($_POST['categorie'])){
        $errors[] = 'La categorieé est obligatoire';
    }
        // si le formulaire est correctement rempli

if (empty($errors)) {
    if (isset($_GET['id'])) { // modification
        $query = 'UPDATE produit SET nom = :nom, reference = :reference, description = :description, prix = :prix, categorie_id = :categorie   WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':nom' => $nom,
            ':reference' => $reference,
            ':description' => $description,
            ':prix' => $prix,
            ':categorie' => $categorie,
            ':id' => $_GET['id']
           
        ]);
    } else { // création
        // insertion en bdd
        $query = <<<EOS
   INSERT INTO produit(
       nom,
       reference,
       description,
       prix,
       categorie_id     
   ) VALUES (
       :nom,
       :reference,
       :description,
       :prix,
       :categorie
   )
EOS;
       dump($_POST['categorie']);
        $stmt = $pdo->prepare($query);
        $stmt ->execute([
       ':nom' => $_POST['nom'],
       ':description' => $_POST['description'],
       ':reference' => $_POST['reference'],
       // enregistrement du mot de passe a l'enregistrement
       ':categorie' => $_POST['categorie'],
       ':prix' => $_POST['prix'], 
      ]);
        
   

    // enregistrement d'un message en session
       setFlashMessage('Le produit est enregistré');
       // redirection vers la page de liste
       header('Location: produits.php');
       die; // termine l'execution du script
    }
    }
} elseif (isset ($_GET['id'])) {
    // en modification, si onn'a pas de retour de formulaire
    // on va chercher la catégorie en bdd pour affichage
    $query = 'SELECT * FROM produit WHERE id=' . (int)$_GET['id'];
    $stmt = $pdo->query($query);
    $produit = $stmt->fetch();
    $nom = $produit['nom'];
    $reference = $produit['reference'];
    $description = $produit['description'];
    $prix = $produit['prix'];
    $categorie = $produit['categorie_id'];
    
}

$stmt_cat = $pdo->query('SELECT * FROM categories');
$produit_cat = $stmt_cat->fetchAll();

require __DIR__ . '/../layout/top.php';

if (!empty($errors)) :
?>
<div class="alert alert-danger">
    <h5 class="alert-heading">Le formulaire contient des erreurs</h5>
    <?= implode('<br>', $errors); // transforme un tableau en chaîne de caractères ?>
</div>
<?php
endif;
?>
<h1>Edition produit</h1>

<!-- L'attribut enctype est obligatoire pour unn formulaire qui contient un telechargement de fichier
-->
<form method="post" class="inscrip_form" enctype="multipart/form-data">
    <div class="form-group">
         <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nom; ?>">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= $description; ?></textarea>
    </div>
    <div class="form-group">
        <label>Reference</label>
        <input type="text" name="reference" class="form-control" value="<?= $reference; ?>">
    </div>
    <div class="form-group">
        <label>Prix</label>
        <input type="text" name="prix" class="form-control" value="<?= $prix; ?>">
    </div>
     <div class="form-group">
        <label>Categorie</label>
        <select name="categorie" class="form-control">
            <option value=""></option>
            <?php
               foreach ($produit_cat as $cat_item) :
                   $selected = ($cat[id] == $categorie)
                       ? 'select'
                       : ''
            ?>
            <option value="<?= $cat_item['id'] ?>" <?= $selected; ?>><?= $cat_item['nom'] ?></option>
    <?php
    endforeach;
    ?>
        </select>
    </div>
    <div class="form-group" style="margin-top:30px;">
        <label>Photo</label></br>
        <input type="file" name="photo">
    </div>
    <div class="form-btn-group text-right" >
        <button type="submit" class="btn btn-primary">
            Enregistrer
        </button>
        <a class="btn btn-secondary" href="produits.php">
            Retour
        </a>
    </div>
</form>
<?php
require __DIR__ . '/../layout/bottom.php';
?>

