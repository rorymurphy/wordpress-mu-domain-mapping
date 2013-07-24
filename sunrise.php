<?php
if ( !defined( 'SUNRISE_LOADED' ) )
{ define( 'SUNRISE_LOADED', 1 ); }

if( !defined( 'AUTHORING_DOMAIN' ) )
{ define( 'AUTHORING_DOMAIN', DOMAIN_CURRENT_SITE ); }

if ( defined( 'COOKIE_DOMAIN' ) ) {
	die( 'The constant "COOKIE_DOMAIN" is defined (probably in wp-config.php). Please remove or comment out that define() line.' );
}

// let the site admin page catch the VHOST == 'no'
$wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
$dm_domain = $wpdb->escape( $_SERVER[ 'HTTP_HOST' ] );

if( ( $nowww = preg_replace( '|^www\.|', '', $dm_domain ) ) != $dm_domain )
	$where = $wpdb->prepare( 'domain IN (%s,%s)', $dm_domain, $nowww );
else
	$where = $wpdb->prepare( 'domain = %s', $dm_domain );

//
// RMURPHY customization to allow both sub-domains and sub-directories
//

function __isAuthoringDomain($domain){
    return strncmp(AUTHORING_DOMAIN, $domain, strlen(AUTHORING_DOMAIN)) == 0;
}

//Ensure that this isn't the authoring domain, in which case we don't need to
//map domains
if( !__isAuthoringDomain($host) ){
    $uri = $_SERVER['REQUEST_URI'];
    $host = $_SERVER['HTTP_HOST'];    
    $rows = $wpdb->get_results("SELECT * FROM {$wpdb->dmtable} WHERE domain LIKE '{$host}%' ORDER BY LENGTH(domain) DESC, active DESC");

    
    foreach($rows as $index=>$map){
        $map->domain = trim($map->domain);
              
        //Checking if this is a sub-directory based mapping, not on the authoring
        //domain
        if(!__isAuthoringDomain($map->domain)
                && strpos($map->domain, '/') !== FALSE){

                $sub_dir = trim(strstr($map->domain, '/'), '/');
                $bad_sub_dir = sprintf('/%1$s', $sub_dir);
                $sub_dir = sprintf('/%1$s/', $sub_dir);
                
                $bad_sub_dir_esc = preg_quote($bad_sub_dir, '/');
                $sub_dir_esc = preg_quote($sub_dir, '/');
                
                $map_domain = substr($map->domain, 0, strpos($map->domain, '/'));
                $map_domain = strtolower(trim($map_domain, '/'));
                
                $req_domain = strtolower(trim($host, '/'));
                
                if($map_domain == $req_domain && preg_match("/^$sub_dir_esc/", $uri)){
                    global $override_domain;
                    $override_domain = rtrim($map->domain, '/') . '/';
                    $domain_mapping_id = $map->blog_id;
                    break;
                }else if($map_domain == $req_domain && preg_match("/^$bad_sub_dir_esc$/i", $uri)){
                    $isHttps = array_key_exists('HTTPS', $_SERVER);
                    $uri = 'http' . $isHttps?'s':'' . '://' . $host . preg_replace("/^$bad_sub_dir_esc$/i", $sub_dir, $uri);
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $uri);
                    die();
                }else if($map_domain == $req_domain && preg_match("/^$sub_dir_esc/i", $uri)){
                    $isHttps = array_key_exists('HTTPS', $_SERVER);
                    $uri = 'http' . ($isHttps?'s':'') . '://' . $host . preg_replace("/^$sub_dir_esc$/i", $sub_dir, $uri);
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $uri);
                    die();
                }            
        }
    }
}


//
// RMURPHY end customization
//



$wpdb->suppress_errors();
// RMURPHY begin customization - wrapping this in an if block to check if $domain_mapping_id was already set up above
if(!isset($domain_mapping_id)){
    $domain_mapping_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->dmtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" );
}
// RMURPHY - end customization
$wpdb->suppress_errors( false );
if( $domain_mapping_id ) {
	$current_blog = $wpdb->get_row("SELECT * FROM {$wpdb->blogs} WHERE blog_id = '$domain_mapping_id' LIMIT 1");
	$current_blog->domain = $_SERVER[ 'HTTP_HOST' ];
//
// RMURPHY begin customization
        if($override_domain){
            $current_blog->path = substr($override_domain, strpos($override_domain, '/'));
        }else{
            $current_blog->path = '/';
        }
//
// RMURPHY end customization
//
	$blog_id = $domain_mapping_id;
	$site_id = $current_blog->site_id;

	define( 'COOKIE_DOMAIN', $_SERVER[ 'HTTP_HOST' ] );

	$current_site = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$current_blog->site_id}' LIMIT 0,1" );
	$current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
	if( function_exists( 'get_current_site_name' ) )
		$current_site = get_current_site_name( $current_site );

	define( 'DOMAIN_MAPPING', 1 );
}
?>
