<?php
require_once __DIR__ . '/include/init.php';

 $Total= 0;
$Quantite= 0 ;

 
if(empty($_SESSION['panier'])){
   
    setFlashMessage('Le panier est vide');
   
}

if(isset($_POST['commander'])){
    $query = <<<SQL
INSERT INTO commande (
    utilisateur_id,
    montant_total
) VALUES (
    :utilisateur_id,
    :montant_total
)
SQL;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':utilisateur_id' => $_SESSION['utilisateur']['id'],
        ':montant_total' => totalPanier()
    ]);
    // recuperation de l'id de la commande  que l'on vient d'inserer
    $commandeId = $pdo->lastInsertId();
    
    $query = <<<SQL
INSERT INTO detail_commande (
    commande_id,
    produit_id,
    prix,
    quantite
) VALUES (
    :commande_id,
    :produit_id,
    :prix,
    :quantite
)
SQL;
    
    $stmt = $pdo->prepare($query);
    
    foreach($_SESSION['panier'] as $produitId => $produit){
        $stmt->execute([
            ':commande_id' => $commandeId,
            ':produit_id' => $produitId,
            ':prix' => $produit['prix'],
            ':quantite' => $produit['quantite']
        ]);
    }
    
    $_SESSION['panier']=[];
    setFlashMessage('La comande est enregistré');
    
     
 }

 if(isset($_POST['modifierQuantite'])){
     modifierQuantitePanier($_POST['id_produit'], $_POST['quantite']);
     
 }
 




require __DIR__ . '/layout/top.php';
/*
 * Remplacer l'affichage de la quantité par un formulaire avec:
 * un <input type="number"> pour la quantité
 * un <input type="hidden"> pour avoir l'id du produit
 * dont on va modifier la quantité
 * un bouton submit
 * Faire une fonction modifierQuantitePanier($prduitID, $quantite)
 * qui modifie la quantité du produit si c'est pas 0,
 * et qui le supprime du panier sinon
 * Appeler cette fonction quand un des formulaires est envoyé
 */
?>
<h1>Panier</h1>

<?php
if(empty($_SESSION['panier'])) :
?>
    <div class="alert alert-info">
        Le panier est vide
    </div>
<?php
else :
?>
<!-- le tableau HTML ici -->
<table class="table_cat th_produits table table-striped">
    <tr>
        <th>Nom</th>
        <th>Prix (unité)</th>
        <th>Quantité</th>
        <th>Total</th>
    </tr>
    <?php
    
    
    foreach ($_SESSION['panier'] as $produitId => $item) :
        //$id_stm = $pdo->query('SELECT nom FROM categories WHERE id='. $item['id'].' ');
        //$produit_cat = $id_stm->fetchAll();
        //dump($produit_cat);
        
        $Total_produit = ($item['prix']) * $item['quantite'];
        //dump($Total_produit);
        $Total += $Total_produit ;
        
        
    ?>
    <tr>
        <td><?= $item['nom']; ?></td>
        <td><?=prixFR($item['prix']) ?></td>
        <!--<td><?= $item['quantite']; ?></td>-->
        <td class="cardQ-formTd">
            <form method="post" class="form-inline">
                <input class="panier_quant" type="number" name="quantite"  min="0" value="<?= $item['quantite']; ?>">
                <input type="hidden" name="id_produit" value="<?= $produitId ; ?>">
                <div style="margin-top: 20px" class="form-btn-group text-right">
                    <button type="submit" class="btn btn-primary" name="modifierQuantite">
            Modifier
        </button>
        </div>
            </form>
        </td>
        <td><?=prixFR($Total_produit); ?></td>
    </tr>
    
    
    <?php
    //dump($produitId); //dump($item['id']);
    endforeach;
    ?>
    <tr>
        <th colspan="3" style="text-align: right">Total :</td>
        <td style="font-weight:bold"><?=  prixFR(totalPanier());?></td>
    </tr>
</table>
<?php
if (!isUserConnected()) :
    ?>
    <div class="alert alert-info">
        Vous devez vous connecter ou vous inscrire pour valider la commande
    </div>
<?php
else :
?>
<form method="post">
    <p class="text-right">
        <button type="submit" name="commander" class="btn btn-primary valid_commande">
            Valider la commande
        </button>
    </p>
</form>
<?php
    endif;

endif;    
?>
<?php
require __DIR__ . '/layout/bottom.php';
?>