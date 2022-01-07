<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 'w') {
    if ($_SESSION['usertype'] == 'cw'){}
    else {
    header("Location: login.php"); }
}
$Date = date("Y/m/d");
$Day = date("l");
$wid = $_SESSION['id'];
if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}
$stats = false;
$reset = false;
$payments = true;
$assignNew = false;
$mrd = false;
$paymentsData = "";
$sql = "SELECT * FROM payments";
$res = $conn->query($sql);
$paymentsData = $res;
if (isset($_POST['pay'])) {
    $payments = true;
    $assignNew = false;
    $reset = false;

    $mrd = false;
    $stats = false;
    $sql = "SELECT * FROM payments";
    $res = $conn->query($sql);
    $paymentsData = $res;
}
if (isset($_POST['mrd'])) {
    $mrd = true;
    $assignNew = false;
    $reset = false;

    $payments = false;
    $stats  = false;
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

if (isset($_POST['pending'])) {
    $payments = true;
    $reset = false;

    $stats = false;
    $mrd = false;

    $assignNew = false;

    $sql = "SELECT * FROM payments where status in ('M', 'F');";
    $res = $conn->query($sql);
    $paymentsData = $res;
}

if (isset($_POST['approved'])) {
    $payments = true;
    $mrd = false;
    $reset = false;

    $stats = false;
    $assignNew = false;

    $sql = "SELECT * FROM payments where status = 'T';";
    $res = $conn->query($sql);
    $paymentsData = $res;
}

if (isset($_POST['approve'])) {
    $key = $_POST['approve'];
    $sql = "update payments set status = 'T' where id = $key";
    $conn->query($sql);
    header("Location: w.php");
}


if (isset($_POST['reject'])) {
    $key = $_POST['reject'];
    $sql = "update payments set status = 'F' where id = $key";
    $conn->query($sql);
    header("Location: w.php");
}

$sql = "select count(*) from payments;";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$totalNumberOfPayments = $row['count(*)'];
$sql = "select count(*) from payments where status = 'T';";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$NumberOfTPayments = $row['count(*)'];
$sql = "select count(*) from payments where status in ('F', 'M');";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$NumberOfFPayments = $row['count(*)'];
$totalNumberOfPayments = $row['count(*)'];
if (isset($_POST['stats'])) {
    $stats = true;
    $payments = false;
    $assignNew = false;
    $reset = false;

    $mrd = false;
    $sql = "select count(*) from payments;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $totalNumberOfPayments = $row['count(*)'];
    $sql = "select count(*) from payments where status = 'T';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $NumberOfTPayments = $row['count(*)'];
    $sql = "select count(*) from payments where status in ('F', 'M');";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $NumberOfFPayments = $row['count(*)'];
}
$b = 0;
$d = 0;
$l = 0;
$preferedDay  = "Choose a date below";
$s = 0;
if (isset($_POST['messFood'])) {
    $preferedDay = $_POST['messFood'];
    $sql = "select avg(breakfast) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $b = $row['avg(breakfast)'];
    $sql = "select avg(lunch) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $l = $row['avg(lunch)'];
    $sql = "select avg(dinner) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $d = $row['avg(dinner)'];
    $sql = "select avg(snacks) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $s = $row['avg(snacks)'];
    $payments  = false;
    $stats = true;
    $mrd = false;
    $assignNew = false;
    $reset = false;
}

if (isset($_POST['assignNew'])) {
    $payments  = false;
    $stats = false;
    $mrd = false;
    $assignNew = true;
    $reset = false;
}
if (isset($_POST['resetPass'])) {
    $newpass  = $_POST['resetPass'];
    $sql = "update users set password = '$newpass' where id = $wid;";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Updated!');</script>";
    header("refresh:5;url=w.php");
}
$resetDepartment = "";
$resetHall = "";
$resetName = "";
$resetpno = "";
$resetemail = "";
$resetDesig = "";
if (isset($_POST['reset'])) {

    $payments  = false;
    $stats = false;
    $mrd = false;
    $assignNew = false;
    $reset = true;
    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and users.id = $wid;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $resetDepartment = $row['department'];
    $resetHall = $row['hall'];
    $resetName = $row['name'];
    $resetpno = $row['pno'];
    $resetemail = $row['email'];
    if ($row['usertype'] == 'w')
        $resetDesig = "Warden";
    else {
        $resetDesig = "Chief Warden";
    }
}

if (isset($_POST['assignNewMR'])) {
    $email1 = $_POST['assignNewMR'];
    $email2 = $_POST['email2'];
    $mn = $_POST['mn'];
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $sql = "insert into users (email, password, usertype) values ('$email1', '$pass', 'ma');";
    $res = $conn->query($sql);
    if (!$res) {
        echo "<script type='text/javascript'>alert('Try unique values!');</script>";
        header("refresh:5;url=w.php");
    }
    $sql = "select * from users where email = '$email1' and password = '$pass';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $mid = $row['id'];
    $sql = "insert into mrdetails values ('$mid', '$name', '$email2', '$mn', 5);";
    $conn->query($sql);
    echo "<div class='alert alert-success' role='alert'>Assigned!</div>";

    $payments  = false;
    $stats = false;
    $reset = false;

    $mrd = false;
    $assignNew = true;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset=utf-8" />
    <title>NIT Andhra Pradesh MMS</title>
    <link rel="stylesheet" href="./css/s.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">

    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>


    <script src="./js/student/s.js"></script>
</head>

<body style=" overflow:scroll;">
    <div class="alert alert-success" role="alert">
        Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="w.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="pay">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Review Payment Details</button>
        </form>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="stats">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Show Stats</button>
        </form>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="mrd">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mess Representative Details</button>
        </form>
        <br>
        <form action="w.php" method="post">
            <input type="hidden" value="m" name="assignNew">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Assign New Mess Representative</button>
        </form>
        <br>
        <form action="w.php" method="post">
            <input type="hidden" value="m" name="reset">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>
    </div>

    <div id="content">
        <div class="container" style="max-height: 500px;  overflow:scroll;">
            <?php if ($payments) : ?>

                <h2 style=" text-align: center;">Approval Dashboard</h2>
                <h5 style=" text-align: center;">Please look through the requests to verify the mess payments</h5>
                <div class="container text-align-center" style="text-align:center;">

                    <form action="w.php" method="post" style="float:left;">
                        <input type="hidden" value="m" name="pending">
                        <button type="submit" class="btn btn-primary" style="width: 100%;"> Show Pending </button>
                    </form>

                    <form action="w.php" method="post" style="float:right;">
                        <input type="hidden" value="m" name="approved">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Show Approved</button>
                    </form>
                    <br>
                    <br>
                    <div class="list-group w-100 align-items-center" style="max-width: 300px; margin: 0 auto;">

                        <?php
                        while ($row = $paymentsData->fetch_array()) :
                        ?>

                            <br>

                            <a href="#" class="list-group-item list-group-item-primary"> Roll Number: <?php echo $row['roll']; ?></a>
                            <a href="#" class="list-group-item list-group-item-primary"> Registration Number: <?php echo $row['reg']; ?></a>
                            <a href="#" class="list-group-item list-group-item-primary"> status: <?php if ($row['status'] == "F") {
                                                                                                        echo "Pending";
                                                                                                    }
                                                                                                    if ($row['status'] == "T") {
                                                                                                        echo "Completed";
                                                                                                    }
                                                                                                    if ($row['status'] == "M") {
                                                                                                        echo "To be reviewed";
                                                                                                    } ?></a>
                            <a href="<?php echo $row['path']; ?>" target="_blank" class="list-group-item list-group-item-info"> Open Payment Proof</a>
                            <br>
                            <form action="w.php" method="post">
                                <input type="hidden" value="<?php echo $row['id']; ?>" name="reject">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Reject</button>
                            </form>
                            <form action="w.php" method="post">
                                <input type="hidden" value="<?php echo $row['id']; ?>" name="approve">
                                <button type="submit" class="btn btn-success" style="width: 100%;">Approve</button>
                            </form>

                            <br>
                            <br>


                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="container">
            <?php if ($stats) : ?>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Student Mess Fee Payment Stats
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Total: <?php echo $totalNumberOfPayments; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Pending: <?php echo $NumberOfFPayments; ?></a>
                    <a href="#" class="list-group-item list-group-item-action">Approved: <?php echo $NumberOfTPayments; ?></a>
                </div>

                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Mess Food Ratings on <?php echo $preferedDay; ?>
                    </a>

                    <form action="w.php" method="post">
                        <input type="date" value="m" name="messFood" required>
                        <button type="submit" class="btn btn-warning">Plot Ratings</button>
                    </form>

                    <canvas id="myChart" width="200" height="200"></canvas>
                    <script>
                        var ctx = document.getElementById('myChart').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Breakfast', 'Lunch', 'Dinner', 'Snacks', 'reference'],
                                datasets: [{
                                    label: 'Rating',
                                    data: <?php echo "[" . $b . ", " . $l . ", " . $d . ", " . $s . ", 5" . "]" ?>,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    </script>
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
                </div>
            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($assignNew) : ?>
                <h3 style=" text-align:center;">Assign a new Mess Representative</h3>
                <p> As you assign a new mess representative you automatically remove the present one from the assignment</p>
                <form action="w.php" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="email" class="form-control" name="assignNewMR" id="assignNewMR" aria-describedby="emailHelp" placeholder="Enter Login Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address 2</label>
                        <input required type="email" class="form-control" name="email2" id="email2" aria-describedby="emailHelp" placeholder="Enter Contact Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" class="form-control" name="name" id="name" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" placeholder="Enter Mobile Number">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="container">
            <?php if ($reset) : ?>
                <h3 style=" text-align:center;">Reset your password</h3>
                <p>Other details are handled by Admin, you can contact for any problems.</p>
                <form action="w.php" method="post">
                    <label for="exampleInputEmail1">Name: <?php echo $resetName; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Email: <?php echo $resetemail; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Mobile Number: <?php echo $resetpno; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Department: <?php echo $resetDepartment; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Hall: <?php echo $resetHall; ?></label>
                    <br>

                    <label for="exampleInputEmail1">Post: <?php echo $resetDesig; ?></label>
                    <br>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input required type="password" class="form-control" name="resetPass" id="resetPass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php endif; ?>
        </div>


    </div>

    </div>




</body>

</html>