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
                    echo ('{ msg = "Ooops! Customer ID not found." }');
                break;
            case 'ViewProfile':
                if(isset($_REQUEST['id']))
                    echo(viewProfile($_REQUEST['id']));
                else
                    echo ('{ msg = "Ooops! Customer ID not found." }');
                break;
            case 'deregister':
                if(isset($_REQUEST['id']))
                    echo(deregister($_REQUEST['id']));
                else
                    echo('{ msg = "Ooops! Customer ID not found." }');
                break;
            case 'newBooking':
                if(!isset($_REQUEST['cID']))
                    echo('{ msg = "Ooops! Customer ID not found." }');
                else if(!isset($_REQUEST['eID']))
                    echo('{ msg = "Ooops! EventHost ID not found." }');
                else
                    echo(makeBooking($_REQUEST['eID'], $_REQUEST['cID']));    
                break;
            default:
                echo('{ msg = "Ooops! Sorry your request could not be proccessed." }');
                break;
        }
    }

    function viewProfile($id){
        $servername = "localhost";
        $username   = "root";
        $passcode   = "IloveAv02";
        $db         = "TestApp";
        $conn       = new mysqli($servername, $username, $passcode, $db);

        if($conn->connect_error){
            die("Ooops! Something went wrong while attempting to connect to the server.");
        }

        $statement = $conn->prepare('SELECT * FROM Customer WHERE C_ID = ?');
        $statement->bind_param('i', $custID);

        // assign value
        $custID = $id;
        $statement->execute();

        // bind results & create Json obj
        $statement->bind_result($id, $fName, $sName, $uName, $pCode, $eMail, $deviceIsActive);
        $statement->fetch();
        $toReturn = '{ id = ' . $id . ', fullname = ' . $fName . ', surname = ' . $sName;
        $toReturn .= ', username = ' . $uName . ', passcode = ' . $pCode . ', email = ' . $eMail;
        $toReturn .= ', deviceIsActive = ' . $deviceIsActive . ' }';
        
        // finalize
        $statement->close();
        $conn->close();

        return $toReturn;        
    }

    function deregister($id){
        $servername = "localhost";
        $username   = "root";
        $passcode   = "IloveAv02";
        $db         = "TestApp";
        $conn       = new mysqli($servername, $username, $passcode, $db);

        if($conn->connect_error){
            die("Ooops! Something went wrong while attempting to connect to the server.");
        }
        $stmt = $conn->prepare("SELECT C_ID FROM Customer WHERE C_ID = ?");
        $stmt->bind_param('i', $custID);

        $custID = $id;      
        $stmt->execute();
        $stmt->bind_result($cID);
        $stmt->fetch();
        $stmt->close();
       
        if($cID != null){
            $statement = $conn->prepare("UPDATE Customer SET C_DeviceIsActive = 'n' WHERE C_ID = ?");
            $statement->bind_param('s', $custID);

            $custID = $id;
            $statement->execute();
            $statement->close();
            $conn->close();

            return '{ msg = "Device Successfully Deactivated" }';
        } else {
            $conn->close();
            return '{ msg = "Device Deactivation Failed." }';
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
        E_Restrictions, E_Description, E_Capacity, E_Entrance_Fee, E_PosterURL
        FROM Event AS e INNER JOIN Booking as b ON e.E_ID = b.B_ID_Event WHERE b.B_ID_Customer = ?');

        // bind
        $statement->bind_param('i', $custID);

        // assign value
        $custID = $id;

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
            $statement = $conn->prepare('INSERT INTO Customer(C_Fullname, C_Surname, C_Username, C_Passcode, C_Email, C_DeviceIsActive) VALUES (?,?,?,?,?,?)');
            
            // bind
            $statement->bind_param('ssssss', $fName, $sName, $uName, $pCode, $eMail, $deviceIsActive);

            // assign values
            $fName = $_POST['fullname'];
            $sName = $_POST['surname'];
            $uName = $_POST['username'];
            $pCode = $_POST['passcode'];
            $eMail = $_POST['email'];
            $deviceIsActive = 'y';

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
            $statement = $conn->prepare('SELECT C_ID FROM Customer WHERE C_Username = ? AND C_Passcode = ?');
            
            // bind
            $statement->bind_param('ss', $uName, $pCode);
            // $_POST['username'] = "KEO";
            // $_POST['passcode'] = "HE";
            
            // assign values
            $uName = $_POST['username'];
            $pCode = $_POST['passcode'];

            // execute and get result
            $statement->execute();
            $statement->bind_result($cID);
            $statement->fetch();

            // close connections
            $statement->close();
            $conn->close();
            
            // finalize
            if($cID != null)
                return '{ msg = "Access Granted" }';
            else 
                return '{ msg = "Access Denied" }';
        } else {
            return '{ msg = "Access Denied" }';
        }
    }

    function makeBooking($eID, $cID){
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
        $statement = $conn->prepare("SELECT C_ID FROM Customer WHERE C_ID = ?");

        // bind
        $statement->bind_param('i', $custID);

        // assign value
        $custID = $cID;

        $statement->execute();
        $statement->bind_result($cID);
        $statement->fetch();
        $statement->close();


        // query
        $statement = $conn->prepare("SELECT E_ID FROM Event WHERE E_ID = ?");

        // bind
        $statement->bind_param('i', $eventID);

        // assign value
        $eventID = $eID;

        $statement->execute();
        $statement->bind_result($eID);
        $statement->fetch();
        $statement->close();
    
        if($eID != null && $cID != null){
            // query
            $statement = $conn->prepare('INSERT INTO Booking (B_ID_Event, B_ID_Customer) VALUES (?, ?)');

            // bind
            $statement->bind_param('ii', $eventID, $custID);

            // assign value
            $eventID = $eID;
            $custID = $cID;

            // execute
            $result = $statement->execute();
            
            // finalize
            $statement->close();
            $conn->close();

            if($result){
                return '{ msg = "Booking Successful" }';
            } else {
                return '{ msg = "Booking Failed" }';
            }

        } else {
            $conn->close();
            return '{ msg = "Booking Failed" }';
        }

    }
?>