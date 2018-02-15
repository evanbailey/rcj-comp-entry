<?php
  function messageForm($con, $heading, $message, $actions){
    CEWritePageHeader(C_SITE_TITLE, $heading);
    WriteConnectUserDetails($con);

    echo '<form name="message">';
    echo '<fieldset><legend>' . $heading . '</legend>';
    echo '<p>' . $message . '</p>';
    foreach ($actions as $caption => $action){
      echo '  <input type="button" value="' . $caption . '" onclick="window.location=\'' . $action . '\';">';
    }
    echo '</fieldset>';
    echo '</form>';    

    
    echo '</body>';
    echo '</html>';
    
  }

function writePageFooter()
  {
    echo '<p></p><hr>
			  <p class="indentsmall">
				<a href="https://enter.rcjonline.org.au/touprivacy.php" target="_blank">Click here to view the Terms of Use and Privacy Policy.</a>
			  </p>
			<hr>
			  <p class="indentsmall">
			  	<a href="http://choosealicense.com/licenses/gpl-3.0/" target="_default">
			  	Free, open source</a> online competition entry system by 
			  <a href="http://www.clubengineer.org/robocup.html" target="_default">Club Engineer</a>.
			  You can download the source 
			  <a href="http://www.clubengineer.org/downloads/competition-entry-software" target="_default"> here</a>.<hr>';          
  }
  
  /* old one with sponsors logos - easier to remove than maintain
  function writePageFooter()
  {
    echo '<p></p><hr>
          <p class="indent">
          <a href="http://www.eng.unimelb.edu.au/"  target="_default">
            <img src="/images/melbourne-university.jpg"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="http://www.teaching.com.au/"  target="_default">
            <img src="/images/mta-logo.jpg"></a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="http://www.araa.asn.au/" target="_default">
            <img src="/images/araa-logo.png"></a></p>
          <hr><p class="indentsmall">
          <a href="http://choosealicense.com/licenses/gpl-3.0/" target="_default">
          Free, open source</a> online competition entry system by 
          <a href="http://www.clubengineer.org/robocup.html" target="_default">Club Engineer</a>.
          You can download the source 
          <a href="http://www.clubengineer.org/downloads/competition-entry-software" target="_default"> here</a>. 
		  <a href="http://www.google.com.au" target="_blank">Click here to view the Terms of Use and Privacy Policy.</a></p><hr>';          
  }
  */
?>