<?php




function copyFolder($source, $destination, $ftpServer, $ftpUsername, $ftpPassword) {
    // Vérifie si le dossier source existe localement
    if (!is_dir($source)) {
        echo "Le dossier source '$source' n'existe pas.";
        return false;
    }

    // Se connecte au serveur FTP
    $ftpConnection = ftp_connect($ftpServer);
    if (!$ftpConnection) {
        echo "Impossible de se connecter au serveur FTP.";
        return false;
    }

    // Authentification FTP
    $ftpLogin = ftp_login($ftpConnection, $ftpUsername, $ftpPassword);
    if (!$ftpLogin) {
        echo "Échec de l'authentification FTP.";
        ftp_close($ftpConnection);
        return false;
    }

    // Activer le mode passif
    ftp_pasv($ftpConnection, true);

    // Vérifie si le dossier destination existe sur le serveur FTP, sinon le crée
    if (!ftp_chdir($ftpConnection, $destination)) {
        if (!ftp_mkdir($ftpConnection, $destination)) {
            echo "Impossible de créer le dossier destination '$destination' sur le serveur FTP.";
            ftp_close($ftpConnection);
            return false;
        }
    }

    // Récupère la liste des fichiers et dossiers dans le dossier source
    $files = scandir($source);

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $sourceFile = $source . '/' . $file;
            $destinationFile = $destination . '/' . $file;

            // Copie les fichiers
            if (is_file($sourceFile)) {
                if (!ftp_put($ftpConnection, $destinationFile, $sourceFile, FTP_BINARY)) {
                    echo "Échec de la copie du fichier '$sourceFile' vers '$destinationFile' sur le serveur FTP.";
                }
            }

            // Copie les dossiers récursivement
            if (is_dir($sourceFile)) {
                if (!copyFolder($sourceFile, $destinationFile, $ftpServer, $ftpUsername, $ftpPassword)) {
                    echo "Échec de la copie du dossier '$sourceFile' vers '$destinationFile' sur le serveur FTP.";
                }
            }
        }
    }

    // Ferme la connexion FTP
    ftp_close($ftpConnection);

   /// echo "Le dossier '$source' a été copié vers '$destination' sur le serveur FTP avec succès.";
    return true;
}


$sourceFolder = $_GET['source'];
$destinationFolder = $_GET['destination'];
$ftpServer = $_GET['serveur_FTP'];
$ftpUsername = $_GET['Utilisateur_FTP'];
$ftpPassword = $_GET['Mot_de_passe_FTP'];
//for($i=1;$i<10;$i++){
$result = copyFolder($sourceFolder, $destinationFolder, $ftpServer, $ftpUsername, $ftpPassword);



$response = [
  'success' => $result,
  //'message' => $result ? "Le dossier '$sourceFolder' a été copié vers '$destinationFolder' avec succès. " : "La copie du dossier a échoué."
  'message' => $result ? 'ok':'nok'

];

// Renvoyer la réponse au client au format JSON
header('Content-Type: application/json');
echo json_encode($response);
//}


?>
