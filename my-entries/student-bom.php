<?php
  class rcjStudent{
    public $uid;  
    public $firstName;
    public $lastName;
    public $gender;
    public $yearAtSchool;
    public $firstNameMessage;
    public $lastNameMessage;
    public $genderMessage;
    public $yearAtSchoolMessage;
	public $dob;
	public $dobMessage;
    
    public function isValid(){
  
      $this->firstNameMessage = '';
      $this->lastNameMessage = '';
      $this->genderMessage = '';
      $this->yearAtSchoolMessage = '';
	  $this->dobMessage = '';

      CECheckNotNull($this->firstName,    $this->firstNameMessage, 'Please enter a first name.');
      CECheckNotNull($this->lastName,     $this->lastNameMessage,  'Please enter a last name.');
      CECheckNotNull($this->gender,       $this->genderMessage,  'Please select the student\'s gender.');
      CECheckNotNull($this->yearAtSchool, $this->yearAtSchoolMessage,  'Please select the student\'s year at school.');
      CECheckNotNull($this->dobMessage,   $this->dobMessage,  'Please end the student\'s date of birth.');

      return (!$this->hasInvalidMessage());

    } 

    public function isEmpty(){
      return (empty($this->firstName) and empty($this->lastName) and empty($this->gender) and empty($this->yearAtSchool) and empty($this->dob));
    }  
  
    public function namesEqual($student){
      return (($this->firstName == $student->firstName) and ($this->lastName == $student->lastName));
    }
    
    public function hasInvalidMessage(){
      return 
        (!(empty($this->firstNameMessage) and 
           empty($this->lastNameMessage) and 
           empty($this->genderMessage) and 
           empty($this->yearAtSchoolMessage) and
		   empty($this->dobMessage)));
    }
  }
?>