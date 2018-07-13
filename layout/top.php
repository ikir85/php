<!DOCTYPE html>
    <head>  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">   
    <title>Boutique</title>
    <style>
      .table_cat {width: 85%; margin: 0 auto; margin-top: 50px;}  
      .table_cat th{text-align: center; color:white; background: #343a40;}
      .table_cat td, .th_produits td{ text-align: center}
      .inscrip_form .form-group,.inscrip_form .form-btn-group{width: 50%; margin: 0 auto}
      .inscrip_form .form-btn-group{margin-top: 20px}
      
      .inscrip_form  .btn-primary {
       color: #fff;
       background-color: #343a40;
       border-color: #343a40;
       }
       
       .inscrip_form .btn-primary:hover {background-color: #6c757d;}
       .table_cat th.th_produits,.table_cat td.th_produits{ width: 35%;}
       .img_produit { width: 50%; margin: 0 auto; margin-top: 25px}
      
       .card-img-top{
           max-width: 150px;
           margin: 0 auto;
       } 
       
       .panier_quant{width: 50px; margin: 0 auto}
       .cardQ-formTd{width: 70px}
       
       .cardQ-formTd .form-btn-group{ margin-top: 0}
       
       .valid_commande{
            margin-top: 45px;
            margin-right: 7.5%;
       }
       
       select[name^=statut] {    
       margin-right: 15px
       }  
    </style>
    </head>
    <body>
         <?php
            if (isUserAdmin()):
         ?>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container navbar-nav">
                    <a class="navbar-brand" href="#">Admin</a>
                    <div class="navbar-collapse">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_WEB; ?>admin/categories.php">
                                   Gestion catégories
                                </a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_WEB; ?>admin/produits.php">
                                   Gestion produits
                                </a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_WEB; ?>admin/commandes.php">
                                   Gestion des comptes
                                </a>
                             </li>
                        </ul>
                    </div>
                </div>
            </nav>
         <?php 
        endif;
        ?>
            <nav class="navbar navbar-expand-md navbar-dark bg-secondary">
                <div class="container navbar-nav">
                    <a class="navbar-brand" href="<?= RACINE_WEB; ?>index.php">Boutique</a>
                    <?php
                      include __DIR__. '/menu-categorie.php';
                    ?>
                    <ul class="navbar-nav">
                        <?php
                         if (isUserConnected()):
                        ?>
                         <li class="nav-item">
                                <a class="nav-link">
                                   <?= getUserFullName(); ?>
                                </a>
                            </li>
                          <li class="nav-item">
                            <a class="nav-link" href="<?= RACINE_WEB; ?>deconnexion.php">Deconnexion</a>
                          </li>
                        
                        <?php
                         else :
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RACINE_WEB; ?>inscription.php">Inscription</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RACINE_WEB; ?>connexion.php">Connexion</a>
                        </li>
                        <?php
                         endif;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RACINE_WEB; ?>panier.php">Panier</a>
                        </li>
                        
                    </ul>
                </div>
            </nav>
        <div class="container">
            <?php
            displayFlashMessage();
            ?>