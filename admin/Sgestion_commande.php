<?php
require("../inc/init.inc.php");

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à la page
if(!utilisateur_est_admin())
{
  header("location:../connexion.php");
  exit(); //permet d'arrêter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}



//*******************************************************
//                  VARIABLES VIDES
//*******************************************************
// Déclaration de variables vides pour affichage dans les values du formulaire
$id_article = "";
$reference = "";
$categorie = "";
$titre = "";
$description = "";
$couleur = "";
$taille = "";
$sexe = "";
$photo_bdd = "";
$prix = "";
$stock = "";

// variable erreur
$erreur="";



// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("../inc/header.inc.php"); 
require("../inc/nav.inc.php");    
//echo '<pre>'; print_r($_POST) ; echo '</pre>';
//echo '<pre>'; print_r($_FILES) ; echo '</pre>';
?>
    <div class="container">

      
      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-pushpin" style="color:plum;"></span>Gestion des commandes</h1>
        <hr/>

        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <div class="row">
        <div class="col-sm-12">
          <table border="1" style="width: 80%; margin: 0 auto; border-collapse: collapse; text-align: center;">
            <tr>
              <th colspan="3">Commande</th>
              <th colspan="4">Details commande</th>
              <th colspan="5">Membre</th>
              <th>Etat de la commande</th>
            </tr>
            <tr>
              <td>id_commande</td>
              <td>Date </td>
              <td>Montant</td>
              <td>id_article</td>
              <td>Titre</td>
              <td>Photo</td>
              <td>Quantité</td>
              <td>id_membre</td>
              <td>pseudo</td>
              <td>Adresse</td>
              <td>Ville</td>
              <td>Code postal</td>
              <td>Etat commande</td>
            </tr>
            <?php
              $result_com = $pdo->query("SELECT * FROM commande");
              $recup_commandes = $result_com->fetchall(PDO::FETCH_ASSOC);
              //echo '<pre>'; echo print_r($recup_commandes); echo '</pre>';
              foreach($recup_commandes AS $com)
              {
                echo '<tr>';
                echo '<td>' . $com['id_commande'] . '</td>';
                echo '<td>' . $com['date'] . '</td>';
                echo '<td>' . $com['montant'] . '</td>';
                echo '</tr>';
              }

              $result_details = $pdo->query("SELECT * FROM details_commande WHERE id_commande = '$com['id_commande']'");
              $recup_details_com = $result_details->fetchall(PDO::FETCH_ASSOC);
              echo '<pre>'; echo print_r($recup_details_com); echo '</pre>';
            
            ?>
          </table>
        </div><!-- /.col-sm-12 -->
      </div><!-- /.row -->

      
    </div><!-- /.container -->

<?php

require("../inc/footer.inc.php"); 