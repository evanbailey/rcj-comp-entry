<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require './mentor-menu-system.php';
  
  if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
      exit(); //==>>
  }

  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Robocup Junior Australia | Online Entry System');
  echo '<h1>Robocup Juior Australia | Online Entry System</h1>';
  
  WriteMentorCompetitionIndex($con);
  CEWritePageFooter();  
?>