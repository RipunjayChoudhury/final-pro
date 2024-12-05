<html><center>
    <head>
        <h1>Book Easy</h1><br><h4>Register youself</h4><br>
        <title>bookeasy_signup</title>
    </head>
    <body>
        <form ation="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
            <table border=1>
                <tr>
                    <th>First name<input type="text" name="firstn" placeholder=""></th>
                    <th>Last Name<input type="text" name="lastn" palceholder=""></th>
                </tr>
                <tr>
                    <th>Phone No.<input type="number" name="pno" placeholder="1234567890"></th>
                    <th>Email <input type="text" name="mail" placeholder="@gmail.com"></th>
                </tr>
                <tr>
                    <th>Password<input type="password" name="pword" placeholder="enter_password"></th>
                   
                </tr>    
                <tr>
                    <th colspan=2>
                        <input type="submit" name="submit" >
                    </th>
                </tr>
            </table>                    
        </form>
    </body>
</center>
</html>

<?php
include ('dbc.php');
if(isset($_POST['submit']))
    {
        $fn= $_POST[ "firstn"];
        $ln= $_POST["lastn"];
        $em= $_POST["mail"];
        $pn= $_POST["pno"];
        $ps= $_POST["pword"];

    $inq="INSERT INTO users (fname,lname,email,phn,pass)
    values ('$fn','$ln','$em','$pn','$ps')";

    $qu=mysqli_query($con,$inq);
    if($qu)
{?>
    <script>
        alert("submited successfully");
    </script>
<?php
}
    else{
    ?>
    <script>
        alert("Error");
    </script>
<?php } }
?>