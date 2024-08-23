<?php
session_start();

if (isset($_SESSION['email']) & isset($_SESSION['password'])  ) {
    $username = $_SESSION['email'] ;
    $password = $_SESSION['password'] ;
    $dernier_acces = $_SESSION['dernier_acces'];

    
}else{header('Location: login.html');
  exit;}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="dist/dist/mark.min.js"></script>

</head>
<body>
<aside>

    <input type="checkbox" id="nav-toggle">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h2><span class="lab la-accusoft"></span> <span> GIGAMANAGER  </span></h2>
        </div>
        <div class="sidebar-menu">
            <article>
            <ul>
                <li>
                    <a href="maindashboard.php"  ><span class="las la-igloo"></span>
                    <span>Dashboard</span></a>
                </li>
                <li>
                    <a href="clients.php"><span class="las la-users"></span>
                    <span>Clients</span></a>
                </li>
                <li>
                    <a href="maj.php"><span class="las la-shopping-bag"></span>
                    <span>Mise à jour</span></a>
                </li>
                

                
            </ul>
        </div>
        <form method="post">
            <li><a> <button id="deco" name="Déconnexion" class="deconnexion" ><span class="las la-power-off"></span>  Déconnexion </button> </a></li>
            </form>
            <?php
            if(isset($_POST['Déconnexion'])){
              session_unset();
              session_destroy();
              
            }
            ?>
    </div>
    </aside>
    </article>
    <script>
  document.addEventListener("DOMContentLoaded", function() {
    const Links = document.querySelectorAll("aside article a");

    Links.forEach((link) => {
      link.addEventListener("click", (event) => {
        const selectedLinkId = link.getAttribute("href");
        localStorage.setItem("selectedLink", selectedLinkId);
      });
    });

    const selectedLinkId = localStorage.getItem("selectedLink");
    if (selectedLinkId) {
      const selectedLink = document.querySelector(`aside article a[href="${selectedLinkId}"]`);
      if (selectedLink) {
        selectedLink.classList.add("selected");
      }
    }
  });
</script>




    <div class="main-content">
        <header>
            <h2>
                <label for="nav-toggle">
                    <span class="las la-bars"></span>
                </label>
                Dashboard
            </h2>
            

            <style>
    .highlight {
        background-color: yellow;
    }
</style>

<div class="search-wrapper">
    <span class="las la-search"></span>
    <input type="search" placeholder="Search here" onkeyup="searchText(this.value)" />
</div>

<script>
    var marker;

    function searchText(keyword) {
        if (marker) {
            marker.unmark();
        }

        if (keyword) {
            marker = new Mark(document.body);
            marker.mark(keyword, {
                separateWordSearch: false,
                className: "highlight"
            });
        }
    }
</script>




            <div class="user-wrapper">
                <img src="admin.png" width="40px" height="40px" alt="">
                <div>
                    <h4>Mr Hicham</h4>
                    <small>Administrateur</small>
                </div>
            </div>
        </header>
       

    </div>
</body>
</html>