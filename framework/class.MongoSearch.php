<?php
Class MongoSearch extends MongoCollection {
	
	public $limit				 = 5000;
	public $skip				 = 0;
	protected $lang				 = 'en';
	protected $rawq 			 = '';
	protected $stopwords_english = 'a,able,about,across,after,all,almost,also,am,among,an,and,any,are,as,at,be,because,been,but,by,can,cannot,could,dear,did,do,does,either,else,ever,every,for,from,get,got,had,has,have,he,her,hers,him,his,how,however,i,if,in,into,is,it,its,just,least,let,like,likely,may,me,might,most,must,my,neither,no,nor,not,of,off,often,on,only,or,other,our,own,rather,said,say,says,she,should,since,so,some,than,that,the,their,them,then,there,these,they,this,tis,to,too,twas,us,wants,was,we,were,what,when,where,which,while,who,whom,why,will,with,would,yet,you,your';
	protected $map_js			 = '';
	protected $reduce_js 		 = "";
	protected $out				 = 'output_collection';
	protected function normalizeString($i) {
		$o 			= $i;
		$a 			= array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
		$b 			= array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$o			= htmlspecialchars_decode($o);
		$o 			= str_replace($a, $b, $o);
	    $o			= preg_replace('/\W/', ' ', $o);
	    return trim($o);
	}
	
	public function map($code) {
		$this->map_js = $code;
		return $this;
	}
	
	public function reduce($code) {
		$this->reduce_js = $code;
		return $this;
	}
	
	public function lang($lang) {
		$this->lang = $lang;
		return $this;
	}
	
	protected function goodWords() {
		return array_values(
			array_diff(
				preg_split('/[\W]+/', strtolower($this->normalizeString($this->rawq))),
				explode(',',$this->stopwords_english))
		);
	}
	
	public function execute($rawq) {
		$this->rawq 	= $rawq;
		$goodwords = $this->goodWords();
		$this->out		= 'mapReduceSearch_' . $this->db->__toString() . '_' . $this->getName() . '_' . md5(implode('|',$goodwords));
		//$this->out		= md5 ( implode('~'$goodwords) . '~' . $this->getName() . '~' . $this->db->__toString() );
		
		$fromCache = true;
		
		if (!$this->db->selectCollection($this->out)->count()) {
			$this->map_js 	= str_ireplace('{%rawquery%}', addslashes($this->rawq), $this->map_js);
			$this->map_js 	= str_ireplace('{%goodwords%}', json_encode($goodwords), $this->map_js);
			$this->map_js 	= str_ireplace('{%lang%}', $this->lang, $this->map_js);
			$map			= new MongoCode($this->map_js);
			$reduce 		= new MongoCode($this->reduce_js);
			$x				= $this->db->command(array(
				"mapreduce" => $this->getName(), 
				"map"		=> $map,
				"reduce"	=> $reduce,
				'verbose' 	=> true,
				'out'		=> $this->out
				)
			);
			$fromCache = false;
		}		
		$y	 = $this->db->selectCollection($this->out)->find(array('value.score' => array('$gt' => 0)))->sort(array('value.score' => -1))->limit($this->limit)->skip($this->skip);
		$out = $this->out;
		$searchresults = $this->db->$out->find()->sort(array('value.score' => -1));
		$niceresults = array();
		while ($row = $searchresults->getNext()) {
			$niceresults[] = array('doc' => $row['_id']->{'$id'}, 'score' => $row['value']['score'] );
		}
		$r2 = array(
			'count'		=> $y->count(false),
			'found'		=> $y->count(true),
			'limit'		=> $this->limit,
			'skip'		=> $this->skip,
			//'mapReduce'	=> $x,
			//'results'	=> $niceresults,
			'collection'=> $this->getName(),
			'rawq'		=> $this->rawq,
			'goodwords'	=> $goodwords,
			'fromCache'	=> $fromCache
		);
		$r1 = $niceresults;
		return array(
			'r'			=> $r1,		
			'_meta'		=> $r2
		);
	}
}
?>