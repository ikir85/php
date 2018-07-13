<?php
/*
Faire le formulaire d'édition de produit
- nom : champ text - obligatoire
- description : champ textarea - obligatoire
- référence : champ text - obligatoire, 50 caractères max., unique
- prix : champ text - obligatoire
- catégorie : liste déroulante - obligatoire
Si le formulaire est correctement rempli : INSERT en bdd
et redirection vers la page de liste avec message de confirmation
sinon messages d'erreurs et champs pré-remplis avec les valeurs saisies

Adapter la page pour la modification
- si on reçoit un id dans l'URL sans retour de POST, pré-remplissage
du formulaire à partir de la bdd
- enregistrement avec UPDATE au lieu d'INSERT
- adapter le contrôle de l'unicité de la référence pour exclure
la référence du produit que l'on modifie de la requête
*/
require_once __DIR__ . '/../include/init.php';
adminSecurity();

$nom = $description = $reference = $prix = $categorie = $photoActuelle = '';

if (!empty($_POST)) {
    
    
    sanitizePost();
    extract($_POST);
    
    if (empty($_POST['nom'])) {
        $errors[] = 'Le nom est obligatoire';
    }
    
    if (empty($_POST['description'])) {
        $errors[] = 'La description est obligatoire';
    }
    
    if (empty($_POST['reference'])) {
        $errors[] = 'La référence est obligatoire';
    }elseif (strlen($_POST['reference']) > 50) {
        $errors[] = 'La référence ne doit pas faire plus de 50 caractères';
    } else {
        $query = 'SELECT count(*) FROM produit WHERE reference = :reference';
        
        if (!empty($_GET['id'])) {
            $query .= ' AND id !=' . (int)$_GET['id'];
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':reference', $_POST['reference']);
        $stmt->execute();
        $nb = $stmt->fetchColumn();
        
        if ($nb != 0) {
            $errors[] = 'Il existe déjà un produit avec cette référence';
        }
    }
    
    if (empty($_POST['prix'])) {
        $errors[] = 'Le prix est obligatoire';
    }
    
    if (empty($_POST['categorie'])) {
        $errors[] = 'La catégorie est obligatoire';
    }
    
    //si une image a été telechargé
    if (!empty($_FILES['photo']['tmp_name'])) {
        //if($_FILES['photo']['size'] > 1000000) : le poinds fichier en octes
        if($_FILES['photo']['size'] > 1000000){
            $errors[] = 'La photo ne doit pas faire plus de 1Mo';
        }
        
        $allowedMimeTypes = [
            'image/jpeg',
            'image/gif',
            'image/png',
            
        ];
        
        if(!in_array($_FILES['photo']['type'] ,$allowedMimeTypes)){
            $errors[] = 'La photo doit être une image GIF, JPG ou PNG';
        }
    }
    
    
    if (empty($errors)) {
        if(!empty($_FILES['photo']['tmp_name'])) {
            $name = $_FILES['photo']['name'];
            // on retrouve l'extension du fichier original à partir de son nom
            $extension = substr($name,strrpos($name,'.'));
            // le nom que va avoir le fichier dans le répertoire photo
            $nomPhoto = $_POST['reference'] . $extension;
            
            // en modification, si le produit avait deja une photo
            // on la supprimme
            if(!empty($photoActuelle)){
                unlink(PHOTO_DIR . $photoActuelle);
            }
            
            // enregistrel:ent de fichier dans le repertoire pho
            move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $nomPhoto);
        } else {
            $nomPhoto = $photoActuelle;
        }
    }
    
    
    
    if (empty($errors)) {
        if (!empty($_GET['id'])) {
            $query = <<<SQL
UPDATE produit SET
    nom = :nom,
    description = :description,
    reference = :reference,
    prix = :prix,
    categorie_id = :categorie_id, 
    photo = :photo             
WHERE id = :id
SQL;
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':description' => $_POST['description'],
                ':reference' => $_POST['reference'],
                ':prix' => $_POST['prix'],
                ':categorie_id' => $_POST['categorie'],
                ':photo' => $nomPhoto,
                ':id' => $_GET['id']
            ]);
        } else {
            $query = <<<SQL
INSERT INTO produit (
    nom,
    description,
    reference,
    prix,
    categorie_id,
    photo
) VALUES (
    :nom,
    :description,
    :reference,
    :prix,
    :categorie_id,
    :photo
)
SQL;
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom' => $_POST['nom'],
                ':description' => $_POST['description'],
                ':reference' => $_POST['reference'],
                ':prix' => $_POST['prix'],
                ':categorie_id' => $_POST['categorie'],
                ':photo' => $nomPhoto,
            ]);
        }
         
        setFlashMessage('Le produit est enregistré');
        header('Location: produits.php');
        die;
   }
} elseif (!empty($_GET['id'])) {
    $query = 'SELECT * FROM produit WHERE id = ' . (int)$_GET['id'];
    $stmt = $pdo->query($query);
    $produit = $stmt->fetch();
    extract($produit);
    $categorie_p = $produit['categorie_id'];
    $photoActuelle= $produit['photo'];
   
}

// pour construire le SELECT des catégories
$query = 'SELECT * FROM categories ORDER BY nom';
$stmt = $pdo->query($query);
$categorie = $stmt->fetchAll();

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
        <input type="text" name="nom"
            class="form-control" value="<?= $nom; ?>">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description"
            class="form-control"><?= $description; ?></textarea>
    </div>
    <div class="form-group">
        <label>Référence</label>
        <input type="text" name="reference"
            class="form-control" value="<?= $reference; ?>">
    </div>
    <div class="form-group">
        <label>Prix</label>
        <input type="text" name="prix"
            class="form-control" value="<?= $prix; ?>">
    </div>
    <div class="form-group">
        <label>Catégorie</label>
        <select name="categorie" class="form-control">
            <option value=""></option>
            <?php
            foreach ($categorie as $cat) :
                $selected = ($cat['id'] == $categorie_p)
                    ? 'selected'
                    : ''
                ;
            ?>
                <option value="<?= $cat['nom']; ?>" <?= $selected; ?>><?= $cat['nom']; ?></option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
     <div class="form-group" style="margin-top:30px;">
        <label>Photo</label></br>
        <input type="file" name="photo">
    </div>
    <?php
    if(!empty($photoActuelle)):
        echo '<p class="img_produit"><img src="'. PHOTO_WEB . $photoActuelle. '" height ="150px"></p>';
    endif;
    ?>
    <input type="hidden" name="photoActuelle" value="<?= $photoActuelle; ?>">
    <div class="form-btn-group text-right">
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