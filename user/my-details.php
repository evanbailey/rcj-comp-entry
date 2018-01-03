<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-bom.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-shared.php';
  
  function WriteHTML($con, $FormAction, $user)
  {
    CEWritePageHeader(C_SITE_TITLE, 'Update my details');
    WriteConnectUserDetails($con);
    CEWriteFormStart('Update my details', 'my-details', '/user/my-details.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $user->uid);
    echo '<fieldset><legend>Required information</legend>';
    CEWriteFormFieldTextAutofocus('email', 'Email Address', $user->email, 245, $user->email_message);
    CEWriteFormFieldText('first_name', 'First Name', $user->first_name, 60, $user->first_name_message);
    CEWriteFormFieldText('last_name', 'Last Name', $user->last_name, 60, $user->last_name_message);
    CEWriteFormFieldText('primary_org', 'Organisation', $user->primary_org, 60, $user->primary_org_message);
	CEWriteFormFieldText('mobile_num', 'Mobile Number', $user->mobile_num, 20, $user->mobile_num_message);
	CEWriteFormFieldCheckbox('mailing_list', 'Do you want to be added to local, regional and national mailing list?', $user->mailing_list);
    echo '<small><a href="javascript:alert(' . getMessage('mailing_list') . ')">Tell me more about this...</a></small>';
    CEWriteFormFieldCheckbox('share_with_sponsor', 'Can we share your details with our sponsors and supporters?', $user->share_with_sponsor);
    echo '<small><a href="javascript:alert(' . getMessage('share_with_sponsor') . ')">Tell me more about this...</a></small>';
    echo '</fieldset><p>';
    ceWriteSaveAndCancelButtons('/'); 
    echo '<br><fieldset><legend>Optional information</legend>';
    writeAddressHTML($user);
    echo '</fieldset><p>';
	ceWriteSaveAndCancelButtons('/'); 
	writeHTMLRCJAMembership($user);
 
    // To Do: Go back to the previous page, not the main menu
    CEWriteFormEnd('/');
    CEWritePageEnd();
  }

  function Save(
    $con, $sql, $user)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',                $user->uid);
    $query->bindParam(':email',              $user->email);
    $query->bindParam(':first_name',         $user->first_name);
    $query->bindParam(':last_name',          $user->last_name);
    $query->bindParam(':primary_org',        $user->primary_org);
    $query->bindParam(':mobile_num',         $user->mobile_num);
	$query->bindParam(':adrs_line_1',        $user->adrs_line_1);
    $query->bindParam(':adrs_line_2',        $user->adrs_line_2);
    $query->bindParam(':suburb',             $user->suburb);
    $query->bindParam(':postcode',           $user->postcode);
    $query->bindParam(':state',              $user->state);
    $query->bindParam(':rcja_member',        ceBoolToInt($user->rcja_member));
    $query->bindParam(':mailing_list',       ceBoolToInt($user->mailing_list));
    $query->bindParam(':share_with_sponsor', ceBoolToInt($user->share_with_sponsor));
    $query->bindParam(':assign_proxy',       ceBoolToInt($user->assign_proxy));
	  
    $result = $query->execute();
    header('location: /');
  }

  
 try
 {
    session_start();
    $action             = postFieldDefault('action');
    $user              = new rcjaUser();
    $user->uid                = $_SESSION['uid_logged_on_user'];
    $user->email              = postFieldDefault('email');
    $user->first_name         = postFieldDefault('first_name');
    $user->last_name          = postFieldDefault('last_name');
    $user->primary_org        = postFieldDefault('primary_org');
	$user->mobile_num         = postFieldDefault('mobile_num');
    $user->access_level       = '';
    $user->adrs_line_1        = postFieldDefault('adrs_line_1');
    $user->adrs_line_2        = postFieldDefault('adrs_line_2');
    $user->suburb             = postFieldDefault('suburb');
    $user->postcode           = postFieldDefault('postcode');
    $user->state              = postFieldDefault('state');
    $user->rcja_member        = postFieldDefault('rcja_member');
    $user->mailing_list       = postFieldDefault('mailing_list');
    $user->share_with_sponsor = postFieldDefault('share_with_sponsor');
    $user->assign_proxy       = postFieldDefault('assign_proxy');
	
	if(!$user->rcja_member):
	  $user->assign_proxy = FALSE;
	endif;
	 
    if (empty($action))
    {
      GetValuesFromPK($con, $user);
      WriteHTML($con, CE_UPDATE, $user);
    }
    else if ($action == CE_UPDATE)
    {
      if (validateUserUpdateSelf($con, $user))
      {
		//get current membership status and adjust start/end dates/member type/proxy as neccessary
		$qry = $con->query('SELECT rcja_member FROM user WHERE uid = "' . $_SESSION['uid_logged_on_user'] . '"');
		$qry = $qry->fetchColumn();

		if($qry != ceBoolToInt($user->rcja_member)):
		  if(ceBoolToInt($user->rcja_member)==1):
		  	$con->query('UPDATE user SET member_begin=NOW(), member_end=NULL, member_type = "Regular" WHERE uid = "' . $_SESSION['uid_logged_on_user'] . '"');
		  else:
		    $con->query('UPDATE user SET member_begin=NULL, member_end=NOW(), member_type="", assign_proxy=0 WHERE uid = "' . $_SESSION['uid_logged_on_user'] . '"');
		  endif;
		endif;
		  
        Save($con, 'update user ' .
		           'set email              = :email,       			' .
                   '    first_name         = :first_name,  			' . 
                   '    last_name          = :last_name,   			' . 
                   '    primary_org        = :primary_org, 			' .
			       '    mobile_num         = :mobile_num,  			' .
                   '    adrs_line_1        = :adrs_line_1, 			' . 
                   '    adrs_line_2        = :adrs_line_2, 			' . 
                   '    suburb             = :suburb,      			' . 
                   '    postcode           = :postcode,    			' . 
                   '    state              = :state,      			' . 
                   '    rcja_member        = :rcja_member,      	' . 
                   '    mailing_list       = :mailing_list,     	' . 
                   '    share_with_sponsor = :share_with_sponsor,	' . 
			 	   '    assign_proxy	   = :assign_proxy      	' . 
				   'where uid = :uid',
             $user);
      }
      else
      {
        WriteHTML($con, CE_UPDATE, $user);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/user');
  }  
?>