<?php
$isDtlTbl = false;
$sidebarShow = false;
$sidebarShowRowDtl = false;

if ($this->paramTheme) {
    $tabNameArr = array();
    $headerName = '';
    $actionButton = '';
    $tabHeaderHead = '';
    $tabContent = '';
    $tabHeaderContent = '';
    $sidebarHeaderArr = array();
    $sidebarDtlRowArr = array();
    $getDtlRowsPopup = array();
    (String) $sidebarContent = "";
    (String) $sidebarGroup = "";
    (String) $sidebarGroupMetaRender = "";
    (String) $sidebarGroupMetaRowsRender = "";

    $tabActiveFirst = 0;
    $row = $this->paramTheme;
    if ($row['type'] == 'detail') {
        (Boolean) $isMultiRow = false;
        (Boolean) $isTab = false;
        (String) $htmlHeaderCell = '';
        (String) $htmlBodyCell = '';
        (String) $htmlGridFoot = '<td></td>';
        (String) $gridHead = '';
        (String) $gridHeadFilter = '';
        (String) $gridBody = '';
        (String) $gridFoot = '';
        (String) $gridBodyRow = '';
        (String) $gridBodyRowAfter = '';
        (String) $gridTabBody = '';
        (String) $gridTabContentHeader = '';
        (String) $gridTabContentBody = '';
        (String) $gridRowTypePath = '';
        (String) $gridClass = '';
        (String) $detialView = false;
        (String) $isAggregate = false;
        (String) $aggregateClass = '';
        (Array) $firstLevelRowArr = array();
        (Array) $sidebarGroupArr_[$row['id']] = array();

        if ($row['dataType'] === 'group' && ($row['isRequired'] === '1' || $row['isFirstRow'] === '1')) {    
            $detialView = true;
        }
        if (isset($row['data']) && $row['isShow'] == '1') {
            if ($row['recordtype'] == 'rows') {

                $isMultiRow = true;
            }

            $gridHead = '<tr>';
            $gridHeadFilter = '<tr class="bp-filter-row">';
            $gridHead .= '<th class="rowNumber" style="width:30px;">№</th>';
            $gridHeadFilter .= '<th></th>';
            $gridFoot = '<tr>';
            $gridFoot .= '<td class="number"></td>';
            $gridBody = '';

            $gridBody .= '<tr class="bp-detail-row">';
            $gridBody .= '<td class="text-center middle"><span>1</span><input type="hidden" name="param[' . $row['code'] . '.mainRowCount][]"/></td>';
            $ii = 0;

            foreach ($row['data'] as $ind => $val) {

                $foodAmount = '';
                $aggregateClass = '';

                if ($val['COLUMN_AGGREGATE'] != '') {
                    $isAggregate = true;
                    $foodAmount = '0.00';
                    $aggregateClass = 'aggregate-' . $val['COLUMN_AGGREGATE'];
                }

                $hideClass = '';
                if ($val['IS_SHOW'] != '1') {
                    $hideClass = ' hide';
                }

                $paramRealPath = str_replace('.', '', $val['PARAM_REAL_PATH']);

                if (strtolower($val['META_TYPE_CODE']) == 'boolean' && $isMultiRow) {
                    if (empty($val['SIDEBAR_NAME'])) {
                        $gridHead .= '<th class="text-center' . $hideClass . ' ' . $paramRealPath . ' bp-head-sort" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '">'
                                . $this->lang->line($val['META_DATA_NAME'])
                                . ($this->themeData['IS_MULTI_LANG'] === '1' ? ' <span class="secondLangText">/' . Lang::lineCode($val['META_DATA_NAME'],
                                                'en') . '/</span>' : '')
                                . '</th>';
                        $gridHeadFilter .= '<th class="' . $hideClass . '" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '"></th>';
                        $gridFoot .= '<td class="text-center' . $hideClass . ' ' . $paramRealPath . '" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '"></td>';
                    }
                } else {
                    if (empty($val['SIDEBAR_NAME']) && $isMultiRow && $val['RECORD_TYPE'] !== 'row' && $val['RECORD_TYPE'] !== 'rows') {
                        $gridHead .= '<th class="' . $hideClass . ' ' . $paramRealPath . ' bp-head-sort" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '" data-aggregate="' . $val['COLUMN_AGGREGATE'] . '">'
                                . $this->lang->line($val['META_DATA_NAME'])
                                . ($this->themeData['IS_MULTI_LANG'] === '1' ? ' <span class="secondLangText">/' . Lang::lineCode($val['META_DATA_NAME'], 'en') . '/</span>' : '')
                                . '</th>';
                        $gridHeadFilter .= '<th class="' . $hideClass . '" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '"><input type="text"/></th>';
                        $gridFoot .= '<td class="text-right' . $hideClass . ' ' . $paramRealPath . ' bigdecimalInit"  data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '">' . $foodAmount . '</td>';
                    }
                }

                if ($isMultiRow) {
                    $gridClass .= Mdwebservice::fieldDetailStyleClass($val, $paramRealPath, 'bp-window-' . $this->methodId);

                    $arg = array(
                        'parentRecordType' => 'rows'
                    );
                    if ($val['RECORD_TYPE'] == 'row') {
                        if ($val['IS_BUTTON'] == '1') {
                            ++$ii;
                            (String) $gridTabActive = '';
                            if ($ii === 1) $gridTabActive = ' active';

                            $isTab = true;
                            $arg['isTab'] = 'tab';

                            $gridRowTypePath = $row['code'] . '.' . $val['META_DATA_CODE'];
                            $gridTabContentHeader .= '<li class="nav-item ' . $hideClass . '">';
                            $gridTabContentHeader .= '<a href="#' . $row['code'] . "_" . $val['META_DATA_CODE'] . '" class="nav-link ' . $gridTabActive . '" data-toggle="tab">' . $this->lang->line($val['META_DATA_NAME']) . '</a>';
                            $gridTabContentHeader .= '</li>';
                            $gridTabContentBody .= '<div class="tab-pane in' . $hideClass . $gridTabActive . '" id="' . $row['code'] . "_" . $val['META_DATA_CODE'] . '" data-section-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '">';
                            $gridTabContentBody .= (new Mdwebservice())->buildTreeParam($this->uniqId, $this->methodId,
                                            $val['META_DATA_NAME'], $row['code'] . '.' . $val['META_DATA_CODE'], 'row', $val['ID'],
                                            null, '', $arg, $val['IS_BUTTON'], $val['COLUMN_COUNT']);
                            $gridTabContentBody .= '</div>';
                        } else {
                            $childRow = Mdwebservice::appendSubRowInProcess($this->uniqId, $gridClass, $this->methodId, $val);
                            $gridHead .= $childRow['header'];
                            $gridHeadFilter .= $childRow['headerFilter'];
                            $gridBody .= $childRow['body'];
                            $gridFoot .= $childRow['footer'];
                        }
                    } elseif ($val['RECORD_TYPE'] == 'rows') {
                        ++$ii;
                        (String) $gridTabActive = "";
                        if ($ii === 1) $gridTabActive = " active";

                        $isTab = true;
                        $arg['isTab'] = 'tab';
                        $arg['isShowAdd'] = $val['IS_SHOW_ADD'];
                        $arg['isShowDelete'] = $val['IS_SHOW_DELETE'];
                        $arg['isShowMultiple'] = $val['IS_SHOW_MULTIPLE'];

                        $gridRowTypePath = $row['code'] . '.' . $val['META_DATA_CODE'];
                        $gridTabContentHeader .= '<li class="nav-item ' . $hideClass . '">';
                        $gridTabContentHeader .= '<a href="#' . $row['code'] . "_" . $val['META_DATA_CODE'] . '" class="nav-link ' . $gridTabActive . '" data-toggle="tab">' . $this->lang->line($val['META_DATA_NAME']) . '</a>';
                        $gridTabContentHeader .= '</li>';
                        $gridTabContentBody .= '<div class="tab-pane in' . $hideClass . $gridTabActive . '" id="' . $row['code'] . "_" . $val['META_DATA_CODE'] . '">';
                        $gridTabContentBody .= (new Mdwebservice())->buildTreeParam($this->uniqId, $this->methodId, 
                                        $val['META_DATA_NAME'], $row['code'] . '.' . $val['META_DATA_CODE'], 'rows', $val['ID'],
                                        null, "", $arg, "", $val['COLUMN_COUNT']);
                        $gridTabContentBody .= '</div>';
                    } elseif (empty($val['SIDEBAR_NAME'])) {
                        $gridBody .= '<td data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '" class="' . $row['code'] . $val['META_DATA_CODE'] . ' stretchInput middle text-center' . $hideClass . ' ' . $row['code'] . $val['META_DATA_CODE'] . ' ' . $aggregateClass . '">';
                        $gridBody .= Mdwebservice::renderParamControl($this->methodId, $val,
                                        "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]",
                                        $row['code'] . "." . $val['META_DATA_CODE'], null);
                        $gridBody .= '</td>';
                    } else {
                        $sidebarShowRowsDtl_[$row['id']] = true;
                        if (!in_array($val['SIDEBAR_NAME'], $sidebarGroupArr_[$row['id']])) {
                            $sidebarGroupArr_[$row['id']][$ind] = $val['SIDEBAR_NAME'];
                            $sidebarDtlRowsContentArr_[$row['id'] . $ind] = array();
                        }

                        $groupKey = array_search($val['SIDEBAR_NAME'], $sidebarGroupArr_[$row['id']]);
                        $labelAttr = array(
                            'text' => $this->lang->line($val['META_DATA_NAME']),
                            'for' => "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]",
                            'data-label-path' => $row['code'] . "." . $val['META_DATA_CODE']
                        );
                        if ($val['IS_REQUIRED'] == '1') {
                            $labelAttr = array_merge($labelAttr, array('required' => 'required'));
                        }
                        if ($val['META_TYPE_CODE'] == 'date') {
                            $inHtml = '<div style="width: 132px; text-align: left;">' . Mdwebservice::renderParamControl($this->methodId,
                                            $val, "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]", 
                                            $row['code'] . "." . $val['META_DATA_CODE'], array()) . "</div>";
                        } else {
                            $inHtml = Mdwebservice::renderParamControl($this->methodId, $val,
                                            "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]", 
                                            $row['code'] . "." . $val['META_DATA_CODE'], array());
                        }
                        $sidebarDtlRowsContentArr_[$row['id'] . $groupKey][] = array(
                            'input_label_txt' => Form::label($labelAttr),
                            'data_path' => $row['code'] . "." . $val['META_DATA_CODE'],
                            'input_html' => $inHtml
                        );
                        $sidebarDtlRowsContentArr_[$row['id']][$groupKey] = $sidebarDtlRowsContentArr_[$row['id'] . $groupKey];
                    }
                } else {

                    $gridClass .= Mdwebservice::fieldDetailRowStyleClass($val, 'bp-window-' . $this->methodId);
                    $arg = array();
                    if (empty($val['SIDEBAR_NAME'])) {

                        if ($isMultiRow) {
                            $gridBody .= '<td data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '" class="' . $row['code'] . $val['META_DATA_CODE'] . ' stretchInput text-center' . $hideClass . '">';
                            $gridBody .= Mdwebservice::renderParamControl($this->methodId, $val,
                                            "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]", 
                                            $row['code'] . "." . $val['META_DATA_CODE'], null);
                            $gridBody .= '</td>';
                        } else {
                            if ($val['RECORD_TYPE'] === 'rows') {
                                $arg['isShowAdd'] = $val['IS_SHOW_ADD'];
                                $arg['isShowDelete'] = $val['IS_SHOW_DELETE'];
                                $arg['isShowMultiple'] = $val['IS_SHOW_MULTIPLE'];

                                $gridBodyRowAfter .= '<tr class="' . $hideClass . '" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '">';

                                if ($val['META_TYPE_CODE'] === 'group' && $val['IS_BUTTON'] == '1') {
                                    $gridBodyRowAfter .= '<td class="text-right middle float-left" style="width: 18%">';
                                    $labelAttr = array(
                                        'text' => $this->lang->line($val['META_DATA_NAME'])
                                    );
                                    if ($val['IS_REQUIRED'] == '1') {
                                        $labelAttr = array_merge($labelAttr, array('required' => 'required'));
                                    }
                                    $gridBodyRowAfter .= Form::label($labelAttr);
                                    $gridBodyRowAfter .= '</td>';
                                    $gridBodyRowAfter .= '<td data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '" style="width: 72%" class="middle float-left">';
                                    $gridBodyRowAfter .= (new Mdwebservice())->buildTreeParam($this->uniqId, $this->methodId, 
                                                    $val['META_DATA_NAME'], $row['code'] . '.' . $val['META_DATA_CODE'],
                                                    $val['RECORD_TYPE'], $val['ID'], null, "", $arg,
                                                    $val['IS_BUTTON'], $val['COLUMN_COUNT']);
                                    $gridBodyRowAfter .= '</td>';
                                } else {
                                    $gridBodyRowAfter .= '<td data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '" style="width: 100%" class="middle float-left" colspan="2">';
                                    $gridBodyRowAfter .= '<p class="meta_description"><i class="fa fa-info-circle"></i> ' . $this->lang->line($val['META_DATA_NAME']) . '</p>';
                                    $gridBodyRowAfter .= (new Mdwebservice())->buildTreeParam($this->uniqId, $this->methodId, 
                                                    $val['META_DATA_NAME'], $row['code'] . '.' . $val['META_DATA_CODE'],
                                                    $val['RECORD_TYPE'], $val['ID'], null, "", $arg,
                                                    $val['IS_BUTTON'], $val['COLUMN_COUNT']);
                                    $gridBodyRowAfter .= '</td>';
                                }

                                $gridBodyRowAfter .= '</tr>';
                            } else if ($val['RECORD_TYPE'] === 'row') {
                                $gridBodyRowAfter .= '<tr class="' . $hideClass . '" data-cell-path="' . $row['code'] . "." . $val['META_DATA_CODE'] . '">';
                                $gridBodyRowAfter .= '<td>';
                                $gridBodyRowAfter .= (new Mdwebservice())->buildTreeParam($this->uniqId, $this->methodId, 
                                                $val['META_DATA_NAME'], $row['code'] . '.' . $val['META_DATA_CODE'], $val['RECORD_TYPE'],
                                                $val['ID'], $this->fillParamData, "", array(), 1,
                                                $val['COLUMN_COUNT']);
                                $gridBodyRowAfter .= '</td>';
                                $gridBodyRowAfter .= '</tr>';
                            } else {
                                array_push($firstLevelRowArr, $val);
                            }
                        }
                    } else {
                        $sidebarShowRowDtl = true;
                        $fillParamData = isset($this->fillParamData[Str::lower($row['code'])]) ? $this->fillParamData[Str::lower($row['code'])]
                                    : null;
                        if (!in_array($val['SIDEBAR_NAME'], $sidebarDtlRowArr)) {
                            $sidebarDtlRowArr[$ind] = $val['SIDEBAR_NAME'];
                            $sidebarDtlRowContentArr[$ind] = array();
                        }

                        $groupKey = array_search($val['SIDEBAR_NAME'], $sidebarDtlRowArr);
                        $labelAttr = array(
                            'text' => $this->lang->line($val['META_DATA_NAME']),
                            'for' => "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]",
                            'data-label-path' => $row['code'] . "." . $val['META_DATA_CODE']
                        );
                        if ($val['IS_REQUIRED'] == '1') {
                            $labelAttr = array_merge($labelAttr, array('required' => 'required'));
                        }
                        $sidebarDtlRowContentArr[$groupKey][] = array(
                            'input_label_txt' => Form::label($labelAttr),
                            'data_path' => $row['code'] . "." . $val['META_DATA_CODE'],
                            'input_html' => Mdwebservice::renderParamControl($this->methodId, $val,
                                    "param[" . $row['code'] . "." . $val['META_DATA_CODE'] . "][0][]", 
                                    $row['code'] . "." . $val['META_DATA_CODE'], $fillParamData)
                        );
                        $sidebarDtlRowContentArr[$groupKey] = $sidebarDtlRowContentArr[$groupKey];
                    }
                }

                $isDtlTbl = true;
            }
            $gridBodyRow .= Mdwebservice::renderFirstLevelAddEditDtlRow($this->methodId, $firstLevelRowArr, $row['code'], $row['columnCount']);
            $gridBodyRow .= $gridBodyRowAfter;

            if ($isMultiRow) {
                $actionWidth = 40;
                if (isset($sidebarShowRowsDtl_[$row['id']])) {
                    $actionWidth = 70;
                }
                $htmlHeaderCell = '<th class="action ' . ($row['isShowDelete'] === '1' ? '' : ' hide') . '" style="width:' . $actionWidth . 'px;"></th>';
                $htmlBodyCell .= '<td class="text-center stretchInput middle' . ($row['isShowDelete'] === '1' ? '' : ' hide') . '">';

                if (isset($sidebarShowRowsDtl_[$row['id']])) {
                    $htmlBodyCell .= '<a href="javascript:;" onclick="proccessRenderPopup(\'div#bp-window-' . $this->methodId . ':visible\', this);" class="btn btn-xs purple-plum" style="width:21px" title="Popup цонхоор харах"><i class="fa fa-external-link"></i></a>';
                    $htmlBodyCell .= '<div class="sidebarDetailSection hide">';

                    if (!empty($sidebarGroupArr_[$row['id']])) {
                        foreach ($sidebarGroupArr_[$row['id']] as $keyPopGroup => $rowPopGroup) {

                            $htmlBodyCell .= '<p class="property_page_title">' . $this->lang->line($rowPopGroup) . '</p>' .
                                    '<div class="panel panel-default bg-inverse grid-row-content">' .
                                    '<table class="table sheetTable sidebar_detail">' .
                                    '<tbody>';
                            foreach ($sidebarDtlRowsContentArr_[$row['id']][$keyPopGroup] as $subrowPopGroup) {
                                $htmlBodyCell .= "<tr data-cell-path='" . $subrowPopGroup['data_path'] . "'>" .
                                        "<td style='width: 229px;' class='left-padding'>" . $this->lang->line($subrowPopGroup['input_label_txt']) . "</td>" .
                                        "<td>" . $subrowPopGroup['input_html'] . "</td>" .
                                        "</tr>";
                            }
                            $htmlBodyCell .= '</tbody></table></div>';
                        }
                    }

                    $htmlBodyCell .= '</div>';
                }
                if ($row['isShowDelete'] === '1') {
                    $htmlBodyCell .= '<a href="javascript:;" class="btn red btn-xs bp-remove-row" title="' . $this->lang->line('delete_btn') . '"><i class="fa fa-trash"></i></a>';
                }
                $htmlBodyCell .= '</td>';
            }

            if ($isTab) {
                $htmlHeaderCell .= '<th style="width:40px" data-cell-path="' . $gridRowTypePath . '"></th>';
                $gridFoot .= '<td data-cell-path="' . $gridRowTypePath . '"></td>';
                $gridBody .= '<td data-cell-path="' . $gridRowTypePath . '" class="text-center stretchInput middle">';
                $gridBody .= '<a href="javascript:;" onclick="paramTreePopup(this, ' . getUID() . ', \'div#bp-window-' . $this->methodId . ':visible\');" class="hide-tbl btn btn-sm purple-plum bp-btn-subdtl" style="width:35px" title="Дэлгэрэнгүй">';
                $gridBody .= '...';
                $gridBody .= '</a> ';
                $gridBody .= '<div class="param-tree-container-tab param-tree-container hide">';
                $gridBody .= '<div class="tabbable-line">
                            <ul class="nav nav-tabs">' . $gridTabContentHeader . '</ul>
                            <div class="tab-content">
                                ' . $gridTabContentBody . '
                            </div>
                          </div>';
                $gridBody .= '</div>';
                $gridBody .= '</td>';
            }
            $gridBody .= $htmlBodyCell;
            $gridBody .= '</tr>';

            $gridHead .= $htmlHeaderCell;
            $gridHead .= '</tr>';
            $gridHeadFilter .= $htmlHeaderCell;
            $gridHeadFilter .= '</tr>';
            $gridFoot .= '<td class="' . ($row['isShowDelete'] === '1' ? '' : ' hide') . '"></td>';
            $gridFoot .= '<tr>';

            $content = '<div class="row" data-section-path="' . $row['code'] . '" data-isclear="' . $row['isRefresh'] . '">
            <div class="col-md-12">';

            if ($isMultiRow) {
                
                $bpDtlAddHtml = $this->cache->get('bpDtlAddDtl_'.$this->methodId.'_'.$row['id']);
                                    
                if ($bpDtlAddHtml == null) {
                    $bpDtlAddHtml = Str::remove_doublewhitespace(str_replace(array("\r\n", "\n", "\r"), '', $gridBody));
                    $this->cache->set('bpDtlAddDtl_'.$this->methodId.'_'.$row['id'], $bpDtlAddHtml, Mdwebservice::$expressionCacheTime);
                }
                    
                $content .= '<div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-6">';

                if ($row['isShowMultiple'] === '1' && $row['groupLookupMeta'] != '' && $row['isShowMultipleMap'] != '0') {
                    $content .= Form::button(array('data-action-path' => $row['code'], 'class' => 'btn btn-xs green-meadow mr5 float-left bp-add-multi-row',
                                'value' => '<i class="icon-plus3 font-size-12"></i> Олноор нэмэх', 'onclick' => 'bpAddMainMultiRow_' . $this->methodId . '(this, \'' . $this->methodId . '\', \'' . $row['groupLookupMeta'] . '\', \'\', \'' . $row['paramPath'] . '\', \'\');'));
                }

                if ($row['groupKeyLookupMeta'] != '' && $row['isShowMultipleKeyMap'] != '0') {
                    $content .= '<div class="input-group quick-item-process float-left bp-add-ac-row" data-action-path="' . $row['code'] . '">';
                    $content .= '<div class="input-icon">';
                    $content .= '<i class="far fa-search"></i>';
                    $content .= Form::text(array(
                        'class' => 'form-control form-control-sm lookup-code-hard-autocomplete lookup-hard-autocomplete',
                        'style' => 'padding-left:25px;',
                        'data-processid' => $this->methodId,
                        'data-param-metadataid' => $row['id'],
                        'data-lookupid' => $row['groupKeyLookupMeta'],
                        'data-path' => $row['paramPath'],
                        'data-in-param' => $row['groupConfigParamPath'],
                        'data-in-lookup-param' => $row['groupConfigLookupPath']
                    ));
                    $content .= '</div>';
                    $content .= '<span class="input-group-btn">';
                    $content .= Form::button(array('data-action-path' => $row['code'], 'class' => 'btn btn-xs green-meadow bp-group-save',
                                'value' => '<i class="icon-plus3 font-size-12"></i>', 'onclick' => 'bpAddMainMultiRow_' . $this->methodId . '(this, \'' . $this->methodId . '\', \'' . $row['groupKeyLookupMeta'] . '\', \'' . $row['groupLookupKeyMetaTypeId'] . '\', \'' . $row['paramPath'] . '\', \'autocomplete\');'));
                    $content .= '</span>';
                    $content .= '</div>';
                }
                $content .= '<div class="clearfix w-100"></div>';
                $content .= '</div>';

                if ($row['isSave'] == '1') {
                    $actionButton .= Form::button(array('class' => 'btn btn-xs green-meadow', 'value' => '<i class="icon-checkmark-circle2"></i> Хадгалах' , 'onclick' => 'bpSaveMainRow(this);'));
                }
                    
                if ($row['isShowAdd'] === '1') {
                    $actionButton .= Form::button(array('data-action-path' => $row['code'], 'class' => 'btn btn-xs green-meadow float-right mr5 theme-edit-btn bp-add-one-row', 'value' => '<i class="icon-plus3 font-size-12"></i> ' . $this->lang->line('addRow'), 'onclick' => 'bpAddMainRow_' . $this->methodId . '(this, \''.$this->methodId.'\', \'' . $row['id'] . '\');'));
                }
                $content .= '</div>
                </div>';
            }

            $gridBodyData = '';

            if ($this->fillParamData) {
                $renderFirstLevelDtl = Mdwebservice::renderFirstLevelDtl($this->uniqId, $this->methodId, $row, $getDtlRowsPopup, $isMultiRow, $this->fillParamData);
                if ($renderFirstLevelDtl) {
                    $gridBody = $renderFirstLevelDtl['gridBody'];
                    $gridBodyRow = Mdwebservice::renderFirstLevelAddEditDtlRow($this->methodId, $firstLevelRowArr, $row['code'], $row['columnCount'], $this->fillParamData);
                    $gridBodyRow .= $renderFirstLevelDtl['gridBodyRow'];
                    $gridBodyData = $renderFirstLevelDtl['gridBodyData'];
                    $isRowState = $renderFirstLevelDtl['isRowState'];
                }
            }

            if (empty($gridBodyRow)) {
                if (!empty($htmlHeaderCell)) {
                    $content .= '<div class="table-scrollable bprocess-table-dtl-div">
                        <style type="text/css">#bp-window-' . $this->methodId . ' .bprocess-table-dtl{table-layout: fixed !important; max-width: ' . Mdwebservice::$tableWidth . 'px !important;} ' . $gridClass . '</style>
                        <table class="table table-sm table-bordered table-hover bprocess-table-dtl bprocess-theme1" data-table-path="' . $row['code'] . '">
                            <thead>
                                ' . $gridHead . $gridHeadFilter . '
                            </thead>
                            <tbody class="tbody">
                                ' . /* is required - one row */($detialView ? $gridBody : '') . $gridBodyData . '
                            </tbody>
                            <tfoot>' . ($isAggregate === true ? $gridFoot : '') . '</tfoot>
                        </table>    
                    </div>
                </div>
            </div>';
                }
            } else {
                if ($row['isSave'] == '1') {
                    $content .= Form::button(array('class' => 'btn btn-xs green-meadow float-right', 'value' => '<i class="icon-checkmark-circle2"></i> Хадгалах', 'onclick' => 'bpSaveMainRow(this);'));
                }
                $content .= '<div class="table-scrollable table-scrollable-borderless mt0" data-section-path="' . $row['code'] . '" data-isclear="' . $row['isRefresh'] . '">
                    <style type="text/css">' . $gridClass . '</style>
                    <table class="table table-sm table-no-bordered bprocess-table-row">
                        <tbody>
                            ' . $gridBodyRow . '
                        </tbody>
                        <tfoot>' . ($isAggregate === true ? $gridFoot : '') . '</tfoot>
                    </table>    
                </div>
            </div>
        </div>';
            }

            if ($row['tabName'] != '') {
                if (!isset($tabNameArr[$row['tabName']])) {
                    $tabHeaderContent = '';

                    $tabActive = '';
                    if ($tabActiveFirst === 0) {
                        $tabActive = ' active';
                    }
                    $headerName .= '<div class="col-md-10 section-title">'
                            . $this->lang->line($row['tabName'])
                            . ($this->themeData['IS_MULTI_LANG'] === '1' ? ' <span class="secondLangText">/' . Lang::lineCode($row['tabName'],
                                            'en') . '/</span>' : '')
                            . '</div>';

                    $tabContent .= '<div class="tab-pane' . $tabActive . '" id="tab_' . $this->methodId . '_' . $row['id'] . '">' . $tabHeaderContent . $content . '<!--' . $row['tabName'] . '--></div>';
                    ++$tabActiveFirst;

                    $tabNameArr[$row['tabName']] = '';
                } else {
                    $tabContent = str_replace('<!--' . $row['tabName'] . '-->', $content . '<!--' . $row['tabName'] . '-->', $tabContent);
                }
            } else {
                $headerName .= '<div class="col-md-10 section-title">'
                        . $this->lang->line($row['name'])
                        . ($this->themeData['IS_MULTI_LANG'] === '1' ? ' <span class="secondLangText">/' . Lang::lineCode($row['name'],
                                        'en') . '/</span>' : '')
                        . '</div>';
            }
        }
    }


    if ($headerName != '') {

        echo '<div class="theme-grid" data-section-path="' . $row['code'] . '">
                <div class="card light shadow theme-grid-area theme-panel" id="theme-grid-area">
                    <div class="theme-grid-title">
                        ' . $headerName . $actionButton . '
                    </div>
                    <div class="theme-data-area">
                        ' . $content . '
                    </div>    
                </div>
              </div>';
    }
}
?>

<div id="responseMethod"></div>