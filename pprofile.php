<?php
include 'config.php';
session_start();

if (isset($_SESSION['worker_id'])) {
    $worker_id = $_SESSION['worker_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $category = $_POST['category'];
    $qualification= $_POST['qualification'];
    $state= $_POST['state'];
    $district= $_POST['district'];
    $hometwon= $_POST['hometwon'];
    $perhour= $_POST['Rupees'];
        // Update profile data
        $sql = "UPDATE workers SET Full_name='$fullname', phone='$phone',Gender='$gender', Age='$age', Category='$category', Qualification='$qualification', State='$state', District='$district', Home_twon='$hometwon', perhour_Rupees='$perhour'  WHERE id='$worker_id'";
        mysqli_query($conn, $sql);

        // Handle document upload
        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['documents']['name'][$key];
                $file_tmp = $_FILES['documents']['tmp_name'][$key];
                $path = "uploads/" . $file_name;
                move_uploaded_file($file_tmp, $path);

                $sql = "INSERT INTO worker_documents (worker_id, document_path) VALUES ('$worker_id', '$path')";
                mysqli_query($conn, $sql);
            }
        }

        echo " <script> 
        alert('Profile updated successfully');
        </script> ";
    }
} else {
    echo "Please log in first.";
}
$sql = "SELECT * FROM workers WHERE id='$worker_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$conn->close();
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
fullname <input type="text" name="fullname" value="<?php echo $user['Full_name']; ?>" placeholder="Full Name"><br></br>
   phone <input type="text" name="phone" value="<?php echo $user['phone']; ?>" placeholder="Phone Number"><br></br>
    gender <input type="text" name="gender" value="<?php echo $user['Gender']; ?>" placeholder="Gender"><br></br>
    Age <input type="text" name="age" value="<?php echo $user['Age']; ?>" placeholder="Age"><br></br>  
   category <select name="category" id="category">
            <option value="electrict"> electrict </option>
            <option value="plumber"> plumber </option>
            <option value="bambu works"> bambu works </option>
            <option value="home servant"> home servant </option>
         </select><br></br>
   qualification <input type="text" name="qualification" value="<?php echo $user['Qualification']; ?>" placeholder="Qualification"><br></br>
  state <input type="text" name="state" value="<?php echo $user['State']; ?>" placeholder="state"><br></br>
    district <input type="text" name="district" value="<?php echo $user['District']; ?>" placeholder="district"><br></br>
    hometwon  <input type="text" name="hometwon" value="<?php echo $user['Home_twon']; ?>" placeholder="home_twon"><br></br>
    per hour charges <input type="text" name="Rupees" value="<?php echo $user['perhour_Rupees']; ?>" placeholder="Rupees"><br></br>
     <!-- <textarea name="profile_data" placeholder="Update your profile..."></textarea>  -->
    <input type="file" name="documents[]" multiple> <br></br>
    <button type="submit">Update Profile</button> 
  <button  a href="logout.php"  > logout </a> </button> 
</form>
