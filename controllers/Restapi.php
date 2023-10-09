<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

/**
 * RestApi Class 
 * 
 * @package     IA PHPframework
 * @subpackage	Middleware
 * @category	Restful API
 * @author	B.Och-Erdene <ocherdene@veritech.mn>
 * @link	http://www.interactive.mn/PHPframework/Middleware/RestApi
 */

class Restapi extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        
        includeLib('Compress/Compression');
        
        $jsonBody = file_get_contents('php://input');
        $jsonBody = Compression::decompress($jsonBody);
            
        $param = json_decode($jsonBody, true);
        $isMobile = false;
        
        if (isset($param['request'])) {
            
            $param       = $param['request'];
            $commandName = issetVar($param['command']);
            $parameters  = issetParam($param['parameters']);
            $isMobile    = (issetVar($param['isMobile']) || issetVar($param['ismobile'])) ? true : false;
            $isLowerCase = (issetVar($param['isLowerCase']) || issetVar($param['islowercase'])) ? true : false;
            $isSessionId = $isUserPass = false;
            
            if (array_key_exists('sessionId', $param) || array_key_exists('sessionid', $param)) {
                
                $isSessionId = true;
                
                $sessionId   = issetVar($param['sessionId']);
                $sessionId   = $sessionId ? $sessionId : issetVar($param['sessionid']);
                
                $setSession = $this->model->setUserSessionBySessionIdModel($sessionId);
                
            } else {
                
                $isUserPass = true;
                
                $username = issetVar($param['username']);
                $password = issetVar($param['password']);
                
                $setSession = $this->model->setUserSessionByUserPassModel($username, $password);
            }
            
            if (!$setSession) {
                
                if ($isSessionId) {
                    $response = array('status' => 'error', 'message' => 'Session not found!');
                } else {
                    $response = array('status' => 'error', 'message' => 'Username or password is wrong!');
                }
                
            } else {
                
                switch ($commandName) {
                    
                    case 'kpiIndicatorDataListConfig':
                    {
                        $kpiMainIndicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $configRow = $this->model->getKpiIndicatorRowModel($kpiMainIndicatorId);

                        if ($configRow) {
                            
                            $result = array(
                                'id'        => $configRow['ID'],
                                'code'      => $configRow['CODE'],
                                'name'      => $configRow['NAME'],
                                'kpiTypeId' => $configRow['KPI_TYPE_ID'], 
                                'isUseWorkflow' => $configRow['IS_USE_WORKFLOW']
                            );
                            
                            $configRow['isIgnoreStandardFields'] = true;
                            $columnsData = $this->model->getKpiIndicatorColumnsModel($kpiMainIndicatorId, $configRow);
                            $processData = $this->model->getKpiIndicatorProcessModel($kpiMainIndicatorId);
                            
                            array_walk($columnsData, function(&$value) {      
                                unset($value['LOOKUP_META_DATA_ID']);
                                unset($value['INPUT_NAME']);
                                unset($value['LOOKUP_CRITERIA']);
                                unset($value['DEFAULT_VALUE']);
                                unset($value['IS_INPUT']);
                                unset($value['IS_UNIQUE']);
                                unset($value['TRG_TABLE_NAME']);
                                unset($value['SEMANTIC_TYPE_NAME']);
                                unset($value['REPORT_AGGREGATE_FUNCTION']);
                                unset($value['EXPRESSION_STRING']);
                            }); 
                            
                            $result['columns'] = $columnsData;
                            $result['process'] = $processData;
                            
                            $response = array('status' => 'success', 'result' => $result);
                        } else {
                            $response = array('status' => 'error', 'text' => 'Not found indicator!');
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorConfig':
                    {
                        $kpiMainIndicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $configRow = $this->model->getKpiIndicatorRowModel($kpiMainIndicatorId);

                        if ($configRow) {
                            
                            $result = array();
                            
                            if ($configRow['KPI_TYPE_ID'] == '2008') { //Method
                                
                                $parentIndicatorId = $configRow['PARENT_ID'];
                                $data = $this->model->getKpiIndicatorTemplateModel($parentIndicatorId);
                                
                                if ($data) {
                                    
                                    $dataFirstRow = $data[0];
                                    $rowExp = $this->model->getKpiIndicatorFullExpressionModel($parentIndicatorId);
                                    
                                    $result['header'] = array(
                                        'NAME' => $dataFirstRow['NAME'],
                                        'LABEL_WIDTH' => $dataFirstRow['LABEL_WIDTH'],
                                        'KPI_TYPE_ID' => $dataFirstRow['KPI_TYPE_ID'],
                                        'NAME_PATTERN' => $dataFirstRow['NAME_PATTERN'],
                                        'IS_USE_COMPONENT' => $dataFirstRow['IS_USE_COMPONENT'], 
                                        'RENDER_THEME' => $dataFirstRow['RENDER_THEME'],
                                        'PROFILE_PICTURE' => $dataFirstRow['PROFILE_PICTURE'],
                                        'WINDOW_TYPE' => $dataFirstRow['WINDOW_TYPE'],
                                        'WINDOW_SIZE' => $dataFirstRow['WINDOW_SIZE'],
                                        'WINDOW_WIDTH' => $dataFirstRow['WINDOW_WIDTH'],
                                        'WINDOW_HEIGHT' => $dataFirstRow['WINDOW_HEIGHT'], 
                                        'PARENT_INDICATOR_ID' => $parentIndicatorId, 
                                        'fullExpression' => $rowExp
                                    );
                                    
                                    unset($data[0]);
                                    
                                    $result['detail'] = array_values($data);
                                            
                                } else {
                                    $response = array('status' => 'error', 'text' => 'No config indicator!');
                                }
                            }
                            
                            $response = array('status' => 'success', 'result' => $result);
                        } else {
                            $response = array('status' => 'error', 'text' => 'Not found indicator!');
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorDataListFilterConfig':
                    {
                        $kpiMainIndicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $result = $this->model->filterKpiIndicatorValueFormModel($kpiMainIndicatorId);

                        if ($result['status'] == 'success') {
                            $response = array('status' => 'success', 'result' => $result['data']);
                        } else {
                            $response = array('status' => 'error', 'text' => $result['message']);
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorDataList':
                    {
                        $_POST['indicatorId'] = Input::paramNum($parameters['indicatorId']);
                        $_POST['page']        = issetParam($parameters['paging']['offset']);
                        $_POST['rows']        = issetParam($parameters['paging']['pageSize']);
                        
                        if ($sortColumnNames = issetVar($parameters['sortColumnNames'])) {
                            if (is_array($sortColumnNames)) {
                                $sortColumns = '';
                                foreach ($sortColumnNames as $sortColumnName => $sortType) {
                                    $sortColumns .= $sortColumnName.'='.$sortType['sortType'].'&';
                                }
                                $_POST['sortFields'] = rtrim($sortColumns, '&');
                            }
                        }
                        
                        if ($criteria = issetParam($parameters['criteria'])) {
                            $_POST['criteria'] = $criteria;
                        }

                        $this->load->model('mdform', 'middleware/models/');
                        $result = $this->model->indicatorDataGridModel();

                        if ($result['status'] == 'success') {

                            $response = array(
                                'status' => 'success', 
                                'result' => array(
                                    'rows' => $result['rows'], 
                                    'paging' => array(
                                        'totalcount' => $result['total']
                                    )
                                )
                            );

                        } else {
                            $response = array('status' => 'error', 'text' => $result['message']);
                        }
                    }
                    break;

                    case 'kpiIndicatorDataSave':
                    {
                        $kpiMainIndicatorId = Input::param($parameters['indicatorId']);
                        
                        unset($parameters['indicatorId']);
                        
                        $parameters = array('kpiMainIndicatorId' => $kpiMainIndicatorId, 'kpiTbl' => Arr::changeKeyUpper($parameters), 'kpiCrudIndicatorId' => Input::param($parameters['crudIndicatorId']));
                        
                        if ($rowId = issetVar($parameters['kpiTbl']['ID'])) {
                            
                            $parameters['kpiTblId'] = $rowId;
                            $removeKeys = array('ID', 'CREATED_DATE', 'CREATED_USER_ID', 'CREATED_USER_NAME', 'MODIFIED_DATE', 'MODIFIED_USER_ID', 'MODIFIED_USER_NAME');
                            
                            $parameters['kpiTbl'] = array_diff_key($parameters['kpiTbl'], array_flip($removeKeys));
                        }
                        
                        $_POST = $parameters;

                        $this->load->model('mdform', 'middleware/models/');
                        $response = $this->model->saveKpiDynamicDataModel();
                        $response = Arr::changeKeyName($response, 'message', 'text');
                    }
                    break;
                
                    case 'kpiIndicatorGetData':
                    {
                        $kpiMainIndicatorId = issetVar($parameters['indicatorId']);
                        
                        if ($kpiMainIndicatorId) {
                            
                            $this->load->model('mdform', 'middleware/models/');
                            unset($parameters['indicatorId']);
                            
                            $recordId = isset($parameters['recordId']) ? Input::param($parameters['recordId']) : null;
                            
                            if ($recordId) {
                                
                                $result = $this->model->getKpiIndicatorDetailDataModel($kpiMainIndicatorId, $recordId);
                                
                            } elseif (count($parameters) > 0) {
                                
                                $result = $this->model->getKpiIndicatorDetailDataModel($kpiMainIndicatorId, null, 'idField', $parameters);
                                
                            } else {
                                $result = array('status' => 'error', 'message' => 'Invalid parameters!');
                            }
                            
                        } else {
                            $result = array('status' => 'error', 'message' => 'Invalid indicatorId!');
                        }

                        if ($result['status'] == 'success') {
                            $response = array('status' => 'success', 'result' => $result['detailData']);
                        } else {
                            $response = array('status' => 'error', 'text' => $result['message']);
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorRemoveData':
                    {
                        $kpiMainIndicatorId = issetVar($parameters['indicatorId']);
                        
                        if ($kpiMainIndicatorId) {
                            
                            $selectedRows = isset($parameters['selectedRows']) ? Input::param($parameters['selectedRows']) : null;
                            
                            if ($selectedRows) {
                                $this->load->model('mdform', 'middleware/models/');
                                
                                $_POST['indicatorId'] = $kpiMainIndicatorId;
                                $_POST['crudIndicatorId'] = issetVar($parameters['crudIndicatorId']);
                                $_POST['selectedRows'] = Arr::changeKeyUpper($selectedRows);
                                
                                $response = $this->model->removeKpiDynamicDataModel();
                                
                                if ($response['status'] == 'success') {
                                    Mdform::clearCacheData($kpiMainIndicatorId);
                                }
                                
                                $response = Arr::changeKeyName($response, 'message', 'text');
                                
                            } else {
                                $response = array('status' => 'error', 'text' => 'Invalid parameters!');
                            }
                        } else {
                            $response = array('status' => 'error', 'text' => 'Invalid indicatorId!');
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorGetMetaInfo':
                    {
                        $kpiMainIndicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $result = $this->model->getKpiIndicatorMetaInfoModel($kpiMainIndicatorId);

                        if ($result['status'] == 'success') {
                            $response = array('status' => 'success', 'result' => $result['detailData']);
                        } else {
                            $response = array('status' => 'error', 'text' => $result['message']);
                        }
                    }
                    break;
                
                    case 'kpiIndicatorModuleMenu':
                    {
                        $this->load->model('mdmenu', 'middleware/models/');
                        $menuData = $this->model->getKpiIndicatorMenuModel($isMobile);
                        
                        $response = array('status' => 'success', 'result' => $menuData);
                    }
                    break;
                    
                    case 'kpiIndicatorChildMenu':
                    {
                        $menuId = Input::param($parameters['menuId']);
                        
                        $this->load->model('mdmenu', 'middleware/models/');
                        $menuData = $this->model->getKpiMenuListByParentIdCacheModel($menuId, $isMobile);
                        
                        $response = array('status' => 'success', 'result' => $menuData);
                    }
                    break;
                
                    case 'kpiIndicatorChartDataConfig':
                    {
                        $_POST = $parameters;

                        $this->load->model('mdform', 'middleware/models/');
                        $result = $this->model->kpiIndicatorChartDataConfigModel();

                        if ($result['status'] == 'success') {
                            $response = $result;
                        } else {
                            $response = array('status' => 'error', 'text' => $result['message']);
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorDashboardConfig':
                    {
                        $indicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $dashboardConfigs = $this->model->getKpiDashboardChartsModel($indicatorId);

                        if ($dashboardConfigs['layoutCode']) {
                            $response = array('status' => 'success', 'result' => $dashboardConfigs);
                        } else {
                            $response = array('status' => 'error', 'text' => 'Тохиргоо олдсонгүй!');
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorDashboardFilterConfig':
                    {
                        $indicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $filterParams = $this->model->getKpiDashboardFilterParamsModel($indicatorId);
                        $response = array('status' => 'success', 'result' => array());
                        
                        if ($filterParams) {
            
                            $_POST['isChartList'] = 1;

                            $filterData = $this->model->filterKpiIndicatorValueFormModel($indicatorId, $filterParams);

                            if ($filterData['status'] == 'success') {
                                $response['result'] = $filterData['data'];
                            } 
                        } 
                    }
                    break;
                    
                    case 'kpiIndicatorCharts':
                    {
                        $indicatorId = Input::param($parameters['indicatorId']);

                        $this->load->model('mdform', 'middleware/models/');
                        $chartConfigs = $this->model->getKpiIndicatorChildChartsModel($indicatorId);

                        if ($chartConfigs['status'] == 'success') {
                            $response = array('status' => 'success', 'result' => $chartConfigs['data']);
                        } else {
                            $response = array('status' => 'error', 'text' => $chartConfigs['message']);
                        }
                    }
                    break;
                    
                    case 'kpiIndicatorExcelExport':
                    {
                        $indicatorId = Input::param($parameters['indicatorId']);
                        
                        $_POST['nult'] = 1;
                        $_POST['indicatorId'] = $indicatorId;
                        $_POST['fDownload'] = 1;
                        
                        if ($criteria = issetParam($parameters['criteria'])) {
                            $_POST['criteria'] = $criteria;
                        }
                        
                        if (issetVar($parameters['isBase64']) == '1') {
                            
                            $response = (new Mdform())->indicatorExcelExport(true);
                            
                            if ($response['status'] == 'error') {
                                $response['text'] = $response['message'];
                                unset($response['message']);
                            }
                            
                        } else {
                            (new Mdform())->indicatorExcelExport();
                            exit;
                        }
                    }
                    break;
                    
                    default: 
                    {
                        $response = array('status' => 'error', 'text' => 'Wrong command!');
                    }
                    break;
                }

                array_walk_recursive($response, function (&$item, $key) {
                    $item = null === $item ? '' : $item;
                });
                
                if ($isMobile || $isLowerCase) {
                    $response = Arr::changeKeyLower($response);
                }
            }
        
        } else {
            $response = array('status' => 'error', 'text' => 'Invalid command!');
        }
        
        if ($isMobile || self::isPhpGzCompressionInProcess() == false) {
            
            $compressed = json_encode(array('response' => $response), JSON_UNESCAPED_UNICODE);
            header('Content-Type: application/json; charset=utf-8');
            
        } else {
            
            $compressed = gzencode(json_encode(array('response' => $response), JSON_UNESCAPED_UNICODE));

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($compressed));
        }

        echo $compressed;
    }
    
    public function isPhpGzCompressionInProcess() {

        if (in_array('ob_gzhandler', ob_list_handlers())) {
            return true;
        }

        if (extension_loaded('zlib') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            return true;
        }

        return false;
    }
    
    public function uploadFile() {
        
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization");
        
        $token = Input::post('token');
        
        if ($token) {
            
            $token = str_replace(array('tttnmhttt', 'ttttntsuttt'), array('+', '='), $token);
            $tokenDate = Hash::decryption($token);
            
            if ((strtotime(Date::currentDate('Y-m-d H:i:s')) - strtotime($tokenDate)) > 20) {
                set_status_header(400);
                $response = array('status' => 'error', 'message' => 'Bad Request /expiredate/');
            } else {

                $uploadResult = Mdwebservice::bpFileUpload(array('ID' => getUID(), 'META_TYPE_CODE' => 'file'), $_FILES['file']);

                if ($uploadResult) {
                    $val = $uploadResult['path'] . $uploadResult['newname'];
                    $response = array('status' => 'success', 'files' => $val);
                } else {
                    $response = array('status' => 'error', 'message' => 'Unkhown error!');
                }
            }
        } 
        
        echo json_encode(array('response' => $response), JSON_UNESCAPED_UNICODE);
    }
    
}