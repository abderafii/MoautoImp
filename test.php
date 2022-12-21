<?php
//start a conncetion to railway online Postgres database
    $db_conn = new PDO("pgsql:host='containers-us-west-165.railway.app'; port=6055 ;dbname='railway'", "postgres", "V8mojHcHw8agMQ7XfCQo");
    if ($db_conn){
    }
//make sure the data posting is done properly
    if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['phone'])) {
         //link variables to posted variables
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

            //Needed queries to get variables

            $Insert = "INSERT INTO customer(CusFname, CusLname, Cusemail, CusPassword, CusPhone, CusAddress) values(:firstname,:lastname,:email,:password,:phone,:address)";

            //prepare queries and bind values
            $stmt = $db_conn->prepare($Insert);
            $stmt->bindValue(":firstname", $firstname);
            $stmt->bindValue(":lastname", $lastname);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":password", $password);
            $stmt->bindValue(":phone", $phone);
            $stmt->bindValue(":address", $address);
            
            //executing and error handling

            if ($stmt->execute()) {
                readfile("successlog.html");
            } else {
                echo $stmt->error;
            }

    
    
    } else {
        echo "All field are required.";
        die();
    }
    

?>