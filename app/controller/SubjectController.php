<?php
    class SubjectController extends BaseController{

        public function add_student(){

        }

        public function add_subject(){
            if(isset($_POST['addnewclass'])) {
                $error = '';
                $classname = '';
                $subjectname = '';
                $room = '';
                $teacher = $_SESSION['username'];
                if (isset($_POST['classname']) && isset($_POST['subjectname']) && isset($_POST['room'])) {
                    $classname = $_POST['classname'];
                    $subjectname = $_POST['subjectname'];
                    $room = $_POST['room'];

                    if (empty($classname)) {
                        $error = 'Vui lòng nhập tên lớp học';
                    } else if (empty($subjectname)) {
                        $error = 'Vui lòng nhập tên môn học';
                    } else if (empty($room)) {
                        $error = 'Vui lòng nhập phòng học';
                    } else {
                        $subject = new SubjectModel();
                        $result = $subject->add_subject($teacher, $classname, $subjectname, $room);
                        if ($result['code'] == 1) {
                            $error = 'Tạo lớp học thất bại';
                        } else {
                            $error = 'Tạo lớp học thành công';
                        }
                    }

                    $_SESSION['error'] = $error;
                    header("Location: ../");
                }
            }
        }

        public function add_notice(){
            if(isset($_POST['addnotice'])){
                $error = '';
                $info = '';
                $username = $_SESSION['username'];
                $code = $_SESSION['currentcode'];
                if(isset($_POST['noticeinfo'])){
                    $noticeinfo = $_POST['noticeinfo'];
                    if(empty($noticeinfo)){
                        $error = 'Vui lòng nhập thông báo';
                    }else{
                        $subject = new SubjectModel();
                        $result = $subject->add_notice($code,$username,$noticeinfo);
                        if ($result['code'] == 1) {
                            $error = 'Đăng thông báo thất bại';
                        } else {
                            $error = 'Đăng thông báo thành công';
                        }
                    }
                    $_SESSION['error'] = $error;
                    header("Location: http://localhost:8080/Final/");
                }
            }
        }

        public function join_code(){
            $error = '';
            if(isset($_POST['join'])){
                $code = $_POST['join_code'];
                $subject = new SubjectModel();
                $data = $subject->join_code($code,$_SESSION['username']);
                if($data['code'] == 1){
                    $error = "Vào lớp thất bại! Vui lòng kiểm tra lại code.";
                } else {
                    $error = "Vào lớp thành công";
                }
                $_SESSION['error'] = $error;
                header("Location: ../");
            }
        }

        public function detail(){
            $error = 'Xin chào'. ' '. $_SESSION['yourname'] . '!';;
            if(!isset($_POST['joinintoclass'])){
                header("Location: http://localhost/Final");
                exit();
            }else{
                $subject = new SubjectModel();

                $result = $subject->view_notice($_POST['currentcode']);

                if(isset($_SESSION['error'])){
                    $error =$_SESSION['error'];
                    unset($_SESSION['error']);
                }
                $_SESSION['currentcode'] = $_POST['currentcode'];
                $data = array("errordetail"=>$error, 'type'=>$_SESSION['type'],'notice'=>$result);

                $this->render('detail.html',$data);
            }
        }



    }
?>