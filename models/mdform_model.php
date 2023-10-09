<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

class Mdform_Model extends Model {
    
    private static $gfServiceAddress = GF_SERVICE_ADDRESS;
    private static $kpiControlDatas = array();
    private static $parentIndicator = array();
    private static $processCodes = array();
    private static $kpiIndicatorRow = array();
    private static $getKpiColumnTypes = array();
    private static $kpiProcessConfig = array();
    private static $kpiTableFields = array();
    private static $kpiTrgIndicatorsBySrcIndicatorId = array();
    private static $placeholderTitle = array();
    private static $getProcessResponses = array();
    private static $indicatorColumns = array();
    private static $lookupDatas = array();
    private static $indicatorIdFields = array();
    private static $calcLogDtl = array();
    private static $isObjectType = false;
    private static $isGraphType = false;
    private static $formType = 'grid';
    private static $indicatorTableName = null;
    private static $calcMartId = null;
    private static $uniqIdIndex = 1;

    public function __construct() {
        parent::__construct();
    }

    public function getIndicatorModel($id) {
        
        $idPh = $this->db->Param('id');

        $bindVars = array(
            'id' => $this->db->addQ($id)
        );
        
        $row = $this->db->GetRow("
            SELECT 
                KI.ID,
                KI.CODE,
                KI.NAME,
                KT.CODE AS TYPE_CODE,
                KI.TABLE_NAME,
                KI.PARENT_ID
            FROM KPI_INDICATOR KI 
                LEFT JOIN KPI_TYPE KT ON KT.ID = KI.KPI_TYPE_ID 
            WHERE KI.ID = $idPh", $bindVars);

        return $row;
    }    

    public function getChildKpiTemplates($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                KT.ID, 
                KT.CODE, 
                KT.NAME, 
                KT.RENDER_TYPE, 
                KT.TYPE_CODE, 
                KT.PIVOT_VALUE_META_DATA_ID, 
                KT.PIVOT_VALUE_CRITERIA, 
                KT.INDICATOR_COL_WIDTH, 
                KT.WIDTH, 
                KT.HEIGHT, 
                KT.EXPRESSION_TEMPLATE_ID, 
                KT.TYPE_ID, 
                KT.DEFAULT_TEMPLATE_ID, 
                KT.MERGE_COL_COUNT 
            FROM KPI_TEMPLATE_MAP TM 
                INNER JOIN KPI_TEMPLATE KT ON KT.ID = TM.TRG_TEMPLATE_ID 
            WHERE TM.SRC_TEMPLATE_ID = ".$this->db->Param(0)." 
            GROUP BY 
                KT.ID, 
                KT.CODE, 
                KT.NAME, 
                KT.RENDER_TYPE, 
                KT.TYPE_CODE,  
                KT.PIVOT_VALUE_META_DATA_ID, 
                KT.PIVOT_VALUE_CRITERIA,  
                KT.INDICATOR_COL_WIDTH,  
                KT.WIDTH, 
                KT.HEIGHT, 
                KT.EXPRESSION_TEMPLATE_ID, 
                KT.TYPE_ID, 
                KT.DEFAULT_TEMPLATE_ID, 
                TM.ORDER_NUM, 
                KT.MERGE_COL_COUNT 
            ORDER BY TM.ORDER_NUM ASC", array($templateId));
        
        return $data;
    }
    
    public function getKpiTemplateRow($templateId) {
        
        $row = $this->db->GetRow("
            SELECT 
                ID, 
                CODE, 
                NAME, 
                RENDER_TYPE, 
                TYPE_CODE, 
                PIVOT_VALUE_META_DATA_ID, 
                PIVOT_VALUE_CRITERIA, 
                INDICATOR_COL_WIDTH, 
                WIDTH, 
                HEIGHT, 
                EXPRESSION_TEMPLATE_ID,
                TYPE_ID, 
                DEFAULT_TEMPLATE_ID, 
                MERGE_COL_COUNT 
            FROM KPI_TEMPLATE 
            WHERE ID = ".$this->db->Param(0), array($templateId));
        
        return $row;
    }
    
    public function getRenderKpiTemplateGridHead($facts, $indicators, $getDtlCol = null) {
        
        $colAttr = array('class' => 'kpiDmDtlid', 'data-cell-path' => 'kpiDmDtl.id', 'data-col-name' => 'indicatorName', 'style' => 'background-color:#e1f0ff;text-align:center;');
        
        if (Mdform::$kpiIndicatorWidth) {
            $colAttr['style'] = 'background-color:#e1f0ff;text-align:center;width:'.Mdform::$kpiIndicatorWidth.';min-width:'.Mdform::$kpiIndicatorWidth.';';
        }        
        
        $indiName = false;
        $cells = '';        
                                
        $getDtlColArr = [];
        if ($indicators) {
            if ($getDtlCol) {
                $getDtlColArr = Arr::groupByArray($getDtlCol, 'COLUMN_NAME');
                foreach ($getDtlCol as $row) {
                    $colName = Str::lower($row['COLUMN_NAME']);
                    $colNum = (int) filter_var($colName, FILTER_SANITIZE_NUMBER_INT);
                    $colAttr = array('class' => 'kpiDmDtlCol', 'data-cell-path' => 'kpiDmDtl.'.$colName, 'data-col-path' => $colName, 'data-col-name' => $colName, 'style' => 'background-color:#e1f0ff;text-align:center;width:'.$row['WIDTH'].'min-width:'.$row['WIDTH']);
                    if ($colName == 'indicator') {
                        $colAttr['style'] = $colAttr['style'].'width:'.$row['WIDTH'].'min-width:'.$row['WIDTH'];
                        if (Mdform::$isIndicatorMerge || Mdform::$isUseMergeMatrix) {
                            $colAttr['colspan'] = Mdform::$mergeColCount;
                        }                        
                        $indiName = true;
                        $cells .= html_tag('th', $colAttr, $row['LABEL_NAME']);                    
                    } elseif (strpos($colName, 'col') !== false) {
                        $cells .= html_tag('th', $colAttr, $row['LABEL_NAME']);                    
                    }
                }
            }
            $showColCnt = intval($indicators[0]['SHOW_COL_CNT']);
            for ($i = 1; $i <= $showColCnt; $i++) {
                if (!array_key_exists('COL'.$i, $getDtlColArr)) {
                    $colAttr = array('class' => 'kpiDmDtlCol'.$i, 'data-cell-path' => 'kpiDmDtl.col'.$i, 'data-col-path' => 'col'.$i, 'data-col-name' => 'col'.$i, 'style' => 'background-color:#e1f0ff;text-align:center;');
                    $cells .= html_tag('th', $colAttr, Lang::eitherOne('kpi_'.Mdform::$kpiTemplateId.'_col'.$i, 'col'.$i, 'Үзүүлэлт '.$i));
                }
            }
        }
        
        if (Mdform::$isIndicatorMerge || Mdform::$isUseMergeMatrix) {
            $colAttr['colspan'] = Mdform::$mergeColCount;
        }        
        
        if (!$indiName) {
            
            $colAttr['data-cell-path'] = 'kpiDmDtl.id';
            $colAttr['data-col-name'] = 'indicatorName';
            $colAttr['data-col-path'] = 'indicatorName';
            $colAttr['class'] = '';
            
            $cells = html_tag('th', array('class' => 'rowNumber', 'style' => 'width:30px;background-color:#e1f0ff;text-align:center;'), '№') . html_tag('th', $colAttr, Lang::eitherOne('META_00133' . Mdform::$kpiTemplateId, 'META_00133', 'META_00133')) . $cells;
        } else {
            $cells = html_tag('th', array('class' => 'rowNumber', 'style' => 'width:30px;background-color:#e1f0ff;text-align:center;'), '№') . $cells;
        }
        
        if (Mdform::$isUseMergeMatrix) {
            $cells .= html_tag('th', array('class' => 'd-none'), '');
        }
        
        if ($facts) {
            
            $rowFactGroupArr = [];
            if ($getDtlCol) {
                $getDtlColArr = Arr::groupByArray($getDtlCol, 'COLUMN_NAME');
                $rowFactGroupArr = Arr::groupByArray($facts, 'PARAM_PATH');

                foreach ($getDtlCol as $row) {
                    $colName = Str::lower($row['COLUMN_NAME']);
                    $colNum = (int) filter_var($colName, FILTER_SANITIZE_NUMBER_INT);

                    if (array_key_exists('fact'.$colNum, $rowFactGroupArr) && strpos($colName, 'fact') !== false) {
                        $fact = $rowFactGroupArr['fact'.$colNum]['row'];
                        
                        $cellAttr = array('class' => 'kpiDmDtl'.$fact['PARAM_PATH'], 'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH'], 'data-col-name' => $fact['PARAM_PATH'], 'style' => 'background-color:#e1f0ff;text-align:center;');

                        if (isset($fact['MIN_VALUE']) && $fact['MIN_VALUE'] != '') {
                            $cellAttr['data-min-value'] = $fact['MIN_VALUE'];
                        }

                        if (isset($fact['MAX_VALUE']) && $fact['MAX_VALUE'] != '') {
                            $cellAttr['data-max-value'] = $fact['MAX_VALUE'];
                        }

                        if (isset($cellAttr['data-min-value']) || isset($cellAttr['data-max-value'])) {
                            $cellAttr['data-indicator-criteria'] = '1';
                        }

                        if (isset($fact['WIDTH']) && $fact['WIDTH']) {
                            $cellAttr['style'] = 'background-color:#e1f0ff;text-align:center;width: '.$fact['WIDTH'].'px;min-width: '.$fact['WIDTH'].'px;max-width: '.$fact['WIDTH'].'px;';
                        }

                        $cells .= html_tag('th', $cellAttr, Lang::line($colName));                                                               
                    }
                }                            
            }                     
            
            foreach ($facts as $fact) {
                
                if (array_key_exists(Str::upper($fact['PARAM_PATH']), $getDtlColArr)) continue;
                
                $cellAttr = array('class' => 'kpiDmDtl'.$fact['PARAM_PATH'], 'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH'], 'data-col-name' => $fact['PARAM_PATH'], 'style' => 'background-color:#e1f0ff;text-align:center;');
                
                if (isset($fact['MIN_VALUE']) && $fact['MIN_VALUE'] != '') {
                    $cellAttr['data-min-value'] = $fact['MIN_VALUE'];
                }
                
                if (isset($fact['MAX_VALUE']) && $fact['MAX_VALUE'] != '') {
                    $cellAttr['data-max-value'] = $fact['MAX_VALUE'];
                }
                
                if (isset($cellAttr['data-min-value']) || isset($cellAttr['data-max-value'])) {
                    $cellAttr['data-indicator-criteria'] = '1';
                }
                
                if (isset($fact['WIDTH']) && $fact['WIDTH']) {
                    $cellAttr['style'] = 'background-color:#e1f0ff;text-align:center;width: '.$fact['WIDTH'].'px;min-width: '.$fact['WIDTH'].'px;max-width: '.$fact['WIDTH'].'px;';
                }
                
                $cells .= html_tag('th', $cellAttr, Lang::line($fact['LABEL_NAME']));
            }
        }
        
        return html_tag('tr', array('class' => 'kpi-grid-header-row'), $cells);
    }
    
    public function getRenderKpiTemplateGridBody($indicators, $facts, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null, $processHeaderParam, $getDtlCol = null) {
        
        self::$formType = 'grid';
        
        $pathPrefix     = Mdform::$pathPrefix;
        $rowCountPrefix = Mdform::$rowCountPrefix;
        $dtlIdField     = (Mdform::$isIndicatorRendering) ? 'MAP_ID' : 'DTL_ID';
        $mainFactCount  = count($facts);
        
        $rows = '';
        $index = 1;
        
        foreach ($indicators as $k => $indicator) {
                
            if ($indicator['PARENT_ID'] == $parent) {
                
                $savedDataRow  = $dtlId = null;
                $templateDtlId = $indicator['DTL_ID'];
                $factCount     = $indicator['FACT_COUNT'];
                
                if ($factCount && $savedData) {
                    
                    if (Mdform::$kpiTypeCode == 2) {
                        
                        $dtlId = isset($savedData['ID']) ? $savedData['ID'] : null;
                        $savedDataRow['fact1'] = isset($savedData[$indicator['COLUMN_NAME']]) ? $savedData[$indicator['COLUMN_NAME']] : null;
                        
                    } else {
                        $arr = array_filter($savedData, function($ar) use($templateDtlId) {
                            return ($ar['templatedtlid'] == $templateDtlId);
                        });

                        unset($savedData[key($arr)]);

                        foreach ($arr as $row) {
                            $savedDataRow = $row;
                        }

                        $dtlId = isset($savedDataRow['id']) ? $savedDataRow['id'] : null;
                    }
                }
        
                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                $isHidden = false;
                $isParentAccordion = false;
                
                Mdform::$kpiControlIndex++;
                
                if (isset(Mdform::$kpiTempCriteria[$templateDtlId]) || $factCount == 0) {
                    
                    Mdform::$parentDtlId = $templateDtlId;
                    
                    Mdform::$kpiControlIndex = Mdform::$kpiControlIndex - 1;
                }
                
                if (Mdform::$isIndicatorMerge && $depth == 0) { 
                       
                    self::$parentIndicator = array('name' => $indicator['INDICATOR_NAME'], 'childCount' => $indicator['CHILD_COUNT']);
                }
                
                if (!Mdform::$isUseMergeMatrix) {
                    
                    /*if ($indicator['CHILD_COUNT']) {
                        Mdform::$tmpParentDtlId = $templateDtlId;
                    }*/
                    
                    $childRows = self::getRenderKpiTemplateGridBody($indicators, $facts, $cellControlDatas, $savedData, $indicator['DTL_ID'], $depth + 1, $levelNum.'.', $processHeaderParam, $getDtlCol);
                    
                    /*if ($indicator['CHILD_COUNT']) {
                        Mdform::$tmpParentDtlId = null;
                    }*/
                    
                } else {
                    $childRows = '';
                }
                
                if ($childRows) {
                    $isBold = ' font-weight-bold';
                }
                
                if ($depth > 0) {
                    Mdform::$tmpParentDtlId = $parent;
                }
                
                Mdform::$indicatorLevel = $depth + 1;
                $cellControl = '';
                $rowFacts = $facts;
                
                if (!$factCount && Mdform::$kpiRenderType == 'form_left_label' && $isBold != '') {
                    $isParentAccordion = true;
                    $rowFacts = array();
                }
                
                $descr = $rowHideClass = '';

                if ($indicator['HELP_PROCESS_META_ID']) {
                    $descr = ' <i class="fas fa-question-circle text-primary cursor-pointer" title="Энд дарна уу" onclick="kpiTmpltDrillByDtlId(this, \''.$indicator['HELP_PROCESS_META_ID'].'\', \''.$templateDtlId.'\')"></i>';
                } elseif ($indicator['DESCRIPTION']) {
                    $descr = ' <i class="fas fa-info-circle text-grey-700" data-qtip-title="'.$indicator['DESCRIPTION'].'"></i>';
                }                
                
                $rowFactGroupArr = $getDtlColArr = [];
                if ($getDtlCol) {
                    $getDtlColArr = Arr::groupByArray($getDtlCol, 'COLUMN_NAME');
                    $rowFactGroupArr = Arr::groupByArray($rowFacts, 'PARAM_PATH');

                    foreach ($getDtlCol as $row) {
                        $colName = Str::lower($row['COLUMN_NAME']);
                        $colNum = (int) filter_var($colName, FILTER_SANITIZE_NUMBER_INT);

                        if (array_key_exists('fact'.$colNum, $rowFactGroupArr) && strpos($colName, 'fact') !== false) {

                            $cellVal = $indicator['COL'.$colNum];
                            $inlineStyle = $row['STYLE_SCRIPT'];
                            if ($row['WIDTH']) {
                                $inlineStyle = 'width: '.$row['WIDTH'].';min-width: '.$row['WIDTH'].';'.$inlineStyle;
                            }
                            if ($row['SHOW_TYPE'] === 'bigdecimal' && is_numeric($cellVal)) {
                                $inlineStyle = 'text-align: right !important;'.$inlineStyle;
                                if ($cellVal) {
                                    $cellVal = Number::fractionRange($cellVal, 2);
                                }
                            }
                            
                            $fact = $rowFactGroupArr['fact'.$colNum]['row'];
                            Mdform::$kpiFactShowType = '';

                            $indicatorCell = $control = '';

                            if (Mdform::$isKpiTempCriteria && isset(Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']])) {

                                $factCriteria = Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']];

                                $factCriteriaLabel = $minVal = $maxVal = '';

                                if (issetParam($factCriteria['minValue']) != '') {
                                    $factCriteriaLabel .= $factCriteria['minValue'] . ' - ';
                                    $minVal = $factCriteria['minValue'];
                                }

                                if (issetParam($factCriteria['maxValue']) != '') {
                                    $factCriteriaLabel .= $factCriteria['maxValue'];
                                    $maxVal = $factCriteria['maxValue'];
                                }

                                $indicatorCell = '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-min-val="'.$minVal.'" data-max-val="'.$maxVal.'">' . $factCriteriaLabel . '</span>';

                                if ($fact['AGGREGATE_FUNCTION']) {
                                    $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                                }

                            } else {
                                $control = self::kpiFormControl($rowIndex, $cellControlDatas, $indicator[$dtlIdField], $fact['ID'], $savedDataRow, $indicator, $processHeaderParam);
                            }

                            if ($control) {

                                $isHidden = true;

                                if ($fact['AGGREGATE_FUNCTION'] && $childRows) {
                                    $indicatorCell .= '<span class="aggregate-indicator-total" data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-aggr-input="1" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                                }

                            } elseif ($indicatorCell == '') {

                                $indicatorCell .= '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'"></span>';

                                if ($fact['AGGREGATE_FUNCTION']) {
                                    $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                                }
                            }                            
                            $cellControl .= html_tag('td', 
                                array(
                                    'class' => 'kpiDmDtl'.$fact['PARAM_PATH'].' stretchInput middle text-center', 
                                    'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH'],
                                    'style' => $inlineStyle
                                ), 
                                $control . $indicatorCell
                            );                               
                        }
                    }                            
                }                
                
                foreach ($rowFacts as $fact) {
                    
                    if (array_key_exists(Str::upper($fact['PARAM_PATH']), $getDtlColArr)) continue;
                    
                    Mdform::$kpiFactShowType = '';
                    
                    $indicatorCell = $control = '';
                    
                    if (Mdform::$isKpiTempCriteria && isset(Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']])) {
                        
                        $factCriteria = Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']];
                        
                        $factCriteriaLabel = $minVal = $maxVal = '';
                        
                        if (issetParam($factCriteria['minValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['minValue'] . ' - ';
                            $minVal = $factCriteria['minValue'];
                        }
                        
                        if (issetParam($factCriteria['maxValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['maxValue'];
                            $maxVal = $factCriteria['maxValue'];
                        }
                        
                        $indicatorCell = '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-min-val="'.$minVal.'" data-max-val="'.$maxVal.'">' . $factCriteriaLabel . '</span>';
                        
                        if ($fact['AGGREGATE_FUNCTION']) {
                            $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                        }
                        
                    } else {
                        $control = self::kpiFormControl($rowIndex, $cellControlDatas, $indicator[$dtlIdField], $fact['ID'], $savedDataRow, $indicator, $processHeaderParam);
                    }
                    
                    if ($control) {
                        
                        $isHidden = true;
                        
                        if ($fact['AGGREGATE_FUNCTION'] && $childRows) {
                            $indicatorCell .= '<span class="aggregate-indicator-total" data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-aggr-input="1" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                        }
                        
                    } elseif ($indicatorCell == '') {
                        
                        $indicatorCell .= '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'"></span>';
                        
                        if ($fact['AGGREGATE_FUNCTION']) {
                            $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                        }
                    }
                    
                    $cellControl .= html_tag('td', 
                        array(
                            'class' => 'kpiDmDtl'.$fact['PARAM_PATH'].' stretchInput middle text-center', 
                            'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH']
                        ), 
                        $control . $indicatorCell
                    );                    
                }
                
                if ($isHidden) {
                    
                    $isInput = 1;
                    $hiddenCount = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.'.$rowCountPrefix, 'data-path' => 'kpiDmDtl.rowCount'));
                    $hiddenId = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.id]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.id', 'value' => $dtlId, 'data-field-name' => 'id'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.indicatorId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.indicatorId', 'value' => $indicator['INDICATOR_ID'], 'data-field-name' => 'indicatorId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.templateDtlId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.templateDtlId', 'value' => $templateDtlId, 'data-field-name' => 'templateDtlId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rowState]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rowState', 'value' => 'modified', 'data-field-name' => 'rowState'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rootTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rootTemplateId', 'value' => Mdform::$rootTemplateId, 'data-field-name' => 'rootTemplateId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.factType]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.factType', 'value' => Mdform::$kpiFactShowType));

                    if (Mdform::$kpiTypeCode == 2) {
                        
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.kpiTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.kpiTemplateId', 'value' => Mdform::$kpiTemplateId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.pdfColumnName]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.pdfColumnName', 'value' => $indicator['COLUMN_NAME']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.defaultTplId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.defaultTplId', 'value' => Mdform::$defaultTplSavedId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.dimensionId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.dimensionId', 'value' => $indicator['DIMENSION_ID']));
                        
                    } elseif (issetParam($processHeaderParam['subKpiDmDtl']) == '1' && isset($processHeaderParam['indicatorId'])) {
                        
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.bookId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.bookId', 'value' => $processHeaderParam['bookId']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rootIndicatorId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rootIndicatorId', 'value' => $processHeaderParam['indicatorId']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.subTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.subTemplateId', 'value' => Mdform::$kpiTemplateId));
                    }
                    
                } else {
                    $isInput = 0;
                    $hiddenCount = $hiddenId = '';
                }
                
                $cells = array();
                
                if (Mdform::$isIndicatorMerge == false) {
                    
                    if (!Mdform::$isUseMergeMatrix) {
                                                
                        $showColCnt = intval($indicator['SHOW_COL_CNT']);
                                                
                        $cells[] = html_tag('td', array('class' => 'kpi-grid-rownum-cell text-center middle'.$isBold), '<span>'.$levelNum.'</span>' . $hiddenCount . $hiddenId);
                        
                        if ($isParentAccordion == false) {                                                       
                            
                            $getDtlColArr = [];
                            if ($getDtlCol) {
                                $getDtlColArr = Arr::groupByArray($getDtlCol, 'COLUMN_NAME');
                                
                                $indicatorTableName = false;  
                            
                                foreach ($getDtlCol as $row) {
                                    $colName = Str::lower($row['COLUMN_NAME']);
                                    $colNum = (int) filter_var($colName, FILTER_SANITIZE_NUMBER_INT);
                                    
                                    if ($colName === 'indicator') {
                                        $cellVal = $indicator['INDICATOR_NAME'];
                                        $inlineStyle = $row['STYLE_SCRIPT'];
                                        if ($row['WIDTH']) {
                                            $inlineStyle = 'width: '.$row['WIDTH'].';min-width: '.$row['WIDTH'].';'.$inlineStyle;
                                        }
                                        if ($row['SHOW_TYPE'] === 'bigdecimal' && is_numeric($cellVal)) {
                                            $inlineStyle = 'text-align: right !important;'.$inlineStyle;
                                            if ($cellVal) {
                                                $cellVal = Number::fractionRange($cellVal, 2);
                                            }
                                        }                                          
                                        if ($index == 1) {
                                            $cells[] = html_tag('td', array('class' => 'text-left middle padding-3 font-weight-bold', 'rowspan' => self::$parentIndicator['childCount']), self::$parentIndicator['name']);                
                                        }                                                                               
                                        $cells[] = html_tag('td', array('data-colcode' => $colName, 'data-cell-path' => 'kpiDmDtl.'.$colName, 'data-merge-cell' => $row['IS_MERGE'] ? 'true' : 'false', 'class' => 'text-left middle padding-3', 'style' => $inlineStyle), $cellVal);                                    
                                        $indicatorTableName = true;
                                        
                                    } elseif (array_key_exists('COL'.$colNum, $indicator) && strpos($colName, 'col') !== false) {
                                        
                                        $cellVal = $indicator['COL'.$colNum];
                                        $inlineStyle = $row['STYLE_SCRIPT'];
                                        if ($row['WIDTH']) {
                                            $inlineStyle = 'width: '.$row['WIDTH'].';min-width: '.$row['WIDTH'].';'.$inlineStyle;
                                        }                                    
                                        if ($row['SHOW_TYPE'] === 'bigdecimal' && is_numeric($cellVal)) {
                                            $inlineStyle = 'text-align: right !important;'.$inlineStyle;
                                            if ($cellVal) {
                                                $cellVal = Number::fractionRange($cellVal, 2);
                                            }
                                        }
                                        $colClass = '';

                                        if (is_numeric($cellVal) && $cellVal != 0) {
                                            $cellVal = number_format($cellVal, 2, '.', ',');
                                            $colClass = ' kpi-colcell-amount';
                                        }

                                        $cells[] = html_tag('td', array('data-merge-cell' => $row['IS_MERGE'] ? 'true' : 'false', 'class' => 'text-left middle padding-3 kpi-lbl-cell'.$colClass, 'data-colcode' => $colName, 'data-cell-path' => 'kpiDmDtl.'.$colName), $cellVal);                
                                    }
                                }
                                if (!$indicatorTableName) {
                                    array_unshift($cells, html_tag('td', array('class' => 'text-left middle padding-3 kpi-lbl-cell kpi-indicator-cell cell-depth-'. $depth . $isBold), Str::cleanOut(Str::nlTobr($indicator['INDICATOR_NAME'])) . $descr));
                                }                                      
                            } else {
                                $cells[] = html_tag('td', array('class' => 'text-left middle padding-3 kpi-lbl-cell kpi-indicator-cell cell-depth-'. $depth . $isBold), Str::cleanOut(Str::nlTobr($indicator['INDICATOR_NAME'])) . $descr);                                
                            }                            
                            
                            for ($i = 1; $i <= $showColCnt; $i++) {
                                if (!isset($getDtlColArr['COL'.$i]) && array_key_exists('COL'.$i, $indicator)) {

                                    $colValue = $indicator['COL'.$i];
                                    $colClass = '';

                                    if (is_numeric($colValue) && $colValue != 0) {
                                        $colValue = number_format($colValue, 2, '.', ',');
                                        $colClass = ' kpi-colcell-amount';
                                    }

                                    $cells[] = html_tag('td', array('class' => 'text-left middle padding-3 kpi-lbl-cell'.$colClass, 'data-colcode' => 'col'.$i, 'data-cell-path' => 'kpiDmDtl.col'.$i), $colValue);                
                                }
                            } 
                            
                            if (Mdform::$kpiRenderType == 'form_left_label' && $parent) {
                                $rowHideClass = 'd-none';
                            }
                            
                        } else {
                            
                            $cells[] = html_tag('td', 
                                array(
                                    'colspan' => $mainFactCount + 1, 
                                    'class' => 'text-left pl-3 p-2 middle kpi-lbl-cell kpi-indicator-cell cell-depth-'. $depth . $isBold
                                ), 
                                '<a href="javascript:;" class="kpi-indicator-child-collapse text-uppercase">'.Str::cleanOut(Str::nlTobr($indicator['INDICATOR_NAME'])) . ' <i class="far fa-plus-square"></i></a>'
                            );
                        }
                        
                        $cells[] = $cellControl;
                        
                        if ($indicator['IS_SHOW'] == '3') {
                            $rowHideClass = 'd-none';
                        }

                        $rows .= html_tag('tr', 
                            array(
                                'data-dtl-code' => $indicator['DTL_CODE'], 
                                'data-is-input' => $isInput, 
                                'data-row-index' => $rowIndex, 
                                'data-dtl-id' => $templateDtlId, 
                                'data-dtl-parentid' => $parent, 
                                'class' => $rowHideClass
                            ), 
                            implode('', $cells)
                        );

                        $rows .= $childRows;
                    
                    } else {
                        
                        $matrixCells = array();
                        $matrixCells[] = '<td data-merge-cell="true" class="text-center middle kpi-num-cell">'.$indicator['NUMBER_COLUMN'].'</td>';
                        
                        for ($c = 1; $c <= Mdform::$mergeColCount; $c++) {
                            $matrixCells[] = '<td data-merge-cell="true" class="middle padding-3">'.$indicator['COLUMN' . $c].'</td>';
                        }
                        
                        $cells[] = implode('', $matrixCells);
                        $cells[] = html_tag('td', array('class' => 'd-none'), $hiddenCount . $hiddenId);
                        $cells[] = $cellControl;

                        $rows .= html_tag('tr', 
                            array(
                                'data-dtl-code'  => $indicator['DTL_CODE'], 
                                'data-is-input'  => $isInput, 
                                'data-row-index' => $rowIndex, 
                                'class'          => 'kpi-row-'.$indicator['ROW_STYLE'], 
                                'data-descr'     => $indicator['IN_DESCRIPTION']
                            ), 
                            implode('', $cells)
                        );
                    }
                    
                } else {
                    
                    if ($depth > 0) {
                        
                        $cells[] = html_tag('td', array('class' => 'text-center middle'), '<span>'.$levelNum.'</span>' . $hiddenCount . $hiddenId);                                                                                     
                        
                        $getDtlColArr = [];
                        if ($getDtlCol) {
                            $getDtlColArr = Arr::groupByArray($getDtlCol, 'COLUMN_NAME');
                            $indicatorTableName = false;
                            
                            foreach ($getDtlCol as $row) {
                                $colName = Str::lower($row['COLUMN_NAME']);
                                $colNum = (int) filter_var($colName, FILTER_SANITIZE_NUMBER_INT);
                                
                                if ($colName === 'indicator') {
                                    $cellVal = $indicator['INDICATOR_NAME'];
                                    $inlineStyle = $row['STYLE_SCRIPT'];
                                    
                                    if ($row['WIDTH']) {
                                        $inlineStyle = 'width: '.$row['WIDTH'].';min-width: '.$row['WIDTH'].';'.$inlineStyle;
                                    }
                                    if ($row['SHOW_TYPE'] === 'bigdecimal' && is_numeric($cellVal)) {
                                        $inlineStyle = 'text-align: right !important;'.$inlineStyle;
                                        if ($cellVal) {
                                            $cellVal = Number::fractionRange($cellVal, 2);
                                        }
                                    }    
                                    if ($index == 1) {
                                        $cells[] = html_tag('td', array('class' => 'text-left middle padding-3 font-weight-bold', 'rowspan' => self::$parentIndicator['childCount']), self::$parentIndicator['name']);                
                                    }                                       
                                    $cells[] = html_tag('td', array('data-colcode' => $colName, 'data-cell-path' => 'kpiDmDtl.'.$colName, 'data-merge-cell' => $row['IS_MERGE'] ? 'true' : 'false', 'class' => 'text-left middle padding-3', 'style' => $inlineStyle), $cellVal);                                    
                                    $indicatorTableName = true;
                                    
                                } elseif (array_key_exists('COL'.$colNum, $indicator) && strpos($colName, 'col') !== false) {
                                    
                                    $cellVal = $indicator['COL'.$colNum];
                                    $inlineStyle = $row['STYLE_SCRIPT'];
                                    if ($row['WIDTH']) {
                                        $inlineStyle = 'width: '.$row['WIDTH'].';min-width: '.$row['WIDTH'].';'.$inlineStyle;
                                    }
                                    if ($row['SHOW_TYPE'] === 'bigdecimal' && is_numeric($cellVal)) {
                                        $inlineStyle = 'text-align: right !important;'.$inlineStyle;
                                        if ($cellVal) {
                                            $cellVal = Number::fractionRange($cellVal, 2);
                                        }
                                    }
                                    $cells[] = html_tag('td', array('data-colcode' => $colName, 'data-cell-path' => 'kpiDmDtl.'.$colName, 'data-merge-cell' => $row['IS_MERGE'] ? 'true' : 'false', 'class' => 'text-left middle padding-3', 'style' => $inlineStyle), $cellVal);                
                                }
                            }
                            if (!$indicatorTableName) {
                                array_unshift($cells, html_tag('td', array('class' => 'text-left middle padding-3 kpi-lbl-cell kpi-indicator-cell cell-depth-'. $depth . $isBold), Str::cleanOut(Str::nlTobr($indicator['INDICATOR_NAME'])) . $descr));
                                if ($index == 1) {
                                    array_unshift($cells, html_tag('td', array('class' => 'text-left middle padding-3 font-weight-bold', 'rowspan' => self::$parentIndicator['childCount']), self::$parentIndicator['name']));                
                                }                                   
                            }                                
                        } else {
                            if ($index == 1) {
                                $cells[] = html_tag('td', array('class' => 'text-left middle padding-3 font-weight-bold', 'rowspan' => self::$parentIndicator['childCount']), self::$parentIndicator['name']);                
                            }                                                                   
                            $cells[] = html_tag('td', array('class' => 'text-left middle padding-3'), $indicator['INDICATOR_NAME']);
                        }
                        
                        $showColCnt = intval($indicator['SHOW_COL_CNT']);
                        for ($i = 1; $i <= $showColCnt; $i++) {
                            if (!array_key_exists('COL'.$i, $getDtlColArr) && array_key_exists('COL'.$i, $indicator)) {
                                $cells[] = html_tag('td', array('class' => 'text-left middle padding-3', 'data-colcode' => 'col'.$i, 'data-cell-path' => 'kpiDmDtl.col'.$i), $indicator['COL'.$i]);                
                            }
                        } 
                        
                        $cells[] = $cellControl;                        

                        $rows .= html_tag('tr', 
                            array(
                                'data-dtl-code'  => $indicator['DTL_CODE'], 
                                'data-is-input'  => $isInput, 
                                'data-row-index' => $rowIndex
                            ), 
                            implode('', $cells)
                        );
                    }
                    
                    $rows .= $childRows;
                }
                
                $index ++;
            }
        }
        
        return $rows;
    }
    
    public function getRenderKpiTemplateHorizontalForm($indicators, $facts, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null, $processHeaderParam) {
        
        self::$formType = 'grid';
        
        $pathPrefix     = Mdform::$pathPrefix;
        $rowCountPrefix = Mdform::$rowCountPrefix;
        
        if (Mdform::$firstTplId) {
            Mdform::$defaultTplSavedId = Mdform::$kpiTemplateId;
        }
                
        $rows = '';
        $index = 1;
        $objectTabs = $graphTabs = array();
        
        foreach ($indicators as $k => $indicator) {

            if ($indicator['PARENT_ID'] == $parent) {

                $savedDataRow  = $dtlId = null;
                $templateDtlId = $indicator['DTL_ID'];
                $factCount     = $indicator['FACT_COUNT'];
                $descr         = ($indicator['DESCRIPTION'] ? ' <i class="fa fa-info-circle text-grey-700" data-qtip-title="'.$indicator['DESCRIPTION'].'"></i>' : '');

                if ($factCount && $savedData) {

                    if (Mdform::$kpiTypeCode == 2) {

                        $dtlId = isset($savedData['ID']) ? $savedData['ID'] : null;
                        $savedDataRow['fact1'] = isset($savedData[$indicator['COLUMN_NAME']]) ? $savedData[$indicator['COLUMN_NAME']] : null;

                    } else {
                        $arr = array_filter($savedData, function($ar) use($templateDtlId) {
                            return ($ar['templatedtlid'] == $templateDtlId);
                        });

                        unset($savedData[key($arr)]);

                        foreach ($arr as $row) {
                            $savedDataRow = $row;
                        }

                        $dtlId = isset($savedDataRow['id']) ? $savedDataRow['id'] : null;
                    }
                }

                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                $isHidden = false;

                Mdform::$kpiControlIndex++;

                if (isset(Mdform::$kpiTempCriteria[$templateDtlId]) || $factCount == 0) {

                    Mdform::$parentDtlId = $templateDtlId;

                    Mdform::$kpiControlIndex = Mdform::$kpiControlIndex - 1;
                }

                $childRows = null; 

                if ($childRows) {
                    $isBold = ' font-weight-bold';
                }

                $cellControl = '';

                foreach ($facts as $fact) {

                    Mdform::$kpiFactShowType = '';
                    $indicatorCell = $control = '';

                    if (Mdform::$isKpiTempCriteria && isset(Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']])) {

                        $factCriteria = Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']];

                        $factCriteriaLabel = $minVal = $maxVal = '';

                        if (issetParam($factCriteria['minValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['minValue'] . ' - ';
                            $minVal = $factCriteria['minValue'];
                        }

                        if (issetParam($factCriteria['maxValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['maxValue'];
                            $maxVal = $factCriteria['maxValue'];
                        }

                        $indicatorCell = '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-min-val="'.$minVal.'" data-max-val="'.$maxVal.'">' . $factCriteriaLabel . '</span>';

                    } else {

                        self::$isObjectType = false;
                        self::$isGraphType = false;

                        $control = self::kpiFormControl($rowIndex, $cellControlDatas, $indicator['DTL_ID'], $fact['ID'], $savedDataRow, $indicator, $processHeaderParam);

                        if (self::$isObjectType) {

                            $indctrName = self::getIndicatorNameByDtlIdModel($templateDtlId, $indicator['INDICATOR_NAME']);

                            $objectTabs[$templateDtlId] = array(
                                'control' => $control, 
                                'tabName' => $indctrName 
                            );

                            $isHidden = true;

                            continue;

                        } elseif (self::$isGraphType) {

                            $graphTabs[$templateDtlId] = array(
                                'control' => $control, 
                                'tabName' => $indicator['INDICATOR_NAME'] 
                            );

                            $isHidden = true;

                            continue;
                        }
                    }
                    
                    $cellControl .= html_tag('td', 
                        array(
                            'class' => 'kpiDmDtl'.$fact['PARAM_PATH'].' stretchInput middle text-left pt5', 
                            'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH']
                        ), 
                        $control . $indicatorCell
                    );   

                    if ($control) {
                        $isHidden = true;
                    }
                }

                if ($isHidden) {

                    $isInput = 1;
                    $hiddenCount = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.'.$rowCountPrefix, 'data-path' => 'kpiDmDtl.rowCount'));
                    $hiddenId = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.id]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.id', 'value' => $dtlId, 'data-field-name' => 'id'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.indicatorId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.indicatorId', 'value' => $indicator['INDICATOR_ID'], 'data-field-name' => 'indicatorId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.templateDtlId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.templateDtlId', 'value' => $templateDtlId, 'data-field-name' => 'templateDtlId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rowState]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rowState', 'value' => 'modified', 'data-field-name' => 'rowState'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rootTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rootTemplateId', 'value' => Mdform::$rootTemplateId, 'data-field-name' => 'rootTemplateId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.factType]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.factType', 'value' => Mdform::$kpiFactShowType));

                    if (Mdform::$kpiTypeCode == 2) {
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.kpiTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.kpiTemplateId', 'value' => Mdform::$kpiTemplateId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.pdfColumnName]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.pdfColumnName', 'value' => $indicator['COLUMN_NAME']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.defaultTplId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.defaultTplId', 'value' => Mdform::$defaultTplSavedId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.dimensionId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.dimensionId', 'value' => $indicator['DIMENSION_ID']));
                    }

                } else {
                    $isInput = 0;
                    $hiddenCount = $hiddenId = '';
                }

                if (self::$isObjectType || self::$isGraphType) {
                    
                    $rows .= '<tr data-dtl-code="'.$indicator['DTL_CODE'].'" data-is-input="0" class="d-none">';
                        $rows .= html_tag('td', array('colspan' => '2'), $hiddenCount . $hiddenId);
                    $rows .= '</tr>';

                } else {
                    
                    $indicatorName = $indicator['INDICATOR_NAME'] . $descr;
                    
                    if ($indicator['REF_ID']) {
                        $indicatorName = '<a href="javascript:;" onclick="kpiIndicatorDrillProcess(this, \''.$indicator['REF_ID'].'\');">' . $indicatorName . ' <i class="fa fa-external-link"></i></a>';
                    }
                    
                    $rows .= '<tr data-dtl-code="'.$indicator['DTL_CODE'].'" data-is-input="'.$isInput.'">';
                        $rows .= html_tag('td', array('style' => 'width: 25%; vertical-align:top;', 'class' => 'text-right line-height-normal pt12 pr10 cell-depth-'. $depth . $isBold), $hiddenCount . $hiddenId . $indicatorName . ':');
                        $rows .= $cellControl;
                    $rows .= '</tr>';
                }

                $index ++;
            }
        }
        
        return array('rows' => $rows, 'objectTabs' => $objectTabs, 'graphTabs' => $graphTabs);
    }
    
    public function getIndicatorNameByDtlIdModel($dtlId, $defaultName) {
        
        $langPh = $this->db->Param(0);
        $idPh = $this->db->Param(1);
        
        $name = $this->db->GetRow("
            SELECT 
                CASE WHEN $langPh != 'mn' THEN KT_SRC.NAME2 ELSE KT_SRC.NAME END AS NAME1, 
                CASE WHEN $langPh != 'mn' THEN KRT.CODE ELSE KRT.NAME END AS NAME2, 
                CASE WHEN $langPh != 'mn' THEN KT_TRG.NAME2 ELSE KT_TRG.NAME END AS NAME3    
            FROM KPI_TEMPLATE_DTL KTD
                INNER JOIN KPI_TEMPLATE_MAP KTM ON KTD.ID = KTM.SRC_TEMPLATE_DTL_ID
                INNER JOIN KPI_TEMPLATE KT_SRC ON KTM.SRC_TEMPLATE_ID = KT_SRC.ID
                INNER JOIN KPI_TEMPLATE KT_TRG ON KTM.TRG_TEMPLATE_ID = KT_TRG.ID
                LEFT JOIN KPI_RELATION_TYPE KRT ON KTD.RELATION_TYPE_ID = KRT.ID
            WHERE KTD.ID = $idPh", 
            array($this->lang->getCode(), $dtlId)
        );
        
        if ($name) {
            return $this->lang->line($name['NAME1']).' <span class="text-grey">'.$this->lang->line($name['NAME2']).'</span> '.$this->lang->line($name['NAME3']);
        }
        
        return $defaultName;
    }
    
    public function getRenderKpiTemplateGridBodyByPrint($indicators, $facts, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null) {
        
        self::$formType = 'grid';
        
        $rows = '';
        $index = 1;
        
        foreach ($indicators as $k => $indicator) {
                
            if ($indicator['PARENT_ID'] == $parent) {
                
                $savedDataRow = null;
                $templateDtlId = $indicator['DTL_ID'];
                $dtlId = null;
                
                if ($savedData) {
                    
                    $arr = array_filter($savedData, function($ar) use($templateDtlId) {
                        return ($ar['templatedtlid'] == $templateDtlId);
                    });
                    
                    unset($savedData[key($arr)]);
                    
                    foreach ($arr as $row) {
                        $savedDataRow = $row;
                    }
                    
                    $dtlId = isset($savedDataRow['id']) ? $savedDataRow['id'] : null;
                }
        
                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                
                Mdform::$kpiControlIndex++;
                
                $childRows = self::getRenderKpiTemplateGridBodyByPrint($indicators, $facts, $cellControlDatas, $savedData, $indicator['DTL_ID'], $depth + 1, $levelNum.'.');
                
                if ($childRows) {
                    $isBold = ' font-weight-bold';
                }
                
                $cells = '';
                $cells .= html_tag('td', array('class' => 'text-center middle'.$isBold), '<span>'.$levelNum.'</span>');
                $cells .= html_tag('td', array('class' => 'text-left middle cell-depth-'. $depth . $isBold), $indicator['INDICATOR_NAME']);
                
                $showColCnt = intval($indicator['SHOW_COL_CNT']);
                for ($i = 1; $i <= $showColCnt; $i++) {
                    if (array_key_exists('COL'.$i, $indicator)) {
                        $cells .= html_tag('td', array('class' => 'text-left middle padding-3 kpi-lbl-cell', 'data-colcode' => 'col'.$i, 'data-cell-path' => 'kpiDmDtl.col'.$i), $indicator['COL'.$i]);                
                    }
                } 
                
                foreach ($facts as $fact) {
                    
                    $control = self::kpiFormControlByPrint($rowIndex, $cellControlDatas, $indicator['DTL_ID'], $fact['ID'], $savedDataRow);
                    
                    $cells .= html_tag('td', 
                        array(
                            'class' => 'middle text-center', 
                            'data-colcode' => $fact['PARAM_PATH']
                        ), 
                        $control
                    );
                }

                $rows .= html_tag('tr', array(), $cells);
                
                $rows .= $childRows;
                
                $index ++;
            }
        }
        
        return $rows;
    }
    
    public function getRenderKpiTemplateFormBody($indicators, $facts, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null) {
        
        self::$formType = 'form';
        $pathPrefix     = Mdform::$pathPrefix;
        $rowCountPrefix = Mdform::$rowCountPrefix;
        $factsCount     = count($facts);
        $dtlIdField     = (Mdform::$isIndicatorRendering) ? 'MAP_ID' : 'DTL_ID';
        
        $rows = '';
        $index = 1;
        
        foreach ($indicators as $k => $indicator) {
                
            if ($indicator['PARENT_ID'] == $parent) {
                
                $savedDataRow  = $dtlId = null;
                $templateDtlId = $indicator['DTL_ID'];
                $factCount     = $indicator['FACT_COUNT'];
                
                if ($savedData) {

                    if (Mdform::$kpiTypeCode == 2) {
                        
                        $dtlId = isset($savedData['ID']) ? $savedData['ID'] : null;
                        $savedDataRow['fact1'] = isset($savedData[$indicator['COLUMN_NAME']]) ? $savedData[$indicator['COLUMN_NAME']] : null;
                        
                    } else {
                        $arr = array_filter($savedData, function($ar) use($templateDtlId) {
                            return ($ar['templatedtlid'] == $templateDtlId);
                        });

                        unset($savedData[key($arr)]);

                        foreach ($arr as $row) {
                            $savedDataRow = $row;
                        }

                        $dtlId = isset($savedDataRow['id']) ? $savedDataRow['id'] : null;
                    }                    
                }
                
                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                $isHidden = false;
                
                $cellFacts = $kpiPath = '';
                
                foreach ($facts as $fact) {
                    
                    Mdform::$kpiFactShowType = '';
                    
                    $control = self::kpiFormControl($rowIndex, $cellControlDatas, $indicator[$dtlIdField], $fact['ID'], $savedDataRow, $indicator);
                    
                    if ($control) {
                        
                        $isHidden = true;
                        
                        $cellFacts .= html_tag('div', 
                            array(
                                'class' => 'kpi-form-control kpiDmDtl'.$fact['PARAM_PATH'], 
                                'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH']
                            ), 
                            $control
                        );
                    }
                }
                
                if ($isHidden) {
                    
                    $isInput = 1;
                    $hiddenCount = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.'.$rowCountPrefix, 'data-path' => 'kpiDmDtl.rowCount'));
                
                    $hiddenId = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.id]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.id', 'value' => $dtlId, 'data-field-name' => 'id'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.indicatorId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.indicatorId', 'value' => $indicator['INDICATOR_ID'], 'data-field-name' => 'indicatorId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.templateDtlId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.templateDtlId', 'value' => $templateDtlId, 'data-field-name' => 'templateDtlId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rowState]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rowState', 'value' => 'modified', 'data-field-name' => 'rowState'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rootTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rootTemplateId', 'value' => Mdform::$rootTemplateId, 'data-field-name' => 'rootTemplateId'));
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.factType]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.factType', 'value' => Mdform::$kpiFactShowType));

                    if (Mdform::$kpiTypeCode == 2) {
                        
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.kpiTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.kpiTemplateId', 'value' => Mdform::$kpiTemplateId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.pdfColumnName]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.pdfColumnName', 'value' => $indicator['COLUMN_NAME']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.defaultTplId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.defaultTplId', 'value' => Mdform::$defaultTplSavedId));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.dimensionId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.dimensionId', 'value' => $indicator['DIMENSION_ID']));
                        
                    } elseif (issetParam($processHeaderParam['subKpiDmDtl']) == '1' && isset($processHeaderParam['indicatorId'])) {
                        
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.bookId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.bookId', 'value' => $processHeaderParam['bookId']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.rootIndicatorId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.rootIndicatorId', 'value' => $processHeaderParam['indicatorId']));
                        $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.subTemplateId]['.$rowIndex.'][]', 'data-path' => 'kpiDmDtl.subTemplateId', 'value' => Mdform::$kpiTemplateId));
                    }
                
                } else {
                    $isInput = 0;
                    $hiddenCount = $hiddenId = '';
                }
                
                $indicatorName = html_tag('div', array(
                    'class' => 'title-name' 
                ), $levelNum . '. ' . $indicator['INDICATOR_NAME']);
                
                Mdform::$kpiControlIndex++;
                
                if ($factCount == 0) {
                    Mdform::$kpiControlIndex = Mdform::$kpiControlIndex - 1;
                }
                
                $childRows = self::getRenderKpiTemplateFormBody($indicators, $facts, $cellControlDatas, $savedData, $indicator['DTL_ID'], $depth + 1, $levelNum.'.');
                
                if ($childRows) {
                    $isBold = ' font-weight-bold';
                }
                
                if (Mdform::$kpiFactShowType) {
                    $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.factType]['.$rowIndex.'][]', 'value' => Mdform::$kpiFactShowType));
                }
                
                $cells = html_tag('td', 
                    array(
                        'class' => 'text-left middle cell-depth-'.$depth.$isBold
                    ), 
                    $hiddenCount . $hiddenId . $indicatorName . $cellFacts
                );
                
                if ($factsCount == 1) {
                    $kpiPath = 'kpiDmDtl.'.$fact['PARAM_PATH'];
                }

                $rows .= html_tag('tr', 
                    array(
                        'data-dtl-code' => $indicator['DTL_CODE'], 
                        'data-kpi-path' => $kpiPath, 
                        'data-is-input' => $isInput, 
                        'data-formkpi-row' => 1
                    ), 
                    $cells
                );
                
                $rows .= $childRows;
                
                $index ++;
            }
        }
        
        return $rows;
    }
    
    public function kpiFormControl($rowIndex, $cellControlDatas, $dtlId, $factId, $savedDataRow, $indicator, $processHeaderParam = null) {
        
        $arr = array_filter($cellControlDatas, function($ar) use($dtlId, $factId) {
            return ($ar['TEMPLATE_DTL_ID'] == $dtlId && $ar['TEMPLATE_FACT_ID'] == $factId);
        });
        
        foreach ($arr as $row) {
            $rowKeyArr = $row;
        }
        
        if (isset($rowKeyArr)) {
            return self::kpiRenderParamControl($rowIndex, $rowKeyArr, $savedDataRow, $indicator, $processHeaderParam);
        }
        
        return null;
    }
    
    public function kpiFormControlByDv($rowIndex, $f, $cellControlDatas, $dtlId, $dtlCode, $factId, $savedDataRow) {
        
        $arr = array_filter($cellControlDatas, function($ar) use($dtlId, $factId) {
            return ($ar['TEMPLATE_DTL_ID'] == $dtlId && $ar['TEMPLATE_FACT_ID'] == $factId);
        });
        
        foreach ($arr as $row) {
            $rowKeyArr = $row;
        }
        
        if (isset($rowKeyArr)) {
            return self::kpiRenderParamControlDv($rowIndex, $f, $dtlCode, $rowKeyArr, $savedDataRow);
        }
        
        return null;
    }
    
    public function kpiRenderParamControlDv($rowIndex, $f, $dtlCode, $row, $val) {
                    
        $showType       = $row['SHOW_TYPE'];
        $paramPath      = $row['PARAM_PATH'];
        $paramRealPath  = Mdform::$pathPrefix.'kpiDmDtl.'.$paramPath;
        $paramPathLower = strtolower($paramPath);
        $labelName      = Mdform::$labelName;
        $inputId        = Mdform::$inputId;
        $defaultValue   = $row['DEFAULT_VALUE'];
        $control        = null;
        $kpiTemplateId  = Mdform::$kpiTemplateId;
        
        $value = ($val ? (array_key_exists($paramPathLower, $val) ? $val[$paramPathLower] : $defaultValue) : $defaultValue);
        
        switch ($showType) {
            case 'text':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$kpiTemplateId.']['.$rowIndex.']['.$inputId.']', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath.'.'.$f, 
                        'data-path-cell' => $dtlCode.'.'.$paramPath.'.'.$f, 
                        'class' => 'form-control input-sm stringInit', 
                        'placeholder' => $labelName, 
                        'value' => $value 
                    )
                );
                
            break;
        
            case 'percent':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$kpiTemplateId.']['.$rowIndex.']['.$inputId.']', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath.'.'.$f, 
                        'data-path-cell' => $dtlCode.'.'.$paramPath.'.'.$f, 
                        'class' => 'form-control input-sm integerInit', 
                        'placeholder' => $labelName, 
                        'value' => $value 
                    )
                );
                
            break;
        
            case 'decimal':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$kpiTemplateId.']['.$rowIndex.']['.$inputId.']', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath.'.'.$f, 
                        'data-path-cell' => $dtlCode.'.'.$paramPath.'.'.$f, 
                        'class' => 'form-control input-sm ', 
                        'placeholder' => $labelName, 
                        'value' => $value
                    )
                );
                
            break;
        
            default:
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$kpiTemplateId.']['.$rowIndex.']['.$inputId.']', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath.'.'.$f, 
                        'data-path-cell' => $dtlCode.'.'.$paramPath.'.'.$f, 
                        'class' => 'form-control input-sm stringInit', 
                        'placeholder' => $labelName, 
                        'value' => $value 
                    )
                );
            break;    
        }
        
        return $control;
    }
    
    public function kpiRenderParamControl($rowIndex, $row, $val, $indicator, $processHeaderParam) {
        
        $showType         = $row['SHOW_TYPE'];
        $lookupMetaDataId = $row['LOOKUP_META_DATA_ID'];
        $paramPath        = $row['PARAM_PATH'];
        $paramRealPath    = Mdform::$pathPrefix.'kpiDmDtl.'.$paramPath;
        $paramPathLower   = strtolower($paramPath);
        $labelName        = Lang::line($row['LABEL_NAME']);
        $placeholder      = self::titleReplacer($row['PLACEHOLDER_NAME'] ? Lang::line($row['PLACEHOLDER_NAME']) : $labelName);
        $lookupCriteria   = $row['LOOKUP_CRITERIA'];
        $defaultValue     = $row['DEFAULT_VALUE'];
        $dataLength       = $row['FACT_DATA_LENGTH'];
        $control          = null;
        $value            = null;
        $factWidth        = ($row['FACT_WIDTH'] ? 'width: '.$row['FACT_WIDTH'].';' : '');
        $required         = ($indicator['IS_REQUIRED'] == '1' ? array('required' => 'required') : ($row['IS_REQUIRED'] == '1' ? array('required' => 'required') : array()));
        
        Mdform::$kpiFactShowType = $showType;

        if ($val && array_key_exists($paramPathLower, $val)) {
            $value = $val[$paramPathLower];
        } 
        
        if ($value == '') {
            
            $value = Mdmetadata::setDefaultValue($defaultValue);
            
            if ($row['GET_PROCESS_ID'] && Mdform::$processParamData) {
                $getVal = self::getValueByGetProcessIdKpiModel($row['GET_PROCESS_ID'], $indicator['INDICATOR_ID']);
                if ($getVal != '') {
                    $value = $getVal;
                }
            }
        }
        
        switch ($showType) {
            case 'text':
                
                if ($row['PATTERN_TEXT'] != '') {
                    
                    $required['data-regex'] = $row['PATTERN_TEXT'];
                    $required['data-regex-message'] = Lang::line($row['GLOBE_MESSAGE']);
                    
                    if ($row['IS_MASK'] === '1') {
                        $required['data-inputmask-regex'] = $row['PATTERN_TEXT'];
                    }
                }
                
                $attrArray = array(
                    'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                    'data-path' => $paramRealPath, 
                    'data-field-name' => $paramPath, 
                    'data-col-path' => $paramPath, 
                    'class' => 'form-control input-sm stringInit', 
                    'autocomplete' => 'off', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'style' => $factWidth
                ) + $required;
                
                if ($dataLength) {
                    $attrArray['maxlength'] = $dataLength;
                    $attrArray['data-maxlength'] = 'true';
                }
                
                if (Lang::isUseMultiLang() && Mdform::$isOnlyKpiMultiLang) {
                    
                    $attrArray['data-c-name'] = $indicator['COLUMN_NAME'];
                    
                    if (Lang::getCode() != Lang::getDefaultLangCode() && $val) {
                            
                        $attrArray['data-dl-value'] = $value;

                        if (isset(Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()])) {
                            $attrArray['value'] = Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()];
                        }
                    }
                    
                    $control = '<div class="input-group">
                        '.Form::text($attrArray).'
                        <span class="input-group-append"><button class="btn btn-primary" type="button" onclick="bpFieldTranslate(this);" title="Орчуулга"><i class="fa fa-language"></i></button></span> 
                    </div>';
                    
                } else {
                    $control = Form::text($attrArray);
                }
                
            break;        
        
            case 'percent':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control input-sm integerInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value,
                        'style' => $factWidth 
                    ) + $required 
                );
                
            break;
        
            case 'decimal':
            case 'bigdecimal':
                
                $parentDtlId = Mdform::$indicatorLevel > 1 ? (Mdform::$tmpParentDtlId ? Mdform::$tmpParentDtlId : Mdform::$parentDtlId) : null;
                
                $attrArray = array(
                    'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                    'data-path' => $paramRealPath, 
                    'data-field-name' => $paramPath, 
                    'data-col-path' => $paramPath, 
                    'class' => 'form-control input-sm kpiDecimalInit decimalInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'data-parent-dtlid' => $parentDtlId, 
                    'style' => $factWidth, 
                    'data-indicator-level' => Mdform::$indicatorLevel
                ) + $required;
                
                if ($dataLength) {
                    $attrArray['data-v-max'] = Number::numberFormat($dataLength, 0);
                }
                
                $control = Form::text($attrArray);
                
            break;
        
            case 'number':
                
                $parentDtlId = Mdform::$tmpParentDtlId ? Mdform::$tmpParentDtlId : Mdform::$parentDtlId;
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control input-sm longInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-parent-dtlid' => $parentDtlId, 
                        'style' => $factWidth 
                    ) + $required 
                );
                
            break;
        
            case 'radio':
                
                if (!is_null($lookupMetaDataId)) {
                    
                    $datas = self::getRadioButtonKpiModel($lookupMetaDataId, $paramPath, $paramRealPath, $lookupCriteria);

                    $radioControl = str_replace(
                        'name="param['.$paramRealPath.'][0][]"', 
                        'name="param['.$paramRealPath.']'.Mdform::$radioPrefix.'['.$rowIndex.'][]" data-path="'.$paramRealPath.'" data-col-path="'.$paramPath.'" data-field-name="'.$paramPath.'" class="md-radio"', 
                        Form::radioMulti($datas, $value)
                    );
        
                    $control = '<div class="radio-list radioInit" data-path="'.$paramRealPath.'">'.$radioControl.'</div>';
                    
                } else {
                    $control = Form::text(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm stringInit', 
                            'placeholder' => $placeholder, 
                            'value' => $value, 
                            'style' => $factWidth 
                        ) + $required 
                    );
                }
                
            break;
        
            case 'combo':
                
                if (!is_null($lookupMetaDataId)) {
                    
                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                    $isTranslateField = '';
                    
                    if (Lang::isUseMultiLang() && Mdform::$isOnlyKpiMultiLang && is_array($datas['data']) && array_key_exists('pftranslationjson', $datas['data'][0])) {
                        
                        $required['pftranslationjson'] = $datas['nameColumnName'];
                        $isTranslateField = '<input type="hidden" name="param['.$paramRealPath.'_isTranslate]['.$rowIndex.'][]" data-path="'.$paramRealPath.'_isTranslate" value="1">';
                    }
                    
                    $attrArray = array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'data-metadataid' => $lookupMetaDataId, 
                        'data-live-search' => $lookupCriteria, 
                        'class' => 'form-control input-sm dropdownInput select2', 
                        'data' => $datas['data'], 
                        'op_value' => $datas['id'], 
                        'op_text' => $datas['name'], 
                        'op_param' => $datas['code'], 
                        'value' => $value, 
                        'style' => $factWidth 
                    ) + $required;
                    
                    $attrArray['op_custom_attr'] = array(array('key' => 'rowData', 'attr' => 'data-row-data'));
                    
                    $control = Form::select($attrArray) . $isTranslateField; 
                    
                } else {
                    
                    $control = Form::text(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm stringInit', 
                            'placeholder' => $placeholder, 
                            'value' => $value, 
                            'style' => $factWidth 
                        ) + $required 
                    );
                }
                
            break;
            
            case 'multicombo':
                
                if (!is_null($lookupMetaDataId)) {
                    
                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                    
                    $isTranslateField = '';
                    
                    if (Lang::isUseMultiLang() && Mdform::$isOnlyKpiMultiLang && is_array($datas['data']) && array_key_exists('pftranslationjson', $datas['data'][0])) {
                        
                        $required['pftranslationjson'] = $datas['nameColumnName'];
                        $isTranslateField = '<input type="hidden" name="param['.$paramRealPath.'_isTranslate]['.$rowIndex.'][]" data-path="'.$paramRealPath.'_isTranslate" value="1">';
                    }
                    
                    $control = Form::multiselect(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm dropdownInput select2', 
                            'data' => $datas['data'], 
                            'op_value' => $datas['id'], 
                            'op_text' => $datas['name'], 
                            'op_param' => $datas['code'], 
                            'value' => $value, 
                            'style' => $factWidth, 
                            'multiple' => 'multiple'
                        ) + $required 
                    ) . $isTranslateField; 
                    
                } else {
                    
                    $control = Form::text(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm stringInit', 
                            'placeholder' => $placeholder, 
                            'value' => $value, 
                            'style' => $factWidth 
                        ) + $required 
                    );
                }
                
            break;
            
            case 'popup':   
                
                if (!is_null($lookupMetaDataId)) {
                    
                    $lowerPath = strtolower($paramPath);
                    $controlConfig = array('GROUP_PARAM_CONFIG_TOTAL' => '0', 'GROUP_CONFIG_PARAM_PATH' => NULL, 'GROUP_CONFIG_LOOKUP_PATH' => NULL, 'GROUP_CONFIG_PARAM_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH' => NULL, 'GROUP_CONFIG_GROUP_PATH' => NULL, 'IS_MULTI_ADD_ROW' => '0', 'IS_MULTI_ADD_ROW_KEY' => '0', 'META_DATA_CODE' => $paramPath, 'LOWER_PARAM_NAME' => $lowerPath, 'META_DATA_NAME' => $labelName, 'DESCRIPTION' => NULL, 'ATTRIBUTE_ID_COLUMN' => NULL, 'ATTRIBUTE_CODE_COLUMN' => NULL, 'ATTRIBUTE_NAME_COLUMN' => NULL, 'IS_SHOW' => '1', 'IS_REQUIRED' => '0', 'DEFAULT_VALUE' => NULL, 'RECORD_TYPE' => NULL, 'LOOKUP_META_DATA_ID' => $lookupMetaDataId, 'LOOKUP_TYPE' => 'popup', 'CHOOSE_TYPE' => 'single', 'DISPLAY_FIELD' => null, 'VALUE_FIELD' => null, 'PARAM_REAL_PATH' => $paramRealPath, 'META_TYPE_CODE' => 'long', 'TAB_NAME' => NULL, 'SIDEBAR_NAME' => NULL, 'FEATURE_NUM' => NULL, 'IS_SAVE' => NULL, 'FILE_EXTENSION' => NULL, 'PATTERN_TEXT' => NULL, 'PATTERN_NAME' => NULL, 'GLOBE_MESSAGE' => NULL, 'IS_MASK' => NULL, 'COLUMN_WIDTH' => NULL, 'COLUMN_AGGREGATE' => NULL, 'SEPARATOR_TYPE' => NULL, 'GROUP_LOOKUP_META_DATA_ID' => NULL, 'IS_BUTTON' => '1', 'COLUMN_COUNT' => NULL, 'MAX_VALUE' => NULL, 'MIN_VALUE' => NULL, 'IS_SHOW_ADD' => NULL, 'IS_SHOW_DELETE' => NULL, 'IS_SHOW_MULTIPLE' => NULL, 'LOOKUP_KEY_META_DATA_ID' => NULL, 'IS_REFRESH' => '0', 'FRACTION_RANGE' => NULL, 'GROUPING_NAME' => NULL, 'PLACEHOLDER_NAME' => null);
                    $rowData = null;
                    
                    if ($value) {
                        $rowData = array($lowerPath => $value);
                    }
                    
                    $control = Mdwebservice::renderParamControl('1479459173907', $controlConfig, 'param['.$paramRealPath.']['.$rowIndex.'][]', $paramPath, $rowData);
                    $control = str_replace('type="hidden"', 'type="hidden" data-col-path="'.$paramPath.'"', $control);
                    
                } else {
                    
                    $control = Form::text(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm stringInit', 
                            'placeholder' => $placeholder, 
                            'value' => $value, 
                            'style' => $factWidth 
                        ) + $required 
                    );
                }
                
            break;
            
            case 'childObject':    
                
                $lowerPath = strtolower($paramPath);
                $controlConfig = array('GROUP_PARAM_CONFIG_TOTAL' => '0', 'GROUP_CONFIG_PARAM_PATH' => NULL, 'GROUP_CONFIG_LOOKUP_PATH' => NULL, 'GROUP_CONFIG_PARAM_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH' => NULL, 'GROUP_CONFIG_GROUP_PATH' => NULL, 'IS_MULTI_ADD_ROW' => '0', 'IS_MULTI_ADD_ROW_KEY' => '0', 'META_DATA_CODE' => $paramPath, 'LOWER_PARAM_NAME' => $lowerPath, 'META_DATA_NAME' => $labelName, 'DESCRIPTION' => NULL, 'ATTRIBUTE_ID_COLUMN' => NULL, 'ATTRIBUTE_CODE_COLUMN' => NULL, 'ATTRIBUTE_NAME_COLUMN' => NULL, 'IS_SHOW' => '1', 'IS_REQUIRED' => '0', 'DEFAULT_VALUE' => NULL, 'RECORD_TYPE' => NULL, 'LOOKUP_META_DATA_ID' => '1563781803539520', 'LOOKUP_TYPE' => 'popup', 'CHOOSE_TYPE' => 'single', 'DISPLAY_FIELD' => null, 'VALUE_FIELD' => null, 'PARAM_REAL_PATH' => $paramRealPath, 'META_TYPE_CODE' => 'long', 'TAB_NAME' => NULL, 'SIDEBAR_NAME' => NULL, 'FEATURE_NUM' => NULL, 'IS_SAVE' => NULL, 'FILE_EXTENSION' => NULL, 'PATTERN_TEXT' => NULL, 'PATTERN_NAME' => NULL, 'GLOBE_MESSAGE' => NULL, 'IS_MASK' => NULL, 'COLUMN_WIDTH' => NULL, 'COLUMN_AGGREGATE' => NULL, 'SEPARATOR_TYPE' => NULL, 'GROUP_LOOKUP_META_DATA_ID' => NULL, 'IS_BUTTON' => '1', 'COLUMN_COUNT' => NULL, 'MAX_VALUE' => NULL, 'MIN_VALUE' => NULL, 'IS_SHOW_ADD' => NULL, 'IS_SHOW_DELETE' => NULL, 'IS_SHOW_MULTIPLE' => NULL, 'LOOKUP_KEY_META_DATA_ID' => NULL, 'IS_REFRESH' => '0', 'FRACTION_RANGE' => NULL, 'GROUPING_NAME' => NULL, 'PLACEHOLDER_NAME' => null);
                
                $rowData = $trgTempId = null;
                    
                if ($value) {
                    
                    $getDvRowData = self::getKpiObjectRelationRowData($value, $row['TEMPLATE_DTL_ID']);
                    
                    if ($getDvRowData) {
                        $rowData = array(
                            $lowerPath => array(
                                'id'      => $getDvRowData['id'], 
                                'code'    => $getDvRowData['name'], 
                                'name'    => $getDvRowData['name'], 
                                'rowdata' => $getDvRowData
                            )
                        );
                        $trgTempId = $getDvRowData['templateid'];
                    } else {
                        $rowData = array($lowerPath => $value);
                    }
                }
                    
                $control = Mdwebservice::renderParamControl('1479459173907', $controlConfig, 'param[kpiDmDtl.childObjectPopup]['.$rowIndex.'][]', $paramPath, $rowData);
                $control = str_replace('class="popupInit"', 'class="popupInit" data-criteria="templateDtlId='.$row['TEMPLATE_DTL_ID'].'"', $control);
                $control .= Form::hidden(array('name' => 'param[kpiDmDtl.childObjectType]['.$rowIndex.'][]', 'value' => $trgTempId));
                        
            break;
        
            case 'check':
                
                $control = Form::checkbox(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control input-sm booleanInit', 
                        'value' => '1', 
                        'saved_val' => $value 
                    )
                );
                
            break;
        
            case 'multicheck':
                    
                if (!is_null($lookupMetaDataId)) {
                    
                    $datas = self::getRadioButtonKpiModel($lookupMetaDataId, $paramPath, $paramRealPath, $lookupCriteria);
                    $multiCheckControl = array();
                    
                    if ($value) {
                        $checkedArr = array();
                        $valueArr = explode(',', $value);
                        
                        foreach ($valueArr as $valueVal) {
                            $checkedArr[$valueVal] = 1;
                        }
                    }
                    
                    foreach ($datas as $check) {
                        
                        $checkControl = '<div class="form-check form-check-inline mr10">';
                            $checkControl .= '<label class="form-check-label">';
                                $checkControl .= '<input type="checkbox" name="param['.$paramRealPath.']['.$rowIndex.'][]" value="'.$check['value'].'" '; 
                                $checkControl .= 'data-col-path="'.$paramPath.'" data-field-name="'.$paramPath.'" class="md-check mr5" ';
                                if (isset($checkedArr[$check['value']])) {
                                    $checkControl .= 'checked="checked" ';
                                }
                                $checkControl .= ((int) $dataLength ? 'data-length="'.$dataLength.'"' : '').'> '.$check['label'].'';
                            $checkControl .= '</label>';
                        $checkControl .= '</div>';
                        
                        $multiCheckControl[] = $checkControl;
                    }
                    
                    $columnCount = issetParam($row['COLUMN_COUNT']);
                    
                    if ($columnCount && $columnCount <= count($multiCheckControl)) {
                        
                        $multiCheckControlCount = count($multiCheckControl);
                        $splitArrays = array_chunk($multiCheckControl, ceil($multiCheckControlCount / $columnCount));
                        
                        $multiCheckControlHtml = '<div class="row">';
                        
                        foreach ($splitArrays as $splitArr) {
                            
                            $multiCheckControlHtml .= '<div class="col">';
                                $multiCheckControlHtml .= implode('', $splitArr);
                            $multiCheckControlHtml .= '</div>';
                        }
                        
                        $multiCheckControlHtml .= '</div>';
                        $multiCheckControlHtml = str_replace(' form-check-inline', '', $multiCheckControlHtml);
                        
                    } else {
                        $multiCheckControlHtml = implode('', $multiCheckControl);
                    }
        
                    $control = '<div class="check-list checkInit" data-path="'.$paramRealPath.'">'.$multiCheckControlHtml.'</div>';
                    
                } else {
                    $control = Form::checkbox(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm booleanInit', 
                            'value' => '1', 
                            'saved_val' => $value 
                        )
                    );
                }
                
            break;
            
            case 'description':
                
                if (self::$formType == 'grid') {
                    
                    $attrArray = array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control form-control-sm mt0', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'style' => 'min-width: 160px; height: 40px;' . $factWidth 
                    ) + $required;
                    
                } else {
                    
                    $attrArray = array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control form-control-sm', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'style' => 'height: 40px;' . $factWidth 
                    ) + $required;
                }
                
                if ($dataLength) {
                    $attrArray['maxlength'] = $dataLength;
                    $attrArray['data-maxlength'] = 'true';
                }
                
                $attrArray['spellcheck'] = 'false'; 
                
                if (Lang::isUseMultiLang() && Mdform::$isOnlyKpiMultiLang) {
                    
                    $attrArray['data-c-name'] = $indicator['COLUMN_NAME'];
                    
                    if (Lang::getCode() != Lang::getDefaultLangCode() && $val) {
                            
                        $attrArray['data-dl-value'] = $value;

                        if (isset(Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()])) {
                            $attrArray['value'] = Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()];
                        }
                    }
                    
                    $attrArray['style'] = str_replace('min-width: 160px', '', $attrArray['style']);
                    
                    $control = '<div class="input-group" style="min-width: 160px">
                        '.Form::textArea($attrArray).'
                        <span class="input-group-append"><button class="btn btn-primary" type="button" onclick="bpFieldTranslate(this);" title="Орчуулга"><i class="fa fa-language"></i></button></span> 
                    </div>';
                    
                } else {
                    $control = Form::textArea($attrArray);
                }
                
            break;
            
            case 'textareaautoleftview':
                
                if (self::$formType == 'grid') {
                    
                    $control = Form::textArea(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control form-control-sm text-left', 
                            'spellcheck' => 'false', 
                            'value' => $value, 
                            'style' => 'min-width: 160px; height: 60px;' . $factWidth 
                        ) + $required 
                    );
                    
                } else {
                    $control = Form::textArea(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control form-control-sm description_autoInit text-left', 
                            'spellcheck' => 'false', 
                            'value' => $value, 
                            'style' => 'height: 40px;' . $factWidth 
                        ) + $required 
                    );
                }
                
            break;
            
            case 'description_auto':
                
                if (self::$formType == 'grid') {
                    
                    $attrArray = array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control form-control-sm description_autoInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'style' => 'min-width: 160px; height: 60px;' . $factWidth 
                    ) + $required;
                    
                } else {
                    
                    $attrArray = array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control form-control-sm description_autoInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'style' => 'height: 40px;' . $factWidth 
                    ) + $required;
                }
                
                if ($dataLength) {
                    $attrArray['maxlength'] = $dataLength;
                    $attrArray['data-maxlength'] = 'true';
                }
                
                $attrArray['spellcheck'] = 'false';
                
                if (Lang::isUseMultiLang() && Mdform::$isOnlyKpiMultiLang) {
                    
                    $attrArray['data-c-name'] = $indicator['COLUMN_NAME'];
                    
                    if (Lang::getCode() != Lang::getDefaultLangCode() && $val) {
                            
                        $attrArray['data-dl-value'] = $value;

                        if (isset(Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()])) {
                            $attrArray['value'] = Mdform::$pfTranslationValue['value'][$indicator['COLUMN_NAME']][Lang::getCode()];
                        }
                    }
                    
                    $control = '<div class="input-group">
                        '.Form::textArea($attrArray).'
                        <span class="input-group-append"><button class="btn btn-primary" type="button" onclick="bpFieldTranslate(this);" title="Орчуулга"><i class="fa fa-language"></i></button></span> 
                    </div>';
                    
                } else {
                    $control = Form::textArea($attrArray);
                }
                
            break;

            case 'text_editor':
                
                if (self::$formType == 'grid') {
                    
                    $control = Form::textArea(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm', 
                            'value' => $value, 
                            'style' => 'min-width: 160px; height: 40px;' . $factWidth 
                        ) + $required 
                    );
                    
                } else {
                    $control = Form::textArea(
                        array(
                            'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                            'data-path' => $paramRealPath, 
                            'data-field-name' => $paramPath, 
                            'data-col-path' => $paramPath, 
                            'class' => 'form-control input-sm text_editorInit', 
                            'value' => $value,
                        ) + $required 
                    );
                }
                
            break;            
            
            case 'date':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control input-sm dateInit', 
                        'placeholder' => $labelName, 
                        'value' => $value 
                    ) + $required 
                );
                
                return html_tag('div', array(
                        'class' => 'dateElement input-group',
                        'style' => 'max-width: 131px !important;'
                    ), $control . '<span class="input-group-btn"><button tabindex="-1" onclick="return false;" class="btn"><i class="fal fa-calendar"></i></button></span>', true
                );
                
            break;
        
            case 'time':
                
                $control = Form::text(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path' => $paramPath, 
                        'class' => 'form-control input-sm timeInit', 
                        'placeholder' => $labelName, 
                        'value' => $value, 
                        'style' => 'width: 60px;' 
                    ) + $required 
                );
                
            break;
        
            case 'file':
                
                if ($fileExtension = issetParam($row['FILE_EXTENSION'])) {
                    $required['data-valid-extension'] = $fileExtension;
                }
                
                $control = Form::file(
                    array(
                        'name'            => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path'       => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path'   => $paramPath, 
                        'class'           => 'form-control input-sm fileInit', 
                        'placeholder'     => $labelName, 
                        'style'           => $factWidth 
                    ) + $required 
                );
                
                if ($value) {
                    $fileExtension = strtolower(substr($value, strrpos($value, '.') + 1));
                    $control .= html_tag('a', array('href' => 'mdobject/downloadFile?fDownload=1&file=' . $value, 'title' => 'Татах', 'class' => 'float-right', 'style' => 'width:22px;margin-top:-23px; margin-right:4px;'), '<i class="icon-download4"></i>');
                    $control .= html_tag('a', array('href'=>'javascript:;', 'data-url'=>$value, 'data-extension'=>$fileExtension, 'onclick'=>'bpFilePreview(this);', 'class'=>'float-right', 'title' => 'Харах', 'style'=>'width:26px;margin-top:-23px; margin-right:22px;'), '<i class="icon-file-picture"></i>');
                    $control .= Form::hidden(array('name' => 'editfile_param['.$paramRealPath.']['.$rowIndex.'][]', 'value' => $value));
                }
                
            break;
            
            case 'multi_file':
                
                $fileView = '';
                
                if (isset($val[$paramPathLower . '_multifile'])) {
                    
                    $fileNames = $val[$paramPathLower . '_multifile'];
                    $removeLabelName = Lang::line('delete_btn');
                    
                    foreach ($fileNames as $k => $fileRow) {
                        
                        if ($fileRow['fileextension'] == 'xls' || $fileRow['fileextension'] == 'xlsx') {
                            
                            $fileView .= '<div class="btn-group mt3 mb3">
                                <button type="button" class="btn btn-outline-success btn-sm mr0" title="'.$fileRow['filename'].'" onclick="bpFilePreview(this);" data-extension="'.$fileRow['fileextension'].'" data-url="'.$fileRow['physicalpath'].'" style="height: 24px;padding: 1px 5px;line-height: 12px;">'.Str::utf8_substr($fileRow['filename'], 0, 20).'..</button>
                                <button type="button" class="btn btn-outline-success btn-icon btn-sm" title="'.$removeLabelName.'" onclick="bpDtlMultiFileRemove(this);" data-id="'.$fileRow['id'].'" data-r-path="'.$paramRealPath.'" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                            
                        } elseif ($fileRow['fileextension'] == 'pdf') {
                            
                            $fileView .= '<div class="btn-group mt3 mb3">
                                <button type="button" class="btn btn-outline-danger btn-sm mr0" title="'.$fileRow['filename'].'" onclick="bpFilePreview(this);" data-extension="'.$fileRow['fileextension'].'" data-url="'.$fileRow['physicalpath'].'" style="height: 24px;padding: 1px 5px;line-height: 12px;">'.Str::utf8_substr($fileRow['filename'], 0, 20).'..</button>
                                <button type="button" class="btn btn-outline-danger btn-icon btn-sm" title="'.$removeLabelName.'" onclick="bpDtlMultiFileRemove(this);" data-id="'.$fileRow['id'].'" data-r-path="'.$paramRealPath.'" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                            
                        } else {
                            
                            $fileView .= '<div class="btn-group mt3 mb3">
                                <button type="button" class="btn btn-outline-primary btn-sm mr0" title="'.$fileRow['filename'].'" onclick="bpFilePreview(this);" data-extension="'.$fileRow['fileextension'].'" data-url="'.$fileRow['physicalpath'].'" style="height: 24px;padding: 1px 5px;line-height: 12px;">'.Str::utf8_substr($fileRow['filename'], 0, 20).'..</button>
                                <button type="button" class="btn btn-outline-primary btn-icon btn-sm" title="'.$removeLabelName.'" onclick="bpDtlMultiFileRemove(this);" data-id="'.$fileRow['id'].'" data-r-path="'.$paramRealPath.'" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                            
                        } 
                    }
                    
                } elseif ($value) {
                    
                    $removeLabelName = Lang::line('delete_btn');
                    $fileNames = explode(',', $value);
                    
                    foreach ($fileNames as $fileName) {
                        
                        $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
                        
                        if ($fileExtension == 'xls' || $fileExtension == 'xlsx') {
                            
                            $fileView .= '<div class="btn-group mt3 mb3 mr-2">
                                <button type="button" class="btn btn-outline-success btn-sm mr0" onclick="bpFilePreview(this);" data-extension="'.$fileExtension.'" data-url="'.$fileName.'" style="height: 24px;padding: 1px 5px;line-height: 12px;">Excel</button>
                                <button type="button" class="btn btn-outline-success btn-icon btn-sm" title="'.$removeLabelName.'" onclick="kpiDtlMultiFileRemove(this);" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                            
                        } elseif ($fileExtension == 'pdf') {
                            
                            $fileView .= '<div class="btn-group mt3 mb3 mr-2">
                                <button type="button" class="btn btn-outline-danger btn-sm mr0" onclick="bpFilePreview(this);" data-extension="'.$fileExtension.'" data-url="'.$fileName.'" style="height: 24px;padding: 1px 5px;line-height: 12px;">PDF</button>
                                <button type="button" class="btn btn-outline-danger btn-icon btn-sm" title="'.$removeLabelName.'" onclick="kpiDtlMultiFileRemove(this);" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                            
                        } else {
                            
                            $fileView .= '<div class="btn-group mt3 mb3 mr-2">
                                <button type="button" class="btn btn-outline-primary btn-sm mr0" onclick="bpFilePreview(this);" data-extension="'.$fileExtension.'" data-url="'.$fileName.'" style="height: 24px;padding: 1px 5px;line-height: 12px;">'.$fileExtension.'</button>
                                <button type="button" class="btn btn-outline-primary btn-icon btn-sm" title="'.$removeLabelName.'" onclick="kpiDtlMultiFileRemove(this);" style="height: 24px;padding: 1px 5px; width: 20px;padding: 2px 2px 2px 1px;line-height: 18px;"><i class="icon-cross"></i></button>
                            </div>';
                        } 
                    }
                    
                    $fileView .= Form::hidden(array('name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 'value' => $value));
                }
                
                if ($fileExtension = issetParam($row['FILE_EXTENSION'])) {
                    $required['data-valid-extension'] = $fileExtension;
                }
                
                $control = Form::file(
                    array(
                        'name'            => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path'       => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'data-col-path'   => $paramPath, 
                        'class'           => 'form-control input-sm fileInit', 
                        'placeholder'     => $labelName, 
                        'style'           => $factWidth, 
                        'multiple'        => 'multiple'
                    ) + $required 
                ) . $fileView;
                
            break;
        
            case 'link':
                
                $control = '<span>' . html_tag('a',
                    array(
                        'href' => 'javascript:;',
                        'onclick' => 'kpiLinkListener(this)'
                    ), '<i class="fa fa-link"></i>'
                );
                $control .= Form::hidden(
                    array(
                        'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                        'data-path' => $paramRealPath, 
                        'data-field-name' => $paramPath, 
                        'class' => 'form-control input-sm stringInit', 
                        'placeholder' => $labelName, 
                        'value' => $value 
                    )
                ) . '</span>';                
                
            break;
            
            case 'object':
                
                self::$isObjectType = true;
                
                $recordId   = $processHeaderParam['bookId'];
                $templateId = Mdform::$kpiTemplateId;
                
                $subTmpId   = self::getTemplateIdByDtlId($row['TEMPLATE_DTL_ID']);
                
                if (!empty($recordId)) {
                    $saveRows = self::getObjectSaveRows($recordId, $indicator['INDICATOR_ID'], $templateId, $indicator['COLUMN_NAME']); 
                } else {
                    $saveRows = null;
                }
                
                $control = '<input type="hidden" name="param['.Mdform::$pathPrefix.'kpiDmDtl.kpiObjectType]['.$rowIndex.'][]" data-path="'.Mdform::$pathPrefix.'kpiDmDtl.kpiObjectType"/>
                        <table class="table table-sm table-hover" data-name="param['.$paramRealPath.']['.$rowIndex.'][]" data-templateDtlId="'.$row['TEMPLATE_DTL_ID'].'" data-recordId="'.$recordId.'" data-subtmpid="'.$subTmpId.'" data-kpi-index="'.$rowIndex.'" data-dtlcode="'.$indicator['DTL_CODE'].'" data-colname="'.$recordId .' - '. $indicator['INDICATOR_ID'].' - '. $templateId.' - '. $indicator['COLUMN_NAME'].'">
                            <tbody>';

                if ($saveRows) {
                    
                    $subTmpBtn = '';
                    
                    if ($subTmpId) {
                        
                        if (!Mdform::$isSubKpiForm) {
                            $subTmpBtn = '<a href="javascript:;" onclick="bpKpiObjectSubTemplate(this, \''.$subTmpId.'\');" class="mr10 font-size-14"><i style="color:#5c6bc0;" class="fa fa-external-link-square"></i></a> ';
                        } else {
                            $subTmpBtn = '<a href="javascript:;" onclick="bpKpiObjectSubSubTemplate(this, \''.$subTmpId.'\');" data-uniqid="'.Mdform::$subUniqId.'" class="mr10 font-size-14"><i style="color:#5c6bc0;" class="fa fa-external-link-square"></i></a> ';
                        }
                    }
                    
                    foreach ($saveRows as $srow) {
                        
                        $srow['DESCRIPTION'] = htmlentities($srow['DESCRIPTION'], ENT_QUOTES, 'UTF-8');
                        $savedDtlId = $srow[$indicator['COLUMN_NAME']];
                        
                        if ($srow['IS_SHOW_ONLY'] != '1') {
                            
                            $control .= '<tr tr-status="0" data-basketrowid="'.$savedDtlId.'" data-relationid="'.$srow['E_ID'].'">'.
                                '<td style="height: 25px; max-width: 0;" class="text-left text-truncate" title="'.$srow['DESCRIPTION'].'"><a href="javascript:;" onclick="drillLinkKpiMenu(\''.$savedDtlId.'\');"><i class="fa fa-tag bgicon"></i>'.$srow['DESCRIPTION'].'</a><input type="hidden" name="param['.$paramRealPath.']['.$rowIndex.'][]" data-path="'.$paramRealPath.'" value="'.$savedDtlId.'~~~'.$srow['DESCRIPTION'].'~~~'.$srow['TRG_TEMPLATE_ID'].'~~~'.$srow['ID'].'|'.$srow['E_ID'].'"></td>'.
                                '<td style="height: 25px; width: 60px" class="text-right" data-dmmartid="'.$srow['ID'].'">'.$subTmpBtn.'<a href="javascript:;" onclick="deleteKpiObjectData(this);" data-objtype-action="remove" class="font-size-14"><i style="color:red" class="fa fa-trash"></i></a></td>'.
                            '</tr>';
                            
                        } else {
                            $control .= '<tr class="ea-tmp-row">'.
                                '<td style="height: 25px; max-width: 0;" class="text-left text-truncate" title="'.$srow['DESCRIPTION'].'"><a href="javascript:;" onclick="drillLinkKpiMenu(\''.$savedDtlId.'\');"><i class="fa fa-tag bgicon"></i>'.$srow['DESCRIPTION'].'</a></td>'.
                                '<td style="height: 25px; width: 60px" class="text-right"></td>'.
                            '</tr>';
                        }
                    }
                }

                $control .= '</tbody></table>';
                
            break;
            
            case 'graph':
                
                self::$isGraphType = true;
                
                $savedId = $savedGraph = ''; $isSavedGraph = false;
                
                if ($value) {
                    
                    $graphRow = self::getKpiDmGraphById($value);
                    
                    if ($graphRow) {
                        $savedId = $graphRow['ID'];
                        $savedGraph = Mdbpmn::graphXmlSpecialCharReplace($graphRow['GRAPH_XML']);
                        $isSavedGraph = true;
                    }
                }
                
                $control = Form::textArea(array('name' => 'param[kpiDmDtl.kpiGraphType]['.$rowIndex.'][]', 'id' => 'graphInput-'.$row['TEMPLATE_DTL_ID'], 'style' => 'display: none', 'value' => $savedGraph, 'class' => 'mxgraph-load', 'data-dtlid' => $row['TEMPLATE_DTL_ID']));
                $control .= Form::hidden(array('name' => 'param[kpiDmDtl.kpiGraphId]['.$rowIndex.'][]', 'value' => $savedId));
                
                $control .= '<div class="bp-graph-field-parent">';
                    $control .= '<button type="button" class="btn btn-light btn-sm" onclick="bpFieldGraphView(this, \''.$row['TEMPLATE_DTL_ID'].'\');"><i class="icon-design"></i> Editor</button>';
                    
                    if ($isSavedGraph) {
                        $control .= '<button type="button" class="btn btn-light btn-sm ml8" onclick="bpFieldGraphFullScreen(this);" title="Fullscreen"><i class="fa fa-expand"></i></button>';
                    }
                    
                    $control .= '<div id="graphview-'.$row['TEMPLATE_DTL_ID'].'" class="svg-d-inline text-center mt10"></div>';
                $control .= '</div>';
                
            break;
            
            case 'star':
                
                $sizeOfStar = 5;
                
                $attrArray = array(
                    'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                    'data-path' => $paramRealPath, 
                    'data-field-name' => $paramPath, 
                    'data-col-path' => $paramPath, 
                    'placeholder' => $labelName, 
                    'value' => $value
                );
                $starControl = Form::hidden($attrArray);
                
                for ($in = 1; $in <= $sizeOfStar; $in++) {
                    if ($in <= $value) {
                        $starControl .= '<li data-id="'.$in.'"><i class="icon-star-full2" style="color: orange; cursor: pointer;"></i></li>';
                    } else {
                        $starControl .= '<li data-id="'.$in.'"><i class="icon-star-empty3" style="color: #ccc; cursor: pointer;"></i></li>';
                    }
                }
            
                $control = '<ul class="nav navbar-nav star-rating">'. $starControl .'</ul>';
                
            break;
        
            default:
                
                $attrArray = array(
                    'name' => 'param['.$paramRealPath.']['.$rowIndex.'][]', 
                    'data-path' => $paramRealPath, 
                    'data-field-name' => $paramPath, 
                    'data-col-path' => $paramPath, 
                    'class' => 'form-control input-sm stringInit', 
                    'placeholder' => $labelName, 
                    'value' => $value, 
                    'style' => $factWidth 
                ) + $required;
                
                if ($dataLength) {
                    $attrArray['maxlength'] = $dataLength;
                    $attrArray['data-maxlength'] = 'true';
                }
                
                $control = Form::text($attrArray);
                
            break;    
        }
        
        if ($row['SUB_TEMPLATE_ID']) {
            
            $control = '<div class="input-group"> 
                '.($showType == 'check' ? '<div class="form-control-plaintext pt4">'.$control.'</div>' : $control).' 
                <span class="input-group-append">
                    <button type="button" class="btn btn-sm btn-primary bp-btn-subkpi" onclick="bpFieldSubKpiForm(this, \''.Mdform::$kpiTemplateId.'\', \''.$row['SUB_TEMPLATE_ID'].'\', \''.$processHeaderParam['bookId'].'\', \''.Mdform::$kpiTypeCode.'\');">..</button>
                </span>
            </div>';
        }
        
        return $control;
    }
    
    public function getKpiDmGraphById($id) {
        
        $row = $this->db->GetRow("SELECT ID, GRAPH_XML FROM KPI_DM_GRAPH WHERE ID = ".$this->db->Param(0), array($id));
        return $row;
    }
    
    public function getObjectRelationCount($recordId, $tempId) {
        
        $row = $this->db->GetRow("
            SELECT 
                DM.INDICATOR3, 
                SUBSTR(DM.INDICATOR3, 0, INSTR(DM.INDICATOR3, ',') - 1) AS INDICATOR3_SUB 
            FROM KPI_DM_MART DM 
                INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID 
                    AND TD.IS_MAIN = 1 
                INNER JOIN KPI_TEMPLATE_DTL KD ON KD.TEMPLATE_ID = TD.TEMPLATE_ID 
                    AND KD.INDICATOR_ID = 1587366486549 
            WHERE DM.RECORD_ID = ".$this->db->Param(0)." 
                AND TD.TEMPLATE_ID = ".$this->db->Param(1)."  
                AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(2)." 
                AND DM.DEFAULT_TEMPLATE_ID IS NULL     
                AND DM.TRG_TEMPLATE_ID IS NULL", 
            array($recordId, $tempId, Ue::sessionScenarioId())
        );
        
        return $row;
    }
    
    public function getObjectSaveRows($recordId, $indicatorId, $templateId, $indicatorName) {
        
        if (Mdform::$firstTplId) {
            Mdform::$defaultTplSavedId = $templateId;
        }

        if (Mdform::$defaultTplSavedId) {
            
            $data = $this->db->GetAll("
                SELECT 
                    DM.ID, 
                    DM.TRG_TEMPLATE_ID, 
                    CASE WHEN ".$this->db->Param(0)." != 'mn' 
                        THEN NVL(DM.DESCRIPTION2, DM.DESCRIPTION) ELSE DM.DESCRIPTION 
                    END AS DESCRIPTION, 
                    DM.$indicatorName, 
                    ER.ID AS E_ID 
                FROM KPI_DM_MART DM 
                    INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID 
                        AND TD.IS_MAIN = 1 
                        AND DM.TEMPLATE_ID = TD.TEMPLATE_ID 
                    INNER JOIN EA_RELATION ER ON ER.SRC_TEMPLATE_ID = DM.TEMPLATE_ID 
                        AND ER.SRC_OBJECT_ID = DM.RECORD_ID 
                        AND ER.TRG_TEMPLATE_ID = DM.TRG_TEMPLATE_ID 
                        AND ER.TRG_OBJECT_ID = DM.$indicatorName      
                        AND DM.INDICATOR_ID = ER.INDICATOR_ID     
                WHERE DM.RECORD_ID = ".$this->db->Param(1)." 
                    AND DM.INDICATOR_ID = ".$this->db->Param(2)." 
                    AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(3)." 
                    AND ".$this->db->IfNull('ER.SCENARIO_ID', '0')." = ".$this->db->Param(3)."          
                    AND ER.SRC_TEMPLATE_ID = ".$this->db->Param(4), 
                array($this->lang->getCode(), $recordId, $indicatorId, Ue::sessionScenarioId(), Mdform::$rootTemplateId)
            );  
            
        } else {
            
            $data = $this->db->GetAll("
                SELECT 
                    REL.ID, 
                    REL.TRG_TEMPLATE_ID, 
                    TO_CHAR(REL.DESCRIPTION) AS DESCRIPTION, 
                    REL.$indicatorName, 
                    REL.E_ID, 
                    REL.IS_SHOW_ONLY 
                FROM (
                    SELECT 
                        DM.ID, 
                        DM.TRG_TEMPLATE_ID, 
                        CASE WHEN ".$this->db->Param(0)." != 'mn' 
                            THEN NVL(DM.DESCRIPTION2, DM.DESCRIPTION) ELSE DM.DESCRIPTION 
                        END AS DESCRIPTION, 
                        DM.$indicatorName, 
                        ER.ID AS E_ID, 
                        0 AS IS_SHOW_ONLY
                    FROM KPI_DM_MART DM 
                        INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID 
                            AND TD.IS_MAIN = 1 
                            AND DM.TEMPLATE_ID = TD.TEMPLATE_ID
                        INNER JOIN EA_RELATION ER ON ER.SRC_TEMPLATE_ID = DM.TEMPLATE_ID 
                            AND ER.SRC_OBJECT_ID = DM.RECORD_ID 
                            AND ER.TRG_TEMPLATE_ID = DM.TRG_TEMPLATE_ID 
                            AND ER.TRG_OBJECT_ID = DM.$indicatorName 
                            AND DM.INDICATOR_ID = ER.INDICATOR_ID 
                    WHERE DM.RECORD_ID = ".$this->db->Param(1)." 
                        AND DM.INDICATOR_ID = ".$this->db->Param(2)." 
                        AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(3)."   
                        AND ".$this->db->IfNull('ER.SCENARIO_ID', '0')." = ".$this->db->Param(3)."     
                        AND ER.SRC_TEMPLATE_ID = ".$this->db->Param(4)." 

                    UNION ALL 

                    SELECT
                        null AS ID,
                        null AS TRG_TEMPLATE_ID,
                        CASE WHEN ".$this->db->Param(0)." != 'mn'
                        THEN NVL(DM.DESCRIPTION2, DM.DESCRIPTION) ELSE DM.DESCRIPTION
                        END AS DESCRIPTION,
                        DM.$indicatorName,
                        null AS E_ID,
                        1 AS IS_SHOW_ONLY
                    FROM KPI_DM_MART DM
                        INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID
                            AND TD.IS_MAIN = 1
                            AND DM.TEMPLATE_ID = TD.TEMPLATE_ID
                        INNER JOIN EA_RELATION ER ON ER.SRC_TEMPLATE_ID = DM.TEMPLATE_ID
                            AND ER.SRC_OBJECT_ID = DM.RECORD_ID
                            AND ER.TRG_TEMPLATE_ID = DM.TRG_TEMPLATE_ID
                            AND ER.TRG_OBJECT_ID = DM.$indicatorName
                            AND DM.INDICATOR_ID = ER.INDICATOR_ID
                    WHERE DM.RECORD_ID IN (
                            SELECT 
                                EO.ID 
                            FROM EA_OBJECT EO 
                            START WITH EO.PARENT_ID = ".$this->db->Param(1)."
                            CONNECT BY NOCYCLE PRIOR EO.ID = EO.PARENT_ID
                        )
                        AND DM.INDICATOR_ID = ".$this->db->Param(2)."
                        AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(3)." 
                        AND ".$this->db->IfNull('ER.SCENARIO_ID', '0')." = ".$this->db->Param(3)." 
                        AND ER.SRC_TEMPLATE_ID = ".$this->db->Param(4)."
                ) REL 
                GROUP BY 
                    REL.ID, 
                    REL.TRG_TEMPLATE_ID, 
                    TO_CHAR(REL.DESCRIPTION), 
                    REL.$indicatorName, 
                    REL.E_ID, 
                    REL.IS_SHOW_ONLY 
                ORDER BY REL.IS_SHOW_ONLY ASC", 
                array($this->lang->getCode(), $recordId, $indicatorId, Ue::sessionScenarioId(), $templateId)
            );  
        }
        
        return $data;
    }
    
    public function getTemplateIdByDtlId($dtlId) {
        $tmpId = $this->db->GetOne("
            SELECT 
               KT.ID
            FROM KPI_TEMPLATE_MAP KTM 
                INNER JOIN KPI_TEMPLATE KT ON KTM.SRC_TEMPLATE_ID = KT.SRC_TEMPLATE_ID 
                    AND KTM.TRG_TEMPLATE_ID = KT.TRG_TEMPLATE_ID 
                    AND KTM.SRC_TEMPLATE_DTL_ID = KT.SRC_TEMPLATE_DTL_ID 
            WHERE KTM.SRC_TEMPLATE_DTL_ID = " . $this->db->Param(0), 
            array($dtlId) 
        );  
        return $tmpId;
    }
    
    public function kpiFormControlByPrint($rowIndex, $cellControlDatas, $dtlId, $factId, $savedDataRow) {
        
        $arr = array_filter($cellControlDatas, function($ar) use($dtlId, $factId) {
            return ($ar['TEMPLATE_DTL_ID'] == $dtlId && $ar['TEMPLATE_FACT_ID'] == $factId);
        });
        
        foreach ($arr as $row) {
            $rowKeyArr = $row;
        }
        
        if (isset($rowKeyArr)) {
            return self::{Mdform::$kpiRenderParamControlView}($rowIndex, $rowKeyArr, $savedDataRow);
        }
        
        return null;
    }
    
    public function kpiRenderParamControlByPrint($rowIndex, $row, $val) {
        
        $showType = $row['SHOW_TYPE'];
        $lookupMetaDataId = $row['LOOKUP_META_DATA_ID'];
        $paramPath = $row['PARAM_PATH'];
        $paramRealPath = 'kpiDmDtl.'.$paramPath;
        $paramPathLower = strtolower($paramPath);
        $lookupCriteria = $row['LOOKUP_CRITERIA'];
        $control = null;
        
        $value = isset($val[$paramPathLower]) ? $val[$paramPathLower] : null;
        
        switch ($showType) {
        
            case 'radio':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    $datas = self::getRadioButtonKpiModel($lookupMetaDataId, $paramPath, $paramRealPath, $lookupCriteria);
                    
                    foreach ($datas as $k => $v) {
                        if ($v['value'] == $value) {
                            $control = $v['label'];
                            break;
                        }
                    }
                    
                } else {
                    $control = $value;
                }
                
            break;
        
            case 'combo':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                    $comboData = $datas['data'];
                    $comboId = $datas['id'];
                    $comboName = $datas['name'];
                    
                    foreach ($comboData as $k => $v) {
                        if ($v[$comboId] == $value) {
                            $control = $v[$comboName];
                            break;
                        }
                    }
                    
                } else {
                    $control = $value;
                }
                
            break;
            
            case 'multicombo':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                    
                    $comboData = $datas['data'];
                    $comboId = $datas['id'];
                    $comboName = $datas['name'];
                    $idsExplode = array_map('trim', explode(',', $value));
                    
                    foreach ($comboData as $k => $v) {
                        if (in_array($v[$comboId], $idsExplode)) {
                            $control .= $v[$comboName] . ', ';
                        }
                    }
                    
                    $control = rtrim($control, ', ');
                    
                } else {
                    $control = $value;
                }
                
            break;
            
            case 'file':
                
                if (strpos($value, '.') !== false) {
                    
                    $href = 'mdobject/downloadFile?fDownload=1&file=' . $value;
                    $fileExtension = strtolower(substr($value, strrpos($value, '.') + 1));
                    $fileName = basename($value);

                    if ($fileExtension == 'pdf') {
                        $fileIcon = 'icon-file-pdf';
                    } elseif ($fileExtension == 'doc' || $fileExtension == 'docx') {
                        $fileIcon = 'icon-file-word';
                    } elseif ($fileExtension == 'xls' || $fileExtension == 'xlsx') {
                        $fileIcon = 'icon-file-excel';
                    } elseif ($fileExtension == 'png' || $fileExtension == 'jpg' || $fileExtension == 'jpeg' 
                        || $fileExtension == 'gif' || $fileExtension == 'bmp') {
                        $fileIcon = 'icon-file-picture';
                    } elseif ($fileExtension == 'zip' || $fileExtension == 'rar') {
                        $fileIcon = 'icon-file-zip';
                    } else {
                        $fileIcon = 'icon-file-text2';
                    }

                    $fileView = html_tag('a', array('href'=>$href,'title'=>Lang::line('download_btn'),'class'=>'btn btn-sm btn-light rounded-0'), '<i class="icon-download"></i>');
                    $fileView .= html_tag('a', array('href'=>'javascript:;','title'=>Lang::line('see_btn'),'data-fileurl'=>$value, 'data-extension'=>$fileExtension, 'data-filename'=>$fileName, 'onclick'=>'bpFilePreview(this);', 'class'=>'btn btn-sm btn-light rounded-0'), '<i class="'.$fileIcon.'"></i>');
                    
                    $control = $fileView;
                }
                
            break;
        
            default:
                $control = $value;
            break;    
        }
        
        return $control;
    }
    
    public function kpiRenderParamControlByLog($rowIndex, $row, $val) {
        
        $showType = $row['SHOW_TYPE'];
        $lookupMetaDataId = $row['LOOKUP_META_DATA_ID'];
        $paramPath = $row['PARAM_PATH'];
        $paramRealPath = 'kpiDmDtl.'.$paramPath;
        $paramPathLower = strtolower($paramPath);
        $lookupCriteria = $row['LOOKUP_CRITERIA'];
        $control = null;
        
        $value = isset($val[$paramPathLower]) ? $val[$paramPathLower] : null;
        
        switch ($showType) {
        
            case 'radio':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    if (strpos($value, Mdcommon::$separator) !== false) {
                        
                        $valueArr = explode(Mdcommon::$separator, $value);
                        $oldVal = $valueArr[0];
                        $newVal = $valueArr[1];
                        
                        $datas = self::getRadioButtonKpiModel($lookupMetaDataId, $paramPath, $paramRealPath, $lookupCriteria);
                        
                        if ($oldVal != '' || $newVal != '') {
                            
                            foreach ($datas as $k => $v) {
                                if ($v['value'] == $oldVal) {
                                    $oldVal = $v['label'];
                                }
                                
                                if ($v['value'] == $newVal) {
                                    $newVal = $v['label'];
                                }
                            }
                        }

                        $oldVal = $oldVal == 'null' ? '' : '<span class="badge badge-warning d-none" data-valmode="old">'.$oldVal.'</span>';
                        $newVal = $newVal == 'null' ? '' : '<span class="badge badge-info" data-valmode="new">'.$newVal.'</span>';

                        $value = $oldVal . $newVal;
                    } 
                    
                    $control = $value;
                    
                } else {
                    
                    $control = self::kpiFieldLogView($value);
                }
                
            break;
        
            case 'combo':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    if (strpos($value, Mdcommon::$separator) !== false) {
                        
                        $valueArr = explode(Mdcommon::$separator, $value);
                        $oldVal = $valueArr[0];
                        $newVal = $valueArr[1];
                        
                        $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                        $comboData = $datas['data'];
                        $comboId = $datas['id'];
                        $comboName = $datas['name'];
                        
                        if ($oldVal != '' || $newVal != '') {
                            
                            foreach ($comboData as $k => $v) {
                                if ($v[$comboId] == $oldVal) {
                                    $oldVal = $v[$comboName];
                                }
                                if ($v[$comboId] == $newVal) {
                                    $newVal = $v[$comboName];
                                }
                            }
                        }

                        $oldVal = $oldVal == 'null' ? '' : '<span class="badge badge-warning d-none" data-valmode="old">'.$oldVal.'</span>';
                        $newVal = $newVal == 'null' ? '' : '<span class="badge badge-info" data-valmode="new">'.$newVal.'</span>';

                        $value = $oldVal . $newVal;
                    } 
                    
                    $control = $value;
                    
                } else {
                    
                    $control = self::kpiFieldLogView($value);
                }
                
            break;
            
            case 'multicombo':
                
                if (!is_null($lookupMetaDataId) && $value != '') {
                    
                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                    
                    $comboData = $datas['data'];
                    $comboId = $datas['id'];
                    $comboName = $datas['name'];
                    $idsExplode = array_map('trim', explode(',', $value));
                    
                    foreach ($comboData as $k => $v) {
                        if (in_array($v[$comboId], $idsExplode)) {
                            $control .= $v[$comboName] . ', ';
                        }
                    }
                    
                    $control = rtrim($control, ', ');
                    
                } else {
                    
                    $control = self::kpiFieldLogView($value);
                }
                
            break;
            
            case 'file':
                
                if (strpos($value, '.') !== false) {
                    
                    $href = 'mdobject/downloadFile?fDownload=1&file=' . $value;
                    $fileExtension = strtolower(substr($value, strrpos($value, '.') + 1));
                    $fileName = basename($value);

                    if ($fileExtension == 'pdf') {
                        $fileIcon = 'icon-file-pdf';
                    } elseif ($fileExtension == 'doc' || $fileExtension == 'docx') {
                        $fileIcon = 'icon-file-word';
                    } elseif ($fileExtension == 'xls' || $fileExtension == 'xlsx') {
                        $fileIcon = 'icon-file-excel';
                    } elseif ($fileExtension == 'png' || $fileExtension == 'jpg' || $fileExtension == 'jpeg' 
                        || $fileExtension == 'gif' || $fileExtension == 'bmp') {
                        $fileIcon = 'icon-file-picture';
                    } elseif ($fileExtension == 'zip' || $fileExtension == 'rar') {
                        $fileIcon = 'icon-file-zip';
                    } else {
                        $fileIcon = 'icon-file-text2';
                    }

                    $fileView = html_tag('a', array('href'=>$href,'title'=>Lang::line('download_btn'),'class'=>'btn btn-sm btn-light rounded-0'), '<i class="icon-download"></i>');
                    $fileView .= html_tag('a', array('href'=>'javascript:;','title'=>Lang::line('see_btn'),'data-fileurl'=>$value, 'data-extension'=>$fileExtension, 'data-filename'=>$fileName, 'onclick'=>'bpFilePreview(this);', 'class'=>'btn btn-sm btn-light rounded-0'), '<i class="'.$fileIcon.'"></i>');
                    
                    $control = $fileView;
                }
                
            break;
        
            default:
                
                $control = self::kpiFieldLogView($value);
            break;    
        }
        
        return $control;
    }
    
    public function kpiFieldLogView($value) {
        
        if (strpos($value, Mdcommon::$separator) !== false) {
                        
            $valueArr = explode(Mdcommon::$separator, $value);

            $oldVal = $valueArr[0] == 'null' ? '' : '<span class="badge badge-warning d-none" data-valmode="old">'.$valueArr[0].'</span>';
            $newVal = $valueArr[1] == 'null' ? '' : '<span class="badge badge-info" data-valmode="new">'.$valueArr[1].'</span>';

            $value = $oldVal . $newVal;
        } 
        
        return $value;
    }
    
    public function getComboKpiModel($lookupMetaDataId, $lookupCriteria) {
        
        if (isset(self::$kpiControlDatas[$lookupMetaDataId]['combo'][$lookupCriteria])) {
            return self::$kpiControlDatas[$lookupMetaDataId]['combo'][$lookupCriteria];
        }
        
        $datas = self::getDataViewValuesByKpiModel($lookupMetaDataId, $lookupCriteria);
        
        $this->load->model('mddatamodel', 'middleware/models/');
        $standartFields = $this->model->getCodeNameFieldNameModel($lookupMetaDataId);
        
        $array = array(
            'data' => $datas, 
            'id' => $standartFields['id'], 
            'name' => $standartFields['name'], 
            'code' => $standartFields['code'], 
            'nameColumnName' => $standartFields['nameColumnName']
        );
        
        if (isset($datas[0]['breadcrumbname'])) {
            $array['name'] = 'breadcrumbname';
            $array['data-name'] = $standartFields['name'];
        }
        
        self::$kpiControlDatas[$lookupMetaDataId]['combo'][$lookupCriteria] = $array;
        
        $this->load->model('mdform', 'middleware/models/');
        
        return $array;
    }

    public function getRadioButtonKpiModel($lookupMetaDataId, $paramPath, $paramRealPath, $lookupCriteria = null) {
        
        if (isset(self::$kpiControlDatas[$lookupMetaDataId]['radio'][$lookupCriteria])) {
            return self::$kpiControlDatas[$lookupMetaDataId]['radio'][$lookupCriteria];
        }
        
        $datas = self::getDataViewValuesByKpiModel($lookupMetaDataId, $lookupCriteria);
        
        $this->load->model('mddatamodel', 'middleware/models/');
        $standartFields = $this->model->getCodeNameFieldNameModel($lookupMetaDataId);
        
        $id = $standartFields['id'];
        $name = $standartFields['name'];
        
        $array = array();
        
        foreach ($datas as $row) {
            $array[] = array(
                'name' => 'param['.$paramRealPath.'][0][]', 
                'value' => $row[$id], 
                'label' => $row[$name], 
                'labelclass' => 'radio-inline'
            );
        }
        
        self::$kpiControlDatas[$lookupMetaDataId]['radio'][$lookupCriteria] = $array;
        
        $this->load->model('mdform', 'middleware/models/');
        
        return $array;
    }

    public function getDataViewValuesByKpiModel($lookupMetaDataId, $lookupCriteria) {
        
        if (isset(self::$kpiControlDatas[$lookupMetaDataId]['data'][$lookupCriteria])) {
            return self::$kpiControlDatas[$lookupMetaDataId]['data'][$lookupCriteria];
        }
        
        $param = array(
            'systemMetaGroupId' => $lookupMetaDataId,
            'showQuery' => 0, 
            'ignorePermission' => 1 
        );
        
        if ($lookupCriteria) {
            parse_str($lookupCriteria, $lookupCriteriaArr);
            
            if (count($lookupCriteriaArr) > 0) {
                foreach ($lookupCriteriaArr as $key => $val) {
                    if ($key && $val != '') {
                        $param['criteria'][$key][] = array(
                            'operator' => '=',
                            'operand' => $val
                        );
                    }
                }
            }
        }
        
        $param['criteria']['filterStartDate'] = array(
            array(
                'operator' => '=',
                'operand' =>  Ue::sessionFiscalPeriodStartDate()
            )                     
        );
        $param['criteria']['filterEndDate'] = array(
            array(
                'operator' => '=',
                'operand' =>  Ue::sessionFiscalPeriodEndDate()
            )                     
        );
        
        $dataViewValues = WebService::runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if ($dataViewValues['status'] === 'success' && isset($dataViewValues['result'][0])) {
                
            unset($dataViewValues['result']['aggregatecolumns']);
            unset($dataViewValues['result']['paging']);
            
            $result = $dataViewValues['result'];
            
            foreach ($result as $k => $row) {
                $result[$k]['rowData'] = htmlentities(str_replace('&quot;', '\\&quot;', json_encode($row, JSON_UNESCAPED_UNICODE)), ENT_QUOTES, 'UTF-8');
            }
            
            $array = self::$kpiControlDatas[$lookupMetaDataId]['data'][$lookupCriteria] = $result;
            
            return $array;
        } 

        return array();
    }
    
    public function getKpiControlsSavedDataByBookId($processHeaderParam) {
        
        if (Mdform::$kpiDmDtlData) {
            return Mdform::$kpiDmDtlData;
        }
        
        Mdform::$pfTranslationValue = array();
        
        if (isset($processHeaderParam['kpiDmMartTemplateId'])) {
            
            $recordId   = $processHeaderParam['bookId'];
            $templateId = $processHeaderParam['kpiDmMartTemplateId'];
            
            $data = $this->db->GetAll("
                SELECT 
                    * 
                FROM KPI_DM_MART 
                WHERE RECORD_ID = ".$this->db->Param(0)." 
                    AND TEMPLATE_ID = ".$this->db->Param(1), 
                array($recordId, $templateId)
            );
            
            $array = Mdform::$kpiDmMart = array();
            
            foreach ($data as $row) {
                foreach ($row as $k => $v) {
                    if ($k != 'ID' && $k != 'DIMENSION_ID' && $k != 'RECORD_ID' && $k != 'FACT_CODE' && $k != 'COLUMN_INDEX' && $k != 'TEMPLATE_CODE' && $k != 'HDR_RECORD_ID' && $k != 'TEMPLATE_ID' && $v != '') {
                        $array[$row['FACT_CODE'].'_'.$row['COLUMN_INDEX'].'_'.$k] = $v;
                    }
                }
            }
            
            Mdform::$kpiDmMart = $array;

            return true;
            
        } elseif (isset($processHeaderParam['subKpiDmDtl']) && isset($processHeaderParam['indicatorId']) && $processHeaderParam['bookId']) {
            
            $recordId        = $processHeaderParam['bookId'];
            $templateId      = Mdform::$kpiTemplateId;
            $rootIndicatorId = $processHeaderParam['indicatorId'];
            
            $data = $this->db->GetAll("
                SELECT 
                    ID, 
                    INDICATOR_ID AS INDICATORID, 
                    FACT1, 
                    FACT2, 
                    FACT3, 
                    FACT4, 
                    FACT5, 
                    FACT6, 
                    FACT7, 
                    FACT8, 
                    FACT9, 
                    FACT10, 
                    FACT11, 
                    FACT12, 
                    FACT13, 
                    FACT14, 
                    FACT15, 
                    FACT16, 
                    FACT17, 
                    FACT18, 
                    FACT19, 
                    FACT20, 
                    TEMPLATE_DTL_ID AS TEMPLATEDTLID 
                FROM KPI_DM_DTL 
                WHERE BOOK_ID = ".$this->db->Param(0)." 
                    AND SUB_TEMPLATE_ID = ".$this->db->Param(1)." 
                    AND ROOT_INDICATOR_ID = ".$this->db->Param(2), 
                array($recordId, $templateId, $rootIndicatorId)
            );
            
            if ($data) {
                return Arr::changeKeyLower($data);
            }
            
            return null;
            
        } elseif (Mdform::$getKpiCommandCode == 'selectQry' && $processHeaderParam['bookId']) {
            
            $recordId   = $processHeaderParam['bookId'];
            $templateId = Mdform::$kpiTemplateId;
            $scenarioId = Ue::sessionScenarioId();
            
            if (Mdform::$firstTplId) {
                Mdform::$defaultTplSavedId = $templateId;
            }
                
            if (Mdform::$defaultTplSavedId) {
        
                $row = $this->db->GetRow("
                    SELECT 
                        DM.* 
                    FROM KPI_DM_MART DM 
                        INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID 
                            AND TD.IS_MAIN = 1 
                    WHERE DM.RECORD_ID = ".$this->db->Param(0)." 
                        AND TD.TEMPLATE_ID = ".$this->db->Param(1)." 
                        AND DM.DEFAULT_TEMPLATE_ID = ".$this->db->Param(2)." 
                        AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(3)."     
                        AND DM.TRG_TEMPLATE_ID IS NULL", 
                    array($recordId, Mdform::$rootTemplateId, Mdform::$defaultTplSavedId, $scenarioId)
                );
                
            } else {
                
                $row = $this->db->GetRow("
                    SELECT 
                        DM.* 
                    FROM KPI_DM_MART DM 
                        INNER JOIN KPI_TEMPLATE_DIMENSION TD ON TD.DIMENSION_ID = DM.DIMENSION_ID 
                            AND TD.IS_MAIN = 1 
                    WHERE DM.RECORD_ID = ".$this->db->Param(0)." 
                        AND ".$this->db->IfNull('TD.TEMPLATE_ID', 'TD.INDICATOR_ID')." = ".$this->db->Param(1)." 
                        AND ".$this->db->IfNull('DM.SCENARIO_ID', '0')." = ".$this->db->Param(2)." 
                        AND DM.DEFAULT_TEMPLATE_ID IS NULL 
                        AND DM.TRG_TEMPLATE_ID IS NULL", 
                    array($recordId, $templateId, $scenarioId)
                );
            }
            
            if ($row) {
                
                Mdform::$isSavedKpiForm = true;
                Mdform::$recordId = $recordId;
                
                if ($row['TRANSLATION_VALUE']) {
                    
                    Mdform::$pfTranslationValue = json_decode($row['TRANSLATION_VALUE'], true);
                    
                    $attrArray['name'] = 'param['.Mdform::$pathPrefix.'kpiDmDtl.pfTranslationValue]';
                    $attrArray['data-path'] = Mdform::$pathPrefix.'kpiDmDtl.pfTranslationValue';
                    $attrArray['value'] = $row['TRANSLATION_VALUE'];
                    $attrArray['style'] = 'display: none';
                
                    Mdform::$pfTranslationValueTextarea = Form::textArea($attrArray);
                }
            }
            
            return $row;
            
        } elseif ($processHeaderParam['ids']) {
            
            $param = array(
                'criteria' => array(
                    'id' => array(
                        array(
                            'operator' => 'IN',
                            'operand' => rtrim($processHeaderParam['ids'], ',') 
                        )
                    )
                )
            );
            
            $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mdform::$getKpiConsolidateCommandCode, $param);
            
            if ($result['status'] == 'success' && isset($result['result']['kpidmdtl'])) {
                return $result['result']['kpidmdtl'];
            }
        }
        
        if ($processHeaderParam['bookId']) {
            
            $param = array(
                'id' => $processHeaderParam['bookId']
            );
                        
            $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mdform::$getKpiCommandCode, $param);

            if ($result['status'] == 'success' && isset($result['result']['kpidmdtl'])) {
                return $result['result']['kpidmdtl'];
            }
        }
        
        return null;
    }
    
    public function getSubTemplateIndicatorIdsModel($templateId, $value) {
        
        $param = array(
            'systemMetaGroupId' => '1602667101242702',
            'showQuery' => 0,
            'ignorePermission' => 1, 
            'criteria' => array(
                'templateId' => array(
                    array(
                        'operator' => '=',
                        'operand' => $templateId
                    )
                ), 
                'kpiSubTemplateIndicatorByCriteria' => array(
                    array(
                        'operator' => '=',
                        'operand' => $value
                    )
                )
            )
        );

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if ($data['status'] == 'success') {
            
            if (isset($data['result'][0])) {
                
                unset($data['result']['aggregatecolumns']);
                unset($data['result']['paging']);
                
                $result = array('status' => 'success', 'ids' => Arr::implode_key(',', $data['result'], 'indicatorid', true));
            } else {
                $result = array('status' => 'error', 'message' => 'KPI indicator олдсонгүй!');
            }
            
        } else {
            $result = array('status' => 'error', 'message' => $this->ws->getResponseMessage($data));
        }
        
        return $result;
    }
    
    public function titleReplacer($title) {
        
        if (isset(self::$placeholderTitle[$title])) {
            return self::$placeholderTitle[$title];
        }
        
        if ($title == '') {
            return '';
        }
        
        $titleName = $title;
        
        if (strpos($title, 'sysdate[') !== false) {
            preg_match_all('/sysdate\[(.*?)\]/', $title, $dateCriterias);
            if (count($dateCriterias[0]) > 0) {
                foreach ($dateCriterias[1] as $ek => $ev) {
                    $date = Date::weekdayAfter('Y-m-d', Date::currentDate('Y-m-d'), $dateCriterias[1][$ek]);
                    $title = str_replace($dateCriterias[0][$ek], $date, $title);
                }
            }
        } 
        if (strpos($title, 'sysyear[') !== false) {
            preg_match_all('/sysyear\[(.*?)\]/', $title, $dateCriterias);
            if (count($dateCriterias[0]) > 0) {
                foreach ($dateCriterias[1] as $ek => $ev) {
                    $year = Date::weekdayAfter('Y', Date::currentDate('Y'), $dateCriterias[1][$ek]);
                    $title = str_replace($dateCriterias[0][$ek], $year, $title);
                }
            }
        } 
        if (strpos($title, 'sysmonth[') !== false) {
            preg_match_all('/sysmonth\[(.*?)\]/', $title, $dateCriterias);
            if (count($dateCriterias[0]) > 0) {
                foreach ($dateCriterias[1] as $ek => $ev) {
                    $month = Date::weekdayAfter('m', Date::currentDate('m'), $dateCriterias[1][$ek]);
                    $title = str_replace($dateCriterias[0][$ek], $month, $title);
                }
            }
        } 
        if (strpos($title, 'sysday[') !== false) {
            preg_match_all('/sysday\[(.*?)\]/', $title, $dateCriterias);
            if (count($dateCriterias[0]) > 0) {
                foreach ($dateCriterias[1] as $ek => $ev) {
                    $day = Date::weekdayAfter('d', Date::currentDate('d'), $dateCriterias[1][$ek]);
                    $title = str_replace($dateCriterias[0][$ek], $day, $title);
                }
            }
        } 
        
        $title = strtr($title, 
            array(
                '[sysdate]' => Date::currentDate('Y-m-d'), 
                '[sysyear]' => Date::currentDate('Y'), 
                '[sysmonth]' => Date::currentDate('m'), 
                '[sysday]' => Date::currentDate('d'), 
                'sysdate' => Date::currentDate('Y-m-d'), 
                'sysyear' => Date::currentDate('Y'), 
                'sysmonth' => Date::currentDate('m'), 
                'sysday' => Date::currentDate('d')
            )
        );
        
        preg_match_all('/\[(.*?)\]/', $title, $pathMatchs);
                
        if (isset($pathMatchs[0][0])) {
            foreach ($pathMatchs[1] as $pathMatch) {
                if (array_key_exists($pathMatch, Mdform::$processParamData)) {
                    $title = str_ireplace("[$pathMatch]", Mdform::$processParamData[$pathMatch], $title);
                }
            }
        }
        
        self::$placeholderTitle[$titleName] = $title;
    
        return $title;
    }

    public function getKpiIndicatorsByTemplateId($templateId) {
        
        $join         = $matrixFields = $where = '';
        $orderBy      = 'TD.ORDER_NUM ASC';
        $parentField  = 'TD.PARENT_ID';
        $idPh1        = $this->db->Param(0);
        $idPh2        = $this->db->Param(1);
        
        if (Mdform::$isUseMergeMatrix) {
            
            $join = 'INNER JOIN KPI_TEMPLATE_MATRIX KMX ON KMX.TEMPLATE_ID = TD.TEMPLATE_ID AND KMX.INDICATOR_ID = TD.INDICATOR_ID';
            $orderBy = 'KMX.ID ASC';
            $parentField = 'null AS PARENT_ID';
            $matrixFields = '
                KMX.NUMBER_COLUMN, 
                KMX.COLUMN1, 
                KMX.COLUMN2, 
                KMX.COLUMN3, 
                KMX.COLUMN4, 
                KMX.COLUMN5, 
                KMX.COLUMN6, 
                KMX.COLUMN7, 
                KMX.COLUMN8, 
                KMX.COLUMN9, 
                KMX.COLUMN10,
                KMX.ROW_STYLE,';
        }
        
        if (Mdform::$subTmpIndctrByCriteria) {
            
            $indIds = self::getSubTemplateIndicatorIdsModel($templateId, Mdform::$subTmpIndctrByCriteria);
            
            if ($indIds['status'] == 'success' && $indIds['ids']) {
                $where = 'AND TD.INDICATOR_ID IN ('.$indIds['ids'].')';
            }
        }
        
        if (Lang::isUseMultiLang() && Lang::getCode() != Lang::getDefaultLangCode()) {
            $indicatorName = "(FNC_TRANSLATE($idPh1, KI.TRANSLATION_VALUE, 'NAME', KI.NAME))";
        } else {
            $indicatorName = 'KI.NAME';
        }
        
        $data = $this->db->GetAll("
            SELECT 
                TD.ID AS DTL_ID, 
                TD.TEMPLATE_ID, 
                TD.INDICATOR_ID, 
                KI.CODE AS INDICATOR_CODE, 
                CASE WHEN $idPh1 != 'mn' 
                    THEN ".$this->db->IfNull('KI.NAME2', $indicatorName)." ELSE KI.NAME 
                END AS INDICATOR_NAME, 
                KI.DESCRIPTION AS IN_DESCRIPTION, 
                $parentField, 
                TD.AGGREGATION_TYPE, 
                TD.IS_REQUIRED, 
                TD.DESCRIPTION, 
                LOWER(TD.CODE) AS DTL_CODE, 
                UPPER(".$this->db->IfNull('TD.COLUMN_NAME', 'KI.COLUMN_NAME').") AS COLUMN_NAME,      
                TD.CELL_STYLE, 
                (
                    SELECT 
                        COUNT(ID) 
                    FROM KPI_TEMPLATE_DTL_FACT 
                    WHERE TEMPLATE_DTL_ID = TD.ID 
                        AND TEMPLATE_FACT_ID IS NOT NULL 
                ) AS FACT_COUNT, 
                (
                    SELECT 
                        COUNT(ID) 
                    FROM KPI_TEMPLATE_DTL 
                    WHERE PARENT_ID = TD.ID
                ) AS CHILD_COUNT, 
                $matrixFields 
                KTI.DIMENSION_ID,
                TD.COL1,
                TD.COL2,
                TD.COL3,
                TD.COL4,
                TD.COL5,
                TD.COL6,
                TD.COL7,
                TD.COL8,
                TD.COL9,
                TD.COL10,
                TD.COL11,
                TD.COL12,
                TD.COL13,
                TD.COL14,
                TD.COL15,
                TD.COL16,
                TD.COL17,
                TD.COL18,
                TD.COL19,
                TD.COL20,
                TD.SHOW_COL_CNT, 
                TD.IS_SHOW, 
                KI.REF_ID, 
                TD.HELP_PROCESS_META_ID 
            FROM KPI_TEMPLATE_DTL TD 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = TD.INDICATOR_ID 
                $join 
                LEFT JOIN KPI_TEMPLATE_DIMENSION KTI ON KTI.TEMPLATE_ID = TD.TEMPLATE_ID 
                    AND KTI.IS_MAIN = 1 
            WHERE TD.TEMPLATE_ID = $idPh2 
                AND KI.IS_ACTIVE = 1 
                AND (TD.WFM_STATUS_ID IS NULL OR TD.WFM_STATUS_ID = 1565666579422270) 
                $where 
            ORDER BY $orderBy", array($this->lang->getCode(), $templateId));
        
        return $data;
    }
    
    public function getKpiFactsByTemplateId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                TF.ID, 
                TF.PARAM_PATH, 
                TF.LABEL_NAME, 
                TF.ORDER_NUM, 
                TC.MIN_VALUE, 
                TC.MAX_VALUE, 
                TF.AGGREGATE_FUNCTION, 
                TF.WIDTH 
            FROM KPI_TEMPLATE_FACT TF 
                LEFT JOIN KPI_TEMPLATE_CRITERIA TC ON TC.TEMPLATE_ID = TF.TEMPLATE_ID 
                    AND LOWER(TC.PARAM_NAME) = LOWER(TF.PARAM_PATH) 
                    AND TC.IS_TOTAL = 1 
                    AND TC.BATCH_NUMBER = 1 
                    AND TC.TEMPLATE_DTL_ID IS NULL 
            WHERE TF.TEMPLATE_ID = ".$this->db->Param(0)." 
            ORDER BY TF.ORDER_NUM ASC", array($templateId));
        
        if ($data) {
            foreach ($data as $k => $row) {
                $data[$k]['LABEL_NAME'] = self::titleReplacer($row['LABEL_NAME']);
            }
        }
        
        return $data;
    }
    
    public function getKpiControlsByTemplateId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                DF.TEMPLATE_DTL_ID, 
                DF.TEMPLATE_FACT_ID, 
                DF.SHOW_TYPE, 
                DF.LOOKUP_META_DATA_ID, 
                TF.PARAM_PATH, 
                TF.LABEL_NAME, 
                ".$this->db->IfNull('TF.DEFAULT_VALUE', 'DF.DEFAULT_VALUE')." AS DEFAULT_VALUE, 
                DF.LOOKUP_CRITERIA, 
                DF.FACT_WIDTH, 
                DF.SUB_TEMPLATE_ID, 
                MFP.PATTERN_TEXT, 
                MFP.PATTERN_NAME, 
                MFP.GLOBE_MESSAGE, 
                MFP.IS_MASK, 
                DF.GET_PROCESS_ID, 
                DF.PLACEHOLDER_NAME, 
                DF.FACT_DATA_LENGTH, 
                DF.FILE_EXTENSION, 
                DF.IS_REQUIRED, 
                DF.COLUMN_COUNT 
            FROM KPI_TEMPLATE_DTL_FACT DF 
                INNER JOIN KPI_TEMPLATE_DTL TD ON TD.ID = DF.TEMPLATE_DTL_ID 
                INNER JOIN KPI_TEMPLATE_FACT TF ON TF.ID = DF.TEMPLATE_FACT_ID 
                LEFT JOIN META_FIELD_PATTERN MFP ON MFP.PATTERN_ID = DF.PATTERN_ID 
            WHERE TD.TEMPLATE_ID = ".$this->db->Param(0), array($templateId));
        
        return $data;
    }
    
    public function getKpiTemplateExpressionModel($templateId, $columnName) {
        
        $tableName = (Mdform::$isIndicatorRendering == false) ? 'KPI_TEMPLATE' : 'KPI_INDICATOR';
        
        return $this->db->GetOne("SELECT $columnName FROM $tableName WHERE ID = ".$this->db->Param(0), array($templateId));
    }
    
    public function getRenderKpiTemplateGridHeadByDv($facts, $dvRecords) {
        
        $data       = issetParamArray($dvRecords['rows']);
        $factsCount = count($facts);
        $rowSpan    = 1;
        
        if ($factsCount > 1) {
            $rowSpan = 2;
        }
        
        $colAttr = array('class' => 'kpiDmDtlid', 'data-cell-path' => 'kpiDmDtl.id', 'rowspan' => $rowSpan);
        
        if (Mdform::$kpiIndicatorWidth) {
            $colAttr['style'] = 'width:'.Mdform::$kpiIndicatorWidth;
        }
        
        $cells = html_tag('th', array('class' => 'rowNumber', 'style' => 'width:30px;', 'rowspan' => $rowSpan), '№');
        $cells .= html_tag('th', $colAttr, Lang::line('META_00133'));
        
        if ($data) {
            
            $idField   = $dvRecords['id'];
            $codeField = $dvRecords['code'];
            $nameField = $dvRecords['name'];
            
            if ($factsCount == 1) {
                
                $kpiTemplateId = Mdform::$kpiTemplateId;
                $factPath      = $facts[0]['PARAM_PATH'];
                
                foreach ($data as $k => $fact) {
                    $k++;
                    $headName = $fact[$nameField] . '<input type="hidden" name="param[kpiPivotHead]['.$kpiTemplateId.']['.$factPath.'][]" value="'.$fact[$idField].'">';
                    $cells .= html_tag('th', array('class' => 'kpiDmDtl'.$factPath, 'data-cell-path' => 'kpiDmDtl.'.$factPath.'.'.$k, 'data-fact-col' => '1'), $headName);
                }
                
            } else {
                
                $secondCells = '';
                
                foreach ($data as $record) {
                    
                    $cells .= html_tag('th', array('class' => 'kpiDmDtl'.$record[$codeField], 'data-cell-path' => 'kpiDmDtl.'.$record[$codeField], 'data-fact-col' => '1', 'colspan' => $factsCount), Lang::line($record[$nameField]));
                    
                    foreach ($facts as $fact) {
                        $headName = Lang::line($fact['LABEL_NAME']) . '<input type="hidden" name="param[kpiPivotHead]['.$kpiTemplateId.']['.$fact['PARAM_PATH'].'][]" value="'.$record[$idField].'">';
                        $secondCells .= html_tag('th', array('class' => 'kpiDmDtl'.$fact['PARAM_PATH'], 'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH']), $headName);
                    }
                }
            }
        }
        
        $rows = html_tag('tr', array(), $cells);
        
        if (isset($secondCells)) {
            $rows .= html_tag('tr', array(), $secondCells);
        }
        
        return $rows;
    }
    
    public function getRenderKpiTemplateGridBodyByDv($indicators, $facts, $dvRecords, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null) {
        
        self::$formType = 'grid';
        $pathPrefix     = Mdform::$pathPrefix;
        $rowCountPrefix = Mdform::$rowCountPrefix;
        $factsCount     = count($facts);
        $dvRows         = issetParamArray($dvRecords['rows']);
        $idField        = $dvRecords['id'];
        $nameField      = $dvRecords['name'];
                
        $rows = '';
        $index = 1;
        
        foreach ($indicators as $k => $indicator) {
                
            if ($indicator['PARENT_ID'] == $parent) {
                
                $dtlId = null;
                $templateDtlId = $indicator['DTL_ID'];
        
                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                
                $kpiTemplateId = Mdform::$kpiTemplateId;
                
                $hiddenId = Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.id]['.$kpiTemplateId.']['.$rowIndex.']', 'data-path' => 'kpiDmDtl.id', 'value' => $dtlId, 'data-field-name' => 'id'));
                $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.indicatorId]['.$kpiTemplateId.']['.$rowIndex.']', 'data-path' => 'kpiDmDtl.indicatorId', 'value' => $indicator['INDICATOR_ID'], 'data-field-name' => 'indicatorId'));
                $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.templateDtlId]['.$kpiTemplateId.']['.$rowIndex.']', 'data-path' => 'kpiDmDtl.templateDtlId', 'value' => $templateDtlId, 'data-field-name' => 'templateDtlId'));
                
                $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.kpiTemplateId]['.$kpiTemplateId.']['.$rowIndex.']', 'data-path' => 'kpiDmDtl.kpiTemplateId', 'value' => Mdform::$kpiTemplateId));
                $hiddenId .= Form::hidden(array('name' => 'param['.$pathPrefix.'kpiDmDtl.indicatorColumnName]['.$kpiTemplateId.']['.$rowIndex.']', 'data-path' => 'kpiDmDtl.indicatorColumnName', 'value' => $indicator['COLUMN_NAME']));
                
                Mdform::$kpiControlIndex++;
                
                $childRows = self::getRenderKpiTemplateGridBodyByDv($indicators, $facts, $dvRecords, $cellControlDatas, $savedData, $indicator['DTL_ID'], $depth + 1, $levelNum.'.');
                
                if ($childRows) {
                    $isBold = ' font-weight-bold';
                }
                
                $cells = '';
                $cells .= html_tag('td', array('class' => 'text-center middle'.$isBold), '<span>'.$levelNum.'</span>');
                $cells .= html_tag('td', array('class' => 'text-left middle padding-3 cell-depth-'. $depth . $isBold, 'style' => $indicator['CELL_STYLE']), $hiddenId . $indicator['INDICATOR_NAME']);
                
                $factPath = $facts[0]['PARAM_PATH'];
                $factId = $facts[0]['ID'];
                
                foreach ($dvRows as $f => $record) {
                    
                    $f++;
                    
                    Mdform::$inputId   = $record[$idField];
                    Mdform::$labelName = $record[$nameField];
                    
                    $savedDataRow = array();
                    
                    if ($savedData && isset(Mdform::$kpiDmMart[$factPath.'_'.$f.'_'.$indicator['COLUMN_NAME']])) {
                        $savedDataRow[$factPath] = Mdform::$kpiDmMart[$factPath.'_'.$f.'_'.$indicator['COLUMN_NAME']];
                    }
                    
                    if ($factsCount == 1) {
                        
                        $control = self::kpiFormControlByDv($rowIndex, $f, $cellControlDatas, $indicator['DTL_ID'], $indicator['DTL_CODE'], $factId, $savedDataRow);
                        
                        $cells .= html_tag('td', 
                            array(
                                'class' => 'kpiDmDtl'.$factPath.' stretchInput middle text-center', 
                                'data-cell-path' => 'kpiDmDtl.'.$factPath.'.'.$f 
                            ), 
                            $control
                        );
                        
                    } else {
                        
                        foreach ($facts as $fact) {
                            
                            $control = self::kpiFormControlByDv($rowIndex, $f, $cellControlDatas, $indicator['DTL_ID'], $indicator['DTL_CODE'], $fact['ID'], $savedDataRow);
                            
                            $cells .= html_tag('td', 
                                array(
                                    'class' => 'kpiDmDtl'.$fact['PARAM_PATH'].' stretchInput middle text-center', 
                                    'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH'].'.'.$f 
                                ), 
                                $control
                            );
                        }
                    }
                }

                $rows .= html_tag('tr', array('data-dtl-code' => $indicator['DTL_CODE']), $cells);
                
                $rows .= $childRows;
                
                $index ++;
            }
        }
        
        return $rows;
    }
    
    public function getKpiHeadDvRecords($row) {
        
        $param = array(
            'systemMetaGroupId' => $row['PIVOT_VALUE_META_DATA_ID'],
            'ignorePermission' => 1, 
            'showQuery' => 0
        );
        $criteria = $row['PIVOT_VALUE_CRITERIA'];
        
        if ($criteria != '') {
            
            parse_str($criteria, $criteriaArr);
            
            foreach ($criteriaArr as $k => $v) {
                $param['criteria'][$k][] = array(
                    'operator' => '=',
                    'operand' => $v
                );
            }
        }
        
        if (Mdform::$processParamData) {
            
            foreach (Mdform::$processParamData as $key => $val) {
                
                if (strpos($key, 'pivotDvFilter') !== false && $val != '') {
                    
                    $param['criteria'][str_replace('pivotDvFilter', '', $key)][] = array(
                        'operator' => '=',
                        'operand' => $val
                    );
                }
            }
        }

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);

        if (isset($data['result']) && isset($data['result'][0])) {
            
            unset($data['result']['aggregatecolumns']);
            unset($data['result']['paging']);
            
            $rows = $data['result'];
            
            $this->load->model('mddatamodel', 'middleware/models/');
            $standartFields = $this->model->getCodeNameFieldNameModel($row['PIVOT_VALUE_META_DATA_ID']);
            
            $facts = array(
                'rows' => $rows, 
                'id' => ($standartFields['id']) ? $standartFields['id'] : 'id', 
                'code' => ($standartFields['code']) ? $standartFields['code'] : 'code', 
                'name' => ($standartFields['name']) ? $standartFields['name'] : 'name', 
            );
            
            $this->load->model('mdform', 'middleware/models/');
        
        } else {
            $facts = array();
        }
        
        return $facts;
    }
    
    public function getKpiIndicatorFactsByTemplateId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                LOWER(TD.CODE)||'.'||TF.PARAM_PATH AS PATH, 
                KI.NAME||' - '||TF.LABEL_NAME AS LABEL_NAME 
            FROM KPI_TEMPLATE_DTL TD 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = TD.INDICATOR_ID 
                INNER JOIN KPI_TEMPLATE_DTL_FACT DF ON DF.TEMPLATE_DTL_ID = TD.ID 
                INNER JOIN KPI_TEMPLATE_FACT TF ON TF.ID = DF.TEMPLATE_FACT_ID 
            WHERE TD.TEMPLATE_ID = ".$this->db->Param(0)." 
            ORDER BY 
                TD.ORDER_NUM ASC, TF.ORDER_NUM ASC", array($templateId));
        
        return $data;
    }
    
    public function getKpiCriteriaByTemplateId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                TC.TEMPLATE_DTL_ID, 
                TC.MIN_VALUE, 
                TC.MAX_VALUE, 
                TC.PARAM_NAME 
            FROM KPI_TEMPLATE_CRITERIA TC 
                INNER JOIN KPI_TEMPLATE_DTL TD ON TD.ID = TC.TEMPLATE_DTL_ID 
            WHERE TC.TEMPLATE_ID = ".$this->db->Param(0)." 
                AND TC.IS_FORM_LABEL = 1 
                AND (TC.MIN_VALUE IS NOT NULL OR TC.MAX_VALUE IS NOT NULL)", array($templateId));
        
        $result = array();
        
        if ($data) {
            
            foreach ($data as $row) {
                $result[$row['TEMPLATE_DTL_ID']][$row['PARAM_NAME']] = array(
                    'minValue' => $row['MIN_VALUE'], 
                    'maxValue' => $row['MAX_VALUE']
                );
            }
            
            Mdform::$isKpiTempCriteria = true;
        }
        
        return $result;
    }
    
    public function getEaRelationListByTmpIdBookId($templateId, $bookId) {
        
        if ($templateId && $bookId) {
            
            $langPh = $this->db->Param(0);
            $templateIdPh = $this->db->Param(1);
            $bookIdPh = $this->db->Param(2);
            $scenarioIdPh = $this->db->Param(3);
            
            $data = $this->db->GetAll(" 
                SELECT 
                    REL.* 
                FROM (
                    SELECT 
                        CASE WHEN $langPh != 'mn' THEN KT_TRG.NAME2 ELSE KT_TRG.NAME END AS NAME1, 
                        CASE WHEN $langPh != 'mn' THEN NVL(KRT_REV.CODE, KRT.CODE) ELSE NVL(KRT_REV.NAME, KRT.NAME) END AS NAME2,  
                        CASE WHEN $langPh != 'mn' THEN SRC_KT.NAME2 ELSE SRC_KT.NAME END AS NAME3, 
                        CASE WHEN $langPh != 'mn' THEN KT_TRG.NAME2 ||' '|| NVL(KRT_REV.CODE, KRT.CODE) ||' '|| SRC_KT.NAME2 ELSE KT_TRG.NAME ||' '|| NVL(KRT_REV.NAME, KRT.NAME) ||' '|| SRC_KT.NAME END AS TEMPLATE_NAME, 
                        CASE WHEN $langPh != 'mn' THEN NVL(SRC_EO.NAME2, SRC_EO.NAME) ELSE SRC_EO.NAME END AS NAME, 
                        KTD.COLUMN_NAME, 
                        KTD.INDICATOR_ID, 
                        KT_MAP.ID AS TEMPLATE_ID, 
                        KT_MAP.SRC_TEMPLATE_ID, 
                        KT_MAP.TRG_TEMPLATE_ID, 
                        ER.ID AS E_ID, 
                        ER.SRC_OBJECT_ID, 
                        ER.TRG_OBJECT_ID, 
                        FNC_GET_EA_KPI_RELATED(ER.SRC_OBJECT_ID, ER.TRG_OBJECT_ID, KTD.COLUMN_NAME, ER.SRC_TEMPLATE_ID, ER.TRG_TEMPLATE_ID, ER.SCENARIO_ID) AS MART_ID,
                        KT_MAP.SRC_TEMPLATE_DTL_ID, 
                        SRC_KT.CATEGORY_ID AS SRC_CATEGORY_ID, 
                        FNC_GET_EA_KPI_RELATION_CNT(ER.ID) AS SHOW_COUNT, 
                        0 AS IS_SHOW_ONLY
                    FROM KPI_TEMPLATE KT_MAP 
                        INNER JOIN KPI_TEMPLATE SRC_KT ON KT_MAP.SRC_TEMPLATE_ID = SRC_KT.ID 
                        INNER JOIN KPI_TEMPLATE_DTL KTD ON KT_MAP.SRC_TEMPLATE_DTL_ID = KTD.ID 
                        INNER JOIN KPI_TEMPLATE KT_TRG ON KT_MAP.TRG_TEMPLATE_ID = KT_TRG.ID 
                        LEFT JOIN KPI_RELATION_TYPE KRT ON KTD.RELATION_TYPE_ID = KRT.ID 
                        LEFT JOIN KPI_RELATION_TYPE KRT_REV ON KRT.REVERSE_ID = KRT_REV.ID 
                        LEFT JOIN EA_RELATION ER ON ER.SRC_TEMPLATE_ID = SRC_KT.ID 
                            AND ER.TRG_TEMPLATE_ID = KT_TRG.ID 
                            AND ER.INDICATOR_ID = KTD.INDICATOR_ID 
                            AND ER.TRG_OBJECT_ID = $bookIdPh 
                            AND ".$this->db->IfNull('ER.SCENARIO_ID', '0')." = $scenarioIdPh 
                        LEFT JOIN EA_OBJECT SRC_EO ON ER.SRC_OBJECT_ID = SRC_EO.ID 
                    WHERE KT_MAP.TRG_TEMPLATE_ID = $templateIdPh 
                    
                    UNION ALL
                    
                    SELECT 
                        CASE WHEN $langPh != 'mn' THEN KT_TRG.NAME2 ELSE KT_TRG.NAME END AS NAME1, 
                        CASE WHEN $langPh != 'mn' THEN NVL(KRT_REV.CODE, KRT.CODE) ELSE NVL(KRT_REV.NAME, KRT.NAME) END AS NAME2,  
                        CASE WHEN $langPh != 'mn' THEN SRC_KT.NAME2 ELSE SRC_KT.NAME END AS NAME3, 
                        CASE WHEN $langPh != 'mn' THEN KT_TRG.NAME2 ||' '|| NVL(KRT_REV.CODE, KRT.CODE) ||' '|| SRC_KT.NAME2 ELSE KT_TRG.NAME ||' '|| NVL(KRT_REV.NAME, KRT.NAME) ||' '|| SRC_KT.NAME END AS TEMPLATE_NAME, 
                        CASE WHEN $langPh != 'mn' THEN NVL(SRC_EO.NAME2, SRC_EO.NAME) ELSE SRC_EO.NAME END AS NAME, 
                        KTD.COLUMN_NAME, 
                        null AS INDICATOR_ID, 
                        null AS TEMPLATE_ID, 
                        null AS SRC_TEMPLATE_ID, 
                        null AS TRG_TEMPLATE_ID, 
                        null AS E_ID, 
                        ER.SRC_OBJECT_ID, 
                        null AS TRG_OBJECT_ID, 
                        null AS MART_ID,
                        null AS SRC_TEMPLATE_DTL_ID, 
                        null AS SRC_CATEGORY_ID, 
                        FNC_GET_EA_KPI_RELATION_CNT(ER.ID) AS SHOW_COUNT,
                        1 AS IS_SHOW_ONLY 
                    FROM KPI_TEMPLATE KT_MAP 
                        INNER JOIN KPI_TEMPLATE SRC_KT ON KT_MAP.SRC_TEMPLATE_ID = SRC_KT.ID 
                        INNER JOIN KPI_TEMPLATE_DTL KTD ON KT_MAP.SRC_TEMPLATE_DTL_ID = KTD.ID 
                        INNER JOIN KPI_TEMPLATE KT_TRG ON KT_MAP.TRG_TEMPLATE_ID = KT_TRG.ID 
                        LEFT JOIN KPI_RELATION_TYPE KRT ON KTD.RELATION_TYPE_ID = KRT.ID 
                        LEFT JOIN KPI_RELATION_TYPE KRT_REV ON KRT.REVERSE_ID = KRT_REV.ID 
                        LEFT JOIN EA_RELATION ER ON ER.SRC_TEMPLATE_ID = SRC_KT.ID 
                            AND ER.TRG_TEMPLATE_ID = KT_TRG.ID 
                            AND ER.INDICATOR_ID = KTD.INDICATOR_ID 
                            AND ER.TRG_OBJECT_ID IN (
                                SELECT 
                                    ID 
                                FROM EA_OBJECT 
                                START WITH PARENT_ID = $bookIdPh 
                                CONNECT BY NOCYCLE PRIOR ID = PARENT_ID 
                            ) 
                            AND ".$this->db->IfNull('ER.SCENARIO_ID', '0')." = $scenarioIdPh 
                        LEFT JOIN EA_OBJECT SRC_EO ON ER.SRC_OBJECT_ID = SRC_EO.ID 
                    WHERE KT_MAP.TRG_TEMPLATE_ID = $templateIdPh 
                        
                ) REL 
                GROUP BY 
                    REL.NAME1, 
                    REL.NAME2, 
                    REL.NAME3, 
                    REL.NAME, 
                    REL.TEMPLATE_NAME, 
                    REL.COLUMN_NAME, 
                    REL.INDICATOR_ID, 
                    REL.TEMPLATE_ID, 
                    REL.SRC_TEMPLATE_ID, 
                    REL.TRG_TEMPLATE_ID, 
                    REL.E_ID, 
                    REL.SRC_OBJECT_ID, 
                    REL.TRG_OBJECT_ID, 
                    REL.MART_ID,
                    REL.SRC_TEMPLATE_DTL_ID, 
                    REL.SRC_CATEGORY_ID, 
                    REL.SHOW_COUNT, 
                    REL.IS_SHOW_ONLY 
                ORDER BY REL.IS_SHOW_ONLY ASC, REL.SHOW_COUNT ASC, REL.NAME1 ASC, REL.NAME2 ASC, REL.NAME3 ASC, REL.NAME ASC", 
                array($this->lang->getCode(), $templateId, $bookId, Ue::sessionScenarioId())
            );

            return $data;
        }
        
        return null;
    }
    
    public function getRenderKpiTemplateGridBodyProc($indicators, $facts, $cellControlDatas, $savedData, $parent = null, $depth = 0, $number = null, $processHeaderParam, $supplierId, $key) {
        
        self::$formType = 'grid';
        
        $pathPrefix     = Mdform::$pathPrefix;
        $rowCountPrefix = Mdform::$rowCountPrefix;
                
        $rows = '';
        $index = 1;
        
        foreach ($indicators as $k => $indicator) {
                
            if ($indicator['PARENT_ID'] == $parent) {
                
                $savedDataRow  = $dtlId = null;
                $templateDtlId = $indicator['DTL_ID'];
                $factCount     = $indicator['FACT_COUNT'];
                
                if ($factCount && $savedData) {
                    
                    if (Mdform::$kpiTypeCode == 2) {
                        
                        $dtlId = isset($savedData['ID']) ? $savedData['ID'] : null;
                        $savedDataRow['fact1'] = isset($savedData[$indicator['COLUMN_NAME']]) ? $savedData[$indicator['COLUMN_NAME']] : null;
                        
                    } else {
                        $arr = array_filter($savedData, function($ar) use($templateDtlId) {
                            return ($ar['templatedtlid'] == $templateDtlId);
                        });

                        unset($savedData[key($arr)]);

                        foreach ($arr as $row) {
                            $savedDataRow = $row;
                        }

                        $dtlId = isset($savedDataRow['id']) ? $savedDataRow['id'] : null;
                    }
                }
        
                $isBold = '';
                $levelNum = $number.$index;
                $rowIndex = Mdform::$kpiControlIndex;
                $isHidden = false;
                
                Mdform::$kpiControlIndex++;
                
                if (isset(Mdform::$kpiTempCriteria[$templateDtlId]) || $factCount == 0) {
                    
                    Mdform::$parentDtlId = $templateDtlId;
                    
                    Mdform::$kpiControlIndex = Mdform::$kpiControlIndex - 1;
                }
                
                if ($key === 0) {
                    $indName = $indicator['INDICATOR_NAME'];
                    if ($indicator['CHILD_COUNT'] > 0) {
                        $indName = '<strong>' . $indicator['INDICATOR_NAME'] . '</strong>';
                    } else if ($depth > 0) {
                        $indName = '<span class="ml10">' . $indicator['INDICATOR_NAME'] . '</span>';
                    }
                    array_push(Mdform::$resultIndicator, $indName);                
                }
                
                $cellControl = '';
                
                foreach ($facts as $fact) {
                    
                    $indicatorCell = $control = '';
                    
                    if (Mdform::$isKpiTempCriteria && isset(Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']])) {
                        
                        $factCriteria = Mdform::$kpiTempCriteria[$templateDtlId][$fact['PARAM_PATH']];
                        
                        $factCriteriaLabel = $minVal = $maxVal = '';
                        
                        if (issetParam($factCriteria['minValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['minValue'] . ' - ';
                            $minVal = $factCriteria['minValue'];
                        }
                        
                        if (issetParam($factCriteria['maxValue']) != '') {
                            $factCriteriaLabel .= $factCriteria['maxValue'];
                            $maxVal = $factCriteria['maxValue'];
                        }
                        
                        $indicatorCell = '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'" data-min-val="'.$minVal.'" data-max-val="'.$maxVal.'">' . $factCriteriaLabel . '</span>';
                        if ($fact['AGGREGATE_FUNCTION']) {
                            $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                        }                                                
                        
                    } else {
                        $control = self::kpiFormControl($rowIndex, $cellControlDatas, $indicator['DTL_ID'], $fact['ID'], $savedDataRow, $indicator, $processHeaderParam);
                    }
                    
                    if ($control) {
                        $isHidden = true;
                    } elseif ($indicatorCell === '') {
                        $indicatorCell .= '<span data-aggregate-indicator="'.$templateDtlId.'" data-fact-code="'.$fact['PARAM_PATH'].'"></span>';
                        if ($fact['AGGREGATE_FUNCTION']) {
                            $indicatorCell .= '<span class="aggregate-indicator-total pull-right pr3" data-aggr-fnc="'.$fact['AGGREGATE_FUNCTION'].'"></span>';
                        }
                        Mdform::$resultFacts[$supplierId][$fact['PARAM_PATH']][] = $control;
                    }
                    
                    $cellControl .= html_tag('td', 
                        array(
                            'class' => 'kpiDmDtl'.$fact['PARAM_PATH'].' stretchInput middle text-center', 
                            'data-cell-path' => 'kpiDmDtl.'.$fact['PARAM_PATH']
                        ), 
                        $control . $indicatorCell
                    );                    
                    
                    if ($control) {
                        Mdform::$resultFacts[$supplierId][$fact['PARAM_PATH']][] = $control;
                    }
                }
                
                self::getRenderKpiTemplateGridBodyProc($indicators, $facts, $cellControlDatas, $savedData, $indicator['DTL_ID'], $depth + 1, $levelNum.'.', $processHeaderParam, $supplierId, $key);
                                
                $index ++;
            }
        }
    }
    
    public function getKpiObjectRelationRowData($id, $dtlId) {
        
        $param = array(
            'systemMetaGroupId' => '1563781803539520',
            'showQuery' => 0,
            'ignorePermission' => 1, 
            'criteria' => array(
                'id' => array(
                    array(
                        'operator' => '=',
                        'operand' => $id
                    )
                ), 
                'templateDtlId' => array(
                    array(
                        'operator' => '=',
                        'operand' => $dtlId
                    )
                )
            )
        );
        
        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if (isset($data['result']) && isset($data['result'][0])) {
            return $data['result'][0];
        }
        
        return null;
    }
    
    public function kpiDmMartTreeGraphModel() {
        
        $result = array();
        $param = array(
            'systemMetaGroupId' => '1583108516118656',
            'showQuery' => 0,
            'ignorePermission' => 1
        );
        
        if (Input::isEmpty('recordId') == false) {
            
            $param['criteria'] = array(
                'id' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('recordId')
                    )
                )
            );
            
            $firstLevel = true;
            
        } else {
            
            $postIds = Input::postData();
            $param['systemMetaGroupId'] = '1582876301961';
            
            foreach ($postIds as $fieldName => $fieldVal) {
                
                if ($fieldVal) {
                    $param['criteria'][$fieldName] = array(
                        array(
                            'operator' => '=',
                            'operand' => $fieldVal
                        )
                    );
                }
            }
        }

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if (isset($data['result']) && isset($data['result'][0])) {
            
            unset($data['result']['aggregatecolumns']);
            unset($data['result']['paging']);
            
            $rows = $data['result'];
            
            if (isset($firstLevel)) {
                
                foreach ($rows as $row) {
                    
                    if (!$row['templateid'] && !$row['relatedtemplateid'] && !$row['arrowtemplateid']) {
                        
                        $result['name'] = $row['name'];
                        $result['color'] = $row['color'];
                        $result['icon'] = $row['icon'];
                        $result['childcount'] = 0;
                        $result['depth'] = 0;
                        
                    } else {
                        $result['children'][] = array(
                            'name' => $row['name'],
                            'childcount' => (int) $row['childcount'], 
                            'rid' => $row['id'], 
                            'templateId' => $row['templateid'], 
                            'relatedTemplateId' => $row['relatedtemplateid'], 
                            'relatedId' => $row['relatedid'], 
                            'arrowTemplateId' => $row['arrowtemplateid'], 
                            'color' => $row['color'], 
                            'icon' => $row['icon']
                        );
                    }
                }
                
                if (isset($result['children'])) {
                    $result['childcount'] = 1;
                }
                
            } else {
                foreach ($rows as $row) {
                    $result[] = array(
                        'name' => $row['name'],
                        'childcount' => (int) $row['childcount'], 
                        '_childcount' => (int) $row['childcount'], 
                        'rid' => $row['id'], 
                        'templateId' => $row['templateid'], 
                        'relatedTemplateId' => $row['relatedtemplateid'], 
                        'relatedId' => $row['relatedid'], 
                        'arrowTemplateId' => $row['arrowtemplateid'], 
                        'color' => $row['color'], 
                        'icon' => $row['icon']
                    );
                }
            }
        }
        
        return $result;
    }
    
    public function getKpiTemplateIdByDmRecordMapModel() {
        
        $param = array(
            'systemMetaGroupId' => '1584498402944',
            'showQuery' => 0,
            'ignorePermission' => 1, 
            'criteria' => array(
                'srcRefStructureId' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('srcRefStructureId')
                    )
                ), 
                'srcWfmStatusId' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('srcWfmStatusId')
                    )
                ), 
                'trgRefStructureId' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('trgRefStructureId')
                    )
                ), 
                'trgWfmStatusId' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('trgWfmStatusId')
                    )
                )
            )
        );

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if (isset($data['result']) && isset($data['result'][0]['templateid'])) {
            return $data['result'][0]['templateid'];
        }
        
        return null;
    }
    
    public function getGroupedObjectListModel($bookId) {
        
        $param = array(
            'systemMetaGroupId' => '1591237517573229',
            'showQuery' => 0,
            'ignorePermission' => 1, 
            'criteria' => array(
                'id' => array(
                    array(
                        'operator' => '=',
                        'operand' => $bookId
                    )
                )
            )
        );

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if (isset($data['result']) && isset($data['result'][0])) {
            
            unset($data['result']['aggregatecolumns']);
            unset($data['result']['paging']);
            
            return $data['result'];
        }
        
        return null;
    }
    
    public function getKpiTemplatesByRefStrIdModel() {
        
        $param = array(
            'systemMetaGroupId' => '1593582574405582',
            'showQuery' => 0,
            'ignorePermission' => 1, 
            'criteria' => array(
                'refStructureId' => array(
                    array(
                        'operator' => '=',
                        'operand' => Input::numeric('refStructureId')
                    )
                )
            )
        );

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);
        
        if ($data['status'] == 'success') {
            
            if (isset($data['result'][0])) {
                
                unset($data['result']['aggregatecolumns']);
                unset($data['result']['paging']);
                
                $result = array('status' => 'success', 'data' => $data['result']);
            } else {
                $result = array('status' => 'error', 'message' => 'KPI загвар олдсонгүй!');
            }
            
        } else {
            $result = array('status' => 'error', 'message' => $this->ws->getResponseMessage($data));
        }
        
        return $result;
    }
    
    public function getDrillPanelTypeListModel() {
        
        $param = array(
            'id' => Input::numeric('objectId')
        );

        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'eaRelationNavigationGetDV_004', $param);
        
        return $data;
    }
    
    public function getKpiTemplatesByFilterNameModel() {
        
        $q = Str::lower(Input::post('q'));
        $type = Input::post('type');
        
        if ($type == 'template') {
            
            $data = $this->db->GetAll("
                SELECT 
                    ID, 
                    CODE, 
                    NAME 
                FROM KPI_TEMPLATE 
                WHERE LOWER(NAME) LIKE '%' || ".$this->db->Param(0)." || '%' 
                ORDER BY NAME ASC", array($q));
            
        } elseif ($type == 'indicator') {
            
            $templateId = Input::numeric('templateId');
            
            $data = $this->db->GetAll("
                SELECT   
                    TD.INDICATOR_ID AS ID, 
                    KI.CODE, 
                    CASE WHEN ".$this->db->Param(0)." != 'mn' 
                        THEN ".$this->db->IfNull('KI.NAME2', 'KI.NAME')." ELSE KI.NAME  
                    END AS NAME 
                FROM KPI_TEMPLATE_DTL TD 
                    INNER JOIN KPI_INDICATOR KI ON KI.ID = TD.INDICATOR_ID 
                WHERE TD.TEMPLATE_ID = ".$this->db->Param(1)." 
                    AND KI.IS_ACTIVE = 1 
                    AND (TD.WFM_STATUS_ID IS NULL OR TD.WFM_STATUS_ID = 1565666579422270) 
                    AND LOWER(KI.NAME) LIKE '%' || ".$this->db->Param(2)." || '%'
                ORDER BY KI.NAME ASC", array($this->lang->getCode(), $templateId, $q));
            
        } else {
            
            $templateId = Input::numeric('templateId');
            $indicatorId = Input::numeric('indicatorId');
            
            $data = $this->db->GetAll("
                SELECT 
                    TF.ID, 
                    TF.PARAM_PATH AS CODE, 
                    TF.LABEL_NAME AS NAME  
                FROM KPI_TEMPLATE_FACT TF 
                    INNER JOIN KPI_TEMPLATE_DTL_FACT DF ON DF.TEMPLATE_FACT_ID = TF.ID 
                    INNER JOIN KPI_TEMPLATE_DTL TD ON TD.ID = DF.TEMPLATE_DTL_ID 
                WHERE TF.TEMPLATE_ID = ".$this->db->Param(0)." 
                    AND TD.INDICATOR_ID = ".$this->db->Param(1)." 
                    AND LOWER(TF.LABEL_NAME) LIKE '%' || ".$this->db->Param(2)." || '%' 
                ORDER BY TF.ORDER_NUM ASC", array($templateId, $indicatorId, $q));
        }
        
        return $data;
    }
    
    public function getKpiTemplateRowByCodeModel($code) {
        $code = Str::lower(Input::param($code));
        $row = $this->db->GetRow("SELECT ID, CODE, NAME FROM KPI_TEMPLATE WHERE LOWER(CODE) = ".$this->db->Param(0), array($code));
        return $row;
    }
    
    public function getKpiIndicatorRowByCodeModel($templateId, $indCode) {
        $indCode = Str::lower(Input::param($indCode));
        
        $row = $this->db->GetRow("
            SELECT   
                TD.INDICATOR_ID AS ID, 
                KI.CODE, 
                CASE WHEN ".$this->db->Param(0)." != 'mn' 
                    THEN ".$this->db->IfNull('KI.NAME2', 'KI.NAME')." ELSE KI.NAME  
                END AS NAME 
            FROM KPI_TEMPLATE_DTL TD 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = TD.INDICATOR_ID 
            WHERE TD.TEMPLATE_ID = ".$this->db->Param(1)." 
                AND KI.IS_ACTIVE = 1 
                AND (TD.WFM_STATUS_ID IS NULL OR TD.WFM_STATUS_ID = 1565666579422270) 
                AND LOWER(KI.CODE) = ".$this->db->Param(2), 
            array($this->lang->getCode(), $templateId, $indCode)
        );
        
        return $row;
    }
    
    public function getKpiFactRowByCodeModel($templateId, $indicatorId, $factCode) {
        $factCode = Str::lower(Input::param($factCode));
        
        $row = $this->db->GetRow("
            SELECT 
                TF.ID, 
                TF.PARAM_PATH AS CODE, 
                TF.LABEL_NAME AS NAME  
            FROM KPI_TEMPLATE_FACT TF 
                INNER JOIN KPI_TEMPLATE_DTL_FACT DF ON DF.TEMPLATE_FACT_ID = TF.ID 
                INNER JOIN KPI_TEMPLATE_DTL TD ON TD.ID = DF.TEMPLATE_DTL_ID 
            WHERE TF.TEMPLATE_ID = ".$this->db->Param(0)." 
                AND TD.INDICATOR_ID = ".$this->db->Param(1)." 
                AND LOWER(TF.PARAM_PATH) = ".$this->db->Param(2), 
            array($templateId, $indicatorId, $factCode)
        );
        
        return $row;
    }
    
    public function getKpiCalculateFunctionsModel() {
        $data = $this->db->GetAll("SELECT FUNCTION_NAME, NAME FROM KPI_CALCULATE_FUNCTION");
        return $data;
    }
    
    public function getValueByGetProcessIdKpiModel($processId, $indicatorId) {
        
        if (!isset(self::$processCodes[$processId])) {
            $processCode = $this->db->GetOne("SELECT META_DATA_CODE FROM META_DATA WHERE META_DATA_ID = ".$this->db->Param(0), array($processId));
            self::$processCodes[$processId] = $processCode ? $processCode : 0;
        }
        
        if (self::$processCodes[$processId]) {
            
            foreach (Mdform::$processParamData as $key => $val) {
                if (!is_array($val) && $val != '') {
                    $param[$key] = $val;
                }
            }
            
            $param['filterIndicatorId'] = $indicatorId;
            
            $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, self::$processCodes[$processId], $param);
            
            if ($result['status'] == 'success' && isset($result['result']['factvalue'])) {
                return $result['result']['factvalue'];
            }
        }
        
        return null;
    }
    
    public function getKpiIndicatorRowModel($templateId) {
        
        if (!isset(self::$kpiIndicatorRow[$templateId])) {
            
            try {
                
                $langCode = Lang::getCode();
            
                $row = $this->db->GetRow("
                    SELECT 
                        KI.ID, 
                        KI.CODE, 
                        ".$this->db->IfNull('KI.LABEL_NAME', "FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME)")." AS NAME, 
                        KI.DESCRIPTION, 
                        KI.RENDER_TYPE, 
                        KI.TABLE_NAME, 
                        KI.QUERY_STRING, 
                        KI.PARENT_ID, 
                        2 AS TYPE_CODE, 
                        KI.NAME_PATTERN, 
                        KI.KPI_TYPE_ID, 
                        KI.STRUCTURE_INDICATOR_ID, 
                        null AS PIVOT_VALUE_META_DATA_ID, 
                        null AS PIVOT_VALUE_CRITERIA, 
                        null AS INDICATOR_COL_WIDTH, 
                        null AS WIDTH, 
                        null AS HEIGHT, 
                        null AS EXPRESSION_TEMPLATE_ID,
                        null AS TYPE_ID, 
                        null AS DEFAULT_TEMPLATE_ID, 
                        null AS MERGE_COL_COUNT, 
                        KI.IS_USE_WORKFLOW, 
                        KI.IS_FILTER_SHOW_DATA, 
                        KI.IS_USE_COMPANY_DEPARTMENT_ID, 
                        KI.IS_DATA_COMPANY_DEPARTMENT_ID, 
                        KI.IS_ADDON_FILE, 
                        KI.IS_ADDON_PHOTO,
                        KI.GRAPH_JSON, 
                        KI.GRID_OPTION, 
                        (
                            SELECT 
                                COUNT(1) 
                            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                                INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                                    AND T1.KPI_TYPE_ID = 2011  
                                INNER JOIN META_REPORT_TEMPLATE_LINK T2 ON T2.MAIN_INDICATOR_ID = T1.ID 
                                    AND T2.DATA_INDICATOR_ID IS NOT NULL 
                            WHERE T0.SRC_INDICATOR_ID = KI.ID  
                                AND T0.SEMANTIC_TYPE_ID = 10000015
                        ) AS COUNT_REPORT_TEMPLATE, 
                        (
                            SELECT 
                                COUNT(1) 
                            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                            WHERE T0.MAIN_INDICATOR_ID = KI.ID 
                                AND T0.IS_UNIQUE = 1 
                                AND T0.COLUMN_NAME IS NOT NULL 
                                AND ".$this->db->IfNull('T0.IS_INPUT', '0')." = 1 
                                AND T0.SHOW_TYPE NOT IN ('row', 'rows', 'label', 'config') 
                        ) AS COUNT_UNIQUE 
                    FROM KPI_INDICATOR KI 
                    WHERE KI.ID = ".$this->db->Param(0), 
                    array($templateId)
                );

                if ($row) {

                    $isQueryString = ($row['QUERY_STRING']) ? true : false;
                    $isCheckSystemTable = $isQueryString ? true : self::isCheckSystemTable($row['TABLE_NAME']);

                    $row['isCheckSystemTable'] = $isCheckSystemTable;
                    
                    if ($row['GRID_OPTION']) {
                        $gridOption = @json_decode($row['GRID_OPTION'], true);
                        
                        if ($gridOption) {
                            $row['gridOption'] = Arr::changeKeyLower($gridOption);
                        }
                    }
                }

                self::$kpiIndicatorRow[$templateId] = $row;
            
            } catch (Exception $ex) {
                $row = array();
                self::$kpiIndicatorRow[$templateId] = $row;
            }
            
        } else {
            $row = self::$kpiIndicatorRow[$templateId];
        }
        
        return $row;
    }
    
    public function getChildKpiIndicatorsModel($indicatorId) {
        
        $langCode = Lang::getCode();
        
        $data = $this->db->GetAll("
            SELECT 
                KI.ID, 
                KI.CODE, 
                FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS NAME, 
                KI.RENDER_TYPE, 
                2 AS TYPE_CODE, 
                null AS PIVOT_VALUE_META_DATA_ID, 
                null AS PIVOT_VALUE_CRITERIA, 
                null AS INDICATOR_COL_WIDTH, 
                null AS WIDTH, 
                null AS HEIGHT, 
                null AS EXPRESSION_TEMPLATE_ID, 
                null AS TYPE_ID, 
                null AS DEFAULT_TEMPLATE_ID, 
                null AS MERGE_COL_COUNT  
            FROM KPI_INDICATOR_INDICATOR_MAP IM 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = IM.TRG_INDICATOR_ID  
                    AND KI.TYPE_ID = 404
            WHERE IM.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
            ORDER BY IM.ID ASC", array($indicatorId));
        
        return $data;
    }
    
    public function getKpiIndicatorsByIndicatorId($templateId) {
        
        $idPh1 = $this->db->Param(0);
        $idPh2 = $this->db->Param(1);
        
        if (Lang::isUseMultiLang() && Lang::getCode() != Lang::getDefaultLangCode()) {
            $indicatorName = "(FNC_TRANSLATE($idPh1, KI.TRANSLATION_VALUE, 'NAME', KI.NAME))";
        } else {
            $indicatorName = 'KI.NAME';
        }
        
        $data = $this->db->GetAll("
            SELECT 
                KI.ID AS DTL_ID, 
                IM.ID AS MAP_ID, 
                KI.ID AS TEMPLATE_ID, 
                KI.ID AS INDICATOR_ID, 
                KI.CODE AS INDICATOR_CODE, 
                CASE WHEN $idPh1 != 'mn' 
                    THEN ".$this->db->IfNull('KI.NAME2', $indicatorName)." ELSE KI.NAME 
                END AS INDICATOR_NAME, 
                KI.DESCRIPTION AS IN_DESCRIPTION, 
                CASE WHEN IM.SRC_INDICATOR_ID = $idPh2 THEN null ELSE IM.SRC_INDICATOR_ID END AS PARENT_ID, 
                null AS AGGREGATION_TYPE, 
                null IS_REQUIRED, 
                IM.DESCRIPTION, 
                LOWER(IM.CODE) AS DTL_CODE, 
                IM.COLUMN_NAME, 
                null AS CELL_STYLE, 
                (
                    SELECT 
                        COUNT(ID) 
                    FROM KPI_INDICATOR_INDICATOR_FACT 
                    WHERE INDICATOR_MAP_ID = IM.ID 
                ) AS FACT_COUNT, 
                (
                    SELECT 
                        COUNT(ID) 
                    FROM KPI_INDICATOR_INDICATOR_MAP 
                    WHERE SRC_INDICATOR_ID = IM.TRG_INDICATOR_ID 
                ) AS CHILD_COUNT, 
                KTI.DIMENSION_ID, 
                null AS COL1,
                null AS COL2,
                null AS COL3,
                null AS COL4,
                null AS COL5,
                null AS COL6,
                null AS COL7,
                null AS COL8,
                null AS COL9,
                null AS COL10,
                null AS COL11,
                null AS COL12,
                null AS COL13,
                null AS COL14,
                null AS COL15,
                null AS SHOW_COL_CNT, 
                1 AS IS_SHOW, 
                null AS REF_ID, 
                null AS HELP_PROCESS_META_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP IM 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = IM.TRG_INDICATOR_ID 
                LEFT JOIN KPI_TEMPLATE_DIMENSION KTI ON KTI.INDICATOR_ID = $idPh2  
                    AND KTI.IS_MAIN = 1 
            CONNECT BY NOCYCLE 
            PRIOR IM.TRG_INDICATOR_ID = IM.SRC_INDICATOR_ID 
            START WITH IM.SRC_INDICATOR_ID = $idPh2   

            ORDER BY IM.ORDER_NUMBER ASC", array($this->lang->getCode(), $templateId));
        
        return $data;
    }
    
    public function getKpiFactsByIndicatorId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                TF.ID, 
                TF.PARAM_PATH, 
                TF.LABEL_NAME, 
                TF.ORDER_NUM, 
                null AS MIN_VALUE, 
                null AS MAX_VALUE, 
                TF.AGGREGATE_FUNCTION, 
                TF.WIDTH 
            FROM KPI_INDICATOR_FACT TF 
            WHERE TF.INDICATOR_ID = ".$this->db->Param(0)." 
            ORDER BY TF.ORDER_NUM ASC", array($templateId));
        
        return $data;
    }
    
    public function getKpiControlsByIndicatorId($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                DF.INDICATOR_MAP_ID AS TEMPLATE_DTL_ID, 
                DF.FACT_ID AS TEMPLATE_FACT_ID, 
                DF.SHOW_TYPE, 
                DF.LOOKUP_META_DATA_ID, 
                TF.PARAM_PATH, 
                TF.LABEL_NAME, 
                ".$this->db->IfNull('TF.DEFAULT_VALUE', 'DF.DEFAULT_VALUE')." AS DEFAULT_VALUE, 
                DF.LOOKUP_CRITERIA, 
                DF.FACT_WIDTH, 
                null AS SUB_TEMPLATE_ID, 
                null AS PATTERN_TEXT, 
                null AS PATTERN_NAME, 
                null AS GLOBE_MESSAGE, 
                null AS IS_MASK, 
                DF.GET_PROCESS_ID, 
                DF.PLACEHOLDER_NAME, 
                DF.FACT_DATA_LENGTH, 
                null AS FILE_EXTENSION, 
                null AS IS_REQUIRED 
            FROM (
                SELECT 
                    IM.ID, 
                    IM.SRC_INDICATOR_ID 
                  FROM KPI_INDICATOR_INDICATOR_MAP IM
                    START WITH IM.SRC_INDICATOR_ID = ".$this->db->Param(0)."
                    CONNECT BY PRIOR IM.TRG_INDICATOR_ID = IM.SRC_INDICATOR_ID
                ) IM
                INNER JOIN KPI_INDICATOR_FACT TF ON IM.SRC_INDICATOR_ID = TF.INDICATOR_ID
                INNER JOIN KPI_INDICATOR_INDICATOR_FACT DF ON IM.ID = DF.INDICATOR_MAP_ID", array($templateId));
        
        return $data;
    }
    
    public function kpiIndicatorBpRunModel() {
        
        try {
            
            $fiscalPeriodId = Input::numeric('fiscalPeriodId');
            $startDate = Date::formatter(Input::post('startDate'), 'Y-m-d');
            $endDate = Date::formatter(Input::post('endDate'), 'Y-m-d');
            $runBpDvId = Input::numeric('runBpDvId');
            $indicatorId = Input::numeric('indicatorId');
            $doneWfmStatusId = Input::numeric('doneWfmStatusId');
            $newWfmStatusId = Input::numeric('newWfmStatusId');

            $param = array(
                'systemMetaGroupId' => $runBpDvId,
                'showQuery' => 0,
                'ignorePermission' => 1, 
                'criteria' => array(
                    'indicatorId' => array(
                        array(
                            'operator' => '=',
                            'operand' => $indicatorId
                        )
                    )
                )
            );

            $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, Mddatamodel::$getDataViewCommand, $param);

            if (isset($data['result']) && isset($data['result'][0])) {
                
                set_time_limit(0);
                
                unset($data['result']['aggregatecolumns']);

                $rows = $data['result'];
                $sessionUserKeyId = Ue::sessionUserKeyId();
                
                foreach ($rows as $k => $row) {
                    
                    $employeeId = $row['employeeid'];
                    
                    $inputParam = array(
                        'filterStartDate' => $startDate, 
                        'filterEndDate' => $endDate, 
                        'filterEmployeeId' => $employeeId
                    );

                    $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, $row['metadatacode'], $inputParam);

                    if (issetParam($result['status']) == 'success' && isset($result['result']) && $result['result']) {

                        $resultRow = $result['result'];
                        
                        foreach ($resultRow as $resultKey => $resultVal) {
                            if (is_numeric($resultVal) && is_float($resultVal + 0)) {
                                $resultRow[$resultKey] = number_format($resultVal, 2, '.', '');
                            }
                        }
                        
                        $insertData = array(
                            'ID' => getUIDAdd($k), 
                            'FISCAL_PERIOD_ID' => $fiscalPeriodId, 
                            'EMPLOYEE_ID' => $employeeId, 
                            'EMPLOYEE_KEY_ID' => $row['employeekeyid'], 
                            'INDICATOR_ID' => $indicatorId, 
                            'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s'), 
                            'CREATED_USER_ID' => $sessionUserKeyId, 
                            'WFM_STATUS_ID' => $newWfmStatusId 
                        );
                        
                        $insertData = array_merge($insertData, $resultRow);
                        
                        $this->db->Execute("
                            DELETE 
                            FROM HCM_KPI_DATA_SOURCE 
                            WHERE FISCAL_PERIOD_ID = $fiscalPeriodId 
                                AND EMPLOYEE_ID = $employeeId 
                                AND WFM_STATUS_ID <> $doneWfmStatusId 
                                AND INDICATOR_ID = $indicatorId");
                        
                        $this->db->AutoExecute('HCM_KPI_DATA_SOURCE', $insertData);

                        $isSuccess = true;
                    }

                    unset($result);
                }

                unset($rows);

                if (isset($isSuccess)) {
                    $response = array('status' => 'success', 'message' => 'Success');
                } else {
                    $response = array('status' => 'error', 'message' => 'Ажилласан процессоос үр дүн олдсонгүй!');
                }

            } else {
                $response = array('status' => 'error', 'message' => $runBpDvId . ' уг жагсаалтаас үр дүн олдсонгүй!');
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiIndicatorValueTemplateModel($indicatorId) {
        
        $data = $this->db->GetAll("
            SELECT 
                * 
            FROM KPI_INDICATOR_VALUE_TEMPLATE 
            WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
            ORDER BY ORDER_NUMBER ASC", 
            array($indicatorId)
        );
        
        return $data;
    }
    
    public function renderKpiIndicatorValueTemplateModel($indicatorId, $data) {
        
        $render = array();
        
        foreach ($data as $row) {
            
            if (!$row['PARENT_ID']) {
                
                $render[] = $row['ROW1'] . ' ' . $row['ROW2'] . ' ' . $row['ROW3'] . ' ' . $row['COLUMN1'] . ' ' . $row['COLUMN2'] . ' ' . $row['COLUMN3'];
                $render[] = '<br />';
                
                $id = $row['ID'];
                $rowType = $row['ROW_TYPE'];
                
                $arr = array_filter($data, function($ar) use($id) {
                    return ($ar['PARENT_ID'] == $id);
                });
                
                if ($arr) {
                    $render[] = self::childKpiIndicatorValueTemplate($indicatorId, $rowType, $arr);
                }
            }
        }
        
        return implode('', $render);
    }
    
    public function childKpiIndicatorValueTemplate($indicatorId, $rowType, $arr) {
        
        $render = array();
        
        if ($rowType == 'TABLE') {
            
            $row1 = $row2 = $row3 = $col1 = $col2 = $col3 = 0;
            
            foreach ($arr as $arrRow) {
                if ($arrRow['ROW1'] != '') {
                    $row1 = 1;
                }
                if ($arrRow['ROW2'] != '') {
                    $row2 = 1;
                }
                if ($arrRow['ROW3'] != '') {
                    $row3 = 1;
                }
                if ($arrRow['COLUMN1'] != '') {
                    $col1 = 1;
                }
                if ($arrRow['COLUMN2'] != '') {
                    $col2 = 1;
                }
                if ($arrRow['COLUMN3'] != '') {
                    $col3 = 1;
                }
            }
            
            $colCount = $row1 + $row2 + $row3 + $col1 + $col2 + $col3;
            $isThead = $isTbody = false;
            
            $render[] = '<table class="table table-bordered">';
            
            foreach ($arr as $arrRow) {
                
                $render[] = '<tr>';
                
                    for ($c = 1; $c <= $colCount; $c++) {
                        
                        $cellVal = '';
                        
                        if ($c == 1) {
                            $cellVal = $arrRow['ROW1'];
                        } elseif ($c == 2) {
                            $cellVal = $arrRow['ROW2'];
                        } elseif ($c == 3) {
                            $cellVal = $arrRow['ROW3'];
                        } elseif ($c == 4) {
                            $cellVal = $arrRow['COLUMN1'];
                        } elseif ($c == 5) {
                            $cellVal = $arrRow['COLUMN2'];
                        } elseif ($c == 6) {
                            $cellVal = $arrRow['COLUMN3'];
                        }
                        
                        $render[] = '<td>'.$cellVal.'</td>';
                    }
                    
                $render[] = '</tr>';
                
                /*if ($arrRow['ROW_TYPE'] == 'HEADER') {
                    
                    if ($isThead == false) {
                        
                        $render[] = '<thead>'.implode('', $renderRow).'</thead>';
                        $isThead = true;
                        $isTbody = false;
                    }
                    
                } elseif ($isTbody == false) {
                    
                    $render[] = '<tbody>'.implode('', $renderRow).'</tbody>';
                    
                    $isThead = false;
                    $isTbody = true;
                }*/
            }
            
            $render[] = '</table>';
        }
        
        return implode('', $render);
    }
    
    public function getKpiIndicatorIndicatorMapModel($id) {
        $row = $this->db->GetRow("SELECT * FROM KPI_INDICATOR_INDICATOR_MAP WHERE ID = ".$this->db->Param(0), $id);
        return $row;
    }
    
    public function getKpiIndicatorTemplateModel($indicatorId, $childId = null, $isExport = false) {
        
        $langCode = Lang::getCode();
        $bindVars = array(
            'filterMainId' => $this->db->addQ($indicatorId)
        );
        $where = '';
        
        if ($columns = issetParam($_POST['param']['columns'])) {
            $where = " AND M.COLUMN_NAME IN ($columns)";
        }
        
        if ($mapId = issetVar($_POST['param']['mapId'])) {
            if (is_numeric($mapId)) {
                $where .= " AND M.COLUMN_NAME IN (SELECT COLUMN_NAME FROM KPI_INDICATOR_INDICATOR_MAP WHERE PARENT_ID = $mapId) ";
            }
        }
        
        if ($isExport) {
            $orderBy = 'ORDER BY PARENT_ID DESC, ORDER_NUMBER ASC';
            $where = " AND M.SHOW_TYPE NOT IN ('file', 'config') ";
        } else {
            $orderBy = 'ORDER BY ORDER_NUMBER ASC';
        }
        
        if ($childId) {
            
            $where .= 'START WITH (M.ID = :filterChildMapId OR :filterChildMapId IS NULL) 
                    CONNECT BY NOCYCLE PRIOR M.ID = M.PARENT_ID ';
            
            $bindVars['filterChildMapId'] = $this->db->addQ($childId);
        }
        
        $cache = phpFastCache();
        $cacheName = 'kpiIndicatorParams_'.$indicatorId.'_'.md5($langCode.'_'.$where);
        $data = $cache->get($cacheName);
        
        if ($data == null) {
            
            $data = $this->db->GetAll("
                SELECT 
                    * 
                FROM (
                    SELECT 
                        KI.ID, 
                        NULL AS CODE, 
                        FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS NAME, 
                        KI.ID AS MAIN_INDICATOR_ID, 
                        NULL AS PARENT_ID, 
                        KI.TEMPLATE_TABLE_NAME, 
                        KI.TABLE_NAME, 
                        UPPER(KI.COLUMN_NAME) AS COLUMN_NAME, 
                        NULL AS COLUMN_NAME_PATH, 
                        NULL AS MAP_ID, 
                        NULL AS SEMANTIC_TYPE_NAME, 
                        KI.RENDER_TYPE,
                        NULL AS SHOW_TYPE,
                        0 AS ORDER_NUMBER,
                        NULL AS FILTER_INDICATOR_ID,
                        NULL AS LOOKUP_CRITERIA,
                        NULL AS GROUP_ORDER,
                        NULL AS MERGE_TYPE,
                        1 AS IS_RENDER,
                        NULL AS IS_PARENT,
                        NULL AS EXPRESSION_STRING,
                        NULL AS LABEL_NAME, 
                        NULL AS INPUT_NAME,  
                        KI.NAME_PATTERN,
                        LOWER(KI.WINDOW_TYPE) AS WINDOW_TYPE, 
                        LOWER(KI.WINDOW_SIZE) AS WINDOW_SIZE, 
                        KI.WINDOW_WIDTH, 
                        KI.WINDOW_HEIGHT, 
                        KI.LABEL_WIDTH, 
                        NULL AS TRG_TABLE_NAME, 
                        NULL AS IS_SELECT_QUERY, 
                        NULL AS DEFAULT_VALUE, 
                        NULL AS CRITERIA_PATH,
                        NULL AS REVERSE_CRITERIA_PATH, 
                        NULL AS LOOKUP_META_DATA_ID,
                        NULL AS IS_REQUIRED, 
                        NULL AS COLUMN_WIDTH, 
                        NULL AS PATTERN_ID, 
                        NULL AS PATTERN_TEXT, 
                        NULL AS PATTERN_NAME, 
                        NULL AS GLOBE_MESSAGE, 
                        NULL AS IS_MASK, 
                        NULL AS PLACEHOLDER_NAME, 
                        NULL AS MIN_VALUE, 
                        NULL AS MAX_VALUE, 
                        CASE WHEN 
                            KC.COMPONENT_ID IS NOT NULL  
                        THEN 1 
                        ELSE 0 END AS IS_USE_COMPONENT, 
                        NULL AS IS_UNIQUE, 
                        NULL AS GROUP_CONFIG_FIELD_PATH, 
                        NULL AS GROUP_CONFIG_PARAM_PATH,
                        NULL AS GROUP_CONFIG_LOOKUP_PATH, 
                        KI.PROFILE_PICTURE, 
                        KI.RENDER_THEME, 
                        NULL AS BODY_ALIGN, 
                        KI.KPI_TYPE_ID, 
                        KI.IS_ADDON_PHOTO, 
                        KI.IS_ADDON_FILE, 
                        KI.IS_ADDON_COMMENT, 
                        NULL AS TAB_NAME, 
                        NULL AS TAB_NAME_TOP, 
                        NULL AS JSON_CONFIG, 
                        NULL AS IS_NOT_TITLE, 
                        NULL AS COLUMN_AGGREGATE, 
                        NULL AS ROW_COUNT_LIMIT, 
                        NULL AS DEFAULT_FILE, 
                        NULL AS IS_EXCEL_IMPORT, 
                        NULL AS IS_TRANSLATE, 
                        NULL AS IS_FROM_MART_EXPRESSION, 
                        NULL AS WIDGET_CODE, 
                        NULL AS IS_ROWS_LOOKUP, 
                        NULL AS REPLACE_PATH, 
                        NULL AS INLINE_FIELDS 
                    FROM KPI_INDICATOR KI 
                        LEFT JOIN (
                            SELECT 
                                MAX(K.ID) AS COMPONENT_ID
                            FROM KPI_INDICATOR K
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M ON K.ID = M.SRC_INDICATOR_ID 
                                    AND M.SEMANTIC_TYPE_ID = 10000010 
                                INNER JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                            WHERE K.ID = :filterMainId 
                        ) KC ON KC.COMPONENT_ID = KI.ID
                    WHERE KI.ID = :filterMainId 

                    UNION  

                    SELECT 
                        M.ID, 
                        M.CODE,
                        NVL(FNC_TRANSLATE('$langCode', M.TRANSLATION_VALUE, 'LABEL_NAME', M.LABEL_NAME), FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME)) AS NAME,
                        M.MAIN_INDICATOR_ID, 
                        NVL(M.PARENT_ID, M.MAIN_INDICATOR_ID) AS PARENT_ID,
                        M.TEMPLATE_TABLE_NAME, 
                        NULL AS TABLE_NAME, 
                        UPPER(M.COLUMN_NAME) AS COLUMN_NAME, 
                        M.COLUMN_NAME_PATH, 
                        M.ID AS MAP_ID, 
                        MST.NAME AS SEMANTIC_TYPE_NAME, 
                        M.RENDER_TYPE,
                        M.SHOW_TYPE,
                        M.ORDER_NUMBER,
                        M.TRG_INDICATOR_ID AS FILTER_INDICATOR_ID,
                        M.LOOKUP_CRITERIA,
                        M.GROUP_ORDER,
                        M.MERGE_TYPE,
                        M.IS_RENDER,
                        M.IS_PARENT,
                        M.EXPRESSION_STRING,
                        M.LABEL_NAME, 
                        M.INPUT_NAME, 
                        NULL AS NAME_PATTERN, 
                        NULL AS WINDOW_TYPE, 
                        NULL AS WINDOW_SIZE, 
                        NULL AS WINDOW_WIDTH, 
                        NULL AS WINDOW_HEIGHT, 
                        NULL AS LABEL_WIDTH,                  
                        KI.TABLE_NAME AS TRG_TABLE_NAME, 
                        CASE WHEN KI.TABLE_NAME IS NOT NULL OR KI.QUERY_STRING IS NOT NULL 
                        THEN 1 ELSE 0 END AS IS_SELECT_QUERY, 
                        M.DEFAULT_VALUE, 
                        CP.CRITERIA_PATH,
                        RCP.REVERSE_CRITERIA_PATH, 
                        M.LOOKUP_META_DATA_ID, 
                        M.IS_REQUIRED, 
                        M.COLUMN_WIDTH, 
                        M.PATTERN_ID, 
                        MFP.PATTERN_TEXT, 
                        MFP.PATTERN_NAME, 
                        MFP.GLOBE_MESSAGE, 
                        MFP.IS_MASK, 
                        FNC_TRANSLATE('$langCode', M.TRANSLATION_VALUE, 'PLACEHOLDER_NAME', M.PLACEHOLDER_NAME) AS PLACEHOLDER_NAME, 
                        M.MIN_VALUE, 
                        M.MAX_VALUE, 
                        NULL AS IS_USE_COMPONENT, 
                        M.IS_UNIQUE, 
                        MC.GROUP_CONFIG_FIELD_PATH, 
                        MCMAP.GROUP_CONFIG_PARAM_PATH, 
                        MCMAP.GROUP_CONFIG_LOOKUP_PATH, 
                        NULL AS PROFILE_PICTURE, 
                        NULL AS RENDER_THEME, 
                        M.BODY_ALIGN, 
                        NULL AS KPI_TYPE_ID, 
                        NULL AS IS_ADDON_PHOTO, 
                        NULL AS IS_ADDON_FILE, 
                        NULL AS IS_ADDON_COMMENT,                        
                        M.TAB_NAME, 
                        M.TAB_NAME_TOP, 
                        TO_CHAR(M.JSON_CONFIG) AS JSON_CONFIG, 
                        M.IS_NOT_TITLE, 
                        M.COLUMN_AGGREGATE, 
                        M.ROW_COUNT_LIMIT, 
                        M.DEFAULT_FILE, 
                        M.IS_EXCEL_IMPORT, 
                        M.IS_TRANSLATE, 
                        CASE WHEN M.ALL_CELL_EXPRESSION IS NOT NULL 
                        THEN 1 ELSE 0 END AS IS_FROM_MART_EXPRESSION, 
                        MW.CODE AS WIDGET_CODE, 
                        CASE WHEN 
                            M.SHOW_TYPE = 'rows' 
                        THEN ( 
                            SELECT 
                                COUNT(1) 
                            FROM KPI_INDICATOR_INDICATOR_MAP 
                            WHERE SEMANTIC_TYPE_ID = 42 
                                AND SRC_INDICATOR_MAP_ID = M.ID 
                                AND (LOOKUP_META_DATA_ID IS NOT NULL OR TRG_INDICATOR_ID IS NOT NULL) 
                        ) ELSE 0 END AS IS_ROWS_LOOKUP, 
                        M.REPLACE_PATH, 
                        CASE WHEN M.PARENT_ID IS NULL AND M.IS_RENDER = 1 AND M.SHOW_TYPE <> 'rows' AND M.SHOW_TYPE <> 'label'
                        THEN ( 
                            SELECT 
                                MAX(T0.COLUMN_NAME) 
                            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                            WHERE T0.MAIN_INDICATOR_ID = M.MAIN_INDICATOR_ID 
                                AND T0.SHOW_TYPE IN ('rows', 'label') 
                                AND T0.IS_RENDER = 1 
                                AND ".$this->db->IfNull('T0.IS_INPUT', '0')." = 1 
                                AND (
                                    T0.JSON_CONFIG LIKE '%\"'||M.COLUMN_NAME||'\"%' 
                                    OR T0.JSON_CONFIG LIKE '%\"'||M.COLUMN_NAME||',%' 
                                    OR T0.JSON_CONFIG LIKE '%,'||M.COLUMN_NAME||',%' 
                                    OR T0.JSON_CONFIG LIKE '%,'||M.COLUMN_NAME||'\"%'
                                ) 
                        ) ELSE NULL END AS INLINE_FIELDS 
                    FROM KPI_INDICATOR_INDICATOR_MAP M 
                        LEFT JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                        LEFT JOIN META_SEMANTIC_TYPE MST ON M.SEMANTIC_TYPE_ID = MST.ID 
                        LEFT JOIN (
                            SELECT 
                                LISTAGG(M1.COLUMN_NAME_PATH, ',') WITHIN GROUP (ORDER BY M.ID) AS CRITERIA_PATH,
                                M.ID
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                INNER JOIN KPI_INDICATOR_VALUE_CRITERIA C ON M.ID = C.INDICATOR_MAP_ID
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M1 ON C.RELATED_INDICATOR_MAP_ID = M1.ID
                            WHERE M.MAIN_INDICATOR_ID = :filterMainId 
                                AND ".$this->db->IfNull('C.IS_CRITERIA', '0')." = 1 
                            GROUP BY M.ID 
                        ) CP ON M.ID = CP.ID 
                        LEFT JOIN (
                            SELECT 
                                LISTAGG(M.COLUMN_NAME_PATH, ',') WITHIN GROUP (ORDER BY M1.ID) AS REVERSE_CRITERIA_PATH,
                                M1.ID
                            FROM KPI_INDICATOR_INDICATOR_MAP M
                                INNER JOIN KPI_INDICATOR_VALUE_CRITERIA C ON M.ID = C.INDICATOR_MAP_ID
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M1 ON C.RELATED_INDICATOR_MAP_ID = M1.ID
                            WHERE M1.MAIN_INDICATOR_ID = :filterMainId 
                                AND ".$this->db->IfNull('C.IS_CRITERIA', '0')." = 1 
                            GROUP BY M1.ID
                        ) RCP ON M.ID = RCP.ID 
                        LEFT JOIN META_FIELD_PATTERN MFP ON MFP.PATTERN_ID = M.PATTERN_ID  
                        LEFT JOIN ( 
                            SELECT 
                                DISTINCT
                                C.SRC_INDICATOR_MAP_ID AS ID, 
                                M1.COLUMN_NAME_PATH AS GROUP_CONFIG_FIELD_PATH 
                            FROM KPI_INDICATOR_MAP_CRITERIA C 
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M1 ON C.INDICATOR_MAP_ID = M1.ID 
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M2 ON C.SRC_INDICATOR_MAP_ID = M2.ID 
                            WHERE M2.MAIN_INDICATOR_ID = :filterMainId     
                        ) MC ON MC.ID = M.ID 
                        LEFT JOIN ( 
                            SELECT 
                                C.INDICATOR_MAP_ID AS ID, 
                                LISTAGG(M2.COLUMN_NAME_PATH, '|') WITHIN GROUP (ORDER BY C.ID) AS GROUP_CONFIG_PARAM_PATH, 
                                LISTAGG(M3.COLUMN_NAME_PATH, '|') WITHIN GROUP (ORDER BY C.ID) AS GROUP_CONFIG_LOOKUP_PATH 
                            FROM KPI_INDICATOR_MAP_CRITERIA C 
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M1 ON C.INDICATOR_MAP_ID = M1.ID 
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M2 ON C.SRC_INDICATOR_MAP_ID = M2.ID 
                                INNER JOIN KPI_INDICATOR_INDICATOR_MAP M3 ON C.TRG_INDICATOR_MAP_ID = M3.ID 
                            WHERE M1.MAIN_INDICATOR_ID = :filterMainId 
                            GROUP BY 
                                C.INDICATOR_MAP_ID 
                        ) MCMAP ON MCMAP.ID = M.ID 
                        LEFT JOIN META_WIDGET MW ON MW.ID = M.WIDGET_ID 
                    WHERE M.MAIN_INDICATOR_ID = :filterMainId  
                        AND ".$this->db->IfNull('M.IS_INPUT', '0')." = 1 
                        AND LOWER(M.COLUMN_NAME) <> 'id' 
                        $where  
                ) 
                ORDER BY ORDER_NUMBER ASC", $bindVars);
            
            $cache->set($cacheName, $data, Mdwebservice::$expressionCacheTime);
        }
        
        return $data;
    }
    
    public function getIndicatorRowsParamModel($indicatorId) {
        
        $langCode = Lang::getCode();
        
        $bindVars = array(
            'filterMainId' => $this->db->addQ($indicatorId)
        );
        
        $data = $this->db->GetAll("
            SELECT 
                M.ID, 
                M.CODE,
                FNC_TRANSLATE('$langCode', M.TRANSLATION_VALUE, 'LABEL_NAME', M.LABEL_NAME) AS NAME,
                M.MAIN_INDICATOR_ID, 
                M.DEFAULT_VALUE, 
                M.IS_REQUIRED, 
                M.BODY_ALIGN, 
                M.TAB_NAME, 
                M.TAB_NAME_TOP 
            FROM KPI_INDICATOR_INDICATOR_MAP M 
                INNER JOIN KPI_INDICATOR KI ON KI.PARENT_ID = M.MAIN_INDICATOR_ID 
            WHERE KI.ID = :filterMainId 
                AND ".$this->db->IfNull('M.IS_INPUT', '0')." = 1 
                AND ".$this->db->IfNull('M.IS_RENDER', '0')." = 1 
                AND M.SHOW_TYPE = 'rows' 
            ORDER BY M.ORDER_NUMBER ASC", $bindVars);
        
        return $data;
    }
    
    public function getKpiIndicatorDetailDataModel($indicatorId, $recordId, $idField = null, $parameters = array()) {
        
        try {
            
            $configRow = self::getKpiIndicatorRowModel($indicatorId);
            
            if (!$configRow) {
                throw new Exception('Тохиргоо олдсонгүй!'); 
            }
            
            $dataTableName = $configRow['TABLE_NAME'];
            $inputId = $idField ? strtoupper($idField) : 'ID';
            
            if ($inputId == 'IDFIELD') {
                
                $columnsData = self::getKpiIndicatorColumnsModel($indicatorId, $configRow); 
                $fieldConfig = self::getKpiIndicatorIdFieldModel($indicatorId, $columnsData);    
                
                $inputId = $fieldConfig['idField'];
            }
            
            if (!$dataTableName && $configRow['QUERY_STRING']) {
                
                $queryString = self::parseQueryString($configRow['QUERY_STRING']);
                
                $configRow['isFilter'] = true;
                $filterParams = self::getKpiIndicatorColumnsModel($indicatorId, $configRow);
                
                foreach ($filterParams as $filterParam) {
                    if ($filterParam['TRG_ALIAS_NAME'] != '') {
                        $queryString = str_ireplace(':'.$filterParam['TRG_ALIAS_NAME'], "''", $queryString);
                    }
                }
                
                $dataTableName = '('.$queryString.')';
            }
            
            Mdform::$kpiDmMart = self::getKpiDynamicDataRowModel($dataTableName, $inputId, $recordId, $parameters);
            
            self::$lookupDatas = array();
            
            if (Mdform::$kpiDmMart) {
                
                Mdform::$defaultTplSavedId = $idField ? Mdform::$kpiDmMart[$inputId] : ($recordId ? $recordId : Mdform::$kpiDmMart[$inputId]);
                
                $configData = self::getKpiIndicatorTemplateModel($indicatorId, null, true);
                
                if (!$configData) {
                    throw new Exception('Invalid config!'); 
                }

                $configFirstRow = $configData[0];
                $dataTableName  = $configFirstRow['TABLE_NAME'];
                
                foreach ($configData as $k => $row) {
            
                    if (!$row['PARENT_ID']) {

                        unset($configData[$k]);

                        $id = $row['ID'];

                        $this->getKpiIndicatorDetailHeaderDataModel($indicatorId, $configData, $id, $row);

                        break;
                    }
                }
            } else {
                throw new Exception('Invalid row data!'); 
            }
            
            unset(Mdform::$kpiDmMart['DATA']);
            
            if (self::$lookupDatas) {
                
                foreach (self::$lookupDatas as $key => $lookupRow) {
                    
                    if ($key == 'indicatorHeader') {
                        
                        foreach ($lookupRow as $lookupId => $row) {
                            
                            $rowIds = Arr::implode_key(',', $row, 'value', true);
                            $datas = self::getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $lookupId, 'TRG_TABLE_NAME' => '', 'rowIds' => $rowIds, 'isData' => true)); 
                            
                            $idField = $datas['id'];
                            $codeField = $datas['name'];
                            $nameField = $datas['name'];
                            
                            foreach ($row as $val) {
                                
                                foreach ($datas['data'] as $dataRow) {
                                    
                                    if ($val['value'] == $dataRow[$idField]) {
                                        Mdform::$kpiDmMart[$val['columnName']] = array(
                                            'id'      => $dataRow[$idField],
                                            'code'    => $dataRow[$codeField],
                                            'name'    => $dataRow[$nameField],
                                            'rowdata' => $dataRow
                                        );
                                        break;
                                    }
                                }
                            }
                        }
                        
                    } elseif ($key == 'metaHeader') {
                        
                        $this->load->model('mdwebservice', 'middleware/models/');
                        
                        foreach ($lookupRow as $lookupId => $row) {
                            
                            $lookupConfig = array(
                                'lookupMetaDataId'      => $lookupId, 
                                'lookupType'            => 'popup', 
                                'groupConfigLookupPath' => '', 
                                'groupConfigParamPath'  => ''
                            );
                            $lookupIdCodeNameRowData = $this->model->getLookupRowDatas($lookupConfig, array(), $row, 'value');
                            
                            foreach ($row as $val) {
                                if ($lookupIdCodeNameRowData && isset($lookupIdCodeNameRowData[$val['value']])) {
                                    Mdform::$kpiDmMart[$val['columnName']] = $lookupIdCodeNameRowData[$val['value']];
                                }
                            }
                        }
                        
                        $this->load->model('mdform', 'middleware/models/');
                    }
                }
            }
            
            $response = array('status' => 'success', 'detailData' => Mdform::$kpiDmMart);
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiIndicatorDetailHeaderDataModel($indicatorId, $configData, $parentId, $row) {
        
        foreach ($configData as $k => $arrRow) {
                    
            if ($arrRow['PARENT_ID'] == $parentId) {
                
                unset($configData[$k]);

                if ($arrRow['SHOW_TYPE'] != 'label' && $arrRow['SHOW_TYPE'] != 'rows') {
                    
                    $columnName        = $arrRow['COLUMN_NAME'];
                    $lookupIndicatorId = $arrRow['FILTER_INDICATOR_ID'];
                    $lookupMetaDataId  = $arrRow['LOOKUP_META_DATA_ID'];
                    $showType          = $arrRow['SHOW_TYPE'];
                    
                    $value = issetParam(Mdform::$kpiDmMart[$columnName]);
                    
                    Mdform::$kpiDmMart[$columnName] = $value;
                    
                    if ($arrRow['IS_RENDER'] == '1' && ($showType == 'combo' || $showType == 'popup')) {
                        
                        if ($lookupIndicatorId && $value != '') {
                            
                            self::$lookupDatas['indicatorHeader'][$lookupIndicatorId][] = array(
                                'columnName' => $columnName,
                                'value' => $value
                            );
                            
                        } elseif ($lookupMetaDataId && $value != '') {
                            
                            self::$lookupDatas['metaHeader'][$lookupMetaDataId][] = array(
                                'columnName' => $columnName,
                                'value' => $value
                            );
                        }
                    }

                } elseif ($arrRow['SHOW_TYPE'] == 'label') {

                    $this->getKpiIndicatorDetailHeaderDataModel($indicatorId, $configData, $arrRow['ID'], $arrRow);

                } elseif ($arrRow['SHOW_TYPE'] == 'rows') {
                    
                    if ($arrRow['FILTER_INDICATOR_ID'] && $arrRow['SEMANTIC_TYPE_NAME'] == 'Sub хүснэгт') {

                        $savedSubTableRows = self::getKpiSubTableRowsModel($indicatorId, $arrRow['FILTER_INDICATOR_ID'], Mdform::$defaultTplSavedId, $arrRow['COLUMN_NAME']);

                        if ($savedSubTableRows) {
                            Mdform::$kpiDmMart[$arrRow['COLUMN_NAME'] . '_subTableRows'] = $savedSubTableRows;
                        }
                    }
                    
                    $this->getKpiIndicatorDetailRowsDataModel($indicatorId, $configData, $arrRow['ID'], $arrRow);
                }
            }
        }
        
        return true;
    }
    
    public function getKpiIndicatorDetailRowsDataModel($indicatorId, $configData, $parentId, $row) {
        
        $arr = array_filter($configData, function($ar) use($parentId) {
            return ($ar['PARENT_ID'] == $parentId);
        });
        
        if ($arr) {
            
            $isSavedDataJson = $isTemplateRows = false;
            $savedDataJson = $mergeRows = array();
            $parentColumnName = $row['COLUMN_NAME'];
            
            if (Mdform::$defaultTplSavedId && Mdform::$kpiDmMart) {
                
                if ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                    
                } elseif ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName . '_subTableRows'])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                    
                    unset(Mdform::$kpiDmMart[$parentColumnName . '_subTableRows']);
                }   
            }
            
            if ($isSavedDataJson) {
                
                $lookupDatas = array();
                
                foreach ($arr as $k => $arrRow) {

                    if ($arrRow['IS_RENDER'] == '1') {

                        $lookupIndicatorId = $arrRow['FILTER_INDICATOR_ID'];
                        $lookupMetaDataId  = $arrRow['LOOKUP_META_DATA_ID'];
                        $showType          = $arrRow['SHOW_TYPE'];

                        if ($showType == 'combo' || $showType == 'popup') {
                            
                            $columnName = $arrRow['COLUMN_NAME'];
                            
                            if ($lookupIndicatorId) {
                                
                                $lookupDatas['indicatorDetail'][$lookupIndicatorId][] = $columnName;
                                
                            } elseif ($lookupMetaDataId) {
                                
                                $lookupDatas['metaDetail'][$lookupMetaDataId][] = $columnName;
                            }
                        }
                    }
                }
                
                if ($lookupDatas) {
                    
                    $indicatorLoop = $dataLoop = array();
                    
                    foreach ($lookupDatas as $lookupType => $lookupRow) {
                        
                        if ($lookupType == 'indicatorDetail') {
                            
                            foreach ($lookupRow as $lookupId => $lookupColumns) {
                                
                                $rowIds = '';
                                
                                foreach ($lookupColumns as $lookupColumn) {
                                    $rowIds .= Arr::implode_key(',', $savedDataJson, $lookupColumn, true) . ',';
                                    $indicatorLoop[$lookupColumn] = $lookupId;
                                }
                                
                                $rowIds = rtrim($rowIds, ',');
                                $datas = self::getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $lookupId, 'TRG_TABLE_NAME' => '', 'rowIds' => $rowIds)); 
                                
                                $idField = $datas['id'];
                                $codeField = $datas['name'];
                                $nameField = $datas['name'];
                                
                                foreach ($datas['data'] as $row) {
                                    $dataLoop[$lookupId][$row[$idField]] = array(
                                        'id'      => $row[$idField],
                                        'code'    => $row[$codeField],
                                        'name'    => $row[$nameField],
                                        'rowdata' => $row
                                    );
                                }
                            }
                            
                        } elseif ($lookupType == 'metaDetail') {
                            
                            $this->load->model('mdwebservice', 'middleware/models/');

                            foreach ($lookupRow as $lookupId => $lookupColumns) {
                                
                                $rowIds = array();
                                
                                foreach ($lookupColumns as $lookupColumn) {
                                    
                                    foreach ($savedDataJson as $savedRow) {
                                        if (isset($savedRow[$lookupColumn])) {
                                            $rowIds[]['value'] = $savedRow[$lookupColumn];
                                        }
                                    }
                                    
                                    $indicatorLoop[$lookupColumn] = $lookupId;
                                }
                                
                                $lookupConfig = array(
                                    'lookupMetaDataId'      => $lookupId, 
                                    'lookupType'            => 'popup', 
                                    'groupConfigLookupPath' => '', 
                                    'groupConfigParamPath'  => ''
                                );
                                $lookupIdCodeNameRowData = $this->model->getLookupRowDatas($lookupConfig, array(), $rowIds, 'value');
                                
                                $dataLoop[$lookupId] = $lookupIdCodeNameRowData;
                            }
                            
                            $this->load->model('mdform', 'middleware/models/');
                        }
                    }
                    
                    foreach ($savedDataJson as $k => $rowData) {
                        
                        if ($indicatorLoop) {
                            foreach ($indicatorLoop as $indicatorLoopCol => $indicatorLookupId) {
                                $savedId = issetParam($savedDataJson[$k][$indicatorLoopCol]);
                                if ($savedId && isset($dataLoop[$indicatorLookupId][$savedId])) {
                                    $savedDataJson[$k][$indicatorLoopCol] = $dataLoop[$indicatorLookupId][$savedId];
                                }
                            }
                        }
                    }
                }
                
                Mdform::$kpiDmMart[$parentColumnName] = $savedDataJson;
            }
        }
        
        return true;
    }
    
    public function renderKpiIndicatorTemplateModel($indicatorId, $dataTableName, $data) {
        
        $postData = Input::postData();
        self::$lookupDatas = array();
        
        if ($uxFlowIndicatorId = issetParam($postData['param']['uxFlowIndicatorId'])) {
            
            $structureIndicatorId = Input::param($postData['param']['structureIndicatorId']);
            $inputId = Mdform::$inputId ? Mdform::$inputId : 'ID';
            
            Mdform::$kpiDmMart = self::getKpiUxFlowDataRowModel($structureIndicatorId, $indicatorId, $postData, $inputId, Mdform::$defaultTplSavedId);

        } elseif ((Mdform::$defaultTplSavedId && $dataTableName) || (Mdform::$defaultTplSavedId && !$dataTableName && Mdform::$inputId == 'idField')) {
            
            $inputId = Mdform::$inputId ? Mdform::$inputId : 'ID';
            $getDetailData = self::getKpiIndicatorDetailDataModel($indicatorId, Mdform::$defaultTplSavedId, $inputId);
            
            Mdform::$kpiDmMart = issetParam($getDetailData['detailData']);
            
        } elseif (Mdform::$recordId && $dataTableName) { 
            
            Mdform::$kpiDmMart = self::getKpiDynamicDataRowModel($dataTableName, 'SRC_RECORD_ID', Mdform::$recordId);
            
        } elseif (Input::postCheck('transferSelectedRow')) {
            
            Mdform::$defaultTplSavedId = 1;
            Mdform::$kpiDmMart = Input::post('transferSelectedRow');
            
        } elseif (Input::postCheck('fillSelectedRow')) {
            
            Mdform::$defaultTplSavedId = 1;
            Mdform::$kpiDmMart = self::getDefaultFillDataModel($indicatorId);
            
            if ($rowId = issetParam(Mdform::$kpiDmMart['ID'])) {
                Mdform::$firstTplId = $rowId;
            }
            
        } elseif (Input::postCheck('fillDynamicSelectedRow')) {
            
            Mdform::$defaultTplSavedId = 1;
            Mdform::$kpiDmMart = self::getDynamictFillDataModel($indicatorId);
            
        } else {
            Mdform::$defaultTplSavedId = 1;
            Mdform::$kpiDmMart = self::getDefaultFillDataModel($indicatorId);
        }
                
        $render = array();
        
        foreach ($data as $k => $row) {
            
            if (!$row['PARENT_ID']) {
                
                unset($data[$k]);
                
                $id = $row['ID'];
                
                $render[] = self::childKpiIndicatorTemplate($indicatorId, $data, $id, $row);
                
                break;
            }
        }
        
        if (Mdform::$tabRender) {
            
            $t = 1;
            $tabItemArr = $tabContentArr = array();
            
            foreach (Mdform::$tabRender as $tabName => $tabContent) {
                
                $tabItemArr[] = '<li class="nav-item">';
                    $tabItemArr[] = '<a href="#tab_'.Mdform::$subUniqId.'_'.$t.'" class="nav-link'.($t == 1 ? ' active' : '').'" data-toggle="tab" aria-expanded="false">'.$tabName.'</a>';
                $tabItemArr[] = '</li>';
                
                $tabContentArr[] = '<div class="tab-pane'.($t == 1 ? ' active' : '').'" id="tab_'.Mdform::$subUniqId.'_'.$t.'">';
                    $tabContentArr[] = implode('', $tabContent);
                $tabContentArr[] = '</div>';
                    
                $t ++;
            }
            
            $render[] = '<div class="bp-tabs tabbable-line mt-3">';
                $render[] = '<ul class="nav nav-tabs">';
                    $render[] = implode('', $tabItemArr);
                $render[] = '</ul>';
                $render[] = '<div class="tab-content">';
                    $render[] = implode('', $tabContentArr);
                $render[] = '</div>';
            $render[] = '</div>';
        }
        
        if (Mdform::$topTabRender) {
            
            $t = 1;
            $topTab = $topTabTmp = array();
            
            foreach (Mdform::$topTabRender as $topTabName => $topTabContent) {
                
                if (strpos($topTabName, 'sysTopTabName') !== false) {
                    
                    $topTabName = str_replace('sysTopTabName', '', $topTabName);
                    
                    $tabItemArr = $tabContentArr = $renderTopTab = array();
                    $activeIndex = 1;
                    
                    foreach ($topTabContent as $tabName => $tabContent) {
                
                        $tabItemArr[] = '<li class="nav-item">';
                            $tabItemArr[] = '<a href="#topChildTab_'.Mdform::$subUniqId.'_'.$t.'" class="nav-link'.($activeIndex == 1 ? ' active' : '').'" data-toggle="tab" aria-expanded="false">'.$tabName.'</a>';
                        $tabItemArr[] = '</li>';

                        $tabContentArr[] = '<div class="tab-pane'.($activeIndex == 1 ? ' active' : '').'" id="topChildTab_'.Mdform::$subUniqId.'_'.$t.'">';
                            $tabContentArr[] = implode('', $tabContent);
                        $tabContentArr[] = '</div>';

                        $t ++;
                        $activeIndex ++;
                    }
                    
                    $renderTopTab[] = '<div class="bp-tabs tabbable-line mt-3">';
                        $renderTopTab[] = '<ul class="nav nav-tabs">';
                            $renderTopTab[] = implode('', $tabItemArr);
                        $renderTopTab[] = '</ul>';
                        $renderTopTab[] = '<div class="tab-content">';
                            $renderTopTab[] = implode('', $tabContentArr);
                        $renderTopTab[] = '</div>';
                    $renderTopTab[] = '</div>';
                    
                    $topTabTmp[$topTabName][] = implode('', $renderTopTab);
                    
                } else {
                    $topTab[$topTabName][] = implode('', $topTabContent);
                }
            }
            
            if ($topTabTmp) {
                foreach ($topTabTmp as $tmpTabName => $tmpTabContent) {
                    $topTab[$tmpTabName][] = implode('', $tmpTabContent);
                }
            }
            
            Mdform::$topTabRenderShow = $topTab;
        }
        
        return implode('', $render);
    }
    
    public function parseExpressionFunctionNames($value) {
        
        $isEval = false;
        
        if (stripos($value, 'getProcessParam(') !== false) {
            preg_match_all('/getProcessParam\((.*?)\)/i', $value, $callProcess);
            
            if (count($callProcess[0]) > 0) {
                foreach ($callProcess[1] as $ek => $ev) {

                    $evArr = explode(',', $ev);
                    $processCode = trim(str_replace("'", '', $evArr[0]));
                    
                    $processCriteria = trim(str_replace("'", '', $evArr[1]));
                    $processOutputParam = trim(str_replace("'", '', $evArr[2]));
                    
                    $execValue = self::getProcessParamByCellExp($processCode, $processCriteria, $processOutputParam);
                    
                    $value = str_replace($callProcess[0][$ek], $execValue, $value);
                }
                
                $isEval = true;
            }   
        }
        
        if ($isEval) {
            $value = @eval('return ' . $value . ';');
        }
        
        return $value;
    }
    
    public function getProcessParamByCellExp($processCode, $processCriteria, $processOutputParam) {
        
        $processOutputParam = strtolower($processOutputParam);
        $param = array();

        if ($processCriteria) {

            $processCriteriaArr = explode('|', $processCriteria);

            if (count($processCriteriaArr) > 0) {

                foreach ($processCriteriaArr as $key => $val) {
                    $valArr = explode('@', $val);

                    $paramVal = $valArr[0];

                    if (strpos($paramVal, '.') !== false) {

                        $paramValArr = explode('.', $paramVal);
                        $parentKey = $paramValArr[0];
                        $columnKey = $paramValArr[1];

                        $paramVal = issetParam(Mdform::$indicatorTemplateRow[$parentKey][$columnKey]);

                    } else {
                        $paramVal = Mdmetadata::setDefaultValue($paramVal);
                    }

                    $paramKey = $valArr[1];

                    $param[$paramKey] = $paramVal;
                }
            }
        }
            
        $processKey = $processCode . '-' . json_encode($param);
        
        if (isset(self::$getProcessResponses[$processKey])) {
            
            if (isset(self::$getProcessResponses[$processKey]['result'][$processOutputParam])) {
                return self::$getProcessResponses[$processKey]['result'][$processOutputParam];
            }
        
        } else {

            $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, $processCode, $param);

            self::$getProcessResponses[$processKey] = $result;

            if (isset(self::$getProcessResponses[$processKey]['result'][$processOutputParam])) {
                return self::$getProcessResponses[$processKey]['result'][$processOutputParam];
            }
        }
        
        return '';
    }
    
    public function kpiIndicatorControl($row, $templateRow = array()) {
        
        Mdform::$kpiIndicatorRowData = $templateRow;
        
        $control           = null;
        $showType          = $row['SHOW_TYPE'];
        $mainIndicatorId   = $row['MAIN_INDICATOR_ID'];
        $lookupMetaDataId  = $row['LOOKUP_META_DATA_ID'];
        $filterIndicatorId = $row['FILTER_INDICATOR_ID'];
        $labelName         = Lang::line($row['NAME']);
        $placeholder       = $labelName;
        $lookupCriteria    = $row['LOOKUP_CRITERIA'];
        $columnName        = $row['COLUMN_NAME'];
        $columnNamePath    = $row['COLUMN_NAME_PATH'];
        $code              = $columnName;
        $defaultValue      = $row['DEFAULT_VALUE'];
        $isRequired        = $row['IS_REQUIRED'];
        $value             = (is_array($templateRow)) ? issetParam($templateRow[$columnName]) : '';
        $controlName       = 'kpiTbl'.Mdform::$addonPathPrefix.Mdform::$pathPrefix.'['.$columnName.']'.Mdform::$pathSuffix;
        $cellId            = '';
        $addAttrs          = array();

        if (!isset($row['ignoreSavedDataRow']) 
            && (Mdform::$recordId || Mdform::$defaultTplSavedId || Mdform::$fillFromExpression) 
            && Mdform::$kpiDmMart && !$value) {
            
            $value = issetParam(Mdform::$kpiDmMart[$columnName]);
        }
        
        if ($value == '') {
            $value = Mdmetadata::setDefaultValue($defaultValue);
        }
        
        if ($cellJson = issetParam($row['cellJson'])) {
            
            $cellJson = json_decode(html_entity_decode($cellJson), true);
            
            if (is_array($cellJson)) {
                
                $cellId = $cellJson['cellId'];
            
                $cellValidation = issetParam($cellJson['validation']);
                $cellExpression = issetParam($cellJson['expression']);
                $cellDefaultValue = issetParam($cellJson['defaultvalue']);
                $cellComment = issetParam($cellJson['comment']);
                $cellStyle = issetParam($cellJson['style']);
                
                if (isset($row['parentColumnName'])) {
                    
                    $cellId = $row['parentColumnName'] . '.' . $cellId;
                    
                    $cellJson['cellId'] = $cellId;
                    $cellJson['parentColumnName'] = $row['parentColumnName'];
                }

                if ($cellValidation || $cellExpression) {
                    
                    Mdform::$indicatorCellExpression[$cellId] = $cellJson;
                    
                    if ($cellExpression) {
                        $addAttrs['readonly'] = 'readonly';
                    }   
                }
                
                if (!$value && $cellDefaultValue) {
                    $value = self::parseExpressionFunctionNames($cellDefaultValue);
                    $addAttrs['data-auto-change'] = '1';
                }
                
                if ($cellComment) {
                    $addAttrs['title'] = $cellComment;
                    $addAttrs['data-comment-style'] = 1;
                }
                
                if ($cellStyle) {
                    Mdform::$indicatorCellStyle = $cellStyle;
                }
            }
        }
        
        if (isset($row['columnReadonly'])) {
            $addAttrs['readonly'] = 'readonly';
        }
        
        if (Mdform::$isControlViewLabel) {
            
            $control = html_tag('span', array('class' => 'font-weight-bold pl8'), $value);
            
            return $control;
        }
        
        if ($isRequired == '1') {
            $addAttrs['required'] = 'required';
        }
        
        if ($row['PLACEHOLDER_NAME'] != '') {
            $addAttrs['placeholder'] = Lang::line($row['PLACEHOLDER_NAME']);
            $placeholder = $addAttrs['placeholder'];
        }

        if ($row['PATTERN_TEXT'] != '') {

            $addAttrs['data-regex'] = $row['PATTERN_TEXT'];
            $addAttrs['data-regex-message'] = Lang::line($row['GLOBE_MESSAGE']);

            if ($row['IS_MASK'] === '1') {
                $addAttrs['data-inputmask-regex'] = $row['PATTERN_TEXT'];
            }
        }
        
        $addAttrs['data-isclear'] = '1';
        
        if ($showType == 'string' || $showType == 'text' || $showType == 'description' || $showType == 'code') {
            
            if ($row['MIN_VALUE'] != '') {
                $addAttrs['minlength'] = $row['MIN_VALUE'];
            }

            if ($row['MAX_VALUE'] != '') {
                $addAttrs['maxlength'] = $row['MAX_VALUE'];
                $addAttrs['data-maxlength'] = 'true';
            }
            
        } elseif ($showType == 'percent' || $showType == 'decimal' || $showType == 'bigdecimal' || $showType == 'number') {

            if ($row['MIN_VALUE'] != '') {
                $addAttrs['data-v-min'] = $row['MIN_VALUE'];
            }
            
            if ($row['MAX_VALUE'] != '') {
                $addAttrs['data-v-max'] = $row['MAX_VALUE'];
            }
        }
        
        switch ($showType) { 
        
            case 'percent':
            {
                $inputControl = Form::text(
                    array(
                        'name' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm amountInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-field-name' => $cellId, 
                        'autocomplete' => 'off'
                    ) + $addAttrs 
                );
                
                $control = '<div class="input-group" style="max-width: 170px">
                    '.$inputControl.'
                    <span class="input-group-append"><button type="button" class="btn btn-light pl8 pr8 border-none" style="cursor:default">%</button></span> 
                </div>';
            }   
            break;
        
            case 'decimal':
            case 'decimal_zero':
            case 'bigdecimal':
            {
                $attrArray = array(
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm kpiDecimalInit bigdecimalInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'data-field-name' => $cellId, 
                    'autocomplete' => 'off', 
                    'inputmode' => 'numeric'
                ) + $addAttrs;
                
                $attrArrayDecimal = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath . '_bigdecimal',
                    'value' => str_replace(',', '', $value)
                );
                
                $control = Form::text($attrArray) . Form::hidden($attrArrayDecimal);
            }   
            break;
        
            case 'number':
            case 'long':
            case 'integer':
            {
                $control = Form::text(
                    array(
                        'name' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm integerInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-field-name' => $cellId, 
                        'autocomplete' => 'off'
                    ) + $addAttrs 
                );
            }   
            break;
        
            case 'check':
            case 'boolean':
            {
                if (isset($templateRow['rowNum']) && $templateRow['rowNum'] !== '') {
                    
                    $controlName = 'kpiTbl'.Mdform::$addonPathPrefix.Mdform::$pathPrefix.'['.$columnName.']['.$templateRow['rowNum'].']';
                }
                
                $control = Form::checkbox(
                    array(
                        'name' => $controlName, 
                        'id' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm booleanInit', 
                        'placeholder' => $placeholder, 
                        'data-field-name' => $cellId, 
                        'value' => '1', 
                        'saved_val' => $value 
                    ) + $addAttrs 
                );
            }
            break;
        
            case 'date':
            {
                if ($value) {
                    $value = Date::formatter($value, 'Y-m-d');
                }
                
                $control = Form::text(
                    array(
                        'name' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm dateInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-field-name' => $cellId
                    ) + $addAttrs 
                );
                
                return html_tag('div', array(
                        'class' => 'dateElement input-group',
                        'style' => 'max-width: 131px;'
                    ), $control . '<span class="input-group-btn"><button tabindex="-1" onclick="return false;" class="btn"><i class="fal fa-calendar"></i></button></span>', true
                );
            }
            break;
            
            case 'datetime':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm datetimeInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value
                ) + $addAttrs;
                
                $control = Form::text($attrArray);
            }
            break;
        
            case 'time':
            {
                if ($value) {
                    $value = Date::formatter($value, 'H:i');
                }
                
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm timeInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'style' => 'width: 60px;' 
                ) + $addAttrs;
                
                $control = Form::text($attrArray);
            }
            break;
            
            case 'combo':
            case 'multicombo':
            {
                $lookupMetaDataId = issetDefaultVal($row['META_LOOKUP_ID'], $row['LOOKUP_META_DATA_ID']);
                
                if ($lookupMetaDataId || $filterIndicatorId) {
                    
                    if ($row['IS_SELECT_QUERY'] == '1' 
                        || $row['GROUP_CONFIG_PARAM_PATH'] != '' 
                        || $row['GROUP_CONFIG_FIELD_PATH'] != '') {
                        
                        $attrArray = array(
                            'name' => $controlName, 
                            'data-path' => $columnNamePath, 
                            'data-col-path' => $code, 
                            'class' => 'form-control input-sm dropdownInput select2 data-combo-set kpi-ind-combo mv-ind-combo', 
                            'data-field-name' => $cellId, 
                            'isReturnArray' => true
                        ) + $addAttrs;
                        
                        $row['isData'] = true;
                        
                        if (isset($value['id'])) {
                            $value = $value['id'];
                        }
                        
                        if ($row['GROUP_CONFIG_PARAM_PATH'] != '') {
                            
                            $attrArray['disabled'] = 'disabled';
                            $attrArray['data-in-param'] = $row['GROUP_CONFIG_PARAM_PATH'];
                            $attrArray['data-in-lookup-param'] = $row['GROUP_CONFIG_LOOKUP_PATH'];
                            
                            if ($value == '') {
                                $row['isData'] = false;
                            }
                        }
                        
                        if ($row['GROUP_CONFIG_FIELD_PATH'] != '') {
                            
                            $attrArray['class'] = $attrArray['class'] . ' linked-combo';
                            $attrArray['data-out-param'] = $row['GROUP_CONFIG_FIELD_PATH'];
                        }
                        
                        $row['value'] = $value;
                        
                        $datas = self::getKpiComboDataModel($row); 
                        
                        if ((isset($attrArray['disabled']) && $datas['data']) || issetParam($datas['isIgnoreDisabled'])) {
                            unset($attrArray['disabled']);
                        }
                        
                        $attrArray['data'] = $datas['data'];
                        $attrArray['op_value'] = $datas['id'];
                        $attrArray['op_text'] = $datas['name'];
                        $attrArray['value'] = $value;
                        
                        $arr = array(
                            'META_DATA_ID' => $filterIndicatorId,
                            'ATTRIBUTE_ID_COLUMN' => $datas['id'],
                            'ATTRIBUTE_CODE_COLUMN' => $datas['name'],
                            'ATTRIBUTE_NAME_COLUMN' => $datas['name'],
                            'PARAM_REAL_PATH' => $columnNamePath,
                            'PROCESS_META_DATA_ID' => $mainIndicatorId,
                            'CHOOSE_TYPE' => 'single'
                        );
                        
                        $attrArray['data-row-data'] = htmlentities(json_encode($arr), ENT_QUOTES, 'UTF-8');
                        
                    } else {
                        
                        if ($filterIndicatorId) {
                            $lookupCriteria = $lookupCriteria . '&indicatorId=' . $filterIndicatorId;
                            $lookupMetaDataId = '1642414747737029';
                        } 
                        
                        if ($row['CRITERIA_PATH']) {
                            
                            $criteriaPath = $row['CRITERIA_PATH'];
                            $addAttrs['disabled'] = 'disabled';
                            $addAttrs['data-in-param'] = $criteriaPath;
                            
                            $datas = array('data' => array(), 'id' => '', 'code' => '', 'name' => '');
                            
                            if (Mdform::$kpiDmMart) {
                                
                                $isEmpty = false;
                                $criteriaPathArr = explode(',', $criteriaPath);
                                
                                foreach ($criteriaPathArr as $c => $criteriaPath) {
                                    
                                    if (!isset(Mdform::$kpiDmMart[$criteriaPath]) || (isset(Mdform::$kpiDmMart[$criteriaPath]) && Mdform::$kpiDmMart[$criteriaPath] == '')) {
                                        
                                        $isEmpty = true;
                                        
                                    } elseif (isset(Mdform::$kpiDmMart[$criteriaPath]) && Mdform::$kpiDmMart[$criteriaPath] != '') {
                                        
                                        $lookupCriteria .= '&criteria' . ($c + 1) . '=' . Mdform::$kpiDmMart[$criteriaPath];
                                    }
                                }
                                
                                if ($isEmpty == false) {
                                    unset($addAttrs['disabled']);
                                    
                                    $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                                }
                            }
                            
                        } else {
                            $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                        }
                        
                        if ($row['REVERSE_CRITERIA_PATH']) {
                            $addAttrs['data-out-param'] = $row['REVERSE_CRITERIA_PATH'];
                        }
                        
                        if (isset($value['id'])) {
                            $value = $value['id'];
                        }

                        $attrArray = array(
                            'name' => $controlName, 
                            'data-path' => $columnNamePath, 
                            'data-metadataid' => $lookupMetaDataId, 
                            'data-live-search' => $lookupCriteria, 
                            'data-col-path' => $code, 
                            'class' => 'form-control input-sm dropdownInput select2 data-combo-set kpi-ind-combo', 
                            'data' => $datas['data'], 
                            'op_value' => $datas['id'], 
                            'op_text' => $datas['name'], 
                            'op_param' => $datas['code'], 
                            'value' => $value, 
                            'data-field-name' => $cellId, 
                            'data-row-data' => htmlentities('{"isIndicator": true}', ENT_QUOTES, 'UTF-8'), 
                            'isReturnArray' => true
                        ) + $addAttrs;

                        if (isset($datas['data-name'])) {
                            $attrArray['data-name'] = $datas['data-name'];
                        }
                    }
                    
                    $attrArray['op_custom_attr'] = array(array('key' => 'rowData', 'attr' => 'data-row-data'));
                    
                    if ($showType == 'multicombo') {
                        
                        $attrArray['multiple'] = 'multiple';
                        $attrArray['name'] = $attrArray['name'] . '[]';
                        
                        $selectControl = Form::multiselect($attrArray); 
                    } else {
                        $selectControl = Form::select($attrArray); 
                    }

                    $control = $selectControl['control']; 
                    
                    $controlName = str_replace('['.$columnName.']', '['.$columnName.'_DESC]', $controlName);
                    $control .= Form::hidden(array('name' => $controlName, 'value' => $selectControl['op_text']));
                    
                } else {
                    
                    $control = Form::text(
                        array(
                            'name' => $controlName, 
                            'data-path' => $columnNamePath, 
                            'data-col-path' => $code, 
                            'class' => 'form-control input-sm stringInit', 
                            'placeholder' => $placeholder, 
                            'value' => $value, 
                            'data-field-name' => $cellId
                        ) + $addAttrs 
                    );
                }
            }
            break;
            
            case 'popup':   
            {    
                $lookupMetaDataId = issetDefaultVal($row['META_LOOKUP_ID'], $row['LOOKUP_META_DATA_ID']);
                
                if (!is_null($lookupMetaDataId)) {
                    
                    $lowerPath = strtolower($columnName);
                    $controlConfig = array('GROUP_PARAM_CONFIG_TOTAL' => '0', 'GROUP_CONFIG_PARAM_PATH' => NULL, 'GROUP_CONFIG_LOOKUP_PATH' => NULL, 'GROUP_CONFIG_PARAM_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH_GROUP' => NULL, 'GROUP_CONFIG_FIELD_PATH' => NULL, 'GROUP_CONFIG_GROUP_PATH' => NULL, 'IS_MULTI_ADD_ROW' => '0', 'IS_MULTI_ADD_ROW_KEY' => '0', 'META_DATA_CODE' => $columnName, 'LOWER_PARAM_NAME' => $lowerPath, 'META_DATA_NAME' => $labelName, 'DESCRIPTION' => NULL, 'ATTRIBUTE_ID_COLUMN' => NULL, 'ATTRIBUTE_CODE_COLUMN' => NULL, 'ATTRIBUTE_NAME_COLUMN' => NULL, 'IS_SHOW' => '1', 'IS_REQUIRED' => '0', 'DEFAULT_VALUE' => NULL, 'RECORD_TYPE' => NULL, 'LOOKUP_META_DATA_ID' => $lookupMetaDataId, 'LOOKUP_TYPE' => 'popup', 'CHOOSE_TYPE' => 'single', 'DISPLAY_FIELD' => null, 'VALUE_FIELD' => null, 'PARAM_REAL_PATH' => $columnNamePath, 'META_TYPE_CODE' => 'long', 'TAB_NAME' => NULL, 'SIDEBAR_NAME' => NULL, 'FEATURE_NUM' => NULL, 'IS_SAVE' => NULL, 'FILE_EXTENSION' => NULL, 'PATTERN_TEXT' => NULL, 'PATTERN_NAME' => NULL, 'GLOBE_MESSAGE' => NULL, 'IS_MASK' => NULL, 'COLUMN_WIDTH' => NULL, 'COLUMN_AGGREGATE' => NULL, 'SEPARATOR_TYPE' => NULL, 'GROUP_LOOKUP_META_DATA_ID' => NULL, 'IS_BUTTON' => '1', 'COLUMN_COUNT' => NULL, 'MAX_VALUE' => NULL, 'MIN_VALUE' => NULL, 'IS_SHOW_ADD' => NULL, 'IS_SHOW_DELETE' => NULL, 'IS_SHOW_MULTIPLE' => NULL, 'LOOKUP_KEY_META_DATA_ID' => NULL, 'IS_REFRESH' => '0', 'FRACTION_RANGE' => NULL, 'GROUPING_NAME' => NULL, 'PLACEHOLDER_NAME' => null);
                    $rowData = null;
                    
                    if ($value) {
                        $rowData = array($lowerPath => $value);
                    }
                    
                    $control = Mdwebservice::renderParamControl('1479459173907', $controlConfig, $controlName, $columnName, $rowData);
                    
                    $name = str_replace('['.$columnName.']', '['.$columnName.'_DESC]', $controlName);
                    $control = str_replace('name="'.$columnName.'_nameField"', 'name="'.$name.'"', $control);
                    $control = str_replace('type="hidden"', 'type="hidden" data-col-path="'.$code.'"', $control);
                    
                } else {
                    
                    $selectedValue = $selectedCode = $selectedName = $lookupRowData = '';
                    
                    if ($value) {
                        $fillParamValue = Mdwebservice::setLookupValFillData(array($columnName => $value), $columnName);
                        if (is_array($fillParamValue)) {
                            $selectedValue = $fillParamValue['META_VALUE_ID'];
                            $selectedCode = $fillParamValue['META_VALUE_CODE'];
                            $selectedName = $fillParamValue['META_VALUE_NAME'];
                            $lookupRowData = $fillParamValue['rowData'];
                        } else {
                            $selectedValue = $fillParamValue;
                        }
                    }
                    
                    $hiddenAttr = array(
                        'name' => $controlName,
                        'id' => $columnName . '_valueField',
                        'data-path' => $columnNamePath,
                        'value' => $selectedValue,
                        'class' => 'popupInit',
                        'data-row-data' => $lookupRowData
                    );
                    
                    $attrArrayButton = array(
                        'class' => 'btn default btn-bordered btn-xs mr-0',
                        'value' => '<i class="far fa-search"></i>', 
                        'onclick' => 'chooseKpiIndicatorRowsFromBasket(this, \''.$filterIndicatorId.'\', \'single\', \'mvFieldFillSelectedRows\');', 
                        'tabindex' => '-1'
                    );
                    
                    $controlHidden = Form::hidden($hiddenAttr);
                    
                    $attrArray['name'] = $columnName . '_displayField';
                    $attrArray['id'] = $controlName . '_displayField';
                    $attrArray['class'] = 'form-control form-control-sm meta-autocomplete lookup-code-autocomplete';
                    $attrArray['data-processid'] = $mainIndicatorId;
                    $attrArray['data-lookupid'] = $filterIndicatorId;
                    $attrArray['placeholder'] = Lang::line('code_search');
                    $attrArray['value'] = $selectedCode;
                    $attrArray['title'] = $selectedCode;
                    
                    $controlCodeInput = Form::text($attrArray);
                    
                    $attrArray['name'] = str_replace('['.$columnName.']', '['.$columnName.'_DESC]', $controlName);
                    $attrArray['id'] = $controlName . '_nameField';
                    $attrArray['class'] = 'form-control form-control-sm meta-name-autocomplete lookup-name-autocomplete';
                    $attrArray['data-processid'] = $mainIndicatorId;
                    $attrArray['data-lookupid'] = $filterIndicatorId;
                    $attrArray['placeholder'] = Lang::line('name_search');
                    $attrArray['value'] = $selectedName;
                    $attrArray['title'] = $selectedName;

                    $controlNameInput = Form::text($attrArray);
                    $controlButton = Form::button($attrArrayButton);
                    
                    $control = '<div class="meta-autocomplete-wrap mv-popup-control" data-section-path="' . $columnNamePath . '">
                        <div class="input-group double-between-input">
                            ' . $controlHidden . '
                            ' . html_entity_decode($controlCodeInput, ENT_QUOTES, 'UTF-8') . '
                            <span class="input-group-btn">
                                ' . $controlButton . '
                            </span> 
                            <span class="input-group-btn">
                                ' . html_entity_decode($controlNameInput, ENT_QUOTES, 'UTF-8') . '      
                            </span> 
                        </div>
                    </div>';
                }
            }    
            break;
            
            case 'radio':
            {    
                if ($row['TRG_TABLE_NAME']) {

                    $row['isData'] = true;
                    $row['value'] = $value;

                    $datas = self::getKpiComboDataModel($row); 
                    
                    $rows = $datas['data'];
                    $id = $datas['id'];
                    $name = $datas['name'];

                    $array = array();

                    foreach ($rows as $row) {
                        $array[] = array(
                            'name' => $controlName, 
                            'value' => $row[$id], 
                            'label' => $row[$name], 
                            'labelclass' => 'radio-inline'
                        );
                    }
                    
                    $radioControlArr = Form::radioMulti($array, $value, true);
                    
                    $radioControl = str_replace(
                        'name="'.$controlName.'"', 
                        'name="'.$controlName.'" data-path="'.$columnNamePath.'" data-col-path="'.$code.'" data-field-name="'.$cellId.'" class="md-radio"', 
                        $radioControlArr['control']
                    );
                    
                    $controlName = str_replace('['.$columnName.']', '['.$columnName.'_DESC]', $controlName);
                    $hiddenControl = Form::hidden(array('name' => $controlName, 'value' => $radioControlArr['op_text']));
                    
                    $control = '<div class="radio-list radioInit" data-path="'.$columnNamePath.'">'.$radioControl.$hiddenControl.'</div>';
                        
                } else {
                    
                    $attrArray = array(
                        'name' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm stringInit', 
                        'autocomplete' => 'off', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-field-name' => $cellId
                    ) + $addAttrs;

                    $control = Form::text($attrArray);
                }
            }
            break;
            
            case 'clob':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control form-control-lg clobInit', 
                    'autocomplete' => 'off', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'data-field-name' => $cellId, 
                    'style' => 'overflow: hidden;',
                    'rows' => 4
                ) + $addAttrs;
                
                $control = Form::textArea($attrArray);
            }   
            break;
        
            case 'file':
            {
                $noFileSelected = 'No file selected';
                $fileNameTag = $noFileSelected;
                $fileView = '';
                
                $attrArray = array(
                    'name' => 'kpiFile'.Mdform::$addonPathPrefix.Mdform::$pathPrefix.'['.$columnName.']'.Mdform::$pathSuffix,
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control form-control-uniform fileInit', 
                    'autocomplete' => 'off', 
                    'placeholder' => $placeholder, 
                    'data-field-name' => $cellId, 
                ) + $addAttrs;
                
                $hiddenAttrArray = array(
                    'name' => $controlName, 
                    'value' => $value 
                );
                
                $fileHidden = Form::hidden($hiddenAttrArray);
                
                if ($value && strpos($value, '.') !== false) {

                    $fileName = $value;

                    if (isset($attrArray['required'])) {
                        unset($attrArray['required']);
                    }

                    $href = 'mdobject/downloadFile?fDownload=1&file=' . $fileName;
                    $realFileName = '';

                    if ($realFileName) {
                        $href .= '&fileName=' . $realFileName;
                        $fileNameTag = $realFileName;
                    } else {
                        $fileNameTag = basename($fileName);
                    }

                    $attrArray['title'] = $fileNameTag;

                    $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));

                    if ($fileExtension == 'pdf') {
                        $fileIcon = 'icon-file-pdf';
                    } elseif ($fileExtension == 'doc' || $fileExtension == 'docx') {
                        $fileIcon = 'icon-file-word';
                    } elseif ($fileExtension == 'xls' || $fileExtension == 'xlsx') {
                        $fileIcon = 'icon-file-excel';
                    } elseif ($fileExtension == 'png' || $fileExtension == 'jpg' || $fileExtension == 'jpeg' 
                        || $fileExtension == 'gif' || $fileExtension == 'bmp' || $fileExtension == 'webp') {
                        $fileIcon = 'icon-file-picture';
                    } elseif ($fileExtension == 'zip' || $fileExtension == 'rar') {
                        $fileIcon = 'icon-file-zip';
                    } else {
                        $fileIcon = 'icon-file-text2';
                    }

                    $fileView = html_tag('a', array('href'=>$href,'title'=>Lang::line('download_btn'),'class'=>'btn btn-sm btn-light rounded-0'), '<i class="icon-download"></i>');
                    $fileView .= html_tag('a', array('href'=>'javascript:;','title'=>Lang::line('see_btn'),'data-url'=>$fileName, 'data-extension'=>$fileExtension, 'onclick'=>'bpFilePreview(this);', 'class'=>'btn btn-sm btn-light rounded-0'), '<i class="'.$fileIcon.'"></i>');
                    $fileView .= html_tag('a', array('href'=>'javascript:;','title'=>Lang::line('delete_btn'),'onclick'=>'bpFileChoosedRemove(this);', 'class'=>'btn btn-sm btn-light rounded-0'), '<i class="icon-trash-alt"></i>');
                }
                
                $control = '<div class="uniform-uploader" data-section-path="' . $columnName . '">
                        '.Form::file($attrArray).$fileHidden.'
                        <span class="filename" data-text="'.$noFileSelected.'" title="'.$fileNameTag.'">'.$fileNameTag.'</span>
                        '.$fileView.'
                        <button type="button" class="action btn btn-sm btn-light bp-file-choose-btn" onclick="bpFileChoose(this);">'.Lang::line('select_file_btn').'</button>
                    </div>';
            }   
            break;
            
            case 'coordinate':
            case 'coordinate_auto':
            case 'polyline':
            case 'polyline_connection':
            {   
                $control = Form::text(
                    array(
                        'name' => $controlName, 
                        'data-path' => $columnNamePath, 
                        'data-col-path' => $code, 
                        'class' => 'form-control input-sm coordinateInit', 
                        'placeholder' => $placeholder, 
                        'value' => $value, 
                        'data-field-name' => $cellId
                    ) + $addAttrs 
                );

                $functionName = ($showType == 'polyline_connection' ? 'setGoogleMapRegion(undefined, this)' : 'setGMapCoordinate(this)');
                $className = ($showType == 'polyline_connection' ? 'gmap-set-region-control' : 'gmap-set-coordinate-control');
                
                $control = '<div class="input-group '. $className .'">
                        '.$control.'
                        <span class="input-group-append"><button type="button" class="btn btn-primary" onclick="'. $functionName .'" data-fix-coordinate-pos="1"><i class="far fa-map-marker"></i></button></span> 
                    </div>';
            }
            break;
            
            case 'description': 
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'data-field-name' => $cellId, 
                    'class' => 'form-control input-sm descriptionInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'spellcheck' => 'false', 
                    'style' => 'height: 39px;'
                ) + $addAttrs;

                $control = Form::textArea($attrArray);
            }
            break;
        
            case 'description_auto': 
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'data-field-name' => $cellId, 
                    'class' => 'form-control input-sm description_autoInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'spellcheck' => 'false', 
                    'style' => 'overflow: hidden;'
                ) + $addAttrs;
                
                $control = Form::textArea($attrArray);
            }
            break;
        
            case 'text_editor':
            {   
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm text_editorInit', 
                    'placeholder' => $placeholder, 
                    'value' => $value
                ) + $addAttrs;
                
                $control = Form::textArea($attrArray);
            }
            break;   

            case 'button':
            {   
                $iconName = (isset($row['ICON_NAME']) && $row['ICON_NAME'] != '') ? '<i class="fa '.$row['ICON_NAME'].'"></i> ' : '';
                $control = '<button type="button" class="btn blue btn-sm" data-path="'.$columnNamePath.'">'.$iconName.$placeholder.'</button>';
            }
            break;   

            case 'port_location_configuration':
            {   
                $iconName = (isset($row['ICON_NAME']) && $row['ICON_NAME'] != '') ? '<i class="fa '.$row['ICON_NAME'].'"></i> ' : '';
                $control = '<button type="button" class="btn blue btn-sm" onclick="initPortLocationConfiguration(this)" data-path="'.$columnNamePath.'">'.$iconName.$placeholder.'</button>';
            }
            break;   
            
            case 'color_picker':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm stringInit', 
                    'autocomplete' => 'off', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'data-field-name' => $cellId
                ) + $addAttrs;

                $control = html_tag('div', array(
                        'class' => 'input-group color bp-color-picker',
                        'data-section-path' => $columnNamePath,
                        'data-color' => $value,
                    ), Form::text($attrArray) . '<span class="input-group-btn"><button tabindex="-1" onclick="initBpColorPicker(this); return false;" class="btn default border-left-0 mr-0 colorpicker-input-addon px-1" style="height: 25px;border-radius: 0 3px 3px 0;"><i style="position:relative; rigth: 0; background-color: ' . $value . '"></i></button></span>', true
                );
            }
            break;
            
            case 'icon_picker':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control form-control-sm icon_pickerInit', 
                    'value' => $value, 
                    'data-field-name' => $cellId
                ) + $addAttrs;
                
                $btnAttr['class'] = 'icon_pickerInit btn btn-sm btn-secondary';
                $btnAttr['data-search-text'] = Lang::line('META_00109');
                $btnAttr['data-placement'] = 'top';
                $btnAttr['data-iconset'] = 'icomoon';
                $btnAttr['data-cols'] = '5';
                
                if ($value) {
                    $btnAttr['data-icon'] = $value;
                    $attrArray['value'] = $value;
                }

                $control = Form::hidden($attrArray).Form::button($btnAttr);
            }
            break;
        
            case 'default_file':
            {   
                $href = 'mdobject/downloadFile?fDownload=1&file=' . $row['DEFAULT_FILE'];
                $control = html_tag('a', array('href'=>$href,'title'=>Lang::line('download_btn'),'class'=>'btn btn-sm btn-light rounded-0'), '<i class="icon-download"></i>');
            }
            break;  
        
            case 'html_clicktoedit':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'data-field-name' => $cellId, 
                    'value' => $value, 
                    'style' => 'display:none;'
                ) + $addAttrs;
                
                $control = '<div class="input-group">
                    <div class="form-control-plaintext texteditor_clicktoeditInit" contenteditable="true" spellcheck="false" style="text-align: initial">
                        '.Str::cleanOut($value).'
                    </div>
                    '.Form::textArea($attrArray).'
                    <span class="input-group-append">
                        <button type="button" class="btn grey-cascade" onclick="bpFieldTextEditorClickToEdit(this);">
                            <i class="icon-design"></i>
                        </button>
                    </span> 
                </div>';
            }
            break;
            
            case 'expression_editor':
            {
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'data-field-name' => $cellId, 
                    'value' => $value, 
                    'placeholder' => $placeholder, 
                    'style' => 'height: 28px; overflow: hidden; resize: none;', 
                    'draggable' => 'false', 
                    'rows' => '1', 
                    'class' => 'form-control form-control-sm expression_editorInit'
                ) + $addAttrs;
                
                $control = '<div class="input-group">
                            '.Form::textArea($attrArray).'
                            <span class="input-group-append"><button class="btn grey-cascade" type="button" onclick="bpExpressionEditor(this);"><i class="far fa-code"></i></button></span> 
                        </div>';
            }
            break;
        
            default:
                
                if (is_array($value)) {
                    $value = null;
                }
                
                $attrArray = array(
                    'name' => $controlName, 
                    'data-path' => $columnNamePath, 
                    'data-col-path' => $code, 
                    'class' => 'form-control input-sm stringInit', 
                    'autocomplete' => 'off', 
                    'placeholder' => $placeholder, 
                    'value' => $value, 
                    'data-field-name' => $cellId
                ) + $addAttrs;
                
                $translationInput = '';
                if (Lang::isUseMultiLang() && $row['IS_TRANSLATE']) {
                    
                    $pfTranslationValue = [];
                    $attrArray['data-c-name'] = $columnName;
                    
                    if (Mdform::$kpiDmMart && issetParam(Mdform::$kpiDmMart['TRANSLATION_VALUE'])) {
                        $pfTranslationValue = json_decode(Mdform::$kpiDmMart['TRANSLATION_VALUE'], true);
                        if (isset($pfTranslationValue['value'][$columnName])) {
                            $translationInput = '<textarea name="'.$columnName.'_translation" style="display:none" data-translate-path="'.$columnName.'">'.json_encode($pfTranslationValue['value'][$columnName]).'</textarea>';
                        }
                    }

                    if (Lang::getCode() != Lang::getDefaultLangCode()) {
                            
                        $attrArray['data-dl-value'] = $value;                        

                        if (isset($pfTranslationValue['value'][$columnName][Lang::getCode()])) {
                            $attrArray['value'] = $pfTranslationValue['value'][$columnName][Lang::getCode()];                            
                        }
                    }
                    
                    $control = '<div class="input-group">
                        '.Form::text($attrArray).$translationInput.'                        
                        <span class="input-group-append"><button class="btn btn-primary" type="button" onclick="bpFieldTranslate(this);" title="Орчуулга"><i class="fa fa-language"></i></button></span> 
                    </div>';
                    
                } else {
                    $control = Form::text($attrArray);
                }
                
            break;    
        }
        
        return $control;
    }
    
    public function getKpiComboDataModel($row) {
        
        try {
            
            $indicatorId = $row['FILTER_INDICATOR_ID'];
            $tableName = $row['TRG_TABLE_NAME'];
            $langCode = Lang::getCode();
            
            $configs = $this->db->GetAll("
                SELECT 
                    UPPER(KIIM.COLUMN_NAME) AS COLUMN_NAME, 
                    KIIM.INPUT_NAME, 
                    KIIM.SHOW_TYPE, 
                    KI.QUERY_STRING, 
                    KI.TABLE_NAME, 
                    FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS INDICATOR_NAME 
                FROM KPI_INDICATOR_INDICATOR_MAP KIIM
                    INNER JOIN KPI_INDICATOR KI ON KI.ID = KIIM.MAIN_INDICATOR_ID 
                WHERE KIIM.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND KIIM.INPUT_NAME IN ('META_VALUE_ID', 'META_VALUE_CODE', 'META_VALUE_NAME') 
                ORDER BY KIIM.INPUT_NAME ASC", 
                array($indicatorId)
            );
            
            $idField = 'ID';
            $codeField = 'CODE';
            $nameField = 'NAME';
            
            if ($configs) {
                foreach ($configs as $config) {
                    
                    if ($config['INPUT_NAME'] == 'META_VALUE_ID') {
                        $idField = $config['COLUMN_NAME'];
                    } elseif ($config['INPUT_NAME'] == 'META_VALUE_CODE') {
                        $codeField = $config['COLUMN_NAME'];
                        $isCodeField = true;
                    } elseif ($config['INPUT_NAME'] == 'META_VALUE_NAME') {
                        
                        $nameField = $config['COLUMN_NAME'];
                        $showType = $config['SHOW_TYPE'];
                        $isNameField = true;
                        
                        if ($showType == 'combo' || $showType == 'radio' || $showType == 'popup') {
                            $nameField .= '_DESC';
                        }
                    }
                }
                
                if (!isset($isCodeField) && isset($isNameField)) {
                    $codeField = $nameField;
                }
                
                if (isset($isCodeField) && !isset($isNameField)) {
                    $nameField = $codeField;
                }
            } 
            
            if (!$tableName) {
                $tableName = issetParam($configs[0]['TABLE_NAME']);
            }
            
            $where          = '';
            $linkLookupPath = issetParam($row['GROUP_CONFIG_LOOKUP_PATH']);
            $indicatorName  = issetParam($configs[0]['INDICATOR_NAME']);
            
            $response       = array('id' => $idField, 'code' => $codeField, 'name' => $nameField, 'indicatorName' => $indicatorName);
            
            if ($linkLookupPath) {

                $linkColumnPath = $row['GROUP_CONFIG_PARAM_PATH'];

                if (Mdform::$kpiIndicatorRowData) {

                    $linkVal = issetParam(Mdform::$kpiIndicatorRowData[$linkColumnPath]);

                    if ($linkVal != '') {
                        $where .= " AND $linkLookupPath = '$linkVal'";
                        $response['isIgnoreDisabled'] = true;
                    }

                } elseif (Mdform::$kpiDmMart) {

                    $linkVal = issetParam(Mdform::$kpiDmMart[$linkColumnPath]);

                    if ($linkVal != '') {
                        
                        if (isset($linkVal['id'])) {
                            $linkVal = $linkVal['id'];
                        }
                        
                        $where .= " AND $linkLookupPath = '$linkVal'";
                        $response['isIgnoreDisabled'] = true;
                    }
                }
            }
            
            $data = array();
            
            if (issetParam($row['isData'])) {
                
                if (!$tableName && $queryString = issetParam($configs[0]['QUERY_STRING'])) {
                    $tableName = self::parseQueryString($queryString);
                    
                    unset(self::$indicatorColumns[$indicatorId]);
                
                    $row['isFilter'] = true;
                    $row['IS_USE_WORKFLOW'] = 0;
                    $row['isIgnoreStandardFields'] = true;
                    
                    $filterParams = self::getKpiIndicatorColumnsModel($indicatorId, $row);
                    
                    if ($drillDownCriteria = issetParam($row['drillDownCriteria'])) {
                        
                        parse_str($drillDownCriteria, $drillDownCriteriaArr);
                        
                        $drillDownCriteriaArr = Arr::changeKeyLower($drillDownCriteriaArr);
                        $drillDownCriterias = array();
                        
                        foreach ($drillDownCriteriaArr as $drillDownCriteriaKey => $drillDownCriteriaVal) {
                            $drillDownCriterias['filter_'.$drillDownCriteriaKey] = $drillDownCriteriaVal;
                        }
                    }

                    foreach ($filterParams as $filterParam) {
                        $trgAliasName = strtolower($filterParam['TRG_ALIAS_NAME']);
                        if ($trgAliasName != '') {
                            $filterVal = isset($drillDownCriterias[$trgAliasName]) ? "'".$drillDownCriterias[$trgAliasName]."'" : "''";
                            $tableName = str_ireplace(':'.$trgAliasName, $filterVal, $tableName);
                        }
                    }
                }
                
                if (stripos($tableName, 'select') !== false && stripos($tableName, 'from') !== false) {
                    
                    $tableName = '('.$tableName.')';
                    
                } elseif (!self::isCheckSystemTable($tableName)) {
                    
                    $where .= ' AND DELETED_USER_ID IS NULL'; 
                }
                
                $isSelectExecute = true;
                
                if (isset($row['rowId'])) {
                    if ($row['rowId'] != '') {
                        $where .= " AND $idField = '".$row['rowId']."'"; 
                    } else {
                        $isSelectExecute = false;
                    }
                }
                
                if (isset($row['rowIds'])) {
                    if ($row['rowIds'] != '') {
                        $where .= " AND $idField IN (".$row['rowIds'].")"; 
                    } else {
                        $isSelectExecute = false;
                    }
                }
                
                if ($isSelectExecute) {
                    $data = $this->db->GetAll("SELECT $idField, $nameField FROM $tableName WHERE 1 = 1 $where ORDER BY TO_CHAR(SUBSTR($nameField, 0, 50)) ASC");
                }
            }
            
            $response['data'] = $data;
            
            return $response;
            
        } catch (Exception $ex) {
            return array('id' => '', 'code' => '', 'name' => '', 'indicatorName' => '', 'data' => array());
        }
    }
    
    public function childKpiIndicatorTemplate($indicatorId, $data, $parentId, $row) {
        
        $render = array();
        $arr = array_filter($data, function($ar) use($parentId) {
            return ($ar['PARENT_ID'] == $parentId);
        });
        
        if ($arr) {
            
            if ($row['RENDER_TYPE'] == 'FORM') { }
            
            if ($row['TABLE_NAME']) {
                self::$indicatorTableName = $row['TABLE_NAME'];
            }
                
            foreach ($arr as $k => $arrRow) {
                
                unset($data[$k]);
                
                Mdform::$pathPrefix = null;
                Mdform::$pathSuffix = null;
                
                $labelTooltip = '';
                $controlRender = array();
                
                if ($arrRow['SHOW_TYPE'] != 'label' && $arrRow['SHOW_TYPE'] != 'rows') {
                    
                    if ($arrRow['IS_RENDER'] != '1') {
                        continue;
                    }
                    
                    if ($arrRow['SHOW_TYPE'] == 'config' && $arrRow['DEFAULT_VALUE'] == 'ORGANIZATION_LOGO_PATH') {
                        $configValue = Config::get($arrRow['DEFAULT_VALUE']);
                        if (file_exists($configValue)) {
                            Mdform::$indicatorFormLogo = $configValue;
                        }
                        continue;
                    }
                    
                    if ($arrRow['EXPRESSION_STRING']) {
                        
                        $expStr = html_entity_decode($arrRow['EXPRESSION_STRING']);
                        $jsonExp = json_decode($expStr, true);
                        
                        if (isset($jsonExp['expression'])) {
                            $exp = $jsonExp['expression'];
                        } else {
                            $exp = $expStr;
                        }
                        
                        Mdform::$indicatorHdrExpression[$arrRow['COLUMN_NAME']] = $exp;
                        $arrRow['columnReadonly'] = true;
                    }
                    
                    $labelText = Lang::line($arrRow['NAME']);
                    $labelAttr = array(
                        'text' => $labelText, 
                        'required' => ($arrRow['IS_REQUIRED'] == '1' ? 'required' : null), 
                        'for' => 'kpiTbl['.$arrRow['COLUMN_NAME'].']'
                    );
                    $inlineRows = issetParam($arrRow['INLINE_FIELDS']);
                    
                    if ($arrRow['JSON_CONFIG']) {
                        
                        $jsonExp = json_decode($arrRow['JSON_CONFIG'], true);
                        
                        if (isset($jsonExp['tooltip'])) {
                            $labelAttr['no_colon'] = 1;
                            $labelText .= '<span class="label-colon">:</span> <i class="fas fa-info-circle text-grey-700" data-qtip-title="'.$this->lang->line($jsonExp['tooltip']).'" data-qtip-pos="top"></i>';
                        }
                    }
                    
                    $labelAttr['text'] = $labelText;
                    $label = Form::label($labelAttr);
                    
                    $control = self::kpiIndicatorControl($arrRow);
                    
                    if ($inlineRows) {
                        
                        Mdform::$headerInlineFields[] = array(
                            'rowsPath' => $inlineRows, 
                            'fieldPath' => $arrRow['COLUMN_NAME_PATH'], 
                            'label' => $label, 
                            'control' => $control
                        );
                        
                        continue;
                        
                    } else {
                        $controlRender[] = '<table class="table kpi-hdr-table">';
                            $controlRender[] = '<tbody>';
                                $controlRender[] = '<tr data-cell-path="'.$arrRow['COLUMN_NAME_PATH'].'">';
                                    $controlRender[] = '<td class="kpi-hdr-table-label" style="width: '.Mdform::$labelWidth.'; text-align: right; border: 1px #ddd solid;background-color: #f5f5f5;">' . $label . '</td>';
                                    $controlRender[] = '<td style="border: 1px #ddd solid;" class="stretchInput">' . $control . '</td>';
                                $controlRender[] = '</tr>';
                            $controlRender[] = '</tbody>';
                        $controlRender[] = '</table>';
                    }
                    
                } else {
                    
                    $isInlineFields = false;
                    
                    if ($arrRow['JSON_CONFIG']) {

                        $jsonExp = json_decode($arrRow['JSON_CONFIG'], true);

                        if (isset($jsonExp['tooltip'])) {
                            $labelTooltip = ' <i class="fas fa-info-circle text-grey-700" data-qtip-title="'.$this->lang->line($jsonExp['tooltip']).'" data-qtip-pos="top"></i>';
                        }

                        if (isset($jsonExp['inlineFields'])) {
                            $isInlineFields = true;
                        }
                    }
                    
                    if ($isInlineFields) {
                        if ($arrRow['IS_NOT_TITLE'] != '1') {
                            $controlRender[] = '<div class="mv-rows-title" style="font-weight: bold; margin: 15px 0 2px 0; text-align: '.$arrRow['BODY_ALIGN'].';">' . Lang::line($arrRow['NAME']) . $labelTooltip . '<!--rows_'.$arrRow['COLUMN_NAME_PATH'].'--></div>';
                        } else {
                            $controlRender[] = '<div class="mv-rows-title"><!--rows_'.$arrRow['COLUMN_NAME_PATH'].'--></div>';
                        }
                    }
                        
                    $controlRender[] = '<div data-section-path="'.$arrRow['COLUMN_NAME_PATH'].'">';
                    
                    if (!$isInlineFields && $arrRow['IS_NOT_TITLE'] != '1') {
                        $controlRender[] = '<div style="font-weight: bold; margin: 15px 0 2px 0; text-align: '.$arrRow['BODY_ALIGN'].';">' . Lang::line($arrRow['NAME']) . $labelTooltip . '</div>';
                    }
                    
                    if ($arrRow['SHOW_TYPE'] == 'label') {
                        
                        $controlRender[] = self::childKpiIndicatorTemplate($indicatorId, $data, $arrRow['ID'], $arrRow);
                        
                    } elseif ($arrRow['SHOW_TYPE'] == 'rows') {
                        
                        if ($arrRow['FILTER_INDICATOR_ID'] && $arrRow['SEMANTIC_TYPE_NAME'] == 'Sub хүснэгт') {
                            
                            Mdform::$pathPrefix = '[subTable]['.$arrRow['COLUMN_NAME'].']';
                            
                            if (Mdform::$defaultTplSavedId && Mdform::$kpiDmMart) {
                                
                                $savedSubTableRows = self::getKpiSubTableRowsModel($indicatorId, $arrRow['FILTER_INDICATOR_ID'], Mdform::$defaultTplSavedId, $arrRow['COLUMN_NAME']);
                                
                                if ($savedSubTableRows) {
                                    Mdform::$kpiDmMart[$arrRow['COLUMN_NAME'] . '_subTableRows'] = $savedSubTableRows;
                                }
                            }
            
                        } else {
                            Mdform::$pathPrefix = '[rows]['.$arrRow['COLUMN_NAME'].']';
                        }
                        
                        Mdform::$pathSuffix = '[0]';
                
                        $controlRender[] = self::rowsKpiIndicatorTemplate($indicatorId, $data, $arrRow['ID'], $arrRow);
                    }
                    
                    $controlRender[] = '</div>';
                }
                
                $renderControl = implode('', $controlRender);
                
                if ($arrRow['TAB_NAME_TOP']) {
                    
                    if ($arrRow['TAB_NAME']) {
                        
                        Mdform::$topTabRender[$arrRow['TAB_NAME_TOP'].'sysTopTabName'][$arrRow['TAB_NAME']][] = $renderControl;
                        
                    } else {
                        Mdform::$topTabRender[$arrRow['TAB_NAME_TOP']][] = $renderControl;
                    }
                    
                } else {
                    
                    if ($arrRow['TAB_NAME']) {
                        
                        Mdform::$tabRender[$arrRow['TAB_NAME']][] = $renderControl;
                    } else {
                        $render[] = $renderControl;
                    }
                }
                
            }
        }
        
        return implode('', $render);
    }
    
    public function getKpiSubTableRowsModel($mainIndicatorId, $trgIndicatorId, $sourceId, $columnName) {
        
        try {
            
            $configRow = $this->db->GetRow("
                SELECT 
                    T1.TABLE_NAME, 
                    T1.ID AS MAIN_INDICATOR_ID, 
                    T2.STRUCTURE_INDICATOR_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                    INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.MAIN_INDICATOR_ID  
                WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND T0.COLUMN_NAME = ".$this->db->Param(1), 
                array($mainIndicatorId, $columnName)
            );
            
            if ($trgTableName = issetParam($configRow['TABLE_NAME'])) {
                
                $data = $this->db->GetAll("
                    SELECT 
                        T1.* 
                    FROM META_DM_RECORD_MAP T0 
                        INNER JOIN $trgTableName T1 ON T1.ID = T0.TRG_RECORD_ID 
                            AND T1.DELETED_USER_ID IS NULL 
                    WHERE T0.SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                        AND T0.TRG_REF_STRUCTURE_ID = ".$this->db->Param(1)." 
                        AND T0.SRC_RECORD_ID = ".$this->db->Param(2)." 
                        AND T0.SRC_NAME = ".$this->db->Param(3)." 
                    ORDER BY TO_NUMBER(T0.ORDER_NUM) ASC", 
                    array($mainIndicatorId, $trgIndicatorId, $sourceId, $columnName)
                );
                
                $result = $data ? $data : null;
                
            } else {
                $result = null;
            }
            
        } catch (Exception $ex) {
            $result = null;
        }
        
        return $result;
    }
    
    public function rowsKpiIndicatorTemplate($indicatorId, $data, $parentId, $row, $rowDatas = array()) {
        
        $render = array();
        $arr = array_filter($data, function($ar) use($parentId) {
            return ($ar['PARENT_ID'] == $parentId);
        });
        $widgetCode = $row['WIDGET_CODE'];
        
        if ($arr) {
            
            $isSavedDataJson = $isColumnAggregate = $isTemplateRows = false;
            $savedDataJson = array();
            $parentColumnName = $row['COLUMN_NAME'];
            $isExcelImport = $row['IS_EXCEL_IMPORT'];
            $isFromMartExpression = $row['IS_FROM_MART_EXPRESSION'];
            
            if ((Mdform::$recordId || Mdform::$defaultTplSavedId) && Mdform::$kpiDmMart) {
                
                if ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                    
                } elseif ($rowJson = issetParam(Mdform::$kpiDmMart[$parentColumnName . '_subTableRows'])) {
                    
                    $savedDataJson = $rowJson;
                    $isSavedDataJson = true;
                }
                
            } elseif ($rowDatas) {
                
                $savedDataJson = $rowDatas;
                $isSavedDataJson = true;
            }
            
            $isTemplateConfig = issetParam($row['isTemplateConfig']);
            
            if ($row['TEMPLATE_TABLE_NAME']) {
                
                $templateRows = self::getKpiIndicatorTemplateRows($row['TEMPLATE_TABLE_NAME']);
                $isTemplateRows = true;
            }
            
            $renderBody = $renderFooter = $mergeRows = $configArr = array();
            $allColumnCountMerge = 0;
            $excelImportButton = $fromMartExpressionButton = '';
            
            $rowRemoveButton = html_tag('button', 
                array(
                    'type' => 'button', 
                    'class' => 'btn red btn-xs bp-remove-row', 
                    /*'onclick' => 'removeRowKpiIndicatorTemplate(this);'*/
                ), 
                '<i class="far fa-trash"></i>'
            );
            
            if ($isExcelImport) {
                
                $excelImportButton = html_tag('button', 
                    array(
                        'type' => 'button', 
                        'class' => 'btn btn-xs btn-outline-success mr-2', 
                        'onclick' => 'mvRowsExcelImportTemplate(this, '.$indicatorId.', '.$row['ID'].', \''.$row['COLUMN_NAME'].'\', '.intval($isTemplateRows).');'
                    ), 
                    '<i class="far fa-file-excel"></i> Эксель импорт'
                );
            }
            
            if ($isFromMartExpression) {
                
                $fromMartExpressionButton = html_tag('button', 
                    array(
                        'type' => 'button', 
                        'class' => 'btn btn-xs btn-outline-primary', 
                        'onclick' => 'mvRowsGetValueFromDataMart(this, '.$indicatorId.', '.$row['ID'].', \''.$row['COLUMN_NAME'].'\');'
                    ), 
                    '<i class="far fa-cloud-download"></i> Утга татах'
                );
            }
            
            $renderFooter[] = '<td></td>';

            if ($isTemplateConfig) {
                
                $render[] = '<div class="mt-2 mb-2">';

                    $render[] = html_tag('button', 
                        array(
                            'type' => 'button', 
                            'class' => 'btn btn-xs green-meadow', 
                            'onclick' => 'addRowKpiIndicatorTemplateConfig(this);'
                        ), 
                        '<i class="far fa-plus"></i> '.$this->lang->line('addRow')
                    );

                $render[] = '</div>';
                
            } elseif ($isTemplateRows == false) {
                
                $render[] = '<div class="mt-2 mb-2 d-flex">';
                    
                    $render[] = '<input type="text" class="form-control input-xs bp-add-one-row-num integerInit" data-v-min="1" data-v-max="1000" data-addrowtype="selectedrow" data-action-path="'.$row['COLUMN_NAME'].'" data-row-limit="'.$row['ROW_COUNT_LIMIT'].'" onkeydown="if(event.keyCode==13){ addRowKpiIndicatorTemplate(this); return false;}"/>';

                    $render[] = html_tag('button', 
                        array(
                            'type' => 'button', 
                            'class' => 'btn btn-xs green-meadow bp-add-one-row mr-2', 
                            'onclick' => 'addRowKpiIndicatorTemplate(this);', 
                            'data-action-path' => $row['COLUMN_NAME'], 
                            'data-row-limit' => $row['ROW_COUNT_LIMIT']
                        ), 
                        '<i class="far fa-plus"></i> '.$this->lang->line('addRow')
                    );
                    
                    if ($row['IS_ROWS_LOOKUP']) {
                        $render[] = self::kpiRowsLookupRender($indicatorId, $row);
                    }
                    
                    if ($isExcelImport) {
                        $render[] = str_replace('btn-outline-success', 'btn-outline-success float-right', $excelImportButton);
                    }

                $render[] = '</div>';
                
                $renderBody[] = '<tr class="bp-detail-row">';
                
            } elseif ($isTemplateRows && ($isExcelImport || $isFromMartExpression)) {
                
                $render[] = '<div class="mb-2" style="float:left;margin-top:-27px">';
                    $render[] = $excelImportButton;
                    $render[] = $fromMartExpressionButton;
                $render[] = '</div>';
                $render[] = '<div style="display:block;clear:both"></div>';
            }
            
            if ($row['REPLACE_PATH'] != '') {
                Mdform::$isRowsReplacePath = true;
            }
            
            $render[] = '<div class="bp-overflow-xy-auto mb15" data-isclear="1">';
                $render[] = '<table class="table table-sm table-bordered table-hover bprocess-theme1 kpi-dtl-table" data-tbl-name="'.$row['TEMPLATE_TABLE_NAME'].'" data-col-name="'.$row['COLUMN_NAME'].'" data-table-path="'.$row['COLUMN_NAME'].'" data-replace-path="'.$row['REPLACE_PATH'].'" style="table-layout: fixed">';
                    $render[] = '<thead>';
                    
                        if ($isTemplateConfig) {
                            
                            $n = 1;
                            $colAlphaCodeArr = array();
                            
                            $render[] = '<tr data-column-code="1">';
                            
                                $render[] = '<th style="width: 112px;min-width: 112px;max-width: 112px;"></th>';
                                $render[] = '<th style="width: 50px;min-width: 50px;max-width: 50px;"></th>';
                            
                                foreach ($arr as $k => $arrRow) {
                                    
                                    if ($arrRow['IS_RENDER'] == '1' && $arrRow['MERGE_TYPE'] != 'row') {
                                        
                                        if ($arrRow['SEMANTIC_TYPE_NAME'] == 'Багана') {
                                            
                                            $colAlphaCode = numToAlpha($n);
                                            $colAlphaCodeArr[$arrRow['COLUMN_NAME']] = $colAlphaCode;
                                            
                                            $render[] = '<th class="text-center font-weight-bold" data-col-alpha-code="'.$colAlphaCode.'" data-cell-path="'.$arrRow['COLUMN_NAME'].'">'.$colAlphaCode.'</th>';
                                            $n++;
                                            
                                        } else {
                                            $render[] = '<th></th>';
                                        }
                                    }
                                }
                                
                                //$render[] = '<th style="width: 45px;"></th>';
                            $render[] = '</tr>';
                        }
                        
                        $render[] = '<tr>';
                        
                        if ($isTemplateConfig) {
                            
                            $render[] = '<th></th>';
                            $render[] = '<th></th>';
                            
                        } elseif ($isTemplateRows == false) {
                            
                            $render[] = '<th style="width: 30px" class="rowNumber bp-dtl-rownumber">№</th>';
                            
                            $renderBody[] = '<td class="bp-dtl-rownumber text-center">1</td>';
                        }
                        
                        foreach ($arr as $k => $arrRow) {
                            
                            if ($arrRow['IS_RENDER'] == '1') {
                                
                                if ($arrRow['MERGE_TYPE'] == 'row') {
                                    
                                    $mergeRows[$arrRow['COLUMN_NAME']] = $arrRow;
                                    
                                } else {
                                    
                                    $columnWidth = $arrRow['COLUMN_WIDTH'];
                                    
                                    if ($columnWidth) {
                                        
                                        $columnWidth = (strpos($columnWidth, '%') !== false) ? $columnWidth : intval($columnWidth).'px';
                                        $columnStyle = 'width: '.$columnWidth.';';
                                        
                                    } else {
                                        $columnStyle = 'width: 100px;';
                                    }
                                    
                                    if ($isTemplateRows == false && $arrRow['COLUMN_AGGREGATE'] != '') {
                                        $isColumnAggregate = true;
                                        $renderFooter[] = '<td class="text-right bigdecimalInit" data-cell-path="' . $arrRow['COLUMN_NAME_PATH'] . '">0</td>';
                                    } else {
                                        $arrRow['COLUMN_AGGREGATE'] = '';
                                        $renderFooter[] = '<td></td>';
                                    }
                                    
                                    $render[] = '<th class="text-center" style="'.$columnStyle.'" data-cell-path="'.$arrRow['COLUMN_NAME_PATH'].'" data-merge-cell="true" data-aggregate="'.$arrRow['COLUMN_AGGREGATE'].'">';
                                        
                                        $render[] = Lang::line($arrRow['NAME']);
                                        
                                        if ($arrRow['JSON_CONFIG']) {

                                            $jsonExp = json_decode($arrRow['JSON_CONFIG'], true);

                                            if (isset($jsonExp['tooltip'])) {
                                                $render[] = ' <i class="fas fa-info-circle text-grey-700" data-qtip-title="'.$this->lang->line($jsonExp['tooltip']).'" data-qtip-pos="top"></i>';
                                            }
                                        }
                    
                                    $render[] = '</th>';
                                    
                                    if ($arrRow['EXPRESSION_STRING']) {
                                        
                                        $colJson = json_decode(html_entity_decode($arrRow['EXPRESSION_STRING']), true);
                                        
                                        $isValidation = isset($colJson['validation']);
                                        $isExpression = isset($colJson['expression']);

                                        if ($isValidation || $isExpression) {
                                            
                                            Mdform::$indicatorColExpression[$arrRow['COLUMN_NAME']] = $colJson;
                                            
                                            if ($isExpression && $colJson['expression']) {
                                                $arr[$k]['columnReadonly'] = true;
                                                $arrRow['columnReadonly'] = true;
                                            }
                                            
                                        } else {
                                            
                                            $colJson = array('expression' => $arrRow['EXPRESSION_STRING']);
                                            Mdform::$indicatorColExpression[$arrRow['COLUMN_NAME_PATH']] = $colJson;
                                        }
                                    }
                                    
                                    $arrRow['ignoreSavedDataRow'] = true;
                                    
                                    $renderBody[] = '<td class="stretchInput middle text-center" data-cell-path="'.$arrRow['COLUMN_NAME_PATH'].'">';
                                        $renderBody[] = self::kpiIndicatorControl($arrRow, array('rowNum' => '0'));
                                    $renderBody[] = '</td>';

                                    $parentId = $arrRow['ID'];

                                    $childArr = array_filter($data, function($ar) use($parentId) {
                                        return ($ar['PARENT_ID'] == $parentId);
                                    });

                                    $arr[$k]['childArr'] = $childArr;
                                    
                                    $allColumnCountMerge ++;
                                }
                            }
                            
                            if ($isTemplateConfig && $arrRow['COLUMN_NAME']) {
                                $arrRow['control'] = self::kpiIndicatorControl($arrRow);
                                $configArr[] = $arrRow;
                            }
                        }
                        
                        if ($isTemplateRows == false) {
                            
                            $render[] = '<th style="width: 45px;"></th>';
                        
                            $renderBody[] = '<td class="text-center stretchInput middle">';
                            
                                $renderBody[] = $rowRemoveButton;
                                
                            $renderBody[] = '</td>';
                            
                            $renderBody[] = '</tr>';
                            
                            $renderFooter[] = '<td></td>';
                            
                        } elseif ($isTemplateConfig) {
                            
                            //$render[] = '<th style="width: 45px;"></th>';
                        }

                        $render[] = '</tr>';
                    $render[] = '</thead>';
                    
                    $render[] = '<tbody class="tbody">';
                    
                    if ($isTemplateRows == true && $templateRows) {
                        
                        $templateRowsCount = count($templateRows);
                        $isShowDescCol = array_key_exists('SHOW_DESC', $templateRows[0]) ? true : false;
                        
                        foreach ($templateRows as $t => $templateRow) {
                            
                            Mdform::$pathSuffix = '['.$t.']';
                            Mdform::$indicatorTemplateRow[$parentColumnName] = $templateRow;
                            
                            $id = issetParam($templateRow['ID']);
                            $rowParentId = issetParam($templateRow['PARENT_ID']);
                            $rowStyle = issetParam($templateRow['ROW_STYLE']);
                            
                            $rowIndex = $t + 1;
                            
                            $templateRowJson = Form::textArea(array(
                                'name' => 'kpiTbl'.Mdform::$pathPrefix.'[columnJson]'.Mdform::$pathSuffix, 
                                'class' => 'd-none',
                                'value' => json_encode($templateRow, JSON_UNESCAPED_UNICODE)
                            ));

                            $render[] = '<tr data-id="'.$id.'" data-parentid="'.$rowParentId.'" data-row-index="'.$rowIndex.'" class="bp-detail-row kpi-row-'.$rowStyle.'">';
                            
                                $render[] = '<td class="d-none">' . $templateRowJson . '</td>';
                            
                            if ($isTemplateConfig) {
                                
                                $render[] = '<td class="text-center">';
                                    
                                    /*start tmp edit*/
                                    $render[] = '<div class="btn-group">';
                                        
                                        if ($t != 0) {
                                            $render[] = '<button type="button" class="btn btn-sm btn-light" onclick="directionKpiIndicatorTemplateConfig(this, \'up\');" style="padding: 1px 6px;"><i class="far fa-arrow-up"></i></button>';
                                        }
                                        
                                        if ($templateRowsCount != $rowIndex) {
                                            $render[] = '<button type="button" class="btn btn-sm btn-light" onclick="directionKpiIndicatorTemplateConfig(this, \'down\');" style="padding: 1px 6px;"><i class="far fa-arrow-down"></i></button>';
                                        }
                                        
                                        if ($t != 0) {
                                            
                                            if ($rowParentId) {
                                                $render[] = '<button type="button" class="btn btn-sm btn-light" onclick="directionKpiIndicatorTemplateConfig(this, \'left\');" style="padding: 1px 6px;"><i class="far fa-arrow-left"></i></button>';
                                            }
                                            
                                            $render[] = '<button type="button" class="btn btn-sm btn-light" onclick="directionKpiIndicatorTemplateConfig(this, \'right\');" style="padding: 1px 6px;"><i class="far fa-arrow-right"></i></button>';
                                        }
                                        
                                    $render[] = '</div>';
                                    /*end tmp edit*/
                                
                                $render[] = '</td>';
                                
                                $render[] = '<td class="text-center font-weight-bold">'.$rowIndex.'</td>';
                            }
                            
                            $templateRow['rowNum'] = $t;
                        
                            foreach ($arr as $k => $arrRow) {
                                
                                if ($arrRow['IS_RENDER'] == '1') {
                                    
                                    if (isset($mergeRows[$arrRow['COLUMN_NAME']])) {
                                        
                                        $rowValueId = $templateRow[$arrRow['COLUMN_NAME']];
                                        
                                        if (isset($mergeRows[$arrRow['COLUMN_NAME']][$rowValueId])) {
                                            
                                            $render[] = '';
                                            
                                        } else {
                                        
                                            $render[] = '<td class="text-center font-weight-bold" colspan="'.$allColumnCountMerge.'">';
                                                $render[] = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                            $render[] = '</td>';
                                            
                                            $mergeRows[$arrRow['COLUMN_NAME']][$rowValueId] = 1;
                                            
                                            continue 2;
                                        }
                                        
                                    } else {
                                    
                                        $cellStyle = $mergeCell = $cellClass = '';
                                        $isWithChild = (isset($arrRow['childArr']) && $arrRow['childArr']);

                                        if (($arrRow['SHOW_TYPE'] == 'label' && $isShowDescCol) || $arrRow['IS_PARENT'] == '1') {

                                            if ($arrRow['IS_PARENT'] == '1') {
                                                $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                            } else {
                                                $showCellValue = $templateRow['SHOW_DESC'];
                                            }

                                            if ($templateRow['PARENT_ID']) {

                                                $cellStyle = self::getIndicatorCellStyle($templateRows, $templateRow['PARENT_ID']);

                                                if ($cellStyle == 1) {
                                                    $cellStyle = 'padding-left: 15px;';
                                                } elseif ($cellStyle == 2) {
                                                    $cellStyle = 'padding-left: 30px;';
                                                } elseif ($cellStyle == 3) {
                                                    $cellStyle = 'padding-left: 45px;';
                                                } elseif ($cellStyle == 4) {
                                                    $cellStyle = 'padding-left: 60px;';
                                                } elseif ($cellStyle == 5) {
                                                    $cellStyle = 'padding-left: 75px;';
                                                } elseif ($cellStyle == 6) {
                                                    $cellStyle = 'padding-left: 90px;';
                                                }

                                            } else {
                                                $cellStyle = 'font-weight: bold;';
                                            }
                                            
                                            $mergeCell = 'true';

                                        } elseif ($arrRow['SEMANTIC_TYPE_NAME'] == 'Мөр') {

                                            $showCellValue = issetParam($templateRow[$arrRow['COLUMN_NAME'].'_DESC']);
                                            $mergeCell = 'true';
                                            
                                        } else {
                                            
                                            $arrRow['parentColumnName'] = $parentColumnName;
                                            $arrRow['cellJson'] = issetParam($templateRow[$arrRow['COLUMN_NAME']]);
                                            
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
                                            
                                            Mdform::$indicatorCellStyle = null;
                                            
                                            $showCellValue = self::kpiIndicatorControl($arrRow, $templateRow);
                                            
                                            $cellClass = 'stretchInput middle';
                                            
                                            if (Mdform::$indicatorCellStyle) {
                                                $cellClass .= ' kpi-col-' . Mdform::$indicatorCellStyle;
                                            }
                                        }
                                        
                                        $templateRowJson = '';
                                        
                                        $alphaCode = $isTemplateConfig ? issetParam($colAlphaCodeArr[$arrRow['COLUMN_NAME']]) : '';
                                        
                                        $render[] = '<td style="padding: 0.2rem;'.$cellStyle.'" class="'.$cellClass.'" data-merge-cell="'.$mergeCell.'" data-merge-mode="'.$arrRow['MERGE_TYPE'].'" data-alpha-code="'.$alphaCode.'" data-cell-path="'.$arrRow['COLUMN_NAME_PATH'].'">';
                                            $render[] = $showCellValue;

                                            if ($isWithChild) {
                                                $render[] = self::kpiIndicatorHiddenControl($arrRow['childArr'], $arrRow, $templateRow);
                                            }

                                        $render[] = '</td>';
                                    }
                                }
                            }
                                
                            $render[] = '</tr>';
                        }
                        
                    } elseif ($isSavedDataJson) {
                        
                        $n = 1;
                        
                        foreach ($savedDataJson as $k => $row) {
                            
                            Mdform::$pathSuffix = '['.($n - 1).']';
                            $row['rowNum'] = $k;
                            
                            $render[] = '<tr class="bp-detail-row saved-bp-row" data-savedrow="1">';
                                $render[] = '<td class="bp-dtl-rownumber text-center">'.$n.'</td>';

                                foreach ($arr as $k => $arrRow) {

                                    if ($arrRow['IS_RENDER'] == '1' && $arrRow['COLUMN_NAME'] && $arrRow['MERGE_TYPE'] != 'row') {

                                        $render[] = '<td class="stretchInput middle text-center" data-cell-path="'.$arrRow['COLUMN_NAME_PATH'].'">';
                                            $render[] = self::kpiIndicatorControl($arrRow, $row);
                                        $render[] = '</td>';
                                    }
                                }
                            
                                $render[] = '<td class="text-center stretchInput middle">';
                                    $render[] = $rowRemoveButton;
                                $render[] = '</td>';
                            $render[] = '</tr>';
                            
                            $n++;
                        }
                        
                    } else {
                        
                        if ($row['IS_REQUIRED'] == '1') {
                            $render[] = implode('', $renderBody);
                        }
                    }
                    
                    $render[] = '</tbody>';
                    
                    $render[] = '<tfoot>' . ($isColumnAggregate === true ? implode('', $renderFooter) : '') . '</tfoot>';
                    
                $render[] = '</table>';
            $render[] = '</div>';
            
            $render[] = '<script type="text/template" data-template="rows">';
                $render[] = implode('', $renderBody);
            $render[] = '</script>';
            
            $render[] = '<script type="text/template" data-template="templateConfig">';
                $render[] = json_encode($configArr, JSON_UNESCAPED_UNICODE);
            $render[] = '</script>';
        }
        
        $rowsContent = implode('', $render);
        
        $frameWidgets = (new Mdwidget())->bpDetailFrameWidgets($widgetCode, $indicatorId, array(), array());
        
        if ($frameWidgets) {
            $rowsContent = str_replace('{content}', $rowsContent, $frameWidgets);
        }
        
        return $rowsContent;
    }
    
    public function kpiRowsLookupRender($indicatorId, $arrRow) {
        
        $rowId = $arrRow['ID'];
        $lookupCount = $arrRow['IS_ROWS_LOOKUP'];
        $lookup = array();
        
        $lookupDatas = $this->db->GetAll("
            SELECT 
                T1.TRG_INDICATOR_ID, 
                T1.LOOKUP_META_DATA_ID, 
                T2.NAME AS INDICATOR_NAME, 
                T3.META_DATA_NAME 
            FROM KPI_INDICATOR_INDICATOR_MAP T1 
                LEFT JOIN KPI_INDICATOR T2 ON T2.ID = T1.TRG_INDICATOR_ID 
                LEFT JOIN META_DATA T3 ON T3.META_DATA_ID = T1.LOOKUP_META_DATA_ID 
            WHERE T1.SEMANTIC_TYPE_ID = 42 
                AND T1.SRC_INDICATOR_MAP_ID = ".$this->db->Param(0)." 
                AND (T1.LOOKUP_META_DATA_ID IS NOT NULL OR T1.TRG_INDICATOR_ID IS NOT NULL) 
                AND (T2.ID IS NOT NULL OR T3.META_DATA_ID IS NOT NULL) 
            ORDER BY T1.ID ASC", 
            array($rowId));
        
        $lookupFirstRow = $lookupDatas[0];
        $lookupIndicatorId = $lookupFirstRow['TRG_INDICATOR_ID'];
        $lookupIndicatorName = $lookupFirstRow['INDICATOR_NAME'];
        $lookupMetaDataId = $lookupFirstRow['LOOKUP_META_DATA_ID'];
        $lookupMetaDataName = $lookupFirstRow['META_DATA_NAME'];
        $lookupType = 'meta';
        $lookupId = $lookupMetaDataId;
        $lookupName = $lookupMetaDataName;
        
        if ($lookupIndicatorId) {
            $lookupType = 'indicator';
            $lookupId = $lookupIndicatorId;
            $lookupName = $lookupIndicatorName;
        }
        
        $lookup[] = '<div class="input-group quick-item-process bp-add-ac-row" data-action-path="' . $arrRow['COLUMN_NAME_PATH'] . '">';
            
            if ($lookupCount > 1) {
                
                $lookup[] = '<div class="input-group-btn">
                    <button type="button" class="btn default dropdown-toggle" data-toggle="dropdown" style="max-width: 170px;overflow: hidden;white-space: nowrap;display: inline-block;text-overflow: ellipsis;">'.$lookupName.'</button>
                    <ul class="dropdown-menu">';
                
                foreach ($lookupDatas as $l => $lookupRow) {
                    
                    $lookupRowIndicatorId = $lookupRow['TRG_INDICATOR_ID'];
                    $lookupRowIndicatorName = $lookupRow['INDICATOR_NAME'];
                    $lookupRowMetaDataId = $lookupRow['LOOKUP_META_DATA_ID'];
                    $lookupRowMetaDataName = $lookupRow['META_DATA_NAME'];
                    $lookupRowType = 'meta';
                    $lookupRowId = $lookupRowMetaDataId;
                    $lookupRowName = $lookupRowMetaDataName;
                        
                    if ($lookupRowIndicatorId) {
                        $lookupRowType = 'indicator';
                        $lookupRowId = $lookupRowIndicatorId;
                        $lookupRowName = $lookupRowIndicatorName;
                    }
                    
                    $lookup[] = '<li'.($l == 0 ? ' style="display:none"' : '').'><a href="javascript:;" onclick="mvDetailAcLookupToggle(this);" data-lookup-type="'.$lookupRowType.'" data-lookup-id="'.$lookupRowId.'">'.$lookupRowName.'</a></li>';
                }
                
                $lookup[] = '</ul></div>';
            } 
            
            $lookup[] = '<div class="input-group-btn">
                    <button type="button" class="btn default dropdown-toggle" data-toggle="dropdown">'.Lang::lineDefault('by_code', 'Кодоор').'</button>
                    <ul class="dropdown-menu">
                        <li style="display:none"><a href="javascript:;" onclick="bpDetailACModeToggle(this);" data-filter-type="code">'.Lang::lineDefault('by_code', 'Кодоор').'</a></li>
                        <li><a href="javascript:;" onclick="bpDetailACModeToggle(this);" data-filter-type="name">'.Lang::lineDefault('by_name', 'Нэрээр').'</a></li>
                    </ul>
                </div>';
            $lookup[] = '<div class="input-icon">';
                $lookup[] = '<i class="far fa-search"></i>';
                $lookup[] = Form::text(array(
                    'class' => 'form-control form-control-sm lookup-code-hard-autocomplete lookup-hard-autocomplete',
                    'style' => 'padding-left:25px;',
                    'data-processid' => $indicatorId,
                    'data-lookupid' => $lookupId,
                    'data-lookuptype' => $lookupType, 
                    'data-path' => $arrRow['COLUMN_NAME_PATH'],
                    'data-rowid' => $rowId, 
                    'placeholder' => 'Хайх..'
                ));
            $lookup[] = '</div>';
            $lookup[] = '<span class="input-group-btn">';
                $lookup[] = Form::button(array(
                    'data-action-path' => $arrRow['COLUMN_NAME_PATH'], 
                    'class' => 'btn btn-xs green-meadow',
                    'value' => '<i class="icon-plus3 font-size-12"></i>', 
                    'onclick' => 'mvDetailAcLookupAddRows(this);'
                ));
            $lookup[] = '</span>';
        $lookup[] = '</div>';

        return implode('', $lookup);
    }
    
    public function getIndicatorCellStyle($rows, $parentRowIndex = null, $depth = 0) {
        
        foreach ($rows as $k => $row) {
            if ($row['ID'] == $parentRowIndex) {
                
                if (!$row['PARENT_ID']) {
                    return $depth + 1;
                }

                $depth = self::getIndicatorCellStyle($rows, $row['PARENT_ID'], $depth+1);
                
                break;
            }
        }

        return $depth;
    }
    
    public function indicatorRowsRender($indicatorId, $groupPath, $rowDatas, $isUpperCase = true) {
        
        $arrRow = $this->db->GetRow("
            SELECT 
                M.*, 
                CASE WHEN M.ALL_CELL_EXPRESSION IS NOT NULL 
                THEN 1 ELSE 0 END AS IS_FROM_MART_EXPRESSION, 
                MW.CODE AS WIDGET_CODE, 
                CASE WHEN 
                    M.SHOW_TYPE = 'rows' 
                THEN ( 
                    SELECT 
                        COUNT(1) 
                    FROM KPI_INDICATOR_INDICATOR_MAP 
                    WHERE SEMANTIC_TYPE_ID = 42 
                        AND SRC_INDICATOR_MAP_ID = M.ID 
                        AND (LOOKUP_META_DATA_ID IS NOT NULL OR TRG_INDICATOR_ID IS NOT NULL)
                ) ELSE 0 END AS IS_ROWS_LOOKUP  
            FROM KPI_INDICATOR_INDICATOR_MAP M 
                LEFT JOIN META_WIDGET MW ON MW.ID = M.WIDGET_ID 
            WHERE M.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                AND LOWER(M.COLUMN_NAME) = ".$this->db->Param(1), 
            array($indicatorId, strtolower($groupPath))
        );
        
        if ($arrRow['TRG_INDICATOR_ID'] && $arrRow['SEMANTIC_TYPE_ID'] == '10000002') {
            Mdform::$pathPrefix = '[subTable]['.$arrRow['COLUMN_NAME'].']';
        } else {
            Mdform::$pathPrefix = '[rows]['.$arrRow['COLUMN_NAME'].']';
        }

        Mdform::$pathSuffix = '[0]';
        
        if ($isUpperCase) {
            $rowDatas = Arr::changeKeyUpper($rowDatas);
        }
        
        $rowDatas = $rowDatas[$groupPath];
        
        $data = self::getKpiIndicatorTemplateModel($indicatorId);
        $render = self::rowsKpiIndicatorTemplate($indicatorId, $data, $arrRow['ID'], $arrRow, $rowDatas);
        
        loadPhpQuery();
        $tblHtml = phpQuery::newDocumentHTML($render);

        return $tblHtml['table.kpi-dtl-table > tbody.tbody']->html();
    }
    
    public function kpiIndicatorHiddenControl($childArr, $arrRow, $templateRow) {
        
        $hidden = array();
        $tblName = self::$indicatorTableName;
        
        foreach ($childArr as $row) {

            $columnName       = $row['COLUMN_NAME'];
            $value            = issetParam($templateRow[$columnName]);
            $controlName      = 'kpiTbl['.$tblName.']['.$columnName.']';
        
            $hidden[] = '<input type="hidden" name="'.$controlName.'" value="'.$value.'">';
        }
        
        return implode('', $hidden);
    }
    
    public function getKpiIndicatorTemplateRows($tableName) {
        
        try {
            $data = $this->db->GetAll("SELECT * FROM $tableName ORDER BY ROW_INDEX ASC");
            return $data;
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getKpiDynamicDataRowModel($dataTableName, $idField, $id, $parameters = array()) {
        
        try {
            
            if (!$id && $parameters) {
                
                $where = '';
                $bindParams = array();
                $paramIndex = 0;
                    
                if (mb_strlen($dataTableName) > 30) {
                    
                    foreach ($parameters as $columnName => $val) {
                        
                        $val = Input::param($val);
                        
                        if (stripos($dataTableName, ':'.$columnName) !== false) {
                            $dataTableName = str_ireplace(':'.$columnName, "'".$val."'", $dataTableName);
                        } else {
                            array_push($bindParams, $val);
                            $where .= Input::param($columnName) . ' = ' . $this->db->Param($paramIndex) . ' AND ';
                            $paramIndex ++;
                        }
                    }
                    
                    if ($where) {
                        $where = trim(rtrim(trim($where), 'AND'));
                        $row = $this->db->GetRow("SELECT * FROM $dataTableName WHERE $where", $bindParams);
                    } else {
                        $row = $this->db->GetRow("SELECT * FROM $dataTableName");
                    }
                    
                } else {

                    foreach ($parameters as $columnName => $val) {

                        array_push($bindParams, Input::param($val));

                        $where .= Input::param($columnName) . ' = ' . $this->db->Param($paramIndex) . ' AND ';

                        $paramIndex ++;
                    }

                    $where = trim(rtrim(trim($where), 'AND'));

                    $row = $this->db->GetRow("SELECT * FROM $dataTableName WHERE $where", $bindParams);
                }
                
            } else {
                $row = $this->db->GetRow("SELECT * FROM $dataTableName WHERE $idField = ".$this->db->Param(0), array($id));
            }

            if ($row) {
                
                if (isset($row['DATA'])) {
                    $data = json_decode($row['DATA'], true);
                    
                    unset($data['ID']);

                    if (is_array($data)) {
                        $row = array_merge($row, $data);
                    }
                }
                
                Mdform::$firstTplId = $row[$idField];
            }
            
            return $row;
            
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getKpiUxFlowDataRowModel($structureIndicatorId, $indicatorId, $postData, $idField, $id) {
        
        $data = $this->model->getKpiIndicatorTemplateModel($structureIndicatorId);

        try {

            if (empty($data)) {
                throw new Exception(''); 
            }
            
            $dataFirstRow = $data[0];
            $dataTableName = $dataFirstRow['TABLE_NAME'];
            
            $row = $this->db->GetRow("SELECT * FROM $dataTableName WHERE $idField = ".$this->db->Param(0), array($id));
            $newRow = [];
        
            if ($row) {
                $uxFlowIndicatorId = issetParam($postData['param']['uxFlowIndicatorId']);
                $uxFlowActionIndicatorId = issetParam($postData['param']['uxFlowActionIndicatorId']);
                $uxflowMaps = (new Mdexpression())->uxFlowExpressionMapping($uxFlowIndicatorId, $uxFlowActionIndicatorId);

                foreach ($uxflowMaps as $rrr) {
                    $newRow[$rrr['trgPath']] = $row[$rrr['srcPath']];
                }
                
                Mdform::$firstTplId = $row[$idField];
            }
            
            return $newRow;
            
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getKpiDynamicActiveDataRowModel($dataTableName, $idField, $id) {
        
        try {
            
            $row = $this->db->GetRow("SELECT * FROM $dataTableName WHERE DELETED_DATE IS NULL AND $idField = ".$this->db->Param(0), array($id));
        
            if ($row) {
                
                if (isset($row['DATA'])) {
                    $data = json_decode($row['DATA'], true);

                    if (is_array($data)) {
                        $row = array_merge($row, $data);
                    }
                }
                
                Mdform::$firstTplId = $row[$idField];
            }
            
            return $row;
            
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getKpiDynamicActiveDataRowsModel($dataTableName, $idField, $id) {
        
        try {
            
            $row = $this->db->GetAll("SELECT * FROM $dataTableName WHERE DELETED_DATE IS NULL AND $idField = ".$this->db->Param(0), array($id));
        
            if ($row) {
                
                if (isset($row['DATA'])) {
                    $data = json_decode($row['DATA'], true);

                    if (is_array($data)) {
                        $row = array_merge($row, $data);
                    }
                }
                
                Mdform::$firstTplId = $row[$idField];
            }
            
            return $row;
            
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function table_exists($db, $table) {
    
        if (isset(self::$kpiTableFields[$table])) {
            
            return self::$kpiTableFields[$table];
        }
        
        try {

            $result = $db->Execute("SELECT * FROM $table WHERE 1 = 0");
            
            $fieldObjs = Arr::objectToArray($result->_fieldobjs);
            
            $fields = array();
            
            foreach ($fieldObjs as $field) {
                $fields[$field['name']] = $field['type'];
            }
            
            self::$kpiTableFields[$table] = $fields;
            
            return $fields;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getKpiDbSchemaName($indicatorId) {
        
        $dbSchemaName = $this->db->GetOne("
            SELECT 
                K.SCHEMA_NAME 
            FROM KPI_INDICATOR I 
                INNER JOIN KPI_INDICATOR_CATEGORY C ON I.ID = C.INDICATOR_ID 
                INNER JOIN KPI_INDICATOR K ON C.CATEGORY_ID = K.ID 
            WHERE K.SCHEMA_NAME IS NOT NULL 
                AND I.ID = ".$this->db->Param(0), 
            array($indicatorId)
        );
        
        if ($dbSchemaName) {
            
            $dbSchemaName = rtrim($dbSchemaName, '.');
            return $dbSchemaName.'.';
            
        } elseif ($schemaName = Config::getFromCache('kpiDbSchemaName')) {
            
            $schemaName = rtrim($schemaName, '.');
            return $schemaName.'.';
        }
        
        return null;
    }
    
    public function getKpiColumnTypesModel($indicatorId) {
        
        if (!isset(self::$getKpiColumnTypes[$indicatorId])) {
            
            $data = self::getKpiIndicatorTemplateModel($indicatorId);
            $headerParent = $types = array();

            foreach ($data as $row) {
                
                if ($row['SHOW_TYPE'] != 'rows') {
                    
                    if ($row['SHOW_TYPE'] != 'label' && $row['PARENT_ID'] == $indicatorId && $row['COLUMN_NAME']) {
                        
                        $types[$row['COLUMN_NAME']] = array(
                            'type' => $row['SHOW_TYPE'], 
                            'isUnique' => $row['IS_UNIQUE']
                        );
                        
                    } elseif ($row['SHOW_TYPE'] == 'label' && $row['PARENT_ID'] == $indicatorId) {
                        $headerParent[] = $row['ID'];
                    }
                } elseif ($row['SHOW_TYPE'] == 'rows') {
                    
                    $types[$row['COLUMN_NAME']] = array(
                        'type' => $row['SHOW_TYPE'], 
                        'trgIndicatorId' => $row['FILTER_INDICATOR_ID'], 
                        'semanticTypeName' => $row['SEMANTIC_TYPE_NAME'], 
                        'isUnique' => $row['IS_UNIQUE']
                    );   
                }
            }

            if ($headerParent) {
                
                foreach ($headerParent as $parentId) {    
                    foreach ($data as $row) {
                        if ($row['PARENT_ID'] == $parentId && $row['COLUMN_NAME']) {
                            $types[$row['COLUMN_NAME']] = array(
                                'type' => $row['SHOW_TYPE'], 
                                'isUnique' => $row['IS_UNIQUE']
                            );   
                        }
                    }
                }
            }
        
            self::$getKpiColumnTypes[$indicatorId] = $types;
        } 
        
        return self::$getKpiColumnTypes[$indicatorId];
    }
    
    public function isCheckSystemTable($tblName, $isTblCreated = false) {
        
        $tblName = strtolower($tblName);
        $firstTwoChar = substr($tblName, 0, 2);
        $firstFourChar = substr($tblName, 0, 4);
        
        if ($firstTwoChar == 'v_' 
            || $firstTwoChar == 'd_' 
            || $firstTwoChar == 't_' 
            || $firstFourChar == 'kpi_' 
            || $firstFourChar == 'cam_' 
            || strpos($tblName, '.v_') !== false 
            || strpos($tblName, '.d_') !== false 
            || strpos($tblName, '.t_') !== false 
            || strpos($tblName, '.kpi_') !== false) {
            
            return false;
        }
        
        return true;
    }
    
    public function saveKpiDynamicDataModel($sourceRecordId = null, $postArrData = array()) {
        
        $postData      = $postArrData ? $postArrData : Input::postData();
        $isExcelImport = Input::numeric('isExcelImport');
        
        try {
            
            $uxFlowActionIndicatorId = issetVar($postData['uxFlowActionIndicatorId']);
            $uxFlowIndicatorId = issetVar($postData['uxFlowIndicatorId']);
            $kpiMainIndicatorId = issetVar($postData['kpiStructureIndicatorId']) ? $postData['kpiStructureIndicatorId'] : issetVar($postData['kpiMainIndicatorId']);
            $configRow          = self::getKpiIndicatorRowModel($kpiMainIndicatorId);
            $sessionUserKeyId   = Ue::sessionUserKeyId();
            
            $configRow['isIgnoreStandardFields'] = true;

            $columnsData = self::getKpiIndicatorColumnsModel($kpiMainIndicatorId, $configRow);
            $fieldConfig = self::getKpiIndicatorIdFieldModel($kpiMainIndicatorId, $columnsData);
                    
            $kpiDataTblName = $configRow['TABLE_NAME'];
            $namePattern    = $configRow['NAME_PATTERN'];
            $kpiTypeId      = $configRow['KPI_TYPE_ID'];
            $kpiTblIdField  = issetVar($postData['customIdField']) ? $postData['customIdField'] : ($fieldConfig['idField'] ? $fieldConfig['idField'] : 'ID');
            
            $schemaName = self::getKpiDbSchemaName($kpiMainIndicatorId);
            $tblName = $kpiDataTblName ? $kpiDataTblName : $schemaName . 'V_'.$kpiMainIndicatorId;
            
            $isTblCreated = self::table_exists($this->db, $tblName);
            $isIgnoreAlter = self::isCheckSystemTable($tblName, $isTblCreated);
            
            $columnTypes = self::getKpiColumnTypesModel($kpiMainIndicatorId);
            
            $kpiTblId = issetVar($postData['kpiTblId']);
            $kpiTbl   = $postData['kpiTbl'];
            $fileData = Input::fileData();
            
            if (isset($fileData['kpiFile'])) {
                $fileParamData = $fileData['kpiFile'];
            }
            
            $saveData = $dataClob = $dbField = $clobField = $dbIndex = $subTables = $addonForm = array();
            $kpiTranslationRow = '';

            foreach ($kpiTbl as $columnName => $val) {

                if (!is_numeric($columnName) && substr($columnName, -5) != '_DESC' && substr($columnName, -12) != '_translation') {
                    
                    $fieldType = issetDefaultVal($columnTypes[$columnName]['type'], 'string');
                    
                    if ($columnName == 'rows') {

                        foreach ($val as $subColumnName => $subDtlVal) {
                            
                            $columnJson = isset($subDtlVal['columnJson']) ? $subDtlVal['columnJson'] : array();
                            $tmpJson = array();
                            
                            unset($subDtlVal['columnJson']);
                            
                            foreach ($subDtlVal as $subKey => $subVal) {
                                
                                foreach ($subVal as $key => $val) {
                                    
                                    if (isset($columnJson[$key]) && !isset($tmpJson[$key])) {
                                        $jsonVal = json_decode($columnJson[$key], true);
                                        $tmpJson[$key] = $jsonVal;
                                    }
                                    
                                    if (isset($fileParamData['name']['rows'][$subColumnName][$subKey][$key])) {
                                        
                                        $fileAttr['name'] = $fileParamData['name']['rows'][$subColumnName][$subKey][$key];
                                        $fileAttr['tmp_name'] = $fileParamData['tmp_name']['rows'][$subColumnName][$subKey][$key];
                                        $fileAttr['size'] = $fileParamData['size']['rows'][$subColumnName][$subKey][$key];
                                        $fileAttr['type'] = $fileParamData['type']['rows'][$subColumnName][$subKey][$key];
                                        
                                        $uploadResult = Mdwebservice::bpFileUpload(array('newFileName' => 'mv_'.getUIDAdd($key), 'META_TYPE_CODE' => 'file'), $fileAttr);

                                        if ($uploadResult) {
                                            $val = $uploadResult['path'] . $uploadResult['newname'];
                                            array_push(FileUpload::$uploadedFiles, $val);
                                        } 
                                    }
                                    
                                    $tmpJson[$key][$subKey] = Input::param($val);
                                    
                                    $columnJson[$key] = $tmpJson[$key];
                                }
                            }

                            $dataClob[$subColumnName] = $columnJson;
                        }

                    } elseif ($columnName == 'subTable') {
                        
                        $removeKeys = array('ID', 'CREATED_DATE', 'CREATED_USER_ID', 'CREATED_USER_NAME', 'MODIFIED_DATE', 'MODIFIED_USER_ID', 'MODIFIED_USER_NAME');
                        
                        foreach ($val as $subColumnName => $subDtlVal) {
                            
                            $columnJson = isset($subDtlVal['columnJson']) ? $subDtlVal['columnJson'] : array();
                            $tmpJson = array();
                            
                            unset($subDtlVal['columnJson']);
                            
                            foreach ($subDtlVal as $subKey => $subVal) {
                                
                                foreach ($subVal as $key => $val) {
                                    
                                    if (isset($columnJson[$key]) && !isset($tmpJson[$key])) {
                                        
                                        $jsonKeyVal = json_decode($columnJson[$key], true);
                                        
                                        if (isset($jsonKeyVal['ROW_INDEX'])) {
                                            
                                            $jsonKeyVal['ROW_ID'] = $jsonKeyVal['ID'];
                                            $jsonKeyVal = array_diff_key($jsonKeyVal, array_flip($removeKeys));
                                            
                                            foreach ($jsonKeyVal as $jsonKey => $jsonVal) {
                                                if (strpos($jsonVal, '"cellId"') !== false) {
                                                    unset($jsonKeyVal[$jsonKey]);
                                                }
                                            }
                                        }
                                        
                                        $tmpJson[$key] = $jsonKeyVal;
                                    }
                                    
                                    $tmpJson[$key][$subKey] = $val;
                                    
                                    $columnJson[$key] = $tmpJson[$key];
                                }
                            }
                            
                            $subTables[$subColumnName] = $columnJson;
                        }
                        
                    } elseif ($columnName == 'addonForm') {
                        
                        $addonForm = $val;
                        
                    } elseif ($columnName == 'metaDmRecordMapsSubForm') {
                        
                        continue;
                        
                    } elseif ($fieldType == 'rows') {
                        
                        $trgIndicatorId = issetParam($columnTypes[$columnName]['trgIndicatorId']);
                        $semanticTypeName = issetParam($columnTypes[$columnName]['semanticTypeName']);
                        
                        if (is_array($val)) {
                            foreach ($val as $v => $vl) {
                                if (issetParam($vl['rowstate']) == 'removed') {
                                    unset($val[$v]);
                                }
                            }
                            $val = array_values($val);
                        }
                        
                        if ($trgIndicatorId && $semanticTypeName == 'Sub хүснэгт') {
                            $subTables[$columnName] = $val;
                        } else {
                            $dataClob[$columnName] = $val;
                        }
                        
                    } elseif (is_array($val) && $fieldType != 'multicombo') {
                        
                        $dataClob[$columnName] = $val;
                        
                    } else {
                        
                        $isUnique = issetParam($columnTypes[$columnName]['isUnique']);
                        
                        if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$columnName]))) {
                            
                            $dbField[] = array('type' => $fieldType, 'name' => $columnName);
                        } 
                        
                        if (array_key_exists($columnName . '_DESC', $kpiTbl)) {
                            
                            $columnNameDesc = $columnName . '_DESC';
                            
                            if ($fieldType == 'multicombo') {
                                $val = implode(',', $val);
                            }
                            
                            $saveData[$columnName] = Input::param($val);
                            $saveData[$columnNameDesc] = Input::param($kpiTbl[$columnName . '_DESC']);
                            $dataClob[$columnName] = Input::param($val);
                            
                            unset($kpiTbl[$columnName . '_DESC']);
                            
                            if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$columnNameDesc]))) {
                            
                                $dbField[] = array('type' => 'varchar', 'name' => $columnNameDesc);
                            } 
                        
                        } elseif (array_key_exists($columnName . '_translation', $kpiTbl)) {
                            
                            $saveData[$columnName] = Input::param($val);                            
                            $kpiTranslationRow .= '"'.$columnName.'":'.html_entity_decode(Input::param($kpiTbl[$columnName . '_translation']), ENT_QUOTES).',';
                                                        
                            unset($kpiTbl[$columnName . '_translation']);
                            
                            if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated['TRANSLATION_VALUE']))) {
                            
                                $dbField[] = array('type' => 'clob', 'name' => 'TRANSLATION_VALUE');
                            } 
                            
                        } else {
                            
                            $val = Input::param($val);
                            
                            if (isset($fileParamData['name'][$columnName]) && is_uploaded_file($fileParamData['tmp_name'][$columnName])) {
                                    
                                $fileAttr['name'] = $fileParamData['name'][$columnName];
                                $fileAttr['tmp_name'] = $fileParamData['tmp_name'][$columnName];
                                $fileAttr['size'] = $fileParamData['size'][$columnName];
                                $fileAttr['type'] = $fileParamData['type'][$columnName];

                                $uploadResult = Mdwebservice::bpFileUpload(array('ID' => $kpiMainIndicatorId, 'META_TYPE_CODE' => 'file'), $fileAttr);

                                if ($uploadResult) {
                                    $val = $uploadResult['path'] . $uploadResult['newname'];
                                    array_push(FileUpload::$uploadedFiles, $val);
                                }
                            } 
                            
                            if ($fieldType == 'time' && $val) {
                                $val = '1999-01-01 '.Input::param($val).':00';
                            }
                            
                            $dataClob[$columnName] = $val;
                            
                            if ($fieldType == 'clob' || $fieldType == 'text_editor' || $fieldType == 'html_clicktoedit') {
                                $clobField[$columnName] = $val;
                            } else {
                                $saveData[$columnName] = $val;
                            }
                        }
                        
                        if ($isUnique == '1') {
                            $dbIndex[] = $columnName;
                        }
                    }
                }
            }

            if ($kpiTranslationRow) {
                $clobField['TRANSLATION_VALUE'] = '{"value":{'.rtrim($kpiTranslationRow, ',').'}}';
            }
            
            if ($isIgnoreAlter == false) {
            
                if ($isTblCreated == false) {

                    $createTblStatus = self::dbCreatedTblKpiDynamic($tblName, $dbField);

                    if ($createTblStatus['status'] == 'error') {
                        return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                    } else {
                        self::updateKpiIndicatorTblName($kpiMainIndicatorId, $tblName);
                    }

                } else {

                    $standardFields = self::kpiDynamicTblStandardFields();

                    foreach ($standardFields as $standardField) { 

                        if (!isset($isTblCreated[$standardField['name']])) {

                            $dbField[] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                        }
                    }

                    if ($dbField) {

                        $alterTblStatus = self::dbAlterTblKpiDynamic($tblName, $dbField);

                        if ($alterTblStatus['status'] == 'error') {
                            return array('status' => 'error', 'message' => 'Alter table: ' . $alterTblStatus['message']);
                        }
                    }
                }
                
                if ($dbIndex) {
                    self::dbAlterTblKpiIndex($kpiMainIndicatorId, $tblName, $dbIndex);
                }
            }
            
            if ($namePattern) {
                
                preg_match_all('/\[(.*?)\]/', $namePattern, $namePatternCatch);
                
                if (isset($namePatternCatch[0][0])) {
                    foreach ($namePatternCatch[1] as $namePath) {
                        $namePattern = str_ireplace("[$namePath]", issetParam($saveData[$namePath]), $namePattern);
                    }
                }
                
                $saveData['NAME'] = $namePattern;
                
            } elseif ($processNameInput = issetVar($_POST['param']['name'])) {
                
                $saveData['NAME'] = $processNameInput;
            }
            
            $isUpdate      = false;
            $sessionValues = Session::get(SESSION_PREFIX . 'sessionValues');
            $sessionName   = issetDefaultVal($sessionValues['sessionusername'], Ue::getSessionPersonWithLastName());
            
            if ($kpiTypeId == '1191') {
                $response = self::runKpiStoredProcedure($kpiMainIndicatorId, $saveData);
                return $response;
            }

            if ($uxFlowActionIndicatorId) {
                $saveData = self::getKpiUxFlowSaveMappingModel($uxFlowIndicatorId, $uxFlowActionIndicatorId, $saveData);
            }
            
            /**
             * Start Microflow expression
             */
            if (!isset($postData['isMicroFlow']) && !$isExcelImport) { 
                $this->db->BeginTrans(); 
                
                $getExpIndicatorId = self::getParentIndicatorFunctionModel(issetVar($postData['kpiCrudIndicatorId']));
                $expIndicatorId = '';
                
                if ($getExpIndicatorId) {                    
                    $expIndicatorId = $getExpIndicatorId['SRC_RECORD_ID'];                    
                } elseif ($kpiTypeId === '2009') {
                    $expIndicatorId = $kpiMainIndicatorId;
                }                
                
                if ($expIndicatorId) {
                    $flowData = $saveData;
                    $flowData['indicatorinfo_id'] = $kpiMainIndicatorId;
                    $microFlowResponce = (new Mdexpression())->executeMicroFlowExpression($expIndicatorId, $flowData);            
                    if ($microFlowResponce != '_microflow_success') {
                        $this->db->RollbackTrans();
                        
                        if (issetParam($microFlowResponce['status']) === 'microflowConfirmation') {
                            return $microFlowResponce;
                        }
                        
                        if (isset($microFlowResponce['data']) && issetParam($microFlowResponce['data']['type']) === 'message') {
                            $messageStr = '<ul style="padding-left: 20px;">';
                            foreach ($microFlowResponce['data']['result'] as $messRow)
                                $messageStr .= '<li>'.$messRow['message'].'</li>';
                            $messageStr .= '</ul>';
                        }
                        if (isset($messageStr)) {
                            $microFlowResponce = $messageStr;
                        }
                        $response = array('status' => 'error', 'message' => $microFlowResponce);
                        return $response;
                    }                               
                }          

                if ($kpiTypeId === '16641793815766') {
                    $getTypeIndicatorId = $this->db->GetOne("SELECT RELATED_INDICATOR_ID FROM KPI_TYPE WHERE ID = ".$this->db->Param(0), array($kpiTypeId));

                    $flowData = $saveData;
                    $flowData['indicatorinfo_id'] = $kpiMainIndicatorId;              
                    $microFlowResponce = (new Mdexpression())->executeMicroFlowExpression($getTypeIndicatorId, $flowData);            
                    if ($microFlowResponce != '_microflow_success') {
                        $this->db->RollbackTrans();
                        
                        if (issetParam($microFlowResponce['status']) === 'microflowConfirmation') {
                            return $microFlowResponce;
                        }
                        
                        if (isset($microFlowResponce['data']) && issetParam($microFlowResponce['data']['type']) === 'message') {
                            $messageStr = '<ul style="padding-left: 20px;">';
                            foreach ($microFlowResponce['data']['result'] as $messRow)
                                $messageStr .= '<li>'.$messRow['message'].'</li>';
                            $messageStr .= '</ul>';
                        }
                        if (isset($messageStr)) {
                            $microFlowResponce = $messageStr;
                        }
                        $response = array('status' => 'error', 'message' => $microFlowResponce);
                        return $response;
                    }                           
                }                 
            }

            if ($kpiTypeId == '2009') {
                $response = array('status' => 'success', 'message' => Lang::line('msg_save_success'), 'uniqId' => getUID());             
                return $response;
            }            

            if (isset($getExpIndicatorId) && isset($_POST['isMicroFlowSelfSave']) && !$isExcelImport) { 
                $this->db->CommitTrans();
                $response = array('status' => 'success', 'message' => Lang::line('msg_save_success'), 'uniqId' => getUID());             
                return $response;
            }            
            /**
             * End Microflow expression
             */
            
            $sfId = issetVar($postData['sf']['ID']);
            
            if (is_numeric($sfId) && !$kpiTblId) {
                $kpiTblId = $sfId;
                $isSetRowIdByExpression = true;
            }
            
            if ($configRow['COUNT_UNIQUE']) {
                $checkUnique = self::beforeSaveCheckUnique($kpiMainIndicatorId, $tblName, $kpiTblIdField, $columnsData, $saveData, $kpiTblId);
                
                if ($checkUnique['status'] != 'success') {
                    return $checkUnique;
                }
            }
            
            if (!$kpiTblId) {
                
                $rowId = getUIDAdd(self::$uniqIdIndex);
                $isUseCompanyDepartmentId = $configRow['IS_USE_COMPANY_DEPARTMENT_ID'];
                $wfmStructureId = $configRow['STRUCTURE_INDICATOR_ID'] ? $configRow['STRUCTURE_INDICATOR_ID'] : $kpiMainIndicatorId;
                
                if ($isUseCompanyDepartmentId && !array_key_exists('COMPANY_DEPARTMENT_ID', $saveData)) { 

                    $sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid']);

                    if ($sessionCompanyDepartmentId && $sessionCompanyDepartmentId != '1') {
                        $saveData['COMPANY_DEPARTMENT_ID'] = $sessionCompanyDepartmentId;
                    }
                }
                
                $saveData[$kpiTblIdField] = $rowId;
                
                $saveData['CREATED_DATE'] = Date::currentDate('Y-m-d H:i:s');
                $saveData['CREATED_USER_ID'] = $sessionUserKeyId;
                $saveData['CREATED_USER_NAME'] = $sessionName;
                
                if (isset($isTblCreated['INDICATOR_ID'])) {
                    
                    $saveData['INDICATOR_ID'] = $kpiMainIndicatorId;

                    if ($sourceRecordId && isset($isTblCreated['SRC_RECORD_ID'])) {
                        $saveData['SRC_RECORD_ID'] = $sourceRecordId;
                    }
                }
                    
                if ($isIgnoreAlter == false) {

                    if ($configRow['IS_USE_WORKFLOW'] == '1' && !Input::numeric('isRunAutoSave')) {

                        $this->load->model('mdobject', 'middleware/models/');

                        $dataRow = $saveData;
                        $dataRow['isIndicator'] = 1;

                        $startWfmStatusId = $this->model->getStartWfmStatusModel($wfmStructureId, $dataRow);

                        if ($startWfmStatusId) {
                            $setStartWfmStatusId = $startWfmStatusId;
                        } else {
                            throw new Exception('Эхлэлийн төлөв олдсонгүй!'); 
                        }
                    }
                }
                
                $rs = self::dbAutoExecuteMetaVerseData($tblName, $saveData);
                
                if ($rs && isset($setStartWfmStatusId)) {
                    $setWfmStatusArr = array('statusId' => $setStartWfmStatusId, 'metaDataId' => $wfmStructureId, 'id' => $rowId);
                }
            
            } else {
                
                $rowId = $kpiTblId;
                $isUpdate = true;
                    
                $saveData['MODIFIED_DATE'] = Date::currentDate('Y-m-d H:i:s');
                $saveData['MODIFIED_USER_ID'] = $sessionUserKeyId;
                $saveData['MODIFIED_USER_NAME'] = $sessionName;
                
                if ($sourceRecordId) {
                    $kpiTblIdField = 'SRC_RECORD_ID';
                    $rowId = $rowId;
                }
                
                self::dbAutoExecuteMetaVerseData($tblName, $saveData, $kpiTblIdField.' = '.$rowId);
            }
            
            if ($isIgnoreAlter == false && $dataClob) {
                $this->db->UpdateClob($tblName, 'DATA', json_encode($dataClob, JSON_UNESCAPED_UNICODE), $kpiTblIdField.' = '.$rowId);
            }
            
            if ($clobField) {
                foreach ($clobField as $clobFieldName => $clobFieldVal) {
                    $this->db->UpdateClob($tblName, $clobFieldName, $clobFieldVal, $kpiTblIdField.' = '.$rowId);
                }
            }
            
            if (!$sourceRecordId) {
                $sourceRecordId = $rowId;
            }
            
            self::kpiSaveMetaDmRecordMap($kpiMainIndicatorId, $rowId, $saveData);
            
            if ($kpiTypeId == '2006') {
                
                $metaProcessResponse = self::runKpiIndicatorInputsToMetaProcess($kpiMainIndicatorId, $tblName, $kpiTblIdField, $rowId, $dataClob, $isUpdate);
                
                if ($metaProcessResponse['status'] != 'success') {
                    throw new Exception($metaProcessResponse['message']); 
                }
            }
            
            if (Input::postCheck('recordMap')) {
                
                $recordMap      = Input::post('recordMap');
                $srcIndicatorId = $recordMap['srcIndicatorId'];
                $srcRecordId    = $recordMap['srcRecordId'];
                $srcPath        = $recordMap['srcPath'];
            
                try {

                    $insertMapRow = array(
                        'ID'                   => getUIDAdd(3), 
                        'SRC_REF_STRUCTURE_ID' => $srcIndicatorId, 
                        'TRG_REF_STRUCTURE_ID' => $kpiMainIndicatorId, 
                        'SRC_RECORD_ID'        => $srcRecordId, 
                        'TRG_RECORD_ID'        => $rowId, 
                        'SRC_NAME'             => $srcPath, 
                        'CREATED_DATE'         => Date::currentDate(), 
                        'CREATED_USER_ID'      => $sessionUserKeyId
                    );

                    $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);

                } catch (Exception $ex) {
                    
                    throw new Exception($ex->getMessage()); 
                }
                
            } else {
                
                if (Input::postCheck('hiddenParams')) {
                    
                    if ($isUpdate == false) {
                        
                        $hiddenParams = Input::post('hiddenParams');
                        $hiddenParams = Crypt::decrypt($hiddenParams);
                        $hiddenParams = @json_decode($hiddenParams, true);

                        if (isset($hiddenParams['mainIndicatorId']) && $mapId = Input::numeric('mapId')) {

                            try {

                                $srcIndicatorId = $hiddenParams['mainIndicatorId'];
                                $srcRecordId    = $hiddenParams['id'];
                                $srcPath        = $this->db->GetOne("SELECT COLUMN_NAME FROM KPI_INDICATOR_INDICATOR_MAP WHERE ID = ".$this->db->Param(0), array($mapId));

                                $insertMapRow = array(
                                    'ID'                   => getUIDAdd(3), 
                                    'SRC_REF_STRUCTURE_ID' => $srcIndicatorId, 
                                    'TRG_REF_STRUCTURE_ID' => $kpiMainIndicatorId, 
                                    'SRC_RECORD_ID'        => $srcRecordId, 
                                    'TRG_RECORD_ID'        => $rowId, 
                                    'SRC_NAME'             => $srcPath, 
                                    'CREATED_DATE'         => Date::currentDate(), 
                                    'CREATED_USER_ID'      => $sessionUserKeyId
                                );

                                $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);

                            } catch (Exception $ex) {

                                throw new Exception($ex->getMessage()); 
                            }

                        } else {
                            throw new Exception('Invalid request!'); 
                        }
                    }
                    
                } elseif (Input::numeric('isIgnoreRemoveRecordMap') != 1 && !isset($isSetRowIdByExpression)) {
                
                    $recordMapDatas = $this->db->GetAll("
                        SELECT 
                            T1.TABLE_NAME, 
                            T0.TRG_RECORD_ID
                        FROM META_DM_RECORD_MAP T0 
                            INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_REF_STRUCTURE_ID 
                        WHERE T0.SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                            AND T0.SRC_RECORD_ID = ".$this->db->Param(1)." 
                            AND T0.SRC_NAME IS NOT NULL", 
                        array($kpiMainIndicatorId, $rowId));

                    if ($recordMapDatas) {

                        $this->db->Execute("
                            DELETE 
                            FROM META_DM_RECORD_MAP 
                            WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                                AND SRC_RECORD_ID = ".$this->db->Param(1)." 
                                AND SRC_NAME IS NOT NULL", 
                            array($kpiMainIndicatorId, $rowId)
                        ); 

                        foreach ($recordMapDatas as $recordMapData) {

                            if ($recordMapData['TABLE_NAME'] && $recordMapData['TRG_RECORD_ID']) {
                                $this->db->Execute("DELETE FROM ".$recordMapData['TABLE_NAME']." WHERE ID = ".$this->db->Param(0), array($recordMapData['TRG_RECORD_ID'])); 
                            }
                        }
                    }
                }
            }
            
            if ($subTables) {
                
                unset($_POST['isKpiComponent']);
                unset($_POST['kpiTblId']);
                unset($_POST['recordMap']);
                unset($_POST['sf']);
                
                foreach ($subTables as $subPath => $subRows) {
                    
                    $subConfigRow = $this->db->GetRow("
                        SELECT 
                            T1.TABLE_NAME, 
                            T1.ID AS MAIN_INDICATOR_ID 
                        FROM KPI_INDICATOR_INDICATOR_MAP T0 
                            INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                        WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                            AND T0.COLUMN_NAME = ".$this->db->Param(1), 
                        array($kpiMainIndicatorId, $subPath)
                    );
                    
                    if ($subConfigRow) {

                        $_POST['kpiMainIndicatorId'] = $subConfigRow['MAIN_INDICATOR_ID'];
                        $_POST['kpiDataTblName']     = $subConfigRow['TABLE_NAME'];
                        $_POST['kpiTblIdField']      = 'ID';
                        $_POST['isExcelImport']      = 1;
                        $_POST['isRunAutoSave']      = 1;
                        
                        $_POST['recordMap']['srcIndicatorId'] = $kpiMainIndicatorId;
                        $_POST['recordMap']['srcPath']        = $subPath;
                        $_POST['recordMap']['srcRecordId']    = $rowId;
                        
                        foreach ($subRows as $s => $subRow) {
                            
                            unset($_POST['kpiTbl']);
                            $_POST['kpiTbl'] = $subRow;
                            
                            self::$uniqIdIndex = $s;
                            
                            $result = self::saveKpiDynamicDataModel($rowId);
                            
                            if ($result['status'] != 'success') {
                                throw new Exception($result['message']); 
                            }
                        }
                        
                        unset($_POST['isRunAutoSave']);
                    }
                }
            }
            
            if ($addonForm) {
                
                unset($_POST['isKpiComponent']);
                unset($_POST['kpiTblId']);
                unset($_POST['recordMap']);
                
                $a = 1;
                
                foreach ($addonForm as $addonIndicatorId => $addonParams) {
                    
                    unset($_FILES);
                    
                    if (isset($fileParamData) && isset($fileParamData['name']['addonForm'][$addonIndicatorId])) {
                        
                        $addonFiles = $fileParamData['name']['addonForm'][$addonIndicatorId];
                        
                        foreach ($addonFiles as $addonFileCol => $addonFileName) {
                            $_FILES['kpiFile']['name'][$addonFileCol] = $addonFileName;
                            $_FILES['kpiFile']['type'][$addonFileCol] = $fileParamData['type']['addonForm'][$addonIndicatorId][$addonFileCol];
                            $_FILES['kpiFile']['tmp_name'][$addonFileCol] = $fileParamData['tmp_name']['addonForm'][$addonIndicatorId][$addonFileCol];
                            $_FILES['kpiFile']['error'][$addonFileCol] = $fileParamData['error']['addonForm'][$addonIndicatorId][$addonFileCol];
                            $_FILES['kpiFile']['size'][$addonFileCol] = $fileParamData['size']['addonForm'][$addonIndicatorId][$addonFileCol];
                        }
                    }
                    
                    $addonRecordId = Input::param($_POST['kpiAddonForm'][$addonIndicatorId]);
                    $addonIndicatorIdArr = explode('_', $addonIndicatorId);
                    $addonIndicatorId = $addonIndicatorIdArr[0];
                    
                    $_POST['kpiMainIndicatorId'] = $addonIndicatorId;
                    $_POST['kpiTblIdField']      = 'ID';
                    $_POST['isExcelImport']      = 1;
                    $_POST['isRunAutoSave']      = 1;
                    $_POST['kpiTblId']           = $addonRecordId;

                    unset($_POST['kpiTbl']);
                    $_POST['kpiTbl'] = $addonParams;

                    self::$uniqIdIndex = $a;

                    $result = self::saveKpiDynamicDataModel($addonRecordId ? null : $rowId);

                    if ($result['status'] != 'success') {
                        
                        throw new Exception($result['message']); 
                        
                    } else if (!$addonRecordId) {
                        
                        $insertMapRow = array(
                            'ID'                   => getUIDAdd($a), 
                            'SRC_REF_STRUCTURE_ID' => $kpiMainIndicatorId, 
                            'TRG_REF_STRUCTURE_ID' => $addonIndicatorId, 
                            'SRC_RECORD_ID'        => $rowId, 
                            'TRG_RECORD_ID'        => $result['rowId'], 
                            'CREATED_DATE'         => Date::currentDate(), 
                            'CREATED_USER_ID'      => $sessionUserKeyId
                        );

                        $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);
                    }
                    
                    unset($_POST['isRunAutoSave']);
                    
                    $a ++;
                }
            }
            
            if (!isset($postData['isMicroFlow']) && !$isExcelImport) { 
                $this->db->CommitTrans();
            }             
            
            self::runGenerateKpiDataMartByIndicatorId($kpiMainIndicatorId, $sourceRecordId);
            self::runGenerateKpiRelationDataMartByIndicatorId($kpiMainIndicatorId);
            
            if (Input::postCheck('wfmStatusParams') && !Input::numeric('isRunAutoSave')) {
                self::mvChangeWfmStatus($configRow, $rowId);
            }
            
            if ($clobField) {
                $saveData = array_merge($saveData, $clobField);
            }
            
            $response = array(
                'status' => 'success', 
                'message' => Lang::line('msg_save_success'), 
                'indicatorId' => $kpiMainIndicatorId, 
                'id' => $rowId, 
                'rowId' => $rowId, 
                'uniqId' => getUID(), 
                'result' => $saveData
            );
            
            if (isset($setWfmStatusArr)) {
                
                $_POST['newWfmStatusid'] = $setWfmStatusArr['statusId'];
                $_POST['metaDataId'] = $setWfmStatusArr['metaDataId'];
                $_POST['dataRow'] = array('id' => $setWfmStatusArr['id']);
                $_POST['description'] = '';
                $_POST['isIndicator'] = 1;
                
                $this->load->model('mdobject', 'middleware/models/');
                $statusResponse = $this->model->setRowWfmStatusModel();
            }
            
            (new Mdwebservice)->saveBpAddOn($kpiMainIndicatorId, $rowId);
            Mdform::clearCacheData($kpiMainIndicatorId);
            
        } catch (Exception $ex) {
            
            if (!isset($postData['isMicroFlow']) && !$isExcelImport) { 
                $this->db->RollbackTrans();
            }
            
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function dbAutoExecuteMetaVerseData($tblName, $saveData, $where = '') {
        try {
            
            if ($where) {
                $this->db->AutoExecute($tblName, $saveData, 'UPDATE', $where);
            } else {
                $this->db->AutoExecute($tblName, $saveData);
            }
            
            return true;
            
        } catch (Exception $ex) {
            
            if (self::dbErrorFix($ex->msg)) {
                return self::dbAutoExecuteMetaVerseData($tblName, $saveData, $where);
            } else {
                throw new Exception($ex->getMessage());
            }
        }
    }
    
    public function dbExecuteMetaVerseData($statement) {
        try {
            
            $this->db->Execute($statement);
            return true;
            
        } catch (Exception $ex) {
            
            if (self::dbErrorFix($ex->msg)) {
                return self::dbExecuteMetaVerseData($statement);
            } else {
                throw new Exception($ex->getMessage());
            }
        }
    }
    
    public function dbErrorFix($msg) {
        
        if (strpos($msg, 'value too large for column') !== false) {
            preg_match_all('/"(.*?)"/i', $msg, $matchs);
            
            if (isset($matchs[1][2])) {
                $schemaName = $matchs[1][0];
                $tableName = $matchs[1][1];
                $columnName = $matchs[1][2];
                
                $this->db->Execute("ALTER TABLE $schemaName.$tableName MODIFY ($columnName VARCHAR2(4000 CHAR))");
                
                return true;
            }
        }
        
        return false;
    }
    
    public function mvChangeWfmStatus($configRow, $rowId) {
        
        $wfmStatusParams = @json_decode($_POST['wfmStatusParams'], true);
        
        if (isset($wfmStatusParams['wfmstatusid'])) {
            
            $mainIndicatorId = Input::param($wfmStatusParams['mainindicatorid']);
            
            if ($mainIndicatorId != $configRow['ID']) {
                $configRow = self::getKpiIndicatorRowModel($mainIndicatorId);
            }
            
            $metaDataId = $configRow['STRUCTURE_INDICATOR_ID'] ? $configRow['STRUCTURE_INDICATOR_ID'] : $configRow['ID'];
            
            $this->load->model('mdobject', 'middleware/models/');
            
            $_POST['newWfmStatusid'] = Input::param($wfmStatusParams['wfmstatusid']);
            $_POST['metaDataId'] = $metaDataId;
            $_POST['dataRow'] = array('id' => Input::param($wfmStatusParams['recordid']), 'wfmstatusid' => Input::param($wfmStatusParams['currentwfmstatusid']));
            $_POST['description'] = '';
            $_POST['isIndicator'] = 1;

            $response = $this->model->setRowWfmStatusModel();
        }
    }
    
    public function beforeSaveCheckUnique($indicatorId, $tblName, $kpiTblIdField, $columnsData, $saveData, $kpiTblId) {
        
        try {
            
            $uniqueColumn = array();
            
            foreach ($columnsData as $row) {
                if ($row['IS_UNIQUE'] == '1' && ($row['SHOW_TYPE'] != 'rows' && $row['SHOW_TYPE'] != 'row' && $row['SHOW_TYPE'] != 'label' && $row['SHOW_TYPE'] != 'config')) {
                    $uniqueColumn[$row['COLUMN_NAME']] = array('showType' => $row['SHOW_TYPE'], 'labelName' => $row['LABEL_NAME']);
                }
            }
            
            $p = 0;
            $whereClause = $uniqueColumns = '';
            $bindParam = array();
            
            foreach ($uniqueColumn as $colName => $col) {
                
                if (!isset($saveData[$colName])) {
                    $ignoreCheckUnique = true;
                    break;
                    
                } elseif ($saveData[$colName] == '') {
                    $ignoreCheckUnique = true;
                    break;
                    
                } else {
                    
                    $showType = $col['showType'];
                    
                    if ($showType == 'date' || $showType == 'datetime' 
                        || $showType == 'combo' || $showType == 'radio' 
                        || $showType == 'popup' || $showType == 'number' 
                        || $showType == 'long' || $showType == 'decimal' 
                        || $showType == 'decimal_zero' || $showType == 'bigdecimal' 
                        || $showType == 'percent') {
                        
                        $whereClause .= "$colName = " . $this->db->Param($p) . ' AND ';
                        $bindParam[] = $saveData[$colName];
                        
                    } else {
                        $whereClause .= "LOWER($colName) = " . $this->db->Param($p) . ' AND ';
                        $bindParam[] = Str::lower($saveData[$colName]);
                    }
                    
                    $uniqueColumns .= $this->lang->line($col['labelName']) . ': ' . $saveData[$colName] . ', ';
                    
                    $p ++;
                }
            }
            
            if (isset($ignoreCheckUnique)) {
                return array('status' => 'success');
            } 
            
            if ($kpiTblId != '') {
                $whereClause .= "$kpiTblIdField <> " . $this->db->Param($p) . ' AND ';
                $bindParam[] = $kpiTblId;
            }
            
            $whereClause = trim(rtrim(trim($whereClause), 'AND'));
            
            $dataCount = $this->db->GetOne("SELECT COUNT(1) FROM $tblName WHERE DELETED_USER_ID IS NULL AND $whereClause", $bindParam);
            
            if ((int) $dataCount > 0) {
                 
                $uniqueColumns = trim(rtrim(trim($uniqueColumns), ','));
            
                $globeMessage = $this->lang->lineDefault('PF_CHECK_UNIQUE_MESSAGE', 'Тухайн өгөгдөл өмнө нь үүссэн байна. /[uniquecolumns]/');
                $globeMessage = str_ireplace('[uniquecolumns]', $uniqueColumns, $globeMessage);
                
                return array('status' => 'warning', 'message' => $globeMessage);
            } 
            
            return array('status' => 'success');
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function runKpiStoredProcedure($indicatorId, $parameters) {
        
        $param = array(
            'indicatorId' => $indicatorId,
            'parameters' => $parameters 
        );
        
        $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'KPI_CALL_PROCEDURE', $param);
        
        if ($result['status'] == 'success') {
            $response = array('status' => 'success', 'response' => issetParamArray($result['result']));
        } else {
            $response = array('status' => 'error', 'message' => $this->ws->getResponseMessage($result));
        }
        
        return $response;
    }
    
    public function runKpiIndicatorInputsToMetaProcess($indicatorId, $tblName, $idField, $rowId, $param, $isUpdate) {
        
        $param['indicatorId'] = $indicatorId;

        $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'indicatorProcess', $param);

        if ($result['status'] == 'success') {
            
            $response = array('status' => 'success');
            
        } else {
            
            if ($isUpdate == false) {
                $this->db->Execute("DELETE FROM $tblName WHERE $idField = ".$this->db->Param(0), array($rowId));
            }
            
            $response = array('status' => 'error', 'message' => $this->ws->getResponseMessage($result));
        }
        
        return $response;
    }
    
    public function runGenerateKpiDataMartByIndicatorId($indicatorId, $sourceRecordId = null) {
        
        if (!isset(self::$kpiTrgIndicatorsBySrcIndicatorId[$indicatorId])) {
            
            $data = $this->db->GetAll("
                SELECT 
                    TRG_INDICATOR_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP 
                WHERE SEMANTIC_TYPE_ID = 10000004 
                    AND SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                ORDER BY ORDER_NUMBER ASC", array($indicatorId));
            
            self::$kpiTrgIndicatorsBySrcIndicatorId[$indicatorId] = $data;
        } 
        
        $data = self::$kpiTrgIndicatorsBySrcIndicatorId[$indicatorId];
        
        if ($data) {
            
            $phpUrl = Config::getFromCache('PHP_URL');
            $phpUrl = rtrim($phpUrl, '/');
            $phpUrl = $phpUrl.'/';
            
            foreach ($data as $row) {
                $this->ws->curlQueue($phpUrl . 'cron/runGenerateKpiDetailDataMart/'.$row['TRG_INDICATOR_ID'].'/'.$sourceRecordId);
            }
        }
        
        return true;
    }
    
    public function updateLastAffectedDateByIndicatorId($indicatorId, $currentDate) {
        
        $this->db->AutoExecute(
            'KPI_DATAMODEL_MAP_KEY', 
            array('LAST_AFFECTED_DATE' => $currentDate), 
            'UPDATE', 
            'MAIN_INDICATOR_ID = '.$indicatorId
        );
        
        return true;
    }
    
    public function runGenerateKpiRelationDataMartByIndicatorId($indicatorId) {
        
        /*$idPh = $this->db->Param(0);
            
        $data = $this->db->GetAll("
            SELECT 
                DISTINCT 
                MAIN_INDICATOR_ID 
            FROM KPI_DATAMODEL_MAP_KEY
            WHERE SRC_INDICATOR_ID = $idPh OR TRG_INDICATOR_ID = $idPh", 
            array($indicatorId)
        );
        
        if ($data) {
            
            $phpUrl = Config::getFromCache('PHP_URL');
            $phpUrl = rtrim($phpUrl, '/');
            $phpUrl = $phpUrl.'/';
            
            foreach ($data as $row) {
                $this->ws->curlQueue($phpUrl . 'cron/runGenerateKpiRelationDataMart/'.$row['MAIN_INDICATOR_ID']);
            }
        }*/
        
        return true;
    }
    
    public function kpiSaveMetaDmRecordMap($srcIndicatorId, $srcRecordId, $saveData = array()) {
        
        if (Input::numeric('isKpiComponent') == 1) {
            
            try {
            
                $metaDmRecordMaps = Input::post('metaDmRecordMaps', array());
                $tmpMetaDmRecordMaps = $metaDmRecordMaps;
                
                $saveData = Arr::changeKeyLower($saveData);
                $fieldConfig = self::$indicatorIdFields[$srcIndicatorId];
                $codeField = strtolower($fieldConfig['codeField']);
                $checkCodeValue = Str::lower(issetParam($saveData[$codeField]));
                                
                $createFieldDataMapConfig = $dynamicDataMapConfig = array();
                
                $createFieldDataMaps = $this->db->GetAll("
                    SELECT 
                        T0.META_INFO_INDICATOR_ID, 
                        T1.TRG_INDICATOR_ID, 
                        T1.SRC_INDICATOR_PATH, 
                        LOWER(T1.TRG_INDICATOR_PATH) AS TRG_INDICATOR_PATH, 
                        T0.CODE, 
                        T0.CRITERIA, 
                        T3.TABLE_NAME 
                    FROM KPI_INDICATOR_INDICATOR_MAP T0 
                        INNER JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.SRC_INDICATOR_MAP_ID = T0.ID 
                        INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.META_INFO_INDICATOR_ID 
                        INNER JOIN KPI_INDICATOR T3 ON T3.ID = T1.TRG_INDICATOR_ID  
                    WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                    GROUP BY 
                        T0.META_INFO_INDICATOR_ID, 
                        T1.TRG_INDICATOR_ID, 
                        T1.SRC_INDICATOR_PATH, 
                        T1.TRG_INDICATOR_PATH, 
                        T0.CODE, 
                        T0.CRITERIA, 
                        T3.TABLE_NAME
                    ORDER BY T0.CODE ASC", 
                    array($srcIndicatorId)
                );
                
                foreach ($createFieldDataMaps as $createFieldRow) {
                    
                    $createFieldDataMapConfig[$createFieldRow['TRG_INDICATOR_ID']][] = $createFieldRow;
                    
                    if ($createFieldRow['CODE'] != '' && $createFieldRow['CRITERIA']) {
                        
                        $createFieldRow['CODE'] = Str::lower($createFieldRow['CODE']);
                        $createFieldRow['CRITERIA'] = html_entity_decode($createFieldRow['CRITERIA'], ENT_QUOTES, 'UTF-8');
                        
                        $dynamicDataMapConfig[$createFieldRow['META_INFO_INDICATOR_ID'].'_'.$createFieldRow['TRG_INDICATOR_ID'].'_'.$createFieldRow['CODE'].'_'.$createFieldRow['CRITERIA']] = $createFieldRow;
                    }
                }
                
                if ($dynamicDataMapConfig) {
                    
                    $this->db->Execute("
                        DELETE 
                        FROM META_DM_RECORD_MAP 
                        WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                            AND SRC_RECORD_ID = ".$this->db->Param(1), 
                        array($srcIndicatorId, $srcRecordId)
                    );
                    
                    $tmpMetaDmRecordMaps = $metaDmRecordMaps = array();
                    
                    foreach ($dynamicDataMapConfig as $dynamicDataMapRow) {
                        
                        if ($checkCodeValue == $dynamicDataMapRow['CODE']) {
                            
                            $dynamicDataMapCriteria = str_replace(array('[', ']'), '', $dynamicDataMapRow['CRITERIA']);
                            
                            $_POST['indicatorId'] = $dynamicDataMapRow['TRG_INDICATOR_ID'];
                            $_POST['page']        = 1;
                            $_POST['rows']        = 500;
                            $_POST['whereClause'] = $dynamicDataMapCriteria;
                            $_POST['isIgnoreCompanyDepartmentId'] = true;

                            $resultRows = $this->indicatorDataGridModel();
                            
                            if ($resultRows['status'] == 'success' && isset($resultRows['rows'][0])) {
                                
                                $columnsData = $this->getKpiIndicatorColumnsModel($dynamicDataMapRow['TRG_INDICATOR_ID'], array('isIgnoreStandardFields' => true)); 
                                $fieldConfig = $this->getKpiIndicatorIdFieldModel($dynamicDataMapRow['TRG_INDICATOR_ID'], $columnsData);
                                $idField     = $fieldConfig['idField'];
                                $firstRow    = $resultRows['rows'][0];
                                
                                if (!isset($firstRow[$idField])) {
                                    continue;
                                }
                        
                                foreach ($resultRows['rows'] as $resultRow) {
                                    
                                    $recordId = $resultRow[$idField];
                                    
                                    if ($tmpMetaDmRecordMaps) {
                                        
                                        $arr = array_filter($tmpMetaDmRecordMaps['recordId'], function($postRecordId) use($recordId) {
                                            return ($postRecordId == $recordId);
                                        });
                                        
                                        if ($arr) {
                                            continue;
                                        }
                                    }
                                    
                                    $metaDmRecordMaps['indicatorId'][] = $dynamicDataMapRow['TRG_INDICATOR_ID'];
                                    $metaDmRecordMaps['recordId'][] = $recordId;
                                    $metaDmRecordMaps['mapId'][] = null;
                                    $metaDmRecordMaps['rowState'][] = 'added';
                                    $metaDmRecordMaps['childRecordId'][] = null;
                                    $metaDmRecordMaps['childRowData'][] = json_encode($resultRow, JSON_UNESCAPED_UNICODE);
                                }
                            }
                        }
                    }
                }
                
                if (isset($metaDmRecordMaps['indicatorId']) && $metaDmRecordMaps['indicatorId']) {
                    
                    $trgIndicatorIds = $metaDmRecordMaps['indicatorId'];
                    $removedRows = $checkDeletedRows = $checkSubRows = array();
                    $sessionUserKeyId = Ue::sessionUserKeyId();
                    $metaDmRecordMapsSubForm = issetParamArray($_POST['kpiTbl']['metaDmRecordMapsSubForm']);
                    $a = 1;
                    
                    foreach ($trgIndicatorIds as $k => $trgIndicatorId) {
                    
                        $rowState = $metaDmRecordMaps['rowState'][$k];
                        $trgRecordId = $metaDmRecordMaps['recordId'][$k];
                        
                        if ($rowState == 'added') {
                            
                            $insertMapRow = array(
                                'ID'                   => getUIDAdd($k), 
                                'SRC_REF_STRUCTURE_ID' => $srcIndicatorId, 
                                'TRG_REF_STRUCTURE_ID' => $trgIndicatorId, 
                                'SRC_RECORD_ID'        => $srcRecordId, 
                                'TRG_RECORD_ID'        => $trgRecordId, 
                                'CREATED_DATE'         => Date::currentDate(), 
                                'CREATED_USER_ID'      => $sessionUserKeyId
                            );

                            $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);
                            
                        } elseif ($rowState == 'removed') {
                            
                            $removedRows[] = array(
                                'mapId' => $metaDmRecordMaps['mapId'][$k], 
                                'trgIndicatorId' => $trgIndicatorId, 
                                'trgRecordId' => $trgRecordId
                            );
                            
                            continue;
                            
                        } elseif ($rowState == 'saved') {
                            
                            $insertMapRow['ID'] = $metaDmRecordMaps['mapId'][$k];
                        } 
                        
                        $childRowData = $metaDmRecordMaps['childRowData'][$k];
                        
                        if (isset($createFieldDataMapConfig[$trgIndicatorId]) && $createFieldDataMapConfig[$trgIndicatorId]) {
                            
                            $childRowData = @json_decode(html_entity_decode($childRowData, ENT_QUOTES, 'UTF-8'), true);
                            
                            if ($childRowData) {
                                
                                $childRowData = Arr::changeKeyLower($childRowData);
                                $createFieldDataMapConfigs = $createFieldDataMapConfig[$trgIndicatorId];

                                foreach ($createFieldDataMapConfigs as $dataMapConfig) {
                                    
                                    $dataMapConfigCode                = Str::lower($dataMapConfig['CODE']);
                                    $dataMapConfigMetaInfoIndicatorId = $dataMapConfig['META_INFO_INDICATOR_ID'];
                                    $dataMapConfigTrgIndicatorId      = $dataMapConfig['TRG_INDICATOR_ID'];
                                    $dataMapConfigSrcIndicatorPath    = $dataMapConfig['SRC_INDICATOR_PATH'];
                                    $dataMapConfigTrgIndicatorPath    = $dataMapConfig['TRG_INDICATOR_PATH'];
                                    
                                    if ($dataMapConfigCode) {
                                        
                                        if ($checkCodeValue == $dataMapConfigCode) {
                                            
                                            $dataMapConfigValue = '';
                                            
                                            if (isset($metaDmRecordMapsSubForm[$k])) {
                                                $subUniqIdKey = array_key_first($metaDmRecordMapsSubForm[$k]);
                                                $subUniqIdArr = explode('_', $subUniqIdKey);
                                                $subUniqId = $subUniqIdArr[1];
                                            } else {
                                                $subUniqId = '999';
                                            }
                                            
                                            if ($dataMapConfigTrgIndicatorId == $trgIndicatorId) {
                                                $dataMapConfigValue = issetParam($childRowData[$dataMapConfigTrgIndicatorPath]);
                                            } elseif ($dataMapConfigTrgIndicatorId == $srcIndicatorId) {
                                                $dataMapConfigValue = issetParam($saveData[$dataMapConfigTrgIndicatorPath]);
                                            }
                                            
                                            $metaDmRecordMapsSubForm[$k][$dataMapConfigMetaInfoIndicatorId.'_'.$subUniqId][$dataMapConfigSrcIndicatorPath] = $dataMapConfigValue;
                                            
                                            if (isset($childRowData['company_department_id']) && $childRowData['company_department_id']) {
                                                $metaDmRecordMapsSubForm[$k][$dataMapConfigMetaInfoIndicatorId.'_'.$subUniqId]['COMPANY_DEPARTMENT_ID'] = $childRowData['company_department_id'];
                                            }
                                        }
                                    }
                                }
                                       
                            }
                        }
                        
                        if (isset($metaDmRecordMapsSubForm[$k])) {
                            
                            $addonForm = $metaDmRecordMapsSubForm[$k];
                            $addonRecordId = $metaDmRecordMaps['childRecordId'][$k];
                            
                            unset($_POST['isKpiComponent']);
                            unset($_POST['kpiTblId']);
                            unset($_POST['recordMap']);
                            unset($_POST['sf']);

                            foreach ($addonForm as $addonIndicatorId => $addonParams) {
                                
                                $addonIndicatorIdArr = explode('_', $addonIndicatorId);
                                $addonIndicatorId = $addonIndicatorIdArr[0];

                                $_POST['kpiMainIndicatorId'] = $addonIndicatorId;
                                $_POST['kpiTblIdField']      = 'ID';
                                $_POST['isExcelImport']      = 1;
                                $_POST['kpiTblId']           = $addonRecordId;

                                unset($_POST['kpiTbl']);
                                $_POST['kpiTbl'] = $addonParams;

                                self::$uniqIdIndex = $a;
                                
                                if (!$addonRecordId && !isset($checkDeletedRows[$trgIndicatorId.'_'.$addonIndicatorId])) {
                                    
                                    $checkRecordMapData = $this->db->GetAll("
                                        SELECT 
                                            ID, 
                                            TRG_RECORD_ID 
                                        FROM META_DM_RECORD_MAP 
                                        WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                                            AND TRG_REF_STRUCTURE_ID = ".$this->db->Param(1), 
                                        array($trgIndicatorId, $addonIndicatorId)
                                    );

                                    if ($checkRecordMapData) {

                                        $addonIndicatorRow = self::getKpiIndicatorRowModel($addonIndicatorId);
                                        $addonIndicatorTblName = $addonIndicatorRow['TABLE_NAME'];
                                        
                                        if ($addonIndicatorTblName && $checkRecordMapData) {
                                            
                                            $idsSplit = array_chunk($checkRecordMapData, 500); 
                                            $where    = ' AND (';
                                            $whereTbl = ' AND (';
                                            
                                            foreach ($idsSplit as $idsArr) {
                                                $where .= ' ID IN (' . Arr::implode_key(',', $idsArr, 'ID', true) . ') OR';
                                                $whereTbl .= ' ID IN (' . Arr::implode_key(',', $idsArr, 'TRG_RECORD_ID', true) . ') OR';
                                            }
                                            
                                            $where = rtrim($where, ' OR');
                                            $where .= ')';
                                            
                                            $whereTbl = rtrim($whereTbl, ' OR');
                                            $whereTbl .= ')';
                                            
                                            $this->db->Execute("DELETE FROM META_DM_RECORD_MAP WHERE 1 = 1 $where");
                                            $this->db->Execute("DELETE FROM $addonIndicatorTblName WHERE 1 = 1 $whereTbl");
                                        }
                                    }

                                    $checkDeletedRows[$trgIndicatorId.'_'.$addonIndicatorId] = 1;
                                }

                                $result = self::saveKpiDynamicDataModel();

                                if ($result['status'] != 'success') {
                                    
                                    throw new Exception($result['message']); 

                                } elseif (!$addonRecordId) {

                                    $childInsertMapRow = array(
                                        'ID'                   => getUIDAdd($a), 
                                        'SRC_REF_STRUCTURE_ID' => $trgIndicatorId, 
                                        'TRG_REF_STRUCTURE_ID' => $addonIndicatorId, 
                                        'SRC_RECORD_ID'        => $insertMapRow['ID'], 
                                        'TRG_RECORD_ID'        => $result['rowId'], 
                                        'CREATED_DATE'         => Date::currentDate(), 
                                        'CREATED_USER_ID'      => $sessionUserKeyId
                                    );

                                    $this->db->AutoExecute('META_DM_RECORD_MAP', $childInsertMapRow);
                                    
                                    $childConfigSubRows = $this->db->GetAll("
                                        SELECT 
                                            K.ID, 
                                            K.TABLE_NAME, 
                                            M.COLUMN_NAME_PATH 
                                        FROM KPI_INDICATOR_INDICATOR_MAP M 
                                            INNER JOIN KPI_INDICATOR K ON K.ID = M.MAIN_INDICATOR_ID 
                                        WHERE M.TRG_INDICATOR_ID = ".$this->db->Param(0)." 
                                            AND M.SEMANTIC_TYPE_ID = 10000002 
                                            AND M.MAIN_INDICATOR_ID IS NOT NULL 
                                            AND K.TABLE_NAME IS NOT NULL 
                                            AND M.COLUMN_NAME_PATH IS NOT NULL 
                                            AND M.SHOW_TYPE = 'rows' 
                                        ORDER BY M.ORDER_NUMBER ASC", 
                                        array($addonIndicatorId)
                                    );
                                    
                                    if ($childConfigSubRows) {
                                        
                                        $srcConfigRow = self::$kpiIndicatorRow[$srcIndicatorId];
                                        $isUseCompanyDepartmentId = $srcConfigRow['IS_USE_COMPANY_DEPARTMENT_ID'];
                                        
                                        $findHeaderDataFilter = '';
                                        
                                        if (isset($addonParams['COMPANY_DEPARTMENT_ID']) && $addonParams['COMPANY_DEPARTMENT_ID']) {
                                            $findHeaderDataFilter = " AND COMPANY_DEPARTMENT_ID = ".$addonParams['COMPANY_DEPARTMENT_ID'];
                                        }
                                        
                                        /*if ($isUseCompanyDepartmentId) {
                                            
                                            $sessionValues = Session::get(SESSION_PREFIX.'sessionValues');
                                            $sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid']);
                                            
                                            if ($sessionCompanyDepartmentId) {
                                                $findHeaderDataFilter = " AND COMPANY_DEPARTMENT_ID = $sessionCompanyDepartmentId";
                                            }
                                        }*/
                                        
                                        foreach ($childConfigSubRows as $childConfigSubRow) {
                                        
                                            try {

                                                $childTableName      = $childConfigSubRow['TABLE_NAME'];
                                                $childRowsPath       = $childConfigSubRow['COLUMN_NAME_PATH'];
                                                $childSrcIndicatorId = $childConfigSubRow['ID'];
                                                
                                                if (!isset($checkDeletedRows[$childSrcIndicatorId.'__'.$addonIndicatorId])) {
                                                    
                                                    $this->db->Execute("
                                                        DELETE 
                                                        FROM META_DM_RECORD_MAP 
                                                        WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                                                            AND TRG_REF_STRUCTURE_ID = ".$this->db->Param(1)." 
                                                            AND SRC_NAME IS NOT NULL", 
                                                        array($childSrcIndicatorId, $addonIndicatorId)
                                                    );
                                                    
                                                    $checkDeletedRows[$childSrcIndicatorId.'__'.$addonIndicatorId] = 1;
                                                }
                                                
                                                if (!isset($checkSubRows[$childSrcIndicatorId.'_'.$addonIndicatorId.'_'.$findHeaderDataFilter])) {
                                                    
                                                    $headerDataRows = $this->db->GetAll("SELECT ID FROM $childTableName WHERE DELETED_USER_ID IS NULL $findHeaderDataFilter");
                                                    $checkSubRows[$childSrcIndicatorId.'_'.$addonIndicatorId.'_'.$findHeaderDataFilter] = $headerDataRows;
                                                    
                                                } else {
                                                    $headerDataRows = $checkSubRows[$childSrcIndicatorId.'_'.$addonIndicatorId.'_'.$findHeaderDataFilter];
                                                }
                                                
                                                foreach ($headerDataRows as $headerDataRow) {
                                                    
                                                    $insertMapRow = array(
                                                        'ID'                   => getUIDAdd($a), 
                                                        'SRC_REF_STRUCTURE_ID' => $childSrcIndicatorId, 
                                                        'TRG_REF_STRUCTURE_ID' => $addonIndicatorId, 
                                                        'SRC_RECORD_ID'        => $headerDataRow['ID'], 
                                                        'TRG_RECORD_ID'        => $result['rowId'], 
                                                        'SRC_NAME'             => $childRowsPath, 
                                                        'CREATED_DATE'         => Date::currentDate(), 
                                                        'CREATED_USER_ID'      => $sessionUserKeyId
                                                    );

                                                    $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);

                                                    $a ++;
                                                }                                            

                                            } catch (Exception $ex) {}
                                        }
                                    }
                                }

                                $a ++;
                            }
                        }
                    }
                    
                    if ($removedRows) {
                        
                        foreach ($removedRows as $removedRow) {
                            
                            $this->db->Execute("
                                DELETE 
                                FROM META_DM_RECORD_MAP 
                                WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                                    AND SRC_RECORD_ID = ".$this->db->Param(1)." 
                                    AND TRG_REF_STRUCTURE_ID = ".$this->db->Param(2)." 
                                    AND TRG_RECORD_ID = ".$this->db->Param(3)." 
                                    AND SRC_NAME IS NULL", 
                                array($srcIndicatorId, $srcRecordId, $removedRow['trgIndicatorId'], $removedRow['trgRecordId'])
                            );
                            
                            $this->db->Execute("
                                DELETE 
                                FROM META_DM_RECORD_MAP 
                                WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                                    AND SRC_RECORD_ID = ".$this->db->Param(1)." 
                                    AND SRC_NAME IS NULL", 
                                array($removedRow['trgIndicatorId'], $removedRow['mapId'])
                            );
                        }
                    }
                }
                
                $response = array('status' => 'success');
                
            } catch (Exception $ex) {
                $response = array('status' => 'error', 'message' => $ex->getMessage());
            }
            
        } else {
            $response = array('status' => 'info');
        }
        
        return $response;
    }

    public function kpiSaveMetaDmRecordMap2() {    
        $metaDmRecordMaps = Input::postData();
        if ($recordIds = issetParam($metaDmRecordMaps['indicatorRecordMaps'])) {                    
            $sessionUserKeyId = Ue::sessionUserKeyId();
            
            foreach ($recordIds as $k => $recordId) {        
                $insertMapRow = array(
                    'ID'                   => getUIDAdd($k), 
                    'SRC_REF_STRUCTURE_ID' => '1479204227214',
                    'TRG_REF_STRUCTURE_ID' => $metaDmRecordMaps['indicatorId'], 
                    'SRC_RECORD_ID'        => $metaDmRecordMaps['mainIndicatorId'],
                    'TRG_RECORD_ID'        => $recordId, 
                    'CREATED_DATE'         => Date::currentDate(), 
                    'CREATED_USER_ID'      => $sessionUserKeyId
                );
                $this->db->AutoExecute('META_DM_RECORD_MAP', $insertMapRow);
            }
        }

        return array('status' => 'success', 'message' => Lang::line('msg_save_success'));
    }    
    
    public function dbCreatedTblKpiTemplate($tblName, $dbField) {
        
        try {
            
            $fields = '';
        
            foreach ($dbField as $row) {
                
                $isCorrectColumnName = (boolean) preg_match("/^[_0-9a-zA-Z]{1,30}$/i", $row['name']);
                
                if (!$isCorrectColumnName) {
                    continue;
                }
                
                if ($row['type'] == 'clob') {
                    $fields .= '"'.$row['name'].'" CLOB,';
                } else {
                    $length = issetDefaultVal($row['length'], 4000);
                    $fields .= '"'.$row['name'].'" VARCHAR2('.$length.' CHAR),';
                }
            }

            $createTableScript = '
            CREATE TABLE '.$tblName.' (	
                "ID" NUMBER(18,0) NOT NULL ENABLE, 
                "ROW_INDEX" NUMBER(18,0), 
                "PARENT_ID" NUMBER(18,0), 
                ' . $fields . '  
                "CREATED_DATE" DATE, 
                "CREATED_USER_ID" NUMBER(18,0), 
                "CREATED_USER_NAME" VARCHAR2(256 CHAR), 
                "MODIFIED_DATE" DATE, 
                "MODIFIED_USER_ID" NUMBER(18,0), 
                "MODIFIED_USER_NAME" VARCHAR2(256 CHAR)
            )';

            $this->db->Execute($createTableScript);
            
            if (strpos($tblName, '.') !== false) {
                $pkName = explode('.', $tblName);
                $pkName = $pkName[1];
            } else {
                $pkName = $tblName;
            }
            
            $this->db->Execute('ALTER TABLE '.$tblName.' ADD CONSTRAINT '.$pkName.'_PK PRIMARY KEY (ID) ENABLE');
            
            $response = array('status' => 'success');
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function dbCreatedTblKpiDynamic($tblName, $dbField) {
        
        try {
            
            $indexing = array();
            $fields = '';
        
            foreach ($dbField as $row) {
                
                $isCorrectColumnName = (boolean) preg_match("/^[_0-9a-zA-Z]{1,30}$/i", $row['name']);
                
                if (!$isCorrectColumnName) {
                    continue;
                }
                
                if ($row['type'] == 'clob' || $row['type'] == 'text_editor' || $row['type'] == 'html_clicktoedit') {
                    $fields .= '"'.$row['name'].'" CLOB,';
                } elseif ($row['type'] == 'date' || $row['type'] == 'datetime' || $row['type'] == 'time') {
                    $fields .= '"'.$row['name'].'" DATE,';
                } elseif ($row['type'] == 'combo' || $row['type'] == 'radio' || $row['type'] == 'popup' || $row['type'] == 'number' || $row['type'] == 'long') {
                    $fields .= '"'.$row['name'].'" NUMBER(18,0),';
                } elseif ($row['type'] == 'decimal' || $row['type'] == 'decimal_zero' || $row['type'] == 'bigdecimal' || $row['type'] == 'percent') {
                    $fields .= '"'.$row['name'].'" NUMBER(24,6),';
                } elseif ($row['type'] == 'code' || $row['type'] == 'icon_picker') {
                    $fields .= '"'.$row['name'].'" VARCHAR2(50 CHAR),';
                } elseif ($row['type'] == 'multicombo') {
                    $fields .= '"'.$row['name'].'" VARCHAR2(256 CHAR),';
                } else {
                    $fields .= '"'.$row['name'].'" VARCHAR2(4000 CHAR),';
                }
                
                if ($row['type'] == 'date' || $row['type'] == 'datetime' || $row['type'] == 'long' || $row['type'] == 'code') {
                    $indexing[] = $row['name'];
                }
            }

            $createTableScript = '
            CREATE TABLE '.$tblName.' (	
                "ID" NUMBER(18,0) NOT NULL ENABLE, 
                "INDICATOR_ID" NUMBER(18,0), 
                "SRC_RECORD_ID" NUMBER(18,0), 
                "DATA" CLOB, 
                "NAME" VARCHAR2(4000 CHAR), 
                ' . $fields . ' 
                "WFM_STATUS_ID" NUMBER(18,0), 
                "WFM_DESCRIPTION" VARCHAR2(4000 CHAR), 
                "CREATED_DATE" DATE, 
                "CREATED_USER_ID" NUMBER(18,0), 
                "CREATED_USER_NAME" VARCHAR2(512 CHAR), 
                "MODIFIED_DATE" DATE, 
                "MODIFIED_USER_ID" NUMBER(18,0), 
                "MODIFIED_USER_NAME" VARCHAR2(512 CHAR), 
                "DELETED_DATE" DATE, 
                "DELETED_USER_ID" NUMBER(18,0), 
                "DELETED_USER_NAME" VARCHAR2(512 CHAR), 
                "COMPANY_DEPARTMENT_ID" NUMBER(18,0),
                "GENERATED_DATE" DATE
            )';

            $this->db->Execute($createTableScript);
            
            if (strpos($tblName, '.') !== false) {
                $pkName = explode('.', $tblName);
                $pkName = $pkName[1];
            } else {
                $pkName = $tblName;
            }
            
            $this->db->Execute('ALTER TABLE '.$tblName.' ADD CONSTRAINT '.$pkName.'_PK PRIMARY KEY (ID) ENABLE');
            
            foreach ($indexing as $i => $colName) {
                
                try {
                
                    $indexId = getUIDAdd($i);
                    $this->db->Execute("CREATE INDEX V_IX$indexId ON $tblName ($colName)");

                } catch (Exception $ex) {}
            }
            
            $response = array('status' => 'success');
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function dbAlterTblKpiDynamic($tblName, $dbField) {
        
        try {
        
            foreach ($dbField as $row) {
                
                $isCorrectColumnName = (boolean) preg_match("/^[_0-9a-zA-Z]{1,30}$/i", $row['name']);
                
                if (!$isCorrectColumnName) {
                    continue;
                }
                
                if ($row['type'] == 'clob' || $row['type'] == 'text_editor' || $row['type'] == 'html_clicktoedit') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." CLOB)");
                    
                } elseif ($row['type'] == 'combo' || $row['type'] == 'radio' || $row['type'] == 'popup' || $row['type'] == 'number') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." NUMBER(18,0))");
                    
                } elseif ($row['type'] == 'date' || $row['type'] == 'datetime' || $row['type'] == 'time') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." DATE)");
                    
                } elseif ($row['type'] == 'decimal' || $row['type'] == 'decimal_zero' || $row['type'] == 'bigdecimal' || $row['type'] == 'percent') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." NUMBER(24,6))");
                    
                } elseif ($row['type'] == 'code' || $row['type'] == 'icon_picker') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." VARCHAR2(50 CHAR))");
                    
                } elseif ($row['type'] == 'multicombo') {
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." VARCHAR2(256 CHAR))");
                    
                } else {
                    
                    $length = ($row['name'] == 'NAME' ? 4000 : issetDefaultVal($row['length'], 4000));
                    
                    $this->db->Execute("ALTER TABLE $tblName ADD (".$row['name']." VARCHAR2($length CHAR))");
                }
            }
            
            $response = array('status' => 'success');
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function dbAlterTblKpiIndex($kpiMainIndicatorId, $tblName, $dbIndex) {
        
        foreach ($dbIndex as $k => $colName) {
            
            if ($colName != '') {
                
                try {
                
                    $indexId = getUIDAdd($k);
                    $this->db->Execute("CREATE INDEX V_IX$indexId ON $tblName ($colName)");

                } catch (Exception $ex) {}
            }
        }
        
        return true;
    }
    
    public function kpiDynamicTblStandardFields() {
        
        $standardFields = array(
            array(
                'type' => 'number', 
                'name' => 'ID'
            ), 
            array(
                'type' => 'number', 
                'name' => 'INDICATOR_ID'
            ), 
            array(
                'type' => 'number', 
                'name' => 'SRC_RECORD_ID'
            ),
            array(
                'type' => 'clob', 
                'name' => 'DATA'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'NAME'
            ),
            array(
                'type' => 'number', 
                'name' => 'WFM_STATUS_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'WFM_DESCRIPTION'
            ),
            array(
                'type' => 'date', 
                'name' => 'CREATED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'CREATED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'CREATED_USER_NAME'
            ),
            array(
                'type' => 'date', 
                'name' => 'MODIFIED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'MODIFIED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'MODIFIED_USER_NAME'
            ),
            array(
                'type' => 'date', 
                'name' => 'DELETED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'DELETED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'DELETED_USER_NAME'
            ), 
            array(
                'type' => 'number', 
                'name' => 'COMPANY_DEPARTMENT_ID'
            ), 
            array(
                'type' => 'date', 
                'name' => 'GENERATED_DATE'
            )
        );
        
        return $standardFields;
    }
    
    public function kpiTemplateTblStandardFields() {
                
        $standardFields = array(
            array(
                'type' => 'number', 
                'name' => 'ID'
            ), 
            array(
                'type' => 'number', 
                'name' => 'ROW_INDEX'
            ),
            array(
                'type' => 'number', 
                'name' => 'PARENT_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'ROW_STYLE'
            ),
            array(
                'type' => 'date', 
                'name' => 'CREATED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'CREATED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'CREATED_USER_NAME'
            ),
            array(
                'type' => 'date', 
                'name' => 'MODIFIED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'MODIFIED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'MODIFIED_USER_NAME'
            ),
        );
        
        return $standardFields;
    }
    
    public function updateKpiIndicatorTblName($kpiMainIndicatorId, $tableName) {
        
        $this->db->AutoExecute('KPI_INDICATOR', array('TABLE_NAME' => $tableName), 'UPDATE', 'ID = '.$kpiMainIndicatorId);
        return true;
    }
    
    public function updateKpiIndicatorMapTblName($kpiIndicatorMapId, $tableName) {
        
        $this->db->AutoExecute('KPI_INDICATOR_INDICATOR_MAP', array('TEMPLATE_TABLE_NAME' => $tableName), 'UPDATE', 'ID = '.$kpiIndicatorMapId);
        return true;
    }
    
    public function getKpiIndicatorColumnsModel($indicatorId, $configRow = array()) {
        
        $langCode = Lang::getCode();
        
        if (!isset(self::$indicatorColumns[$indicatorId])) {
            
            try {
                
                if (issetParam($configRow['isFilter'])) {
                    $where = ' AND KIIM.IS_FILTER = 1 ';
                } else {
                    $where = " AND KIIM.COLUMN_NAME IS NOT NULL AND KIIM.COLUMN_NAME <> 'ID' ";
                }
                
                if ($mapId = Input::numeric('mapId')) { 
                    $where .= " AND KIIM.COLUMN_NAME IN (SELECT COLUMN_NAME FROM KPI_INDICATOR_INDICATOR_MAP WHERE PARENT_ID = $mapId)";
                }
                
                if (issetParam($configRow['isParentIdNull'])) {
                    $where .= " AND KIIM.PARENT_ID IS NULL ";
                }

                $data = $this->db->GetAll("
                    SELECT 
                        UPPER(KIIM.COLUMN_NAME) AS COLUMN_NAME, 
                        KIIM.CODE, 
                        FNC_TRANSLATE('$langCode', KIIM.TRANSLATION_VALUE, 'LABEL_NAME', KIIM.LABEL_NAME) AS LABEL_NAME, 
                        FNC_TRANSLATE('$langCode', KIIM.TRANSLATION_VALUE, 'PLACEHOLDER_NAME', KIIM.PLACEHOLDER_NAME) AS PLACEHOLDER_NAME, 
                        KIIM.COLUMN_WIDTH, 
                        KIIM.INPUT_NAME, 
                        KIIM.SHOW_TYPE, 
                        KIIM.IS_RENDER, 
                        KIIM.TRG_INDICATOR_ID AS LOOKUP_META_DATA_ID, 
                        KIIM.LOOKUP_META_DATA_ID AS META_LOOKUP_ID, 
                        KIIM.LOOKUP_CRITERIA, 
                        KIIM.IS_FILTER, 
                        KIIM.IS_INPUT, 
                        KIIM.SORT_ORDER, 
                        KIIM.SORT_TYPE, 
                        KIIM.COLUMN_AGGREGATE, 
                        KIIM.EXPRESSION_STRING, 
                        KIIM.DEFAULT_VALUE, 
                        KIIM.IS_UNIQUE, 
                        KIIM.REPORT_AGGREGATE_FUNCTION, 
                        KIIM.SIDEBAR_NAME, 
                        KIIM.TRG_ALIAS_NAME, 
                        KIIM.TRG_INDICATOR_ID AS FILTER_INDICATOR_ID, 
                        KIIM.IS_PUBLISH, 
                        KIIM.MERGE_TYPE, 
                        KIIM.MIN_VALUE, 
                        KIIM.MAX_VALUE, 
                        KIIM.IS_TRANSLATE, 
                        KIIM.ORDER_NUMBER AS ORDER_NUM, 
                        LOWER(KIIM.BODY_ALIGN) AS BODY_ALIGN, 
                        MST.NAME AS SEMANTIC_TYPE_NAME, 
                        KI.TABLE_NAME AS TRG_TABLE_NAME, 
                        CASE WHEN KI.TABLE_NAME IS NOT NULL OR KI.QUERY_STRING IS NOT NULL 
                        THEN 1 ELSE 0 END AS IS_SELECT_QUERY, 
                        NULL AS GROUP_CONFIG_FIELD_PATH, 
                        NULL AS GROUP_CONFIG_PARAM_PATH, 
                        NULL AS GROUP_CONFIG_LOOKUP_PATH, 
                        NULL AS CRITERIA_PATH, 
                        NULL AS REVERSE_CRITERIA_PATH, 
                        (
                            SELECT 
                                COUNT(1) 
                            FROM META_DM_DRILLDOWN_DTL T1 
                            WHERE T1.MAIN_INDICATOR_ID = KIIM.MAIN_INDICATOR_ID 
                                AND T1.MAIN_GROUP_LINK_PARAM = KIIM.COLUMN_NAME 
                        ) AS IS_DRILL
                    FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                        LEFT JOIN KPI_INDICATOR KI ON KIIM.TRG_INDICATOR_ID = KI.ID 
                        LEFT JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
                    WHERE KIIM.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                        AND (
                            KIIM.PARENT_ID IN ( 
                                SELECT 
                                    KIIM1.ID 
                                FROM KPI_INDICATOR_INDICATOR_MAP KIIM1 
                                WHERE KIIM1.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                                    AND KIIM1.SHOW_TYPE IN ('label')
                            ) 
                            OR KIIM.PARENT_ID IS NULL
                        ) 
                        AND ".$this->db->IfNull('KIIM.IS_INPUT', '0')." = 1 
                        AND KIIM.SHOW_TYPE NOT IN ('row', 'rows', 'label', 'config') 
                        $where 
                    ORDER BY KIIM.ORDER_NUMBER ASC", 
                    array($indicatorId)
                );

                self::$indicatorColumns[$indicatorId] = $data;
            
            } catch (Exception $ex) {
                
                $data = array();
                self::$indicatorColumns[$indicatorId] = $data;
            }
            
        } else {
            $data = self::$indicatorColumns[$indicatorId];
        }
        
        if ($tableName = issetParam($configRow['TABLE_NAME'])) {
            
            if (strpos($tableName, '.D_') !== false || substr($tableName, 0, 2) == 'D_') {
                $configRow['isIgnoreStandardFields'] = true;
            }
        } elseif (issetParam($configRow['QUERY_STRING'])) {
            $configRow['isIgnoreStandardFields'] = true;
        }
        
        $nameFields = $addonFields = array();
        
        if (issetParam($configRow['NAME_PATTERN'])) {
            
            $nameFields = array(
                array(
                    'COLUMN_NAME' => 'NAME', 
                    'LABEL_NAME' => 'Нэр', 
                    'COLUMN_WIDTH' => '200', 
                    'INPUT_NAME' => '', 
                    'SIDEBAR_NAME' => '', 
                    'SHOW_TYPE' => '', 
                    'IS_RENDER' => '1', 
                    'SEMANTIC_TYPE_NAME' => '', 
                    'SORT_ORDER' => '', 
                    'SORT_TYPE' => '', 
                    'COLUMN_AGGREGATE' => ''
                )
            );  
        } 
        
        if (!issetParam($configRow['isIgnoreStandardFields'])) {
            
            $addonFields = array( 
                array(
                    'COLUMN_NAME' => 'CREATED_DATE', 
                    'LABEL_NAME' => 'Үүсгэсэн огноо', 
                    'COLUMN_WIDTH' => '123', 
                    'INPUT_NAME' => '', 
                    'SIDEBAR_NAME' => '', 
                    'SHOW_TYPE' => 'datetime', 
                    'IS_RENDER' => '1', 
                    'SEMANTIC_TYPE_NAME' => '', 
                    'SORT_ORDER' => '', 
                    'SORT_TYPE' => '', 
                    'COLUMN_AGGREGATE' => ''
                ), 
                array(
                    'COLUMN_NAME' => 'CREATED_USER_NAME', 
                    'LABEL_NAME' => 'Үүсгэсэн хэрэглэгч', 
                    'COLUMN_WIDTH' => '150', 
                    'INPUT_NAME' => '', 
                    'SIDEBAR_NAME' => '', 
                    'SHOW_TYPE' => '', 
                    'IS_RENDER' => '1', 
                    'SEMANTIC_TYPE_NAME' => '', 
                    'SORT_ORDER' => '', 
                    'SORT_TYPE' => '', 
                    'COLUMN_AGGREGATE' => ''
                ), 
                array(
                    'COLUMN_NAME' => 'MODIFIED_DATE', 
                    'LABEL_NAME' => 'Өөрчилсөн огноо', 
                    'COLUMN_WIDTH' => '123', 
                    'INPUT_NAME' => '', 
                    'SIDEBAR_NAME' => '', 
                    'SHOW_TYPE' => 'datetime', 
                    'IS_RENDER' => '1', 
                    'SEMANTIC_TYPE_NAME' => '', 
                    'SORT_ORDER' => '', 
                    'SORT_TYPE' => '', 
                    'COLUMN_AGGREGATE' => ''
                ), 
                array(
                    'COLUMN_NAME' => 'MODIFIED_USER_NAME', 
                    'LABEL_NAME' => 'Өөрчилсөн хэрэглэгч', 
                    'COLUMN_WIDTH' => '150', 
                    'INPUT_NAME' => '', 
                    'SIDEBAR_NAME' => '', 
                    'SHOW_TYPE' => '', 
                    'IS_RENDER' => '1', 
                    'SEMANTIC_TYPE_NAME' => '', 
                    'SORT_ORDER' => '', 
                    'SORT_TYPE' => '', 
                    'COLUMN_AGGREGATE' => ''
                )
            );
            
            if ($configRow['IS_USE_WORKFLOW'] == '1') {
                
                $isAlreadyWfmColumn = false;
                
                foreach ($data as $checkRow) {
                    if ($checkRow['COLUMN_NAME'] == 'WFM_STATUS_NAME') {
                        $isAlreadyWfmColumn = true;
                        break;
                    }
                }
                
                if ($isAlreadyWfmColumn == false) {
                    
                    $wfmRow = array(
                        'COLUMN_NAME' => 'WFM_STATUS_NAME', 
                        'LABEL_NAME' => 'Төлөв', 
                        'COLUMN_WIDTH' => '200', 
                        'INPUT_NAME' => '', 
                        'SIDEBAR_NAME' => '', 
                        'SHOW_TYPE' => 'wfmstatus', 
                        'IS_RENDER' => '1', 
                        'SEMANTIC_TYPE_NAME' => '', 
                        'SORT_ORDER' => '', 
                        'SORT_TYPE' => '', 
                        'COLUMN_AGGREGATE' => ''
                    );

                    $addonFields = array_merge_recursive(array($wfmRow), $addonFields);
                }
            }
        }
        
        $data = array_merge_recursive($nameFields, $data, $addonFields);
        
        return $data;
    }
    
    public function renderKpiIndicatorColumnsModel($indicatorId, $isCheckSystemTable, $columnsData) {
        
        $columns = $mergeColumns = array();
        
        $dm = &getInstance();
        $dm->load->model('Mdobject', 'middleware/models/');        
        
        $mergedGridData = $dm->model->resolveHtmlTableMergeHeader($columnsData, $indicatorId, [], 'COLUMN_NAME');
        
        foreach ($mergedGridData as $mergeIndex => $mergeRow) {
            $columns[] = '[';
            foreach ($mergeRow as $mrowIndex => $column) { 

                if ($column['IS_RENDER'] == '1' && $column['SHOW_TYPE'] != 'label') {                         
                    
                    if (isset($column['MERGE_LABEL_NAME'])) {                        
                        $columns[] = "{title:'". Lang::line($column['MERGE_LABEL_NAME']) ."',colspan:". $column['_COLSPAN'] ."},";
                        continue;
                    }                                
                    
                    $rowspan = '';
                    if (isset($column['_COLSPAN'])) {
                        $rowspan .= 'colspan:'.$column['_COLSPAN'].',';
                    }
                    if (isset($column['_ROWSPAN'])) {
                        $rowspan .= 'rowspan:'.$column['_ROWSPAN'].',';
                    }                          

                    if ($column['COLUMN_NAME'] == 'WFM_STATUS_NAME') {

                        $width = $column['COLUMN_WIDTH'] ? $column['COLUMN_WIDTH'] : 200;

                        $columns[] = "{field: 'wfmstatusname', title: '".Lang::line($column['LABEL_NAME'])."', sortable: true, fixed: true, width: '$width', halign: 'center', ".$rowspan;
                        $columns[] = "align: 'center', ";
                        $columns[] = "formatter: function(v, r, i) {return dataViewWfmStatusName(v, r, i, '$indicatorId', '$indicatorId');},";

                    } else {

                        if (!$isCheckSystemTable && ($column['SHOW_TYPE'] == 'combo' || $column['SHOW_TYPE'] == 'popup' || $column['SHOW_TYPE'] == 'radio')) {
                            $columnName = $column['COLUMN_NAME'] . '_DESC';
                        } else {
                            $columnName = $column['COLUMN_NAME'];
                        }

                        $width       = $column['COLUMN_WIDTH'] ? $column['COLUMN_WIDTH'] : '150';
                        $labelName   = Lang::line($column['LABEL_NAME']);
                        $labelName   = str_replace("'", "\'", $labelName);
                        $isDrill     = issetParam($column['IS_DRILL']);
                        $startAnchor = '';
                        $endAnchor   = '';

                        if ($isDrill) {
                            $startAnchor = "'<a href=\"javascript:;\" onclick=\"mvColumnDrillDown(this, \'$indicatorId\', \'".$column['COLUMN_NAME']."\', '+i+');\">'+";
                            $endAnchor   = "+'</a>'";
                        }

                        $columns[] = "{field: '$columnName', title: '$labelName', sortable:true, fixed: true, width: '$width', halign: 'center', ".$rowspan;

                        if ($column['SHOW_TYPE'] == 'decimal' || $column['SHOW_TYPE'] == 'bigdecimal') {

                            $columns[] = "align: 'right', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."gridAmountField(v, r)$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'number' || $column['SHOW_TYPE'] == 'long' || $column['SHOW_TYPE'] == 'integer') {

                            $columns[] = "align: 'right', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."gridLongField(v, r)$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'file') {

                            $columns[] = "align: 'center', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."gridFileField(v, r)$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'date') {

                            $columns[] = "align: 'center', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."dateFormatter('Y-m-d', v)$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'datetime') { 

                            $columns[] = "align: 'center', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."v$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'time') {

                            $columns[] = "align: 'center', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."dateFormatter('H:i', v)$endAnchor; },";

                        } elseif ($column['SHOW_TYPE'] == 'percent') { 

                            $columns[] = "align: 'right', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."gridPercentField(v)$endAnchor; },";

                        } else {
                            $bodyAlign = checkDefaultVal($column['BODY_ALIGN'], 'left');
                            $columns[] = "align: '$bodyAlign', ";
                            $columns[] = "formatter: function(v, r, i) { return ".$startAnchor."v$endAnchor; },";
                        }
                    }

                    if (isset(Mdform::$gridStyler['cell'][$column['COLUMN_NAME']])) {
                        $columns[] = 'styler:function cellStyler(value,row,index){ '.Mdform::$gridStyler['cell'][$column['COLUMN_NAME']].' },';
                    }

                    $columns[] = '},';
                    
                    if (issetParam($column['MERGE_TYPE']) == 'row') {
                        array_push($mergeColumns, $column['COLUMN_NAME']);
                    }
                }
            }
            $columns[] = '],';
        }
        
        $colString = implode('', $columns);
        $colString = ltrim(rtrim($colString, '],'), '[');
        
        return array('columnsRender' => $colString, 'mergeColumns' => $mergeColumns);
    }
    
    public function mvGridStylerModel($indicatorId) {
        
        try {
            
            $schemaName = Config::getFromCache('kpiDbSchemaName');
            $schemaName = $schemaName ? rtrim($schemaName, '.').'.' : '';
            
            $dataJson = $this->db->GetOne("SELECT DATA FROM ".$schemaName."V_16765981522079 WHERE SRC_RECORD_ID = ".$this->db->Param(0), array($indicatorId));
            
            if ($dataJson) {
                
                $data = @json_decode($dataJson, true);
                
                if (isset($data['CONFIG'][0]['STRUCTURE'])) {
                    $configs = $data['CONFIG'];
                    $rowStyler = '';
                    
                    foreach ($configs as $row) {
                        
                        if ($row['STRUCTURE']) {
                            $field = strtoupper($row['STRUCTURE']);
                            $value = $row['VALUE'];
                            $operator = html_entity_decode($row['OPERATOR_DESC']);
                            
                            if ($field && $value != '' && $operator != '') {
                                
                                $rowColor  = $row['ROW_COLOR'];
                                $cellColor = $row['CELL_COLOR'];
                                $textColor = $row['TEXT_COLOR'];
                                $textStyle = $row['TEXT_STYLE_DESC'];
                                $textAlign = $row['TEXT_ALIGN_DESC'];
                                
                                if (is_numeric($value)) {
                                    $ifField = 'dvFieldNumeric(row.'.$field.')';
                                    $ifValue = $value;
                                } else {
                                    $ifField = 'dvFieldValueShow(row.'.$field.').toLowerCase()';
                                    $ifValue = ($value == 'null') ? "''" : "('".$value."').toLowerCase()";
                                }
                                    
                                if ($rowColor) {
                                    $rowStyler .= 'if ('.$ifField.' '.$operator.' '.$ifValue.') { return \'background-color:'.$rowColor.';\'; } ';
                                }
                                
                                $cellStyler = '';
                                
                                if ($cellColor) {
                                    $cellStyler .= 'background-color:'.$cellColor.';';
                                }
                                if ($textColor) {
                                    $cellStyler .= 'color:'.$textColor.';';
                                }
                                if ($textStyle) {
                                    $textStyle = strtolower($textStyle);
                                    if ($textStyle == 'bold') {
                                        $cellStyler .= 'font-weight:bold;';
                                    } elseif ($textStyle == 'italic') {
                                        $cellStyler .= 'font-style:italic;';
                                    } elseif ($textStyle == 'underline') {
                                        $cellStyler .= 'text-decoration:underline;';
                                    }
                                }
                                if ($textAlign) {
                                    $cellStyler .= 'text-align:'.$textAlign.';';
                                }
                                
                                if ($cellStyler) {
                                    Mdform::$gridStyler['cell'][$field] = 'if ('.$ifField.' '.$operator.' '.$ifValue.') { return \''.$cellStyler.'\'; } ';
                                }
                            }
                        }
                    }
                    
                    if ($rowStyler) {
                        Mdform::$gridStyler['row'] = $rowStyler;
                    }
                }
            }
            
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function validateFilters($indicatorId, $filters, $responseType = 'json') {
        if (is_array($filters)) {
            
            $arr = array();
            
            foreach ($filters as $key => $val) {
                $key = preg_replace('/[^A-Za-z0-9_\s]/', '', strip_tags(html_entity_decode($key)));
                $key = Input::param($key);
                $val = preg_replace('/[^A-Za-zФЦУЖЭНГШҮЗКЪЙЫБӨАХРОЛДПЯЧЁСМИТЬВЮЕЩфцужэнгшүзкъйыбөахролдпячёсмитьвюещ0-9_\s]/', '', strip_tags(html_entity_decode($val)));
                $arr[$key] = Input::param($val);
            }
            
            if ($responseType == 'json') {
                return json_encode($arr, JSON_UNESCAPED_UNICODE);
            } else {
                return $arr;
            }
        }
        
        return null;
    }
    
    public function getKpiIndicatorIdFieldModel($indicatorId, $columnsData) {
        
        if (!isset(self::$indicatorIdFields[$indicatorId])) {
            
            $idField = 'ID';
            $codeField = 'CODE'; 
            $nameField = 'NAME'; 
            $parentField = '';
            $coordinateField = '';

            foreach ($columnsData as $col) {

                if ($col['COLUMN_NAME'] != '') {

                    if ($col['INPUT_NAME'] == 'META_VALUE_ID') {

                        $idField = strtoupper($col['COLUMN_NAME']);

                    } elseif ($col['INPUT_NAME'] == 'META_VALUE_CODE') {

                        $codeField = strtoupper($col['COLUMN_NAME']);
                        $isCodeField = true;

                    } elseif ($col['INPUT_NAME'] == 'META_VALUE_NAME') {

                        $nameField = strtoupper($col['COLUMN_NAME']);
                        $isNameField = true;

                        if ($col['SHOW_TYPE'] == 'combo' || $col['SHOW_TYPE'] == 'radio' || $col['SHOW_TYPE'] == 'popup') {
                            $nameField .= '_DESC';
                        }

                    } elseif ($col['INPUT_NAME'] == 'PARENT_ID') {

                        $parentField = strtoupper($col['COLUMN_NAME']);

                    } elseif ($col['SHOW_TYPE'] == 'coordinate' || $col['SHOW_TYPE'] == 'coordinate_auto' || $col['SHOW_TYPE'] == 'polyline') {

                        $coordinateField = $col['COLUMN_NAME'];
                    }
                }
            }

            if (!isset($isCodeField) && isset($isNameField)) {
                $codeField = $nameField;
            }

            if (isset($isCodeField) && !isset($isNameField)) {
                $nameField = $codeField;
            }

            $response = array('idField' => $idField, 'codeField' => $codeField, 'nameField' => $nameField, 'parentField' => $parentField, 'coordinateField' => $coordinateField);
            
            self::$indicatorIdFields[$indicatorId] = $response;
        }
        
        return self::$indicatorIdFields[$indicatorId];
    }
    
    public function parseQueryString($queryString) {
        
        $queryString = trim($queryString);
        
        if (strlen($queryString) > 30) {    
            
            includeLib('Compress/Compression');
            
            $queryString = Compression::decompress($queryString);
            $matches = DBSql::getQueryNamedParams($queryString);
            
            if ($matches) {
                foreach ($matches as $match) {
                    
                    $matchLower = strtolower($match);
                    $isReplaceVal = false;
                    
                    if ($matchLower == ':sessionuserid') {
                        $replaceVal = Ue::sessionUserKeyId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':languagecode' || $matchLower == ':langcode') {
                        $replaceVal = "'".Lang::getCode()."'";
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessionpositionkeyid') {
                        $replaceVal = Ue::sessionPositionKeyId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessionemployeeid') {
                        $replaceVal = Ue::sessionEmployeeId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessionemployeekeyid') {
                        $replaceVal = Ue::sessionEmployeeKeyId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessiondepartmentid') {
                        $replaceVal = Ue::sessionDepartmentId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessionuserkeydepartmentid') {
                        $replaceVal = Ue::sessionUserKeyDepartmentId();
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':sessionpersonid') {
                        $replaceVal = Session::get(SESSION_PREFIX.'personid');
                        $isReplaceVal = true;
                    } elseif ($matchLower == ':fiscalperiodid') {
                        $replaceVal = Ue::sessionFiscalPeriodId();
                        $isReplaceVal = true;
                    } elseif ($criterias = Input::post('criteria')) {
                        
                        foreach ($criterias as $criteriaColumn => $criteria) {
                            if (':'.strtolower($criteriaColumn) == $matchLower) {
                                $replaceVal = ($criteria[0]['operand'] != '') ? "'".$criteria[0]['operand']."'" : 'NULL';
                                $isReplaceVal = true;
                                unset($_POST['criteria'][$criteriaColumn]);
                            }
                        }
                    }
                    
                    if ($isReplaceVal) {
                        $queryString = str_ireplace($match, $replaceVal, $queryString);
                    }
                }
            }
            
            $queryString = self::replaceKpiDbSchemaName($queryString);
        }
        
        return $queryString;
    }
    
    public function replaceKpiDbSchemaName($qryStr) {
        
        $schemaName = Config::getFromCache('kpiDbSchemaName');
        $schemaName = $schemaName ? $schemaName.'.' : '';
        $qryStr = str_ireplace('[kpiDbSchemaName]', $schemaName, $qryStr);
        
        if (!Config::getFromCache('is_dev')) {
            $qryStr = str_ireplace('vt_data.', '', $qryStr);
        }
        
        return $qryStr;
    }
    
    public function isOwnerDepartmentModel($indicatorId, $sessionCompanyDepartmentId) {
        
        try {
            
            $schemaName = Config::getFromCache('kpiDbSchemaName');
            $schemaName = $schemaName ? rtrim($schemaName, '.') . '.' : '';
            
            $jsonData = $this->db->GetOne("SELECT DATA FROM ".$schemaName."V_DOMAIN_META WHERE SRC_RECORD_ID = ".$this->db->Param(0), array($indicatorId));
            
            if ($jsonData && strpos($jsonData, '"OWNERS"') !== false) {
                
                $arrData = json_decode($jsonData, true);
                $owners = $arrData['OWNERS'];
                
                foreach ($owners as $owner) {
                    if ($owner['COMPANY_DEPARTMENT_ID'] == $sessionCompanyDepartmentId) {
                        return false;
                    }
                }
            }
            
            return true;
            
        } catch (Exception $ex) {
            return true;
        }
    }
    
    public function indicatorDataGridModel() {
        
        try {
            
            $cache = phpFastCache();
            
            if (Input::post('ignoreFirstLoad') == 'true') {
                return array('status' => 'ignoreFirstLoad', 'rows' => array(), 'total' => 0);
            }
        
            $indicatorId = Input::numeric('indicatorId');
        
            $row = self::getKpiIndicatorRowModel($indicatorId); 
            
            $tableName = $row['TABLE_NAME'];
            $queryString = self::parseQueryString($row['QUERY_STRING']);
            
            $isTableName = ($tableName) ? true : false;
            $isQueryString = ($queryString) ? true : false;
            
            if ($isTableName == false && $isQueryString == false) {
                return array('status' => 'error', 'message' => '', 'rows' => array(), 'total' => 0); /*Invalid table_name!*/
            }

            $page = Input::numeric('page', 1);
            $rows = Input::numeric('rows', 50);
            $offset = ($page - 1) * $rows;
            
            $isExportExcel = Input::numeric('isExportExcel');
            $isShowPivot   = Input::numeric('isShowPivot');
            $isGoogleMap   = Input::numeric('isGoogleMap');
            $isComboData   = Input::numeric('isComboData');
            
            $isUseWorkflow             = $row['IS_USE_WORKFLOW'];
            $isAddonPhoto              = $row['IS_ADDON_PHOTO'];
            $isUseCompanyDepartmentId  = $row['IS_USE_COMPANY_DEPARTMENT_ID'];
            $isDataCompanyDepartmentId = $row['IS_DATA_COMPANY_DEPARTMENT_ID'];
            $langCode                  = Lang::getCode();
            
            $isCheckSystemTable = $isQueryString ? true : self::isCheckSystemTable($tableName);
            $isSubCondition = false;
            
            $fields = $sortField = $aggregateField = $coordinateField = $polylineField = $polygonField = $polylineFieldGroupBy = $polylineConnectionField = $subCondition = '';
            $mainCondition = 'DELETED_USER_ID IS NULL';
            
            if ($isShowPivot || $isQueryString) {
                $row['isIgnoreStandardFields'] = true;
            }
            
            if ($isCheckSystemTable) {
                $mainCondition = '1 = 1';
            }
            
            if ($isUseCompanyDepartmentId && Input::numeric('isIgnoreCompanyDepartmentId') != 1) { 
                
                $sessionValues = Session::get(SESSION_PREFIX.'sessionValues');
                $sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid']);
                
                if ($sessionCompanyDepartmentId && $sessionCompanyDepartmentId != '1') {                    
                    $mainCondition .= ' AND COMPANY_DEPARTMENT_ID = '.$sessionCompanyDepartmentId;
                }
            }        
            
            if (Input::isEmpty('postHiddenParams') == false) {
                
                $hiddenParams = Input::post('postHiddenParams');
                $hiddenParams = Crypt::decrypt($hiddenParams);
                $hiddenParams = @json_decode($hiddenParams, true);

                if (isset($hiddenParams['mainIndicatorId']) && isset($hiddenParams['id'])) {
                    
                    $mainCondition .= " AND ID IN (
                        SELECT 
                            TRG_RECORD_ID 
                        FROM META_DM_RECORD_MAP 
                        WHERE SRC_REF_STRUCTURE_ID = ".$hiddenParams['mainIndicatorId']." 
                            AND SRC_RECORD_ID = ".$hiddenParams['id']." 
                            AND TRG_REF_STRUCTURE_ID = ".$indicatorId."        
                    )";
                    
                } else {
                    throw new Exception('Invalid request!');
                }
            }
            
            $sortColumns = $showColumns = $columnsConfig = array();
            
            $columns = self::getKpiIndicatorColumnsModel($indicatorId, $row);
            
            if ($isQueryString) {
                
                unset(self::$indicatorColumns[$indicatorId]);
                
                $row['isFilter'] = true;
                $filterParams = self::getKpiIndicatorColumnsModel($indicatorId, $row);
                $replaceQueryString = "($queryString)";
                $tableName = $replaceQueryString;
                
                foreach ($filterParams as $filterParam) {
                    if ($filterParam['TRG_ALIAS_NAME'] != '') {
                        $replaceQueryString = str_ireplace(':'.$filterParam['TRG_ALIAS_NAME'], "''", $replaceQueryString);
                    }
                }
                
                $dbColumns = self::table_exists($this->db, $replaceQueryString);
                
                unset(self::$indicatorColumns[$indicatorId]);
                
            } else {
                $dbColumns = self::table_exists($this->db, $tableName);
            }
            
            $idField = 'ID';
            
            foreach ($columns as $c => $column) {
                
                if ($column['COLUMN_NAME'] && $column['SHOW_TYPE'] != 'label') {
                    
                    if ($column['SORT_ORDER'] && $column['SORT_TYPE']) {
                        
                        $sortColumns[$column['SORT_ORDER'].'_'.$c] = $column;
                    }

                    if ($column['COLUMN_AGGREGATE']) {
                        
                        $aggregateField .= $column['COLUMN_AGGREGATE'] . '(TO_NUMBER(REPLACE(T0.'.$column['COLUMN_NAME'].', \',\'))) AS '.$column['COLUMN_NAME'].',';
                    }
                    
                    if ($column['INPUT_NAME'] == 'META_VALUE_ID') {
                        
                        $idField = strtoupper($column['COLUMN_NAME']);
                        
                    } elseif ($column['INPUT_NAME'] == 'META_VALUE_NAME') {
                        
                        $polylineFieldGroupBy = strtoupper($column['COLUMN_NAME']);
                    }
                    
                    if ($isGoogleMap) {
                        
                        if ($column['SHOW_TYPE'] == 'coordinate' || $column['SHOW_TYPE'] == 'coordinate_auto') {
                            $coordinateField = $column['COLUMN_NAME'];
                        } elseif ($column['SHOW_TYPE'] == 'polyline') {
                            $polylineField = $column['COLUMN_NAME'];
                        } elseif ($column['SHOW_TYPE'] == 'polygon') {
                            $polygonField = $column['COLUMN_NAME'];
                        } elseif ($column['SHOW_TYPE'] == 'polyline_connection') {
                            $polylineConnectionField = $column['COLUMN_NAME'];
                        }
                        
                        if ($column['IS_RENDER'] == '1') {
                            $showColumns[] = $column;
                        }
                    }
                    
                    $columnsConfig[$column['COLUMN_NAME']] = array('showType' => $column['SHOW_TYPE'], 'isTranslate' => issetParam($column['IS_TRANSLATE']));
                    
                    if (issetParam($column['IS_FILTER']) == '1' 
                        && $column['COLUMN_NAME'] != '' 
                        && $column['DEFAULT_VALUE'] != '' 
                        && !Input::postCheck('criteria') 
                        && !Input::postCheck('filterData')) {
                        
                        $filterDefaultValue = Mdmetadata::setDefaultValue($column['DEFAULT_VALUE']);
                        
                        if (in_array($column['SHOW_TYPE'], array('bigdecimal', 'decimal', 'number', 'long', 'date'))) {
                            
                            $_POST['filterData'][$column['COLUMN_NAME']][] = array(
                                'begin' => $filterDefaultValue, 'end' => $filterDefaultValue
                            );
                            
                        } else {
                            $_POST['filterData'][$column['COLUMN_NAME']][] = $filterDefaultValue;
                        }
                    }
                }
            }
            
            if ($polylineField) {
                
                $result = $cache->get('mvPolylineData_'.$indicatorId);

                if ($result) {
                    return $result;
                }
            }
            
            if ($isExportExcel || $isShowPivot || $isQueryString) {
                
                if ((!$isExportExcel && !$isShowPivot) && isset($dbColumns['ID'])) {
                    $fields .= 'T0.ID, ';
                }

                foreach ($columns as $colName) {
                    
                    if ($colName['COLUMN_NAME'] == 'WFM_STATUS_NAME' || (($isExportExcel || $isShowPivot) && $colName['IS_RENDER'] != '1')) {
                        continue;
                    }
                    
                    $columnName = (!$isCheckSystemTable && ($colName['SHOW_TYPE'] == 'combo' || $colName['SHOW_TYPE'] == 'popup' || $colName['SHOW_TYPE'] == 'radio')) ? $colName['COLUMN_NAME'] . '_DESC' : $colName['COLUMN_NAME'];
                    
                    if (isset($dbColumns[$columnName])) {
                        $fields .= 'T0.'.$columnName . ', ';
                    }
                }
                
            } elseif ($dbColumns) {
                    
                foreach ($dbColumns as $colName => $colType) {

                    if ($colName == 'DATA') {
                        continue;
                    }

                    if (isset($columnsConfig[$colName]) && $columnsConfig[$colName]['isTranslate']) {
                        $fields .= 'CASE
                        WHEN LOWER(\''.$langCode.'\') = \'mn\' THEN T0.'.$colName . 
                        ' ELSE FNC_TRANSLATE(\''.$langCode.'\', T0.TRANSLATION_VALUE, \''.$colName . '\', T0.'.$colName.')
                       END AS '.$colName . ', ';
                    } else {
                        $fields .= 'T0.'.$colName . ', ';
                    }
                }
            }
            
            if ($sortColumns) {
                
                ksort($sortColumns);
                
                foreach ($sortColumns as $sortColumn) {
                    
                    if (isset($dbColumns[$sortColumn['COLUMN_NAME']])) {
                        $sortField .= $sortColumn['COLUMN_NAME'] . ' ' . $sortColumn['SORT_TYPE'] . ', ';
                    }
                }
                
                $sortField = rtrim(trim($sortField), ',');
            }

            if (Input::postCheck('sort') && Input::postCheck('order')) {
                
                $sortField = Input::post('sort') . ' ' . Input::post('order');
            }
            
            if (Input::postCheck('sortFields')) {
                
                parse_str(Input::post('sortFields'), $sortFields);
                
                if (count($sortFields) > 0) {
                    
                    foreach ($sortFields as $sortKey => $sortType) {
                        
                        $sortType = strtoupper($sortType);
                        
                        if ($sortType != 'ASC' && $sortType != 'DESC') {
                            $sortType = 'ASC';
                        }
                        
                        $sortField = Input::param($sortKey) . ' ' . Input::param($sortType);
                    }
                }
            }
            
            if ($sortField) {
                $sortField .= ", T0.$idField DESC";
            } else {
                $sortField = "T0.$idField DESC";
            }
            
            if (Input::postCheck('filterRules')) {

                $filterRules = json_decode($_POST['filterRules'], true);

                foreach ($filterRules as $rule) {

                    $field = $rule['field'];
                    $value = Input::param(Str::lower($rule['value']));

                    if ($value != '') {
                        $value = self::fixFilterColValue($value);
                        $subCondition .= " AND (LOWER($field) LIKE '%$value%')";
                        $isSubCondition = true;
                    }
                }
            }
            
            $columnDataGrouping = array();
            
            if ($criterias = Input::post('criteria')) {
                
                $filterData = array();
                
                foreach ($criterias as $criteriaColumn => $criteria) {
                    foreach ($criteria as $criteriaRow) {
                        $filterData[$criteriaColumn][] = $criteriaRow;
                    }
                }
            } else {
                $filterData = Input::post('filterData');
            }
            
            if ($filterData) {
                
                foreach ($filterData as $filterColName => $filterColVals) {
                    
                    if (strpos($filterColName, '_groupingSum') !== false) {
                        
                        $filterColNameArr = explode('_groupingSum', $filterColName);
                        $columnDataGrouping[$filterColNameArr[0]] = 1;
                        
                        continue;
                    }
                    
                    if (isset($filterColVals[0]['begin'])) {
                        
                        foreach ($filterColVals as $filterColVal) {
                            
                            $filterShowType = $columnsConfig[$filterColName]['showType'];
                            $filterColBeginVal = $filterColVal['begin'];
                            $filterColEndVal = $filterColVal['end'];
                            
                            if ($filterShowType == 'bigdecimal' || $filterShowType == 'decimal' || $filterShowType == 'number') {
                                
                                $subCondition .= " AND $filterColName BETWEEN $filterColBeginVal AND $filterColEndVal";
                                
                            } elseif ($filterShowType == 'date') {
                                
                                $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD');
                                
                            } elseif ($filterShowType == 'datetime') {
                                
                                $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD HH24:MI:SS')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD HH24:MI:SS');
                            }
                        }
                        
                    } elseif (isset($filterColVals[0]['operator'])) { 
                        
                        $orCriteria = '';
                        
                        foreach ($filterColVals as $filterColVal) {
                            $orCriteria .= "LOWER($filterColName) ".$filterColVal['operator']." '".Str::lower(self::fixFilterColValue($filterColVal['operand']))."' OR ";
                        }
                        
                        $orCriteria = rtrim(trim($orCriteria), ' OR');
                        $subCondition .= " AND ($orCriteria)";
                        
                    } elseif (!is_array($filterColVals)) {
                        
                        $tableName = str_ireplace(':'.$filterColName, "'".$filterColVals."'", $tableName);
                        
                    } else {
                        
                        if (stripos($tableName, ':'.$filterColName) !== false) {
                            
                            $tableName = str_ireplace(':'.$filterColName.' is null', '1 IS NULL', $tableName);
                            $tableName = str_ireplace(':'.$filterColName.' is not null', '1 IS NOT NULL', $tableName);
                            $tableName = str_ireplace(':'.$filterColName, "'".Arr::implode_r("','", $filterColVals, true)."'", $tableName);
                            
                        } else {
                            
                            $subCondition .= ' AND ( ';

                            foreach ($filterColVals as $filterColVal) {
                                if ($filterColVal == 'isnull') {
                                    $subCondition .= " $filterColName = '' OR $filterColName IS NULL OR";
                                } else {
                                    $filterColVal = self::fixFilterColValue($filterColVal);
                                    $subCondition .= " $filterColName = '$filterColVal' OR";
                                }
                            }

                            $subCondition = rtrim($subCondition, 'OR');

                            $subCondition .= ' ) ';
                        }
                    }
                }
                
                $isSubCondition = true;
                
            } elseif (isset($filterParams)) {
                
                foreach ($filterParams as $filterParam) {
                    
                    if ($filterParam['TRG_ALIAS_NAME'] != '') {    
                        $bindVal = ($filterParam['DEFAULT_VALUE'] != '') ? "'".Mdmetadata::setDefaultValue($filterParam['DEFAULT_VALUE'])."'" : 'null';
                        $tableName = str_ireplace(':'.$filterParam['TRG_ALIAS_NAME'], $bindVal, $tableName);
                    }
                }
            }
            
            if ($whereClause = Input::postNonTags('whereClause')) {
                $subCondition .= " AND $whereClause";
            }
            
            $drillDownCriteria = Input::post('drillDownCriteria');
            
            if ($drillDownCriteria) {
                
                parse_str($drillDownCriteria, $defaultCriteriaData);
                
                if (is_array($defaultCriteriaData)) {
                    foreach ($defaultCriteriaData as $drillColumn => $drillVal) {
                        if ($drillVal != '') {
                            $subCondition .= " AND $drillColumn = '".self::fixFilterColValue(Input::param($drillVal))."'";
                            $isSubCondition = true;
                        }
                    }
                }
            }            
            
            if ($filterJson = Input::post('filter')) {
                
                $filterJson = html_entity_decode($filterJson, ENT_QUOTES, 'UTF-8');
                
                if (Json::isJson($filterJson)) {
                    $filterJson = @json_decode($filterJson, true);
                    foreach ($filterJson as $filterKey => $filterVal) {
                        if ($filterVal != '') {
                            $subCondition .= " AND T0.$filterKey = '".self::fixFilterColValue(Input::param($filterVal))."'";
                            $isSubCondition = true;
                        }
                    }
                }
            }
            
            $fields = rtrim(trim($fields), ',');
            
            if (Input::isEmpty('treeConfigs') == false) {

                parse_str(Input::post('treeConfigs'), $treeConfigs);
                $parentField = Input::param($treeConfigs['parent']);
                $idField = Input::param($treeConfigs['id']);
                
                if (Input::isEmpty('id') == false) {
                    
                    $id = Input::post('id');   
                    
                    $subCondition = " AND $parentField = $id";
                    $aggregateField = null;

                } else if (!$isSubCondition) {
                    
                    $subCondition .= " AND $parentField IS NULL";
                }
                
                $fields .= ", (SELECT COUNT(1) FROM $tableName WHERE $parentField = T0.$idField) AS CHILDRECORDCOUNT";
            }

            if ($isDataCompanyDepartmentId && array_key_exists('COMPANY_DEPARTMENT_ID', $dbColumns)) { 
                
                $sessionValues = Session::get(SESSION_PREFIX.'sessionValues');
                $sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid']);
                
                if ($sessionCompanyDepartmentId) {
                    
                    $isOwnerDepartment = self::isOwnerDepartmentModel($indicatorId, $sessionCompanyDepartmentId);
                    
                    if ($isOwnerDepartment) {
                        $mainCondition .= ' AND COMPANY_DEPARTMENT_ID = '.$sessionCompanyDepartmentId;
                    }
                }
            }              
            
            $subCondition = $mainCondition . $subCondition;
            
            $permissionCriteria = self::getUmPermissionKeyModel($indicatorId);
            
            if ($permissionCriteria) {
                $tableName = "(SELECT * FROM $tableName WHERE $permissionCriteria)";
            }
            
            /*if (Ue::sessionRoleId() != 1) {
                if (!self::getUmPermissionKeyPrecheckModel($indicatorId)) {
                    $tableName = "(SELECT * FROM $tableName WHERE 1=0)";
                } elseif ($permissionCriteria) {                    
                    $tableName = "(SELECT * FROM $tableName WHERE $permissionCriteria)";
                }
            }*/
            
            if ($isUseWorkflow) {
                
                $structureIndicatorId = $row['STRUCTURE_INDICATOR_ID'] ? $row['STRUCTURE_INDICATOR_ID'] : $indicatorId;
                $sessionUserKeyId = Ue::sessionUserKeyId();
                
                if ($isQueryString) {
                    $fields .= ', T0.WFM_STATUS_ID'; 
                }
                
                $wfmJoin = "
                    LEFT JOIN (
                        SELECT 
                            MWA.RECORD_ID, 
                            MWA.WFM_STATUS_ID, 
                            MWA.DESCRIPTION, 
                            MWA.USER_STATUS_ID, 
                            NULL AS TRANSLATION_VALUE 
                        FROM 
                            META_WFM_ASSIGNMENT MWA 
                        WHERE 
                            MWA.REF_STRUCTURE_ID = $structureIndicatorId 
                            AND COALESCE(MWA.IS_TRANSFERED, 0) = 0 
                            AND COALESCE(MWA.IS_ACTIVE, 1) = 1 
                            AND COALESCE(MWA.IS_EDIT, 2) = 2 
                            AND MWA.USER_ID = $sessionUserKeyId  
                        GROUP BY 
                            MWA.RECORD_ID, 
                            MWA.WFM_STATUS_ID, 
                            MWA.DESCRIPTION, 
                            MWA.USER_STATUS_ID
                    ) MWA ON T0.$idField = MWA.RECORD_ID AND T0.WFM_STATUS_ID = MWA.WFM_STATUS_ID 
                    LEFT JOIN (
                        SELECT 
                            TO_MWA.RECORD_ID, 
                            TO_MWA.WFM_STATUS_ID, 
                            COUNT(1) AS CNT 
                        FROM 
                            META_WFM_ASSIGNMENT TO_MWA 
                        WHERE 
                            TO_MWA.REF_STRUCTURE_ID = $structureIndicatorId 
                            AND COALESCE(TO_MWA.IS_ACTIVE, 1) = 1 
                            AND COALESCE(TO_MWA.IS_EDIT, 2) = 2 
                            AND TO_MWA.ASSIGNED_USER_ID = $sessionUserKeyId  
                        GROUP BY 
                            TO_MWA.RECORD_ID, 
                            TO_MWA.WFM_STATUS_ID
                    ) TO_MWA ON T0.$idField = TO_MWA.RECORD_ID AND T0.WFM_STATUS_ID = TO_MWA.WFM_STATUS_ID 
                    LEFT JOIN META_WFM_STATUS MWFS ON COALESCE(MWA.USER_STATUS_ID, T0.WFM_STATUS_ID) = MWFS.ID";
                
                $selectCount = "
                    SELECT 
                        $aggregateField 
                        COUNT(1) AS ROW_COUNT 
                    FROM $tableName T0 
                        $wfmJoin 
                    WHERE $subCondition";
                
                $selectList = "
                    SELECT 
                        $fields, 
                        (
                            CASE WHEN T0.WFM_STATUS_ID <> MWFS.ID 
                            THEN FNC_TRANSLATE('$langCode', MWA.TRANSLATION_VALUE, 'DESCRIPTION', MWA.DESCRIPTION) 
                            ELSE T0.WFM_DESCRIPTION END 
                        ) AS WFMDESCRIPTION, 
                        MWFS.WFM_STATUS_CODE AS WFMSTATUSCODE, 
                        (
                            CASE WHEN MWA.RECORD_ID IS NOT NULL 
                                AND MWA.USER_STATUS_ID IS NULL 
                                AND MWFS.TO_STATUS_NAME IS NOT NULL 
                            THEN FNC_TRANSLATE('$langCode', MWFS.TRANSLATION_VALUE, 'TO_STATUS_NAME', MWFS.TO_STATUS_NAME) 
                            WHEN TO_MWA.CNT > 0 
                                AND MWFS.FROM_STATUS_NAME IS NOT NULL 
                            THEN FNC_TRANSLATE('$langCode', MWFS.TRANSLATION_VALUE, 'FROM_STATUS_NAME', MWFS.FROM_STATUS_NAME) 
                            ELSE FNC_TRANSLATE('$langCode', MWFS.TRANSLATION_VALUE, 'WFM_STATUS_NAME', MWFS.WFM_STATUS_NAME) END
                        ) AS WFMSTATUSNAME, 
                        MWFS.WFM_STATUS_COLOR AS WFMSTATUSCOLOR    
                    FROM $tableName T0 
                        $wfmJoin 
                    WHERE $subCondition 
                    ORDER BY $sortField";

            } else {
                
                $selectCount = "
                    SELECT 
                        $aggregateField 
                        COUNT(1) AS ROW_COUNT 
                    FROM $tableName T0 
                    WHERE $subCondition";
                
                $selectList = "
                    SELECT 
                        $fields 
                    FROM $tableName T0 
                    WHERE $subCondition 
                    ORDER BY $sortField";
            }
            
            if ($isExportExcel || $isComboData) {
                
                $mainSortFields = '';
                
                if ($columnDataGrouping) {
                    
                    $fields = $groupByFields = '';
                    
                    foreach ($columns as $colName) {
                    
                        if ($colName['COLUMN_NAME'] == 'WFM_STATUS_NAME' || isset($columnDataGrouping[$colName['COLUMN_NAME']]) || $colName['REPORT_AGGREGATE_FUNCTION'] != '') {
                            continue;
                        }

                        $columnName = (!$isCheckSystemTable && ($colName['SHOW_TYPE'] == 'combo' || $colName['SHOW_TYPE'] == 'popup' || $colName['SHOW_TYPE'] == 'radio')) ? $colName['COLUMN_NAME'] . '_DESC' : $colName['COLUMN_NAME'];
                        
                        if ($colName['SHOW_TYPE'] == 'bigdecimal' || $colName['SHOW_TYPE'] == 'decimal' || $colName['SHOW_TYPE'] == 'number') {
                            
                            $colAggregate = strtoupper($colName['COLUMN_AGGREGATE'] ? $colName['COLUMN_AGGREGATE'] : 'SUM');
                            
                            if ($colAggregate == 'AVG') {
                                $fields .= $colAggregate.'(decode(T1.'.$columnName.', 0, null, T1.'.$columnName.')) AS '.$columnName.', ';
                            } else {
                                $fields .= $colAggregate.'(T1.'.$columnName.') AS '.$columnName.', ';
                            }
                            
                        } else {
                            $fields .= 'T1.'.$columnName.', ';
                            $groupByFields .= 'T1.'.$columnName.', ';
                        }
                    }
                    
                    $fields = rtrim(trim($fields), ',');
                    $groupByFields = rtrim(trim($groupByFields), ',');

                    $selectList = "SELECT $fields FROM ($selectList) T1 GROUP BY $groupByFields";
                    
                    if ($sortColumns) {
                        
                        ksort($sortColumns);

                        foreach ($sortColumns as $sortColumn) {
                            $mainSortFields .= 'PDD.'.$sortColumn['COLUMN_NAME'] . ' ' . $sortColumn['SORT_TYPE'] . ', ';
                        }

                        $mainSortFields = rtrim(trim($mainSortFields), ',');
                    }
                }
                
                $sql = 'SELECT '. ((DB_DRIVER == 'postgres9') ? 'ROW_NUMBER () OVER ()' : 'ROWNUM') .' AS RID, PDD.* FROM ('.$selectList.') PDD';
                
                if ($mainSortFields) {
                    $sql .= ' ORDER BY '.$mainSortFields;
                }
                        
                $this->db->StartTrans(); 
                $this->db->Execute(Ue::createSessionInfo());
            
                $rows = $this->db->GetAll($sql);
                
                $this->db->CompleteTrans();
                
                if (Input::numeric('isLowerCase')) {
                    $rows = Arr::changeKeyLower($rows);
                }
                
                return array('status' => 'success', 'rows' => $rows);
                
            } elseif ($isShowPivot) {
                
                return array('status' => 'success', 'sql' => $selectList);
            }
            
            $this->db->StartTrans(); 
            $this->db->Execute(Ue::createSessionInfo());

            $rowCount = $this->db->GetRow($selectCount);
            
            $result['status'] = 'success';
            $result['total'] = $rowCount['ROW_COUNT'];
            $result['rows'] = array();
            
            if ($isGoogleMap && $polylineField) {
                
                $rows = $this->db->GetAll($selectList);
                
                if ($rows) {
                    
                    $checkGroupingIndex = 0;
                    $groupingRows = $checkGroupingRows = array();
        
                    foreach ($rows as $k => $row) {
                        
                        $rowId = $row[$polylineFieldGroupBy] . '_' . $row['C4'];
                                
                        if (isset($checkGroupingRows[$rowId])) {
                            
                            $groupingRows[$rowId . '_' . $checkGroupingIndex][] = $row;
                            
                        } else {
                            
                            $checkGroupingIndex++;
                            
                            $groupingRows[$rowId . '_' . $checkGroupingIndex][] = $row;
                            $checkGroupingRows[$rowId] = 1;
                        }
                    }
                    
                    $rows = $groupingRows;
                }
                
                $rs = new stdClass();
                $rs->_array = $rows;
   
            } else {
                
                $rs = $this->db->SelectLimit($selectList, $rows, $offset);
            }
            
            $this->db->CompleteTrans();

            if (isset($rs->_array)) {
                
                $rows = $rs->_array;                  
                $childRecordCountKey = 'CHILDRECORDCOUNT';
                
                if ($isUseWorkflow) {

                    array_walk($rows, function(&$value) {
                        
                        $value['id'] = issetParam($value['ID']);
                        $value['wfmstatusid'] = $value['WFM_STATUS_ID'];
                        $value['wfmstatuscode'] = $value['WFMSTATUSCODE'];
                        $value['wfmstatusname'] = $value['WFMSTATUSNAME'];
                        $value['wfmstatuscolor'] = $value['WFMSTATUSCOLOR'];
                        $value['iskpiindicator'] = 1;
                    }); 
                }
                
                if (Input::numeric('isLowerCase')) {
                    $rows = Arr::changeKeyLower($rows);
                    $childRecordCountKey = 'childrecordcount';
                }
                
                if ($aggregateField) {
                    $result['footer'] = array($rowCount);
                }
                
                if (isset($treeConfigs)) {
                        
                    if (isset($rows[0])) {
                        foreach ($rows as $rowIndex => $rowData) {
                            $rows[$rowIndex]['state'] = (isset($rowData[$childRecordCountKey]) && $rowData[$childRecordCountKey]) ? 'closed' : 'open';
                        }
                    }

                    if (Input::postCheck('id')) {

                        unset($result['total']);
                        unset($result['rows']);
                        unset($result['status']);
                        unset($result['footer']);
                        
                        $result = $rows;

                    } else {
                        $result['rows'] = $rows;
                    }

                } else {
                    $result['rows'] = $rows;
                }
            }
            
            if ($isGoogleMap) {

                $result['coordinateField'] = $coordinateField;
                $result['polylineField'] = $polylineField;
                $result['polygonField'] = $polygonField;
                $result['polylineConnectionField'] = $polylineConnectionField;
                $result['showColumns'] = $showColumns;
                $result['isAddonPhoto'] = $isAddonPhoto;
            }            
            
            if ($polylineField) {
                $cache->set('mvPolylineData_'.$indicatorId, $result, Mdwebservice::$expressionCacheTime);
            }

            return $result;
        
        } catch (Exception $ex) {
            
            $exceptionMessage = $ex->getMessage();
            
            return self::responseGridExceptionMessage($exceptionMessage);
        }
    }
    
    public function responseGridExceptionMessage($message) {
        
        if (strpos($message, 'ORA-00942') !== false) {
            $response = array('status' => 'success', 'message' => $message, 'rows' => array(), 'total' => 0);
        } else {
            $response = array('status' => 'error', 'message' => $message, 'rows' => array(), 'total' => 0);
        }
        
        return $response;
    }
    
    public function fixFilterColValue($val) {
        return str_replace("'", "''", $val);
    }
    
    public function getUmPermissionKeyModel($indicatorId) {
        
        $result = null;
        
        try {

            $data = $this->db->GetAll("
            SELECT 
                DISTINCT 
                TO_CHAR(R.CRITERIA) as INDICATOR_CRITERIA
            FROM UM_PERMISSION_KEY K
            INNER JOIN REF_SEGMENTATION R ON K.SEGMENTATION_ID = R.ID
                WHERE 
                    (
                        CASE 
                        WHEN K.USER_ID = :sessionUserId AND :sessionUserId != (SELECT COALESCE((SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1),9) FROM DUAL)
                        THEN 1
                        WHEN K.ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId) AND :sessionUserId != (SELECT COALESCE((SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1), 9) FROM DUAL)
                        THEN 1 
                        WHEN K.USER_ID IS NULL AND K.ROLE_ID IS NULL
                        THEN 1 
                        WHEN :sessionUserId = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1) OR :sessionUserId = 1
                        THEN 0
                        ELSE 0
                        END = 1
                    ) 
                    AND K.INDICATOR_ID = :filterMainId 
                    AND R.CRITERIA IS NOT NULL", 
                array(
                    'filterMainId'  => $indicatorId, 
                    'sessionUserId' => Ue::sessionUserKeyId() 
                )
            );            
            
            // $data = $this->db->GetAll("
            //     SELECT 
            //         DISTINCT 
            //         INDICATOR_CRITERIA
            //     FROM UM_PERMISSION_KEY 
            //     WHERE 
            //         (
            //             CASE 
            //             WHEN USER_ID = :sessionUserId AND :sessionUserId != (SELECT COALESCE((SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1),9) FROM DUAL)
            //             THEN 1
            //             WHEN ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId) AND :sessionUserId != (SELECT COALESCE((SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1), 9) FROM DUAL)
            //             THEN 1 
            //             WHEN USER_ID IS NULL AND ROLE_ID IS NULL
            //             THEN 1 
            //             WHEN :sessionUserId = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :sessionUserId AND ROLE_ID = 1) OR :sessionUserId = 1
            //             THEN 0
            //             ELSE 0
            //             END = 1
            //         ) 
            //         AND INDICATOR_ID = :filterMainId 
            //         AND INDICATOR_CRITERIA IS NOT NULL", 
            //     array(
            //         'filterMainId'  => $indicatorId, 
            //         'sessionUserId' => Ue::sessionUserKeyId() 
            //     )
            // );

            if ($data) {

                foreach ($data as $row) {

                    if ($row['INDICATOR_CRITERIA'] != '') { 
                        $criteria = self::replaceNamedParameters(html_entity_decode($row['INDICATOR_CRITERIA'])); 
                        $result .= '('.$criteria.') AND ';
                    }
                }

                $result = rtrim(trim($result), 'AND');
            }
        
        } catch (Exception $ex) {
            $result = $result;
        }
        
        return $result;
    }
    
    public function getUmPermissionKeyPrecheckModel($indicatorId) {
        
        $result = null;
        
        try {

            $data = $this->db->GetAll("
            SELECT K.ID FROM UM_PERMISSION_KEY K
                WHERE 
                (
                CASE WHEN :SESSIONUSERID = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = :SESSIONUSERID AND ROLE_ID = 1)
                THEN 1
                WHEN K.ROLE_ID NOT   IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = :SESSIONUSERID)
                THEN 0
                ELSE 1
                END = 1
                )
                AND
                K.INDICATOR_ID = :FILTERMAINID", 
                array(
                    'FILTERMAINID'  => $indicatorId, 
                    'SESSIONUSERID' => Ue::sessionUserKeyId() 
                )
            );            
            
            return $data;
        
        } catch (Exception $ex) {
            $result = $result;
        }
        
        return $result;
    }
    
    public function getKpiDashboardFilterMapModel($srcIndicatorId, $trgIndicatorId) {
        
        $data = $this->db->GetAll("
            SELECT 
                UPPER(t3.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                T3.LABEL_NAME AS SRC_LABEL_NAME,  
                T3.SHOW_TYPE AS SRC_SHOW_TYPE, 
                UPPER(T4.COLUMN_NAME) AS TRG_COLUMN_NAME, 
                T4.LABEL_NAME AS TRG_LABEL_NAME,  
                T4.SHOW_TYPE AS TRG_SHOW_TYPE 
            FROM KPI_DATAMODEL_MAP T0 
                INNER JOIN KPI_DATAMODEL_MAP_KEY T1 ON T1.MAIN_INDICATOR_ID = T0.SRC_INDICATOR_ID 
                    AND T1.TRG_INDICATOR_ID = T0.TRG_INDICATOR_ID 
                INNER JOIN KPI_DATAMODEL_MAP_KEY_DTL T2 ON T2.DATAMODEL_MAP_KEY_ID = T1.ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T3 ON T3.ID = T2.SRC_INDICATOR_MAP_ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T4 ON T4.ID = T2.TRG_INDICATOR_MAP_ID  
            WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                AND T0.TRG_INDICATOR_ID = ".$this->db->Param(1)."  
                AND T3.IS_FILTER = 1 
            ORDER BY T3.ORDER_NUMBER ASC", array($srcIndicatorId, $trgIndicatorId));
        
        return $data;
    }
    
    public function removeKpiDynamicDataModel() {
        
        try {
            
            $indicatorId = Input::numeric('indicatorId');
            $crudIndicatorId = Input::numeric('crudIndicatorId');
            
            if (!$indicatorId) {
                return array('status' => 'error', 'message' => 'Invalid indicator id!');
            }
            
            if (!$crudIndicatorId) {
                return array('status' => 'error', 'message' => 'Invalid crudIndicator id! Please refresh your browser.');
            }
        
            $row = self::getKpiIndicatorRowModel($indicatorId);
            
            if (!isset($row['TABLE_NAME'])) {
                return array('status' => 'error', 'message' => 'Invalid table_name!');
            }
            
            $tableName    = $row['TABLE_NAME'];
            $selectedRows = Input::post('selectedRows');
            $idField      = Input::post('idField');
            $idField      = $idField ? $idField : 'ID';
            $ids          = Input::param(Arr::implode_key(',', $selectedRows, $idField, true));
            
            $isSystemTable = self::isCheckSystemTable($tableName);
            $criteriaCount = self::getCountIndicatorActionCriteria($indicatorId, $crudIndicatorId);
            
            if ($criteriaCount['status'] == 'error') {
                return $criteriaCount;
            }
            
            if ((int) $criteriaCount['count'] > 0) {
                
                $checkResult = self::checkMethodAccessModel($indicatorId, $crudIndicatorId, $selectedRows);
                
                if ($checkResult['status'] != 'success') {
                    return $checkResult;
                }
            }
            
            if ($isSystemTable == false) {
                
                $sessionValues = Session::get(SESSION_PREFIX . 'sessionValues');
                $sessionName   = issetDefaultVal($sessionValues['sessionusername'], Ue::getSessionPersonWithLastName());

                $updateData = array(
                    'DELETED_DATE'      => Date::currentDate('Y-m-d H:i:s'), 
                    'DELETED_USER_ID'   => Ue::sessionUserKeyId(), 
                    'DELETED_USER_NAME' => $sessionName
                );

                $result = $this->db->AutoExecute($tableName, $updateData, 'UPDATE', "$idField IN ($ids)");
                
            } else {
                $result = $this->db->Execute("DELETE FROM $tableName WHERE $idField IN ($ids)");
            }
            
            if ($result) {
                
                self::runGenerateKpiRelationDataMartByIndicatorId($indicatorId);
                self::runGenerateKpiDataMartByIndicatorId($indicatorId, $ids);
                
                return array('status' => 'success', 'indicatorId' => $indicatorId, 'message' => $this->lang->line('msg_delete_success'));
            }
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function checkMethodAccessModel($mainIndicatorId, $actionIndicatorId, $rows = array()) {
        
        if ($mainIndicatorId && $actionIndicatorId) {
            $criterias = self::getIndicatorActionCriteriaByPermission($mainIndicatorId, $actionIndicatorId);
            
            if ($criterias) {
                
                if (!$rows) {
                    if ($selectedRows = Input::post('selectedRow')) {
                        $rows = $selectedRows;
                    } elseif ($selectedRows = Input::post('fillSelectedRow')) {
                        $rows = $selectedRows;
                    } elseif ($selectedRows = Input::post('fillDynamicSelectedRow')) {
                        $rows = $selectedRows;
                    }
                }
                
                if ($rows) {
                    
                    $selectedRows = !isset($rows[0]) ? array($rows) : $rows;
                    
                    foreach ($selectedRows as $selectedRow) {
                    
                        $selectedRow = Arr::changeKeyLower($selectedRow);

                        foreach ($criterias as $criteriaRow) {
                            $criteria = $criteriaRow['CRITERIA'];
                            $message = $this->lang->line($criteriaRow['MESSAGE']);

                            foreach ($selectedRow as $sk => $sv) {

                                if (!is_array($sv)) {

                                    if (is_string($sv) && strpos($sv, "'") === false) {
                                        $sv = "'".Str::lower($sv)."'";
                                    } elseif (is_null($sv)) {
                                        $sv = "''";
                                    }

                                    $sk = ($sk == '' ? 'tmpkey' : $sk);

                                    $criteria = preg_replace('/\b'.$sk.'\b/u', $sv, $criteria);
                                }
                            }

                            try {
                                
                                ob_start();
                                $isCheck = !eval(sprintf('return (%s);', $criteria));
                                $error = ob_get_contents();
                                ob_end_clean();

                                if ($error) {
                                    return array('status' => 'warning', 'message' => Mdcommon::parseCodeErrorMsg($error));
                                } elseif ($isCheck) {
                                    
                                    $lastError = error_get_last();
                                    if (isset($lastError['file']) && strpos($lastError['file'], 'mdform_model') !== false) {
                                        return array('status' => 'warning', 'message' => Mdcommon::parseCodeErrorMsg($lastError['message']));
                                    } 

                                    return array('status' => 'warning', 'message' => $message);
                                }
            
                            } catch (ParseError $p) {
                                return array('status' => 'error', 'message' => $p->getMessage());
                            } catch (Error $p) {
                                return array('status' => 'error', 'message' => $p->getMessage());
                            } catch (Throwable $p) {
                                return array('status' => 'error', 'message' => $p->getMessage());
                            } catch (Exception $p) {
                                return array('status' => 'error', 'message' => $p->getMessage());
                            }
                        }
                    }
                } else {
                    return array('status' => 'warning', 'message' => $this->lang->line('msg_pls_list_select'));
                }
            }
        }
        
        return array('status' => 'success');
    }
    
    public function getCountIndicatorActionCriteria($mainIndicatorId, $actionIndicatorId) {
        
        try {
            
            $count = $this->db->GetOne("
                SELECT 
                    COUNT(1)
                FROM UM_PERMISSION_KEY PK 
                    INNER JOIN REF_SEGMENTATION RS ON RS.ID = PK.SEGMENTATION_ID
                WHERE RS.INDICATOR_ID = ".$this->db->Param(0)." 
                    AND PK.INDICATOR_ID = ".$this->db->Param(1)." 
                    AND RS.CRITERIA IS NOT NULL", 
                array($mainIndicatorId, $actionIndicatorId));
            
            return array('status' => 'success', 'count' => $count);
            
        } catch (ADODB_Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function getIndicatorActionCriteriaByPermission($mainIndicatorId, $actionIndicatorId) {
        
        $idPh1 = $this->db->Param(0);
        $idPh2 = $this->db->Param(1);
        $idPh3 = $this->db->Param(2);
        
        $data = $this->db->GetAll("
            SELECT 
                T1.MESSAGE, 
                LOWER(T1.CRITERIA) AS CRITERIA 
            FROM (
                SELECT 
                    PK.SEGMENTATION_ID 
                FROM UM_PERMISSION_KEY PK 
                    INNER JOIN REF_SEGMENTATION RS ON RS.ID = PK.SEGMENTATION_ID 
                    LEFT JOIN UM_USER_ROLE UR ON UR.ROLE_ID = PK.ROLE_ID 
                        AND UR.IS_ACTIVE = 1 
                        AND UR.USER_ID = $idPh3 
                    LEFT JOIN UM_USER UK ON UK.USER_ID = PK.USER_ID 
                        AND UK.USER_ID = $idPh3 
                WHERE RS.INDICATOR_ID = $idPh1 
                    AND PK.INDICATOR_ID = $idPh2 
                    AND RS.CRITERIA IS NOT NULL 
                    AND (UR.USER_ID IS NOT NULL OR UK.USER_ID IS NOT NULL) 
                GROUP BY  
                    PK.SEGMENTATION_ID
            ) T0 
                INNER JOIN REF_SEGMENTATION T1 ON T1.ID = T0.SEGMENTATION_ID", 
            array($mainIndicatorId, $actionIndicatorId, Ue::sessionUserKeyId()));
        
        return $data;
    }
    
    public function removeAddonStructureFormModel() {
        
        $_POST['indicatorId'] = Input::numeric('trgIndicatorId');
        $_POST['selectedRows'] = array(array('ID' => Input::numeric('trgRecordId')));
        
        $result = self::removeKpiDynamicDataModel();
        
        if ($result['status'] == 'success') {
            
            $_POST['refStructureId'] = Input::numeric('srcIndicatorId');
            $_POST['sourceId'] = Input::numeric('srcRecordId');
            $_POST['trgRefStructureId'] = Input::numeric('trgIndicatorId');
            $_POST['trgSourceId'] = Input::numeric('trgRecordId');
            
            return self::bpRelationRemoveRowModel();
            
        } else {
            return $result;
        }
    }
    
    public function addRowKpiIndicatorTemplateModel() {
        
        try {
            
            $templateTableName = Input::post('templateTableName');
            $kpiMainIndicatorId = Input::numeric('kpiMainIndicatorId');
            $kpiIndicatorIndicatorMapId = Input::numeric('kpiIndicatorIndicatorMapId');
            
            $rowId = Input::numeric('rowId');
            $parentId = Input::numeric('parentId');
            $nextId = Input::numeric('nextId');
            
            $kpiTbl = Input::post('kpiTbl');
            $descName = Input::post('descName');
            $cellJson = Input::post('cellJson');
            $cellJsonComment = Input::post('cellJsonComment');
            $cellJsonExpression = Input::post('cellJsonExpression');
            $cellJsonDefaultValue = Input::post('cellJsonDefaultValue');
            $cellJsonStyle = Input::post('cellJsonStyle');
            
            $schemaName = self::getKpiDbSchemaName($kpiMainIndicatorId);
            
            $templateTableName = $templateTableName ? $templateTableName : $schemaName . 'T_'.$kpiIndicatorIndicatorMapId.'_TEMPLATE';
            $isTblCreated = self::table_exists($this->db, $templateTableName);
            
            $saveData = $dbField = $cellExpression = array();
            
            if (!$rowId || ($rowId && $nextId) || ($rowId && $parentId)) {
                
                if ($isTblCreated) {
                    $lastId = self::getKpiTemplateLastIdModel($templateTableName, 'ID');
                    $cellRowId = (int) $lastId + 1;
                } else {
                    $cellRowId = 1;
                }
                
            } else {
                $cellRowId = $rowId;
            }
            
            foreach ($kpiTbl as $columnName => $columnVal) {
                
                if (!is_numeric($columnName) && strpos($columnName, '_DESC') === false) {
                    
                    if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$columnName]))) {
                            
                        $dbField[] = array('type' => 'varchar', 'name' => $columnName);
                        $dbField[] = array('type' => 'varchar', 'name' => $columnName . '_DESC');
                        
                    } elseif ($isTblCreated && !isset($isTblCreated[$columnName . '_DESC'])) {
                        
                        $dbField[] = array('type' => 'varchar', 'name' => $columnName . '_DESC');
                    }
                    
                    $saveData[$columnName] = Input::param($columnVal);
                    $saveData[$columnName . '_DESC'] = issetVar($descName[$columnName]);
                }
            }
            
            if ($cellJson) {
                
                foreach ($cellJson as $cellJsonColumnName => $cellJsonColumnVal) {
                    
                    if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$cellJsonColumnName]))) {
                        $dbField[] = array('type' => 'varchar', 'length' => 4000, 'name' => $cellJsonColumnName);
                    }
                    
                    $cellJsonColumnVal = array(
                        'cellId' => $cellJsonColumnName .'.'. $cellRowId, 
                        'comment' => issetVar($cellJsonComment[$cellJsonColumnName]), 
                        'expression' => issetParam($cellJsonExpression[$cellJsonColumnName]), 
                        'defaultvalue' => issetParam($cellJsonDefaultValue[$cellJsonColumnName]), 
                        'style' => issetParam($cellJsonStyle[$cellJsonColumnName])
                    );
                    
                    $getValFromMartInputSrc = issetParamArray($_POST['cellJsonGetValFromMartInputSrc'][$cellJsonColumnName]);
                    
                    if ($getValFromMartInputSrc) {
                        $inputsArr = $outputsArr = array();
                        
                        foreach ($getValFromMartInputSrc as $indicatorId => $inputs) {
                            foreach ($inputs as $i => $inputSrcPath) {
                                
                                $inputTrgPath = issetVar($_POST['cellJsonGetValFromMartInputTrg'][$cellJsonColumnName][$indicatorId][$i]);
                                $inputDefaultVal = issetVar($_POST['cellJsonGetValFromMartInputDefaultVal'][$cellJsonColumnName][$indicatorId][$i]);
                                
                                if ($inputTrgPath != '' || $inputDefaultVal != '') {
                                    $cellJsonColumnVal['getValFromMart'][$indicatorId]['input'][$inputSrcPath] = array('trg' => $inputTrgPath, 'defaultVal' => $inputDefaultVal);
                                }
                            }
                        }
                        
                        $getValFromMartOutputSrc = issetParamArray($_POST['cellJsonGetValFromMartOutputSrc'][$cellJsonColumnName]);
                        
                        foreach ($getValFromMartOutputSrc as $indicatorId => $outputs) {
                            foreach ($outputs as $o => $outputSrcPath) {
                                
                                $outputAggregate = issetVar($_POST['cellJsonGetValFromMartOutputAggregate'][$cellJsonColumnName][$indicatorId][$o]);
                                $cellJsonColumnVal['getValFromMart'][$indicatorId]['output'][$outputSrcPath] = array('path' => $outputSrcPath, 'aggregate' => $outputAggregate);
                            }
                        }
                        
                        $cellJsonColumnVal['getValFromMart']['expression'] = issetVar($_POST['cellJsonGetValFromMartExpression'][$cellJsonColumnName]);
                        
                        $cellExpression[$cellJsonColumnVal['cellId']] = $cellJsonColumnVal['getValFromMart'];
                    }
                    
                    $saveData[$cellJsonColumnName] = json_encode($cellJsonColumnVal, JSON_UNESCAPED_UNICODE);
                }
            }
            
            if ($isTblCreated == false) {
                
                $createTblStatus = self::dbCreatedTblKpiTemplate($templateTableName, $dbField);
                
                if ($createTblStatus['status'] == 'error') {
                    return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                } else {
                    self::updateKpiIndicatorMapTblName($kpiIndicatorIndicatorMapId, $templateTableName);
                }
                
            } else {
                
                $standardFields = self::kpiTemplateTblStandardFields();
                
                foreach ($standardFields as $standardField) {
                    
                    if (!isset($isTblCreated[$standardField['name']])) {
                        
                        $dbField[] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                    }
                }
                
                if ($dbField) {
                
                    $alterTblStatus = self::dbAlterTblKpiDynamic($templateTableName, $dbField);

                    if ($alterTblStatus['status'] == 'error') {
                        return array('status' => 'error', 'message' => 'Alter table: ' . $alterTblStatus['message']);
                    }
                }
            }
            
            $sessionValues = Session::get(SESSION_PREFIX . 'sessionValues');
            $sessionName = issetDefaultVal($sessionValues['sessionusername'], Ue::getSessionPersonWithLastName());
            
            if (!$rowId || ($rowId && $nextId) || ($rowId && $parentId)) {
                
                $rowId = $cellRowId;
                $saveData['ID'] = $rowId;
                
                if ($nextId || $parentId) {
                    $rowIndex = self::getKpiTemplateRowIndexByIdModel($templateTableName, $nextId ? $nextId : $parentId);
                    $rowIndex = (int) $rowIndex + 1;
                    $saveData['ROW_INDEX'] = $rowIndex;
                }
                
                if ($rowId && $parentId) {
                    
                    $saveData['PARENT_ID'] = $parentId;
                    
                } elseif (!$nextId && !$parentId) {
                    
                    $lastRowIndex = self::getKpiTemplateLastIdModel($templateTableName, 'ROW_INDEX');
                    $saveData['ROW_INDEX'] = (int)$lastRowIndex + 1;
                }

                $saveData['CREATED_DATE'] = Date::currentDate('Y-m-d H:i:s');
                $saveData['CREATED_USER_ID'] = Ue::sessionUserKeyId();
                $saveData['CREATED_USER_NAME'] = $sessionName;
                
                $this->db->AutoExecute($templateTableName, $saveData);
                
                if ($nextId || $parentId) {
                    self::rowIndexResetingByNextIdModel($templateTableName, $rowId, $rowIndex);
                }
            
            } else {

                $saveData['MODIFIED_DATE'] = Date::currentDate('Y-m-d H:i:s');
                $saveData['MODIFIED_USER_ID'] = Ue::sessionUserKeyId();
                $saveData['MODIFIED_USER_NAME'] = $sessionName;

                $this->db->AutoExecute($templateTableName, $saveData, 'UPDATE', 'ID = '.$rowId);
            }
            
            if ($cellExpression) {
                
                $getCellExpression = $this->db->GetOne("SELECT ALL_CELL_EXPRESSION FROM KPI_INDICATOR_INDICATOR_MAP WHERE ID = ".$this->db->Param(0), array($kpiIndicatorIndicatorMapId));
                $getCellExpression = @json_decode($getCellExpression, true);
                $getCellExpression = issetParamArray($getCellExpression['getValFromMart']);
                $cellExpression = array_merge($getCellExpression, $cellExpression);
                
                $this->db->UpdateClob('KPI_INDICATOR_INDICATOR_MAP', 'ALL_CELL_EXPRESSION', json_encode(array('getValFromMart' => $cellExpression), JSON_UNESCAPED_UNICODE), 'ID = '.$kpiIndicatorIndicatorMapId);
            }
            
            $response = array('status' => 'success', 'message' => Lang::line('msg_save_success'), 'templateTableName' => $templateTableName, 'rowId' => $rowId);
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function rowIndexResetingByNextIdModel($tblName, $id, $rowIndex) {
        
        $idPh = $this->db->Param(0);
        
        $rows = $this->db->GetAll("
            SELECT 
                ID 
            FROM $tblName 
            WHERE ROW_INDEX >= (SELECT ROW_INDEX FROM $tblName WHERE ID = $idPh) 
                AND ID NOT IN ($idPh) 
            ORDER BY ROW_INDEX ASC", 
            array($id)
        );
        
        if ($rows) {
            $n = (int) $rowIndex;
            foreach ($rows as $row) {
                $n ++;
                $this->db->AutoExecute($tblName, array('ROW_INDEX' => $n), 'UPDATE', 'ID = '.$row['ID']);
            }
        }
        
        return true;
    }
    
    public function getKpiTemplateRowIndexByIdModel($tblName, $id) {
        
        $rowIndex = $this->db->GetOne("
            SELECT 
                ROW_INDEX 
            FROM $tblName 
                CONNECT BY NOCYCLE 
                PRIOR ID = PARENT_ID 
                START WITH ID = ".$this->db->Param(0)." 
            ORDER BY ROW_INDEX DESC", 
            array($id)
        );
        
        return $rowIndex;
    }
    
    public function getKpiTemplateLastIdModel($tblName, $colName) {
        $lastRowIndex = $this->db->GetOne("SELECT MAX($colName) FROM $tblName");
        return $lastRowIndex;
    }
    
    public function getKpiIndicatorTemplateRowModel($templateTableName = null, $rowId = null) {
        
        try {
            
            if (!$templateTableName) {
                $templateTableName = Input::post('templateTableName');
                $rowId = Input::numeric('rowId');
            }
            
            $rowData = $this->db->GetRow("
                SELECT 
                    TT.*, 
                    (SELECT COUNT(ID) FROM $templateTableName WHERE PARENT_ID = TT.ID) AS IS_CHILD 
                FROM $templateTableName TT
                WHERE TT.ID = ".$this->db->Param(0), 
                array($rowId)
            );
            
            $response = array('status' => 'success', 'rowData' => $rowData);
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function removeRowKpiDynamicTemplateModel() {
        
        try {
            
            $rowId = Input::numeric('rowId');
            $templateTableName = Input::post('templateTableName');
            
            $row = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowId);
            $row = issetParam($row['rowData']);
            
            if ($row) {
                
                if ($row['PARENT_ID'] && $row['IS_CHILD']) {
                    
                    $childRows = $this->db->GetAll("
                        SELECT 
                            ID 
                        FROM $templateTableName 
                        WHERE PARENT_ID = $rowId 
                        ORDER BY ROW_INDEX ASC");
                    
                    $this->db->AutoExecute($templateTableName, array('PARENT_ID' => $row['PARENT_ID']), 'UPDATE', 'ID IN ('.Arr::implode_key(',', $childRows, 'ID', true).')');
                }
                
                $this->db->Execute("DELETE FROM $templateTableName WHERE ID = ".$this->db->Param(0), array($rowId));
            }
            
            $response = array('status' => 'success', 'message' => $this->lang->line('msg_delete_success'));
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function directionRowKpiDynamicTemplateModel() {
        
         try {
            
            $direction = Input::post('direction');
            $templateTableName = Input::post('templateTableName');
            $rowId = Input::numeric('rowId');
            
            if ($direction == 'down') {
                
                $nextRowId = Input::numeric('nextRowId');
                
                $rowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowId);
                $rowData = issetParam($rowData['rowData']);
                
                $nextRowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $nextRowId);
                $nextRowData = issetParam($nextRowData['rowData']);
                
                if ($rowData && $nextRowData) {
                    
                    $isChildRow = $rowData['IS_CHILD'];
                    $isChildNext = $nextRowData['IS_CHILD'];
                    
                    if (!$rowData['PARENT_ID'] && !$nextRowData['PARENT_ID']) {
                        
                        if ($isChildNext) {
                            
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX'], 'PARENT_ID' => $nextRowId), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                        
                        } else {
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                        }
                        
                    } elseif (!$rowData['PARENT_ID'] && $rowId == $nextRowData['PARENT_ID']) {
                        
                        $firstRow = $this->db->GetRow("
                            SELECT 
                                T.ID, 
                                T.ROW_INDEX, 
                                T.PARENT_ID, 
                                (SELECT COUNT(ID) FROM $templateTableName WHERE PARENT_ID = T.ID) AS IS_CHILD
                            FROM $templateTableName T
                            WHERE T.ROW_INDEX >= (SELECT ROW_INDEX FROM $templateTableName WHERE ID = $rowId) 
                                AND T.ID NOT IN ($rowId) 
                                AND T.PARENT_ID IS NULL 
                            ORDER BY T.ROW_INDEX ASC");
                        
                        if ($firstRow) {
                            
                            if ($firstRow['IS_CHILD']) {
                                
                                $childRows = $this->db->GetAll("
                                    SELECT 
                                        ID, 
                                        ROW_INDEX, 
                                        PARENT_ID 
                                    FROM $templateTableName 
                                        CONNECT BY NOCYCLE 
                                        PRIOR ID = PARENT_ID  
                                        START WITH ID = $rowId   
                                    ORDER BY ROW_INDEX ASC");
                                
                                $nextChildRows = $this->db->GetAll("
                                    SELECT 
                                        ID, 
                                        ROW_INDEX, 
                                        PARENT_ID 
                                    FROM $templateTableName 
                                        CONNECT BY NOCYCLE 
                                        PRIOR ID = PARENT_ID  
                                        START WITH ID = ".$firstRow['ID']."  
                                    ORDER BY ROW_INDEX ASC");
                                
                                $lastRowIndex = (int) $childRows[0]['ROW_INDEX'];
                                $ids = '';
                                
                                foreach ($nextChildRows as $nextChildRow) {
                                    
                                    $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $lastRowIndex), 'UPDATE', 'ID = '.$nextChildRow['ID']);
                                    
                                    $lastRowIndex ++;
                                    
                                    $ids .= $nextChildRow['ID'].',';
                                }
                                
                                $rows = $this->db->GetAll("
                                    SELECT 
                                        ID 
                                    FROM $templateTableName 
                                    WHERE ROW_INDEX >= (SELECT ROW_INDEX FROM $templateTableName WHERE ID = $rowId) 
                                        AND ID NOT IN (".rtrim($ids, ',').") 
                                    ORDER BY ROW_INDEX ASC"
                                );
                                
                                foreach ($rows as $childRow) {
                                    
                                    $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $lastRowIndex), 'UPDATE', 'ID = '.$childRow['ID']);
                                    
                                    $lastRowIndex ++;
                                }
                                
                            } else {
                                
                            }
                        }
                        
                    } elseif ($rowData['PARENT_ID'] && $rowId == $nextRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX'], 'PARENT_ID' => $rowData['PARENT_ID']), 'UPDATE', 'ID = '.$nextRowId);
                        
                    } elseif ($rowData['PARENT_ID'] && $rowData['PARENT_ID'] == $nextRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                        
                    } elseif ($rowData['PARENT_ID'] && !$nextRowData['PARENT_ID']) {
                        
                        if ($isChildNext) {
                            
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX'], 'PARENT_ID' => $nextRowId), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                        
                        } else {
                            
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX'], 'PARENT_ID' => null), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                        }
                        
                    } elseif ($rowData['PARENT_ID'] && $nextRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $nextRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$nextRowId);
                    }
                }
                
            } elseif ($direction == 'up') {
                
                $prevRowId = Input::numeric('prevRowId');
                
                $rowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowId);
                $rowData = issetParam($rowData['rowData']);
                
                $prevRowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $prevRowId);
                $prevRowData = issetParam($prevRowData['rowData']);
                
                if ($rowData && $prevRowData) {
                    
                    $isChildPrev = $prevRowData['IS_CHILD'];
                    $isChildRow = $rowData['IS_CHILD'];
                        
                    if ((!$rowData['PARENT_ID'] && !$prevRowData['PARENT_ID']) || ($rowData['PARENT_ID'] && !$prevRowData['PARENT_ID'])) {
                        
                        if (!$isChildRow) {
                            
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowData['ROW_INDEX'], 'PARENT_ID' => null), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$prevRowId);
                            
                        } else {
                            
                            $prevRowIndex = $this->db->GetOne("SELECT ROW_INDEX FROM $templateTableName WHERE ID = $prevRowId");
                            $prevRowIndex = (int) $prevRowIndex;
                            
                            $ids = '';
                            $childRows = $this->db->GetAll("
                                SELECT 
                                    ID, 
                                    ROW_INDEX, 
                                    PARENT_ID 
                                FROM $templateTableName 
                                    CONNECT BY NOCYCLE 
                                    PRIOR ID = PARENT_ID  
                                    START WITH ID = $rowId   
                                ORDER BY ROW_INDEX ASC");
                            
                            foreach ($childRows as $childRow) {
                                
                                $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowIndex), 'UPDATE', 'ID = '.$childRow['ID']);
                                
                                $prevRowIndex ++;
                                
                                $ids .= $childRow['ID'].',';
                            }
                            
                            $rows = $this->db->GetAll("
                                SELECT 
                                    ID 
                                FROM $templateTableName 
                                WHERE ROW_INDEX >= (SELECT ROW_INDEX FROM $templateTableName WHERE ID = $rowId) 
                                    AND ID NOT IN (".rtrim($ids, ',').") 
                                ORDER BY ROW_INDEX ASC"
                            );
                            
                            foreach ($rows as $childRow) {
                                    
                                $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowIndex), 'UPDATE', 'ID = '.$childRow['ID']);

                                $prevRowIndex ++;
                            }
                        }
                        
                    } elseif (!$rowData['PARENT_ID'] && $rowId == $prevRowData['PARENT_ID']) {
                        
                        var_dump($rowData);die;
                        
                    } elseif ($rowData['PARENT_ID'] && $rowId == $prevRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX'], 'PARENT_ID' => $rowData['PARENT_ID']), 'UPDATE', 'ID = '.$prevRowId);
                        
                    } elseif (!$rowData['PARENT_ID'] && $prevRowData['PARENT_ID']) {
                        
                        if ($isChildRow) {
                            
                            $prevRows = $this->db->GetAll("
                                SELECT 
                                    ID, 
                                    ROW_INDEX, 
                                    PARENT_ID 
                                FROM $templateTableName 
                                    CONNECT BY NOCYCLE 
                                    PRIOR PARENT_ID = ID  
                                    START WITH ID = $prevRowId 
                                ORDER BY ROW_INDEX ASC");

                            $prevRowIndex = (int) $prevRows[0]['ROW_INDEX'];

                            $ids = '';
                            $childRows = $this->db->GetAll("
                                SELECT 
                                    ID, 
                                    ROW_INDEX, 
                                    PARENT_ID 
                                FROM $templateTableName 
                                    CONNECT BY NOCYCLE 
                                    PRIOR ID = PARENT_ID  
                                    START WITH ID = $rowId   
                                ORDER BY ROW_INDEX ASC");

                            foreach ($childRows as $childRow) {

                                $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowIndex), 'UPDATE', 'ID = '.$childRow['ID']);

                                $prevRowIndex ++;

                                $ids .= $childRow['ID'].',';
                            }

                            $rows = $this->db->GetAll("
                                SELECT 
                                    ID 
                                FROM $templateTableName 
                                WHERE ROW_INDEX >= (SELECT ROW_INDEX FROM $templateTableName WHERE ID = ".$prevRows[0]['ID'].") 
                                    AND ID NOT IN (".rtrim($ids, ',').") 
                                ORDER BY ROW_INDEX ASC"
                            );

                            foreach ($rows as $childRow) {

                                $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowIndex), 'UPDATE', 'ID = '.$childRow['ID']);

                                $prevRowIndex ++;
                            }

                        } else {
                            
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowData['ROW_INDEX'], 'PARENT_ID' => $prevRowData['PARENT_ID']), 'UPDATE', 'ID = '.$rowId);
                            $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$prevRowId);
                        }
                        
                    } elseif ($rowData['PARENT_ID'] == $prevRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $prevRowData['ROW_INDEX']), 'UPDATE', 'ID = '.$rowId);
                        $this->db->AutoExecute($templateTableName, array('ROW_INDEX' => $rowData['ROW_INDEX']), 'UPDATE', 'ID = '.$prevRowId);
                        
                    }
                }
                
            } elseif ($direction == 'right') {
                
                $prevRowId = Input::numeric('prevRowId');
                
                $rowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowId);
                $rowData = issetParam($rowData['rowData']);
                
                $prevRowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $prevRowId);
                $prevRowData = issetParam($prevRowData['rowData']);
                
                if ($rowData && $prevRowData) {
                    
                    if (!$prevRowData['PARENT_ID'] || $rowData['PARENT_ID'] == $prevRowData['PARENT_ID'] || $prevRowId == $rowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('PARENT_ID' => $prevRowId), 'UPDATE', 'ID = '.$rowId);
                        
                    } elseif (!$rowData['PARENT_ID'] && $prevRowData['PARENT_ID']) {
                        
                        $prevRows = $this->db->GetAll("
                            SELECT 
                                ID, 
                                ROW_INDEX, 
                                PARENT_ID 
                            FROM $templateTableName 
                                CONNECT BY NOCYCLE 
                                PRIOR PARENT_ID = ID  
                                START WITH ID = $prevRowId 
                            ORDER BY ROW_INDEX ASC");
                        
                        $this->db->AutoExecute($templateTableName, array('PARENT_ID' => $prevRows[0]['ID']), 'UPDATE', 'ID = '.$rowId);
                        
                    } elseif ($rowData['PARENT_ID'] && $prevRowData['PARENT_ID']) {
                        
                        $prevRows = $this->db->GetAll("
                            SELECT 
                                ID, 
                                ROW_INDEX, 
                                PARENT_ID 
                            FROM $templateTableName 
                                CONNECT BY NOCYCLE 
                                PRIOR PARENT_ID = ID  
                                START WITH ID = $prevRowId 
                            ORDER BY ROW_INDEX ASC");
                        
                        foreach ($prevRows as $prevRow) {
                            if ($prevRow['PARENT_ID'] && $prevRow['ID'] != $rowData['PARENT_ID']) {
                                $this->db->AutoExecute($templateTableName, array('PARENT_ID' => $prevRow['ID']), 'UPDATE', 'ID = '.$rowId);
                                break;
                            }
                        }
                    }
                }
                
            } elseif ($direction == 'left') {
                
                $prevRowId = Input::numeric('prevRowId');
                
                $rowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowId);
                $rowData = issetParam($rowData['rowData']);
                
                $prevRowData = self::getKpiIndicatorTemplateRowModel($templateTableName, $prevRowId);
                $prevRowData = issetParam($prevRowData['rowData']);
                
                if ($rowData && $prevRowData) {
                    
                    if (!$prevRowData['PARENT_ID']) {
                        
                        $this->db->AutoExecute($templateTableName, array('PARENT_ID' => null), 'UPDATE', 'ID = '.$rowId);
                        
                    } else {
                        
                        $rowParentId = $rowData['PARENT_ID'];
                        
                        $rowParent = self::getKpiIndicatorTemplateRowModel($templateTableName, $rowParentId);
                        $rowParent = issetParam($rowParent['rowData']);
                        
                        if ($rowParent) {
                            
                            if (!$rowParent['PARENT_ID']) {
                                $this->db->AutoExecute($templateTableName, array('PARENT_ID' => null), 'UPDATE', 'ID = '.$rowId);
                            } else {
                                $this->db->AutoExecute($templateTableName, array('PARENT_ID' => $rowParent['PARENT_ID']), 'UPDATE', 'ID = '.$rowId);
                            }
                        }
                    }
                }
            }
            
            $response = array('status' => 'success');
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiDataToDistributeConfigModel($indicatorId, $isMulti = false) {
        
        if ($isMulti) {
            
            $where = "KIIM.MAIN_INDICATOR_ID IN (
                SELECT 
                    TRG_INDICATOR_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP 
                WHERE SEMANTIC_TYPE_ID = 10000004 
                    AND SRC_INDICATOR_ID = ".$this->db->Param(0)." 
            )";
            
        } else {
            $where = "KIIM.MAIN_INDICATOR_ID = ".$this->db->Param(0);
        }
        
        $data = $this->db->GetAll("
            SELECT 
                KIIM.LABEL_NAME, 
                KIIM_SRC_MAIN.NAME||'.'||KIIM_SRC.NAME_PATH AS SRC_NAME, 
                KIIM.ORDER_NUMBER, 
                KIIM_MAIN.TABLE_NAME, 
                UPPER(KIIM.COLUMN_NAME) AS COLUMN_NAME, 
                KIIM_SRC_MAIN.TABLE_NAME AS SRC_TABLE_NAME, 
                KIIM_SRC.COLUMN_NAME_PATH AS SRC_COLUMN_NAME_PATH, 
                KIIM_SRC.SHOW_TYPE, 
                KIIM_SRC.INPUT_NAME AS SRC_INPUT_NAME, 
                KIIM.MAIN_INDICATOR_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP KIIM_SRC ON KIIM.SRC_INDICATOR_ID = KIIM_SRC.MAIN_INDICATOR_ID 
                    AND KIIM.SRC_INDICATOR_MAP_ID = KIIM_SRC.ID 
                LEFT JOIN KPI_INDICATOR KIIM_SRC_MAIN ON KIIM_SRC.MAIN_INDICATOR_ID = KIIM_SRC_MAIN.ID 
                INNER JOIN KPI_INDICATOR KIIM_MAIN ON KIIM.MAIN_INDICATOR_ID = KIIM_MAIN.ID 
            WHERE $where 
            ORDER BY KIIM.ORDER_NUMBER ASC", array($indicatorId));
        
        if ($data) {
            $data = Arr::groupByArray($data, 'MAIN_INDICATOR_ID');
        }
        
        return $data;
    }
    
    public function getKpiDataMartRelationColumnsModel($mainIndicatorId) {
        
        $idPh1 = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT 
                KIIM.ID, 
                KI.TABLE_NAME AS SRC_TABLE_NAME, 
                KIIM.LABEL_NAME, 
                UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                KIIM.AGGREGATE_FUNCTION, 
                KIIM.EXPRESSION_STRING, 
                KIIM.EVENT_STRING, 
                KIIM.EVENT_EXPRESSION_STRING, 
                KIIM.BLOCK_DIAGRAM, 
                KIIM.TRG_ALIAS_NAME, 
                MST.NAME AS SEMANTIC_TYPE_NAME, 
                KIIM.SRC_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_MAP_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                INNER JOIN KPI_INDICATOR KI ON KIIM.MAIN_INDICATOR_ID = KI.ID
                INNER JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
            WHERE KIIM.MAIN_INDICATOR_ID = $idPh1 
            ORDER BY KIIM.ORDER_NUMBER ASC", array($mainIndicatorId));
        
        return $data;
    }
    
    public function getKpiDataMartRelationCriteriasModel($mainIndicatorId) {
        
        $idPh1 = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT 
                ID, 
                INDICATOR_ID, 
                ALIAS_NAME, 
                CRITERIA  
            FROM KPI_DATAMODEL_CRITERIA  
            WHERE MAIN_INDICATOR_ID = $idPh1 
            ORDER BY ALIAS_NAME ASC", array($mainIndicatorId));
        
        return $data;
    }
    
    public function getKpiDataMartRelationConfigModel($mainIndicatorId) {
        
        $idPh1 = $this->db->Param(0);
        
        $columnData = $this->db->GetAll("  
            SELECT 
                KIIM.ID,
                KI_SRC.TABLE_NAME AS SRC_TABLE_NAME, 
                UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                KIIM.AGGREGATE_FUNCTION, 
                KIIM.EXPRESSION_STRING, 
                KI_TRG.TABLE_NAME AS TRG_TABLE_NAME, 
                KIIM.TRG_ALIAS_NAME, 
                UPPER(KIIM_TRG.COLUMN_NAME) AS TRG_COLUMN_NAME, 
                KIIM.SHOW_TYPE AS SRC_SHOW_TYPE, 
                KIIM_TRG.SHOW_TYPE AS TRG_SHOW_TYPE, 
                MST.NAME AS SEMANTIC_TYPE_NAME, 
                KIIM.SRC_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_MAP_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                LEFT JOIN KPI_INDICATOR KI_SRC ON KIIM.MAIN_INDICATOR_ID = KI_SRC.ID 
                LEFT JOIN KPI_INDICATOR KI_TRG ON KIIM.TRG_INDICATOR_ID = KI_TRG.ID 
                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP KIIM_TRG ON KIIM.TRG_INDICATOR_ID = KIIM_TRG.MAIN_INDICATOR_ID 
                    AND KIIM.TRG_INDICATOR_MAP_ID = KIIM_TRG.ID 
                INNER JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
            WHERE KIIM.MAIN_INDICATOR_ID = $idPh1 
                AND (KIIM_TRG.COLUMN_NAME IS NOT NULL OR KIIM.EXPRESSION_STRING IS NOT NULL) 
            ORDER BY 
                KIIM.ORDER_NUMBER ASC", array($mainIndicatorId));
        
        $relationData = $this->db->GetAll("
            SELECT 
                KI_SRC.TABLE_NAME AS SRC_TABLE_NAME, 
                KI_SRC.QUERY_STRING AS SRC_QUERY_STRING, 
                UPPER(KIIM_SRC.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                KI_TRG.TABLE_NAME AS TRG_TABLE_NAME, 
                KI_TRG.QUERY_STRING AS TRG_QUERY_STRING, 
                UPPER(KIIM_TRG.COLUMN_NAME) AS TRG_COLUMN_NAME, 
                D.DEFAULT_VALUE,
                K.SRC_ALIAS_NAME,
                K.TRG_ALIAS_NAME,
                D.OPERATOR_NAME, 
                KIIM_SRC.SHOW_TYPE AS SRC_SHOW_TYPE, 
                KIIM_TRG.SHOW_TYPE AS TRG_SHOW_TYPE, 
                K.JOIN_TYPE, 
                D.DATAMODEL_MAP_KEY_ID, 
                MK.SRC_ALIAS_NAME AS PARENT_ALIAS_NAME, 
                KI_TRG_PARENT.TABLE_NAME AS PARENT_TABLE_NAME, 
                KI_SRC.NAME AS SRC_LABEL_NAME, 
                KI_TRG.NAME AS TRG_LABEL_NAME, 
                K.SRC_INDICATOR_ID, 
                K.TRG_INDICATOR_ID, 
                K.LAST_AFFECTED_DATE 
            FROM KPI_DATAMODEL_MAP_KEY K 
                INNER JOIN KPI_INDICATOR KI_SRC ON K.SRC_INDICATOR_ID = KI_SRC.ID 
                LEFT JOIN KPI_INDICATOR KI_TRG ON K.TRG_INDICATOR_ID = KI_TRG.ID 
                LEFT JOIN KPI_INDICATOR KI_TRG_PARENT ON KI_TRG.PARENT_ID = KI_TRG_PARENT.ID 
                    AND K.SRC_INDICATOR_ID = KI_TRG_PARENT.ID 
                LEFT JOIN KPI_DATAMODEL_MAP_KEY MK ON KI_TRG_PARENT.ID = MK.SRC_INDICATOR_ID 
                    AND KI_TRG.ID = MK.TRG_INDICATOR_ID 
                LEFT JOIN KPI_DATAMODEL_MAP_KEY_DTL D ON K.ID = D.DATAMODEL_MAP_KEY_ID 
                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP KIIM_SRC ON D.SRC_INDICATOR_MAP_ID = KIIM_SRC.ID 
                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP KIIM_TRG ON D.TRG_INDICATOR_MAP_ID = KIIM_TRG.ID 
            WHERE K.MAIN_INDICATOR_ID = $idPh1
            ORDER BY 
                K.JOIN_ORDER_NUMBER ASC, 
                K.ID ASC, 
                D.ID ASC", array($mainIndicatorId));
        
        $criteriaData = $this->db->GetAll("
            SELECT 
                INDICATOR_ID, 
                ALIAS_NAME, 
                CRITERIA 
            FROM KPI_DATAMODEL_CRITERIA 
            WHERE MAIN_INDICATOR_ID = $idPh1", array($mainIndicatorId));
        
        $criteriaArr = array();
        
        if ($criteriaData) {
            foreach ($criteriaData as $criteriaRow) {
                $criteriaArr[$criteriaRow['ALIAS_NAME'] . '_' . $criteriaRow['INDICATOR_ID']] = html_entity_decode($criteriaRow['CRITERIA'], ENT_QUOTES, 'UTF-8');
            }
        }
        
        return array('column' => $columnData, 'relation' => $relationData, 'criteria' => $criteriaArr);
    }
    
    public function generateKpiDataMartModel($indicatorId = null, $mainIndicatorId = null) {
        
        try {
            
            if ($mainIndicatorId) {
                $configDatas = self::getKpiDataToDistributeConfigModel($mainIndicatorId, true);    
            } else { 
                $configDatas = self::getKpiDataToDistributeConfigModel($indicatorId);
            }
            
            if ($indicatorId && $mainIndicatorId && $indicatorId == $mainIndicatorId) {
                
                Mdform::clearCacheData($indicatorId);
                $webServiceRow = self::getIndicatorAdditionalInfoModel(1080, $indicatorId);
                
                if ($classIndicatorId = issetParam($webServiceRow['CLASS_INDICATOR_ID'])) {
                    Mdform::clearCacheData($classIndicatorId);
                }
            }
            
            if ($configDatas) {
                
                $firstKey     = array_key_first($configDatas);
                $firstRow     = $configDatas[$firstKey]['row'];
                $srcTableName = $firstRow['SRC_TABLE_NAME'];
                
                $srcRecordId  = Input::post('sourceRecordId');
                $size         = 200; 
                $where        = 'AND 1 = 1'; 
                $whereDelete  = '';
                
                if (isset($srcRecordId) && $srcRecordId) {
                    
                    if (strpos($srcRecordId, ',') !== false) {
                        $where = "AND ID IN ($srcRecordId)";
                    } else {
                        $where = "AND (SRC_RECORD_ID = $srcRecordId OR ID = $srcRecordId)";
                    }
                    
                } elseif (Input::numeric('isGeneratedDate') == 1) {
                    
                    $isGeneratedDate = true;
                    $where = 'AND GENERATED_DATE IS NULL';
                    $size = 100;
                } 
                
                $dbField = $checkDbField = $data = $checkTable = $rowsRecordIds = array();
                $n     = 0;
                
                $rows = $this->db->GetAll("SELECT * FROM $srcTableName WHERE 1 = 1 $where");

                foreach ($rows as $row) {
                    
                    if (isset($row['DELETED_USER_ID']) && $row['DELETED_USER_ID']) {
                        
                        $deleteRowId = $row['ID'];
                        
                        foreach ($configDatas as $mId => $configRows) {
                            
                            $schemaName   = self::getKpiDbSchemaName($mId);
                            $tableName    = $schemaName . 'D_'.$mId;
                            $isTblCreated = self::table_exists($this->db, $tableName);
                            
                            if ($isTblCreated) {
                                
                                $this->db->Execute("DELETE FROM $tableName WHERE SRC_RECORD_ID = $deleteRowId");
                                Mdform::clearCacheData($mId);
                            }
                        }
                        
                        continue;
                    }
                    
                    foreach ($configDatas as $mId => $configRows) {

                        $headerData = $detailData = array();
                        $configData = $configRows['rows'];

                        if (!isset($checkTable[$mId])) {

                            $schemaName   = self::getKpiDbSchemaName($mId);
                            $tableName    = $schemaName . 'D_'.$mId;
                            $isTblCreated = self::table_exists($this->db, $tableName);

                            $checkTable[$mId] = array(
                                'tableName' => $tableName, 
                                'isTblCreated' => $isTblCreated
                            );
                        }

                        $tableName    = $checkTable[$mId]['tableName'];
                        $isTblCreated = $checkTable[$mId]['isTblCreated'];

                        foreach ($configData as $configRow) {

                            $columnName        = $configRow['COLUMN_NAME'];
                            $srcColumnNamePath = $configRow['SRC_COLUMN_NAME_PATH'];
                            $showType          = $configRow['SHOW_TYPE'];

                            if (strpos($srcColumnNamePath, '.') !== false && isset($row['DATA'])) {

                                $dataJson          = json_decode($row['DATA'], true);
                                $srcColumnNamePath = explode('.', $srcColumnNamePath);
                                $rowsPath          = $srcColumnNamePath[0];
                                $childPath         = $srcColumnNamePath[1];

                                if (isset($dataJson[$rowsPath]) 
                                    && is_array($dataJson[$rowsPath]) 
                                    && isset($dataJson[$rowsPath][0]) 
                                    && is_array($dataJson[$rowsPath][0]) 
                                    && array_key_exists($childPath, $dataJson[$rowsPath][0])) {

                                    $dataJsonRows = $dataJson[$rowsPath];
                                    $index = 0;

                                    foreach ($dataJsonRows as $dataJsonRow) {

                                        $detailData[$index][$columnName] = issetDefaultVal($dataJsonRow[$childPath], null);

                                        if (isset($dataJsonRow[$childPath . '_DESC'])) {

                                            $detailData[$index][$columnName.'_DESC'] = issetDefaultVal($dataJsonRow[$childPath . '_DESC'], $dataJsonRow[$childPath]);

                                            if ($detailData[$index][$columnName] == '' && $detailData[$index][$columnName.'_DESC'] != '') {
                                                //$detailData[$index][$columnName] = $detailData[$index][$columnName.'_DESC'];
                                            }

                                            if ($isTblCreated && !isset($checkDbField[$mId][$columnName.'_DESC']) && !isset($isTblCreated[$columnName.'_DESC'])) {

                                                $dbField[$mId][] = array('type' => 'text', 'name' => $columnName . '_DESC');

                                                $checkDbField[$mId][$columnName.'_DESC'] = 1;
                                            }
                                        } 

                                        $index ++;
                                    }
                                }

                            } elseif (isset($row[$srcColumnNamePath])) {

                                if ($showType == 'combo' || $showType == 'popup' || $showType == 'radio') {

                                    $headerData[$columnName] = issetDefaultVal($row[$srcColumnNamePath], null);
                                    $headerData[$columnName . '_DESC'] = issetDefaultVal($row[$srcColumnNamePath . '_DESC'], $row[$srcColumnNamePath]);

                                } else {
                                    $headerData[$columnName] = $row[$srcColumnNamePath];
                                }

                                if ($configRow['SRC_INPUT_NAME'] == 'META_VALUE_CODE') {

                                    $whereDelete .= ' AND '. $columnName . " = '".$headerData[$columnName]."'";
                                }
                            }
                        }

                        if (isset($row['SRC_RECORD_ID']) && $row['SRC_RECORD_ID']) {

                            $headerData['SRC_RECORD_ID'] = $row['SRC_RECORD_ID'];

                        } elseif (isset($row['ID']) && $row['ID']) {

                            $headerData['SRC_RECORD_ID'] = $row['ID'];
                        }

                        $rowsRecordIds[$mId][] = $headerData['SRC_RECORD_ID'];

                        if ($detailData) {

                            foreach ($detailData as $detailRow) {

                                $headerData['ID'] = getUIDAdd($n);
                                $detailRow = $headerData + $detailRow;

                                $data[$mId][] = $detailRow;

                                $n ++;
                            }

                        } /*else {

                            $headerData['ID'] = getUIDAdd($n);
                            $data[$mId][] = $headerData;

                            $n ++;
                        }*/
                    }
                }
                
                if ($checkTable) {
                    
                    $successIds = array();
                    
                    foreach ($configDatas as $mId => $configRows) {

                        $configData   = $configRows['rows'];
                        
                        $tableName    = $checkTable[$mId]['tableName'];
                        $isTblCreated = $checkTable[$mId]['isTblCreated'];

                        foreach ($configData as $configRow) {

                            $columnName = $configRow['COLUMN_NAME'];

                            if ($columnName == 'ID') {
                                continue;
                            }

                            if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$columnName]))) {

                                if ($configRow['SHOW_TYPE'] == 'combo' || $configRow['SHOW_TYPE'] == 'popup' || $configRow['SHOW_TYPE'] == 'radio') {

                                    $dbField[$mId][] = array('type' => 'number', 'name' => $columnName);

                                    if (!isset($checkDbField[$mId][$columnName.'_DESC'])) {
                                        $dbField[$mId][] = array('type' => 'text', 'name' => $columnName . '_DESC');
                                    }

                                } else {
                                    $dbField[$mId][] = array('type' => $configRow['SHOW_TYPE'], 'name' => $columnName);
                                }

                            } elseif ($isTblCreated && ($configRow['SHOW_TYPE'] == 'combo' || $configRow['SHOW_TYPE'] == 'popup' || $configRow['SHOW_TYPE'] == 'radio') && !isset($isTblCreated[$columnName.'_DESC']) && !isset($checkDbField[$mId][$columnName.'_DESC'])) {

                                $dbField[$mId][] = array('type' => 'text', 'name' => $columnName . '_DESC');
                            }
                        }

                        if ($isTblCreated == false) {

                            $createTblStatus = self::dbCreatedTblKpiDataMart($tableName, $dbField[$mId]);

                            if ($createTblStatus['status'] == 'error') {
                                return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                            } else {
                                self::updateKpiIndicatorTblName($mId, $tableName); 
                            }

                        } else {

                            $standardFields = array(
                                array(
                                    'type' => 'number', 
                                    'name' => 'ID'
                                ), 
                                array(
                                    'type' => 'number', 
                                    'name' => 'SRC_RECORD_ID'
                                ), 
                                array(
                                    'type' => 'number', 
                                    'name' => 'WFM_STATUS_ID'
                                ),
                                array(
                                    'type' => 'varchar', 
                                    'name' => 'WFM_DESCRIPTION'
                                ),
                                array(
                                    'type' => 'date', 
                                    'name' => 'CREATED_DATE'
                                ),
                                array(
                                    'type' => 'number', 
                                    'name' => 'CREATED_USER_ID'
                                ),
                                array(
                                    'type' => 'varchar', 
                                    'name' => 'CREATED_USER_NAME'
                                ),
                                array(
                                    'type' => 'date', 
                                    'name' => 'MODIFIED_DATE'
                                ),
                                array(
                                    'type' => 'number', 
                                    'name' => 'MODIFIED_USER_ID'
                                ),
                                array(
                                    'type' => 'varchar', 
                                    'name' => 'MODIFIED_USER_NAME'
                                ),
                                array(
                                    'type' => 'date', 
                                    'name' => 'DELETED_DATE'
                                ),
                                array(
                                    'type' => 'number', 
                                    'name' => 'DELETED_USER_ID'
                                ),
                                array(
                                    'type' => 'varchar', 
                                    'name' => 'DELETED_USER_NAME'
                                ), 
                                array(
                                    'type' => 'number', 
                                    'name' => 'COMPANY_DEPARTMENT_ID'
                                ), 
                                array(
                                    'type' => 'date', 
                                    'name' => 'GENERATED_DATE'
                                )
                            );

                            foreach ($standardFields as $standardField) {

                                if (!isset($isTblCreated[$standardField['name']])) {

                                    $dbField[$mId][] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                                }
                            }

                            if (isset($dbField[$mId])) {

                                $alterTblStatus = self::dbAlterTblKpiDynamic($tableName, $dbField[$mId]);

                                if ($alterTblStatus['status'] == 'error') {
                                    return array('status' => 'error', 'message' => 'Alter table: ' . $alterTblStatus['message']);
                                }
                            }
                        }
                        
                        /*if ($whereDelete) {
                            $where = $whereDelete;
                        }*/
                        
                        if (isset($rowsRecordIds[$mId])) {
                            
                            $dataRecordIds = $rowsRecordIds[$mId];
                            
                            $idsSplit      = array_chunk($dataRecordIds, 500); 
                            $where         = ' AND (';
                            
                            foreach ($idsSplit as $idsArr) {
                                $where .= ' SRC_RECORD_ID IN (' . implode(',', $idsArr) . ') OR';
                            }

                            $where = rtrim($where, ' OR');
                            $where .= ')';
                                    
                            $this->db->BeginTrans(); 
                            $this->db->Execute("DELETE FROM $tableName WHERE 1 = 1 $where");
                            
                            if (isset($data[$mId])) {
                                $datas = $data[$mId];

                                foreach ($datas as $dataRow) {

                                    try {

                                        $insertColName = '';
                                        $insertColBind = '';

                                        $b = 0;

                                        foreach ($dataRow as $dataField => $dataVal) {

                                            $insertColName .= $dataField . ',';
                                            $insertColBind .= "'" . str_replace("'", "''", $dataVal) . "',";
                                        }

                                        $insertColName = rtrim($insertColName, ',');
                                        $insertColBind = rtrim($insertColBind, ',');

                                        $this->db->Execute("INSERT INTO $tableName ($insertColName) VALUES ($insertColBind)");

                                        $successIds[$dataRow['SRC_RECORD_ID']] = $dataRow['SRC_RECORD_ID'];

                                    } catch (Exception $ex) {

                                        $message = $ex->getMessage();

                                        //$this->db->RollbackTrans();
                                        //return array('status' => 'error', 'message' => $message);
                                    }
                                }
                            }

                            $this->db->CommitTrans();

                            self::runGenerateKpiRelationDataMartByIndicatorId($mId);
                            Mdform::clearCacheData($mId);
                        }
                    }
                    
                    if ($successIds) {
                        
                        $idsSplit = array_chunk($successIds, 500); 
                        $where    = '';

                        foreach ($idsSplit as $idsArr) {
                            $where .= ' ID IN (' . implode(',', $idsArr) . ') OR';
                        }

                        $where = rtrim($where, ' OR');
                            
                        $this->db->AutoExecute($srcTableName, 
                            array('GENERATED_DATE' => Date::currentDate('Y-m-d H:i:s')), 
                            'UPDATE', 
                            $where
                        );
                    }
                    
                    $phpUrl = Config::getFromCache('PHP_URL');
                    $phpUrl = rtrim($phpUrl, '/');
                    $phpUrl = $phpUrl.'/';

                    $this->ws->curlQueue($phpUrl . 'cron/generateKpiDataHeaderToDetail/'.$indicatorId);
                    
                    self::runPivotDataMartModel($indicatorId);
                }
                
                return array('status' => 'success');
                
            } else {
                $response = array('status' => 'error', 'message' => 'No config!');
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function generateKpiRelationDataMartModel($mainIndicatorId) {
        
        try {
            
            $configData = self::getKpiDataMartRelationConfigModel($mainIndicatorId);
            
            $columnConfig = $configData['column'];
            $relationConfig = $configData['relation'];
            $relationCriteria = $configData['criteria'];
            
            if ($columnConfig && $relationConfig) {
                
                $firstRow     = $columnConfig[0];
                $srcTableName = $firstRow['SRC_TABLE_NAME'];
                $lastDate     = $relationConfig[0]['LAST_AFFECTED_DATE'];
                $currentDate  = Date::currentDate('Y-m-d H:i:s');
                
                $schemaName   = self::getKpiDbSchemaName($mainIndicatorId);
                $tableName    = $srcTableName ? $srcTableName : $schemaName . 'D_'.$mainIndicatorId;
                $isTblCreated = self::table_exists($this->db, $tableName);
                
                $isMainQueryString = false;
                $dbField = $rowCols = $updateCols = array();
                
                $expressionSelectCol = $subSelectCol = $where = $join = $groupBy = 
                $insertIntoCol = $mainIndicator = $mainTableName = $mainAliasName = 
                $mainLabelName = $martSelectColumn = $valueUpdateWhere = $valueUpdateJoin = 
                $dateWhere = $martSelectAliasColumn = $martGroupByColumn = '';
                
                foreach ($columnConfig as $col) {
                    
                    $aggregateFunction = $col['AGGREGATE_FUNCTION'];
                    $expressionString  = $col['EXPRESSION_STRING'];
                    $srcColumnName     = $col['SRC_COLUMN_NAME'];
                    $trgAliasName      = $col['TRG_ALIAS_NAME'];
                    $trgColumnName     = $col['TRG_COLUMN_NAME'];
                    $srcShowType       = $col['SRC_SHOW_TYPE'];
                    $trgShowType       = $col['TRG_SHOW_TYPE'];
                    $semanticTypeName  = $col['SEMANTIC_TYPE_NAME'];
                    
                    if (!$expressionString && $trgAliasName && $trgColumnName) {

                        if ($aggregateFunction) {
                            
                            $selectColumn = "$trgAliasName.$trgColumnName";
                            
                            if ($trgShowType == 'decimal') {
                                
                                $selectColumn = "TO_NUMBER(REPLACE($selectColumn, ','))";
                                
                            } elseif ($trgShowType == 'date') {
                                
                                $selectColumn = "TO_DATE($selectColumn, 'YYYY-MM-DD')";
                            }
                            
                            $subSelectCol .= "$aggregateFunction($selectColumn) AS $srcColumnName,";
                            
                        } else {
                            
                            $selectColumn = "$trgAliasName.$trgColumnName";
                            
                            if ($trgShowType == 'decimal') {
                                
                                $selectColumn = "TO_NUMBER(REPLACE($selectColumn, ','))";
                                
                            } elseif ($trgShowType == 'combo' || $trgShowType == 'popup' || $trgShowType == 'radio') {
                                
                                $selectColumn = $selectColumn.'_DESC';
                            }
                            
                            $subSelectCol .= "$selectColumn AS $srcColumnName,";
                            $groupBy .= "$selectColumn,";
                        }
                        
                        $martSelectColumn .= "SRC.$srcColumnName,";
                        $martSelectAliasColumn .= "SRC.$srcColumnName,";
                        
                        $martGroupByColumn .= "SRC.$srcColumnName,";
                        
                    } elseif ($expressionString) {
                        
                        if ($trgAliasName 
                                && $trgColumnName 
                                && $aggregateFunction 
                                && strtolower($trgColumnName) == strtolower(str_replace(array('[', ']'), '', $expressionString))) {
                             
                            $selectColumn = "$trgAliasName.$trgColumnName";
                            
                            $expressionSelectCol .= "$aggregateFunction(SRC.$srcColumnName) AS $srcColumnName,";
                            $martSelectColumn .= "$aggregateFunction(SRC.$srcColumnName) AS $srcColumnName,";
                            
                            $subSelectCol .= "$selectColumn AS $srcColumnName,";
                            $groupBy .= "$selectColumn,";
                            
                            $isMartGroupByColumn = true;
                            
                        } else {
                            
                            $expressionString = html_entity_decode($expressionString);
                            $expressionString = str_replace('[', 'SRC.', $expressionString);
                            $expressionString = str_replace(']', '', $expressionString);

                            $expressionSelectCol .= "($expressionString) AS $srcColumnName,";

                            $martSelectColumn .= "($expressionString) AS $srcColumnName,";
                        }
                        
                        $martSelectAliasColumn .= "SRC.$srcColumnName,";
                    }
                    
                    if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$srcColumnName]))) {
                        
                        $dbField[] = array('type' => $srcShowType, 'length' => 4000, 'name' => $srcColumnName);
                    }
                    
                    if ($semanticTypeName == 'Мөр') {
                        
                        $rowCols[] = array('columnName' => $srcColumnName, 'showType' => $srcShowType);
                        
                    } else {
                        $updateCols[] = array('columnName' => $srcColumnName, 'showType' => $srcShowType);
                    }
                    
                    $insertIntoCol .= $srcColumnName.',';
                }
                
                foreach ($relationConfig as $rel) {
                    
                    $isMain = true;
                    
                    $srcTableName   = $rel['SRC_TABLE_NAME'];
                    $srcQueryString = self::parseQueryString($rel['SRC_QUERY_STRING']);
                    $srcAliasName   = $rel['SRC_ALIAS_NAME'];
                    
                    foreach ($relationConfig as $relChild) {
                        
                        if ($srcAliasName == $relChild['TRG_ALIAS_NAME'] 
                            && $srcTableName == $relChild['TRG_TABLE_NAME'] 
                            && $srcQueryString == self::parseQueryString($relChild['TRG_QUERY_STRING'])) {
                            
                            $isMain = false;
                        }
                    }
                    
                    if ($isMain) {
                        
                        $mainIndicator = $rel['SRC_INDICATOR_ID'];
                        $mainTableName = $srcTableName;
                        $mainAliasName = $srcAliasName;
                        $mainLabelName = $rel['SRC_LABEL_NAME'];
                        $mainCriteria  = issetParam($relationCriteria[$srcAliasName . '_' .$rel['SRC_INDICATOR_ID']]);
                        
                        if ($srcQueryString) {
                            $mainTableName = "($srcQueryString)";
                            $isMainQueryString = true;
                        }
                        
                        break;
                    }
                }
                
                $isMainTableSystemTable = $isMainQueryString ? true : self::isCheckSystemTable($mainTableName);
                $relationConfigs        = Arr::groupByArray($relationConfig, 'TRG_INDICATOR_ID');
                $defaultMinDate         = $this->db->ToDate("'1970-01-01 01:01:01'", 'YYYY-MM-DD HH24:MI:SS');
                
                foreach ($relationConfigs as $relationConfigRow) {
                    
                    $relConfigRow      = $relationConfigRow['row'];
                    $relConfigRows     = $relationConfigRow['rows'];
                    $relJoinType       = $relConfigRow['JOIN_TYPE'] ? $relConfigRow['JOIN_TYPE'] : 'INNER';
                    $relTrgTableName   = $relConfigRow['TRG_TABLE_NAME'];
                    $relTrgQueryString = self::parseQueryString($relConfigRow['TRG_QUERY_STRING']);
                    $relTrgAliasName   = $relConfigRow['TRG_ALIAS_NAME'];
                    
                    $isTrgQueryString  = false;
                            
                    if (!$relTrgTableName && !$relTrgQueryString) {
                        continue;
                    }
                    
                    if ($relTrgQueryString) {
                        $isTrgQueryString = true;
                        $relTrgTableName = "($relTrgQueryString)";
                    }

                    $rowJoin = $relJoinType.' JOIN '.$relTrgTableName.' '.$relTrgAliasName.' ON '; 
                    $isTrgTableSystemTable = $isTrgQueryString ? true : self::isCheckSystemTable($relTrgTableName);
                    
                    foreach ($relConfigRows as $relRow) {
                        
                        $operatorName  = $relRow['OPERATOR_NAME'] ? html_entity_decode($relRow['OPERATOR_NAME']) : '=';
                        $srcShowType   = $relRow['SRC_SHOW_TYPE'];
                        $trgShowType   = $relRow['TRG_SHOW_TYPE'];
                        $srcAliasName  = $relRow['SRC_ALIAS_NAME'];
                        $srcColumnName = $relRow['SRC_COLUMN_NAME'];
                        $trgAliasName  = $relRow['TRG_ALIAS_NAME'];
                        $trgColumnName = $relRow['TRG_COLUMN_NAME'];
                        $prntAliasName = $relRow['PARENT_ALIAS_NAME'];
                        $defaultValue  = $relRow['DEFAULT_VALUE'];
                        
                        $srcSelectCol  = $srcAliasName.'.'.$srcColumnName;
                        $trgSelectCol  = $trgAliasName.'.'.$trgColumnName;
                        
                        if (($srcShowType != 'combo' && $srcShowType != 'radio') && ($trgShowType == 'combo' || $trgShowType == 'radio')) {
                            
                            $trgSelectCol = $trgAliasName.'.'.$trgColumnName.'_DESC';
                            
                        } elseif (($trgShowType != 'combo' && $trgShowType != 'radio') && ($srcShowType == 'combo' || $srcShowType == 'radio')) {
                            
                            $srcSelectCol = $srcAliasName.'.'.$srcColumnName.'_DESC';
                        }
                        
                        if ($srcShowType == 'decimal') {
                        
                            $srcSelectCol = "TO_NUMBER(REPLACE($srcSelectCol, ','))";
                        } 
                        
                        if ($trgShowType == 'decimal') {
                            
                            $trgSelectCol = "TO_NUMBER(REPLACE($trgSelectCol, ','))";
                        } 
                        
                        if ($defaultValue) {
                            
                            if ($srcColumnName == '') {
                                
                                $srcSelectCol = $defaultValue;
                                
                            } elseif ($trgColumnName == '') {
                                
                                $trgSelectCol = $defaultValue;
                            }
                        }
                        
                        $rowJoin .= $srcSelectCol." $operatorName ".$trgSelectCol.' AND ';
                        
                        if ($prntAliasName) {
                            $rowJoin .= "$prntAliasName.ID = $trgAliasName.SRC_RECORD_ID AND "; 
                        }
                    }
                    
                    if ($joinCriteria = issetParam($relationCriteria[$relTrgAliasName . '_' .$relConfigRow['TRG_INDICATOR_ID']])) {
                        
                        $rowJoin .= self::joinCriteriaParse($relTrgAliasName, $joinCriteria).' AND '; 
                    }
                    
                    $valueUpdateJoin .= rtrim(trim($rowJoin), 'AND');
                    
                    $join .= $rowJoin;
                    
                    if ($isTrgTableSystemTable == false) {
                        $join .= $relTrgAliasName.'.DELETED_USER_ID IS NULL AND ';
                    }
                    
                    $join = rtrim(trim($join), 'AND');
                    
                    $join .= ' /*'.$relConfigRow['TRG_LABEL_NAME'].' - '.$relConfigRow['TRG_INDICATOR_ID'].'*/ ' . "\n";
                    
                    if ($isTrgQueryString) {
                        $dateWhere .= $relTrgAliasName.'.CREATED_DATE,';
                    } else {
                        $dateWhere .= "COALESCE($relTrgAliasName.MODIFIED_DATE, $relTrgAliasName.CREATED_DATE, $defaultMinDate),";
                    }
                }
                
                if ($isMainTableSystemTable == false) {
                    $where .= $mainAliasName.'.DELETED_USER_ID IS NULL AND ';
                }
                
                if (!$updateCols) {
                    $lastDate = null;
                }
                
                if ($lastDate) {
                    
                    if ($isMainQueryString) {
                        $dateWhere .= $mainAliasName.'.CREATED_DATE,';
                    } else {
                        $dateWhere .= "COALESCE($mainAliasName.MODIFIED_DATE, $mainAliasName.CREATED_DATE, $defaultMinDate),"; 
                    }
                    
                    $dateWhere = rtrim($dateWhere, ',');
                    
                    $where .= 'GREATEST('.$dateWhere.') > '.$this->db->SQLDate('Y-m-d H:i:s', "'".$lastDate."'", 'TO_DATE').' AND ';
                    
                    if ($isMainTableSystemTable == false) {
                        $valueUpdateWhere .= $this->db->SQLDate('Y-m-d H:i:s', $mainAliasName.'.DELETED_DATE', 'TO_DATE').' > '.$this->db->SQLDate('Y-m-d H:i:s', "'".$lastDate."'", 'TO_DATE').' AND ';
                    }
                }
                
                $where .= '1 = 1 AND ';
                
                if (isset($mainCriteria) && $mainCriteria) {
                    $where .= self::joinCriteriaParse($mainAliasName, $mainCriteria).' AND '; 
                }
                
                $subSelectCol .= $this->db->SQLDate('Y-m-d H:i:s', "'".$currentDate."'", 'TO_DATE').' AS DATAMART_DATE,'; 
                
                $subSelectCol = rtrim($subSelectCol, ',');
                $groupBy      = rtrim($groupBy, ',');
                $where        = rtrim(trim($where), 'AND');
                
                $mainSelectQry = "SELECT " . "\n";
                $mainSelectQry .= "$subSelectCol " . "\n";
                $mainSelectQry .= "FROM " . "\n";
                $mainSelectQry .= "$mainTableName $mainAliasName /*".$mainLabelName." - ".$mainIndicator."*/ " . "\n";
                $mainSelectQry .= $join;
                $mainSelectQry .= "WHERE " . "\n";
                $mainSelectQry .= "$where " . "\n";
                
                if ($groupBy) {
                    $mainSelectQry .= "GROUP BY " . "\n";
                    $mainSelectQry .= "$groupBy " . "\n";
                }
                
                $martSelectColumn .= $this->db->SQLDate('Y-m-d H:i:s', "'".$currentDate."'", 'TO_DATE').' AS DATAMART_DATE,'; 
                $martSelectColumn = rtrim($martSelectColumn, ',');
                
                if ($expressionSelectCol) {
                    $mainSelectQry = "SELECT $martSelectColumn FROM ($mainSelectQry) SRC"; 
                    
                    if (isset($isMartGroupByColumn)) {
                        $martGroupByColumn = rtrim($martGroupByColumn, ',');
                        $mainSelectQry .= "\n" . "GROUP BY $martGroupByColumn";
                    }
                }
                
                if (Input::numeric('isSqlView')) {
                    return array('status' => 'success', 'sql' => $mainSelectQry);
                }
                
                if ($isTblCreated == false) {
                
                    $createTblStatus = self::dbCreatedTblKpiRelationDataMart($tableName, $dbField);

                    if ($createTblStatus['status'] == 'error') {
                        return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                    } else {
                        self::updateKpiIndicatorTblName($mainIndicatorId, $tableName); 
                    }

                } else {

                    $standardFields = array(
                        array(
                            'type' => 'number', 
                            'name' => 'ID'
                        ), 
                        array(
                            'type' => 'number', 
                            'name' => 'INDICATOR_ID'
                        ), 
                        array(
                            'type' => 'number', 
                            'name' => 'WFM_STATUS_ID'
                        ),
                        array(
                            'type' => 'varchar', 
                            'name' => 'WFM_DESCRIPTION'
                        ), 
                        array(
                            'type' => 'date', 
                            'name' => 'CREATED_DATE'
                        ),
                        array(
                            'type' => 'number', 
                            'name' => 'CREATED_USER_ID'
                        ),
                        array(
                            'type' => 'varchar', 
                            'name' => 'CREATED_USER_NAME'
                        ), 
                        array(
                            'type' => 'date', 
                            'name' => 'MODIFIED_DATE'
                        ),
                        array(
                            'type' => 'number', 
                            'name' => 'MODIFIED_USER_ID'
                        ),
                        array(
                            'type' => 'varchar', 
                            'name' => 'MODIFIED_USER_NAME'
                        ), 
                        array(
                            'type' => 'date', 
                            'name' => 'DELETED_DATE'
                        ),
                        array(
                            'type' => 'number', 
                            'name' => 'DELETED_USER_ID'
                        ),
                        array(
                            'type' => 'varchar', 
                            'name' => 'DELETED_USER_NAME'
                        ), 
                        array(
                            'type' => 'number', 
                            'name' => 'COMPANY_DEPARTMENT_ID'
                        ), 
                        array(
                            'type' => 'date', 
                            'name' => 'GENERATED_DATE'
                        )
                    );

                    foreach ($standardFields as $standardField) {

                        if (!isset($isTblCreated[$standardField['name']])) {

                            $dbField[] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                        }
                    }

                    if ($dbField) {

                        $alterTblStatus = self::dbAlterTblKpiDynamic($tableName, $dbField);

                        if ($alterTblStatus['status'] == 'error') {
                            return array('status' => 'error', 'message' => 'Alter table: ' . $alterTblStatus['message']);
                        }
                    }
                }
                
                $this->db->BeginTrans(); 
                
                try {
                    
                    if ($rowCols && $updateCols) {
                        
                        $rowEqualCol = $updateEqualCol = '';
                        
                        foreach ($rowCols as $rowCol) {
                            
                            $rowColumnName = $rowCol['columnName'];
                            $rowShowType = $rowCol['showType'];
                            
                            if ($rowShowType == 'date') {
                                $rowEqualCol .= $this->db->IfNull("TRG.$rowColumnName", $this->db->SQLDate('Y-m-d H:i:s', "'1901-01-01 00:00:00'", 'TO_DATE')). " = ".$this->db->IfNull("SRC.$rowColumnName", $this->db->SQLDate('Y-m-d H:i:s', "'1901-01-01 00:00:00'", 'TO_DATE'))." AND ";
                            } else {
                                $rowEqualCol .= $this->db->IfNull("TRG.$rowColumnName", "'-1'"). " = ".$this->db->IfNull("SRC.$rowColumnName", "'-1'")." AND ";
                            }
                        }
                        
                        foreach ($updateCols as $updateCol) {
                            
                            $updateColumnName = $updateCol['columnName'];
                            
                            $updateEqualCol .= "TRG.$updateColumnName = SRC.$updateColumnName,";
                        }
                        
                        $updateEqualCol .= "TRG.MODIFIED_DATE = SRC.DATAMART_DATE,";
                        
                        $rowEqualCol = rtrim($rowEqualCol, 'AND ');
                        $updateEqualCol = rtrim($updateEqualCol, ',');
                        
                        $this->db->Execute("
                            MERGE INTO $tableName TRG 
                            USING ( 
                                $mainSelectQry 
                            ) SRC ON ($rowEqualCol) 
                            WHEN MATCHED THEN UPDATE SET
                                $updateEqualCol");
                        
                        if (isset($isMartGroupByColumn)) {
                            $martSelectAliasColumn .= $this->db->SQLDate('Y-m-d H:i:s', "'".$currentDate."'", 'TO_DATE').' AS DATAMART_DATE,'; 
                            $martSelectAliasColumn = rtrim($martSelectAliasColumn, ',');
                            $martSelectAliasColumn = $martSelectAliasColumn;
                        } else {
                            $martSelectAliasColumn = $martSelectColumn;
                        }
                        
                        $sql = "
                            SELECT 
                                ID_SEQ.nextval AS ID, 
                                $mainIndicatorId AS INDICATOR_ID, 
                                $martSelectAliasColumn 
                            FROM ($mainSelectQry) SRC 
                                LEFT JOIN $tableName TRG ON $rowEqualCol 
                            WHERE TRG.ID IS NULL";
                        
                        if ($lastDate && $valueUpdateWhere) {
                            
                            $valueUpdateWhere = rtrim(trim($valueUpdateWhere), 'AND');
                            $updateEqualCol   = '';
                            
                            $valueUpdateSelectQry = "SELECT " . "\n";
                            $valueUpdateSelectQry .= "$subSelectCol " . "\n";
                            $valueUpdateSelectQry .= "FROM " . "\n";
                            $valueUpdateSelectQry .= "$mainTableName $mainAliasName /*".$mainLabelName."*/ " . "\n";
                            $valueUpdateSelectQry .= $valueUpdateJoin . ' ' . "\n";
                            $valueUpdateSelectQry .= "WHERE " . "\n";
                            $valueUpdateSelectQry .= "$valueUpdateWhere " . "\n";
                            
                            if ($groupBy) {
                                $valueUpdateSelectQry .= "GROUP BY " . "\n";
                                $valueUpdateSelectQry .= "$groupBy " . "\n";
                            }
                            
                            foreach ($updateCols as $updateCol) {
                            
                                $updateColumnName = $updateCol['columnName'];
                                $updateShowType = $updateCol['showType'];

                                if ($updateShowType == 'decimal' || $updateShowType == 'bigdecimal' || $updateShowType == 'percent' || $updateShowType == 'number') {

                                    $updateEqualCol .= "TRG.$updateColumnName = TRG.$updateColumnName - SRC.$updateColumnName,";

                                } else {
                                    $updateEqualCol .= "TRG.$updateColumnName = SRC.$updateColumnName,";
                                }
                            }
                            
                            if ($expressionSelectCol) {
                                $valueUpdateSelectQry = "SELECT $martSelectColumn FROM ($valueUpdateSelectQry) SRC";
                            }
                            
                            $updateEqualCol .= "TRG.MODIFIED_DATE = SRC.DATAMART_DATE,";
                            $updateEqualCol = rtrim($updateEqualCol, ',');
                
                            $this->db->Execute("
                                MERGE INTO $tableName TRG 
                                USING ( 
                                    $valueUpdateSelectQry 
                                ) SRC ON ($rowEqualCol) 
                                WHEN MATCHED THEN UPDATE SET
                                    $updateEqualCol");
                        }
                        
                    } else {
                        
                        $this->db->Execute("DELETE FROM $tableName");
                        
                        $sql = "SELECT ID_SEQ.nextval AS ID, $mainIndicatorId AS INDICATOR_ID, $martSelectColumn FROM ($mainSelectQry) SRC";
                    }
                    
                    $insertIntoCol .= 'CREATED_DATE,'; 
                    $insertIntoCol = rtrim($insertIntoCol, ',');
                    
                    $this->db->Execute("INSERT INTO $tableName (ID, INDICATOR_ID, $insertIntoCol) $sql");
                
                } catch (Exception $ex) {
                    
                    $message = $ex->getMessage();
                    $this->db->RollbackTrans();

                    return array('status' => 'error', 'message' => $message);
                }
                
                $this->db->CommitTrans();
                
                $response = array('status' => 'success', 'message' => 'Successfully');
                
                self::updateLastAffectedDateByIndicatorId($mainIndicatorId, $currentDate);
                self::runGenerateKpiRelationDataMartByIndicatorId($mainIndicatorId);
                
                Mdform::clearCacheData($mainIndicatorId);
                
            } else {
                $response = array('status' => 'error', 'message' => 'No config!');
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getIndicatorFieldConfigModel($indicatorId, $isOnlyHeader = false) {
        
        $arr = array();
        
        try {
            
            $data = $this->db->GetAll("
                SELECT 
                    UPPER(COLUMN_NAME) AS COLUMN_NAME, 
                    LABEL_NAME, 
                    SHOW_TYPE, 
                    SEMANTIC_TYPE_ID, 
                    SRC_INDICATOR_PATH, 
                    SIDEBAR_NAME, 
                    TRG_ALIAS_NAME, 
                    COLUMN_WIDTH, 
                    AGGREGATE_FUNCTION, 
                    BODY_ALIGN, 
                    SORT_ORDER, 
                    SORT_TYPE 
                FROM KPI_INDICATOR_INDICATOR_MAP 
                WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND SEMANTIC_TYPE_ID IN (10000000, 10000001) 
                    AND COLUMN_NAME IS NOT NULL 
                    ".($isOnlyHeader ? ' AND PARENT_ID IS NULL' : '')."
                ORDER BY ORDER_NUMBER ASC", 
                array($indicatorId)
            );

            foreach ($data as $row) {
                $arr[$row['COLUMN_NAME']] = array(
                    'columnName'   => $row['COLUMN_NAME'], 
                    'showType'     => $row['SHOW_TYPE'], 
                    'labelName'    => $row['LABEL_NAME'], 
                    'typeId'       => $row['SEMANTIC_TYPE_ID'], 
                    'srcPath'      => $row['SRC_INDICATOR_PATH'], 
                    'sidebarName'  => $row['SIDEBAR_NAME'], 
                    'trgAliasName' => $row['TRG_ALIAS_NAME'], 
                    'columnWidth'  => $row['COLUMN_WIDTH'], 
                    'aggrFunction' => $row['AGGREGATE_FUNCTION'], 
                    'bodyAlign'    => $row['BODY_ALIGN'], 
                    'sortOrder'    => $row['SORT_ORDER'], 
                    'sortType'     => $row['SORT_TYPE'] 
                );
            }
        
        } catch (Exception $ex) { }
        
        return $arr;
    }
    
    public function generateKpiRawDataMartModel($mainIndicatorId, $isSubLoop = false) {
        
        try {
            
            $row = self::getKpiIndicatorRowModel($mainIndicatorId);
            
            $kpiTypeId      = $row['KPI_TYPE_ID'];
            $idPh1          = $this->db->Param(0);
            
            $defaultMinDate = '1970-01-01 01:01:01';
            $lastDate       = null;
            
            if ($kpiTypeId != '1100') {
                
                $columnConfig = $this->db->GetAll("  
                    SELECT 
                        KI_SRC.TABLE_NAME AS SRC_TABLE_NAME, 
                        UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                        KIIM.SHOW_TYPE AS SRC_SHOW_TYPE 
                    FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                        INNER JOIN KPI_INDICATOR KI_SRC ON KIIM.MAIN_INDICATOR_ID = KI_SRC.ID 
                    WHERE KIIM.MAIN_INDICATOR_ID = $idPh1 
                        AND KIIM.COLUMN_NAME IS NOT NULL 
                        AND KIIM.COLUMN_NAME <> 'ID' 
                    GROUP BY 
                        KI_SRC.TABLE_NAME, 
                        KIIM.COLUMN_NAME, 
                        KIIM.ORDER_NUMBER, 
                        KIIM.SHOW_TYPE 
                    ORDER BY 
                        KIIM.ORDER_NUMBER ASC", 
                    array($mainIndicatorId)
                );

                if ($columnConfig) {

                    $firstRow     = $columnConfig[0];
                    $srcTableName = $firstRow['SRC_TABLE_NAME'];
                    $currentDate  = Date::currentDate('Y-m-d H:i:s');

                    $schemaName   = self::getKpiDbSchemaName($mainIndicatorId);
                    $tableName    = $srcTableName ? $srcTableName : $schemaName . 'D_'.$mainIndicatorId;
                    $isTblCreated = self::table_exists($this->db, $tableName);

                    $dbField      = array();

                    foreach ($columnConfig as $col) {

                        $srcColumnName = $col['SRC_COLUMN_NAME'];
                        $srcShowType   = $col['SRC_SHOW_TYPE'];

                        if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$srcColumnName]))) {

                            $dbField[] = array('type' => $srcShowType, 'length' => 4000, 'name' => $srcColumnName);
                        }
                    }

                    if ($isTblCreated == false) {

                        $createTblStatus = self::dbCreatedTblKpiRelationDataMart($tableName, $dbField);

                        if ($createTblStatus['status'] == 'error') {
                            return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                        } else {
                            self::updateKpiIndicatorTblName($mainIndicatorId, $tableName); 
                        }

                    } else {

                        $standardFields = array(
                            array(
                                'type' => 'number', 
                                'name' => 'ID'
                            ), 
                            array(
                                'type' => 'number', 
                                'name' => 'INDICATOR_ID'
                            ), 
                            array(
                                'type' => 'number', 
                                'name' => 'WFM_STATUS_ID'
                            ),
                            array(
                                'type' => 'varchar', 
                                'name' => 'WFM_DESCRIPTION'
                            ), 
                            array(
                                'type' => 'date', 
                                'name' => 'CREATED_DATE'
                            ),
                            array(
                                'type' => 'number', 
                                'name' => 'CREATED_USER_ID'
                            ),
                            array(
                                'type' => 'varchar', 
                                'name' => 'CREATED_USER_NAME'
                            ), 
                            array(
                                'type' => 'date', 
                                'name' => 'MODIFIED_DATE'
                            ),
                            array(
                                'type' => 'number', 
                                'name' => 'MODIFIED_USER_ID'
                            ),
                            array(
                                'type' => 'varchar', 
                                'name' => 'MODIFIED_USER_NAME'
                            ), 
                            array(
                                'type' => 'date', 
                                'name' => 'DELETED_DATE'
                            ),
                            array(
                                'type' => 'number', 
                                'name' => 'DELETED_USER_ID'
                            ),
                            array(
                                'type' => 'varchar', 
                                'name' => 'DELETED_USER_NAME'
                            ), 
                            array(
                                'type' => 'number', 
                                'name' => 'COMPANY_DEPARTMENT_ID'
                            ), 
                            array(
                                'type' => 'date', 
                                'name' => 'GENERATED_DATE'
                            )
                        );

                        foreach ($standardFields as $standardField) {

                            if (!isset($isTblCreated[$standardField['name']])) {

                                $dbField[] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                            }
                        }

                        if ($dbField) {

                            $alterTblStatus = self::dbAlterTblKpiDynamic($tableName, $dbField);

                            if ($alterTblStatus['status'] == 'error') {
                                return array('status' => 'error', 'message' => 'Alter table: ' . $alterTblStatus['message']);
                            }
                        }
                    }
                }
            
            }
            
            Mdform::$isIndicatorRendering = true;       
            $rowExpJson = self::getKpiTemplateExpressionModel($mainIndicatorId, 'VAR_FNC_EXPRESSION_STRING_JSON');

            if ($rowExpJson) {

                $expression = (new Mdexpression())->uxFlowExpression($mainIndicatorId, array('expJson' => $rowExpJson));

                if (isset($expression[0])) {

                    $orderByCase = '';
                    $ids = Arr::implode_key(',', $expression, 'id', true);

                    foreach ($expression as $f => $fv) {
                        $orderByCase .= 'WHEN '.$fv['id'].' THEN '.(++$f).' ';
                    }

                    $executeIndicators = $this->db->GetAll("
                        SELECT 
                            ID, 
                            CODE, 
                            NAME, 
                            KPI_TYPE_ID, 
                            QUERY_STRING 
                        FROM KPI_INDICATOR 
                        WHERE ID IN ($ids) 
                            AND KPI_TYPE_ID IN (1040, 1043, 1044) 
                            AND DELETED_USER_ID IS NULL 
                        ORDER BY 
                            CASE ID 
                            $orderByCase 
                            END ASC");

                } else {
                    throw new Exception('Flowchart-с indicator олдсонгүй!'); 
                }

            } else {

                $executeIndicators = $this->db->GetAll("
                    SELECT 
                        T0.ID, 
                        T0.CODE, 
                        T0.NAME, 
                        T0.KPI_TYPE_ID, 
                        T0.QUERY_STRING, 
                        (
                            SELECT 
                                COUNT(1) 
                            FROM KPI_INDICATOR_INDICATOR_MAP 
                            WHERE SRC_INDICATOR_ID = T0.ID 
                                AND CODE IS NOT NULL 
                                AND CRITERIA IS NOT NULL 
                                AND TRG_INDICATOR_ID IS NOT NULL 
                                AND META_INFO_INDICATOR_ID IS NOT NULL 
                        ) AS IS_DYNAMIC_RELATION 
                    FROM KPI_INDICATOR T0 
                    WHERE T0.PARENT_ID = $idPh1 
                        AND T0.KPI_TYPE_ID IN (1040, 1043, 1044, 1100, 1000) 
                        AND T0.DELETED_USER_ID IS NULL 
                    ORDER BY T0.ORDER_NUMBER ASC", 
                    array($mainIndicatorId)
                );
            }

            if (!$isSubLoop && !$executeIndicators) {
                throw new Exception('Ажиллуулах indicator олдсонгүй!'); 
            }
            
            if ($isSubLoop == false) {
                
                $this->db->BeginTrans(); 
                $this->db->Execute(Ue::createSessionInfo());
            }
            
            $errorIndicatorId = '';
            self::$calcLogDtl = array();
            
            try {

                foreach ($executeIndicators as $row) {
                    
                    $errorIndicatorId = $row['ID'];

                    if ($row['KPI_TYPE_ID'] == '1040') {
                        
                        self::generateKpiRelationDataMartModel($row['ID']);
                        
                        Mdform::clearCacheData($row['ID']);

                    } elseif ($row['KPI_TYPE_ID'] == '1043' && $row['QUERY_STRING']) {

                        $queryStr = $row['QUERY_STRING'];
                        $queryStr = self::replaceNamedParameters($queryStr, $lastDate); 
                        
                        $startTime = Date::currentDate();
                        $this->db->Execute($queryStr);
                        $endTime = Date::currentDate();
                        
                        $affectedRows = $this->db->affected_rows();
                        
                        self::$calcLogDtl[] = array(
                            'indicatorId'  => $errorIndicatorId, 
                            'queryStr'     => $queryStr, 
                            'startTime'    => $startTime, 
                            'endTime'      => $endTime, 
                            'affectedRows' => $affectedRows
                        );
                        
                    } elseif ($row['KPI_TYPE_ID'] == '1044' || $row['KPI_TYPE_ID'] == '1100') {

                        $subResult = self::generateKpiRawDataMartModel($row['ID'], true);
                        
                        if ($subResult['status'] != 'success') {
                            
                            $this->db->RollbackTrans();
                            return array('status' => 'error', 'message' => $subResult['message']);
                            
                        } else {
                            Mdform::clearCacheData($row['ID']);
                        }
                        
                    } elseif ($row['KPI_TYPE_ID'] == '1000' && $row['IS_DYNAMIC_RELATION']) {
                        
                        $subResult = self::runRowsDymamicRelationModel($row['ID']);
                        Mdform::clearCacheData($row['ID']);
                    }
                }

            } catch (Exception $ex) {

                $message = $ex->getMessage();
                $this->db->RollbackTrans();
                
                self::$calcLogDtl[] = array(
                    'indicatorId' => $errorIndicatorId, 
                    'errorMsg'    => $message
                );
                
                self::insertKpiDmCalcMartDtl();

                return array('status' => 'error', 'message' => $message .'<br />IndicatorId: '. $errorIndicatorId);
            }
            
            if ($isSubLoop == false) {
                
                $this->db->CommitTrans();
                
                self::insertKpiDmCalcMartDtl();
            }

            $response = array('status' => 'success', 'message' => 'Successfully');
            
            if ($isSubLoop == false) {

                self::runPivotDataMartModel($mainIndicatorId);
            }
            
        } catch (Exception $ex) {
            
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function insertKpiDmCalcMartDtl() {
        
        if (self::$calcLogDtl) {
            
            try {
                
                foreach (self::$calcLogDtl as $k => $row) {
                    
                    $errorMsg = $queryStr = null;
                    
                    $data = array(
                        'ID'              => getUIDAdd($k), 
                        'DM_CALC_MART_ID' => self::$calcMartId, 
                        'INDICATOR_ID'    => $row['indicatorId'], 
                        'CREATED_DATE'    => Date::currentDate(), 
                        'CREATED_USER_ID' => Ue::sessionUserKeyId()
                    );
                    
                    if (isset($row['errorMsg'])) {
                        
                        if (mb_strlen($row['errorMsg']) > 4000) {
                            $errorMsg = $row['errorMsg'];
                        } else {
                            $data['ERROR_MSG'] = $row['errorMsg'];
                        }
                        
                    } else {
                        
                        $data['FIRST_LOAD_TIME'] = $row['startTime'];
                        $data['LAST_LOAD_TIME'] = $row['endTime'];
                        $data['AFFECTED_ROWS_QTY'] = is_bool($row['affectedRows']) ? null : $row['affectedRows'];
                        
                        if (mb_strlen($row['queryStr']) > 4000) {
                            $queryStr = $row['queryStr'];
                        } else {
                            $data['EXECUTED_QUERY'] = $row['queryStr'];
                        }
                    }

                    $this->db->AutoExecute('KPI_DM_CALC_MART_DTL', $data);
                    
                    if ($errorMsg) {
                        $this->db->UpdateClob('KPI_DM_CALC_MART_DTL', 'ERROR_MSG', $errorMsg, 'ID = '.$data['ID']);
                    }
                    
                    if ($queryStr) {
                        $this->db->UpdateClob('KPI_DM_CALC_MART_DTL', 'EXECUTED_QUERY', $queryStr, 'ID = '.$data['ID']);
                    }
                }
                
            } catch (Exception $ex) {
                return false;
            }
        }
        
        return true;
    }
    
    public function getKpiRowsModel($id) {
        
        $_POST['indicatorId'] = $id;
        $_POST['page']        = 1;
        $_POST['rows']        = 500;
        $_POST['isIgnoreCompanyDepartmentId'] = true;

        return $this->indicatorDataGridModel();

    }

    public function runRowsDymamicRelationModel($id) {
        
        $_POST['indicatorId'] = $id;
        $_POST['page']        = 1;
        $_POST['rows']        = 500;
        $_POST['isIgnoreCompanyDepartmentId'] = true;

        $resultRows = $this->indicatorDataGridModel();

        if ($resultRows['status'] == 'success' && isset($resultRows['rows'][0])) {
            
            $configRow = self::getKpiIndicatorRowModel($id);
            $configRow['isIgnoreStandardFields'] = true;

            $columnsData = self::getKpiIndicatorColumnsModel($id, $configRow);
            $fieldConfig = self::getKpiIndicatorIdFieldModel($id, $columnsData);
                
            foreach ($resultRows['rows'] as $resultRow) {
                
                $_POST['isKpiComponent'] = 1;
                self::kpiSaveMetaDmRecordMap($id, $resultRow['ID'], $resultRow);
            }
        }
    }
    
    public function runPivotDataMartModel($indicatorId) {
        
        try {
            
            $schemaName = Config::getFromCache('kpiDbSchemaName');
            $schemaName = $schemaName ? rtrim($schemaName, '.').'.' : '';
            
            $data = $this->db->GetAll("
                SELECT 
                    SRC_RECORD_ID, 
                    DATA 
                FROM ".$schemaName."V_16705727959689 
                WHERE RAW_DATASET_ID = ".$this->db->Param(0), 
                array($indicatorId)
            );
            
            foreach ($data as $row) {
                
                $response = self::runOnePivotDataMartModel($indicatorId, $row['SRC_RECORD_ID'], $row['DATA']);
                
                if ($response['status'] != 'success') {
                    break;
                }
            }
            
            if (!isset($response)) {
                $response = array('status' => 'error', 'message' => 'empty');
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function runOnePivotDataMartModel($dataIndicatorId, $pivotIndicatorId, $jsonStr) {
        
        try {
            
            $jsonData = json_decode($jsonStr, true);
            
            if (isset($jsonData['ROW_GROUP']) && isset($jsonData['COLUMN_GROUP']) && isset($jsonData['DATA_GROUP'])) {
                
                $rowGroup      = Arr::sortBy('ROW_GROUP_ORDER', $jsonData['ROW_GROUP'], 'asc');
                $columnGroup   = Arr::sortBy('COLUMN_GROUP_ORDER', $jsonData['COLUMN_GROUP'], 'asc');
                $dataGroup     = Arr::sortBy('DATA_GROUP_ORDER', $jsonData['DATA_GROUP'], 'asc');
                
                $allRowAggregate = issetParam($jsonData['ROW_AGGREGATE']);
                $allColAggregate = issetParam($jsonData['COLUMN_AGGREGATE']);
                $filterGroup     = issetParam($jsonData['FILTER_GROUP']);
                
                $configRow     = self::getKpiIndicatorRowModel($dataIndicatorId);
                $dataTableName = $configRow['TABLE_NAME'];
                
                $sessionUserId = Ue::sessionUserKeyId();
                $currentDate   = Date::currentDate();
                $columnAggr    = array();
                
                $columnGroupFields = $columnGroupGroupBy = $columnGroupOrderBy = $dataFilterWhere = '';
                
                if ($filterGroup) {
                    
                    $dataFilter = '';
                    
                    foreach ($filterGroup as $filterGroupRow) {
                        
                        $filterGroupCondition = $filterGroupRow['FILTER_GROUP_CONDITION'];
                        
                        if ($filterGroupCondition) {
                            $dataFilter .= ' AND ' . $filterGroupCondition;
                        }
                    }
                    
                    $dataFilterWhere = $dataFilter;
                }
                
                $fieldConfigs = self::getIndicatorFieldConfigModel($dataIndicatorId);
                    
                if (!$fieldConfigs) {
                    throw new Exception($dataIndicatorId . ' тохиргоо олдсонгүй!'); 
                }
                
                foreach ($columnGroup as $columnRow) {
                    
                    $columnGroupField = $columnRow['COLUMN_GROUP_FIELD'];
                    $columnGroupAggr  = issetParam($columnRow['COLUMN_GROUP_AGGREGATE']);
                    
                    if (isset($fieldConfigs[$columnGroupField]['showType']) && $fieldConfigs[$columnGroupField]['showType'] == 'combo') {
                        
                        $columnGroupFields .= $columnGroupField . '_DESC AS ' . $columnGroupField . ', ';
                        $columnGroupGroupBy .= $columnGroupField . '_DESC, ';
                        $columnGroupOrderBy .= $columnGroupField . '_DESC ASC, ';
                        
                    } else {
                        $columnGroupFields .= $columnGroupField . ', ';
                        $columnGroupGroupBy .= $columnGroupField . ', ';
                        $columnGroupOrderBy .= $columnGroupField . ' ASC, ';
                    }
                    
                    if ($columnGroupAggr && !$columnAggr) {
                        
                        $columnAggr = array(
                            'field'     => $columnGroupField, 
                            'aggregate' => $columnGroupAggr, 
                            'mask'      => issetParam($columnRow['COLUMN_GROUP_AGGREGATE_MASK'])
                        );
                    }
                }
                
                $columnGroupFields  = rtrim(trim($columnGroupFields), ',');
                $columnGroupGroupBy = rtrim(trim($columnGroupGroupBy), ',');
                $columnGroupOrderBy = rtrim(trim($columnGroupOrderBy), ',');
                
                $columnGroupSelect = "
                    SELECT 
                        $columnGroupFields 
                    FROM $dataTableName 
                    WHERE 1 = 1 
                        $dataFilterWhere 
                    GROUP BY 
                       $columnGroupGroupBy 
                    ORDER BY 
                        $columnGroupOrderBy";
                
                $columnGroupData = $this->db->GetAll($columnGroupSelect);
                
                if ($columnGroupData) {
                    
                    $configRow      = self::getKpiIndicatorRowModel($pivotIndicatorId);
                    $schemaName     = self::getKpiDbSchemaName($pivotIndicatorId);
                    $pivotTableName = $configRow['TABLE_NAME'];
                    $pivotTableName = $pivotTableName ? $pivotTableName : $schemaName . 'D_'.$pivotIndicatorId;
                    $isTblCreated   = self::table_exists($this->db, $pivotTableName);
                    $pivotPrefix    = 'P';
                    
                    $c = $n = 1;
                    $columnGroupCount = count($columnGroupData);
                    
                    $map = $mapField = $checkAddAggrCol = $tempDataGroup = array();
                    $aggregateNames = array('sum' => 'нийт', 'avg' => 'дундаж');
                    
                    foreach ($dataGroup as $dataGroupRow) {
                        
                        if ($dataGroupRow['DATA_GROUP_FIELD'] != '') {
                            
                            $dataGroupFieldUpper = strtoupper($dataGroupRow['DATA_GROUP_FIELD']);
                            
                            $dataGroupRow['columnWidth'] = issetDefaultVal($fieldConfigs[$dataGroupFieldUpper]['columnWidth'], null);
                            $dataGroupRow['showType']    = issetDefaultVal($fieldConfigs[$dataGroupFieldUpper]['showType'], 'text');
                            $dataGroupRow['bodyAlign']   = issetDefaultVal($fieldConfigs[$dataGroupFieldUpper]['bodyAlign'], null);
                            
                            $tempDataGroup[$dataGroupFieldUpper] = $dataGroupRow;
                        }
                    }

                    $dataGroup      = $tempDataGroup;
                    $dataGroupCount = count($dataGroup);
                    
                    foreach ($rowGroup as $r => $rowGroupRow) {
                        
                        $colName     = strtoupper($rowGroupRow['ROW_GROUP_FIELD']);
                        $isFilter    = issetParam($rowGroupRow['ROW_GROUP_IS_FILTER']);
                        $isHide      = issetParam($rowGroupRow['ROW_GROUP_IS_HIDE']);
                        $labelName   = issetParam($rowGroupRow['ROW_GROUP_LABEL_NAME']);
                        $labelName   = $labelName ? $labelName : issetDefaultVal($fieldConfigs[$colName]['labelName'], $colName);
                        $defaultVal  = issetDefaultVal($rowGroupRow['ROW_GROUP_FILTER_DEFAULT_VALUE'], null);
                        $showType    = issetDefaultVal($fieldConfigs[$colName]['showType'], 'text');
                        $showType    = ($showType == 'combo') ? 'text' : $showType;
                        $columnWidth = issetDefaultVal($fieldConfigs[$colName]['columnWidth'], null);
                        $bodyAlign   = issetDefaultVal($fieldConfigs[$colName]['bodyAlign'], null);
                        
                        $map[] = array(
                            'ID'                => getUIDAdd($n), 
                            'COLUMN_NAME'       => $colName, 
                            'COLUMN_NAME_PATH'  => $colName, 
                            'SIDEBAR_NAME'      => null, 
                            'LABEL_NAME'        => $labelName, 
                            'MAIN_INDICATOR_ID' => $pivotIndicatorId, 
                            'IS_INPUT'          => 1, 
                            'IS_FILTER'         => ($isFilter == '1' ? 1 : null),
                            'IS_RENDER'         => ($isHide == '1' ? null : 1),
                            'DEFAULT_VALUE'     => $defaultVal,
                            'SHOW_TYPE'         => $showType, 
                            'COLUMN_WIDTH'      => $columnWidth, 
                            'BODY_ALIGN'        => $bodyAlign,
                            'SEMANTIC_TYPE_ID'  => 10000000, 
                            'ORDER_NUMBER'      => $n, 
                            'CREATED_DATE'      => $currentDate, 
                            'CREATED_USER_ID'   => $sessionUserId
                        );

                        $mapField[] = array(
                            'name' => $colName, 
                            'type' => $showType
                        );
                        
                        $n ++;
                    }
                    
                    foreach ($columnGroupData as $ck => $columnGroupRow) {
                        
                        $mergeTitle = Arr::implode_r('|', $columnGroupRow, true);
                        
                        foreach ($dataGroup as $dataGroupRow) {
                            
                            $dataField   = $dataGroupRow['DATA_GROUP_FIELD'];
                            $labelName   = $dataGroupRow['DATA_GROUP_LABEL'] ? $dataGroupRow['DATA_GROUP_LABEL'] : $dataField;
                            $showType    = $dataGroupRow['showType'];
                            $columnWidth = $dataGroupRow['columnWidth'];
                            $bodyAlign   = $dataGroupRow['bodyAlign'];
                            $colName     = $pivotPrefix.$c;
                            
                            if ($dataGroupCount == 1) {
                                $rowLabelName = $mergeTitle;
                            } else {
                                $rowLabelName = $mergeTitle . '|' . $labelName;
                            }
                            
                            $map[] = array(
                                'ID'                 => getUIDAdd($n), 
                                'COLUMN_NAME'        => $colName, 
                                'COLUMN_NAME_PATH'   => $colName, 
                                'SIDEBAR_NAME'       => $mergeTitle, 
                                'LABEL_NAME'         => $rowLabelName, 
                                'MAIN_INDICATOR_ID'  => $pivotIndicatorId, 
                                'IS_INPUT'           => 1, 
                                'SHOW_TYPE'          => $showType, 
                                'SEMANTIC_TYPE_ID'   => 10000001, 
                                'SRC_INDICATOR_PATH' => $mergeTitle . '|' . $dataField,
                                'TRG_ALIAS_NAME'     => $dataField, 
                                'COLUMN_WIDTH'       => $columnWidth, 
                                'BODY_ALIGN'         => $bodyAlign,
                                'ORDER_NUMBER'       => $n, 
                                'CREATED_DATE'       => $currentDate, 
                                'CREATED_USER_ID'    => $sessionUserId
                            );
                            
                            $mapField[] = array(
                                'name' => $colName, 
                                'type' => $showType
                            );
                            
                            $c ++;
                            $n ++;
                        }
                        
                        if ($columnAggr) {
                            
                            if ($dataGroupCount == 1) {
                                
                                $addAggrCol = $columnGroupRow[$columnAggr['field']];

                                if ($ck == 0) {
                                    $checkAddAggrCol[$addAggrCol] = 1;
                                    $aggrLabelName = $addAggrCol;
                                }

                                if ($columnGroupCount == ($ck + 1)) {
                                    $aggrLabelName = $addAggrCol;
                                    $addAggrCol = '/*-';
                                    $orderNum = $n;
                                } else {
                                    $orderNum = $n - 2;
                                }

                                if (!isset($checkAddAggrCol[$addAggrCol])) {

                                    $colName     = $pivotPrefix.$c;
                                    $showType    = 'decimal';
                                    $columnWidth = '80px';
                                    
                                    if ($columnAggr['mask']) {
                                        
                                        $labelName = str_replace('['.$columnAggr['field'].']', $aggrLabelName, $columnAggr['mask']);
                                        
                                    } else {
                                        $labelName = $aggrLabelName . ' ' .$aggregateNames[$columnAggr['aggregate']];
                                    }

                                    $map[] = array(
                                        'ID'                 => getUIDAdd($n), 
                                        'COLUMN_NAME'        => $colName, 
                                        'COLUMN_NAME_PATH'   => $colName, 
                                        'SIDEBAR_NAME'       => null, 
                                        'LABEL_NAME'         => $labelName, 
                                        'MAIN_INDICATOR_ID'  => $pivotIndicatorId, 
                                        'IS_INPUT'           => 1, 
                                        'SHOW_TYPE'          => $showType, 
                                        'COLUMN_WIDTH'       => $columnWidth, 
                                        'SEMANTIC_TYPE_ID'   => 10000001, 
                                        'SRC_INDICATOR_PATH' => $columnAggr['field'].'='.$aggrLabelName,
                                        'AGGREGATE_FUNCTION' => $columnAggr['aggregate'],
                                        'TRG_ALIAS_NAME'     => $dataField, 
                                        'ORDER_NUMBER'       => $orderNum, 
                                        'CREATED_DATE'       => $currentDate, 
                                        'CREATED_USER_ID'    => $sessionUserId
                                    );

                                    $mapField[] = array(
                                        'name' => $colName, 
                                        'type' => $showType
                                    );

                                    $c ++;
                                    $n ++;

                                    $checkAddAggrCol[$addAggrCol] = 1;
                                    $aggrLabelName = $addAggrCol;
                                }
                            }
                            
                        }
                    }
                    
                    if ($allRowAggregate) {
                        
                        $allRowAggregateMask = issetParam($jsonData['ROW_AGGREGATE_MASK']);
                        
                        if ($allRowAggregateMask) {
                            $allRowAggregateMaskLabel = $allRowAggregateMask;
                        } else {
                            $allRowAggregateMaskLabel = 'Бүгд ' .$aggregateNames[$allRowAggregate];
                        }
                        
                        foreach ($dataGroup as $dataGroupRow) {
                            
                            $dataField = strtoupper($dataGroupRow['DATA_GROUP_FIELD']);
                            $labelName = $dataGroupRow['DATA_GROUP_LABEL'] ? $dataGroupRow['DATA_GROUP_LABEL'] : $dataField;
                            
                            $colName     = $pivotPrefix.$c;
                            $showType    = 'decimal';
                            $columnWidth = '90px';
                            
                            if ($dataGroupCount == 1) {
                                $rowLabelName = $allRowAggregateMaskLabel;
                            } else {
                                $rowLabelName = $allRowAggregateMaskLabel . '|' . $labelName;
                            }

                            $map[] = array(
                                'ID'                 => getUIDAdd($n), 
                                'COLUMN_NAME'        => $colName, 
                                'COLUMN_NAME_PATH'   => $colName, 
                                'SIDEBAR_NAME'       => null, 
                                'LABEL_NAME'         => $rowLabelName, 
                                'MAIN_INDICATOR_ID'  => $pivotIndicatorId, 
                                'IS_INPUT'           => 1, 
                                'SHOW_TYPE'          => $showType, 
                                'COLUMN_WIDTH'       => $columnWidth, 
                                'SEMANTIC_TYPE_ID'   => 10000001, 
                                'SRC_INDICATOR_PATH' => null,
                                'AGGREGATE_FUNCTION' => $allRowAggregate,
                                'TRG_ALIAS_NAME'     => $dataField, 
                                'ORDER_NUMBER'       => $n, 
                                'CREATED_DATE'       => $currentDate, 
                                'CREATED_USER_ID'    => $sessionUserId
                            );

                            $mapField[] = array(
                                'name' => $colName, 
                                'type' => $showType
                            );

                            $c ++;
                            $n ++;
                        }
                    }
                    
                    $rowGroupFields = $rowGroupGroupBy = $rowGroupOrderBy = $deleteJoin = $mergeMatch = '';
                
                    foreach ($rowGroup as $rowRow) {

                        $rowGroupField = $rowRow['ROW_GROUP_FIELD'];
                        
                        if (isset($fieldConfigs[$rowGroupField]['showType']) && $fieldConfigs[$rowGroupField]['showType'] == 'combo') {
                            $rowGroupFields .= $rowGroupField . '_DESC AS ' . $rowGroupField . ', ';
                            $rowGroupGroupBy .= $rowGroupField . '_DESC, ';
                            $rowGroupOrderBy .= $rowGroupField . '_DESC ASC, ';
                        } else {
                            $rowGroupFields .= $rowGroupField . ', ';
                            $rowGroupGroupBy .= $rowGroupField . ', ';
                            $rowGroupOrderBy .= $rowGroupField . ' ASC, ';
                        }
                        
                        $deleteJoin .= "T1.$rowGroupField = T0.$rowGroupField AND ";
                        
                        if (!isset($deleteJoinCheckField)) {
                            $deleteJoinCheckField = $rowGroupField;
                        }
                        
                        //$mergeMatch .= $this->db->IfNull("TRG.$rowGroupField", "'-1'"). " = ".$this->db->IfNull("SRC.$rowGroupField", "'-1'")." AND ";
                        $mergeMatch .= "TRG.$rowGroupField = SRC.$rowGroupField AND ";
                    }

                    $rowGroupFields = rtrim(trim($rowGroupFields), ',');
                    $rowGroupGroupBy = rtrim(trim($rowGroupGroupBy), ',');
                    $rowGroupOrderBy = rtrim(trim($rowGroupOrderBy), ',');
                    
                    $sortColumns = array();
                    
                    foreach ($fieldConfigs as $c => $column) {
                        
                        if (isset($column['sortOrder']) && isset($column['sortType']) && $column['sortOrder'] != '' && $column['sortType'] != '') {
                            $sortColumns[$column['sortOrder'].'_'.$c] = $column;
                        }
                    }
                    
                    if ($sortColumns) {
                
                        ksort($sortColumns);
                        
                        $rowGroupOrderBy = '';
                        
                        foreach ($sortColumns as $sortColumn) {
                            
                            $sortColumnName = $sortColumn['columnName'];
                            
                            if (isset($fieldConfigs[$sortColumnName]['showType']) && $fieldConfigs[$sortColumnName]['showType'] == 'combo') {
                                
                                $rowGroupOrderBy .= $sortColumnName . '_DESC ' . $sortColumn['sortType'] . ', ';
                                $rowGroupGroupBy .= ', ' . $sortColumnName . '_DESC';
                                
                            } else {
                                $rowGroupOrderBy .= $sortColumnName . ' ' . $sortColumn['sortType'] . ', ';
                                $rowGroupGroupBy .= ', ' . $sortColumnName;
                            }
                        }

                        $rowGroupOrderBy = rtrim(trim($rowGroupOrderBy), ',');
                    }

                    $rowGroupSelect = "
                        SELECT 
                            $rowGroupFields 
                        FROM $dataTableName 
                        WHERE 1 = 1 
                            $dataFilterWhere 
                        GROUP BY 
                            $rowGroupGroupBy 
                        ORDER BY 
                            $rowGroupOrderBy";

                    $rowGroupData = $this->db->GetAll($rowGroupSelect);
                    
                    if ($rowGroupData) {
                        
                        if (!$isTblCreated) {
                        
                            $createTblStatus = self::dbCreatedTblKpiDataMart($pivotTableName, $mapField);

                            if ($createTblStatus['status'] == 'error') {
                                return array('status' => 'error', 'message' => 'Create table: ' . $createTblStatus['message']);
                            } else {
                                self::updateKpiIndicatorTblName($pivotIndicatorId, $pivotTableName); 
                            }
                            
                        } else {
                            
                            $this->db->Execute("DROP TABLE $pivotTableName CASCADE CONSTRAINTS");
                            
                            self::dbCreatedTblKpiDataMart($pivotTableName, $mapField);
                        }
                        
                        $this->db->Execute("DELETE FROM KPI_INDICATOR_INDICATOR_MAP WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0), array($pivotIndicatorId));
                        
                        foreach ($map as $insertMap) {
                            $this->db->AutoExecute('KPI_INDICATOR_INDICATOR_MAP', $insertMap);
                        }
                        
                        $this->db->Execute("DELETE FROM $pivotTableName");
                        
                        $firstSequenceId = getUID();
                        
                        foreach ($rowGroupData as $r => $rowGroupDataRow) {
                            
                            $rowGroupDataRow['ID'] = $firstSequenceId + $r;    
                            $rowGroupDataRow['CREATED_USER_ID'] = $sessionUserId;
                            $rowGroupDataRow['CREATED_DATE'] = $currentDate;    
                            $rowGroupDataRow['GENERATED_DATE'] = $currentDate;  
                            
                            $this->db->AutoExecute($pivotTableName, $rowGroupDataRow);
                        }
                        
                        $pivotFieldConfigs = self::getIndicatorFieldConfigModel($pivotIndicatorId);
                        $rowMergeFields = $rowGroupFields;
                                
                        foreach ($dataGroup as $dataGroupRow) {
                            
                            $dataField = strtoupper($dataGroupRow['DATA_GROUP_FIELD']);
                            $rowMergeFields .= ", SUM(NVL($dataField, 0)) AS $dataField";
                        }
                        
                        $columnMergeFields = '';
                        
                        foreach ($columnGroup as $i => $columnRow) {
                            
                            $columnGroupField = $columnRow['COLUMN_GROUP_FIELD'];
                            
                            if (isset($fieldConfigs[$columnGroupField]['showType']) && $fieldConfigs[$columnGroupField]['showType'] == 'combo') {
                                $columnMergeFields .= " AND " . $columnGroupField . "_DESC = ':$i'";
                            } else {
                                $columnMergeFields .= " AND $columnGroupField = ':$i'";
                            }
                        }
                        
                        $mergeMatch = rtrim(trim($mergeMatch), 'AND');
                        
                        $criteriaAggregate = issetParam($jsonData['CRITERIA_AGGREGATE']);
                        
                        $criteriaAggregateTmp = $criteriaAggregateAllRowTmp = 
                        $criteriaAggregateLoop = $criteriaAggregateAllRowLoop = array();
                        
                        if ($criteriaAggregate) {
                            
                            foreach ($criteriaAggregate as $criteriaAggregateRow) {
                                
                                if ($criteriaAggregateRow['AGGREGATE_COLUMN_FIELD'] 
                                    && $criteriaAggregateRow['AGGREGATE_DATA_FIELD'] 
                                    && $criteriaAggregateRow['AGGREGATE_FUNCTION'] 
                                    && $criteriaAggregateRow['AGGREGATE_CRITERIA']) {
                                    
                                    $aggregateColumnFieldUpper = strtoupper($criteriaAggregateRow['AGGREGATE_COLUMN_FIELD']);
                                    $aggregateDataFieldUpper   = strtoupper($criteriaAggregateRow['AGGREGATE_DATA_FIELD']);
                                    
                                    $criteriaAggregateTmp[$aggregateColumnFieldUpper][$aggregateDataFieldUpper] = array(
                                        'function' => $criteriaAggregateRow['AGGREGATE_FUNCTION'], 
                                        'criteria' => $criteriaAggregateRow['AGGREGATE_CRITERIA']
                                    );
                                    
                                } elseif ($criteriaAggregateRow['AGGREGATE_DATA_FIELD'] 
                                    && $criteriaAggregateRow['AGGREGATE_FUNCTION'] 
                                    && $criteriaAggregateRow['AGGREGATE_CRITERIA']) {
                                    
                                    $aggregateDataFieldUpper = strtoupper($criteriaAggregateRow['AGGREGATE_DATA_FIELD']);
                                    
                                    $criteriaAggregateAllRowTmp[$aggregateDataFieldUpper] = array(
                                        'function' => $criteriaAggregateRow['AGGREGATE_FUNCTION'], 
                                        'criteria' => $criteriaAggregateRow['AGGREGATE_CRITERIA']
                                    );
                                }
                            }
                        }
                        
                        foreach ($pivotFieldConfigs as $pivotColumn => $pivotFieldConfigRow) {
                            
                            $typeId = $pivotFieldConfigRow['typeId'];
                            
                            if ($typeId == '10000001') {
                                
                                $aggrFunction = $pivotFieldConfigRow['aggrFunction'];
                                $trgAliasName = $pivotFieldConfigRow['trgAliasName'];
                                $srcPath      = $pivotFieldConfigRow['srcPath'];
                                
                                $columnMergeFieldsLoop = '';
                                
                                if ($aggrFunction) {
                                    
                                    if ($srcPath) {
                                        
                                        $srcPath = explode('|', $srcPath);
                                        
                                        foreach ($srcPath as $srcKey => $srcVal) {
                                            
                                            $srcValArr = explode('=', $srcVal);
                                            $srcColName = $srcValArr[0];
                                            
                                            if (isset($fieldConfigs[$srcColName]['showType']) && $fieldConfigs[$srcColName]['showType'] == 'combo') {
                                                $columnMergeFieldsLoop .= ' AND ' . $srcColName . "_DESC = '" . $srcValArr[1] . "'";
                                            } else {
                                                $columnMergeFieldsLoop .= ' AND ' . $srcColName . " = '" . $srcValArr[1] . "'";
                                            }
                                            
                                            if ($srcKey == 0 && isset($criteriaAggregateTmp[$srcColName][$trgAliasName])) {
                                                
                                                $criteriaAggregateStr = $criteriaAggregateTmp[$srcColName][$trgAliasName]['criteria'];
                                                $criteriaAggregateStr = rtrim(trim($criteriaAggregateStr), 'AND');
                                                $criteriaAggregateStr = ltrim(trim($criteriaAggregateStr), 'AND');
                                                
                                                $criteriaAggregateLoop[] = array(
                                                    'pivotColumn'  => $pivotColumn, 
                                                    'trgAliasName' => $trgAliasName, 
                                                    'function'     => $criteriaAggregateTmp[$srcColName][$trgAliasName]['function'], 
                                                    'criteria'     => $columnMergeFieldsLoop . ' AND '.$criteriaAggregateStr
                                                );
                                            }
                                        }
                                        
                                    } else {
                                        
                                        $columnMergeFieldsLoop = '';
                                        
                                        if (isset($criteriaAggregateAllRowTmp[$trgAliasName])) {
                                            
                                            $criteriaAggregateStr = $criteriaAggregateAllRowTmp[$trgAliasName]['criteria'];
                                            $criteriaAggregateStr = rtrim(trim($criteriaAggregateStr), 'AND');
                                            $criteriaAggregateStr = ltrim(trim($criteriaAggregateStr), 'AND');
                                                
                                            $criteriaAggregateAllRowLoop[] = array(
                                                'pivotColumn'  => $pivotColumn, 
                                                'trgAliasName' => $trgAliasName, 
                                                'function'     => $criteriaAggregateAllRowTmp[$trgAliasName]['function'], 
                                                'criteria'     => $criteriaAggregateStr
                                            );
                                        }
                                    }
                                    
                                } else {
                                    
                                    $columnMergeFieldsLoop = $columnMergeFields;
                                    $srcPath = explode('|', $srcPath);
                                
                                    foreach ($srcPath as $srcKey => $srcVal) {
                                        $columnMergeFieldsLoop = str_replace("':$srcKey'", "'$srcVal'", $columnMergeFieldsLoop);
                                    }
                                }
                                
                                $mergeSql = "
                                    MERGE INTO $pivotTableName TRG 
                                    USING ( 
                                        SELECT 
                                            $rowMergeFields 
                                        FROM 
                                            $dataTableName 
                                        WHERE 1 = 1 
                                            $dataFilterWhere 
                                            $columnMergeFieldsLoop  
                                        GROUP BY 
                                            $rowGroupGroupBy 
                                    ) SRC ON ($mergeMatch) 
                                    WHEN MATCHED THEN UPDATE SET 
                                        $pivotColumn = SRC.$trgAliasName";

                                $this->db->Execute($mergeSql);
                            }
                        }
                        
                        if ($criteriaAggregateLoop) {
                            
                            foreach ($criteriaAggregateLoop as $criteriaAggregateLoopRow) {
                                
                                $pivotColumn  = $criteriaAggregateLoopRow['pivotColumn'];
                                $trgAliasName = $criteriaAggregateLoopRow['trgAliasName'];
                                $criteria     = $criteriaAggregateLoopRow['criteria'];
                                $function     = $criteriaAggregateLoopRow['function'];
                                
                                $rowMergeFields = $rowGroupFields;
                                
                                $rowMergeFields .= ", $function(NVL($trgAliasName, 0)) AS $trgAliasName";
                                
                                $mergeSql = "
                                    MERGE INTO $pivotTableName TRG 
                                    USING ( 
                                        SELECT 
                                            $rowMergeFields 
                                        FROM 
                                            $dataTableName 
                                        WHERE 1 = 1 
                                            $dataFilterWhere 
                                            $criteria 
                                        GROUP BY 
                                            $rowGroupGroupBy 
                                    ) SRC ON ($mergeMatch) 
                                    WHEN MATCHED THEN UPDATE SET 
                                        $pivotColumn = SRC.$trgAliasName";
                                
                                $this->db->Execute($mergeSql);
                            }
                        }
                        
                        if ($criteriaAggregateAllRowLoop) {
                            
                            $allRowGroupBy = $rowGroupFields = $allRowGroupByTmp = $rowGroupFieldsTmp = '';
                            
                            foreach ($columnGroup as $columnRow) {
                                
                                if ($columnRow['COLUMN_GROUP_FIELD'] && $columnRow['COLUMN_GROUP_AGGREGATE']) {
                                    
                                    $allRowGroupBy .= $columnRow['COLUMN_GROUP_FIELD'] . ',';
                                }
                            }
                            
                            foreach ($rowGroup as $rowRow) {

                                $rowGroupField = $rowRow['ROW_GROUP_FIELD'];

                                $rowGroupFields    .= $rowGroupField . ', ';
                                $allRowGroupBy     .= $rowGroupField . ', ';
                                
                                $rowGroupFieldsTmp .= 'TMP.' . $rowGroupField . ', ';
                                $allRowGroupByTmp  .= 'TMP.' . $rowGroupField . ', ';
                            }
                            
                            $rowGroupFields    = rtrim(trim($rowGroupFields), ',');
                            $rowGroupFieldsTmp = rtrim(trim($rowGroupFieldsTmp), ',');
                            $allRowGroupBy     = rtrim(trim($allRowGroupBy), ',');
                            $allRowGroupByTmp  = rtrim(trim($allRowGroupByTmp), ',');
                            
                            foreach ($criteriaAggregateAllRowLoop as $criteriaAggregateAllRowLoopRow) {
                                
                                $pivotColumn  = $criteriaAggregateAllRowLoopRow['pivotColumn'];
                                $trgAliasName = $criteriaAggregateAllRowLoopRow['trgAliasName'];
                                $function     = $criteriaAggregateAllRowLoopRow['function'];
                                $criteria     = $criteriaAggregateAllRowLoopRow['criteria'];
                                $criteria     = 'AND ' . $criteria;
                                
                                $mergeSql = "
                                    MERGE INTO $pivotTableName TRG 
                                    USING ( 
                                        SELECT 
                                            $rowGroupFieldsTmp, $function(NVL(TMP.$trgAliasName, 0)) AS $trgAliasName 
                                        FROM ( 
                                                SELECT 
                                                    $rowGroupFields, $function(NVL($trgAliasName, 0)) AS $trgAliasName 
                                                FROM 
                                                    $dataTableName 
                                                WHERE 1 = 1 
                                                    $dataFilterWhere 
                                                    $criteria 
                                                GROUP BY 
                                                    $allRowGroupBy 
                                            ) TMP 
                                        GROUP BY 
                                            $allRowGroupByTmp
                                    ) SRC ON ($mergeMatch) 
                                    WHEN MATCHED THEN UPDATE SET 
                                        $pivotColumn = SRC.$trgAliasName";
                                
                                $this->db->Execute($mergeSql);
                            }
                        }
                    }
                    
                    $response = array('status' => 'success', 'message' => 'Successfuly');
                }
                
            } else {
                throw new Exception('Pivot тохиргоо олдсонгүй!'); 
            }
            
        } catch (Exception $ex) {
            file_put_contents('log/dddddd.log', $ex->getMessage() . "\n");
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function joinCriteriaParse($aliasName, $criteria) {
        
        preg_match_all('/\[(.*?)\]/', $criteria, $colCatch);
                
        if (isset($colCatch[0][0])) {
            foreach ($colCatch[1] as $eventPath) {
                $criteria = str_replace("[$eventPath]", $aliasName.'.'.$eventPath, $criteria);
            }
        }
        
        $criteria = self::replaceNamedParameters($criteria);
        
        return $criteria;
    }
    
    public function replaceNamedParameters($criteria, $lastDate = 'null') {
        
        $sessionValues = Session::get(SESSION_PREFIX.'sessionValues');
        $sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid']);
        $sessionDepartmentId = Ue::sessionUserKeyDepartmentId();
        $sessionUserKeyId = Ue::sessionUserKeyId();
        $sessionUserId = Ue::sessionUserId();
        
        if (!$sessionCompanyDepartmentId) {
            $sessionCompanyDepartmentId = $sessionDepartmentId;
        }
        
        $replaceParams = array(
            ':sessionCompanyDepartmentId' => $sessionCompanyDepartmentId, 
            ':sessionDepartmentId'        => $sessionDepartmentId, 
            ':sessionUserKeyDepartmentId' => $sessionDepartmentId, 
            ':sessionUserKeyId'           => $sessionUserKeyId, 
            ':sessionUserId'              => $sessionUserId, 
            ':sessionPositionKeyId'       => Ue::sessionPositionKeyId(), 
            ':sessionEmployeeId'          => Ue::sessionEmployeeId(), 
            ':sessionEmployeeKeyId'       => Ue::sessionEmployeeKeyId(), 
            ':sessionUserKeyDepartmentId' => Ue::sessionUserKeyDepartmentId(), 
            ':sessionPersonId'            => Session::get(SESSION_PREFIX.'personid'), 
            ':langCode'                   => "'".Lang::getCode()."'", 
            ':lastAffectedDate'           => $lastDate
        );
        
        $criteria = str_ireplace(array_keys($replaceParams), array_values($replaceParams), $criteria);
        $criteria = self::replaceKpiDbSchemaName($criteria);
        
        return $criteria;
    }
    
    public function dbCreatedTblKpiDataMart($tblName, $dbField) {
        
        try {
            
            $indexing = array();
            $fields = '';
        
            foreach ($dbField as $row) {
                
                $lowerName = strtolower($row['name']);
                $isCorrectColumnName = (boolean) preg_match("/^[_0-9a-zA-Z]{1,30}$/i", $row['name']);
                
                if ($lowerName == 'company_department_id' || !$isCorrectColumnName) {
                    continue;
                }
                
                if ($row['type'] == 'clob') {
                    $fields .= '"'.$row['name'].'" CLOB,';
                } elseif ($row['type'] == 'date' || $row['type'] == 'datetime') {
                    $fields .= '"'.$row['name'].'" DATE,';
                } elseif ($row['type'] == 'combo' || $row['type'] == 'popup' || $row['type'] == 'radio' || $row['type'] == 'number' || $row['type'] == 'long') {
                    $fields .= '"'.$row['name'].'" NUMBER(18,0),';
                } elseif ($row['type'] == 'decimal' || $row['type'] == 'decimal_zero' || $row['type'] == 'bigdecimal' || $row['type'] == 'percent') {
                    $fields .= '"'.$row['name'].'" NUMBER(24,6),';
                } elseif ($row['type'] == 'code') {
                    $fields .= '"'.$row['name'].'" VARCHAR2(50 CHAR),';
                } else {
                    $fields .= '"'.$row['name'].'" VARCHAR2(4000 CHAR),';
                }
                
                if ($row['type'] == 'date' || $row['type'] == 'datetime' || $row['type'] == 'long' || $row['type'] == 'code') {
                    $indexing[] = $row['name'];
                }
            }

            $createTableScript = '
            CREATE TABLE '.$tblName.' (	
                "ID" NUMBER(18,0) NOT NULL ENABLE, 
                "SRC_RECORD_ID" NUMBER(18,0), 
                ' . $fields . ' 
                "WFM_STATUS_ID" NUMBER(18,0), 
                "WFM_DESCRIPTION" VARCHAR2(4000 CHAR), 
                "CREATED_DATE" DATE, 
                "CREATED_USER_ID" NUMBER(18,0), 
                "CREATED_USER_NAME" VARCHAR2(512 CHAR), 
                "MODIFIED_DATE" DATE, 
                "MODIFIED_USER_ID" NUMBER(18,0), 
                "MODIFIED_USER_NAME" VARCHAR2(512 CHAR), 
                "DELETED_DATE" DATE, 
                "DELETED_USER_ID" NUMBER(18,0), 
                "DELETED_USER_NAME" VARCHAR2(512 CHAR), 
                "COMPANY_DEPARTMENT_ID" NUMBER(18,0), 
                "GENERATED_DATE" DATE 
            )';

            $this->db->Execute($createTableScript);
            
            if (strpos($tblName, '.') !== false) {
                $pkName = explode('.', $tblName);
                $pkName = $pkName[1];
            } else {
                $pkName = $tblName;
            }
            
            $this->db->Execute('ALTER TABLE '.$tblName.' ADD CONSTRAINT '.$pkName.'_PK PRIMARY KEY (ID) ENABLE');
            
            foreach ($indexing as $i => $colName) {
                
                try {
                
                    $indexId = getUIDAdd($i);
                    $this->db->Execute("CREATE INDEX D_IX$indexId ON $tblName ($colName)");

                } catch (Exception $ex) {}
            }
            
            $response = array('status' => 'success');
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function dbCreatedTblKpiRelationDataMart($tblName, $dbField) { 
        
        try {
            
            $indexing = array();
            $fields = '';
        
            foreach ($dbField as $row) {
                
                $lowerName = strtolower($row['name']);
                $isCorrectColumnName = (boolean) preg_match("/^[_0-9a-zA-Z]{1,30}$/i", $row['name']);
                
                if ($lowerName == 'company_department_id' || !$isCorrectColumnName) {
                    continue;
                }
                
                if ($row['type'] == 'clob') {
                    $fields .= '"'.$row['name'].'" CLOB,';
                } elseif ($row['type'] == 'date' || $row['type'] == 'datetime') {
                    $fields .= '"'.$row['name'].'" DATE,';
                } elseif ($row['type'] == 'combo' || $row['type'] == 'popup' || $row['type'] == 'radio' || $row['type'] == 'number' || $row['type'] == 'long') {
                    $fields .= '"'.$row['name'].'" NUMBER(18,0),';
                } elseif ($row['type'] == 'decimal' || $row['type'] == 'decimal_zero' || $row['type'] == 'bigdecimal' || $row['type'] == 'percent') {
                    $fields .= '"'.$row['name'].'" NUMBER(24,6),';
                } elseif ($row['type'] == 'code') {
                    $fields .= '"'.$row['name'].'" VARCHAR2(50 CHAR),';
                } else {
                    $fields .= '"'.$row['name'].'" VARCHAR2(4000 CHAR),';
                }
                
                if ($row['type'] == 'date' || $row['type'] == 'datetime' || $row['type'] == 'long' || $row['type'] == 'code') {
                    $indexing[] = $row['name'];
                }
            }

            $createTableScript = '
            CREATE TABLE '.$tblName.' (	
                "ID" NUMBER(18,0) NOT NULL ENABLE, 
                "INDICATOR_ID" NUMBER(18,0), 
                ' . $fields . ' 
                "WFM_STATUS_ID" NUMBER(18,0), 
                "WFM_DESCRIPTION" VARCHAR2(4000 CHAR), 
                "CREATED_DATE" DATE, 
                "CREATED_USER_ID" NUMBER(18,0), 
                "CREATED_USER_NAME" VARCHAR2(512 CHAR), 
                "MODIFIED_DATE" DATE, 
                "MODIFIED_USER_ID" NUMBER(18,0), 
                "MODIFIED_USER_NAME" VARCHAR2(512 CHAR), 
                "DELETED_DATE" DATE, 
                "DELETED_USER_ID" NUMBER(18,0), 
                "DELETED_USER_NAME" VARCHAR2(512 CHAR), 
                "COMPANY_DEPARTMENT_ID" NUMBER(18,0), 
                "GENERATED_DATE" DATE 
            )';

            $this->db->Execute($createTableScript);
            
            if (strpos($tblName, '.') !== false) {
                $pkName = explode('.', $tblName);
                $pkName = $pkName[1];
            } else {
                $pkName = $tblName;
            }
            
            $this->db->Execute('ALTER TABLE '.$tblName.' ADD CONSTRAINT '.$pkName.'_PK PRIMARY KEY (ID) ENABLE');
            $this->db->Execute('CREATE INDEX '.$tblName.'_IX_GD ON '.$tblName.' (GENERATED_DATE)');
            
            foreach ($indexing as $i => $colName) {
                
                try {
                
                    $indexId = getUIDAdd($i);
                    $this->db->Execute("CREATE INDEX D_IX$indexId ON $tblName ($colName)");

                } catch (Exception $ex) {}
            }
            
            
            $response = array('status' => 'success');
        
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiDashboardTargetColumnsModel($targetMapId) {
        
        $data = $this->db->GetAll("
            SELECT 
                T2.TABLE_NAME, 
                T2.QUERY_STRING, 
                T3.COLUMN_NAME, 
                T3.SHOW_TYPE 
            FROM KPI_DATAMODEL_MAP_KEY_DTL T0 
                INNER JOIN KPI_DATAMODEL_MAP_KEY T1 ON T1.ID = T0.DATAMODEL_MAP_KEY_ID 
                INNER JOIN KPI_INDICATOR T2 ON T2.ID = T1.TRG_INDICATOR_ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T3 ON T3.ID = T0.TRG_INDICATOR_MAP_ID 
            WHERE T0.SRC_INDICATOR_MAP_ID = ".$this->db->Param(0), 
            array($targetMapId)
        );
        
        return $data;
    }
    
    public function filterKpiIndicatorValueFormModel($indicatorId, $headerDatas = array()) {
        
        try {
            
            $isChartList = Input::numeric('isChartList');
            $configRow   = self::getKpiIndicatorRowModel($indicatorId);
            
            if (!$configRow) {
                throw new Exception('Invalid indicatorId!'); 
            }

            $configRow['isIgnoreStandardFields'] = true;
            $configRow['isFilter'] = true;
            
            if (!$headerDatas) {
                $headerDatas = self::getKpiIndicatorColumnsModel($indicatorId, $configRow);
            }
            
            $permissionCriteria = self::getUmPermissionKeyModel($indicatorId);

            $tableName          = $configRow['TABLE_NAME'];
            $queryString        = self::parseQueryString($configRow['QUERY_STRING']); 
            $isQueryString      = $queryString ? true : false;
            $isCheckSystemTable = $isQueryString ? true : self::isCheckSystemTable($tableName);
            $sessionUserKeyId   = Ue::sessionUserKeyId();
            $drillDownCriteria  = Input::post('drillDownCriteria');
            $ignoreColName      = Input::post('ignoreColName');
            
            $result = array();
            
            if ($isQueryString) {
                $tableName = "($queryString)";
            }
            
            $tblColumns = self::table_exists($this->db, $tableName);
            
            if ($permissionCriteria) {
                $tableName = "(SELECT * FROM $tableName WHERE $permissionCriteria)";
            }
            
            $isUseCompanyDepartmentId = $configRow['IS_USE_COMPANY_DEPARTMENT_ID'];
            
            if ($isCheckSystemTable) {
                $subCondition = '1 = 1';
            } else {
                $subCondition = 'DELETED_USER_ID IS NULL';
            }
            
            if ($isUseCompanyDepartmentId) { 
                
                $sessionValues = Session::get(SESSION_PREFIX.'sessionValues');
            
                if ($sessionCompanyDepartmentId = issetParam($sessionValues['sessioncompanydepartmentid'])) {
                    $subCondition .= ' AND COMPANY_DEPARTMENT_ID = '.$sessionCompanyDepartmentId;
                }
            }
            
            if ($isChartList == 1 || $ignoreColName) {
                
                $filterData = Input::post('filterData');
                
                if ($filterData) {
                    
                    $columnsConfig = array();
                    
                    foreach ($headerDatas as $col) {
                
                        $columnName = $col['COLUMN_NAME'];
                        $showType   = $col['SHOW_TYPE'];

                        $columnsConfig[$columnName] = array('labelName' => $col['LABEL_NAME'], 'showType' => $showType);
                    }
                    
                    foreach ($filterData as $filterColName => $filterColVals) {
                        
                        if (isset($filterColVals[0]['begin'])) {
                            
                            foreach ($filterColVals as $filterColVal) {
                            
                                $filterShowType = $columnsConfig[$filterColName]['showType'];
                                $filterColBeginVal = $filterColVal['begin'];
                                $filterColEndVal = $filterColVal['end'];

                                if ($filterShowType == 'bigdecimal' || $filterShowType == 'decimal' || $filterShowType == 'number') {

                                    $subCondition .= " AND $filterColName BETWEEN $filterColBeginVal AND $filterColEndVal";

                                } elseif ($filterShowType == 'date') {

                                    $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD');

                                } elseif ($filterShowType == 'datetime') {

                                    $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD HH24:MI:SS')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD HH24:MI:SS');
                                }
                            }
                        } else {
                            $subCondition .= " AND $filterColName IN ('".Arr::implode_r("','", $filterColVals, true)."')";
                        }
                    }
                }
            }
            
            $cache = phpFastCache();
            
            $this->db->StartTrans(); 
            $this->db->Execute(Ue::createSessionInfo());
            
            foreach ($headerDatas as $k => $headerData) {
                
                if ($headerData['IS_FILTER'] == '1') {
                    
                    $row = array();
                    
                    $runMode = issetParam($headerData['RUN_MODE']);
                    $showType = $headerData['SHOW_TYPE'];
                    $labelName = $headerData['LABEL_NAME'];
                    $trgAliasName = $headerData['TRG_ALIAS_NAME'];
                    $realColumnName = $headerData['COLUMN_NAME'];
                    
                    if ($runMode == 'dashboard') {
                        
                        $row['config'] = array(
                            'showType' => $showType, 
                            'labelName' => $labelName, 
                            'defaultValue' => Mdmetadata::setDefaultValue($headerData['DEFAULT_VALUE']), 
                            'namedParam' => false
                        ); 
                        
                        if ($showType != 'bigdecimal' && $showType != 'decimal' && $showType != 'number' && $showType != 'date' && $showType != 'datetime') {
                            
                            $targetColumns = self::getKpiDashboardTargetColumnsModel($headerData['SRC_INDICATOR_MAP_ID']);
                            
                            if ($targetColumns) {
                                
                                $unionSql = '';
                                
                                foreach ($targetColumns as $targetColumn) {
                                    
                                    $tableName          = $targetColumn['TABLE_NAME'];
                                    $queryString        = self::parseQueryString($targetColumn['QUERY_STRING']); 
                                    $isQueryString      = $queryString ? true : false;
                                    $isCheckSystemTable = $isQueryString ? true : self::isCheckSystemTable($tableName);
                                    $columnName         = $targetColumn['COLUMN_NAME'];
                                    
                                    if ($isQueryString) {
                                        $tableName = "($queryString)";
                                    }
                                    
                                    $subCondition = str_replace('DELETED_USER_ID IS NULL', '1 = 1', $subCondition);
            
                                    $unionSql .= " 
                                        SELECT 
                                            TO_CHAR($columnName) AS LABEL_NAME 
                                        FROM $tableName 
                                        WHERE $subCondition 
                                        GROUP BY $columnName 
                                        UNION ALL";
                                }
                                
                                $unionSql = rtrim($unionSql, 'UNION ALL');
                                $unionSql = "SELECT US.LABEL_NAME FROM ($unionSql) US GROUP BY US.LABEL_NAME ORDER BY US.LABEL_NAME ASC";

                                $data = $this->db->GetAll($unionSql);

                                $row['rows'] = $data;
                            }
                        }
                        
                        $result[$realColumnName] = $row;
                        
                        continue;
                    }
                    
                    if (($showType == 'combo' || $showType == 'popup' || $showType == 'radio') && isset($tblColumns[$realColumnName . '_DESC'])) {
                        $columnName = $realColumnName . '_DESC';
                    } else {
                        $columnName = $realColumnName;
                    }
                    
                    if ($ignoreColName && $ignoreColName == $columnName) {
                        continue;
                    }
                    
                    $row['config'] = array(
                        'showType'       => $showType, 
                        'labelName'      => $labelName, 
                        'reportAggrFunc' => $headerData['REPORT_AGGREGATE_FUNCTION'], 
                        'defaultValue'   => Mdmetadata::setDefaultValue($headerData['DEFAULT_VALUE']), 
                        'namedParam'     => false
                    ); 
                    
                    if ($realColumnName == '' && $trgAliasName != '') {
                        
                        $headerData['MAIN_INDICATOR_ID'] = $indicatorId;
                        $headerData['NAME'] = $headerData['LABEL_NAME'];
                        $headerData['COLUMN_NAME'] = $trgAliasName;
                        $headerData['COLUMN_NAME_PATH'] = $trgAliasName;
                        $headerData['IS_REQUIRED'] = 0;
                        $headerData['PATTERN_TEXT'] = '';
                        $headerData['drillDownCriteria'] = $drillDownCriteria;
                        
                        $row['config']['namedParam'] = true;
                        $row['config']['row'] = $headerData;
                        
                        $result[$trgAliasName] = $row;
                        
                    } elseif (isset($tblColumns[$columnName])) {
                                
                        if ($showType != 'bigdecimal' && $showType != 'decimal' && $showType != 'number' && $showType != 'date' && $showType != 'datetime') {
                            
                            $cacheName = 'mvFilterData_'.$indicatorId.'_'.md5($columnName.'_'.$sessionUserKeyId.'_'.$subCondition);
                            $data = $cache->get($cacheName);
                            
                            if ($data == null) {
                                
                                $sql = "
                                    SELECT 
                                        TMP.* 
                                    FROM (
                                        SELECT  
                                            $columnName AS LABEL_NAME, 
                                            COUNT(1) AS RECORD_COUNT 
                                        FROM $tableName 
                                        WHERE $subCondition     
                                        GROUP BY $columnName 
                                    ) TMP 
                                    ORDER BY TMP.LABEL_NAME ASC";
                                
                                /*ORDER BY 
                                        TO_NUMBER(REGEXP_SUBSTR(TMP.LABEL_NAME, '^[0-9]+')) ASC, 
                                        TO_NUMBER(REGEXP_SUBSTR(TMP.LABEL_NAME, '$[0-9]+')) ASC*/

                                $data = $this->db->GetAll($sql);
                                
                                $cache->set($cacheName, $data, Mdwebservice::$expressionCacheTime);
                            }
                            
                            $row['rows'] = $data;
                        }

                        $result[$columnName] = $row;
                    }
                }
            }
            
            $this->db->CompleteTrans();
            
            return array('status' => 'success', 'data' => $result);
        
        } catch (Exception $ex) {
            
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function chartKpiIndicatorColumnsModel($columnsData) {
        
        $categoryColumns = $valueColumns = array();
        
        foreach ($columnsData as $column) {
            
            if ($column['SEMANTIC_TYPE_NAME'] != 'Багана') {
                $categoryColumns[] = $column;
            } else {
                $valueColumns[] = $column;
            }
        }
        
        return array('categoryColumns' => $categoryColumns, 'valueColumns' => $valueColumns);
    }
    
    public function filterKpiIndicatorValueChartModel() {
        
        try {
            
            $indicatorId     = Input::numeric('indicatorId');
            $chartConfig     = Input::post('chartConfig');
            $mainType        = issetParam($chartConfig['mainType']);
            $mapsCountry     = issetParam($chartConfig['mapsChartConfig']['country']);
            $isBuild         = issetParam($chartConfig['mainType']) === 'echart' ? '1' : '0'; 
            $chartType       = $chartConfig['type'];
            $aggregate       = $chartConfig['aggregate'];
            $category        = $chartConfig['axisX'];
            $value           = $chartConfig['axisY'];
            $categoryGroup   = issetParam($chartConfig['axisXGroup']);
            $lineChartConfig = issetParam($chartConfig['lineChartConfig']);
            $axisYSortType   = issetParam($chartConfig['axisYSortType']);
            $rowNum          = issetParam($chartConfig['rowNum']);
            $categoryCol     = $category;
            
            $filterData      = Input::post('filterData');
            $valueArr        = explode(',', $value);
            
            $configRow       = self::getKpiIndicatorRowModel($indicatorId);
            $columns         = self::getKpiIndicatorColumnsModel($indicatorId, array('isIgnoreStandardFields' => true)); 
            
            $permissionCriteria = self::getUmPermissionKeyModel($indicatorId);
            
            $tableName      = $configRow['TABLE_NAME'];
            $queryString    = self::parseQueryString($configRow['QUERY_STRING']);
            $isQueryString  = $queryString ? true : false;
            
            $valueSelectCol = '';
            $orderBy        = '';
            $subCondition   = 'AND DELETED_USER_ID IS NULL';
            $columnsConfig  = $defaultValueFilterData = array();
            
            $isCategoryCombo = $isCategoryGroupCombo = false;
            $isFirstLoad = Input::numeric('isFirstLoad');
            
            if ($isQueryString) {
                $tableName = "($queryString)";
                $subCondition = '';
            } 
            
            if ($permissionCriteria) {
                $tableName = "(SELECT * FROM $tableName WHERE $permissionCriteria)";
            }

            foreach ($columns as $col) {
                
                $columnName   = $col['COLUMN_NAME'];
                $defaultValue = $col['DEFAULT_VALUE'];
                $showType     = $col['SHOW_TYPE'];
                
                if ($columnName == $category) {
                    
                    if ($showType == 'combo' || $showType == 'popup' || $showType == 'radio') {
                        $isCategoryCombo = true;
                    } elseif ($showType == 'date') {
                        $category = $this->db->SQLDate('Y-m-d', $category);
                    }
                }
                
                if ($columnName == $categoryGroup && ($showType == 'combo' || $showType == 'popup' || $showType == 'radio')) {
                    $isCategoryGroupCombo = true;
                }
                
                $columnsConfig[$columnName] = array('labelName' => $col['LABEL_NAME'], 'showType' => $showType);
                
                if ($lineChartConfig) {
                    
                    $lineChartConfigColumn = $lineChartConfig['column'];
                    $lineChartConfigAggregate = $lineChartConfig['aggregate'];
                    
                    if ($columnName == $lineChartConfigColumn && $lineChartConfigAggregate) {
                        
                        if ($col['EXPRESSION_STRING']) {
                            
                            $expressionString = $col['EXPRESSION_STRING'];
                            
                            preg_match_all('/\[(.*?)\]/', $expressionString, $expPathCatch);
                            
                            if (isset($expPathCatch[0][0])) {
                                foreach ($expPathCatch[0] as $e => $expPath) {
                                    $expressionString = str_replace($expPath, "$lineChartConfigAggregate(TO_NUMBER(REPLACE(".$expPathCatch[1][$e].", ',')))", $expressionString);
                                }
                            }
                            
                            $valueSelectCol .= "($expressionString) AS $lineChartConfigColumn,";
                            
                        } else {
                            $valueSelectCol .= "$lineChartConfigAggregate(TO_NUMBER(REPLACE($lineChartConfigColumn, ','))) AS $lineChartConfigColumn,";
                        }
                    }
                }
                
                if (($isFirstLoad || $isFirstLoad == null) && $defaultValue != '' && !$filterData && ($showType == 'bigdecimal' || $showType == 'decimal' || $showType == 'number')) {
                    
                    $defaultValue = Mdmetadata::setDefaultValue($defaultValue);
                    
                    $defaultValueFilterData[$columnName][] = array('begin' => $defaultValue, 'end' => $defaultValue);
                }
            }
            
            if ($defaultValueFilterData) {
                $filterData = $defaultValueFilterData;
            }
            
            if ($criterias = Input::post('criteria')) {
                foreach ($criterias as $criteriaColumn => $criteria) {
                    unset($filterData[$criteriaColumn]);
                    foreach ($criteria as $criteriaRow) {
                        $filterData[$criteriaColumn][] = $criteriaRow;
                    }
                }
            } 
            
            if (Input::postCheck('dashboardFilter')) {
                
                $dashboardFilter = Input::post('dashboardFilter');
                
                if ($criterias = issetParam($dashboardFilter['criteria'])) {
                
                    $dashboardFilterData = array();

                    foreach ($criterias as $criteriaColumn => $criteria) {
                        foreach ($criteria as $criteriaRow) {
                            $dashboardFilterData[$criteriaColumn][] = $criteriaRow;
                        }
                    }
                } else {
                    $dashboardFilterData = issetParamArray($dashboardFilter['filterData']);
                }
                
                if ($dashboardFilterIndicatorId = issetParam($dashboardFilter['indicatorId'])) {
                    
                    $dashboardFilterMaps = self::getKpiDashboardFilterMapModel($dashboardFilterIndicatorId, $indicatorId);
                
                    foreach ($dashboardFilterMaps as $dashboardFilterMapRow) {
                        $dashboardFilterSrcColumn = $dashboardFilterMapRow['SRC_COLUMN_NAME'];

                        if (isset($dashboardFilterData[$dashboardFilterSrcColumn])) {

                            $filterColVals = $dashboardFilterData[$dashboardFilterSrcColumn];
                            $dashboardFilterTrgColumn = $dashboardFilterMapRow['TRG_COLUMN_NAME'];

                            if (isset($filterColVals[0]['begin'])) {

                                $dashboardFilterTrgShowType = $dashboardFilterMapRow['TRG_SHOW_TYPE'];

                                foreach ($filterColVals as $filterColVal) {

                                    $filterColBeginVal = $filterColVal['begin'];
                                    $filterColEndVal = $filterColVal['end'];

                                    if ($dashboardFilterTrgShowType == 'bigdecimal' || $dashboardFilterTrgShowType == 'decimal' || $dashboardFilterTrgShowType == 'number') {

                                        $subCondition .= " AND $dashboardFilterTrgColumn BETWEEN $filterColBeginVal AND $filterColEndVal";

                                    } elseif ($dashboardFilterTrgShowType == 'date') {

                                        $subCondition .= " AND $dashboardFilterTrgColumn BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD');

                                    } elseif ($dashboardFilterTrgShowType == 'datetime') {

                                        $subCondition .= " AND $dashboardFilterTrgColumn BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD HH24:MI:SS')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD HH24:MI:SS');
                                    }
                                }

                            } elseif (isset($filterColVals[0]['operator'])) {
                                
                                $orCriteria = '';
                                
                                foreach ($filterColVals as $filterColVal) {
                                    $orCriteria .= "LOWER($dashboardFilterTrgColumn) ".$filterColVal['operator']." '".Str::lower(self::fixFilterColValue($filterColVal['operand']))."' OR ";
                                }

                                $orCriteria = rtrim(trim($orCriteria), ' OR');
                                $subCondition .= " AND ($orCriteria)";

                            } else {

                                $subCondition .= ' AND ( ';

                                foreach ($filterColVals as $filterColVal) {
                                    if ($filterColVal == 'isnull') {
                                        $subCondition .= " $dashboardFilterTrgColumn = '' OR $dashboardFilterTrgColumn IS NULL OR";
                                    } else {
                                        $filterColVal = self::fixFilterColValue($filterColVal);
                                        $subCondition .= " $dashboardFilterTrgColumn = '$filterColVal' OR";
                                    }
                                }

                                $subCondition = rtrim($subCondition, 'OR');

                                $subCondition .= ' ) ';
                            } 

                            unset($filterData[$dashboardFilterTrgColumn]);
                        }
                    }
                }
            }
            
            if ($filterData) {
                
                foreach ($filterData as $filterColName => $filterColVals) {
                    
                    if (isset($filterColVals[0]['begin'])) {
                        
                        foreach ($filterColVals as $filterColVal) {
                            
                            $filterShowType = $columnsConfig[$filterColName]['showType'];
                            $filterColBeginVal = $filterColVal['begin'];
                            $filterColEndVal = $filterColVal['end'];
                            
                            if ($filterShowType == 'bigdecimal' || $filterShowType == 'decimal' || $filterShowType == 'number') {
                                
                                $subCondition .= " AND $filterColName BETWEEN $filterColBeginVal AND $filterColEndVal";
                                
                            } elseif ($filterShowType == 'date') {
                                
                                $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD');
                                
                            } elseif ($filterShowType == 'datetime') {
                                
                                $subCondition .= " AND $filterColName BETWEEN ".$this->db->ToDate("'$filterColBeginVal'", 'YYYY-MM-DD HH24:MI:SS')." AND ".$this->db->ToDate("'$filterColEndVal'", 'YYYY-MM-DD HH24:MI:SS');
                            }
                        }
                        
                    } elseif (isset($filterColVals[0]['operator'])) { 
                        
                        $orCriteria = '';
                        
                        foreach ($filterColVals as $filterColVal) {
                            $orCriteria .= "LOWER($filterColName) ".$filterColVal['operator']." '".Str::lower(self::fixFilterColValue($filterColVal['operand']))."' OR ";
                        }
                        
                        $orCriteria = rtrim(trim($orCriteria), ' OR');
                        $subCondition .= " AND ($orCriteria)";
                        
                    } else {
                        
                        $subCondition .= ' AND ( ';

                        foreach ($filterColVals as $filterColVal) {
                            if ($filterColVal == 'isnull') {
                                $subCondition .= " $filterColName = '' OR $filterColName IS NULL OR";
                            } else {
                                $filterColVal = self::fixFilterColValue($filterColVal);
                                $subCondition .= " $filterColName = '$filterColVal' OR";
                            }
                        }

                        $subCondition = rtrim($subCondition, 'OR');

                        $subCondition .= ' ) ';
                    }
                }
            }
            
            foreach ($valueArr as $valueCol) {
                
                $aliasName = (($isBuild == '1') ? 'value' : $valueCol);
                
                if ($aggregate == 'COUNT' && $valueCol == '') {
                    
                    $countColumn = (($isBuild == '1') ? 'value' : 'COUNT_COL');
                    $valueSelectCol .= "$aggregate(1) AS $countColumn,";
                    
                    if ($axisYSortType && $chartType != 'clustered_column') {
                        $orderBy .= "COUNT(1) $axisYSortType, ";
                    }
                    
                } else {
                    
                    $valueShowType = isset($columnsConfig[$valueCol]) ? $columnsConfig[$valueCol]['showType'] : '';
                    
                    if ($valueShowType == 'text' || $valueShowType == 'description' || $valueShowType == 'description_auto') {
                        
                        $valueSelectCol .= "$aggregate($valueCol) AS $aliasName,";
                        
                        if ($axisYSortType && $chartType != 'clustered_column') {
                            $orderBy .= "$aggregate($valueCol) $axisYSortType, ";
                        }
                    
                    } else {
                        
                        $valueSelectCol .= "$aggregate(TO_NUMBER(REPLACE($valueCol, ','))) AS $aliasName,";
                        
                        if ($axisYSortType && $chartType != 'clustered_column') {
                            
                            $orderBy .= $this->db->IfNull("TO_NUMBER(REPLACE($valueCol, ','))", '0') . " $axisYSortType, ";
                        }
                    }
                }
            }
            
            $valueSelectCol = rtrim($valueSelectCol, ',');
            $valueAliasCol = ($isBuild == '1') ? 'NAME' : $categoryCol;
            
            if ($isCategoryCombo) {
                
                if ($isCategoryGroupCombo) {
                    
                    $sql = "
                        SELECT 
                            ".$category."_DESC AS $category, 
                            ".$categoryGroup."_DESC AS $categoryGroup,      
                            $valueSelectCol 
                        FROM $tableName 
                        WHERE ".$category."_DESC IS NOT NULL 
                            AND ".$categoryGroup."_DESC IS NOT NULL 
                            $subCondition 
                        GROUP BY 
                            $category, 
                            ".$category."_DESC, 
                            $categoryGroup, 
                            ".$categoryGroup."_DESC";
                    
                } else {
                    
                    $sql = "
                        SELECT 
                            ".$category."_DESC AS $category, 
                            $valueSelectCol 
                        FROM $tableName 
                        WHERE ".$category."_DESC IS NOT NULL 
                            $subCondition 
                        GROUP BY $category, ".$category."_DESC";
                }
                
            } else {
                
                if ($chartType == 'card' || $chartType == 'card_vertical') {
                    
                    $sql = "
                        SELECT  
                            $valueSelectCol 
                        FROM $tableName 
                        WHERE 1 = 1  
                            $subCondition";
                    
                } elseif ($chartType === 'tree' || $chartType === 'tree_circle') {
                    $groupRows = Arr::groupByArrayOnlyRows($columns, 'CODE');
                    $valueSelectCol = $categoryCol . ' AS NAME, ' . $categoryCol . ' AS VALUE' ;

                    foreach ($columns as $k => $r) {
                        $valueSelectCol .= ', ' . $r['COLUMN_NAME']  . ' AS "' . $r['LABEL_NAME'] . '"';
                    }
                    $idSelectCol = issetParam($groupRows['ID']['0']['COLUMN_NAME']) !== '' ? $groupRows['ID']['0']['COLUMN_NAME'] . ' AS ID, ' : '';
                    $parentIdSelectCol = issetParam($groupRows['PARENT_ID']['0']['COLUMN_NAME']) !== '' ? $groupRows['PARENT_ID']['0']['COLUMN_NAME'] . ' AS PARENTID, ' : '';
                    if ($idSelectCol !== '' && $parentIdSelectCol !== '') {
                        $sql = "
                            SELECT 
                                $idSelectCol
                                $parentIdSelectCol
                                $valueSelectCol
                            FROM $tableName 
                            WHERE $categoryCol IS NOT NULL 
                                $subCondition ";
                    }
                    
                } elseif ($categoryGroup) {
                    
                    if ($isCategoryGroupCombo) {
                        
                        $sql = "
                            SELECT 
                                $category AS $valueAliasCol, 
                                ".$categoryGroup."_DESC AS $categoryGroup,     
                                $valueSelectCol 
                            FROM $tableName 
                            WHERE $categoryCol IS NOT NULL 
                                AND ".$categoryGroup."_DESC IS NOT NULL 
                                $subCondition 
                            GROUP BY $categoryCol, $categoryGroup, ".$categoryGroup."_DESC";
                        
                    } else {
                        
                        $sql = "
                            SELECT 
                                $category AS $valueAliasCol,  
                                $categoryGroup, 
                                $valueSelectCol 
                            FROM $tableName 
                            WHERE $categoryCol IS NOT NULL 
                                AND $categoryGroup IS NOT NULL     
                                $subCondition 
                            GROUP BY $categoryCol, $categoryGroup";
                    }
                    
                } else {
                    
                    $sql = "
                        SELECT 
                            $category AS $valueAliasCol, 
                            $valueSelectCol
                        FROM $tableName 
                        WHERE $categoryCol IS NOT NULL 
                            $subCondition 
                        GROUP BY $categoryCol";
                }
            } 
            
            if ($orderBy) {
                $orderBy = rtrim(trim($orderBy), ',');
                $sql .= ' ORDER BY ' . $orderBy;
            }
            
            if ($rowNum) {
                $sql .= "[rownum=$rowNum]";
            }
            
            $cache = phpFastCache();
            
            $cacheName = 'mvData_'.$indicatorId.'_'.md5($sql);
            $data = null; $cache->get($cacheName);

            $cacheXaxisName = 'mvXaxisData_'.$indicatorId.'_'.md5($sql);
            $xAxis = $cache->get($cacheXaxisName);
            $labelText = '';
            
            if ($mapsCountry !== 'earth' && $data == null) {
                if ($rowNum) {
                    
                    $sql = str_replace("[rownum=$rowNum]", '', $sql);
                    
                    $rs = $this->db->SelectLimit($sql, $rowNum, 0);

                    if (isset($rs->_array)) {
                        $data = $rs->_array;
                    } 

                } else {
                    $data = $this->db->GetAll($sql);
                }
                
                if ($chartType == 'card' || $chartType == 'card_vertical') {

                    $data = $data;

                } elseif ($axisYSortType && $chartType != 'clustered_column') {

                    $axisYSortTypeLower = strtolower($axisYSortType);

                    if (isset($countColumn)) {
                        $data = Arr::sortBy($countColumn, $data, $axisYSortTypeLower);
                    } else {
                        $data = Arr::sortBy($value, $data, $axisYSortTypeLower);
                    }

                } elseif ($chartType === 'tree' || $chartType === 'tree_circle') {
                    $data = Arr::buildTree($data, 0, 'ID', 'PARENTID');
                } else {
                    $data = Arr::sortBy($valueAliasCol, $data, 'asc');
                }

                if ($isBuild === '1' &&  $chartType !== 'card' && $chartType != 'card_vertical') {
                    $xAxis = array();
                    $data = Arr::changeKeyLower($data);
                    $lowerValueAliasCol = Str::lower($valueAliasCol);
                    foreach ($data as $key => $row) {
                        $value = ($row[$lowerValueAliasCol]) ? $row[$lowerValueAliasCol] : '';
                        array_push($xAxis, $value);
                    }
                }
                
                $cache->set($cacheName, $data, Mdwebservice::$expressionCacheTime);
                $cache->set($cacheXaxisName, $xAxis, Mdwebservice::$expressionCacheTime);
            }
            
            if ($mapsCountry === 'earth') {
                $sql = " SELECT 
                            C6 AS NAME,
                            C7 as CITY,
                            C8 as COUNTRY, 
                            C9 as LONGITUDE,
                            C10 as LATITUDE,
                            c3,
                            c4,
                            c11,
                            c12,
                            c13,
                            c14,
                            c15
                        FROM VT_DATA.V_16892575946699 
                        WHERE C6 IS NOT NULL 
                            AND DELETED_USER_ID IS NULL ";
                
                $resultData = $this->db->GetAll($sql);
                (Array) $airlines = $airports = $routes = array();

                foreach ($resultData as $key => $row) {
                    $tmp = array($row['NAME'], $row['COUNTRY']);
                    if (!in_array($tmp, $airlines)) {
                        array_push($airlines, $tmp);
                    }

                    $tmp = array($row['NAME'], $row['CITY'], $row['COUNTRY'], (float) $row['LONGITUDE'], (float) $row['LATITUDE']);
                    if (!in_array($tmp, $airports)) {
                        array_push($airports, $tmp);
                    }
                }

                for ($i = 0; $i < sizeOf($resultData); $i++) {
                    for ($j = $i; $j < sizeOf($resultData); $j++) {
                        if ($i != $j) {
                            $tmp = array($i, $j, (float) random_int(0, 8));
                            array_push($routes, $tmp);
                        }
                    }
                }

                $data = array(
                    'airportsFields' => array('name', 'city', 'country', 'longitude', 'latitude'),
                    'airlineFields' => array('name', 'country'),
                    'airports' => $airports,
                    'airlines' => $airlines,
                    'routes' => $routes,
                );
            }

            if ($chartType == 'card' || $chartType == 'card_vertical') {
                
                $labelText = issetParam($chartConfig['labelText']);
                    
                $labelText = str_ireplace('[sysweekstartdate]', Mdmetadata::setDefaultValue('sysweekstartdate'), $labelText);
                $labelText = str_ireplace('[sysweekenddate]', Mdmetadata::setDefaultValue('sysweekenddate'), $labelText);

                $columnsConfig['labelText'] = $labelText;
            }

            $response = array('status' => 'success', 'data' => $data,'dataXaxis' => $xAxis, 'columnsConfig' => $columnsConfig);
            
        } catch(Exception $ex) {
            
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function saveKpiDataMartRelationConfigModel() {

        $jsonBody = file_get_contents('php://input');
        $param = json_decode($jsonBody, true);

        if ($param) {
            $id = $param['id'];
            $connections = $param['connections'];
            $objects = $param['objects'];            
            $position = $param['graphJson'];
            $columns = $param['columns'];
            $criterias = array();
        } else {
            $id = Input::numeric('id');
            $connections = Input::post('connections');
            $objects = Input::post('objects');            
            $position = Input::post('graphJson');
            $columns = Input::post('columns');
            $criterias = Input::post('criterias');
        }        
        
        if (is_numeric($id)) {
            
            try {
                
                $param = array(
                    'id'         => $id, 
                    'graphJson'  => $position, 
                    'typeCode'   => 'relation',
                    'KPI_DATAMODEL_MAP'      => $objects, 
                    'KPI_DATAMODEL_MAP_KEY'  => $connections,
                    'KPI_DATAMODEL_CRITERIA' => $criterias
                );
                
                $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'dataModelKpiIndicator_002', $param);
                
                if ($result['status'] == 'success') {
                    
                    $response = array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));                                        
                
                    $param = array(
                        'id'                  => $id, 
                        'data_IndicatorMapDV' => $columns
                    );

                    $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'data_IndicatorMapDV_HDR_002', $param);
                    
                    if ($result['status'] == 'success') {
                        $this->relationConfigToUnionSql($id);
                    }
                    
                } else {
                    $response = array('status' => 'error', 'message' => $this->ws->getResponseMessage($result));
                }
                
            } catch (Exception $ex) {
                $response = array('status' => 'error', 'message' => $ex->getMessage());
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        return $response;
    }
    
    public function relationConfigToUnionSql($id) {
        
        try {
            $row = $this->getKpiIndicatorRowModel($id);
            
            if ($row['KPI_TYPE_ID'] == '2019') {
                
                $relationConfig = $this->db->GetAll("
                    SELECT 
                        MK.TRG_INDICATOR_ID, 
                        SRC_MAP.COLUMN_NAME AS SRC_COLUMN_NAME, 
                        SRC_MAP.SHOW_TYPE AS SRC_SHOW_TYPE, 
                        LOWER(SRC_MAP.TRG_ALIAS_NAME) AS SRC_ALIAS_NAME, 
                        TRG_MAP.COLUMN_NAME AS TRG_COLUMN_NAME, 
                        TRG_MAP.SHOW_TYPE AS TRG_SHOW_TYPE, 
                        LOWER(TRG_MAP.TRG_ALIAS_NAME) AS TRG_ALIAS_NAME, 
                        TRG.TABLE_NAME AS TRG_TABLE_NAME, 
                        TRG.QUERY_STRING AS TRG_QUERY_STRING
                    FROM KPI_DATAMODEL_MAP_KEY MK  
                        INNER JOIN KPI_INDICATOR SRC ON SRC.ID = MK.SRC_INDICATOR_ID 
                        INNER JOIN KPI_INDICATOR_INDICATOR_MAP SRC_MAP ON SRC_MAP.MAIN_INDICATOR_ID = SRC.ID  
                        INNER JOIN KPI_INDICATOR TRG ON TRG.ID = MK.TRG_INDICATOR_ID 
                        INNER JOIN KPI_INDICATOR_INDICATOR_MAP TRG_MAP ON TRG_MAP.MAIN_INDICATOR_ID = TRG.ID 
                            AND LOWER(TRG_MAP.TRG_ALIAS_NAME) = LOWER(SRC_MAP.TRG_ALIAS_NAME) 
                    WHERE MK.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                        AND SRC_MAP.COLUMN_NAME IS NOT NULL 
                        AND TRG_MAP.COLUMN_NAME IS NOT NULL 
                        AND SRC_MAP.TRG_ALIAS_NAME IS NOT NULL 
                        AND TRG_MAP.TRG_ALIAS_NAME IS NOT NULL 
                        AND SRC_MAP.SHOW_TYPE NOT IN ('row', 'rows', 'label', 'config') 
                        AND SRC_MAP.PARENT_ID IS NULL
                    ORDER BY SRC_MAP.ORDER_NUMBER ASC", array($id));
                
                if ($relationConfig) {
                    $trgSql = array();
                    
                    foreach ($relationConfig as $relation) {
                        
                        $src_column_name = $relation['SRC_COLUMN_NAME'];
                        $trg_column_name = $relation['TRG_COLUMN_NAME'];
                        
                        if ($src_column_name && $trg_column_name) {
                            
                            $trg_indicator_id = $relation['TRG_INDICATOR_ID'];
                            $src_show_type = $relation['SRC_SHOW_TYPE'];
                            $trg_show_type = $relation['TRG_SHOW_TYPE'];
                            $src_alias_name = $relation['SRC_ALIAS_NAME'];
                            $trg_alias_name = $relation['TRG_ALIAS_NAME'];
                            
                            $field = array(
                                'src_show_type' => $src_show_type, 
                                'trg_show_type' => $trg_show_type, 
                                'src_column_name' => $src_column_name, 
                                'trg_column_name' => $trg_column_name, 
                                'src_alias_name' => $src_alias_name, 
                                'trg_alias_name' => $trg_alias_name
                            );
                            
                            if (!isset($trgSql[$trg_indicator_id])) {
                                $trgSql[$trg_indicator_id] = array(
                                    'tableName' => $relation['TRG_TABLE_NAME'], 
                                    'queryString' => $relation['TRG_QUERY_STRING'], 
                                    'fields' => array($field)
                                );
                            } else {
                                $trgSql[$trg_indicator_id]['fields'][] = $field;
                            }
                        }
                    }
                    
                    if ($trgSql) {
                        $unionSql = '';
                        $schemaName = Config::getFromCache('kpiDbSchemaName');
                        
                        foreach ($trgSql as $trgRow) {
                            $tableName = $trgRow['tableName'];
                            
                            if (!$tableName) {
                                continue;
                            }
                            
                            if ($schemaName) {
                                $tableName = str_ireplace($schemaName.'.', '[kpiDbSchemaName]', $tableName);
                            } else {
                                $tableName = '[kpiDbSchemaName]'.$tableName;
                            }
        
                            $fields = $trgRow['fields'];
                            
                            $unionSqlField = '';
                            $unionSql .= 'SELECT ';
                            
                            foreach ($fields as $field) {
                                if ($field['trg_show_type'] == 'combo') {
                                    $unionSqlField .= $field['trg_column_name'] . '_DESC AS '.$field['src_column_name'].', ';
                                } else {
                                    $unionSqlField .= $field['trg_column_name'] . ' AS '.$field['src_column_name'].', ';
                                }
                            }
                            
                            $unionSqlField = rtrim(trim($unionSqlField), ',');
                            $unionSql .= $unionSqlField . ' ';
                            
                            $unionSql .= 'FROM ' . $tableName . ' ';
                            $unionSql .= 'WHERE DELETED_USER_ID IS NULL';
                            
                            $unionSql .= ' UNION ';
                        }
                        
                        $unionSql = rtrim(trim($unionSql), 'UNION');
                    }
                    
                    $updateSql = 'SELECT * FROM ('.trim($unionSql).')';
                    
                    $result = $this->db->UpdateClob('KPI_INDICATOR', 'QUERY_STRING', $updateSql, 'ID = '.$id);
                    $response = array('status' => 'success');
                    
                } else {
                    throw new Exception('Relation тохиргоо олдсонгүй!'); 
                }
                
            } else {
                throw new Exception('Invalid UNION Type!'); 
            }
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function createKpiDmChartModel() {
        
        try {
            
            $chartLineColumn = Input::post('chartLineColumn');
            $chartLineAggregate = Input::post('chartLineAggregate');
            $postData = Input::postData();
            $jsonConfig = array(
                'chartConfig' => array(
                    'type' => Input::post('chartType'), 
                    'mainType' => Input::post('chartMainType'), 
                    'axisX' => Input::post('chartCategory'), 
                    'axisXGroup' => Input::post('chartCategoryGroup'), 
                    'axisY' => Input::post('chartValue'), 
                    'aggregate' => Input::post('chartAggregate'), 
                    'axisYSortType' => Input::post('chartValueSortType'), 
                    'rowNum' => Input::post('chartRowNum'), 
                    'labelText' => Input::post('chartLabelText'), 
                    'bgColor' => Input::post('chartBgColor'), 
                    'iconName' => Input::post('chartIconName')
                )
            );

            /* if (issetParam($postData['buildCharConfig']) !== '') {
                $buildCharConfig = json_decode($postData['buildCharConfig'], true);
                $buildCharConfig['isBuild'] = '1';
                $buildCharConfig['cartType'] = 'echarts';

                $jsonConfig['chartConfig'] = array_merge($jsonConfig['chartConfig'], $buildCharConfig);
            } */

            if (Config::getFromCache('useEchartsBuilder')) {
                foreach($postData as $key => $row) {
                    if ($row) {
                        $jsonConfig['chartConfig'][$key] = $row;
                    }
                }
            }

            if ($chartLineColumn != '' && $chartLineAggregate != '') {
                $jsonConfig['chartConfig']['lineChartConfig'] = array('column' => $chartLineColumn, 'aggregate' => $chartLineAggregate);
            }

            if ($jsonConfig['chartConfig']['type'] == 'maps') {
                $jsonConfig['chartConfig']['mapsChartConfig'] = array('country' => Input::post('chartMapCountry'));
            }

            if ($chartFilterCriteria = Input::post('chartFilterCriteria')) {
                $jsonConfig['chartFilterCriteria'] = $chartFilterCriteria;
            }

            $param = array(
                'name'            => Input::post('chartTitle'), 
                'mainIndicatorId' => Input::numeric('indicatorId'), 
                'jsonConfig'      => json_encode($jsonConfig, JSON_UNESCAPED_UNICODE)
            );

            if ($chartIndicatorId = Input::numeric('chartIndicatorId')) {

                $this->db->AutoExecute('KPI_INDICATOR', array('NAME' => $param['name']), 'UPDATE', 'ID = '.$chartIndicatorId);
                $this->db->UpdateClob('KPI_INDICATOR', 'GRAPH_JSON', $param['jsonConfig'], 'ID = '.$chartIndicatorId);
                
                $result['status'] = 'success';

            } else {
                $result = $this->ws->runArrayResponse(self::$gfServiceAddress, 'data_IndicatorDV_Chart_001', $param);
            }

            if ($result['status'] == 'success') {

                Mdform::clearCacheData($param['mainIndicatorId']);

                $response = array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));
            } else {
                $response = array('status' => 'error', 'message' => $this->ws->getResponseMessage($result));
            }
        
        } catch (ADODB_Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiIndicatorChildChartsModel($indicatorId) {
        
        try {
            
            $langCode = Lang::getCode();
        
            $data = $this->db->GetAll("
                SELECT 
                    KI.ID, 
                    FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS NAME,  
                    KI.GRAPH_JSON 
                FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                    INNER JOIN KPI_INDICATOR KI ON KI.ID = KIIM.TRG_INDICATOR_ID 
                WHERE KIIM.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND KIIM.SEMANTIC_TYPE_ID = 10000006 
                    AND KI.GRAPH_JSON IS NOT NULL 
                    AND KI.DELETED_USER_ID IS NULL", 
                array($indicatorId)
            );
            
            $response = array('status' => 'success', 'data' => $data);
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getKpiIndicatorChartRowModel($indicatorId) {
        
        $langCode = Lang::getCode();
        
        $row = $this->db->GetRow("
            SELECT 
                KI.ID,
                FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS NAME, 
                KI.GRAPH_JSON,
                KIIM.SRC_INDICATOR_ID, 
                KT.RELATED_INDICATOR_ID 
            FROM KPI_INDICATOR KI
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP KIIM ON KIIM.TRG_INDICATOR_ID = KI.ID
                    AND KIIM.SEMANTIC_TYPE_ID = 10000006 
                LEFT JOIN KPI_TYPE KT ON KT.ID = KI.KPI_TYPE_ID     
            WHERE KI.ID = ".$this->db->Param(0), 
            array($indicatorId)
        );
        
        return $row;
    }
    
    public function getKpiIndicatorKnowledgeRowModel($indicatorId) {
        $knowledge = $this->db->GetOne("SELECT KNOWLEDGE FROM KPI_INDICATOR WHERE ID = ".$this->db->Param(0), array($indicatorId));
        return $knowledge;
    }
    
    public function excelImportKpiIndicatorValueModel() {
        
        if (!empty($_FILES['excelFile']['name'])) { 
            
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            
            $headerSheetName = 'Sheet1';
            $headerSheetNameLower = Str::lower($headerSheetName);
            
            $fileName = $_FILES['excelFile']['name'];
            $tmpName = $_FILES['excelFile']['tmp_name'];
            
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (!in_array($extension, array('xls', 'xlsx'))) {
                return array('status' => 'error', 'message' => "The extension '$extension' is invalid!");
            }
            
            if (!FileUpload::checkContentType($fileName, $tmpName)) {
                return array('status' => 'error', 'message' => 'ContentType is invalid!');
            }
            
            if ($extension == 'xlsx') {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLSX');
                
                $xlsx = SimpleXLSX::parse($tmpName);
                $sheetNames = $xlsx->sheetNames();
                
            } else {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLS');
                
                $xlsx = SimpleXLS::parse($tmpName);
                $sheetNames = $xlsx->sheets;
            }
            
            foreach ($sheetNames as $sheetKey => $sheetName) {
                
                $sheetNameLower = Str::lower($sheetName);
                
                if ($sheetNameLower == $headerSheetNameLower) {
                    $sheetIndex = $sheetKey;
                    break;
                }
            }
            
            if (isset($sheetIndex)) {
                
                $rows = $xlsx->rows($sheetIndex);
                
                $columnRow = $rows[0];
                
                unset($rows[0]);
                unset($rows[1]);
                
                if (is_countable($rows) && count($rows)) {
                    
                    try {
                        
                        $indicatorId = Input::numeric('indicatorId');

                        $configRow = self::getKpiIndicatorRowModel($indicatorId);
                        $configRow['isIgnoreStandardFields'] = true;
                        unset($configRow['NAME_PATTERN']);

                        $columnsData = self::getKpiIndicatorColumnsModel($indicatorId, $configRow); 

                        $fieldConfig = self::getKpiIndicatorIdFieldModel($indicatorId, $columnsData);
                        $idField = $fieldConfig['idField'];
                        $kpiDataTblName = $configRow['TABLE_NAME'];
                        $kpiTblIdField  = $idField ? $idField : 'ID';

                        $schemaName = self::getKpiDbSchemaName($indicatorId);
                        $tblName = $kpiDataTblName ? $kpiDataTblName : $schemaName . 'V_'.$indicatorId;

                        $sessionUserKeyId = Ue::sessionUserKeyId();
                        $sessionValues = Session::get(SESSION_PREFIX . 'sessionValues');
                        $sessionName   = issetDefaultVal($sessionValues['sessionusername'], Ue::getSessionPersonWithLastName());

                        $columnConfig = $comboDatas = $expressionColumns = array();
                        $evalRow = '';
                        $n = 3;

                        foreach ($columnsData as $columnsRow) {

                            $columnName = $columnsRow['COLUMN_NAME'];

                            if ($columnName) {

                                $columnConfig[$columnName] = $columnsRow;

                                if ($columnsRow['EXPRESSION_STRING']) {
                                    $expressionColumns[$columnName] = $columnsRow;
                                }
                            }
                        }

                        if ($expressionColumns) { 
                            $evalExp = self::expressionServerSideParse($expressionColumns, $columnConfig);
                        }

                        $columnRow = array_map('strtoupper', $columnRow);

                        unset($_POST);

                        $_POST['kpiMainIndicatorId'] = $indicatorId;
                        $_POST['kpiDataTblName'] = $configRow['TABLE_NAME'];
                        $_POST['kpiTblIdField'] = $idField;
                        $_POST['isExcelImport'] = 1;

                        $this->db->BeginTrans(); 

                        foreach ($rows as $row) {
                            if (empty($row[0])) continue;                       
                                 
                            $translationValue = [];

                            if ($n == 3) {

                                unset($_POST['kpiTbl']);

                                foreach ($columnRow as $key => $colName) {

                                    if ($colName == '') {
                                        continue;
                                    }

                                    $tspliter = explode("_:", $colName);
                                    if (count($tspliter) === 2) {
                                        if ($row[$key]) {
                                            $translationValue[$tspliter[0]][Str::lower($tspliter[1])] = $row[$key];
                                        }
                                        continue;
                                    }

                                    $cellVal = Input::param($row[$key]);
                                    $colConfig = $columnConfig[$colName]; 
                                    $showType = $colConfig['SHOW_TYPE']; 

                                    if ($showType == 'decimal' || $showType == 'bigdecimal') {

                                        $cellVal = Number::decimal($cellVal);
                                        $evalRow .= '$insertData[\''.$colName.'\'] = self::cleanDecimal($row['.$key.']); ';

                                    } elseif ($showType == 'date') {

                                        $cellVal = Date::formatter($cellVal, 'Y-m-d');
                                        $evalRow .= '$insertData[\''.$colName.'\'] = Date::formatter($row['.$key.'], \'Y-m-d\'); ';

                                    } elseif ($showType == 'combo' || $showType == 'popup' || $showType == 'radio') {

                                        $lookupMetaDataId = $colConfig['TRG_TABLE_NAME'] ? $colConfig['TRG_TABLE_NAME'] : $colConfig['LOOKUP_META_DATA_ID'];
                                        
                                        $_POST['kpiTbl'][$colName.'_DESC'] = '';
                                        
                                        if ($colConfig['META_LOOKUP_ID']) {

                                            $lookupMetaDataId = $colConfig['META_LOOKUP_ID'];
                                            $datas = self::getComboKpiModel($lookupMetaDataId, '');

                                            $comboRows = array();
                                            $dataRows = $datas['data'];
                                            $dataId = $datas['id'];
                                            $dataName = $datas['name'];

                                            foreach ($dataRows as $dataRow) {
                                                $comboRows[Str::lower($dataRow[$dataName])] = array(
                                                    'id' => $dataRow[$dataId], 
                                                    'name' => $dataRow[$dataName]
                                                );
                                            }

                                            $comboDatas[$lookupMetaDataId] = $comboRows;

                                        } elseif (!isset($comboDatas[$lookupMetaDataId])) { 

                                            $colConfig['isData'] = true;
                                            $colConfig['FILTER_INDICATOR_ID'] = $colConfig['LOOKUP_META_DATA_ID'];

                                            $datas = self::getKpiComboDataModel($colConfig);

                                            $comboRows = array();
                                            $dataRows = $datas['data'];
                                            $dataId = $datas['id'];
                                            $dataName = $datas['name'];

                                            if (isset($datas['data-name'])) {
                                                $dataName = $datas['data-name'];
                                            }

                                            foreach ($dataRows as $dataRow) {
                                                $comboRows[Str::lower($dataRow[$dataName])] = array(
                                                    'id' => $dataRow[$dataId], 
                                                    'name' => $dataRow[$dataName]
                                                );
                                            }

                                            $comboDatas[$lookupMetaDataId] = $comboRows;
                                        }

                                        $cellValLower = Str::lower($cellVal);

                                        if (isset($comboDatas[$lookupMetaDataId][$cellValLower])) {

                                            $comboRow = $comboDatas[$lookupMetaDataId][$cellValLower];

                                            $_POST['kpiTbl'][$colName.'_DESC'] = $comboRow['name'];

                                            $cellVal = $comboRow['id'];

                                        } else {

                                            $this->db->RollbackTrans();
                                            return array('status' => 'error', 'message' => "$n мөрний $colName баганын $cellVal утга олдсонгүй!");
                                        }
                                        
                                        $evalRow .= 'if ($row['.$key.'] != \'\') { ';
                                            
                                            $evalRow .= 'if ($getLookupRow = issetParam($comboDatas[\''.$lookupMetaDataId.'\'][Str::lower(self::cleanVal($row['.$key.']))])) { ';
                                            
                                                $evalRow .= '$insertData[\''.$colName.'\'] = $getLookupRow[\'id\']; ';
                                                $evalRow .= '$insertData[\''.$colName.'_DESC\'] = $getLookupRow[\'name\']; ';
                                            
                                            $evalRow .= '} else { ';
                                            
                                                $evalRow .= 'throw new Exception("$n мөрний '.$colName.' баганын ".$row['.$key.']." утга олдсонгүй!"); '; 
                                                
                                            $evalRow .= '} ';
                                        
                                        $evalRow .= '} ';

                                    } else {
                                        $evalRow .= '$insertData[\''.$colName.'\'] = self::cleanVal($row['.$key.']); ';
                                    }

                                    $_POST['kpiTbl'][$colName] = $cellVal;
                                } 

                                if ($translationValue) {
                                    foreach ($translationValue as $tkey => $tval) {
                                        $evalRow .= '$insertData[\'TRANSLATION_VALUE\'] = \'{"value":{'.json_encode($translationValue).'}}\'; ';
                                        $_POST['kpiTbl'][$tkey.'_translation'] = json_encode($tval);
                                    }
                                }

                                if (isset($evalExp)) {
                                    eval($evalExp);
                                    $evalRow .= str_replace('$_POST[\'kpiTbl\']', '$insertData', $evalExp);
                                }

                                self::$uniqIdIndex = $n;

                                $result = self::saveKpiDynamicDataModel();

                                if ($result['status'] != 'success') {
                                    $this->db->RollbackTrans();
                                    return array('status' => 'error', 'message' => $result['message']);
                                } 

                            } else {

                                $insertData = array(
                                    // 'INDICATOR_ID'      => $indicatorId,
                                    'CREATED_DATE'      => Date::currentDate('Y-m-d H:i:s'), 
                                    'CREATED_USER_ID'   => $sessionUserKeyId, 
                                    // 'CREATED_USER_NAME' => $sessionName
                                );

                                eval($evalRow);
                                $insertData[$kpiTblIdField] = getUIDAdd($n);
                                
                                $insertColumns = $insertValues = '';
                                
                                foreach ($insertData as $insertCol => $insertVal) {
                                    
                                    if ($insertVal != '') {
                                        $insertColumns .= "$insertCol,";
                                        $insertValues .= "'$insertVal',";
                                    }
                                }
                                
                                self::dbExecuteMetaVerseData("INSERT INTO $tblName (".rtrim($insertColumns, ',').") VALUES (".rtrim($insertValues, ',').")");
                            }

                            $n ++;
                        }

                        $this->db->CommitTrans();
                        
                        Mdform::clearCacheData($indicatorId);
                        $response = array('status' => 'success', 'message' => 'Амжилттай импорт хийгдлээ.');
                    
                    } catch (Exception $ex) {
                        
                        $this->db->RollbackTrans();
                        $response = array('status' => 'error', 'message' => $ex->getMessage());
                    }
                    
                } else {
                    $response = array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
                }
                
            } else {
                $response = array('status' => 'error', 'message' => $headerSheetName.' гэсэн sheet нэр олдсонгүй!');
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Please select excel file!');
        }
        
        return $response;
    }
    
    public function cleanVal($string) {
        if ($string == '' || $string === null) {
            return null;
        }
        
        $string = str_replace(array('‘', '’', '“', '”', "'"), array("''", "''", '"', '"', "''"), $string);
        $string = htmlspecialchars($string);
        $string = str_replace('&amp;', '&', $string);
        $string = stripslashes($string);
        $string = strip_tags($string);
        $string = trim($string);
        $string = Str::remove_doublewhitespace($string);
        
        return $string;
    }
    
    public function cleanDecimal($number)
    { 
        if ($number == '' || $number === null) {
            return null;
        }
        
        $number = str_replace(',', '', $number);
        if (is_numeric($number)) {
            return $number;
        }
        return 0; 
    }
    
    public function expressionServerSideParse($expressionColumns, $columnConfig) {
        
        $evalExp = '';
        
        foreach ($expressionColumns as $expColName => $expColRow) {
            
            $expression = $expColRow['EXPRESSION_STRING'];
            
            preg_match_all('/\[(.*?)\]/', $expression, $expEventCatch);
            
            if (isset($expEventCatch[0][0])) {
                
                foreach ($expEventCatch[1] as $eventPath) {
                    $expression = self::kpiExpressionReplaceFncNames($expression);
                    $expression = str_replace("[$eventPath]", 'issetParam($_POST[\'kpiTbl\'][\''.$eventPath.'\'])', $expression);
                }
                
                $evalExp .= '$_POST[\'kpiTbl\'][\''.$expColName.'\'] = '.$expression.'; ';
            }
        }
        
        return $evalExp;
    }
    
    public function kpiExpressionReplaceFncNames($expression) {
        
        $expression = str_replace('dateFormat(', 'self::kpiDateFormat(', $expression);
        
        return $expression;
    }
    
    public function kpiDateFormat($format, $dateStr) {
        
        if ($dateStr != '' && $format != '') {
            if ($format == 'S') {
                
                $getMonth = date('m', strtotime($dateStr));
                
                if ($getMonth == '01' || $getMonth == '02' || $getMonth == '03') {
                    $season = 1; 
                } elseif ($getMonth == '04' || $getMonth == '05' || $getMonth == '06') {
                    $season = 2; 
                } elseif ($getMonth == '07' || $getMonth == '08' || $getMonth == '09') {
                    $season = 3;
                } else {
                    $season = 4;
                }
                
                return $season;
            } else {
                return date($format, strtotime($dateStr));
            }
        } 
        
        return '';
    }

    public function saveToProcess($postData) {
        
        $mainIndicatorId = $postData['kpiMainIndicatorId'];
        
        if (!isset(self::$kpiProcessConfig[$mainIndicatorId])) {
            
            $data = $this->db->GetAll("
                SELECT
                    MPPL.META_PROCESS_PARAM_LINK_ID,
                    MPPL.MAIN_BP_ID,
                    MPPL.DO_BP_ID,
                    MPPL.DO_BP_PARAM_PATH,
                    MPPL.DO_BP_PARAM_IS_INPUT,
                    MPPL.DONE_BP_ID,
                    MPPL.DONE_BP_PARAM_PATH,
                    MPPL.DONE_BP_PARAM_IS_INPUT,
                    MPPL.DONE_MODEL_ID, 
                    MPPL.DONE_MODEL_PARAM_PATH, 
                    MPPL.DONE_MODEL_PARAM_IS_INPUT, 
                    MPPL.DEFAULT_VALUE 
                FROM META_PROCESS_PARAM_LINK MPPL 
                    INNER JOIN META_DATA MD1 ON MD1.META_DATA_ID = MPPL.DO_BP_ID 
                WHERE MPPL.DONE_MODEL_ID = ".$this->db->Param(0), 
                array($mainIndicatorId)
            );
            
            self::$kpiProcessConfig[$mainIndicatorId] = $data;
        }
        
        $data = self::$kpiProcessConfig[$mainIndicatorId];
        
        if ($data) {
            
            $this->load->model('mdmetadata', 'middleware/models/');
            $metaRow = $this->model->getMetaDataModel($data[0]['DO_BP_ID']);        

            $dataGroup = Arr::groupByArray($data, 'DONE_MODEL_PARAM_PATH');            
            $param = [];

            foreach ($postData['kpiTbl'] as $key => $row) {
                if (array_key_exists($key, $dataGroup)) {
                    $bppath = $dataGroup[$key]['row']['DO_BP_PARAM_PATH'];
                    $param[$bppath] = $row;
                }
            }
            if (isset($postData['kpiTbl']['rows'])) {
                foreach ($postData['kpiTbl']['rows'] as $key => $row) {
                    foreach ($row as $key2 => $row2) {
                        if (array_key_exists($key.'.'.$key2, $dataGroup)) {
                            $bppath = explode(".", $dataGroup[$key.'.'.$key2]['row']['DO_BP_PARAM_PATH']);
                            foreach ($row2 as $key3 => $row3) {
                                $param[$bppath[0]][$key3][$bppath[1]] = $row3;
                            }
                        }
                    }
                }
            }
            $resultBp = $this->ws->runSerializeResponse(self::$gfServiceAddress, $metaRow['META_DATA_CODE'], $param);
        }
        
        return true;
    }
    
    public function actionKpiDataTableModel($indicatorId, $isCheckActionPermission) {
        
        if ($isCheckActionPermission) {
            
            $indicatorIdPh = $this->db->Param(0);
            $userIdPh = $this->db->Param(1);
        
            $row = $this->db->GetRow("
                SELECT 
                    K.RELATED_INDICATOR_ID,
                    K.META_DATA_ID,
                    1 AS IS_LIST,
                    CASE WHEN K.IS_CREATE = 1 AND (K.USER_ID = $userIdPh OR K.ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh))
                        THEN 1
                    WHEN $userIdPh = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh AND ROLE_ID = 1) OR $userIdPh = 1
                        THEN 1
                        ELSE 0
                    END AS IS_CREATE,
                    CASE WHEN K.IS_UPDATE = 1 AND (K.USER_ID = $userIdPh OR K.ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh))
                        THEN 1
                    WHEN $userIdPh = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh AND ROLE_ID = 1) OR $userIdPh = 1
                        THEN 1
                        ELSE 0
                    END AS IS_UPDATE,
                    CASE WHEN K.IS_DELETE = 1 AND (K.USER_ID = $userIdPh OR K.ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh))
                        THEN 1
                    WHEN $userIdPh = (SELECT USER_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh AND ROLE_ID = 1) OR $userIdPh = 1
                        THEN 1
                        ELSE 0
                    END AS IS_DELETE
                FROM (
                SELECT 
                    K.RELATED_INDICATOR_ID,
                    PK.USER_ID,
                    PK.ROLE_ID,
                    K.META_DATA_ID,
                    SUM(
                    CASE WHEN U.ACTION_ID = 300101010000006
                        THEN 1
                        ELSE 0
                    END
                    ) AS IS_LIST,
                    SUM(
                    CASE WHEN U.ACTION_ID = 300101010000001
                        THEN 1
                        ELSE 0
                    END
                    ) AS IS_CREATE,
                    SUM(
                    CASE WHEN U.ACTION_ID = 300101010000002
                        THEN 1
                        ELSE 0
                    END
                    ) AS IS_UPDATE,
                    SUM(
                    CASE WHEN U.ACTION_ID = 300101010000003
                        THEN 1
                        ELSE 0
                    END
                    ) AS IS_DELETE
                FROM KPI_MENU K 
                    LEFT JOIN UM_PERMISSION_KEY PK ON K.SRC_RECORD_ID = PK.INDICATOR_ID
                    LEFT JOIN UM_ACTION U ON PK.ACTION_ID = U.ACTION_ID
                WHERE K.RELATED_INDICATOR_ID = $indicatorIdPh 
                GROUP BY
                    K.RELATED_INDICATOR_ID,
                    K.META_DATA_ID,
                    PK.USER_ID,
                    PK.ROLE_ID
                ) K", 
                array($indicatorId, Ue::sessionUserKeyId())
            );
            
            $actions = array(
                'IS_CREATE' => issetParam($row['IS_CREATE']) ? true : false, 
                'IS_UPDATE' => issetParam($row['IS_UPDATE']) ? true : false, 
                'IS_DELETE' => issetParam($row['IS_DELETE']) ? true : false
            );
            
        } else {
            
            $actions = array(
                'IS_CREATE' => true, 
                'IS_UPDATE' => true, 
                'IS_DELETE' => true
            );
        }
        
        return $actions;
    }
    
    public function getKpiDashboardChartsModel($indicatorId) {
        
        $layoutRow = $this->db->GetRow("
            SELECT 
                DL.LAYOUT_CODE, 
                DL.DATA, 
                KI.PROFILE_PICTURE 
            FROM KPI_DASHBOARD_LAYOUT DL 
                INNER JOIN KPI_INDICATOR KI ON KI.ID = DL.SRC_RECORD_ID 
            WHERE DL.SRC_RECORD_ID = ".$this->db->Param(0), 
            array($indicatorId)
        );
        
        $layoutCode = issetParam($layoutRow['LAYOUT_CODE']);
        $bgImage = issetParam($layoutRow['PROFILE_PICTURE']);
        $positionData = array();
        
        if ($layoutCode) {
            
            $data = @json_decode($layoutRow['DATA'], true);
            
            if (isset($data['DTL'])) {
                
                $langCode = Lang::getCode();
                $chartIndicatorIds = Arr::implode_key(',', $data['DTL'], 'DASHBOARD_POSITION', true);
                
                $chartIndicators = $this->db->GetAll("
                    SELECT  
                        KI.ID AS RELATED_INDICATOR_ID, 
                        FNC_TRANSLATE('$langCode', KI.TRANSLATION_VALUE, 'NAME', KI.NAME) AS NAME, 
                        KI.KPI_TYPE_ID,     
                        KI.GRAPH_JSON, 
                        KIIM.SRC_INDICATOR_ID 
                    FROM KPI_INDICATOR KI 
                        INNER JOIN KPI_INDICATOR_INDICATOR_MAP KIIM ON KIIM.TRG_INDICATOR_ID = KI.ID 
                            AND KIIM.SEMANTIC_TYPE_ID = 10000006  
                    WHERE KI.ID IN ($chartIndicatorIds)");
                
                foreach ($data['DTL'] as $row) {
                    
                    $name = $srcIndicatorId = $kpiTypeId = $graphJson = '';
                    
                    foreach ($chartIndicators as $chartIndicator) {
                        
                        if ($chartIndicator['RELATED_INDICATOR_ID'] == $row['DASHBOARD_POSITION']) {
                            
                            $name = $chartIndicator['NAME'];
                            $srcIndicatorId = $chartIndicator['SRC_INDICATOR_ID'];
                            $kpiTypeId = $chartIndicator['KPI_TYPE_ID'];
                            $graphJson = $chartIndicator['GRAPH_JSON'];
                            
                            break;
                        }
                    }
                    
                    $positionData[] = array(
                        'NAME'                 => $name, 
                        'SRC_INDICATOR_ID'     => $srcIndicatorId, 
                        'KPI_TYPE_ID'          => $kpiTypeId, 
                        'GRAPH_JSON'           => $graphJson, 
                        'RELATED_INDICATOR_ID' => $row['DASHBOARD_POSITION'], 
                        'POSITION_CODE'        => $row['ORDER_NUMBER']
                    );
                }
            }
        }
        
        return array('layoutCode' => $layoutCode, 'bgImage' => $bgImage, 'positionData' => $positionData);
    }
    
    public function getKpiDashboardFilterParamsModel($indicatorId) {
        
        $langCode = Lang::getCode();
        
        $data = $this->db->GetAll("
            SELECT 
                UPPER(T3.COLUMN_NAME) AS COLUMN_NAME, 
                FNC_TRANSLATE('$langCode', T3.TRANSLATION_VALUE, 'LABEL_NAME', T3.LABEL_NAME) AS LABEL_NAME, 
                T3.COLUMN_WIDTH, 
                T3.INPUT_NAME, 
                T3.SHOW_TYPE, 
                T3.IS_RENDER, 
                T3.TRG_INDICATOR_ID AS LOOKUP_META_DATA_ID, 
                T3.LOOKUP_CRITERIA, 
                T3.IS_FILTER, 
                T3.IS_INPUT, 
                T3.SORT_ORDER, 
                T3.SORT_TYPE, 
                T3.COLUMN_AGGREGATE, 
                T3.EXPRESSION_STRING, 
                T3.DEFAULT_VALUE, 
                T3.REPORT_AGGREGATE_FUNCTION, 
                T3.TRG_ALIAS_NAME, 
                'dashboard' AS RUN_MODE, 
                T2.SRC_INDICATOR_MAP_ID, 
                T2.TRG_INDICATOR_MAP_ID  
            FROM KPI_DATAMODEL_MAP T0 
                INNER JOIN KPI_DATAMODEL_MAP_KEY T1 ON T1.MAIN_INDICATOR_ID = T0.SRC_INDICATOR_ID 
                    AND T1.TRG_INDICATOR_ID = T0.TRG_INDICATOR_ID 
                INNER JOIN KPI_DATAMODEL_MAP_KEY_DTL T2 ON T2.DATAMODEL_MAP_KEY_ID = T1.ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T3 ON T3.ID = T2.SRC_INDICATOR_MAP_ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T4 ON T4.ID = T2.TRG_INDICATOR_MAP_ID 
            WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                AND T3.IS_FILTER = 1 
            ORDER BY T3.ORDER_NUMBER ASC", 
            array($indicatorId)
        );
        
        return $data;
    }
    
    public function getKpiAdditionalInfoModel($indicatorId) {

       $row = $this->db->GetRow("
           SELECT 
               *  
           FROM KPI_ADDITIONAL_INFO DL 
           WHERE SRC_RECORD_ID = ".$this->db->Param(0), 
           array($indicatorId)
       );
       
       return $row;
    }
    
    public function kpiSetMultiPathConfigModel($indicatorId) {
        
        $indicatorIdPh = $this->db->Param('indicatorId');

        $bindVars = array(
            'indicatorId' => $this->db->addQ($indicatorId)
        );
            
        $data = $this->db->GetAll("
            SELECT 
                NVL(TRG_INDICATOR_ID, LOOKUP_META_DATA_ID) AS LOOKUP_META_DATA_ID, 
                
                CASE 
                    WHEN SHOW_TYPE = 'combo' OR SHOW_TYPE = 'popup' OR SHOW_TYPE = 'radio' OR SHOW_TYPE = 'text' 
                    THEN 'string' 
                    WHEN SHOW_TYPE = 'decimal' 
                    THEN 'bigdecimal' 
                    WHEN SHOW_TYPE = 'check' 
                    THEN 'boolean' 
                ELSE SHOW_TYPE END AS META_TYPE_CODE, 
                
                CASE 
                    WHEN PARENT_ID = $indicatorIdPh 
                    THEN NULL  
                ELSE PARENT_ID END AS PARENT_ID, 

                NULL AS SIDEBAR_NAME, 
                LOWER(COLUMN_NAME_PATH) AS PARAM_REAL_PATH, 
                COLUMN_NAME_PATH AS PATH, 
                LABEL_NAME, 
                0 AS IS_TRANSLATE, 
                IS_RENDER AS IS_SHOW,  
                'single' AS CHOOSE_TYPE, 
                CASE 
                    WHEN SHOW_TYPE = 'combo' 
                    THEN 'combo' 
                    WHEN SHOW_TYPE = 'popup' 
                    THEN 'popup' 
                    WHEN SHOW_TYPE = 'radio' 
                    THEN 'radio' 
                ELSE NULL END AS LOOKUP_TYPE, 
                NULL AS JSON_CONFIG, 
                NULL AS ABILITY_TOGGLE 
            FROM KPI_INDICATOR_INDICATOR_MAP 
            WHERE MAIN_INDICATOR_ID = $indicatorIdPh 
                AND ".$this->db->IfNull('IS_INPUT', '0')." = 1", $bindVars); 
        
        $array = array();

        if ($data) {
            foreach ($data as $row) {
                $array[$row['PARAM_REAL_PATH']] = $row;
            }
        }

        return $array;
    }
    
    public function updateGraphJsonKpiIndicatorModel($id, $json) {
        $result = $this->db->UpdateClob("KPI_INDICATOR", "GRAPH_JSON", $json, 'ID = '.$id);
        if ($result){
            $response = array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));
        } else {
            $response = array('status' => 'error', 'message' => 'Error!');
        }
        return $response;
    }
    
    public function getKpiIndicatorMapModel($srcId, $semanticTypeId, $trgId = null) {
        
        try {
            $langCode = Lang::getCode();
        
            $data = $this->db->GetAll("
                SELECT 
                    T1.ID, 
                    FNC_TRANSLATE('$langCode', T1.TRANSLATION_VALUE, 'NAME', T1.NAME) AS NAME, 
                    T1.TABLE_NAME, 
                    T1.QUERY_STRING, 
                    T1.KPI_TYPE_ID, 
                    T0.ID AS MAP_ID, 
                    T0.ICON, 
                    T0.COLOR, 
                    T0.IS_ADDON_FORM, 
                    T0.META_INFO_INDICATOR_ID, 
                    T0.CODE, 
                    ".$this->db->IfNull('T0.DESCRIPTION', "'Холбоос'")." AS DESCRIPTION, 
                    T2.TABLE_NAME AS SRC_TABLE_NAME, 
                    T2.QUERY_STRING AS SRC_QUERY_STRING,
                    T1.PARENT_ID, 
                    FNC_TRANSLATE('$langCode', T3.TRANSLATION_VALUE, 'NAME', T3.NAME) AS PARENT_NAME 
                FROM (
                        SELECT 
                            MAX(KIIM.ID) AS ID, 
                            MAX(KIIM.ICON) AS ICON, 
                            MAX(KIIM.COLOR) AS COLOR, 
                            MAX(KIIM.ORDER_NUMBER) AS ORDER_NUMBER, 
                            MAX(KIIM.IS_ADDON_FORM) AS IS_ADDON_FORM, 
                            MAX(KIIM.DESCRIPTION) AS DESCRIPTION, 
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.META_INFO_INDICATOR_ID, 
                            KIIM.CODE 
                        FROM (
                            SELECT 
                                KIIM.ID, 
                                KIIM.ICON, 
                                KIIM.COLOR, 
                                KIIM.ORDER_NUMBER, 
                                KIIM.CODE, 
                                KIIM.DESCRIPTION, 
                                KIIM.SRC_INDICATOR_ID, 
                                KIIM.TRG_INDICATOR_ID, 
                                K.ID AS META_INFO_INDICATOR_ID, 
                                (
                                    SELECT 
                                        COUNT(1) 
                                    FROM KPI_INDICATOR_INDICATOR_MAP M 
                                        INNER JOIN KPI_INDICATOR I ON I.ID = M.TRG_INDICATOR_ID 
                                    WHERE M.SRC_INDICATOR_MAP_ID = KIIM.ID 
                                        AND M.TRG_INDICATOR_ID IS NOT NULL 
                                        AND M.SRC_INDICATOR_PATH IS NOT NULL 
                                        AND M.TRG_INDICATOR_PATH IS NOT NULL 
                                ) AS IS_ADDON_FORM 
                            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                                LEFT JOIN KPI_INDICATOR K ON K.ID = KIIM.META_INFO_INDICATOR_ID 
                            WHERE KIIM.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                                AND KIIM.SEMANTIC_TYPE_ID = ".$this->db->Param(1)." 
                                ".($trgId ? 'AND KIIM.TRG_INDICATOR_ID = '.$trgId : '')."  
                        ) KIIM 
                        GROUP BY 
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.META_INFO_INDICATOR_ID, 
                            KIIM.CODE 
                    ) T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                        AND T1.DELETED_USER_ID IS NULL 
                    INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.SRC_INDICATOR_ID 
                        AND T2.DELETED_USER_ID IS NULL 
                    LEFT JOIN KPI_INDICATOR T3 ON T3.ID = T1.PARENT_ID 
                ORDER BY T0.ORDER_NUMBER ASC", 
                array($srcId, $semanticTypeId)
            );
            
        } catch (Exception $ex) {
            $data = array();
        }
        
        return $data;
    }
    
    public function getKpiIndicatorMapByMapIdModel($mapId) {
        
        $row = $this->db->GetRow("
            SELECT 
                KIIM.ID, 
                KIIM.ICON, 
                KIIM.COLOR, 
                KIIM.TRG_INDICATOR_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
            WHERE KIIM.ID = ".$this->db->Param(0), 
            array($mapId)
        );
        
        return $row;
    }
    
    public function getKpiIndicatorProcessModel($srcId, $trgId = null) {
        
        try {
            $idPh = $this->db->Param(0);
            $userIdPh = $this->db->Param(1);
            $bindParams = array($srcId, Ue::sessionUserKeyId()); 
            $where = ' AND (M.IS_HIDE_LIST = 0 OR M.IS_HIDE_LIST IS NULL)';

            if ($trgId) {
                $trgIdPh = $this->db->Param(2);
                array_push($bindParams, $trgId);

                $where = ' AND KI.ID = '.$trgIdPh;
            }

            $data = $this->db->GetAll("
                SELECT 
                    K.ID AS MAIN_INDICATOR_ID, 
                    KI.ID AS CRUD_INDICATOR_ID, 
                    COALESCE(KI.PARENT_ID, M1.SRC_INDICATOR_ID) AS STRUCTURE_INDICATOR_ID, 
                    KI.CODE, 
                    KI.NAME, 
                    LOWER(KI.TYPE_CODE) AS TYPE_CODE, 
                    KI.KPI_TYPE_ID, 
                    M.LABEL_NAME, 
                    M.DESCRIPTION, 
                    (
                        SELECT 
                            COUNT(1) 
                        FROM KPI_INDICATOR_INDICATOR_MAP 
                        WHERE TRG_INDICATOR_ID = KI.ID 
                            AND SRC_INDICATOR_ID = K.ID 
                            AND SEMANTIC_TYPE_ID = 10000015 
                    ) AS IS_FILL_RELATION, 
                    (
                        SELECT 
                            COUNT(1) 
                        FROM KPI_INDICATOR_INDICATOR_MAP 
                        WHERE TRG_INDICATOR_ID = KI.ID 
                            AND SRC_INDICATOR_ID = K.ID 
                            AND (SEMANTIC_TYPE_ID = 10000009 AND JSON_CONFIG IS NOT NULL) 
                    ) AS IS_DFILL_RELATION 
                FROM KPI_INDICATOR K 
                    INNER JOIN KPI_INDICATOR_INDICATOR_MAP M ON K.ID = M.SRC_INDICATOR_ID 
                        AND M.SEMANTIC_TYPE_ID = 10000009 
                    INNER JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                    INNER JOIN KPI_INDICATOR_INDICATOR_MAP M1 ON KI.ID = M1.TRG_INDICATOR_ID 
                        AND K.ID = M1.SRC_INDICATOR_ID 
                        AND M1.SEMANTIC_TYPE_ID = 10000009 
                WHERE K.ID = $idPh 
                    $where 
                    AND (
                        K.CREATED_USER_ID = $userIdPh 
                            OR 
                        1 = $userIdPh 
                            OR 
                        KI.ID IN ( 
                            SELECT 
                                INDICATOR_ID 
                            FROM UM_PERMISSION_KEY 
                            WHERE USER_ID = $userIdPh 
                                OR ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh) 
                            GROUP BY INDICATOR_ID 

                            UNION ALL 

                            SELECT 
                                ID AS INDICATOR_ID 
                            FROM KPI_INDICATOR 
                            WHERE $userIdPh = (SELECT DISTINCT USER_ID FROM UM_USER_ROLE WHERE ROLE_ID = 1 AND USER_ID = $userIdPh) 
                        )
                    )
                ORDER BY M.ORDER_NUMBER ASC", 
                $bindParams
            );

            return Arr::changeKeyLower($data);
        
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getKpiIndicatorProcessWidgetModel($srcId, $mapId) {
        
        $data = array(
            array(
                'structure_indicator_id' => $srcId, 
                'type_code' => 'create', 
                'kpi_type_id' => '2008', 
                'map_id' => $mapId, 
                'crud_indicator_id' => '999'
            ), 
            array(
                'structure_indicator_id' => $srcId, 
                'type_code' => 'update', 
                'kpi_type_id' => '2008', 
                'map_id' => $mapId, 
                'crud_indicator_id' => '999'
            ), 
            array(
                'structure_indicator_id' => $srcId, 
                'type_code' => 'delete', 
                'kpi_type_id' => '2008', 
                'map_id' => $mapId, 
                'crud_indicator_id' => '999'
            )
        );
        
        return $data;
    }
    
    public function getSavedRecordMapKpiComponentsModel($srcIndicatorId, $srcRecordId, $components) {
        
        try {
            
            $result = array();
        
            foreach ($components as $component) {
                
                $tableName = $component['TABLE_NAME'];
                $queryString = $component['QUERY_STRING'];
                
                if ($tableName || $queryString) {

                    $trgIndicatorId = $component['ID'];

                    $fieldConfig = self::getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $trgIndicatorId, 'TRG_TABLE_NAME' => $tableName, 'isData' => false));
                    
                    $idField = $fieldConfig['id'];
                    $nameField = $fieldConfig['name'];
                    
                    if ($idField && $nameField) {
                        
                        if (!$tableName && $queryString) {
                            $tableName = self::parseQueryString($queryString);
                        }

                        if (stripos($tableName, 'select') !== false && stripos($tableName, 'from') !== false) {
                            $tableName = '('.$tableName.')';
                        }

                        $sql = "
                            SELECT 
                                MRM.ID AS PF_MAP_ID, 
                                MRM.TRG_RECORD_ID AS PF_MAP_RECORD_ID, 
                                T0.$nameField AS PF_MAP_NAME, 
                                T1.TRG_RECORD_ID AS PF_MAP_TRG_RECORD_ID, 
                                T0.* 
                            FROM 
                                (
                                    SELECT 
                                        ID, 
                                        TRG_RECORD_ID 
                                    FROM META_DM_RECORD_MAP 
                                    WHERE SRC_REF_STRUCTURE_ID = $srcIndicatorId 
                                        AND SRC_RECORD_ID = $srcRecordId 
                                        AND TRG_REF_STRUCTURE_ID = $trgIndicatorId
                                ) MRM 

                                INNER JOIN $tableName T0 ON T0.$idField = MRM.TRG_RECORD_ID 
                                LEFT JOIN META_DM_RECORD_MAP T1 ON T1.SRC_RECORD_ID = MRM.ID 
                                    AND T1.SRC_REF_STRUCTURE_ID = $trgIndicatorId 

                            ORDER BY MRM.ID ASC";

                        $rows = $this->db->GetAll($sql);
                        
                    } else {
                        $rows = array();
                    }
                    
                    $result[$trgIndicatorId] = $rows;
                }
            }
        
        } catch (Exception $ex) {
            $result = array();
        }
        
        return $result;
    }
    
    public function getMetaProcessRecordRelationModel($refStructureId, $sourceId, $processId) {
        
        $result = array('isUserControl' => true, 'data' => array());
        
        try {
            
            $processIndicatorMap = $this->db->GetOne("
                SELECT 
                    COUNT(1) 
                FROM CUSTOMER_USE_CHILD T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.INDICATOR_ID 
                        AND T1.DELETED_USER_ID IS NULL 
                WHERE T0.TRG_META_DATA_ID = ".$this->db->Param(0)."  
                    AND T0.IS_USE = 1 
                    AND T0.SRC_META_DATA_ID IS NULL", array($processId));
        
            if ($processIndicatorMap) {

                $mapData = $this->db->GetAll("
                    SELECT 
                        T0.INDICATOR_ID AS TRG_REF_STRUCTURE_ID, 
                        T2.TRG_RECORD_ID 
                    FROM CUSTOMER_USE_CHILD T0 
                        INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.INDICATOR_ID 
                            AND T1.DELETED_USER_ID IS NULL 
                        LEFT JOIN META_DM_RECORD_MAP T2 ON T2.TRG_REF_STRUCTURE_ID = T1.ID 
                            AND T2.SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                            AND T2.SRC_RECORD_ID = ".$this->db->Param(1)." 
                    WHERE T0.TRG_META_DATA_ID = ".$this->db->Param(2)." 
                        AND T0.IS_USE = 1 
                        AND T0.SRC_META_DATA_ID IS NULL 
                    GROUP BY 
                        T0.INDICATOR_ID,
                        T2.TRG_RECORD_ID", 
                    array($refStructureId, $sourceId, $processId)
                );
                
            } elseif ($refStructureId && $sourceId) {

                $mapData = $this->db->GetAll("
                    SELECT 
                        T0.TRG_REF_STRUCTURE_ID, 
                        T0.TRG_RECORD_ID 
                    FROM META_DM_RECORD_MAP T0 
                        INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_REF_STRUCTURE_ID 
                    WHERE T0.SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                        AND T0.SRC_RECORD_ID = ".$this->db->Param(1)." 
                    GROUP BY 
                        T0.TRG_REF_STRUCTURE_ID,
                        T0.TRG_RECORD_ID", 
                    array($refStructureId, $sourceId)
                );
            } 
            
            if (isset($mapData) && $mapData) {

                $mapData = Arr::groupByArray($mapData, 'TRG_REF_STRUCTURE_ID');
                $loop = array();
                
                foreach ($mapData as $row) {

                    $trgIndicatorId = $row['row']['TRG_REF_STRUCTURE_ID'];
                    $rowIds         = Arr::implode_key(',', $row['rows'], 'TRG_RECORD_ID', true);
                    
                    $fieldConfig    = self::getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $trgIndicatorId, 'TRG_TABLE_NAME' => '', 'isData' => true, 'rowIds' => $rowIds));
                    
                    $loop[$trgIndicatorId] = $fieldConfig;
                }
                
                $result = array(
                    'isUserControl' => ($processIndicatorMap ? false : true), 
                    'data' => $loop
                );
            }
        
        } catch (Exception $ex) { }
        
        return $result; 
    }
    
    public function bpRelationRemoveRowModel() {
        
        try {
            
            $refStructureId    = Input::numeric('refStructureId');
            $sourceId          = Input::numeric('sourceId');
            $trgRefStructureId = Input::numeric('trgRefStructureId');
            $trgSourceId       = Input::numeric('trgSourceId');
            
            if ($refStructureId && $sourceId && $trgRefStructureId && $trgSourceId) {
                
                $this->db->Execute("
                    DELETE 
                    FROM META_DM_RECORD_MAP 
                    WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                        AND TRG_REF_STRUCTURE_ID = ".$this->db->Param(1)." 
                        AND SRC_RECORD_ID = ".$this->db->Param(2)." 
                        AND TRG_RECORD_ID = ".$this->db->Param(3), 
                    array($refStructureId, $trgRefStructureId, $sourceId, $trgSourceId)
                );
                
            } elseif ($refStructureId && $sourceId && $trgRefStructureId) {
                
                $this->db->Execute("
                    DELETE 
                    FROM META_DM_RECORD_MAP 
                    WHERE SRC_REF_STRUCTURE_ID = ".$this->db->Param(0)." 
                        AND TRG_REF_STRUCTURE_ID = ".$this->db->Param(1)." 
                        AND SRC_RECORD_ID = ".$this->db->Param(2), 
                    array($refStructureId, $trgRefStructureId, $sourceId)
                );
            }
            
            $response = array('status' => 'success', 'message' => $this->lang->line('msg_delete_success'));
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }

    public function saveBlockExpressionModel() {

        $param = Input::postData();

        $param['varFncExpressionString'] = $param['varFncExpressionString'];
        $param['varFncExpressionStringJson'] = $param['varFncExpressionStringJson'];

        try {            
            $result = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'kpiExpression_001', $param);
            
            if ($result['status'] == 'success') {
                
                Mdexpression::clearCacheFlowchart();
                $response = array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));       
                
            } else {
                $response = array('status' => 'error', 'message' => $this->ws->getResponseMessage($result));
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }

        return $response;
    }    

    public function getBlockExpressionModel($id) {
        
        $param = array('id' => $id);
        $data = $this->ws->runSerializeResponse(self::$gfServiceAddress, 'kpiExpression_004', $param);
        
        return $data;
    }    
    
    public function kpiIndicatorChartDataConfigModel() {
        
        try {
            
            $chartIndicatorId = Input::numeric('indicatorId');
            
            if (!$chartIndicatorId) {
                throw new Exception('Invalid indicatorId!'); 
            }
            
            $configRow = self::getKpiIndicatorChartRowModel($chartIndicatorId);
            
            if (!$configRow) {
                throw new Exception("$chartIndicatorId уг indicatorId-р чарт олдсонгүй!"); 
            }
            
            $configJson = json_decode($configRow['GRAPH_JSON'], true);
            
            if (!$configJson) {
                throw new Exception('Чартын тохиргоо олдсонгүй!'); 
            }
            
            $dataSetIndicatorId = $configRow['SRC_INDICATOR_ID'];
            
            if (!$dataSetIndicatorId) {
                throw new Exception('Dataset indicatorId олдсонгүй!'); 
            }
            
            $dashboardFilter = Input::post('dashboardFilter');
            $criteria = Input::post('criteria');
            
            unset($_POST);
            
            $configJson['indicatorId'] = $dataSetIndicatorId;
            
            if ($chartFilterCriteriaStr = issetParam($configJson['chartFilterCriteria'])) {
                
                $chartFilterCriteriaArr = json_decode(html_entity_decode($chartFilterCriteriaStr, ENT_QUOTES, 'UTF-8'), true);
                
                if (is_array($chartFilterCriteriaArr) && count($chartFilterCriteriaArr)) {
                    
                    if ($criteria) {
                        foreach ($chartFilterCriteriaArr as $chartFilterCriteriaCol => $chartFilterCriteriaRow) {
                            if (isset($criteria[$chartFilterCriteriaCol])) {
                                unset($chartFilterCriteriaArr[$chartFilterCriteriaCol]);
                            }
                        }
                    }
                    
                    if ($chartFilterCriteriaArr) {
                        $configJson['filterData'] = $chartFilterCriteriaArr;
                    }
                }
            }
            
            $_POST = $configJson;
            
            if ($dashboardFilter) {
                $_POST['dashboardFilter'] = $dashboardFilter;
            }
            
            if ($criteria) {
                $_POST['criteria'] = $criteria;
            }
            
            $result = $this->model->filterKpiIndicatorValueChartModel();
            
            if ($result['status'] == 'success') {
                
                $configJson['chartConfig']['name'] = $configRow['NAME'];
                $result['chartConfig'] = $configJson['chartConfig'];
                
                $response = $result;
                
            } else {
                $response = $result;
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }

    public function getKpiIOIndicatorColumnsModel($indicatorId, $isInput = 0) {
        
        $data = $this->db->GetAll("
            SELECT 
                UPPER(KIIM.COLUMN_NAME) AS COLUMN_NAME, 
                KIIM.LABEL_NAME, 
                KIIM.COLUMN_WIDTH, 
                KIIM.INPUT_NAME, 
                KIIM.SHOW_TYPE, 
                KIIM.IS_RENDER, 
                KIIM.TRG_INDICATOR_ID AS LOOKUP_META_DATA_ID, 
                KIIM.LOOKUP_CRITERIA, 
                KIIM.IS_FILTER, 
                KIIM.IS_INPUT, 
                KIIM.SORT_ORDER, 
                KIIM.SORT_TYPE, 
                KIIM.COLUMN_AGGREGATE, 
                KIIM.EXPRESSION_STRING, 
                MST.NAME AS SEMANTIC_TYPE_NAME, 
                KI.TABLE_NAME AS TRG_TABLE_NAME 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                LEFT JOIN KPI_INDICATOR KI ON KIIM.TRG_INDICATOR_ID = KI.ID 
                LEFT JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
            WHERE KIIM.MAIN_INDICATOR_ID = ".$this->db->Param(0).
                (is_null($isInput) ? "" : " AND ".$this->db->IfNull('KIIM.IS_INPUT', '0')." = ".$this->db->Param(1))." 
                AND KIIM.COLUMN_NAME <> 'ID' 
            ORDER BY KIIM.ORDER_NUMBER ASC", 
            array($indicatorId, $isInput)
        );

        return $data;
    }

    public function getKpiIndicatorByCodeModel($templateCode) {
        
        $row = $this->db->GetRow("
            SELECT 
                ID, 
                CODE, 
                NAME, 
                RENDER_TYPE, 
                TABLE_NAME, 
                QUERY_STRING, 
                2 AS TYPE_CODE, 
                NAME_PATTERN, 
                null AS PIVOT_VALUE_META_DATA_ID, 
                null AS PIVOT_VALUE_CRITERIA, 
                null AS INDICATOR_COL_WIDTH, 
                null AS WIDTH, 
                null AS HEIGHT, 
                null AS EXPRESSION_TEMPLATE_ID,
                null AS TYPE_ID, 
                null AS DEFAULT_TEMPLATE_ID, 
                null AS MERGE_COL_COUNT, 
                KPI_TYPE_ID, 
                IS_USE_WORKFLOW, 
                IS_FILTER_SHOW_DATA, 
                IS_USE_COMPANY_DEPARTMENT_ID, 
                IS_ADDON_FILE, 
                IS_ADDON_PHOTO,
                PARENT_ID,
                CLASS_NAME,
                VAR_FNC_EXPRESSION_STRING  
            FROM KPI_INDICATOR 
            WHERE CLASS_NAME = ".$this->db->Param(0), 
            array($templateCode)
        );
        
        return $row;
    }    

    public function getKpiDataMartRelationColumnsByCodeModel($mainIndicatorCode) {
        
        $idPh1 = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT 
                KIIM.ID, 
                KI.TABLE_NAME AS SRC_TABLE_NAME, 
                KIIM.LABEL_NAME, 
                UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                KIIM.AGGREGATE_FUNCTION, 
                KIIM.EXPRESSION_STRING, 
                KIIM.EVENT_STRING, 
                KIIM.EVENT_EXPRESSION_STRING, 
                KIIM.BLOCK_DIAGRAM, 
                KIIM.TRG_ALIAS_NAME, 
                MST.NAME AS SEMANTIC_TYPE_NAME, 
                KIIM.SRC_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_MAP_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                INNER JOIN KPI_INDICATOR KI ON KIIM.MAIN_INDICATOR_ID = KI.ID
                INNER JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
            WHERE KI.CODE = '$mainIndicatorCode' 
            ORDER BY KIIM.ORDER_NUMBER ASC", array($mainIndicatorCode));
        
        return $data;
    }    

    public function objectMethodModel($id) {
        
        $idPh1 = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT 
                KI.ID, 
                KI.CODE, 
                KI.NAME,
                KI.TYPE_CODE
            FROM 
                KPI_INDICATOR KI 
            WHERE KI.PARENT_ID = $id AND KI.VAR_FNC_EXPRESSION_STRING IS NOT NULL
            ORDER BY KI.ORDER_NUMBER", array($id));
        
        return $data;
    }    
    
    public function getKpiDataMartRelationAllColumnsModel() {
        
        $sql = "
            SELECT KI.CLASS_NAME||' - class name' AS TEXT, 
            KI.CLASS_NAME AS VALUE
        FROM KPI_INDICATOR KI 
        WHERE KI.CLASS_NAME IS NOT NULL
        ORDER BY KI.CLASS_NAME";
        
//        $sql = "
//            SELECT 
//                KIIM.ID, 
//                KI.TABLE_NAME AS SRC_TABLE_NAME, 
//                KI.CODE, 
//                KIIM.LABEL_NAME, 
//                UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
//                KIIM.AGGREGATE_FUNCTION, 
//                KIIM.EXPRESSION_STRING, 
//                KIIM.EVENT_STRING, 
//                KIIM.EVENT_EXPRESSION_STRING, 
//                KIIM.BLOCK_DIAGRAM, 
//                KIIM.TRG_ALIAS_NAME, 
//                MST.NAME AS SEMANTIC_TYPE_NAME, 
//                KIIM.SRC_INDICATOR_ID, 
//                KIIM.TRG_INDICATOR_ID, 
//                KIIM.TRG_INDICATOR_MAP_ID 
//            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
//                INNER JOIN KPI_INDICATOR KI ON KIIM.MAIN_INDICATOR_ID = KI.ID
//                INNER JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
//            ORDER BY KIIM.ORDER_NUMBER ASC";

            $rs   = $this->db->SelectLimit($sql, 200, 0);
            $row = isset($rs->_array) ? $rs->_array : array();            
        
        return $row;
    }    
    
    public function getParentIndicatorFunctionModel($id) {
        
        $row = $this->db->GetRow("
            SELECT 
                SRC_RECORD_ID 
            FROM KPI_FUNCTION 
            WHERE PARENT_INDICATOR_ID = ".$this->db->Param(0), 
            array($id)
        );
        
        return $row;
    }        
    
    public function runAllKpiDataMartModel($mode = '') {
        
        if ($mode == 'kpiDmCalcMart') {
            
            $data = $this->db->GetAll("
                SELECT 
                    T1.ID, T1.CODE, T1.KPI_TYPE_ID, T0.ID AS CALC_MART_ID 
                FROM KPI_DM_CALC_MART T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.INDICATOR_ID 
                WHERE T1.DELETED_USER_ID IS NULL 
                    AND T0.FIRST_LOAD_TIME IS NULL 
                ORDER BY T0.ID ASC");
            
        } else {
            
            $data = $this->db->GetAll("
                SELECT 
                    TMP.ID, TMP.CODE, TMP.KPI_TYPE_ID, 1 AS TMP_ORDER
                FROM ( 
                    SELECT 
                        KPI.ID, 
                        KPI.CODE, 
                        KPI.KPI_TYPE_ID, 
                        KPI.IS_CONNECT_TRG, 
                        KPI.IS_CONNECT_SRC_TRG 
                    FROM (
                        SELECT 
                            T0.ID, 
                            T0.CODE, 
                            T0.NAME, 
                            T0.KPI_TYPE_ID, 
                            CASE WHEN T3.ID IS NULL THEN 0 
                            ELSE 1 END AS IS_CONNECT_TRG, 
                            CASE WHEN T4.ID IS NOT NULL THEN 1 
                            ELSE 0 END AS IS_CONNECT_SRC_TRG, 
                            T2.ID AS MAP_ID 
                        FROM KPI_INDICATOR T0 
                            INNER JOIN KPI_DATAMODEL_MAP T1 ON T1.SRC_INDICATOR_ID = T0.ID 
                            LEFT JOIN KPI_DATAMODEL_MAP T2 ON T0.ID = T2.TRG_INDICATOR_ID 
                            LEFT JOIN KPI_DATAMODEL_MAP_KEY T3 ON T2.TRG_INDICATOR_ID = T3.TRG_INDICATOR_ID 
                            LEFT JOIN KPI_DATAMODEL_MAP_KEY T4 ON T4.TRG_INDICATOR_ID = T3.SRC_INDICATOR_ID 
                                AND T4.SRC_INDICATOR_ID = T3.TRG_INDICATOR_ID 
                        WHERE T0.KPI_TYPE_ID = 1040 
                            AND T0.DELETED_USER_ID IS NULL 
                    ) KPI 
                    GROUP BY 
                        KPI.ID, 
                        KPI.CODE, 
                        KPI.NAME, 
                        KPI.KPI_TYPE_ID, 
                        KPI.IS_CONNECT_TRG, 
                        KPI.IS_CONNECT_SRC_TRG 
                    ORDER BY 
                        KPI.IS_CONNECT_TRG DESC, 
                        KPI.IS_CONNECT_SRC_TRG ASC, 
                        MAX(KPI.MAP_ID) DESC 
                ) TMP 

                UNION ALL 

                SELECT
                    TMP.ID, TMP.CODE, TMP.KPI_TYPE_ID, 2 AS TMP_ORDER 
                FROM (
                    SELECT
                        ID,
                        CODE,
                        NAME,
                        PARENT_ID,
                        ORDER_NUMBER,
                        KPI_TYPE_ID,
                        LEVEL AS LVL 
                    FROM (
                        SELECT 
                            KI.ID, 
                            KI.CODE, 
                            KI.NAME, 
                            KI.PARENT_ID, 
                            KI.ORDER_NUMBER, 
                            KI.KPI_TYPE_ID 
                        FROM KPI_INDICATOR KI 
                        WHERE KI.DELETED_USER_ID IS NULL 
                            AND KI.KPI_TYPE_ID NOT IN (1050, 2008)
                        ORDER BY KI.ORDER_NUMBER
                    )
                    START WITH PARENT_ID IS NULL 
                    CONNECT BY NOCYCLE PRIOR ID = PARENT_ID 
                    ORDER SIBLINGS BY ORDER_NUMBER 
                ) TMP 
                WHERE TMP.KPI_TYPE_ID = 1044");
        }
        
        $arr = array();
        $result = array('status' => 'success');
        
        foreach ($data as $row) {
            
            $indicatorId = $row['ID'];
            
            if (!isset($arr[$indicatorId])) {
                
                $arr[$indicatorId] = array('kpiTypeId' => $row['KPI_TYPE_ID']);
                
                if (isset($row['CALC_MART_ID'])) {
                    $arr[$indicatorId]['calcMartId'] = $row['CALC_MART_ID'];
                }
            }
        }
        
        $cacheTmpDir = Mdcommon::getCacheDirectory();
        $tempdir     = Mdcache::createCacheFolder($cacheTmpDir . '/kpidatamart', 5);
            
        if ($arr) {
            
            $startIndicatorId = $data[0]['ID'];
            
            $fileName    = getUID();
            $filePath    = $tempdir.'/'.$fileName.'.json';
            $json        = json_encode($arr, JSON_PRETTY_PRINT);

            $f = fopen($filePath, "w+");
            fwrite($f, $json);
            fclose($f);
            
            if (file_exists($filePath)) {
            
                $phpUrl = Config::getFromCache('PHP_URL');
                $phpUrl = rtrim($phpUrl, '/');
                $phpUrl = $phpUrl . '/';
                
                $phpUrl = $phpUrl . 'cron/runOneKpiDataMart/'.$startIndicatorId.'/'.$fileName;
                
                $this->ws->curlQueue($phpUrl);
                
                file_put_contents($tempdir.'/url.log', $phpUrl);
                
            } else {
                $result = array('status' => 'error', 'message' => 'Json файл үүсгэж чадсангүй');
                file_put_contents($tempdir.'/log.log', $result['message']);
            }
            
        } else {
            $result = array('status' => 'error', 'message' => 'Datamart төрөлтэй indicator олдсонгүй!');
            file_put_contents($tempdir.'/log.log', $result['message']);
        }
        
        return $result;
    }
    
    public function runOneKpiDataMartModel($startIndicatorId, $fileName) {
        
        $cacheTmpDir = Mdcommon::getCacheDirectory();
        $jsonFile    = $cacheTmpDir . '/kpidatamart/'.$fileName.'.json';
        $response    = array('status' => 'success');
        
        if (file_exists($jsonFile)) {
            
            $fileArr = json_decode(file_get_contents($jsonFile), true);
            
            if (isset($fileArr[$startIndicatorId])) {
                
                $rowConfig = $fileArr[$startIndicatorId];
                $kpiTypeId = $rowConfig['kpiTypeId'];
                
                self::$calcMartId = null;
                
                if (isset($rowConfig['calcMartId'])) {
                    self::$calcMartId = $rowConfig['calcMartId'];
                    self::updateKpiDmCalcMartModel(self::$calcMartId, false);
                }
                
                $startTime = microtime(true);
                    
                    if ($kpiTypeId == '1040') { 
                        $result = self::generateKpiRelationDataMartModel($startIndicatorId);
                    } elseif ($kpiTypeId == '1044') { 
                        $result = self::runAllKpiDataMartByIndicatorIdModel($startIndicatorId);
                    } else {
                        $result = array('status' => 'error', 'message' => 'Unknown kpi type id!');
                    }
                    
                $endTime = microtime(true);
                
                $durationSeconds = $endTime - $startTime;
                
                if ($result['status'] == 'success') {
                    $fileArr[$startIndicatorId] = array('kpiTypeId' => $kpiTypeId, 'status' => 'success', 'seconds' => $durationSeconds);
                } else {
                    $fileArr[$startIndicatorId] = array('kpiTypeId' => $kpiTypeId, 'status' => 'error', 'message' => $result['message']);
                }
                
                if (self::$calcMartId) {
                    self::updateKpiDmCalcMartModel(self::$calcMartId, true);
                    $fileArr[$startIndicatorId]['calcMartId'] = self::$calcMartId;
                }
                
                $isFileUpdate = @file_put_contents($jsonFile, json_encode($fileArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                
                if ($isFileUpdate) {
                    
                    $nextIndicatorId = Arr::getNextKeyArray($fileArr, $startIndicatorId);

                    if ($nextIndicatorId) {

                        $phpUrl = Config::getFromCache('PHP_URL');
                        $phpUrl = rtrim($phpUrl, '/');
                        $phpUrl = $phpUrl.'/';

                        $this->ws->curlQueue($phpUrl . 'cron/runOneKpiDataMart/'.$nextIndicatorId.'/'.$fileName);
                    }
                    
                } else {
                    $response = array('status' => 'error', 'message' => 'Json файл үүсгэж чадсангүй');
                    file_put_contents($cacheTmpDir . '/kpidatamart/log-one.log', $response['message']);
                }
                
            } else {
                $response = array('status' => 'error', 'message' => 'Indicator not found - '.$startIndicatorId);
                file_put_contents($cacheTmpDir . '/kpidatamart/log-one.log', $response['message']);
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'File not found - '.$jsonFile);
            file_put_contents($cacheTmpDir . '/kpidatamart/log-one.log', $response['message']);
        }
        
        return $response;
    }
    
    public function updateKpiDmCalcMartModel($calcMartId, $isLastDate = false) {
        
        try {
            
            if ($isLastDate == false) {
                $updateData = array('FIRST_LOAD_TIME' => Date::currentDate());
            } else {
                $updateData = array('LAST_LOAD_TIME' => Date::currentDate());
            }
            
            $this->db->AutoExecute('KPI_DM_CALC_MART', $updateData, 'UPDATE', 'ID = '.$calcMartId);
            $response = array('status' => 'success');
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function runAllKpiDataMartByIndicatorIdModel($indicatorId) {
        
        $idPh = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT
                ID,
                NAME,
                KPI_TYPE_ID, 
                RANK() OVER(ORDER BY RNUM DESC) AS EXECUTE_RANK
            FROM
            (
                SELECT 
                    ID, 
                    NAME, 
                    KPI_TYPE_ID, 
                    MAX(RNUM) AS RNUM 
                FROM
                (
                    SELECT
                        1 AS RNUM, 
                        ID AS PARENT_ID, 
                        ID, 
                        NAME, 
                        KPI_TYPE_ID 
                    FROM KPI_INDICATOR
                    WHERE ID = $idPh 
                        AND KPI_TYPE_ID IN (1040, 1044) 
                        AND DELETED_USER_ID IS NULL 
                        
                    UNION 
                    
                    SELECT
                        1 + ROWNUM AS RNUM, 
                        PARENT_ID, 
                        ID, 
                        NAME, 
                        KPI_TYPE_ID 
                    FROM
                    (
                        SELECT
                            KI.ID AS PARENT_ID, 
                            SKI.ID, 
                            SKI.NAME, 
                            SKI.KPI_TYPE_ID
                        FROM KPI_INDICATOR KI
                            INNER JOIN KPI_DATAMODEL_MAP_KEY MK ON KI.ID = MK.MAIN_INDICATOR_ID 
                            INNER JOIN KPI_INDICATOR SKI ON MK.SRC_INDICATOR_ID = SKI.ID 
                        WHERE KI.KPI_TYPE_ID IN (1040, 1044) 
                            AND SKI.KPI_TYPE_ID IN (1040, 1044) 
                            AND KI.DELETED_USER_ID IS NULL 
                            
                        UNION 
                        
                        SELECT
                            KI.ID AS PARENT_ID, 
                            TKI.ID, 
                            TKI.NAME, 
                            TKI.KPI_TYPE_ID 
                        FROM KPI_INDICATOR KI 
                            INNER JOIN KPI_DATAMODEL_MAP_KEY MK ON KI.ID = MK.MAIN_INDICATOR_ID 
                            INNER JOIN KPI_INDICATOR TKI ON MK.TRG_INDICATOR_ID = TKI.ID 
                        WHERE KI.KPI_TYPE_ID IN (1040, 1044) 
                            AND TKI.KPI_TYPE_ID IN (1040, 1044) 
                            AND KI.DELETED_USER_ID IS NULL 
                    )
                    START WITH PARENT_ID = $idPh 
                    CONNECT BY NOCYCLE PRIOR ID = PARENT_ID
                ) 
                GROUP BY 
                    ID, 
                    NAME, 
                    KPI_TYPE_ID 
            )", 
            array($indicatorId)
        );
        
        $result = $calculateCheck = array();
        $isThroughCalculate = Input::numeric('isThroughCalculate');
        
        if ($isThroughCalculate) {
            
            $calculateIndicators = self::getKpiIndicatorMapModel($indicatorId, '10000013');
            
            if ($calculateIndicators) {
                $data = array_merge_recursive($calculateIndicators, $data);
            }
        }
        
        foreach ($data as $row) {
            
            if (isset($calculateCheck[$row['ID']])) {
                continue;
            }
            
            $calculateCheck[$row['ID']] = 1;
            
            if ($row['KPI_TYPE_ID'] == '1040') {
                $response = self::generateKpiRelationDataMartModel($row['ID']);
            } elseif ($row['KPI_TYPE_ID'] == '1044') {
                $response = self::generateKpiRawDataMartModel($row['ID']);
            }
            
            if ($row['ID'] == $indicatorId) {
                $result = $response;
            }
            
            Mdform::clearCacheData($row['ID']);
        }
        
        return $result;
    }

    public function getKpiDataMartRelationColumnsWithParentInputModel($id) {
        
        $row = $this->db->GetRow("
            SELECT 
                * 
            FROM KPI_INDICATOR 
            WHERE ID = ".$this->db->Param(0), 
            array($id)
        );
        
        return $row;
    }        

    public function getKpiDataMartRelationColumnsWithInputModel($mainIndicatorId) {
        
        $idPh1 = $this->db->Param(0);
        
        $data = $this->db->GetAll("
            SELECT 
                KIIM.ID, 
                KI.TABLE_NAME AS SRC_TABLE_NAME, 
                KIIM.LABEL_NAME, 
                UPPER(KIIM.COLUMN_NAME) AS SRC_COLUMN_NAME, 
                KIIM.AGGREGATE_FUNCTION, 
                KIIM.EXPRESSION_STRING, 
                KIIM.EVENT_STRING, 
                KIIM.EVENT_EXPRESSION_STRING, 
                KIIM.BLOCK_DIAGRAM, 
                KIIM.TRG_ALIAS_NAME, 
                MST.NAME AS SEMANTIC_TYPE_NAME, 
                KIIM.SRC_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_ID, 
                KIIM.TRG_INDICATOR_MAP_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                INNER JOIN KPI_INDICATOR KI ON KIIM.MAIN_INDICATOR_ID = KI.ID
                INNER JOIN META_SEMANTIC_TYPE MST ON KIIM.SEMANTIC_TYPE_ID = MST.ID 
            WHERE KIIM.MAIN_INDICATOR_ID = $idPh1  
                AND KIIM.IS_INPUT = 1
            ORDER BY KIIM.ORDER_NUMBER ASC", array($mainIndicatorId));
        
        return $data;
    }    

    public function getFlowClientExpressionModel($templateId) {
        
        $data = $this->db->GetAll("
            SELECT 
                IIM.COLUMN_NAME, 
                IIM.COLUMN_NAME_PATH, 
                IIF.* 
            FROM KPI_INDICATOR_INDICATOR_MAP IIM
                INNER JOIN KPI_INDICATOR_FLOWCHART_MAP IIF ON IIF.MAP_ID = IIM.ID
            WHERE IIM.MAIN_INDICATOR_ID = ".$this->db->Param(0), 
            array($templateId)
        );
        
        return $data;
    }    

    public function indicatorParameterListModel($mainIndicatorId, $crudIndicatorId) {

        $crudInfo = self::getKpiDataMartRelationColumnsWithParentInputModel($crudIndicatorId);
        $doneBpList = self::getKpiDataMartRelationColumnsWithInputModel($crudInfo['PARENT_ID']);
        $metaDatas = self::getKpiDataMartRelationColumnsWithInputModel($mainIndicatorId);
        $html = array();

        foreach ($metaDatas as $row) {
            
            $doneBpId = '';
            $doneBpParamIsInput = 0;
            $doneBpParamPath = '';
            $metaProcessParamLinkId = $row['ID'];
            $defaultValue = '';

            $html[] = '<tr>';
            $html[] = '<td class="middle">';
            $html[] = $row['LABEL_NAME'];
            $html[] = Form::hidden(array('name' => 'srcId[]', 'class' => 'id', 'value' => $metaProcessParamLinkId));
            $html[] = '</td>';
            $html[] = '<td>';
            $html[] = Form::text(array('name' => 'srcPath[]', 'id' => '', 'value' => $row['SRC_COLUMN_NAME'], 'class' => 'form-control form-control-sm', 'readonly' => 'readonly'));
            $html[] = '</td>';
            $html[] = '<td><div class="d-flex"><i class="far fa-arrow-alt-right mt5 ml3" style="font-size: 16px;"></i> ';
            $html[] = Form::select(array(
                'name' => 'trgId[]',
                'id' => '',
                'class' => 'form-control form-control-sm ml10',
                'data' => $doneBpList,
                'op_value' => 'ID',
                'op_text' => 'LABEL_NAME|-|SRC_COLUMN_NAME',
                'data-placeholder' => '...',
                'op_custom_attr' => array(array(
                    'attr' => 'data-trgpath',
                    'key' => 'SRC_COLUMN_NAME'
                )),
                'value' => $doneBpId,
                'text' => ' ',
                'required' => 'required'
            ));
            $html[] = '</div></td>';        
            $html[] = '</tr>';
        }
        
        return implode('', $html);
    }    

    public function checkuxFlowUserPermissionModel($srcId) {
        
        $idPh = $this->db->Param(0);
        $userIdPh = $this->db->Param(1);
        
        $data = $this->db->GetRow("
        SELECT 
            INDICATOR_ID 
        FROM UM_PERMISSION_KEY 
        WHERE INDICATOR_ID = $idPh
            AND (USER_ID = $userIdPh 
            OR CREATED_USER_ID = $userIdPh 
            OR ROLE_ID IN (SELECT ROLE_ID FROM UM_USER_ROLE WHERE USER_ID = $userIdPh))", 
            array($srcId, Ue::sessionUserKeyId())
        );
        
        return $data ? true : false;
    }    

    public function getKpiUxFlowSaveMappingModel($uxFlowIndicatorId, $uxFlowActionIndicatorId, $saveData) {

        $newRow = [];
        
        $uxflowMaps = (new Mdexpression())->uxFlowExpressionMapping($uxFlowIndicatorId, $uxFlowActionIndicatorId);

        foreach ($uxflowMaps as $rrr) {
            $newRow[$rrr['srcPath']] = $saveData[$rrr['trgPath']];
        }
        
        return $newRow;
    }    
    
    public function getKpiIndicatorMetaInfoModel($indicatorId) {
        
        try {
            
            $hdrRow = self::getKpiIndicatorRowModel($indicatorId);
            
            if ($hdrRow) {
                
                $detailData = array(
                    'id' => $hdrRow['ID'],
                    'code' => $hdrRow['CODE'],
                    'name' => $hdrRow['NAME'],
                    'kpiTypeId' => $hdrRow['KPI_TYPE_ID'],
                    'description' => $hdrRow['DESCRIPTION']
                );
                
                $row = self::getIndicatorAdditionalInfoModel($hdrRow['KPI_TYPE_ID'], $indicatorId);
                
                if ($row) {
                    $detailData['relatedIndicatorId'] = $row['RELATED_INDICATOR_ID'];
                    $detailData['metaInfo'] = $row;
                }
                
                $response = array('status' => 'success', 'detailData' => $detailData);
            } else {
                throw new Exception('Not found indicator!');
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        return $response;
    }
    
    public function getIndicatorAdditionalInfoModel($kpiTypeId, $indicatorId) {
        
        try {
            
            $idPh1 = $this->db->Param(0);
            $configRow = $this->db->GetRow("
                SELECT 
                    KI.TABLE_NAME, 
                    KT.RELATED_INDICATOR_ID 
                FROM KPI_TYPE KT 
                    INNER JOIN KPI_INDICATOR KI ON KI.ID = KT.RELATED_INDICATOR_ID 
                WHERE KT.ID = $idPh1", 
                array($kpiTypeId)
            );
            $row = array();
            
            if ($configRow) {
                $row = $this->db->GetRow("SELECT * FROM ".$configRow['TABLE_NAME']." WHERE SRC_RECORD_ID = $idPh1", array($indicatorId));
                $row['RELATED_INDICATOR_ID'] = $configRow['RELATED_INDICATOR_ID'];
            }
            
            return $row;
        
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getIndicatorDynamicTblDataModel($indicatorId) {
        
        try {
            
            $idPh1 = $this->db->Param(0);
        
            $tableName = $this->db->GetOne("
                SELECT 
                    KD.TABLE_NAME 
                FROM KPI_INDICATOR KI 
                    INNER JOIN KPI_TYPE KT ON KT.ID = KI.KPI_TYPE_ID 
                    INNER JOIN KPI_INDICATOR KD ON KD.ID = KT.RELATED_INDICATOR_ID
                WHERE KI.ID = $idPh1", 
                array($indicatorId)
            );

            $dataJson = $this->db->GetOne("SELECT DATA FROM $tableName WHERE SRC_RECORD_ID = $idPh1", array($indicatorId));
            $data = @json_decode($dataJson, true);

            return $data;
        
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getMetaWidgetModel($widgetId) {
        $code = $this->db->GetOne("SELECT CODE FROM META_WIDGET WHERE ID = ".$this->db->Param(0), array($widgetId));
        return $code;
    }
    
    public function getExcelTemplateByKpiTypeModel($indicatorId) {
        
        try {
            
            $idPh1 = $this->db->Param(0);
        
            $data = $this->db->GetAll("
                SELECT 
                    T1.ID, 
                    T1.CODE, 
                    T1.NAME 
                FROM KPI_INDICATOR T1 
                    INNER JOIN KPI_INDICATOR T0 ON T0.KPI_TYPE_ID = T1.TYPE_ID 
                WHERE T0.ID = $idPh1 
                    AND T1.KPI_TYPE_ID = 1001", 
                array($indicatorId)
            );

            return $data;
        
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function kpiIndicatorExcelImportModel() {
        
        try {
            
            if (!empty($_FILES['excelFile']['name'])) { 
                
                set_time_limit(0);
                ini_set('memory_limit', '-1');

                $fileName  = $_FILES['excelFile']['name'];
                $tmpName   = $_FILES['excelFile']['tmp_name'];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($extension, array('xls', 'xlsx'))) {
                    throw new Exception("The extension '$extension' is invalid!");
                }

                if (!FileUpload::checkContentType($fileName, $tmpName)) {
                    throw new Exception('ContentType is invalid!');
                }
                
                if ($extension == 'xlsx') {
                
                    includeLib('Office/Excel/simplexlsx/SimpleXLSX');

                    $xlsx = SimpleXLSX::parse($tmpName);
                    $sheetNames = $xlsx->sheetNames();

                } else {

                    includeLib('Office/Excel/simplexlsx/SimpleXLS');

                    $xlsx = SimpleXLS::parse($tmpName);
                    $sheetNames = $xlsx->sheets;
                }
                
                $headerSheetName = Input::post('sheetName');
                $headerSheetNameLower = Str::lower($headerSheetName);
                
                foreach ($sheetNames as $sheetKey => $sheetName) {
                
                    $sheetNameLower = Str::lower($sheetName);

                    if ($sheetNameLower == $headerSheetNameLower) {
                        $sheetIndex = $sheetKey;
                        break;
                    }
                }
                
                if (isset($sheetIndex)) {
                    
                    $rows      = $xlsx->rows($sheetIndex);
                    
                    $rowNumber = Input::numeric('rowNumber') - 1;
                    $rows      = array_slice($rows, $rowNumber, null, true);
                    
                    if ($rows) {
                        
                        $templateId  = Input::numeric('templateId');
                        $indicatorId = Input::numeric('indicatorId');
                        
                        $configs     = self::getIndicatorExcelImportConfigModel($templateId);
                        
                        if ($configs) {
                            
                            $importData = $checkUniqueData = $columnConfig = $comboDatas = array();
                            $structureIndicatorId = $configs[0]['STRUCTURE_INDICATOR_ID'];
                            
                            $configRow = self::getKpiIndicatorRowModel($structureIndicatorId);
                            $configRow['isIgnoreStandardFields'] = true;
                            
                            unset($configRow['NAME_PATTERN']);

                            $columnsData = self::getKpiIndicatorColumnsModel($structureIndicatorId, $configRow); 
                            $fieldConfig = self::getKpiIndicatorIdFieldModel($structureIndicatorId, $columnsData);
                            
                            $idField = $fieldConfig['idField'];
                            
                            foreach ($columnsData as $columnsRow) {
                        
                                $columnName = $columnsRow['COLUMN_NAME'];

                                if ($columnName) {
                                    $columnConfig[$columnName] = $columnsRow;
                                }
                            }
                            
                            unset($_POST);
                            
                            $_POST['kpiMainIndicatorId'] = $structureIndicatorId;
                            $_POST['kpiDataTblName'] = $configRow['TABLE_NAME'];
                            $_POST['kpiTblIdField'] = $idField;
                            $_POST['isExcelImport'] = 1;

                            $this->db->BeginTrans(); 
                            
                            foreach ($rows as $n => $row) {
                                
                                $n = $n + 1;
                                
                                $rowData = array();
                                $isIndicatorName = false;
                                
                                unset($_POST['kpiTbl']);
                                
                                foreach ($configs as $configRow) {
                                
                                    $trgAliasName     = $configRow['TRG_ALIAS_NAME'];
                                    $numberColumnName = alphaToNum($trgAliasName) - 1;
                                    $columnName       = $configRow['COLUMN_NAME'];
                                    
                                    if ($columnName == 'INDICATOR_NAME') {
                                        $isIndicatorName = true;
                                    }
                                    
                                    if (isset($row[$numberColumnName])) {
                                        
                                        $showType = $configRow['SHOW_TYPE'];
                                        $cellVal  = Input::param($row[$numberColumnName]);
                                        
                                        if ($columnName == 'INDICATOR_NAME' && $cellVal == '') {
                                            continue 2;
                                        }
                                        
                                        if ($showType == 'decimal' || $showType == 'bigdecimal') {
                                
                                            $cellVal = Number::decimal($cellVal);

                                        } elseif ($showType == 'date') {

                                            $cellVal = Date::formatter($cellVal, 'Y-m-d');

                                        } elseif ($showType == 'combo' || $showType == 'popup' || $showType == 'radio') {
                                            
                                            $colConfig = $columnConfig[$columnName]; 
                                            $lookupMetaDataId = $configRow['TRG_TABLE_NAME'] ? $configRow['TRG_TABLE_NAME'] : $configRow['LOOKUP_META_DATA_ID'];

                                            if (!isset($comboDatas[$lookupMetaDataId])) {

                                                /*if ($configRow['TRG_TABLE_NAME']) {

                                                    $colConfig['isData'] = true;
                                                    $colConfig['FILTER_INDICATOR_ID'] = $colConfig['LOOKUP_META_DATA_ID'];

                                                    $datas = self::getKpiComboDataModel($colConfig);

                                                } else {

                                                    $lookupCriteria = $colConfig['LOOKUP_CRITERIA'];
                                                    $lookupCriteria = $lookupCriteria . '&indicatorId=' . $lookupMetaDataId;
                                                    $datas = self::getComboKpiModel('1642414747737029', $lookupCriteria);
                                                }*/
                                                
                                                $colConfig['isData'] = true;
                                                $colConfig['FILTER_INDICATOR_ID'] = $configRow['LOOKUP_META_DATA_ID'];

                                                $datas = self::getKpiComboDataModel($colConfig);

                                                $comboRows = array();
                                                $dataRows = $datas['data'];
                                                $dataId = $datas['id'];
                                                $dataName = $datas['name'];

                                                if (isset($datas['data-name'])) {
                                                    $dataName = $datas['data-name'];
                                                }

                                                foreach ($dataRows as $dataRow) {
                                                    $comboRows[Str::lower(Str::remove_doublewhitespace($dataRow[$dataName]))] = array(
                                                        'id' => $dataRow[$dataId], 
                                                        'name' => $dataRow[$dataName]
                                                    );
                                                }

                                                $comboDatas[$lookupMetaDataId] = $comboRows;
                                            }

                                            $cellValLower = Str::lower(Str::remove_doublewhitespace($cellVal));

                                            if (isset($comboDatas[$lookupMetaDataId][$cellValLower])) {

                                                $comboRow = $comboDatas[$lookupMetaDataId][$cellValLower];

                                                $_POST['kpiTbl'][$columnName.'_DESC'] = $comboRow['name'];

                                                $cellVal = $comboRow['id'];

                                            } elseif ($cellVal != '') {

                                                $this->db->RollbackTrans();
                                                throw new Exception("'$n' мөрний '$columnName' баганын '$cellVal' утга олдсонгүй!");
                                            }
                                        }
                                        
                                        $rowData[$columnName] = $cellVal;
                                        $_POST['kpiTbl'][$columnName] = $cellVal;
                                    }
                                }
                                
                                if ($isIndicatorName == false) {
                                    
                                    $this->db->RollbackTrans();
                                    throw new Exception('INDICATOR_NAME багана олдсонгүй!');
                                }
                                
                                $checkUniqueStr = implode('', $rowData);
                                
                                if ($checkUniqueStr && !isset($checkUniqueData[$checkUniqueStr])) {
                                    
                                    $importData[] = $rowData;
                                    $checkUniqueData[$checkUniqueStr] = 1;
                                    
                                    self::$uniqIdIndex = $n;
                                    $sourceRecordId = getUIDAdd($n);
                                    
                                    $result = self::saveKpiDynamicDataModel($sourceRecordId);
                                    
                                    if ($result['status'] != 'success') {
                            
                                        $this->db->RollbackTrans();
                                        throw new Exception($result['message']);
                                        
                                    } else {
                                        
                                        self::createKpiIndicatorByImport(array(
                                            'ID'          => $sourceRecordId, 
                                            'NAME'        => $rowData['INDICATOR_NAME'], 
                                            'PARENT_ID'   => $indicatorId, 
                                            'KPI_TYPE_ID' => $configs[0]['KPI_TYPE_ID'], 
                                            'rowNumber'   => $n
                                        ));
                                    }
                                }
                            }
                            
                            if (!$importData) {
                                
                                $this->db->RollbackTrans();
                                throw new Exception('Импорт хийх Indicator олдсонгүй!');
                                
                            } else {
                                
                                $this->db->CommitTrans();
                                
                                $response = array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));
                            }
                            
                        } else {
                            throw new Exception('Баганын тохиргоо олдсонгүй!');
                        }
                        
                    } else {
                        throw new Exception('Өгөгдөл олдсонгүй!');
                    }
                    
                } else {
                    throw new Exception($headerSheetName.' гэсэн sheet нэр олдсонгүй!');
                }
                
            } else {
                throw new Exception('Please select excel file!');
            }
            
        } catch(Exception $ex) {
            
            $this->db->RollbackTrans();
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function createKpiIndicatorByImport($arr) {
        
        try {
            
            $data = array(
                'ID'              => $arr['ID'],
                'CODE'            => $arr['ID'],
                'NAME'            => $arr['NAME'],
                'PARENT_ID'       => $arr['PARENT_ID'], 
                'KPI_TYPE_ID'     => $arr['KPI_TYPE_ID'], 
                'CREATED_DATE'    => Date::currentDate(), 
                'CREATED_USER_ID' => Ue::sessionUserKeyId()
            );
            
            $result = $this->db->AutoExecute('KPI_INDICATOR', $data);
            
            if ($result) {
                
                $categoryMap = $this->db->GetAll("
                    SELECT 
                        CATEGORY_ID 
                    FROM KPI_INDICATOR_CATEGORY 
                    WHERE INDICATOR_ID = ".$this->db->Param(0), 
                    array($data['PARENT_ID'])
                );
                
                foreach ($categoryMap as $c => $categoryRow) {
                    
                    $categoryData = array(
                        'ID'              => getUIDAdd($arr['rowNumber'] + $c), 
                        'CATEGORY_ID'     => $categoryRow['CATEGORY_ID'], 
                        'INDICATOR_ID'    => $data['ID'],
                        'CREATED_DATE'    => Date::currentDate(), 
                        'CREATED_USER_ID' => Ue::sessionUserKeyId()
                    );

                    $this->db->AutoExecute('KPI_INDICATOR_CATEGORY', $categoryData);
                }
            }
            
            $response = array('status' => 'success');
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function getIndicatorExcelImportConfigModel($indicatorId) {
        
        $data = $this->db->GetAll("
            SELECT 
                UPPER(T0.COLUMN_NAME) AS COLUMN_NAME, 
                UPPER(T0.TRG_ALIAS_NAME) AS TRG_ALIAS_NAME, 
                T3.SHOW_TYPE, 
                T3.TRG_INDICATOR_ID AS LOOKUP_META_DATA_ID, 
                T4.TABLE_NAME AS TRG_TABLE_NAME, 
                T2.RELATED_INDICATOR_ID AS STRUCTURE_INDICATOR_ID, 
                T2.ID AS KPI_TYPE_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP T0
                INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.MAIN_INDICATOR_ID 
                INNER JOIN KPI_TYPE T2 ON T2.ID = T1.TYPE_ID 
                INNER JOIN KPI_INDICATOR_INDICATOR_MAP T3 ON T3.MAIN_INDICATOR_ID = T2.RELATED_INDICATOR_ID 
                    AND LOWER(T3.COLUMN_NAME) = LOWER(T0.COLUMN_NAME) 
                LEFT JOIN KPI_INDICATOR T4 ON T4.ID = T3.TRG_INDICATOR_ID 
            WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
            ORDER BY T0.TRG_ALIAS_NAME ASC", 
            array($indicatorId)
        );
        
        return $data;
    }
    
    public function generateKpiDataMartFromStatementModel($mainIndicatorId, $dataIndicatorId) {
        
        $response = self::runAllKpiDataMartByIndicatorIdModel($dataIndicatorId);
        
        $srcStatementIdPh = $this->db->Param(0);
        
        $childData = $this->db->GetAll("
            SELECT 
                T0.TRG_INDICATOR_ID AS STATEMENT_META_ID, 
                T2.DATA_INDICATOR_ID AS DATA_VIEW_ID, 
                T3.ID AS MART_INDICATOR_ID, 
                T3.KPI_TYPE_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                INNER JOIN META_STATEMENT_LINK T2 ON T2.MAIN_INDICATOR_ID = T0.TRG_INDICATOR_ID 
                INNER JOIN KPI_INDICATOR T3 ON T3.ID = T2.DATA_INDICATOR_ID 
            WHERE T0.SRC_INDICATOR_ID = $srcStatementIdPh 
                AND T0.SEMANTIC_TYPE_ID = 28 
                AND T0.TRG_INDICATOR_ID <> $srcStatementIdPh 
                AND T3.KPI_TYPE_ID IN (1040, 1044) 
            GROUP BY 
                T0.TRG_INDICATOR_ID, 
                T2.DATA_INDICATOR_ID, 
                T3.ID, 
                T3.KPI_TYPE_ID, 
                T0.ORDER_NUMBER  
            ORDER BY T0.ORDER_NUMBER ASC", 
            array($mainIndicatorId)
        );
        
        if ($childData) {
            
            foreach ($childData as $childRow) {
                
                if ($childRow['KPI_TYPE_ID'] == '1040') { 
                    $response = self::generateKpiRelationDataMartModel($childRow['MART_INDICATOR_ID']);
                } elseif ($childRow['KPI_TYPE_ID'] == '1044') { 
                    $response = self::runAllKpiDataMartByIndicatorIdModel($childRow['MART_INDICATOR_ID']);
                } 
                
                if ($response['status'] != 'success') {
                    
                    return $response;
                }
            }
        }
        
        return $response;
    }
    
    public function getFillPositionDataModel($indicatorId, $positionConfigs) {
        
        try {
            
            $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, 10000015);
            $fillData = array();

            foreach ($fillMapRows as $fillMapRow) {

                $mapId        = $fillMapRow['MAP_ID'];
                $typeInfoData = self::getIndicatorAdditionalInfoModel(2015, $mapId);

                if (isset($typeInfoData['DATA']) && $fillMapRow['TABLE_NAME']) {

                    $dataJson = @json_decode($typeInfoData['DATA'], true);

                    if (isset($dataJson['RELATION_DTL'])) {

                        $relationDtls = $dataJson['RELATION_DTL'];
                        $where = '';

                        foreach ($relationDtls as $relationDtl) {

                            $src = $relationDtl['SRC'];
                            $trg = $relationDtl['TRG'];
                            $defaultValue = $relationDtl['DEFAULT_VALUE'];
                            
                            if ($trg && $defaultValue != '') {
                                
                                $where .= $trg . ' = ' . Mdmetadata::setDefaultValue($defaultValue) . ' AND ';
                            }
                        }
                        
                        $where = rtrim(trim($where), 'AND');
                        
                        try {
                            
                            $rowData = $this->db->GetRow("SELECT * FROM ".$fillMapRow['TABLE_NAME']." WHERE $where");
                            
                            unset($rowData['DATA']);
                            
                            $fillData = array_merge($fillData, $rowData);
                            
                        } catch (Exception $ex) { }
                        
                    }
                }
            }

            $positionMapIds = Arr::implode_key(',', $positionConfigs, 'FIELD', true);
            $positionData = array();

            $positionFieldConfigs = $this->db->GetAll("
                SELECT 
                    ID, 
                    COLUMN_NAME, 
                    SHOW_TYPE, 
                    LABEL_NAME 
                FROM KPI_INDICATOR_INDICATOR_MAP 
                WHERE ID IN ($positionMapIds) 
                ORDER BY ORDER_NUMBER ASC");

            foreach ($positionConfigs as $positionConfig) {

                foreach ($positionFieldConfigs as $p => $positionFieldConfig) {

                    if ($positionConfig['FIELD'] == $positionFieldConfig['ID']) {

                        $positionFieldConfig['VALUE'] = issetDefaultVal($fillData[$positionFieldConfig['COLUMN_NAME']], $positionFieldConfig['LABEL_NAME']);
                        $positionData[$positionConfig['POSITION']] = $positionFieldConfig;

                        unset($positionFieldConfigs[$p]);
                        break;
                    }
                }
            }
            
            if ($rowId = issetParam($fillData['ID'])) {
                $positionData[0] = array('COLUMN_NAME' => 'ID', 'SHOW_TYPE' => 'long', 'VALUE' => $rowId);
            }
            
            return $positionData;
            
        } catch (Exception $ex) {
            return array();
        }
    }
    
    public function getDefaultFillDataModel($indicatorId) {
        
        try {
            
            $crudIndicatorId = issetVar($_POST['param']['crudIndicatorId']);
            
            if (Input::postCheck('fillSelectedRow') && $indicatorId = issetVar($_POST['param']['mainIndicatorId'])) {

                $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, 10000015, $crudIndicatorId);
                $fillSelectedRow = Arr::changeKeyLower(Input::post('fillSelectedRow'));
                $isFillRelation = true;
                
            } else {
                $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, 10000015);
            }
            
            $fillData = array();

            foreach ($fillMapRows as $fillMapRow) {
                
                $mapId        = $fillMapRow['MAP_ID'];
                $typeInfoData = self::getIndicatorAdditionalInfoModel(2015, $mapId);
                
                if (isset($typeInfoData['DATA'])) {

                    $dataJson = @json_decode($typeInfoData['DATA'], true);

                    if (isset($dataJson['RELATION_DTL']) 
                        && ($fillMapRow['TABLE_NAME'] || $fillMapRow['SRC_TABLE_NAME'] 
                                || $fillMapRow['QUERY_STRING'] || $fillMapRow['SRC_QUERY_STRING'])) {

                        $relationDtls = $dataJson['RELATION_DTL'];
                        
                        if ($GET_INDICATOR_ID = issetVar($_POST['param']['GET_INDICATOR_ID'])) {
                            $dataJson['GET_INDICATOR_ID'] = $GET_INDICATOR_ID;
                        }
                        
                        $where = '';

                        foreach ($relationDtls as $relationDtl) {

                            $src = strtolower(trim($relationDtl['SRC']));
                            
                            if ($src == 'reporttemplaterowspath') {
                                $setRowsPath = strtolower(trim($relationDtl['TRG']));
                                continue;
                            }
                                
                            $trg = trim($relationDtl['TRG']);
                            $defaultValue = trim($relationDtl['DEFAULT_VALUE']);
                            
                            if ($trg) {
                                
                                if ($defaultValue != '') {
                                    
                                    $where .= " AND $trg = " . Mdmetadata::setDefaultValue($defaultValue);
                                    
                                } elseif (isset($isFillRelation)) {
                                    
                                    if (array_key_exists($src, $fillSelectedRow)) {
                                        
                                        $equalVal = issetParam($fillSelectedRow[$src]);
                                        $where .= " AND $trg = '$equalVal'";
                                        
                                    } elseif (isset($fillSelectedRow[0])) {
                                        
                                        $equalVal = array();
                                        
                                        foreach ($fillSelectedRow as $selectedRow) {
                                            $equalVal[] = $selectedRow[$src];
                                        }
                                    }
                                }
                            }
                        }
                        
                        try {
                            
                            if (isset($dataJson['GET_INDICATOR_ID']) && $dataJson['GET_INDICATOR_ID']) {
                                
                                $getIndicatorRow = self::getKpiIndicatorRowModel($dataJson['GET_INDICATOR_ID']);
                                
                                if (!$getIndicatorRow) {
                                    throw new Exception($dataJson['GET_INDICATOR_ID'] . ' - GET_INDICATOR_ID тохиргоо олдсонгүй!');
                                }
                                
                                $isMethodWithParam = 0;
                                
                                if ($crudIndicatorId) {
                                    $isMethodWithParam = self::getKpiIndicatorMapChildCountModel($crudIndicatorId);
                                    Mdform::$methodStructureIndicatorId = $dataJson['GET_INDICATOR_ID'];
                                }
                                
                                if ($dataJson['GET_INDICATOR_ID'] == issetVar($_POST['param']['indicatorId']) || $isMethodWithParam) {
                                    
                                    if (!isset($equalVal)) {
                                        throw new Exception('Шүүх талбарын тохиргоо олдсонгүй!');
                                    }
                                    
                                    if (is_array($equalVal)) {
                                        
                                        $resultData = array();
                                        
                                        foreach ($equalVal as $eVal) {
                                            $getDetailData = self::getKpiIndicatorDetailDataModel($dataJson['GET_INDICATOR_ID'], $eVal, $trg);
                                            
                                            if ($detailData = issetParam($getDetailData['detailData'])) {
                                                $resultData[] = $detailData;
                                            }
                                        }
                                        
                                        return isset($setRowsPath) ? array($setRowsPath => $resultData) : $resultData;              
                                        
                                    } else {
                                        
                                        $getDetailData = self::getKpiIndicatorDetailDataModel($dataJson['GET_INDICATOR_ID'], $equalVal, $trg);
                                    
                                        if ($detailData = issetParam($getDetailData['detailData'])) {
                                            return isset($setRowsPath) ? array($setRowsPath => $detailData) : $detailData;                
                                        }
                                    }
                                }
                                
                                $fillMapTableName = $getIndicatorRow['TABLE_NAME'] ? $getIndicatorRow['TABLE_NAME'] : $fillMapRow['QUERY_STRING'];
                                
                            } else {
                                
                                $fillMapTableName = $fillMapRow['TABLE_NAME'] ? $fillMapRow['TABLE_NAME'] : $fillMapRow['SRC_TABLE_NAME'];
                            
                                if (!$fillMapTableName) {
                                    $fillMapTableName = '('.($fillMapRow['QUERY_STRING'] ? $fillMapRow['QUERY_STRING'] : $fillMapRow['SRC_QUERY_STRING']).')';
                                }
                            }
                            
                            $fillMapTableName = self::parseQueryString($fillMapTableName);
                            $queryNamedParams = DBSql::getQueryNamedParams($fillMapTableName);
                            
                            foreach ($queryNamedParams as $queryNamedParam) {
                                $fillMapTableName = str_ireplace($queryNamedParam, "''", $fillMapTableName);
                            }
                            
                            $rowData = $this->db->GetRow("SELECT * FROM $fillMapTableName WHERE 1 = 1 $where");
                            
                            if ($rowData) {
                                
                                $trgIndicatorId = (isset($isFillRelation) && $isFillRelation) ? issetVar($_POST['param']['indicatorId']) : $fillMapRow['ID'];
                                $dataJsonArr    = (isset($rowData['DATA']) && $rowData['DATA']) ? @json_decode($rowData['DATA'], true) : array();
                                
                                unset($rowData['DATA']);
                                
                                if (isset($isFillRelation)) {
                                    $tmpIndicatorId = $indicatorId;
                                    $indicatorId = $trgIndicatorId;
                                    $trgIndicatorId = $tmpIndicatorId;
                                    $linkIndicatorId = $indicatorId;
                                } else {
                                    $linkIndicatorId = $trgIndicatorId;
                                }
                                
                                $pathRelationConfigs = $this->db->GetAll("
                                    SELECT 
                                        LOWER(T0.ALIAS_NAME) AS ALIAS_NAME, 
                                        T0.COLUMN_NAME, 
                                        T0.SHOW_TYPE,
                                        T0.ID,
                                        T0.PARENT_ID, 
                                        T1.TRG_INDICATOR_ID, 
                                        T1.SEMANTIC_TYPE_ID, 
                                        T1.COLUMN_NAME AS TRG_COLUMN_NAME, 
                                        T1.SHOW_TYPE AS TRG_SHOW_TYPE,
                                        T1.ID AS TRG_ID,
                                        T1.PARENT_ID AS TRG_PARENT_ID 
                                    FROM (
                                        SELECT 
                                            CASE WHEN T1.TRG_ALIAS_NAME IS NULL 
                                                THEN T0.TRG_ALIAS_NAME 
                                            ELSE T1.TRG_ALIAS_NAME||'.'||T0.TRG_ALIAS_NAME 
                                            END AS ALIAS_NAME, 
                                            T0.ID,
                                            T0.COLUMN_NAME, 
                                            T0.SHOW_TYPE,
                                            T0.PARENT_ID, 
                                            T0.ORDER_NUMBER, 
                                            T0.TRG_INDICATOR_ID, 
                                            T0.SEMANTIC_TYPE_ID 
                                        FROM KPI_INDICATOR_INDICATOR_MAP T0 
                                            LEFT JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.ID = T0.PARENT_ID 
                                        WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                                            AND T0.TRG_ALIAS_NAME IS NOT NULL
                                    ) T0 
                                    INNER JOIN (
                                        SELECT 
                                            CASE WHEN T1.TRG_ALIAS_NAME IS NULL 
                                                THEN T0.TRG_ALIAS_NAME 
                                            ELSE T1.TRG_ALIAS_NAME||'.'||T0.TRG_ALIAS_NAME 
                                            END AS ALIAS_NAME, 
                                            T0.ID,
                                            T0.COLUMN_NAME, 
                                            T0.SHOW_TYPE,
                                            T0.PARENT_ID, 
                                            T0.TRG_INDICATOR_ID, 
                                            T0.SEMANTIC_TYPE_ID 
                                        FROM KPI_INDICATOR_INDICATOR_MAP T0 
                                            LEFT JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.ID = T0.PARENT_ID 
                                        WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(1)." 
                                            AND T0.TRG_ALIAS_NAME IS NOT NULL 
                                    ) T1 ON LOWER(T1.ALIAS_NAME) = LOWER(T0.ALIAS_NAME) 
                                    ORDER BY T0.PARENT_ID DESC, T0.ORDER_NUMBER ASC", 
                                    array($indicatorId, $trgIndicatorId)    
                                );
                                
                                foreach ($pathRelationConfigs as $p => $pathRelationConfig) {
                                    
                                    $srcColumnName = $pathRelationConfig['COLUMN_NAME'];
                                    $srcShowType = $pathRelationConfig['SHOW_TYPE'];
                                    $srcParentId = $pathRelationConfig['PARENT_ID'];
                                    
                                    $trgColumnName = $pathRelationConfig['TRG_COLUMN_NAME'];
                                    $trgShowType = $pathRelationConfig['TRG_SHOW_TYPE'];
                                    $trgParentId = $pathRelationConfig['TRG_PARENT_ID'];
                                    $mapIndicatorId = $pathRelationConfig['TRG_INDICATOR_ID'];
                                    $semanticTypeId = $pathRelationConfig['SEMANTIC_TYPE_ID'];
                                    $savedSubTableRows = array();
                                    
                                    if ($srcShowType != 'rows' && $srcParentId == '') {
                                        
                                        $fillData[$srcColumnName] = issetParam($rowData[$trgColumnName]);
                                        
                                        unset($pathRelationConfigs[$p]);
                                        
                                    } elseif ($srcShowType == 'rows') {
                                        
                                        if ($trgIndicatorId && $semanticTypeId == '10000002') {
                                            
                                            $srcRowId = $rowData['ID'];
                                            $savedSubTableRows = self::getKpiSubTableRowsModel($linkIndicatorId, $mapIndicatorId, $srcRowId, $trgColumnName);
                                            
                                        } elseif ($dataJsonArr) {
                                            
                                            $savedSubTableRows = issetParamArray($dataJsonArr[$trgColumnName]);
                                        }
                                        
                                        unset($pathRelationConfigs[$p]);
                                    }
                                    
                                    if ($savedSubTableRows) {
                                            
                                        $subRows = array();
                                        
                                        foreach ($savedSubTableRows as $s => $savedSubTableRow) {
                                            
                                            foreach ($pathRelationConfigs as $pathRelationConfigSub) {
                                                
                                                if ($pathRelationConfig['TRG_ID'] == $pathRelationConfigSub['TRG_PARENT_ID'] 
                                                    && isset($savedSubTableRow[$pathRelationConfigSub['TRG_COLUMN_NAME']])) {

                                                    $subRows[$s][$pathRelationConfigSub['COLUMN_NAME']] = $savedSubTableRow[$pathRelationConfigSub['TRG_COLUMN_NAME']];
                                                }
                                            }
                                        }
                                        
                                        if ($subRows) {
                                            $fillData[$srcColumnName] = $subRows;
                                        }
                                    }
                                }
                            }
                            
                        } catch (Exception $ex) { }
                        
                    }
                }
            }
            
            $response = $fillData;
            
        } catch (Exception $ex) {
            $response = array();
        }
        
        return $response;
    }
    
    public function getDynamictFillDataModel($indicatorId) {
        
        try {
            
            $crudIndicatorId = $_POST['param']['crudIndicatorId'];
            if (Input::postCheck('fillDynamicSelectedRow') && $indicatorId = issetVar($_POST['param']['mainIndicatorId'])) {

                $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, 10000009, issetVar($crudIndicatorId));
                $fillSelectedRow = Arr::changeKeyLower(Input::post('fillDynamicSelectedRow'));
                $isFillRelation = true;
                $getDetailData = self::getKpiIndicatorDetailDataModel($indicatorId, $fillSelectedRow['id']);
                Mdform::$kpiDmMart = issetParam($getDetailData['detailData']);                
                
            } else {
                $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, 10000009);
            }
            
            $getJsonConfig = $this->db->GetRow("
                SELECT 
                    T0.ID,
                    T0.COLUMN_NAME, 
                    T0.JSON_CONFIG, 
                    T0.SHOW_TYPE,
                    T0.PARENT_ID, 
                    T0.ORDER_NUMBER, 
                    T0.TRG_INDICATOR_ID, 
                    T0.SEMANTIC_TYPE_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP T0 
                    LEFT JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.ID = T0.PARENT_ID 
                WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(0)." AND T0.TRG_INDICATOR_ID = ".$this->db->Param(1),
                array($indicatorId, $crudIndicatorId)
            );          
            $getJsonConfigArr = json_decode($getJsonConfig['JSON_CONFIG'], true);
            
            $fillData = array();
            
            $sourceConfigs = $this->db->GetAll("
                SELECT 
                    T0.ID,
                    T0.COLUMN_NAME, 
                    T0.SHOW_TYPE,
                    T0.PARENT_ID, 
                    T0.ORDER_NUMBER, 
                    T0.TRG_INDICATOR_ID, 
                    T0.SEMANTIC_TYPE_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP T0 
                    LEFT JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.ID = T0.PARENT_ID 
                WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                ORDER BY T0.PARENT_ID DESC, T0.ORDER_NUMBER ASC", 
                array($getJsonConfigArr['source'])    
            );            
            $sourceConfigsKey = Arr::groupByArray($sourceConfigs, 'ID');
            
            $targetConfigs = $this->db->GetAll("
                SELECT 
                    T0.ID,
                    T0.COLUMN_NAME, 
                    T0.SHOW_TYPE,
                    T0.PARENT_ID, 
                    T0.ORDER_NUMBER, 
                    T0.TRG_INDICATOR_ID, 
                    T0.SEMANTIC_TYPE_ID 
                FROM KPI_INDICATOR_INDICATOR_MAP T0 
                    LEFT JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T1.ID = T0.PARENT_ID 
                WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                ORDER BY T0.PARENT_ID DESC, T0.ORDER_NUMBER ASC", 
                array($getJsonConfigArr['target'])    
            );
            $targetConfigsKey = Arr::groupByArray($targetConfigs, 'ID');

            $subRows = array();
            $detailTargetColName = '';
            
            if ($getJsonConfigArr) {
                foreach ($getJsonConfigArr['link'] as $p => $pathRelationConfig) {

                    if ($sourceConfigsKey && $sourceConfigsKey[$pathRelationConfig['sourceId']]['row']['PARENT_ID'] == '') {

                        $fillData[$targetConfigsKey[$pathRelationConfig['targetId']]['row']['COLUMN_NAME']] = Mdform::$kpiDmMart[$sourceConfigsKey[$pathRelationConfig['sourceId']]['row']['COLUMN_NAME']];

                    } elseif ($sourceConfigsKey && $sourceConfigsKey[$pathRelationConfig['sourceId']]['row']['PARENT_ID'] != '') {
                        $detailColName = $sourceConfigsKey[$sourceConfigsKey[$pathRelationConfig['sourceId']]['row']['PARENT_ID']]['row']['COLUMN_NAME'];
                        $detailTargetColName = $targetConfigsKey[$targetConfigsKey[$pathRelationConfig['targetId']]['row']['PARENT_ID']]['row']['COLUMN_NAME'];
                        $savedDetail = issetVar(Mdform::$kpiDmMart[$detailColName]);

                        if ($savedDetail) {

                            foreach ($savedDetail as $s => $savedSubTableRow) {
                                $subRows[$s][$targetConfigsKey[$pathRelationConfig['targetId']]['row']['COLUMN_NAME']] = $savedSubTableRow[$sourceConfigsKey[$pathRelationConfig['sourceId']]['row']['COLUMN_NAME']];
                            }

                        }
                    }
                }
            }
            
            if ($subRows) {
                $fillData[$detailTargetColName] = $subRows;
            }                        

            
            //pa($fillData);
            $response = $fillData;
            
        } catch (Exception $ex) {
            $response = array();
        }
        
        return $response;
    }
    
    public function getFieldValueFormatter($row, $rowData) {
        
        $columnName = $row['COLUMN_NAME'];
        $value = issetParam($rowData[$columnName]);
        
        if ($value != '') {
            
            $showType          = $row['SHOW_TYPE'];
            $lookupMetaDataId  = $row['LOOKUP_META_DATA_ID'];
            $filterIndicatorId = $row['FILTER_INDICATOR_ID'];
            $lookupCriteria    = $row['LOOKUP_CRITERIA'];
            
            switch ($showType) {        
        
                case 'check':
                case 'boolean':
                {
                    if ($value == '1' || $value == 'true') {
                        $value = 'Тийм';
                    }
                }
                break;

                case 'date':
                {
                    $value = Date::formatter($value, 'Y-m-d');
                }
                break;
                
                case 'popup':
                case 'radio':
                case 'combo':
                {
                    if ($lookupMetaDataId || $filterIndicatorId) {
                        
                        if (isset($rowData[$columnName]['id'])) {
                            $value = $rowData[$columnName]['name'];
                        } else {
                            
                            $value = issetParam($rowData[$columnName.'_DESC']);
                        
                            if ($value == '') {

                                if ($row['IS_SELECT_QUERY'] == '1' 
                                || $row['GROUP_CONFIG_PARAM_PATH'] != '' 
                                || $row['GROUP_CONFIG_FIELD_PATH'] != '') {

                                    $row['isData'] = true;

                                    if ($row['GROUP_CONFIG_PARAM_PATH'] != '') {
                                        $row['isData'] = false;
                                    }

                                    $row['rowId'] = $value;

                                    $datas = self::getKpiComboDataModel($row); 
                                    $value = issetParam($datas[0][$datas['name']]);

                                } else {

                                    if ($filterIndicatorId) {
                                        $lookupCriteria = $lookupCriteria . '&indicatorId=' . $filterIndicatorId;
                                        $lookupMetaDataId = '1642414747737029';
                                    } 

                                    $lookupCriteria .= '&id=' . $value;

                                    if ($row['CRITERIA_PATH']) {

                                        $criteriaPath = $row['CRITERIA_PATH'];

                                        $datas = array('data' => array(), 'id' => '', 'code' => '', 'name' => '');

                                        if (Mdform::$kpiDmMart) {

                                            $isEmpty = false;
                                            $criteriaPathArr = explode(',', $criteriaPath);

                                            foreach ($criteriaPathArr as $c => $criteriaPath) {

                                                if (!isset(Mdform::$kpiDmMart[$criteriaPath]) || (isset(Mdform::$kpiDmMart[$criteriaPath]) && Mdform::$kpiDmMart[$criteriaPath] == '')) {

                                                    $isEmpty = true;

                                                } elseif (isset(Mdform::$kpiDmMart[$criteriaPath]) && Mdform::$kpiDmMart[$criteriaPath] != '') {

                                                    $lookupCriteria .= '&criteria' . ($c + 1) . '=' . Mdform::$kpiDmMart[$criteriaPath];
                                                }
                                            }

                                            if ($isEmpty == false) {
                                                $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                                            }
                                        }

                                    } else {
                                        $datas = self::getComboKpiModel($lookupMetaDataId, $lookupCriteria);
                                    }

                                    $value = issetParam($datas['data'][0][$datas['name']]);
                                }
                            }
                        }
                    } 
                }
                break;       

                default:
                    $value = $value;
                break;    
            }
        }
        
        return $value;
    }
    
    public function getColumnDrillDownConfigModel() {
        
        try {
            
            $indicatorId = Input::numeric('indicatorId');
            $columnName  = Input::post('columnName');
            
            $data = $this->db->GetAll("
                SELECT 
                    T0.ID, 
                    T0.LINK_INDICATOR_ID, 
                    T0.LINK_META_DATA_ID, 
                    T0.SHOW_TYPE, 
                    T0.CRITERIA, 
                    T0.DIALOG_WIDTH, 
                    T0.DIALOG_HEIGHT, 
                    T2.KPI_TYPE_ID, 
                    T1.SRC_PARAM, 
                    T1.TRG_PARAM, 
                    T1.DEFAULT_VALUE, 
                    LOWER(T4.META_TYPE_CODE) AS META_TYPE_CODE 
                FROM META_DM_DRILLDOWN_DTL T0 
                    INNER JOIN META_DM_DRILLDOWN_PARAM T1 ON T1.DM_DRILLDOWN_DTL_ID = T0.ID 
                    LEFT JOIN KPI_INDICATOR T2 ON T2.ID = T0.LINK_INDICATOR_ID 
                    LEFT JOIN META_DATA T3 ON T3.META_DATA_ID = T0.LINK_META_DATA_ID 
                    LEFT JOIN META_TYPE T4 ON T4.META_TYPE_ID = T3.META_TYPE_ID 
                WHERE T0.MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND T0.MAIN_GROUP_LINK_PARAM = ".$this->db->Param(1), 
                array($indicatorId, $columnName)
            );
            
            if ($data) {
                return array('status' => 'success', 'data' => Arr::groupByArray($data, 'ID'));
            } else {
                return array('status' => 'error', 'message' => 'No config!');
            }
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => 'Error');
        }
    }
        
    public function updateJsonKpiIndicatorMapModel() {
        $jsonBody = file_get_contents('php://input');
        $param = json_decode($jsonBody, true);
        
        $this->db->UpdateClob("KPI_INDICATOR_INDICATOR_MAP", "JSON_CONFIG", json_encode($param['graphJson'], JSON_UNESCAPED_UNICODE), 'ID = '.Input::param($param['id']));
        
        return array('status' => 'success', 'message' => $this->lang->line('msg_save_success'));
    }
    
    public function getJsonKpiIndicatorMapModel() {
        $jsonBody = file_get_contents('php://input');
        $param = json_decode($jsonBody, true);
        
        $idPh = $this->db->Param('id');

        $bindVars = array(
            'id' => $this->db->addQ(Input::param($param['id']))
        );
        
        $row = $this->db->GetRow("
            SELECT 
                JSON_CONFIG
            FROM KPI_INDICATOR_INDICATOR_MAP
            WHERE ID = $idPh", $bindVars
        );

        return json_decode($row['JSON_CONFIG']);
    }
    
    public function getIndicatorSemanticMapCountModel($indicatorId, $semanticTypeId) {
        $data = $this->db->GetAll("
            SELECT 
                TRG_INDICATOR_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP 
            WHERE SRC_INDICATOR_ID = ".$this->db->Param(0)."
                AND SEMANTIC_TYPE_ID = ".$this->db->Param(1), 
            array($indicatorId, $semanticTypeId)
        );
        return $data;
    }
    
    public function getIndicatorMapBySemanticSavedIdsModel($srcIndicatorId, $srcRecordId, $structureMap) {
        
        $data = $this->db->GetAll("
            SELECT 
                T0.TRG_INDICATOR_ID AS ID, 
                T1.TRG_RECORD_ID 
            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.TRG_INDICATOR_ID 
                    AND T2.DELETED_USER_ID IS NULL 
                INNER JOIN META_DM_RECORD_MAP T1 ON T1.SRC_REF_STRUCTURE_ID = T0.SRC_INDICATOR_ID 
                    AND T1.TRG_REF_STRUCTURE_ID = T0.TRG_INDICATOR_ID 
                    AND T1.SRC_RECORD_ID = ".$this->db->Param(0)." 
            WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(1)." 
                AND T0.TRG_INDICATOR_ID IN (".Arr::implode_key(',', $structureMap, 'TRG_INDICATOR_ID', true).") 
                AND T0.SEMANTIC_TYPE_ID = ".$this->db->Param(2)." 
            ORDER BY T1.TRG_RECORD_ID ASC", 
            array($srcRecordId, $srcIndicatorId, 10000017)
        );
        
        return $data;
    }
    
    public function getIndicatorMapBySemanticSavedIdsAllModel($srcIndicatorId, $srcRecordId, $structureMap) {
        
        $langCode = Lang::getCode();
                            
        $data = $this->db->GetAll("
            SELECT 
                T0.TRG_INDICATOR_ID AS ID, 
                FNC_TRANSLATE('$langCode', T2.TRANSLATION_VALUE, 'NAME', T2.NAME) AS NAME, 
                T1.TRG_RECORD_ID, 
                T3.ID AS PARENT_ID, 
                FNC_TRANSLATE('$langCode', T3.TRANSLATION_VALUE, 'NAME', T3.NAME) AS PARENT_NAME 
            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.TRG_INDICATOR_ID 
                    AND T2.DELETED_USER_ID IS NULL 
                LEFT JOIN KPI_INDICATOR T3 ON T3.ID = T2.PARENT_ID     
                LEFT JOIN META_DM_RECORD_MAP T1 ON T1.SRC_REF_STRUCTURE_ID = T0.SRC_INDICATOR_ID 
                    AND T1.TRG_REF_STRUCTURE_ID = T0.TRG_INDICATOR_ID 
                    AND T1.SRC_RECORD_ID = ".$this->db->Param(0)." 
            WHERE T0.SRC_INDICATOR_ID = ".$this->db->Param(1)." 
                AND T0.TRG_INDICATOR_ID IN (".Arr::implode_key(',', $structureMap, 'TRG_INDICATOR_ID', true).") 
                AND T0.SEMANTIC_TYPE_ID = ".$this->db->Param(2)." 
            ORDER BY T0.ORDER_NUMBER ASC", 
            array($srcRecordId, $srcIndicatorId, 10000017)
        );
        
        return $data;
    }
    
    public function getRowsIndicatorByCriteriaModel($lookupId, $criteria, $idField, $codeField, $nameField) {
        
        unset($_POST);
        $_POST['indicatorId'] = $lookupId;
        $_POST['page']        = 1;
        $_POST['rows']        = 30;
        $_POST['criteria']    = $criteria;

        $result = self::indicatorDataGridModel();
        
        if (isset($result['rows'][0])) {
            $data = array();
            
            foreach ($result['rows'] as $row) {
                $name = html_entity_decode($row[$idField].'|'.$row[$codeField].'|'.$row[$nameField], ENT_QUOTES, 'UTF-8');
                array_push($data, array('codeName' => $name, 'row' => $row));	
            }
            
            return $data;
        }
        
        return array();
    }
    
    public function getRowIndicatorByCriteriaModel($lookupId, $criteria) {
        
        unset($_POST);
        $_POST['indicatorId'] = $lookupId;
        $_POST['page']        = 1;
        $_POST['rows']        = 1;
        $_POST['criteria']    = $criteria;

        $result = self::indicatorDataGridModel();
        
        if (isset($result['rows'][0])) {
            return $result['rows'][0];
        }
        
        return array();
    }
    
    public function getIndicatorIdByCodeModel($code) {
        return $this->db->GetOne("SELECT ID FROM KPI_INDICATOR WHERE LOWER(CODE) = ".$this->db->Param(0), array(Str::lower($code)));
    }
    
    public function getKpiDtlColModel($templateId) {
        try {
            return $this->db->GetAll("SELECT * FROM KPI_TEMPLATE_DTL_COL WHERE TEMPLATE_ID = ".$this->db->Param(0).' ORDER BY ORDER_NUM', array($templateId));
        } catch (Exception $ex) {
            return array();
        }        
    }
    
    public function getKpiDtlColCountFreezeModel($templateId) {
        try {
            return $this->db->GetOne("SELECT COUNT(ID) FROM KPI_TEMPLATE_DTL_COL WHERE TEMPLATE_ID = ".$this->db->Param(0).' AND IS_FREEZE = 1', array($templateId));
        } catch (Exception $ex) {
            return 0;
        }        
    }
    
    public function transferIndicatorActionModel() {
        
        $mainIndicatorId = Input::numeric('mainIndicatorId');
        $methodIndicatorId = Input::numeric('methodIndicatorId');
        
        $row = $this->getKpiIndicatorProcessModel($mainIndicatorId, $methodIndicatorId);
        
        if (isset($row[0])) {
            return array('status' => 'success', 'data' => $row[0]);
        } else {
            return array('status' => 'error', 'message' => 'No access!');
        }
    }
    
    public function mvGetExcelFileSheetModel() {
        
        if (!empty($_FILES['excelFile']['name'])) { 
            
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            
            $fileName = $_FILES['excelFile']['name'];
            $tmpName = $_FILES['excelFile']['tmp_name'];
            
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (!in_array($extension, array('xls', 'xlsx'))) {
                return array('status' => 'error', 'message' => "The extension '$extension' is invalid!");
            }
            
            if (!FileUpload::checkContentType($fileName, $tmpName)) {
                return array('status' => 'error', 'message' => 'ContentType is invalid!');
            }
            
            /*
            includeLib('Office/Excel/phpspreadsheet/vendor/autoload');
            
            $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
            $reader->setReadDataOnly(true);
            
            $spreadsheet = $reader->load($tmpName);
            $sheets = $spreadsheet->getSheetNames();
            */
            
            if ($extension == 'xlsx') {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLSX');
                
                $xlsx = SimpleXLSX::parse($tmpName);
                $sheetNames = $xlsx->sheetNames();
                
            } else {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLS');
                
                $xlsx = SimpleXLS::parse($tmpName);
                
                if (isset($xlsx->boundsheets[0]['name'])) {
                    
                    $boundsheets = $xlsx->boundsheets;
                    $sheetNames = array();
                    
                    foreach ($boundsheets as $b => $boundsheet) {
                        $sheetNames[$b] = $boundsheet['name'];
                    }
                } else {
                    $sheetNames = $xlsx->sheets;
                }
            }
            
            $response = array('status' => 'success', 'message' => 'Sheet сонгоно уу!', 'sheets' => $sheetNames);
        } else {
            $response = array('status' => 'error', 'message' => 'Please select excel file!');
        }
        
        return $response;
    }
    
    public function mvImportRowsExcelFileModel() {
        
        try {
            
            if (!empty($_FILES['excelFile']['name'])) { 
            
                set_time_limit(0);
                ini_set('memory_limit', '-1');

                $fileName = $_FILES['excelFile']['name'];
                $tmpName = $_FILES['excelFile']['tmp_name'];

                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($extension, array('xls', 'xlsx'))) {
                    return array('status' => 'error', 'message' => "The extension '$extension' is invalid!");
                }
                if (!FileUpload::checkContentType($fileName, $tmpName)) {
                    return array('status' => 'error', 'message' => 'ContentType is invalid!');
                }
                
                $sheetIndex = Input::numeric('sheetIndex');

                if ($extension == 'xlsx') {

                    includeLib('Office/Excel/simplexlsx/SimpleXLSX');

                    $xlsx = SimpleXLSX::parse($tmpName);
                    $sheetNames = $xlsx->sheetNames();

                } else {

                    includeLib('Office/Excel/simplexlsx/SimpleXLS');
                
                    $xlsx = SimpleXLS::parse($tmpName);

                    if (isset($xlsx->boundsheets[0])) {

                        $boundsheets = $xlsx->boundsheets;
                        $sheetNames = array();

                        foreach ($boundsheets as $b => $boundsheet) {
                            $sheetNames[$b] = $boundsheet['name'];
                        }
                    } else {
                        $sheetNames = $xlsx->sheets;
                    }
                }

                if (isset($sheetNames[$sheetIndex])) {

                    $rows = $xlsx->rows($sheetIndex);
                    
                    if (!isset($rows[0][0])) {
                        throw new Exception('Sheet дотор өгөгдөл олдсонгүй!');
                    }
                    
                    $response = array('status' => 'success', 'rows' => $rows);
                    
                    $indicatorId = Input::numeric('indicatorId');
                    $rowId       = Input::numeric('rowId');
                    
                    $configs = $this->db->GetAll("
                        SELECT 
                            CODE, 
                            SHOW_TYPE, 
                            COLUMN_NAME, 
                            COLUMN_NAME_PATH, 
                            IS_UNIQUE 
                        FROM KPI_INDICATOR_INDICATOR_MAP 
                        WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                            AND PARENT_ID = ".$this->db->Param(1)." 
                            AND ".$this->db->IfNull('IS_INPUT', '0')." = 1 
                            AND CODE IS NOT NULL 
                        ORDER BY CODE ASC", array($indicatorId, $rowId));
                    
                    foreach ($configs as $c => $configRow) {
                        $configs[$c]['colIndex'] = alphaToNum($configRow['CODE']);
                    }
                    
                    $response['configs'] = $configs;

                } else {
                    throw new Exception($sheetIndex.' гэсэн sheet нэр олдсонгүй!');
                }

            } else {
                throw new Exception('Please select excel file!');
            }

        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function createMvStructureFromFileModel() {
        
        try {
            
            $indicatorId = Input::numeric('indicatorId');
            $isOnlyTableCreate = Input::numeric('isOnlyTableCreate');
            $headerDatas = $_POST['headerData'];
            
            $configRow = self::getKpiIndicatorRowModel($indicatorId);
            
            if (!$configRow && Input::postCheck('name')) {
                
                $createIndicator = self::createIndicatorStructure($indicatorId);
                if ($createIndicator['status'] != 'success') {
                    return $createIndicator;
                }
                
                unset(self::$kpiIndicatorRow[$indicatorId]);
                $configRow = self::getKpiIndicatorRowModel($indicatorId);
            }
            
            $kpiDataTblName = $configRow['TABLE_NAME'];
            $schemaName = self::getKpiDbSchemaName($indicatorId);
            $tblName = $kpiDataTblName ? $kpiDataTblName : $schemaName . 'V_'.$indicatorId;
            $kpiTblIdField  = 'ID';
            $sessionUserKeyId = Ue::sessionUserKeyId();
            $sessionValues = Session::get(SESSION_PREFIX . 'sessionValues');
            $sessionName   = issetDefaultVal($sessionValues['sessionusername'], Ue::getSessionPersonWithLastName());
            
            if ($isOnlyTableCreate) {

                $isTblCreated = self::table_exists($this->db, $tblName);
                $dbField = $mapFields = $dbIndex = array();
                
                foreach ($headerDatas as $c => $headerData) {
                    
                    $columnName = 'C' . ($c + 1);
                    
                    if ($isTblCreated == false || ($isTblCreated && !isset($isTblCreated[$columnName]))) { 
                        
                        $rowField = array('type' => Input::param($headerData['showType']), 'name' => $columnName, 'labelName' => Input::param($headerData['labelName']));
                        
                        $dbField[] = $rowField;
                        $mapFields[] = $rowField;
                    } 
                }
                
                if ($isTblCreated == false) {

                    $createTblStatus = self::dbCreatedTblKpiDynamic($tblName, $dbField);

                    if ($createTblStatus['status'] == 'error') {
                        throw new Exception('Create table: ' . $createTblStatus['message']); 
                    } else {
                        self::updateKpiIndicatorTblName($indicatorId, $tblName);
                    }

                } else {

                    $standardFields = self::kpiDynamicTblStandardFields();

                    foreach ($standardFields as $standardField) { 

                        if (!isset($isTblCreated[$standardField['name']])) {

                            $dbField[] = array('type' => $standardField['type'], 'name' => $standardField['name']);
                        }
                    }

                    if ($dbField) {

                        $alterTblStatus = self::dbAlterTblKpiDynamic($tblName, $dbField);

                        if ($alterTblStatus['status'] == 'error') {
                            throw new Exception('Alter table: ' . $alterTblStatus['message']); 
                        }
                    }
                }
                
                if ($dbIndex) {
                    self::dbAlterTblKpiIndex($indicatorId, $tblName, $dbIndex);
                }
                
                if ($mapFields) {                    
                    
                    foreach ($mapFields as $k => $mapField) {
                        
                        $this->db->AutoExecute('KPI_INDICATOR_INDICATOR_MAP', array(
                            'ID'                => getUIDAdd($k), 
                            'ORDER_NUMBER'      => ($k + 1), 
                            'COLUMN_ORDER'      => ($k + 1), 
                            'COLUMN_NAME'       => $mapField['name'], 
                            'COLUMN_NAME_PATH'  => $mapField['name'], 
                            'CREATED_DATE'      => Date::currentDate(), 
                            'CREATED_USER_ID'   => $sessionUserKeyId,
                            'MAIN_INDICATOR_ID' => $indicatorId,
                            'SHOW_TYPE'         => $mapField['type'], 
                            'IS_RENDER'         => 1, 
                            'IS_INPUT'          => 1, 
                            'LABEL_NAME'        => $mapField['labelName']
                        ));
                    }
                }
                
            } else {
                
                $delimiter = Input::post('delimiter');
                $fileExtention = Input::post('fileExtention');
                $rowsDatas = $_POST['rowsData'];
                $evalRow = '';
                
                if ($delimiter == 'tab') {
                    $delimiter = "　";
                }
                
                foreach ($headerDatas as $c => $headerData) {

                    $columnName = 'C' . ($c + 1);
                    $showType = $headerData['showType']; 

                    if ($showType == 'decimal' || $showType == 'bigdecimal') {

                        $evalRow .= '$insertData[\''.$columnName.'\'] = self::cleanDecimal($row['.$c.']); ';

                    } elseif ($showType == 'date') {

                        $evalRow .= '$insertData[\''.$columnName.'\'] = Date::formatter($row['.$c.'], \'Y-m-d\'); ';

                    } else {
                        $evalRow .= '$insertData[\''.$columnName.'\'] = self::cleanVal($row['.$c.']); ';
                    }
                } 
                
                $this->db->BeginTrans(); 
                
                foreach ($rowsDatas as $n => $rowsData) {
                    
                    if ($rowsData != '') {
                        
                        if ($fileExtention == 'txt') {
                            $row = explode($delimiter, $rowsData);
                        } else {
                            $row = $rowsData;
                        }
                        
                        $insertData = array(
                            $kpiTblIdField      => getUIDAdd($n), 
                            'INDICATOR_ID'      => $indicatorId,
                            'CREATED_DATE'      => Date::currentDate('Y-m-d H:i:s'), 
                            'CREATED_USER_ID'   => $sessionUserKeyId, 
                            'CREATED_USER_NAME' => $sessionName
                        );
                        
                        eval($evalRow);

                        $insertColumns = $insertValues = '';

                        foreach ($insertData as $insertCol => $insertVal) {

                            if ($insertVal != '') {
                                $insertColumns .= "$insertCol,";
                                $insertValues .= "'$insertVal',";
                            }
                        }

                        self::dbExecuteMetaVerseData("INSERT INTO $tblName (".rtrim($insertColumns, ',').") VALUES (".rtrim($insertValues, ',').")");
                        
                        $n++;
                    }
                }
                
                $this->db->CommitTrans();
                        
                Mdform::clearCacheData($indicatorId);
            }
            
            $response = array('status' => 'success');
            
        } catch (Exception $ex) {
            
            $this->db->RollbackTrans();
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function createIndicatorStructure($indicatorId) {
        
        try {
            
            $dataIndicator = array(
                'ID' => $indicatorId,
                'CODE' => $indicatorId, 
                'NAME' => Input::post('name'),
                'PARENT_ID' => Input::numeric('parentId'),
                'IS_ACTIVE' => 1, 
                'KPI_TYPE_ID' => 1000, 
                'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s'), 
                'CREATED_USER_ID' => Ue::sessionUserKeyId()
            );
            $this->db->AutoExecute('KPI_INDICATOR', $dataIndicator);
            
            $dataIndicatorCat = array(
                'ID' => getUID(),
                'INDICATOR_ID' => $indicatorId, 
                'CATEGORY_ID' => Input::numeric('categoryId'),
                'IS_DEFAULT' => 1, 
                'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s'), 
                'CREATED_USER_ID' => Ue::sessionUserKeyId()
            );
            $this->db->AutoExecute('KPI_INDICATOR_CATEGORY', $dataIndicatorCat);

            $dataIndicatorType = array(
                'ID' => getUID(),
                'INDICATOR_ID' => $indicatorId, 
                'TYPE_ID' => 1000,
                'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s'), 
                'CREATED_USER_ID' => Ue::sessionUserKeyId()
            );
            $this->db->AutoExecute('KPI_INDICATOR_TYPE_MAP', $dataIndicatorType);
            
            $result = array('status' => 'success');
            
        } catch (Exception $ex) {
            $result = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $result;
    }
    
    public function removeTempIndicatorModel() {
        
        try {
            
            $id = Input::numeric('id');
            
            if (!$id) { throw new Exception('Invalid id!');  }
            
            $idPh = $this->db->Param(0);
            $row = $this->db->GetRow("
                SELECT ID FROM KPI_INDICATOR WHERE ID = $idPh AND CREATED_USER_ID = ".$this->db->Param(1), 
                array($id, Ue::sessionUserKeyId())
            );
            
            if ($row) {
                
                $this->db->Execute("DELETE FROM KPI_INDICATOR_TYPE_MAP WHERE INDICATOR_ID = $idPh", array($id));
                $this->db->Execute("DELETE FROM KPI_INDICATOR_CATEGORY WHERE INDICATOR_ID = $idPh", array($id));
                $this->db->Execute("DELETE FROM KPI_INDICATOR_INDICATOR_MAP WHERE MAIN_INDICATOR_ID = $idPh", array($id));
                $this->db->Execute("DELETE FROM KPI_INDICATOR WHERE ID = $idPh", array($id));
                
                $result = array('status' => 'success');
            } else {
                throw new Exception('Access denied!'); 
            }
            
        } catch (Exception $ex) {
            $result = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $result;
    }
    
    public function standardHiddenFieldsModel() {
        
        $fields = array(
            array(
                'type' => 'number', 
                'name' => 'ID'
            ), 
            array(
                'type' => 'date', 
                'name' => 'CREATED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'CREATED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'CREATED_USER_NAME'
            ),
            array(
                'type' => 'date', 
                'name' => 'MODIFIED_DATE'
            ),
            array(
                'type' => 'number', 
                'name' => 'MODIFIED_USER_ID'
            ),
            array(
                'type' => 'varchar', 
                'name' => 'MODIFIED_USER_NAME'
            ), 
            array(
                'type' => 'number', 
                'name' => 'WFM_STATUS_ID'
            )
        );
        $hidden = '';
        
        foreach ($fields as $field) {
            $hidden .= Form::hidden(array('name' => Mdform::$mvPathPrefix.'sf['.$field['name'].']'.Mdform::$mvPathSuffix, 'data-path' => $field['name'], 'value' => isset(Mdform::$kpiDmMart[$field['name']]) ? Mdform::$kpiDmMart[$field['name']] : ''));
        }
        
        return $hidden;
    }
    
    public function getKpiIndicatorMapMetaModel($srcId, $semanticTypeId, $trgId = null) {
        
        try {
            $langCode = Lang::getCode();
            $schemaName = Config::getFromCache('kpiDbSchemaName');
            $schemaName = $schemaName ? rtrim($schemaName, '.').'.' : '';            
        
            $data = $this->db->GetRow("
                SELECT 
                    T1.META_DATA_ID, 
                    T1.META_DATA_CODE,
                    T0.ID AS MAP_ID, 
                    T0.ICON, 
                    T0.COLOR,
                    T2.DATA
                FROM (
                        SELECT 
                            KIIM.ID, 
                            KIIM.ICON, 
                            KIIM.COLOR, 
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.LOOKUP_META_DATA_ID, 
                            KIIM.ORDER_NUMBER 
                        FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                        WHERE KIIM.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                            AND KIIM.SEMANTIC_TYPE_ID = ".$this->db->Param(1)."
                        GROUP BY 
                            KIIM.ID, 
                            KIIM.ICON, 
                            KIIM.COLOR,  
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.LOOKUP_META_DATA_ID,
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.ORDER_NUMBER 
                    ) T0 
                    INNER JOIN META_DATA T1 ON T1.META_DATA_ID = T0.LOOKUP_META_DATA_ID 
                    LEFT JOIN ".$schemaName."V_16754202632369 T2 ON T2.SRC_RECORD_ID = T0.ID 
                ORDER BY T0.ORDER_NUMBER ASC", 
                array($srcId, $semanticTypeId)
            );
            
        } catch (Exception $ex) {
            $data = array();
        }
        
        return $data;
    }        
    
    public function getKpiIndicatorChildColumnsModel($indicatorId, $parentId) {
            
        try {

            $langCode = Lang::getCode();

            $data = $this->db->GetAll(" 
                SELECT 
                    T0.* 
                FROM ( 
                    SELECT 
                        UPPER(COLUMN_NAME) AS COLUMN_NAME, 
                        FNC_TRANSLATE('$langCode', TRANSLATION_VALUE, 'LABEL_NAME', LABEL_NAME) AS LABEL_NAME, 
                        COLUMN_WIDTH, 
                        SHOW_TYPE, 
                        0 AS ORDER_NUMBER 
                    FROM KPI_INDICATOR_INDICATOR_MAP 
                    WHERE ID = ".$this->db->Param(1)." 

                    UNION ALL 

                    SELECT 
                        UPPER(COLUMN_NAME) AS COLUMN_NAME, 
                        FNC_TRANSLATE('$langCode', TRANSLATION_VALUE, 'LABEL_NAME', LABEL_NAME) AS LABEL_NAME, 
                        COLUMN_WIDTH, 
                        SHOW_TYPE, 
                        ORDER_NUMBER 
                    FROM KPI_INDICATOR_INDICATOR_MAP  
                    WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                        AND PARENT_ID = ".$this->db->Param(1)." 
                        AND ".$this->db->IfNull('IS_INPUT', '0')." = 1 
                        AND IS_RENDER = 1 
                        AND COLUMN_NAME IS NOT NULL 
                        AND LOWER(COLUMN_NAME) <> 'id' 
                ) T0 
                ORDER BY T0.ORDER_NUMBER ASC", 
                array($indicatorId, $parentId)
            );
            
        } catch (Exception $ex) {
            $data = array();
        }
        
        return $data;
    }
    
    public function rowsImportExcelModel() {
        
        if (!empty($_FILES['excelFile']['name'])) { 
            
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            
            $headerSheetName = 'Detail';
            $headerSheetNameLower = Str::lower($headerSheetName);
            $fileName = $_FILES['excelFile']['name'];
            $tmpName = $_FILES['excelFile']['tmp_name'];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (!in_array($extension, array('xls', 'xlsx'))) {
                return array('status' => 'error', 'message' => "The extension '$extension' is invalid!");
            }
            
            if (!FileUpload::checkContentType($fileName, $tmpName)) {
                return array('status' => 'error', 'message' => 'ContentType is invalid!');
            }
            
            if ($extension == 'xlsx') {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLSX');
                
                $xlsx = SimpleXLSX::parse($tmpName);
                $sheetNames = $xlsx->sheetNames();
                
            } else {
                
                includeLib('Office/Excel/simplexlsx/SimpleXLS');
                
                $xlsx = SimpleXLS::parse($tmpName);
                $sheetNames = $xlsx->sheets;
            }
            
            foreach ($sheetNames as $sheetKey => $sheetName) {
                
                $sheetNameLower = Str::lower($sheetName);
                
                if ($sheetNameLower == $headerSheetNameLower) {
                    $sheetIndex = $sheetKey;
                    break;
                }
            }
            
            if (isset($sheetIndex)) {
                
                $indicatorId = Input::numeric('indicatorId');
                $rowId = Input::numeric('rowId');
                $groupPath = Input::post('groupPath');
                
                $rows = $xlsx->rows($sheetIndex);
                $paths = array_map('strtolower', $rows[0]);
                
                $rows = array_slice($rows, 2, null, false);
                
                $lookupFields = $this->getIndicatorWithLookupFieldsModel($indicatorId, $rowId, $paths, $rows);
                
                $array = array();
                
                foreach ($rows as $row) {
                    
                    $val = array(); 
                    $isEmpty = true;
                    
                    foreach ($row as $k => $v) {
                        
                        $v = strval($v);
                        
                        if ($v != '') {
                            
                            $path = strtoupper($paths[$k]);
                            
                            if (isset($lookupFields[$path])) {
                                
                                if (isset($lookupFields[$path][Str::lower($v)])) {
                                    
                                    $val[$path] = $lookupFields[$path][Str::lower($v)];
                                    $isEmpty = false;
                                }
                                
                            } else {
                                $val[$path] = $v;
                                $isEmpty = false;
                            }
                        }
                    }
                    
                    if ($isEmpty == false) {
                        $array[] = $val;
                    }
                }
                
                if ($array) {

                    $rowsHtml = self::indicatorRowsRender($indicatorId, $groupPath, array($groupPath => $array), false);
                    
                    $response = array('status' => 'success', 'rows' => $rowsHtml, 'message' => 'Амжилттай импорт хийгдлээ.');
                    
                } else {
                    $response = array('status' => 'error', 'message' => 'No data!');
                }
                
            } else {
                $response = array('status' => 'error', 'message' => $headerSheetName.' гэсэн sheet нэр олдсонгүй!');
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Please select excel file!');
        }
        
        return $response;
    }
    
    public function getIndicatorWithLookupFieldsModel($indicatorId, $rowId, $paths, $selectedRows) {
        
        $array = array();
        
        $data = $this->db->GetAll("
            SELECT 
                COLUMN_NAME, 
                COLUMN_NAME_PATH, 
                TRG_INDICATOR_ID, 
                LOOKUP_META_DATA_ID, 
                SHOW_TYPE 
            FROM KPI_INDICATOR_INDICATOR_MAP 
            WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                AND PARENT_ID = ".$this->db->Param(1)." 
                AND SHOW_TYPE IN ('popup', 'combo') 
                AND ".$this->db->IfNull('IS_INPUT', '0')." = 1 
                AND IS_RENDER = 1
                AND (LOOKUP_META_DATA_ID IS NOT NULL OR TRG_INDICATOR_ID IS NOT NULL)", 
            array($indicatorId, $rowId)
        ); 
        
        if ($data) {
            
            $arrayMove = array();
            foreach ($paths as $pathKey => $pathVal) {
                $arrayMove[$pathVal] = $pathKey;
            }
            
            foreach ($data as $row) {
                
                $showType = $row['SHOW_TYPE'];
                $columnNameLower = strtolower($row['COLUMN_NAME']);
                
                if ($row['LOOKUP_META_DATA_ID']) {
                    
                    $this->load->model('mdwebservice', 'middleware/models/');
                    
                    $lookupMetaDataId = $row['LOOKUP_META_DATA_ID'];
                    $lookupFieldConfig = array(

                        'lookupMetaDataId' => $lookupMetaDataId, 
                        'lookupType'       => $showType, 
                        'displayField'     => '', 
                        'valueField'       => '', 

                        'groupConfigLookupPath' => '', 
                        'groupConfigParamPath'  => ''
                    );
                    
                    if ($showType == 'combo') {
                        $rowDatas = $this->model->getLookupRowDatas($lookupFieldConfig, '', $selectedRows, $arrayMove[$columnNameLower], 'name');
                    } else {
                        $rowDatas = $this->model->getLookupRowDatas($lookupFieldConfig, '', $selectedRows, $arrayMove[$columnNameLower], 'code');
                    }
                    
                    $this->load->model('mdform', 'middleware/models/');

                } else {
                    $rowDatas = array();
                } /*else { 

                    $colConfig['isData'] = true;
                    $colConfig['FILTER_INDICATOR_ID'] = $colConfig['LOOKUP_META_DATA_ID'];

                    $datas = self::getKpiComboDataModel($colConfig);

                    $comboRows = array();
                    $dataRows = $datas['data'];
                    $dataId = $datas['id'];
                    $dataName = $datas['name'];

                    if (isset($datas['data-name'])) {
                        $dataName = $datas['data-name'];
                    }

                    foreach ($dataRows as $dataRow) {
                        $comboRows[Str::lower($dataRow[$dataName])] = array(
                            'id' => $dataRow[$dataId], 
                            'name' => $dataRow[$dataName]
                        );
                    }

                    $comboDatas[$lookupMetaDataId] = $comboRows;
                }*/
                
                $array[$row['COLUMN_NAME']] = $rowDatas;
            }
        }
        
        return $array;
    }
    
    public function getKpiIndicatorMapChildCountModel($indicatorId) {
        
        $count = $this->db->GetOne("
            SELECT 
                COUNT(1) 
            FROM KPI_INDICATOR_INDICATOR_MAP 
            WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                AND COLUMN_NAME IS NOT NULL", 
            array($indicatorId)
        );
        
        return $count;
    }
    
    public function getIndicatorInputOutputFieldsModel($indicatorId, $mode = null) {
        
        $arr = array('input' => array(), 'output' => array());
        
        try {
            
            $sql = "
                SELECT 
                    UPPER(COLUMN_NAME) AS COLUMN_NAME, 
                    LABEL_NAME, 
                    SHOW_TYPE, 
                    SEMANTIC_TYPE_ID, 
                    SRC_INDICATOR_PATH, 
                    SIDEBAR_NAME, 
                    TRG_ALIAS_NAME, 
                    COLUMN_WIDTH, 
                    AGGREGATE_FUNCTION, 
                    BODY_ALIGN, 
                    SORT_ORDER, 
                    SORT_TYPE 
                FROM KPI_INDICATOR_INDICATOR_MAP 
                WHERE MAIN_INDICATOR_ID = ".$this->db->Param(0)." 
                    AND SEMANTIC_TYPE_ID IN (10000000, 10000001) 
                    AND COLUMN_NAME IS NOT NULL 
                    AND PARENT_ID IS NULL 
                ORDER BY ORDER_NUMBER ASC";
            
            $data = $this->db->GetAll($sql, array($indicatorId));
            
            foreach ($data as $row) {
                if ($row['SEMANTIC_TYPE_ID'] == '10000000') {
                    $arr['input'][] = $row;
                } else {
                    $arr['output'][] = $row;
                }
            }
        
        } catch (Exception $ex) { }
        
        return $arr;
    }
    
    public function rowsGetValueFromDataMartModel() {
        
        try {
            
            $rowId = Input::numeric('rowId');
            $getExpression = $this->db->GetOne("SELECT ALL_CELL_EXPRESSION FROM KPI_INDICATOR_INDICATOR_MAP WHERE ID = ".$this->db->Param(0), array($rowId));
            
            if ($getExpression) {
                
                $getExpression = @json_decode($getExpression, true);
                
                if (isset($getExpression['getValFromMart']) && $getExpression['getValFromMart']) {
                    
                    $headerData = Arr::changeKeyLower(Input::post('headerData'));
                    $cellExpression = $getExpression['getValFromMart'];
                    $resultCellData = array();
                    
                    foreach ($cellExpression as $cellId => $indicatorConfig) {
                        
                        $expression = strtolower($indicatorConfig['expression']);
                        
                        if ($expression != '') {
                            unset($indicatorConfig['expression']);
                            $resultData = array();
                            
                            foreach ($indicatorConfig as $indicatorId => $configs) {

                                $indicatorRow = $this->getKpiIndicatorRowModel($indicatorId);

                                if ($indicatorRow) {

                                    $kpiTypeId = $indicatorRow['KPI_TYPE_ID'];
                                    $queryStr = $indicatorRow['QUERY_STRING'] ? $indicatorRow['QUERY_STRING'] : $indicatorRow['TABLE_NAME'];
                                    
                                    if ($queryStr) {
                                        
                                        $inputs = $configs['input'];
                                        $outputs = $configs['output'];
                                        $selectField = $where = '';
                                        $whereClause = array();
                                        
                                        krsort($inputs);
                                        
                                        $queryStr = self::parseQueryString($queryStr);

                                        if (stripos($queryStr, 'select') !== false && stripos($queryStr, 'from') !== false) {

                                            $queryStr = '('.$queryStr.')';

                                        } elseif (!self::isCheckSystemTable($queryStr)) {

                                            $where .= ' AND T.DELETED_USER_ID IS NULL'; 
                                        }
                                        
                                        foreach ($inputs as $inputPath => $inputConfig) {
                                            $trg = strtolower($inputConfig['trg']);
                                            $defaultVal = $inputConfig['defaultVal'];

                                            $setInputVal = $trg ? issetParam($headerData[$trg]) : Mdmetadata::setDefaultValue($defaultVal);
                                            
                                            if (stripos($queryStr, ':'.$inputPath) !== false) {
                                                $queryStr = str_ireplace(':'.$inputPath, "'$setInputVal'", $queryStr);
                                            } else {
                                                $whereClause[strtolower($inputPath)] = $setInputVal;
                                            }
                                        }

                                        $queryNamedParams = DBSql::getQueryNamedParams($queryStr);

                                        if ($queryNamedParams) {
                                            foreach ($queryNamedParams as $queryNamedParam) {
                                                $queryStr = str_ireplace($queryNamedParam, "''", $queryStr);
                                                unset($whereClause[strtolower(str_replace(':', '', $queryNamedParam))]);
                                            }
                                        }
                                        
                                        if ($whereClause) {
                                            foreach ($whereClause as $whereField => $whereVal) {
                                                $where .= " AND T.$whereField = '$whereVal'"; 
                                            }
                                        }

                                        foreach ($outputs as $outputPath => $outputConfig) {
                                            
                                            if (stripos($expression, '.'.$outputPath.']') !== false) {
                                                
                                                $aggregate = $outputConfig['aggregate'];
                                                $selectAggregate = $aggregate."(T.$outputPath)";

                                                if ($aggregate == 'sum' || $aggregate == 'min' || $aggregate == 'max' || $aggregate == 'avg') {
                                                    $selectAggregate = "NVL($selectAggregate, 0)";
                                                } 

                                                $selectField .= $selectAggregate." AS $outputPath,";
                                            }
                                        }

                                        $selectField = rtrim($selectField, ',');

                                        $resultRow = $this->db->GetRow("SELECT $selectField FROM $queryStr T WHERE 1 = 1 $where");
                                        $resultData[$indicatorId] = Arr::changeKeyLower($resultRow);
                                    }
                                }
                            }
                            
                            foreach ($resultData as $setIndicatorId => $setFields) {
                                foreach ($setFields as $setField => $setVal) {
                                    $expression = str_replace("[$setIndicatorId.$setField]", $setVal, $expression);
                                }
                            }

                            try {
                                $eval = eval(sprintf('return (%s);', $expression));
                                $resultCellData[$cellId]['success'] = $eval;
                            } catch (ParseError $p) {
                                $resultCellData[$cellId]['error'] = $p->getMessage();
                            }
                        }
                    }
                    
                    $result = array('status' => 'success', 'cells' => $resultCellData);
                    
                } else {
                    throw new Exception('Expression олдсонгүй!'); 
                }
            } else {
                throw new Exception('Expression олдсонгүй!'); 
            }
            
        } catch (Exception $ex) {
            $result = array('status' => 'error', 'message' => $ex->msg);
        }
        
        return $result;
    }
    
    public function getKpiIndicatorFullExpressionModel($templateId) {
        $row = $this->db->GetRow("
            SELECT 
                LOAD_EXPRESSION_STRING, 
                VAR_FNC_EXPRESSION_STRING, 
                EVENT_EXPRESSION_STRING, 
                SAVE_EXPRESSION_STRING, 
                AFTER_SAVE_EXPRESSION_STRING,
                GRAPH_JSON
            FROM KPI_INDICATOR 
            WHERE ID = ".$this->db->Param(0), 
            array($templateId)
        );
        return $row;
    }
    
    public function getExecuteEventCodeModel($indicatorId) {
        
        $expIndicatorId = $eventCode = null;
        
        try {
            
            $executeTypeId = 73;
            $fillMapRows = self::getKpiIndicatorMapModel($indicatorId, $executeTypeId);
            
            if ($mapId = issetParam($fillMapRows[0]['MAP_ID'])) {
                
                $expIndicatorId = $fillMapRows[0]['ID'];
                $idPh1 = $this->db->Param(0);
                
                $configRow = $this->db->GetRow("
                    SELECT 
                        KI.TABLE_NAME, 
                        KT.RELATED_INDICATOR_ID 
                    FROM META_SEMANTIC_TYPE KT 
                        INNER JOIN KPI_INDICATOR KI ON KI.ID = KT.RELATED_INDICATOR_ID 
                    WHERE KT.ID = $idPh1", 
                    array($executeTypeId)
                );

                if ($configRow) {
                    $eventCode = $this->db->GetOne("SELECT EVENT_CODE FROM ".$configRow['TABLE_NAME']." WHERE SRC_RECORD_ID = $idPh1", array($mapId));
                }
            } 
            
        } catch (Exception $ex) {}
        
        return array('expIndicatorId' => $expIndicatorId, 'eventCode' => $eventCode);
    }
    
    public function getIndicatorsOnTheProcessModel($processId = '') {
        
        $result = array();
        
        if ($processId) {
            
            try {    
                $result = $this->db->GetAll("
                    SELECT 
                        T0.INDICATOR_ID, 
                        T1.NAME 
                    FROM CUSTOMER_USE_CHILD T0 
                        INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.INDICATOR_ID 
                    WHERE T0.SRC_META_DATA_ID = ".$this->db->Param(0)." 
                        AND T0.IS_USE = 1 
                    ORDER BY T0.ID ASC", 
                    array($processId)
                );
            } catch (Exception $ex) {}
        }
        
        return $result;
    }

    public function kpiTypeIndicatorData($filterId) {
        $qry = "SELECT 
                        T1.CODE, 
                        T1.ID,
                        T1.PARENT_ID,
                        T1.SHOW_TYPE,
                        T1.ORDER_NUMBER,
                        t1.DEFAULT_VALUE
                    FROM KPI_TYPE T0 
                    INNER JOIN KPI_INDICATOR T2 ON T0.RELATED_INDICATOR_ID = T2.ID
                    INNER JOIN KPI_INDICATOR_INDICATOR_MAP T1 ON T2.ID = T1.MAIN_INDICATOR_ID
                    WHERE T0.ID = ". $this->db->Param(0) . " ORDER BY T1.ORDER_NUMBER";
        $typeData = $this->db->GetAll($qry, array($filterId));
        $indicatorData = Arr::buildTree($typeData, 0, 'ID', 'PARENT_ID');
        /* if ($indicatorData) 
            $indicatorData = Arr::changeKeyLower($indicatorData);
         */
        return $indicatorData;
    }

    public function mainChartTypeDataModel($filterCode = '002') {
        $qry = "SELECT DISTINCT
                        t2.* ,
                        t1.CODE AS TYPE_CODE
                    FROM KPI_TYPE t0
                    LEFT JOIN (
                        SELECT DISTINCT
                            t0.ID as MAIN_ID, 
                            t1.ID, 
                            t4.CODE,
                            t1.NAME,
                            t1.RELATED_INDICATOR_ID,
                            t1.COLOR,
                            t1.ICON,
                            t1.PARENT_ID
                        FROM KPI_TYPE t0 
                        INNER JOIN KPI_TYPE t1 ON t0.ID = t1.PARENT_ID
                        INNER JOIN KPI_INDICATOR t3 ON t1.RELATED_INDICATOR_ID = t3.ID
                        INNER JOIN KPI_INDICATOR_INDICATOR_MAP t4 ON t3.ID = t4.MAIN_INDICATOR_ID
                        WHERE t0.CODE =  ". $this->db->Param(0) ."
                    ) t1 ON t1.MAIN_ID = t0.ID
                    INNER JOIN (
                            SELECT 
                                t0.ID as MAIN_ID, 
                                t1.ID as CHILD_ID, 
                                t0.CODE||'.'||t1.CODE as PARENT_CODE, 
                                t2.ID, 
                                t2.CODE,
                                t2.NAME,
                                t2.RELATED_INDICATOR_ID,
                                t2.COLOR,
                                t2.ICON,
                                t2.PARENT_ID,
                                '' AS SUB_TYPE_CODE --LISTAGG( t4.CODE,'##') WITHIN GROUP ( ORDER BY t4.CODE)
                            FROM KPI_TYPE t0 
                            INNER JOIN KPI_TYPE t1 ON t0.ID = t1.PARENT_ID
                            INNER JOIN KPI_TYPE t2 ON t1.ID = t2.PARENT_ID
                            LEFT JOIN KPI_INDICATOR t3 ON t2.RELATED_INDICATOR_ID = t3.ID
                            LEFT JOIN KPI_INDICATOR_INDICATOR_MAP t4 ON t3.ID = t4.MAIN_INDICATOR_ID
                        WHERE t0.CODE =  ". $this->db->Param(0) ."
                        GROUP BY t0.ID , 
                                t1.ID , 
                                t0.CODE, t1.CODE, 
                                t2.ID, 
                                t2.CODE,
                                t2.NAME,
                                t2.RELATED_INDICATOR_ID,
                                t2.COLOR,
                                t2.ICON,
                                t2.PARENT_ID
                    ) t2 ON t2.MAIN_ID = t0.ID AND t1.ID = t2.CHILD_ID
                    where t0.CODE =  ". $this->db->Param(0) ." ORDER BY t1.CODE";

        $typeCode = $this->db->GetAll($qry, array($filterCode));

        $subSql = "SELECT 
                        t4.CODE AS TYPE_CODE,
                        t4.PARENT_ID MPARENT_ID,
                        t4.ID MID,
                        t4.LABEL_NAME,
                        LOWER(t4.SHOW_TYPE) AS SHOW_TYPE,
                        t4.TRG_INDICATOR_ID
                    FROM KPI_TYPE t0 
                    INNER JOIN KPI_TYPE t1 ON t0.ID = t1.PARENT_ID
                    INNER JOIN KPI_TYPE t2 ON t1.ID = t2.PARENT_ID
                    INNER JOIN KPI_INDICATOR t3 ON t2.RELATED_INDICATOR_ID = t3.ID
                    INNER JOIN KPI_INDICATOR_INDICATOR_MAP t4 ON t3.ID = t4.MAIN_INDICATOR_ID
                    WHERE t0.CODE = ". $this->db->Param(0);
        /* echo $subSql; die; */
        $typeTreeListData = $this->db->GetAll($subSql, array($filterCode));
        $clearData = $tmp = array();
        foreach ($typeTreeListData as $key => $row) {
            if (!in_array($row['TYPE_CODE'], $tmp)) {
                array_push($tmp, $row['TYPE_CODE']);
                array_push($clearData, $row);
            }
        }
        $typeTreeList = Arr::buildTree($clearData, 0, 'MID', 'MPARENT_ID');

        return array('typeCode' => $typeCode, 'typeTreeList' => $typeTreeList);
    }

    public function renderConfigControl($panel, $graphJsonConfig, $panelCode) {
        $html = '';
        
        if (issetParamArray($panel['children']) && $panel['children']) {
            foreach ($panel['children'] as $key => $row) {
                $panelCode = $row['TYPE_CODE'];
                $html .= self::renderConfigControl($row, $graphJsonConfig, $panelCode);
            }
        } else {
            $panelCode =  Str::replace('.', '_', $panel['TYPE_CODE']);
            $html .= '<div class="form-group row configration '. $panelCode .'">';
                $html .= Form::label(array('text'=> $panel['LABEL_NAME'], 'class'=>'col-form-label col-md-auto text-right pr-0 pt-1', 'style' => 'width: 100px', 'for' => $panelCode));
                $html .= '<div class="col">';
                    switch ($panel['SHOW_TYPE']) {
                        case 'combo':
                            $trgIndicatorData = self::getKpiRowsModel($panel['TRG_INDICATOR_ID']);
                            $trgIndicatorData = Arr::changeKeyLower($trgIndicatorData);
                            
                            $form = Form::select(array(
                                'class' => 'form-control form-control-sm', 
                                'name' => $panelCode,
                                'id' => $panelCode,
                                'data' => issetParamArray($trgIndicatorData['rows']), 
                                'op_value' => 'id', 
                                'op_text' => 'name', 
                                /* 'text' => 'notext',  */
                                'value' => issetParam($graphJsonConfig['chartConfig'][$panelCode])
                            ));
                            break;
                        case 'checkbox':
                            $array = array(
                                'class' => 'form-control form-control-sm', 
                                'name' => $panelCode,
                                'id' => $panelCode,
                                'value' => '1', 
                                'saved_val' => issetParam($graphJsonConfig['chartConfig'][$panelCode]) ? '1' : ''
                            );
                            
                            if (issetParam($graphJsonConfig['chartConfig'][$panelCode]) === '1') {
                                $array['checked'] = 'checked';
                            }
    
                            $form =  Form::checkbox($array);
                            break;
                        
                        default:
                            $form = Form::text(array(
                                'class' => 'form-control form-control-sm ' . $panel['SHOW_TYPE'] . 'Init', 
                                'name' => $panelCode, 
                                'id' => $panelCode,
                                'value' => issetParam($graphJsonConfig['chartConfig'][$panelCode])
                            ));
                            break;
                    }
                    $html .= $form;
                $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    public function runInternalQueryModel() {
        $indicatorId = Input::numeric('indicatorId');
        
        $row = self::getKpiIndicatorRowModel($indicatorId);         
        $queryString = self::parseQueryString($row['QUERY_STRING']); 

        try {
            
            $this->db->Execute($queryString);
            return [
                'status' => 'success',
                'message' => 'Success'
            ];
            
        } catch (Exception $ex) {
            return [
                'status' => 'error',
                'message' => $ex->getMessage()
            ];
        }
    }

    public function getMetaDataValueCount($metaDataId = 0, $metaValueId = 0, $type = 'photo') {
        
        $data = '0';
        $id1Ph = $this->db->Param(0);
        $id2Ph = $this->db->Param(1);
        
        if ($type == 'photo') {
            
            $data = $this->db->GetOne("
                SELECT 
                    COUNT(FA.CONTENT_ID)
                FROM ECM_CONTENT FA 
                    INNER JOIN ECM_CONTENT_MAP MP ON MP.CONTENT_ID = FA.CONTENT_ID 
                WHERE MP.REF_STRUCTURE_ID = $id1Ph  
                    AND MP.RECORD_ID = $id2Ph 
                    AND FA.IS_PHOTO = 1", array($metaDataId, $metaValueId));
            
        } elseif ($type == 'file') {
            
            $data = $this->db->GetOne("
                SELECT 
                    COUNT(FA.CONTENT_ID) 
                FROM ECM_CONTENT FA 
                    INNER JOIN ECM_CONTENT_MAP MP ON MP.CONTENT_ID = FA.CONTENT_ID 
                WHERE MP.REF_STRUCTURE_ID = $id1Ph 
                    AND (MP.RECORD_ID = $id2Ph OR MP.MAIN_RECORD_ID = $id2Ph) 
                    AND FA.IS_PHOTO = 0 
                    AND (FA.TYPE_ID <> 4001 OR FA.TYPE_ID IS NULL)", array($metaDataId, $metaValueId));
            
        } elseif ($type == 'comment') {
            
            $data = $this->db->GetOne("
                SELECT 
                    COUNT(ID) 
                FROM ECM_COMMENT 
                WHERE REF_STRUCTURE_ID = $id1Ph 
                    AND RECORD_ID = $id2Ph 
                    AND IS_DELETED = 0", array($metaDataId, $metaValueId));
            
        } elseif ($type == 'relation') {
            $data = 0;
        }
        
        if ($data != '0') {
            return ' <span data-file-count="'.$data.'">('.$data.')</span>';
        }
        return '';
    }    

    public function getSavedRecordMapKpiModel($srcIndicatorId, $srcRecordId, $components) {
        
        try {
            
            $result = array();
        
            foreach ($components as $component) {
                
                $tableName = $component['TABLE_NAME'];
                $queryString = $component['QUERY_STRING'];
                
                if ($tableName || $queryString) {

                    $trgIndicatorId = $component['ID'];

                    $fieldConfig = self::getKpiComboDataModel(array('FILTER_INDICATOR_ID' => $trgIndicatorId, 'TRG_TABLE_NAME' => $tableName, 'isData' => false));
                    
                    $idField = $fieldConfig['id'];
                    $nameField = $fieldConfig['name'];
                    
                    if ($idField && $nameField) {
                        
                        if (!$tableName && $queryString) {
                            $tableName = self::parseQueryString($queryString);
                        }

                        if (stripos($tableName, 'select') !== false && stripos($tableName, 'from') !== false) {
                            $tableName = '('.$tableName.')';
                        }

                        $sql = "
                            SELECT 
                                MRM.ID AS PF_MAP_ID, 
                                MRM.TRG_RECORD_ID AS PF_MAP_RECORD_ID, 
                                ".$this->db->IfNull("PL.PROCESS_NAME", "T0.".$nameField)." AS PF_MAP_NAME,
                                T1.TRG_RECORD_ID AS PF_MAP_TRG_RECORD_ID, 
                                T0.* 
                            FROM 
                                (
                                    SELECT  
                                        ID, 
                                        TRG_RECORD_ID 
                                    FROM META_DM_RECORD_MAP 
                                    WHERE SRC_REF_STRUCTURE_ID = 1479204227214 
                                        AND SRC_RECORD_ID = $srcRecordId 
                                        AND TRG_REF_STRUCTURE_ID = $trgIndicatorId
                                ) MRM 

                                INNER JOIN $tableName T0 ON T0.$idField = MRM.TRG_RECORD_ID 
                                LEFT JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = MRM.TRG_RECORD_ID
                                LEFT JOIN META_DM_RECORD_MAP T1 ON T1.SRC_RECORD_ID = MRM.ID 
                                    AND T1.SRC_REF_STRUCTURE_ID = $trgIndicatorId 

                            ORDER BY MRM.ID ASC";

                        $rows = $this->db->GetAll($sql);
                        
                    } else {
                        $rows = array();
                    }
                    
                    $result[$trgIndicatorId] = $rows;
                }
            }
        
        } catch (Exception $ex) {
            $result = array();
        }
        
        return $result;
    }    

    public function getKpiIndicatorMapWithoutTypeModel($srcId, $semanticTypeId, $trgId = null) {
        
        try {
            $langCode = Lang::getCode();
        
            $data = $this->db->GetAll("
                SELECT 
                    T1.ID, 
                    FNC_TRANSLATE('$langCode', T1.TRANSLATION_VALUE, 'NAME', T1.NAME) AS NAME, 
                    T1.TABLE_NAME, 
                    T1.QUERY_STRING, 
                    T1.KPI_TYPE_ID, 
                    T0.ID AS MAP_ID, 
                    T0.ICON, 
                    T0.COLOR, 
                    T0.IS_ADDON_FORM, 
                    T0.META_INFO_INDICATOR_ID, 
                    T0.CODE, 
                    T0.SEMANTIC_TYPE_NAME, 
                    T0.SEMANTIC_TYPE_ICON, 
                    T0.SEMANTIC_TYPE_ID, 
                    ".$this->db->IfNull('T0.DESCRIPTION', "'Холбоос'")." AS DESCRIPTION, 
                    T2.TABLE_NAME AS SRC_TABLE_NAME, 
                    T2.QUERY_STRING AS SRC_QUERY_STRING,
                    T1.PARENT_ID, 
                    FNC_TRANSLATE('$langCode', T3.TRANSLATION_VALUE, 'NAME', T3.NAME) AS PARENT_NAME 
                FROM (
                        SELECT 
                            MAX(KIIM.ID) AS ID, 
                            MAX(KIIM.ICON) AS ICON, 
                            MAX(KIIM.COLOR) AS COLOR, 
                            MAX(KIIM.ORDER_NUMBER) AS ORDER_NUMBER, 
                            MAX(KIIM.IS_ADDON_FORM) AS IS_ADDON_FORM, 
                            MAX(KIIM.DESCRIPTION) AS DESCRIPTION, 
                            MAX(KIIM.SEMANTIC_TYPE_NAME) AS SEMANTIC_TYPE_NAME, 
                            MAX(KIIM.SEMANTIC_TYPE_ICON) AS SEMANTIC_TYPE_ICON, 
                            MAX(KIIM.SEMANTIC_TYPE_ID) AS SEMANTIC_TYPE_ID, 
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.META_INFO_INDICATOR_ID, 
                            KIIM.CODE 
                        FROM (
                            SELECT 
                                KIIM.ID, 
                                KIIM.ICON, 
                                KIIM.COLOR, 
                                KIIM.ORDER_NUMBER, 
                                KIIM.CODE, 
                                KIIM.DESCRIPTION, 
                                KIIM.SRC_INDICATOR_ID, 
                                KIIM.TRG_INDICATOR_ID, 
                                K.ID AS META_INFO_INDICATOR_ID, 
                                ST.NAME AS SEMANTIC_TYPE_NAME, 
                                ST.ICON AS SEMANTIC_TYPE_ICON, 
                                KIIM.SEMANTIC_TYPE_ID, 
                                (
                                    SELECT 
                                        COUNT(1) 
                                    FROM KPI_INDICATOR_INDICATOR_MAP M 
                                        INNER JOIN KPI_INDICATOR I ON I.ID = M.TRG_INDICATOR_ID 
                                    WHERE M.SRC_INDICATOR_MAP_ID = KIIM.ID 
                                        AND M.TRG_INDICATOR_ID IS NOT NULL 
                                        AND M.SRC_INDICATOR_PATH IS NOT NULL 
                                        AND M.TRG_INDICATOR_PATH IS NOT NULL 
                                ) AS IS_ADDON_FORM 
                            FROM KPI_INDICATOR_INDICATOR_MAP KIIM 
                                LEFT JOIN KPI_INDICATOR K ON K.ID = KIIM.META_INFO_INDICATOR_ID 
                                LEFT JOIN META_SEMANTIC_TYPE ST ON ST.ID = KIIM.SEMANTIC_TYPE_ID 
                            WHERE KIIM.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                                AND KIIM.SEMANTIC_TYPE_ID != ".$this->db->Param(1)." 
                                ".($trgId ? 'AND KIIM.TRG_INDICATOR_ID = '.$trgId : '')."  
                        ) KIIM 
                        GROUP BY 
                            KIIM.SRC_INDICATOR_ID, 
                            KIIM.TRG_INDICATOR_ID, 
                            KIIM.META_INFO_INDICATOR_ID, 
                            KIIM.CODE 
                    ) T0 
                    INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.TRG_INDICATOR_ID 
                        AND T1.DELETED_USER_ID IS NULL 
                    INNER JOIN KPI_INDICATOR T2 ON T2.ID = T0.SRC_INDICATOR_ID 
                        AND T2.DELETED_USER_ID IS NULL 
                    LEFT JOIN KPI_INDICATOR T3 ON T3.ID = T1.PARENT_ID 
                ORDER BY T0.ORDER_NUMBER ASC", 
                array($srcId, $semanticTypeId)
            );
            
        } catch (Exception $ex) {
            $data = array();
        }
        
        return $data;
    }    

    public function getKpiIndicatorExpressionModel($templateId) {        
        return $this->db->GetRow("SELECT ".$this->db->IfNull("VAR_FNC_EXPRESSION_STRING", "FLOWCHART_EXPRESSION")." AS VAR_FNC_EXPRESSION_STRING, ".$this->db->IfNull("VAR_FNC_EXPRESSION_STRING_JSON", "FLOWCHART_EXPRESSION_RAW")." AS VAR_FNC_EXPRESSION_STRING_JSON FROM KPI_INDICATOR WHERE ID = ".$this->db->Param(0), array($templateId));
    }    
    
}