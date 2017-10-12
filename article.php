<?php
require("inc/init.inc.php");

// Déclaration de variables vides pour affichage dans les values du formulaire
$id_article = "";
$reference = "";
$categorie = "";
$titre = "";
$description = "";
$couleur = "";
$taille = "";
$sexe = "";
$photo = "";
$prix = "";
$stock = "";

// controle sur tous les champs provenant du formulaire (sauf bouton validation) pour voir si ils existent
if(isset($_POST['id_article']) && isset($_POST['reference']) && isset($_POST['categorie']) && isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['couleur']) && isset($_POST['taille']) && isset($_POST['sexe']) && isset($_POST['prix']) && isset($_POST['stock']))
{
  $id_article = $_POST['id_article'];
  $reference = $_POST['reference'];
  $categorie = $_POST['categorie'];
  $titre = $_POST['titre'];
  $description = $_POST['description'];
  $couleur = $_POST['couleur'];
  $taille = $_POST['taille'];
  $sexe = $_POST['sexe'];
  $prix = $_POST['prix'];
  $stock = $_POST['stock'];
  echo '<h1>OK</h1>';

} // fin de if isset

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
echo '<pre>'; print_r($_POST) ; echo '</pre>';

?>
    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-download-alt" style="color:plum;"></span>
          Enregistrement d\'un article
        </h1>

        <!-- ##### PHP ##### -->
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            <!--En cas de pièce jointe, on met type file et on ajoute enctype="multipart/form-data"-->
          <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group">
            <!--id_article caché (hidden) et sans label-->
                <input type="hidden" name="id_article" id="id_article" class="form-control" value="<?php echo $id_article;?>" />
            </div>

            <div class="form-group">
                <label for="reference">Reference</label>
                <input type="text" name="reference" id="reference" class="form-control" value="<?php echo $reference;?>" />
            </div>

            <div class="form-group">
                <label for="categorie">Categorie</label>
                <select name="categorie" id="categorie" class="form-control" />
                  <option value="pant">Pantalons</option>
                  <option value="tee"<?php if($categorie=='tee'){echo 'selected'; } ?>>Tee Shirt</option>
                  <option value="chem"<?php if($categorie=='chem'){echo 'selected'; } ?>>Chemise</option>
                  <option value="swe"<?php if($categorie=='swe'){echo 'selected'; } ?>>Sweet</option>
                </select>
            </div>

            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" name="titre" id="titre" class="form-control" value="<?php echo $titre;?>" />
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?php echo $description;?></textarea>
              </div>

              <div class="form-group">
                <label for="couleur">Couleur</label>
                <select name="couleur" id="couleur" class="form-control" />
                  <option value="rouge">Rouge</option>
                  <option value="vert"<?php if($couleur=='vert'){echo 'selected'; } ?>>Vert</option>
                  <option value="bleu"<?php if($couleur=='bleu'){echo 'selected'; } ?>>Bleu</option>
                  <option value="jaune"<?php if($couleur=='jaune'){echo 'selected'; } ?>>Jaune</option>
                  <option value="blanc"<?php if($couleur=='blanc'){echo 'selected'; } ?>>Blanc</option>
                  <option value="noir"<?php if($couleur=='noir'){echo 'selected'; } ?>>Noir</option>
                </select>
            </div>

            <div class="form-group">
                <label for="taille">Taille</label>
                <select name="taille" id="taille" class="form-control" />
                  <option value="s"<?php if($taille=='s'){echo 'selected'; } ?>>Small</option>
                  <option value="m"<?php if($taille=='m'){echo 'selected'; } ?>>Medium</option>
                  <option value="l"<?php if($taille=='l'){echo 'selected'; } ?>>Large</option>
                  <option value="xl"<?php if($taille=='xl'){echo 'selected'; } ?>>XLarge</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sexe">Vous êtes</label>
                <select name="sexe" id="sexe" class="form-control" />
                  <option value="m">Homme</option>
                  <option value="f" <?php if($sexe=='f'){echo 'selected'; } ?>>Femme</option>
                </select>
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo" class="form-control" value="<?php echo $photo;?>" />
            </div>

            <div class="form-group">
                <label for="prix">Prix</label>
                <input type="text" name="prix" id="prix" class="form-control" value="<?php echo $prix;?>" />
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="text" name="stock" id="stock" class="form-control" value="<?php echo $stock;?>" />
            </div>


              <div class="form-group">
                <button class="form-control btn btn-info"><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span>Inscription<span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span></button>
              </div>

          </form>
        </div><!-- /.col-sm-4 -->
      </div><!-- /.row -->

    </div><!-- /.container -->

<?php

require("inc/footer.inc.php"); 