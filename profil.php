<?php
require("inc/init.inc.php");

// on vérifie si l'utilisateur est connecté sinon on le redirige sur la page connection
if(!utilisateur_est_connecte())
{
    header("location:connexion.php");
}

$statut = $_SESSION['utilisateur']['statut'];
if($statut == 1)
{
    $role = "Administrateur";
}
else{
    $role = "Membre";
}

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
?>
    <div class="container">

      <div class="starter-template">
        <h1>Profil (<?php echo $role; ?>)</h1>
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>
      <div class="row">
        <div class="col-sm-4">
            <ul>
                <li>Pseudo :<?php echo $_SESSION['utilisateur']['pseudo']; ?> </li>
                <li>Nom :<?php echo $_SESSION['utilisateur']['nom']; ?> </li>
                <li>Prénom :<?php echo $_SESSION['utilisateur']['prenom']; ?> </li>
                <li>Adresse :<?php echo $_SESSION['utilisateur']['adresse']; ?> </li>
                <li>Ville :<?php echo $_SESSION['utilisateur']['ville']; ?> </li>
                <li>Code postal :<?php echo $_SESSION['utilisateur']['cp']; ?> </li>
            </ul>
        </div>
      </div><!--.row-->
    </div><!-- /.container -->

<?php

require("inc/footer.inc.php"); 