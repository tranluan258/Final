<?php
    class SubjectModel extends BaseModel{

        function get_all()
        {
            // TODO: Implement get_all() method.
        }

        function get_by_id($id)
        {
            // TODO: Implement get_by_id() method.
        }


        public function add_subject($teacher,$classname,$subjectname,$room){
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, $charactersLength - 1)];
            }

            $sql = "insert into subject(code,teacher,classname,subjectname,room) values (?,?,?,?,?)";
            $params = array('sssss',&$code,&$teacher,&$classname,&$subjectname,&$room);

            $data = $this->query_prepare_insert($sql,$params);
            if($data['code']==1){
                return array('code'=>1, 'message'=>'Tạo lớp học thất bại');
            }else{
                return array('code'=>0, 'message'=>'Tạo lớp học thành công');
            }
        }

        public function join_code($code,$student){
            $sql = "SELECT code from subject where code = ?";
            $params = array('s',&$code);
            $data = $this->query_prepare_select($sql,$params);
            if($data['code'] == 1){
                $error = 'Sai code';
            }else{
                $sql = "insert into subject_info(code,student) values(?,?)";
                $params = array('ss',&$code,&$student);
                $data = $this->query_prepare_insert($sql,$params);
                if($data['code'] == 1){
                    return array('code'=>1,'error'=>'That bai');
                }else{
                    return array('code'=>0,'error'=>'Thanh cong');
                }
            }
        }

        public function view_notice($code){
            $sql = "select id,username,information,datepost from notice where class = ?";
            $params = array('s',&$code);
            $dataSubject = $this->query_prepare_select($sql,$params);
            if($dataSubject['code'] == 1){
                $error = 'Lỗi';
            }else{
                $data = array();

                while ($item = $dataSubject['data']->fetch_assoc()){
                    array_push($data,array('idnotice'=>$item['id'],'username'=>$item['username'],'information'=>$item['information'],'datepost'=>$item['datepost']));
                }
                return array('data'=>$data);
            }
        }

        public function view_comment_highlight_notice($idnotice){
            $sql = "select id, username, comment from notice_comment where idnotice = ?";
            $params = array('i',&$idnotice);
            $dataNotice = $this->query_prepare_select($sql,$params);
            if($dataNotice['code'] == 1){
                $error = 'Lỗi';
            }else{
                $data = array();
                $count = 0;
                while ($item = $dataNotice['data']->fetch_assoc()){
                    if($count<3){
                        array_push($data,array('idcmt'=>$item['id'],'username'=>$item['username'],'comment'=>$item['comment']));
                        $count++;
                    }

                }
                return array('data'=>$data);
            }
        }

        public function view_comment_subject($idnotice){
            $sql = "select id, username, comment from notice_comment where idnotice = ?";
            $params = array('i',&$idnotice);
            $dataNotice = $this->query_prepare_select($sql,$params);
            if($dataNotice['code'] == 1){
                $error = 'Lỗi';
            }else{
                $data = array();
                while ($item = $dataNotice['data']->fetch_assoc()){
                    array_push($data,array('idcmt'=>$item['id'],'username'=>$item['username'],'comment'=>$item['comment']));
                }
                return array('data'=>$data);
            }
        }

        public function view_people_teacher($code){
            $sql = "select subject.teacher, account.yourname, account.email from subject,account where subject.teacher = account.username and code = ?";
            $params = array('s',&$code);
            $dataTeacher = $this->query_prepare_select($sql,$params);
            if($dataTeacher['code'] == 1){
                $error = 'Lỗi';
            }else{
                $data = array();
                while ($item = $dataTeacher['data']->fetch_assoc()){
                    array_push($data,array('teacher'=>$item['teacher'],'name'=>$item['yourname'],'email'=>$item['email']));
                }
                return array('data'=>$data);
            }
        }

        public function view_people_student($code){
            $sql = "select subject_info.student, account.yourname, account.email from subject_info,account where subject_info.student = account.username and code = ?";
            $params = array('s',&$code);
            $dataStudent = $this->query_prepare_select($sql,$params);
            if($dataStudent['code'] == 1){
                $error = 'Lỗi';
            }else{
                $data = array();
                while ($item = $dataStudent['data']->fetch_assoc()){
                    array_push($data,array('student'=>$item['student'],'name'=>$item['yourname'],'email'=>$item['email']));
                }
                return array('data'=>$data);
            }
        }

        public function add_notice($code,$username,$information){
            $sql = "insert into notice(class,username,information) values (?,?,?)";
            $params = array('sss',&$code,&$username,&$information);

            $data = $this->query_prepare_insert($sql,$params);

            if($data['code']==1){
                return array('code'=>1, 'message'=>'Đăng thông báo thất bại!');
            }else{
                return array('code'=>0, 'message'=>'Đăng thông báo thành công!');
            }
        }

        public function leave_class($code,$student){
            $sql = "DELETE FROM `subject_info` WHERE code = ? and student = ?";
            $params = array('ss',&$code,&$student);

            $data = $this->query_prepare_delete($sql,$params);

            if($data['code']==1){
                return array('code'=>1, 'message'=>'Rời lớp học thất bại!');
            }else{
                return array('code'=>0, 'message'=>'Rời lớp học thành công!');
            }
        }

    }
?>