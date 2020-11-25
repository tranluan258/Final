<?php
    class AccountController extends BaseController{

        public function login(){
            $remember = null;
            $error = '';
            $user = '';
            $pass = '';

            if(isset($_COOKIE['username'])){
                $user = $_COOKIE['username'];
            }

            if (isset($_POST['username']) && isset($_POST['password'])) {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                if (empty($user)) {
                    $error = 'Vui lòng nhập username';
                }
                else if (empty($pass)) {
                    $error = 'Vui lòng nhập password';
                }
                else if (strlen($pass) < 6) {
                    $error = 'Password ít nhất 6 kí tự';
                }
                else{
                    $account = new AccountModel();
                    $data = $account->login($user,$pass);
                    if($data){
                        if($data['data']['actived'] === 0){
                            $error = 'Account not activated';
                        }else{
                            if(isset($_POST['remember'])){
                                setcookie('username',$user,time()+3600);
                            }
                            $_SESSION['username'] = $user;
                            $_SESSION['yourname'] = $data['data']['yourname'];
                            $_SESSION['type'] = $data['data']['type'];
                            header("Location: ../");
                            die();
                        }
                    }
                    else{
                        $error = 'Username hoặc password không đúng';
                    }
                }
            }

            $this->render('login.html',array('message'=>$error,'user'=>$user,'pass'=>$pass));

        }

        public function logout(){
            session_destroy();
            header("Location: http://localhost/Final/account/login");
        }
        
        public function signup(){
            if(isset($_POST['signup'])){
                $error = '';
                $yourname = '';
                $email = '';
                $username = '';
                $password = '';
                $phone = '';
                $birthday = '';
                if (isset($_POST['username']) && isset($_POST['yourname']) && isset($_POST['password'])
                    && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['birthday']))
                {
                    $username = $_POST['username'];
                    $yourname = $_POST['yourname'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $birthday = $_POST['birthday'];
                    $phone = $_POST['phone'];

                    if (empty($yourname)) {
                        $error = 'Vui lòng nhập tên';
                    }
                    else if (empty($email)) {
                        $error = 'Vui lòng nhập mật khẩu';
                    }
                    else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                        $error = 'Email không đúng format';
                    }
                    else if (empty($username)) {
                        $error = 'Vui lòng nhập username';
                    }
                    else if (empty($password)) {
                        $error = 'Vui lòng nhập password';
                    }
                    else if(empty($birthday)){
                        $error = 'Vui lòng nhập ngày sinh';
                    }
                    else if(empty($phone)) {
                        $error = 'Vui lòng nhập sô điện thoại';
                    }
                    else if (strlen($password) < 6) {
                        $error = 'Password ít nhất 6 kí tự';
                    }
                    else {
                        //lam ham register
                        $account = new AccountModel();
                        $result = $account->signup($username,$password,$yourname,$birthday,$email,$phone);
                        if($result['code'] == 1){
                            $error = "Username đã tồn tại";
                        }
                        else if($result['code'] == 2){
                            $error = "Đăng kí thất bại";
                        }else{
                            $error = "Đăng kí thành công";
                        }
                    }

                    $this->render('signup.html',array("error"=>$error));
                }
            }else{
                $this->render('signup.html');
            }
        }

        public function  actived(){
            $error = '';
            if (isset($_GET['email']) && isset($_GET['token'])) {
                $email = $_GET['email'];
                $token = $_GET['token'];
                if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    $error = 'Invalid email address';
                } else if (strlen($token) != 32) {
                    $error = 'Invalid token format';
                }else{
                    $account = new AccountModel();
                    $result = $account->activatedToken($email,$token);
                    if($result['code'] == 0){
                        $error = '';
                    }else{
                        $error = $result['message'];
                    }
                }
            } else {
                $error = 'Invalid url';
            }
            $this->render('actived.html',array('data'=>$error));
        }

        public function reset(){ 
            $error = '';
            if (isset($_GET['email']) && isset($_GET['code'])) {
                $email = $_GET['email'];
                $code = $_GET['code'];
                if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    $error = 'Invalid email address';
                } else if (strlen($code) != 32) {
                    $error = 'Invalid token format';
                }else{
                    //check form reset
                    $password = "";
                    $account = new AccountModel();
                    $result = $account->resetpassword($email, $password);
                    if($result['code'] == 0){
                        $error = 'Thành công';
                    }else{
                        $error = $result['message'];
                    }
                }
            } else {
                $error = 'Invalid url';
            }
            $this->render('reset.html',array('data'=>$error));
        }

        public function forgot(){ 
            //check form forgot;
        }
    }
?>