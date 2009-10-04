<?php
/*
+ ----------------------------------------------------------------------------+
|     esGlobalBan - Language File.
|
|     $Source: /cvsroot/banned/languages/French/lan_configuration.php,v $
|     $Revision: 1.0 $
|     $Date: 2009/07/02 30:36:39 $
|     $Author: Odonel $
+----------------------------------------------------------------------------+
*/
/*
$la_variable['email_valide'] = 'LE MESSAGE'
*/

$LANCONFIGURATION_001 = "Veuillez entrer une adresse mail valide !";
$LANCONFIGURATION_002 = "existe déjà";

$LANCONFIGURATION_003 = "Vous devez spécifiez un dossier pour les démos.";
$LANCONFIGURATION_004 = 'Vous devez spécifiez une taille limite pour les démos.';
$LANCONFIGURATION_005 = "Vous devez spécifiez un message lors d\'un ban.";
$LANCONFIGURATION_006 = 'You must specify the number of days to keep a pending ban banned for';
$LANCONFIGURATION_007 = 'Vous devez spécifiez un code de sécurité.';
$LANCONFIGURATION_008 = 'Vous devez spécifiez un préfixe de table pour SMF.';
$LANCONFIGURATION_009 = 'Vous devez spécifier un groupe de SMF qui ont les pleins pouvoirs.';
$LANCONFIGURATION_010 = 'Vous devez spécifier un groupe SMF qui auront les pouvoirs pour le Ban Manager.';
$LANCONFIGURATION_011 = 'Vous devez spécifier un groupe SMF qui auront les pouvoirs pour les admins.';
$LANCONFIGURATION_012 = 'Vous devez spécifier un groupe SMF qui auront les pouvoirs pour les membres.';
$LANCONFIGURATION_013 = "Vous devez spécifier un groupe de SMF qui n'ont pas de pouvoirs immédiat.";
$LANCONFIGURATION_014 = "Vous devez spécifier un code de création que vous devrez fournir aux membres souhaitant s\'enregistrer.";

$LANCONFIGURATION_015 = "Information sur la Version";
$LANCONFIGURATION_016 = 'Votre Version :';
$LANCONFIGURATION_017 = "Paramétrage Web !";
$LANCONFIGURATION_018 = 'Nom du site';
$LANCONFIGURATION_019 = "C\'est le nom de votre communauté qui apparaît dans la barre du titre du navigateur.";
$LANCONFIGURATION_020 = "Logo";
$LANCONFIGURATION_021 = "S\'affichera en haut à gauche.";
$LANCONFIGURATION_023 = "Activer le lien du forum";
$LANCONFIGURATION_024 = "Ceci ajoutera le lien de votre forum sur l\'index.";
$LANCONFIGURATION_025 = "Oui";
$LANCONFIGURATION_026 = "Non";
$LANCONFIGURATION_027 = "Adresse du Forum";
$LANCONFIGURATION_028 = "Indiquez le lien de votre forum (Uniquement si vous avez activé l\'option Activer le lien du forum)";
$LANCONFIGURATION_029 = "Nbr de ban affiché / pages";
$LANCONFIGURATION_030 = "Ceci affichera le nombre de bannis par page.";
$LANCONFIGURATION_031 = "Nbr de page / liens";
$LANCONFIGURATION_032 = "Le nombre de liens à afficher avant et après la page sélectionnée (IE: fixé à 2 vous verrez 1 2 ... 10 11 [12] 13 14 ... 23 24).";
$LANCONFIGURATION_033 = "Dossier des démos";
$LANCONFIGURATION_034 = "Le répertoire courant par rapport à cette page web (Exemple : /var/www/banned alors le dossier demos sera /var/www/banned/demos).";
$LANCONFIGURATION_081 = "Taille max. des Démos (MB)";
$LANCONFIGURATION_035 = "Dossier des démos";
$LANCONFIGURATION_036 = "Taille maximale des démos pouvant être uploadées. Vous pouvez modifiez cette option dans le fichier php.ini si vous avez un serveur dédié.";
$LANCONFIGURATION_037 = "Code pour la création d'un utilisateur";
$LANCONFIGURATION_038 = "Il s'agit du code que vous pouvez donner aux membres et/ou admins pour accéer à ce site.";
$LANCONFIGURATION_039 = "Envoyer un e-mail";
$LANCONFIGURATION_040 = "Si Oui, tous les e-mails énumérés ci-dessous recevront un e-mail quand un ban est ajouté.";
$LANCONFIGURATION_041 = "Envoyer un mail (Si démos ajoutées) :";
$LANCONFIGURATION_042 = "Si Oui, tous les e-mails énumérés ci-dessous recevront un e-mail quand une démo est ajoutée.";
$LANCONFIGURATION_043 = "Adresse e-mail de l\'expéditeur";
$LANCONFIGURATION_044 = "Les emails cités ci-dessous recevront un email quand un ban ou une démo est ajoutée.";
$LANCONFIGURATION_045 = "Adresse e-mail destinataire";
$LANCONFIGURATION_046 = "L'adresse e-mail des personnes qui recevront une notification sur l\'ajout de démo ou d\'un ban.";
$LANCONFIGURATION_047 = "Ajouter >>";
$LANCONFIGURATION_048 = "<< Supprimer";
$LANCONFIGURATION_049 = "Paramétrage Banne";
$LANCONFIGURATION_050 = "Message";
$LANCONFIGURATION_051 = "Ceci est le message affiché aux joueurs qui essayeront de se connecter au(x) serveur(s).";
$LANCONFIGURATION_052 = "Permet aux admins de bannir les admins.";
$LANCONFIGURATION_053 = "Si cela est activé, vous pourrez bannir des admins.";
$LANCONFIGURATION_054 = "Nombre de jours où le banne est placé en attente";
$LANCONFIGURATION_055 = "Ceci est le nombre de jours où le banne est placé en attente. Cela s\'applique uniquement à des bannes de plus de 1 heure et qui ont été délivré par un membre. Le banne sera inactif après le nombre de jours écoulés si le banne n\'est pas retiré du status pending (attente). Set to 0 to let anyone banned by a member for more than an hour to be able to rejoin instantly.";
$LANCONFIGURATION_056 = "Remove pending on demo upload";
$LANCONFIGURATION_057 = "Suppression de l\'attente d\'un ban si un membre télécharge une démo pour les bannir en attendant.";
$LANCONFIGURATION_058 = "Code de sécurité";
$LANCONFIGURATION_059 = "Il s\'agit d\'un code secret utilisé qui dialogue avec le serveur Web et le serveur de jeu. Ceci évitera les abus.";
$LANCONFIGURATION_060 = "Renseigner les Admins";
$LANCONFIGURATION_061 = "Si vous mettez Oui, un message indiquera de Taper <b>!banmenu</b> après la mort d\'un joueur et/ou d\'un admin. Cela permet de rappeler comment bannir un joueur.";
$LANCONFIGURATION_062 = "Paramètrage SMF";
$LANCONFIGURATION_063 = "Activer SMF";
$LANCONFIGURATION_064 = "Activer ceci pour intégrer SMF. Les tables SMF tables devront avoir comme préfixe smf_. GlobalBan devra être installé dans le dossier forum. Exemple : (yoursite.com/Forums/banned).";
$LANCONFIGURATION_065 = "Préfixe des tables SMF";
$LANCONFIGURATION_066 = "Préfixe des tables SMF (smf_ par défaut).";
$LANCONFIGURATION_067 = "SMF Super-User Group";
$LANCONFIGURATION_068 = "Entrer l\'ID du groupe qui aura les pleins pouvoirs pour accéder au site.";
$LANCONFIGURATION_069 = "SMF Ban Manger Group";
$LANCONFIGURATION_070 = "Entrer l\'ID du groupe qui aura accès à tout les bannis.";
$LANCONFIGURATION_071 = "SMF Admin Group";
$LANCONFIGURATION_072 = "Entrer l\'ID du groupe qui sera en mesure de bannir n'importe qui sans aucune restriction.";
$LANCONFIGURATION_073 = "SMF Member Group";
$LANCONFIGURATION_074 = "Entrer l\'ID du groupe qui sera en mesure de bannir mais tout les bans durant plus d'une heure seront mis en attente. Si le ban n'est pas supprimé dans la période spécifié alors celui-ci deviendra inactif.";
$LANCONFIGURATION_075 = "SMF No Power Group";
$LANCONFIGURATION_076 = "Entrez l\'ID du groupe qui n\'aura pas de pouvoirs.";
$LANCONFIGURATION_077 = "Sauvegarder la Configuration";
$LANCONFIGURATION_078 = "Note: La sauvegarde de la configuration sera mis à jour dans le fichier GlobalBan.cfg pour tous les serveurs actifs.";
$LANCONFIGURATION_079 = "Accès refusé !";
$LANCONFIGURATION_080 = "Renseigner les Admins";

?>