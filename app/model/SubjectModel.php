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
    }
?>