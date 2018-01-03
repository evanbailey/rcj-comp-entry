<?php 

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require './my-entries/mentor-menu-system.php';
  
  function WriteMainMenu($con){

      WriteConnectUserDetails($con);
      CEWritePageHeader(C_SITE_TITLE, 'Main Menu');
      
      $uid_logged_on_user = $_SESSION['uid_logged_on_user'];
      $display_text_logged_on_user = '';
      $primary_org_logged_on_user  = '';
      $access_level_logged_on_user = '';

      GetUserDetails($con, $uid_logged_on_user, 
        $display_text_logged_on_user, 
        $primary_org_logged_on_user, 
        $access_level_logged_on_user);
            
      echo '<h1>Robocup Junior Australia | Online Entry System</h1>';

      if (($access_level_logged_on_user == C_SYS_DEV))
      {
        echo '<h2>Developer</h2>
              <p class="indent"><a href="/view-to-do.php">Developer to-do list</a></p>
              <p class="indent"><a href="/sys-admin/comp-year/">Years (eg 2014, 2015, etc)</a> | 
                                <a href="/sys-admin/comp-state/">States (eg VIC, NSW, etc)</a><br>              
                                <a href="/sys-admin/comp-name/">Competitions (eg Ballarat, Melbourne, etc)</a> | 
                                <a href="/sys-admin/comp-division/">Divisions (eg Rescue, Dance, etc...)</a><br>
                                <a href="/sys-admin/mentor-team/">Mentor-teams</a> | 
                                <a href="/sys-admin/team/">Teams</a> | 
                                <a href="/sys-admin/team-member/">Team members</a></p>
              <p class="indent"><a href="/sys-admin/db/">Update Database structure</a> |
                                <a href="/sys-admin/db-admin">Direct database access</a></p>';
  	    date_default_timezone_set('Australia/Melbourne');
	  	  echo '<p class="indent">PHP Version: ' . phpversion() . ' | 
		        PHP OS: ' . PHP_OS . ' |  System date: ' . date("Y-m-d H:i:s") . '<br>' .
                'php_uname(): ' . php_uname() . '</p>';

      }

      if (($access_level_logged_on_user == C_SYS_DEV) or
          ($access_level_logged_on_user == C_SYS_ADMIN))
      {
        echo '<h2>System Administrator Actions</h2>
              <p class="indent"><a href="/sys-admin/user/">Manage users</a></p>
              <p class="indent"><a href="/sys-admin/backup/">Backup and restore database</a></p>
              <p class="indent"><a href="/sys-admin/log/">View system logs</a></p>
              <p class="indent"><a href="/org-admin/mem-reg">View membership register</a></p>';
              
      }
      
      if (($access_level_logged_on_user == C_SYS_DEV) or
          ($access_level_logged_on_user == C_SYS_ADMIN) or
          ($access_level_logged_on_user == C_COMP_ADMIN))
      {
        echo '<h2>Competition Administrator Actions</h2>';  
        echo '<p class="indent"><a href="/comp-admin/">Manage competitions and divisions</a></p>';
        echo '<p class="indent"><a href="/report/competition">Download by competition</a></p>';
        echo '<p class="indent"><a href="/report/year">Download by year</a></p>';
        echo '<h2>Mentor Actions</h2>';
      }
      
      if (($access_level_logged_on_user == C_SYS_DEV) or
          ($access_level_logged_on_user == C_SYS_ADMIN) or
          ($access_level_logged_on_user == C_COMP_ADMIN) or
          ($access_level_logged_on_user == C_MENTOR))
      {
        WriteMentorCompetitionIndex($con);
      }
      echo '<br>';
      writePageFooter();
      echo '</body>
            </html>';
  }
   
   session_start();
   if (empty($_SESSION['uid_logged_on_user']))
     header('location: /login');
   else{
	     //force information update for missing mobile or last login in previous year
	 	 $update = 0;
	   	 $sql = 'SELECT uid FROM user WHERE uid = "' . $_SESSION['uid_logged_on_user'] . '" AND (mobile_num IS NULL OR last_login IS NULL OR YEAR(last_login) < YEAR(NOW()))';
	     $result = $con->query($sql);
	     if($result->fetchColumn()):
	     	$update = 1;
	     endif;
	   
		 //udpate latest login time in user table
		 $sql = 'UPDATE user SET last_login = now() WHERE uid = "' . $_SESSION['uid_logged_on_user'] . '"';
		 $con->query($sql);
	   
	     //redirect for force update
	     if($update == 1):
	     	header("Location: user/my-details.php?message=1");
	   		die();
	   	 endif;
	   
	 WriteMainMenu($con);
	 

   }  

?>