<?php
require("inc/init.inc.php");

// deconnexion de l'utilisateur

if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
    session_destroy();
}

// on vérifie si l'utilisateur est connecté auquel cas on le redirige sur la page profil
if(utilisateur_est_connecte())
{
    header("location:profil.php");
}

// Déclaration de variables vides pour affichage dans les values du formulaire
$pseudo = "";
$mdp = "";

// controle sur tous les champs provenant du formulaire (sauf bouton validation) pour voir si ils existent
if(isset($_POST['pseudo']) && isset($_POST['mdp']))
{
  $pseudo = $_POST['pseudo'];
  $mdp = $_POST['mdp'];

  $verif_connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo and mdp = :mdp");
  $verif_connexion->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
  $verif_connexion->bindParam(":mdp", $mdp, PDO::PARAM_STR);
  $verif_connexion->execute();

  if($verif_connexion->rowCount() > 0)
  {
      // si on a une ligne alors le pseudo et le mdp sont corrects
      $info_utilisateur = $verif_connexion->fetch(PDO::FETCH_ASSOC);
      $_SESSION['utilisateur'] = array();
      $_SESSION['utilisateur']['id_membre'] = $info_utilisateur['id_membre'];
      $_SESSION['utilisateur']['pseudo'] = $info_utilisateur['pseudo'];
      $_SESSION['utilisateur']['nom'] = $info_utilisateur['nom'];
      $_SESSION['utilisateur']['prenom'] = $info_utilisateur['prenom'];
      $_SESSION['utilisateur']['email'] = $info_utilisateur['email'];
      $_SESSION['utilisateur']['sexe'] = $info_utilisateur['sexe'];
      $_SESSION['utilisateur']['cp'] = $info_utilisateur['cp'];
      $_SESSION['utilisateur']['adresse'] = $info_utilisateur['adresse'];
      $_SESSION['utilisateur']['statut'] = $info_utilisateur['statut'];
      $_SESSION['utilisateur']['ville'] = $info_utilisateur['ville'];

      // on redirige sur profile
      header("location:profil.php");

    // même chose avec un foreach // à reprendre il y a un bug
   /* $_SESSION['utilisateur'] = array();
    foreach($info_utilisateur AS $indice => $valeur)
    {
        if($indice != 'mdp')
        {
        $_SESSION['utilisateur'][$indice] = $valeur;
        }
    }*/

  }
  else{
      $message .= '<div class="alert alert-danger" role="alert" style="margin-top: 20px;">Attention! Vos identifiants ne sont pas valides<br/> Veuillez recommencer la saisie !</div>';
  }

} // fin du isset

// c'est à partir de la ligne suivante que commencent les affichages dans la page
require("inc/header.inc.php"); 
require("inc/nav.inc.php");    
echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>
    <div class="container">

      <div class="starter-template">
        <h1>Connexion</h1>
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
                <button class="form-control btn btn-info"><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span>Connexion<span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span><span class="glyphicon glyphicon-star" style="color: red;"></span></button>
              </div>

          </form>
        </div><!-- /.col-sm-4 -->
      </div><!-- /.row -->
    </div><!-- /.container -->

<?php

require("inc/footer.inc.php"); 