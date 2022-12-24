<?php
//start session to get variables from other php files

session_start();
//start a conncetion to railway online Postgres database

$db_conn = new PDO("pgsql:host='containers-us-west-165.railway.app'; port=6055 ;dbname='railway'", "postgres", "V8mojHcHw8agMQ7XfCQo");
if ($db_conn) {
}
//make sure the data posting is done properly

if (isset($_POST['num'])) {
         //link variables to posted variables or to $_SESSION variables

    $number = $_POST['num'];
    $productid = 3;
    $linehistoricalprice = 80;
    $linepaymenttype= 'C';
    $invoiceidentification = $_SESSION['inv'];
    $shoppingcartid= $_SESSION['shop'];
    //Needed queries to get variables

    $Insertt = " Insert into line( invoiceid, cartid, productid, linequantity, linehistoricalprice, linepaymenttype, linetotalamount) values (:invoiceid, :cartid, :productid, :linequantity, :linehistoricalprice, :linepaymenttype, :linetotalamount)"; 
     //prepare queries and bind values

    $stmt = $db_conn->prepare($Insertt);
    $stmt->bindValue(":invoiceid", $invoiceidentification);
    $stmt->bindValue(":cartid", $shoppingcartid);
    $stmt->bindValue(":productid", $productid);
    $stmt->bindValue(":linequantity", $number);
    $stmt->bindValue(":linehistoricalprice", $linehistoricalprice);
    $stmt->bindValue(":linepaymenttype", $linepaymenttype);
    $stmt->bindValue(":linetotalamount", $linehistoricalprice);

    //executing and error handling
    if ($stmt->execute()) {
        $user = $stmt->fetch();
        readfile('shopagain.html');
    } else {
        echo $stmt->error;
    }
} else {
    echo "All field are required.";
    die();
}
