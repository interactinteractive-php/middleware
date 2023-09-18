<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');
    
class Restapi_Model extends Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function setUserSessionBySessionIdModel($sessionId) {
        
        $userRow = self::getUserSessionInfoBySessionIdModel($sessionId);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionByUserPassModel($username, $password) {
        
        $userRow = self::getUserSessionInfoByUserPassModel($username, $password);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionModel($userRow) {
        
        $sessionValues = array('sessioncompanydepartmentid' => $userRow['COMPANY_DEPARTMENT_ID']);

        Session::init();

        Session::set(SESSION_PREFIX . 'LoggedIn', true);
        Session::set(SESSION_PREFIX . 'userkeyid', $userRow['USER_ID']);
        Session::set(SESSION_PREFIX . 'userid', $userRow['SYSTEM_USER_ID']);
        Session::set(SESSION_PREFIX . 'username', $userRow['USERNAME']);
        Session::set(SESSION_PREFIX . 'firstname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'personname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'employeeid', $userRow['EMPLOYEE_ID']);
        Session::set(SESSION_PREFIX . 'employeekeyid', $userRow['EMPLOYEE_KEY_ID']);
        Session::set(SESSION_PREFIX . 'departmentid', $userRow['DEPARTMENT_ID']);
        Session::set(SESSION_PREFIX . 'sessionValues', $sessionValues);
        Session::set(SESSION_PREFIX . 'periodStartDate', Date::currentDate('Y-m') . '-01');

        return true;
    }
    
    public function getUserSessionInfoBySessionIdModel($sessionId) {
        
        try {
            
            $row = $this->db->GetRow("
                SELECT 
                    US.USER_ID, 
                    US.SYSTEM_USER_ID, 
                    US.EMPLOYEE_ID, 
                    US.EMPLOYEE_KEY_ID, 
                    US.DEPARTMENT_ID, 
                    US.COMPANY_DEPARTMENT_ID, 
                    USU.USERNAME, 
                    BP.FIRST_NAME 
                FROM UM_USER_SESSION US 
                    INNER JOIN UM_SYSTEM_USER USU ON USU.USER_ID = US.SYSTEM_USER_ID 
                    LEFT JOIN BASE_PERSON BP ON BP.PERSON_ID = USU.PERSON_ID 
                WHERE US.SESSION_ID = ".$this->db->Param(0), 
                array($sessionId)
            );

            return $row;
        
        } catch (Exception $ex) {  
            
            return array();
        }
    }
    
    public function getUserSessionInfoByUserPassModel($username, $password) {
        
        try {
            
            $username = Str::lower($username);
            $oldHash  = Hash::createMD5reverse($password);
            $newHash  = Hash::create('sha256', $password);

            $usernamePh = $this->db->Param(0);
            $oldpassPh  = $this->db->Param(1);
            $newpassPh  = $this->db->Param(2);

            $row = $this->db->GetRow("
                SELECT 
                    UM.USER_ID, 
                    USU.USER_ID AS SYSTEM_USER_ID, 
                    VU.EMPLOYEE_ID, 
                    VU.EMPLOYEE_KEY_ID, 
                    VU.DEPARTMENT_ID, 
                    UM.COMPANY_DEPARTMENT_ID, 
                    USU.USERNAME, 
                    VU.FIRST_NAME 
                FROM UM_SYSTEM_USER USU 
                    LEFT JOIN UM_USER UM ON UM.SYSTEM_USER_ID = USU.USER_ID 
                    LEFT JOIN VW_USER VU ON VU.USER_ID = USU.USER_ID 
                WHERE LOWER(USU.USERNAME) = $usernamePh 
                    AND (USU.PASSWORD_HASH = $oldpassPh OR USU.PASSWORD_HASH = $newpassPh)", 

                array($username, $oldHash, $newHash)
            );

            return $row;
        
        } catch (Exception $ex) {
            
            return array();
        }
    }
    
}