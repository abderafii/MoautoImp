<?php
//start a conncetion to railway online Postgres database
$db_conn = new PDO("pgsql:host='containers-us-west-165.railway.app'; port=6055 ;dbname='railway'", "postgres", "V8mojHcHw8agMQ7XfCQo");
if ($db_conn) {
}
//make sure the data posting is done properly
if (isset($_POST['password']) && isset($_POST['email'])) {
    //link variables to posted variables
    $email = $_POST['email'];
    $password = $_POST['password'];

    //Needed queries to get variables
    $Check = " Select cusbalance from Customer where Cusemail = :email and cusPassword = :password";

     //prepare queries and bind values
    $stmt = $db_conn->prepare($Check);
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":password", $password);

    //executing and error handling
    if ($stmt->execute()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo 'The total amount for you is: ' .$user['cusbalance'];
        } else {
            readfile("failtotal.html");
        }
    } else {
        echo $stmt->error;
    }
} else {
    echo "All field are required.";
    die();
}
