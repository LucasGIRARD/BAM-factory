<?php
ini_set('max_execution_time', '300');
$path = ini_get('include_path').':/srv/data/web/vhosts/factory.cbam.fr/htdocs/pear';

ini_set('include_path', $path);

function xcopy($source, $dest, $permissions = 0755)
{

    // Check for symlinks
    if (is_link($source)) {
        //return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        //return true;
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        @mkdir($dest, $permissions, true);
    }

    // Loop through the folder
    $dir = dir($source);

    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}


require_once 'XML/RPC2/Client.php';
/*
require_once 'pear/Net/URL2.php';
require_once 'pear/HTTP/Request2/Exception.php';
require_once 'pear/HTTP/Request2.php';
*/

$apikey = 'GH9k6WgkNrWkehpBhXApGrEr';
$siteName = 'test2';
$domain = 'cbam.fr';
$done = false;

$paas_api = XML_RPC2_Client::create(
    'https://rpc.gandi.net/xmlrpc/',
    array( 'prefix' => 'paas.', 'sslverify' => false )
);

$domain_webredir_api = XML_RPC2_Client::create(
    'https://rpc.gandi.net/xmlrpc/',
    array( 'prefix' => 'domain.webredir.', 'sslverify' => True )
);

$operation_api = XML_RPC2_Client::create(
    'https://rpc.gandi.net/xmlrpc/',
    array( 'prefix' => 'operation.', 'sslverify' => True )
);

/*HEBERGEMENT*/


$result = $paas_api->__call("list", $apikey);
echo $paas_id = $result[0]['id'];
echo '<pre>';
print_r($result);
echo '</pre>';


/*SITE*/
exit();
$resultLS = $paas_api->__call("vhost.list", [$apikey,['items_per_page' => 500]]);
$liste = array_column($resultLS, 'name');

if (!in_array('www.'.$siteName.'.'.$domain,$liste)) {
    $param = ['paas_id' => 115793,'vhost' => 'www.'.$siteName.'.'.$domain,'zone_alter' => true, 'override' => true];
    $resultS = $paas_api->__call("vhost.create", [$apikey,$param]);
} else {
    foreach ($resultLS as $value) {
        if ($value['name'] == 'www.'.$siteName.'.'.$domain) {
            $resultS[0]['eta'] ='';
            $resultS[0]['id'] = '';
            $done = true;
            break;
        }
    }
}

/*
echo '<pre>';
print_r($resultLS);
echo '</pre>';
*/

/*
echo '<pre>';
print_r($resultS);
echo '</pre>';
*/

/*REDIRECTION*/
$resultLR = $domain_webredir_api->__call("list", [$apikey, $domain]);
$liste = array_column($resultLR, 'host');
if (!in_array($siteName,$liste)) {
    $webredir_specs = array(
        'host' => $siteName,
        'url' => 'http://www.'.$siteName.'.'.$domain,
        'type' => 'http301'
    );
    $resultR = $domain_webredir_api->__call('create',[$apikey, $domain, $webredir_specs]);
}


/*
echo '<pre>';
print_r($resultLR);
echo '</pre>';
*/

/*
echo '<pre>';
print_r($resultR);
echo '</pre>';
*/

/*VERIFY SITE*/


$time = $resultS[0]['eta'] + 4;
$paramOperation = $resultS[0]['id'];
while ($done == false) {
    sleep($time);
    $result = $operation_api->__call("info", [$apikey,$paramOperation]);
    if ($result['step'] == 'DONE') {
        $done = true;
    } else {
        $time = 30;
    }
}

/*
echo '<pre>';
print_r($result);
echo '</pre>';
*/

/*MOVE*/
xcopy('vendors','../../www.'.$siteName.'.'.$domain.'/htdocs/vendors');

echo 'ok';
?>