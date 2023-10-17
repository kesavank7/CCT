<?php
class Courses_model extends CI_Model
{
   public function __construct()
    {
        parent::__construct();
    }

    //insert course
    public function insert_course($insert)
    {
        $result=$this->db->insert(TBL_COURSE,$insert);
        if($result){ 
            return TRUE;
        }
        else{
            return FALSE;
        }
    }   

    //update course
    public function update_course($id,$update)
    {
        // echo '<pre>';
        // echo $id;
        // echo '</pre>';
        // die;
        $this->db->where('course_id',$id);
        $result=$this->db->update(TBL_COURSE,$update);

        if($result){ 
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    

    //get all active courses list
    public function get_all_courses_active_list() { 
        $this->db->select('course_id,course_title,course_description,course_link,course_source,created_date');       
        $this->db->from(TBL_COURSE);
        $this->db->order_by("created_date", "desc");

        $query = $this->db->get();
        return $query->result();
    }

    //get course info
    public function get_course_info($id) {
        if($id){
            $this->db->where(array('course_id'=>$id));
        }
       
        $query = $this->db->get(TBL_COURSE);
        return $query->row();        
    }

    //delete course info
    public function delete_course($id){

        if($this->db->delete(TBL_COURSE, array('course_id'=>$id))){
            return TRUE;
        }
        else{
            return FALSE;
        }

    }   

}
?>