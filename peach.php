<?
	/*
	* Peach is a library for making PHP better. It simplifies function names,
	* makes argument order consistent, removes cases where argument order isn't
	* specified, and is generally juicy and delicious.
	*
	* Currently, Peach's new methods are accessed via $Peach->MethodName(). This
	* is clumsier than overriding native functions, but it also means that
	* Peach can be used in existing PHP projects without refactoring.
	*
	* In most cases, Peach is just a thin abstraction over existing PHP
	* functions. The benefit is that it's easier to write code if you don't
	* have to remember whether 
	*/

abstract class Stems {
	const CaseSensitive = true;
	const CaseInsensitive = false;
}

class PeachExceptions {
	static function GenerateTypeException($String, $Expected)
	{
		return 'Type mismatch: argument expected to be of type \''.$Expected.'\', but \''.gettype($String).'\' given.';
	}
}
	
class Peach {
	private $Datatypes;
	public $Value;

	function __CONSTRUCT() {
		$this->Datatypes = array();
	}
	
	function __toString() {
		// Throwing an exception inside of __toString() is not allowed and causes
		// a fatal error... which is fine, because that's the kind of exception
		// we would have raised anyway. Thanks, PHP!
		$this->TypeCheck($this->Value, 'String');
		return $this->Value;
	}
	
	private function TypeCheck($Value, $Types){
		// We store a separate "PHPType" value reflecting PHP's internal type
		// naming so that our exception text can include Peach's improved types
		if (!is_array($Types)) { $Types = array($Types); }
		foreach ($Types as $Type) {
			$PHPType = strtolower($Type); 
			if ($PHPType == "hash") { $PHPType = "array"; }
			$PHPTypes[] = $PHPType;
		}
		if (in_array(gettype($Value), $PHPTypes)) {
			return true;
		} else {
			throw new Exception(PeachExceptions::GenerateTypeException($Value, implode(', ', $Types)));
		}
	}
	
	private function IsRegex($String){
		$this->TypeCheck($Value, 'String');
		$Config = ini_get('track_errors');
		ini_set('track_errors', 'on');
		$Error = '';
		@preg_match($String, '');
		ini_set('track_errors', 'off');
		if ($Error) {
			return false;
		} else {
			return true;
		}
	}
	
	private function Pass() {
		//Passes data by copying the object
		$Pass = clone $this;
		return $Pass;
	}
	
	// Because __toString() is the only magic "getter", only strings can be returned
	// from the Peach object natively without a final, returning function.
	function Get() {
		return $this->Value;
	}
	
	// Base method for array handling. We can't use Array() for our method name
	// because it can't be overridden, but PHP's "arrays" are hashes anyway
	// so let's do that.
	public function Hash($Value){
		$this->TypeCheck($Value, 'Hash');
		$this->Datatypes[] = "hash";
		$this->Value = $Value;
		return $this;
	}
	// Base method for string handling
	public function String($Value){
		$this->TypeCheck($Value, 'String');
		$this->Datatypes[] = "string";
		$this->Value = $Value;
		return $this;
	}
	
		////////////////////
		// STRING METHODS //
		////////////////////
	
		public function Length() {
			$this->TypeCheck($this->Value, 'String');
			$Pass = $this->Pass();
			return strlen($Pass->Value);
		}
	
		public function Substring($Start, $Length = null) {
			// this little dance is necessary because PHP bases substr's behavior
			// on whether or not the Length string is specified at all, and not
			// on whether it's null. In fact, a null/false/0 Length means it'll 
			// return an empty string, which is baffling because I can't imagine
			// any circumstances in which this behavior would be in any way desirable.
			$this->TypeCheck($this->Value, 'String');
			$Pass = $this->Pass();
			if ($Length !== null){
				$Pass->Value = substr($Pass->Value, $Start, $Length);
			} else {
				$Pass->Value = substr($Pass->Value, $Start);
			}
		}
		
		public function Replace($Search, $Replace, $CaseSensitive = Stems::CaseSensitive, $Count = null){
			// This method replaces both str_replace and str_ireplace with a
			// single, switchable method call.
			$this->TypeCheck($this->Value, 'String');
			$Pass = $this->Pass();
			if ($CaseSensitive == Stems::CaseSensitive) {
				$Pass->Value = str_replace($Search, $Replace, $Pass->Value, $Count);
			} else {
				$Pass->Value = str_ireplace($Search, $Replace, $Pass->Value, $Count);
			}
			
			return $Pass;
		}
		
		public function Contains($Search, $CaseSensitive = Stems::CaseSensitive, $Offset = 0) {
				$Pass = $this->Pass();
				if (in_array('hash', $Pass->Datatypes)) {
					return $Pass->Array_Contains($Search, $CaseSensitive);
				}
				
				$this->TypeCheck($this->Value, 'String');
				if ($CaseSensitive == Stems::CaseSensitive) {
					return strpos($Pass->Value, $Search, $Offset);
				} else {
					return strpos($Pass->Value, $Search, $Offset);
				}
		}
		
		public function Uppercase()
		{
			$this->TypeCheck($this->Value, 'String');
			$Pass = $this->Pass();
			$Pass->Value = strtoupper($Pass->Value);
			return $this;
		}
		
		public function Lowercase()
		{
			
			$Pass->Value = strtoupper($Pass->Value);
			return $this;
		}
		
		public function Split($By) {
		// Splits the string into an array by the $By variable. If $By is an integer,
		// the string will be split into pieces of that size. If $By is a string,
		// it will split into pieces using that string as a delimiter.
			$this->TypeCheck($this->Value, array('string', 'integer'));
			$Pass = $this->Pass();
			
			if (is_numeric($By)){
				$Pass->Value = str_split($Pass->Value, $By);
			} else if (is_string($By)) {
				$Pass->Value = explode($By, $Pass->Value);
			}
			
			$Pass->Datatypes = array("hash");
			
			return $Pass;
		}
		
		//////////////////
		// HASH METHODS //
		//////////////////
		
		public function Join($Glue = ''){
		// Glues an array together into a string with $Glue used as a delimiter.
		// By default, it joins with an empty string.
		
			$this->TypeCheck($this->Value, array('hash'));
			$Pass = $this->Pass();
		
			$Pass->Value = implode($Glue, $Pass->Value);

			$Pass->Datatypes = array("string");
			return $Pass;
		}
		
		public function Array_Contains($Search, $CaseSensitive){
		// Searches an array to see if it contains the string provided. I can't
		// implement namespaces within Peach without screwing up the calling
		// syntax so the string Contains() method contains logic to call this
		// method if it's given an array. Please don't call this method
		// correctly - call Contains() instead.
			$this->TypeCheck($this->Value, array('hash'));
			$Pass = $this->Pass();
			if ($CaseSensitive == Stems::CaseSensitive) {
				return in_array($Search, $Pass->Value);
			} else {
				return in_array(strtolower($Search), array_map('strtolower', $Pass->Value));
			}
		}
		
		public function Keys()
		{
			$this->TypeCheck($this->Value, array('hash'));
			$Pass = $this->Pass();
			return array_keys($Pass->Value);
		}
		
		public function Filter($Criteria)
		{
			$this->TypeCheck($this->Value, array('hash'));
			$this->TypeCheck($Criteria, array('string'));
			$Pass = $this->Pass();
			
			if ($this->IsRegex($Criteria)){
				
			}
			
		}
}
