<?php if(!defined('IN_RAT')){ die('you cannot load this page directly.'); }
/**
 * Theme Functions
 *
 * These functions are used within the front-end of a RAT6 installation
 *
 * @link http://www.facebook.com/pages/RAT6/196237143792761/docs/theme-codex/
 *
 * @package RAT6
 * @subpackage Theme-Functions
 */

/**
 * Get Page Content
 *
 * @since 1.0
 * @uses $content 
 * @uses exec_action
 * @uses exec_filter
 * @uses strip_decode
 *
 * @return string Echos.
 */
function get_page_content() {
	global $content;
	exec_action('content-top');
	$content = strip_decode($content);
	$content = exec_filter('content',$content);
	echo $content;
	exec_action('content-bottom');
}

/**
 * Get Page Excerpt
 *
 * @since 2.0
 * @uses $content
 * @uses exec_filter
 * @uses strip_decode
 *
 * @param string $n Optional, default is 200.
 * @param bool $html Optional, default is false.  
 * 				If this is true, it will strip out html from $content
 * @return string Echos.
 */
function get_page_excerpt($n=200, $html=false) {
	global $content;
	$content_e = strip_decode($content);
	$content_e = exec_filter('content',$content_e);
	
	if (!$html) {
		$content_e = strip_taRAT($content_e);
	}
	
	if (function_exists('mb_substr')) { 
		$content_e = trim(mb_substr($content_e, 0, $n)) . '...';
	} else {
		$content_e = trim(substr($content_e, 0, $n)) . '...';
	}

	echo $content_e;
}

/**
 * Get Page Meta Keywords
 *
 * @since 2.0
 * @uses $metak
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_keywords($echo=true) {
	global $metak;
	$myVar = strip_decode($metak);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Meta Description
 *
 * @since 2.0
 * @uses $metad
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_desc($echo=true) {
	global $metad;
	$myVar = strip_decode($metad);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Title
 *
 * @since 1.0
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_title($echo=true) {
	global $title;
	$myVar = strip_decode($title);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Clean Title
 *
 * This will remove all HTML from the title before returning
 *
 * @since 1.0
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_clean_title($echo=true) {
	global $title;
	$myVar = strip_taRAT(strip_decode($title));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Slug
 *
 * This will return the slug value of a particular page
 *
 * @since 1.0
 * @uses $url
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_slug($echo=true) {
	global $url;
	$myVar = $url;
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Parent Slug
 *
 * This will return the slug value of a particular page's parent
 *
 * @since 1.0
 * @uses $parent
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_parent($echo=true) {
	global $parent;
	$myVar = $parent;
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Date
 *
 * This will return the page's updated date/timestamp
 *
 * @since 1.0
 * @uses $date
 * @uses $TIMEZONE
 *
 * @param string $i Optional, default is "l, F jS, Y - g:i A"
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_date($i = "l, F jS, Y - g:i A", $echo=true) {
	global $date;
	global $TIMEZONE;
	if ($TIMEZONE != '') {
		if (function_exists('date_default_timezone_set')) {
			date_default_timezone_set($TIMEZONE);
		}
	}
	
	$myVar = date($i, strtotime($date));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Page Full URL
 *
 * This will return the full url
 *
 * @since 1.0
 * @uses $parent
 * @uses $url
 * @uses $SITEURL
 * @uses $PRETTYURLS
 * @uses find_url
 *
 * @param bool $echo Optional, default is false. True will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_url($echo=false) {
	global $url;
	global $SITEURL;
	global $PRETTYURLS;
	global $parent;

	if (!$echo) {
		echo find_url($url, $parent);
	} else {
		return find_url($url, $parent);
	}
}

/**
 * Get Page Header HTML
 *
 * This will return header html for a particular page. This will include the 
 * meta desriptions & keywords, canonical and title taRAT
 *
 * @since 1.0
 * @uses exec_action
 * @uses get_page_url
 * @uses strip_quotes
 * @uses get_page_meta_desc
 * @uses get_page_meta_keywords
 * @uses $metad
 * @uses $title
 * @uses $content
 * @uses $site_full_name from configuration.php
 * @uses RATADMININCPATH
 *
 * @return string HTML for template header
 */
function get_header($full=true) {
	global $metad;
	global $title;
	global $content;
	include(RATADMININCPATH.'configuration.php');
	
	if (function_exists('mb_substr')) { 
		$description = trim(mb_substr(strip_taRAT(strip_decode($content)), 0, 160));
	} else {
		$description = trim(substr(strip_taRAT(strip_decode($content)), 0, 160));
	}
	
	if ($metad != '') {
		$description = get_page_meta_desc(FALSE);
	} else {
		$description = str_replace('"','', $description);
		$description = str_replace("'",'', $description);
		$description = preg_replace('/\n/', " ", $description);
		$description = preg_replace('/\r/', " ", $description);
		$description = preg_replace('/\t/', " ", $description);
		$description = preg_replace('/ +/', " ", $description);
	}
	
	$keywords = get_page_meta_keywords(FALSE);
	
	echo '<meta name="description" content="'.encode_quotes($description).'" />'."\n";
	echo '<meta name="keywords" content="'.encode_quotes($keywords).'" />'."\n";
	if ($full) {
		echo '<meta name="generator" content="'. $site_full_name .'" />'."\n";
		echo '<link rel="canonical" href="'. get_page_url(true) .'" />'."\n";
	}
	exec_action('theme-header');
}

/**
 * Get Page Footer HTML
 *
 * This will return footer html for a particular page. Right now
 * this function only executes a plugin hook so developers can hook into
 * the bottom of a site's template.
 *
 * @since 2.0
 * @uses exec_action
 *
 * @return string HTML for template header
 */
function get_footer() {
	exec_action('theme-footer');
}

/**
 * Get Site URL
 *
 * This will return the site's full base URL
 * This is the value set in the control panel
 *
 * @since 1.0
 * @uses $SITEURL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_url($echo=true) {
	global $SITEURL;
	
	if ($echo) {
		echo $SITEURL;
	} else {
		return $SITEURL;
	}
}

/**
 * Get Theme URL
 *
 * This will return the current active theme's full URL 
 *
 * @since 1.0
 * @uses $SITEURL
 * @uses $TEMPLATE
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_theme_url($echo=true) {
	global $SITEURL;
	global $TEMPLATE;
	$myVar = trim($SITEURL . "theme/" . $TEMPLATE);
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Site's Name
 *
 * This will return the value set in the control panel
 *
 * @since 1.0
 * @uses $SITENAME
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_name($echo=true) {
	global $SITENAME;
	$myVar = trim(stripslashes($SITENAME));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Administrator's Email Address
 *
 * This will return the value set in the control panel
 *
 * @since 1.0
 * @uses $EMAIL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_email($echo=true) {
	global $EMAIL;
	$myVar = trim(stripslashes($EMAIL));
	
	if ($echo) {
		echo $myVar;
	} else {
		return $myVar;
	}
}

/**
 * Get Installed RAT6 Version
 *
 * This will return the version of RAT6 that is installed
 *
 * @since 1.0
 * @uses RATVERSION
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_version($echo=true) {
	include(RATADMININCPATH.'configuration.php');
	if ($echo) {
		echo RATVERSION;
	} else {
		return RATVERSION;
	}
}

/**
 * Get Site Credits
 *
 * This will return HTML that displays 'Powered by RAT6 X.XX'
 * It will always be nice if developers left this in their templates 
 * to help promote RAT6. 
 *
 * @since 1.0
 * @uses $site_link_back_url from configuration.php
 * @uses $site_full_name from configuration.php
 * @uses RATVERSION
 * @uses RATADMININCPATH
 *
 * @param string $text Optional, default is 'Powered by'
 * @return string 
 */
function get_site_credits($text ='Powered by ') {
	include(RATADMININCPATH.'configuration.php');
	
	$site_credit_link = '<a href="'.$site_link_back_url.'" >'.$text.' '.$site_full_name.'</a>';
	echo stripslashes($site_credit_link);
}

/**
 * Menu Data
 *
 * This will return data to be used in custom navigation functions
 *
 * @since 2.0
 * @uses RATDATAPAGESPATH
 * @uses find_url
 * @uses getXML
 * @uses subval_sort
 *
 * @param bool $xml Optional, default is false. 
 *				True will return value in XML format. False will return an array
 * @return array|string Type 'string' in this case will be XML 
 */
function menu_data($id = null,$xml=false) {
    $menu_extract = '';
    
    $path = RATDATAPAGESPATH;
    $dir_handle = opendir($path) or die("Unable to open $path");
    $filenames = array();
    while ($filename = readdir($dir_handle)) {
        $filenames[] = $filename;
    }
    closedir($dir_handle);
    
    $count="0";
    $pagesArray = array();
    if (count($filenames) != 0) {
        foreach ($filenames as $file) {
            if ($file == "." || $file == ".." || is_dir($path . $file) || $file == ".htaccess"  ) {
                // not a page data file
            } else {
								$data = getXML($path . $file);
                if ($data->private != 'Y') {
                    $pagesArray[$count]['menuStatus'] = $data->menuStatus;
                    $pagesArray[$count]['menuOrder'] = $data->menuOrder;
                    $pagesArray[$count]['menu'] = $data->menu;
                    $pagesArray[$count]['parent'] = $data->parent;
                    $pagesArray[$count]['title'] = $data->title;
                    $pagesArray[$count]['url'] = $data->url;
                    $pagesArray[$count]['private'] = $data->private;
                    $pagesArray[$count]['pubDate'] = $data->pubDate;
                    $count++;
                }
            }
        }
    }
    
    $pagesSorted = subval_sort($pagesArray,'menuOrder');
    if (count($pagesSorted) != 0) { 
      $count = 0;
      if (!$xml){
        foreach ($pagesSorted as $page) {
          $text = (string)$page['menu'];
          $pri = (string)$page['menuOrder'];
          $parent = (string)$page['parent'];
          $title = (string)$page['title'];
          $slug = (string)$page['url'];
          $menuStatus = (string)$page['menuStatus'];
          $private = (string)$page['private'];
					$pubDate = (string)$page['pubDate'];
          
          $url = find_url($slug,$parent);
          
          $specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
          
          if ($id == $slug) { 
              return $specific; 
              exit; 
          } else {
              $menu_extract[] = $specific;
          }
        } 
        return $menu_extract;
      } else {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><channel>';    
	        foreach ($pagesSorted as $page) {
            $text = $page['menu'];
            $pri = $page['menuOrder'];
            $parent = $page['parent'];
            $title = $page['title'];
            $slug = $page['url'];
            $pubDate = $page['pubDate'];
            $menuStatus = $page['menuStatus'];
            $private = $page['private'];
           	
            $url = find_url($slug,$parent);
            
            $xml.="<item>";
            $xml.="<slug><![CDATA[".$slug."]]></slug>";
            $xml.="<pubDate><![CDATA[".$pubDate."]]></pubDate>";
            $xml.="<url><![CDATA[".$url."]]></url>";
            $xml.="<parent><![CDATA[".$parent."]]></parent>";
            $xml.="<title><![CDATA[".$title."]]></title>";
            $xml.="<menuOrder><![CDATA[".$pri."]]></menuOrder>";
            $xml.="<menu><![CDATA[".$text."]]></menu>";
            $xml.="<menuStatus><![CDATA[".$menuStatus."]]></menuStatus>";
            $xml.="<private><![CDATA[".$private."]]></private>";
            $xml.="</item>";
	        }
	        $xml.="</channel>";
	        return $xml;
        }
    }
}

/**
 * Get Component
 *
 * This will return the component requested. 
 * Components are parsed for PHP within them.
 *
 * @since 1.0
 * @uses RATDATAOTHERPATH
 * @uses getXML
 *
 * @param string $id This is the ID of the component you want to display
 *				True will return value in XML format. False will return an array
 * @return string 
 */
function get_component($id) {
	if (file_exists(RATDATAOTHERPATH.'components.xml')) {
		$data = getXML(RATDATAOTHERPATH.'components.xml');
		$components = $data->item;
		
		if (count($components) != 0) {
			foreach ($components as $component) {
				if ($id == $component->slug) { 
					eval("?>" . strip_decode($component->value) . "<?php "); 
				}
			}
		}
	}
}

/**
 * Get Main Navigation
 *
 * This will return unordered list of main navigation
 * This function uses the menu opitions listed within the 'Edit Page' control panel screen
 *
 * @since 1.0
 * @uses RATDATAOTHERPATH
 * @uses getXML
 * @uses subval_sort
 * @uses find_url
 * @uses strip_quotes 
 * @uses exec_filter 
 *
 * @param string $currentpage This is the ID of the current page the visitor is on
 * @return string 
 */	
function get_navigation($currentpage) {

	$menu = '';

	$path = RATDATAPAGESPATH;
	$dir_handle = opendir($path) or die("Unable to open $path");
	$filenames = array();
	while ($filename = readdir($dir_handle)) {
		$filenames[] = $filename;
	}
	
	$count="0";
	$pagesArray = array();
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			if ($file == "." || $file == ".." || is_dir($path . $file) || $file == ".htaccess"  ) {
				// not a page data file
			} else {
				$data = getXML($path . $file);
				if ($data->private != 'Y') {
					$pagesArray[$count]['menuStatus'] = $data->menuStatus;
					$pagesArray[$count]['menuOrder'] = $data->menuOrder;
					$pagesArray[$count]['menu'] = strip_decode($data->menu);
					$pagesArray[$count]['url'] = $data->url;
					$pagesArray[$count]['title'] = strip_decode($data->title);
					$pagesArray[$count]['parent'] = $data->parent;
					$count++;
				}
			}
		}
	}
	
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	if (count($pagesSorted) != 0) { 
		foreach ($pagesSorted as $page) {
			$sel = ''; $classes = '';
			$url_nav = $page['url'];
			
			if ($page['menuStatus'] == 'Y') { 
				if ("$currentpage" == "$url_nav") { $classes = "current ". $page['parent'] ." ". $url_nav; } else { $classes = trim($page['parent'] ." ". $url_nav); }
				if ($page['menu'] == '') { $page['menu'] = $page['title']; }
				if ($page['title'] == '') { $page['title'] = $page['menu']; }
				$menu .= '<li class="'. $classes .'"><a href="'. find_url($page['url'],$page['parent']) . '" title="'. strip_quotes($page['title']) .'">'.$page['menu'].'</a></li>'."\n";
			}
		}
		
	}
	
	closedir($dir_handle);
	echo exec_filter('menuitems',$menu);
}

	
	
	
	
	
/**
 * @depreciated as of 2.04
 */
function return_page_title() {
	return get_page_title(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_parent() {
	return get_parent(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_page_slug() {
  return get_page_slug(FALSE);
}
/**
 * @depreciated as of 2.04
 */
function return_site_ver() {
	return get_site_version(FALSE);
}	
/**
 * @depreciated as of 2.03
 */
if(!function_exists('set_contact_page')) {
	function set_contact_page() {
		#removed functionality	
	}
}
?>