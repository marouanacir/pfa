<?php
// Récupérer les données envoyées par la requête XMLHttpRequest
$clientId = $_GET['clientId'];
$majNumber = $_GET['majNumber'];
$app = $_GET['app'];
$errorCount = $_GET['errorCount'];


// Chemin du répertoire de mise à jour du client
$clientUpdateDirectory = 'Database/màj_clients/C' . $clientId;

// Vérifier si le répertoire de mise à jour existe, sinon le créer
if (!is_dir($clientUpdateDirectory)) {
  mkdir($clientUpdateDirectory, 0777, true);
}

// Chemin du fichier XML de mise à jour pour le client actuel
$clientUpdateFilePath = $clientUpdateDirectory . '/mise_à_jour_client.xml';

// Vérifier si le fichier XML de mise à jour existe, sinon le créer avec la structure de base
if (!file_exists($clientUpdateFilePath)) {
  $xmlContent = '<?xml version="1.0" encoding="iso-8859-1"?>
<majs></majs>';
  file_put_contents($clientUpdateFilePath, $xmlContent);
}

// Charger le contenu XML du fichier de mise à jour
$clientUpdateXml = simplexml_load_file($clientUpdateFilePath);


// Enregistrer les modifications dans le fichier XML du client uniquement si aucune erreur ne s'est produite
if ($errorCount == 0) {
  // Créer un nouvel élément <maj> avec ses attributs
$newMaj = $clientUpdateXml->addChild('maj');
$newMaj->addAttribute('id', $majNumber);
$newMaj->addAttribute('date_maj', date("Y-m-d"));
/////////////////ok
  $clientUpdateXml->asXML($clientUpdateFilePath);
}

  // Exemple de réponse renvoyée au client
  $response = [
    'success' => true,
    'message' => 'Mise à jour effectuée avec succès'
  ];

  //fonction pour trouvr l'id de mise à jour
  function findIdByMajNumber($majNumber,$app) {
    $majXmlFilePath = 'Database/màj.xml';
  
    // Charger le contenu XML du fichier de mises à jour
    $majXml = simplexml_load_file($majXmlFilePath);
  
    // Rechercher l'élément <maj> qui a le numéro de mise à jour correspondant
    foreach ($majXml->maj as $maj) {
      $numeroMaj = (string)$maj->numero_maj;
      $application = (string)$maj->app;
      if ($numeroMaj == $majNumber && $application == $app) {
        return (string)$maj['id'];
      }
    }
  
    // Si aucune correspondance n'est trouvée, retourner null ou une valeur indiquant l'absence d'id correspondant
    return null;
  }
  $majId = findIdByMajNumber($majNumber,$app);
 // exit($majId.'dd');
 $response["ID_MSG"]=$majId;
 $response["ID_CLIEnt"]=$clientId;
 $response["NUM_MAJ"]=$majNumber;
 $response["APP"]=$app;
 require_once('func_folder.php');
  //lister les fichiers à copier
  $updateXml = simplexml_load_file("Database/$majId/fichiers.xml");
  $response["num_fichier"]=array();
  $response["src_fichier"]=array();
  $response["dest_fichier"]=array();
  foreach ($updateXml->fichier as $index => $file) {
  $response["num_fichier"][] = (string) $file['numero'];
  $response["src_fichier"][] = (string) $file['url_serv'];
  $response["dest_fichier"][] = (string) $file['url_client'];
    //copyFolder($file['url_serv'], $file['url_client']);
}
  //lister les requetes à executer
  $updateXml = simplexml_load_file("Database/$majId/requetes.xml");
  $response["num_requete"]=array();
  $response["type_requete"]=array();
  $response["nom_table"]=array();
  $response["condition"]=array();
  $response["contenu"]=array();

  foreach ($updateXml->requete as $index => $req) {
  $response["num_requete"][] = (string) $req['numero'];
  $response["type_requete"][] = (string) $req['type'];
  $response["nom_table"][] = (string) $req['table'];
  $response["condition"][] = (string) $req['condition'];
  $contenu = (string) $req->contenu; // Récupérer le contenu de la balise <contenu>
  $response["contenu"][] = $contenu; // Ajouter le contenu au tableau
  }

  // Charger le contenu XML du fichier des clients
$clientsXml = simplexml_load_file('Database/clients.xml');

// Parcourir les éléments <client> pour trouver le client ayant le même ID que $clientId
foreach ($clientsXml->client as $client) {
  if ((string)$client['id'] == $clientId) {
      // Récupérer les données du client
      $serveurFTP = (string)$client->serveur_FTP;
      $userFTP = (string)$client->Utilisateur_FTP;
      $passwordFTP = (string)$client->Mot_de_passe_FTP;

      // Ajouter les données du client à la réponse
      $response['serveur_FTP'] = $serveurFTP;
      $response['Utilisateur_FTP'] = $userFTP;
      $response['Mot_de_passe_FTP'] = $passwordFTP;

      break; // Sortir de la boucle une fois le client trouvé
  }
}



// Renvoyer la réponse au client au format JSON
header('Content-Type: application/json');
echo json_encode($response);


?>
