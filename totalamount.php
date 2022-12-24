<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <title>MOAUTO | Cart</title>
    <link rel="icon" type="image/x-icon" href="Images/car.png">
</head>
<body>

    <header>
        <a href="index.html" class="logo"><img src="Images/MOAUTO 100.png" alt="MOAUTO Logo"></a>
        <div>
            <ul id="navigation">
                <li><a href="index.html">Home</a></li>
                <li><a href="Shop.html">Shop</a></li>
                <li><a href="About.html">About Us</a></li>
                <li><a href="Contact.html">Contact Us</a></li>
                <li><a class="active" href="total.html"><i class="fa-solid fa-cart-shopping"></i></a></li>
                <li><a href="User.html"><i class="fa-solid fa-arrow-right-to-bracket"></i></a></li>
            </ul>
        </div>
    </header>

    <section id="create" class="padding1">

        <div>

            <div>
                <form method="POST" action="total.php">
                    <div>
                        <h2 class="text1">Your Total Amount: <?php @session_start(); echo $_SESSION['customerbalance']." MAD"; ?> </h2>
                    
                        <div>
                </form>
            </div>
        </div>

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