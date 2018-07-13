<?php
require_once __DIR__ . '/../include/init.php';
adminSecurity();

// lister les catégories dans un tableau HTML

// le requêtage ici
$stmt = $pdo->query('SELECT * FROM categories');
$categories = $stmt->fetchAll();

require __DIR__ . '/../layout/top.php';
?>
<h1>Gestion catégories</h1>

<p><a href="categorie-edit.php">Ajouter une catégorie</a></p>

<!-- le tableau HTML ici -->
<table class="table_cat table table-striped">
    <tr>
        <th>Id</th>
        <th>Nom</th>
        <th width="400px"></th>
    </tr>
    <?php
    foreach ($categories as $categorie) :
    ?>
    <tr>
        <td><?= $categorie['id']; ?></td>
        <td><?= $categorie['nom']; ?></td>
        <td>
            <a class="btn btn-primary"
               href="categorie-edit.php?id=<?= $categorie['id']; ?>">
               Modifier
            </a>
            <a class="btn btn-danger"
               href="categorie-delete.php?id=<?= $categorie['id']; ?>">
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
