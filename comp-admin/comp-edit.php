<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/comp-admin/comp-bom.php';
  
  function UniqueCheck($con, $comp)
  {
    $sql = $con->prepare(
	  'select count(*) as count from comp_name ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_year = :uid_year and ' .
	  '  uid_state = :uid_state and  ' .
	  '  comp_name = :comp_name');
    $sql->bindParam(':uid',       $comp->uid);
    $sql->bindParam(':uid_year',  $comp->uid_year);
    $sql->bindParam(':uid_state', $comp->uid_state);
    $sql->bindParam(':comp_name', $comp->comp_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $comp)
  {
    $sql = $con->prepare('select comp_name, uid_year, uid_state, event_date, start_date, end_date, entry_fee, custom_message, uid_enquiries_to, venue, invoice_message, invoice_address from comp_name where uid = :uid');
    $sql->bindParam(':uid', $comp->uid);
    $sql->execute();
    $row              = $sql->fetch(PDO::FETCH_ASSOC);
    $comp->comp_name        =  $row['comp_name'] ;
    $comp->uid_comp_year    =  $row['uid_year'] ;
    $comp->uid_comp_state   =  $row['uid_state'] ;
    $comp->event_date       =  $row['event_date'] ;
    $comp->start_date       =  $row['start_date'] ;
    $comp->end_date         =  $row['end_date'] ;
    $comp->entry_fee        =  $row['entry_fee'] ;
    $comp->custom_message   =  $row['custom_message'] ;
    $comp->uid_enquiries_to =  $row['uid_enquiries_to'] ; 
    $comp->venue            =  $row['venue'] ;
    $comp->invoice_message  =  $row['invoice_message'] ;
    $comp->invoice_address  =  $row['invoice_address'] ;
  }  

  function Validate($con, $comp)
  {
    $comp->comp_name_error  = '';
    $comp->comp_state_error = '';
    CECheckNotNull($comp->comp_name,          $comp->comp_name_error,  'Please enter a competition name.');
	  CECheckMaxStrLength($comp->comp_name, 60, $comp->comp_name_error,  'Please enter a competition name which is less than 60 characters.');
    CECheckNotNull($comp->uid_comp_state,     $comp->comp_state_error, 'Please select a competition state.');
    CECheckNotNull($comp->entry_fee,          $comp->entry_fee_error,  'Please enter the entry fee.');

	  CECheckMaxStrLength($comp->custom_message, 1000, $comp->custom_message_error,  'Please enter a custom message less than 1,000 characters.');
    CECheckNotNull($comp->uid_enquiries_to, $comp->uid_enquiries_to_error, 'Please select the person to direct enquiries to.');
	  CECheckMaxStrLength($comp->venue, 500, $comp->venue_error,  'Please enter venue details less than 500 characters.');
    CECheckNotNull($comp->venue, $comp->venue_error, 'Please enter details of the venue');

	  CECheckMaxStrLength($comp->invoice_message, 1000, $comp->invoice_message_error,  'Please enter invoice message less than 1000 characters.');
    CECheckNotNull($comp->invoice_message, $comp->invoice_message_error, 'Please enter the invoice message');

	  CECheckMaxStrLength($comp->invoice_address, 500, $comp->invoice_address_error,  'Please enter invoice address less than 500 characters.');
    CECheckNotNull($comp->invoice_address, $comp->invoice_address_error, 'Please enter the invoice address');
    
    if (!UniqueCheck($con, $comp))
    {  
      $comp->comp_name_error = 'The competition name "' . 
	    $comp->comp_name . 
		'" already exists for this year & state combination. Please enter a unique name.'; 
    }
    
	// ToDo: Need some validation for start_date & end_date
	
    return 
      empty($comp->comp_name_error) and 
      empty($comp->comp_state_error) and 
  	  empty($comp->event_date_error) and 
  	  empty($comp->start_date_error) and 
	    empty($comp->end_date_error) and 
	    empty($comp->entry_fee_error) and 
      empty($comp->uid_enquiries_to_error) and 
      empty($comp->custom_message_error) and 
      empty($comp->venue_error) and   
      empty($comp->invoice_message_error) and 
      empty($comp->invoice_address_error);   
  }
  
  function WriteHTML($con, $Heading, $FormAction, $uidReturnTo, $comp)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-name-add', 'comp-edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $comp->uid);
    CEWriteFormFieldHidden('uid_return_to', $uidReturnTo);
    CEWriteFormFieldHidden('uid_comp_year', $comp->uid_comp_year);
    echo '<fieldset><legend>Competition name</legend>';
    CEWriteFormFieldTextAutofocus('comp_name', 'Competition Name', $comp->comp_name, 60, $comp->comp_name_error);
    CEWriteFormFieldDropDown(
      'uid_comp_state', 'Competition State', $comp->uid_comp_state, $con, 
      'select uid, state as display from comp_state order by display',
      $comp->comp_state_error);
    CEWriteFormFieldDropDown('uid_enquiries_to', 'Direct enquiries to', $comp->uid_enquiries_to, $con, 
      'select 
        uid, concat(first_name, " ", last_name, " (", primary_org, ")") as display 
      from 
        user
      where 
        access_level in ("COMP_ADMIN", "SYS_ADMIN", "SYS_DEV")      
      order by 
        first_name, last_name', 
      $comp->uid_enquiries_to_error);    
    CEWriteFormFieldCurrency('entry_fee', 'Entry fee', $comp->entry_fee, 0, 999, $comp->entry_fee_error);
    echo '</fieldset><br><fieldset><legend>Competition dates</legend>';
    CEWriteFormFieldDate('event_date', 'Event Date', $comp->event_date, $comp->event_date_error);
    CEWriteFormFieldDate('start_date', 'Entries Open Date', $comp->start_date, $comp->start_date_error);
    CEWriteFormFieldDate('end_date', 'Entries Close Date', $comp->end_date, $comp->end_date_error);
    echo '</fieldset><br><fieldset><legend>Entry form messages</legend>';
    CEWriteFormFieldMemo('venue', 'Name and address of venue', $comp->venue, 2, 63, $comp->venue_error);
    CEWriteFormFieldMemo('custom_message', 'Message on entry page', $comp->custom_message, 10, 63, $comp->custom_message_error);
    echo '</fieldset><br><fieldset><legend>Invoice messages</legend>';
    CEWriteFormFieldMemo('invoice_message', 'Message on invoice', $comp->invoice_message, 10, 63, $comp->invoice_message_error);
    CEWriteFormFieldMemo('invoice_address', 'Address for payment communication', $comp->invoice_address, 5, 63, $comp->invoice_address_error);

    echo '</fieldset>';
    CEWriteFormEnd('/comp-admin#' . $uidReturnTo);
    CEWritePageEnd();
  }

  function saveDefaultDivision($con, $comp, $dispOrder, $divName){
    ceNewUIDIfRequired($uid);
    $query = $con->prepare('insert into comp_division (uid, uid_comp_name, disp_order, div_name) values (:uid, :uid_comp_name, :disp_order, :div_name)');
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':uid_comp_name', $comp->uid);
    $query->bindParam(':disp_order',    $dispOrder);
    $query->bindParam(':div_name',      $divName);
    $result = $query->execute();
  }

  // ToDo: Make saveDefaultDivision() data driven - taking the input from a memo field
  function saveDefaultDivisions($con, $comp){
    saveDefaultDivision($con, $comp,  0, 'Primary Dance');
    saveDefaultDivision($con, $comp,  1, 'Secondary Dance');
    saveDefaultDivision($con, $comp,  2, 'Open Dance');
    saveDefaultDivision($con, $comp,  3, 'Riley Rover Rescue');
    saveDefaultDivision($con, $comp,  4, 'Primary Rescue');
    saveDefaultDivision($con, $comp,  5, 'Secondary Rescue');
    saveDefaultDivision($con, $comp,  6, 'Open Rescue');
    saveDefaultDivision($con, $comp,  7, 'Simple Simon Soccer');
    saveDefaultDivision($con, $comp,  8, 'Standard Soccer');
    saveDefaultDivision($con, $comp,  9, 'Lightweight Soccer');
    saveDefaultDivision($con, $comp, 10, 'Open Soccer');
  }
  
  function Save($con, $action, $uidReturnTo, $comp){

    if ($action == CE_UPDATE){
      $sql = 'update comp_name set comp_name = :comp_name, uid_year = :uid_year, uid_state = :uid_state, event_date = :event_date, start_date = :start_date, end_date = :end_date, entry_fee = :entry_fee, custom_message = :custom_message, uid_enquiries_to = :uid_enquiries_to, venue = :venue, invoice_message = :invoice_message, invoice_address = :invoice_address where uid = :uid';
    } else {
      $sql = 'insert into comp_name (uid, uid_year, uid_state, comp_name, event_date, start_date, end_date, entry_fee, custom_message, uid_enquiries_to, venue, invoice_message, invoice_address) values (:uid, :uid_year, :uid_state, :comp_name, :event_date, :start_date, :end_date, :entry_fee, :custom_message, :uid_enquiries_to, :venue, :invoice_message, :invoice_address)';
    } 
    
    $query = $con->prepare($sql);
    $query->bindParam(':uid',              $comp->uid);
    $query->bindParam(':comp_name',        $comp->comp_name);
    $query->bindParam(':uid_year',         $comp->uid_comp_year);
    $query->bindParam(':uid_state',        $comp->uid_comp_state);
    $query->bindParam(':event_date',       $comp->event_date);
    $query->bindParam(':start_date',       $comp->start_date);
    $query->bindParam(':end_date',         $comp->end_date);
    $query->bindParam(':entry_fee',        $comp->entry_fee);
    $query->bindParam(':custom_message',   $comp->custom_message);
    $query->bindParam(':uid_enquiries_to', $comp->uid_enquiries_to);
    $query->bindParam(':venue',            $comp->venue);
    $query->bindParam(':invoice_message',  $comp->invoice_message);
    $query->bindParam(':invoice_address',  $comp->invoice_address);
    
    $result = $query->execute();
    if ($action == CE_INSERT){
      saveDefaultDivisions($con, $comp);
    }  
    header('location: /comp-admin#' . $uidReturnTo);
  }

  function setCompDefaults($comp){
    date_default_timezone_set('Australia/Melbourne');
    $comp->start_date = date('m/d/Y');
    $comp->end_date   = date('m/d/Y', strtotime('+ 1 month'));
    $comp->event_date = date('m/d/Y', strtotime('+ 2 month'));
    $comp->entry_fee  = 50.00;    
    $comp->invoice_message = 
      'RoboCupJunior Australia is a non profit organisation so no GST applies.<br><br>' . PHP_EOL . 
      'Payment must be made by EFT to:<br><br>' . PHP_EOL .
      '<strong>Account Name: </strong>Enter Account Name Here<br>' . PHP_EOL .
      '<strong>BSB: </strong>Enter BSB Here<br>' . PHP_EOL .
      '<strong>Account Number: </strong>Enter account number here<br><br>' . PHP_EOL .
      'Or credit card by following the link <a href="enter-payment-url-here">here</a>.<br><br>' . PHP_EOL .
      'Please email payment confirmation to <a href="mailto:enter-treasurer-email-here">enter-treasurer-email-here</a> and' . PHP_EOL . 
      'quote reference [INVOICE_REF].<br><br>' . PHP_EOL .
      'Payment must be received by [ENTRY_CLOSING_DATE].<br>';    
    $comp->invoice_address = 
      'Brian Thomas<br>' . PHP_EOL .
      '117 Clifton Springs Rd<br>' . PHP_EOL .
      'Drysdale  VIC  3222';
  }
  
  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
  
    date_default_timezone_set('Australia/Victoria');
    
    $action                 = postFieldDefault('action');
    $uidReturnTo            = postFieldDefault('uid_return_to');
    $comp                   = new rcjaComp();
    $comp->uid              = postFieldDefault('uid');
    $comp->comp_name        = trim(postFieldDefault('comp_name'));
    $comp->uid_comp_year    = trim(postFieldDefault('uid_comp_year'));
    $comp->uid_comp_state   = trim(postFieldDefault('uid_comp_state'));
    $comp->event_date       = trim(postFieldDefault('event_date'));
    $comp->start_date       = trim(postFieldDefault('start_date'));
    $comp->end_date         = trim(postFieldDefault('end_date'));
    $comp->entry_fee        = trim(postFieldDefault('entry_fee'));
    $comp->custom_message   = trim(postFieldDefault('custom_message'));   
    $comp->uid_enquiries_to = trim(postFieldDefault('uid_enquiries_to'));
    $comp->venue            = trim(postFieldDefault('venue'));
    $comp->invoice_message  = trim(postFieldDefault('invoice_message'));
    $comp->invoice_address  = trim(postFieldDefault('invoice_address'));

    $comp->comp_name_error        = '';
    $comp->comp_state_error       = ''; 
    $comp->event_date_error       = ''; 
    $comp->start_date_error       = ''; 
    $comp->end_date_error         = ''; 
    $comp->entry_fee_error        = ''; 
    $comp->custom_message_error   = '';
    $comp->uid_enquiries_to_error = '';
    $comp->venue_error            = '';
    $comp->invoice_message_error  = '';
    $comp->invoice_address_error  = '';

    if ($action == CE_NEW)
    {
      setCompDefaults($comp);      
      WriteHTML(
        $con, 'Add a new competition', 
        CE_INSERT, $uidReturnTo, $comp);
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $comp);
      WriteHTML(
        $con, 'Edit a competition', 
        CE_UPDATE, $uidReturnTo, $comp);
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($comp->uid);
      if (empty($uidReturnTo)){
        $uidReturnTo = $uid;
      }
      if (Validate($con, $comp))
      {
        Save($con, CE_INSERT, $uidReturnTo, $comp);
      }
      else
      {
        WriteHTML(
          $con, 'Add a new competition', 
          CE_INSERT, $uidReturnTo, $comp);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $comp))
      {
        Save($con, CE_UPDATE, $uidReturnTo, $comp);
      }
      else
      {
        WriteHTML(
          $con, 'Edit a competition ', 
          CE_UPDATE, $uidReturnTo, $comp);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uidReturnTo);
  }  
?>