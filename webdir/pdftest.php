<?php
/* This is where the data files are. Adjust as necessary. */
$pdfinput = "/var/cms/msp/cmsfiles/012029s_ma_kombi_povinna_2010.pdf";
//$pdfinput = "/var/cms/msptest/cmsfiles/003580s_FM_MSP.ppt";
$ret = exec ( "pdftk $pdfinput output /dev/null 2>&1", $output, $retval );
echo 'Operation result: *'.$ret.'*<br/>';
echo 'Output: '; print_r ( $output ); echo '*<br/>';
echo 'Retval: *'.$retval.'*';
?>
