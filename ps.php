<?php
    if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['phone'])) {

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
    
    
        $host = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbName = "moauto";
    
        $conn = new pg_connect("host='containers-us-west-165.railway.app', dbname=railway user=postgres password=V8mojHcHw8agMQ7XfCQo");
    
        if ($conn->connect_error) {
            die('Could not connect to the database.');
        } else {
            $Insert = "INSERT INTO customer(CusFname, CusLname, Cusemail, CusPassword, CusPhone, CusAddress) values(?, ?, ?, ?, ?, ?)";
    
            $stmt = $conn->prepare($Insert);
            $stmt->bind_param("ssssis", $firstname, $lastname, $email, $password, $phone, $address);
            if ($stmt->execute()) {
                echo 'Great success';
            } else {
                echo $stmt->error;
            }
        }
    
    
        $conn->close();
    } else {
        echo "All field are required.";
        die();
    }
