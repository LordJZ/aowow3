<?phpinterface ICacheable{	function GetCacheHash($id);	static function GetCacheLifeTime();}interface ICacheable_SerializeableObject extends ICacheable{	function __create($id);	function __wakeup();	function __sleep();}interface ICacheable_DatabaseObject extends ICacheable_SerializeableObject{	static function GetCacheTablePostfix();}interface ICacheable_FileObject extends ICacheable_SerializeableObject{	static function GetCacheFileName();	static function GetCacheFolder();}interface ICacheable_FileOutput extends ICacheable{	function GetOutput();}class Cache{	static function Get($objectName, $id = false)	{		$data = self::GetCacheData($objectName, $id);		if ($data)		{			echo 'Returning cached instance of '.$objectName;			return $data->object;		}		// create a new object		$obj = new $objectName;		$obj->__create($id);		// TODO: uncomment when ready		//self::Set($objectName, $hash);		return $obj;	}	static function GetCacheData($objectName, $id = false)	{		do		{			if (!implements_interface($objectName, 'ICacheable'))				break;			$lifetime = $objectName::GetCacheLifeTime();			$hash = $objectName::GetCacheHash($id);			if (implements_interface($objectName, 'ICacheable_DatabaseObject'))			{				$postfix = $objectName::GetCacheTablePostfix();				$cache_data = DB::World()->SelectRow('						SELECT version, created, serialization						FROM ?_cache_'.$postfix.'						WHERE hash = ?					',					$hash				);				if (!$cache_data)					break;				if ($cache_data['created'] + $lifetime < time() || $cache_data['version'] != VERSION)				{					self::i_drop_db($postfix, $hash);					break;				}				return new CachedObject(					$cache_data['version'],					unserialize($cache_data['serialization']),					$cache_data['created']				);			}			$isOutput = false;			if (implements_interface($objectName, 'ICacheable_FileObject')				|| ($isOutput = implements_interface($objectName, 'ICacheable_FileOutput')))			{				$filename = $objectName::GetCacheFileName();				$folder = $objectName::GetCacheFolder();				if (!file_exists($filename = './cache/'.$folder.'/'.$filename))					break;				$cache_data = unserialize(file_get_contents($filename));				if ($cache_data->timestamp + $lifetime < time()					|| $cache_data->version != VERSION)				{					self::i_drop_file($filename);					break;				}				if ($isOutput)					exit($cache_data->object);				return $cache_data;			}		} while (false);		return null;	}	static function GetCurrent($id = false)	{		$trace = debug_backtrace();		$name = $trace[1]['class'];		return self::Get($name, $id);	}	static function SetCurrent($id = false)	{		$trace = debug_backtrace();		$name = $trace[1]['class'];		$obj = $trace[1]['object'];		if (!implements_interface($name, 'ICacheable'))			return false;		$lifetime = $name::GetCacheLifeTime();		$hash = $name::GetCacheHash($id);		if (implements_interface($name, 'ICacheable_DatabaseObject'))		{			$postfix = $name::GetCacheTablePostfix();			DB::World()->Query('					REPLACE INTO ?_cache_'.$postfix.' (hash, version, created, serialization)					VALUES (?, ?, ?, ?)				',				$hash, VERSION, time(), serialize($obj)			);		}		$isOutput = false;		if (implements_interface($name, 'ICacheable_FileObject')			|| ($isOutput = implements_interface($name, 'ICacheable_FileOutput')))		{			$filename = $name::GetCacheFileName();			$folder = $name::GetCacheFolder();			if (file_exists($filename = './cache/'.$folder.'/'.$filename))				unlink($filename);			// TODO: continue development of cache-saving methods		}	}	private static function i_drop_file($filename)	{		unlink($filename);	}	private static function i_drop_db($postfix, $hash)	{		DB::World()->Query('DELETE FROM ?_cache_'.$postfix.' WHERE hash = ?', $hash);	}	// 6 bits for type - max 63	// 25 bits for id - max 33,554,431	static function CreateHash($type, $id = false)	{		if ($id === false)		{			$id = $type;			$type = 0;		}		return ($type << 25) | ($id & 0x1FFFFFF);	}}class CachedObject{	var $version;	var $object;	var $timestamp;	function __construct($version, $object, $timestamp)	{		$this->version = $version;		$this->object = $object;		$this->timestamp = $timestamp;	}}?>