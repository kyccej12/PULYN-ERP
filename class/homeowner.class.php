<?php
	session_start();
	
	
	class HomeOwner {

		public $owner_id;
		public $type;
		public $title;
		public $lname;
		public $fname;
		public $mname;
		public $owner_tower;
		public $owner_floor;
		public $owner_unit;
		public $owner_parking;
		public $owner_telno;
		public $nationality;
		public $occupation;
		public $birthdate;
		public $marital_status;
		public $spouse_lname;
		public $spouse_fname;
		public $spouse_mname;
		public $emergency_contact;
		public $acr_no;
		public $tower_utype;

		public $owner_house;
		public $owner_village;
		public $owner_city;
		public $owner_province;
		public $owner_country;
		public $owner_email;
		public $owner_contactno;
		public $spouse_bday;
		public $spouse_acrno;
		
		public $contract_no;
		public $contract_start;
		public $contract_end;

		public function __construct($owner_id){

			$data = getArray("SELECT
								`record_type`,`title`, lname,fname,mname,tower,assigned_parking,tower_unit,floor_no,tel_no,id_pic,nationality,occupation,company,date_format(birthdate,'%m/%d/%Y') as birthdate,marital_status,spouse_lname,spouse_fname, 
							     spouse_mname,emergency_contact,acr_no,`owner_house`,`owner_village`,`owner_city`,`owner_province`,`owner_country`,`owner_email`,`owner_contactno`,date_format(spouse_bday,'%m/%d/%Y') as spouse_bday,spouse_acrno,contract_no,if(contract_start = '0000-00-00','',date_format(contract_start,'%m/%d/%Y')) as contract_start,if(contract_end = '0000-00-00','',date_format(contract_end,'%m/%d/%Y')) as contract_end  
							FROM
							     citylights.homeowners where record_id = '$owner_id';");

			$this->owner_id = $owner_id;
			$this->type = $data['record_type'];
			$this->title = $data['title'];
			$this->lname = $data['lname'];
			$this->fname = $data['fname'];
			$this->mname = $data['mname'];
			$this->owner_tower = $data['tower'];
			$this->owner_floor = $data['floor_no'];
			$this->owner_unit = $data['tower_unit'];
			$this->owner_parking = $data['assigned_parking'];
			$this->owner_telno = $data['tel_no'];
			$this->owner_nationality = $data['nationality'];
			$this->spouse_lname = $data['spouse_lname'];
			$this->spouse_fname = $data['spouse_fname'];
			$this->spouse_mname = $data['spouse_mname'];
			$this->owner_house = $data['owner_house'];
			$this->owner_village = $data['owner_village'];
			$this->owner_city = $data['owner_city'];
			$this->owner_province = $data['owner_province'];
			$this->owner_country = $data['owner_country'];
			$this->occupation = $data['occupation'];
			$this->company = $data['company'];
			$this->birthdate = $data['birthdate'];
			$this->acr_no = $data['acr_no'];
			$this->owner_email = $data['owner_email'];
			$this->owner_contactno = $data['owner_contactno'];
			$this->spouse_bday = $data['spouse_bday']; 
			$this->spouse_acrno = $data['spouse_acrno'];
			$this->emergency_contact = $data['emergency_contact'];
			$this->marital_status = $data['marital_status'];
			$this->contract_no = $data['contract_no'];
			$this->contract_start = $data['contract_start'];
			$this->contract_end = $data['contract_end'];
			
		}

		function saveData(){
			if($this->owner_id==0){
				$qry = "INSERT IGNORE INTO citylights.homeowners (record_type, title, lname, fname, mname, tower, assigned_parking, tower_unit, floor_no, tel_no, id_pic, nationality, owner_country, occupation, birthdate
    					, marital_status, spouse_lname, spouse_fname, spouse_mname, spouse_bday, spouse_acrno, emergency_contact, acr_no, contract_no, contract_start, contract_end,created_by,created_on) VALUES ('$this->type','$this->title', '$this->lname', '$this->fname', '$this->mname', '$this->owner_tower', '$this->owner_parking', '$this->owner_unit', '$this->owner_floor', '$this->owner_telno', 'id_pic', '$this->owner_nationality', '$this->owner_country', '$this->occupation', '$this->birthdate'
    					, '$this->marital_status', '$this->spouse_lname', '$this->spouse_fname', '$this->spouse_mname', '".formatDate($this->spouse_bday)."', '$this->spouse_acrno', '$this->emergency_contact', '$this->acr_no','$this->contract_no','".formatDate($this->contract_start)."','".formatDate($this->contract_end)."','$_SESSION[userid]',now());";
			}else{
				$qry = "UPDATE IGNORE `citylights`.`homeowners` 
							SET
							    `record_type` = '$this->type'
							    ,`title` = '$this->title'
							    , `lname` = '$this->lname' 
							    , `fname` = '$this->fname'
							    , `mname` = '$this->mname'
							    , `tower` = '$this->owner_tower'
							    , `assigned_parking` = '$this->owner_parking'
							    , `tower_unit` = '$this->owner_unit'
							    , `floor_no` = '$this->owner_floor'
							    , `tel_no` = '$this->owner_telno'
							    , `occupation` = '$this->occupation'
							    , `birthdate` = '$this->birthdate'
							    , `marital_status` = '$this->marital_status'
							    , `spouse_lname` = '$this->spouse_lname'
							    , `spouse_fname` = '$this->spouse_fname'
							    , `spouse_mname` = '$this->spouse_mname'
							    , `spouse_bday` = '".formatDate($this->spouse_bday)."'
							    , `spouse_acrno` = '$this->spouse_acrno'
							    , `emergency_contact` = '$this->emergency_contact'
							    , `acr_no` = '$this->acr_no'
							    ,`owner_house`  = '$this->owner_house'
							    ,`owner_village` = '$this->owner_village'
							    , `owner_city` = '$this->owner_city'
							    , `owner_province` = '$this->owner_province'
							    , `owner_country` = '$this->owner_country'
							    , `owner_email` = '$this->owner_email'
							    , `owner_contactno` = '$this->owner_contactno'
							    ,`nationality` = '$this->owner_nationality'
							    ,`contract_no` = '$this->contract_no'
							    ,`contract_start` = '".formatDate($this->contract_start)."'
							    ,`contract_end` = '".formatDate($this->contract_end)."'
								,`updated_by` = '$_SESSION[userid]'
								,`updated_on` = now()
							WHERE 
								record_id = '$this->owner_id';";
			} 
			echo $qry;
			dbquery($qry);
		}

	}
	
?>