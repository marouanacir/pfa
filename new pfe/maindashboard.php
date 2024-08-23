<?php
// Load the XML file
$xml = simplexml_load_file('Database/màj.xml');

// Create an empty array to store the application data
$applications = array();

// Loop through each maj element and collect the data
foreach ($xml->maj as $maj) {
    $application = (string) $maj->app;

    // Check if the application already exists in the array
    if (isset($applications[$application])) {
        // Update the date if the current date is greater
        $currentDate = strtotime($maj->Date_creation);
        $existingDate = strtotime($applications[$application]['date']);

        if ($currentDate > $existingDate) {
            $applications[$application]['date'] = date('Y-m-d', $currentDate);
        }

        // Increment the update count
        $applications[$application]['updateCount']++;
    } else {
        // Add a new entry for the application
        $applications[$application] = array(
            'date' => (string) $maj->Date_creation,
            'updateCount' => 1
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="maindashboard.css">
    <title>Document</title>
</head>
<body>
        <?php   
           include 'dashboard.php';
        ?>
    <main >
        <div class="cards">
            <div class="card-single">
                <?php
                // Load the XML file
                $xml = simplexml_load_file('Database/clients.xml');

                // Count the number of clients
                $numberOfClients = count($xml->client);

                ?>

                <div>
                    <h1><?php echo $numberOfClients; ?></h1>
                    <span>Clients</span>
                </div>
                <div>
                    <span class="las la-users"></span>
                </div>
            </div>
            <div class="card-single">
            <?php
                // Load the XML file
                $xml = simplexml_load_file('Database/màj.xml');

                // Count the number of maj
                $numberOfmajs = count($xml->maj);

                ?>

                <div>
                    <h1><?php echo $numberOfmajs; ?></h1>
                    <span>Mises à jour</span>
                </div>
                <div>
                    <span class="las la-clipboard-list"></span>
                </div>
            </div>
            <div class="card-single">
                <div>
                    <h1>4</h1>
                    <span>Applications</span>
                </div>
                <div>
                    <span class="las la-laptop-code"></span>
                </div>
            </div>
            <div class="card-single">

                <div>
                    <h1><?php echo $dernier_acces; ?></h1>
                    <span>Dernier accès</span>
                </div>
                <div>
                    <span class="las la-calendar-day "></span>
                </div>
            </div>
       </div>
       
       <div class="recent-grid">
            <div class="projects">
                <div class="card">
                    <div class="card-header">
                        <h3>Mises à jour dans le serveur</h3>
                        <a href="maj.php"><button class="see">Voir plus <span class="las la-arrow-right"></span></button></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table width="100%">
                            <thead>
                                <tr>
                                <td>Application</td>
                                <td>Date de la dernière mise à jour</td>
                                <td>Totale des mises à jour</td>
                                </tr>
                            </thead>
                            <tbody>
        <?php
        // Loop through the applications array and generate table rows
        foreach ($applications as $application => $data) {
            echo '<tr>';
            echo '<td>' . $application . '</td>';
            echo '<td>' . $data['date'] . '</td>';
            echo '<td>' . $data['updateCount'] .  ' Mises à jour disponibles</td>'.'</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                    
            </div>
        <div class="customers">
            <div class="card">
                <div class="card-header">
                    <h3> Nos clients</h3>
                    <a href="clients.php"><button class="see"> Voir plus <span class="las la-arrow-right"></span></button></a>
                </div>
                <div class="card-body">
                <?php
                        $xmlC = simplexml_load_file('Database/clients.xml');
                        foreach ($xmlC->client as $index => $client) {
                        ?>
                    <div class="customer">
                        <div class="info">
                        
                        <img src="client.png" width="40px" height="40px" alt="">
                        <div>
                        <h4> <?php     echo '<td>' . $client->nom . '</td>';
                        ?></h4>
                        <small><?php     echo '<td>' . $client->url . '</td>';
                        ?></small>
                    </div>
                </div>
                <div class="contact">
                    <span class="las la-user-circle"></span>
                    <span class="las la-comment"></span>
                    <span class="las la-phone"></span>
                </div>
                </div>
                <?php
                        }
                ?>
                
            </div>
        </div>
       </div>
       </div>
       
    </main>
</body>
</html>