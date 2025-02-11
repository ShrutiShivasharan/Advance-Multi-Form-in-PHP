<?php
include('./connection.php');
session_start();

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1 ;

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if($step === 1){
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        //check if email exist or not
        $check_sql = "SELECT * FROM user WHERE email='$email'";
        $result = $con -> query($check_sql);
        if($result->num_rows > 0){
            //fetch user details
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['address'] = $user['address'];
            $stored_password = $user['password']; //hash password

            if(password_verify($password,$stored_password)){
                if(empty($user['name']) || empty($user['phone'])){
                    $step = 2;
                    echo json_encode("success");
                }else{
                    //direct display step 3 
                    header('Content-Type: application/json');
                    $response = array(
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "phone" => $user['phone'],
                        "address" => $user['address'],
                    );
                    echo json_encode($response); //sending as json
                }
            }else{
                echo json_encode("Incorrect_Password");
            }
        }else{
            //new user add
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql  = "INSERT INTO user (email, password) VALUES ('$email', '$hashPassword')";
            if($con->query($insert_sql) === TRUE){
                $_SESSION['user_id'] = $con-> insert_id;
                $_SESSION['email'] = $email;
                $step = 2;
                echo json_encode("success");
                // echo json_encode(["status" => "success", "step" => 2]);
            }else{
                echo "error".$con->error;
            }
        } 
    }elseif($step === 2){
        $user_id = $_SESSION['user_id'];
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        $update_sql = "UPDATE user SET name='$name', phone='$phone', address='$address' WHERE id='$user_id'";
        if($con->query($update_sql) === TRUE){
            $_SESSION['name'] = $name;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;
            // echo json_encode("success");
            //direct display step 3 
            header('Content-Type: application/json');
            $response = array(
                "name" => $_SESSION['name'],
                "email" => $_SESSION['email'],
                "phone" =>  $_SESSION['phone'],
                "address" => $_SESSION['address'],
            );
            $step = 3;
            echo json_encode($response); //sending as json
        }else{
            echo "error".$con->error;
        }
    }
}

?>