<?php
    require_once('dbconfig.php');

    if(isset($_REQUEST) && isset($_REQUEST['q'])){
        switch ($_REQUEST['q']) {
            case 'newEvent':
                if(isset($_REQUEST['id']))
                    echo(makeNewEvent($_REQUEST['id']));
                else
                    echo ('{ msg = "Ooops! EventHost ID not found." }');
                break;
            case 'BrosweEvents':
                echo(browseEvents());
                break;
            default:
                echo('{ msg = "Ooops! Sorry your request could not be proccessed." }');
                break;
        }
    }

    function makeNewEvent($id){

        if(isset($_POST)){
            $servername = "localhost";
            $username   = "root";
            $passcode   = "IloveAv02";
            $db         = "TestApp";
            $conn       = new mysqli($servername, $username, $passcode, $db);

            if($conn->connect_error){
                die("Ooops! Something went wrong while attempting to connect to the server.");
            }      

            $statement = $conn->prepare("INSERT INTO 
            Event (E_Title, E_Venue, E_Location, E_Time, E_Date, E_Restrictions, E_Description, 
            E_Capacity, E_Entrance_Fee, E_PosterURL, EH_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // bind parms
            $statement->bind_param("sssssssiisi", $etitle, $evenue, $elocation, $etime, $edate, 
            $erestrictions, $edescription, $ecapacity, $eentrancefee, $eposterurl, $eehid );

            // assign args
            $etitle = $_POST['title'];
            $evenue = $_POST['venue'];
            $elocation = $_POST['location'];
            $etime = $_POST['time'];
            $edate = $_POST['date'];
            $erestrictions = $_POST['restrictions'];
            $edescription = $_POST['description'];
            $ecapacity = $_POST['capacity'];
            $eentrancefee = $_POST['entrancefee'];
            $eposterurl = $_POST['poster'];
            $eehid = $id;

            $statement->execute();
            $statement->close();
            $conn->close();
            echo('{ msg = "Event successfully created!" }');
        }
    } 

    function browseEvents(){
        $servername = "localhost";
        $username   = "root";
        $passcode   = "IloveAv02";
        $db         = "TestApp";
        $conn       = new mysqli($servername, $username, $passcode, $db);

        if($conn->connect_error){
            die("Ooops! Something went wrong while attempting to connect to the server.");
        }      

        $statement = 'SELECT * FROM Event';
        $result = $conn->query($statement);
        $size = $result->num_rows;
        if($size > 0){
            $i = 0;
            $toReturn = '{ events = [';
            while($row = $result->fetch_assoc() && $i < $size){
                $toReturn .= '{';
                $toReturn .= 'id = ' . $row['E_ID'] . ', title = ' . $row['E_Title']. ', venue = ' . $row['E_Venue'];
                $toReturn .= ', location = ' . $row['E_Location'] . ', time = ' . $row['E_Time'];
                $toReturn .= ', date = ' . $row['E_Date'] . ', capacity = ' . $row['E_Capacity'];
                $toReturn .= ', entrancefee = ' . $row['E_Entrance_Fee'] . ', poster = ' . $row['E_PosterURL'];
                $toReturn .= ', description = ' . $row['E_Description'] . ', restriction = ' . $row['E_Restrictions']; 
                if($i == $size - 1)
                    $toReturn .= '}';
                else
                    $toReturn .= '}, '; 
                $i++;
            }
            $toReturn .= ']}';
        } else {
            return '{ msg = "No Events found."}';
        }
    }
?>