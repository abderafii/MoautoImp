<?php
$username = filter_input(INPUT_POST, 'CusFname');
$password = filter_input(INPUT_POST, 'CusPassword');
if (!empty($username)) {
    if (!empty($password)) {
        $host = "localhost";
        $dbusername = "root";
        $dbpassword = "";
        $dbname = "moauto";
        // Create connection
        $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);


        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        } else {
            $sql = "INSERT INTO customer(CusFname, CusPassword) values ('$username','$password')";
            if ($conn->query($sql)) {
                echo "New record is inserted sucessfully";
            } else {
                echo "Error: " . $sql . "
" . $conn->error;
            }
            $conn->close();
        }
    } else {
        echo "Password should not be empty";
        die();
    }
} else {
    echo "Firstname should not be empty";
    die();
}
?>