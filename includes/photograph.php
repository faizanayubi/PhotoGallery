<?php
require_once(LIB_PATH.DS.'database.php');

/**
* Photograph
*/
class Photograph extends DatabaseObject {
	protected static $table_name = "photographs";
	public $id;
	public $filename;
	public $type;
	public $size;
	public $caption;

	private $temp_path;
	protected $upload_dir="images";
	public $errors = array();
	protected static $db_fields=array('id', 'filename', 'type', 'size', 'caption');
	protected $upload_errors = array(
		UPLOAD_ERR_OK 			=> "No errors.",
		UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
		UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
		UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
		UPLOAD_ERR_NO_FILE 		=> "No file.",
		UPLOAD_ERR_NO_TMP_DIR 	=> "No temporary directory.",
		UPLOAD_ERR_CANT_WRITE 	=> "Can't write to disk.",
		UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
		);
	
	public function attach_file($file) {
		if (!$file || empty($file) || !is_array($file)) {
			$this->errors[] = "No file was uploaded.";
			return false;
		} elseif ($file['error'] != 0) {
			$this->errors[] = $this->upload_errors[$file['error']];
			return false;
		} else {
			$this->temp_path = $file['tmp_name'];
			$this->filename  = basename($file['name']);
			$this->type 	 = $file['type'];
			$this->size 	 = $file['size'];
			return true;
		}
	}

	public function save() {
		if (isset($this->id)) {
			$this->update();
		} else {
			if (!empty($this->errors)) { return false;}
			if (strlen($this->caption) > 255) {
				$this->errors[] = "The caption can only be 255 characters long.";
				return false;
			}
			if (empty($this->filename) || empty($this->temp_path)) {
				$this->errors[]= "The file location was not available.";
				return false;
			}

			$target_path = SITE_ROOT .DS. 'public' .DS. $this->upload_dir .DS.$this->filename;
			if (file_exists($target_path)) {
				$this->errors[] = "The file {$this->filename} already exists.";
				return false;
			}
			if (move_uploaded_file($this->temp_path, $target_path)) {
				if ($this->create()) {
					unset($this->temp_path);
					return true;
				}
			} else {
				$this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
				return false;
			}
		}
	}

	public function destroy() {
		if ($this->delete()) {
			$target_path = SITE_ROOT.DS.'public'.DS.$this->image_path();
			return unlink($target_path) ? true : false;
		} else {
			return false;
		}
		
	}

	public function image_path() {
		//image_path() = ../images/filename 	../upload_dir/$this->filename
		return $this->upload_dir.DS.$this->filename;
	}

	public function size_as_text(){
		if ($this->size <1024) {
			return "{$this->size} bytes";
		} elseif ($this->size <1048576) {
			$size_kb = round($this->size/1024);
			return "{$size_kb} KB";
		} else {
			$size_mb = round($this->size/1048576);
			return "{$size_mb} MB";
		}	
	}

	public function comments() {
		return Comment::find_comments_on($this->id);
	}

	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
	}

	public static function find_by_id($id=0) {
		global $database;
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id =".$database->escape_value($id)." LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}

	public static function find_by_sql($sql="") {
		global $database;
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

	private static function instantiate($record) {
		$object = new self;
		// $object->username  = $record['username'];
		// $object->firstname = $record['first_name'];
		// $object->lastname  = $record['last_name'];

		foreach ($record as $attribute => $value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	private function has_attribute($attribute) {
		$object_vars = $this->attributes();
		return array_key_exists($attribute, $object_vars);
	}

	protected function attributes() {
		$attributes = array();
		foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}

	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}

	protected function create() {
		global $database;
		$attributes = $this->sanitized_attributes();
		$sql  = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";

		if ($database->query($sql)) {
			$this->id = $database->insert_id();
			return true;
		} else {
			return false;
		}
	}

	protected function update() {
		global $database;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach ($attribute_pairs as $key => $value) {
			$attribute_pairs[] = "{$key} = '{$value}'";
		}
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id = ".$database->escape_value($this->id);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	protected function delete() {
		global $database;
		$sql  = "DELETE FROM ".self::$table_name;
		$sql .= " WHERE id = ".$database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;	
	}
}

?>