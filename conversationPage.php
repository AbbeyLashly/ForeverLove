<!DOCTYPE html>
<html>

<head>

    <?php include("includes/metatags.html");
    require_once 'core/init.php'; ?>
    <title>Conversation</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom-base-page.css" rel="stylesheet">
    <?php include("includes/fonts.html");
        include("Classes/DB.php");
        include("Classes/Config.php");
        include("Classes/MessageMgr.php");
        include("Classes/BrowserHelper.php")?>

</head>


<body class="full">


<!--Main page content-->

<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12 text-center">
                <br><br>
                <h2>
                    <small>
                        <strong>
                            <?php
                                if (!isset($_POST['convoID']))
                                    $convoID = $_SERVER['QUERY_STRING'];
                                else
                                    $convoID = ($_POST["convoID"]);

                                if($_SESSION["permissions"] == "admin" && isset($_POST['permission']))
                                {
                                    $users = DB::getInstance()->query("SELECT user1_id, user2_id FROM conversations WHERE conversation_id = '$convoID'")->results();
                                    $uid1 = $users[0]->user1_id;
                                    $uid2 = $users[0]->user2_id;
                                    $MsgMgr = new MessageMgr($uid1);
                                    $name1 = $MsgMgr->getUsername();
                                    $name2 = $MsgMgr->getUsername($uid2);
                                    echo "Conversation Between $name1 And $name2";
                                }
                                else if($_SESSION["permissions"] != "admin")
                                {
                                    $uid = $_SESSION['user_id'];
                                    $MsgMgr = new MessageMgr($uid);
                                    if (isset($_POST['reveal']))
                                        $MsgMgr->revealButton($convoID);
                                    if (!empty($convoID))
                                    {
                                        if ($MsgMgr->isUserInConversation($convoID) == true)
                                        {
                                            $user2_id = $MsgMgr->getConversationPartner($convoID);
                                            $vis = $MsgMgr->isProfileVisible($convoID);
                                            if (!$vis)
                                                $name = "Blind Date";
                                            else
                                                $name = $MsgMgr->getUsername($user2_id);
                                            echo $name;
                                        }
                                        else
                                            echo "Nobody.";
                                    }
                                    else
                                        echo "Invalid Conversation";
                                }
                                else
                                    echo "You Do Not Have Permission To View This Conversation"
                            ?>
                        </strong>
                    </small>
                </h2>
                <hr class="tagline-divider">
                <p>
                    <br><br>
                    <?php
                        //if more not pressed
                        //if more is pressed then the number is passed back
                        if (!isset($_POST['convoID'])) //get conversation id
                            $convoID = $_SERVER['QUERY_STRING'];
                        else
                            $convoID = ($_POST["convoID"]);
                        if(isset($_POST['permission']))
                            $perm = 1;
                        else
                            $perm = 0;

                        if($_SESSION["permissions"] == "admin" && $perm != 1)
                            echo "<div class=\"alert alert-danger\">
                                       To View This Conversation, You Must Follow The Appropriate Link In A Report
                                  </div>";
                        if(isset($_POST["num"]))
                            $num = $_POST["num"] + 50;
                        else
                            $num = 50; //num is number of messages to be loaded
                        $messageCount = MessageMgr::messageCount($convoID);
                        if($messageCount > $num && $_SESSION["permissions"] != "admin")
                            echo "<form name =\"f\"  action=\"conversationPage.php?$convoID\" method=\"post\">
                                       <input type = \"hidden\" name = \"num\" value = $num>
                                       <a href=\"#\"  role = \"button\" class=\"btn btn-info\" onclick=\"document.forms['f'].submit();\">Load More Messages</a>
                                   </form><br>";
                        else if($messageCount > $num && ($_SESSION["permissions"] == "admin" && $perm == 1 || $_SESSION["permissions"] == "user"))
                        {
                            $report_id = $_POST["report_id"];
                            echo "<form name =\"form\"  action=\"conversationPage.php?$convoID\" method=\"post\">
                                           <input type = \"hidden\" name = \"num\" value = $num>
                                           <input type = \"hidden\" name = \"permission\" value = $perm>
                                           <input type = \"hidden\" name = \"report_id\" value = $report_id>
                                           <a href=\"#\"  role = \"button\" class=\"btn btn-info\" onclick=\"document.forms['form'].submit();\">Load More Messages</a>
                                       </form><br>";
                        }
                        if($_SESSION["permissions"] == "admin" && $perm == 1)
                            MessageMgr::loadConversationAdmin($convoID, $num);
                        else if($_SESSION["permissions"] != "admin")
                        {
                            $uid = $_SESSION['user_id'];
                            $MsgMgr = new MessageMgr($uid);
                            if (isset($_POST['convoID']))
                                if ($_POST['message'] != "")
                                    $MsgMgr->sendMessage($_POST["message"], $convoID);
                            if ($MsgMgr->conversationExists($convoID))
                                $MsgMgr->conversationLoader($convoID, $num);
                            else
                                echo "<div class=\"alert alert-danger\">
                                       This Conversation Does Not Exist.
                                      </div>";
                        }
                    ?>
                    <a name="bottom"></a>
                </p>
                <?php
                    if($_SESSION["permissions"] != "admin")
                    {
                        include("includes/navbar.php");
                        echo "<div style = \"text-align: left\">
                                <a href=\"existingConversationPage.php\" class=\"btn btn-info\" role=\"button\"><span class=\"glyphicon glyphicon-chevron-left\"></span> Back To Conversation List</a>
                            </div>";
                    }
                    else
                    {
                        if(isset($_POST["report_id"]))
                        {
                            $rid = $_POST["report_id"];
                            echo "<div style = \"text-align: left\">
                                    <a href=\"secret_location/reportPage.php?report_id=$rid\" class=\"btn btn-info\" role=\"button\"><span class=\"glyphicon glyphicon-chevron-left\"></span>Back to Report</a>
                                </div>";
                        }
                        include("includes/navbarAdmin.html");
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.html"); ?>
</body>
</html>