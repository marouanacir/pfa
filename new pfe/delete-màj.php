<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];

    // Charger le fichier XML
    $xml = simplexml_load_file('Database/màj.xml');

    // Rechercher la mise à jour avec l'ID spécifié
    $majNode = $xml->xpath("//maj[@id='$id']")[0];

    // Supprimer le nœud de la mise à jour s'il a été trouvé
    if ($majNode != null) {
        // Obtenir l'ID de la mise à jour
        $miseAJourID = (string)$majNode['id'];

        // Supprimer le nœud de la mise à jour
        unset($majNode[0]);

        // Enregistrer les modifications dans le fichier XML
        $xml->asXML('Database/màj.xml');

        // Supprimer le dossier correspondant à la mise à jour
        $dossierChemin = 'Database/' . $miseAJourID;
        deleteDirectory($dossierChemin);

        // Rediriger vers la page maj.php après la suppression
        header("Location: maj.php");
        exit();
    }
}

// Fonction pour supprimer récursivement un dossier et son contenu
function deleteDirectory($directoryPath) {
    if (is_dir($directoryPath)) {
        $files = glob($directoryPath . "/*");
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directoryPath);
    }
}
?>
