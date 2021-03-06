<?php

// Time defines
define('MINUTE', 60);
define('HOUR', 60*MINUTE);
define('DAY', 24*HOUR);
define('WEEK', 7*DAY);
define('MONTH', 28*DAY);
define('YEAR', 365*DAY);

enum(array( // UserRoles
	'U_MODERATOR'		=> 0x01,
	'U_GAMEMASTER'		=> 0x02,
	'U_ADMINISTRATOR'	=> 0x04,
	'U_DEVELOPER'		=> 0x08,
	'U_TESTER'			=> 0x10,
	'U_VIP'				=> 0x20,
//	'U_RESERVED_1'		=> 0x40,
//	'U_RESERVED_2'		=> 0x80,
));

enum(array( // UserGroups
	'U_GROUP_ANY'		=> 0,
	'U_GROUP_ALL'		=> 0xFF,
	'U_GROUP_STAFFERS'	=> U_MODERATOR | U_GAMEMASTER | U_ADMINISTRATOR | U_DEVELOPER,
	'U_GROUP_SUPREME'	=> U_ADMINISTRATOR | U_DEVELOPER,
));

enum(array( // ContentTypes
	'CONTENT_NPC'		=> 1,
	'CONTENT_OBJECT'	=> 2,
	'CONTENT_ITEM'		=> 3,
	'CONTENT_ITEMSET'	=> 4,
	'CONTENT_QUEST'		=> 5,
	'CONTENT_SPELL'		=> 6,
	'CONTENT_ZONE'		=> 7,
	'CONTENT_FACTION'	=> 8,
	'CONTENT_PET'		=> 9,
	'CONTENT_ACHIEVEMENT'=>10,
	'CONTENT_TITLE'		=> 11,
	'CONTENT_EVENT'		=> 12,
	'CONTENT_CLASS'		=> 13,
	'CONTENT_RACE'		=> 14,
	'CONTENT_SKILL'		=> 15,
	'CONTENT_STATISTIC'	=> 16,

	// Non-wowheadish
	// Move them upper

	'CONTENT_USER'		=> 21,
	'CONTENT_MARKUP'	=> 23,

	'CONTENT_ARTICLE'	=> 20,	// OBSOLETE
	'CONTENT_PATCHNOTES'=> 22,	// OBSOLETE
));

enum(array( // Sides
	'ALLIANCE'	=> 1,
	'HORDE'		=> 2,
	'BOTH'		=> 3,
));

enum(array( // Genders
	'MALE'		=> 0,
	'FEMALE'	=> 1,
	'NO_GENDER'	=> 2,
));

enum(array(
	'TAB_DATABASE'		=> 0,
	'TAB_TOOLS'			=> 1,
	'TAB_BUGTRACKER'	=> 2,
	'TAB_MORE'			=> 3,
	'TAB_STAFF'			=> 4,
));

define('NUM_TALENT_TABS', 3);

?>