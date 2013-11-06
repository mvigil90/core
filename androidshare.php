<?php
/**
*Share script for sharing with android phone
*VillageShare project
*Smruthi Manjunath
*/
require_once "lib/base.php";

$itemType = $_POST["itemType"];
$itemSource = $_POST["itemSource"];
$shareType = $_POST["shareType"];
$shareWith = $_POST["shareWith"];
$permissions = $_POST["permission"];
$uid_owner = $_POST["uidOwner"];

$stmt = OC_DB::prepare( "SELECT `fileid` FROM `*PREFIX*filecache` WHERE `path` = ?" );
$result = $stmt->execute( array( $itemSource ));


$row = $result->fetchRow();
if($row)
{
	$fileId = $row['fileid'];
} else
{
	error_log("No such file ".$itemSource." exists");
	$retval = "INVALID_FILE";
	$params = array(
		'SHARE_STATUS' => $retval
	);
	echo json_encode($params);
}

error_log($fileId);
switch($shareType)
{
	case "0": $shareType = OCP\Share::SHARE_TYPE_USER; break;
	case "1": $shareType = OCP\Share::SHARE_TYPE_GROUP; break;
	case "3": $shareType = OCP\Share::SHARE_TYPE_LINK; break;
	case "5": $shareType = OCP\Share::SHARE_TYPE_CONTACT; break;
	case "6": $shareType = OCP\Share::SHARE_TYPE_REMOTE; break;
	default: {
		  $retval = "INVALID_SHARETYPE";
		  $params = array(
                        'SHARE_STATUS' => $retval
                  );
		  echo json_encode($params);
		  }
}
OC_User::setUserId($uid_owner);
//we need to setup the filesystem for the user, otherwise OC_FileSystem::getRoot will fail and break
OC_Util::setupFS($uid_owner);

try {
	OCP\Share::shareItem($itemType, $fileId, $shareType, $shareWith, $permissions,$uid_owner);
	$retval = true;
	$params = array(
                        'SHARE_STATUS' => $retval
                 );
                echo json_encode($params);
} catch(Exception $e) {
	 $update_error = true;
	$retval = false;
		error_log($itemType." ".$itemSource." ".$shareType." ".$shareWith." ".$permissions." ".$uid_owner);
		error_log($e->getMessage());
                                OCP\Util::writeLog('files_sharing',
                                        'Upgrade Routine: Skipping sharing " to "'.$shareWith
                                        .'" (error is "'.$e->getMessage().'")',
                                        OCP\Util::WARN);
	$params = array(
                        'SHARE_STATUS' => $retval
                 );
                echo json_encode($params);

}
?>
