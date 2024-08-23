<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];

    // Charger le fichier XML
    $xml = simplexml_load_file('Database/clients.xml');

    // Rechercher le client avec l'ID spécifié
    $clientNode = $xml->xpath("//client[@id='$id']")[0];

    // Supprimer le nœud du client s'il a été trouvé
    if ($clientNode != null) {
        unset($clientNode[0]);

        // Enregistrer les modifications dans le fichier XML
        $xml->asXML('Database/clients.xml');

        // Supprimer le dossier du client
        $clientId = 'C' . $id;
        $directoryPath = 'Database/màj_clients/' . $clientId;

        if (is_dir($directoryPath)) {
            // Supprimer récursivement le dossier et son contenu
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }

            // Supprimer le dossier principal du client
            rmdir($directoryPath);
        }

        // Rediriger vers la page clients.php après la suppression
        header("Location: clients.php");
        exit();
    }
}
?>
