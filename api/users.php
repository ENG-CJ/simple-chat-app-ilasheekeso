<?php

include_once "../config/conn.php";

class Users extends Connection
{

    public static function createUser()
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();

        if ($hasProfile == "false") {
            $sql = "INSERT INTO users(username,email,password) VALUES('$username','$email','$password')";
            if (!$conn)
                $res = array("status" => false, "error" => "there is an error connection");
            else {
                $result = $conn->query($sql);
                if ($result) {
                    $res = array("status" => true, "message" => "Created");
                } else {
                    $res = array("status" => false, "error" => "there is an error connection");
                }
            }
            echo json_encode($res);
        } else {
            $filename = $_FILES['image']['name'];
            $extension = explode(".", $filename)[1];
            $temp = $_FILES['image']['tmp_name'];

            $realProfileName = rand() . "." . $extension;
            $actualPathUploaded = "../images/" . $realProfileName;

            $isUploaded = Users::uploadMyProfile($temp, $actualPathUploaded);
            $user = new Users;
            if ($isUploaded)
                $user->createUserWithProfile($realProfileName);
            else
                $user->createUserWithProfile();
        }
    }

    public static function createUserWithProfile($profileName = 'no_profile')
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();
        $sql = "INSERT INTO users(username,email,password,profile) VALUES('$username','$email','$password','$profileName')";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                $res = array("status" => true, "message" => "Created");
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }
        echo json_encode($res);
    }
    private static function uploadMyProfile($temp, $actualPath): bool
    {
        $isUploaded = false;
        if (move_uploaded_file($temp, $actualPath))
            $isUploaded = true;

        return $isUploaded;
    }
    public static function createChat()
    {
        $conn = Users::connect("root", "", "chats");
        session_start();
        extract($_POST);
        $fromUser = $_SESSION['id'];
        $res = array();

        $sql = "INSERT INTO messages(from_user,to_user,message) VALUES('$fromUser','$to_user','$message')";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                $res = array("status" => true, "message" => "Created");
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        echo json_encode($res);
    }
    public static function login()
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();

        $sql = "SELECT *FROM users where username='$username' and password='$password'";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    session_start();
                    $row = $result->fetch_assoc();
                    $_SESSION["id"] = $row['id'];
                    $res = array("status" => true, "found" => true);
                } else
                    $res = array("status" => true, "found" => false);
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        echo json_encode($res);
    }

    public static function fetchMessages()
    {
        $conn = Users::connect("root", "", "chats");
        session_start();
        extract($_POST);
        $fromUser = $_SESSION['id'];
        $sql = "SELECT *FROM messages
                WHERE ((messages.from_user='$fromUser' AND messages.to_user='$toUser')
                OR (messages.from_user='$toUser' AND messages.to_user='$fromUser'));;";
        $result = $conn->query($sql);
        $output = "";
        if ($result) {
            $count = mysqli_num_rows($result);
            if ($count == 0) {
?>
                <div class="error-provider">
                    <div class="error-message">
                        <strong>Messages Will Appear Here!</strong>
                    </div>
                </div>

            <?php
                return;
            }

            ?>

            <div class="position-relative">
                <div class="chat-messages p-4">
                    <?php
                    while ($rows = $result->fetch_assoc()) {

                        if ($rows['from_user'] == $_SESSION['id']) {
                            $profile_pic = Users::getProfile($rows['from_user']);
                    ?>
                            <div class="chat-message-right pb-4">
                                <div>
                                    <img src="<?php echo $profile_pic?>" class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40" />
                                    <!-- <div class="text-muted small text-nowrap mt-2">You</div> -->
                                </div>
                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                    <div class="font-weight-bold mb-1">You</div>
                                    <?php echo $rows['message'] ?>
                                </div>
                            </div>
                        <?php
                        } else {
                            $name = Users::getUsername($rows['from_user']);
                            $profile_pic = Users::getProfile($rows['from_user']);
                        ?>
                            <div class="chat-message-left pb-4">
                                <div>
                                    <img src="<?php echo $profile_pic?>" class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40" />
                                </div>
                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                                    <div class="font-weight-bold mb-1"><?php echo $name ?></div>
                                    <?php echo $rows['message'] ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>

                </div>
            </div>
<?php

        }
    }
    public static function readUsers()
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();
        session_start();
        $id = $_SESSION['id'];

        $sql = "SELECT *FROM users where id!='$id'";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $data = array();
                    while ($rows = $result->fetch_assoc())
                        $data[] = $rows;
                    $res = array("status" => true, "found" => true, "data" => $data);
                } else
                    $res = array("status" => true, "found" => false);
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        echo json_encode($res);
    }
    private static function getUsername($id): string
    {
        $conn = Users::connect("root", "", "chats");
        $name = '';

        $sql = "SELECT *FROM users where id='$id'";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                $row = $result->fetch_assoc();
                $name = $row['username'];
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        return $name;
    }
    private static function getProfile($id): string
    {
        $conn = Users::connect("root", "", "chats");
        $profile = '../images/user.png';

        $sql = "SELECT profile FROM users where id='$id'";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row['profile'] != "no_profile")
                    $profile = "../images/" . $row['profile'];
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        return $profile;
    }
    public static function readMessages()
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();

        $sql = "SELECT *FROM users";
        if (!$conn)
            $res = array("status" => false, "error" => "there is an error connection");
        else {
            $result = $conn->query($sql);
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $data = array();
                    while ($rows = $result->fetch_assoc())
                        $data[] = $rows;
                    $res = array("status" => true, "found" => true, "data" => $data);
                } else
                    $res = array("status" => true, "found" => false);
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        echo json_encode($res);
    }
}


$action  = $_POST['action'];
if (isset($action)) {
    Users::$action();
}
