<?php
namespace wcf\system\cache\builder;
use \wcf\system\database\util\PreparedStatementConditionBuilder;
use \wcf\util\ArrayUtil;
use \wcf\system\WCF;

/**
 * @author		kaffeemon
 * @license		MIT
 * @package		com.github.kaffeemon.wcf.boxes
 * @subpackage	system.cache.builder
 */
class UsersBoxTypeCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		list($cache, $boxTypeID, $boxID) = explode('-', $cacheResource['cache']);
		$box = new \wcf\data\box\Box($boxID);
		
		$data = array(
			'userIDs' => array(),
			'groups' => array()
		);
		
		$conditions = new PreparedStatementConditionBuilder();
		
		if (strlen($box->usernames))
			$conditions->add("u.username IN (?)", array(ArrayUtil::trim(explode(',', $box->usernames)));
		
		if (strlen($box->groupIDs))
			$conditions->add("g.groupID IN (?)", array(ArrayUtil::trim(explode(',', $box->groupIDs)));
		
		$sql = "SELECT DISTINCT u.userID, g.groupID
				FROM wcf".WCF_N."_user u
				LEFT JOIN wcf".WCF_N."_user_to_group g
				ON u.userID = g.userID
				".str_replace(" AND ", " OR ", $conditions);
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		foreach ($row = $statement->fetchArray()) {
			$data['userIDs'][] = $row['userID'];
			if (!isset($data['groups'][$row['groupID']])) $data['groups'][$row['groupID']] = array();
			$data['groups'][$row['groupID']][] = $row['userID'];
		}
		
		return $data;
	}
}

