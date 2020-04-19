<?php
include("header.php");
$Uploaded_Files = "Mailing-list/Uploaded files/";
$conn = new mysqli($servername, $username, $password, $mailerDB);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<html>

<head id="head_tag">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Mailing List: SVCE-ACM CMS</title>
    <link rel="icon" type="image/png" sizes="600x600" href="../assets/img/Logo_White.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body id="page-top">
    <div id="wrapper">
     <?php include("navigation.php"); ?>
        <div class="container-fluid" >
                <style>
                .upload-btn-wrapper input[type=file] {
                    opacity: 0;
                }
                </style>
                <h3 class="text-dark mb-1" >Mailing List Generator</h3>

                <div class="" style="padding-bottom: 30px; padding-top:30px">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="file" required />
                    <br><br>
                    <input type="text" name="mailer_name" class="form-control border-1 small" style="width: 68%;max-width:15em;" placeholder="Enter the Mailer Name" required />
                    <br>
                    <input class="btn btn-primary" type="submit" name="submit" />
                </form>
                </div>
                <div class="card shadow">
                    <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold">Metadata</p>
                    </div>
                    <div class="card-body">
                    <?php
                    $executed=false;
                        /* Driver */
                        if (isset($_POST["submit"])) {
                            echo "<title><head>Processing......</head></title>";
                            if (isset($_FILES["file"])) {
                                if ($_FILES["file"]["error"] > 0) {
                                    echo "<b>Return Code</b>: " . $_FILES["file"]["error"] . "<br />";
                                }
                                else {
                                    echo "<b>Upload</b>: " . $_FILES["file"]["name"] . "<br />";
                                    echo "<b>Type</b>: " . $_FILES["file"]["type"] . "<br />";
                                    echo "<b>Size</b>: " . round (($_FILES["file"]["size"] / 1024),2) . " Kb<br />";
                                    if (file_exists("upload/" . $_FILES["file"]["name"])) {
                                        echo $_FILES["file"]["name"] . " already exists. ";
                                    }
                                    else {
                                        $storagename = $_FILES["file"]["name"];
                                        move_uploaded_file($_FILES["file"]["tmp_name"], $Uploaded_Files . $storagename);
                                        // echo "<b>Stored in</b>: " . "Uploaded files/" . $_FILES["file"]["name"] . "<br />";
                                    }
                                }
                            }
                            else {
                                echo "No file selected <br />";
                            }
                            //Create a table for this list
                            $table_name = str_replace(" ","_",$_POST["mailer_name"]);
                            $creation_query = "CREATE TABLE IF NOT EXISTS ".$table_name." (id int PRIMARY KEY AUTO_INCREMENT NOT NULL,name VARCHAR(255),email VARCHAR(255));";
                            $submit_stmt = $conn->prepare($creation_query);
                            if (!$submit_stmt) {
                                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "<br>";
                            }
                            $submit_stmt->execute();


                            echo "<br>";
                            $flag = true;
                            if (isset($storagename) && $handle = fopen($Uploaded_Files . $_FILES["file"]["name"], "r")) {
                    	        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    	            if ($flag) {
                    	                $flag = false;
                    	                continue;
                    	            }
                                    $name = ucwords($data[0]);
                                    $email = $data[1];
                                    $insert_query = 'INSERT INTO `'.$table_name.'` (`name`,`email`) VALUES ("'.$name.'","'.$email.'");';
                                    $insert_stmt = $conn->prepare($insert_query);
                                    if (!$insert_stmt) {
                                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "<br>";
                                    }
                                    $insert_stmt->execute();
                                    $executed='true';
                                }

                            }
                        }


                    ?>


                </div></div>
                <?php
                if($executed==true){
                    echo '<div class="alert alert-success" role="alert" style="margin-top:20px;">
                            Mailing list created!
                            </div>';
                }
                ?>
                <br><br>
            </div>
        </div>




        <footer class="bg-white sticky-footer">
            <div class="container my-auto">
                <div class="text-center my-auto copyright"><span>SVCE ACM Student Chapter</span></div>
            </div>
        </footer>


    </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="../assets/js/theme.js"></script>

</body>

</html>
