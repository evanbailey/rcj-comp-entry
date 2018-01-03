<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-bom.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-gui.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-svr.php';
  
  function GetValuesFromPK($con, $student)
  {
    $sql = $con->prepare('select first_name, last_name, gender, year_at_school, dob from team_member where uid = :uid_team_member');
    $sql->bindParam(':uid_team_member', $student->uid);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $student->firstName = $row['first_name'] ;
    $student->lastName  = $row['last_name'] ;
    $student->gender  = $row['gender'] ;
    $student->yearAtSchool  = $row['year_at_school'] ;
	$student->dob = $row['dob'] ;
  }  

  function WriteHTML($con, $Heading, $FormAction, $uid_team, $student)
  {
    
    WriteConnectUserDetails($con);
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    CEWriteFormStart($Heading, 'edit-team-member', 'edit-team-member.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid_team', $uid_team);
    writeHTMLStudent($student);
    CEWriteFormEnd('/my-entries/edit-comp.php');
    CEWritePageEnd();
  }

  function UniqueCheck($con, $uid_team, $student)
  {
    $sql = $con->prepare(
	  'select count(*) as count from team_member ' .
	  'where ' .
	  '  uid <> :uid_team_member and ' .
	  '  uid_team = :uid_team and ' .
	  '  first_name = :first_name and  ' .
	  '  last_name  = :last_name');
    $sql->bindParam(':uid_team_member', $student->uid);
    $sql->bindParam(':uid_team',   $uid_team);
    $sql->bindParam(':first_name', $student->firstName);
    $sql->bindParam(':last_name',  $student->lastName);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function Validate($con, $uid_team, $student)
  {
    $student->isValid();
    
    if (empty($student->firstNameMessage) and empty($student->lastNameMessage))
    {
      if (!UniqueCheck($con, $uid_team, $student))
      {  
        $student->firstNameMessage = 'This team member has already been entered. Please enter a unique team member name.'; 
      }
    }
    
    return 
      empty($student->firstNameMessage) and 
      empty($student->lastNameMessage) and  
      empty($student->genderMessage) and
      empty($student->yearAtSchoolMessage) and
	  empty($student->dob); 
   
  }
  
  function Update($con, $student){
    $query = $con->prepare('update team_member set first_name = :first_name, last_name = :last_name, gender = :gender, year_at_school = :year_at_school, dob = :dob where uid = :uid');
    $query->bindParam(':uid',        $student->uid);
    $query->bindParam(':first_name', $student->firstName);
    $query->bindParam(':last_name',  $student->lastName);
    $query->bindParam(':gender',  $student->gender);
    $query->bindParam(':year_at_school',  $student->yearAtSchool);
	$query->bindParam(':dob', $student->dob);
    $result = $query->execute();
    header('location: /my-entries/edit-comp.php');
  }

   function Insert($con, $uid_team, $student){
    insertStudent($con, $uid_team, $student);
    header('location: /my-entries/edit-comp.php');
  }

try
 {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

    $uid_user                     = $_SESSION['uid_logged_on_user'];
    $action                       = postFieldDefault('action');    
    $uid_team                     = postFieldDefault('uid_team');
    $student                      = new rcjStudent();
    $student->uid                 = postFieldDefault('uid_team_member');
    $student->firstName           = postFieldDefault('first_name');
    $student->lastName            = postFieldDefault('last_name'); 
    $student->gender              = postFieldDefault('gender');
    $student->yearAtSchool        = postFieldDefault('year_at_school');
	$student->dob				  = postFieldDefault('dob');
    $student->firstNameMessage    = '';
    $student->lastNameMessage     = ''; 
    $student->genderMessage       = '';
    $student->yearAtSchoolMessage = '';
	$student->dob				  - '';
     
    if ($action == CE_NEW)
    {
      WriteHTML(
        $con, 'Add a team member', CE_INSERT, $uid_team, null);        
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $student);
      WriteHTML(
        $con, 'Edit a team member', CE_UPDATE, $uid_team, $student);        
    }
    else if ($action == CE_INSERT)
    {
       ceNewUIDIfRequired($student->uid);
       if (Validate($con, $uid_team, $student))
       {
		 Insert($con, $uid_team, $student);
       }
       else
       {
        WriteHTML(
          $con, 'Add a team member', CE_INSERT, $uid_team, $student);            
       }    
    }     
    else if ($action == CE_UPDATE)
    {
       if (Validate($con, $uid_team, $student))
       {
		 Update($con, $student);
       }
       else
       {
        WriteHTML(
          $con, 'Edit a team member', CE_UPDATE, $uid_team, $student);            
       }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }

  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/my-entries/edit-comp.php');
  }  
  
?>