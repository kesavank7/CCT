<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// if($this->session->userdata('admin_logged_in')!=1){
		// 	redirect(base_url('login'), 'location');
		// }
        
        $this->load->model('courses_model');    
	}

	public function index()
	{
		//CSS REQUIRED FOR THIS PAGE ONLY
		$data['page_css']=array(base_url()."assets/plugins/datatables/dataTables.bootstrap.css");

		$data['title']="Courses";
		$data['page_title']= $this->lang->line('Courses');//page title
        $data['page_desc']="";//page desc
        $data['breadcrumb']=$this->lang->line('Courses');//breadcrumb
	
       	$data['all_courses']=$this->courses_model->get_all_courses_active_list();	
		$this->load->view("courses",$data);
	}

	public function add()
    {

		$data['title']="Courses";
		$data['page_id']=3;
		$data['page_title']= "Courses";//page title
        $data['page_desc']="";//page desc
        $data['breadcrumb']="Courses";//breadcrumb

		$this->load->view("add_course",$data);
    }

    public function add_course()
    {
        if($_POST){
        	$errors = array();      // array to hold validation errors
            $result = array();      // array to pass back data

            $this->form_validation->set_rules("course_title", "Title","trim|required");
            $this->form_validation->set_rules("course_description", "Description","trim|required");
           	$this->form_validation->set_rules("course_link", "Link","trim");
            $this->form_validation->set_rules("course_source", "Source","trim");
            

           if ($this->form_validation->run('course_title')==FALSE){
                if(form_error('course_title'))
                $errors['course_title'] = "Please enter course title";
            }	

            if ($this->form_validation->run('course_description')==FALSE){
                if(form_error('course_description'))
                $errors['course_description'] = "Please enter course description";
            }
           
		    if($errors){
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            else{
                $insert['course_title'] = $this->input->post('course_title');
                $insert['course_description'] = $this->input->post('course_description');
                $insert['course_link'] = $this->input->post('course_link');	
                $insert['course_source'] = $this->input->post('course_source');
				$insert['created_date'] = date("Y-m-d H:i:s");   
				       
                $last_insert_id = $this->courses_model->insert_course($insert);
               
                // end insert notification
                $result['success'] = true;
                $result['message'] = "Course added";
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

  

    public function edit($course_id)
    {
    	if(!$course_id){
    		redirect(base_url('courses'));
    	} 
		
       	$data['course_detail']=$this->courses_model->get_course_info($course_id);
           
        // echo '<pre>';
        // print_r($data['course_detail']);
        // echo '</pre>';
        // die;
		
		$this->load->view("edit_course",$data);
    }

    public function edit_course()
    {
        if($_POST){
            $errors = array();      // array to hold validation errors
            $result = array();      // array to pass back data
            $this->form_validation->set_rules("course_id", "Course id","trim|required");
            $this->form_validation->set_rules("course_title", "Title","trim|required");
            $this->form_validation->set_rules("course_description", "Description","trim|required");
            $this->form_validation->set_rules("course_link", "Link","trim");
            $this->form_validation->set_rules("course_source", "Source","trim");
            

           if ($this->form_validation->run('course_title')==FALSE){
                if(form_error('course_title'))
                $errors['course_title'] = "Please enter course title";
            }   

            if ($this->form_validation->run('course_description')==FALSE){
                if(form_error('course_description'))
                $errors['course_description'] = "Please enter course description";
            }

            if($errors){
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            else{
                $insert['course_title'] = $this->input->post('course_title');
                $insert['course_description'] = $this->input->post('course_description');
                $insert['course_link'] = $this->input->post('course_link'); 
                $insert['course_source'] = $this->input->post('course_source');

                $last_insert_id = $this->courses_model->update_course($this->input->post('course_id'),$insert);
                // echo '<script>
                // console.log('.$last_insert_id.');
                // </script>';
                
                if($last_insert_id){
                    $result['success'] = true;
                    $result['message'] = "Course Updated";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }
	
	
	public function delete_course($id){
		if($id){		
			
			$is_delete = $this->courses_model->delete_course($id);				
			if ($is_delete) {
				$this->session->set_flashdata('flashmsg', 'success');
			}
			else{
				$this->session->set_flashdata('flashmsg', 'fail');	
			}
		}
		$url=base_url()."courses";
		redirect($url,'location');
	}
   
}
