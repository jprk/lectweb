<?php
// Test ldap connection.

$ldap_host='ldaps://fdnode1.fd.cvut.cz';
$ldap_basedn='o=fadop';
$ldap_use_start_tls=false;
//$ldap_binddn='uid=prikrjan,dn=fd.cvut.cz';
$ldap_binddn='prikrjan';
//$ldap_binddn='xflegl';
//$ldap_bindpw='35fc3l3kw';
//$ldap_binddn='';

ini_set('html_errors','off');
header('Content-Type: text/plain');

ldap_set_option ( NULL, LDAP_OPT_DEBUG_LEVEL, 7 );

echo "LDAP query test\n";
echo "Connecting ...\n";
$ds=ldap_connect($ldap_host);  // must be a valid LDAP server!
echo "connect result is " . $ds . "\n";

if ($ds) {
    if (ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
        echo "Using LDAPv3\n";
    } else {
        echo "Failed to set protocol version to 3\n";
    }

    // do not use TLS in case of secure LDAP connection
    if ($ldap_use_start_tls) {
        echo "Enabling StartTLS\n";
        echo "Result is ";
        var_dump(ldap_start_tls($ds));
    }

    $searchString = "cn=$ldap_binddn";
    echo "Search `$searchString` before bind ...\n";
    $res  = ldap_search ( $ds, $ldap_basedn, $searchString, array("uniqueMember"));
    //$res  = ldap_search ( $ds, $ldap_basedn, "cvutid=353398", array("uniqueMember"));
    //$res  = ldap_search ( $ds, $ldap_basedn, "cvutid=353398");
    if ( !$res )
    {
    	$errno = ldap_errno ( $ds );
    	$estr  = ldap_err2str ( $errno );
    	die ( 'LDAP error when searching cvutid=xxx: ' . $estr );	
    }
    
    print_r ( $res );
    echo "\nNumber of entires returned is " . ldap_count_entries($ds, $res) . "\n";
    echo "Getting entries ...\n";
    $info = ldap_get_entries($ds, $res);
    print_r ( $info );
    echo "\n";
    
    echo "Binding";
    $r = NULL;
    if ($ldap_binddn!='') {
        echo " with authenticated bind ...\n";
        $r = ldap_bind($ds,$info[0]['dn'],$ldap_bindpw);
    } else {
        echo " with anonymous bind ...\n";
        $r=ldap_bind($ds);
    }
    if ( $r )
    {
    	echo "Bind succesful\n";
    	print_r ( $r );
    }
    else
    {
    	echo "Bind failed\n";
    	$errno = ldap_errno ( $ds );
    	$estr  = ldap_err2str ( $errno );
    	echo "  errno = $errno\n";
    	echo "  estr  = $estr\n";
    }
    

    echo "Searching for (cn=*) ...\n";
    // Search surname entry
    $sr=ldap_search($ds, $ldap_basedn, "cn=prikr*"); 
    echo "Search result is " . $sr . "\n";

    echo "Number of entires returned is " . ldap_count_entries($ds, $sr) . "\n";

    echo "Getting entries ...\n";
    $info = ldap_get_entries($ds, $sr);
    echo "Data for " . $info["count"] . " items returned:\n\n";
    print_r ( $info );
    echo "\n";
    
    for ($i=0; $i<$info["count"]; $i++) {
        echo "dn is: " . $info[$i]["dn"] . "\n";
        echo "first cn entry is: " . $info[$i]["cn"][0] . "\n";
        echo "first email entry is: " . $info[$i]["mail"][0] . "\n------\n";
    }

    echo "Closing connection\n";
    ldap_close($ds);

} else {
    echo "Unable to connect to LDAP server\n";
}
?>
