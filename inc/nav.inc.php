<nav class="navbar navbar-inverse navbar-fixed-top">
 <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo URL; ?>boutique.php">Mon Echoppe</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">


            <li class="active"><a href="boutique.php">Accueil</a></li>
            <li><a href="<?php echo URL; ?>panier.php">Panier</a></li>


              <?php
                if(!utilisateur_est_connecte())
                {
              ?>
            <li><a href="<?php echo URL; ?>inscription.php">Inscription</a></li>
            <li><a href="<?php echo URL; ?>connexion.php">Connexion</a></li>
              <?php
                }
                else{
              ?>
              <li><a href="<?php echo URL; ?>profil.php">Profile</a></li>
              <li><a href="<?php echo URL; ?>connexion.php?action=deconnexion">Deconnexion</a></li>
              <?php
              } // fermeture du else du if(!utilisateur est connecte)

              // ajout des liens d'administration si l'utilisateur est admin
              if(utilisateur_est_admin())
              {
                echo '<li class="dropdown">      
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administration<span class="caret"></span></a>';
                echo '<ul class="dropdown-menu">';
                echo '<li><a href="'. URL.'admin/gestion_boutique.php">Gestion boutique</a></li>';
                echo '<li><a href="'. URL .'admin/gestion_commande.php">Gestion commande</a></li>';
                echo '<li><a href="'. URL .'admin/gestion_utilisateur.php">Gestion utilisateur</a></li>';
                echo '</ul></li>';
              }







              ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>