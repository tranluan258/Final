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
                    header("Location: detail");
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

        public function leave_class(){
            $error = '';
            $code = $_SESSION['currentcode'];
            $student = $_SESSION['username'];
            if(isset($_POST['leaveclass'])){
                $subject = new SubjectModel();
                $data = $subject->leave_class($code,$student);
                if($data['code'] == 1){
                    $error = "Rời lớp học thất bại!";
                } else {
                    $error = "Rời lớp học thành công!";
                }
                $_SESSION['error'] = $error;
                header("Location: ../");
            }
        }

        public function delete_student(){
            $error = '';
            $code = $_SESSION['currentcode'];
            if(isset($_POST['deletestudent'])){
                $student = $_POST['currentstudentdelete'];
                $subject = new SubjectModel();
                $data = $subject->leave_class($code,$student);
                if($data['code'] == 1){
                    $error = "Xóa học sinh khỏi lớp thất bại!";
                } else {
                    $error = "Xóa học sinh khỏi lớp thành công!";
                }
                $_SESSION['error'] = $error;
                header("Location: detail");
            }
        }

        public function detail()
        {
            $error = 'Xin chào' . ' ' . $_SESSION['yourname'] . '!';;

            $subject = new SubjectModel();
            if(isset($_POST['joinintoclass'])){
                unset($_SESSION['currentcode']);
            }
            if (isset($_SESSION['currentcode'])) {
                $notice = $subject->view_notice($_SESSION['currentcode']);
                $teacher = $subject->view_people_teacher($_SESSION['currentcode']);
                $student = $subject->view_people_student($_SESSION['currentcode']);
            }else{
                $notice = $subject->view_notice($_POST['currentcode']);
                $teacher = $subject->view_people_teacher($_POST['currentcode']);
                $student = $subject->view_people_student($_POST['currentcode']);
                $_SESSION['currentcode'] = $_POST['currentcode'];
            }
            $datacmt = array();
            foreach ($notice as &$noticecmt){
                foreach ($noticecmt as &$value){
                    $currentnotice = $value['idnotice'];
                    $cmt = $subject->view_comment_highlight($value['idnotice']);
                    array_push($datacmt,array($currentnotice=>$cmt));
                }
            }


            if (isset($_SESSION['error'])) {
                $error = $_SESSION['error'];
                unset($_SESSION['error']);
            }
            $data = array("errordetail" => $error, 'type' => $_SESSION['type'], 'notice' => $notice, 'noticecmt'=>$datacmt, 'teacher'=>$teacher, 'student'=>$student);

            $this->render('detail.html', $data);
        }

        public function notice(){
            $subject = new SubjectModel();
            if(isset($_POST['viewnotice'])){
                unset($_SESSION['currentnotice']);
            }
            if (isset($_SESSION['currentnotice'])) {
                $result = $subject->view_comment($_SESSION['currentnotice']);
            }else{
                $result = $subject->view_comment($_POST['currentnotice']);
                $_SESSION['currentnotice'] = $_POST['currentnotice'];
            }

            $data = array('type' => $_SESSION['type'], 'comment' => $result);
            print_r($data);
            $this->render('notice.html', $data);
        }

    }
?>