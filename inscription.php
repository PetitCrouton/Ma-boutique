<?php
require("inc/init.inc.php");

// Déclaration de variables vides pour affichage dans les values du formulaire
$pseudo = "";
$mdp = "";
$nom = "";
$prenom = "";
$email = "";
$sexe = "";
$ville = "";
$cp = "";
$adresse = "";

// controle sur tous les champs provenant du formulaire (sauf bouton validation) pour voir si ils existent
if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['sexe']) && isset($_POST['cp']) && isset($_POST['adresse']))
{
  $pseudo = $_POST['pseudo'];
  $mdp = $_POST['mdp'];
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $email = $_POST['email'];
  $sexe = $_POST['sexe'];
  $cp = $_POST['cp'];
  $adresse = $_POST['adresse'];

// variable de contrôle des erreurs
$erreur = "";

// contrôle sur la taille du pseudo (entre 4 et 14 caractères inclus)
$taille_pseudo = iconv_strlen($pseudo);
if($taille_pseudo < 4 || $taille_pseudo > 14)
{
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention, taille du pseudo incorrecte.<br/>Le pseudo doit contenir entre 4 et 14 caractères inclus</div>';
    $erreur = true;
}

// contrôle des caractères dans le pseudo (autorisés: a-z A-Z 0-9 _-.)
$verif_caracteres = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
/*
- preg_match() va vérifier les caractères contenus dans la variable pseudo selon une expression régulière fournie en premier argument.
- renvoit 1 si tout est ok, sinon 0
- expression : 
    # => permet d'indiquer le début et la fin de l'expression
    ^ => indique que la chaîne ($pseudo) ne peut commencer par ces caractères
    $ => indique que la chaîne ($pseudo) ne peut que finir par ces caractères
    + => indique que les caractères autorisés peuvent apparaître plusioeurs fois
   [] => contiennent les caractères autorisés
*/
if(!$verif_caracteres && !empty($pseudo))
{
    //on entre dans cette condition si $verif_caracteres contient 0 donc qu'il y a des caractères non autorisés
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Le pseudo contient des caractères non autorisés!</div>';
    $erreur = true;
}

// Contrôle des caractères de l'email

if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email))
{
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Le format de votre adresse mail n\'est pas valide !</div>';
    $erreur = true;
}

// Contrôle sur la disponibilité du pseudo
$pseudo_existe = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo");
$pseudo_existe->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
$pseudo_existe->execute();

if($pseudo_existe->rowCount() > 0) // rq on fait un rowCount car un pdostatement ne retourne false que si on a fait une erreur dans la requete
{
    $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Ce pseudo est déjà pris, choisissez un autre pseudo !</div>';
    $erreur = true;
}

// Insertion dans la base de données
if($erreur !== true) // s'il n'y a pas d'erreurs
{
    // pour crypter (par hachage) le mdp
    // $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    // pour voir la gestion du mdp lors de la connexion, voir le fichier connexion_avec_mdp_hash.php à récuperer de Mathieu
    $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, sexe , ville, cp, adresse, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :sexe , :ville, :cp, :adresse, 0)");
    $enregistrement->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(":mdp", $mdp, PDO::PARAM_STR);
    $enregistrement->bindParam(":nom", $nom, PDO::PARAM_STR);
    $enregistrement->bindParam(":prenom", $prenom, PDO::PARAM_STR);
    $enregistrement->bindParam(":email", $email, PDO::PARAM_STR);
    $enregistrement->bindParam(":sexe", $sexe, PDO::PARAM_STR);
    $enregistrement->bindParam(":ville", $ville, PDO::PARAM_STR);
    $enregistrement->bindParam(":cp", $cp, PDO::PARAM_STR);
    $enregistrement->bindParam(":adresse", $adresse, PDO::PARAM_STR);
    $enregistrement->execute();

    //on redirige sur la page connexion.php
    header("location:connexion.php");
} // fin du if pour insertion

} // fin de if isset

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
echo '<pre>'; print_r($_POST) ; echo '</pre>';

?>
    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user" style="color:plum;"></span>
          Inscription
        </h1>

        <!-- ##### PHP ##### -->
        <?php //echo $message; // messages destinés à l'utilisateur?>
        <?= $message;?> <!--Raccourci pour faire un echo, égal à la ligne au-dessus-->
      </div>

      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
          <form action="" method="post">

            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo;?>" />
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="text" name="mdp" id="mdp" class="form-control" value="<?php echo $mdp;?>" />
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom;?>" />
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom;?>" />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="<?php echo $email;?>" />
            </div>

            <div class="form-group">
                <label for="sexe">Vous êtes</label>
                <select name="sexe" id="sexe" class="form-control" />
                  <option value="m">Homme</option>
                  <option value="f" <?php if($sexe=='f'){echo 'selected'; } ?>>Femme</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" name="ville" id="ville" class="form-control" value="<?php echo $ville;?>" />
            </div>

            <div class="form-group">
                <label for="cp">Code postal</label>
                <input type="text" name="cp" id="cp" class="form-control" value="<?php echo $cp;?>" />
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea name="adresse" id="adresse" class="form-control"></textarea>
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