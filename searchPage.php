<!DOCTYPE html>
<html>

<head>
    <?php
    require_once 'core/init.php';
    include("includes/metatags.html");
    include("includes/fonts.html");

    $me = $_SESSION['user_id'];

    $db = DB::getInstance();
    $hobbies = $db->query('SELECT * FROM user_hobbies ORDER BY hobby_name')->results();
    $preferences = SearchServiceMgr::searchablePreferences();

    if(isset($_POST['submit'])){
        $selectedPreferences = [];
        foreach($preferences as $preference => $options){
            $setPreference = strtolower(str_replace(' ', '_',$preference));
            if(isset($_POST[$setPreference]) && $_POST[$setPreference] != 1){
                $selectedPreferences[$setPreference] = $_POST[$setPreference];
            }
        }
        if(isset($_POST['list'])){
            $results = SearchServiceMgr::byCriteria($me, $_POST['list'], $selectedPreferences);
        }
        else if(count($selectedPreferences)){
            $results = SearchServiceMgr::byCriteria($me, [], $selectedPreferences);
        }
        else{
            $results = SearchServiceMgr::suggestions($me);
        }
        if(isset($_POST['age'])){
            $results = SearchServiceMgr::filterAge($_POST['age'], $results);
        }
    }
    ?>
    <title>Search Page</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom-base-page.css" rel="stylesheet">
</head>

<body class="full">
<?php include("includes/navbar.php"); ?>

<!--Main page content-->

<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12 text-center">
                <br><br>
                <hr class="tagline-divider">
                <h2 class="intro-text text-center">
                    <strong>Search By Criteria</strong>
                </h2>
                <hr class="tagline-divider"><br>
                <form class="form-horizontal" role="form" method="post">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-3">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        Hobbies
                                    </div>
                                    <div class="panel-body">
                                        <div style="height: 350px;overflow: auto;">
                                            <?php
                                            foreach($hobbies as $hobby){?>
                                                <div class="pull-left" style="clear:both;">
                                                    <label class="text-muted">
                                                        <input type="checkbox" name="list[]" value="<?php echo $hobby->hobby_id;?>"><?php echo $hobby->hobby_name;?>
                                                    </label>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6 col-sm-6">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        Results
                                    </div>
                                    <div class="panel-body">
                                        <div style="height: 350px;overflow: auto;">
                                            <?php
                                            if(isset($results)){
                                                if(($n = count($results)) > 10){$n = 10;}
                                                for($i = 0; $i < $n; $i++){
                                                    ?>
                                                    <a href="profilePage.php?uid=<?php echo $results[$i]->user_id;?>">
                                                        <div class="display_box">
                                                            <div class="row">
                                                                <div class="col-xs-12">
                                                                    <div class="media">
                                                                        <div class="media-left">
                                                                            <?php $result_uid = $results[$i]->user_id; ?>
                                                                            <img  height="78" width="78" class="media-object" title="Profile Image" src="<?php echo $db->query("SELECT image_path FROM images WHERE user_id = '$result_uid' && image_id = '1'")->results()[0]->image_path;?>"/>
                                                                        </div>
                                                                        <div class="media-body" style="padding-top: 3px;">
                                                                            <h4 class="media-heading" title="Username"><?php echo $results[$i]->username; ?></h4>
                                                                            <small style="white-space: nowrap;" title="Tag Line"><?php echo $results[$i]->tag_line; ?></small>
                                                                        </div>
                                                                        <div class="media-right media-middle">
                                                                            <h5 class="media-heading" title="Location"><?php echo $results[$i]->city; ?></h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <?php
                                                }
                                            }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-3">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        Preferences
                                    </div>
                                    <div class="panel-body">
                                        <div style="height: 350px;overflow: auto;">
                                            <select class="form-control" name="age">
                                                <option disabled selected>Age Range</option>
                                                <option value="18">18 - 24</option>
                                                <option value="25">25 - 34</option>
                                                <option value="35">35 - 44</option>
                                                <option value="45">45 - 54</option>
                                                <option value="55">55 or Older</option>
                                            </select>
                                            <?php
                                            foreach($preferences as $preference => $options){?>
                                                <select class="form-control" name="<?php echo strtolower(str_replace(' ', '_',$preference));?>">
                                                    <option disabled selected><?php echo $preference;?></option>
                                                    <?php
                                                    foreach($options as $option => $value){?>
                                                        <option value="<?php echo $option;?>"><?php echo $value;?></option>;
                                                        <?php
                                                    }?>
                                                </select>
                                                <?php
                                            }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <input class="btn btn-info" id="search_button" name="submit" type="submit" value="Search">
                    </div>
                </form>
                <br><br><br>
                <br><br>
            </div>
        </div>
    </div>

</div>
<?php include("includes/footer.html"); ?>
</body>

</html>