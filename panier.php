<?php
require("inc/init.inc.php");

$erreur = "";

//vider le panier
                  
if(isset($_GET['action']) && $_GET['action'] == 'vider_panier' && (!empty($_SESSION['panier']['id_article'])))
{
  unset($_SESSION['panier']);
}

 // retirer un article du panier
if(isset($_GET['action']) && $_GET['action'] == "retirer" && !empty($_GET['id_article']))
{
  retirer_article_du_panier($_GET['id_article']);
}

// création panier Cf. function.inc.php_ini_loaded_file
creation_panier();

if(isset($_POST['ajout_panier']))
{
  // si l'indice existe dans POST alors l'utilisateur a cliqué sur le bouton ajouter au panier (depuis la page fiche_article.php)
  $infos_article = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
  $infos_article->bindParam(":id_article", $_POST['id_article'], PDO::PARAM_STR);
  $infos_article->execute();

  $article = $infos_article->fetch(PDO::FETCH_ASSOC);

  // ajout de TVA sur  le prix
  $article['prix'] = $article['prix'] * 1.2;

  // fonction pour ajouter un article au panier cf.fonction.inc
  ajouter_un_article_au_panier($_POST['id_article'], $article['prix'], $_POST['quantite'], $article['titre'], $article['photo']);
  // on redirige sur la même page pour perdre les infromationsdans post afin que l'utilisateur actualise la page (F5) l'article ne soit pas rentré une nouvelle fois
  header("location:panier.php");
}



// VALIDATION DU PAIEMENT DU PANIER
if(isset($_GET['action']) && $_GET['action'] == 'payer' && !empty($_SESSION['panier']['prix']))
{
  // si l'utilisateur clique sur le bouton payer le panier
  // 1ere action : vérification du stock disponible en comparaison des quantités demandées

  for($i = 0; $i < count($_SESSION['panier']['titre']); $i++)
  {
    $resultat = $pdo->query("SELECT * FROM article WHERE id_article = " . $_SESSION['panier']['id_article'][$i]);
    $verif_stock = $resultat->fetch(PDO::FETCH_ASSOC);

    if($verif_stock['stock'] < $_SESSION['panier']['quantite'][$i])
    {
      // sio n entre dans cette condition alors il y a un stock inférieur à la quantité demandée
      // 2 possibilités: stock à 0 ou stock inférieur à la quntité demandée
      if($verif_stock['stock'] > 0)
      {
        // il reste du stock alors on affecte directement le stock restant pour la quantité demandée
        $_SESSION['panier']['quantite'][$i] = $verif_stock['stock'];
        $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, la quantité de l\'article "' . $_SESSION['panier']['quantite'][$i]  . ' "a été modifiée car notre stock est insuffisant! <br/> Veuillez vérifer votre commande.</div>';
	
      }
      else{
        // si le stock est à 0, on enlève l'article panier
        
        $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, votre article "' . $_SESSION['panier']['titre'][$i]  . ' "a été supprimé de votre panier car nous sommes en rupture de stock! <br/> Veuillez vérifer votre commande.</div>';
        retirer_article_du_panier($_SESSION['panier']['id_article'][$i]);
        // si on enlève un article du panier, il est nécessaire de décrémenter la variable $i car avec array_splice (cf. retirer_article_du_panier() sur fonction.inc.php) les indices sont réordonnés
        $i--;
      }
      $erreur = true;
    }

  }
  if(!$erreur)
  {
    $id_membre = $_SESSION['utilisateur']['id_membre'];
    $montant_commande = montant_total();
    $pdo->query("INSERT INTO commande (id_membre, montant, date) VALUES ($id_membre, $montant_commande, NOW())");
    $id_commande = $pdo->lastInsertId(); // On récupère l'id inséré par la dernière requête
    $nb_tout_panier = count($_SESSION['panier']['titre']);
    for($i = 0; $i < $nb_tout_panier; $i++)
    {
      $id_article_commande = $_SESSION['panier']['id_article'][$i];
      $quantite_commande = $_SESSION['panier']['quantite'][$i];
      $prix_commande = $_SESSION['panier']['prix'][$i];
      $pdo->query("INSERT INTO details_commande (id_commande, id_article, quantite, prix) VALUES ($id_commande, $id_article_commande, $quantite_commande, $prix_commande)");

      // mise à jour du stock
      $pdo->query("UPDATE article SET stock = stock - $quantite_commande WHERE id_article = $id_article_commande");
    }
    unset($_SESSION['panier']);
  }
}

//echo '<pre>'; echo var_dump($_POST); echo '</pre>';
//echo '<pre>'; echo print_r($_POST); echo '</pre>';
//echo '<pre>'; echo var_dump($_SESSION); echo '</pre>';
//echo '<pre>'; echo print_r($_SESSION); echo '</pre>';





// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
?>
    <div class="container">

      <div class="starter-template">
        <h1>Panier</h1>
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <div class="row" >  
        <div class="col-sm-8 col-sm-offset-2">
          <table class="table table-bordered">
            <tr>
              <th colspan="7">PANIER</th>
            </tr>
            <tr>
              <th>Article</th>
              <th>Titre</th>
              <th>Quantité</th>
              <th>Prix unitaire</th>
              <th>Photo </th>              
              <th>Prix total par article </th>
              <th>Retirer</th>

            </tr>
            <?php
              // vérification si le panier est vide sur n'importe quel tableau array du dernier niveau (id_article, prix ou titre)
              if(empty($_SESSION['panier']['id_article']))
              {
                echo '<tr><th colspan="7" style="color:red;">Aucun article dans votre panier</th></tr>';
              }
              else{
                // sinon on affiche tous les produits dans un tableau html
                
                $taille_tableau = count($_SESSION['panier']['titre']);
                for($i = 0; $i < $taille_tableau; $i++)
                {
                  echo '<tr>';
                  $total_par_article = $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
                    echo '<td>' . $_SESSION['panier']['id_article'][$i] . '</td>';
                    echo '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
                    echo '<td>' . $_SESSION['panier']['quantite'][$i] . '</td>';
                    echo '<td>' . number_format($_SESSION['panier']['prix'][$i],2) . ' ' .'€' .'</td>';                  
                    echo '<td><img width="100" class="image-responsive" src="' . URL . 'photo/'. $_SESSION['panier']['photo'][$i] .'"></td>';
                    echo '<td>' . number_format($total_par_article) . '€</td>';
                    echo '<td><a href="?action=retirer&id_article=' . $_SESSION['panier']['id_article'][$i] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';
                  echo '</tr>';
                }
               

                // l'utilisateur doit être connecté pour accéder au bouton payer
                if(!utilisateur_est_connecte())
                {
                  echo '<tr><th colspan="7" style="color:red;">Vous devez être connecté pour effectuer des achats</th></tr>';
                }
                else{
                  echo '<tr>';
                    echo '<td colspan="7" style="text-align:center;"><a href="?action=payer">Payer</a></td>';                  
                  echo '</tr>';
                  }

                  echo '<tr>';
                    echo '<td colspan="7" style="text-align:center;"><a href="?action=vider_panier">Vider le panier</a></td>';
                  echo '</tr>';

                //vider le panier
                  
                  if(isset($_GET['action']) && $_GET['action'] == 'vider_panier' && (!empty($_SESSION['panier']['id_article'])))
                  {
                    unset($_SESSION['panier']);
                  }

                  // Affichage du prix total du panier
                  // pour aller plus loin, reprendre sur le doc de Matthieu la bidouille pour l'affichage du prix en format français et non anglais avec str_replace()
                  echo '<tr>';
                    echo '<td colspan="3" style="text-align:center;">Montant total du panier TTC </td>';
                    echo '<td colspan="4" style="text-align:center;">' . number_format(montant_total(), 2) . " " . '€' .  '</td>';
                  echo '</tr>';
              }

              // BOUTON PAYER : Ajouter une ligne du tableau qui affiche un lien a href (?action=payer) pour payer le panier si l'utilisateur est connecté. Sinon, afficher un texte pour proposer à l'utilisateur de s'inscrire ou de se connecter

              // BOUTON VIDER LE PANIER : Ajouter une ligne du tableau qui affiche un bouton vider le panier uniquement si le panier n'est pas vide. Et faire le traitement afin que si on clique sur le bouton, il faut vider le panier (unset())

            ?>
          </table><!-- /div class table -->
          <hr/>
          <p>Règlement par chèque uniquement ! <br/> A l'adresse: 18 Rue Geoffroy Lasnier 75004 Paris</p>
          <hr/>
          <p>
          <?php
          if(utilisateur_est_connecte())
          {
            // si l'utilisateur est connecté, on affiche son adresse de livraison
            echo '<adress><b>Votre adresse de livraison est: </b><br/>' . $_SESSION['utilisateur']['adresse'] . '<br/>' . $_SESSION['utilisateur']['cp'] . '<br/>' . $_SESSION['utilisateur']['ville'] . '</adress>';
          }
          ?>
          </p>
        </div><!-- /.col-sm-8 -->
      </div><!-- /.row -->

    </div><!-- /.container -->

<?php

require("inc/footer.inc.php"); 

// mettre une photo de l'article dans la page panier
// afficher le prix total pour chaque article
// mes des filtres de recherche sur la page boutique  (couleur / taille / sexe / prix /mots cles)








