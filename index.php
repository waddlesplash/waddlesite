<?php
/* WaddleSite - v1.0
 * ===============
 * simple template-based website system
 * (c) 2012-2013 waddlesplash (http://github.com/waddlesplash)
 * MIT license.
 */
require_once('lib/mustache.php');
require_once('config.php');
$WADDLESITE_VER = '1.0';
$MOD_REWRITE = file_exists('.htaccess');

/* slides.js data */
$SLIDES_BEGIN = '<table><tr><td><div class="slides"><table><tr><td><a href="#" class="prev">'.
				'<img class="one" src="data/arrow-prev.png" width="24" height="43" alt="Arrow Prev" />'.
				'<img class="two" src="data/arrow-prev_hover.png" width="24" height="43" alt="Arrow Prev" />'.
				'</a></td><td><div class="slides_container">';

$SLIDES_END = '</div></td><td><a href="#" class="next">'.
			  '<img class="one" src="data/arrow-next.png" width="24" height="43" alt="Arrow Next" />'.
			  '<img class="two" src="data/arrow-next_hover.png" width="24" height="43" alt="Arrow Next" />'.
			  '</a></td></tr></table></div></td></tr></table>';

function fatalError($num,$txt) {
	global $WADDLESITE_VER;
	echo "<title>Error $num</title><h1>$num $txt</h1>Please contact the webmaster!";
	echo '<br/><br/><br/><hr width="30%" align="left" /><i>this site is running on ';
	echo '<a target="_blank" href="https://github.com/waddlesplash/waddlesite/">';
	echo 'waddlesite</a> v'.$WADDLESITE_VER.'</i>';
	exit(1);
}

/* get the page name & check it exists */
if(isset($_GET['p'])) { $p = $_GET['p']; }
else { $p = $DEFAULT_PAGE; }
if($p == 'index') { $p = $DEFAULT_PAGE; }

if(!file_exists("content/$p.page"))
{ fatalError(404,'Page Not Found'); }

/* create header links */
if(!file_exists('content/header.lst')) { 
	fatalError(404,'Header Listing Not Found');
}

if($CUSTOM_HEADER) {
	$headerTemplate = file_get_contents('content/headeritem.template');
}
$h = file_get_contents('content/header.lst');
$h = explode("\n",$h);
$header = '';
foreach($h as $i => $a) {
	$b = explode(';;',$a);
	if(count($b) != 2) { continue; }
	if(!$MOD_REWRITE && (strpos($b[0],'http://') !== 0) 
					 && (strpos($b[0],'https://') !== 0)) {
		$href = '?p='.$b[0];
	} else {
		$href = $b[0];
	}
	if($CUSTOM_HEADER) {
		$data = array('onthis' => ($p == $b[0]),
					  'href' => $href,
					  'text' => $b[1]);
		$m = new Mustache_Engine;
		$header .= $m->render($headerTemplate, $data);
	} else {
		$header .= '<a href="'.$href.'">';
		$header .= $b[1].'</a>';
	}
	if($i != count($h)-1)
	{ $header .= " $HEADER_SPACER "; }
}
$header .= "\n";

/* pre-render page */
$page_dat = file_get_contents("content/$p.page");
$data = array('slides_begin' => $SLIDES_BEGIN,
			  'slides_end' => $SLIDES_END);
$m = new Mustache_Engine;
$page = $m->render($page_dat, $data);

/* render template to page & output */
$template = file_get_contents('content/page.template');
$data = array('title' => ucwords(strtolower($p)),
			  'page_content' => $page,
			  'waddlesite_ver' => $WADDLESITE_VER,
			  'header_links' => $header,
			  'slides_begin' => $SLIDES_BEGIN,
			  'slides_end' => $SLIDES_END);
$m = new Mustache_Engine;
echo $m->render($template, $data);
?>
