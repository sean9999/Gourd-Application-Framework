<?php
 class Header {
 
	public  $assets		 			 = array();
	private $contents				 = array();
	public  $DoctypeDeclaration		 = '<!DOCTYPE html>';
	public  $title					 = '';
	public	$load_jquery			 = false;
	public  $load_jqueryui			 = false;
	public  $body_id				 = '';
	public  $body_class				 = '';
	public	$has_opengraph_data		 = false;
	public  $lang					 = 'en';
	public  $jquery_location		 = 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js';
	public  $jqueryui_location		 = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js';
	public	$jqueryui_theme_location = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/blitzer/jquery-ui.css';
	public	$combine_css			 = true;
	public  $combine_js				 = true;
	public	$include_html5shiv		 = true;
	public  $manifest				 = false;
	
	public function __construct() {
		if (!defined('BR'))					define('BR',				"\n");
		if (!defined('URLROOT_Z')) 			define('URLROOT_Z',			'http://'.$_SERVER['HTTP_HOST']);
		if (!defined('PATHROOT_Z'))			define('PATHROOT_Z',		str_replace('/web','/z',$_SERVER['DOCUMENT_ROOT']));
		if (!defined('URLROOT_MINIFY'))		define('URLROOT_MINIFY',	'http://cdn1.pakt.ca');
		if (!defined('PATHROOT_MINIFY'))	define('PATHROOT_MINIFY',	'/var/www/domains/pakt.ca/prod/web');
		if (!defined('PATHROOT_CORE'))		define('PATHROOT_CORE',		'/var/www/domains/default/web');
		if (!defined('URLROOT_CORE'))		define('URLROOT_CORE',		'http://core.gourdisgood.com');		
	}
	
	private function add($area,$stuff) {
		if ($stuff) {
			if (!isset($this->assets[$area])) $this->assets[$area] = array();
			$this->assets[$area][] = $stuff;		
		}
	}
	
	public function addmsg($msg,$type='message') {
		if ($msg) {
			if (!isset($this->assets['msgs']))			$this->assets['msgs'] = array();
			if (!isset($this->assets['msgs'][$type]))	$this->assets['msgs'][$type] = array();
			$this->assets['msgs'][$type][] = $msg;
			$this->load_jqueryui();
			$this->addcss('/lib/messages/messages.css');
			$this->addjquery('
				$("[data-role=\'closemessage\']").live("click", function () {
					$(this).closest("[data-role=\'systemmessage\']").slideUp();
					return false;
				});
				if ( !$("div#msgs").length ) {
					$("<div></div>").attr("id","msgs").prependTo( $("body") );
				}
				msgnode = $("div#msgs");
				$("div.systemmessage").prependTo(msgnode);
				$("[data-role=\"systemmessage\"]").slideDown().delay(5000).slideUp();
			');
		}
		return $this;
	}	
	
	public function cleanseassets() {
	
		//	move rawjs and jquery to real file
		if (is_dir(PATHROOT_Z . '/js/raw')) {
			$content = '';
			if (!empty($this->assets['rawjs'])) {
				$content 	= implode(BR,$this->assets['rawjs']);
				unset($this->assets['rawjs']);
			}
			if (!empty($this->assets['jquery'])) {
				$content .= BR . '$(function() { ';
				$content .= implode(BR,$this->assets['jquery']);
				$content .= BR . '});';
				unset($this->assets['jquery']);
			}
			if (strlen($content)) {
				$filename	= md5($content);
				$filepath	= '/js/raw/' . $filename . '.js';
				file_put_contents(PATHROOT_Z . $filepath,$content);
				$this->addjs($filepath);
			}
		}
		//	same for raw css
		if (is_dir(PATHROOT_Z . '/css/raw')) {
			$content = '';
			if (!empty($this->assets['rawcss'])) {
				$content 	= implode(BR,$this->assets['rawcss']);
				unset($this->assets['rawcss']);
				if (strlen($content)) {
					$filename	= md5($content);
					$filepath	= '/css/raw/' . $filename . '.css';
					file_put_contents(PATHROOT_Z . $filepath,$content);
					$this->addcss($filepath);
				}				
			}
		}	
	
		foreach ($this->assets as $areaname => $area) {
			$cleanfyles = array_unique(array_filter($area));
			if (sizeof($cleanfyles)) {
				if (in_array($areaname,array('js','css','jqueryfile'))) {
					$this->assets[$areaname] = array_map('trim',$cleanfyles,array_pad((array)'/',sizeof($cleanfyles),'/'));
				} elseif ($areaname == 'msgs') {
					//	do nuthin
				} else {
					$this->assets[$areaname] = $cleanfyles;
				}
			}
			else unset($this->assets[$areaname]);
		}
		return $this->assets;
	}
	
	public function load_jquery($trueorfalse=true) {
		$this->load_jquery = $trueorfalse;
		return $this;
	}

	public function load_jqueryui($trueorfalse=true) {
		$this->load_jqueryui	= $trueorfalse;
		$this->load_jquery		= true;
		$this->addcss($this->jqueryui_theme_location);
		return $this;
	}
	
	public function setDocType($string) {
		$lcase_shorthand = preg_replace('/\s\s+/', ' ', strtolower($string));
		switch ($lcase_shorthand) {
			case 'html4strict':
			$o = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
			break;
			case 'xhtml1.1':
			$o = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
			break;
			case 'xhtml1.0':
			$o = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			break;
			case 'xhtml1.0 transitional':
			$o = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			break;
			case 'html5':
			$o = '<!DOCTYPE html>';
			break;
			default:
			$o = $string;
			break;
		}
		$this->DoctypeDeclaration = $o;
		return $this;
	}
	
	public function addmeta($stuff) {
		$this->add('meta',$stuff);
		return $this;
	}
	
	public function addtheme($theme) {
		$this->addmeta('<link rel="stylesheet" id="themestyle" href="' . URLROOT_Z . '/themes/'.$theme.'/css/style.css" >');
	}
	
	public function addog($property,$content) {
		$o = '<meta property="og:' . $property . '" content="' . $content .'" />';
		$this->add('meta',$o);
		$this->has_opengraph_data = true;
		return $this;
	}
	
	public function addcss($sheet) {
		$this->add('css',$sheet);
		return $this;
	}
	
	public function addjs($script) {
		$this->add('js',$script);
		return $this;
	}
	
	public function addrawjs($javascript) {
		$this->add('rawjs',$javascript);
		return $this;
	}
	
	public function addrawcss($stuff) {
		$this->add('rawcss',$stuff);
		return $this;
	}
	
	public function addjqueryfile($file) {
		if (strlen(trim($file))) {
			if (parse_url($file,PHP_URL_SCHEME)) {
				$j = '$.getScript("' . $file .'");';
			} else {
				$j = '$.getScript("' . URLROOT_Z . $file .'");';
			}
			$this->add('jquery',$j);
			$this->load_jquery = true;
		}
		return $this;
	}
	
	public function addjquery($stuff) {
		if (strlen(trim($stuff))) {
			$this->add('jquery',$stuff);
			$this->load_jquery = true;
		}
		return $this;
	}
	
	public function addonload($stuff) {
		$this->add('onload',$stuff);
		return $this;
	}
	
	protected function is_external($url) {
		$r = false;
		if (is_scalar($url)) {
			if (strpos($url,'ttp://') !== false)	$r = true;
			if (strpos($url,'ttps://') !== false)	$r = true;		
		}
		return $r;
	}
	
	public function chunkMinFiles($minfyles,$minurl_host) {
		$maxChars	= 200;
		$totalchars = strlen($minurl_host);
		$i			= 0;
		$r			= array();
		foreach ($minfyles as $minfyle) {
			$thisLength = strlen($minfyle);
			$totalchars += $thisLength;
			if ($totalchars > $maxChars) {
				$i++;
				$totalchars = $thisLength;
			}
			$r[$i][] = $minfyle;
		}
		return $r;
	}
	
	public function buildhtml() {
		
		//	css
		if ($this->load_jqueryui) {
			$this->addcss( $this->jqueryui_theme_location );
		}		
		
		$this->cleanseassets();
		
		if ($this->load_jqueryui == true) $this->load_jquery = true;
		
		$o = '';
		$o  .= $this->DoctypeDeclaration . BR;
		
		if ($this->has_opengraph_data) {
		$o .='<html prefix="og: http://ogp.me/ns#">';
		} elseif (strpos($this->DoctypeDeclaration, 'XHTML 1.0 Transitional')) {
		$o .= '<html xmlns="http://www.w3.org/1999/xhtml">';
		} else {
		$o .='<html lang="' . $this->lang .'"';
		
		if ($this->manifest) {
			$o .= ' manifest="'.$this->manifest.'"';
		}
		
		$o .= '>';
		}
		$o .= BR;
		$o .= '<head>';
		$o .= BR;
		$o .= '<title>' . $this->title . '</title>';
		
		if (isset($this->assets['meta'])) {
			foreach ($this->assets['meta'] as $m) {
				$o .= BR;
				$o .= $m;
			}
		}
		
		if ($this->include_html5shiv) {
			$o .= BR;
			$o .= '<!--[if lt IE 9]>';
			$o .= BR;
			$o .= '<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>';
			$o .= BR;
			$o .= '<![endif]-->';
		}
		
		if (isset($this->assets['css'])) {
			$cssfyles = array();
				$minfyles = array();
				$minurl_host = URLROOT_MINIFY . '/' . parse_url(URLROOT_Z,PHP_URL_HOST) . '/';
				foreach ($this->assets['css'] as $fyle) {
					if ($this->is_external($fyle) || !$this->combine_css) {
						if ($this->is_external($fyle)) {
							$cssfyles[] = $fyle;
						} else {
							$cssfyles[] = URLROOT_Z . '/' . $fyle;
						}
					} else {
						$minfyles[] = str_ireplace('/','!',$fyle);
					}
				}
				if (sizeof($minfyles)) {
					$cssfyles[] = $minurl_host . implode(',',$minfyles);
				}
				foreach ($cssfyles as $cssfyle) {
				$o .= BR;
				$o .= '<link rel="stylesheet" href="'.$cssfyle.'" type="text/css" />';
			}
		}
		
		//	inline css
		if (!empty($this->assets['rawcss'])) {
			if (!empty($this->assets['rawcss'])) {
				$o .= BR;
				$o .= '<style type="text/css">';
				foreach ($this->assets['rawcss'] as $rawcss) {
					$o .= BR;
					$o .= $rawcss;	
				}
				$o .= BR;
				$o .= '</style>';
			}		
		}
		if ($this->load_jquery) $o .= BR . '<script type="text/javascript" src="' . $this->jquery_location . '"></script>';
		if ($this->load_jqueryui) {
			$o .= '<script type="text/javascript" src="' . $this->jqueryui_location . '"></script>';
		}
		
		//	js
		if (isset($this->assets['js'])) {
			$jsfyles = array();
			$minurl_fyles = array();
			$minurl_host = URLROOT_MINIFY . '/' . parse_url(URLROOT_Z,PHP_URL_HOST) . '/';
			foreach ($this->assets['js'] as $fyle) {
				if ($this->is_external($fyle)) {
					$jsfyles[] = $fyle;
				} elseif (!$this->combine_js) {
					$jsfyles[] = URLROOT_Z . '/' . $fyle;
				} else {
					$minurl_fyles[] = str_ireplace('/','!',$fyle);
				}	
			}
			if (sizeof($minurl_fyles)) {
				$chunks = $this->chunkMinFiles($minurl_fyles,$minurl_host);
				foreach ($chunks as $fylez) {
					$jsfyles[] = $minurl_host . implode(',',$fylez);	
				}
			}
			foreach ($jsfyles as $jsfyle) {
				$o .= BR;
				$o .= '<script src="'.$jsfyle.'" type="text/javascript"></script>';
			}
		}
		
		
		if ( !empty( $this->assets['rawjs']) || !empty( $this->assets['jquery'] ) ) {
			$o .= BR;
			$o .= '<script type="text/javascript">';
		}
		
		if ( !empty( $this->assets['rawjs']) ) {
			foreach ($this->assets['rawjs'] as $rj) {
				$o .= BR;
				$o .= '//	rawjs' . BR;
				$o .= $rj;
			}
		}

		if (isset($this->assets['jquery'])) {
			$o .= BR . '//	jquery';
			$o .= BR . '$(function() {';
			foreach ($this->assets['jquery'] as $jq) {
				$o .= BR . "\t";
				$o .= trim($jq);
			}
			$o .= BR . '});';
		}

		if ( !empty( $this->assets['rawjs']) || !empty( $this->assets['jquery'] ) ) {
			$o .=  BR . '</script>';			
		}		
	
		$o .= BR;
		$o .= '</head>';
		$o .= BR;
		
		$o .= '<body';
		if (strlen($this->body_id))		$o .=  ' id="' . $this->body_id . '"';
		if (strlen($this->body_class))	$o .=  ' class="' . $this->body_class . '"';
		$o .= '>';
		$o .= BR;
	
		//	messages
		if (!empty($this->assets['msgs'])) {
		foreach ($this->assets['msgs'] as $type => $msgs) {
			foreach ($msgs as $msg) {
				$o .= '
				<div class="content ' . $type . ' hidden systemmessage" data-role="systemmessage">
					<div class="messageBox">
					<p><strong>' . $type . '!</strong> ' . $msg . '</p>
					</p><div class="clearer"></div>
					</div>
					<div class="closeBox">
					<p><a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button" data-role="closemessage"><span class="ui-icon ui-icon-closethick">close</span></a></p>
					<div class="clearer"></div>		
					</div>
					<div class="clearer"></div>
				</div>';
				}
			}
		$o .= BR;
		}
	$o .= BR;
	return $o;
	}
	
	public function display() {
		$html = $this->buildhtml();
		echo $html;
	}
	
	public function spill() {
		$html = $this->buildhtml();
		return $html;
	}
	
	public function display_pretty() {
		$html = $this->buildhtml();
		echo $this->prettify($html);
	}
	
	public function prettify($html) {
		$tidy	= new tidy;
		$config	= array(
			'indent'         	=> true,
			'new-inline-tags'	=> 'header,canvas,article',
			'output-xhtml'		=> true,
			'indent-attributes'	=> false,
			'wrap-attributes'	=> false,
			'indent-spaces'		=> 4,
			'tab-size'			=> 4,
			'wrap'				=> 0,
			'output-bom'		=> false,
			'doctype'			=> 'omit',
			'markup'			=> true
		);
		$tidy->parseString($html, $config, 'utf8');
		$tidy->cleanRepair();
		$harr	= explode('<!-- /hdr -->',$tidy);
		$hdr	= $this->DoctypeDeclaration . "\n" . $harr[0];
		return $hdr;
	}
	
	public function dbg() {
		$html = $this->buildhtml();
		//$prettyhtml = $this->prettify($html);
		$prettyhtml = $html;
		$o = '<pre>' . htmlspecialchars($prettyhtml) . '</pre>';
		$o2 = '<pre>' . var_export($this->assets) . '</pre>';
		echo $o . $o2;
	}			
}

?>