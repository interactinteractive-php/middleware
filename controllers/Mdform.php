<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

/**
 * Mdform Class 
 * 
 * @package     IA PHPframework
 * @subpackage	Middleware
 * @category	Form
 * @author	B.Och-Erdene <ocherdene@interactive.mn>
 * @link	http://www.interactive.mn/PHPframework/Middleware/Mdform
 */
class Mdform extends Controller {
    
    public static $getKpiCommandCode = 'kpiDmBookGetDtl_004';
    public static $getKpiConsolidateCommandCode = 'kpiDmBookGetDtl_007';
    public static $rowCountPrefix = 'mainRowCount][]';
    public static $kpiRenderParamControlView = 'kpiRenderParamControlByPrint';
    public static $pathPrefix = null;
    public static $pathSuffix = null;
    public static $addonPathPrefix = null;
    public static $mvPathPrefix = null;
    public static $mvPathSuffix = null;
    public static $kpiTypeCode = null;
    public static $kpiTemplateId = null;
    public static $rootTemplateId = null;
    public static $parentDtlId = null;
    public static $tmpParentDtlId = null;
    public static $kpiExpressionTempId = null;
    public static $defaultTplId = null;
    public static $defaultTplSavedId = null;
    public static $fillFromExpression = null;
    public static $firstTplId = null;
    public static $recordId = null;
    public static $subTmpIndctrByCriteria = null;
    public static $pfTranslationValueTextarea = null;
    public static $indicatorCellStyle = null;
    public static $indicatorFormLogo = null;
    public static $methodStructureIndicatorId = null;
    public static $addonId = 0;
    public static $radioPrefix = '';
    public static $labelName = '';
    public static $inputId = '';
    public static $kpiIndicatorWidth = '';
    public static $kpiFactShowType = '';
    public static $subUniqId = '';
    public static $kpiFullExpressions = '';
    public static $kpiRenderType = '';
    public static $kpiControlIndex = 0;
    public static $isShowName = 0;
    public static $mergeColCount = 2;
    public static $indicatorLevel = 0;
    public static $labelWidth = '180px';
    public static $isKpiTempCriteria = false;
    public static $isIndicatorMerge = false;
    public static $isSubKpiForm = false;
    public static $isSavedKpiForm = false;
    public static $isUseMergeMatrix = false;
    public static $isWizard = false;
    public static $isOnlyKpiMultiLang = true;
    public static $isIndicatorRendering = false;
    public static $isControlViewLabel = false;
    public static $isRawDataMart = false;
    public static $isRowsReplacePath = false;
    public static $processParamData = array();
    public static $kpiDmDtlData = array();
    public static $kpiDmMart = array();
    public static $kpiTempCriteria = array();
    public static $resultIndicator = array();
    public static $resultFacts = array();
    public static $pfTranslationValue = array();
    public static $kpiIndicatorRowData = array();
    public static $indicatorCellExpression = array();
    public static $indicatorColExpression = array();
    public static $indicatorHdrExpression = array();
    public static $indicatorTemplateRow = array();
    public static $indicatorConfigValues = array();
    public static $tabRender = array();
    public static $topTabRender = array();
    public static $topTabRenderShow = array();
    public static $gridStyler = array();
    public static $typeIds = array('100', '101', '102', '103', '104', '105');
    public static $headerInlineFields = array();
    private static $viewPath = 'middleware/views/form/';
    private $spreadsheet = null;
    private $spreadsheetArr = array();
    private $sheetName = array();
    private $sIndex = array();
    private $sheetIndex = array();
    private $oneLineSheetColumnIndex = array();
    private $oneLineMergeCells = array();
    private $oneLineTitleRowIndex = 0;
    private $oneLineLabelRowIndex = 0;
    private $oneLineValueRowIndex = 0;
    
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }

    public function showKpiForm() {
        
        $postData = Input::postData();
        
        if (isset($postData['param']) && 
            (
                (isset($postData['param']['kpiTemplateId']) && !empty($postData['param']['kpiTemplateId'])) 
                || (isset($postData['param']['templateId']) && !empty($postData['param']['templateId'])) 
            )) {
            
            $paramData = $postData['param'];
            
            self::$processParamData = $paramData;
            self::$isShowName = Input::post('isShowName');
            self::$isOnlyKpiMultiLang = Config::getFromCache('isIgnoreKpiMultiLang') == '1' ? false : true;
            
            if (array_key_exists('kpiDtlGetProcess', $paramData)) {
                Mdform::$getKpiCommandCode = $paramData['kpiDtlGetProcess'];
            }
            
            if (array_key_exists('kpiTemplateId', $paramData)) {
                $templateId = $paramData['kpiTemplateId']; 
            } elseif (array_key_exists('templateId', $paramData)) {
                $templateId = $paramData['templateId']; 
            }
            
            $bookId = $ids = null;
            
            if (array_key_exists('kpiId', $paramData) && $paramData['kpiId']) {
                $bookId = $paramData['kpiId'];
            } elseif (array_key_exists('id', $paramData)) {
                $bookId = $paramData['id'];
            }
            
            if ($bookId && !is_numeric($bookId) && in_array(strtolower($bookId), array('vid1', 'vid2', 'vid3', 'seq'))) {
                $bookId = null;
            }
            
            if (array_key_exists('kpiIds', $paramData) && $paramData['kpiIds']) {
                $ids = $paramData['kpiIds'];
            } elseif (array_key_exists('ids', $paramData)) {
                $ids = $paramData['ids'];
            }
            
            $processArr = array(
                'uniqId'    => $postData['uniqId'], 
                'processId' => $postData['methodId'], 
                'viewMode'  => $postData['viewMode']
            );
            
            $processHeaderParam = array(
                'bookId' => $bookId, 
                'ids'    => $ids 
            );
            
            $form = '';
            
            $tempRow = $this->model->getKpiTemplateRow($templateId);
            $childs  = $this->model->getChildKpiTemplates($templateId);

            if ($childs && !in_array($tempRow['TYPE_ID'], Mdform::$typeIds)) {
                
                self::$rootTemplateId = $templateId;
                
                if ($processArr['viewMode'] == 'print') {
                    
                    foreach ($childs as $child) {
                        $formKpi = self::renderKpiTemplateFormTypeByPrint($child, $processArr, true, $processHeaderParam);
                        $form .= str_replace('_'.$child['ID'], '_'.$postData['uniqId'], $formKpi);
                    }
                    
                } else {
                    
                    if ($processArr['viewMode'] == 'wizard') {
                        
                        $form = self::renderKpiWizardForm($childs, $processArr, $processHeaderParam, $postData['uniqId']);
                                
                    } else {
                        
                        foreach ($childs as $child) {
                                                
                            $formKpi = self::renderKpiTemplateFormType($child, $processArr, true, $processHeaderParam);

                            if (Mdform::$kpiExpressionTempId) {
                                $formKpi = str_replace('_'.Mdform::$kpiExpressionTempId, '_'.$postData['uniqId'], $formKpi);
                            }

                            $form .= str_replace('_'.$child['ID'], '_'.$postData['uniqId'], $formKpi);
                        }
                    }
                }
                
            } else { 
                
                if (!empty($tempRow)) {
                    
                    if ($processArr['viewMode'] == 'print') {
                        
                        $form = self::renderKpiTemplateFormTypeByPrint($tempRow, $processArr, false, $processHeaderParam);
                        
                    } else {
                        
                        $form = self::renderKpiTemplateFormType($tempRow, $processArr, false, $processHeaderParam);
                        
                        if (Mdform::$kpiExpressionTempId) {
                            $form = str_replace('_'.Mdform::$kpiExpressionTempId, '_'.$postData['uniqId'], $form);
                        }
                    }
                    
                    $form = str_replace('_'.$templateId, '_'.$postData['uniqId'], $form);
                }
            }
            
            $response = array('status' => 'success', 'html' => $form);
            
        } else {
            $response = array('status' => 'info', 'message' => 'Загвар сонгоно уу.');
        }
        
        echo json_encode($response);
    }
    
    public function showIndicatorForm() {
        
        $postData = Input::postData();
        
        if (isset($postData['param']) && (isset($postData['param']['indicatorId']) && !empty($postData['param']['indicatorId']))) {
            
            $paramData = $postData['param'];
            
            self::$isIndicatorRendering = true;
            self::$processParamData = $paramData;
            self::$isShowName = Input::post('isShowName');
            self::$isOnlyKpiMultiLang = Config::getFromCache('isIgnoreKpiMultiLang') == '1' ? false : true;
            
            $templateId = $paramData['indicatorId']; 
            $bookId = null;
            
            if (array_key_exists('id', $paramData)) {
                $bookId = $paramData['id'];
            }
            
            $processArr = array(
                'uniqId'    => $postData['uniqId'], 
                'processId' => $postData['methodId'], 
                'viewMode'  => $postData['viewMode']
            );
            
            $processHeaderParam = array(
                'bookId' => $bookId, 
                'ids'    => null 
            );
            
            $form = '';
            
            $tempRow = $this->model->getKpiIndicatorRowModel($templateId);
            $childs  = $this->model->getChildKpiIndicatorsModel($templateId);

            if ($childs) {
                
                self::$rootTemplateId = $templateId;
                
                if ($processArr['viewMode'] == 'print') {
                    
                    foreach ($childs as $child) {
                        $formKpi = self::renderKpiTemplateFormTypeByPrint($child, $processArr, true, $processHeaderParam);
                        $form .= str_replace('_'.$child['ID'], '_'.$postData['uniqId'], $formKpi);
                    }
                    
                } else {
                    
                    foreach ($childs as $child) {
                                                
                        $formKpi = self::renderKpiTemplateFormType($child, $processArr, true, $processHeaderParam);

                        if (Mdform::$kpiExpressionTempId) {
                            $formKpi = str_replace('_'.Mdform::$kpiExpressionTempId, '_'.$postData['uniqId'], $formKpi);
                        }

                        $form .= str_replace('_'.$child['ID'], '_'.$postData['uniqId'], $formKpi);
                    }
                }
                
            } else { 
                
                if (!empty($tempRow)) {
                    
                    if ($processArr['viewMode'] == 'print') {
                        
                        $form = self::renderKpiTemplateFormTypeByPrint($tempRow, $processArr, false, $processHeaderParam);
                        
                    } else {
                        
                        $form = self::renderKpiTemplateFormType($tempRow, $processArr, false, $processHeaderParam);
                        
                        if (Mdform::$kpiExpressionTempId) {
                            $form = str_replace('_'.Mdform::$kpiExpressionTempId, '_'.$postData['uniqId'], $form);
                        }
                    }
                    
                    $form = str_replace('_'.$templateId, '_'.$postData['uniqId'], $form);
                }
            }
            
            $response = array('status' => 'success', 'html' => $form);
            
        } else {
            $response = array('status' => 'info', 'message' => 'Загвар сонгоно уу.');
        }
        
        echo json_encode($response); exit;
    }
    
    public function renderKpiWizardForm($childs, $processArr, $processHeaderParam, $uniqId) {
        
        Mdform::$isWizard = true;
        $this->view->wizard = '<div id="wizard-'.$uniqId.'" class="d-none">';
        
        foreach ($childs as $child) {
                                                
            $formKpi = self::renderKpiTemplateFormType($child, $processArr, false, $processHeaderParam);

            if (Mdform::$kpiExpressionTempId) {
                $formKpi = str_replace('_' . Mdform::$kpiExpressionTempId, '_' . $uniqId, $formKpi);
            }
            
            $this->view->wizard .= '<h3>'.$child['NAME'].'</h3>';
            
            $this->view->wizard .= '<section>';
                $this->view->wizard .= str_replace('_' . $child['ID'], '_' . $uniqId, $formKpi);
            $this->view->wizard .= '</section>';
            
            Mdform::$kpiFullExpressions = str_replace('_' . $child['ID'], '_' . $uniqId, Mdform::$kpiFullExpressions);
        }
        
        $this->view->wizard .= '</div>';
        
        $this->view->uid = $uniqId;
        
        return $this->view->renderPrint('kpi/wizard', self::$viewPath);
    }
    
    public function subKpiForm() {
        
        $postData   = Input::postData();
        $templateId = Input::param($postData['templateId']);
        
        $tempRow = $this->model->getKpiTemplateRow($templateId);
                
        if (!empty($tempRow)) {
            
            $processArr = array(
                'uniqId'    => $postData['uniqId'], 
                'processId' => $postData['methodId'], 
                'viewMode'  => $postData['viewMode']
            );
            
            $processHeaderParam = array(
                'bookId' => $postData['bookId'], 
                'ids'    => null 
            );
            
            if (isset($postData['subKpiDmDtl'])) {
                
                $processHeaderParam['subKpiDmDtl'] = 1;
                $processHeaderParam['indicatorId'] = $postData['indicatorId'];
                
                $tempRow['TYPE_CODE'] = null;
            }
            
            if (isset($postData['getProcessCode'])) {
                Mdform::$getKpiCommandCode = $postData['getProcessCode'];
            }
            
            if (isset($postData['subTmpIndctrByCriteria'])) {
                Mdform::$subTmpIndctrByCriteria = Input::param($postData['subTmpIndctrByCriteria']);
            }
            
            Mdform::$isSubKpiForm = true;
            Mdform::$pathPrefix = $postData['groupPath'];
            Mdform::$rowCountPrefix = 'rowCount][0][]';
            Mdform::$subUniqId = $processArr['uniqId'];
            Mdexpression::$setSubMainSelector = $processArr['uniqId'];
            Mdexpression::$kpiExpresssionPrefix = $postData['groupPath'];
            
            //Mdform::$radioPrefix = '[0]';
            
            $form = '';
            
            $childs = $this->model->getChildKpiTemplates($templateId);

            if ($childs && !in_array($tempRow['TYPE_ID'], Mdform::$typeIds)) {
                
                self::$rootTemplateId = $templateId;
                
                $replacerUIDs = array();
                
                if ($processArr['viewMode'] == 'print') {
                    
                    foreach ($childs as $child) {
                        
                        $formKpi = self::renderKpiTemplateFormTypeByPrint($child, $processArr, true, $processHeaderParam);
                        
                        $uid = getUID();
                        
                        $form .= str_replace('_' . $child['ID'], '_' . $uid, $formKpi);
                        
                        $replacerUIDs[$child['ID']] = $uid;
                    }
                
                } else {
                    
                    foreach ($childs as $child) {
                                            
                        $formKpi = self::renderKpiTemplateFormType($child, $processArr, true, $processHeaderParam);

                        $uid = getUID();

                        $formKpi = str_replace('kpiDmDtl-' . $child['ID'], 'kpiDmDtl-' . $uid, $formKpi);
                        $formKpi = str_replace('bp_window_', '$kpiTmp_', $formKpi);

                        $form .= str_replace('_' . $child['ID'], '_' . $uid, $formKpi);

                        $replacerUIDs[$child['ID']] = $uid;
                    }
                }
                
                foreach ($replacerUIDs as $replacerTempId => $replacerUID) {
                    $form = str_replace('_' . $replacerTempId, '_' . $replacerUID, $form);
                }
                
            } else {
                
                self::$rootTemplateId = issetParam($postData['rootTemplateId']);
                
                if ($processArr['viewMode'] == 'print') {
			
                    $form = self::renderKpiTemplateFormTypeByPrint($tempRow, $processArr, false, $processHeaderParam);
			
		} else {
                    $form = self::renderKpiTemplateFormType($tempRow, $processArr, false, $processHeaderParam);
		}

                $uid = getUID();

                $form = str_replace('kpiDmDtl-' . $templateId, 'kpiDmDtl-' . $uid, $form);
                $form = str_replace('bp_window_', '$kpiTmp_', $form);
                $form = str_replace('_' . $templateId, '_' . $uid, $form);
            }
            
            $response = array('status' => 'success', 'html' => $form);
            
            if ($tempRow['WIDTH']) {
                $response['width'] = $tempRow['WIDTH'];
            }
            
        } else {
            $response = array('status' => 'info', 'message' => 'Загвар сонгоно уу.');
        }
        
        echo json_encode($response); exit;
    }
    
    public function returnKpiForm($templateId, $bookId = null, $getCode = null) {
        
        if (empty($templateId)) {
            return '';
        }
        
        $this->load->model('mdform', 'middleware/models/');
        
        if ($getCode) {
            Mdform::$getKpiCommandCode = $getCode;
        }
            
        $processHeaderParam = array(
            'bookId' => $bookId, 
            'ids' => null 
        );

        $processArr = array(
            'uniqId' => '', 
            'processId' => '', 
            'viewMode' => 'print'
        );

        $form = '';

        $childs = $this->model->getChildKpiTemplates($templateId);

        if ($childs) {

            foreach ($childs as $child) {

                $form .= self::renderKpiTemplateFormTypeByPrint($child, $processArr, true, $processHeaderParam);
            }

        } else {

            $tempRow = $this->model->getKpiTemplateRow($templateId);

            if (!empty($tempRow)) {
                $form = self::renderKpiTemplateFormTypeByPrint($tempRow, $processArr, false, $processHeaderParam);
            }
        }
        
        return $form;
    }
    
    public function printKpiForm($html) {
        if (strpos($html, 'printKpiForm(') !== false) {
            preg_match_all('/printKpiForm\((.*?)\)/i', $html, $htmlKpiForms);
            
            if (count($htmlKpiForms[0]) > 0) {
                
                foreach ($htmlKpiForms[1] as $ek => $ev) {
                    
                    if (strpos($ev, ',') !== false) {
                        $evArr = explode(',', $ev);
                        
                        if (count($evArr) >= 2) {
                            
                            $templateId = trim(strip_tags($evArr[0]));
                            $bookId = trim(strip_tags($evArr[1]));
                            $getCode = trim(strip_tags(issetParam($evArr[2])));
                            
                            $returnKpiForm = self::returnKpiForm($templateId, $bookId, $getCode);
                        
                            $html = str_replace($htmlKpiForms[0][$ek], $returnKpiForm, $html);
                        }
                    } 
                }
            }
        }
        
        return $html;
    }
    
    public function viewKpiFromBp($templateId, $data) {
        
        self::$kpiDmDtlData = $data;
        self::$kpiRenderParamControlView = 'kpiRenderParamControlByLog';
        
        return self::returnKpiForm($templateId);
    }
    
    public function renderKpiTemplateFormType($tempRow, $processArr, $isMulti = false, $processHeaderParam = array()) {
        
        $form = '';

        if ($tempRow['PIVOT_VALUE_META_DATA_ID'] == '') {
            
            if ($tempRow['RENDER_TYPE'] == 'form') {
                $form = self::renderKpiTemplateForm($tempRow, $processArr, $isMulti, $processHeaderParam);
            } else {
                $form = self::renderKpiTemplateGrid($tempRow, $processArr, $isMulti, $processHeaderParam);
            }
            
        } else {
            $form = self::renderKpiTemplateGridByDv($tempRow, $processArr, $isMulti, $processHeaderParam);
        }
        
        return $form;
    }
    
    public function renderKpiTemplateFormTypeByPrint($tempRow, $processArr, $isMulti = false, $processHeaderParam = array()) {
        
        $form = '';
        
        if ($tempRow['RENDER_TYPE'] == 'form') {
            $form = self::renderKpiTemplateFormByPrint($tempRow, $processArr, $isMulti, $processHeaderParam);
        } else {
            $form = self::renderKpiTemplateGridByPrint($tempRow, $processArr, $isMulti, $processHeaderParam);
        }
        
        return $form;
    }
    
    public function renderKpiTemplateGrid($tempRow, $processArr, $isMulti, $processHeaderParam) {
        
        $this->load->model('mdform', 'middleware/models/');
        
        if (Mdform::$isSubKpiForm) {
            $tempRow['DEFAULT_TEMPLATE_ID'] = null;
        }
        
        if (!Mdform::$firstTplId && $tempRow['DEFAULT_TEMPLATE_ID']) {
            Mdform::$firstTplId = $tempRow['ID'];
            Mdform::$rootTemplateId = $tempRow['ID'];
        }
        
        if ($tempRow['DEFAULT_TEMPLATE_ID']) {
                
            $defaultTempRow = $this->model->getKpiTemplateRow($tempRow['DEFAULT_TEMPLATE_ID']);
            
            Mdform::$defaultTplId = $tempRow['DEFAULT_TEMPLATE_ID'];
            
            $defaultTemplateForm = self::renderKpiTemplateFormType($defaultTempRow, $processArr, false, $processHeaderParam);
            
            $this->view->defaultTemplateForm = str_replace('_'.$tempRow['DEFAULT_TEMPLATE_ID'], '_'.$processArr['uniqId'], $defaultTemplateForm);
            
            Mdform::$pathPrefix = null;
            Mdform::$defaultTplId = null;
        }
        
        if (Mdform::$firstTplId == $tempRow['ID']) {
            
            Mdform::$defaultTplSavedId = null;
            Mdform::$rootTemplateId = null;
            Mdform::$firstTplId = null;
        }
        
        Mdform::$kpiTypeCode       = $tempRow['TYPE_CODE'];
        Mdform::$kpiTemplateId     = $tempRow['ID'];
        Mdform::$kpiIndicatorWidth = $tempRow['INDICATOR_COL_WIDTH'];
        Mdform::$isKpiTempCriteria = false;
        Mdform::$pfTranslationValue = array();
        Mdform::$pfTranslationValueTextarea = null;
        
        Mdform::$kpiExpressionTempId = $tempRow['EXPRESSION_TEMPLATE_ID'];
        
        $this->view->templateId   = $tempRow['ID'];
        $this->view->templateName = '';
        $this->view->templateCode = '';
        $this->view->templateWidth  = $tempRow['WIDTH'];
        $this->view->templateHeight = ($tempRow['RENDER_TYPE'] == 'detail' ? 'auto' : $tempRow['HEIGHT']);
        $this->view->renderType     = $tempRow['RENDER_TYPE'];
        Mdform::$kpiRenderType      = $this->view->renderType;
        
        $this->view->viewMode     = $processArr['viewMode'];
        
        if ($isMulti || Mdform::$isShowName) {
            $this->view->templateName = html_tag('div', array('class' => 'kpi-template-name'), '<i class="fa fa-check-square-o"></i> '.$tempRow['NAME']);
        }
        
        if (Mdform::$kpiTypeCode == 2) {
            Mdform::$getKpiCommandCode = 'selectQry';
        }
        
        if ($tempRow['MERGE_COL_COUNT']) {
            Mdform::$mergeColCount = $tempRow['MERGE_COL_COUNT'];
            Mdform::$isUseMergeMatrix = true;
        }
        
        if (self::$isIndicatorRendering == false) {
            
            $indicators       = $this->model->getKpiIndicatorsByTemplateId($this->view->templateId);
            $facts            = $this->model->getKpiFactsByTemplateId($this->view->templateId);
            $cellControlDatas = $this->model->getKpiControlsByTemplateId($this->view->templateId);
            $savedData        = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
            
        } else {
                        
            $indicators       = $this->model->getKpiIndicatorsByIndicatorId($this->view->templateId);
            $facts            = $this->model->getKpiFactsByIndicatorId($this->view->templateId);
            $cellControlDatas = $this->model->getKpiControlsByIndicatorId($this->view->templateId);
            $savedData        = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
        }
        
        Mdform::$kpiTempCriteria = $this->model->getKpiCriteriaByTemplateId($this->view->templateId);
        
        $kpiTempId = Mdform::$kpiExpressionTempId ? Mdform::$kpiExpressionTempId : Mdform::$kpiTemplateId;        
        $this->view->kpiCountColumnFreeze = $this->model->getKpiDtlColCountFreezeModel($kpiTempId);
        $this->view->kpiCountColumnFreeze = $this->view->kpiCountColumnFreeze ? $this->view->kpiCountColumnFreeze : 1;
        
        $this->view->scripts = self::kpiFullExpression($this->view->templateId, $processArr);
        
        if (Mdform::$isWizard) {
            Mdform::$kpiFullExpressions .= $this->view->scripts;
            $this->view->scripts = '';
        }
        
        if ($processArr['viewMode'] == 'horizontalform') {
            
            $this->view->gridHead = '';
            
            $objectArray = $this->model->getRenderKpiTemplateHorizontalForm($indicators, $facts, $cellControlDatas, $savedData, null, 0, null, $processHeaderParam);
            
            $this->view->gridBody = $objectArray['rows'];
            $this->view->objectTabs = $objectArray['objectTabs'];
            $this->view->graphTabs = $objectArray['graphTabs'];
            
            $this->view->relationList = $this->model->getEaRelationListByTmpIdBookId($this->view->templateId, $processHeaderParam['bookId']);
            
            if (Mdform::$isSavedKpiForm) {
                $this->view->groupedObjectList = $this->model->getGroupedObjectListModel($processHeaderParam['bookId']);
                $this->view->groupedObject = $this->view->renderPrint('kpi/groupedObjectList', self::$viewPath);
            }
            
            return $this->view->renderPrint('kpi/material', self::$viewPath);
            
        } else {
            
            Mdform::$isIndicatorMerge = false;
            
            if ($tempRow['RENDER_TYPE'] == 'grid_indicator_merge') {
                Mdform::$isIndicatorMerge = true;
            }
                        
            $getDtlCol = $this->model->getKpiDtlColModel($kpiTempId);
            
            $this->view->gridHead = $this->model->getRenderKpiTemplateGridHead($facts, $indicators, $getDtlCol);
            $this->view->gridBody = $this->model->getRenderKpiTemplateGridBody($indicators, $facts, $cellControlDatas, $savedData, null, 0, null, $processHeaderParam, $getDtlCol);

            if ($this->view->renderType == 'form_left_label') {
                
                $this->view->gridHead = str_replace('<tr class="kpi-grid-header-row">', '<tr class="kpi-grid-header-row" style="visibility: collapse;">', $this->view->gridHead);
                $this->view->gridHead = str_replace('<th class="rowNumber"', '<th class="rowNumber d-none"', $this->view->gridHead);
                $this->view->gridBody = str_replace('kpi-grid-rownum-cell', 'kpi-grid-rownum-cell d-none', $this->view->gridBody);
                $this->view->gridBody = str_replace('text-left middle', 'text-right middle kpi-cell-grey-bg', $this->view->gridBody);
                
                $this->view->renderType = 'grid';
            }
            
            return $this->view->renderPrint('kpi/grid', self::$viewPath);
        }
    }
    
    public function renderKpiTemplateGridByPrint($tempRow, $processArr, $isMulti, $processHeaderParam) {
        
        $this->load->model('mdform', 'middleware/models/');
        
        Mdform::$kpiIndicatorWidth = $tempRow['INDICATOR_COL_WIDTH'];
        
        $this->view->templateId = $tempRow['ID'];
        $this->view->templateName = '';
        $this->view->templateCode = '';
        
        if ($isMulti) {
            $this->view->templateName = html_tag('div', array('style' => 'margin-bottom: 5px'), $tempRow['NAME']);
        }
        
        $indicators = $this->model->getKpiIndicatorsByTemplateId($this->view->templateId);
        $facts = $this->model->getKpiFactsByTemplateId($this->view->templateId);
        $cellControlDatas = $this->model->getKpiControlsByTemplateId($this->view->templateId);
        $savedData = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
        
        $this->view->gridHead = $this->model->getRenderKpiTemplateGridHead($facts, $indicators);
        $this->view->gridBody = $this->model->getRenderKpiTemplateGridBodyByPrint($indicators, $facts, $cellControlDatas, $savedData);
        
        return $this->view->renderPrint('kpi/gridPrint', self::$viewPath);
    }
    
    public function renderKpiTemplateForm($tempRow, $processArr, $isMulti, $processHeaderParam) {
        
        $this->load->model('mdform', 'middleware/models/');
        
        Mdform::$kpiTypeCode = $tempRow['TYPE_CODE'];
        Mdform::$kpiTemplateId = $tempRow['ID'];
        Mdform::$pfTranslationValue = array();
        Mdform::$pfTranslationValueTextarea = null;
        
        $this->view->templateId   = $tempRow['ID'];
        $this->view->templateName = '';
        $this->view->templateCode = '';
        $this->view->renderType   = $tempRow['RENDER_TYPE'];
        
        $this->view->viewMode     = $processArr['viewMode'];
        
        if ($isMulti) {
            $this->view->templateName = html_tag('div', array('class' => 'kpi-template-name'), '<i class="fa fa-check-square-o"></i> '.$tempRow['NAME']);
        }
        
        if (Mdform::$kpiTypeCode == 2) {
            Mdform::$getKpiCommandCode = 'selectQry';
        }
        
        if (self::$isIndicatorRendering == false) {
            
            $indicators = $this->model->getKpiIndicatorsByTemplateId($this->view->templateId);
            $facts = $this->model->getKpiFactsByTemplateId($this->view->templateId);
            $cellControlDatas = $this->model->getKpiControlsByTemplateId($this->view->templateId);
            $savedData = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
            
        } else {
            
            $indicators       = $this->model->getKpiIndicatorsByIndicatorId($this->view->templateId);
            $facts            = $this->model->getKpiFactsByIndicatorId($this->view->templateId);
            $cellControlDatas = $this->model->getKpiControlsByIndicatorId($this->view->templateId);
            $savedData        = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
        }
        
        $this->view->formBody = $this->model->getRenderKpiTemplateFormBody($indicators, $facts, $cellControlDatas, $savedData);
        
        $this->view->scripts = self::kpiFullExpression($this->view->templateId, $processArr);
        
        if (Mdform::$isWizard) {
            Mdform::$kpiFullExpressions .= $this->view->scripts;
            $this->view->scripts = '';
        }
        
        return $this->view->renderPrint('kpi/form', self::$viewPath);
    }
    
    public function renderKpiTemplateFormByPrint($tempRow, $processArr, $isMulti, $processHeaderParam) {
        
        $this->view->templateName = '';
        $this->view->formBody = '';
        
        return $this->view->renderPrint('kpi/formPrint', self::$viewPath);
    }
    
    public function kpiFullExpression($templateId, $processArr) {
        
        $cache = phpFastCache();
        
        $expTemplateId = $templateId;
        
        if (Mdform::$kpiExpressionTempId) {
            $expTemplateId = Mdform::$kpiExpressionTempId;
        }
        
        $this->view->templateId = $templateId;
        $this->view->processId = $processArr['processId'];
        $this->view->viewMode = $processArr['viewMode'];
        
        $expCacheId = $expTemplateId . '_' . $this->view->processId;

        $this->view->kpiFullExpressionEvent = $cache->get('kpiFullExpressionEvent_' . $expCacheId);
        $this->view->kpiFullExpressionVarFnc = $cache->get('kpiFullExpressionVarFnc_' . $expCacheId);
        $this->view->kpiFullExpressionBeforeSave = $cache->get('kpiFullExpressionBeforeSave_' . $expCacheId);
        
        if (Mdexpression::$setSubMainSelector) {
            Mdexpression::$setMainSelector = '$kpiTmp_'.$expTemplateId;
        } else {
            Mdexpression::$setMainSelector = 'bp_window_'.$expTemplateId;
        }
        
        $mdf = &getInstance();
        
        if ($this->view->kpiFullExpressionVarFnc == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($expTemplateId, 'VAR_FNC_EXPRESSION_STRING');
            
            $this->view->kpiFullExpressionVarFnc = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $processArr['processId'], '', true);
            $cache->set('kpiFullExpressionVarFnc_' . $expCacheId, $this->view->kpiFullExpressionVarFnc, Mdwebservice::$expressionCacheTime);
        }
        
        if ($this->view->kpiFullExpressionEvent == null) {

            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($expTemplateId, 'EVENT_EXPRESSION_STRING');
            
            $this->view->kpiFullExpressionEvent = (new Mdexpression())->fullExpressionConvertEvent($rowExp, $processArr['processId']);
            $cache->set('kpiFullExpressionEvent_' . $expCacheId, $this->view->kpiFullExpressionEvent, Mdwebservice::$expressionCacheTime);
        }
        
        if ($this->view->kpiFullExpressionBeforeSave == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($expTemplateId, 'SAVE_EXPRESSION_STRING');
            
            $this->view->kpiFullExpressionBeforeSave = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $processArr['processId']);
            $cache->set('kpiFullExpressionBeforeSave_' . $expCacheId, $this->view->kpiFullExpressionBeforeSave, Mdwebservice::$expressionCacheTime);
        }
        
        $script = $this->view->renderPrint('kpi/script', self::$viewPath);
        
        if (Mdform::$isSubKpiForm) {
            $script = str_replace('_'.$processArr['processId'], '_'.$processArr['uniqId'], $script);
        }
        
        return $script;
    }
    
    public function renderKpiTemplateGridByDv($tempRow, $processArr, $isMulti, $processHeaderParam) {
        
        $this->load->model('mdform', 'middleware/models/');
        
        Mdform::$kpiTypeCode   = $tempRow['TYPE_CODE'];
        Mdform::$kpiTemplateId = $tempRow['ID'];
        
        $this->view->templateId   = $tempRow['ID'];
        $this->view->templateCode = $tempRow['CODE'];
        $this->view->templateWidth = $tempRow['WIDTH'];
        $this->view->templateHeight = $tempRow['HEIGHT'];
        $this->view->templateName = '';
        $this->view->renderType = $tempRow['RENDER_TYPE'];
        
        $this->view->viewMode = $processArr['viewMode'];
        
        if ($isMulti) {
            $this->view->templateName = html_tag('div', array('class' => 'kpi-template-name'), '<i class="fa fa-check-square-o"></i> '.$tempRow['NAME']);
        }
        
        if (Mdform::$kpiTypeCode == 2) {
            Mdform::$getKpiCommandCode = 'selectQry';
        }
        
        if (isset($processHeaderParam['bookId']) && $processHeaderParam['bookId']) {
            $processHeaderParam['kpiDmMartTemplateId'] = $this->view->templateId;
        }
        
        $indicators       = $this->model->getKpiIndicatorsByTemplateId($this->view->templateId);
        $facts            = $this->model->getKpiFactsByTemplateId($this->view->templateId);
        $cellControlDatas = $this->model->getKpiControlsByTemplateId($this->view->templateId);
        $savedData        = $this->model->getKpiControlsSavedDataByBookId($processHeaderParam);
        
        $dvRecords        = $this->model->getKpiHeadDvRecords($tempRow);
        
        $this->view->gridHead = $this->model->getRenderKpiTemplateGridHeadByDv($facts, $dvRecords);
        $this->view->gridBody = $this->model->getRenderKpiTemplateGridBodyByDv($indicators, $facts, $dvRecords, $cellControlDatas, $savedData);
        
        $this->view->scripts  = self::kpiFullExpression($this->view->templateId, $processArr);
        
        return $this->view->renderPrint('kpi/grid', self::$viewPath);
    }
    
    public function eaRenderControl($row, $value, $uniqid) {
        
        $showType = $row['showtype'];
        $control  = null;
        
        switch ($showType) {
        
            case 'radio':
                
                $control = '<div class="pull-left"><strong>'.$row['labelname'].':</strong></div>';
                $control .= '<div class="pull-left">';
                if ($row['values']) {
                    foreach ($row['values'] as $subRow) {
                        $control .= '<label><input '.($subRow['id'] == '1' ? 'checked' : '').' type="radio" name="'.$row['paramname'].'_value" value="'.$subRow['id'].'"> '.$subRow['name'].'</label>';
                    }
                    $control .= '<input type="hidden" value="'.$row['criteria'].'" name="'.$row['paramname'].'_criteria">';
                    $control .= '<input type="hidden" value="'.$row['relatedtemplateid'].'" name="'.$row['paramname'].'_relatedtemplateid">';
                    $control .= '<input type="hidden" value="'.$row['paramname'].'" name="eapath[]">';                    
                }
                $control .= '</div>';
                
            break;
            
            case 'checkbox':
                
                $control = '<div class="pull-left"><strong>'.$row['labelname'].':</strong></div>';
                $control .= '<div class="pull-left">';
                $control .= '<label><input type="checkbox" value="1" onclick="footPrintCardFilter'.$uniqid.'(\'\', \''.$row['paramname'].'\', this, \'checkbox\');"></label>';
                $control .= '<input type="hidden" value="'.$row['criteria'].'" name="'.$row['paramname'].'_criteria">';
                $control .= '<input type="hidden" value="'.$row['relatedtemplateid'].'" name="'.$row['paramname'].'_relatedtemplateid">';
                $control .= '<input type="hidden" value="" name="'.$row['paramname'].'_value">';
                $control .= '<input type="hidden" value="'.$row['paramname'].'" name="eapath[]">';                    
                $control .= '</div>';
                
            break;
        
            case 'showhide':
                
                $control = '<button class="btn red-sunglo btn-sm" onclick="dynamicKeyCard'.$uniqid.'(\''.$row['id'].'\'); return false;">' . $row['labelname'] . '</button>';
                
                if ($row['values']) {
                    $colorArr = array('hsla(350, 50%, 45%, 1)', 'hsla(350, 50%, 65%, 1)', 'hsla(220, 70%, 40%, 1)', 'hsla(200, 80%, 40%, 1)','#ad0000', '#6800de', '#ce00d6', '#3260AE','#A02C64', '#00adab', '#F06C00', '#4C5A6C', 'hsla(130, 50%, 30%, 1)');
                    $control .= '<div class="dynamicKey" id="dynamicKeyCard'.$row['id'].'" style="display: none;">';
                    $control .= '<div class="threeColModel_dynamicKeyTitle small" style="width:170px;"><div style="padding-top: 1.5px;"><strong><span class="uppercase">'.$row['labelname'].':</span></strong></div></div>';
                    
                    foreach ($row['values'] as $subRow) {
                        $rand_color = array_rand($colorArr);
                        $rand_color = $colorArr[$rand_color];
                        $control .= '<div class="threeColModel_dynamicKeyContainer mt5">'.
                                   '<div class="threeColModel_dynamicKeyObject fontBlack" style="background-color: '.$rand_color.'">'
                                    . '<div style="padding-top: 17.5px;">'
                                        . '<a onclick="footPrintCardFilter'.$uniqid.'(\''.$subRow['id'].'\', \''.$row['paramname'].'\', this, \'card\');" class="text-white context-menu-busObjGenMenu" href="javascript:;">'
                                            .$subRow['name']
                                        . '</a>'
                                     . '</div>'
                                    .'</div>'.
                                '</div>';
                    }
                    $control .= '<input type="hidden" value="'.$row['criteria'].'" name="'.$row['paramname'].'_criteria">';
                    $control .= '<input type="hidden" value="" name="'.$row['paramname'].'_value">';
                    $control .= '<input type="hidden" value="'.$row['relatedtemplateid'].'" name="'.$row['paramname'].'_relatedtemplateid">';
                    $control .= '<input type="hidden" value="'.$row['paramname'].'" name="eapath[]">';
                    $control .= '</div>';
                }
                
            break;        
        
            default:
            break;    
        }
        
        return $control;
    }
    
    public function renderKpiTemplateFormProcurement($processHeaderParam = null) {
        
        $this->load->model('mdproc', 'middleware/models/');
        $getProcCustomerList = $this->model->getProcCustomerListModel(Input::post('rfId'));      
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->templateId   = Input::post('templateId');
        $tempRow = $this->model->getKpiTemplateRow($this->view->templateId);
        parse_str(Input::post('supplierKpiData'), $supplierKpiData);
        
        Mdform::$kpiTypeCode       = $tempRow['TYPE_CODE'];
        Mdform::$kpiTemplateId     = $tempRow['ID'];
        Mdform::$kpiIndicatorWidth = $tempRow['INDICATOR_COL_WIDTH'];
        Mdform::$isKpiTempCriteria = false;
        
        Mdform::$kpiExpressionTempId = $tempRow['EXPRESSION_TEMPLATE_ID'];
        
        $this->view->templateId   = $tempRow['ID'];
        $this->view->templateName = '';
        $this->view->templateCode = '';
        
        if (Mdform::$kpiTypeCode == 2) {
            Mdform::$getKpiCommandCode = 'selectQry';
        }
        
        $indicators       = $this->model->getKpiIndicatorsByTemplateId($this->view->templateId);
        $facts            = $this->model->getKpiFactsByTemplateId($this->view->templateId);
        $cellControlDatas = $this->model->getKpiControlsByTemplateId($this->view->templateId);
        $savedData        = $supplierKpiData['kpidmdtl'.Input::post('itemId')];
        
        Mdform::$kpiTempCriteria = $this->model->getKpiCriteriaByTemplateId($this->view->templateId);
        
        foreach ($getProcCustomerList as $key => $row) {
            $this->load->model('mdform', 'middleware/models/');
            
            $this->model->getRenderKpiTemplateGridBodyProc($indicators, $facts, $cellControlDatas, json_decode($savedData[$key], true), null, 0, null, $processHeaderParam, $row['supplierid'], $key);
        }
        
        jsonResponse(array('indicator' => Mdform::$resultIndicator, 'facts' => Mdform::$resultFacts, 'suppliers' => $getProcCustomerList, 'status' => 'success'));
    }

    public function kpiLinkedCombo() {
        jsonResponse($this->model->getComboKpiModel(Input::post('lookupMetaDataId'), Input::post('lookupCriteria')));
    }
    
    public function kpiDmMartTreeGraph() {
        $response = $this->model->kpiDmMartTreeGraphModel();
        jsonResponse($response);
    }
    
    public function kpiFormByDmRecordMap() {
        
        $templateId = $this->model->getKpiTemplateIdByDmRecordMapModel();
        
        if ($templateId) {
            
            $uniqId = Input::numeric('uniqId');
            
            unset($_POST);
            
            $_POST['uniqId'] = $uniqId;
            $_POST['methodId'] = $uniqId;
            $_POST['viewMode'] = '';
            $_POST['param']['templateId'] = $templateId;
            
            $this->showKpiForm();
            
        } else {
            jsonResponse(array('status' => 'error', 'message' => 'Invalid templateId!'));
        }
    }
    
    public function getKpiTemplatesByRefStrId() {
        
        $this->view->getTemplates = $this->model->getKpiTemplatesByRefStrIdModel();
        
        if (isset($this->view->getTemplates['data'])) {
            
            $this->view->uniqId = getUID();
            
            $response = array(
                'status' => 'success', 
                'html' => $this->view->renderPrint('kpi/dvCriteria', self::$viewPath)
            );
            
        } else {
            $response = $this->view->getTemplates;
        }
        
        jsonResponse($response);
    }
    
    public function kpiFormByTemplateId() {
        
        $uniqId = Input::numeric('uniqId');
        $templateId = Input::numeric('templateId');
            
        unset($_POST);

        $_POST['uniqId'] = $uniqId;
        $_POST['methodId'] = $uniqId;
        $_POST['viewMode'] = '';
        $_POST['param']['templateId'] = $templateId;

        $this->showKpiForm();
    }

    public function getKpiTemplatesByRefStrIdByInline($structureId) {
        
        $html = '';
        $this->load->model('mdform', 'middleware/models/');
        $_POST['refStructureId'] = $structureId;
        $this->view->getTemplates = $this->model->getKpiTemplatesByRefStrIdModel();
        
        if (isset($this->view->getTemplates['data'])) {
            
            foreach ($this->view->getTemplates['data'] as $row) {
                $uniqId = getUID();
                unset($_POST);

                $_POST['uniqId'] = $uniqId;
                $_POST['methodId'] = $uniqId;
                $_POST['viewMode'] = '';
                $_POST['param']['templateId'] = $row['id'];

                ob_start();
                $this->showKpiForm();
                $getResponse = ob_get_contents();
                ob_end_clean();                
        
                $getResponse = json_decode($getResponse, true);
                
                $html .= '<div class="dv-kpiform-criteria col-md-12">';
                $html .= '<div class="col-md-12 mb5 mt5">'.$row['name'].'</div>';
                $html .= $getResponse['html'];
                $html .= '</div>';
            }
            
            $html = str_replace('bp_window_', '$kpiTmp_', $html);
        }
        
        return $html;
    }    
    
    public function getDrillPanelTypeList() {
        
        $data = $this->model->getDrillPanelTypeListModel();
        $dvId = $data['result']['metadataid'];

        $_POST['filterMenuTreeIds'] = $data['result']['templatedtl'];
        $_POST['filterObjectDtl'] = $data['result']['objectdtl'];
        
        (new Mdobject)->dataview($dvId, 0, 'json'); 
        exit;
    }
    
    public function kpiExpAutoComplete() {
        
        $response = $this->model->getKpiTemplatesByFilterNameModel();
        
        echo json_encode($response); exit;
    }
    
    public function kpiIndicatorBpRunForm() {
        
        $this->view->fiscalPeriodDvId = Input::numeric('fiscalPeriodDvId');
        $this->view->render('kpi/kpiIndicatorBpRunForm', self::$viewPath);
    }
    
    public function kpiIndicatorBpRun() {
        $response = $this->model->kpiIndicatorBpRunModel();
        jsonResponse($response);
    }
    
    public function kpiIndicatorTemplateRender() {
        
        $postData = Input::postData();
        
        if (isset($postData['param']['indicatorId'])) {
            
            $this->load->model('mdform', 'middleware/models/');
            
            $paramData = $postData['param'];
            
            Mdform::$recordId = issetVar($paramData['id']);
            Mdform::$defaultTplSavedId = issetVar($paramData['dynamicRecordId']);
            Mdform::$inputId = issetVar($paramData['idField']);
            
            $this->view->indicatorId = $paramData['indicatorId'];
            $this->view->crudIndicatorId = issetParam($paramData['crudIndicatorId']);
            
            $structureIndicatorId = $this->view->indicatorId;
            $defaultTplSavedId = Mdform::$defaultTplSavedId;
            $isResponseArray = Input::numeric('isResponseArray');
            
            $checkMethodAccess = $this->model->checkMethodAccessModel(issetVar($paramData['mainIndicatorId']), $this->view->crudIndicatorId);
            
            if ($checkMethodAccess['status'] != 'success') {
                if ($isResponseArray == 1) {
                    return $checkMethodAccess;
                } else {
                    echo json_encode($checkMethodAccess, JSON_UNESCAPED_UNICODE); exit;
                }
            }
            
            if ($this->view->crudIndicatorId) {
                
                $isMethodWithParam = $this->model->getKpiIndicatorMapChildCountModel($this->view->crudIndicatorId);
                
                if ($isMethodWithParam) {
                    $structureIndicatorRow = $this->model->getKpiIndicatorRowModel($structureIndicatorId);
                    $this->view->indicatorId = $this->view->crudIndicatorId;
                }
            }
            
            $data = $this->model->getKpiIndicatorTemplateModel($this->view->indicatorId);
            
            if ($data) {
                
                $dataFirstRow = $data[0];
                 
                $this->view->structureIndicatorId = issetParam($paramData['structureIndicatorId']); 
                $this->view->uxFlowActionIndicatorId = issetParam($paramData['uxFlowActionIndicatorId']); 
                $this->view->uxFlowIndicatorId = issetParam($paramData['uxFlowIndicatorId']); 
                $this->view->isKpiIndicatorRender = issetParam($paramData['isKpiIndicatorRender']); 
                $this->view->actionType = issetParam($paramData['actionType']); 
                $this->view->recordMapRender = '';
                $this->view->uniqId = getUID();   
                
                if (Mdform::$defaultTplSavedId) {
                    $this->view->addonTabs = self::renderEditModeIndicatorTab($this->view->uniqId, $dataFirstRow, Mdform::$defaultTplSavedId); 
                } else {
                    $this->view->addonTabs = self::renderAddModeIndicatorTab($this->view->uniqId, $dataFirstRow); 
                }
                
                if (isset($structureIndicatorRow)) {
                    
                    $this->view->dataTableName = $structureIndicatorRow['TABLE_NAME'];
                    $this->view->kpiTypeId = $structureIndicatorRow['KPI_TYPE_ID'];
                    $this->view->namePattern = $structureIndicatorRow['NAME_PATTERN'];
                    
                } else {
                    $this->view->dataTableName = $dataFirstRow['TABLE_NAME'];
                    $this->view->kpiTypeId = $dataFirstRow['KPI_TYPE_ID'];
                    $this->view->namePattern = $dataFirstRow['NAME_PATTERN'];
                }

                $this->view->isUseComponent = $dataFirstRow['IS_USE_COMPONENT'];
                
                self::$subUniqId = $this->view->uniqId;
                
                if ($dataFirstRow['LABEL_WIDTH']) {
                    self::$labelWidth = $dataFirstRow['LABEL_WIDTH'];
                }
                
                self::$topTabRender = array();
                self::$tabRender = array();
                
                $this->view->form = $this->model->renderKpiIndicatorTemplateModel($structureIndicatorId, $this->view->dataTableName, $data);   
                $this->view->standardHiddenFields = $this->model->standardHiddenFieldsModel();
                $firstTplId = Mdform::$firstTplId;
                
                $this->view->scripts = self::fncIndicatorColExpression($this->view->uniqId, $this->view->indicatorId, Mdform::$indicatorColExpression);
                $this->view->scripts .= self::fncIndicatorCellExpression($this->view->uniqId, $this->view->indicatorId, Mdform::$indicatorCellExpression);
                $this->view->scripts .= self::fncIndicatorHdrExpression($this->view->uniqId, $this->view->indicatorId, Mdform::$indicatorHdrExpression);
                
                $this->view->fullExp = self::indicatorFullExpression($this->view->uniqId, $this->view->indicatorId, $this->view->kpiTypeId);
                $this->view->flowchartscripts = '';
                
                $this->view->mainTabName = 'Үндсэн';
                $this->view->componentRenderType = 'tab';
                
                if (Mdform::$methodStructureIndicatorId) {
                    
                    $structureIndicatorRow = $this->model->getKpiIndicatorRowModel(Mdform::$methodStructureIndicatorId);
                    
                    $this->view->dataTableName = $structureIndicatorRow['TABLE_NAME'];
                    $this->view->kpiTypeId = $structureIndicatorRow['KPI_TYPE_ID'];
                    $this->view->namePattern = $structureIndicatorRow['NAME_PATTERN'];
                    
                    $structureIndicatorId = Mdform::$methodStructureIndicatorId;
                }
                
                if ($this->view->isUseComponent) {
                    
                    $components = $this->model->getKpiIndicatorMapModel($this->view->indicatorId, 10000010);                    
                    
                    if (Mdform::$defaultTplSavedId) {
                        $this->view->savedComponentRows = $this->model->getSavedRecordMapKpiComponentsModel($this->view->indicatorId, Mdform::$defaultTplSavedId, $components);
                    }
                    
                    $componentTabs = Arr::groupByArray($components, 'DESCRIPTION');
                    
                    foreach ($componentTabs as $componentTabName => $componentTab) {
                        
                        $this->view->componentUniqId = getUID();
                        $this->view->components = $componentTab['rows'];
                        
                        Mdform::$topTabRenderShow[$this->lang->line($componentTabName)] = array($this->view->renderPrint('kpi/indicator/recordmap/recordmap', self::$viewPath));
                    }
                    
                    $this->view->recordMapRender = '';
                    $this->view->componentRenderType = '';
                }
                
                if ($this->view->kpiTypeId == '2013') {
                    $this->view->additionalInfo = $this->model->getIndicatorAdditionalInfoModel($this->view->kpiTypeId, $this->view->indicatorId);
                    
                    if (isset($this->view->additionalInfo['STRUCTURE_LIMIT']) && isset($this->view->additionalInfo['STRUCTURE_TAB_NAME'])) {
                        
                        $structureMap = $this->model->getIndicatorSemanticMapCountModel($this->view->indicatorId, 10000017);
                        
                        if ($structureMap) {
                            
                            $this->view->structureTabContent = self::addonStructureForm($this->view->indicatorId, $structureMap);
                            $this->view->structureTab = array(
                                'tabName'    => $this->view->additionalInfo['STRUCTURE_TAB_NAME'], 
                                'tabContent' => $this->view->renderPrint('kpi/indicator/recordmap/addonStructure', self::$viewPath)
                            );
                            
                            if ($mainTabName = issetParam($this->view->additionalInfo['DEFAULT_TAB_NAME'])) {
                                $this->view->mainTabName = $mainTabName;
                            }
                        }
                    }
                }
                
                if ($this->view->kpiTypeId == '2009') {
                    $this->view->isKpiIndicatorRender = '1';
                }
                
                Mdform::$defaultTplSavedId = $defaultTplSavedId;
                Mdform::$firstTplId = $firstTplId;
                
                $this->view->title = $this->lang->line($dataFirstRow['NAME']);
                $this->view->indicatorId = $structureIndicatorId;
                $this->view->form = $this->view->renderPrint('kpi/indicator/form', self::$viewPath);
                Mdform::$addonPathPrefix = null;
                
                if ($dataFirstRow['RENDER_THEME'] != '') {
                    $this->view->bgImage = $dataFirstRow['PROFILE_PICTURE'];
                    $this->view->form = $this->view->renderPrint('kpi/indicator/theme/' . $dataFirstRow['RENDER_THEME'], self::$viewPath);
                }
                
                if (Mdform::$headerInlineFields) {
                    foreach (Mdform::$headerInlineFields as $inlineFields) {
                        $replaceControl = '<div class="mv-inline-field">'.$inlineFields['control'] . $inlineFields['label'].'</div>';
                        $this->view->form = str_replace('<!--rows_'.$inlineFields['rowsPath'].'-->', $replaceControl.'<!--rows_'.$inlineFields['rowsPath'].'-->', $this->view->form);
                    }
                }
                        
                $response = array(
                    'status' => 'success', 
                    'html' => $this->view->form, 
                    'name' => $this->view->title, 
                    'windowType' => $dataFirstRow['WINDOW_TYPE'], 
                    'windowSize' => $dataFirstRow['WINDOW_SIZE'], 
                    'windowWidth' => $dataFirstRow['WINDOW_WIDTH'], 
                    'windowHeight' => $dataFirstRow['WINDOW_HEIGHT'], 
                    'uniqId' => $this->view->uniqId 
                );
                
            } else {
                $response = array('status' => 'info', 'message' => 'Тохиргоо олдсонгүй!');
            }
            
        } else {
            $response = array('status' => 'info', 'message' => 'Загвар сонгоно уу.');
        }
        
        if ($isResponseArray == 1) {
            return $response;
        } else {
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function indicatorFullExpression($uniqId, $indicatorId, $kpiTypeId) {
        
        $cache = phpFastCache();
        
        $expCacheId = $indicatorId;

        $kpiFullExpressionEvent = $cache->get('kpiFullExpressionEvent_' . $expCacheId);
        $kpiFullExpressionWithoutEvent = $cache->get('kpiFullExpressionWithoutEvent_' . $expCacheId);
        $kpiFullExpressionVarFnc = $cache->get('kpiFullExpressionVarFnc_' . $expCacheId);
        $kpiFullExpressionBeforeSave = $cache->get('kpiFullExpressionBeforeSave_' . $expCacheId);
        $kpiFullExpressionAfterSave = $cache->get('kpiFullExpressionAfterSave_' . $expCacheId);
        
        Mdform::$isIndicatorRendering = true;
        Mdexpression::$setMainSelector = 'bp_window_'.$indicatorId;
        
        $mdf = &getInstance();
        
        if ($kpiFullExpressionWithoutEvent == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($indicatorId, 'LOAD_EXPRESSION_STRING');
            
            if ($rowExp) {
                $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
                Mdexpression::$isMultiPathConfig = true;
                Mdexpression::$multiPathConfig = $pathResult;
            }
            
            $kpiFullExpressionWithoutEvent = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $indicatorId, '', true);
            $cache->set('kpiFullExpressionWithoutEvent_' . $expCacheId, $kpiFullExpressionWithoutEvent, Mdwebservice::$expressionCacheTime);
        }
        
        $kpiFullExpressionWithoutEvent = str_replace('_'.$indicatorId, '_'.$uniqId, $kpiFullExpressionWithoutEvent);
        
        if ($kpiTypeId == '2009') { 
            return array('varFnc' => '', 'event' => '', 'beforeSave' => '', 'withoutEvent' => $kpiFullExpressionWithoutEvent);
        }
        
        if ($kpiFullExpressionVarFnc == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($indicatorId, 'VAR_FNC_EXPRESSION_STRING');
            
            if ($rowExp) {
                $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
                Mdexpression::$isMultiPathConfig = true;
                Mdexpression::$multiPathConfig = $pathResult;
            }
            
            $kpiFullExpressionVarFnc = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $indicatorId, '', true);
            $cache->set('kpiFullExpressionVarFnc_' . $expCacheId, $kpiFullExpressionVarFnc, Mdwebservice::$expressionCacheTime);
        }
        
        if ($kpiFullExpressionEvent == null) {

            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($indicatorId, 'EVENT_EXPRESSION_STRING');
            //$rowExp .= self::flowChartExpression($indicatorId);
            
            if ($rowExp) {
                $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
                Mdexpression::$isMultiPathConfig = true;
                Mdexpression::$multiPathConfig = $pathResult;
            }
            
            $kpiFullExpressionEvent = (new Mdexpression())->fullExpressionConvertEvent($rowExp, $indicatorId);
            $kpiFullExpressionEvent = str_replace('♥♥♥', '};', $kpiFullExpressionEvent);
            $cache->set('kpiFullExpressionEvent_' . $expCacheId, $kpiFullExpressionEvent, Mdwebservice::$expressionCacheTime);
        }
        
        if ($kpiFullExpressionBeforeSave == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($indicatorId, 'SAVE_EXPRESSION_STRING');
            
            if ($rowExp) {
                $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
                Mdexpression::$isMultiPathConfig = true;
                Mdexpression::$multiPathConfig = $pathResult;
            }
            
            $kpiFullExpressionBeforeSave = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $indicatorId);
            $cache->set('kpiFullExpressionBeforeSave_' . $expCacheId, $kpiFullExpressionBeforeSave, Mdwebservice::$expressionCacheTime);
        }
        
        if ($kpiFullExpressionAfterSave == null) {
            
            $mdf->load->model('mdform', 'middleware/models/');
            $rowExp = $mdf->model->getKpiTemplateExpressionModel($indicatorId, 'AFTER_SAVE_EXPRESSION_STRING');
            
            if ($rowExp) {
                $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
                Mdexpression::$isMultiPathConfig = true;
                Mdexpression::$multiPathConfig = $pathResult;
            }
            
            $kpiFullExpressionAfterSave = (new Mdexpression())->fullExpressionConvertWithoutEvent($rowExp, $indicatorId);
            $cache->set('kpiFullExpressionAfterSave_' . $expCacheId, $kpiFullExpressionAfterSave, Mdwebservice::$expressionCacheTime);
        }
        
        $kpiFullExpressionVarFnc = str_replace('_'.$indicatorId, '_'.$uniqId, $kpiFullExpressionVarFnc);
        $kpiFullExpressionEvent = str_replace('_'.$indicatorId, '_'.$uniqId, $kpiFullExpressionEvent);
        $kpiFullExpressionBeforeSave = str_replace('_'.$indicatorId, '_'.$uniqId, $kpiFullExpressionBeforeSave);
        $kpiFullExpressionAfterSave = str_replace('_'.$indicatorId, '_'.$uniqId, $kpiFullExpressionAfterSave);
        
        return array(
            'varFnc' => $kpiFullExpressionVarFnc, 
            'withoutEvent' => $kpiFullExpressionWithoutEvent, 
            'event' => $kpiFullExpressionEvent, 
            'beforeSave' => $kpiFullExpressionBeforeSave, 
            'afterSave' => $kpiFullExpressionAfterSave
        );
    }
    
    public function indicatorFlowchartExpression($uniqId, $indicatorId) {
        
        $kpiFlowchartExpressionEvent = (new Mdexpression())->microFlowClientExpression($indicatorId);
        
        Mdform::$isIndicatorRendering = true;
        Mdexpression::$setMainSelector = 'bp_window_'.$indicatorId;
        
        $mdf = &getInstance();
        $mdf->load->model('mdform', 'middleware/models/');

        $rowExp = $kpiFlowchartExpressionEvent;
        
        if ($rowExp) {
            $pathResult = $mdf->model->kpiSetMultiPathConfigModel($indicatorId);
            Mdexpression::$isMultiPathConfig = true;
            Mdexpression::$multiPathConfig = $pathResult;
        }
        
        $kpiFullExpressionEvent = (new Mdexpression())->fullExpressionConvertEvent($rowExp, $indicatorId);
        
        return $kpiFullExpressionEvent;
    }
    
    public function fncIndicatorHdrExpression($uniqId, $indicatorId, $expressionArr) {
        
        if (!$expressionArr) {
            return null;
        }
        
        Mdexpression::$setMainSelector = '$kpiTmp_'.$uniqId;
        
        $script = (new Mdexpression())->convertIndicatorHdrExpression($uniqId, $indicatorId, $expressionArr);
        
        return $script;
    }
    
    public function fncIndicatorColExpression($uniqId, $indicatorId, $expressionArr) {
        
        if (!$expressionArr) {
            return null;
        }
        
        Mdexpression::$setMainSelector = '$kpiTmp_'.$uniqId;
        
        $script = (new Mdexpression())->convertIndicatorColExpression($uniqId, $indicatorId, $expressionArr);
        
        return $script;
    }
    
    public function fncIndicatorCellExpression($uniqId, $indicatorId, $expressionArr) {
        
        if (!$expressionArr) {
            return null;
        }
        
        /*$cache = phpFastCache();

        $script = $cache->get('kpiFullExpressionEvent_' . $indicatorId);
        
        Mdexpression::$setMainSelector = 'bp_window_'.$indicatorId;
        
        if ($script == null) {
            
            $script = (new Mdexpression())->convertIndicatorCellExpression($uniqId, $indicatorId, $expressionArr);
            $cache->set('kpiFullExpressionVarFnc_' . $indicatorId, $script, Mdwebservice::$expressionCacheTime);
        }*/
        
        Mdexpression::$setMainSelector = '$kpiTmp_'.$uniqId;
        
        $script = (new Mdexpression())->convertIndicatorCellExpression($uniqId, $indicatorId, $expressionArr);
        
        return $script;
    }
    
    public function kpiIndicatorTemplateConfig() {
        
        $selectedRow = Input::post('selectedRow');
        
        $mainIndicatorId = issetParam($selectedRow['parentid']);
        $childIndicatorId = issetParam($selectedRow['id']);
        
        if ($mainIndicatorId && $childIndicatorId) {
            
            self::kpiIndicatorTemplateConfigRender($mainIndicatorId, $childIndicatorId);
            
            $form = $this->view->renderPrint('kpi/indicator/config', self::$viewPath);
            
            $response = array('status' => 'success', 'html' => $form);
            
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid parameters!');
        }
        
        jsonResponse($response);
    }
    
    public function kpiIndicatorTemplateConfigRender($mainIndicatorId, $childIndicatorId) {
        
        $data = $this->model->getKpiIndicatorTemplateModel($mainIndicatorId, $childIndicatorId);
        $indMapRow = $this->model->getKpiIndicatorIndicatorMapModel($childIndicatorId);

        $arrRow = $data[0];
        $arrRow['isTemplateConfig'] = true;

        $arrRow['ID'] = $childIndicatorId;
        $arrRow['TEMPLATE_TABLE_NAME'] = issetParam($indMapRow['TEMPLATE_TABLE_NAME']);

        $this->view->uniqId = getUID();
        $this->view->templateTableName = $arrRow['TEMPLATE_TABLE_NAME'];
        $this->view->kpiMainIndicatorId = $mainIndicatorId;
        $this->view->kpiIndicatorIndicatorMapId = $childIndicatorId;

        $this->view->form = $this->model->rowsKpiIndicatorTemplate($childIndicatorId, $data, $arrRow['ID'], $arrRow);
    }
    
    public function saveKpiDynamicData($sourceRecordId = null) {
        $this->load->model('mdform', 'middleware/models/');
        $response = $this->model->saveKpiDynamicDataModel($sourceRecordId);
        
        if ($response['status'] != 'success') {
            Mdwebservice::deleteUploadedFiles(FileUpload::$uploadedFiles);
        }
        
        return $response;
    }
    
    public function saveKpiDynamicDataByList() {
        $response = self::saveKpiDynamicData();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function removeKpiDynamicData() {
        $response = $this->model->removeKpiDynamicDataModel();
        
        if ($response['status'] == 'success' && $indicatorId = $response['indicatorId']) {
            self::clearCacheData($indicatorId);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function removeAddonStructureForm() {
        $response = $this->model->removeAddonStructureFormModel();
        jsonResponse($response);
    }
    
    public function indicatorList($indicatorId = '', $isReturnArray = false) {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if ($relatedindicatorid = issetVar($workSpaceParamArray['workSpaceParam']['relatedindicatorid'])) { 
                    $this->view->indicatorId = $relatedindicatorid;
                } elseif (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->isAjax = is_ajax_request();
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->indicatorCode = $this->view->row['CODE'];
        
        if ($this->view->row['KPI_TYPE_ID'] == '2007') { /*map*/
            
            if (isset($isReturnArray) && $isReturnArray) {
                $content = $this->indicatorMapRender(true);
                if (Input::numeric('isJson')) {
                    echo json_encode(array('title' => $this->view->title, 'html' => $content['html']), JSON_UNESCAPED_UNICODE);
                } else {
                    return $content;
                }
            } else {
                $this->indicatorMapRender();
            }
        
            exit;
        }
        
        $this->view->columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row);
        $fieldConfig = $this->model->getKpiIndicatorIdFieldModel($this->view->indicatorId, $this->view->columnsData);
        
        $this->view->idField = $fieldConfig['idField'];
        $this->view->codeField = $fieldConfig['codeField'];
        $this->view->nameField = $fieldConfig['nameField'];
        $this->view->parentField = $fieldConfig['parentField'];
        $this->view->coordinateField = $fieldConfig['coordinateField'];
        
        $this->view->isGridType = 'datagrid';
        $this->view->isTreeGridData = '';
        $this->view->isUseWorkflow = $this->view->row['IS_USE_WORKFLOW'];
        $this->view->isFilterShowData = $this->view->row['IS_FILTER_SHOW_DATA'];
        $this->view->isPrint = $this->view->row['COUNT_REPORT_TEMPLATE'] ? true : false;
        
        $this->view->isDataMart = $this->view->row['KPI_TYPE_ID'] == '1040' ? true : false; 
        $this->view->isCallWebService = ($this->view->row['KPI_TYPE_ID'] == '1080' || $this->view->row['KPI_TYPE_ID'] == '1160' || $this->view->row['KPI_TYPE_ID'] == '1161');
        $this->view->isRawDataMart = $this->view->row['KPI_TYPE_ID'] == '1044' ? true : false;
        $this->view->isCheckQuery = $this->view->row['KPI_TYPE_ID'] == '1200' ? true : false;
        $this->view->drillDownCriteria = Input::post('drillDownCriteria');
        $this->view->postHiddenParams = '';
        $this->view->filter = '';
        
        if ($this->view->idField && $this->view->nameField && $this->view->parentField) {
            $this->view->isGridType = 'treegrid';
            $this->view->isTreeGridData = 'id='.$this->view->idField.'&name='.$this->view->nameField.'&parent='.$this->view->parentField;
        }
        
        $this->view->process = $this->model->getKpiIndicatorProcessModel($this->view->indicatorId);
        
        if ($this->view->row['KPI_TYPE_ID'] == '2016') {
            $this->view->renderGrid = self::renderCustomView($this->view->row['KPI_TYPE_ID']);
        } else {
            $this->model->mvGridStylerModel($this->view->indicatorId);
            
            if ($filter = Input::get('filter')) {
                $this->view->filter = $this->model->validateFilters($this->view->indicatorId, $filter);
            }
        
            $this->view->columns = $this->model->renderKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row['isCheckSystemTable'], $this->view->columnsData);
            $this->view->renderGrid = $this->view->renderPrint('kpi/indicator/renderGrid', self::$viewPath);
        }
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        if (isset($isReturnArray) && $isReturnArray) {
            if (Input::numeric('isJson')) {
                echo json_encode(array('title' => $this->view->title, 'html' => $this->view->renderPrint('kpi/indicator/list', self::$viewPath)), JSON_UNESCAPED_UNICODE);
            } else {
                return array('title' => $this->view->title, 'html' => $this->view->renderPrint('kpi/indicator/list', self::$viewPath));
            }
        } else {
            $this->view->render('kpi/indicator/list', self::$viewPath);
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }
    
    public function renderCustomView($kpiTypeId) {
        
        $_POST['indicatorId'] = $this->view->indicatorId;
        $this->view->renderGrid = self::renderCustomGrid($kpiTypeId, true);
        
        $renderGrid = $this->view->renderPrint('kpi/indicator/renderCustomGrid', self::$viewPath);
        
        return $renderGrid;
    }
    
    public function renderCustomGrid($kpiTypeId = '', $isReturn = false) {
        
        $this->view->response = $this->model->indicatorDataGridModel();
        
        if ($kpiTypeId == '2016') {
            $viewCode = 'cardview';
        } else {
            $viewCode = 'cardview';
        }
        
        if ($isReturn) {
            $renderGrid = $this->view->renderPrint('kpi/indicator/widget/grid/'.$viewCode, self::$viewPath);
            return $renderGrid;
        } else {
            $this->view->indicatorId = Input::numeric('indicatorId');
            $this->view->render('kpi/indicator/widget/grid/cardview/'.$viewCode, self::$viewPath);
        }
    }
    
    public function renderCustomMoreView() {
        
        $this->view->uniqId = getUID();
        $this->view->indicatorId = Input::numeric('indicatorId');
        $this->view->rowId = Input::numeric('rowId');
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if ($this->view->row['KPI_TYPE_ID'] == '2016') {
            
            $queryString = $this->view->row['QUERY_STRING'];
            $dataTableName = $queryString ? '('.$queryString.')' : $this->view->row['TABLE_NAME'];
            
            $this->view->moreData = $this->model->getKpiDynamicDataRowModel($dataTableName, 'ID', $this->view->rowId);
            
            $this->view->render('kpi/indicator/widget/moreview/cardview', self::$viewPath);
        }
    }
    
    public function indicatorDataList($indicatorId = '') {
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $get = $this->model->getIndicatorModel($indicatorId);
        $this->view->isCheckActionPermission = true;
        
        if ($get && $get['TYPE_CODE'] == '10005') {
            $this->uxFlowIndicatorList($indicatorId);
        } else {
            $this->indicatorList($indicatorId);
        }
    }
    
    public function indicatorDataGrid() {
        
        if (!is_ajax_request()) {
            Message::add('i', '', URL . 'appmenu');
        }
        
        $response = $this->model->indicatorDataGridModel();
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function uxFlowIndicatorList($indicatorId = '', $isReturnArray = false) {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->view->uxFlowIndicatorId = $indicatorId;
        $expression = (new Mdexpression())->uxFlowExpression($indicatorId);
        $indicatorId = $expression['id'];
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->isAjax = is_ajax_request();
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->indicatorCode = $this->view->row['CODE'];
        
        if ($this->view->row['KPI_TYPE_ID'] == '2007') { /*map*/
            
            if (isset($isReturnArray) && $isReturnArray) {
                return $this->indicatorMapRender(true);
            } else {
                $this->indicatorMapRender();
            }
        
            exit;
        }
        
        $this->view->columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row);
        $fieldConfig = $this->model->getKpiIndicatorIdFieldModel($this->view->indicatorId, $this->view->columnsData);
        
        $this->view->idField = $fieldConfig['idField'];
        $this->view->nameField = $fieldConfig['nameField'];
        $this->view->parentField = $fieldConfig['parentField'];
        $this->view->coordinateField = $fieldConfig['coordinateField'];
        
        $this->view->isGridType = 'datagrid';
        $this->view->isTreeGridData = '';
        $this->view->isDataMart = $this->view->row['KPI_TYPE_ID'] == '1040' ? true : false; 
        $this->view->isUseWorkflow = $this->view->row['IS_USE_WORKFLOW'];
        $this->view->isCallWebService = ($this->view->row['KPI_TYPE_ID'] == '1080' || $this->view->row['KPI_TYPE_ID'] == '1160' || $this->view->row['KPI_TYPE_ID'] == '1161');
        $this->view->isFilterShowData = $this->view->row['IS_FILTER_SHOW_DATA'];
        $this->view->postHiddenParams = '';
        $this->view->filter = '';
        
        if ($this->view->idField && $this->view->nameField && $this->view->parentField) {
            $this->view->isGridType = 'treegrid';
            $this->view->isTreeGridData = 'id='.$this->view->idField.'&name='.$this->view->nameField.'&parent='.$this->view->parentField;
        }
        
        $this->view->process = $expression['buttons'];
        $this->view->columns = $this->model->renderKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row['isCheckSystemTable'], $this->view->columnsData);

        $this->view->renderGrid = $this->view->renderPrint('kpi/indicator/renderGrid', self::$viewPath);
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        if (isset($isReturnArray) && $isReturnArray) {
            return array('html' => $this->view->renderPrint('kpi/indicator/list', self::$viewPath));
        } else {
            $this->view->render('kpi/indicator/list', self::$viewPath);
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }
    
    public function addRowKpiIndicatorTemplate() {
        
        $response = $this->model->addRowKpiIndicatorTemplateModel();
        
        $kpiMainIndicatorId = Input::numeric('kpiMainIndicatorId');
        $kpiIndicatorIndicatorMapId = Input::numeric('kpiIndicatorIndicatorMapId');
        
        self::kpiIndicatorTemplateConfigRender($kpiMainIndicatorId, $kpiIndicatorIndicatorMapId);
        
        $response['html'] = $this->view->form;
        
        jsonResponse($response);
    }
    
    public function getKpiIndicatorTemplateRow() {
        $response = $this->model->getKpiIndicatorTemplateRowModel();
        jsonResponse($response);
    }
    
    public function directionRowKpiDynamicTemplate() {
        
        $response = $this->model->directionRowKpiDynamicTemplateModel();
        
        $kpiMainIndicatorId = Input::numeric('kpiMainIndicatorId');
        $kpiIndicatorIndicatorMapId = Input::numeric('kpiIndicatorIndicatorMapId');
        
        self::kpiIndicatorTemplateConfigRender($kpiMainIndicatorId, $kpiIndicatorIndicatorMapId);
        
        $response['html'] = $this->view->form;
        
        jsonResponse($response);
    }
    
    public function removeRowKpiDynamicTemplate() {
        
        $response = $this->model->removeRowKpiDynamicTemplateModel();
        
        $kpiMainIndicatorId = Input::numeric('kpiMainIndicatorId');
        $kpiIndicatorIndicatorMapId = Input::numeric('kpiIndicatorIndicatorMapId');
        
        self::kpiIndicatorTemplateConfigRender($kpiMainIndicatorId, $kpiIndicatorIndicatorMapId);
        
        $response['html'] = $this->view->form;
        
        jsonResponse($response);
    }
    
    public function indicatorExcelExport($isBase64 = false) {
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $this->load->model('mdform', 'middleware/models/');
        
        $_POST['isExportExcel'] = 1;
        $exportData = $this->model->indicatorDataGridModel();
        
        if ($exportData['status'] == 'error') {
            
            if ($isBase64) {
                return array('status' => 'error', 'message' => $exportData['message']);
                
            } else {
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=false; path=/');
                echo $exportData['message']; exit;
            }
        }
        
        $indicatorId = Input::numeric('indicatorId');
        
        $configRow   = $this->model->getKpiIndicatorRowModel($indicatorId);
        $headerDatas = $this->model->getKpiIndicatorColumnsModel($indicatorId, $configRow);

        $header = $widths = array();
        
        $header['№'] = 'integer';
        $widths[] = 5;

        foreach ($headerDatas as $headerDataRow) {
            
            if ($headerDataRow['IS_RENDER'] == '1') {
            
                $headerTypeCode = $headerDataRow['SHOW_TYPE'];
                $columnWidth    = $headerDataRow['COLUMN_WIDTH'];
                $labelName      = Lang::line($headerDataRow['LABEL_NAME']);

                if ($headerTypeCode == 'date') {

                    $header[$labelName] = 'date';
                    $widths[] = $columnWidth ? $columnWidth : 13.5;

                } elseif ($headerTypeCode == 'datetime') {

                    $header[$labelName] = 'datetime';
                    $widths[] = $columnWidth ? $columnWidth : 18.5;

                } elseif ($headerTypeCode == 'bigdecimal') {

                    $header[$labelName] = 'price';
                    $widths[] = $columnWidth ? $columnWidth : 13;

                } elseif ($headerTypeCode == 'number' || $headerTypeCode == 'long' || $headerTypeCode == 'integer') {

                    $header[$labelName] = 'integer';
                    $widths[] = $columnWidth ? $columnWidth : 13;

                } else {
                    $header[$labelName] = 'string';
                    $widths[] = $columnWidth ? ((int) $columnWidth / 10) : 13;
                }
            }
        }

        $exportDataRows = $exportData['rows'];
        array_walk($exportDataRows, function(&$value) {      
            unset($value['RID']);
        }); 
        
        $listName = Str::excelSheetName(Lang::line($configRow['NAME']));
        
        includeLib('Office/Excel/xlsxwriter/xlsxwriter.class');
        
        if ($isBase64) {
            
            $writer = new XLSXWriter();
            $writer->setAuthor('Veritech ERP');
            $writer->writeSheetHeader($listName, $header, array('freeze_rows'=>1,'widths'=>$widths,'color'=>'#000','fill'=>'#74ad42','font-style'=>'bold','border'=>'left,right','border-style'=>'thin','border-color'=>'#000','height'=>'15.5','valign'=>'center'));
            $writer->writeSheet($exportDataRows, $listName);
            
            return array('status' => 'success', 'result' => array('fileName' => $listName, 'base64' => base64_encode($writer->writeToString())));
                
        } else {
            
            try {

                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=true; path=/');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
                header('Content-Disposition: attachment;filename="' . $listName . ' - ' . Date::currentDate('YmdHi') . '.xlsx"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                ob_end_clean();

                $writer = new XLSXWriter();
                $writer->setAuthor('Veritech ERP');
                $writer->writeSheetHeader($listName, $header, array('freeze_rows'=>1,'widths'=>$widths,'color'=>'#000','fill'=>'#74ad42','font-style'=>'bold','border'=>'left,right','border-style'=>'thin','border-color'=>'#000','height'=>'15.5','valign'=>'center'));
                $writer->writeSheet($exportDataRows, $listName);
                $writer->writeToStdOut(); 

            } catch (Exception $e) {

                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=false; path=/');
                echo $e->getMessage(); 
            }
        }
    }
    
    public function generateKpiRelationDataMart($mainIndicatorId) {
        
        $this->load->model('mdform', 'middleware/models/');
        $response = $this->model->generateKpiRelationDataMartModel($mainIndicatorId);
        
        var_dump($response); exit;
    }
    
    public function generateKpiDataMart($mainIndicatorId) {
        
        $this->load->model('mdform', 'middleware/models/');
        
        $srcRecordId = Input::numeric('sourceRecordId');
        $response = $this->model->runGenerateKpiDataMartByIndicatorId($mainIndicatorId, $srcRecordId);
        
        //var_dump($response); exit;
    }
    
    public function generateKpiDetailDataMart($indicatorId, $mainIndicatorId = '') {
        
        $this->load->model('mdform', 'middleware/models/');
        $response = $this->model->generateKpiDataMartModel($indicatorId, $mainIndicatorId);
        
        var_dump($response); exit;
    }
    
    public function generateKpiDataMartByPost() {
        $indicatorId = Input::numeric('indicatorId');
        
        if (Input::numeric('isSqlView') == 1) {
            $response = $this->model->generateKpiRelationDataMartModel($indicatorId);
        } else {
            $response = $this->model->runAllKpiDataMartByIndicatorIdModel($indicatorId);
        }
        
        echo json_encode($response);
    }
    
    public function generateKpiDataMartFromStatement() {
        $mainIndicatorId = Input::numeric('mainIndicatorId');
        $dataIndicatorId = Input::numeric('dataIndicatorId');
        $response = $this->model->generateKpiDataMartFromStatementModel($mainIndicatorId, $dataIndicatorId);
        echo json_encode($response);
    }
    
    public function runAllKpiDataMart($mode = '') {
        $this->load->model('mdform', 'middleware/models/');
        $response = $this->model->runAllKpiDataMartModel($mode);
        echo json_encode($response);
    }
    
    public function runOneKpiDataMart($startIndicatorId, $fileName) {
        $this->load->model('mdform', 'middleware/models/');
        $response = $this->model->runOneKpiDataMartModel($startIndicatorId, $fileName);
        echo json_encode($response);
    }
    
    public function filterKpiIndicatorValueForm() {
        
        $this->view->uniqId = Input::numeric('uniqId');
        $this->view->indicatorId = Input::numeric('indicatorId');
        $this->view->isChartList = Input::numeric('isChartList');
        
        $filterData = $this->model->filterKpiIndicatorValueFormModel($this->view->indicatorId);
        
        if ($filterData['status'] == 'success') {
            $this->view->filterData = $filterData['data'];
            
            $response = array(
                'status' => 'success', 
                'html' => $this->view->renderPrint('kpi/indicator/filterForm', self::$viewPath)
            );
        } else {
            $response = $filterData;
        }
        
        jsonResponse($response);
    }
    
    public function kpiDataMartRelationConfig() {
        $this->view->id = Input::numeric('id');
        
        if ($this->view->id) {
            
            $this->view->columns = $this->model->getKpiDataMartRelationColumnsModel($this->view->id);
            $this->view->criterias = $this->model->getKpiDataMartRelationCriteriasModel($this->view->id);
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $objects = $this->model->getDataMartGetDataModel('data_dataModelGetDV_004', array('id' => $this->view->id));
            
            $response = array(
                'html'      => $this->view->renderPrint('form/kpi/indicator/relation/relationConfig', 'middleware/views/'), 
                'status'    => 'success', 
                'objects'   => $objects
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        jsonResponse($response);
    }
    
    public function kpiDataMartRelationConfig2() {
        $this->view->id = Input::numeric('id');
        
        if ($this->view->id) {
            
            $this->view->columns = $this->model->getKpiDataMartRelationColumnsModel($this->view->id);
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $objects = $this->model->getDataMartGetDataModel('data_dataModelGetDV_004', array('id' => $this->view->id));
            
            $response = array(
                'html'      => $this->view->renderPrint('form/kpi/indicator/relation/relationConfig2', 'middleware/views/'), 
                'status'    => 'success', 
                'objects'   => $objects
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        jsonResponse($response);
    }
    
    public function indicatorChart($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        $this->view->returnBuilder = Input::post('returnBuilder');
        $this->view->kolIndex = Input::post('kolIndex');
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }


        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->uniqId = getUID();
        $this->view->isAjax = is_ajax_request();
        $this->view->row['isIgnoreStandardFields'] = true;
        
        if ($this->view->row['KPI_TYPE_ID'] == '1060') {
            
            $graphJsonRow = $this->model->getKpiIndicatorChartRowModel($this->view->indicatorId);
            
            $this->view->graphJsonConfig = json_decode($graphJsonRow['GRAPH_JSON'], true);
            $this->view->chartIndicatorId = $this->view->indicatorId;
            $this->view->indicatorId = $graphJsonRow['SRC_INDICATOR_ID'];
            $this->view->chartName = $this->view->row['NAME'];
        }
        
        $columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row); 
        $columns = $this->model->chartKpiIndicatorColumnsModel($columnsData);
        
        $this->view->categoryColumns = $columns['categoryColumns'];
        $this->view->valueColumns = $columns['valueColumns'];
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $render = 'kpi/indicator/chart/chart';

        $this->view->isBuild = '1'; /*Config::getFromCache('useEchartsBuilder');*/
        /* $this->view->isBuild = ($this->view->indicatorId === '157348521') ? '1' : $this->view->isBuild; */
        $filterMainId = '16903372738229';
        $systemMetaGroupId = '1642419374729118';
        $this->view->chartMainTypeData = array(); 
        $this->view->chartTypesConfigration = array(); 
        $this->view->chartDefaultTypes = array();
        
        $cache = phpFastCache();
        $cacheName = 'dvGetChartData_'.$systemMetaGroupId.'_'.$filterMainId;
        $data = null; //$cache->get($cacheName);
        if ($data === null) {
            $this->view->chartMainTypeData = $chartTypeData = $this->model->mainChartTypeDataModel();
            $data = array(
                'chartMainTypeData' => $this->view->chartMainTypeData,
                'chartTypesConfigration' => $this->view->chartTypesConfigration,
                'chartDefaultTypes' => $this->view->chartDefaultTypes,
            );

            $cache->set($cacheName, $data, Mdwebservice::$expressionCacheTime);
        }

        $this->view->chartMainTypeData = issetParamArray($data['chartMainTypeData']); 
        $this->view->chartTypesConfigration = issetParamArray($data['chartTypesConfigration']); 
        $this->view->chartDefaultTypes = issetParamArray($data['chartDefaultTypes']); 
        
        if ($this->view->isBuild === '1') {
            $render = 'kpi/indicator/chart/build';
        }

        $this->view->renderChart = $this->view->renderPrint($render, self::$viewPath);
        
        if (isset($isReturnArray)) {
            return array('html' => $this->view->renderPrint('kpi/indicator/chart/index', self::$viewPath));
        } else {
            $this->view->render('kpi/indicator/chart/index', self::$viewPath);
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }
    
    public function indicatorChartList($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->uniqId = getUID();
        $this->view->isAjax = is_ajax_request();
        
        if ($this->view->row['KPI_TYPE_ID'] == '1060') {
            
            $chartRow = $this->model->getKpiIndicatorChartRowModel($this->view->indicatorId); 
            $this->view->indicatorId = $chartRow['SRC_INDICATOR_ID'];
            
            $this->view->charts = array($chartRow);
            
        } else {
            $charts = $this->model->getKpiIndicatorChildChartsModel($this->view->indicatorId); 
            $this->view->charts = issetParamArray($charts['data']);
        }
        
        $this->view->isBuild = '1'; /*Config::getFromCache('useEchartsBuilder');*/
        if (strpos(issetParam($this->view->charts['0']['GRAPH_JSON']), '"cartType":"echarts"') !== false) {
            $this->view->isBuild = '1';
        }

        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $this->view->renderChart = $this->view->renderPrint('kpi/indicator/chart/list', self::$viewPath);
        
        if (isset($isReturnArray)) {
            return array('html' => $this->view->renderPrint('kpi/indicator/chart/chartList', self::$viewPath));
        } else {
            $this->view->render('kpi/indicator/chart/chartList', self::$viewPath);
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }
    
    public function filterKpiIndicatorValueChart() {
        $response = $this->model->filterKpiIndicatorValueChartModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function clearCacheData($indicatorId = '') {
        
        $tmp_dir = Mdcommon::getCacheDirectory();
        $clearFiles = array();
        
        if ($indicatorId != '') {
            
            $cacheFiles = glob($tmp_dir."/*/mv/mvData_".$indicatorId."_*.txt");
            $filterCacheFiles = glob($tmp_dir."/*/mv/mvFilterData_".$indicatorId."_*.txt");
            $polylineFiles = glob($tmp_dir."/*/mv/mvPolylineData_".$indicatorId.".txt");
            
            $clearFiles = array_merge_recursive($clearFiles, $cacheFiles, $filterCacheFiles, $polylineFiles);
            
        } else {
            
            $cacheFiles = glob($tmp_dir."/*/mv/mvData_*.txt");
            $filterCacheFiles = glob($tmp_dir."/*/mv/mvFilterData_*.txt");
            $polylineFiles = glob($tmp_dir."/*/mv/mvPolylineData_*.txt");
            
            $clearFiles = array_merge_recursive($clearFiles, $cacheFiles, $filterCacheFiles);
        }
        
        if (count($clearFiles)) {
            foreach ($clearFiles as $cacheFile) {
                @unlink($cacheFile);
            }
        }
        
        return true;
    }
    
    public function getKpiDataMartObjectRelation() {
        $this->load->model('mddatamodel', 'middleware/models/');
        
        $sourceIndicatorId = Input::numeric('sourceIndicatorId');
        $targetIndicatorId = Input::numeric('targetIndicatorId');
        
        $sourceAttrs = $this->model->getDataMartDvRowsModel('164992036725010', array(
            'filterMainId' => array(
                array(
                    'operator' => '=',
                    'operand' => $sourceIndicatorId
                )
            ))
        );
        
        $targetAttrs = $this->model->getDataMartDvRowsModel('164992036725010', array(
            'filterMainId' => array(
                array(
                    'operator' => '=',
                    'operand' => $targetIndicatorId
                )
            ))
        );
        
        $response = array('sourceAttrs' => $sourceAttrs, 'targetAttrs' => $targetAttrs);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function getKpiIndicatorAttrs() {
        $this->load->model('mddatamodel', 'middleware/models/');
        
        $indicatorId = Input::numeric('indicatorId');
        
        $attrs = $this->model->getDataMartDvRowsModel('164992036725010', array(
            'filterMainId' => array(
                array(
                    'operator' => '=',
                    'operand' => $indicatorId
                )
            ))
        );
        
        echo json_encode($attrs, JSON_UNESCAPED_UNICODE);
    }
    
    public function saveKpiDataMartRelationConfig() {
        $response = $this->model->saveKpiDataMartRelationConfigModel();
        jsonResponse($response);
    }
    
    public function createKpiDmChart() {
        $response = $this->model->createKpiDmChartModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function downloadExcelImportTemplate() {
        
        $indicatorId = Input::numeric('indicatorId');
        $indicatorIsTranslate = Input::post('isDataTranslate');
        $parentId = Input::numeric('parentId');
        
        $row = $this->model->getKpiIndicatorRowModel($indicatorId);
        $title = $this->lang->line($row['NAME']);
            
        if ($indicatorId && $parentId) {
            
            $columnsData = $this->model->getKpiIndicatorChildColumnsModel($indicatorId, $parentId);
            $title = $title.' - '.$columnsData[0]['LABEL_NAME'];
            $sheetName = 'Detail';
            
            unset($columnsData[0]);
            
        } else {

            unset($row['NAME_PATTERN']);
            $row['isIgnoreStandardFields'] = true;

            $columnsData = $this->model->getKpiIndicatorColumnsModel($indicatorId, $row);
            $sheetName = 'Sheet1';
        }
        
        if ($columnsData) {
        
            includeLib('Office/Excel/PHPExcel');
            includeLib('Office/Excel/PHPExcel/Writer/Excel2007');

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                    ->setCreator('Veritech ERP')
                    ->setCompany('Veritech ERP')
                    ->setLastModifiedBy('')
                    ->setTitle('Office 2007 - Document')
                    ->setSubject('Office 2007 - Document')
                    ->setDescription('')
                    ->setKeywords('')
                    ->setCategory('');

            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();

            $sheet->setTitle(Str::excelSheetName($sheetName));
            
            $n = 0;
            $n2 = 0;
            
            foreach ($columnsData as $col) {
                
                if ($col['COLUMN_NAME']) {
                    
                    $n2 ++;
                    $n ++;
                    
                    $alphaCol2 = numToAlpha($n2);
                    
                    $sheet->setCellValue($alphaCol2 . '1', $col['COLUMN_NAME']);
                    if ($indicatorIsTranslate && Lang::isUseMultiLang()) {
                        $tlist = Lang::getLanguageList();
                        $tlistCount = count($tlist) - 1;

                        foreach ($tlist as $countryCode) {
                            if (Lang::getDefaultLangCode() != $countryCode['SHORT_CODE']) {
                                $n2 ++;
                                $sheet->setCellValue(numToAlpha($n2) . '1', $col['COLUMN_NAME'].'_:'.$countryCode['SHORT_CODE']);
                            }
                        }

                        $sheet->setCellValue(numToAlpha($n) . '2', $col['LABEL_NAME']);
                        $sheet->mergeCells(numToAlpha($n) . '2:' . numToAlpha($n+$tlistCount) . '2');
                        $n = $n + $tlistCount;                        
                    } else {
                        $sheet->setCellValue($alphaCol2 . '2', $col['LABEL_NAME']);
                    }
                }
            }
            
            $getHighestRowNum = $sheet->getHighestRow();
            
            foreach (range(0, $n2) as $columnID) {
                $sheet->getColumnDimensionByColumn($columnID)->setAutoSize(true);
            }
            
            $sheet->getStyle('A1:' . numToAlpha($n2) . $getHighestRowNum)->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'wrap' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '74ad42')
                    )
                )
            );
            
            $sheet->freezePane('A3');

            try {
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=true; path=/');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
                header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');

                ob_end_clean();
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');

            } catch (Exception $e) {
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=false; path=/');
                echo $e->getMessage();
            }
        }
    }
    
    public function excelImportKpiIndicatorValue() {
        $response = $this->model->excelImportKpiIndicatorValueModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function rowsImportExcel() {
        
        $response = $this->model->rowsImportExcelModel();
        
        if ($response['status'] == 'success') {
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            jsonResponse($response);
        }
    }

    public function kpiDataMartRelationConfigJson() {
        $this->view->id = Input::numeric('id');
        jsonResponse($this->model->getKpiDataMartRelationColumnsModel($this->view->id));
    }

    public function getKpiDataMartRelationColumnsWithInput() {
        $this->view->id = Input::numeric('id');
        jsonResponse($this->model->getKpiDataMartRelationColumnsWithInputModel($this->view->id));
    }

    public function objectMethod() {
        $this->view->id = Input::numeric('id');
        jsonResponse($this->model->objectMethodModel($this->view->id));
    }     
    
    public function indicatorDashboard($indicatorId = '') {

        
        $filterParams = $this->model->getKpiDashboardFilterParamsModel($indicatorId);
        $dashboardConfigs = $this->model->getKpiDashboardChartsModel($indicatorId);
        
        $layoutCode = $dashboardConfigs['layoutCode'];
        $positionData = $dashboardConfigs['positionData'];
        
        $this->view->isAjax = is_ajax_request();
        $this->view->uniqId = getUID();
        $this->view->indicatorId = $indicatorId;
        $this->view->isChartList = 1;
        $this->view->bgImage = $dashboardConfigs['bgImage'];
        $this->view->isBuild = '1'; /*Config::getFromCache('useEchartsBuilder');*/
        if ($indicatorId === '099' || $layoutCode === '100') {
            $mdIntegration = new Mdintegration();
            $this->view->weatherData = $mdIntegration->getForecast5day('Дархан');
        }
        if ($filterParams) {
            
            $_POST['isChartList'] = 1;
            
            $filterData = $this->model->filterKpiIndicatorValueFormModel($this->view->indicatorId, $filterParams);
            
            if ($filterData['status'] == 'success') {
                
                $this->view->isDashboard = 1;
                $this->view->filterData = $filterData['data'];
                
                $this->view->filterForm = $this->view->renderPrint('kpi/indicator/filterForm', self::$viewPath);
            } 
        }
        
        if (!$layoutCode || ($layoutCode && !file_exists(self::$viewPath . 'kpi/indicator/dashboard/layout/' . $layoutCode . '.php'))) {
            $layoutCode = '005';
        }

        $this->view->layout = $this->view->renderPrint('kpi/indicator/dashboard/layout/'.$layoutCode, self::$viewPath);
        
        foreach ($positionData as $posRow) {
            
            $posCode = $posRow['POSITION_CODE'];
            $title = $posRow['NAME'];
            $relatedIndicatorId = $posRow['RELATED_INDICATOR_ID'];
            $srcIndicatorId = $posRow['SRC_INDICATOR_ID'];
            
            $posRow['GRAPH_JSON'] = str_replace('{"type":', '{"chartName": "'.$title.'", "type":', $posRow['GRAPH_JSON']);
            $graphJson = $posRow['GRAPH_JSON'];
            
            $elementId = 'kpi-datamart-chart-render-'.$this->view->uniqId.'-'.$posCode.'-'.$relatedIndicatorId;
            
            $jsonScript = '<script type="text/template" data-id="'.$elementId.'">'.$graphJson.'</script>';
            
            $this->view->layout = str_replace('data-kl-col="'.$posCode.'"', 'data-kl-col="'.$posCode.'" data-kpis-indicatorid="'.$relatedIndicatorId.'" data-src-indicatorid="'.$srcIndicatorId.'"', $this->view->layout);
            $this->view->layout = str_replace('<!--sectionCode'.$posCode.'-title-->', '<div class="card-header"><h6 class="card-title">'.$title.'</h6></div>' . $jsonScript, $this->view->layout);
            $this->view->layout = str_replace('data-section-code="'.$posCode.'"', 'data-section-code="'.$posCode.'" id="'.$elementId.'"', $this->view->layout);
        }
        
        if ($this->view->isAjax == false) {
            $this->view->title = 'APP MENU'; 
            
            $this->view->css = array_unique(array_merge(array('custom/css/vr-card-menu.css'), AssetNew::metaCss()));
            $this->view->js = array_unique(array_merge(array('custom/addon/admin/pages/scripts/app.js'), AssetNew::metaOtherJs()));
            $this->view->fullUrlJs = array('middleware/assets/js/addon/indicator.js', 'assets/custom/addon/plugins/echarts/echarts.js', 'middleware/assets/js/addon/echartsBuilder.js');

            $this->view->render('header');
        }
            $this->view->render('kpi/indicator/dashboard/index', self::$viewPath);
        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
         
    }
    
    public function indicatorOneChart($indicatorId = '') {
        
        $this->view->uniqId = getUID();
        $this->view->indicatorId = $indicatorId;
        $this->view->row = $this->model->getKpiIndicatorChartRowModel($this->view->indicatorId);
        
        if ($this->view->row['RELATED_INDICATOR_ID']) {
            
            Mdform::$recordId = $this->view->indicatorId;
            Mdform::$isControlViewLabel = true;
            
            $relatedIndicatorId = $this->view->row['RELATED_INDICATOR_ID'];
            
            $data = $this->model->getKpiIndicatorTemplateModel($relatedIndicatorId);
            $dataTableName = $data[0]['TABLE_NAME'];

            $form = $this->model->renderKpiIndicatorTemplateModel($relatedIndicatorId, $dataTableName, $data);
            
            if (Mdform::$firstTplId) {
                $this->view->form = $form;
            }
        }
        
        if (Input::numeric('isOnlyFormPrint') == 1) {
            echo issetParam($this->view->form); exit;
        }
        
        $this->view->render('kpi/indicator/chart/oneChart', self::$viewPath);
    }
    
    public function indicatorStatement($indicatorId = '') {
        
        $this->view->isAjax = is_ajax_request();
        $this->view->uniqId = getUID();
        $this->view->mainIndicatorId = $indicatorId;
        
        $st = new Mdstatement();
        
        Mdstatement::$isKpiIndicator = true;
        
        $this->load->model('mdstatement', 'middleware/models/');
        $this->view->reportViewer = $st->dataModelReportViewer($this->view->mainIndicatorId);
        
        $_POST['isChartList'] = 1;
        
        $this->view->indicatorId = Mdform::$kpiTemplateId;
        
        $filterData = $this->model->filterKpiIndicatorValueFormModel($this->view->indicatorId);

        if ($filterData['status'] == 'success') {

            $this->view->isDashboard = 1;
            $this->view->filterData = $filterData['data'];

            $this->view->filterForm = $this->view->renderPrint('kpi/indicator/filterForm', self::$viewPath);
        } 
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $this->view->render('kpi/indicator/statement/render', self::$viewPath);
        
        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }
    
    public function indicatorKnowledge($indicatorId = '') {
        
        $this->view->uniqId = getUID();
        $this->view->indicatorId = $indicatorId;
        
        $this->load->model('mdwebservice', 'middleware/models/');
        $files = $this->model->getMetaDataValueFilesModel('16426514644071', $this->view->indicatorId);
        $this->view->knowledge = '';
        
        if ($files) {
            
            $this->view->fileArr = array();
            
            foreach ($files as $filePath) {

                if (file_exists($filePath['ATTACH'])) {
                    
                    $extension = $filePath['FILE_EXTENSION'];
                    
                    if (!$extension) {
                        $pathInfo = pathinfo($filePath['ATTACH']);
                        $extension = $pathInfo['extension'];
                    }
                    
                    $this->view->fileArr[] = array(
                        'contentId' => $filePath['ATTACH_ID'], 
                        'path'      => $filePath['ATTACH'], 
                        'thumbPath' => $filePath['THUMB_PHYSICAL_PATH'], 
                        'extention' => $extension, 
                        'name'      => ($filePath['ATTACH_NAME'] ? $filePath['ATTACH_NAME'] : 'File name is empty!')
                    );
                }
            }
            
            if ($this->view->fileArr) {
                
                $this->view->isIframe = false;

                if (defined('CONFIG_FILE_VIEWER_ADDRESS') && CONFIG_FILE_VIEWER_ADDRESS) {
                    $this->view->isIframe = true;
                }

                $this->view->knowledge = $this->view->renderPrint('multiFileViewer', 'middleware/views/preview/');
            }
            
        } else {
            $this->load->model('mdwebservice', 'middleware/models/');
            
            $knowledge = $this->model->getKpiIndicatorKnowledgeRowModel($this->view->indicatorId);
            $this->view->knowledge = Str::cleanOut($knowledge);
        }
        
        $this->view->render('kpi/indicator/knowledge/index', self::$viewPath);
    }
    
    public function indicatorIframe($indicatorId = '') {
        
        $this->view->uniqId = getUID();
        $this->view->mainIndicatorId = $indicatorId;
        $this->view->row = $this->model->getKpiAdditionalInfoModel($this->view->mainIndicatorId);
        
        $this->view->render('kpi/indicator/iframe/render', self::$viewPath);
    }

    public function indicatorRender($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        $this->view->indicatorName = '';
        $isReturnArray = false;
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $this->view->indicatorName = Input::param($workSpaceParamArray['workSpaceParam']['name']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                    $this->view->indicatorName = Input::param($workSpaceParamArray['workSpaceParam']['name']);
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->uniqId = getUID();
        $this->view->isAjax = is_ajax_request();
        $this->view->row['isIgnoreStandardFields'] = true;
        
        $columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row); 
        $columns = $this->model->chartKpiIndicatorColumnsModel($columnsData);
        
        $this->view->categoryColumns = $columns['categoryColumns'];
        $this->view->valueColumns = $columns['valueColumns'];
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $this->view->renderChart = '';
        
        switch ($this->view->row['KPI_TYPE_ID']) {
            case '1130':
                self::indicatorDashboard($this->view->indicatorId);
                break;
            case '2020':
                self::buildPagemanagment($this->view->indicatorId, 'render', $this->view->row);
                break;
            case '1170':
                self::areaRender($isReturnArray);
                break;
            case '2006':
                self::bpmnRender($isReturnArray, $this->view->indicatorId, $this->view->indicatorName);
                break;
            default:
                echo 'Харуулах боломжгүй байна.';
                break;
        }        

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }    

    public function areaRender($isReturnArray) {
        if ($isReturnArray) {
            return array('html' => $this->view->renderPrint('kpi/indicator/area/index', self::$viewPath));
        } else {
            return $this->view->render('kpi/indicator/area/index', self::$viewPath);
        }        
    }

    public function bpmnRender($isReturnArray, $indicatorId, $indicatorName) {
        $this->view->generateBpmnScript = '';
        $this->view->savedValue = [];

        $this->load->model('mdprocessflow', 'middleware/models/');
        $metaRow = $this->model->getBpmnXmlIndicatorModel($indicatorId);
        if ($metaRow) {
            $this->view->generateBpmnScript = $metaRow["GRAPH_JSON"];
            $this->view->savedValue = $metaRow;
        }
        $this->view->mainBpId = $indicatorId;
        $this->view->uniqId = getUID();
        $this->view->bpUniqId = Input::post('bpUniqId');
        $this->view->indicatorId = $indicatorId;
        $this->view->indicatorName = $indicatorName;
        $this->view->bpPath = "";

        if ($isReturnArray) {
            return array('html' => $this->view->renderPrint('indexindicator2', 'middleware/views/bpmn/'));
        } else {
            return $this->view->render('indexindicator2', 'middleware/views/bpmn/');
        }        
    }

    public function kpiExpressionVisual($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '16527538570202';
        $this->view->indicatorName = '';
        $isReturnArray = false;

        $exp = '{"cells":[{"type":"standard.Rectangle","position":{"x":530,"y":50},"size":{"width":90,"height":54},"angle":0,"id":"87c1c52e-a55e-4f39-91f6-6f3ec950248f","z":1,"attrs":{"body":{"stroke":"#31d0c6","fill":"transparent","rx":2,"ry":2,"width":50,"height":30,"strokeDasharray":"0"},"label":{"fontSize":11,"fill":"#c6c7e2","text":"value=1000","fontFamily":"Roboto Condensed","fontWeight":"Normal","strokeWidth":0},"root":{"dataTooltipPosition":"left","dataTooltipPositionSelector":".joint-stencil"}}},{"type":"standard.Polygon","position":{"x":530,"y":176.0056915283203},"size":{"width":90,"height":54},"angle":0,"id":"43e06bd5-d04c-44df-8f44-453bc0a87fb4","z":2,"attrs":{"body":{"refPoints":"50,0 100,50 50,100 0,50","stroke":"#31d0c6","fill":"transparent","strokeDasharray":"0"},"label":{"fontSize":11,"fill":"#c6c7e2","text":"condition == abcd","fontFamily":"Roboto Condensed","fontWeight":"Normal","strokeWidth":0},"root":{"dataTooltipPosition":"left","dataTooltipPositionSelector":".joint-stencil"}}},{"type":"app.Link","router":{"name":"normal"},"connector":{"name":"rounded"},"labels":[],"source":{"id":"87c1c52e-a55e-4f39-91f6-6f3ec950248f"},"target":{"id":"43e06bd5-d04c-44df-8f44-453bc0a87fb4"},"id":"456c9612-6b00-4d50-b965-3afb5270aef8","z":3,"attrs":{}},{"type":"standard.Rectangle","position":{"x":440,"y":333.00567626953125},"size":{"width":90,"height":54},"angle":0,"id":"4584a1ad-df67-456a-9dad-1e2860ddb898","z":4,"attrs":{"body":{"stroke":"#31d0c6","fill":"transparent","rx":2,"ry":2,"width":50,"height":30,"strokeDasharray":"0"},"label":{"fontSize":11,"fill":"#c6c7e2","text":"condition value set","fontFamily":"Roboto Condensed","fontWeight":"Normal","strokeWidth":0},"root":{"dataTooltipPosition":"left","dataTooltipPositionSelector":".joint-stencil"}}},{"type":"app.Link","router":{"name":"orthogonal"},"connector":{"name":"rounded"},"labels":[{"attrs":{"text":{"text":"true"}},"position":0.5}],"source":{"id":"43e06bd5-d04c-44df-8f44-453bc0a87fb4"},"target":{"id":"4584a1ad-df67-456a-9dad-1e2860ddb898"},"id":"b369388a-14d0-45bc-8251-c62dfd686ba6","z":5,"attrs":{}}]}';          

        parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);

        if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
            $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
            $this->view->indicatorName = Input::param($workSpaceParamArray['workSpaceParam']['name']);
            $isReturnArray = true;
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->isAjax = is_ajax_request();
        $this->view->uniqId = getUID();
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        $this->view->expressionJson = Input::post('expressionJson');

        if (empty($this->view->expressionJson)) {
            $getExp = $this->model->getBlockExpressionModel($this->view->indicatorId);
            if ($getExp['status'] == 'success' && isset($getExp['result'])) {
                $this->view->expressionJson = $getExp['result']['varfncexpressionstringjson'];
            }
        }
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        }         
    
        $this->view->columns = $this->model->getKpiDataMartRelationAllColumnsModel();

        if ($this->view->isAjax == false) {
            $this->view->render('form/kpi/indicator/flowchart/expressionEditor', 'middleware/views/');
        } else {
            echo $this->view->renderPrint('form/kpi/indicator/flowchart/expressionEditor', 'middleware/views/');
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }        
    }       

    public function updateGraphJsonKpiIndicator() {
        jsonResponse($this->model->updateGraphJsonKpiIndicatorModel(Input::numeric('id'), $_POST['json']));
    }
    
    public function indicatorMapRender($isReturnArray = false) {
        
        $this->view->mapLayers = $this->model->getKpiIndicatorMapModel($this->view->indicatorId, 10000007);
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $this->view->drillDownCriteria = Input::post('drillDownCriteria');
        $this->view->renderGrid = $this->view->renderPrint('kpi/indicator/map/render', self::$viewPath);
        
        if ($isReturnArray) {
            return array('html' => $this->view->renderPrint('kpi/indicator/list', self::$viewPath));
        } else {
            $this->view->render('kpi/indicator/list', self::$viewPath);
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }

    public function flowChartExpression($indicatorId = '') {
        
        $mdf2 = &getInstance();
        $mdf2->load->model('mdform', 'middleware/models/');
        $kpiIndicatorExp = $mdf2->model->getKpiDataMartRelationColumnsModel($indicatorId);        
        $eventExpString = '';
        
        foreach ($kpiIndicatorExp as $posRow) {
            if ($posRow['BLOCK_DIAGRAM']) {
                $exp = str_replace('};', '♥♥♥', $posRow['EVENT_EXPRESSION_STRING']);
                $eventExpString .= '['.$posRow['SRC_COLUMN_NAME'].'].'.$posRow['EVENT_STRING'].'(){'.$exp.'};';
            }
        }

        return $eventExpString;
    }    
    
    public function indicatorSelectableGrid() {
        
        $this->view->isBasket = true;
        $this->view->indicatorId = Input::numeric('indicatorId');
        $this->view->chooseType = Input::post('chooseType');
        
        $mainList = $this->indicatorList($this->view->indicatorId, true);
        
        $this->view->mainList = $mainList['html'];        
        $this->view->dataGridBody = $this->view->idField . ': row.' . $this->view->idField . ',';
        
        foreach ($this->view->columnsData as $row) {
            
            $this->view->dataGridBody .= $row['COLUMN_NAME'] . ': row.' . $row['COLUMN_NAME'] . ',';
            
            if ($row['SHOW_TYPE'] == 'combo' || $row['SHOW_TYPE'] == 'popup' || $row['SHOW_TYPE'] == 'radio') {
                $this->view->dataGridBody .= $row['COLUMN_NAME'].'_DESC' . ': row.' . $row['COLUMN_NAME'] . '_DESC,';
            }
        }
        
        echo json_encode(array(
            'title' => $this->view->title, 
            'idField' => $this->view->idField, 
            'codeField' => $this->view->codeField, 
            'nameField' => $this->view->nameField, 
            'html' => $this->view->renderPrint('kpi/indicator/basket/index', self::$viewPath)
        ), JSON_UNESCAPED_UNICODE);
    }

    public function saveBlockExpression() {
        $response = $this->model->saveBlockExpressionModel();
        jsonResponse($response);
    }    

    public function getShowInputParams($id) {
        $this->load->model('mdwebservice', 'middleware/models/');
        $returnData = $this->model->getShowInputParams($id);
        jsonResponse($returnData);
    }    
    
    public function kpiIndicatorLinkedCombo() {
        
        $jsonAttr = Input::post('jsonAttr');
        
        if (!issetParam($jsonAttr['PROCESS_META_DATA_ID']) || !issetParam($jsonAttr['META_DATA_ID']) || !issetParam($jsonAttr['PARAM_REAL_PATH'])) {
            echo json_encode(array('emptyCombo' => 'OK')); exit;
        }
        
        $lookupIndicatorId = $jsonAttr['META_DATA_ID'];
        $inputParamStr     = Input::post('inputParams');
        $filterData        = array();
        
        parse_str(urldecode($inputParamStr), $inputParamArr);
        
        foreach ($inputParamArr as $inputParam => $inputParamVal) {
            $filterData[$inputParam][] = $inputParamVal;
        }
        
        $_POST['indicatorId'] = $lookupIndicatorId;
        $_POST['filterData']  = $filterData;
        $_POST['isComboData'] = 1;
        
        $result = $this->model->indicatorDataGridModel();
        
        if (isset($result['status']) && $result['status'] == 'success') {
            
            $response = array();
            $rows     = $result['rows'];
            
            $attrId   = $jsonAttr['ATTRIBUTE_ID_COLUMN'];
            $attrName = $jsonAttr['ATTRIBUTE_NAME_COLUMN'];

            foreach ($rows as $key => $value) {
                
                $response[$key]['META_VALUE_ID']   = $value[$attrId];
                $response[$key]['META_VALUE_NAME'] = $value[$attrName];
                $response[$key]['ROW_DATA']        = $value;
            }
                
        } else {
            $response = $result;
        }
        
        jsonResponse($response);
    }

    public function microflowAddCriteria() {
        $this->view->id = Input::numeric('id');
        $this->view->code = Input::post('srcObjectCode');
        
        if ($this->view->id) {
            
            $this->view->columns = $this->model->getKpiDataMartRelationColumnsByCodeModel($this->view->code);
            $this->view->mainColumns = $this->model->getKpiDataMartRelationColumnsModel($this->view->id);
            $this->view->criterias = json_decode(html_entity_decode(Input::post('criteria'), ENT_QUOTES, 'UTF-8'), true);
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $objects = $this->model->getDataMartGetDataModel('data_dataModelGetDV_004', array('id' => $this->view->id));
            
            $response = array(
                'html'      => $this->view->renderPrint('form/kpi/indicator/microflow/criteria', 'middleware/views/'), 
                'status'    => 'success', 
                'objects'   => $objects
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        jsonResponse($response);
    }    

    public function microflowAddParams() {
        $this->view->id = Input::numeric('id');
        $this->view->code = Input::post('srcObjectCode');
        
        if ($this->view->id) {
            
            $this->view->columns = $this->model->getKpiDataMartRelationColumnsByCodeModel($this->view->code);
            $this->view->mainColumns = $this->model->getKpiDataMartRelationColumnsModel($this->view->id);
            $this->view->criterias = json_decode(html_entity_decode(Input::post('criteria'), ENT_QUOTES, 'UTF-8'), true);
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $objects = $this->model->getDataMartGetDataModel('data_dataModelGetDV_004', array('id' => $this->view->id));
            
            $response = array(
                'html'      => $this->view->renderPrint('form/kpi/indicator/microflow/criteriaParams', 'middleware/views/'), 
                'status'    => 'success', 
                'objects'   => $objects
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        jsonResponse($response);
    }    
    
    public function indicatorParameterList() {
        
        $mainIndicatorId = Input::post('mainIndicatorId');
        $crudIndicatorId = Input::post('crudIndicatorId');        
        
        $this->view->doProcessid = '';        
        $this->view->inputlist = $this->model->indicatorParameterListModel($mainIndicatorId, $crudIndicatorId);        

        echo $this->view->renderPrint('form/inputparameterindicator', 'middleware/views/');
    }
    
    public function kpiIndicatorExcelImportForm() {
        
        $param       = Input::post('param');
        $selectedRow = Input::post('selectedRow');
        $rowId       = strtolower(issetParam($param['id']));
        
        $indicatorId = issetParam($selectedRow[$rowId]);
        
        $this->view->templateList = $this->model->getExcelTemplateByKpiTypeModel($indicatorId);
        
        $response = array(
            'status' => 'success', 
            'indicatorId' => $indicatorId, 
            'html' => $this->view->renderPrint('kpi/indicator/excelimport/form', self::$viewPath)
        );
        
        echo json_encode($response);
    }
    
    public function kpiIndicatorExcelImport() {
        
        $response = $this->model->kpiIndicatorExcelImportModel();
        echo json_encode($response);
    }
    
    public function indicatorProcessWidget($indicatorId = '') {
        
        $this->view->uniqId = getUID();
        $this->view->indicatorId = $indicatorId;
        
        $data = $this->model->getIndicatorDynamicTblDataModel($indicatorId);
        
        if (isset($data['PROCESS_ID']) && isset($data['WIDGET_ID']) && isset($data['PROCESS_METHOD_RELATION'])) {
            
            $widgetCode = $this->model->getMetaWidgetModel($data['WIDGET_ID']);
            
            if (file_exists(self::$viewPath . 'kpi/indicator/widget/process/header/' . $widgetCode . '.php')) {
                
                $this->view->isAjax = is_ajax_request();
                
                $this->view->positionData = $this->model->getFillPositionDataModel($indicatorId, $data['PROCESS_METHOD_RELATION']);
                $this->view->rowsPath = $this->model->getIndicatorRowsParamModel($data['PROCESS_ID']);
                
                $this->view->mainIndicatorId = $this->view->rowsPath[0]['MAIN_INDICATOR_ID'];
                
                $hiddenParams = array('mainIndicatorId' => $this->view->mainIndicatorId, 'id' => issetParam($this->view->positionData[0]['VALUE']));
                
                $this->view->isCreateMode = $hiddenParams['id'] ? 0 : 1;
                $this->view->hiddenParams = Crypt::encrypt(json_encode($hiddenParams));
                
                $this->view->render('kpi/indicator/widget/process/header/' . $widgetCode, self::$viewPath);
                
            } else {
                echo 'Not widget! /'.$widgetCode.'/';
            }
        } else {
            echo 'Not config!';
        }
    }
    
    public function mvWidgetColumnsRender() {
        
        if (Input::postCheck('hiddenParams')) {
            
            $hiddenParams = Input::post('hiddenParams');
            $hiddenParams = Crypt::decrypt($hiddenParams);
            $hiddenParams = @json_decode($hiddenParams, true);
            
            if (isset($hiddenParams['mainIndicatorId'])) {

                $_POST['param']['indicatorId'] = $hiddenParams['mainIndicatorId'];
                $_POST['param']['dynamicRecordId'] = $hiddenParams['id'];
                $_POST['param']['columns'] = "'" . Arr::implode_r("','", Input::post('columns'), true) . "'";

                self::kpiIndicatorTemplateRender();
                exit;
            }
        }
        
        jsonResponse(array('status' => 'error', 'message' => 'Invalid request!'));
    }
    
    public function saveMvWidgetSave() {
        
        if (Input::postCheck('hiddenParams')) {
            
            $hiddenParams = Input::post('hiddenParams');
            $hiddenParams = Crypt::decrypt($hiddenParams);
            $hiddenParams = @json_decode($hiddenParams, true);
            
            if (isset($hiddenParams['mainIndicatorId'])) {
                
                $_POST['kpiMainIndicatorId'] = $hiddenParams['mainIndicatorId'];
                $_POST['kpiTblId'] = $hiddenParams['id'];
                $_POST['isIgnoreRemoveRecordMap'] = 1;
                
                unset($_POST['hiddenParams']);
                
                $response = self::saveKpiDynamicData();
        
                if ($response['status'] == 'success' && $indicatorId = $response['indicatorId']) {
                    self::clearCacheData($indicatorId);
                }
                
                $response['hiddenParams'] = Crypt::encrypt(json_encode(array('mainIndicatorId' => $hiddenParams['mainIndicatorId'], 'id' => $response['rowId'])));

                jsonResponse($response);
            }
        } 
        
        jsonResponse(array('status' => 'error', 'message' => 'Invalid request!'));
    }
    
    public function mvWidgetGridRender() {
        
        if (Input::postCheck('hiddenParams') && Input::postCheck('mapId')) {
            
            $hiddenParams     = Input::post('hiddenParams');
            $postHiddenParams = $hiddenParams;
            
            $hiddenParams = Crypt::decrypt($hiddenParams);
            $hiddenParams = @json_decode($hiddenParams, true);
            
            if (isset($hiddenParams['mainIndicatorId']) && isset($hiddenParams['id'])) {
                
                $mapId = Input::numeric('mapId');
                $mapRow = $this->model->getKpiIndicatorMapByMapIdModel($mapId);
                
                $this->view->indicatorId = $mapRow['TRG_INDICATOR_ID'];
                $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
                
                $this->view->isAjax = is_ajax_request();
                $this->view->title = $this->lang->line($this->view->row['NAME']);
                $this->view->indicatorCode = $this->view->row['CODE'];
        
                $this->view->columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row);
                $fieldConfig = $this->model->getKpiIndicatorIdFieldModel($this->view->indicatorId, $this->view->columnsData);

                $this->view->idField = $fieldConfig['idField'];
                $this->view->nameField = $fieldConfig['nameField'];
                $this->view->parentField = $fieldConfig['parentField'];
                $this->view->coordinateField = $fieldConfig['coordinateField'];

                $this->view->isGridType = 'datagrid';
                $this->view->isTreeGridData = '';
                $this->view->isUseWorkflow = 0;
                $this->view->isFilterShowData = 0;

                $this->view->isDataMart = false; 
                $this->view->isCallWebService = false;
                $this->view->isRawDataMart = false;
                $this->view->isIgnoreFilter = true;
                $this->view->drillDownCriteria = '';

                if ($this->view->idField && $this->view->nameField && $this->view->parentField) {
                    $this->view->isGridType = 'treegrid';
                    $this->view->isTreeGridData = 'id='.$this->view->idField.'&name='.$this->view->nameField.'&parent='.$this->view->parentField;
                }

                $this->view->process = $this->model->getKpiIndicatorProcessWidgetModel($this->view->indicatorId, $mapId);
                $this->view->columns = $this->model->renderKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row['isCheckSystemTable'], $this->view->columnsData);
                
                $this->view->postHiddenParams = $postHiddenParams;
                $this->view->renderGrid = $this->view->renderPrint('kpi/indicator/renderGrid', self::$viewPath);

                $this->view->render('kpi/indicator/list', self::$viewPath); 
                exit;
            }
        }
        
        echo 'Invalid request!';
    }
    
    public function indicatorRowExport() {
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        try {
            
            $indicatorId = Input::numeric('indicatorId');
            $idField     = Input::post('idField');
            $selectedRow = Input::post('selectedRow');
            
            if (Mdform::$defaultTplSavedId = issetParam($selectedRow[$idField])) {
                
                $configData = $this->model->getKpiIndicatorTemplateModel($indicatorId, null, true);
                
                if (!$configData) {
                    throw new Exception('Invalid config!'); 
                }
                
                $configFirstRow = $configData[0];
                
                Mdform::$kpiDmMart = $this->model->getKpiIndicatorDetailDataModel($indicatorId, Mdform::$defaultTplSavedId);
            
                if (Mdform::$kpiDmMart['status'] != 'success') {
                    throw new Exception('Invalid row data!'); 
                }
                
                Mdform::$kpiDmMart = Mdform::$kpiDmMart['detailData'];
                $fileName = $configFirstRow['NAME'].' - '.Date::currentDate('YmdHi').'.xlsx';
                
                includeLib('Office/Excel/phpspreadsheet/vendor/autoload');
                
                $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $this->spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');
                
                $this->sIndex = array();
                $this->sheetName['mainSheet'] = 'Үндсэн';
                $this->sheetIndex['mainSheet'] = 0;
                $this->sIndex[$this->sheetName['mainSheet']] = 1;
                
                $this->spreadsheetArr['mainSheet'] = $this->spreadsheet->getActiveSheet();
                
                $this->spreadsheetArr['mainSheet']->setTitle($this->sheetName['mainSheet']);
                $this->spreadsheetArr['mainSheet']->getColumnDimension('A')->setWidth(40, 'pt');
                
                foreach (range('C', 'Z') as $setCol) { 
                    $this->spreadsheetArr['mainSheet']->getColumnDimension($setCol)->setWidth(25, 'pt');
                }
                
                $this->spreadsheet->setActiveSheetIndex(0);
                
                foreach ($configData as $k => $row) {
            
                    if (!$row['PARENT_ID']) {

                        unset($configData[$k]);

                        $id = $row['ID'];

                        $this->excelExportKpiIndicatorFields($indicatorId, $configData, $id, $row);

                        break;
                    }
                }
                
                $this->spreadsheet->setActiveSheetIndex(0);
                
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=true; path=/');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
                header('Content-Disposition: attachment;filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                
                ob_end_clean();

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Xlsx');
                $writer->save('php://output'); 
                
            } else {
                throw new Exception('Invalid row id!'); 
            }
            
        } catch (Exception $ex) {
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Set-Cookie: fileDownload=false; path=/');
            echo $ex->getMessage();
        }
    }
    
    public function excelExportKpiIndicatorFields($indicatorId, $configData, $parentId, $row) {
        
        foreach ($configData as $k => $arrRow) {
                    
            if ($arrRow['PARENT_ID'] == $parentId) {
                
                unset($configData[$k]);
                
                if ($arrRow['TAB_NAME_TOP'] == '') {
                    $sheetName = 'mainSheet';
                } else {
                    
                    $sheetName = $arrRow['TAB_NAME_TOP'];
                    
                    if (!isset($this->sheetName[$sheetName])) {

                        $this->spreadsheetArr[$sheetName] = $this->spreadsheet->createSheet();
                        $this->spreadsheetArr[$sheetName]->setTitle($sheetName);
                        
                        $this->sheetIndex[$sheetName] = count($this->sheetName);
                        $this->sheetName[$sheetName] = $sheetName;
                        $this->sIndex[$sheetName] = 1;
                        
                        $this->spreadsheetArr[$sheetName]->getColumnDimension('A')->setWidth(40, 'pt');
                        
                        foreach (range('C', 'Z') as $setCol) { 
                            $this->spreadsheetArr[$sheetName]->getColumnDimension($setCol)->setWidth(25, 'pt');
                        }
                    }
                }
                
                $this->spreadsheet->setActiveSheetIndex($this->sheetIndex[$sheetName]);

                if ($arrRow['SHOW_TYPE'] != 'label' && $arrRow['SHOW_TYPE'] != 'rows') {

                    $index = $this->sIndex[$this->sheetName[$sheetName]];

                    $this->spreadsheetArr[$sheetName]->setCellValue('A'.$index, $arrRow['LABEL_NAME'].':'); 
                    $this->spreadsheetArr[$sheetName]->getStyle('A'.$index)->getAlignment()->setWrapText(true)->setHorizontal('right');
                    $this->spreadsheetArr[$sheetName]->setCellValue('B'.$index, $this->model->getFieldValueFormatter($arrRow, Mdform::$kpiDmMart));

                    $this->sIndex[$this->sheetName[$sheetName]] ++;

                } elseif ($arrRow['SHOW_TYPE'] == 'label') {

                    $index = $this->sIndex[$this->sheetName[$sheetName]];

                    $this->spreadsheetArr[$sheetName]->setCellValue('A'.$index, $arrRow['LABEL_NAME']); 
                    $this->spreadsheetArr[$sheetName]->getStyle('A'.$index)->applyFromArray(array('font' => array('bold' => true)));

                    $this->sIndex[$this->sheetName[$sheetName]] ++;

                    $this->excelExportKpiIndicatorFields($indicatorId, $configData, $arrRow['ID'], $arrRow);

                } elseif ($arrRow['SHOW_TYPE'] == 'rows') {

                    $index = $this->sIndex[$this->sheetName[$sheetName]];

                    $this->spreadsheetArr[$sheetName]->setCellValue('A'.$index, $arrRow['LABEL_NAME']); 
                    $this->spreadsheetArr[$sheetName]->getStyle('A'.$index)->applyFromArray(array('font' => array('bold' => true)));
                    
                    $this->sIndex[$this->sheetName[$sheetName]] ++;
                    
                    /*if ($arrRow['FILTER_INDICATOR_ID'] && $arrRow['SEMANTIC_TYPE_NAME'] == 'Sub хүснэгт') {

                        $savedSubTableRows = $this->model->getKpiSubTableRowsModel($indicatorId, $arrRow['FILTER_INDICATOR_ID'], Mdform::$defaultTplSavedId, $arrRow['COLUMN_NAME']);

                        if ($savedSubTableRows) {
                            Mdform::$kpiDmMart[$arrRow['COLUMN_NAME'] . '_subTableRows'] = $savedSubTableRows;
                        }
                    }*/
                    
                    $this->excelExportKpiIndicatorRows($indicatorId, $configData, $arrRow['ID'], $arrRow, $sheetName);
                }
            }
        }
        
        return true;
    }
    
    public function excelExportKpiIndicatorRows($indicatorId, $configData, $parentId, $row, $sheetName) {
        
        $arr = array_filter($configData, function($ar) use($parentId) {
            return ($ar['PARENT_ID'] == $parentId);
        });
        
        if ($arr) {
            
            $isSavedDataJson = $isColumnAggregate = $isTemplateRows = false;
            $savedDataJson = $mergeRows = array();
            $allColumnCountMerge = 0;
            $parentColumnName = $row['COLUMN_NAME'];
            
            if (Mdform::$defaultTplSavedId && Mdform::$kpiDmMart) {
                
                if ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                    
                } elseif ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName . '_subTableRows'])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                }   
            }
            
            if ($row['TEMPLATE_TABLE_NAME']) {
                
                $templateRows = $this->model->getKpiIndicatorTemplateRows($row['TEMPLATE_TABLE_NAME']);
                $isTemplateRows = true;
            }
            
            $index = $this->sIndex[$this->sheetName[$sheetName]];
            $startIndex = $index;
            
            $this->spreadsheetArr[$sheetName]->setCellValue('B'.$index, '№'); 
            $n = 3;

            foreach ($arr as $k => $arrRow) {

                if ($arrRow['IS_RENDER'] == '1') {

                    if ($arrRow['MERGE_TYPE'] == 'row') {

                        $mergeRows[$arrRow['COLUMN_NAME']] = $arrRow;

                    } else {

                        if ($isTemplateRows == false && $arrRow['COLUMN_AGGREGATE'] != '') {
                            $isColumnAggregate = true;
                        } else {
                            $arrRow['COLUMN_AGGREGATE'] = '';
                        }

                        $this->spreadsheetArr[$sheetName]->setCellValue(numToAlpha($n).$index, Lang::line($arrRow['NAME'])); 
                        $this->spreadsheetArr[$sheetName]->getStyle(numToAlpha($n).$index)->getAlignment()->setWrapText(true)->setHorizontal('center');

                        $parentId = $arrRow['ID'];

                        $childArr = array_filter($configData, function($ar) use($parentId) {
                            return ($ar['PARENT_ID'] == $parentId);
                        });

                        $arr[$k]['childArr'] = $childArr;

                        $allColumnCountMerge ++;
                        $n ++;
                    }
                }
            }

            $this->spreadsheetArr[$sheetName]->getStyle('B'.$startIndex.':'.numToAlpha($n - 1).$startIndex)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C6EFCE');

            $this->sIndex[$this->sheetName[$sheetName]] ++;
            $i = 1;
                
            if ($isTemplateRows == true && $templateRows) {
                
                $isShowDescCol = array_key_exists('SHOW_DESC', $templateRows[0]) ? true : false;

                foreach ($templateRows as $t => $templateRow) {
                    
                    $c = 3;
                    $index = $this->sIndex[$this->sheetName[$sheetName]];
                    
                    $this->spreadsheetArr[$sheetName]->setCellValue('B'.$index, ''.$i); 

                    Mdform::$indicatorTemplateRow[$parentColumnName] = $templateRow;

                    $id = issetParam($templateRow['ID']);

                    foreach ($arr as $k => $arrRow) {

                        if ($arrRow['IS_RENDER'] == '1') {

                            if (isset($mergeRows[$arrRow['COLUMN_NAME']])) {

                                $rowValueId = $templateRow[$arrRow['COLUMN_NAME']];

                                if (!isset($mergeRows[$arrRow['COLUMN_NAME']][$rowValueId])) {
                                    
                                    $this->spreadsheetArr[$sheetName]->setCellValue(numToAlpha($c).$index, issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC'])); 
                                    $this->spreadsheetArr[$sheetName]->getStyle(numToAlpha($c).$index)->getAlignment()->setWrapText(true);

                                    $mergeRows[$arrRow['COLUMN_NAME']][$rowValueId] = 1;
                                    
                                    $c ++;

                                    continue 2;
                                }

                            } else {

                                $cellStyle = $mergeCell = $cellClass = '';

                                if (($arrRow['SHOW_TYPE'] == 'label' && $isShowDescCol) || $arrRow['IS_PARENT'] == '1') {

                                    if ($arrRow['IS_PARENT'] == '1') {
                                        $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                    } else {
                                        $showCellValue = $templateRow['SHOW_DESC'];
                                    }

                                    /*if ($templateRow['PARENT_ID']) {

                                        $cellStyle = self::getIndicatorCellStyle($templateRows, $templateRow['PARENT_ID']);

                                        if ($cellStyle == 1) {
                                            $cellStyle = 'padding-left: 35px;';
                                        } elseif ($cellStyle == 2) {
                                            $cellStyle = 'padding-left: 55px;';
                                        } elseif ($cellStyle == 3) {
                                            $cellStyle = 'padding-left: 75px;';
                                        } elseif ($cellStyle == 4) {
                                            $cellStyle = 'padding-left: 95px;';
                                        } elseif ($cellStyle == 5) {
                                            $cellStyle = 'padding-left: 115px;';
                                        } elseif ($cellStyle == 6) {
                                            $cellStyle = 'padding-left: 135px;';
                                        }

                                    } else {
                                        $cellStyle = 'font-weight: bold;';
                                    }*/

                                    $mergeCell = 'true';

                                } elseif ($arrRow['SEMANTIC_TYPE_NAME'] == 'Мөр') {

                                    $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                    $mergeCell = 'true';

                                } else {

                                    $arrRow['parentColumnName'] = $parentColumnName;

                                    $templateRow[$arrRow['COLUMN_NAME']] = null;

                                    if ($isSavedDataJson) {

                                        $savedRow = array();

                                        foreach ($savedDataJson as $savedDataRow) {
                                            if ((isset($savedDataRow['ROW_ID']) && $savedDataRow['ROW_ID'] == $id) || $savedDataRow['ID'] == $id) {
                                                $savedRow = $savedDataRow;
                                                break;
                                            }
                                        }

                                        if ($savedRow) {
                                            $templateRow[$arrRow['COLUMN_NAME']] = $savedRow[$arrRow['COLUMN_NAME']];
                                        }
                                    }
                                    
                                    $showCellValue = $this->model->getFieldValueFormatter($arrRow, $templateRow);
                                }
                                
                                $this->spreadsheetArr[$sheetName]->setCellValue(numToAlpha($c).$index, $showCellValue); 
                                $this->spreadsheetArr[$sheetName]->getStyle(numToAlpha($c).$index)->getAlignment()->setWrapText(true);
                                
                                $c ++;
                            }
                        }
                    }
                    
                    $this->sIndex[$this->sheetName[$sheetName]] ++;
                    $i ++;
                }
                
            } else {
                
                foreach ($savedDataJson as $k => $row) {
                    
                    $c = 3;
                    $index = $this->sIndex[$this->sheetName[$sheetName]];
                    
                    $this->spreadsheetArr[$sheetName]->setCellValue('B'.$index, ''.$i); 
                    
                    foreach ($arr as $k => $arrRow) {

                        if ($arrRow['IS_RENDER'] == '1' && $arrRow['COLUMN_NAME'] && $arrRow['MERGE_TYPE'] != 'row') {
                            
                            $this->spreadsheetArr[$sheetName]->setCellValue(numToAlpha($c).$index, $this->model->getFieldValueFormatter($arrRow, $row) ); 
                            $this->spreadsheetArr[$sheetName]->getStyle(numToAlpha($c).$index)->getAlignment()->setWrapText(true);
                            
                            $c ++;
                        }
                    }
                    
                    $this->sIndex[$this->sheetName[$sheetName]] ++;
                    $i ++;
                }
            }
            
            $this->spreadsheetArr[$sheetName]->getStyle('B'.$startIndex.':'.numToAlpha($n - 1).($this->sIndex[$this->sheetName[$sheetName]] - 1))
                ->applyFromArray(
                array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 
                            'color' => array('argb' => '000')
                        )
                    )
                )
            );
        }
        
        return true;
    }
    
    public function indicatorRowExcelExportOneLine() {
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        try {
            
            $indicatorId = Input::numeric('indicatorId');
            $idField     = Input::post('idField');
            $selectedRow = Input::post('selectedRow');
            
            if (Mdform::$defaultTplSavedId = issetParam($selectedRow[$idField])) {
                
                $configData = $this->model->getKpiIndicatorTemplateModel($indicatorId, null, true);
                
                if (!$configData) {
                    throw new Exception('Invalid config!'); 
                }
                
                $configFirstRow = $configData[0];
                
                Mdform::$kpiDmMart = $this->model->getKpiIndicatorDetailDataModel($indicatorId, Mdform::$defaultTplSavedId);
            
                if (Mdform::$kpiDmMart['status'] != 'success') {
                    throw new Exception('Invalid row data!'); 
                }
                
                $detailData = Mdform::$kpiDmMart['detailData'];
                $fileName = $configFirstRow['NAME'].' - '.Date::currentDate('YmdHi').'.xlsx';
                
                includeLib('Office/Excel/phpspreadsheet/vendor/autoload');
                
                $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $this->spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');
                
                $this->sIndex = array();
                $this->oneLineSheetColumnIndex = array();
                $this->oneLineTitleRowIndex = 2;
                $this->oneLineLabelRowIndex = $this->oneLineTitleRowIndex + 1;
                $this->oneLineValueRowIndex = $this->oneLineLabelRowIndex + 1;
                $mainTabName = 'Үндсэн';
                
                $this->spreadsheet->setActiveSheetIndex(0);
                
                if ($configFirstRow['KPI_TYPE_ID'] == '2013') {
                    $additionalInfo = $this->model->getIndicatorAdditionalInfoModel($configFirstRow['KPI_TYPE_ID'], $indicatorId);
                    
                    if (isset($additionalInfo['STRUCTURE_LIMIT']) && isset($additionalInfo['STRUCTURE_TAB_NAME'])) {
                        
                        $structureMap = $this->model->getIndicatorSemanticMapCountModel($indicatorId, 10000017);
                        
                        if ($structureMap) {
                            self::excelExportOneLineAddonStructureForm($indicatorId, $additionalInfo['STRUCTURE_LIMIT'], $structureMap, $additionalInfo['STRUCTURE_TAB_NAME']);
                        }
                    }
                    
                    if ($mainTabNameConfig = issetParam($additionalInfo['DEFAULT_TAB_NAME'])) {
                        $mainTabName = $mainTabNameConfig;
                    }
                }
                
                Mdform::$kpiDmMart = $detailData;
                
                $this->sheetIndex['mainSheet'] = count($this->sheetName);
                $this->sheetName['mainSheet'] = $mainTabName;
                $this->sIndex[$this->sheetName['mainSheet']] = 1;
                
                $this->oneLineSheetColumnIndex[$this->sheetName['mainSheet']] = 1;
                
                if ($this->sheetIndex['mainSheet'] > 0) {
                    
                    $this->spreadsheetArr['mainSheet'] = $this->spreadsheet->createSheet();
                    $this->spreadsheetArr['mainSheet']->setTitle($this->sheetName['mainSheet']);
                }
                
                $this->spreadsheet->setActiveSheetIndex($this->sheetIndex['mainSheet']);
                
                $this->spreadsheetArr['mainSheet'] = $this->spreadsheet->getActiveSheet();
                $this->spreadsheetArr['mainSheet']->setTitle($this->sheetName['mainSheet']);
                
                foreach ($configData as $k => $row) {
            
                    if (!$row['PARENT_ID']) {

                        unset($configData[$k]);

                        $id = $row['ID'];

                        $this->excelExportOneLineKpiIndicatorFields($indicatorId, $configData, $id, $row, 'mainSheet');

                        break;
                    }
                }
                
                if ($this->oneLineMergeCells) {
                            
                    foreach ($this->oneLineMergeCells as $sheetName => $cells) {
                        
                        if (count($cells) > 1) {
                            
                            $this->spreadsheet->setActiveSheetIndex($this->sheetIndex[$sheetName]);
                            
                            $checkMerge = $checkMergeLoop = array();
                            $c = $t = 0;
                            
                            foreach ($cells as $cellName => $cellVal) {
                                
                                $checkMergeLoop[$c] = array('cellVal' => $cellVal, 'cellName' => $cellName);
                                
                                if ($c == 0) {
                                    $checkMerge[$t] = array('start' => $cellName);
                                    $t ++;
                                } elseif ($checkMergeLoop[$c - 1]['cellVal'] != $cellVal) {
                                    $checkMerge[$t - 1]['end'] = $checkMergeLoop[$c - 1]['cellName'];
                                    $checkMerge[$t] = array('start' => $cellName);
                                    $t ++;
                                } 
                                
                                $c ++;
                            }
                            
                            $checkMerge[$t - 1]['end'] = $cellName;
                            
                            if ($checkMerge) {
                                foreach ($checkMerge as $setMerge) {
                                    if (isset($setMerge['end'])) {
                                        
                                        $start = $setMerge['start'];
                                        $end = $setMerge['end'];

                                        if ($start != $end) {
                                            $this->spreadsheet->getActiveSheet()->mergeCells($start.':'.$end);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                foreach ($this->sheetIndex as $shName => $shIndex) {
                    
                    if (!isset($this->oneLineMergeCells[$shName])) {
                        $this->spreadsheet->removeSheetByIndex($shIndex);
                        continue;
                    }
                    
                    $this->spreadsheet->setActiveSheetIndex($shIndex);
                    
                    $cells = $this->oneLineMergeCells[$shName];
                    $firstKey = array_key_first($cells);
                    $lastKey = array_key_last($cells);
                    
                    $onlyFirstAlpha = preg_replace("/[^A-Z]+/", "", $firstKey);
                    $onlyLastAlpha = preg_replace("/[^A-Z]+/", "", $lastKey);
                    
                    $s = alphaToNum($onlyFirstAlpha);
                    $e = alphaToNum($onlyLastAlpha);
                    
                    for ($i = $s; $i <= $e; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(numToAlpha($i))->setWidth(25, 'pt');
                    }
                    
                    $this->spreadsheet->getActiveSheet()->getStyle($firstKey.':'.$lastKey)
                        ->applyFromArray(
                            array(
                                'font' => array('bold' => true), 
                                'alignment' => array(
                                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                                ),
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => array(
                                        'argb' => 'DDEBF7'
                                    )
                                )
                            )
                        );
                    
                    $this->spreadsheet->getActiveSheet()->getStyle($onlyFirstAlpha.$this->oneLineLabelRowIndex.':'.$onlyLastAlpha.$this->oneLineLabelRowIndex)
                        ->applyFromArray(
                            array(
                                'alignment' => array(
                                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                ),
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => array(
                                        'argb' => 'C6EFCE'
                                    )
                                )
                            )
                        );
                    
                    $this->spreadsheet->getActiveSheet()->getStyle($firstKey.':'.$onlyLastAlpha.$this->oneLineValueRowIndex)
                        ->applyFromArray(
                        array(
                            'borders' => array(
                                'allBorders' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 
                                    'color' => array('argb' => '000')
                                )
                            )
                        )
                    );
                    
                    $this->spreadsheet->getActiveSheet()->getStyle($firstKey.':'.$onlyLastAlpha.$this->oneLineValueRowIndex)->getAlignment()->setWrapText(true);
                }

                $this->spreadsheet->setActiveSheetIndex(0);
                
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Set-Cookie: fileDownload=true; path=/');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
                header('Content-Disposition: attachment;filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                
                ob_end_clean();

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Xlsx');
                $writer->save('php://output'); 
                
            } else {
                throw new Exception('Invalid row id!'); 
            }
            
        } catch (Exception $ex) {
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Set-Cookie: fileDownload=false; path=/');
            echo $ex->getMessage();
        }
    }
    
    public function excelExportOneLineKpiIndicatorFields($indicatorId, $configData, $parentId, $row, $mainSheetName, $parentName = null) {
        
        foreach ($configData as $k => $arrRow) {
                    
            if ($arrRow['PARENT_ID'] == $parentId) {
                
                unset($configData[$k]);
                
                if ($arrRow['TAB_NAME_TOP'] == '') {
                    $sheetName = $mainSheetName;
                } else {
                    
                    $sheetName = $arrRow['TAB_NAME_TOP'];
                    
                    if (!isset($this->sheetName[$sheetName])) {

                        $this->spreadsheetArr[$sheetName] = $this->spreadsheet->createSheet();
                        $this->spreadsheetArr[$sheetName]->setTitle($sheetName);
                        
                        $this->sheetIndex[$sheetName] = count($this->sheetName);
                        $this->sheetName[$sheetName] = $sheetName;
                        $this->sIndex[$sheetName] = 1;
                        $this->oneLineSheetColumnIndex[$sheetName] = 1;
                    }
                }
                
                $this->spreadsheet->setActiveSheetIndex($this->sheetIndex[$sheetName]);

                if ($arrRow['SHOW_TYPE'] != 'label' && $arrRow['SHOW_TYPE'] != 'rows') {

                    $colIndex = numToAlpha($this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]]);
                    $title = $parentName ? $parentName : $this->sheetName[$sheetName];
                    
                    $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineTitleRowIndex, $title); 
                    $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineLabelRowIndex, $arrRow['LABEL_NAME']);
                    $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineValueRowIndex, $this->model->getFieldValueFormatter($arrRow, Mdform::$kpiDmMart));
                    
                    $this->oneLineMergeCells[$sheetName][$colIndex.$this->oneLineTitleRowIndex] = $title;
                    
                    $this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]] ++;

                } elseif ($arrRow['SHOW_TYPE'] == 'label') {

                    $this->excelExportOneLineKpiIndicatorFields($indicatorId, $configData, $arrRow['ID'], $arrRow, $mainSheetName, $arrRow['LABEL_NAME']);

                } elseif ($arrRow['SHOW_TYPE'] == 'rows') {

                    $this->excelExportOneLineKpiIndicatorRows($indicatorId, $configData, $arrRow['ID'], $arrRow, $sheetName, $parentName);
                }
            }
        }
        
        return true;
    }
    
    public function excelExportOneLineKpiIndicatorRows($indicatorId, $configData, $parentId, $row, $sheetName, $parentName) {
        
        $arr = array_filter($configData, function($ar) use($parentId) {
            return ($ar['PARENT_ID'] == $parentId);
        });
        
        if ($arr) {
            
            $isSavedDataJson = $isColumnAggregate = $isTemplateRows = false;
            $savedDataJson = $mergeRows = array();
            $allColumnCountMerge = 0;
            $n = 1;
            $parentColumnName = $row['COLUMN_NAME'];
            $labelName = html_entity_decode($row['LABEL_NAME']);
            
            if (Mdform::$defaultTplSavedId && Mdform::$kpiDmMart) {
                
                if ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                    
                } elseif ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName . '_subTableRows'])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                }   
            }
            
            if ($row['TEMPLATE_TABLE_NAME']) {
                
                $templateRows = $this->model->getKpiIndicatorTemplateRows($row['TEMPLATE_TABLE_NAME']);
                $isTemplateRows = true;
            }

            foreach ($arr as $k => $arrRow) {

                if ($arrRow['IS_RENDER'] == '1') {

                    if ($arrRow['MERGE_TYPE'] == 'row') {

                        $mergeRows[$arrRow['COLUMN_NAME']] = $arrRow;

                    } else {

                        if ($isTemplateRows == false && $arrRow['COLUMN_AGGREGATE'] != '') {
                            $isColumnAggregate = true;
                        } else {
                            $arrRow['COLUMN_AGGREGATE'] = '';
                        }

                        $parentId = $arrRow['ID'];

                        $childArr = array_filter($configData, function($ar) use($parentId) {
                            return ($ar['PARENT_ID'] == $parentId);
                        });

                        $arr[$k]['childArr'] = $childArr;

                        $allColumnCountMerge ++;
                    }
                }
            }
                
            if ($isTemplateRows == true && $templateRows) {
                
                $isShowDescCol = array_key_exists('SHOW_DESC', $templateRows[0]) ? true : false;

                foreach ($templateRows as $t => $templateRow) {
                    
                    $c = 3;

                    Mdform::$indicatorTemplateRow[$parentColumnName] = $templateRow;

                    $id = issetParam($templateRow['ID']);

                    foreach ($arr as $k => $arrRow) {

                        if ($arrRow['IS_RENDER'] == '1') {

                            if (isset($mergeRows[$arrRow['COLUMN_NAME']])) {

                                $rowValueId = $templateRow[$arrRow['COLUMN_NAME']];

                                if (!isset($mergeRows[$arrRow['COLUMN_NAME']][$rowValueId])) {
                                    
                                    //$this->spreadsheetArr[$sheetName]->setCellValue(numToAlpha($c).$index, issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC'])); 

                                    $mergeRows[$arrRow['COLUMN_NAME']][$rowValueId] = 1;
                                    
                                    $c ++;

                                    continue 2;
                                }

                            } else {

                                $cellStyle = $mergeCell = $cellClass = '';

                                if (($arrRow['SHOW_TYPE'] == 'label' && $isShowDescCol) || $arrRow['IS_PARENT'] == '1') {

                                    if ($arrRow['IS_PARENT'] == '1') {
                                        $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                    } else {
                                        $showCellValue = $templateRow['SHOW_DESC'];
                                    }

                                    $mergeCell = 'true';

                                } elseif ($arrRow['SEMANTIC_TYPE_NAME'] == 'Мөр') {

                                    $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                    $mergeCell = 'true';

                                } else {

                                    $arrRow['parentColumnName'] = $parentColumnName;
                                    $templateRow[$arrRow['COLUMN_NAME']] = null;

                                    if ($isSavedDataJson) {

                                        $savedRow = array();

                                        foreach ($savedDataJson as $savedDataRow) {
                                            if ((isset($savedDataRow['ROW_ID']) && $savedDataRow['ROW_ID'] == $id) || $savedDataRow['ID'] == $id) {
                                                $savedRow = $savedDataRow;
                                                break;
                                            }
                                        }

                                        if ($savedRow) {
                                            $templateRow[$arrRow['COLUMN_NAME']] = $savedRow[$arrRow['COLUMN_NAME']];
                                        }
                                    }
                                    
                                    $showCellValue = $this->model->getFieldValueFormatter($arrRow, $templateRow);
                                }
                                
                                $colIndex = numToAlpha($this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]]);
                                
                                $titleName = ($parentName ? $parentName.' / ' : '').$labelName.' '.$n;
                                
                                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineTitleRowIndex, $titleName); 
                                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineLabelRowIndex, html_entity_decode($arrRow['LABEL_NAME']));
                                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineValueRowIndex, html_entity_decode($showCellValue));
                                
                                $this->oneLineMergeCells[$sheetName][$colIndex.$this->oneLineTitleRowIndex] = $titleName;
                                
                                $this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]] ++;
                                
                                $c ++;
                            }
                        }
                    }
                    
                    $n ++;
                }
                
            } else {
                
                if ($row['ROW_COUNT_LIMIT']) {
                    $rowCountLimit = $row['ROW_COUNT_LIMIT'];
                } elseif ($rowCountLimitCount = count($savedDataJson)) {
                    $rowCountLimit = $rowCountLimitCount;
                } else {
                    $rowCountLimit = 1;
                }
                
                for ($i = 0; $i < $rowCountLimit; $i++) {
                    
                    foreach ($arr as $k => $arrRow) {

                        if ($arrRow['IS_RENDER'] == '1' && $arrRow['COLUMN_NAME'] && $arrRow['MERGE_TYPE'] != 'row') {
                            
                            $colIndex = numToAlpha($this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]]);
                            $rowData = issetParamArray($savedDataJson[$i]);
                            $titleName = ($parentName ? $parentName.' / ' : '').$labelName.' '.$n;
                            
                            $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineTitleRowIndex, $titleName); 
                            $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineLabelRowIndex, html_entity_decode($arrRow['LABEL_NAME']));
                            $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineValueRowIndex, $this->model->getFieldValueFormatter($arrRow, $rowData));
                            
                            $this->oneLineMergeCells[$sheetName][$colIndex.$this->oneLineTitleRowIndex] = $titleName;
                            
                            $this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]] ++;
                        }
                    }
                    
                    $n ++;
                }
            }
        }
        
        return true;
    }
    
    public function excelExportOneLineAddonStructureForm($srcIndicatorId, $structureLimit, $structureMap, $sheetName) {
        
        $selectedIds = $this->model->getIndicatorMapBySemanticSavedIdsAllModel($srcIndicatorId, Mdform::$defaultTplSavedId, $structureMap);
        
        $this->sheetIndex[$sheetName] = count($this->sheetName);
        $this->sheetName[$sheetName] = $sheetName;
        $this->sIndex[$this->sheetName[$sheetName]] = 1;

        $this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]] = 1;

        $this->spreadsheetArr[$sheetName] = $this->spreadsheet->getActiveSheet();
        $this->spreadsheetArr[$sheetName]->setTitle($this->sheetName[$sheetName]);
        
        $structureSaved = $structureConfigData = $structureCheck = array();
        $s = 1;
        
        foreach ($selectedIds as $selectedRow) {
            if ($selectedRow['TRG_RECORD_ID'] != '') {
                $structureSaved[$s] = array('name' => $selectedRow['NAME'], 'parentName' => $selectedRow['PARENT_NAME']);
                $s ++;
            }
        }
        
        for ($s = 1; $s <= $structureLimit; $s++) {
            
            for ($z = 1; $z <= 2; $z++) {
                $colIndex = numToAlpha($this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]]);
                
                if ($z == 1) {
                    $subTitle = 'Үндсэн сонголт';
                    $structureName = issetParam($structureSaved[$s]['parentName']);
                } else {
                    $subTitle = 'Дэд сонголт';
                    $structureName = issetParam($structureSaved[$s]['name']);
                }
                
                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineTitleRowIndex, $sheetName.' '.$s); 
                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineLabelRowIndex, $subTitle);
                $this->spreadsheetArr[$sheetName]->setCellValue($colIndex.$this->oneLineValueRowIndex, $structureName);

                $this->oneLineMergeCells[$sheetName][$colIndex.$this->oneLineTitleRowIndex] = $sheetName.' '.$s;

                $this->oneLineSheetColumnIndex[$this->sheetName[$sheetName]] ++;
            }
        }
        
        for ($s = 1; $s <= $structureLimit; $s++) {
            
            foreach ($selectedIds as $key => $selectedRow) {

                $indicatorId = $selectedRow['ID'];
                
                if (isset($structureCheck[$s][$indicatorId])) {
                    
                    unset($selectedIds[$structureCheck[$s][$indicatorId]]);

                } else {
                
                    if (!isset($structureConfigData[$indicatorId])) {
                        $configData = $this->model->getKpiIndicatorTemplateModel($indicatorId);

                        if (!$configData) {
                            throw new Exception('Invalid config!'); 
                        } 

                        $structureConfigData[$indicatorId] = $configData;
                    } else {
                        $configData = $structureConfigData[$indicatorId];
                    }

                    Mdform::$defaultTplSavedId = $selectedRow['TRG_RECORD_ID'];
                    Mdform::$kpiDmMart = $this->model->getKpiIndicatorDetailDataModel($indicatorId, Mdform::$defaultTplSavedId);

                    $selectedIds[$key]['TRG_RECORD_ID'] = null;

                    if (Mdform::$kpiDmMart['status'] == 'success') {
                        Mdform::$kpiDmMart = Mdform::$kpiDmMart['detailData'];
                    } else {
                        Mdform::$kpiDmMart = array();
                    }        

                    $dataFirstRow = $configData[0];

                    foreach ($configData as $k => $row) {

                        if (!$row['PARENT_ID']) {

                            unset($configData[$k]);

                            $id = $row['ID'];

                            $this->excelExportOneLineKpiIndicatorFields($indicatorId, $configData, $id, $row, $sheetName, $dataFirstRow['NAME']);

                            break;
                        }
                    }

                    $structureCheck[$s][$indicatorId] = $key;
                }
            }
        }
        
        return true;
    }
    
    public function callWebservice() {
        $postData = Input::postData();
        
        $this->load->model('mdwebservice', 'middleware/models/');
        $response = $this->model->execProcessModel($postData);
        
        if (issetParam($response['status']) == 'success' && $indicatorId = issetVar($postData['paramData'][0]['value'])) {
            self::clearCacheData($indicatorId);
        }
        
        echo json_encode($response);
    }
    
    public function getColumnDrillDownConfig() {
        $response = $this->model->getColumnDrillDownConfigModel();
        echo json_encode($response);
    }
    
    public function updateJsonKpiIndicatorMap() {
        $response = $this->model->updateJsonKpiIndicatorMapModel();
        echo json_encode($response); exit;
    }
    
    public function getJsonKpiIndicatorMap() {
        $response = $this->model->getJsonKpiIndicatorMapModel();
        echo json_encode($response); exit;
    }    
    
    public function kpiDataMartRelationConfig3() {
        $this->view->id = Input::numeric('id');
        $this->view->mainId = Input::numeric('mainId');
        $this->view->idIndicatorId = $this->model->getIndicatorModel(Input::numeric('idIndicatorId'));
        $this->view->idIndicatorId = $this->view->idIndicatorId['PARENT_ID'];
        
        if ($this->view->id) {
            
            $this->view->columns = $this->model->getKpiDataMartRelationColumnsModel($this->view->id);
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $objects = $this->model->getDataMartGetDataModel('data_dataModelGetDV_004', array('id' => $this->view->id));
            
            $response = array(
                'html'      => $this->view->renderPrint('form/kpi/indicator/relation/relationConfig3', 'middleware/views/'), 
                'status'    => 'success', 
                'objects'   => $objects
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        jsonResponse($response);
    }    
    
    public function renderMetaProcessRelationTab() {
        
        $this->view->refStructureId = Input::numeric('refStructureId');
        $this->view->sourceId = Input::numeric('sourceId');
        $this->view->processId = Input::numeric('processId');
        
        $result = $this->model->getMetaProcessRecordRelationModel($this->view->refStructureId, $this->view->sourceId, $this->view->processId);
        
        $this->view->isUserControl = $result['isUserControl'];
        $this->view->savedRows = $result['data'];
        
        $this->view->render('kpi/indicator/metaprocess/relationTab', self::$viewPath); 
    }
    
    public function bpRelationRemoveRow() {
        $response = $this->model->bpRelationRemoveRowModel();
        echo json_encode($response);
    }
    
    public function getIndicatorMapBySemantic() {
        
        $indicatorId    = Input::numeric('indicatorId');
        $semanticTypeId = Input::numeric('semanticTypeId');
        
        $response = $this->model->getKpiIndicatorMapModel($indicatorId, $semanticTypeId);
        array_walk($response, function(&$value) { unset($value['TABLE_NAME']); unset($value['SRC_TABLE_NAME']); }); 
                            
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function addonStructureForm($srcIndicatorId = '', $structureMap = array()) {
        
        $response = $selectedIds = array();
        $postIds = Input::post('selectedIds');
        $rowIndex = '[addonForm]';
        
        if ($postIds) {
            
            $isJson = true;
            $postRowIndex = Input::numeric('rowIndex');
            
            if ($postRowIndex != '') {
                $subFormKeyName = Input::post('subFormKeyName');
                $rowIndex = '['.$subFormKeyName.']['.$postRowIndex.']';
            }
            
            foreach ($postIds as $selectedId) {
                $selectedIds[] = array('ID' => $selectedId, 'TRG_RECORD_ID' => Input::numeric('savedRecordId'));
            }
            
        } elseif (Mdform::$defaultTplSavedId && $srcIndicatorId && $structureMap) {
            $selectedIds = $this->model->getIndicatorMapBySemanticSavedIdsModel($srcIndicatorId, Mdform::$defaultTplSavedId, $structureMap);
        }
        
        foreach ($selectedIds as $selectedRow) {
            
            $selectedId = $selectedRow['ID'];
            $data = $this->model->getKpiIndicatorTemplateModel($selectedId);
            
            if ($data) {
                
                $dataFirstRow = $data[0];          
                self::$subUniqId = getUID();
                self::$addonPathPrefix = $rowIndex.'['.$selectedId.'_'.self::$subUniqId.']';
                
                if ($dataFirstRow['LABEL_WIDTH']) {
                    self::$labelWidth = $dataFirstRow['LABEL_WIDTH'];
                }
                
                self::$tabRender = array();
                self::$topTabRender = array();
                self::$indicatorColExpression = array();
                self::$indicatorCellExpression = array();
                self::$indicatorHdrExpression = array();
                self::$addonId = 1;
                
                Mdform::$defaultTplSavedId = $selectedRow['TRG_RECORD_ID'];
                
                $form = $this->model->renderKpiIndicatorTemplateModel($selectedId, $dataFirstRow['TABLE_NAME'], $data);  
                
                $this->view->subUniqId = self::$subUniqId;
                
                $this->view->addonScripts = self::fncIndicatorColExpression($this->view->subUniqId, $selectedId, Mdform::$indicatorColExpression);
                $this->view->addonScripts .= self::fncIndicatorCellExpression($this->view->subUniqId, $selectedId, Mdform::$indicatorCellExpression);
                $this->view->addonScripts .= self::fncIndicatorHdrExpression($this->view->subUniqId, $selectedId, Mdform::$indicatorHdrExpression);
                
                $this->view->addonFullExp = self::indicatorFullExpression($this->view->subUniqId, $selectedId, '2013');
                
                $scripts = $this->view->renderPrint('kpi/indicator/recordmap/addonStructureScript', self::$viewPath); 
                
                $response[] = array(
                    'id'       => $selectedId,
                    'recordId' => $selectedRow['TRG_RECORD_ID'],
                    'uniqId'   => self::$subUniqId,
                    'name'     => $this->lang->line($dataFirstRow['NAME']),
                    'form'     => $form . $scripts
                );
            }
        }
        
        if (isset($isJson)) {
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            return $response;
        }
    }
    
    public function lookupAutoComplete() {
        
        $type = Input::post('type');
        $lookupId = Input::numeric('lookupId');
        $processId = Input::numeric('processId');
        $paramRealPath = Input::post('paramRealPath');
        
        $attributes = $this->model->getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $lookupId, 'TRG_TABLE_NAME' => null, 'isData' => false));

        if ($type == 'code') {
            if (isset($attributes['code']) && $attributes['code']) {
                
                $q = Input::post('q');
                $q = trim(str_replace('_', '', str_replace('_-_', '', $q)));
                
                $idField = $attributes['id'];
                $codeField = $attributes['code'];
                $nameField = $attributes['name'];
                        
                $criteria[$codeField][] = array(
                    'operator' => 'LIKE',
                    'operand' => Str::filterLikePos($q, '*', 'r')
                );
                
                if (Input::isEmpty('criteriaParams') == false) {
                    
                    $requestParams = urldecode(Input::post('criteriaParams'));
                    parse_str($requestParams, $criteriaParams);
                    
                    foreach ($criteriaParams as $k => $v) {
                        
                        if (is_array($v)) {
                            
                            $criteria[$k][] = array(
                                'operator' => 'IN', 
                                'operand' => Arr::implode_r(',', $v, true) 
                            );
                            
                        } elseif ($v != '') {
                            
                            if (strpos($v, '^') === false) {
                                $criteria[$k][] = array(
                                    'operator' => '=',
                                    'operand' => Input::param($v)
                                );
                            } else {
                                $criteria[$k][] = array(
                                    'operator' => 'IN',
                                    'operand' => Arr::implode_r(',', explode('^', $v), true)
                                );
                            }
                        }
                    }
                }
                
                if (Input::post('linkedPopup') === 'OK') {
                    
                    parse_str(urldecode(Input::post('params')), $cardFilterData);
                    
                    if (count($cardFilterData) > 0) {
                        
                        foreach ($cardFilterData as $key => $val) {
                            if (!empty($val)) {
                                $criteria[$key][] = array(
                                    'operator' => '=',
                                    'operand' => Input::param($val)
                                );
                            }
                        }
                    }
                } 

                $result = $this->model->getRowsIndicatorByCriteriaModel($lookupId, $criteria, $idField, $codeField, $nameField);
                        
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                
            } else {
                echo json_encode(array());
            }
            
        } else {
            
            if (isset($attributes['name']) && $attributes['name']) {
                
                $q = Input::post('q');
                
                $idField = $attributes['id'];
                $codeField = $attributes['code'];
                $nameField = $attributes['name'];
                        
                $criteria[$nameField][] = array(
                    'operator' => 'LIKE',
                    'operand' => Str::filterLikePos($q, '*', 'b')
                );
                
                if (Input::isEmpty('criteriaParams') == false) {
                    
                    $requestParams = urldecode(Input::post('criteriaParams'));
                    parse_str($requestParams, $criteriaParams);
                    
                    foreach ($criteriaParams as $k => $v) {
                        
                        if (is_array($v)) {
                            
                            $criteria[$k][] = array(
                                'operator' => 'IN', 
                                'operand' => Arr::implode_r(',', $v, true) 
                            );
                            
                        } elseif ($v != '') {
                            
                            if (strpos($v, '^') === false) {
                                $criteria[$k][] = array(
                                    'operator' => '=',
                                    'operand' => Input::param($v)
                                );
                            } else {
                                $criteria[$k][] = array(
                                    'operator' => 'IN',
                                    'operand' => Arr::implode_r(',', explode('^', $v), true)
                                );
                            }
                        } 
                    }
                }
                
                if (Input::post('linkedPopup') == 'OK') {
                    
                    parse_str(urldecode(Input::post('params')), $cardFilterData);
                    
                    if (count($cardFilterData) > 0) {
                        foreach ($cardFilterData as $key => $val) {
                            if (!empty($val)) {
                                $criteria[$key][] = array(
                                    'operator' => '=',
                                    'operand' => Input::param($val)
                                );
                            }
                        }
                    }
                }        
                
                $result = $this->model->getRowsIndicatorByCriteriaModel($lookupId, $criteria, $idField, $codeField, $nameField);
                        
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array());
            }
        }
    }
    
    public function autoCompleteById() {

        $processMetaDataId = Input::numeric('processMetaDataId');
        $loopupMetaId = Input::numeric('lookupId');
        $paramRealPath = Input::post('paramRealPath');
        $code = !is_array(Input::post('code')) ? Str::lower(Input::post('code')) : '';
        
        $row = array();
        $isName = $isId = false;
        $isValueNotEmpty = true;

        if ($code == '') {
            $isValueNotEmpty = false;
        }

        if ($loopupMetaId != '' && $isValueNotEmpty) {

            if (Input::postCheck('isName')) {
                $isNamePost = Input::post('isName');
                if ($isNamePost == 'true') {
                    $isName = true;
                }
                if ($isNamePost == 'idselect') {
                    $isId = true;
                }
            }
            
            $attributes = $this->model->getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $loopupMetaId, 'TRG_TABLE_NAME' => null, 'isData' => false));
            unset($attributes['data']);
            
            if ($isId) {

                if (isset($attributes['id']) && $attributes['id']) {

                    $idField = $attributes['id'];

                    $criteria[$idField][] = array(
                        'operator' => '=',
                        'operand' => $code
                    );

                    if (Input::isEmpty('params') === false) {
                        parse_str(urldecode(Input::post('params')), $inputParamArr);
                        foreach ($inputParamArr as $key => $rowVal) {
                            
                            if (is_array($rowVal)) {
                                
                                $criteria[$key][] = array(
                                    'operator' => 'IN', 
                                    'operand' => Arr::implode_r(',', $rowVal, true) 
                                );
                                
                            } elseif ($rowVal != '') {
                                
                                if (strpos($rowVal, '^') === false) {
                                    $criteria[$key][] = array(
                                        'operator' => '=',
                                        'operand' => $rowVal
                                    );
                                } else {
                                    $criteria[$key][] = array(
                                        'operator' => 'IN',
                                        'operand' => Arr::implode_r(',', explode('^', $rowVal), true)
                                    );
                                }
                            }
                        }
                    }
                    
                    $result = $this->model->getRowIndicatorByCriteriaModel($loopupMetaId, $criteria);

                    if ($result) {
                        
                        $codeField = $attributes['code'];
                        $nameField = $attributes['name'];

                        $row = array(
                            'META_VALUE_ID' => ($idField ? $result[$idField] : (isset($result['ID']) ? $result['ID'] : '')),
                            'META_VALUE_CODE' => (isset($result[$codeField]) ? html_entity_decode($result[$codeField], ENT_QUOTES, 'UTF-8') : ''),
                            'META_VALUE_NAME' => (isset($result[$nameField]) ? html_entity_decode($result[$nameField], ENT_QUOTES, 'UTF-8') : ''), 
                            'rowData' => $result
                        );
                    }
                }

            } else {
                if ($isName) {
                    if (isset($attributes['name']) && $attributes['name']) {

                        $idField = $attributes['id'];
                        $nameField = $attributes['name'];

                        $criteria[$nameField][] = array(
                            'operator' => '=',
                            'operand' => $code
                        );

                        if (Input::isEmpty('params') === false) {
                            
                            parse_str(urldecode(Input::post('params')), $inputParamArr);
                            
                            foreach ($inputParamArr as $key => $rowVal) {
                                
                                if (is_array($rowVal)) {
                                    $criteria[$key][] = array(
                                        'operator' => 'IN', 
                                        'operand' => Arr::implode_r(',', $rowVal, true) 
                                    );
                                } elseif ($rowVal != '') {
                                    
                                    if (strpos($rowVal, '^') === false) {
                                        $criteria[$key][] = array(
                                            'operator' => '=',
                                            'operand' => $rowVal
                                        );
                                    } else {
                                        $criteria[$key][] = array(
                                            'operator' => 'IN',
                                            'operand' => Arr::implode_r(',', explode('^', $rowVal), true)
                                        );
                                    }
                                }
                            }
                        }

                        $result = $this->model->getRowIndicatorByCriteriaModel($loopupMetaId, $criteria);

                        if ($result) {
                            $codeField = $attributes['code'];

                            $row = array(
                                'META_VALUE_ID' => ($idField ? $result[$idField] : (isset($result['ID']) ? $result['ID'] : '')),
                                'META_VALUE_CODE' => (isset($result[$codeField]) ? html_entity_decode($result[$codeField], ENT_QUOTES, 'UTF-8') : ''),
                                'META_VALUE_NAME' => (isset($result[$nameField]) ? html_entity_decode($result[$nameField], ENT_QUOTES, 'UTF-8') : ''), 
                                'rowData' => $result
                            );
                        }
                    }

                } else {

                    if (isset($attributes['code']) && $attributes['code']) {

                        $idField = $attributes['id'];
                        $codeField = $attributes['code'];

                        $criteria[$codeField][] = array(
                            'operator' => '=',
                            'operand' => $code
                        );

                        if (Input::isEmpty('params') === false) {
                            parse_str(urldecode(Input::post('params')), $inputParamArr);

                            foreach ($inputParamArr as $key => $rowVal) {
                                
                                if (is_array($rowVal)) {
                                    $criteria[$key][] = array(
                                        'operator' => 'IN', 
                                        'operand' => Arr::implode_r(',', $rowVal, true) 
                                    );
                                } elseif ($rowVal != '') {
                                    
                                    if (strpos($rowVal, '^') === false) {
                                        $criteria[$key][] = array(
                                            'operator' => '=',
                                            'operand' => $rowVal 
                                        );
                                    } else {
                                        $criteria[$key][] = array(
                                            'operator' => 'IN',
                                            'operand' => Arr::implode_r(',', explode('^', $rowVal), true)
                                        );
                                    }
                                }
                            }
                        }

                        $result = $this->model->getRowIndicatorByCriteriaModel($loopupMetaId, $criteria);

                        if ($result) {
                            $nameField = $attributes['name'];

                            $row = array(
                                'META_VALUE_ID' => $idField ? $result[$idField] : (isset($result['ID']) ? $result['ID'] : ''),
                                'META_VALUE_CODE' => isset($result[$codeField]) ? html_entity_decode($result[$codeField], ENT_QUOTES, 'UTF-8') : '',
                                'META_VALUE_NAME' => isset($result[$nameField]) ? html_entity_decode($result[$nameField], ENT_QUOTES, 'UTF-8') : '', 
                                'rowData' => $result
                            );
                        }
                    }
                }
            }
        }

        if ($row) {
            $row['attributes'] = $attributes;
            echo json_encode($row, JSON_UNESCAPED_UNICODE);
        } else {
            $response = array('META_VALUE_ID' => '', 'META_VALUE_CODE' => '', 'META_VALUE_NAME' => '');
            echo json_encode($response);
        }
    }

    public function kpiPortLocationVisual($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $id = Input::post('id');
        $linkId = Input::post('linkId');
        $this->view->linkRecordId = Input::post('linkRecordId');
        $this->view->indicatorName = '';
        $isReturnArray = false;       
        
        $this->view->isAjax = is_ajax_request();
        $this->view->uniqId = getUID();
        $this->view->row = $this->model->getKpiIndicatorRowModel($id);
        $this->view->getRow = $this->model->getIndicatorAdditionalInfoModel(1180, $id);
        $this->view->getRow['GRAPH_JSON'] = $this->view->row['GRAPH_JSON'];
        
        if ($linkId) {
            $this->view->row2 = $this->model->getKpiIndicatorRowModel($linkId);
            $this->view->getRow2 = $this->model->getIndicatorAdditionalInfoModel(1180, $linkId);
            $this->view->getRow2['GRAPH_JSON'] = $this->view->row2['GRAPH_JSON'];            
        }
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        }         

        if ($this->view->isAjax == false) {
            $this->view->render('form/kpi/indicator/portlocation/portEditor', 'middleware/views/');
        } else {
            echo $this->view->renderPrint('form/kpi/indicator/portlocation/portEditor', 'middleware/views/');
        }

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }        
    }           
    
    public function fillGroupByIndicator() {
        $postData = Input::postData();
        
        if (isset($postData['dataViewCode'])) {
            
            $dataViewCode = Input::param($postData['dataViewCode']);
        
            if ($dataViewId = $this->model->getIndicatorIdByCodeModel($dataViewCode)) {

                $uniqId = Input::param($postData['uniqId']);
                $processMetaDataId = Input::param($postData['processId']);
                $groupPath = Input::param($postData['groupPath']);
                
                if (Input::numeric('isIndicator') == 1) {
                    
                    $inputParamsData = isset($postData['inputParamsData']) ? $postData['inputParamsData'] : null;
                    $mappingParamsData = $postData['mappingParamsData'];
                    
                    $_POST['indicatorId'] = $dataViewId;
                    $_POST['page']        = 1;
                    $_POST['rows']        = 200;

                    if ($inputParamsData) {
                        
                        $paramFilter = array();

                        foreach ($inputParamsData as $row) {

                            $value = issetParam($row['value']);

                            if ($value != '') {

                                if (is_array($value)) {
                                    $paramFilter[$row['inputPath']][] = array(
                                        'operator' => 'IN',
                                        'operand' => Arr::implode_r(',', $value, true)
                                    );
                                } else {
                                    $paramFilter[$row['inputPath']][] = array(
                                        'operator' => '=',
                                        'operand' => $value
                                    );
                                }
                            }
                        }

                        $_POST['criteria'] = $paramFilter;
                    }

                    $rowDatas = $this->model->indicatorDataGridModel();
                    
                    if (isset($rowDatas['rows'][0])) {
                        
                        $result = $rowDatas['rows'];
                        $array = array();

                        foreach ($result as $k => $row) {

                            foreach ($mappingParamsData as $map) {

                                if (isset($map['processPath'])) {
                                    
                                    $explode = explode('.', $map['processPath']);
                                    $processPath = array_pop($explode);
                                    $array[$k][$processPath] = $row[strtoupper($map['dataviewPath'])];
                                } else {
                                    echo ''; exit;
                                }
                            }
                        }

                        $rowDatas = array(strtolower($groupPath) => $array);
                        
                        $rowsRender = $this->model->indicatorRowsRender($processMetaDataId, $groupPath, $rowDatas);

                        ob_start('ob_html_compress'); 
                            echo $rowsRender;
                        ob_end_flush();
                    }
                }
            }
        } 
        
        echo '';
    }
    
    public function mvRowsDetailFillRender() {
        
        try {
            
            $isIndicator = Input::numeric('isIndicator');
            $rowId = Input::numeric('rowId');
            $mainIndicatorId = Input::numeric('processId');
            $lookupId = Input::numeric('lookupId');
            $groupPath = Input::post('groupPath');
            $rows = Arr::changeKeyLower(Input::post('rows'));

            $paramMap = $this->db->GetAll("
                SELECT 
                    LOWER(T2.SRC_INDICATOR_PATH) AS SRC_INDICATOR_PATH, 
                    UPPER(T2.TRG_INDICATOR_PATH) AS TRG_INDICATOR_PATH, 
                    T2.DEFAULT_VALUE 
                FROM KPI_INDICATOR_INDICATOR_MAP T1 
                    INNER JOIN KPI_INDICATOR_INDICATOR_MAP T2 ON T2.SRC_INDICATOR_MAP_ID = T1.ID 
                WHERE T1.SEMANTIC_TYPE_ID = 42 
                    AND T2.SEMANTIC_TYPE_ID = 43 
                    AND T1.SRC_INDICATOR_MAP_ID = ".$this->db->Param(0)." 
                    AND ".($isIndicator ? 'T1.TRG_INDICATOR_ID' : 'T1.LOOKUP_META_DATA_ID')." = ".$this->db->Param(1)." 
                ORDER BY T2.ID ASC", 
                array($rowId, $lookupId));

            $fields = 'array(';

            foreach ($paramMap as $field) {
                /*if (isset($lookupFieldData[$field['PARAM_FIELD_PATH']])) {
                    $fields .= '\''.$field['PARAM_FIELD_PATH'].'\' => issetParam($lookupRowDatas[\''.$field['PARAM_FIELD_PATH'].'\'][$field[\''.$field['LOOKUP_FIELD_PATH'].'\']]), ';
                } else {
                    $fields .= '\''.$field['PARAM_FIELD_PATH'].'\' => issetParam($field[\''.$field['LOOKUP_FIELD_PATH'].'\']), ';
                }*/
                
                if ($field['DEFAULT_VALUE'] != '') {
                    $fields .= '\''.$field['TRG_INDICATOR_PATH'].'\' => \''.$field['DEFAULT_VALUE'].'\', ';
                } else {
                    $fields .= '\''.$field['TRG_INDICATOR_PATH'].'\' => issetParam($field[\''.$field['SRC_INDICATOR_PATH'].'\']), ';
                }
            }

            $fields .= ')';

            eval('$array = array_map(function($field) { return '.$fields.'; }, $rows);');

            $rowsHtml = $this->model->indicatorRowsRender($mainIndicatorId, $groupPath, array($groupPath => $array), false);
            
            $response = array('status' => 'success', 'rows' => $rowsHtml);
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function microUpdateClientObject($obj, $id) {   
        $getObject = $this->model->getKpiIndicatorByCodeModel(Input::post('getNameOfObjectName'));
        $postData = Input::postData();
        unset($postData['recordId']);
        unset($postData['getNameOfObjectName']);

        $postArrData['kpiMainIndicatorId'] = $getObject['ID'];
        $postArrData['kpiDataTblName'] = $getObject['TABLE_NAME'];        
        $postArrData['kpiTbl'] = $postData;
        $postArrData['kpiTblId'] = Input::post('recordId');
        $postArrData['isMicroFlow'] = true;        
        
        jsonResponse($this->model->saveKpiDynamicDataModel(null, $postArrData));
    }    
    
    public function transferIndicatorAction() {
        $response = $this->model->transferIndicatorActionModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function mvGetExcelFileSheet() {
        $response = $this->model->mvGetExcelFileSheetModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function mvImportRowsExcelFile() {
        $response = $this->model->mvImportRowsExcelFileModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function createMvStructureFromFileForm() {
        $this->view->indicatorId = Input::numeric('indicatorId');
        
        if ($this->view->indicatorId) {
            $configRow = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
            
            if ($configRow) {
                $response = array(
                    'status' => 'success', 
                    'html' => $this->view->renderPrint('kpi/indicator/importfile/render', self::$viewPath)
                );
            } else {
                $response = array('status' => 'info', 'message' => 'Indicator олдсонгүй!');
            }
        } else {
            $this->view->isCreateIndicator = true;
            $response = array(
                'status' => 'success', 
                'indicatorId' => getUID(),
                'html' => $this->view->renderPrint('kpi/indicator/importfile/render', self::$viewPath)
            );
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function createMvStructureFromFile() {
        $response = $this->model->createMvStructureFromFileModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function removeTempIndicator() {
        $response = $this->model->removeTempIndicatorModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function indicatorMapProcess() {
        $metaProcess = $this->model->getKpiIndicatorMapMetaModel(Input::numeric('indicatorId'), 10000015);
        echo json_encode($metaProcess, JSON_UNESCAPED_UNICODE);
    }    
    
    public function getIndicatorInputFields() {
        $indicatorId = Input::numeric('indicatorId');
        $response = $this->model->getIndicatorInputOutputFieldsModel($indicatorId, 'input');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function getIndicatorInputOutputFields() {
        $indicatorId = Input::numeric('indicatorId');
        $response = $this->model->getIndicatorInputOutputFieldsModel($indicatorId);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function getIndicatorRow() {
        $indicatorId = Input::numeric('indicatorId');
        $response = $this->model->getIndicatorModel($indicatorId);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function rowsGetValueFromDataMart() {
        $response = $this->model->rowsGetValueFromDataMartModel();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function mvFlowChartExecute() {
        
        $indicatorId = Input::numeric('indicatorId');
        $result = $this->model->getExecuteEventCodeModel($indicatorId);
        
        if ($result['expIndicatorId']) {
            $expIndicatorId = $result['expIndicatorId'];
            $response = (new Mdexpression())->executeMicroFlowExpression($expIndicatorId);
        } else {
            $response = array('status' => 'error');
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function mvValueRender($type, $value = '') {
        
        if ($type == 'check' || $type == 'boolean') {
            $value = ($value == '1' || $value == 'true') ? 'Тийм' : 'Үгүй';
        } elseif ($type == 'decimal' || $type == 'bigdecimal' || $type == 'decimal_zero') {
            $value = empty($value) ? '' : number_format($value, 2, '.', ',');
        }
        
        return $value;
    }
    
    public function renderProcessAddonInfo() {
        
        $processId = Input::numeric('processId');
        $data = $this->model->getIndicatorsOnTheProcessModel($processId);
        $render = array();
        
        if ($data) {
                
            $uniqId = Input::numeric('uniqId');
            $sourceId = Input::numeric('sourceId');
            
            $tabItemArr = $tabContentArr = array();
            
            $_POST['isResponseArray'] = 1;
            $_POST['param']['id'] = $sourceId; 
                
            foreach ($data as $k => $row) {
                
                Mdform::$addonPathPrefix = '[bpAddonInfo]['.$row['INDICATOR_ID'].']';
                Mdform::$mvPathPrefix = 'mvSysPath['.$row['INDICATOR_ID'].'][';
                Mdform::$mvPathSuffix = ']';
    
                $_POST['param']['indicatorId'] = $row['INDICATOR_ID'];
                $_POST['param']['actionType'] = 'create';

                $indicatorContent = self::kpiIndicatorTemplateRender(); 

                $tabItemArr[] = '<li class="nav-item">';
                    $tabItemArr[] = '<a href="#mv_tab_'.$uniqId.'_'.$k.'" class="nav-link'.($k == 0 ? ' active' : '').'" data-toggle="tab" aria-expanded="false">'.$row['NAME'].'</a>';
                $tabItemArr[] = '</li>';

                $tabContentArr[] = '<div class="tab-pane'.($k == 0 ? ' active' : '').'" id="mv_tab_'.$uniqId.'_'.$k.'">';
                        $tabContentArr[] = $indicatorContent['html'];
                $tabContentArr[] = '</div>';
            }

            $render[] = '<div class="bp-tabs tabbable-line">';
                $render[] = '<ul class="nav nav-tabs">';
                    $render[] = implode('', $tabItemArr);
                $render[] = '</ul>';
                $render[] = '<div class="tab-content">';
                    $render[] = implode('', $tabContentArr);
                $render[] = '</div>';
            $render[] = '</div>';
        }
        
        echo implode('', $render);
    }

    public function runInternalQuery() {
        
        $params = Input::post('kpiTbl');
        $_POST['indicatorId'] = Input::post('kpiMainIndicatorId');

        $criteriaData = [];
        if ($params) {
            foreach ($params as $pkey => $pvalue) {
                $criteriaData[$pkey] = [[
                    'operator' => '=',
                    'operand' => $pvalue                    
                ]];
            }
        }
        $_POST['criteria'] = $criteriaData;

        $this->load->model('mdform', 'middleware/models/');
        $result = $this->model->runInternalQueryModel();
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }    

    public function kpiSaveMetaDmRecordMap2() {
        $this->load->model('mdform', 'middleware/models/');
        $result = $this->model->kpiSaveMetaDmRecordMap2();
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }    

    public function renderAddModeIndicatorTab($uniqId, $row) {

        $tabHtml = $tabEndHtml = $tabStart = $tabEnd = $commentAddin = $tabcontentStyle = '';
        
        if ($row['IS_ADDON_PHOTO'] && issetParam($row['isIgnorePhotoTab']) != '1') {
            $tabHtml .= '<li class="nav-item"><a href="#indicator_photo_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderAddModeIndicatorTab(\''.$uniqId.'\', \'' . $row['MAIN_INDICATOR_ID'] . '\', \'photo\', this);" data-required="' . $row['IS_ADDON_PHOTO'] . '" data-addon-type="photo">'
                    . ($row['IS_ADDON_PHOTO'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabPhoto_'.$row['MAIN_INDICATOR_ID'], 'photo') . '</a></li>';
            $tabEndHtml .= '<div class="tab-pane" id="indicator_photo_tab_'.$uniqId.'"></div>';
        }
        
        if ($row['IS_ADDON_FILE'] && issetParam($row['isIgnoreFileTab']) != '1') {
            $tabHtml .= '<li class="nav-item"><a href="#indicator_file_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderAddModeIndicatorTab(\''.$uniqId.'\', \'' . $row['MAIN_INDICATOR_ID'] . '\', \'file\', this);" data-required="' . $row['IS_ADDON_FILE'] . '" data-addon-type="file">'
                    . ($row['IS_ADDON_FILE'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabFile_'.$row['MAIN_INDICATOR_ID'], 'file') . '</a></li>';
            $tabEndHtml .= '<div class="tab-pane" id="indicator_file_tab_'.$uniqId.'"></div>';
        }
        
        if ($row['IS_ADDON_COMMENT'] && issetParam($row['isIgnoreCommentTab']) != '1') {
            
            if (issetParam($row['IS_ADDON_COMMENT_TYPE'])) {
                
                switch ($row['IS_ADDON_COMMENT_TYPE']) {
                    case 'right':
                        $tabcontentStyle = 'style="width: 60%; float: left;"';
                        $commentAddin = 'style="width: 40%; padding-left: 5px;"';
                        break;
                    case 'left':
                        $tabcontentStyle = 'style="width: 60%; float: right;"';
                        $commentAddin = 'style="width: 40%; padding-right: 5px;"';
                        break;
                        
                    default:
                        $commentAddin = '';
                        break;
                }
                
                $tabHtml .= '<input type="hidden"  />';
                $tabEndHtml .= '<script>jQuery(document).ready(function () { renderAddModeIndicatorTab(\''.$uniqId.'\', \'' . $row['MAIN_INDICATOR_ID'] . '\', \'commentbtm\', this); });</script> <fieldset class="collapsible"  '. $commentAddin .'> <legend>'. ($row['IS_ADDON_COMMENT'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabComment_'.$row['MAIN_INDICATOR_ID'], 'comment') . ' '.(!empty($addOnCount) ? $addOnCount : '') .'</legend>';
                $tabEndHtml .= '<div class="indicator_comment_tab_'.$uniqId.'"></div></fieldset>';
                
            } else {
                $tabHtml .= '<li class="nav-item"><a href="#indicator_comment_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderAddModeIndicatorTab(\''.$uniqId.'\', \'' . $row['MAIN_INDICATOR_ID'] . '\', \'comment\', this);" data-required="' . $row['IS_ADDON_COMMENT'] . '" data-addon-type="comment">'
                        . ($row['IS_ADDON_COMMENT'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabComment_'.$row['MAIN_INDICATOR_ID'], 'comment') . '</a></li>';
                $tabEndHtml .= '<div class="tab-pane" id="indicator_comment_tab_'.$uniqId.'"></div>';
            }
        }

        if ($tabHtml != '') {
            $tabStart = $tabHtml;
            $tabEnd = $tabEndHtml;
        }

        return array('tabStart' => $tabStart, 'tabEnd' => $tabEnd);
    }

    public function renderEditModeIndicatorTab($uniqId, $row, $sourceId, $selectedRowData = array(), $dmMetaDataId = null) {

        $tabHtml = $tabEndHtml = $tabStart = $tabEnd = $photoCount = $fileCount = $commentCount = '';
        $refStructureId = $row['MAIN_INDICATOR_ID'];

        $actionType = '';            
        
        if ($row['IS_ADDON_PHOTO'] && issetParam($row['isIgnorePhotoTab']) != '1') {
            
            $addOnCount = $this->model->getMetaDataValueCount($refStructureId, $sourceId, 'photo');

            $tabHtml .= '<li class="nav-item"><a href="#bp_photo_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderEditModeBpTab(\''.$uniqId.'\', \''.$refStructureId.'\', \''.$sourceId.'\', \'photo\', this, \''.$dmMetaDataId.'\');" data-required="' . $row['IS_ADDON_PHOTO'] . '" data-addon-type="photo" data-actiontype="'.$actionType.'">'
                    . ($row['IS_ADDON_PHOTO'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabPhoto_'.$row['MAIN_INDICATOR_ID'], 'photo') . ' '.(!empty($addOnCount) ? $addOnCount : '').'</a></li>';
            $tabEndHtml .= '<div class="tab-pane" id="bp_photo_tab_'.$uniqId.'"></div>';
        }
        
        if ($row['IS_ADDON_FILE'] && issetParam($row['isIgnoreFileTab']) != '1') {
            
            $addOnCount = $this->model->getMetaDataValueCount($refStructureId, $sourceId, 'file');

            $tabHtml .= '<li class="nav-item"><a href="#bp_file_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderEditModeBpTab(\'' . $uniqId . '\', \'' . $refStructureId . '\', \'' . $sourceId . '\', \'file\', this, \''. $dmMetaDataId . '\');" data-required="' . $row['IS_ADDON_FILE'] . '" data-addon-type="file" data-actiontype="'.$actionType.'">'
                    . ($row['IS_ADDON_FILE'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabFile_'.$row['MAIN_INDICATOR_ID'], 'file') . ' '.(!empty($addOnCount) ? $addOnCount : '').'</a></li>';
            $tabEndHtml .= '<div class="tab-pane" id="bp_file_tab_'.$uniqId.'"></div>';
        }
        
        if ($row['IS_ADDON_COMMENT'] && issetParam($row['isIgnoreCommentTab']) != '1') {
            
            $selectedRow = array_key_exists(0, $selectedRowData) ? $selectedRowData[0] : $selectedRowData;
            $addOnCount = $this->model->getMetaDataValueCount($refStructureId, $sourceId, 'comment');
            
            if (issetParam($row['IS_ADDON_COMMENT_TYPE'])) {

                $tabHtml .= '<input type="hidden"/>';
                $tabEndHtml .= '<fieldset class="collapsible"><legend>'. ($row['IS_ADDON_COMMENT'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabComment_'.$row['MAIN_INDICATOR_ID'], 'comment') . ' '.(!empty($addOnCount) ? $addOnCount : '') .'</legend>';
                $tabEndHtml .= '<div class="bp_comment_tab_'.$uniqId.'">'.(new Mdwebservice())->renderEditModeBpCommentTab($uniqId, $row['MAIN_INDICATOR_ID'], $refStructureId, $sourceId).'</div></fieldset>';
    
            } else {
                $tabHtml .= '<li class="nav-item"><a href="#bp_comment_tab_'.$uniqId.'" data-toggle="tab" class="nav-link" onclick="renderEditModeBpTab(\'' . $uniqId . '\', \'' . $refStructureId . '\', \'' . $sourceId . '\', \'comment\', this, \''. $dmMetaDataId . '\');" data-required="' . $row['IS_ADDON_COMMENT'] . '" data-addon-type="comment" data-actiontype="'.$actionType.'">'
                        . ($row['IS_ADDON_COMMENT'] == '2' ? '<span class="required">*</span>' : '') . Lang::eitherOne('bpTabComment_'.$row['MAIN_INDICATOR_ID'], 'comment') . ' '.(!empty($addOnCount) ? $addOnCount : '').'</a></li>';
                $tabEndHtml .= '<div class="tab-pane" id="bp_comment_tab_'.$uniqId.'"></div>';
            }
        }        

        if ($tabHtml != '') {
            $tabStart = $tabHtml;
            $tabEnd = $tabEndHtml;
        }

        return array('tabStart' => $tabStart, 'tabEnd' => $tabEnd);
    }    

    public function renderRelationKpi() {
        $selectedRow = Arr::decode(Input::post('selectedRow'));
        $this->view->indicatorId = $selectedRow['dataRow']['id'];
        $components = $this->model->getKpiIndicatorMapWithoutTypeModel($this->view->indicatorId, 10000009);                    
                    
        $this->view->savedComponentRows = $this->model->getSavedRecordMapKpiModel($this->view->indicatorId, $this->view->indicatorId, $components);
        
        // $componentTabs = Arr::groupByArray($components, 'DESCRIPTION');
        
        // foreach ($componentTabs as $componentTabName => $componentTab) {
            
        //     $this->view->componentUniqId = getUID();
        //     $this->view->components = $componentTab['rows'];
            
        //     Mdform::$topTabRenderShow[$this->lang->line($componentTabName)] = array($this->view->renderPrint('kpi/indicator/recordmap/recordmap', self::$viewPath));
        // }

        $this->view->componentUniqId = getUID();
        $this->view->fromWebLink = true;
        $this->view->components = $components;

        $response = array(
            'status' => 'success', 
            'html' => $this->view->renderPrint('kpi/indicator/recordmap/recordmap2', self::$viewPath)
        );
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);        
    }    

    public function renderRelationKpiReload() {
        $this->view->indicatorId = Input::post('indicatorId');
        $components = $this->model->getKpiIndicatorMapWithoutTypeModel($this->view->indicatorId, 10000009);            
        $this->view->savedComponentRows = $this->model->getSavedRecordMapKpiModel($this->view->indicatorId, $this->view->indicatorId, $components);
        
        $this->view->fromWebLink = true;
        $this->view->components = $components;

        $response = array(
            'status' => 'success', 
            'html' => $this->view->renderPrint('kpi/indicator/recordmap/recordmap2content', self::$viewPath)
        );
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);        
    }    

    public function deleteRelationpKpi() {
        $mapId = Input::post('mapId');
        $this->model->dbExecuteMetaVerseData("
            DELETE 
            FROM META_DM_RECORD_MAP 
            WHERE ID = ".$mapId
        );     

        $response = array(
            'status' => 'success', 
        );        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);        
    }
    
    public function indicatorBuilder($indicatorId = '') {
        
        if (!isset($this->view)) {
            $this->view = new View();
        } 
        
        $this->load->model('mdform', 'middleware/models/');
        
        $this->view->indicatorId = '';
        $this->view->indicatorName = '';
        $isReturnArray = false;
        
        if (strpos($indicatorId, 'workSpaceParam') !== false) {

            parse_str($indicatorId, $workSpaceParamArray);

            if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                $this->view->indicatorId = Input::param($workSpaceParamArray['workSpaceParam']['id']);
                $this->view->indicatorName = Input::param($workSpaceParamArray['workSpaceParam']['name']);
                $isReturnArray = true;
            }

        } else {
            if (Input::numeric('isWorkFlow') == 1) {
                parse_str(Input::post('workSpaceParams'), $workSpaceParamArray);
                if (isset($workSpaceParamArray['workSpaceParam']['id'])) {
                    $this->view->indicatorId = $workSpaceParamArray['workSpaceParam']['id'];
                    $this->view->indicatorName = Input::param($workSpaceParamArray['workSpaceParam']['name']);
                }
            } else {
                $this->view->indicatorId = Input::param($indicatorId);
            }
        }
        
        if ($this->view->indicatorId == '') {
            echo 'Invalid indicatorId!'; exit;
        }
        
        $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
        
        if (!isset($this->view->row['NAME'])) {
            Message::add('e', '', 'back');
        }
        
        $this->view->title = $this->lang->line($this->view->row['NAME']);
        $this->view->uniqId = getUID();
        $this->view->isAjax = is_ajax_request();
        $this->view->row['isIgnoreStandardFields'] = true;
        
        $columnsData = $this->model->getKpiIndicatorColumnsModel($this->view->indicatorId, $this->view->row); 
        $columns = $this->model->chartKpiIndicatorColumnsModel($columnsData);
        
        $this->view->categoryColumns = $columns['categoryColumns'];
        $this->view->valueColumns = $columns['valueColumns'];
        
        if ($this->view->isAjax == false) {
            
            $this->view->css = AssetNew::metaCss();
            $this->view->js = AssetNew::metaOtherJs();
            $this->view->fullUrlJs = AssetNew::amChartJs();
        
            $this->view->render('header');
        } 
        
        $this->view->renderChart = '';
        switch ($this->view->row['KPI_TYPE_ID']) {
            case '1130':
                self::indicatorDashboard($this->view->indicatorId);
                break;
            case '2020':
                self::buildPagemanagment($this->view->indicatorId, 'html', $this->view->row);
                break;
            default:
                echo 'Харуулах боломжгүй байна.';
                break;
        }        

        if ($this->view->isAjax == false) {
            $this->view->render('footer');
        }
    }

    public function buildPagemanagment($indicatorId = '', $returnType = '', $mainIndicatorData = array()) {
        
        $this->view->uniqId = getUID();
        $this->view->indicatorId = $indicatorId;
        $this->view->configJsonData = array();
        $this->view->indicatorData = $this->model->getKpiIndicatorFullExpressionModel($indicatorId);
        $this->view->chartMainTypeData = $this->model->mainChartTypeDataModel();

        $this->view->isAjax = is_ajax_request();
        /* $this->load->model('mdwidget', 'middleware/models/');
        $this->view->widgetList = $this->model->getWidgetStandartListModel(); */
        
        if ($mainIndicatorData) {
            $this->view->kpiTypeIndicatorData = $this->model->kpiTypeIndicatorData($mainIndicatorData['KPI_TYPE_ID']);
        } else {
            $this->view->row = $this->model->getKpiIndicatorRowModel($this->view->indicatorId);
            $this->view->kpiTypeIndicatorData = $this->model->kpiTypeIndicatorData($this->view->row['KPI_TYPE_ID']);
        }
        
        $this->view->js = array_unique(array_merge(array('custom/addon/admin/pages/scripts/app.js'), AssetNew::metaOtherJs()));
        $this->view->css = AssetNew::metaCss();

        $this->view->fullUrlJs = array(
            'middleware/assets/plugins/builder/dom-to-image.min.js',
            'middleware/assets/plugins/builder/builder.js',
        );

        $this->view->fullUrlJs = array_unique(array_merge($this->view->fullUrlJs, AssetNew::amChartJs()));

        if (!$this->view->isAjax && $returnType === '') {
            $this->view->render('header');
            $this->view->render('/layout/index', "projects/views/contentui/build");
            $this->view->render('footer');
        } else {
            switch ($returnType) {
                case 'render':
                    $this->view->render('/layout/render', "projects/views/contentui/build");
                    break;
                case 'html':
                    $this->view->render('/layout/index', "projects/views/contentui/build");
                    break;
                default:
                    $response = array(
                        'Title' => '',
                        'Width' => '700',
                        'uniqId' => $this->view->uniqId,
                        'Html' => $this->view->renderPrint('/layout/index', "projects/views/contentui/build"),
                        'save_btn' => Lang::line('save_btn'),
                        'close_btn' => Lang::line('close_btn')
                    );
        
                    echo json_encode($response);
                    break;
            }
        }
    }

    public function buildingPage() {
        $processCode = 'PORTAL_LAYOUT_HDR_001';
        $postData = Input::postData();

        //create steps
        //1. META_BP_LAYOUT
        //2. META_BP_LAYOUT_HDR
        //3. META_BP_LAYOUT_SECTION
        //4. META_BP_LAYOUT_SECTION_DTL
        
        $currentDate       = Date::currentDate();
        $createdUserId     = Ue::sessionUserKeyId();
        $date              = Date::currentDate('Ym');
        $postData = Input::postData();
        
        $response =  array('status' => 'error', 'text' => Lang::lineCode('msg_save_error'));
        try {
            
            $uniqId = getUID();
            $newPath = UPLOADPATH . 'layout/' . $date . '/';
            
            if (!is_dir($newPath)) {
                mkdir($newPath, 0777, true);
            }

            $base64Img = Input::post('shortcut');
            $filePath = base64_to_jpeg($base64Img, $newPath. $uniqId .'.jpg' );

            $layoutData = array(
                'ID' => Input::post('id', $uniqId),
                'CODE' => Input::post('CODE', 'layoutCode_' . $uniqId),
                'NAME' => Input::post('name', 'LAYOUT-NAME-' . $uniqId),
                'PREVIEW_WEBIMAGE' => $filePath,
                'PREVIEW_MOBILEIMAGE' => $filePath,
                'DESCRIPTION' => Input::post('description', 'auto create'),
            );

            $result = $this->db->AutoExecute('META_BP_LAYOUT', $layoutData);
            if (!$result) {
                throw new Exception("LAYOUT үүсгэхэд алдаа гарлаа!"); 
            }
            
            $newPath = UPLOADPATH . 'page/' . $date . '/';
            if (!is_dir($newPath)) {
                mkdir($newPath, 0777, true);
            }

            $html = html_entity_decode(Input::post('page'));
            $layoutFile = fopen($newPath . $layoutData['CODE'].".php", "w");
            fwrite($layoutFile, $html);
            fclose($layoutFile);

            $uniqId = getUID();
            $metaData = array(
                'META_DATA_ID' => getUID(),
                'META_DATA_CODE' => $layoutData['CODE'],
                'META_DATA_NAME' => $layoutData['NAME'],
                'META_TYPE_ID' => '200101010000044',
                'IS_ACTIVE' => '1',
                'CREATED_DATE' => $currentDate,
                'CREATED_USER_ID' => $createdUserId,
            );

            $result = $this->db->AutoExecute('META_DATA', $metaData);
            if (!$result) {
                throw new Exception("META_DATA үүсгэхэд алдаа гарлаа!"); 
            }
            
            $uniqId = getUID();
            $folderData = array(
                'ID' => getUID(),
                'FOLDER_ID' => '1678855515714192',
                'META_DATA_ID' => $metaData['META_DATA_ID'],
            );

            $result = $this->db->AutoExecute('META_DATA_FOLDER_MAP', $folderData);

            if (!$result) {
                throw new Exception("META_DATA_FOLDER_MAP үүсгэхэд алдаа гарлаа!"); 
            }
            
            $uniqId = getUID();
            $hdrData = array(
                'ID' => Input::post('id', $uniqId),
                'META_DATA_ID' => $metaData['META_DATA_ID'],
                'LAYOUT_CODE' => $layoutData['CODE'],
                'IS_PORTAL' => '1',
                'CREATED_DATE' => $currentDate,
                'CREATED_USER_ID' => $createdUserId,
            );

            $result = $this->db->AutoExecute('META_BP_LAYOUT_HDR', $hdrData);
            if (!$result) {
                throw new Exception("LAYOUT HEADER үүсгэхэд алдаа гарлаа!"); 
            }
            
            $dataBlobArr = array();
            if (Input::post('blockUniqId')) {
                $index = 1;
                foreach ($postData['blockUniqId'] as $unId) {
                    $secNemgoo = json_decode($postData['secNemgoo'][$unId], true);

                    if (issetParam($secNemgoo['html'])) {
                        $html = html_entity_decode($secNemgoo['html']);
                        $newPath = UPLOADPATH . 'page/section/' . $date . '/';
                        if (!is_dir($newPath)) {
                            mkdir($newPath, 0777, true);
                        }
                        $layoutFile = fopen($newPath . $layoutData['CODE'].".php", "w");
                        $secNemgoo['htmlPath'] = $newPath . $layoutData['CODE'].".php";
                        $secNemgoo['html'] =  $html; /* $html; */

                        fwrite($layoutFile, $html);
                        fclose($layoutFile);

                        $postData['secNemgoo'][$unId] = json_encode($secNemgoo, JSON_UNESCAPED_UNICODE);
                        /* var_dump($postData['secNemgoo'][$unId]);
                        die; */
                    }

                    $sectionData = array(
                        'ID' => getUID(),
                        'CODE' => $index,
                        'TITLE' => 'TITLE-' . $index,
                        'HEADER_ID' => $hdrData['ID'],
                        'WIDGET_ID' => $postData['widgetId'][$unId],
                        'META_DATA_ID' => $postData['metaDataId'][$unId],
                        'OTHER_ATTR' => $postData['secNemgoo'][$unId],
                    );

                    $tmpArr = array();
                    if ($postData['secNemgoo'][$unId]) {
                        $tmpArr = json_decode($postData['secNemgoo'][$unId], true);
                    }

                    $tmpArr['widgetId'] = $sectionData['WIDGET_ID'];
                    $tmpArr['metaDataId'] = $sectionData['META_DATA_ID'];
                    
                    $result = $this->db->AutoExecute('META_BP_LAYOUT_SECTION', $sectionData);
    
                    if ($result) {
                        if (issetParamArray($postData['positionName'][$unId])) {
                            foreach ($postData['positionName'][$unId] as $key => $posId) {
                                if ($postData['positionName'][$unId][$key]) {
                                    $sectionDtlData = array(
                                        'ID' => getUID(),
                                        'SECTION_ID' => $sectionData['ID'],
                                        'POSITION_NAME' => $postData['positionName'][$unId][$key],
                                        'FIELD_PATH' => $postData['fieldPath'][$unId][$key],
                                        'OTHER_ATTR' => $postData['posNemgoo'][$unId][$key],
                                    );
                                    $result = $this->db->AutoExecute('META_BP_LAYOUT_SECTION_DTL', $sectionDtlData);
                                }
                            }
                        }
                    }

                    $dataBlobArr[$unId] = $tmpArr;
                    $index++;
                }
            }
            /* json_encode($jsonConfig, JSON_UNESCAPED_UNICODE); */
            if (Input::post('indicatorId')) {
                $indicatorId = Input::post('indicatorId');
                
                if ($dataBlobArr)
                    $this->db->UpdateClob('KPI_INDICATOR', 'GRAPH_JSON', json_encode($dataBlobArr, JSON_UNESCAPED_UNICODE), 'ID = '. $indicatorId);

                if (Input::post('page'))
                    $this->db->UpdateClob('KPI_INDICATOR', 'LOAD_EXPRESSION_STRING', Input::post('page'), 'ID = '. $indicatorId);

            }

            $response =  array('status' => 'success', 'text' => Lang::lineCode('msg_save_success'));
            
        } catch (Exception $e) {
            $response =  array('status' => 'warning', 'text' => $e->getMessage());
        }
        
        convJson($response);
    }

    public function paramConfigForm() {

        $this->view->uniqId = Input::post('uniqId');
        $this->view->itemCfId = Input::post('itemCfId');
        $this->view->kpiTypeId = Input::post('kpiTypeId');

        $postData = Input::postData();
        $metaTypeCode = issetParam($postData['metaTypeCode']);

        switch ($metaTypeCode) {
            case 'echart':
                $this->view->kpiTypeIndicatorData = $this->model->kpiTypeIndicatorData($this->view->kpiTypeId);
                convJson(array('data' => $this->view->kpiTypeIndicatorData)); die();
                break;
            
            default:
                $this->view->kpiTypeIndicatorData = array();
                break;
        }        

        $response = array(
            'Title' => '',
            'Width' => '700',
            'uniqId' => $this->view->uniqId,
            'uniqId' => $this->view->uniqId,
            'Html' => $this->view->renderPrint('/layout/config', "projects/views/contentui/build"),
            'save_btn' => Lang::line('save_btn'),
            'close_btn' => Lang::line('close_btn')
        );

        echo json_encode($response);
    }
    
    public function runPivotDataMartTest() {
        $rs = $this->model->runPivotDataMartModel('184186803');
        var_dump($rs);die;
    }
    
    public function getKpiIOIndicatorColumns() {
        $getParamInput = $this->model->getKpiIOIndicatorColumnsModel(Input::post('mainIndicatorId'), 1);
        echo json_encode($getParamInput, JSON_UNESCAPED_UNICODE);
    }

}
