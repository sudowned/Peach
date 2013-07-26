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
	const CaseSensitive = '__PEACH_CASE_SENSITIVE__';
	const CaseInsensitive = '__PEACH_CASE_INSENSITIVE__';
}

class PeachExceptions {
	static function GenerateTypeException($String, $Expected)
	{
		return 'Type mismatch: argument expected to be of type \''.$Expected.'\', but \''.gettype($String).'\' given.';
	}
}
	
class Peach {
	public $Datatypes;
	public $Data;

	function __CONSTRUCT() {
		$This->Datatypes = array();
	}
	
	private function TypeCheck($Data, $Type){
		// We store a separate "PHPTtype" value reflecting PHP's internal type
		// naming so that our exception text can include Peach's improved types
		$PHPType = strtolower($Type); 
		if ($PHPType == "hash") { $PHPType = "array"; }
		if (gettype($Data) == strtolower($PHPType)) {
			return true;
		} else {
			throw new Exception(PeachExceptions::GenerateTypeException($Data, $Type));
		}
	}
	
	// Base method for array handling. We can't use Array() for our method name
	// because it can't be overridden, but PHP's "arrays" are hashes anyway
	// so let's do that.
	public function Hash($Data){
		$this->TypeCheck($Data, 'Hash');
		$this->Datatypes[] = "hash";
		$this->Data = $Data;
		return $this;
	}
	// Base method for string handling
	public function String($Data){
		$this->TypeCheck($Data, 'String');
		$this->Datatypes[] = "string";
		$this->Data = $Data;
		return $this;
	}
	
		public function Length() {
			$this->TypeCheck($this->Data, 'String');
			return strlen($this->Data);
		}
	
		public function Substring($Start, $Length = null) {
			// this little dance is necessary because PHP bases substr's behavior
			// on whether or not the Length string is specified at all, and not
			// on whether it's null. In fact, a null/false/0 Length means it'll 
			// return an empty string, which is baffling because I can't imagine
			// any circumstances in which this behavior would be in any way desirable.
			$this->TypeCheck($this->Data, 'String');
			if ($Length !== null){
				return substr($this->Data, $Start, $Length);
			} else {
				return substr($this->Data, $Start);
			}
		}
		
		public function Replace($Search, $Replace, $CaseSensitive = Stems::CaseSensitive, $Count = null)
		{
			$this->TypeCheck($this->Data, 'String');
			if ($CaseSensitive == Stems::CaseSensitive) {
				return str_replace($Search, $Replace, $this->Data, $Count);
			} else {
				return str_ireplace($Search, $Replace, $this->Data, $Count);
			}
		}
}
