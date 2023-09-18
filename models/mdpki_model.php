<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

class Mdpki_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getCertificateInformation() {
        $userId = Ue::sessionUserKeyId();
        return $this->db->GetRow("SELECT MONPASS_USER_ID, CERTIFICATE_SERIAL_NUMBER, TOKEN_SERIAL_NUMBER FROM UM_USER_MONPASS_MAP WHERE USER_ID = '$userId' AND IS_ACTIVE = 1");
    }

    public function saveHashForDocumentSign($hash, $ecmContentId = null) {
        try{
            $userId = Ue::sessionUserKeyId();
            $hash = Security::sanitize($hash);
            $expiryDate = strtotime(Date::currentDate('Y-m-d H:i:s') . "+10minutes");
            $plainText = null;
            $array = array(
                'ID' => getUID(),
                'HASH' => $hash, 
                'IS_ACTIVE' => 1, 
                'EXPIRY_DATE' => $expiryDate, 
                'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s'), 
                'ECM_CONTENT_ID' => $ecmContentId, 
                'PLAIN_TEXT' => $plainText,
                'CREATED_USER_ID' => $userId
            );
            $this->db->AutoExecute('PKI_ONE_TIME_HASH', $array);
            return true;
        }catch(Exception $e) {
            return false;
        }
    }

    public function checkIsSigned($ecmContentId, $filePath) {
        
        if ($ecmContentId) {
            try {
                $this->db->AutoExecute('ECM_CONTENT', array('IS_SIGNED' => 1, 'THUMB_PHYSICAL_PATH' => $filePath), 'UPDATE', 'CONTENT_ID = ' . $ecmContentId);
                return true;
            } catch(Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getRegisterNumber() {
        $row = $this->db->GetOne("
            SELECT
                BP.STATE_REG_NUMBER
            FROM BASE_PERSON BP
                LEFT JOIN UM_SYSTEM_USER CRM  ON CRM.PERSON_ID = BP.PERSON_ID 
            WHERE CRM.USER_ID = " . Ue::sessionUserId()
        );        

        if ($row) {
            return $row;
        }
        return '';
    }
    
}