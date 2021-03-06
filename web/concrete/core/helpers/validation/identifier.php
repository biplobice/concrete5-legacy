<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * A helper that allows the creation of unique strings, for use when creating hashes, identifiers.
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class Concrete5_Helper_Validation_Identifier {

	/**
	 * Like generate() below, but simply appends an ever increasing number to what you provide
	 * until it comes back as not found
	 */
	public function generateFromBase($string, $table, $key) {
		$foundRecord = false;
		$db = Loader::db();
		$i = '';
		while ($foundRecord == false) {
			$_string = $string . $i;
			$cnt = $db->GetOne("select count(" . $key . ") as total from " . $table . " where " . $key . " = ?", array($_string));
			if ($cnt < 1) {
				$foundRecord = true;
			} else {
				if ($i == '') {
					$i = 0;
				}
				$i++;
			}
		}
		return $_string;
	}

	/**
	 * Generates a unique identifier for an item in a database table. Used, among other places, in generating
	 * User hashes for email validation
	 * @param string table
	 * @param string key
	 * @param int length
	 */
	public function generate($table, $key, $length = 12, $lowercase = false) {
		$foundHash = false;
		$db = Loader::db();
		while ($foundHash == false) {
			$string = $this->getString($length);
			if ($lowercase) {
				$string = strtolower($string);
			}
			$cnt = $db->GetOne("select count(" . $key . ") as total from " . $table . " where " . $key . " = ?", array($string));
			if ($cnt < 1) {
				$foundHash = true;
			}
		}
		return $string;
	}

    /**
     * Generate a cryptographically secure random string
     * @param int $length
     * @return string
     */
    public function getString($length = 12)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length / 2);
        } else {
            Loader::library('3rdparty/phpass/PasswordHash');
            $hash = new PasswordHash(8, false);
            $bytes = $hash->get_random_bytes($length / 2);
        }

        return bin2hex($bytes);
    }

	public function deleteKey($table, $keyCol, $uHash){
		$db = Loader::db();
		$db->Execute("DELETE FROM ".$table." WHERE ".$keyCol."=?", array($uHash) );
	}

}
