<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Charger le fichier XML
    $xml = simplexml_load_file('Database/màj.xml');

    // Trouver la dernière mise à jour pour obtenir le dernier ID
    $derniereMiseAJour = $xml->maj[count($xml->maj) - 1];

    // Obtenir le dernier ID
    $dernierID = (int) $derniereMiseAJour['id'];

    // Récupérer l'application sélectionnée dans le formulaire
    $application = $_POST['app'];

    // Compter le nombre de mises à jour pour l'application sélectionnée
    $nombreMisesAJour = 0;
    foreach ($xml->maj as $miseAJour) {
        if ($miseAJour->app == $application) {
            $nombreMisesAJour++;
        }
    }

    // Créer un nouvel élément maj avec un nouvel ID
    $nouvelleMiseAJour = $xml->addChild('maj');
    $nouvelleMiseAJour['id'] = $dernierID + 1;

    // Ajouter les éléments numero_maj, Date_creation et app avec les valeurs saisies
    $nouvelleMiseAJour->numero_maj = $nombreMisesAJour + 1;
    $nouvelleMiseAJour->Date_creation = $_POST['date_publication'];
    $nouvelleMiseAJour->app = $application;

    // Enregistrer les modifications dans le fichier XML
    $xml->asXML('Database/màj.xml');

    echo "Mise à jour ajoutée avec succès !";

    // Créer un dossier avec le nom de l'ID de la mise à jour
    $dossier = 'Database/' . ($dernierID + 1);
    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
        echo "Dossier créé avec succès !";
    }

    // Chemin du fichier fichiers.xml dans le dossier de la mise à jour
    $fichiersXmlPath = $dossier . '/fichiers.xml';

    if (file_exists($fichiersXmlPath)) {
        // Le fichier fichiers.xml existe déjà, charger son contenu
        $fichiersXml = simplexml_load_file($fichiersXmlPath);
    } else {
        // Le fichier fichiers.xml n'existe pas, créer un nouvel élément racine
        $fichiersXml = new SimpleXMLElement('<?xml version="1.0" encoding="iso-8859-1"?><fichiers></fichiers>');
    }

    // Récupérer les champs des fichiers à déplacer
    $types = $_POST['typerep'];
    $source = $_POST['dossier'];
    $serveurs = $_POST['serveur'];
    $clients = $_POST['client'];

    // Parcourir les champs et ajouter les éléments au fichier fichiers.xml
    for ($i = 0; $i < count($types); $i++) {
        $fichier = $fichiersXml->addChild('fichier');
        $fichier['numero'] = $i + 1;
        $fichier['dossier'] = $source;
        $fichier['type'] = $types[$i];
        $fichier['url_serv'] = $serveurs[$i];
        $fichier['url_client'] = $clients[$i];
    }

    // Enregistrer les modifications dans le fichier fichiers.xml
    $fichiersXml->asXML($fichiersXmlPath);





// Chemin du fichier requetes.xml dans le dossier de la mise à jour
$requetesXmlPath = $dossier . '/requetes.xml';


if (file_exists($requetesXmlPath)) {
    // Le fichier fichiers.xml existe déjà, charger son contenu
    $requetesXml = simplexml_load_file($requetesXmlPath);
} else {
    // Le fichier fichiers.xml n'existe pas, créer un nouvel élément racine
    $requetesXml = new SimpleXMLElement('<?xml version="1.0" encoding="iso-8859-1"?><requetes></requetes>');
    
}

// Récupérer les champs des requêtes à exécuter
$typesRequete = $_POST['typeRequete'];
$tables = $_POST['nomTable'];
$conditions = $_POST['conditionRequete'];
$contenus = $_POST['contenuRequete'];

// Parcourir les champs et ajouter les éléments au fichier requetes.xml
for ($i = 0; $i < count($typesRequete); $i++) {
    $requeteXml = $requetesXml->addChild('requete');
    $requeteXml->addAttribute('numero', $i + 1);
    $requeteXml->addAttribute('type', $typesRequete[$i]);
    $requeteXml->addAttribute('table', $tables[$i]);
    $requeteXml->addAttribute('condition', $conditions[$i]);
    $requeteXml->addChild('contenu', $contenus[$i]);
}

// Enregistrer le contenu XML dans le fichier requetes.xml
$requetesXml->asXML($requetesXmlPath);

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="clients.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"  />

    <title>Document</title>
</head>
<body>
        <?php   
           include 'dashboard.php';
        ?>
    <main>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Tableau stylé</title>
           
        </head>
        <body>
            <h1>Liste des mises à jour</h1>
           <a href="#" id="add-maj-button"> <button class="ajout"> Ajouter une mise à jour </button> </a>
           <div id="modal" class="modal">
            <div class="modal-content">
                <span id="close-btn" class="close">&times;</span>
                <h2>Ajouter une mise à jour</h2>
                <form action="maj.php" method="post">
                <label for="app">Application:</label>
                        <select name="app" class="app">
                            <option value="default">---</option>
                            <option value="OpticManager">OpticManager</option>
                            <option value="GestionEcole">GestionEcole</option>
                            <option value="E-Vente">E-Vente</option>
                            <option value="InfoParc">InfoParc</option>
                        </select>
                    <label for="date_publication">Date de publication:</label>
                    <input type="date" name="date_publication" id="date_publication">
                    <script>
                        // Obtenir la date d'aujourd'hui
                        var today = new Date();

                        // Obtenir la chaîne de date au format ISO (YYYY-MM-DD)
                        var dateString = today.toISOString().split('T')[0];

                        // Définir la valeur de l'attribut "value" de l'élément <input> avec la date d'aujourd'hui
                        document.getElementById("date_publication").value = dateString;
                    </script>


                    <fieldset>
                    <legend> Fichiers à deplacer</legend>
                    <div id="repContainer">
                    <div>
                        <label for="dossier">Dossier source:</label>
                        <input type="text" name="dossier" class="repInput">
                        </div>
                        <div>
                        <label for="typerepe[]">Type:</label>
                        <select name="typerep[]" class="repInput">
                            <option value="default">---</option>
                            <option value="f">Fichier</option>
                            <option value="d">Dossier</option>

                        </select>
                        </div>
                        <div>
                        <label for="serveur[]">Emplacement serveur:</label>
                        <input type="text" name="serveur[]" class="repInput">
                        </div>
                        <div>
                        <label for="client[]">Emplacement client:</label>
                        <input type="text" name="client[]" class="repInput">
                        </div>
                    </div>
                    </fieldset>

                    <button type="button" id="ajouterrep" >+</button>

                    <script>
                    var ajouterrepBtn = document.getElementById("ajouterrep");
                    var repContainer = document.getElementById("repContainer");

                    ajouterrepBtn.addEventListener("click", function() {
                        var newrepDiv = document.createElement("div");
                        newrepDiv.innerHTML = `
                        <div>
                        <label for="typerepe[]">Type:</label>
                        <select name="typerep[]" class="repInput">
                            <option value="default">---</option>
                            <option value="f">Fichier</option>
                            <option value="d">Dossier</option>

                        </select>
                        </div>
                        <div>
                        <label for="serveur[]">Emplacement serveur:</label>
                        <input type="text" name="serveur[]" class="repInput">
                        </div>
                        <div>
                        <label for="client[]">Emplacement client:</label>
                        <input type="text" name="client[]" class="repInput">
                        </div>                       
                        `;
                        repContainer.appendChild(newrepDiv);
                    });
                    </script>



                    
                <fieldset>
                    <legend>Requêtes à exécuter</legend>
                    <div id="requeteContainer">
                        <div>
                        <label for="typeRequete[]">Type:</label>
                        <select name="typeRequete[]" class="requeteInput">
                            <option value="default">---</option>
                            <option value="create">Create</option>
                            <option value="delete">Delete</option>
                            <option value="insert">Insert</option>
                            <option value="update">Update</option>
                        </select>
                        </div>
                        <div>
                        <label for="nomTable[]">Table:</label>
                        <input type="text" name="nomTable[]" class="requeteInput">
                        </div>
                        <div>
                        <label for="conditionRequete[]">Condition:</label>
                        <input type="text" name="conditionRequete[]" class="requeteInput">
                        </div>
                        <div>
                        <label for="contenuRequete[]">Contenu:</label>
                        <input type="text" name="contenuRequete[]" class="requeteInput">
                        </div>
                    </div>
                    </fieldset>

                    <button type="button" id="ajouterRequete">+</button>

                    <script>
                    var ajouterRequeteBtn = document.getElementById("ajouterRequete");
                    var requeteContainer = document.getElementById("requeteContainer");

                    ajouterRequeteBtn.addEventListener("click", function() {
                        var newRequeteDiv = document.createElement("div");
                        newRequeteDiv.innerHTML = `
                        <div>
                            <label for="typeRequete[]">Type:</label>
                            <select name="typeRequete[]" class="requeteInput">
                            <option value="default">---</option>
                            <option value="create">Create</option>
                            <option value="delete">Delete</option>
                            <option value="insert">Insert</option>
                            <option value="update">Update</option>
                            </select>
                        </div>
                        <div>
                            <label for="nomTable[]">Table:</label>
                            <input type="text" name="nomTable[]" class="requeteInput">
                        </div>
                        <div>
                            <label for="conditionRequete[]">Condition:</label>
                            <input type="text" name="conditionRequete[]" class="requeteInput">
                        </div>
                        <div>
                            <label for="contenuRequete[]">Contenu:</label>
                            <input type="text" name="contenuRequete[]" class="requeteInput">
                        </div>
                        `;
                        requeteContainer.appendChild(newRequeteDiv);
                    });
                    </script>

                    <button type="submit">Ajouter</button>
                </form>
            </div>
        </div>

            <table>
                <thead>
                    <tr>
                        <th>  Ordre </th>
                        <th>  Numéro de mise à jour</th>
                        <th> Date de publication </th>
                        <th> Application </th>
                        <th> Action </th>

                    </tr>
                </thead>
                <tbody>
                <?php
                // Load the XML file
                $xml = simplexml_load_file('Database/màj.xml');

                // Loop through each maj element and output the data in the HTML table
                foreach ($xml->maj as $maj) {
                    echo '<tr>';
                    echo '<td>' . $maj['id'] . '</td>';
                    echo '<td>' . $maj->numero_maj . '</td>';
                    echo '<td>' . $maj->Date_creation . '</td>';
                    echo '<td>' . $maj->app . '</td>';
                    echo '<td><a href="delete-màj.php?id=' . $maj['id'] . '" onclick="return confirm(&quot;Êtes-vous sûr de vouloir supprimer cette mise à jour ?&quot;)"><i class="fas fa-trash-alt"></i></a></td>';

                    echo '</tr>';
                }
                ?>

                </tbody>
            </table>
        </body>
        </html>
        
</main>
<script>
    // Gestionnaire d'événements pour afficher le formulaire d'ajout de client
    var addClientButton = document.getElementById("add-maj-button");
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