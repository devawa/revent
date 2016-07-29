<?php
    require_once('dbconfig.php');

    if(isset($_REQUEST) && isset($_REQUEST['q'])){
        switch ($_REQUEST['q']) {
            case 'SignUp':
                echo(signUp());
                break;
            case 'Login':
                echo(login());
                break;
            case 'ViewHistory':
                if(isset($_REQUEST['id']))
                    echo(viewHistory($_REQUEST['id']));
                else
                    echo ('{ msg = "Ooops! EventHost ID not found." }');
                break;
            case 'ViewProfile':
                if(isset($_REQUEST['id']))
                    echo(viewProfile($_REQUEST['id']));
                else
                    echo ('{ msg = "Ooops! EventHost ID not found." }');
                break;
            default:
                echo('{ msg = "Ooops! Sorry your request could not be proccessed." }');
                break;
        }
    }

    
    function viewHistory($id){
        // initialize
        $servername = "localhost";
        $username   = "root";
        $passcode   = "IloveAv02";
        $db         = "TestApp";
        $conn       = new mysqli($servername, $username, $passcode, $db);

        if($conn->connect_error){
            die("Ooops! Something went wrong while attempting to connect to the server.");
        }

        // query 
        $statement = $conn->prepare('SELECT E_ID, E_Title, E_Venue ,E_Location, E_Time, E_Date, 
        E_Restrictions, E_Description, E_Capacity, E_Entrance_Fee, E_PosterURL FROM Event WHERE EH_ID = ?');
        
        // bind
        $statement->bind_param('i', $ehID);

        // assign value
        $ehID = $id;

        $statement->execute();
        $statement->bind_result($id, $title, $venue, $location, $time, $date, $restrictions, $description, $capacity, $entrancefee, $posterUrl);
        
        // fetch results & populate array with Json objs
        $size = $statement->num_rows;
        if($size > 0){
            $counter = 0;
            $toReturn = '{ events = ['; 
            while($statement->fetch() && $counter < $size){
                $toReturn .= '{';
                $toReturn .= 'id = ' . $row['E_ID'] . ', title = ' . $row['E_Title']. ', venue = ' . $row['E_Venue'];
                $toReturn .= ', location = ' . $row['E_Location'] . ', time = ' . $row['E_Time'];
                $toReturn .= ', date = ' . $row['E_Date'] . ', capacity = ' . $row['E_Capacity'];
                $toReturn .= ', entrancefee = ' . $row['E_Entrance_Fee'] . ', poster = ' . $row['E_PosterURL'];
                $toReturn .= ', description = ' . $row['E_Description'] . ', restriction = ' . $row['E_Restrictions']; 
                if($counter == $size - 1)
                    $toReturn .= '}';
                else
                    $toReturn .= '}, '; 
                $counter++;
            }
            $toReturn .= ']}';
        } else{
            return '{ msg = "No Events found."}';
        }

        // close connection
        $statement->close();
        $conn->close();

        return $toReturn;          
    }

    function viewProfile($id){
        // initialize
        $servername = "localhost";
        $username   = "root";
        $passcode   = "IloveAv02";
        $db         = "TestApp";
        $conn       = new mysqli($servername, $username, $passcode, $db);

        if($conn->connect_error){
            die("Ooops! Something went wrong while attempting to connect to the server.");
        }

        // query
        $statement = $conn->prepare('SELECT EH_ID, EH_Username, EH_Passcode, EH_Email FROM EventHost WHERE EH_ID = ?');
        
        // bind
        $statement->bind_param('i', $ehID);
        
        // assign value
        $ehID = $id;
        $statement->execute();
        
        // bind results & create Json obj
        $statement->bind_result($id, $uName, $pCode, $eMail);    
        $statement->fetch();
        $toReturn = '{ id = ' . $id . ', username = ' . $uName .', passcode = ' . $pCode . ', email = ' . $eMail . ' }';

        // finalize
        $statement->close();
        $conn->close();

        return $toReturn;     
    }

    function signUp(){
        if(isset($_POST)){
            // initialize
            $servername = "localhost";
            $username   = "root";
            $passcode   = "IloveAv02";
            $db         = "TestApp";
            $conn       = new mysqli($servername, $username, $passcode, $db);

            if($conn->connect_error){
                die("Ooops! Something went wrong while attempting to connect to the server.");
            }

            // query
            $statement = $conn->prepare('INSERT INTO EventHost(EH_Username, EH_Passcode, EH_Email) VALUES (?,?,?)');
            
            // bind
            $statement->bind_param('sss', $uName, $pCode, $eMail);

            // assign values
            $uName = $_POST['username'];
            $pCode = $_POST['passcode'];
            $eMail = $_POST['email'];

            // execute and get results
            $result = $statement->execute();

            // close connection
            $statement->close();
            $conn->close();

            if($result){
                return '{ msg = "SignUp Successful" }';
            } else {
                return '{ msg = "SignUp Failed" }';    
            }
        } else {
            return '{ msg = "SignUp Failed" }';
        }
    }

    function login(){
        if(isset($_POST)){
            // initialize
            $servername = "localhost";
            $username   = "root";
            $passcode   = "IloveAv02";
            $db         = "TestApp";
            $conn       = new mysqli($servername, $username, $passcode, $db);

            if($conn->connect_error){
                die("Ooops! Something went wrong while attempting to connect to the server.");
            }

            // query
            $statement = $conn->prepare('SELECT EH_ID FROM EventHost WHERE EH_Username = ? AND EH_Passcode = ?');
            
            // bind
            $statement->bind_param('ss', $uName, $pCode);
            // $_POST['username'] = "KEO";
            // $_POST['passcode'] = "HE";
            
            // assign values
            $uName = $_POST['username'];
            $pCode = $_POST['passcode'];

            // execute and get result
            $statement->execute();
            $statement->bind_result($ehID);
            $statement->fetch();

            // close connections
            $statement->close();
            $conn->close();
            
            // finalize
            if($ehID != null)
                return '{ msg = "Access Granted" }';
            else 
                return '{ msg = "Access Denied" }';
        } else {
            return '{ msg = "Access Denied" }';
        }
    }
?>