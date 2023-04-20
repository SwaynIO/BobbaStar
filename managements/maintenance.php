<?PHP
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|
#|                                                                        #|
#|         Copyright © 2014-2023 - MyHabbo Tout droits réservés.          #|
#|																		  #|
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|

include("../config.php");

if(!isset($_SESSION['username']))
	{
		Redirect("".$url."/index");
		exit();
	}
	
	if($user['rank'] < 7) {
	Redirect("".$url."/managements/acces_neg");
	exit();
	}
	if($user['rank'] > 8) {              
	Redirect("".$url."/managements/acces_neg");
	exit();
	}	
$sql = $bdd->query("SELECT * FROM gabcms_maintenance WHERE id = '1'");
$m = $sql->fetch(PDO::FETCH_ASSOC);

if(isset($_GET['modif_etat'])) { $modif_etat = Secu($_GET['modif_etat']); }

if(isset($_GET['do'])) {
$do = Secu($_GET['do']);
if($do == "modif") {
	if(isset($_POST['info'])) {
   $info = Secu($_POST['info']);
      if($info != "") {
        $insertn1 = $bdd->prepare("INSERT INTO gabcms_stafflog (pseudo,action,date) VALUES (:pseudo, :action, :date)");
            $insertn1->bindValue(':pseudo', $user['username']);
            $insertn1->bindValue(':action', 'a modifié le texte de la page de maintenance');
            $insertn1->bindValue(':date', FullDate('full'));
        $insertn1->execute(); 
        $bdd->query("UPDATE gabcms_maintenance SET info = '".addslashes($info)."', auteur = '".$user['username']."', datestr = '".FullDate('full')."' WHERE id = '1'");
	  echo '<h4 class="alert_success">Mise à jour bien prise en compte.</h4>';
	  } else {
	  echo '<h4 class="alert_error">Merci de marquer une information.</h4>';
	  }
  }
}
}
if(isset($_GET['modif_etat'])) {
	if($modif_etat == "Non") {
      if($modif_etat == "Non" && $m['activ'] == "Oui") {
		if($user['rank'] >= '7') {
        $insertn1 = $bdd->prepare("INSERT INTO gabcms_stafflog (pseudo,action,date) VALUES (:pseudo, :action, :date)");
            $insertn1->bindValue(':pseudo', $user['username']);
            $insertn1->bindValue(':action', 'a <b>désactivé</b> la maintenance');
            $insertn1->bindValue(':date', FullDate('full'));
        $insertn1->execute(); 
        $bdd->query("UPDATE gabcms_maintenance SET activ = 'Non' WHERE id = '1'");
	    echo '<h4 class="alert_success">Mise à jour bien prise en compte.</h4>';
		} else {
		echo '<h4 class="alert_error">Tu n\'as pas le rank requis.</h4>';
		} } else {
		echo '<h4 class="alert_error">La maintenance n\'est pas active pour pouvoir l\'enlever.</h4>';
		}	
	}
}
if(isset($_GET['modif_etat'])) {
	if($modif_etat == "Oui") {
      if($modif_etat == "Oui" && $m['activ'] == "Non") {
		if($user['rank'] >= '7') {
        $insertn1 = $bdd->prepare("INSERT INTO gabcms_stafflog (pseudo,action,date) VALUES (:pseudo, :action, :date)");
            $insertn1->bindValue(':pseudo', $user['username']);
            $insertn1->bindValue(':action', 'a <b>activé</b> la maintenance');
            $insertn1->bindValue(':date', FullDate('full'));
        $insertn1->execute(); 
        $bdd->query("UPDATE gabcms_maintenance SET activ = 'Oui' WHERE id = '1'");
	    echo '<h4 class="alert_success">Mise à jour bien prise en compte.</h4>';
		} else {
		echo '<h4 class="alert_error">Tu n\'as pas le rank requis.</h4>';
		} } else {
		echo '<h4 class="alert_error">La maintenance n\'est pas désactivé pour pouvoir l\'activer.</h4>';
		}	
	}
}
?>
<link rel="stylesheet" href="css/contenu.css<?php echo '?'.mt_rand(); ?>" type="text/css" media="screen" /><body>
<span id="titre">Maintenance</span><br/>
Affiches un message sur la page de maintenance.<br/><br/>
 <script type="text/javascript" src="editeur_html/jscripts/tiny_mce/tiny_mce.js"></script>
 
 <form name='editor' method='post' action="?do=modif"><br/>
<td width='100' class='tbl'><b>Message que tu souhaites afficher :</b><br/><br/></td>
<td width='80%' class='tbl'><textarea name="info" rows=4 cols=75 ><?PHP echo nl2br($m['info']);?></textarea><br/></td>
<br/><br/>
<td align='center' colspan='2' class='tbl'>
<input type='submit' name='submit' value='Modifier' class='submit'></form><hr/>
Actives ou désactives la maintenance.<br/><br/>
<?PHP if($m['activ'] == "Non") { ?><a href="<?PHP echo $url ?>/managements/maintenance?modif_etat=Oui"><img src="<?PHP echo $url ?>/managements/img/images/activer.png" /></a><?PHP } ?> 
<?PHP if($m['activ'] == "Oui") { ?><a href="<?PHP echo $url ?>/managements/maintenance?modif_etat=Non"><img src="<?PHP echo $url ?>/managements/img/images/desactiver.png" /></a><?PHP } ?>
<br/>

</body>
</html>
