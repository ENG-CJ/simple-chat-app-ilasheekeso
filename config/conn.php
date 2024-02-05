

<?php 


class Connection{

    public static function connect($user, $password,$db, $host='localhost') : mysqli | bool{
        $conn = new mysqli($host, $user, $password,$db);
        if($conn->connect_error)
             return false;

        return $conn;
    }


}


?>