<?php
// Ouvrir le fichier XML
$xml = simplexml_load_file('Database/admin.xml');



// Récupérer les informations d'identification entrées par l'utilisateur
$username = $_POST['email'];
$password = $_POST['password'];

// Parcourir les utilisateurs de la table "admin"
foreach ($xml->admin as $admin) {
    // Vérifier si l'utilisateur existe
    if ((string)$admin->nom == $username) {
        // Vérifier si le mot de passe correspond
        if ((string)$admin->pwd == $password) {
            // Démarrer une session et stocker les informations de l'utilisateur
            session_start();
            $_SESSION['email'] = $username;
            $_SESSION['password'] = $password;
            $_SESSION['dernier_acces'] = date("Y-m-d");
            // Connecter l'utilisateur
            header("Location: maindashboard.php");
            break;
        }
        else { echo '<script>
            setTimeout(function(){
              alert("Mot de passe incorrect. Veuillez réessayer !");
              window.location.href = "login.html";
            }, 200);
          </script>';
            break;
        }
    }
    else { echo '<script>
        setTimeout(function(){
          alert("Email introuvable. Veuillez réessayer !");
          window.location.href = "login.html";
        }, 200);
      </script>';
  }
}


?>