<?php
/**
*Modified user registration for android client
*
*/
require_once "lib/base.php";

$uid = $_POST["regname"];
$pass1 = $_POST["regpass1"];
$pass2 = $_POST["regpass2"];
//$data;
if(is_null($uid)) {
        echo "$uid is null";
}
                try {
                         OC_User::createUser($uid, $pass1);
			 $params = array('reply' => "ACCOUNT_CREATED");	 
                }
                catch(Exception $exception) {
			//$data = false;
                        $error[] = $exception->getMessage();
			if($exception->getMessage() === "The username is already being used") {
			
			$params = array('reply' => "USER_ALREADY_EXISTS");
	}
			//error_log($params);
			
                }
                if(count($error) == 0) {
                        // User was successfully created and their name was modified with a location
                        if (OC_App::isEnabled('multiinstance')) {
                                if (\OCA\MultiInstance\Lib\MILocation::uidContainsLocation($uid)){
                                        $uid_location = $uid;
                                }
                                else { //Always add for this location 
                                        $location = \OCP\Config::getAppValue('multiinstance', 'location');
                                        $uid_location = $uid . "@" . $location;
                                }
                        }
                        else {
                                $uid_location = $uid;
                        }


                        //OC_User::login($uid_location, $pass1);
                        //$data = true;
			//error_log($params);
			//return $this->renderJSON($params);
			echo json_encode(array('reply' => $params));
                }
                else {
                        //$data = false;
                        echo json_encode(array('reply' => $params));
			//return $this->renderJSON($params);
		}
                ?>

