<?php
    $to = "ab.abdou@aui.ma";

    if (isset($_POST['Name']) && isset($_POST['Subject']) && isset($_POST['message'])){
        
        $name = $_POST['Name'];
        $subject = '$name says:';
        
        $subject .= $_POST['Subject'];
        $message = $_POST['message'];
        $header = 'FROM: Ghafour <a.abdou@aui.ma>\r\n';

    $retval = mail ($to,$subject,$message,$header);
         
         if( $retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }
        }
?>