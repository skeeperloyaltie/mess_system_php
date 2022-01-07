<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 's') {
    header("Location: login.php");
}
$menupath = "./uploads/1.jpeg";
$sql = "SELECT * FROM timetable ORDER BY id DESC LIMIT 1";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$menupath = $row['path'];
$uid = $_SESSION['id'];
$rm = false;
$menu = true;
$mrd = false;
$reset = false;
$cww = false;
$pay = false;
$c  = false;
$Date = date("Y/m/d");
$Day = date("l");



if (isset($_POST['rm'])) {
    $rm = true;
    $reset = false;
    $menu = false;
    $mrd = false;
    $cww = false;

    $pay = false;
    $c = false;

    $Date = date("Y/m/d");
    $Day = date("l");
}
if (isset($_POST['menu'])) {
    $menu = true;
    $rm = false;
    $cww = false;
    $pay = false;
    $reset = false;
    $mrd = false;
    $c = false;



    $menupath = "./uploads/1.jpeg";
    $sql = "SELECT * FROM timetable ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $menupath = $row['path'];
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No menu is uploaded yet by the mess representative </div>";
    }
}
if (isset($_POST['sr'])) {
    $sql = "SELECT * FROM mrdetails ORDER BY id DESC LIMIT 1;";
    $res = $conn->query($sql);
    $sr = $_POST['sr'];
    $br = $_POST['br'];
    $lr = $_POST['lr'];
    $dr = $_POST['dr'];


    if ($res && $row = $res->fetch_assoc()) {
        $ID = $row['id'];
        $sql = "SELECT * FROM mratings where day = CURRENT_DATE() and id = '$uid'";
        $res1 = $conn->query($sql);
        if ($res1 && $row2 = $res1->fetch_assoc()) { // update
            echo "<script type='text/javascript'>alert('Already Given, updated your response');</script>";
            $sql = "Update mratings set breakfast = $br, lunch = $lr, dinner = $dr, snacks = $sr where id = $uid and day = CURRENT_DATE() and mid = $ID;";
            $conn->query($sql);
        } else { // add
            $sql = "INSERT INTO mratings VALUES ($uid,$ID,$br,$lr,$sr,$dr, CURRENT_DATE());";
            $conn->query($sql);
            echo "<script type='text/javascript'>alert('Rating Given');</script>";
        }
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No mess representative assigned, so rating is unsuccessful </div>";
    }
}

if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}
if (isset($_POST['mrd'])) {
    $rm = false;
    $mrd = true;
    $menu = false;
    $pay = false;
    $c = false;
    $reset = false;

    $cww = false;

    $n = "";
    $e = "";
    $p = 0;
    $r = 0;
    $sql = "SELECT * FROM mrdetails ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $n = $row['name'];
        $e = $row['email'];
        $p = $row['pno'];
        $r = $row['rating'];
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No Mess Representative assigned yet</div>";
    }
}

$cwwdata = "";
if (isset($_POST['cww'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = true;
    $pay = false;
    $c = false;
    $reset = false;


    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and users.usertype in ('w','cw')";
    $res = $conn->query($sql);
    $cwwdata = $res;
}
$status = "F";
if (isset($_POST['p'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $reset = false;
    $cww = false;
    $pay = true;

    $c = false;
    $sql = "SELECT * FROM payments where id = $uid";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $status = $row['status'];
    } else {
        $sql = "insert into payments values ($uid, 'F', './')";
        $res = $conn->query($sql);
        echo "<script type='text/javascript'>alert('Setting things!');</script>";
        header("refresh:5;url=s.php");
    }
}



if (isset($_POST['rating'])) {
    $rating = ($_POST['rating']);
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM ratings where id = '$id'";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) { // update
        $sql = "update ratings set rate = '$rating' where id = '$id'";
        $res2 = $conn->query($sql);
    } else {
        $sql = "insert into ratings values ('$id','$rating')";
        $res2 = $conn->query($sql);
    }
    echo "<script type='text/javascript'>alert('Rating Given');</script>";
}

if (isset($_POST['fileupload'])) {
    $file  = $_FILES['fileupload'];

    $filename = $_FILES['fileupload']['name'];
    echo  $filename;
    $filetmpname = $_FILES['fileupload']['tmp_name'];
    $filesize = $_FILES['fileupload']['size'];
    $fileerror = $_FILES['fileupload']['error'];
    $filetype = $_FILES['fileupload']['type'];

    $fileext = explode('.', $filename);
    $fileaext = strtolower(end($fileext));

    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($fileaext, $allowed)) {
        if ($fileerror == 0) {
            if ($filesize < 5000000) {
                $filenewname  = uniqid('', true) . '.' . $fileaext;
                $filedestination = './uploads/payments/' . $filenewname;
                move_uploaded_file($filetmpname, $filedestination);
                $sql = "update payments set path = '$filedestination', status = 'M' where id = $uid";
                $conn->query($sql);
                echo "<script type='text/javascript'>alert('Success!');</script>";
                header("refresh:5;url=s.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=s.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=s.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=s.php");
    }
}



if (isset($_POST['comp'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $pay = false;
    $c = true;
    $reset = false;
}

if (isset($_POST['addeating'])) {
    header("Location: eat.php");
}

if (isset($_POST['resetPass'])) {
    $newpass  = $_POST['resetPass'];
    $sql = "update users set password = '$newpass' where id = $uid;";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Updated!');</script>";
    header("refresh:5;url=s.php");
}
$resetPos = "";
$resetReg = "";
$resetRoll = "";
$resetemail = "";
if (isset($_POST['reset'])) {

    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $pay = false;
    $c = false;
    $reset = true;
    $sql = "select * from users, payments where users.id = payments.id and users.id = $uid;";
    $res = $conn->query($sql);
    if(!($res))
    {
        $sql = "select * from users where users.id = $uid;";
        $res = $conn->query($sql);  
    }
    $row = $res->fetch_assoc();
    $resetPos = "Student";
    $resetReg = $row['reg'];
    $resetRoll = $row['roll'];
    $resetemail = $row['email'];
    if ($row['usertype'] == 'w')
        $resetDesig = "Warden";
    else {
        $resetDesig = "Chief Warden";
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset=utf-8" />
    <title>NIT Andhra Pradesh MMS</title>
    <link rel="stylesheet" href="./css/s.css">
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script src="./js/student/s.js"></script>
</head>

<body style=" overflow:scroll;">
    <div class="alert alert-success" role="alert">
        Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="s.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>


        <form action="s.php" method="post">
            <input type="hidden" value="sdfd" name="addeating">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add today's status</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="menu">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Show Menu</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="rm">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Rate Today's Meal</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="mrd">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mess Representative Details</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="cww">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Chief Warden and Warden</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="p">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Payment Details</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="c" name="comp">
            <button type="submit" class="btn btn-primary" style="width: 100%;">File Complaint</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="reset">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>
    </div>

    <div id="content">
        <div class="container">
            <?php if ($menu) : ?>
                <h2>NIT AP Mess Menu</h2>
                <img src="<?php echo $menupath; ?>" class="rounded img-fluid" style="width: 100%;" alt="No menu yet!" />
            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($c) : ?>
                <h2 style="text-align: center;">File a complaint</h2>
                <div class="card text-center">
                    <div class="card-header">
                        File a complaint through CTS
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Mobile App</h5>
                        <p class="card-text">Please click the below link and download the apk file and file your complaint</p>
                        <a href="https://drive.google.com/file/d/1nvscqageL6RFWyvb_lyxioBgYIqvXCHQ/view" class="btn btn-primary">Download</a>
                    </div>
                    <div class="card-footer text-muted">
                        50MB
                    </div>
                </div>

            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($mrd) : ?>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Mess Representative Details
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Name: <?php echo $n; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Email: <?php echo $e; ?></a>
                    <a href="#" class="list-group-item list-group-item-action">Phone Number: <?php echo $p; ?></a>
                    <a href="#" class="list-group-item list-group-item-action"> Average Rating: <?php echo $r; ?></a>
                    <form method="POST" action="s.php">
                        <div class="form-group">
                            <label for="rating">Give your rating</label>
                            <input min="1" max="5" value="1" required type="number" class="form-control" name="rating" id="rating" aria-describedby="ratingHelp">
                            <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="container" style="overflow: scroll; max-height: 500px;">

            <?php if ($cww) : ?>
                <div class="list-group">

                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Chief Warden and Warden Details
                    </a>
                    <?php
                    while ($row = $cwwdata->fetch_array()) :
                    ?>
                        <br>
                        <a href="#" class="list-group-item list-group-item-action"><?php if ($row['usertype'] == "w") {
                                                                                        echo "<h4>Warden</h4>";
                                                                                    } else {
                                                                                        echo "<h4>Chief Warden</h4>";
                                                                                    } ?></a>
                        <a href="#" class="list-group-item list-group-item-action">Name: <?php echo $row['name']; ?> </a>
                        <a href="#" class="list-group-item list-group-item-action">Email: <?php echo $row['email']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action">Phone Number: <?php echo $row['pno']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action"> Department: <?php echo $row['department']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action"> Hall: <?php echo $row['hall']; ?></a>
                        <br>


                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="container">

            <?php if ($rm) : ?>

                <?php echo "<div class='alert alert-primary' role='alert'> Date: $Date <br> Day: $Day</div>"; ?>
                <form method="POST" action="s.php">
                    <div class="form-group">
                        <label for="rating">Breakfast:</label>
                        <input min="1" max="5" value="1" required type="number" class="form-control" name="br" id="br" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Lunch:</label>
                        <input min="1" max="5" value="1" required type="number" class="form-control" name="lr" id="lr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Snacks:</label>
                        <input min="1" max="5" value="1" required type="number" class="form-control" name="sr" id="sr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Dinner</label>
                        <input min="1" max="5" value="1" required type="number" class="form-control" name="dr" id="dr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form><?php endif; ?>
        </div>

        <div class="container">
            <?php if ($reset) : ?>
                <h3 style=" text-align:center;">Reset your password</h3>
                <p>Other details are handled by Admin, you can contact for any problems.</p>
                <form action="s.php" method="post">
                    <label for="exampleInputEmail1">Email: <?php echo $resetemail; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Roll Number: <?php echo $resetRoll; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Registration Number: <?php echo $resetReg; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Position: <?php echo $resetPos; ?></label>
                    <br>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input required type="password" class="form-control" name="resetPass" id="resetPass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($pay) : ?>
                <?php if ($status == 'T') : ?>
                    <div class="alert alert-success" role="alert">
                    <?php endif; ?>
                    <?php if ($status == 'F') : ?>
                        <div class="alert alert-danger" role="alert">
                        <?php endif; ?>
                        <h4 class="alert-heading" style="text-align: center">Mess Payment Status</h4>
                        <?php if ($status == 'F') : ?>
                            <p>Your mess payment is pending please upload your SBI Challan below and sit back.</p>
                        <?php endif; ?>
                        <?php if ($status == 'M') : ?>
                            <div class="alert alert-primary" role="alert">
                                <p>Your challan is being processed. If this turn red again, reupload right documents any problems then please contact warden which you can find in the Chief Warden and Warden Sections</p>
                            <?php endif; ?>
                            <hr>
                            <?php if ($status == 'F') : ?>
                                <p class="mb-0">Payment is Pending</p>
                            <?php endif; ?>
                            <?php if ($status == 'T') : ?>
                                <p class="mb-0">Payment is done and verified <br> Contact your care tacker in the hostel to get your mess id card</p>
                            <?php endif; ?>
                            <?php if ($status == 'M') : ?>
                                <p class="mb-0">Payment is being reviewed</p>
                            <?php endif; ?>
                            </div>
                            <?php if ($status == 'F') : ?>

                                <form method="POST" action="s.php" enctype="multipart/form-data">
                                    <input type="file" class="custom-file-input" id="fileupload" name="fileupload">
                                    <label class="custom-file-label" for="customFile">Choose payment file</label>

                                    <button type="submit" name="fileupload" class="btn btn-primary">Upload</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>




                    </div>
        </div>
    </div>



</body>

</html>