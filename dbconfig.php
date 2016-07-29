<?php 
/*
* Author: Keoagile Dinake
* Date: 22/07/2016
* Todo: Database configuration file
*/
$servername = "localhost";
$username   = "root";
$passcode   = "IloveAv02";
$db         = "TestApp";
    
function initDB(){
    $servername = $GLOBALS["servername"];
    $username   = $GLOBALS["username"];
    $passcode   = $GLOBALS["passcode"];
    $db         = $GLOBALS["db"];
    $conn       = new mysqli($servername, $username, $passcode);

    if($conn->connect_error){
        die("Ooops! Something went wrong while attempting to connect to the server.");
    }

    $statement = "CREATE DATABASE IF NOT EXISTS TestApp";
   
    if(!$conn->query($statement)){
        die("Ooops! Something went wrong while attempting to configure the database.");
    }

    initDBTables();
}

function initDBTables(){
    $servername = $GLOBALS["servername"];
    $username   = $GLOBALS["username"];
    $passcode   = $GLOBALS["passcode"];
    $db         = $GLOBALS["db"];
    $conn       = new mysqli($servername, $username, $passcode, $db);

    if($conn->connect_error){
        die("Ooops! Something went wrong while attempting to connect to the server.");
    }

    if(!isExist('Customer')){
        $statement = 'CREATE TABLE Customer(
            C_ID INT AUTO_INCREMENT NOT NULL,
            C_Fullname VARCHAR(50) NULL,
            C_Surname VARCHAR(50) NULL,
            C_Username VARCHAR(50) NOT NULL,
            C_Passcode VARCHAR(64) NOT NULL,
            C_Email VARCHAR(50) NOT NULL,
            C_DeviceIsActive CHAR(1) NOT NULL,
            PRIMARY KEY (C_ID) 
        )';
        if(!$conn->query($statement)){
            die("Ooops! Something went wrong while attempting to configure the database[Customer]");
        }
    } // else { die("Ooops! Something went wrong while attempting to configure the database."); }

    if(!isExist('EventHost')){
        $statement = 'CREATE TABLE EventHost(
            EH_ID INT AUTO_INCREMENT NOT NULL,
            EH_Username VARCHAR(50) NOT NULL,
            EH_Passcode VARCHAR(64) NOT NULL,
            EH_Email VARCHAR(50) NOT NULL,
            PRIMARY KEY (EH_ID)
        )';
        if(!$conn->query($statement)){
            die("Ooops! Something went wrong while attempting to configure the database[EventHost]");
        }
    } // else { die("Ooops! Something went wrong while attempting to configure the database."); }

    if(!isExist('Event')){
        $statement = 'CREATE TABLE Event(
            E_ID INT AUTO_INCREMENT NOT NULL,
            E_Title VARCHAR(50) NOT NULL,
            E_Venue VARCHAR(50) NOT NULL,
            E_Location VARCHAR(50) NOT NULL,
            E_Time VARCHAR(20) NOT NULL,
            E_Date VARCHAR(20) NOT NULL,
            E_Restrictions VARCHAR(255) NULL,
            E_Description VARCHAR(255) NULL,
            E_Capacity INT NOT NULL,
            E_Entrance_Fee INT NOT NULL,
            E_PosterURL VARCHAR(255) NULL,
            EH_ID INT NOT NULL,
            PRIMARY KEY (E_ID),
            FOREIGN KEY (EH_ID) REFERENCES EventHost(EH_ID) 
        )';
        if(!$conn->query($statement)){
            die("Ooops! Something went wrong while attempting to configure the database[Event]");
        }
    } // else { die("Ooops! Something went wrong while attempting to configure the database."); }

    if(!isExist('Booking')){
        $statement = 'CREATE TABLE Booking(
            B_ID_Event INT NOT NULL,
            B_ID_Customer INT NOT NULL,
            PRIMARY KEY (B_ID_Event, B_ID_Customer),
            FOREIGN KEY (B_ID_Event) REFERENCES Event(E_ID),
            FOREIGN KEY (B_ID_Customer) REFERENCES Customer(C_ID)     
        )';
        if(!$conn->query($statement)){
            die("Ooops! Something went wrong while attempting to configure the database[Booking]");
        }
    } // else { die("Ooops! Something went wrong while attempting to configure the database."); }
    
}

function isExist($tableName){
    if(is_null($tableName)){
        return false;
    }

    $servername = $GLOBALS["servername"];
    $username   = $GLOBALS["username"];
    $passcode   = $GLOBALS["passcode"];
    $db         = $GLOBALS["db"];
    $conn       = new mysqli($servername, $username, $passcode, $db);

    if($conn->connect_error){
        return false;
    }
    $sqlstmt = "SELECT * FROM " . $tableName . " LIMIT 1";
    $toReturn  = $conn->query($sqlstmt) ? true : false;
    return $toReturn;
}

// Main
    initDB();
?>