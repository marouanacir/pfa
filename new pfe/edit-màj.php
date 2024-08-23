<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit-id"])) {
    $id = $_POST["edit-id"];
    $nom = $_POST["edit-nom"];
    $url = $_POST["edit-url"];
    $date_debut = $_POST["edit-date_debut"];
    $dernier_paiement = $_POST["edit-Dernier_paiement"];
    $prochain_paiement = $_POST["edit-Prochain_paiement"];

    // Charger le fichier XML
    $xml = simplexml_load_file('Database/clients.xml');

    // Rechercher le client avec l'ID spécifié
    $clientNode = $xml->xpath("//client[@id='$id']")[0];

    // Mettre à jour les valeurs du client
    $clientNode->nom = $nom;
    $clientNode->url = $url;
    $clientNode->date_debut = $date_debut;
    $clientNode->date_dernier_paiement = $dernier_paiement;
    $clientNode->date_prochain_paiement = $prochain_paiement;

    // Enregistrer les modifications dans le fichier XML
    $xml->asXML('Database/clients.xml');

    // Rediriger vers la page maj.php après la modification
    header("Location: clients.php");
    exit();
}
?>
