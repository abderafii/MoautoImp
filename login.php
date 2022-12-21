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
    $Check = " Select * from Customer where Cusemail = :email and cusPassword = :password";
    $result = 'Select cusid FROM Customer WHERE Cusemail = :email and cusPassword = :password';
    $getcartID = 'SELECT cartid FROM shopping_cart WHERE cusid = :customerid';
    $getinvID = 'SELECT invoiceid FROM customer_invoice WHERE cusid = :customerid';
    
    //prepare queries
    $stmt = $db_conn->prepare($Check);
    $stmt2 = $db_conn->prepare($result);

    //bind values
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":password", $password);
    $stmt2->bindValue(":email", $email);
    $stmt2->bindValue(":password", $password);

    //execute query
    $stmt2 ->execute();
    
    //get result
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
     //error handling
  if (!$stmt2) { 
    die("Error: ..");
    }
    $customerid = $row['cusid'];
    if ($stmt->execute()) {
        $user = $stmt->fetch();
        if ($user) {
            readfile("successlog.html");
        } else {
            readfile("failedlog.html");
        }
    } else {
        echo $stmt->error;
    }
    //prepare queries and bind values
    $stmt3 = $db_conn->prepare($getcartID);
    $stmt4 = $db_conn->prepare($getinvID);
    $stmt3->bindValue(":customerid", $customerid);
    $stmt4->bindValue(":customerid", $customerid);
    //execute query
    $stmt3 ->execute();
    //get result
    $row2 = $stmt3->fetch(PDO::FETCH_ASSOC);
    //error handling and getting variables data using $_SESSION
    if (!$stmt3) { 
        die("Error: ..");
        }    
        echo $shoppingcartid = $row2['cartid'];
        $_SESSION['shop'] = $shoppingcartid;

    
    $stmt4 ->execute();
    $row3 = $stmt4->fetch(PDO::FETCH_ASSOC);
    if (!$stmt4) { 
        die("Error: ..");
        }
        echo $invoiceidentification = $row3['invoiceid'];
        $_SESSION['inv'] = $invoiceidentification;
} else {
    echo "All field are required.";
    die();
}
