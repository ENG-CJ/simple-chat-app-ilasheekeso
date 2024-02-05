<?php

include_once "../config/conn.php";

class Users extends Connection
{

    public static function createUser()
    {
        $conn = Users::connect("root", "", "chats");
        extract($_POST);
        $res = array();

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

    public static function readUsers()
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
                    $data =array();
                    while ($rows=$result->fetch_assoc()) 
                        $data[]=$rows;
                    $res = array("status" => true, "found" => true,"data"=>$data);
                } else
                    $res = array("status" => true, "found" => false);
            } else {
                $res = array("status" => false, "error" => "there is an error connection");
            }
        }

        echo json_encode($res);
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
                    $data =array();
                    while ($rows=$result->fetch_assoc()) 
                        $data[]=$rows;
                    $res = array("status" => true, "found" => true,"data"=>$data);
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
