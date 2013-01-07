<?php

class GourdSQL extends mysqli {

	public function good_query_assoc($sql) {
		$result = $this->query($sql);		
		if (mysqli_error($this)) {
			return $result->error . ' [ ' . $sql . ' ]';
		} else {
			$r = $result->fetch_assoc();
			$result->free();
			return $r;
		}
	}

	public function good_query_table($sql) {
		$r = array();
		$result = $this->query($sql);
		if (mysqli_error($this)) {
			return $this->error . ' [ ' . $sql . ' ]';
		} else {
			while ($row = $result->fetch_assoc()) {
	        	$r[] = $row;
			}
			$result->free();
			return $r;
		}
	}
	
	public function good_query_vertical($sql) {
		$r = array();
		$result = $this->query($sql);
		if (!$this->error) {
			while ($fields = $result->fetch_array(MYSQLI_NUM)) {
				$r[] = $fields[0];
			}
		}
		$result->free();
		return $r;		
	}
	
	public function good_query_value($sql) {
		$r		= false;
		$result = $this->query($sql);
		//if (mysqli_error($this)) {
		if (!$this->error) {
			$fields = $result->fetch_array(MYSQLI_NUM);
			$r		= $fields[0];
			$result->free();
		}
		return $r;
	}
	
	public function good_query($sql) {
		$r = false;
		$result = $this->query($sql);
		if (mysqli_error($this)) {
			return $this->error . ' :: <pre>' . htmlspecialchars($sql) . '</pre>'; 
		} else {
			return $result;
		}
	}

}

?>