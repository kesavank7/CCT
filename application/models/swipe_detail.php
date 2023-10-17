<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Swipe_detail extends CI_Model {
	
		private $employee_db;

		public function __construct() {
			$this->employee_db = $this->load->database(_DB_NAME_EMPLOYEE, TRUE);
		}

		/**
		 * Function to get all leave entry details
		 * @return type
		 */
		function getSwipeDetails($user_id, $from_date, $to_date,$reporting_to) {
			
			//echo "dbo.CGVak_PunchTimeDetails_View.date Between dateadd(hh,6,'".$from_date."')  AND dateadd(d,1,dateadd(hh,6,'".$to_date."')) )";exit;
			if(isset($user_id)) {
				// $this->db->select("Distinct CGVak_EmployeeMaster_view.employeenumber, CGVak_EmployeeMaster_view.employeeicode, (dbo.CGVak_PunchTimeDetails_View.date) as punch, (dbo.CGVak_PunchTimeDetails_View.flag) as unit, convert(varchar,dbo.CGVak_PunchTimeDetails_View.date,0)[time], dbo.CGVak_PunchTimeDetails_View.date As T,  dbo.CGVak_PunchTimeDetails_View.date, dbo.CGVak_EmployeeMaster_view.departmenticode, (dbo.CGVak_EmployeeMaster_view.EmployeeFirstName+' '+dbo.CGVak_EmployeeMaster_view.EmployeelastName ) [EmployeeFirstName],  (Case 
				// When (dbo.CGVak_PunchTimeDetails_View.date > dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))) AND
				// dateadd(d,1,dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))) > dbo.CGVak_PunchTimeDetails_View.date)
				// Then convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))
				// Else dateadd(d,-1,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))
				// End ) AS Group_date,
				// datename(dw, (Case 
				// When (dbo.CGVak_PunchTimeDetails_View.date > dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))) AND
				// dateadd(d,1,dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))) > dbo.CGVak_PunchTimeDetails_View.date)
				// Then convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))
				// Else dateadd(d,-1,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))
				// End )) date_name,
				// dbo.CGVak_PunchTimeDetails_View.IsApproved,
				// dbo.CGVak_PunchTimeDetails_View.Approvedby,
				// (select a.EmployeeFirstName+' '+a.EmployeelastName from dbo.CGVak_EmployeeMaster_view a where a.employeeicode =dbo.CGVak_PunchTimeDetails_View.Approvedby)approvedbyname,
				// dbo.CGVak_PunchTimeDetails_View.Approvedon,
				// dbo.CGVak_PunchTimeDetails_View.PunchTimeDetailsId,
				// dbo.CGVak_PunchTimeDetails_View.description");
				// $this->db->from("dbo.CGVak_EmployeeMaster_view");
				// $this->db->join("dbo.CGVak_PunchTimeDetails_View", "Cast(dbo.CGVak_PunchTimeDetails_View.tktno as Int)= Cast(dbo.CGVak_EmployeeMaster_view.employeeNumber as Int)");
				// $this->db->where("dbo.CGVak_EmployeeMaster_view.employeeicode",$user_id);
				// //$this->db->where("dbo.CGVak_PunchTimeDetails_View.date Between dateadd(hh,6,'".$from_date."') AND dateadd(d,1,dateadd(hh,6,'".$to_date."') )");
				// $this->db->where("dbo.CGVak_PunchTimeDetails_View.date Between dateadd(hh,6,'".$from_date."')  AND dateadd(d,1,dateadd(hh,6,'".$to_date."') )");
				// $this->db->where("cgvak_employeemaster_view.isactive","1");
				
				// $result = $this->db->order_by('dbo.CGVak_PunchTimeDetails_View.date','asc')->get()->result_array();
				// return $result;

				$sql = "select employeeicode,punch,unit,[time],t,pdate,employeefirstname,group_date,date_name,isapproved,approvedby,approvedon,punchtimedetailsid,description,status,hoursworked,
				(case when status='Out' then 
				   convert(varchar,(dateadd(ms,sum(isnull(hoursworked,0)) OVER (PARTITION BY  Group_date order by pdate)*1000,0)),108)
				else
					''
				end)'HR:MIN:SS'   
					from(
				-- convert(varchar,dateadd(ms,datediff(SS,LAG(punch) OVER (PARTITION BY  Group_date order by pdate),punch) *1000,0),108)
				
				select employeeicode,punch,unit,[time],t,pdate,employeefirstname,group_date,date_name,isapproved,approvedby,approvedon,punchtimedetailsid,description
				,(case when ROW_NUMBER() OVER(PARTITION BY  Group_date order by pdate)% 2 = 0 then 'Out' else 'In' end)status,
				 (case when ROW_NUMBER() OVER(PARTITION BY  Group_date order by pdate)% 2 = 0 then 
					-- convert(varchar,dateadd(ms,datediff(SS,LAG(punch) OVER (PARTITION BY  Group_date order by pdate),punch) *1000,0),108) else '00:00:00' 
					  datediff(SS,LAG(punch) OVER (PARTITION BY  Group_date order by pdate),punch)
				 end) hoursworked
				  
				 from(SELECT row_number() over(order by employeeicode)rownumber,
				CGVak_EmployeeMaster_view.employeenumber,
								 CGVak_EmployeeMaster_view. employeeicode,
								 (dbo.CGVak_PunchTimeDetails_View.date)punch,
									(dbo.CGVak_PunchTimeDetails_View.flag)unit,
								   convert(varchar,dbo.CGVak_PunchTimeDetails_View.date ,0)[time],
								 dbo.CGVak_PunchTimeDetails_View.date As 'T' ,
								 dbo.CGVak_PunchTimeDetails_View.date as pdate,
								 dbo.CGVak_EmployeeMaster_view.departmenticode,
								 (dbo.CGVak_EmployeeMaster_view.EmployeeFirstName+' '+dbo.CGVak_EmployeeMaster_view.EmployeelastName ) [EmployeeFirstName],
								 (Case 
									 When (dbo.CGVak_PunchTimeDetails_View.date > dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))) AND
										 dateadd(d,1,dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))) > dbo.CGVak_PunchTimeDetails_View.date)
									Then convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))
									Else dateadd(d,-1,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))
									End ) Group_date,
									   datename(dw, (Case 
										When (dbo.CGVak_PunchTimeDetails_View.date > dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))) AND
										 dateadd(d,1,dateadd(hh,6,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))) > dbo.CGVak_PunchTimeDetails_View.date)
									Then convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120))
									Else dateadd(d,-1,convert(datetime,convert(varchar(10),dbo.CGVak_PunchTimeDetails_View.date,120)))
									End )) date_name,
									dbo.CGVak_PunchTimeDetails_View.IsApproved,
									dbo.CGVak_PunchTimeDetails_View.Approvedby,
									(select a.EmployeeFirstName+' '+a.EmployeelastName from dbo.CGVak_EmployeeMaster_view a where a.employeeicode =dbo.CGVak_PunchTimeDetails_View.Approvedby)approvedbyname,
									dbo.CGVak_PunchTimeDetails_View.Approvedon,
									dbo.CGVak_PunchTimeDetails_View.PunchTimeDetailsId,
				dbo.CGVak_PunchTimeDetails_View.description FROM dbo.CGVak_EmployeeMaster_view,dbo.CGVak_PunchTimeDetails_View WHERE Cast(dbo.CGVak_PunchTimeDetails_View.tktno as Int)= Cast(dbo.CGVak_EmployeeMaster_view.employeeNumber as Int) AND (dbo.CGVak_EmployeeMaster_view.employeeicode =$user_id ) And cgvak_employeemaster_view.isactive =1
							 AND   dbo.CGVak_PunchTimeDetails_View.date Between dateadd(hh,6,{d '".$from_date."' })  AND dateadd(d,1,dateadd(hh,6,{d '".$to_date."' }) ) 	 
							 ) as t )as u
							 Order by EmployeeFirstName, Group_date, pdate";
							 $query = $this->db->query($sql);

				$result = $query->result_array();
			
				
				foreach ($result as $key => $value) {
					foreach ($reporting_to as $key1 => $value1) {
						if($value1['EmployeeICode']==$value['approvedby']){
							$result[$key]['EmployeeDisplayName'] = $value1['EmployeeDisplayName'];		
							
							break;
					   }else{
						$result[$key]['EmployeeDisplayName'] = "";
					   }
					}
					
				 }

				 return $result;

			}
		}

		function saveManualEntry(){
			if(!empty($data)){
				$result = $this->db->insert_batch('tbl_user', $data); 
				return $result;
			}
		}

		function getLeaveDetails($tablename, $user_id,$current_year,$month='') {
			if ((isset($tablename) && isset($user_id))) {
				//$current_year = date("Y");
				$this->db->select('leaveentry.*,employee.EmployeeDisplayName as emp_name');
				$this->db->from($tablename.' as leaveentry');
				$this->db->join(_DB_EMPLOYEE.'CGVak_EmployeeMaster as employee', 'employee.EmployeeICode = leaveentry.empreportingto');
				$this->db->where('leaveentry.employeeicode',$user_id);
				$this->db->where("YEAR(empleavedatefrom)",$current_year);
				if(isset($month) && $month!='' && $month!='all') {
					$this->db->where("MONTH(empleavedatefrom)",$month);
				}
				$result = $this->db->order_by('createddate','desc')->get()->result_array();
				return $result;
			}
		}
		
		function getLeaveDetailsByID($tablename,$user_id,$id)
		{
			if ((isset($tablename) && isset($user_id)) && isset($id)) {
				$this->db->select('leaveentry.*,employee.EmployeeDisplayName as emp_name');
				$this->db->from($tablename.' as leaveentry');
				$this->db->join(_DB_EMPLOYEE.'CGVak_EmployeeMaster as employee', 'employee.EmployeeICode = leaveentry.empreportingto');
				$this->db->where('leaveentry.employeeicode',$user_id);
				$this->db->where('leaveentry.employeeleaveicode',$id);
				$result = $this->db->get()->row_array();
				return $result;
			}
		}
		
		function updatedLeaveByID($tablename,$user_id,$id,$data)
		{
			$result = $this->db->where(array('employeeleaveicode'=>$id,'employeeicode'=>$user_id))->update($tablename,$data);
			return $result;
		}
		
		function validateCompensationDate($tablename,$user_id,$required_date)
		{
			$result = $this->db->select('TaskProgressICode')->where(array('TaskProgressDate'=>$required_date,'CreatedBy'=>$user_id))->get($tablename)->row_array();
			return $result;
		}
		
		function validateLeaveDate($tablename,$user_id,$InputData)
		{
			$this->db->select('*')
					 ->where('empleavedatefrom >=',$InputData['from_date'])
					 ->where('empleavedateto <=',$InputData['to_date'])
					 ->where('employeeicode',$user_id);
		    $result = $this->db->get($tablename)->row_array();
			return $result;
		}
}
