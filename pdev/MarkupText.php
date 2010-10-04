<?php

enum(array( // MarkupTextFlags
	'MTFLAG_PARAGRAPH'	=> 0x01,
	'MTFLAG_FORCETEXT'	=> 0x02,
));

enum(array( // BasicMarkupTexts
	'MARKUPTEXT_ONELINER'	=> 1,
	'MARKUPTEXT_NEWSTEXT'	=> 2,
));

class MarkupText implements ICacheable_Database
{
	var $id = 0;
	private $contents = '';
	var $html = '';
	var $hash = '';
	var $flags = 0;
	var $links = array();
	var $parsed = false;

	function parse($group = U_GROUP_ALL)
	{
		if (!$this->parsed)
		{
			$this->contents = trim(str_replace("\r", '', $this->contents));

			if(!($group & U_GROUP_SEE_ADMIN_TAG))
				$this->contents = preg_replace('/(\[admin\](.+?)\[\/admin\])/s', '', $this->contents);

			if($group & U_GROUP_LINKS_ALLOWED)
			{
				if(preg_match_all('/(\[(achievement|item|quest|spell|npc)=(\d+)\])/', $this->contents, $matches))
				{
					// TODO: fill in $this->links here
				}
			}

			$this->hash = substr(md5($this->contents), 0, 6);
		}
	}

	function toHTML()
	{
		if (!$this->parsed)
			$this->parse();

		if (empty($this->html))
		{
			$element = $this->hasFlag(MTFLAG_PARAGRAPH) ? 'p' : 'div';
			$this->html = '<'.$element.' ';
			$this->html .= 'id="markup-'.($this->hash).'"';

			if($this->hasFlag(MTFLAG_FORCETEXT))
				$this->html .= ' class="text"';

			$this->html .= '></'.$element.'>';
			$this->html .= '<script type="text/javascript">';

			$this->html .= 'Markup.printHtml("'.jsEscape($this->contents).'", "markup-'.($this->hash).'"';

			if($this->id)
				$this->html .= ', { uid: '.($this->id).' }';

			$this->html .= ')</script>';
		}

		return $this->html;
	}

	/*
	function replace($arr)
	{
		foreach($arr as $name => $value)
			$this->contents = str_replace('%'.$name.'%', $value, $this->contents);
	}
	*/

	function setFlag($to) { $this->flags |= $to; }
	function hasFlag($flag) { return ($this->flags & $flag) != 0; }

	static function GetCacheLifeTime() { return 30*DAY; }
	static function GetCacheTable() { return CACHE_TEXTS; }
	static function GetCacheType() { return CACHE_TEXTS_MARKUPTEXT; }
	function GetCacheId() { return $this->id; }

	function __sleep()
	{
		if (empty($this->html))
			$this->toHTML();

		return array(
			'id',
			'contents',
			'html',
			'hash',
			'flags',
			'links',
			//'parsed',
		);
	}

	function __wakeup()
	{
		if ($this->links)
			Main::$gathered = array_merge_replace_recursive(Main::$gathered, $this->links);
	}

	function __create($id)
	{
		$this->id = $id;
		$result = DB::World()->SelectRow('SELECT id, content, flags FROM ?_markup2_texts WHERE id = ?d', $id);

		if($result)
		{
			$this->contents = $result['content'];
			$this->flags = $result['flags'];
		}
	}

	public function getContents() { return $this->contents; }

	public function setContents($contents)
	{
		$this->html = '';
		$this->contents = $contents;
	}
}

?>