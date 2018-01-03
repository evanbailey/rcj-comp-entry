<?php
  error_reporting(E_ALL); ini_set('display_errors', '1');
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }
  
  function redirect($url, $statusCode = 303)
   {
   header('Location: ' . $url, true, $statusCode);
   die();
   }
  
  function formatAddress($row){
    $return = '';
    if (!empty($row['adrs_line_1'])){
      $adrs = '<small>' . htmlspecialchars($row['adrs_line_1']) . '<br>';
      if (!empty($row['adrs_line_2'])){
        $adrs = $adrs . htmlspecialchars($row['adrs_line_2']) . '<br>'; 
      }
      $return = $adrs;  
    } 
    return $return; 
  }
  
  if(isset($_GET['action'])):
	if($_GET['action']=='revoke'):
	  $stmt = $con->prepare('UPDATE user SET rcja_member=0, assign_proxy=0, member_type=NULL, member_end=NOW(), member_begin=NULL WHERE uid=:uid');
	  $stmt->bindParam(':uid', $_GET['uid']);
	  $stmt->execute();
      redirect('default.php');

	elseif($_GET['action']=='cleanup_a'):
	  $sql = 'UPDATE user SET rcja_member=0, assign_proxy=0, member_type=NULL, member_end = NOW(), member_begin=NULL WHERE member_type = "Regular" AND assign_proxy=0 AND rcja_member=1';
	  $con->query($sql);
	  redirect('default.php');

    elseif($_GET['action']=='cleanup_b'):
	  $sql = 'UPDATE user SET rcja_member=0, assign_proxy=0, member_type=NULL, member_end = NOW(), member_begin=NULL WHERE member_type = "Regular" AND rcja_member=1 AND last_login < DATE_SUB(NOW(), INTERVAL 2 YEAR)';
	  $con->query($sql);
	  redirect('default.php');

	endif;
  endif;
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'RCJA Membership Register');

  if(isset($_GET['cleanup'])):
	if($_GET['cleanup']=='one'):
	  $sql = '';
	  $con->query($sql);
	elseif($_GET['two']):
	  $sql = '';
	  $con->query($sql);
	endif;
  endif;
       
  echo '<h1>RCJA Membership Register</h1>';

  echo '<h2>Revoke Memberships</h2>';//needs to be finished
  echo '<p class="indent"><a href="default.php?action=cleanup_a">Revoke memberships where the member type is Regular AND member has not assigned proxy</a></p>';
  echo '<p class="indent"><a href="default.php?action=cleanup_b">Revoke memberships where member has not logged in for two years AND member type is Regular</a></p>';
  
  echo '<h2>Self Voting Members</h2>';
  $sql = 
    'select           
       uid,           
       email,         
       last_name,     
       first_name,    
       adrs_line_1,   
       adrs_line_2,   
       suburb,        
       postcode,      
       state,
	   last_login,
	   member_type
     from             
       user
	 where
	   rcja_member = 1 AND assign_proxy = 0
     order by
	   state ASC,
       first_name ASC,    
       last_name ASC
	   '; 

  echo "<table border='1'>
  <tr>
  <th>First Name</th>
  <th>Last Name</th>
  <th>Email</th>
  <th>Address</th>
  <th>Suburb</th>
  <th>State</th>
  <th>Postcode</th>
  <th>Last Login</th>
  <th>Member Type</th>
  <th>Revoke Membership</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
	echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
	echo '<td>' . htmlspecialchars($row['email']) . '</td>';  
    echo '<td>' . formatAddress($row) . '</td>';
	echo '<td>' . htmlspecialchars($row['suburb']) . '</td>';
	echo '<td>' . htmlspecialchars($row['state']) . '</td>';
	echo '<td>' . htmlspecialchars($row['postcode']) . '</td>';
	echo '<td>' . htmlspecialchars($row['last_login']) . '</td>';
	echo '<td>' . htmlspecialchars($row['member_type']) . '</td>';  
	echo '<td>   <a href="default.php?action=revoke&uid=' . $row['uid'] . '" onclick=\"return confirm(\'Are you sure?\')\"\n">Revoke Membership for ' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . ' </a></td>'; //confirmation not working? 
    echo "</tr>";
  }

  echo "</table>";

  echo '<h2>Proxy Voting and Member Summary</h2>';
  $sql = 
    'SELECT           
       count(uid) as count,                
       state
     FROM             
       user
	 WHERE
	   rcja_member = 1 AND assign_proxy = 1
	 GROUP BY
	   state
     ORDER BY
	   state ASC
	   '; 

  echo "<table border='1'>
  <tr>
  <th>State/Territory</th>
  <th>Proxy Member Count</th>

  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
	echo '<td>' . htmlspecialchars($row['state']) . '</td>';
    echo '<td>' . htmlspecialchars($row['count']) . '</td>';

    echo "</tr>";
  }

  echo '<tr><td>&nbsp&nbsp</td><td>&nbsp&nbsp</td></tr>';

  $sql = 'SELECT count(uid) AS count FROM user WHERE assign_proxy=1';
  $result = $con->query($sql)->fetch();

  echo '<tr>
    <td> Count of Proxy Members </td>
    <td>' . $result['count'] . '</td>
  </tr>';

  $sql = 'SELECT count(uid) AS count FROM user WHERE rcja_member=1';
  $result = $con->query($sql)->fetch();

  echo '<tr>
    <td> Count of All Members </td>
    <td>' . $result['count'] . '</td>
  </tr>';

  echo "</table>";

  echo '<h2>Proxy Voting Members</h2>';
  $sql = 
    'select           
       uid,           
       email,         
       last_name,     
       first_name,    
       adrs_line_1,   
       adrs_line_2,   
       suburb,        
       postcode,      
       state,
	   last_login,
	   member_type
     from             
       user
	 where
	   rcja_member = 1 AND assign_proxy = 1
     order by
	   state ASC,
       first_name ASC,    
       last_name ASC
	   '; 

  echo "<table border='1'>
  <tr>
  <th>First Name</th>
  <th>Last Name</th>
  <th>Email</th>
  <th>Address</th>
  <th>Suburb</th>
  <th>State</th>
  <th>Postcode</th>
  <th>Last Login</th>
  <th>Member Type</th>
  <th>Revoke Membership</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
	echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
	echo '<td>' . htmlspecialchars($row['email']) . '</td>';  
    echo '<td>' . formatAddress($row) . '</td>';
	echo '<td>' . htmlspecialchars($row['suburb']) . '</td>';
	echo '<td>' . htmlspecialchars($row['state']) . '</td>';
	echo '<td>' . htmlspecialchars($row['postcode']) . '</td>';
	echo '<td>' . htmlspecialchars($row['last_login']) . '</td>';
	echo '<td>' . htmlspecialchars($row['member_type']) . '</td>';  
	echo '<td>   <a href="default.php?action=revoke&uid=' . $row['uid'] . '" onclick=\"return confirm(\'Are you sure?\')\"\n">Revoke Membership for ' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . ' </a></td>'; //confirmation not working? 
    echo "</tr>";
  }

  echo "</table>";

  echo '<h2>CAV Compliant Membership Register</h2>';
  $sql = 
    'select           
       uid,           
       email,         
       last_name,     
       first_name,    
       adrs_line_1,   
       adrs_line_2,   
       suburb,        
       postcode,      
       state
     from             
       user
	 where
	   rcja_member = 1
     order by   
       last_name ASC,
	   first_name ASC
	   '; 

  echo "<table border='1'>
  <tr>
  <th>First Name</th>
  <th>Last Name</th>
  <th>Email</th>
  <th>Address</th>
  <th>Suburb</th>
  <th>State</th>
  <th>Postcode</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
	echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
	echo '<td>' . htmlspecialchars($row['email']) . '</td>';  
    echo '<td>' . formatAddress($row) . '</td>';
	echo '<td>' . htmlspecialchars($row['suburb']) . '</td>';
	echo '<td>' . htmlspecialchars($row['state']) . '</td>';
	echo '<td>' . htmlspecialchars($row['postcode']) . '</td>';
    echo "</tr>";
  }

  echo "</table>";

  CEWritePageFooter();
  
?>