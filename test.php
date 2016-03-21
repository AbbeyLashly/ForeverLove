<!DOCTYPE html>

<head>

    <?php
    require_once 'core/init.php';
    include("includes/metatags.html");
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/UserServiceMgr.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/ReturnShortcuts.php');

    //$uid = $_SESSION['user_id'];
    $uid = 5;

    $regOrUpdate = UserServiceMgr::determineUpdateOrReg($uid);

    $dbvalue=array();

    if($regOrUpdate == "Update"){
        $dbvalue = ReturnShortcuts::returnPreferences($uid);
    }

    ?>

    <title><?php echo $regOrUpdate?> Preferences</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom-base-page.css" rel="stylesheet">
    <link href="css/custom-form-page.css" rel="stylesheet">
    <?php include("includes/fonts.html");?>

    <?php
    if(!empty($_POST)){
        $success = UserServiceMgr::updateUserPreferences($uid, $_POST, $regOrUpdate);
    }
    ?>


</head>

<body class="full">
<?php include("includes/navbar.html"); ?>

<!--Main page content-->

<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12 text-center">
                <br><br>
                <h2>
                    <small>
                        <strong><?php echo $regOrUpdate?> Prefrences</strong>
                    </small>
                </h2>
                <hr class="tagline-divider">
                <p>
                    <br>

                <br><br>
                <br><br>
                </p>
            </div>
        </div>
    </div>

</div>
<?php include("includes/footer.html"); ?>
</body>

</html>