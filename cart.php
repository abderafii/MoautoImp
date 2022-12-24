<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <title>MOAUTO | Login</title>
    <link rel="icon" type="image/x-icon" href="Images/car.png">
</head>

<body>

    <header>
        <a href="indexs.html" class="logo"><img src="Images/MOAUTO 100.png" alt="MOAUTO Logo"></a>
        <div>
            <ul id="navigation">
                <li><a href="indexs.html">Home</a></li>
                <li><a href="Shops.html">Shop</a></li>
                <li><a href="Abouts.html">About Us</a></li>
                <li><a href="Contacts.html">Contact Us</a></li>
                <li><a class="active" href="cartauth.html"><i class="fa-solid fa-cart-shopping"></i></a></li>
            </ul>
        </div>
    </header>


    <section id="Cart" class="padding1">
        <table width="100%">
            <thead>
                <tr>
                    <td>Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php 
                            //start session to get variables from other php files the '@' is there to skip the error message
                            @session_start(); 
                            //start a conncetion to railway online Postgres database
                            $db_conn = new PDO("pgsql:host='containers-us-west-165.railway.app'; port=6055 ;dbname='railway'", "postgres", "V8mojHcHw8agMQ7XfCQo");
                            if ($db_conn) {
                            //link variables to session variables
                            $invoiceidentification = $_SESSION['invoiceid'];
                            $shoppingcartid= $_SESSION['cartid'];

                            //Needed queries to get variables
                            $query = "select distinct product.ProductDescription as Name, sum(line.LineQuantity) as Quantity, LineHistoricalPrice as Price, sum(line.LineTotalAmount) as SubTotal from line natural join product where invoiceid = :invoiceid and cartid = :cartid group by ProductDescription, LineHistoricalPrice;";

                            //prepare queries and bind values
                            $stmt = $db_conn->prepare($query);
                            $stmt->bindValue(":invoiceid", $invoiceidentification);
                            $stmt->bindValue(":cartid", $shoppingcartid);

                            //executing and error handling
                            if ($stmt->execute()) {
                                //get the data from the query
                                $user = $stmt->fetchall(PDO::FETCH_ASSOC);
                                if ($user) {
                                    //print the result
                                    echo '<table>';
                                    foreach($user as $row) {
                                        echo '<tr>';
                                        echo '<td>'.$row['name'].'</td>';                                        
                                        echo '<td>'.$row['price'].'</td>';
                                        echo '<td>'.$row['quantity'].'</td>';
                                        echo '<td>'.$row['subtotal'].'</td>';
                                       echo '</tr>';
                                       
                                     }
                                     echo '</table>';                                     
                                    echo '<br>'.'<br>'.'<br>'.'<h2>'.'<center>'."Your total is: " .$_SESSION['customerbalance']." MAD".'</center>'.'</h2>';
                                   
                                } else {
                                    readfile("failedcartauth.html");
                                }
                            } else {
                                echo $stmt->error;
                            }

                            
                            } else echo "Issue with database connection, it sometimes happens with railway online psql database, try later!";
                            ?> 
                </tr>
            </tbody>
        </table>
    </section>

    <footer class="padding1">
        <div class="col">
            <img src="Images/MOAUTO 100.png" alt="MOAUTO Logo">
            <h4>Contact</h4> <br>
            <p><strong>Address: </strong> 7 Rue 23, Casablanca 20600</p>
            <p><strong>Phone:</strong> 05 22 73 24 21</p>
            <p><strong>Hours:</strong> Monday to Saturday 8:00 am to 8:00 pm</p>

        </div>

        <div class="col">
            <h4>About</h4> <br>
            <a href="Abouts.html">About Us</a>
            <a href="Files/Delivery Info.pdf">Delivery Information</a>
            <a href="Files/Privacy Policy.pdf">Privacy Policy</a>
            <a href="Files/Terms and Conditions.pdf">Terms and Conditions</a>
            <a href="Contacts.html">Contact Us</a>
        </div>

        <div class="col">
            <h4>Account</h4> <br>
            <a href="createacc.html">Log out</a>
            <a href="Files/Help Instructions.pdf">Help</a>
        </div>

        <div class="creater">
            <p>2022, Abderafii Abdou</p>
        </div>

    </footer>

</body>

</html>