<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Récupération des paramètres de la requête
$typerequete = $_GET['type'];
$nomtable = $_GET['table'];
$condition = $_GET['condition'];
$contenu = $_GET['contenu'];
$errorCount = $_GET['errorCount'];


// Construction de la requête en fonction du type
$sql = "";
if ($typerequete === "create") {
    $sql = "CREATE TABLE $nomtable ($contenu)";
} elseif ($typerequete === "delete") {
    $sql = "DELETE FROM $nomtable WHERE $condition";
} elseif ($typerequete === "insert") {
    $sql = "INSERT INTO $nomtable $contenu";
} elseif ($typerequete === "update") {
    $sql = "UPDATE $nomtable SET $contenu WHERE $condition";
}

// Exécution de la requête dans la base de données
if ($sql !== "") {
    if ($conn->query($sql) === TRUE) {
        echo "Requête exécutée avec succès.";
    } else {
        echo "Erreur lors de l'exécution de la requête : " . $conn->error;
        // Augmenter le compteur d'erreurs
        $errorCount = 1;
    }
} else {
    echo "Type de requête non valide.";
}

// Fermeture de la connexion à la base de données
$conn->close();
$response = array(
    'errorCount' => $errorCount
);

// Convertir le tableau en JSON
$jsonResponse = json_encode($response);

// Envoyer la réponse JSON
echo $jsonResponse;
?>
