<?php
    class SubjectController extends BaseController{

        public function add_student(){
            if(isset($_POST['btn-add-student'])){
                $subject = new SubjectModel();

                $list_student = $subject->get_list_student_not_join($_SESSION['currentcode']);
                $subjectinfo = $subject->get_subject($_SESSION['currentcode']);
                $this->render('add_student.html',array('student'=>$list_student,'subjectinfo'=>$subjectinfo));
            }
        }

        public function add_student_choose(){
            if(isset($_POST['add-student-choose'])){
                $error = '';
                $subject = new SubjectModel();
                $data_email_teacher = $subject->get_email_by_username($_SESSION['username']);
                $email_teacher = $data_email_teacher['email'];
                $code = $_SESSION['currentcode'];
                $message = "Bạn được mời tham gia vào lớp học, sử dụng code: <b>$code</b> để tham gia vào lớp.";
                if(isset($_POST['liststudent'])){
                    $list_choose = $_POST['liststudent'];

                    for($i=0;$i<count($list_choose);$i++){
                        $subject->send_email_student($email_teacher,$list_choose[$i],$message);
                    }
                    $error = 'Mời học sinh vào lớp thành công';
                    $_SESSION['error'] = $error;
                    header("Location: detail");

                }

            }
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
                $dataSubject = $subject->get_subject($_SESSION['currentcode']);
            }else{
                $notice = $subject->view_notice($_POST['currentcode']);
                $teacher = $subject->view_people_teacher($_POST['currentcode']);
                $student = $subject->view_people_student($_POST['currentcode']);
                $dataSubject = $subject->get_subject($_POST['currentcode']);
                $_SESSION['currentcode'] = $_POST['currentcode'];
            }
            $datacmt = array();
            foreach ($notice as &$noticecmt){
                foreach ($noticecmt as &$value){
                    $currentnotice = $value['idnotice'];
                    $cmt = $subject->view_comment_highlight_notice($value['idnotice']);
                    array_push($datacmt,array($currentnotice=>$cmt));
                }
            }


            if (isset($_SESSION['error'])) {
                $error = $_SESSION['error'];
                unset($_SESSION['error']);
            }
            $data = array("errordetail" => $error, 'type' => $_SESSION['type'], 'notice' => $notice, 'noticecmt'=>$datacmt, 'teacher'=>$teacher, 'student'=>$student, 'subjectinfo'=>$dataSubject);

            $this->render('detail.html', $data);
        }

        public function notice(){
            $subject = new SubjectModel();
            if(isset($_POST['viewnotice'])){
                unset($_SESSION['currentnotice']);
            }
            if (isset($_SESSION['currentnotice'])) {
                $result = $subject->view_comment_notice($_SESSION['currentnotice']);
                $infonotice = $subject->get_notice_by_id($_SESSION['currentnotice']);
            }else{
                $result = $subject->view_comment_notice($_POST['currentnotice']);
                $infonotice = $subject->get_notice_by_id($_POST['currentnotice']);
                $_SESSION['currentnotice'] = $_POST['currentnotice'];
            }

            $data = array('type' => $_SESSION['type'], 'comment' => $result, 'notice' => $infonotice);

            $this->render('notice.html', $data);
        }

    }
?>