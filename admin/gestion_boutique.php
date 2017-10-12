<?php
require("../inc/init.inc.php");

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à la page
if(!utilisateur_est_admin())
{
  header("location:../connexion.php");
  exit(); //permet d'arrêter l'exécution du script au cas où une personne malveillante ferait des injections via GET
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un produit
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_article']) && is_numeric($_GET['id_article']))
{
	// is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POSt sont toujours de type string)
	
	// on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
	$id_article = $_GET['id_article'];
	$article_a_supprimer = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
	$article_a_supprimer->bindParam(":id_article", $id_article, PDO::PARAM_STR);
	$article_a_supprimer->execute();
	
	$article_a_suppr = $article_a_supprimer->fetch(PDO::FETCH_ASSOC);
	// on vérifie si la photo existe
	if(!empty($article_a_suppr['photo']))
	{
		// on vérifie le chemin si le fichier existe
		$chemin_photo = RACINE_SERVEUR . 'photo/' . $article_a_suppr['photo'];
		// $message .= $chemin_photo;
		if(file_exists($chemin_photo))
		{
			unlink($chemin_photo); // unlink() permet de supprimer un fichier sur le serveur.			 
		}
	}
	$suppression = $pdo->prepare("DELETE FROM article WHERE id_article = :id_article");	
	$suppression->bindParam(":id_article", $id_article, PDO::PARAM_STR);
	$suppression->execute();
	$message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">L\'article numéro ' . $id_article . ' a bien été supprimé</div>';
	
	// on bascule sur l'affichage du tableau
	$_GET['action'] = 'affichage';
	
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
// RECUPERATION DES INFORMATIONS D'UN ARTICLE A MODIFIER
//*******************************************************
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_article']) && is_numeric($_GET['id_article']))
{
	$id_article = $_GET['id_article'];
	$article_a_modif = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
	$article_a_modif->bindParam(":id_article", $id_article, PDO::PARAM_STR);
	$article_a_modif->execute();
	$article_actuel = $article_a_modif->fetch(PDO::FETCH_ASSOC);
	
	$id_article = $article_actuel['id_article'];
	$reference = $article_actuel['reference'];
	$categorie = $article_actuel['categorie'];
	$titre = $article_actuel['titre'];
	$description = $article_actuel['description']; 
	$couleur = $article_actuel['couleur'];
	$taille = $article_actuel['taille'];
	$sexe = $article_actuel['sexe'];
	$prix = $article_actuel['prix'];
	$stock = $article_actuel['stock'];
	// on récupère la photo de l'article dans une nouvelle variable
	$photo_actuelle = $article_actuel['photo'];
}

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

  // Contrôle anti doublons sur la référence si on est dans le cas d'un ajout car lors de la modification la refernce existera touojurs
  $verif_doublon_ref = $pdo->prepare("SELECT * FROM article WHERE reference = :reference");
  $verif_doublon_ref->bindParam(":reference", $reference, PDO::PARAM_STR);
  $verif_doublon_ref->execute();

  if($verif_doublon_ref->rowCount() > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout' ) 
  // rq on fait un rowCount car un pdostatement ne retourne false que si on a fait une erreur dans la requete
  {
      $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Cette référence existe déjà, choisissez une autre référence !</div>';
      $erreur = true;
  }

  // on vérifie que le titre n'est pas vide
  if(empty($titre))
  {
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Le titre est obligatoire!</div>';
    $erreur = true;
  }

  // récupération de l'ancienne photo dans le cadre d'une modification
  if(isset($_GET['action']) && $_GET['action'] == "modification")
  {
    if(isset($_POST['ancienne_photo']))
    {
      $photo_bdd = $_POST['ancienne_photo'];
    }
  }


  // On cherche à savoir si l'admin a mis une image ou non (rq ce n'est pas obligatoire)
  if(!empty($_FILES['photo']['name']))
  {
    // si ce n'est pas vide, alors un fichier a bien été chargé via le formulaire

    // on concatène la reference sur le titre afin de jamais avoir un fichier avec un nom déjà existant sur le serveur. Dans la bdd, la photo sera désignée par la référence de l'article, qui est unique, couplée au nom de la photo+extension
    $photo_bdd = $reference . $_FILES['photo']['name'];

    // vérification de l'extension de l'image (extensions acceptées: jpg, jpeg, png, gif)
    $extension = strrchr($_FILES['photo']['name'], '.'); // cette fonction prédéfinie permet de découper une chaine de caractères fournie en deuxième argument (ici le .)
    // Attention, cette fonction découpera la chaine à partir de la dernière occurence du 2eme argument (donc nous renvoit la chaine comprise apres le dernier point trouvé)
    // emple: maphoto.jpg => on récupère .jpeg2wbmp
    // var_dump($extension);

    // On transforme $extension afin que tous les caractères soient en minuscule
    $extension = strtolower($extension); // à l'inverse strtoupper()
    // on enlève le . 
    $extension = substr($extension, 1); // exemple .jpg devient jpg
    // les extensions acceptées
    $tab_extension_valide = array ("jpg", "jpeg", "png", "gif");
    // nous pouvons donc vérifier si $extension fait partie des valeurs autorisées dans $tab_extension_valide
    $verif_extension = in_array($extension, $tab_extension_valide); // in_array vérifie si une valeur fournie en premier argument fait partie des valeurs connues contenues dans un tableau array fourni en ddeuxxième argument

    if($verif_extension && !$erreur)
    {
      // si $verif_extension = true et que $erreur n'est pas = à true, on lance la copie vers l'endroit où on veut l'amener (avec la constante)
      $photo_dossier = RACINE_SERVEUR . 'photo/' . $photo_bdd;

      // depuis l'espace temporaire où elle se trouve à partir du moment où l'administrateur la télécharge via le formulaire
      copy($_FILES['photo']['tmp_name'], $photo_dossier);
      // copy permet de copier un fichier depuis un emplacement fourni en premier argument vers un autre emplacement fourni en deuxième argument
    }
    elseif(!verif_extension){
      $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Extension de fichier photo non valide !</div>';
      $erreur = true;
    }

  }
 
// Insertion d'un produit dans la base de données
if(!$erreur) // s'il n'y a pas d'erreurs
{
    // pour crypter (par hachage) le mdp
    // $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    // pour voir la gestion du mdp lors de la connexion, voir le fichier connexion_avec_mdp_hash.php à récuperer de Mathieu
    if(isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
    $enregistrement = $pdo->prepare("INSERT INTO article (reference, categorie, titre, description, couleur, taille, sexe, prix, stock, photo) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :prix, :stock, :photo)");
    }
    elseif(isset($_GET['action']) && $_GET['action'] == 'modification'){
      $enregistrement = $pdo->prepare("UPDATE article SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, sexe = :sexe, prix = :prix, stock = :stock, photo = :photo WHERE id_article = :id_article");
      $id_article = $_POST['id_article'];
      $enregistrement->bindParam(":id_article", $id_article, PDO::PARAM_STR);
    }

    $enregistrement->bindParam(":reference", $reference, PDO::PARAM_STR);
    $enregistrement->bindParam(":categorie", $categorie, PDO::PARAM_STR);
    $enregistrement->bindParam(":titre", $titre, PDO::PARAM_STR);
    $enregistrement->bindParam(":description", $description, PDO::PARAM_STR);
    $enregistrement->bindParam(":couleur", $couleur, PDO::PARAM_STR);
    $enregistrement->bindParam(":taille", $taille, PDO::PARAM_STR);
    $enregistrement->bindParam(":sexe", $sexe, PDO::PARAM_STR);
    $enregistrement->bindParam(":prix", $prix, PDO::PARAM_STR);
    $enregistrement->bindParam(":stock", $stock, PDO::PARAM_STR);
    $enregistrement->bindParam(":photo", $photo_bdd, PDO::PARAM_STR);
    $enregistrement->execute();
}

} // fin de if isset

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("../inc/header.inc.php"); 
require("../inc/nav.inc.php");    
//echo '<pre>'; print_r($_POST) ; echo '</pre>';
//echo '<pre>'; print_r($_FILES) ; echo '</pre>';
?>
    <div class="container">

      
      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-download-alt" style="color:plum;"></span>Enregistrement d'un article</h1>
        <hr/>
        <a href="?action=ajout" class="btn btn-warning">Ajouter un produit</a>
        <a href="?action=affichage" class="btn btn-info">Afficher les produits</a>

       

        <!-- ##### PHP ##### -->
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      
        
        <?php

        if(isset($_GET['action']) && $_GET['action'] == 'affichage')
        {
          $resultat = $pdo->query("SELECT * FROM article");
          echo 'Nombre d\'articles présents dans la base:' . " " .$resultat->rowCount();
          
          echo '<hr>';
          echo '<div class="row">';
          echo '<div class="col-sm-12">';

        // balise d'ouverture du tableau
        echo '<table border="1" style="width: 80%; margin: 0 auto; border-collapse: collapse; text-align: center;">';
        // premiere ligne du tableau pour le nom des colonnes
        echo '<tr>';
        // récupération du nombre de colonnes dans la requete:
        $nb_col = $resultat->columnCount();

        // création des variables modifier et supprimer
        $modifier = 'Modifier';
        $supprimer = 'Supprimer';

        for($i = 0; $i < $nb_col; $i++)
        {
            //echo '<pre>'; echo print_r($resultat->getColumnMeta($i)); echo '</pre>'; echo '<hr/>';
            $colonne = $resultat->getColumnMeta($i); // on récupére les informations del a colonne en cours afin ensuite de demander le name
            echo '<th style="padding: 10px;">' . $colonne['name'] . '</th>';
        }
        
            echo '<th style="padding: 10px;">' . $modifier . '</th>';
            echo '<th style="padding: 10px;">' . $supprimer . '</th>';

        echo '</tr>';

        while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
        {
            echo '<tr>';

            foreach($ligne AS $indice => $info)
            {
              if($indice == 'photo')
              {
                echo '<td style="padding: 10px;"><img src="' . URL . 'photo/'. $info . '" class="img-thumbnail" width="140" /></td>';
              }
              elseif($indice == "description"){
                echo '<td>' . substr($info, 0, 56) . '...<a href="#">Voir la fiche produit</a></td>';
              }
              elseif($indice == "prix")
              {
                echo '<td><span style="color: red">' . $info . '€</span></td>';
              }
              else{
                echo '<td style="padding: 10px;">' . $info . '</td>';
                }
            }

            echo '<td><a href="?action=modification&id_article=' . $ligne['id_article'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></td>';

            echo '<td><a onclick="return(confirm(\'Etes vous sûr\'));" href="?action=suppression&id_article=' . $ligne['id_article'] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';

            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
        }
         
        ?>

        <?php 
        // affichage du formulaire d'enregistrement
        if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) { ?>  


      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            <!--En cas de pièce jointe, on met type file et on ajoute enctype="multipart/form-data"-->
          <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group">
            <!--id_article caché (hidden) et sans label-->
                <input type="hidden" name="id_article" id="id_article" class="form-control" value="<?php echo $id_article;?>" />
            </div>

            <div class="form-group">
                <span style="color: red; font-size: 11px; margin-bottom:20px;" class="glyphicon glyphicon-asterisk">(Champs obligatoires)</span><br/>
                <label for="reference">Reference<span style="color: red; font-size: 11px;" class="glyphicon glyphicon-asterisk"></span></label>
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
                <label for="titre">Titre<span style="color: red; font-size: 11px;" class="glyphicon glyphicon-asterisk"></span></label>
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

            <?php 
          // affichage de la photo actuelle dans le cas d'une modification d'article
            if(isset($article_actuel)) // si cette variable existe alors nous sommes dans le cas d'une modification
            {
              echo '<div class="form-group">';
              echo '<label>Photo actuelle</label><br />';
              echo '<img src="' . URL . 'photo/' . $photo_actuelle . '" class="img-thumbnail" width="210" />';
              // on crée un champ caché qui contiendra la nom de la photo afin de le récupérer lors de la validation du formulaire.
              echo '<input type="hidden" name="ancienne_photo" value="' . $photo_actuelle . '" />';
              echo '</div>';
            }
				  ?>


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

      <?php
      } // fermeture de est-ce que l'admin a cliqué sur Ajouter un produit
      ?>


    </div><!-- /.container -->

<?php

require("../inc/footer.inc.php"); 