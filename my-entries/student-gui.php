<?php

  function writeHTMLStudent($student, $index = '0'){
    if ($index != '0'){
      $prefix = 's' . $index . '_'; 
    } else {
      $prefix = '';
    }  

    if ($student != null){
      $uidTeamMember       = $student->uid;  
      $firstName           = $student->firstName;
      $firstNameMessage    = $student->firstNameMessage;
      $lastName            = $student->lastName;
      $lastNameMessage     = $student->lastNameMessage;
      $gender              = $student->gender;
      $genderMessage       = $student->genderMessage;
      $yearAtSchool        = $student->yearAtSchool;
      $yearAtSchoolMessage = $student->yearAtSchoolMessage;
	  $dob				   = $student->dob;
	  $dobMessage          = $student->dobMessage;
    } else {         
      $uidTeamMember       = '';  
      $firstName           = '';
      $firstNameMessage    = '';
      $lastName            = '';
      $lastNameMessage     = '';
      $gender              = '';
      $genderMessage       = '';
      $yearAtSchool        = '';      
      $yearAtSchoolMessage = '';
      $dob                 = '';      
      $dobMessage 		   = '';
    }
    
    $genderItems       = array('FEMALE' => 'Female', 'MALE' => 'Male');
    $yearAtSchoolItems =  array('04' => 'Year 4 or below', '05' => 'Year 5', '06' => 'Year 6', '07' => 'Year 7', '08' => 'Year 8', '09' => 'Year 9', '10' => 'Year 10', '11' => 'Year 11', '12' => 'Year 12');
    
    if ($index != '0'){
      echo '<fieldset><legend>Student #' . $index . '</legend>';
    }       
        
    CEWriteFormFieldHidden($prefix . 'uid_team_member', $uidTeamMember);
    if ($index == '0'){    
      CEWriteFormFieldTextAutofocus($prefix . 'first_name', 'First Name', $firstName, 60, $firstNameMessage);
    } else {
      CEWriteFormFieldText($prefix . 'first_name', 'First Name', $firstName, 60, $firstNameMessage);        
    }
    
    CEWriteFormFieldText($prefix . 'last_name',  'Last Name', $lastName, 60, $lastNameMessage);
    CEWriteFormFieldDropDownHardCoded($prefix . 'gender', 'Gender', $gender, $genderItems, $genderMessage);
    CEWriteFormFieldDropDownHardCoded($prefix . 'year_at_school', 'Year at School', $yearAtSchool, $yearAtSchoolItems, $yearAtSchoolMessage);    
    CEWriteFormFieldDate($prefix . 'dob', 'Date of Birth', $dob, $dobMessage);
	  
    if ($index != '0'){
      echo '</fieldset>';
    } 
    
  }
  
  function readStudentFromHTML($student, $index = 0){
    if ($index != '0'){
        $prefix              = 's' . $index . '_'; 
    } else {
        $prefix = '';
    }        
    $student->firstName = postFieldDefault($prefix . 'first_name'); 
    $student->lastName  = postFieldDefault($prefix . 'last_name'); 
    $student->gender    = postFieldDefault($prefix . 'gender');
    $student->yearAtSchool = postFieldDefault($prefix . 'year_at_school');    
	$student->dob = postFieldDefault($prefix . 'dob');    
  } 
  
?>