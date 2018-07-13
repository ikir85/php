<?php

$stmt = $pdo ->query('SELECT * FROM categories');
$categoriesMenu = $stmt -> fetchAll();
?>

<div class='navbar-collapse'>
    <ul class="navbar-nav">
        <?php
        foreach($categoriesMenu as $categoriesMenu) :
        ?>
      <li class="nav-item">
          <a class="nav-link" href="<?= RACINE_WEB; ?>categorie.php?id=<?=$categoriesMenu['id']; ?>">
              <?=$categoriesMenu['nom']; ?>
          </a>
          </li>
       
       <?php
        
        endforeach;
        ?>
        
        
        
        
        
        
        
        
        
        
    </ul>
    
</div>