<?php
// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les valeurs du formulaire
    $nom = $_POST["nom"];
    $app = $_POST["app"];
    $url = $_POST["url"];
    $date_debut = $_POST["date_debut"];
    $dernier_paiement = $_POST["Dernier_paiement"];
    $prochain_paiement = $_POST["Prochain_paiement"];
    $ftpServer = $_POST["serveur_FTP"];
    $ftpUsername = $_POST["Utilisateur_FTP"];
    $ftpPassword = $_POST["Mot_de_passe_FTP"];


    // Charge le fichier XML
    $xml = simplexml_load_file('Database/clients.xml');

    // Récupère le nombre total de clients
    $totalClients = count($xml->client);

    // Incrémente le nombre total de clients pour générer le nouvel ID
    $newId = $totalClients + 1;

    // Crée un nouvel élément client avec le nouvel ID
    $client = $xml->addChild('client');
    $client->addAttribute('id', $newId);

    // Ajoute les valeurs du client
    $client->addChild('nom', $nom);
    $client->addChild('app', $app);
    $client->addChild('url', $url);
    $client->addChild('date_debut', $date_debut);
    $client->addChild('date_dernier_paiement', $dernier_paiement);
    $client->addChild('date_prochain_paiement', $prochain_paiement);
    $client->addChild('serveur_FTP', $ftpServer);
    $client->addChild('Utilisateur_FTP', $ftpUsername);
    $client->addChild('Mot_de_passe_FTP', $ftpPassword);

    

    // Enregistre les modifications dans le fichier XML
    $xml->asXML('Database/clients.xml');

    // Crée un dossier pour le nouveau client
    $clientId = 'C' . $newId;
    $directoryPath = 'Database/màj_clients/' . $clientId;

    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0777, true);
    }

    // ...

// Créer le chemin du fichier XML de mise à jour
$updateFilePath = $directoryPath . '/mise_à_jour_client.xml';

// Créer le contenu XML de la mise à jour
$updateXmlContent = '<?xml version="1.0" encoding="iso-8859-1"?><majs></majs>';

// Écrire le contenu XML dans le fichier mise_à_jour_client.xml
file_put_contents($updateFilePath, $updateXmlContent);

}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="clients.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"  />
    <!-- Inclure SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <!-- Inclure SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>

    <title>Document</title>
</head>
<body>
        <?php   
           include 'dashboard.php';
        ?>
    <main>
        
        <h1>Liste des clients</h1>
        <a href="#" id="add-client-button"> <button class="ajout"> Ajouter un client </button> </a>
 
        <div id="modal" class="modal">
            <div class="modal-content">
                <span id="close-btn" class="close">&times;</span>
                <h2>Ajouter un client</h2>
                <form action="clients.php" method="post">
                    <label for="nom">Nom:</label>
                    <input type="text" name="nom" id="nom">
                    <label for="app">Application:</label>
                        <select name="app" class="app">
                            <option value="default">---</option>
                            <option value="OpticManager">OpticManager</option>
                            <option value="GestionEcole">GestionEcole</option>
                            <option value="E-Vente">E-Vente</option>
                            <option value="InfoParc">InfoParc</option>
                        </select>
                    <label for="url">URL:</label>
                    <input type="text" name="url" id="url">
                    <label for="date_debut">Date de début:</label>
                    <input type="date" name="date_debut" id="date_debut">
                    <label for="Dernier_paiement">Dernier paiement:</label>
                    <input type="date" name="Dernier_paiement" id="Dernier_paiement">
                    <label for="Prochain_paiement">Prochain paiement:</label>
                    <input type="date" name="Prochain_paiement" id="Prochain_paiement">
                    <label for="serveur_FTP">serveur FTP:</label>
                    <input type="text" name="serveur_FTP" id="serveur_FTP">
                    <label for="Utilisateur_FTP">Utilisateur FTP:</label>
                    <input type="text" name="Utilisateur_FTP" id="Utilisateur_FTP">
                    <label for="Mot_de_passe_FTP">Mot de passe FTP:</label>
                    <input type="text" name="Mot_de_passe_FTP" id="Mot_de_passe_FTP">
                    <button type="submit">Ajouter</button>
                </form>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th> ID </th>
                    <th> Nom </th>
                    <th> Application </th>
                    <th> URL </th>
                    <th> Date de debut </th>
                    <th> Dernier paiement </th>
                    <th> Prochain paiement</th>
                    <th> Derniere MàJ </th>
                    <th> Màj Disponible </th>
                    <th> Emplacement </th>
                    <th style="display: none;"> FTPuser </th>
                    <th style="display: none;"> FTPpwd </th>
                    <th> Action </th>
                </tr>
            </thead>
            <tbody>
<?php
// Load the XML file
$xml = simplexml_load_file('Database/clients.xml');

function getLatestUpdateIdmaj($clientId) {
    $updateFilePath = 'Database/màj_clients/' . $clientId . '/mise_à_jour_client.xml';
    
    if (file_exists($updateFilePath)) {
        $updateXml = simplexml_load_file($updateFilePath);
        $latestUpdate = $updateXml->xpath("//maj[last()]");
        
        if (!empty($latestUpdate)) {
            return (int)$latestUpdate[0]['id'];
        }
    }

    return 0;
}


// Définir la fonction getLatestUpdateId ici
function getLatestUpdateId($app) {
    // Charger le fichier XML des mises à jour
    $updateXml = simplexml_load_file('Database/màj.xml');

    // Rechercher les mises à jour pour l'application donnée
    $updateNodes = $updateXml->xpath("/majs/maj[app='" . $app . "']");

    $maxUpdateId = 0;
    foreach ($updateNodes as $updateNode) {
        $currentUpdateId = (int)$updateNode->numero_maj;
        if ($currentUpdateId > $maxUpdateId) {
            $maxUpdateId = $currentUpdateId;
        }
    }

    return $maxUpdateId;
}



// Appeler la fonction getLatestUpdateId pour récupérer le numéro de mise à jour le plus élevé
$app = '';
$maxUpdateId = 0;

// Loop through each client row and output the data in the HTML table
foreach ($xml->client as $index => $client) {
    echo '<tr id="row-' . $index . '" data-id="' . $client['id'] . '" data-app="' . $client->app . '">';
    echo '<td>' . $client['id'] . '</td>';
    echo '<td>' . $client->nom . '</td>';
    echo '<td>' . $client->app . '</td>';
    echo '<td>' . $client->url . '</td>';
    echo '<td>' . $client->date_debut . '</td>';
    echo '<td>' . $client->date_dernier_paiement . '</td>';
    echo '<td>' . $client->date_prochain_paiement . '</td>';
    // Appeler la fonction getLatestUpdateIdmaj pour chaque client
    $clientId = 'C' . $client['id'];
    $latestUpdateId = getLatestUpdateIdmaj($clientId);

    echo '<td>' . $latestUpdateId . '</td>';


    // Appeler la fonction getLatestUpdateId pour chaque client
    $app = (string)$client->app;
    $maxUpdateId = getLatestUpdateId($app);

    echo '<td>' . $maxUpdateId . '</td>';
    echo '<td>' . $client->serveur_FTP . '</td>';
    echo '<td style="display: none;">' . $client->Utilisateur_FTP . '</td>';
    echo '<td style="display: none;">' . $client->Mot_de_passe_FTP . '</td>';



    // Ajoutez d'autres colonnes ici si nécessaire
    echo '<td>
    <a href="#" class="edit-client" data-id="' . $client['id'] . '"><i class="fas fa-edit"></i></a>
    <a href="delete-client.php?id=' . $client['id'] . '" onclick="return confirm(&quot;Êtes-vous sûr de vouloir supprimer ce client ?&quot;)"><i class="fas fa-trash-alt"></i></a>
    <a href="#" class="maj" data-id="' . $client['id'] . '" data-maj="' . $latestUpdateId . '" data-app="' . $client->app . '"><i class="fas fa-shopping-bag"></i></a>

    </td>';


    

}



?>

<!-- Placez le code JavaScript ci-dessous à l'extérieur de la boucle PHP -->
<script>
    var errorCount = 0; // Variable pour compter les erreurs

document.addEventListener("DOMContentLoaded", function() {
  var confirmed;
  var majButtons = document.getElementsByClassName("maj");

  for (var i = 0; i < majButtons.length; i++) {
    majButtons[i].addEventListener("click", function(event) {
      event.preventDefault();
      var row = event.target.closest("tr");
      var clientId = row.getAttribute("data-id");
      var appli = row.getAttribute("data-app");
      var storedMajNumber = localStorage.getItem("majNumber_" + clientId + appli);
      var majNumber;

      if (storedMajNumber !== null) {
        majNumber = parseInt(storedMajNumber, 10);
      } else {
        majNumber = 0;
      }

      var derniere_maj = parseInt(row.cells[7].innerText, 10); // Récupérer la valeur de la cellule contenant maxUpdateId
      var incrementedMajNumber = derniere_maj + 1;

      // Vérifier si incrementedMajNumber dépasse maxUpdateId
      var maxUpdateId = parseInt(row.cells[8].innerText, 10); // Récupérer la valeur de la cellule contenant maxUpdateId
      if (incrementedMajNumber > maxUpdateId) {
        // Afficher un message d'erreur ou prendre une autre action appropriée
        var errorMessage = "Le client est à jour";
        alert(errorMessage);
        return;
      }

      var confirmMessage = "Voulez-vous effectuer la mise à jour numéro " + incrementedMajNumber + " pour le client ID " + clientId + "?" + appli;
      confirmed = confirm(confirmMessage);

      if (confirmed) {
        var progressModal = document.getElementById("progress-modal");
        progressModal.style.display = "block";

        // Gestionnaire d'événements pour fermer le formulaire d'ajout de client
        var closeButton = document.getElementById("progress-close-btn");
        closeButton.addEventListener("click", function(event) {
          event.preventDefault();
          var modal = document.getElementById("progress-modal");
          modal.style.display = "none";
        });

        var progressBar = document.getElementById("progress-bar");
        var progressStatus = document.getElementById("progress-status");

        function getXMLHttpRequest() {
          var xhr2 = null;
          if (window.XMLHttpRequest || window.ActiveXObject) {
            if (window.ActiveXObject) {
              try {
                xhr2 = new ActiveXObject("Msxml2.XMLHTTP");
              } catch (e) {
                xhr2 = new ActiveXObject("Microsoft.XMLHTTP");
              }
            } else {
              xhr2 = new XMLHttpRequest();
            }
          } else {
            alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
            return null;
          }
          return xhr2;
        }

        var xhr = getXMLHttpRequest();
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            // Récupérer les informations du tableau renvoyé
            var response;
            try {
              response = JSON.parse(xhr.responseText);
            } catch (error) {
              console.error('Erreur lors du parsing de la réponse JSON :', error);
              return;
            }

            // Vérifier si la réponse contient des erreurs
            if (response.error) {
              console.error('Erreur renvoyée par le serveur :', response.error);
              return;
            }
            //recup fichiers
            var numFichiers = response["num_fichier"];
            var srcFichiers = response["src_fichier"];
            var destFichiers = response["dest_fichier"];
            //recup requetes
            var numrequetes = response["num_requete"];
            var typerequetes = response["type_requete"];
            var nomtables = response["nom_table"];
            var conditions = response["condition"];
            var contenues = response["contenu"];
            //infos ftp
            var serveurFTP = response["serveur_FTP"];
            var userFTP = response["Utilisateur_FTP"];
            var passwordFTP = response["Mot_de_passe_FTP"];



            // Utiliser les informations récupérées
var numFilesCopied = 0;
var totalFiles = numFichiers.length;
var progressInterval = 100 / totalFiles; // Pourcentage de progression par fichier copié

var interval = setInterval(updateProgress, 100); // Appeler la fonction updateProgress toutes les 100 millisecondes

function updateProgress() {
  
  numFilesCopied++;
  var progress = Math.floor(numFilesCopied * (progressInterval / totalFiles));
  progressBar.style.width = progress + "%";
  progressStatus.innerText = progress + "%";

  if (progress === 100) {
  clearInterval(interval);
  progressModal.style.display = "none";
  if (errorCount > 0) {
    Swal.fire({
      icon: 'error',
      title: 'Une ou plusieurs erreurs se sont produites lors de la mise à jour',
      text: 'Veuillez vérifier les fichiers des logs pour plus d\'informations.',
      showConfirmButton: true,
    });
  } else {
    Swal.fire({
      icon: 'success',
      title: 'Mise à jour effectuée avec succès',
      showConfirmButton: false,
      timer: 2000, // La boîte de dialogue se ferme après 2 secondes
    }).then(function () {
      location.reload(); // Actualiser la page
    });
  }
}

}
  // Vérifier si une erreur s'est produite lors des requêtes AJAX
  if (xhr.readyState === 4) {
    if (xhr.status === 200) {
      // Mettre à jour la progression
      updateProgress();
    } else {
      // Une erreur s'est produite lors de l'exécution de la requête
      clearInterval(interval);
      progressModal.style.display = "none";

      Swal.fire({
        icon: 'error',
        title: 'Une erreur s\'est produite lors de la mise à jour',
        text: 'Veuillez vérifier le fichier des logs pour plus d\'informations.',
        showConfirmButton: true,
      });
      // Augmenter le compteur d'erreurs
      errorCount++;
    }
  

// Vérifier si une erreur s'est produite lors de la copie des fichiers
if (response.ok) {
    // La copie a été effectuée avec succès
    // Mettre à jour la progression
    updateProgress();
  } else {
    // La copie a échoué
    console.log('La copie a échoué : ' + (response.status ?? 'Erreur inconnue'));
    // Augmenter le compteur d'erreurs
    errorCount++;
  }
}

            var totalTasks = numFichiers.length + numrequetes.length;

//execution des requetes
for (var j = 0; j < numrequetes.length ; j++) {
              var numrequete = numrequetes[j];
              var typerequete = typerequetes[j];
              var nomtable = nomtables[j];
              var condition = conditions[j];
              var contenu = contenues[j];
                console.log(numrequete);
                console.log(typerequete);
                console.log(nomtable);
                console.log(condition);
                console.log(contenu);

                // Appeler la fonction de copie côté serveur via une requête AJAX
                fetch('https://www.demo37.maroceco.com/maroua/test_requetes/executer_requetes.php?type=' + encodeURIComponent(typerequete) + '&table=' + encodeURIComponent(nomtable) + '&condition=' + encodeURIComponent(condition) + '&contenu=' + encodeURIComponent(contenu) + '&errorCount=' + encodeURIComponent(errorCount))
    .then(function(response) {
        // Gérer la réponse de la requête AJAX
        if (response.ok) {
            // Mettre à jour la progression
            updateProgress();
            
            
        } else {
            // La copie a échoué
            console.log('Execution a échoué : ' + response.status);
            // Augmenter le compteur d'erreurs
            errorCount++;
            throw new Error('Requête AJAX a échoué');
            // Récupérer la réponse JSON
            return response.json();
        }
    })
    .then(function(jsonResponse) {
        // Vérifier si la réponse contient des erreurs
        if (jsonResponse && jsonResponse.errorCount) {
            var errorCount = jsonResponse.errorCount;
            // Faire ce que vous voulez avec la variable errorCount dans votre code JavaScript
            // ...
        }
    })
    .catch(function(error) {
        // Gérer les erreurs de la requête AJAX
        if (error && error.message) {
          console.log("Erreur lors de la requête AJAX : " + error.message);

        } else {
            console.log("Une erreur d'exécution d'une requête s'est produite.");
        }
    });

                }




//copie des fichiers
            for (var j = 0; j < numFichiers.length; j++) {
              var numFichier = numFichiers[j];
              var srcFichier = srcFichiers[j];
              var destFichier = destFichiers[j];

              // Vérifier si srcFichier est une chaîne de caractères
              if (typeof srcFichier === 'string') {
                console.log(srcFichier);
                console.log(destFichier);
                // Effectuer les opérations de copie appropriées ici
                // copyFolder(srcFichier, destFichier);

                // Appeler la fonction de copie côté serveur via une requête AJAX
                str1='copy_files.php?source=' + encodeURIComponent(srcFichier) + '&destination=' + encodeURIComponent(destFichier) + '&serveur_FTP=' + encodeURIComponent(serveurFTP) + '&Utilisateur_FTP=' + encodeURIComponent(userFTP) + '&Mot_de_passe_FTP=' + encodeURIComponent(passwordFTP);
                console.log(str1);
                fetch(str1)
                  .then(function(response) {
                    // Gérer la réponse de la requête AJAX
                    if (response.ok) {
                      // La copie a été effectuée avec succès
                      // Mettre à jour la progression
                      updateProgress();
                    } else {
                      // La copie a échoué
                      console.log('La copie a échoué : ' + response.status);
                      // Augmenter le compteur d'erreurs
              errorCount++;
                    }
                  })
                  .catch(function(error) {
                  // Gérer les erreurs de la requête AJAX
                  if (error && error.style) {
                    console.log("Erreur lors de la requête AJAX : " + error);

                  } else {
                    console.log("Une erreur s'est produite lors de la copie.");
                  }
                  });

              } else {
                console.log('srcFichier n\'est pas une chaîne de caractères');
              }
            }

            event.target.setAttribute("data-maj", incrementedMajNumber);
            localStorage.setItem("majNumber_" + clientId + appli, incrementedMajNumber);
          }
        };
        console.log(errorCount);

        xhr.open("GET", "update.php?clientId=" + clientId + "&majNumber=" + incrementedMajNumber + "&app=" + appli + "&errorCount=" + errorCount );
        xhr.send(null);
      }
    });
  }
});
</script>


<div id="progress-modal" class="modal">
  <div class="modal-content">
    <span id="progress-close-btn" class="close">&times;</span>
    <h2>Mise à jour en cours...</h2>
    <div id="progress-bar-container">
      <div id="progress-bar"></div>
    </div>
    <p id="progress-status">0%</p>
    
  </div>
</div>




  <div id="edit-modal" class="modal">
  <div class="modal-content">
    <span id="edit-close-btn" class="close">&times;</span>
    <h2>Modifier le client</h2>
    <form action="edit-client.php" method="post">
      <!-- Ajoutez les champs du formulaire avec les valeurs existantes du client -->
      <label for="edit-nom">Nom:</label>
      <input type="text" name="edit-nom" id="edit-nom">
      <label for="edit-app">Application:</label>
      <select name="edit-app" id="edit-app" class="app">
                            <option value="default">---</option>
                            <option value="OpticManager">OpticManager</option>
                            <option value="GestionEcole">GestionEcole</option>
                            <option value="E-Vente">E-Vente</option>
                            <option value="InfoParc">InfoParc</option>
                        </select>
      <label for="edit-url">URL:</label>
      <input type="text" name="edit-url" id="edit-url">
      <label for="edit-date_debut">Date de début:</label>
      <input type="date" name="edit-date_debut" id="edit-date_debut">
      <label for="edit-Dernier_paiement">Dernier paiement:</label>
      <input type="date" name="edit-Dernier_paiement" id="edit-Dernier_paiement">
      <label for="edit-Prochain_paiement">Prochain paiement:</label>
      <input type="date" name="edit-Prochain_paiement" id="edit-Prochain_paiement">
      <label for="edit-serveur_FTP">serveur FTP:</label>
      <input type="text" name="edit-serveur_FTP" id="edit-serveur_FTP">
      <label for="edit-Utilisateur_FTP">Utilisateur FTP:</label>
      <input type="text" name="edit-Utilisateur_FTP" id="edit-Utilisateur_FTP">
      <label for="edit-Mot_de_passe_FTP">Mot de passe FTP:</label>
      <input type="text" name="edit-Mot_de_passe_FTP" id="edit-Mot_de_passe_FTP">

      <button type="submit">Modifier</button>
    </form>
  </div>
</div>
<script>// Gestionnaire d'événements pour afficher le formulaire de modification de client
var editClientButtons = document.getElementsByClassName("edit-client");
for (var i = 0; i < editClientButtons.length; i++) {
  editClientButtons[i].addEventListener("click", function(event) {
    event.preventDefault();
    var editModal = document.getElementById("edit-modal");
    editModal.style.display = "block";

    // Récupérer les valeurs du client à partir des cellules de la ligne du tableau
    var row = event.target.closest("tr");
    var id = row.cells[0].innerText;
    var nom = row.cells[1].innerText;
    var app = row.cells[2].innerText;
    var url = row.cells[3].innerText;
    var date_debut = row.cells[4].innerText;
    var dernier_paiement = row.cells[5].innerText;
    var prochain_paiement = row.cells[6].innerText;
    var serveur_FTP = row.cells[9].innerText;
    var Utilisateur_FTP = row.cells[10].innerText;
    var Mot_de_passe_FTP = row.cells[11].innerText;



    // Pré-remplir les champs du formulaire de modification avec les valeurs existantes du client
    document.getElementById("edit-nom").value = nom;
    document.getElementById("edit-app").value = app;
    document.getElementById("edit-url").value = url;
    document.getElementById("edit-date_debut").value = date_debut;
    document.getElementById("edit-Dernier_paiement").value = dernier_paiement;
    document.getElementById("edit-Prochain_paiement").value = prochain_paiement;
    document.getElementById("edit-serveur_FTP").value = serveur_FTP;
    document.getElementById("edit-Utilisateur_FTP").value = Utilisateur_FTP;
    document.getElementById("edit-Mot_de_passe_FTP").value = Mot_de_passe_FTP;


    // Ajouter l'ID du client en tant que champ caché dans le formulaire
    var hiddenIdField = document.createElement("input");
    hiddenIdField.type = "hidden";
    hiddenIdField.name = "edit-id";
    hiddenIdField.value = id;
    var editForm = editModal.querySelector("form");
    editForm.appendChild(hiddenIdField);
  });
}

// Gestionnaire d'événements pour fermer le formulaire de modification de client
var editCloseButton = document.getElementById("edit-close-btn");
editCloseButton.addEventListener("click", function(event) {
  event.preventDefault();
  var editModal = document.getElementById("edit-modal");
  editModal.style.display = "none";
});
</script>
            </tbody>
        </table>
    </main>
<script>
    // Gestionnaire d'événements pour afficher le formulaire d'ajout de client
    var addClientButton = document.getElementById("add-client-button");
    addClientButton.addEventListener("click", function(event) {
        event.preventDefault();
        var modal = document.getElementById("modal");
        modal.style.display = "block";
    });

    // Gestionnaire d'événements pour fermer le formulaire d'ajout de client
    var closeButton = document.getElementById("close-btn");
    closeButton.addEventListener("click", function(event) {
        event.preventDefault();
        var modal = document.getElementById("modal");
        modal.style.display = "none";
    });
</script>

</body>
</html>
