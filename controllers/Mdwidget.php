<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

class Mdwidget extends Controller {

    private static $viewPath = 'middleware/views/widget/';
    public static $_trackerStatus = array(0 => 'NotFound', 1 => 'Editing', 2 => 'MustSave', 3 => 'Corrupted', 4 => 'Closed');  
    public static $configs = array();
    public static $loadedSectionCss = array();

    public function __construct() {
        parent::__construct();
    }
    
    public function bpDetailAvailableWidgets($widgetCode) {
        
        $availableWidgets = array(
            '14' => array(
                'topAddRow' => true, 
                'topAddOneRow' => true
            ), 
            'card_one_column' => array(
                'topAddRow' => true, 
                'topAddOneRow' => true
            ), 
            'detail_withoutlabel1' => array(
                'topAddRow' => true, 
                'topAddOneRow' => true
            ), 
            'detail_circle_photo' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_colorable_tag' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_attachments' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_attachments2' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_user_card_001' => array(
                'topAddRow' => true, 
                'topAddOneRow' => false
            ), 
            'detail_circle_icon' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_circle_file' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_notes' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_doc_history' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_buttons' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'detail_file_preview_001' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            ), 
            'pfprocessphotowidget' => array(
                'topAddRow' => true, 
                'topAddOneRow' => false
            ), 
            'pfprocessfilewidget' => array(
                'topAddRow' => true, 
                'topAddOneRow' => false
            ), 
            'pfprocesscommentwidget' => array(
                'topAddRow' => true, 
                'topAddOneRow' => false
            ),
            'detail_frame_paper_tree' => array(
                'topAddRow' => false, 
                'topAddOneRow' => false
            )            
        );
        
        if (isset($availableWidgets[$widgetCode])) {
            return $availableWidgets[$widgetCode];
        }
        
        return null;
    }
    
    public function bpDetailFrameWidgets($widgetCode, $methodId, $row, $fillParamData) {
        
        $availableWidgets = array(
            'detail_frame_paper_001' => array(
                'topAddRow' => true, 
                'topAddOneRow' => true
            ),
            'detail_frame_paper_tree' => array(
                'topAddRow' => true, 
                'topAddOneRow' => false
            ),
        );
        
        if (isset($availableWidgets[$widgetCode])) {
            
            $this->view->methodId = $methodId;
            $this->view->paramConfig = $row;
            $this->view->fillParamData = $fillParamData;
            
            return $this->view->renderPrint('sub/frame/' . $widgetCode, self::$viewPath);
        }
        
        return null;
    }
    
    public function run($args = array()) {
        
        $this->view->uniqId = getUID();
        $this->view->configs = array('height' => '500px');
        $this->view->widgetData = $args['data'];
        
        $widgetCode = $args['widgetCode'];
            
        return $this->view->renderPrint('sub/' . $widgetCode, self::$viewPath);
    }
    
    public function runBpDetail($args = array()) {
        
        $this->view->methodId = $args['methodId'];
        $this->view->uniqId = $args['uniqId'];
        $this->view->row = $args['row'];
        $this->view->fillParamData = $args['fillParamData'];
        
        $widgetCode = $args['row']['widgetCode'];
            
        return $this->view->renderPrint('sub/' . $widgetCode, self::$viewPath);
    }
    
    public function bpDetailAddRow($args = array()) {
        
        $this->view->methodId = $args['methodId'];
        $this->view->uniqId = $args['uniqId'];
        $this->view->row = $args['row'];
        
        $widgetCode = issetParam($this->view->row['widgetCode']);
        
        $topCustomAddRow = $bottomCustomAddRow = $customLocationAddBtn = '';
        
        if ($widgetCode == 'detail_circle_photo' || $widgetCode == 'detail_colorable_tag' 
            || $widgetCode == 'detail_attachments' || $widgetCode == 'detail_attachments2' 
            || $widgetCode == 'detail_user_card_001') {
            $bottomCustomAddRow = $this->view->renderPrint('sub/addrow/' . $widgetCode, self::$viewPath);
        }

        if ($widgetCode == 'detail_frame_paper_tree') {
            $customLocationAddBtn = $this->view->renderPrint('sub/addrow/' . $widgetCode, self::$viewPath);
        }

        return array('topCustomAddRow' => $topCustomAddRow, 'bottomCustomAddRow' => $bottomCustomAddRow, 'customLocationAddBtn' => $customLocationAddBtn);
    }
    
    public function widgetDataTemplate($widgetCode) {
        
        $widgetDataTmplt = array();
        
        if ($widgetCode == 'chart_donut1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => '',
                'position3' => '',
                'position4' => array(
                    array(
                        'position5' => '', 
                        'position6' => ''
                    )
                )
            );
            
        } elseif ($widgetCode == 'chart_gauge1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => '',
                'position3' => array(
                    array(
                        'position4' => '', 
                        'position5' => '', 
                        'position6' => '', 
                        'position7' => ''
                    )
                )
            );
            
        } elseif ($widgetCode == 'chart_zoomable_bubble1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => '',
                'position3' => '',
                'position4' => '',
                'position5' => array(
                    array(
                        'position6' => '', 
                        'position7' => '', 
                        'position8' => '', 
                        'position9' => '', 
                        'position10' => '', 
                        'position11' => '', 
                        'position12' => ''
                    )
                )
            );
            
        } elseif ($widgetCode == 'chart_radar1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => array(
                    array(
                        'position3' => '', 
                        'position4' => '', 
                        'position5' => ''
                    )
                )
            );
            
        } elseif ($widgetCode == 'chart_stacked_bar1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => array(
                    array(
                        'position3' => '', 
                        'position4' => '', 
                        'position5' => ''
                    )
                )
            );
            
        } elseif ($widgetCode == 'card_with_icons1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => '',
                'position3' => '',
                'position4' => ''
            );
            
        } elseif ($widgetCode == 'grouped_list1') {
            
            $widgetDataTmplt = array(
                'position1' => '',
                'position2' => ''
            );
        }
        
        return $widgetDataTmplt;
    }
    
    public function generateBpSectionCss($methodId, $widgetCode) {
        
        if (!isset(self::$loadedSectionCss[$methodId][$widgetCode])) {
            
            $css = $this->view->renderPrint('sub/css/' . $widgetCode, self::$viewPath);
            $css = str_replace(array('<style type="text/css">', '</style>'), '', $css);
            $css = str_replace('methodId', $methodId, $css);
            
            self::$loadedSectionCss[$methodId][$widgetCode] = 1;
            
        } else {
            $css = null;
        }
        
        return $css;
    }
    
    public function generateBpSectionWidget($methodId, $sectionCode, $widgetCode, $fillData) {
        
        $this->view->methodId = $methodId;
        $this->view->sectionCode = $sectionCode;
        $this->view->fillData = $fillData;
        
        $render = $this->view->renderPrint('sub/section/' . $widgetCode, self::$viewPath);
        
        return $render;
    }
    
    public function runField($args) {
        
        $widgetCode = $args['widgetCode'];
        $this->view->control = $args['control'];
        
        if (file_exists(self::$viewPath.'sub/'.$widgetCode.'.php')) {
            
            $this->view->methodId = $args['methodId'];
            $this->view->methodRow = $args['methodRow'];
            $this->view->paramData = issetParamArray($args['paramData']);
            
            return $this->view->renderPrint('sub/' . $widgetCode, self::$viewPath);
            
        } else {
            return $this->view->control;
        }
    }
    
    public function runGauge() {
        
        $this->view->js = AssetNew::metaOtherJs();
        $this->view->fullUrlCss = AssetNew::amChartCss();
        $this->view->fullUrlJs = AssetNew::amChartJs();
        
        $this->view->isAjax = is_ajax_request();
        $this->view->uniqId = getUID();
        
        $this->view->configs = array('height' => '500px');
        $widgetCode = 'chart_radar1';
        
        $this->view->widgetData = array(
            'position1' => 'Title',
            'position2' => array(
                array(
                    'position3' => 'mn', 
                    'position4' => '45', 
                    'position5' => 'Legend 1', 
                ), 
                array(
                    'position3' => 'en', 
                    'position4' => '89', 
                    'position5' => 'Legend 1', 
                ), 
                array(
                    'position3' => 'ru', 
                    'position4' => '15', 
                    'position5' => 'Legend 1', 
                ), 
                
                array(
                    'position3' => 'mn', 
                    'position4' => '66', 
                    'position5' => 'Legend 2', 
                ), 
                array(
                    'position3' => 'en', 
                    'position4' => '33', 
                    'position5' => 'Legend 2', 
                ), 
                array(
                    'position3' => 'ru', 
                    'position4' => '11', 
                    'position5' => 'Legend 2', 
                )
            )
        );
        
        if (!$this->view->isAjax) {
            $this->view->render('header');
        }
        
        $this->view->render('sub/' . $widgetCode, self::$viewPath);

        if (!$this->view->isAjax) {
            $this->view->render('footer');
        }
    }
    
    public function positionList() {
        $position = array();
                    
        for ($p = 1; $p <= 100; $p++) {
            $position[] = array('id' => $p, 'name' => 'Position '.$p);
        }
        
        return $position;
    }
    
    public function index($metaDataId = '') {
        
        Auth::handleLogin();
        
        if ($metaDataId == '') {
            Message::add('e', '', 'back');
        }
        
        $this->load->model('mdmetadata', 'middleware/models/');
        
        $metaRow = $this->model->getMetaDataModel($metaDataId, true);
        
        if (!$metaRow) {
            Message::add('e', '', 'back');
        }
        
        $this->view->title = Lang::line($metaRow['META_DATA_NAME']);
        $this->view->widgetCode = $metaRow['META_DATA_CODE'];
        $this->view->metaDataId = $metaDataId;
        $this->view->uniqId = getUID();
        
        $this->view->css = AssetNew::metaCss();
        $this->view->fullUrlCss = AssetNew::amChartCss();
        
        $this->view->js = array_unique(array_merge(
            AssetNew::metaOtherJs(),
            AssetNew::highchartJs()
        ));
        $this->view->fullUrlJs = AssetNew::amChartJs();
        
        $this->view->folderId = '';
        $this->view->metaBackLink = 'mdmetadata/system';
        
        if (isset($metaRow['FOLDER_ID'])) {
            $this->view->folderId = $metaRow['FOLDER_ID'];
            $this->view->metaBackLink = 'mdmetadata/system#objectType=folder&objectId=' . $metaRow['FOLDER_ID'];
        }

        $this->view->isBackLink = Config::getFromCache('CONFIG_OBJECT_BACKLINK');
        $this->view->isAjax = is_ajax_request();
        
        if (!$this->view->isAjax) {
            $this->view->render('header');
        }
        
        $this->view->render('index', self::$viewPath);

        if (!$this->view->isAjax) {
            $this->view->render('footer');
        }
    }
    
    public function runWidget() {
        
        $widgetCode = strtolower(Input::post('widgetCode'));
        
        if (!$widgetCode) {
            set_status_header(404);
        
            $err = Controller::loadController('Err');
            $err->index();
            exit;
        }
        
        $uniqId = Input::postCheck('uniqId') === false ? getUID() : Input::post('uniqId');
        $response = array();
        
        switch ($widgetCode) {
            
            case 'exchangerate':
                
                $paramData = Input::post('paramData');
            
                if (is_countable($paramData) && count($paramData)) {
                    foreach ($paramData as $inputField) {
                        if (strtolower($inputField['inputPath']) == 'currencyid') {
                            $currencyId = $inputField['value'];
                        }
                    }
                }

                $param = array(
                    'currencyId' => isset($currencyId) ? $currencyId : '11343899901327', 
                    'startDate' => Ue::sessionFiscalPeriodStartDate(), 
                    'endDate' => Ue::sessionFiscalPeriodEndDate()
                );
                $response = self::exchangeRate($uniqId, $param);
                
            break;    
            
            case 'sales':
                
                $response = self::sales($uniqId);

            break;    
            
            case 'sales_club':
                $response = self::sales_club($uniqId);
            break;

            case 'sales_juicebar':
                $response = self::sales_juicebar($uniqId);
            break;

            case 'sales_shangrila':
                $response = self::sales_shangrila($uniqId);
            break;

            case 'club_cash':
                $response = self::club_cash($uniqId);
            break;

            case 'club_bank':
                $response = self::club_bank($uniqId);
            break;

            case 'dcreport':
                $response = self::dcreport($uniqId);

            break;

            case 'dcreport2':
                $response = self::dcreport2($uniqId);
            break;  

            case 'dcreport3':
                $response = self::dcreport3($uniqId);
            break;   

            case 'dcreport4':
                $response = self::dcreport4($uniqId);
            break;         

            case 'rclub':
                
                $response = self::rclub($uniqId);
                
            break;             

            case 'rclub2':
                
                $response = self::rclub2($uniqId);
                
            break;             
            
            case 'cash':
                
                $response = self::cash($uniqId);
                
            break;    
            
            case 'bank':
                
                $response = self::bank($uniqId);
                
            break;    
            
            case 'supply':
                
                $response = self::supply($uniqId);
                
            break;    
            
            case 'industry':
                
                $response = self::industry($uniqId);
                
            break;    
        
            case 'salesstore':
                
                $response = self::salesStore($uniqId);
                
            break;        
        
            case 'hrms':
                
                $response = self::hrms($uniqId);
                
            break;        
        
            case 'salary':
                
                $response = self::salary($uniqId);
                
            break;        
        
            case 'recruitment_status':
                
                $response = self::recruitment_status($uniqId, Input::post('startDate'), Input::post('endDate'));
                
            break;        
        
            case 'hrm_activity':
                
                $response = self::hrm_activity($uniqId, Input::post('startDate'), Input::post('endDate'));
                
            break;        
        
            case 'suma':
                
                $response = self::supplyManagement($uniqId);
                
            break;        
        
            case 'widget_user_comment':
                
                $response = self::userComment($uniqId, Input::postData());
                
            break;            
        
            case 'widget_news':
                
                $response = self::news($uniqId, Input::postData());
                
            break;          
        
            case 'hrm_uz':
                
                $response = self::hrm_uz($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'));
                
            break;     
        
            case 'widgethrmtimesheetlog':
                
                $response = self::hrmTimesheetLog($uniqId);
                
            break;    
        
            case 'widgethrmtimesheetlogv2':
            case 'widgethrmtimesheetlogv3':
                $response = self::hrmTimesheetLogV2($uniqId, $widgetCode);
                break;    
        
            case 'glance':
                
                $response = self::glanceWigdetDashboard($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                break;    
        
            case 'withother':
                $response = self::glanceWithOtherChartsDashboard($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                break;    

            case 'project':
                
                $response = self::project($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'project4':
                
                $response = self::project4($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'project5':
                
                $response = self::project5($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'project3':
                
                $response = self::project3($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'project1':
                
                $response = self::project1($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'project2':
                
                $response = self::project2($uniqId, Input::post('startDate'), Input::post('endDate'), Input::post('depIds'), Input::post('isHierarchy'));
                
            break;                

            case 'contentwidget':
                
                $response = self::contentWidget($uniqId, Input::postData());
                
            break;                
        
            case 'supplywidget':
                
                $response = self::widgetVeriSupply($uniqId);
                
            break;            
        
            case 'metro_widget1':
                
                $response = self::metrowidget1($uniqId);
                
            break;            
        
            case 'metro_widget2':
                
                $response = self::metrowidget2($uniqId);
                
            break;            
        
            case 'metro_widget3':
                
                $response = self::metrowidget3($uniqId);
                
            break;            
        }
        
        if (!is_ajax_request()) {
            return $response;
        } else {
            echo json_encode($response); exit;
        }
    }
    
    public function exchangeRate($uniqId, $param) {
        
        $this->view->uniqId = $uniqId;
        $this->view->rateData = $this->model->exchangeRateModel($param);
        $this->view->currencyName = $this->model->getCurrencyNameModel($param['currencyId']);
        
        return array(
            'html' => $this->view->renderPrint('exchangeRate', self::$viewPath)
        );
    }
    
    public function getWidgetDataSource() {
        
        $widgetCode = strtolower(Input::post('widgetCode'));
        $response = null;
                
        switch ($widgetCode) {
            
            case 'exchangerate':
                
                $paramData = Input::post('paramData');
            
                if (count($paramData)) {
                    foreach ($paramData as $inputField) {
                        if (strtolower($inputField['inputPath']) == 'currencyid') {
                            $currencyId = $inputField['value'];
                        }
                    }
                }

                $param = array(
                    'currencyId' => isset($currencyId) ? $currencyId : '11343899901327', 
                    'startDate' => Ue::sessionFiscalPeriodStartDate(), 
                    'endDate' => Ue::sessionFiscalPeriodEndDate()
                );
                
                $rateData = $this->model->exchangeRateModel($param);
                $array = array();
                
                if ($rateData) {
                    
                    foreach ($rateData as $row) {
                        $array[] = array(
                            'date' => Date::formatter($row['BANK_DATE'], 'Y-m-d'), 
                            'value' => $row['RATE']
                        );
                    }
                } 
                
                $response = array('widgetData' => $array, 'title' => $this->model->getCurrencyNameModel($param['currencyId']));
                
            break;    
        }
        
        echo json_encode($response); exit;
    }
    
    public function dcreport($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->load->model('mdwidget', 'middleware/models/');

        $repDate = $this->view->dcReport = $this->model->loadApartmentModel('1547034302529');
        $this->view->resultKpi = $this->model->getTemplateDCModel();

        return array(
            'html' => $this->view->renderPrint('dcreport', self::$viewPath)
        );
    }

    public function dcreport2($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->load->model('mdwidget', 'middleware/models/');
     
        $repDate2 = $this->view->dcReport2 = $this->model->loadRateOfficeModel('21543754315276');

        $this->view->resultKpi = $this->model->getTemplateDCModel();
        
        return array(
            'html' => $this->view->renderPrint('dcreport2', self::$viewPath)
        );
    }
    
    public function dcreport4($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->load->model('mdwidget', 'middleware/models/');
     
        $repDate4 = $this->view->dcReport4 = $this->model->loadRateHashaaModel('1544590987343');
        //var_dump($repDate4);die();
        $this->view->resultKpi = $this->model->getTemplateDCModel();
        return array(
            'html' => $this->view->renderPrint('dcreport4', self::$viewPath)
        );
    }

    public function dcreport3($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->load->model('mdwidget', 'middleware/models/');
     
        $repDate = $this->view->dcReport = $this->model->loadRateModel('1538098354982');
        $this->view->resultKpi = $this->model->getTemplateDCModel();
        return array(
            'html' => $this->view->renderPrint('dcreport3', self::$viewPath)
        );
    }

    public function dcreportprocess() {
        $resultKpi = $this->model->saveTemplateDCModel();
        
        if($resultKpi['status'] == 'error') {
            jsonResponse(array(
                'status' => 'error',
                'message' => $resultKpi['text']
            ));
        } else {
            jsonResponse(array(
                'status' => 'success',
                'message' => 'Амжилттай'
            ));
        }    
    }   
    
    public function sales_club($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSalesByChannelType = $this->model->getDataSalesByChannelTypeModel();
        $this->view->getDataActiveMember = $this->model->getDataActiveMemberModel();
        $this->view->getDataSalesByType = $this->model->getDataSalesFtByTypeModel();
        $this->view->getDataSalesByActivity = $this->model->getDataSalesByActivityModel();
        $this->view->getDataSalesList = $this->model->getDataSalesFtListModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveFtItemModel();        

        $this->load->model('mdwidget', 'middleware/models/');
        $this->view->numberPlan = $this->model->loadListModel('1561960420548');
        //$this->view->getAllCountSales = $this->model->getAllCountSalesModel();        
        
        // var_dump($this->view->getDataActiveMember);die;
        return array(
            'html' => $this->view->renderPrint('sales_club', self::$viewPath)
        );
    }    

    public function sales_juicebar($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId; 
        $this->view->getDataSalesByChannelType = $this->model->getDataSalesByChannelTypeModel();
        $this->view->getDataActiveMember = $this->model->getDataActiveMemberModel();
        $this->view->getDataSalesByType = $this->model->getDataSalesFtByJuiseModel();
        $this->view->getDataSalesByActivity = $this->model->getDataSalesByActivityModel();
        // $this->view->getDataSalesList = $this->model->getDataSalesFtListModel();
        $this->view->getDataSalesList = $this->model->getPaymentJuiceBarSaleModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveFtItemJuiseModel();        

        $this->load->model('mdwidget', 'middleware/models/');
        // $this->view->numberPlan = $this->model->loadListModel('1561960420548');
        $this->view->numberPlan = $this->model->loadListModel('1568963630290');
        //$this->view->getAllCountSales = $this->model->getAllCountSalesModel();        
        
        // var_dump($this->view->getDataActiveMember);die;
        return array(
            'html' => $this->view->renderPrint('sales_juicebar', self::$viewPath)
        );
    }   

    public function sales_shangrila($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId; 
        $this->view->getDataSalesByChannelType = $this->model->getDataSalesByChannelTypeModel();
        $this->view->getDataActiveMember = $this->model->getDataActiveMemberModel();
        $this->view->getDataSalesByType = $this->model->getDataSalesFtByShangrilaModel();
        $this->view->getDataSalesByActivity = $this->model->getDataSalesByActivityModel();
        // $this->view->getDataSalesList = $this->model->getDataSalesFtListModel(); 
        $this->view->getDataSalesList = $this->model->getPaymentShangrilaSaleModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveFtItemShangrilaModel();        

        $this->load->model('mdwidget', 'middleware/models/');
        // $this->view->numberPlan = $this->model->loadListModel('1561960420548');
        $this->view->numberPlan = $this->model->loadListModel('1571730684731');
        //$this->view->getAllCountSales = $this->model->getAllCountSalesModel();        
        
        // var_dump($this->view->getDataActiveMember);die;
        return array(
            'html' => $this->view->renderPrint('sales_shangrila', self::$viewPath)
        );
    }    

    public function club_cash($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getCashBeginAmount = $this->model->getCashBeginAmountModel();
        $this->view->getAllCashCurrency = $this->model->getAllCashCurrencyModel();
        $this->view->getDataCashIncome = $this->model->getDataCashIncomeModel();
        $this->view->getDataCashOutcome = $this->model->getDataCashOutcomeModel();
        $this->view->getCashEndAmount = $this->model->getCashEndAmountModel();
        $this->view->getDataCashByActivity = $this->model->getDataCashByActivityModel();      
        
        return array(
            'html' => $this->view->renderPrint('club_cash', self::$viewPath)
        );
    } 

    public function club_bank($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getBankBeginAmount = $this->model->getBankBeginAmountModel();
        $this->view->getAllBankCurrency = $this->model->getAllBankCurrencyModel();
        $this->view->getDataBankIncome = $this->model->getDataBankIncomeModel();
        $this->view->getDataBankOutcome = $this->model->getDataBankOutcomeModel();
        $this->view->getDataBankByActivity = $this->model->getDataBankByActivityModel();      
        
        return array(
            'html' => $this->view->renderPrint('club_bank', self::$viewPath)
        );
    }    

    public function sales($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSalesByChannelType = $this->model->getDataSalesByChannelTypeModel();
        $this->view->getDataSalesByType = $this->model->getDataSalesByTypeModel();
        $this->view->getDataSalesByActivity = $this->model->getDataSalesByActivityModel();
        $this->view->getDataSalesList = $this->model->getDataSalesListModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveItemModel();        

        $this->load->model('mdwidget', 'middleware/models/');
        $this->view->numberPlan = $this->model->loadListModel('1527578362266');
        //$this->view->getAllCountSales = $this->model->getAllCountSalesModel();        
        
        return array(
            'html' => $this->view->renderPrint('sales', self::$viewPath)
        );
    }    

    public function rclub($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSalesByChannelType = $this->model->getDataRSalesByChannelTypeModel();
        $this->view->getDataSalesByType = $this->model->getDataRSalesByTypeModel();
        $this->view->getDataSalesByActivity = $this->model->getDataRSalesByActivityModel();
        $this->view->getDataSalesList = $this->model->getDataRSalesListModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveRItemModel();        
        
        return array(
            'html' => $this->view->renderPrint('rclub', self::$viewPath)
        );
    }     

    public function rclub2($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSalesByChannelType = $this->model->getDataR2SalesByChannelTypeModel();
        $this->view->getDataSalesByType = $this->model->getDataR2SalesByTypeModel();
        $this->view->getDataSalesByActivity = $this->model->getDataR2SalesByActivityModel();
        $this->view->getDataSalesList = $this->model->getDataR2SalesListModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveR2ItemModel();        
        
        return array(
            'html' => $this->view->renderPrint('rclub2', self::$viewPath)
        );
    }
    
    public function cash($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getCashBeginAmount = $this->model->getCashBeginAmountModel();
        $this->view->getAllCashCurrency = $this->model->getAllCashCurrencyModel();
        $this->view->getDataCashIncome = $this->model->getDataCashIncomeModel();
        $this->view->getDataCashOutcome = $this->model->getDataCashOutcomeModel();
        $this->view->getCashEndAmount = $this->model->getCashEndAmountModel();
        $this->view->getDataCashByActivity = $this->model->getDataCashByActivityModel();      
        
        return array(
            'html' => $this->view->renderPrint('cash', self::$viewPath)
        );
    }    
    
    public function bank($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getBankBeginAmount = $this->model->getBankBeginAmountModel();
        $this->view->getAllBankCurrency = $this->model->getAllBankCurrencyModel();
        $this->view->getDataBankIncome = $this->model->getDataBankIncomeModel();
        $this->view->getDataBankOutcome = $this->model->getDataBankOutcomeModel();
        $this->view->getDataBankByActivity = $this->model->getDataBankByActivityModel();      
        
        return array(
            'html' => $this->view->renderPrint('bank', self::$viewPath)
        );
    }    
    
    public function supply($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSupplyByActivity = $this->model->getDataSupplyByActivityModel();
        $this->view->getDataTop5Supplier = $this->model->getDataTop5SupplierModel();        
        $this->view->getTopFiveSupplyItem = $this->model->getTopFiveSupplyItem();        
        $this->view->getDataItemReturnSupply = $this->model->getDataItemReturnSupplyModel();        
        
        return array(
            'html' => $this->view->renderPrint('supply', self::$viewPath)
        );
    }    
    
    public function industry($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataIndustryByActivity = $this->model->getDataIndustryByActivityModel();
        $this->view->getTopFiveIndustryItem = $this->model->getTopFiveIndustryItem();
        $this->view->getDataItemReturnIndustry = $this->model->getDataItemReturnIndustryModel();        
        $this->view->getDataItemReturnPerIndustry = $this->model->getDataItemReturnPerIndustryModel();        
        
        return array(
            'html' => $this->view->renderPrint('industry', self::$viewPath)
        );
    }    
    
    public function salesStore($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataSalesByType = $this->model->getDataSalesStoreByTypeModel();
        $this->view->getDataSalesByChannelType = $this->model->getDataSalesStoreByChannelTypeModel();
        $this->view->getDataSalesByActivity = $this->model->getDataSalesStoreByActivityModel();
        $this->view->getDataSalesList = $this->model->getDataSalesStoreListModel();
        $this->view->getDataSalesDayYearList = $this->model->getDataSalesDayYearChartModel();        
        $this->view->getTopFiveItem = $this->model->getTopFiveItemStoreModel();        

        $this->load->model('mdwidget', 'middleware/models/');
        $this->view->numberPlan = $this->model->loadListModel('1527658935041092');        
        
        return array(
            'html' => $this->view->renderPrint('salesStore', self::$viewPath)
        );
    }    
    
    public function hrms($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataHrmsCart = $this->model->getDataHrmsCartModel();
        $this->view->getDataHrmsByType = $this->model->getDataHrmsByTypeModel();
        $this->view->getDataHrmsList = $this->model->getDataHrmsListModel();
        $this->view->getDataHrmsByDepartment = $this->model->getDataHrmsByDepartmentModel();        
        $this->view->getDataHrmsByRegimen = $this->model->getDataHrmsByRegimenModel();        
        $this->view->getDataHrmsByPension = $this->model->getDataHrmsByPensionModel();        
        $this->view->getDataHrmsByWorkOut = $this->model->getDataHrmsByWorkOutModel();        
        $this->view->getDataTimeByDepartment = $this->model->getDataTimeByDepartmentModel();
        
        return array(
            'html' => $this->view->renderPrint('hrms', self::$viewPath)
        );
    }    
    
    public function salary($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->getDataCart1Salary = $this->model->getDataCart1SalaryModel();
        $this->view->getDataCart2Salary = $this->model->getDataCart2SalaryModel();
        $this->view->getDataCart3Salary = $this->model->getDataCart3SalaryModel();
        $this->view->getDataCart4Salary = $this->model->getDataCart4SalaryModel();
        $this->view->getDataHrmsByType = $this->model->getDataHrmsByTypeModel();
        $this->view->getDataHrmsList = $this->model->getDataHrmsListModel();
        $this->view->getDataHrmsByDepartment = $this->model->getDataHrmsByDepartmentModel();        
        $this->view->getDataHrmsByRegimen = $this->model->getDataHrmsByRegimenModel();        
        $this->view->getDataHrmsByPension = $this->model->getDataSalaryByPensionModel();        
        $this->view->getDataHrmsByPension1 = $this->model->getDataSalaryByPension1Model();        
        $this->view->getDataHhoatSalary = $this->model->getDataHhoatSalaryModel();        
        $this->view->getDataRestYearSalary = $this->model->getDataRestYearSalaryModel();
        
        return array(
            'html' => $this->view->renderPrint('salary', self::$viewPath)
        );
    }    
    
    public function recruitment_status($uniqId, $startDate, $endDate) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->openRoles = $this->model->getOpenRolesModel();
        $this->view->filledRoles = $this->model->getFilledRolesModel();
        $this->view->getDataSalesByActivity = $this->model->getDataRecruitmentModel();   
        $this->view->startDate = $startDate;
        $this->view->endDate = $endDate;
        
        return array(
            'html' => $this->view->renderPrint('recruitment_status', self::$viewPath)
        );
    }    
    
    public function hrm_activity($uniqId, $startDate, $endDate) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->list1 = $this->model->list1Model($startDate, $endDate);
        $this->view->list22 = $this->model->list22Model($startDate, $endDate);
        $this->view->list3 = $this->model->list3Model($startDate, $endDate);
        $this->view->list24 = $this->model->list24Model($startDate, $endDate);
        $this->view->list25 = $this->model->list25Model($startDate, $endDate);
        $this->view->list31 = $this->model->list31Model($startDate, $endDate);
        $this->view->list41 = $this->model->list41Model($startDate, $endDate);   
        $this->view->list42 = $this->model->list42Model($startDate, $endDate);   
        $this->view->list51 = $this->model->list51Model($startDate, $endDate);   
        $this->view->list52 = $this->model->list52Model($startDate, $endDate);   
        $this->view->list53 = $this->model->list53Model($startDate, $endDate);   
        $this->view->list54 = $this->model->list54Model($startDate, $endDate);   
        $this->view->list55 = $this->model->list55Model($startDate, $endDate);   
        $this->view->list61 = $this->model->list61Model($startDate, $endDate);   
        $this->view->list62 = $this->model->list62Model($startDate, $endDate);   
        $this->view->list72 = $this->model->list72Model($startDate, $endDate);   
        $this->view->list82 = $this->model->list82Model($startDate, $endDate);   
        $this->view->list91 = $this->model->list91Model($startDate, $endDate);   
        $this->view->list92 = $this->model->list92Model($startDate, $endDate);   
        $this->view->filledRoles = $this->model->getFilledRolesModel();
        $this->view->startDate = $startDate;
        $this->view->endDate = $endDate;
        
        return array(
            'html' => $this->view->renderPrint('hrm_activity', self::$viewPath)
        );
    } 
    
    public function supplyManagement($uniqId) {
        $this->load->model('dashboard', 'middleware/models/');
        
        $this->view->uniqId = $uniqId;
        $this->view->sumaCart1 = $this->model->sumaCart1Model();
        $this->view->sumaCart2 = $this->model->sumaCart2Model();
        $this->view->sumaCart3 = $this->model->sumaCart3Model();
        $this->view->sumaCart4 = $this->model->sumaCart4Model();
        $this->view->sumaPie5 = $this->model->sumaPie5Model();
        $this->view->sumaPie6 = $this->model->sumaPie6Model();
        
        return array(
            'html' => $this->view->renderPrint('supplyManagement', self::$viewPath)
        );
    }        
    
    public function userComment($uniqId, $param) {
        
        $this->view->uniqId = $uniqId;
        
        $this->load->model('Mdlayoutrender', 'middleware/models/');
        $this->view->getLayoutParamMap = $this->model->getSingleLayoutParamMapModel($param);
        
        $this->load->model('Mdwidget', 'middleware/models/');
        $this->view->layoutParamConfig = $this->model->layoutParamConfigModel($param);
        $this->view->getRows = $this->model->loadListModel($this->view->getLayoutParamMap['BP_META_DATA_ID']);
        
        return array(
            'html' => $this->view->renderPrint('userComment', self::$viewPath)
        );
    }    
    
    public function news($uniqId, $param) {
        
        $this->view->uniqId = $uniqId;
        
        $this->load->model('Mdlayoutrender', 'middleware/models/');
        $this->view->getLayoutParamMap = $this->model->getSingleLayoutParamMapModel($param);
        
        $this->load->model('Mdwidget', 'middleware/models/');
        $this->view->layoutParamConfig = $this->model->layoutParamConfigModel($param);
        $this->view->getRows = $this->model->loadListModel($this->view->getLayoutParamMap['BP_META_DATA_ID']);
        
        return array(
            'html' => $this->view->renderPrint('news', self::$viewPath)
        );
    }    
    
    public function hrm_uz($uniqId, $startDate, $endDate, $depId = '') {
        $this->load->model('dashboard', 'middleware/models/');
        
        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;
        
        $this->view->uniqId = $uniqId;
        $this->view->depList = $this->model->baseHrmUzModel('1522827257749', $startDate, $endDate, '');
        
        $this->view->list1 = $this->model->baseHrmUzModel('1522639396371955', $startDate, $endDate, $depId);
        $this->view->list2 = $this->model->baseHrmUzModel('1522404326607', $startDate, $endDate, $depId);
        $this->view->list3 = $this->model->baseHrmUzModel('1522404517040', $startDate, $endDate, $depId);
        $this->view->list4 = $this->model->baseHrmUzModel('1522404517419', $startDate, $endDate, $depId);
        $this->view->list5 = $this->model->baseHrmUzModel('1522404327737', $startDate, $endDate, $depId);
        $this->view->list6 = $this->model->baseHrmUzModel('1522404327766', $startDate, $endDate, $depId);
        $this->view->list7 = $this->model->baseHrmUzModel('1522404327982', $startDate, $endDate, $depId);
        $this->view->list8 = $this->model->baseHrmUzModel('1522404517460', $startDate, $endDate, $depId);
        $this->view->list9 = $this->model->baseHrmUzModel('1522404328023', $startDate, $endDate, $depId);
        $this->view->list10 = $this->model->baseHrmUzModel('1522404328105', $startDate, $endDate, $depId);
        $this->view->list11 = $this->model->baseHrmUzModel('1522404517231', $startDate, $endDate, $depId);
        $this->view->list12 = $this->model->baseHrmUzModel('1522404518744', $startDate, $endDate, $depId);
        
        $this->view->startDate = $startDate;
        $this->view->endDate = $endDate;
        $this->view->depId = $depId;
        $this->view->depName = $this->model->depNames($depId);
        
        return array(
            'html' => $this->view->renderPrint('hrm_uz', self::$viewPath)
        );
    }    
    
    public function hrmTimesheetLog($uniqId) {
        
        $this->view->uniqId = $uniqId;
        
        self::hrmTimesheetLogLoad();
        
        return array(
            'html' => $this->view->renderPrint('calendar/hrmTimesheetLog', self::$viewPath)
        );
    }
    
    public function hrmTimesheetLogV2($uniqId, $wigCode) {
        
        $this->view->uniqId = $uniqId;
        
        self::hrmTimesheetLogLoadV2();
        
        switch ($wigCode) {
            case 'widgethrmtimesheetlogv3':
                return array(
                    'html' => $this->view->renderPrint('calendar/hrmTimesheetLogV3', self::$viewPath)
                );
                break;
            default :
                return array(
                    'html' => $this->view->renderPrint('calendar/hrmTimesheetLogV2', self::$viewPath)
                );
                break;
        }
    }
    
    public function widgetVeriSupply($uniqId) {
        
        $this->view->uniqId = $uniqId;
        
        self::widgetVeriSupplyDataLoad();
        
        return array(
            'html' => $this->view->renderPrint('veriSupply2', self::$viewPath)
        );
    }
    
    public function metrowidget1($uniqId) {
        
        $this->view->uniqId = $uniqId;
        
        return array(
            'html' => $this->view->renderPrint('metro_widget/widget1', self::$viewPath)
        );
    }
    
    public function metrowidget2($uniqId) {
        
        $this->view->uniqId = $uniqId;
        
        return array(
            'html' => $this->view->renderPrint('metro_widget/widget2', self::$viewPath)
        );
    }
    
    public function metrowidget3($uniqId) {
        $this->view->uniqId = $uniqId;
        
        return array(
            'html' => $this->view->renderPrint('metro_widget/widget3', self::$viewPath)
        );
    }
    
    public function hrmTimesheetLogLoad() {
        
        Auth::handleLogin();
        
        if (Input::postCheck('yearMonth')) {
            $this->view->filterStartDate = Input::post('yearMonth').'-01';
            $this->view->filterEndDate = Input::post('yearMonth').'-'.date('t', strtotime($this->view->filterStartDate));
        } else {
            $this->view->filterStartDate = Date::currentDate('Y-m').'-01';
            $this->view->filterEndDate = Date::currentDate('Y-m').'-'.date('t', strtotime($this->view->filterStartDate));
        }
        
        $criteria = array(
            'sessionEmployeeId' => array(
                array(
                    'operator' => '=',
                    'operand' => Ue::sessionEmployeeId()
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterStartDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterEndDate
                )
            )
        );

        $sidebarDvId = '1525387880376';
        $calendarDvId = '1525387904712';
        
        $this->view->startBgColor = Config::getFromCacheDefault('startBgColor', null, '#199ec7');
        $this->view->endBgColor = Config::getFromCacheDefault('endBgColor',null,'#1BBC9B'); 

        $sidebarData = $this->model->loadListModel($sidebarDvId, $criteria);
        $this->view->sidebarLabelName = $this->model->getHeaderLabelNameModel($sidebarDvId);
        $this->view->sidebarData = isset($sidebarData[0]) ? $sidebarData[0] : array();
        
        $this->view->sidebarHtml = $this->view->renderPrint('calendar/hrmTimesheetLogSidebar', self::$viewPath);
        
        $this->view->calendarData = $this->model->loadListModel($calendarDvId, $criteria);
    }
    
    public function hrmTimesheetLogLoadV2() {
        
        Auth::handleLogin();
        
        if (Input::postCheck('yearMonth')) {
            $this->view->filterStartDate = Input::post('yearMonth').'-01';
            $this->view->filterEndDate = Input::post('yearMonth').'-'.date('t', strtotime($this->view->filterStartDate));
        } else {
            $this->view->filterStartDate = Date::currentDate('Y-m').'-01';
            $this->view->filterEndDate = Date::currentDate('Y-m').'-'.date('t', strtotime($this->view->filterStartDate));
        }
        
        $criteria = array(
            'sessionEmployeeId' => array(
                array(
                    'operator' => '=',
                    'operand' => Ue::sessionEmployeeId()
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterStartDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterEndDate
                )
            )
        );

        $sidebarDvId = 1525387880376;
        
        $this->view->calendarDvId = Config::getFromCache('hrmCalendarDVId');
        
        $this->view->procId = Config::getFromCache('hrmCalendarAddProcessId');
        $this->view->procEditId = Config::getFromCache('hrmCalendarEditProcessId');
        $this->view->procViewId = Config::getFromCache('hrmCalendarViewProcessId');
        $this->view->startBgColor = Config::getFromCacheDefault('startBgColor', null, '#199ec7');
        $this->view->endBgColor = Config::getFromCache('endBgColor', null, '#1BBC9B'); 
        
        if ($this->view->procId) {
            $addProcessIds[] = $this->view->procId;
        }
            
        for ($p = 2; $p <= 20; $p++) {
            
            $addProcessId = Config::getFromCache('hrmCalendarAddProcessId' . $p);
            
            if ($addProcessId) {
                $addProcessIds[] = $addProcessId;
            }
        }
        
        if (isset($addProcessIds) && is_countable($addProcessIds) && count($addProcessIds) > 1) {
            
            $processIdsData = $this->model->getProcessTitleByIdsModel($addProcessIds);

            if ($processIdsData) {
                foreach ($processIdsData as $processIdRow) {
                    $this->view->addProcessIds[] = array(
                        'id'    => $processIdRow['META_DATA_ID'], 
                        'title' => $this->lang->line($processIdRow['META_DATA_NAME'])
                    );
                }
            }
        }
        
        $sidebarData = $this->model->loadListModel($sidebarDvId, $criteria);
        
        $this->view->sidebarLabelName = $this->model->getHeaderLabelNameModel($sidebarDvId);
        $this->view->sidebarData = isset($sidebarData[0]) ? $sidebarData[0] : array();
        
        $this->view->sidebarHtml = $this->view->renderPrint('calendar/hrmTimesheetLogSidebar', self::$viewPath);
        
        $this->view->calendarData = $this->model->loadListModel($this->view->calendarDvId, $criteria);
        
        $this->load->model('mdobject', 'middleware/models/');
        
        $this->view->getDataviewConfig = $this->model->getDataViewGridAllFieldsModel($this->view->calendarDvId); 
        $this->view->getDataviewConfig = Arr::groupByArrayOnlyRow($this->view->getDataviewConfig, 'FIELD_PATH', 'TEXT_COLOR');        
    }
    
    public function widgetVeriSupplyDataLoad() {
        Auth::handleLogin();        
        $this->load->model('mdwidget', 'middleware/models/');
        
        $criteria = array();
        $paging = array(
            'offset' => 1,
            'pageSize' => 10
        );
        
        $this->view->purchase_list = $this->model->loadListModel(1519967541804438, $criteria, $paging);
        $this->view->carts = $this->model->loadListModel(1519905899854, $criteria, $paging);
        
        $criteria = array(
            'itemcategoryid' => array(
                array(
                    'operator' => '=',
                    'operand' => 1515665343763
                )
            )
        );        
        $this->view->itemList = $this->model->loadListModel(1502340560463829, $criteria, $paging);
        
        $criteria = array(
            'itemcategoryid' => array(
                array(
                    'operator' => '=',
                    'operand' => 1515665344218
                )
            )
        );        
        $this->view->itemList2 = $this->model->loadListModel(1502340560463829, $criteria, $paging);
        
        $criteria = array(
            'itemcategoryid' => array(
                array(
                    'operator' => '=',
                    'operand' => 1518576801934
                )
            )
        );        
        $this->view->itemList3 = $this->model->loadListModel(1502340560463829, $criteria, $paging);
        
    }
    
    public function hrmTimesheetLogJson() {
        
        self::hrmTimesheetLogLoad();
        
        $response = array(
            'events' => $this->view->calendarData, 
            'sidebarHtml' => $this->view->sidebarHtml
        );
        
        echo json_encode($response); exit;
    }
    
    public function hrmTimesheetSidebar() {
        
        Auth::handleLogin();
        
        $criteria = array(
            'sessionEmployeeId' => array(
                array(
                    'operator' => '=',
                    'operand' => Ue::sessionEmployeeId()
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => Input::post('startDate')
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => Input::post('endDate')
                )
            )
        );
        $sidebarDvId = 1525387880376;
        $sidebarData = $this->model->loadListModel($sidebarDvId, $criteria);
        
        $this->view->sidebarLabelName = $this->model->getHeaderLabelNameModel($sidebarDvId);
        $this->view->sidebarData = isset($sidebarData[0]) ? $sidebarData[0] : array();
        $this->view->render('calendar/hrmTimesheetLogSidebar', self::$viewPath);
    }
    
    public function glanceWigdetDashboard($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {
        
        $this->view->uniqId = $uniqId;
        
        self::glanceWigdetDashboardLoad($uniqId, $startDate, $endDate, $depId, $isHierarchy);
        
        return array(
            'html' => $this->view->renderPrint('dashboard/glance', self::$viewPath)
        );
    }
    
    public function glanceWigdetDashboardLoad($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {
        Auth::handleLogin();
        
        if (Input::postCheck('yearMonth')) {
            $filterStartDate = Input::post('yearMonth').'-01';
            $filterEndDate = Input::post('yearMonth').'-'.date('t', strtotime('now'));
        } else {
            $filterStartDate = Date::currentDate('Y-m').'-01';
            $filterEndDate = Date::currentDate('Y-m').'-'.date('t', strtotime('now'));
        }
        
        $this->load->model('mdwidget', 'middleware/models/');
        $this->view->depList = $this->model->loadListModel('1526908301977'); 
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;
        
        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );       

        $sidebarDvId_left = 1526898184244412;
        $sidebarDvId_right = 1526898379379;

        $chartDvId_left = 1526898379701;
        $chartDvId_right = 1526908301253;
        $this->view->sdataViewId = 1519909696868;
        
        $this->view->positionData1 = $this->model->loadListModel($sidebarDvId_left, $criteria);
        $this->view->positionData2 = $this->model->loadListModel($sidebarDvId_right, $criteria);
        $this->view->positionData3 = $this->model->loadListModel($chartDvId_left, $criteria);
        $this->view->positionData4 = $this->model->loadListModel($chartDvId_right, $criteria);
        $this->view->positionData5 = $this->model->loadListModel($this->view->sdataViewId, $criteria);
//        var_dump($this->view->positionData5); die;
        (Array) $data = array();
        foreach ($this->view->positionData3 as $row) {
            
            $row['value1'] = (float) $row['value1'];
            $row['value2'] = (float) $row['value2'];
            array_push($data, $row);
        }
        
        $this->view->positionData3 = $data;
        
        (Array) $data = array();
        foreach ($this->view->positionData4 as $row) {
            
            $row['value1'] = (float) $row['value1'];
            $row['value2'] = (float) $row['value2'];
            array_push($data, $row);
        }
        
        $this->view->positionData4 = $data;
        
    }

    public function project($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->getDataSalesByType = $this->model->loadListModel('1526902085781660', $criteria);
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526976132584', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526976133926', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526907345817522', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $result = $this->model->loadListModel('1526898380253', $criteria);        
        $this->view->getTopFiveItem = array();
        foreach($result as $row) {
            $criteria['id'] = array(array(
                    'operator' => '=',
                    'operand' => $row['id']
            ));
            $dtlItem = $this->model->loadListModel('1526898380280', $criteria);
            $row['itemDtl'] = $dtlItem;
            array_push($this->view->getTopFiveItem, $row);
        }

        unset($criteria['id']);
        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526908301224', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526976132789', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526908301264', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project', self::$viewPath)
        );
    }    

    public function project3($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->getDataSalesByType = $this->model->loadListModel('1526983815312', $criteria);
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526983815349', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526983815374', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526983815263', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $result = $this->model->loadListModel('1526983815287', $criteria);        
        $this->view->getTopFiveItem = array();
        foreach($result as $row) {
            $criteria['id'] = array(array(
                    'operator' => '=',
                    'operand' => $row['id']
            ));
            $dtlItem = $this->model->loadListModel('1526983815300', $criteria);
            $row['itemDtl'] = $dtlItem;
            array_push($this->view->getTopFiveItem, $row);
        }

        unset($criteria['id']);
        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526983815324', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526983815361', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526983815337', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project3', self::$viewPath)
        );
    }    

    public function project1($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->getDataSalesByType = $this->model->loadListModel('1526976137828', $criteria);
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526976137872', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526990208516', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526976137764', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $result = $this->model->loadListModel('1526976137803', $criteria);        
        $this->view->getTopFiveItem = array();
        foreach($result as $row) {
            $criteria['id'] = array(array(
                    'operator' => '=',
                    'operand' => $row['id']
            ));
            $dtlItem = $this->model->loadListModel('1526976137816', $criteria);
            $row['itemDtl'] = $dtlItem;
            array_push($this->view->getTopFiveItem, $row);
        }

        unset($criteria['id']);
        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526976137841', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526976137884', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526976137860', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project1', self::$viewPath)
        );
    }    

    public function project2($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->getDataSalesByType = $this->model->loadListModel('1526983815201', $criteria);
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526983815238', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526990208529', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526983815160', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $result = $this->model->loadListModel('1526983815188', $criteria);        
        $this->view->getTopFiveItem = array();
        foreach($result as $row) {
            $criteria['id'] = array(array(
                    'operator' => '=',
                    'operand' => $row['id']
            ));
            $dtlItem = $this->model->loadListModel('1526976137816', $criteria);
            $row['itemDtl'] = $dtlItem;
            array_push($this->view->getTopFiveItem, $row);
        }

        unset($criteria['id']);
        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526983815213', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526983815250', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526983815226', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project2', self::$viewPath)
        );
    }    
    
    public function glanceWithOtherChartsDashboard($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->getDataSalesByType = $this->model->loadListModel('1526902085781660', $criteria);
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526976132584', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526976133926', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526907345817522', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $result = $this->model->loadListModel('1526898380253', $criteria);        
        $this->view->getTopFiveItem = array();
        foreach($result as $row) {
            $criteria['id'] = array(array(
                    'operator' => '=',
                    'operand' => $row['id']
            ));
            $dtlItem = $this->model->loadListModel('1526898380280', $criteria);
            $row['itemDtl'] = $dtlItem;
            array_push($this->view->getTopFiveItem, $row);
        }

        unset($criteria['id']);
        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526908301224', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526976132789', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526908301264', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('dashboard/glanceWithOther', self::$viewPath)
        );
    } 

    public function project4($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->positionData1 = $this->model->loadListModel('1526990304256', $criteria);
        $this->view->getDataSalesByType = $this->model->loadListModel('1526990309315', $criteria); // donut
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526990305378', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526990309666', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526907345817522', $criteria);
        $this->view->getCartData2 = $this->model->loadListModel('1526990309227', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $this->view->getTopFiveItem = $this->model->loadListModel('1526990309634', $criteria);

        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526908301224', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526990304400', $criteria);
        $this->view->getDataSalesByActivity22 = $this->model->loadListModel('1526990309650', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526990304423', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project4', self::$viewPath)
        );
    }        

    public function project5($uniqId, $startDate, $endDate, $depId = '', $isHierarchy = '') {

        $this->view->uniqId = $uniqId;        
        $this->load->model('mdwidget', 'middleware/models/');
        Session::init();

        $startDate = $startDate === '_' ? '' : $startDate;
        $endDate = $endDate === '_' ? '' : $endDate;        

        $criteria = array(
            'filterDepartmentId' => array(
                array(
                    'operator' => '=',
                    'operand' => $depId
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $startDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $endDate
                )
            ),
            'isHierarchy' => array(
                array(
                    'operator' => '=',
                    'operand' => $isHierarchy
                )
            )
        );        
        $this->view->positionData1 = $this->model->loadListModel('1526990304256', $criteria);
        $this->view->getDataSalesByType = $this->model->loadListModel('1526990309315', $criteria); // donut
        $this->view->getDataSalesByType2 = $this->model->loadListModel('1526990305378', $criteria);
        $this->view->getDataSalesByType22 = $this->model->loadListModel('1526990309666', $criteria);
        $this->view->getCartData = $this->model->loadListModel('1526907345817522', $criteria);
        $this->view->getCartData2 = $this->model->loadListModel('1526990309227', $criteria);
        $this->view->getCartData = issetVar($this->view->getCartData[0]);

        $this->view->getDataSalesByActivity = $this->model->loadListModel('1526908301224', $criteria);
        $this->view->getDataSalesByActivity2 = $this->model->loadListModel('1526990304400', $criteria);
        $this->view->getAreaList = $this->model->loadListModel('1526990304423', $criteria);      

        $this->view->depList = $this->model->loadListModel('1526908301977');        
        $this->view->depId = empty($depId) ? Ue::sessionUserKeyDepartmentId() : $depId;
        $this->view->startDate = $startDate === '_' || empty($startDate) ? Date::currentDate('Y-m') . '-01' : $startDate;
        $this->view->endDate = $endDate === '_' || empty($endDate) ? Date::currentDate('Y-m-d') : $endDate;
        $this->view->isHierarchy = $isHierarchy;

        return array(
            'html' => $this->view->renderPrint('project5', self::$viewPath)
        );
    }        

    public function contentWidget($uniqId, $param) {
        
        $this->view->uniqId = $uniqId;

        $this->load->model('Mdwidget', 'middleware/models/');
        $layoutParamConfig = $this->model->layoutListParamConfigModel($param);        
        
        /*if($param['position'] == '2')
            return array('html' => $widgetHtml = file_get_contents('middleware/views/widget/content/content-widget.html'));        
        
        if($param['position'] == '3')
            return array('html' => $widgetHtml = file_get_contents('middleware/views/widget/content/content-widget2.html'));        
        
        if($param['position'] == '4')
            return array('html' => $widgetHtml = file_get_contents('middleware/views/widget/content/content-widget3.html'));        
        
        if($param['position'] == '5')
            return array('html' => $widgetHtml = file_get_contents('middleware/views/widget/content/content-widget4.html'));
        
        if($param['position'] == '6')
            return array('html' => $widgetHtml = file_get_contents('middleware/views/widget/content/content-widget5.html'));*/

        if(empty($layoutParamConfig))
            return array(
                'html' => ''
            );        

        //$data = file_get_contents('C:/Users/Ulaankhuu/Desktop/chart_data.json');
        //$data = json_decode($data, true);
        //print_r($data); die;
        
        $widgetHtml = file_get_contents('middleware/views/widget/content/sales-charts.html');

        /*foreach ($layoutParamConfig as $row) {
            $extractValues = json_decode($row['WIDGET_POSITION_CONFIGS'], true);

            if($extractValues) {
                $getDataSource = $this->model->loadListModel($extractValues['dataviewid']);
                $position = $row['WIDGET_PARAM_NAME'];

                $searchReplace  = array(
                    '{CHART_VALUE_' . $position . '}',
                    '{CHART_NAME_' . $position . '}',
                    '{CHART_DATASOURCE_' . $position . '}',
                );
                $replaced = array(
                    $extractValues['value'],
                    $extractValues['name'],
                    json_encode($getDataSource),
                );

                $widgetHtml = str_replace($searchReplace, $replaced, $widgetHtml);        
            }
        }*/
        
        return array(
            'html' => $widgetHtml
        );
    }    
    
    public function viewTemplateDatabank() {
        
        $postData = Input::postData();
        $this->view->uniqId = getUID();
        $type = 1;
        
        (String) $Html = '';
        (Array) $this->view->data = array();
        
        $this->view->selectedRow = isset($postData['row']) ? $postData['row'] : '';
        $this->view->updateType = isset($postData['updateType']) ? $postData['updateType'] : '';
        
        $response = array(
            'Html' => $Html,
            'postData' => $postData
        );
        
        if (isset($postData['processId'])  && $postData['processId'] !== '') {
            
            $processData = $this->db->GetRow("SELECT * FROM META_DATA WHERE META_DATA_ID = " . $postData['processId']);
            
            if ($processData) {
                
                if (Config::getFromCache('CONFIG_KHANBANK') && isset($this->view->selectedRow['parentid'])) {

                    $this->view->metaDataId = $postData['metaDataId'];
                    $this->load->model('mdwidget', 'middleware/models/');
                    
                    if ($this->view->updateType === '1') {
                        $param = array('id' => $this->view->selectedRow['ids']);
                    } else {
                        $param = array('id' => $this->view->selectedRow['id']);
                    }
                    
                    $viewPrint = 'databank/view1';
                    (Array) $result = array();
                    $type = '0';
                    if ($this->view->selectedRow['parentid'] === '' || $this->view->selectedRow['parentid'] === '0') {

                        $result = $this->ws->caller('WSDL-DE', GF_SERVICE_ADDRESS,  $processData['META_DATA_CODE'], 'return', $param, 'serialize');
                        $viewPrint = 'databank/view2';

                    } else {

                        $result = $this->ws->caller('WSDL-DE', GF_SERVICE_ADDRESS,  $processData['META_DATA_CODE'], 'return', $param, 'serialize');
                        $viewPrint = 'databank/view3';

                    }
                    
                    if (isset($result['result']) && !empty($result['result'])) {
                        $this->view->data = $result['result'];
                        $Html = $this->view->renderPrint($viewPrint, self::$viewPath);
                    }

                    $response = array(
                        'Html' => $Html,
                        'Title' => '',
                        'Width' => '800',
                        'postData' => $this->view->data,
                        'Btn' => $type,
                        'save_btn' => Lang::line('save_btn'),
                        'close_btn' => Lang::line('close_btn')
                    );

                }
                
            } else {
                
                $response = array(
                    'Html' => '',
                    'processData' => $processData,
                    'message' => 'Процесс олдсонгүй.',
                );
                
            }    
            
        } else {
            $response = array(
                'Html' => '',
                'message' => 'Процессын <strong>ID</strong> олдсонгүй.',
            );
        }
        
        echo json_encode($response);
        
    }
    
    public function controlTemplateDatabank() {
        
    $postData = Input::postData();
        $this->view->uniqId = getUID();
        $type = 1;
        
        Session::init();
        $this->view->sessionUserId = Ue::sessionUserKeyId();
        
        $this->view->selectedRow = array_change_key_case(isset($postData['row']) ? $postData['row'] : array(), CASE_LOWER);
        
        $this->view->updateType = isset($postData['updateType']) ? $postData['updateType'] : '';
        $this->view->readonly = ($this->view->updateType == '1' || $this->view->updateType == '2') ? false : true;
        $this->view->posRepairManReadonly = ($this->view->updateType == '1' || $this->view->updateType == '2') ? false : true;
        $this->view->agentReadonly = ($this->view->updateType == '4') ? true : false;
        
        (String) $Html = '';
        
        $response = array (
            'Html' => $Html,
            'postData' => $postData
        );
        
        $this->view->wfmStatusVerif = array();
        $this->view->wfmStatusCancel = array();
        
        if (isset($postData['processId'])  && $postData['processId'] !== '') {
            
            $processData = $this->db->GetRow("SELECT * FROM META_DATA WHERE META_DATA_ID = " . $postData['processId']);
            
            if ($processData) {

                if (Config::getFromCache('CONFIG_KHANBANK') && isset($this->view->selectedRow['parentid'])) {
                    
                    $this->view->metaDataId = $postData['metaDataId'];
                    $this->load->model('mdobject', 'middleware/models/');
                    
                    $this->view->row = $this->model->getDataViewConfigRowModel($this->view->metaDataId);
                    
                    $this->view->metaDataCode = $this->view->row['META_DATA_CODE'];
                    $this->view->refStructureId = $this->view->row['REF_STRUCTURE_ID'];

                    $this->view->dataViewId = Input::numeric('metaDataId');
                    $this->view->refStructureId = Input::post('refStructureId');

                    $lookupDatabank = self::getLookupDatabank();
                    
                    $this->view->rejectReasonType = isset($lookupDatabank['rejectReasonType']) ? $lookupDatabank['rejectReasonType'] : array();
                    $this->view->industryType = isset($lookupDatabank['industryType']) ? $lookupDatabank['industryType'] : array();
                    $this->view->agentList = isset($lookupDatabank['agentList']) ? $lookupDatabank['agentList'] : array();
                    
                    $this->view->levelType = isset($lookupDatabank['levelType']) ? $lookupDatabank['levelType'] : array();
                    $this->view->centerList = isset($lookupDatabank['centerList']) ? $lookupDatabank['centerList'] : array();
                    $this->view->posRepairman = isset($lookupDatabank['posRepairman']) ? $lookupDatabank['posRepairman'] : array();

                    $this->load->model('mdwidget', 'middleware/models/');
                    
                    if ($this->view->updateType === '1') {
                        $param = array('id' => $this->view->selectedRow['ids']);
                    } else {
                        $param = array('id' => $this->view->selectedRow['id']);
                    }

                    $viewPrint = 'databank/view1';
                    (Array) $result = $this->view->data = array();
                    $type = '1';

                    $result = $this->ws->caller('WSDL-DE', GF_SERVICE_ADDRESS, $processData['META_DATA_CODE'], 'return', $param, 'serialize');

                    if (isset($result['result']) && !empty($result['result'])) {

                        (Array) $dataResult = $item = $result['result'];
                        
                        $this->view->updateType = isset($postData['updateType']) ? $postData['updateType'] : '';
                        $this->view->wfmStatusVerif = isset($result['result']['statusids']) ? explode(',', $result['result']['statusids']) : array(); /* Tatgalzsan, Tudgelzsen, Batalsan, Shine huselt tolowiin idnuud */
                        $this->view->wfmStatusCancel = isset($result['result']['denyids']) ? explode(',', $result['result']['denyids']) : array(); /* Tudgelzsen tolowiin id*/
                        $this->view->newWfmStatusIds = isset($result['result']['newids']) ? explode(',', $result['result']['newids']) : array(); /* Shine huseltiin id */
                        
                        if (isset($item['posgetmerchantrequestlist']) && !empty($item['posgetmerchantrequestlist'])) {

                            foreach ($item['posgetmerchantrequestlist'] as $key => $merchant) {

                                if (isset($merchant['posgetterminalrequestlist']) && !empty($merchant['posgetterminalrequestlist'])) {
                                    
                                    foreach ($merchant['posgetterminalrequestlist'] as $key1 => $terminal) {

                                        $param = array(
                                            'systemMetaGroupId' => $terminal['dataviewid'],
                                            'showQuery' => 0, 
                                            'ignorePermission' => 1 
                                        );
                                        
                                        $param = array_merge($param, $terminal);
                                        
                                        if (in_array($terminal['wfmstatusid'], $this->view->wfmStatusVerif) && $terminal['doneids'] !== '1') {
                                            $terminal['statusData'] = (new Mdworkflow())->getWorkflowNextStatus($terminal['dataviewid'], $terminal, $terminal['structureid'], true, true);
                                        }
                                        
                                        if ($terminal['verifieduserid']) {
                                            
                                            if ($terminal['wfmlog']) {
                                                
                                                $dataGroup = Arr::groupByArrayOnlyRows($terminal['wfmlog'], 'createduserid');

                                                if (sizeof($terminal['wfmlog']) > 1) {

                                                    if ($terminal['isdenied'] === '1' && $terminal['verifieduserid'] === $this->view->sessionUserId) {
                                                        $this->view->readonly = false;
                                                        $terminal['isreadonly'] = '0';

                                                    } else {
                                                        $this->view->readonly = true;
                                                        $terminal['isreadonly'] = (isset($dataGroup[$this->view->sessionUserId]) && $dataGroup[$this->view->sessionUserId]) ? '1' : '0';

                                                    }

                                                } else {

                                                    if (isset($dataGroup[$this->view->sessionUserId]) && $dataGroup[$this->view->sessionUserId]) {

                                                        $this->view->readonly = true;
                                                        $terminal['isreadonly'] = '1';

                                                    } else {

                                                        $terminal['isreadonly'] = '0';
                                                        if ($terminal['verifieduserid'] !== $this->view->sessionUserId) {
                                                            $this->view->readonly = true;
                                                        }

                                                    }

                                                }
                                            }
                                            
                                            if ($terminal['countdeny'] && $this->view->sessionUserId !== $terminal['laststatususer'] && $this->view->sessionUserId !== $terminal['verifieduserid']) {
                                                $terminal['isreadonly'] = '0';
                                            }
                                            
                                        } else {
                                            $this->view->readonly = false;
                                            $terminal['isreadonly'] = '0';
                                        }
                                        
                                        /*
                                        if ($terminal['isrequered'] == '1') {
                                            $this->view->readonly = true;
                                            $terminal['isreadonly'] = '1';
                                        }*/
                                        
                                        $dataResult['posgetmerchantrequestlist'][$key]['posgetterminalrequestlist'][$key1] = $terminal;

                                    }

                                }

                            }
                            
                        }

                        $this->view->data = $dataResult;
                        
                        $response = array(
                            'Html' => $this->view->renderPrint($viewPrint, self::$viewPath),
                            'Title' => '',
                            'Width' => '1100',
                            'Btn' => $type,
                            'postData' => $dataResult,
                            'sessionUserId' => $this->view->sessionUserId,
                            'centerList' => $this->view->centerList,
                            'uniqId' => $this->view->uniqId,
                            'metaDataId' => $this->view->metaDataId,
                            'updateType' => $this->view->updateType,
                            'save_btn' => Lang::line('save_btn'),
                            'close_btn' => Lang::line('close_btn')
                        );
                    } else {
                        $response = array(
                            'Html' => '',
                            'processData' => $result,
                            'message' => 'Мэдээлэл олдсонгүй.',
                        );
                    }


                } else {

                    $response = array(
                        'Html' => '',
                        'processData' => $processData,
                        'message' => 'parentid олдсонгүй.',
                    );
                }

            } else {
                $response = array(
                    'Html' => '',
                    'processData' => $processData,
                    'message' => 'Процесс олдсонгүй.',
                );
            }
            
        }
        else {
           $response = array(
               'Html' => '',
               'message' => 'Процессын <strong>ID</strong> олдсонгүй.',
           );
        }
        
        echo json_encode($response);
        
    }
    
    public function saveTemplateDatabank1() {
        Session::init();
        
        $postData = Input::postData();
        $createdUserId = Ue::sessionUserKeyId();
        
        $mainInvoiceBookId = $postData['customerInvoiceBookId'];
        $mainInvoiceBookLevelTypeId = $postData['levelType'];
        (Array) $response = array("status" => "success", "message" => Lang::line("msg_save_success"));
        
        $checkUpdateType = isset($postData['checkUpdate']) ? $postData['checkUpdate'] : '0';
        
        (Array) $changeStatusform = array();
        (String) $tableName = $subTable = '';
        (String) $tablePath = 'DIM_11';
        (String) $tablePath1 = 'DIM_9';
        (String) $tablePath2 = 'TEXT_2';
        
        switch ($checkUpdateType) {
            case '1':// Шинэ хүсэлт
                
                $tableName = 'IC_INVOICE_BOOK';
                $subTable = 'IC_INVOICE_BOOK';
                
                if ($mainInvoiceBookLevelTypeId) {
                    $this->db->AutoExecute($tableName, array('DIM_11' => $mainInvoiceBookLevelTypeId), "UPDATE", "ID = $mainInvoiceBookId");
                }
                break;
            case '4':// Дундын пос
                
                $tablePath1 = 'DIM_1';
                $tableName = 'IC_INVOICE_BOOK';
                $subTable = 'IC_INVOICE_BOOK';
                
                break;
            case '2': // Нэмэлт күб
            case '3': // Нэмэлт пос
                
                $tableName = 'IC_INVOICE_BOOK';
                $subTable = 'IC_INVOICE_BOOK_DTL';

                break;
            
            default:
                $tableName = 'IC_INVOICE_BOOK';
                $subTable = 'IC_INVOICE_BOOK';
                break;
        }
        
        try {
            
            if (isset($postData['terminalId']) && $postData['terminalId']) {
                
                $kubInvoiceBookId = $postData['kubInvoiceBookId'];

                $terminalId = $postData['terminalId'];
                $terminalwfmstatusid_old = $postData['wfmstatusid'];
                $terminalDataviewid = $postData['dataviewid'];

                $terminalRejectReasion = isset($postData['rejectReasonTypeId']) ? $postData['rejectReasonTypeId'] : array();
                $terminalWfmStatusId_new = isset($postData['newwfmstatusid']) ? $postData['newwfmstatusid'] : array();
                $terminalDescription = isset($postData['description']) ? $postData['description'] : array();

                $terminalIndustryType = isset($postData['industryType']) ? $postData['industryType'] : array();
                $terminalAgent = isset($postData['agentid']) ? $postData['agentid'] : array();
                
                foreach ($kubInvoiceBookId as $kubKey => $kubRow) {
                    
                    if (isset($postData['storeId'][$kubKey]) && $postData['storeId'][$kubKey]) {
                        $mainInvoiceBookStore = $postData['storeId'][$kubKey];
                        $salesManId = isset($postData['salesManId'][$kubKey]) ? $postData['salesManId'][$kubKey] : '';

                        switch ($checkUpdateType) {
                            case '3': 
                                break;
                            case '1':
                                $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_2' => $mainInvoiceBookStore), "UPDATE", "DIM_1 = $kubRow");
                                if ($salesManId) {
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('DIM_12' => $salesManId), "UPDATE", "DIM_1 = $kubRow");
                                }
                                break;
                            case '2': 
                                if ($mainInvoiceBookStore) {
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_2' => $mainInvoiceBookStore), "UPDATE", "ID = $mainInvoiceBookId");
                                }
                                if ($salesManId) {
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('DIM_12' => $salesManId), "UPDATE", "ID = $kubRow");
                                }
                                break;
                            case '4':
                                if ($mainInvoiceBookStore) {
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_6' => $mainInvoiceBookStore), "UPDATE", "ID = $mainInvoiceBookId");
                                }
                                break;
                        }
                    }
                    
                    if (isset($terminalId[$kubRow])) {

                        foreach ($terminalId[$kubRow] as $terKey => $terRow) {
                            
                            if (
                                    isset($terminalwfmstatusid_old[$kubRow][$terKey]) && $terminalwfmstatusid_old[$kubRow][$terKey] !== '' &&
                                    isset($terminalDataviewid[$kubRow][$terKey]) && $terminalDataviewid[$kubRow][$terKey] !== '' &&
                                    isset($terminalWfmStatusId_new[$kubRow][$terKey]) && $terminalWfmStatusId_new[$kubRow][$terKey] !== '' 
                                ) {

                                (Array) $param = array(
                                                    'systemMetaGroupId' => $terminalDataviewid[$kubRow][$terKey],
                                                    'showQuery' => 0, 
                                                    'ignorePermission' => 1 ,
                                                    'id' => $terRow,
                                                    'wfmStatusId' => $terminalwfmstatusid_old[$kubRow][$terKey],
                                                    'newWfmDescription' => isset($terminalDescription[$kubRow][$terKey]) ? $terminalDescription[$kubRow][$terKey] : '',
                                                    'newWfmStatusId' => $terminalWfmStatusId_new[$kubRow][$terKey],
                                                    'createdUserId' => $createdUserId,
                                                );
                                if ($param['newWfmStatusId'] === '1529390462954467' || $param['newWfmStatusId'] == '1528963418491658' ||
                                    $param['newWfmStatusId'] === '1530766341810732' || $param['newWfmStatusId'] === '1530417172804921' ||
                                    $param['newWfmStatusId'] === '1530414729357243' || $param['newWfmStatusId'] == '1530414661733960') {
                                    
                                    $param['newWfmDescription'] = ((isset($terminalRejectReasion[$kubRow][$terKey]) && $terminalRejectReasion[$kubRow][$terKey]) ? Arr::implode_r(", ", $terminalRejectReasion[$kubRow][$terKey], true) : '' ) . ' ' . ((isset($param['newWfmDescription']) && $param['newWfmDescription']) ? $param['newWfmDescription'] : ''); 
                                    
                                }
                                
                                $result = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                
                                array_push($changeStatusform, array('param' => $param, 'result' => $result));
                                
                                if ($result['status'] == 'success') {
                                    
                                    if ($param['newWfmStatusId'] === '1528784819812' 
                                            || $param['newWfmStatusId'] === '1530766400784026' 
                                            || $param['newWfmStatusId'] === '1530415404252287'
                                            ) {
                                        $response['nextProcess'] = '1';
                                        $response['nextProcessWfmStatus'] = '1528784819812';
                                    }
                                    
                                    if ($param['newWfmStatusId'] === '1530767197767825') {
                                        $response['isConfirm'] = '0';
                                    }
                                    
                                    (Array) $data = array();

                                    if (isset($terminalIndustryType[$kubRow][$terKey]) && $terminalIndustryType[$kubRow][$terKey]) {
                                        $data[$tablePath1] = $terminalIndustryType[$kubRow][$terKey];
                                    }

                                    if (isset($terminalAgent[$kubRow][$terKey]) && $terminalAgent[$kubRow][$terKey]) {
                                        $data['DIM_10'] = $terminalAgent[$kubRow][$terKey];
                                    }

                                    if ($data) {
                                        $this->db->AutoExecute($subTable, $data, "UPDATE", "ID = $terRow");
                                    }
                                }

                            }
                        }

                    }
                    
                    (Boolean) $ticketU = $ticketUConfirm = false;
                    (Boolean) $ticketCancel = false;
                    (Int) $counter = $ssCounter = 0;
                    
                    $this->view->wfmStatusCancel = array(
                                        '1530414729357243', '1530417172804921', '1530414729357243',
                                        '1528963418491658', '1528784819504', '1528784819769', '1533354517829913',
                                        '1535612431297683', '1535612388296509', '1534141477576717'
                        
                    );
                    
                    switch ($checkUpdateType) {
                        
                        case '1':
                            $dataWfmStatus = $this->db->GetAll("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK WHERE DIM_1 = $kubRow");
                            
                            if ($dataWfmStatus) {
                                foreach ($dataWfmStatus as $row) {
                                    if ($row['WFM_STATUS_ID'] == '1528784819750' ||
                                            $row['WFM_STATUS_ID'] == '1528784819816' ||
                                            $row['WFM_STATUS_ID'] == '1528784819812') {
                                        $ticketU = true;
                                    }
                                    
                                    if (
                                            $row['WFM_STATUS_ID'] == '1529390462954467' || // tatgalzsan
                                            $row['WFM_STATUS_ID'] == '1528963418491658' || // tudgelzsen
                                            $row['WFM_STATUS_ID'] == '1528784819504' || // tudgelzsen
                                            $row['WFM_STATUS_ID'] == '1528784819769' || // tudgelzsen
                                            $row['WFM_STATUS_ID'] == '1533354517829913'    // tudgelzsen
                                        ) {
                                        $ticketCancel = true;
                                        $counter++;
                                    }
                                    
                                    if ($row['WFM_STATUS_ID'] == '1529390462954467' ) {
                                        $ticketCancel = true;
                                        $ssCounter++;
                                    }
                                }
                            }
                            
                            $checkWfmStatusId = $this->db->GetOne("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK WHERE ID = $mainInvoiceBookId");
                            
                            if ($ticketU) {
                                
                                if ($checkWfmStatusId !== '1528784819820') {
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1532094194070',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                        'newWfmStatusId' => '1528784819820',
                                                        'createdUserId' => $createdUserId
                                                    );
                                    
                                    $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                }
                            } 
                            else {
                                
                                if ($ticketCancel && sizeOf($dataWfmStatus) === $counter) {
                                    
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1532094194070',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    if (sizeof($dataWfmStatus) === $ssCounter) {
                                        $param['newWfmStatusId'] = '1533356273748088'; //Түдгэлзсэн
                                    } else {
                                        $param['newWfmStatusId'] = '1530708341341872'; //Татгалзсан
                                    }
                                    
                                    if ($checkWfmStatusId !== $param['newWfmStatusId']) {
                                        $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                    }
                                }
                                
                            }
                            
                            break;
                        case '2':
                            
                            $dataWfmStatus = $this->db->GetAll("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK_DTL WHERE INVOICE_BOOK_ID = $kubRow");
                            
                            if ($dataWfmStatus) {
                                foreach ($dataWfmStatus as $row) {
                                    if ($row['WFM_STATUS_ID'] == '1530767197767825' ||
                                            $row['WFM_STATUS_ID'] == '1530767185291621') {
                                        $ticketU = true;
                                    }
                                    
                                    if (
                                            $row['WFM_STATUS_ID'] == '1535612431297683' ||
                                            $row['WFM_STATUS_ID'] == '1535612388296509' ||
                                            $row['WFM_STATUS_ID'] == '1534141477576717' ||
                                            $row['WFM_STATUS_ID'] == '1530766341810732'
                                            /* || // tatgalzsan
                                            $row['WFM_STATUS_ID'] == '1528963418491658' || // tudgelzsen
                                            $row['WFM_STATUS_ID'] == '1533354517829913'    // tudgelzsen*/
                                        ) {
                                        $ticketCancel = true;
                                        $counter++;
                                    }
                                    
                                    if (
                                            $row['WFM_STATUS_ID'] == '1535612431297683' ||
                                            $row['WFM_STATUS_ID'] == '1535612388296509' ||
                                            $row['WFM_STATUS_ID'] == '1534141477576717'
                                        ) {
                                        $ticketCancel = true;
                                        $ssCounter++;
                                    }
                                    
                                    if ($row['WFM_STATUS_ID'] == '1530766400784026') {
                                        $ticketUConfirm = true;
                                    }
                                }
                            }
                            
                            $checkWfmStatusId = $this->db->GetOne("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK WHERE ID = $mainInvoiceBookId");
                            
                            if ($ticketU || $ticketUConfirm) {
                                if ($checkWfmStatusId !== '1528784819839' && $ticketUConfirm == false) {
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1530760343467544',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                        'newWfmStatusId' => '1528784819839',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                }
                                
                                if ($checkWfmStatusId !== '1530417562134983' && $ticketUConfirm == true) {
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1530760343467544',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                        'newWfmStatusId' => '1530417562134983',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                }
                            }  else {
                                
                                if ($ticketCancel && sizeOf($dataWfmStatus) === $counter) {
                                    
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1532094194070',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    if (sizeof($dataWfmStatus) === $ssCounter) {
                                        $param['newWfmStatusId'] = '1535462933561937';
                                    } else {
                                        $param['newWfmStatusId'] = '1530414523234210';
                                    }
                                    
                                    if ($checkWfmStatusId !== $param['newWfmStatusId']) {
                                        $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                    }
                                }
                                
                            }
                            
                            break;
                        case '3':
                            
                            $dataWfmStatus = $this->db->GetAll("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK_DTL WHERE INVOICE_BOOK_ID = $kubRow");
                            
                            if ($dataWfmStatus) {
                                foreach ($dataWfmStatus as $row) {
                                    if ($row['WFM_STATUS_ID'] == '1530767197767825' ||
                                            $row['WFM_STATUS_ID'] == '1530767185291621') {
                                        $ticketU = true;
                                    }
                                    
                                    if (
                                            $row['WFM_STATUS_ID'] == '1535612431297683' ||
                                            $row['WFM_STATUS_ID'] == '1535612388296509' ||
                                            $row['WFM_STATUS_ID'] == '1534141477576717' ||
                                            $row['WFM_STATUS_ID'] == '1530766341810732'/* || // tatgalzsan
                                            $row['WFM_STATUS_ID'] == '1528963418491658' || // tudgelzsen
                                            $row['WFM_STATUS_ID'] == '1533354517829913'    // tudgelzsen*/
                                        ) {
                                        $ticketCancel = true;
                                        $counter++;
                                    }
                                    
                                    if (
                                            $row['WFM_STATUS_ID'] == '1535612431297683' ||
                                            $row['WFM_STATUS_ID'] == '1535612388296509' ||
                                            $row['WFM_STATUS_ID'] == '1534141477576717'
                                            
                                        ) {
                                        $ticketCancel = true;
                                        $ssCounter++;
                                    }
                                    
                                    if ($row['WFM_STATUS_ID'] == '1530766400784026') {
                                        $ticketUConfirm = true;
                                    }
                                }
                            }
                            
                            $checkWfmStatusId = $this->db->GetOne("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK WHERE ID = $mainInvoiceBookId");
                            
                            if ($ticketU || $ticketUConfirm) {
                                
                                if ($checkWfmStatusId !== '1528784819652' && $ticketUConfirm == false) {
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1531120176782',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Төлөв өөрчлөгдөв.',
                                                        'newWfmStatusId' => '1528784819652',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                }
                                
                                if ($checkWfmStatusId !== '1530417172333347' && $ticketUConfirm == true) {
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1531120176782',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'newWfmDescription' => 'Төлөв өөрчлөгдөв.',
                                                        'newWfmStatusId' => '1530417172333347',
                                                        'createdUserId' => $createdUserId,
                                                    );
                                    
                                    $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                }
                                
                            } else {
                                
                                if ($ticketCancel && sizeOf($dataWfmStatus) === $counter) {
                                    
                                    (Array) $param = array(
                                                        'systemMetaGroupId' => '1532094194070',
                                                        'showQuery' => 0, 
                                                        'ignorePermission' => 1 ,
                                                        'id' => $mainInvoiceBookId,
                                                        'wfmStatusId' => $checkWfmStatusId,
                                                        'createdUserId' => $createdUserId,
                                                        'newWfmDescription' => 'Терминалын төлөв өөрчлөгдөв.',
                                                    );
                                    
                                    if (sizeof($dataWfmStatus) === $ssCounter) {
                                        $param['newWfmStatusId'] = '1535463071383420';
                                    } else {
                                        $param['newWfmStatusId'] = '1535463071611486';
                                    }
                                    
                                    if ($checkWfmStatusId !== $param['newWfmStatusId']) {
                                        $this->ws->runResponse(GF_SERVICE_ADDRESS, 'SET_ROW_WFM_STATUS', $param);
                                    }
                                }
                                
                            }
                            
                            break;
                        case '4':
                            $this->db->GetAll("SELECT WFM_STATUS_ID FROM IC_INVOICE_BOOK WHERE ID = $kubRow");
                            
                            break;
                        
                    }

                }    
                
            }
            else {
                if (isset($postData['kubInvoiceBookId']) && $postData['kubInvoiceBookId']) {
                    $kubInvoiceBookId = $postData['kubInvoiceBookId'];
                    foreach ($kubInvoiceBookId as $kubKey => $kubRow) {
                        
                        if (isset($postData['storeId'][$kubKey]) && $postData['storeId'][$kubKey]) {
                            $mainInvoiceBookStore = $postData['storeId'][$kubKey];

                            switch ($checkUpdateType) {
                                case '3': 
                                    break;
                                case '1':
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_2' => $mainInvoiceBookStore), "UPDATE", "DIM_1 = $kubRow");
                                    break;
                                case '2': 
                                    if ($mainInvoiceBookStore) {
                                        $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_2' => $mainInvoiceBookStore), "UPDATE", "ID = $mainInvoiceBookId");
                                    }
                                    if ($salesManId) {
                                        $this->db->AutoExecute('IC_INVOICE_BOOK', array('DIM_12' => $salesManId), "UPDATE", "ID = $mainInvoiceBookId");
                                    }
                                    break;
                                case '4':
                                    if ($mainInvoiceBookStore) {
                                        $this->db->AutoExecute('IC_INVOICE_BOOK', array('TEXT_6' => $mainInvoiceBookStore), "UPDATE", "ID = $mainInvoiceBookId");
                                    }
                                    break;
                            }
                        }
                        if (isset($postData['salesManId'][$kubKey]) && $postData['salesManId'][$kubKey]) {
                            $salesManId = $postData['salesManId'][$kubKey];
                            
                            switch ($checkUpdateType) {
                                case '1':
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('DIM_12' => $salesManId), "UPDATE", "DIM_1 = $kubRow");
                                    break;
                                case '2': 
                                    $this->db->AutoExecute('IC_INVOICE_BOOK', array('DIM_12' => $salesManId), "UPDATE", "ID = $kubRow");
                                    break;
                            }
                        }
                        
                    } 
                }
            }
            
        } catch (Exception $ex) {
            $response = array("status" => "error", "message" => Lang::line("msg_save_error"), 'ex' => $ex);
        }
        
        if (issetParam($postData['selectedRow']['id'])) {
            (Array) $param = array(
                'systemMetaGroupId' => $postData['metaDataId'],
                'ignorePermission' => 1,
                'showQuery' => '0',
                'criteria' => array(
                                    'id' => array(
                                        array(
                                            'operator' => '=',
                                            'operand' => $postData['selectedRow']['id']
                                        )
                                    )
                                )
            );

            $data = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);
            $response['selectedRow'] = issetParamArray($data['result'][0]);
        }
        
        $response['changeStatus'] = $changeStatusform;
        echo json_encode($response);
    }
    
    public function getLookupDatabank() {
        
        $response = array();
        
        (Array) $param = array(
            'systemMetaGroupId' => '1471315591341',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );

        $levelType = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        (Array) $param = array(
            'systemMetaGroupId' => '1530160540148',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );

        $rejectReasonType = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        (Array) $param = array(
            'systemMetaGroupId' => '1448347511992',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );
        $industryType = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        (Array) $param = array(
            'systemMetaGroupId' => '1532357231512',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );

        $agentList = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        (Array) $param = array(
            'systemMetaGroupId' => '1533139046714',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );

        $centerList = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        (Array) $param = array(
            'systemMetaGroupId' => '1533787298504',
            'ignorePermission' => 1,
            'showQuery' => '0',
            'criteria' => array(),
            'paging' => array(),
        );

        $posRepairman = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);

        if (isset($levelType['result']) && !empty($levelType['result'])) {

            unset($levelType['result']['paging']);
            unset($levelType['result']['aggregatecolumns']);
            $response['levelType'] = $levelType['result'];
        }

        if (isset($rejectReasonType['result']) && !empty($rejectReasonType['result'])) {

            unset($rejectReasonType['result']['paging']);
            unset($rejectReasonType['result']['aggregatecolumns']);
            $response['rejectReasonType'] = $rejectReasonType['result'];
        }

        if (isset($industryType['result']) && !empty($industryType['result'])) {

            unset($industryType['result']['paging']);
            unset($industryType['result']['aggregatecolumns']);
            $response['industryType'] = $industryType['result'];

        }

        if (isset($agentList['result']) && !empty($agentList['result'])) {

            unset($agentList['result']['paging']);
            unset($agentList['result']['aggregatecolumns']);
            $response['agentList'] = $agentList['result'];

        }

        if (isset($centerList['result']) && !empty($centerList['result'])) {

            unset($centerList['result']['paging']);
            unset($centerList['result']['aggregatecolumns']);
            $response['centerList'] = $centerList['result'];

        }

        if (isset($posRepairman['result']) && !empty($posRepairman['result'])) {

            unset($posRepairman['result']['paging']);
            unset($posRepairman['result']['aggregatecolumns']);
            $response['posRepairman'] = $posRepairman['result'];

        }
        
        return $response;
    }
    
    public function dashboardV2Widget($metaDataId = '1543820675861659') {
        
        $this->view->dataViewId = $metaDataId;
        $this->view->title = 'DASHBOARD';
        
        $this->view->uniqId = Input::post('uniqId');getUID();
        $this->view->uniqId = issetParam($this->view->uniqId) ? $this->view->uniqId : getUID();
        $this->view->reload = Input::post('reload');
        $param = array('systemMetaGroupId' => $metaDataId, 'showQuery' => 1);
        
        $result = $this->ws->runResponse(GF_SERVICE_ADDRESS, 'PL_MDVIEW_004', $param);
        
        $pivotColumn =  Config::getFromCache('kh_pos');
        $this->view->mainData = array();
        
        if (isset($result['result'])) {
            $select = "SELECT 
                            *
                        FROM (". $result['result'] .")
                        PIVOT ( 
                            MAX(SUM||'#'||wfmstatusid||'#'||EQUALCOLUMN) FOR type IN ($pivotColumn)
                        )";
            
            $this->view->mainData = $this->db->GetAll($select);
            $this->view->mainData = Arr::naturalsort($this->view->mainData, 'TYPEID');
        }
        
        $this->view->isAjax = true;
        
        if (!is_ajax_request()) {
            $this->view->isAjax = false;
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = array_unique(array_merge(array('custom/pages/scripts/appmenu.js'), AssetNew::metaOtherJs()));
            
            $this->view->render('header');
        }
        
        if (issetParam($this->view->reload) === '1') {
            echo json_encode(array('Html' => $this->view->renderPrint('dashboard/widget_v2', self::$viewPath)));
        } else {
            $this->view->render('dashboard/widget_v2', self::$viewPath);
        }

        if (!$this->view->isAjax) {
            $this->view->render('footer');
        }
    }

    public function webeditorCallBack() {
        if (isset($_GET["type"]) && !empty($_GET["type"])) { 
            @header( 'Content-Type: application/json; charset==utf-8');
            @header( 'X-Robots-Tag: noindex' );
            @header( 'X-Content-Type-Options: nosniff' );

            $this->nocache_headers();

            $type = $_GET["type"];

            switch($type) {
                case "track":
                    $response_array = $this->track();
                    $response_array['status'] = 'success';
                    die (json_encode($response_array));
                default:
                    $response_array['status'] = 'error';
                    $response_array['error'] = '404 Method not found';
                    die(json_encode($response_array));
            }
        }
    }

    public function sendlog($msg, $logFileName) {
        $logsFolder = "log/";
        if (!file_exists($logsFolder)) {
            mkdir($logsFolder);
        }
        file_put_contents($logsFolder . $logFileName, $msg . PHP_EOL, FILE_APPEND);
        return true;
    }

    public function track() {
        // $this->sendlog("Track START", "webedior-ajax.log");
        // $this->sendlog("contentid : ". $_GET["contentid"], "webedior-ajax.log");
        $result["error"] = 0;
        if (($body_stream = file_get_contents('php://input'))===FALSE) {
            // $this->sendlog("bad request", "webedior-ajax.log");
            $result["error"] = "Bad Request";
            return $result;
        }
        $data = json_decode($body_stream, TRUE); //json_decode - PHP 5 >= 5.2.0
        // $this->sendlog("data". json_encode($data), "webedior-ajax.log");
        if ($data === NULL) {
            $result["error"] = "Bad Response";
            return $result;
        }
        $status = self::$_trackerStatus[$data["status"]];
        // $this->sendlog($status . ' - ' . $data["status"], "webedior-ajax.log");
        switch ($status) {
            case "MustSave":
            case "Corrupted":
                $saved = 1;
                $uniqId = getUID();
                $key = $data["key"];
                $downloadUri = $data["url"];
                    if (isset($_GET["contentid"]) && !empty($_GET["contentid"])) {
                        // if($isModified == false){
                            try {

                                $targeturl = 'storage/uploads/process/doc_' . $uniqId . '.docx';
                                file_put_contents($targeturl, fopen($downloadUri, 'r'));
                                $changesurl = $data["changesurl"];
                                if(!empty($changesurl)){
                                    $targetchangeurl = 'storage/uploads/process/changefile_' . $uniqId . '.zip';
                                    file_put_contents($targetchangeurl, fopen($changesurl, 'r'));
                                }else{
                                    $targetchangeurl = '';
                                }

                                $contentId = $_GET["contentid"];
                                $key = $data["key"];
                                $status = $data["status"];
                                $history = json_encode($data["history"]);
                                $users = json_encode($data["users"]);
                                $actions = json_encode($data["actions"]);
                                $lastSaveDate = substr($data["lastsave"], 0, 10) . ' ' . substr($data["lastsave"], 11, 8);
                                $isModified = $data["notmodified"];
                                $result = $this->model->saveEcmOfficeVersion($key, $status, $targeturl, $targetchangeurl, $history, $users, $actions, $lastSaveDate, $isModified, $contentId);
                            }catch (Exception $e) {
                                // $this->sendlog("Caught exception: " . $e->getMessage(), "webedior-ajax.log"); 
                            }
                        // }
                    }else{
                        $fullName = $_GET["fullName"];
                        // $downloadUri = $data["url"];
                        // $path_parts = pathinfo(basename($fullName));
                        // $curExt = '.' . $path_parts['extension'];
                        // $downloadExt = strtolower('.' . pathinfo($downloadUri, PATHINFO_EXTENSION));
                        // if ($downloadExt != $curExt) {
                        //     $key = $this->getDocEditorKey($downloadUri);

                        //     try {
                        //         $convertedUri;
                        //         $percent = $this->GetConvertedUri($downloadUri, $downloadExt, $curExt, $key, FALSE, $convertedUri);
                        //         $downloadUri = $convertedUri;
                        //     } catch (Exception $e) {
                        //         $result["error"] = "error: " . $e->getMessage();
                        //         return $result;
                        //     }
                        // }
                        $saved = 1;
                        $arrContextOptions=array(
                            "ssl"=>array(
                                "verify_peer"=>false,
                                "verify_peer_name"=>false,
                            ),
                        );
                        if (($new_data = file_get_contents($downloadUri, false, stream_context_create($arrContextOptions))) === FALSE) {
                            // $this->sendlog("filegetcont: " . file_get_contents($downloadUri), "webedior-ajax.log");
                        // if (($new_data = file_get_contents($downloadUri)) === FALSE) {
                            $saved = 0;
                        } else {
                            // $this->sendlog("InputStream data: " . serialize($data), "webedior-ajax.log");
                            try { file_put_contents($fullName, $new_data, LOCK_EX); } 
                            catch (Exception $e) { 
                                // $this->sendlog("Caught exception: " . $e->getMessage(), "webedior-ajax.log"); 
                            }
                        }
                    }
                // $this->sendlog("saved: " . $saved, "webedior-ajax.log");
                $result["c"] = "saved";
                $result["status"] = $saved;
                break;
            }
        return $result;
    }

    public function getDocEditorKey($fileName) {
        $key = $fileName;
        $stat = filemtime($key);
        $key = $key . $stat;
        return $this->GenerateRevisionId($key);
    }    

    public function GenerateRevisionId($expected_key) {
        if (strlen($expected_key) > 20) $expected_key = crc32( $expected_key);
        $key = preg_replace("[^0-9-.a-zA-Z_=]", "_", $expected_key);
        $key = substr($key, 0, min(array(strlen($key), 20)));
        return $key;
    }    

    public function nocache_headers() {
        $headers = array(
            'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        );
        $headers['Last-Modified'] = false;
        unset( $headers['Last-Modified'] );
        // In PHP 5.3+, make sure we are not sending a Last-Modified header.
        if ( function_exists( 'header_remove' ) ) {
            @header_remove( 'Last-Modified' );
        } else {
            // In PHP 5.2, send an empty Last-Modified header, but only as a
            // last resort to override a header already sent. #WP23021
            foreach ( headers_list() as $header ) {
                if ( 0 === stripos( $header, 'Last-Modified' ) ) {
                    $headers['Last-Modified'] = '';
                    break;
                }
            }
        }
        foreach( $headers as $name => $field_value )
            @header("{$name}: {$field_value}");
    }    

    public function viewer($workSpaceParams) {
        parse_str($workSpaceParams, $param);
        $this->view->uniqId = getUID();

        $this->load->model('mddoc', 'middleware/models/');

        if (isset($param['workSpaceParam']['id'])) {
            $_POST['rowId'] = $param['workSpaceParam']['id'];
            $this->view->getContent = $this->model->documentEcmMapListModel();
        }
        
        if (isset($this->view->getContent)) {
            return array(
                'Html' => $this->view->renderPrint('ecm_content_viewer', self::$viewPath)
            );            
        }

        return array(
            'Html' => ''
        );
    }

    public function removeHrmTimeSheet() {
        (Array) $param = array(
            'id' => Input::post('id')
        );

        $this->load->model('mdmetadata', 'middleware/models/');
        
        $metaRow = $this->model->getMetaDataModel(Config::getFromCache('hrmCalendarDeleteProcessId'));        
        $result = $this->ws->runResponse(GF_SERVICE_ADDRESS, $metaRow['META_DATA_CODE'], $param);       

        if ($result['status'] == 'success') {
            jsonResponse(array(
                'status' => 'success',
                'message' => Lang::line('msg_delete_success')
            ));
        } else {
            jsonResponse(array(
                'status' => 'warning', 
                'message' => $this->ws->getResponseMessage($result)
            ));
        }        
    }

    public function replaceDocxTexts() {
        require_once(BASEPATH . LIBS . "Office/Word/PHPWord.php");
        $srcFile = Input::post('sourceFile');
        $replaceTag = Input::post('replaceTag');

        $replaceTextArr = json_decode($_POST['replaceArr']);
        $pinfo = pathinfo($srcFile);

        $pdfArray = $outArray = array();
        if (!is_array($replaceTextArr)) {
            $x = $replaceTextArr;
            $replaceTextArr = array($x);
        }

        foreach ($replaceTextArr as $key => $value) {
            $uniqid = getUID();
            $templateProcessor = PHPWord::loadTemplate($srcFile);
            $templateProcessor->setValue($replaceTag, $value);
            $newpath = 'storage/uploads/signedDocument/test_'. $pinfo['filename'] . '_' . $uniqid . '.' . $pinfo['extension'];

            $templateProcessor->save($newpath);
            $pdfpath = $this->docxConverter($newpath);
            unlink($newpath);
            array_push($pdfArray, $pdfpath);
        }

        if(sizeof($pdfArray) > 0){
            jsonResponse(array(
                'status' => 'success', 
                'pdfArr' => $pdfArray
            ));
        }else{
            jsonResponse(array(
                'status' => 'error'
            ));
        }
    }

    public function docxConverter($path){
        $url = URL . $path;
        $dockey = $this->getDocEditorKey($path);
        $service_url = Config::getFromCacheDefault('DOC_SERVER_LOCAL', null, '') . '/ConvertService.ashx';
        $curl = curl_init($service_url);
        $data = array('async' => false, 
            'filetype' => 'docx', 
            'key' => $dockey, 
            'outputtype' => 'pdf', 
            'url' => $url);
        $curl_post_data = json_encode($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            return false;
        }else{
            curl_close($curl);
            $xml=simplexml_load_string($curl_response);
            if($xml->FileUrl->__toString() != '-1'){
                $resultPath = 'storage/uploads/process/' . basename($path, ".docx") . '_' .  getUID() .'.pdf';
                copy($xml->FileUrl->__toString(), $resultPath);
                return $resultPath;
            }
        }
    }

    public function docxToPdfConverter(){
        $path =  Input::post('path');
        if($path){
            $path_parts = pathinfo($path);
            $output =  Input::post('output');
            if(empty($output)){
                $output = 'pdf';
            }
            $url = URL . $path;
            $ext = $path_parts['extension'];
            $dockey = $this->getDocEditorKey($path);
            $service_url = Config::getFromCacheDefault('DOC_SERVER_LOCAL', null, '') . '/ConvertService.ashx';
            $curl = curl_init($service_url);
            $data = array('async' => false, 
                'filetype' => $ext, 
                'key' => $dockey, 
                'outputtype' => $output, 
                // 'title' => 'Example', 
                'url' => $url);
            $curl_post_data = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            $curl_response = curl_exec($curl);

            $response = array( 'status' => 'error', 'message' => 'Docx to pdf conversion service failed. Please contact service admin.' );
            if ($curl_response === false) {
                $info = curl_getinfo($curl);
                curl_close($curl);
            }else{
                curl_close($curl);
                $xml=simplexml_load_string($curl_response);
                if($xml->FileUrl->__toString() != '-1'){
                    $resultPath = 'storage/uploads/process/' . $path_parts['filename'] .  '.' . $output;
                    copy($xml->FileUrl->__toString(), $resultPath);
                    $response = array( 'status' => 'success', 'url' => $resultPath, 'key' => $dockey );
                }
            }
        }else{
            $response = array( 'status' => 'error', 'message' => 'Path to file not received' );
        }
        echo json_encode($response);
    }  

    public function checkGantt($metaDataId =  null) {
        $this->view->metaDataId = ($metaDataId) ? $metaDataId : Input::getCheck('metaDataId');
        
        $mdObjectCtrl = Controller::loadController('Mdmetadata', 'middleware/controllers/');
        $resultData = $mdObjectCtrl->getMetaDataByGroup($this->view->metaDataId);
        if(array_search('id', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) &&
            array_search('text', (array_map('strtolower',array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) ) &&
            array_search('start_date', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) &&
            array_search('duration', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) &&
            array_search('progress', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) &&
            array_search('order', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) &&
            array_search('parent', (array_map('strtolower',array_column($resultData, 'FIELD_PATH')))) )
        {
            $this->view->drawGantt = true;
        } else{
            $this->view->drawGantt = false;
        };
        
        echo $this->view->drawGantt;
    }
    
    public function gantt() {
        /* test */
        
        $this->view->metaDataId = $this->view->dataViewId = Input::post('metaDataId');
        
        $this->load->model('mdobject', 'middleware/models/');
        $this->view->gridOption = $this->model->getDVGridOptionsModel($this->view->metaDataId);
        $this->view->row = $this->model->getDataViewConfigRowModel($this->view->metaDataId);
        $_POST['sort']      = $this->view->gridOption['SORTNAME'];
        $_POST['order']     = $this->view->gridOption['SORTORDER'];
        $_POST['defaultCriteriaData']     = '';
        Session::init();
        $this->view->recordList = $this->model->getDataViewRecordListModel($this->view->metaDataId, null, null);
        $this->view->metaDataCode = '';
        $this->view->dataViewProcessCommand = $this->model->dataViewProcessCommandModel($this->view->metaDataId, '', false, true, $this->view->metaDataId);

        $this->view->name1 = isset($this->view->row['dataViewLayoutTypes']['explorer']['fields']['name1']) ? $this->view->row['dataViewLayoutTypes']['explorer']['fields']['name1'] : null;
        $this->view->name2 = isset($this->view->row['dataViewLayoutTypes']['explorer']['fields']['name2']) ? $this->view->row['dataViewLayoutTypes']['explorer']['fields']['name2'] : null;
        $this->view->name3 = isset($this->view->row['dataViewLayoutTypes']['explorer']['fields']['name3']) ? $this->view->row['dataViewLayoutTypes']['explorer']['fields']['name3'] : null;

        $dataSource = $this->model->buildGanttChartDataSource($this->view->row, $this->view->recordList);

        $this->view->dataSourceJson = json_encode($dataSource);
        $this->view->istrigger = true;
        $response = array(
            'Html' => $this->view->renderPrint('viewer/explorer/layout/ganttchart', 'middleware/views/metadata/dataview/'),
            'Title' => 'gantt',
            'close_btn' => $this->lang->line('close_btn'),
        );
        echo json_encode($response); die;
        /* test */
        $mdObjectCtrl = Controller::loadController('Mdmetadata', 'middleware/controllers/');
        $resultData = $mdObjectCtrl->getMetaDataByGroup($this->view->metaDataId);
        $response = array(
            'Html' => $this->view->renderPrint('gantt', "middleware/views/metadata/dataview/viewer/detail/layout/ecommerce/"),
            'Title' => 'gantt',
            'close_btn' => $this->lang->line('close_btn'),
        );
        echo json_encode($response);
    }

    public function getEvents(){
        $metaDataId = get('metaDataId');
        if($metaDataId){
            $_POST['metaDataId'] = $metaDataId;
        }
        $mdObjectCtrl = Controller::loadController('Mdobject', 'middleware/controllers/');
        $resultData = $mdObjectCtrl->dataViewDataGrid(false, false);
        $arr = $resultData['rows'];     
        echo json_encode(array('data' => $arr));
    }
    
    public function ajaxHrmTimesheetLogLoad() {
        
        Auth::handleLogin();
        
        if (Input::postCheck('filterStartDate') && Input::postCheck('filterEndDate')) {
            $this->view->filterStartDate = Input::post('filterStartDate');
            $this->view->filterEndDate = Input::post('filterEndDate');
        } else {
            $this->view->filterStartDate = Date::currentDate('Y-m').'-01';
            $this->view->filterEndDate = Date::currentDate('Y-m').'-'.date('t', strtotime($this->view->filterStartDate));
        }
        
        $criteria = array(
            'sessionEmployeeId' => array(
                array(
                    'operator' => '=',
                    'operand' => Ue::sessionEmployeeId()
                )
            ), 
            'filterStartDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterStartDate
                )
            ), 
            'filterEndDate' => array(
                array(
                    'operator' => '=',
                    'operand' => $this->view->filterEndDate
                )
            )
        );

        $this->view->calendarDvId = Config::getFromCache('hrmCalendarDVId');
        $this->view->calendarData = $this->model->loadListModel($this->view->calendarDvId, $criteria);
        
        $calendarGroupedData = Arr::groupByArray($this->view->calendarData, 'balancedate');
        (Array) $cdata = array();
        foreach ($calendarGroupedData as $balanceDate => $row) {

            $evnt = $row['row'];
            $startdate = $enddate = $plantime = $charintime = $charouttime = ''; 
            $rowdata = $requests = array();
            
            if ($evnt['starttime']) {
                $startdate = $balanceDate.' '.$evnt['starttime'].':00';
            }
            if ($evnt['endtime']) {
                $enddate = $balanceDate.' '.$evnt['endtime'].':00';
            }

            if ($startdate == '' && $evnt['charintime']) {
                $startdate = $balanceDate.' '.$evnt['charintime'];
            } elseif ($startdate == '') {
                $startdate = $balanceDate.' 00:00:00';
            }
            if ($enddate == '' && $evnt['charouttime']) {
                $enddate = $balanceDate.' '.$evnt['charouttime'];
            }  elseif ($enddate == '') {
                $enddate = $balanceDate.' 00:00:00';
            }
            if ($evnt['plantime']) {
                $plantime = $evnt['plantime'];
            }

            if (isset($row['rows'])) {
                $rows = $row['rows'];
                
                foreach ($rows as $child) {
                    if ($child['id']) {
                        $tempD = array();
                        foreach($child as $ck => $crow) {
                            $tempD[$ck] = str_replace("'", "\'", Str::nlToSpace($crow));
                        }
                        array_push($requests, $tempD);
                    }
                }
            }
            
            $tempD = array();
            foreach ($evnt as $ck => $crow) {
                if ($ck != 'id') {
                    $tempD[$ck] = str_replace("'", "\'", Str::nlToSpace($crow));
                }
                $rowdata = $tempD;
            }
            
            if ($startdate !== ' 00:00:00' && $startdate) {
                $tempData = array(
                    'start' => $startdate,
                    'end' => $enddate,
                    'plantime' => $plantime,
                    'workingtime' => issetParam($evnt['cleantime']),
                    'absenttime' => issetParam($evnt['absenttime']),
                    'latetime' => issetParam($evnt['latetime']),
                    'earlytime' => issetParam($evnt['earlytime']),
                    'cause3' => issetParam($evnt['cause3']),
                    'cause4' => issetParam($evnt['cause4']),
                    'cause5' => issetParam($evnt['cause5']),
                    'cause6' => issetParam($evnt['cause6']),
                    'cause20' => issetParam($evnt['cause20']),
                    'cause7' => issetParam($evnt['cause7']),
                    'cause8' => issetParam($evnt['cause8']),
                    'cause10' => issetParam($evnt['cause10']),
                    'cause11' => issetParam($evnt['cause11']),
                    'cause12' => issetParam($evnt['cause12']),
                    'cause1' => issetParam($evnt['cause1']),
                    'charintime' => issetParam($evnt['charintime']),
                    'charouttime' => issetParam($evnt['charouttime']),
                    'holidaycolor' => issetParam($evnt['holidaycolor']),
                    'holidayname' => issetParam($evnt['holidayname']),
                    'id' => issetParam($evnt['id']),
                    'requests' => $requests,
                    'rowdata' => $rowdata
                );
                
                array_push($cdata, $tempData);
            }
        }
        
        echo json_encode(array('data' => $cdata)); exit;
    }

    public function widgetStandart($listConfig, $jsonAttr) {
        try {
            $criteria = array();
            $paging = array(
                'offset' => 1,
                'pageSize' => 100
            );
            // $criteria = array(
            //     'itemcategoryid' => array(
            //         array(
            //             'operator' => '=',
            //             'operand' => 1515665343763
            //         )
            //     )
            // );       
            $this->view->uniqId = getUID();            
            $this->view->datasrc = [];
            $this->load->model('mdmetadata', 'middleware/models/');
            $metaRow = $this->model->getMetaDataModel($listConfig["metadataid"]);
            $this->load->model('mdwidget', 'middleware/models/'); 

            $widgetCode = Str::lower($listConfig["widgetcode"]);

            if (issetParam($metaRow['META_TYPE_ID']) == Mdmetadata::$businessProcessMetaTypeId) {                
                $this->view->datasrc = $this->model->loadListProcessModel($metaRow["META_DATA_CODE"]);        
                if ($listConfig["bpsectiondtl"]) {
                    if ($widgetCode === 'cloud_list_linechart') { // Tur shiidel zasah ystoi shuu!!!
                        $getDtlPath = explode(".", $listConfig["bpsectiondtl"][0]['fieldpath']);
                        foreach ($listConfig["bpsectiondtl"] as $bkey => $brow) {
                            $listConfig["bpsectiondtl"][$bkey]['fieldpath'] = preg_replace('/^(.*?)\./', '', $brow['fieldpath']);
                        }                    
                        $this->view->datasrc = $this->view->datasrc[$getDtlPath[0]];
                    }
                } else {
                    $this->view->datasrc = [];
                }
            } elseif (issetParam($jsonAttr['renderType']) !== "dataview") {
                $this->view->datasrc = $this->model->loadListModel($listConfig["metadataid"], $criteria, $paging, 0, $jsonAttr);                
                
                if (empty($this->view->datasrc) && issetParam($jsonAttr['data'])) {
                    $this->view->datasrc = $jsonAttr['data'];
                }
            }

            $this->view->positionConfig = [];
            if ($listConfig["bpsectiondtl"]) {
                $this->view->positionConfig = Arr::groupByArray($listConfig["bpsectiondtl"], 'positionname');
            }
            
            $this->view->jsonAttr = $jsonAttr;                                    

            if (issetParam($jsonAttr['renderType']) === "dataview") {
                $_POST['dvIgnoreToolbar'] = 1;
                $_POST['isNeedTitle'] = '0';
                $_POST['needTitle'] = '0';
                $_POST['ignorePermission'] = '1';
                $_POST['isAjax'] = '1';
                $dataViewHtml = '<style>.datagrid-row-alt{background:transparent}.object-height-row2-minus-'.$listConfig["metadataid"].'{display:none}#objectDataView_'.$listConfig["metadataid"].' .row{margin-right: 0;margin-left: 0} #objectDataView_'.$listConfig["metadataid"].' .col{padding-right: 0;padding-left: 0}</style>';
                $dataViewForm = (new Mdobject())->dataview($listConfig["metadataid"], false, 'array');                                
                $dataViewHtml .= '<div class="bg-white px-3 py-3" style="'.issetParam($jsonAttr['style']).'">'.(issetParam($jsonAttr['title']) ? '<div style="font-size:15px;color:#585858;" class="mb-3 font-bold">'.Lang::line($jsonAttr['title']).'</div>' : '');
                $dataViewHtml .= $dataViewForm['Html'];
                if (issetParam($jsonAttr['viewAll'])) {
                    $dataViewHtml .= '<div class="d-flex justify-content-end"><div style="color:#A0A0A0;font-size: 11px;margin-top: 12px;float: right;cursor: pointer;" onclick="dataViewAll(this)" data-row={} data-dataviewid="'.$jsonAttr['viewAll'].'">'.Lang::lineDefault('view_all', 'Бүгдийг харах').'</div></div>';
                }
                $dataViewHtml .= '</div>';
                return $dataViewHtml;
            } elseif (issetParam($metaRow['META_TYPE_ID']) == Mdmetadata::$packageMetaTypeId) {
                $packageHtml = '<div class="bg-white px-3 py-3">';
                ob_start();
                (new Mdobject())->package($listConfig["metadataid"]);                                
                $packageHtml .= ob_get_clean(); 
                $packageHtml .= '</div>';
                return $packageHtml;
            } elseif (issetParam($metaRow['META_TYPE_ID']) == Mdmetadata::$diagramMetaTypeId) {
                $diagramHtml = '<div class="bg-white" style="'.issetParam($jsonAttr['style']).'">'.(issetParam($jsonAttr['title']) ? '<div style="font-size:15px;color:#585858;" class="font-bold">'.Lang::line($jsonAttr['title']).'</div>' : '');
                $diagramHtml .= (issetParam($jsonAttr['subTitle']) ? '<div style="color:#BCB5C3;font-size: 11px">'.Lang::line($jsonAttr['subTitle']).'</div>' : '');
                $diagramHtml .= '<div id="widget-layout-'.$listConfig["metadataid"].'"><script type="text/javascript">widgetLayoutCallDiagramByMeta("'.$listConfig["metadataid"].'");</script></div>';
                $diagramHtml .= '</div>';
                return $diagramHtml;
            } elseif (file_exists(self::$viewPath.'widgetStandart/'.$widgetCode.'.php')) {
                return $this->view->renderPrint('widgetStandart/'.$widgetCode, self::$viewPath);
            } else {
                return "Widget code: ".$widgetCode;
            }

        } catch (Exception $ex) {
        }        
    }

    public function renderAtom($row, $path, $positionConfig = [], $default = "", $removeDot = false) {
        if (isset($positionConfig[$path])) {
            $pathTemp = $path;
            $path = Str::lower($positionConfig[$path]["row"]["fieldpath"]);

            if ($removeDot) {
                $path = preg_replace('/^(.*?)\./', '', $path);
            }

            if (empty($row)) {
                return $path;
            }            
            
            $pathValue = issetDefaultVal($row[$path], $positionConfig[$pathTemp]["row"]["fieldpath"]);

            return $pathValue ? $pathValue : $default;
        } else {
            return $default;
        }
    }

    public function renderAtomPath($path, $positionConfig = [], $default = "") {
        if (isset($positionConfig[$path])) {
            return Str::lower($positionConfig[$path]["row"]["fieldpath"]);
        } else {
            return 'Path тохируулаагүй байна!';
        }
    }

    public function playWidgetStandart($widgetCode = '', $type = 'none') {
        if (is_numeric($widgetCode)) {
            $mdf = &getInstance();
            $mdf->load->model('mdform', 'middleware/models/');
            $widgetCodeTmp = $mdf->model->getMetaWidgetModel($widgetCode);
            if (file_exists(self::$viewPath.'widgetStandart/'.$widgetCodeTmp.'.php')) {
                $widgetCode = $widgetCodeTmp;
            }
        }

        if (file_exists(self::$viewPath.'widgetStandart/'.$widgetCode.'.php')) {
            $dummyFile = file_get_contents('assets/custom/widget/widget_dummy_data.txt');
            eval('$dummyData = '.$dummyFile.';'); 
            $this->view->uniqId = getUID();

            $this->view->datasrc = $dummyData[$widgetCode]['data'];
            $this->view->positionConfig = $dummyData[$widgetCode]['positionConfig'];
            
            if ($type === 'widget') {
                $this->view->title = 'Widget';  
                $this->view->css = array_unique(array_merge(array('custom/css/vr-card-menu.css'), AssetNew::metaCss()));
                $this->view->js = AssetNew::metaOtherJs();
                echo $this->view->renderPrint('header', 'projects/views/contentui/build/');
                echo '<link rel="stylesheet" href="assets/custom/css/tailwind.min.css">
                        <style type="text/css">
                            /* .content-wrapper .content {
                                padding-left: 0px;
                            } */
                            .widget-container {
                                margin-left: -15px;
                            }
                            .shadow-citizen {
                                / box-shadow: 0 0 #0000, 0 0 #0000, 0px 20px 27px 0px rgba(0, 0, 0, 0.05); /
                                box-shadow: 0px 2px 14px rgba(0, 0, 0, 0.1);
                            }
                            .p-4 {
                                padding: 1rem !important;
                            }    
                            .p-3 {
                                padding: .75rem !important;
                            }   
                            .mb-5 {
                            margin-bottom: 1.25rem !important;
                            }
                            .page-content > .content-wrapper > .content {
                                padding-bottom: 0 !important;
                            }     
                            .bg-ssoSecond {
                                background-color: rgba(67, 56, 202, 1);
                            }   
                            .text-ssoSecond {
                                color: rgba(67, 56, 202, 1);
                            }     
                            .hover\:bg-ssoSecond:hover {
                                background-color: rgba(67, 56, 202, 1) !important;
                            }    
                            .bg-gradient-to-r {
                                background-image: linear-gradient(to right, #4338CA, #4338ca75, rgba(67, 56, 202, 0));
                            }   
                            .hover\:text-white:hover {
                                color: rgba(255, 255, 255, 1);
                            }
                            .hover\:text-white:hover i {
                                color: #fff;
                            }
                            .hover\:from-sso:hover {
                                background-color: #B2E392 !important;
                            }    
                            .cloud-font-color-black {
                                color: #333;
                            }
                            div.datepicker .table-sm {
                                width:100%;
                                height: 500px;
                            }    
                            div.datepicker .table-sm td, div.datepicker .table-sm th {
                                font-size: 16px;
                            }    
                            div.datepicker {
                                background-color: #F7F8FF;
                                padding-top: 30px;
                                margin-top: 20px;
                                padding-bottom: 30px;
                                border-radius: 1rem !important;
                                margin-right: 20px;
                            }   
                            .cloud-grid-icon i {
                                color:#B2E392
                            } 
                            .cloud-grid-icon {
                                text-align: center;
                                margin-top:10px;
                            }
                            .cloud-modulelist-tab {
                                margin-top: 20px;
                            } 
                            .cloud-modulelist-tab li {
                                display: inline-block;
                                font-size: 14px;
                                color: #585858;        
                                margin-right: 25px;
                                cursor: pointer;
                                font-weight: bold;
                            }
                            .cloud-modulelist-tab li.active {
                                color: #fff;        
                                border-bottom: 2px solid #fff;
                            }
                            .cloud-badge {
                                border-radius: 7px;
                                font-size:14px;
                                padding: 4px 13px 4px 13px;
                                border-color:#D1D5DB !important;        
                                color: #67748E !important;
                                font-weight: normal;
                                cursor: pointer;
                            }
                            .cloud-badge.active {
                                border-color:#699BF7 !important;        
                                color: #699BF7 !important
                            }
                        </style>';
                echo $this->view->renderPrint('widgetStandart/'.$widgetCode, self::$viewPath);
                echo $this->view->renderPrint('footer', 'projects/views/contentui/build/');
            } else {
                echo $this->view->renderPrint('widgetStandart/'.$widgetCode, self::$viewPath);
            }
        } else {
            echo 'Widget олдсонгүй';
        }        
    }
    
}
