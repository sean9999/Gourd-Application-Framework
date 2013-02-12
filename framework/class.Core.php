<?php
class Core {
	protected $sqlconnections	= array();
	protected $mongoconnections	= array();
	protected $hdr;
	public function mong($db=NULL) {
		if (is_null($db)) $db = MONGODB_DB;
		if (!isset($this->mongoconnections[$db])) {
			if (is_null(MONGODB_USER)) {
				$conn = new Mongo("mongodb://" . MONGODB_SERVER . ":" . MONGODB_PORT . '/' . $db);
			} else {
				$conn = new Mongo("mongodb://" . MONGODB_USER . ":" . MONGODB_PASSWD . "@" . MONGODB_SERVER . ":" . MONGODB_PORT . '/' . $db);
			}
			
			$this->mongoconnections[$db] = $conn->selectDB($db);
		}
		return $this->mongoconnections[$db];
	}
	public function sql($db=NULL) {
		if (is_null($db)) $db = SQL_DB;		
		if (!isset($this->sqlconnections[$db])) {
			$this->sqlconnections[$db] = new GourdSQL(SQL_HOST,SQL_USER,SQL_PASSWD,$db);
		}
		return $this->sqlconnections[$db];
	}
	public function page() {
		return $this;
	}
	public function head() {
		if ( !is_a($this->hdr,'Header') ) {
			$this->hdr = new Header;
		}
		return $this->hdr;
	}
}
?>