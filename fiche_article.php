<?php
require("inc/init.inc.php");

$message = "";


// On vérifie si l'indice id_article existe dans GET ou s'il n'est pas vide || on teste aussi la valeur est bien un chiffre
if(empty($_GET['id_article']) || !is_numeric($_GET['id_article']))
{
  header("location:boutique.php");
}
// rcupération des informations de l'article en bdd
$id_article = $_GET['id_article'];
$recup_article = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
$recup_article->bindParam(":id_article", $id_article, PDO::PARAM_STR);
$recup_article->execute();

// vérification si on a bien récupéré un article ou si nous avons une réponse vide (exemple changmeemnt d'idi_article dans l'URL par l'utilisateur)

if($recup_article->rowCount() < 1)
{
  // si c'est vide on redirige l'utilisatuer sur la page boutique
  header("location:boutique.php");
}

$article = $recup_article->fetch(PDO::FETCH_ASSOC);
echo '<pre>'; print_r($article); echo '</pre>';
// Exo : afficher les infos de l'article sauf le stock
// Exo : mettre un retour vers la séléction dans la boutique

$id_article = $article['id_article'];
$reference = $article['reference'];
$categorie = $article['categorie'];
$titre = $article['titre'];
$description = $article['description'];
$couleur = $article['couleur'];
$taille= $article['taille'];
$sexe = $article['sexe'];
$photo = $article['photo'];
$prix = $article['prix'];

if($sexe == 'm')
{
  $sexe = 'Masculin';
}else{
  $sexe = 'Féminin';
}

if($categorie == 'tee')
{
  $categorie = 'Tee Shirt';
}
elseif($categorie == 'pant')
{
  $categorie = 'Pantalons';
}
elseif($categorie == 'chem')
{
  $categorie = 'Chemise';
}
elseif($categorie == 'swe')
{
  $categorie = 'Hauts';
}

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
?>
    <div class="container">

      <div class="starter-template">
        <h1>Fiche article</h1>
        <p><a href="#" class="btn btn-primary" role="button">Retour à votre sélection</a></p>
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <div class="row">
        <div class="col-sm-7 col-md-5">
          <div class="thumbnail">
            <img src="<?= URL . 'photo/' .  $photo; ?>" alt="...">
            <div class="caption">
              <h3><?= $titre; ?></h3>
              <p>ID article : <?= $id_article; ?></p><br/>
              <p>Référence : <?= $reference; ?></p><br/>              
              <p>Catégorie : <?= $categorie; ?></p><br/>         
              <p>Description : <?= $description; ?></p><br/>
              <p>Taille : <?= $taille; ?></p><br/>
              <p>Sexe : <?= $sexe; ?></p><br/>
              <p>Prix : <?= $prix; ?></p><br/>
              
              
              <?php
              $options = "";
              for($i = 1; $i <= $article['stock'] && $i <8; $i++)
              {
                $options .= '<option>' . $i . '</option>';
              }

              // on ajoute le formulaire d'ajout au panier suelement si le stock est supérieur à 0
              if($article['stock'] > 0)
              {
              ?>

              <p>
              <!--Formulaire d'ajout au panier-->
              <form method="post" action="panier.php">

              <!--on récupère l'id_article dansun champ caché afin de savoir ensuite quel est le produit qui a été ajouté"-->
              <input type="hidden" name="id_article" value="<?= $article['id_article'];?>"  />

              <select name="quantite" class="form-control">
                  <?= $options;?> 
              </select>
              <input type="submit" name="ajout_panier" value="Ajouter au panier" class="form-control btn btn-warning" />
              </form>

              <?php 
              }
              else
              {
                echo '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Cet article est en rupture de stock !</div>';
              }
               ?>
               <hr/>
               <a href="boutique.php?categorie=<?= $article['categorie'];?>" class="form-control btn btn-success">Retour à votre sélection</a>
    
              </p>

            </div>
          </div>
        </div>
      </div>

    </div><!-- /.container -->

<?php

require("inc/footer.inc.php"); 








