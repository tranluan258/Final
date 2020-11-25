<?php
    class SubjectController extends BaseController{
        public function detail(){
            $this->render('detail.html');
        }
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

        public  function  add_thongbao(){

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

    }
?>