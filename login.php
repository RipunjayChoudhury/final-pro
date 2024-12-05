<html><center>
    
    <head>
        <h1>Book Easy</h1><br><h4>Login</h4><br>
        <title>bookeasy_login</title>
    </head>
    <body>
        <form ation="" method="post"><table border=1>
            <tr>
                <th>Email <input type="text" name="email" placeholder="@gmail.com"></th>
            </tr>
            <tr>
                <th>Password<input type="password" name="pass" placeholder="enter_password"></th>
            </tr>    
            <tr>
                <th colspan=2>
                    <input type="submit" name="submit">
                </th>
            </tr>







</table>                    
</form>
</body>
</center>
</html>

<?php
session_start();
include 'dbc.php';
if(isset($_POST['submit']))
{
    $username=$_POST['email'];
    $passw=$_POST['pass'];
    $sql="SELECT * from users where email= '$username'";
    $query= mysqli_query($con,$sql);
    $uname_count=mysqli_num_rows($query);
    if($uname_count)
    {
        $upass=mysqli_fetch_assoc($query);
        $_SESSION['name']=$upass['email'];
        $dpass=$upass['pass'];
        //$pass_decode=password_verify($passw,$dpass);
        if($dpass==$passw)
        {
            echo "Log in successful";
        ?>
        <script>
            window.location.href='home2.php';
        </script>
        <?php 
        }
        else
        {
            echo"Password wrong";
        }  
    }
   
    else
    {
        echo "Invalid username";
    } 
    
}    

?>