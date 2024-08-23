<?php
// Connexion à la base de données
$servername = "localhost";
$username = "marorygq_testmaroua";
$password = "UEYZEZEs723766";
$dbname = "marorygq_testmaroua";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Vérifier l'existence de la table "etudiant"
$tableExists = false;
$result = $conn->query("SHOW TABLES LIKE 'Prof'");
if ($result->num_rows > 0) {
    $tableExists = true;
}

if ($tableExists) {
    // Requête pour récupérer la liste des étudiants
    $sql = "SELECT * FROM Prof";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Affichage du tableau des étudiants
        echo "<table>";
        echo "<tr><th>Nom</th><th>Prénom</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["nom"] . "</td><td>" . $row["prenom"] . "</td><td>" ;
        }

        echo "</table>";
    } else {
        echo "Aucun Prof trouvé.";
    }
} else {
    echo "La table 'Prof' n'existe pas.";
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
