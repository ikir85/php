<?php
// Liste produits dans un tableau HTML
//tous les champs sauf description
//bonu:
//afficher le nom de la categorie au lieu de son id 

require_once __DIR__ . '/../include/init.php';
adminSecurity();

// lister les catégories dans un tableau HTML

// le requêtage ici
$stmt = $pdo->query('SELECT P.*, C.nom AS cat_name FROM produit P INNER JOIN categories C ON P.categorie_id = C.id ');
$produit = $stmt->fetchAll();

/*
 * $query = <<<SQL
 * SELECT P.*, C.nom AS cat_name   
 * FROM produit P
 * JOIN categorie C ON p.categorie_id = C.id
 * SQL;

 */

require __DIR__ . '/../layout/top.php';
?>

<h1>Gestion Produits</h1>

<p><a href="produit-edit.php">Ajouter une produit</a></p>

<!-- le tableau HTML ici -->
<table class="table_cat th_produits table table-striped">
    <tr>
        <th>Id</th>
        <th>Nom</th>
        <th>Reference</th>
        <th>Prix</th>
        <th>Categorie</th>
        <th></th>
        
    </tr>
    <?php
    foreach ($produit as $item) :
        //$id_stm = $pdo->query('SELECT nom FROM categories WHERE id='. $item['id'].' ');
        //$produit_cat = $id_stm->fetchAll();
        //dump($produit_cat);
    ?>
    <tr>
        <td><?= $item['id']; ?></td>
        <td><?= $item['nom']; ?></td>
        <td><?= $item['reference']; ?></td>
        <td><?=  prixFR($item['prix']); ?></td>
        <td><?= $item['cat_name']; ?></td>
        <td>
            <a class="btn btn-primary"
               href="produit-edit.php?id=<?= $item['id']; ?>">
               Modifier
            </a>
        <a class="btn btn-danger"
               href="produit-delete.php?id=<?= $item['id']; ?>" onclick="myFunction()">
               Supprimer
            </a>
        </td>
    </tr>
    
    <?php
    endforeach;
    ?>
</table>

<?php
require __DIR__ . '/../layout/bottom.php';
?>

