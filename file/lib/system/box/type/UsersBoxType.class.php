<?php
namespace wcf\system\box\type;
use \wcf\system\WCF;

/**
 * @author		kaffeemon
 * @license		MIT
 * @package		com.github.kaffeemon.wcf.boxes
 * @subpackage	system.box.type
 */
class UsersBoxType extends \wcf\system\box\CachedBoxType {
	/**
	 * @see \wcf\system\box\AbstractBoxType::$templateName
	 */
	public $templateName = 'usersBoxType';
	
	/**
	 * @see \wcf\system\box\CachedBoxType::$cacheBuilder
	 */
	public $cacheBuilder = 'wcf\system\cache\builder\UsersBoxTypeCacheBuilder';
	
	public $users = array();
	
	/**
	 * @see \wcf\system\box\IBoxType::getOptions()
	 */
	public static function getOptions() {
		return array(
			new \wcf\data\option\Option(null, array(
				'optionName' => 'usernames',
				'optionType' => 'text'
			)),
			
			new \wcf\data\option\Option(null, array(
				'optionName' => 'groupIDs',
				'optionType' => 'userGroups'
			)),
			
			new \wcf\data\option\Option(null, array(
				'optionName' => 'hideOfflineUsers',
				'optionType' => 'boolean'
			)),
			
			new \wcf\data\option\Option(null, array(
				'optionName' => 'showOnlineStatus',
				'optionType' => 'boolean',
				'defaultValue' => 1
			)),
			
			new \wcf\data\option\Option(null, array(
				'optionName' => 'groupByUserGroup',
				'optionType' => 'boolean',
				'defaultValue' => 1
			)),
		);
	}
	
	/**
	 * @see \wcf\system\box\IBoxType::validateOptions()
	 */
	public static function validateOptions($options) {
		parent::validateOptions($options);
		
		if (!strlen($options['usernames']) && !strlen($options['groupIDs'])) {
			throw new \wcf\system\exception\UserInputException('options', array(
				'usernames' => 'noCondition',
				'groupIDs' => 'noCondition'
			));
		}
	}
	
	/**
	 * @see \wcf\system\box\IBoxType::render()
	 */
	public function render() {
		$this->readCache();
		
		$this->users = \wcf\data\user\UserProfile::getUserProfiles($this->boxCache['userIDs']);
		
		if ($this->hideOfflineUsers) {
			foreach ($this->users as &$user)
				if (!$user->isOnline()) unset($user);
		}
		
		return parent::render();
	}
}

