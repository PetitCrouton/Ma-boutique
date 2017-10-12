<?php
require("../inc/init.inc.php");

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à la page
if(!utilisateur_est_admin())
{
  header("location:../connexion.php");
  exit(); //permet d'arrêter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}
//*******************************************************
//           Modification etat de commande
//*******************************************************

//echo '<pre>'; echo print_r($_POST); echo '</pre>';

if(isset($_POST['id_commande']) && isset($_POST['etat']))
{
  $id_commande = $_POST['id_commande'];
  $etat = $_POST['etat'];

  $prepare = $pdo->prepare("UPDATE commande SET etat = :etat WHERE id_commande = :id_commande");
  $prepare->bindParam(":id_commande", $id_commande, PDO::PARAM_STR);
  $prepare->bindParam(":etat", $etat, PDO::PARAM_STR);
  $prepare->execute();
  //$maj_etat = $prepare->fetch(PDO::FETCH_ASSOC);
  
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

//*******************************************************
//                 CHIFFRE D AFFAIRES
//*******************************************************

$requete_ca = $pdo->query("SELECT sum(montant) FROM commande");
$ca = $requete_ca->fetch(PDO::FETCH_ASSOC);
$ca = $ca['sum(montant)'];
//echo '<pre>'; print_r($ca); echo '</pre>';

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("../inc/header.inc.php"); 
require("../inc/nav.inc.php");    
//echo '<pre>'; print_r($_POST) ; echo '</pre>';
//echo '<pre>'; print_r($_FILES) ; echo '</pre>';

//*******************************************************
//                 BOUTON DE TRI
//*******************************************************

?>
    <div class="container">

      
      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-pushpin" style="color:plum;"></span>Gestion des commandes</h1>
        <hr/>

        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <p>Chiffre d'affaires : <?= $ca; ?> €</p>

      <div class="row">
        <div class="col-sm-12">
          <table style="width: 80%; margin: 0 auto; text-align: center;" class="table table-bordered">
            <tr>
              <th colspan="3">Commande</th>
              <th colspan="4">Details commande</th>
              <th colspan="5">Membre</th>
              <th colspan="2">Etat de la commande</th>
            </tr>
            <tr>
              <td>id_commande<a href="?action=tri_id_commande_up" class="btn btn-danger"><span class="glyphicon glyphicon-glyphicon glyphicon-arrow-up"></span></a> <a href="?action=tri_id_commande_down" class="btn btn-danger"><span class="glyphicon glyphicon-glyphicon glyphicon-arrow-down"></span></a></td>
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
              <td>Modif etat commande</td>
            </tr>
            <?php
            $order = "";
            if(isset($_GET['action']) && $_GET['action'] == 'tri_id_commande_up')
            {
              $order = " ORDER BY c.id_commande";
              
            }
            elseif(isset($_GET['action']) && $_GET['action'] == 'tri_id_commande_down')
            {
              $order = " ORDER BY c.id_commande DESC";
            }

            $result_com = $pdo->query("SELECT c.id_commande, c.date, c.montant, d.id_article, a.titre, a.photo, d.quantite, m.id_membre, m.pseudo, m.adresse, m.ville, m.cp, c.etat FROM commande c, details_commande d, membre m, article a WHERE c.id_membre= m.id_membre AND c.id_commande = d.id_commande AND d.id_article = a.id_article" . $order);
             
              $recup_commandes = $result_com->fetchall(PDO::FETCH_ASSOC);
              //echo '<pre>'; echo print_r($recup_commandes); echo '</pre>';
              foreach($recup_commandes AS $com)
              {
                echo '<tr>';
                echo '<td>' . $com['id_commande'] . '</td>';
                echo '<td>' . $com['date'] . '</td>';
                echo '<td>' . $com['montant'] . '</td>';
                echo '<td>' . $com['id_article'] . '</td>';
                echo '<td>' . $com['titre'] . '</td>';
                echo '<td><img width="60" class="image-responsive" src="' . URL. 'photo/' . $com['photo'] . '"/></td>';
                echo '<td>' . $com['quantite'] . '</td>';
                echo '<td>' . $com['id_membre'] . '</td>';
                echo '<td>' . $com['pseudo'] . '</td>';
                echo '<td>' . $com['adresse'] . '</td>';
                echo '<td>' . $com['ville'] . '</td>';
                echo '<td>' . $com['cp'] . '</td>';
                echo '<td>' . $com['etat'] . '</td>';
                echo 
                '<td>
                  <form method="post" action="">
                    <div class="form-group">
                      <input type="hidden" name="id_commande" value="' . $com['id_commande'] . '"/>     <select name="etat" id="etat" class="form-control">
                              <option value="en cours de traitement">En cours de traitement</option>
                              <option value="envoyé">Envoyé</option>
                              <option value="livré">Livré</option>
                        </select>
                              <div class="form-group">
                                <button type="submit" name="valider" id="valider" class="form-control btn btn-info">Valider</button>
                              </div>
                    </div>
                  </form>
                </td>';
                echo '</tr>';
              }

              
         
            ?>
          </table>
        </div><!-- /.col-sm-12 -->
      </div><!-- /.row -->

      
    </div><!-- /.container -->

<?php

require("../inc/footer.inc.php"); 