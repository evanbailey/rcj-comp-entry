<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/password-utils.php';
  
  function WritePageHeader()
  {  echo '<html>
    <head>
      <title>Robocup Junior Australia | Online Entry System</title>
      <link rel="icon" type="image/ico" href="favicon.ico">
	  <link rel="stylesheet" type="text/css" href="/shared/style.css">
      <script src="/ce/sha-1.js"></script>
      <script src="/ce/ce-utils.js"></script>
     </head>
      <body>
      <img src="/images/robocup-rescue-logo.png" style="position:absolute; top:0px; left:580px" />
      <h1>Robocup Junior Australia | Online Entry System</h1>';
  }  

  function WriteLogin($email, $message_email)
  {  echo '<!-- New login -->
      <script>
        function DoLogin()
        {
            cePost("/login/default.php", 
                   {action: "LOGIN", 
                    lo_email: document.login1.lo_email.value, 
                    lo_password: sha1(document.login1.lo_password.value)} );
        }
      </script>
      
      <form name="login1" action="javascript:DoLogin()" method="post">
      <input type="hidden" name="action" id="action" value="LOGIN">
      <fieldset><legend>Existing users</legend>

      <p><span class="field_message">' . $message_email . '</span>
         <span class="field_label">Email Address:</span>
         <input type="text" name="lo_email" id="lo_email" maxlength="254" value="' . $email . '" class="textbox"></p>
       
      <p><span class="field_label">Password:</span>
         <input type="password" name="lo_password" id="password" maxlength="20" value="" class="textbox"></p>
       
      <input type="submit" value="Login">
      </fieldset>                                   
      </form>';
  }

  function WriteNewUser($email, $message_email, 
    $password, $message_password, $password_again, $message_password_again, 
    $first_name, $message_first_name, $last_name, $message_last_name,      
    $organisation, $message_organisation, $mobile_num, $message_mobile_num)
  {

    echo '<form name="new_user" action="/login/default.php" method="post">';    
    echo '  
      <input type="hidden" name="action" id="action" value="NEW_USER">
      <fieldset><legend>New User</legend>';

      echo '<p><span class="field_message">' . $message_email . '</span>';
      echo '
         <span class="field_label">Email Address:</span>
         <input type="text" name="nu_email" id="nu_email" maxlength="254" value="' . $email . '" class="textbox"></p>';

      echo '<p><span class="field_message">' . $message_password . '</span>';
      echo '
         <span class="field_label">Password:</span>
         <input type="password" name="nu_password" id="nu_password" maxlength="20" value="' . $password . '" class="textbox"></p>';

      echo '<p><span class="field_message">' . $message_password_again . '</span>';
      echo '
         <span class="field_label">Password Again:</span>
         <input type="password" name="nu_password_again" id="nu_password_again" maxlength="20" value="' . $password . '" class="textbox"></p>';

      echo '<p><span class="field_message">' . $message_first_name . '</span>';
      echo '
         <span class="field_label">First Name:</span>
         <input type="text" name="nu_first_name" id="nu_first_name" maxlength="60" value="' . $first_name . '" class="textbox"></p>';

      echo '<p><span class="field_message">' . $message_last_name . '</span>';
      echo '
         <span class="field_label">Last Name:</span>
         <input type="text" name="nu_last_name" id="nu_last_name" maxlength="60" value="' . $last_name . '" class="textbox"></p>';

      echo '<p><span class="field_message">' . $message_organisation . '</span>';
      echo '
         <span class="field_label">Organisation:</span>
         <input type="text" name="nu_organisation" id="nu_organisation" maxlength="60" value="' . $organisation . '" class="textbox"></p>';
	  
	  echo '<p><span class="field_message">' . $message_mobile_num . '</span>';
      echo '
         <span class="field_label">Mobile Number:</span>
         <input type="text" name="nu_mobile_num" id="nu_mobile_num" maxlength="20" value="' . $mobile_num . '" class="textbox"></p>
      <p></p>
	  <p>By creating an account, you agree to sign up to regional, state and national email newsletters that are relevant, to me, and to share my name and email address with RoboCup Junior Australia regional, state and national supporters and sponsors. You can choose to opt out of these on the page displayed following account creation.</p> 
	  <p>';
	  echo 'By ticking this box, I confirm that I have read and agree to abide by the Terms of Use and Privacy Policy of this website, which are linked to in the footer below'; CEWriteFormFieldToUPrivacyCheckBox();
	  echo '<br/><br/>I understand I must be in receipt of signed Data Release Content forms prior to registering students for events'; CEWriteFormFieldToUPrivacyCheckBox();
	  echo '<br/><br/>I understand I must submit signed Data Relase Consent and Media Release Deed forms and a signed Mentor Declaration on the day for all registered students'; CEWriteFormFieldToUPrivacyCheckBox();
	  echo '</p>
      <p><input type="submit" value="Create my account"></p> 
      </fieldset>                                   
      </form>';
                       
  }

  function WritePasswordReset($email, $message_password)
  { echo '
      <form name="password_reset" action="/login/default.php" method="post">
      <input type="hidden" name="action" id="action" value="PASSWORD_RESET">
      <fieldset><legend>Password reset</legend>';
      echo '<p><span class="field_message">' . $message_password . '</span>';
      echo '<span class="field_label">Email Address:</span>
         <input type="text" name="pr_email" id="pr_email" maxlength="254" value="' . $email . '" class="textbox"></p>
      <input type="submit" value="Email my password">
      </fieldset>
      </form>';
  }

  function WriteHTML($lo_email, $lo_message_email,
                     $nu_email, $nu_message_email, 
                     $nu_password, $nu_message_password, 
                     $nu_password_again, $nu_message_password_again, 
                     $nu_first_name, $nu_message_first_name, $nu_last_name, $nu_message_last_name,      
                     $nu_organisation, $nu_message_organisation, $nu_mobile_num, $nu_mobile_num_message, $pr_email, $pr_message_password)
  {
    WritePageHeader();
    WriteLogin($lo_email, $lo_message_email);
                
    WriteNewUser($nu_email, $nu_message_email, $nu_password, $nu_message_password, $nu_password_again, $nu_message_password_again, 
                 $nu_first_name, $nu_message_first_name, $nu_last_name, $nu_message_last_name,      
                 $nu_organisation, $nu_message_organisation, $nu_mobile_num, $nu_mobile_num_message);
                 
    WritePasswordReset($pr_email, $pr_message_password);
    writePageFooter();
    echo '</body></html>';
  }  
  
  function ValidateOldLogin($con, $uid_user)
  {
	$sql = $con->prepare('select count(*) as count from user where uid = :uid');
    $sql->bindParam(':uid', $uid_user);
    $sql->execute();
    $row           = $sql->fetch(PDO::FETCH_ASSOC);
    return ($row['count'] > 0);
  }

  function CheckUnique($con, $email)
  {
    $sql = $con->prepare(
	  'select count(*) as count from user ' .
	  'where ' .
	  '  email = :email');
    $sql->bindParam(':email', $email);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }       

  function ValidateNewUser($con,
             $email,               &$message_email,          
             $password_hash,       &$message_password,       
             $password_hash_again, &$message_password_again, 
             $first_name,          &$message_first_name,   
             $last_name,           &$message_last_name,      
             $organisation,        &$message_organisation,
	         $mobile_num, 		   &$message_mobile_num)
  {
    $message_email          = ''; 
    $message_password       = '';
    $message_password_again = '';
    $message_first_name     = ''; 
    $message_last_name      = '';
    $message_organisation   = '';
	$message_mobile_num     = '';
    
    CECheckNotNull($email,          $message_email,          'Please enter an email address.');
    CECheckNotNull($password_hash_again, $message_password_again, 'Please re-enter the password.');
    CECheckNotNull($first_name,     $message_first_name,     'Please enter your first name.');
    CECheckNotNull($last_name,      $message_last_name,     'Please enter your last name.');
    CECheckNotNull($organisation,   $message_organisation, 'Please enter your organisation.');
    CECheckNotNull($mobile_num,   $message_mobile_num, 'Please enter your mobile number.');
  
	  
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
      $message_email = 'This dose not look like a valid email address.'; 
	}    
     
    if (!CheckUnique($con, $email)) 
    {
      $message_email = 'This email already exists. Please try again for a unique email.';    
    }
    
    if (empty($message_password) and (strlen($password_hash) < 6))
    {
      $message_password = 'Please enter a password of 6 - 20 characters.'; 
    }
    else
    {
      if ($password_hash <> $password_hash_again)
      {
        $message_password_again = 'Passwords did not match.';
      }
    }
    return empty($message_email) and 
           empty($message_password) and       
           empty($message_password_again) and 
           empty($$message_first_name) and   
           empty($message_last_name) and      
           empty($message_organisation) and 
		   empty($message_mobile_num);    
  }

  function LogonUser($con, $email, $password_hash, &$user_id, &$message)
  {
    $user_id = '';
    $message = '';
    $sql = $con->prepare(
	  'select uid from user ' .
	  'where ' .
	  '  email = :email and password_hash = :password_hash');
    $sql->bindParam(':email', $email);
    $sql->bindParam(':password_hash', $password_hash);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $user_id = $row['uid'];
    if (empty($user_id))
    {
      $message = 'Invalid email address and password.';
      return false;
    }
    else
    {
      return true;    
    }
  }
  
  function Save($con,
             $uid,
             $email,
             $password,
             $first_name,
             $last_name,
             $organisation, 
			 $mobile_num)
  {
    $passwordHash = sha1($password);  
    $query = $con->prepare(
      'insert into user ' .
      '(uid, email, password_hash, first_name, last_name, primary_org, mobile_num, access_level, rcja_member, mailing_list, share_with_sponsor) ' .
      'values ' .
      '(:uid, :email, :password_hash, :first_name, :last_name, :primary_org, :mobile_num, "' . C_MENTOR . '", 0, 1, 1)');      
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':email',         $email);
    $query->bindParam(':password_hash', $passwordHash);
    $query->bindParam(':first_name',    $first_name);
    $query->bindParam(':last_name',     $last_name);
    $query->bindParam(':primary_org',   $organisation);
	$query->bindParam(':mobile_num',   $mobile_num);
    $result = $query->execute();                 
  } 
  
  function UpdatePassword($con, $email, $password_hash){
    $query = $con->prepare(
      'update user set password_hash = :password_hash where email = :email');
    $query->bindParam(':email',         $email);
    $query->bindParam(':password_hash', $password_hash);
    $result = $query->execute();      
  }
  
  function DoPasswordReset($con, $email, &$message){
    if (CheckUnique($con, $email)){
        $message = 'Oops, we can not find this email address. Please try again.';
        return false;
    }
    else {
      $password = RandomPassword();
      $password_hash = sha1($password);
      UpdatePassword($con, $email, $password_hash);
      EmailPassword($email, $password);
      return true;      
    }  
  }
  
  try
  {
	
	$action = postFieldDefault('action');
	
    if (empty($action))
    {
      WriteHTML('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''); //wtf is this for?
    }
    else if ($action == 'LOGIN')
    {
       $email               = postFieldDefault('lo_email');
       $password_hash       = postFieldDefault('lo_password');
       if (LogonUser($con, $email, $password_hash, $uid_user, $message))
       {
         session_start();
	     $_SESSION['uid_logged_on_user'] = $uid_user;
         header('location: /');
	     exit();        
       }
       else
       {
         WriteHTML($email, $message, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''); //comment as above...I've added two '' to both assuming it matches the field/message count
       }
    }
    else if ($action == 'NEW_USER')
    {
       $nu_email               = postFieldDefault('nu_email');
       $nu_password            = postFieldDefault('nu_password');
       $nu_password_again      = postFieldDefault('nu_password_again');
       $nu_first_name          = postFieldDefault('nu_first_name');
       $nu_last_name           = postFieldDefault('nu_last_name');
       $nu_organisation        = postFieldDefault('nu_organisation');
	   $nu_mobile_num          = postFieldDefault('nu_mobile_num');
       if (ValidateNewUser($con,
         $nu_email,               $nu_message_email,          
         $nu_password,            $nu_message_password,       
         $nu_password_again,      $nu_message_password_again, 
         $nu_first_name,          $nu_message_first_name,   
         $nu_last_name,           $nu_message_last_name,      
         $nu_organisation,        $nu_message_organisation,
		 $nu_mobile_num,          $nu_message_mobile_num))
       {
         ceNewUIDIfRequired($nu_uid_user);
         Save($con,
              $nu_uid_user,         
              $nu_email,
              $nu_password,       
              $nu_first_name,     
              $nu_last_name,      
              $nu_organisation,
			  $nu_mobile_num);
         session_start();
	     $_SESSION['uid_logged_on_user'] = $nu_uid_user;
         header('location: /');
	     exit();      
       }
       else
       {
         WriteHTML('', '', 
                   $nu_email, $nu_message_email, $nu_password, $nu_message_password, $nu_password_again, $nu_message_password_again, 
                   $nu_first_name, $nu_message_first_name, $nu_last_name, $nu_message_last_name,      
                   $nu_organisation, $nu_message_organisation, $nu_mobile_num, $nu_message_mobile_num,
                   '', '');
       }
    }
    else if ($action == 'PASSWORD_RESET')
    {
      $pr_email               = postFieldDefault('pr_email');
      $pr_message_password    = ''; 
      if (DoPasswordReset($con, $pr_email, $pr_message_password)) {
        WritePageHeader();
        WriteLogin($pr_email, 'Your new password has been emailed to &lt;' . $pr_email . '&gt; You can now login below using the new password.');
        WritePageFooter();
      }
      else {
         WriteHTML('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $pr_email, $pr_message_password); //again, wtf?
      }
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/');
  } 
 
  
?>