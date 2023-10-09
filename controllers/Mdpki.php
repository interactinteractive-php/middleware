<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

/**
 * Mdpki Class 
 * 
 * @package     IA PHPframework
 * @subpackage	Middleware
 * @category	PKI
 * @author	B.Munkh-Erdene <munkherdene@interactive.mn>
 * @link	http://www.interactive.mn/PHPframework/Middleware/Mdpki
 */
class Mdpki extends Controller {

    private static $signFileUploadUrlAddress = "token/uploadUrl";
    
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    /**
     * Жагсаалтын цонхон дээрээс get ажлуулж xml цуглуулж xml болон hash үүсгэх
     */
    public function signByGet() {
        $paramCode = Input::post('paramCode');
        
        (Array) $paramFilters = array();
        (Array) $param = array(
            'systemMetaGroupId' => Input::numeric('metaDataId'),
            'showQuery' => 0,
        );
        $response = array(
            'status' => 'warning',
            'message'=> 'Амжилтгүй боллоо'
        );

        if (Input::postCheck('dataRow') && !Input::isEmpty('dataRow')) {
            $dataRow = Input::post('dataRow');
            foreach ($dataRow as $path => $row) {
                $paramFilters[$path] = $row;
            }
        }

        $param = array_merge($param, $paramFilters);

        $result = $this->ws->runResponse(GF_SERVICE_ADDRESS, $paramCode, $param);

        if (!$result) {
            $response = array(
                'status' => 'warning',
                'message'=> 'Амжилтгүй боллоо'
            );
        } else {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><sign></sign>");
            $node = $xml->addChild('processData');        
            // хэрэглэгч ямар ч тэмдэгт sign хийж болох учраас албаар security sanitize хийсэнгүй 
            $this->array_to_xml($result, $node);
            $xmlString = $xml->asXML();

            $hash = hash('sha256', $xmlString);
            $response = array(
                'status' => 'success', 
                'hash' => $hash, 
                'plainText' => $xmlString, 
                'guid' => '9981a19b-d380-4dad-a167-c223175e9321' // Энэ статик user - г солих хэрэгтэй логин хийсэн user - н guid
            );
        }
        
        echo json_encode($response); exit;
    }
    
    public function saveCipherText() {
        /*$plainText = Input::post('plainText');
        $cyphertext = Input::post('cyphertext');
        $certificateSerialNumber = Input::post('certificateSerialNumber');*/
        
        echo json_encode(array('status' => 'success'));
    }
    
    /**
     * дурын пост -оос hash гаргаж авна
     * param талбар заавал байна.
     */
    public function generateHashFromPost() {
        if (!isset($_POST['param'])) {
            return false;
        }
        
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><sign></sign>");
        $node = $xml->addChild('processData');        
        // хэрэглэгч ямар ч тэмдэгт sign хийж болох учраас албаар security sanitize хийсэнгүй 
        $this->array_to_xml($_POST['param'], $node);
        $xmlString = $xml->asXML();
        
        $hash = hash('sha256', $xmlString);
        
        echo json_encode(array('status' => 'success', 'hash' => $hash, 'plainText' => $xmlString, 'guid' => '9981a19b-d380-4dad-a167-c223175e9321'));
        exit;
    }
    
    public function generateHashFromFile() {
        $filePath = Input::post('filePath');
        $handle = fopen($filePath, "r");
        $contents = fread($handle, filesize($filePath));
        ob_flush(); 
        fclose($handle);        
        $hash = hash('sha256', $contents);
        
        echo json_encode(array('status' => 'success', 'hash' => $hash, 'plainText' => $contents, 'guid' => '9981a19b-d380-4dad-a167-c223175e9321'));
        exit;
    }
    
    public function array_to_xml($array, &$xml) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    // хэрэглэгч юу ч sign хийж болох учраас албаар security sanitize хийсэнгүй 
                    $this->array_to_xml($value, $subnode);
                } else {
                    // хэрэглэгч юу ч sign хийж болох учраас албаар security sanitize хийсэнгүй 
                    $this->array_to_xml($value, $xml);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }
    }
    
    public function generateHashFromFileByDataView() {
        
        $selectedRow = Input::post('selectedRow');
        
        if (isset($selectedRow['plaintextforcipher'])) {
            
            $contents = $selectedRow['plaintextforcipher'];
            
        } else {
            
            if (isset($selectedRow['physicalpath']) && file_exists($selectedRow['physicalpath'])) {

                $filePath = $selectedRow['physicalpath'];

                $handle = fopen($filePath, 'r');
                $contents = fread($handle, filesize($filePath));

                ob_flush(); 
                fclose($handle);

            } else {

                unset($selectedRow['wfmstatusid']);
                unset($selectedRow['wfmstatusname']);
                unset($selectedRow['wfmstatuscolor']);
                unset($selectedRow['wfmdescription']);

                $contents = implode('', $selectedRow);
            }
        }
        
        $hash = hash('sha256', $contents);
        
        $response = array(
            'status'    => 'success', 
            'hash'      => $hash, 
            'plainText' => $hash, 
            'guid'      => Session::get(SESSION_PREFIX.'monpassGUID')
        );
        
        echo json_encode($response); exit;
    }
    
    public function getCertificateSerialNumberByGUID($guid) {
        $this->load->model('mdpki', 'middleware/models/');
        return $this->model->getCertificateSerialNumberByGUIDModel($guid);
    }

    public function rowVerifyDataView() {
        
        $refStructureId = Input::post('refStructureId');
        $selectedRow = Input::post('selectedRow');
        $recordId = isset($selectedRow['id']) ? $selectedRow['id'] : null;
        
        $this->load->model('mdworkflow', 'middleware/models/');
        
        $cipherText = $this->model->getLastCreatedCipherTextModel($refStructureId, $recordId);
        
        if ($cipherText) {
            
            if (isset($selectedRow['wfmstatusid'])) {
                unset($selectedRow['wfmstatusid']);
            }
            
            if (isset($selectedRow['wfmstatusname'])) {
                unset($selectedRow['wfmstatusname']);
            }
            
            if (isset($selectedRow['wfmstatuscolor'])) {
                unset($selectedRow['wfmstatuscolor']);
            }
            
            if (isset($selectedRow['wfmdescription'])) {
                unset($selectedRow['wfmdescription']);
            }

            $contents = implode('', $selectedRow);

            $hash = hash('sha256', $contents);
            $guid = Session::get(SESSION_PREFIX.'monpassGUID');
            
            $response = array(
                'status' => 'success', 
                'plainText' => $hash, 
                'cipherText' => $cipherText, 
                'guid' => $guid,  
                'certificateSerialNumber' => self::getCertificateSerialNumberByGUID($guid) 
            );
            
        } else {
            $response = array(
                'status' => 'error', 
                'message' => 'Error'
            );
        }
        
        echo json_encode($response);
    }
    
    /**
     * Тухай файл path - д зориулж unique file upload хийх URL үүсгэнэ
     */
    public function getInformationForDocumentSign() {
        $ecmContentId = Input::post('ecmContentId');
        $filePath = Input::post('filePath');
        $hash = $this->genereateHashForDocumentSign($filePath, $ecmContentId);
        $userCertificateArray = $this->model->getCertificateInformation();
        $filePathInfo = pathinfo($filePath);

        if($userCertificateArray) {
            
            $date = Date::currentDate('Ym');
            $newPath = UPLOADPATH . 'signed_file/' . $date . '/';
            if (!is_dir($newPath)) {
                mkdir($newPath, 0777, true);
            }
            
            $result = array (
                'certificateSerialNumber' => $userCertificateArray['CERTIFICATE_SERIAL_NUMBER'],
                'userId' => $userCertificateArray['MONPASS_USER_ID'],
                'serverAddress' => Config::getFromCache('MONPASS_SERVER'),
                'uploadUrl' => URL . self::$signFileUploadUrlAddress . '/' . $hash,
                'newFileName' => 'signed_v_' . getUID() . '.' . $filePathInfo['extension'],
                'filePath' => $newPath,
                'status' => 'success'
            );
        }else{
            $result = array('status' => 'error');
        }
        
        
        echo json_encode($result);
    }
    
    /**
     * Тухайн file path - д зориулж давтагдашгүй hash үүсгэж авч байна.
     * @param type $filePath
     * @return type
     */
    private function genereateHashForDocumentSign($filePath, $ecmContentId) {        
        $time = time();
        // generate hash
        $hash = sha1($time.$filePath);
        // save hash
        $this->model->saveHashForDocumentSign($hash, $ecmContentId);
        
        return $hash;
    }
    
    public function fileAuthentication() {
        $certificateSerialNumber = Input::post('certificateSerialNumber');
        $cyphertext = Input::post('cyphertext');
        $fileName = Input::post('fileName');
        $plainText = Input::post('plainText');
        $ecmContentId = Input::post('ecmContentId');

        $filePath = Input::post('filePath', UPLOADPATH . 'signedDocument/') . $fileName;
        
        if (file_exists($filePath)) {
            
            $this->model->checkIsSigned($ecmContentId, $filePath);
            
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'error');
        }

        echo json_encode($response); exit;
    }

    public function cloudSignPrepare() {

        $jsonString = json_encode($_POST['param']);        
        $hash = hash('sha256', $jsonString);
        $postData = array(
            'ciphertext' => $hash,
            'stateRegNumber' => $this->model->getRegisterNumber()
        );
        $clientId  = Config::getFromCache('mp_client_id');
        $clientSecret  = Config::getFromCache('mp_client_secret');
        $userPass = base64_encode($clientId . ':' . $clientSecret);

        $url = 'https://sso.monpass.mn/sign/signRequest?client_id='.$clientId;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HEADER => false, 
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $userPass,
                'Content-Type: application/json',
            )
        ));            
        $response = curl_exec($curl);       
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $response = array('status' => 'error', 'message' => $err);
            jsonResponse($response);
        } else {
            jsonResponse(json_decode($response));
        }  
    }

    public function cloudSign() {
        $token = Input::post('monpass_token');

        $url = 'https://sso.monpass.mn/sign/getSignInfo';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HEADER => false, 
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        ));    
        
        $response = curl_exec($curl);       
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $response = array('status' => 'error', 'message' => $err);
            jsonResponse($response);
        } else {
            jsonResponse(json_decode($response));
        }  
    }
}
