<?php
//start session to get variables from other php files
session_start();
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
    $Check = " Select distinct customer_invoice.invoiceid, line.cartid from Customer natural join customer_invoice natural join line where Cusemail = :email and cusPassword = :password";

     //prepare queries and bind values
    $stmt = $db_conn->prepare($Check);
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":password", $password);
    

    //executing and error handling
    if ($stmt->execute()) {
        $user = $stmt->fetch();
        if ($user) {
            $invoiceid = $user[0];
            $cartid = $user[1];
            $_SESSION['invoiceid'] = $invoiceid;
            $_SESSION['cartid'] = $cartid;
            require("cart.php");
        } else {
            readfile("failedcartauth.html");
        }
    } else {
        echo $stmt->error;
    }
} else {
    readfile("failedcartauth.html");
    die();
}
