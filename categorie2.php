<?php
require_once __DIR__ . '/include/init.php';


require __DIR__ . '/layout/top.php';


$stmt = $pdo->query('SELECT * from categories WHERE id='. (int)$_GET['id']);
$cat_dir = $stmt->fetch();

$stmt_produit = $pdo->query('SELECT * from produit WHERE categorie_id='. (int)$_GET['id']);
$produit_all = $stmt_produit->fetchAll();

?>

<h1 style="margin-top:25px;"><?= ucfirst($cat_dir['nom']); ?></h1>


<table class="table_cat th_produits table table-striped">
    <tr>
        <th>Iddd</th>
        <th>Nom</th>
        <th>Reference</th>
        <th>Prix</th>
        <th>Image</th>
    </tr>
    <?php
    foreach ($produit_all as $item) :
        //$id_stm = $pdo->query('SELECT nom FROM categories WHERE id='. $item['id'].' ');
        //$produit_cat = $id_stm->fetchAll();
        //dump($produit_cat);
        if(empty( $item['photo'])){
            $url_img=PHOTO_DEFAULT;
        }else{
            $url_img = PHOTO_WEB . $item['photo'] ;
        }
    ?>
    <tr>
        <td><?= $item['id']; ?></td>
        <td><a  href="<?= RACINE_WEB; ?>produit-single.php?id=<?=$item['id']; ?>"><?= $item['nom']; ?></a></td>
        <td><?= $item['reference']; ?></td>
        <td><?=  prixFR($item['prix']); ?></td>
        <td><img src="<?= $url_img; ?>" height ="150px"></td>
    </tr>
    
    <?php
    endforeach;
    ?>
</table>



<?php
require __DIR__ . '/layout/bottom.php'
?>