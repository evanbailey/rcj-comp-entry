<?php

  function insertStudent($con, $uidTeam, $student){
     $query = $con->prepare('insert into team_member ' .
      '(uid, uid_team, first_name, last_name, gender, year_at_school, dob) ' .
      'values ' .
      '(:uid, :uid_team, :first_name, :last_name, :gender, :year_at_school, :dob)');
    ceNewUIDIfRequired($student->uid);
    $query->bindParam(':uid',             $student->uid);
    $query->bindParam(':uid_team',        $uidTeam);
    $query->bindParam(':first_name',      $student->firstName);
    $query->bindParam(':last_name',       $student->lastName);
    $query->bindParam(':gender',          $student->gender);
    $query->bindParam(':year_at_school',  $student->yearAtSchool);
	$query->bindParam(':dob',             $student->dob);
    $result = $query->execute();     
  }

  function insertStudentIfValid($con, $uidTeam, $student){
    if ($student->isValid()){
      insertStudent($con, $uidTeam, $student);
    }
  }


?>