<?php
  
  function ceBoolToInt($value){
    if ($value){
      return 1;
    } else {
      return 0;
    }
  }
  
  function ceIntToBool($value){
    if ($value == 1){
      return TRUE;
    } else {
      return FALSE;
    }  
  }
  
  function UniqueCheck($con, $user)
  {
    $sql = $con->prepare('select count(*) as count from user where uid <> :uid and email = :email');
    $sql->bindParam(':uid', $user->uid);
    $sql->bindParam(':email', $user->email);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  
  
  function GetValuesFromPK(
    $con, $user)
  {
    $sql = $con->prepare('
      select 
        email, first_name, last_name, primary_org, access_level, mobile_num,
        adrs_line_1, adrs_line_2, suburb, postcode, state, rcja_member, mailing_list, share_with_sponsor, assign_proxy
      from 
        user 
      where 
        uid = :uid');
    $sql->bindParam(':uid', $user->uid);
    
    $sql->execute();
    $row                      =  $sql->fetch(PDO::FETCH_ASSOC);
    $user->email              =  $row['email'] ;
    $user->first_name         =  $row['first_name'] ;
    $user->last_name          =  $row['last_name'] ;
    $user->primary_org        =  $row['primary_org'] ;
	$user->mobile_num		  =  $row['mobile_num'] ;
    $user->access_level       =  $row['access_level'] ;
    $user->adrs_line_1        =  $row['adrs_line_1'] ;
    $user->adrs_line_2        =  $row['adrs_line_2'] ;
    $user->suburb             =  $row['suburb'] ;
    $user->postcode           =  $row['postcode'] ;
    $user->state              =  $row['state'] ;
    $user->rcja_member        =  ceIntToBool($row['rcja_member']) ;
    $user->mailing_list       =  ceIntToBool($row['mailing_list']) ;
    $user->share_with_sponsor =  ceIntToBool($row['share_with_sponsor']) ;
 	$user->assign_proxy   	  =  ceIntToBool($row['assign_proxy']) ;
  }  
  
  function validateUserAddress($user){
    $user->adrs_line_1_message = '';  
    $hasData = (!empty($user->adrs_line_1) or !empty($user->adrs_line_2) or 
      !empty($user->suburb) or !empty($user->postcode) or !empty($user->state));
    if (!$hasData){
      return true;
    } else {
      if (empty($user->adrs_line_1) or empty($user->suburb) or empty($user->postcode) or empty($user->state)){
        $user->adrs_line_1_message = 'Looks like the address is not complete.';
        return false;
      } else {
        return true;
      }
    }
  }
  
  function validateUserUpdateSelf($con, $user)
  {
    $user->email_message         = '';
    $user->first_name_message    = '';
    $user->last_name_message     = '';
    $user->primary_org_message   = '';
	$user->mobile_num_message    = '';
    $user->adrs_line_1_message   = '';
    $user->adrs_line_2_message   = '';
    $user->suburb_message        = '';
    $user->postcode_message      = '';
    $user->state_message         = '';

    if ((strlen($user->email) < 7) or (strlen($user->email) > 254))
    {  
      $user->email_message = 'Please enter an email address less than 254 characters long.'; 
    }

    if (!filter_var($user->email, FILTER_VALIDATE_EMAIL))
    {
      $user->email_message = 'Seriously, do you call than an email???'; 
    }

    if (!UniqueCheck($con, $user))
    {  
      $user->email_message = 'This email address already exists. Please enter a unique email address.'; 
    }

    if (empty($user->first_name))
    {
        $user->first_name_message = "Please the user's first name.";
    }

    if (empty($user->last_name))
    {
        $user->last_name_message = "Please the user's  last name.";
    }

    if (empty($user->primary_org))
    {
        $user->primary_org_message = 'Please enter your school or club name. If you are a independant or private team, enter the word "Independant".';
    }
    
    if (!ctype_digit($user->mobile_num))
    {
        $user->mobile_num_message = 'Seriously, do you call that a mobile number???';
    } 
	  
	if (strlen($user->mobile_num) < 10 OR strlen($user->mobile_num) > 20 )
    {
        $user->mobile_num_message = 'Please enter your Mobile Number, 10 to 20 characters long';
    } 
	  
    if (!empty($user->adrs_line_1) and strlen($user->adrs_line_1) > 100)
    {
        $user->adrs_line_1_message = 'Address lines must be less than 100 characters.';
    }

    if (!empty($user->adrs_line_2) and strlen($user->adrs_line_2) > 100)
    {
       $user->adrs_line_2_message  = 'Address lines must be less than 100 characters.';
    }

    if (!empty($user->suburb) and strlen($user->suburb) > 100)
    {
       $user->suburb_message  = 'Suburb must be less than 100 characters.';
    }

    if (!empty($user->postcode) and (strlen($user->postcode) != 4))
    {
        $user->postcode_message = 'Post code must be four characters.';
    }

    if (!empty($user->state) and ((strlen($user->state) < 2) or (strlen($user->state) > 4)))
    {
        $user->state_message = 'State must be 2 - 3 characters.';
    }

    $validAddress = 
      validateUserAddress($user);            
      
    return
      ($validAddress) and     
      empty($user->email_message) and 
      empty($user->first_name_message) and 
      empty($user->last_name_message) and 
	  empty($user->mobile_num_message) and 
      empty($user->primary_org_message) and 
      empty($user->adrs_line_1_message) and 
      empty($user->adrs_line_2_message) and 
      empty($user->suburb_message) and
      empty($user->postcode_message) and 
      empty($user->state_message);

  }

  function validateUserUpdateSysAdmin($con, $user)
  {
    $user->access_level_message  = '';

    $valid = validateUserUpdateSelf($con, $user);
    
    
    if (empty($user->access_level))
    {
        $user->access_level_message = 'Please select an access level.';
    }

    return
      ($valid) and     
      empty($user->access_level_message);

  }

  function writeAddressHTML($user)
  {   
    CEWriteFormFieldText('adrs_line_1', 'Address line 1', $user->adrs_line_1, 100, $user->adrs_line_1_message);
    CEWriteFormFieldText('adrs_line_2', 'Address line 2', $user->adrs_line_2, 100, $user->adrs_line_2_message);
    CEWriteFormFieldText('suburb',      'Suburb',         $user->suburb,      100, $user->suburb_message);
    CEWriteFormFieldDropDownHardCoded('state', 'State', $user->state, 
      array('ACT' => 'ACT', 'NSW' => 'NSW', 
            'QLD' => 'QLD', 'SA' => 'SA',
            'TAS' => 'TAS', 'VIC' => 'VIC',
            'WA' => 'WA'), $user->state_message);
    CEWriteFormFieldText('postcode',    'Postcode',      $user->postcode,      4, $user->postcode_message);
  } 

  function getMessage($messageID){
    if ($messageID == 'rcja_member'){
      return '\'Membership of RCJA allows you to vote at the RCJA AGM, held during the RCJA Australian Open National Competition each year. ' . 
			 ' Membership is available to anyone who, in the past two years has: ' .
             'i) volunteered at any RCJA event or ii) been a mentor of students competing, and iii) is aged 18 years or over.\''; 
    } else if ($messageID == 'mailing_list'){
      return '\'Would you like to be kept up to date with National, State and Regional RCJA information via our mailing list(s)?\'';         
    } else {
      return '\'RCJA depends on sponsor contributions to help fund our operations. Are you happy for your information to be shared with National, State and Regional sponsors?\'';    
    }
  }
  
  function writeHTMLRCJAMembership($user){
    echo '<br><fieldset><legend>Your Robocup Junior Australia Membership</legend>';
	echo '<p>Membership of RoboCup Junior Australia allows to you join committees, be elected to an electable position at an AGM and vote at an AGM (or assign your vote to your State/Terriroties proxy).</p>';
    echo '<p>Membership is free and open to anybody aged 18 or over who mentors teams or volunteers for RCJA.</p>';
	echo '<p>You may chose to assign your vote at an AGM to your State/Territory representative, as chosen by your State/Terriroty Committee. If you do not assign your vote to proxy, memberships are valid until the conclusion of the next AGM. If you assign your proxy, memberships are valid for two years since you last logged into this system. Membership for Committee Members at any level are valid indefinitly.</p>';
    CEWriteFormFieldCheckbox('rcja_member', 'Do you want to be a RCJA Member?', $user->rcja_member);
    CEWriteFormFieldCheckbox('assign_proxy', 'Do you wish to assign your vote to your State/Terriroty proxy?', $user->assign_proxy);
    echo '</fieldset><p>';      
  }
?>