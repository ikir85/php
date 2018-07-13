<?php
/* 
 * Lister les commandes dans un tableau HTML
 * id de la commande
 * nom prenom de l'utilisateur qui a passé la commande
 * montant formaté
 * date de la commande
 * statut
 * date de statut
 * 
 * Passer le statut en liste deroulant avec un bouton Modifier
 * pour changer le statut de la commande en bdd (nécessité un champs caché pour l'id de la commande)
 */

require_once __DIR__ . '/../include/init.php';
adminSecurity();

if(isset($_POST['modifierStatut'])){
    $query = <<<SQL
UPDATE commande SET
   statut = :statut,
   date_statut = now()
WHERE id = :id
SQL;
            


$stmt = $pdo->prepare($query);
$stmt->execute([
    ':statut' => $_POST['statut'],
    ':id' => $_POST['commandeId']
]);

setFlashMessage('Le statut est modifié');
}
        
// concat_ws(' ', u.prenom, u.nom) //concatenation valeur sql
$query = 'SELECT C.*,U.nom as user_name, U.prenom as user_prenom FROM commande C INNER JOIN utilisateur U ON C.utilisateur_id = U.id  ';
$stmt = $pdo->query($query);
$commandes = $stmt->fetchAll();

$statuts = [
     'en cours',
     'envoyé',
     'livré',
     'annulé'  
];

require __DIR__ . '/../layout/top.php';
?>
<h1>Commande</h1>

<!-- le tableau HTML ici -->
<table class="table_cat th_produits table table-striped">
    <tr>
        <th>Id</th>
        <th>Nom</th>
        <th>Prenom</th>
        <th>Montant</th>
        <th>Date de commande</th>
        <th>Statut</th>
        <th>Date de statut</th>
        
        
    </tr>
    <?php
    
     
    foreach ($commandes as $commande) :
        //$id_stm = $pdo->query('SELECT nom FROM categories WHERE id='. $item['id'].' ');
        //$produit_cat = $id_stm->fetchAll();
        //dump($commande['date_statut']);  
    ?>
    <tr>
        <td><?= $commande['id']; ?></td>
        <td><?= $commande['user_name']; ?></td>
        <td><?= $commande['user_prenom']; ?></td>
        <td><?=  prixFR($commande['montant_total']); ?></td>
        <td><?=  datetimeFR($commande['date_commande']); ?></td>
        <!--<td><?= $commande['statut']; ?></td>-->
        <td>
            <form method="post" class="form-inline">
          <select name="statut" class="form-control">
              <?php
                foreach ($statuts as $statut) :
                $selected = ($statut == $commande['statut'])
                    ? 'selected'
                    : ''
               ;
              ?>
                <option value="<?= $statut; ?>" <?= $selected; ?>><?= $statut; ?></option>
              <?php
                 endforeach;
              ?>  
          </select>
                <input type="hidden" name="commandeId" value="<?= $commande['id'];?>">
                <button type="submit" name="modifierStatut" class="btn btn-primary ">Modifier</button>
            </form>
        </td>
        <td><?= datetimeFR($commande['date_statut']); ?></td>
       
    </tr>
    
    <?php
    endforeach;
    ?>
</table>


<?php
require __DIR__ . '/../layout/bottom.php';
?>