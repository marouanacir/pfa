<?php
function copyFolder($source, $destination) {
    // Vérifie si le dossier source existe
    if (!is_dir($source)) {
        echo "Le dossier source '$source' n'existe pas.";
        return false;
    }

    // Vérifie si le dossier destination existe, sinon le crée
    if (!is_dir($destination)) {
        mkdir($destination);
    }

    // Récupère la liste des fichiers et dossiers dans le dossier source
    $files = scandir($source);

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $sourceFile = $source . '/' . $file;
            $destinationFile = $destination . '/' . $file;

            // Copie les fichiers
            if (is_file($sourceFile)) {
                copy($sourceFile, $destinationFile);
            }

            // Copie les dossiers récursivement
            if (is_dir($sourceFile)) {
                copyFolder($sourceFile, $destinationFile);
            }
        }
    }

    echo "Le dossier '$source' a été copié vers '$destination' avec succès.";
    return true;
}
?>