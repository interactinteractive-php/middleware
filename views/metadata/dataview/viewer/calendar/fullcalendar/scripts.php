<?php
$colorField = '';

$image1 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['image1']);
$eventResizeProcessCode = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['eventResizeProcessCode']);
$eventDropProcessCode = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['eventDropProcessCode']);
$aspectRatio = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['aspectRatio']);
$todayFocus = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['todayFocus']);
$slotDuration = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['slotDuration']);

$isAddonField = false;
$topLeft1 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft1']);
$topLeft2 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft2']);
$topLeft3 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft3']);
$borderLeftColor = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['borderLeftColor']);
$borderRightColor = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['borderRightColor']);
$loopColorCircle = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['loopColorCircle']);
$loopColorCircleTitle = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['loopColorCircleTitle']);

if ($topLeft1 || $topLeft2 || $topLeft3) {
    $isAddonField = true;
}

$isTooltipField = false;
$tooltipPos1 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos1']);
$tooltipPos2 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos2']);
$tooltipPos3 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos3']);
$tooltipImage1 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipImage1']);
$tooltipPos4 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos4']);
$tooltipPos5 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos5']);
$tooltipPos6 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6']);
$tooltipPos6Color = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6Color']);
$tooltipPos6TextColor = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6TextColor']);

$tooltipPos6Desc = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6Desc']);
$tooltipPos6DescColor = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6DescColor']);
$tooltipPos6DescTextColor = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6DescTextColor']);

$tooltipPos7 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos7']);
$tooltipPos7Color = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos7Color']);
$tooltipPos8 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos8']);
$tooltipPos8_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos8_labelname']);
$tooltipPos9 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos9']);
$tooltipPos9_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos9_labelname']);
$defaultview_calendar = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['defaultview_calendar']);

$tooltipPos10 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos10']);
$tooltipPos10_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos10_labelname']);

$tooltipPos11 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos11']);
$tooltipPos11_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos11_labelname']);

$tooltipPos12 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos12']);
$tooltipPos12_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos12_labelname']);

$tooltipPos13 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos13']);
$tooltipPos13_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos13_labelname']);

$tooltipPos14 = strtolower(issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos14']));
$tooltipPos14_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos14_labelname']);
$tooltipPos14Color = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos14Color']);

$headerRight = issetDefaultVal($this->row['dataViewLayoutTypes']['calendar']['fields']['headerRight'], 'listViewBtn,month,basicWeek,basicDay');
$minTime = issetDefaultVal($this->row['dataViewLayoutTypes']['calendar']['fields']['minTime'], '00:00:00');
$maxTime = issetDefaultVal($this->row['dataViewLayoutTypes']['calendar']['fields']['maxTime'], '24:00:00');

$eventPosition1 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['eventPosition1']);
$eventPosition2 = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['eventPosition2']);
$timeViewVerticalAlign = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['timeViewVerticalAlign']);
$refreshBtn = 1; //issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['refreshBtn']);
$listDvBtn = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['listDvBtn']);

if ($tooltipPos1 || $tooltipPos2 || $tooltipPos3 || $tooltipImage1 
    || $tooltipPos4 || $tooltipPos5 || $tooltipPos6 || $tooltipPos6Color || $tooltipPos7 || $tooltipPos7Color || $tooltipPos8) {
    $isTooltipField = true;
}
?>

<script type="text/javascript">

var objectdatagrid_<?php echo $this->mid; ?> = $('#objectdatagrid-<?php echo $this->mid; ?>');
var windowId_<?php echo $this->mid; ?> = 'div#object-value-list-<?php echo $this->mid; ?>';
var $dv_search_<?php echo $this->mid; ?> = $("#calendar-searchform-<?php echo $this->mid; ?>");
var calendarEvent_<?php echo $this->mid; ?> = null;
var doubleClick_<?php echo $this->mid; ?> = false;
var filterClick_<?php echo $this->mid; ?> = false;
var isIgnoreWfmHistory_<?php echo $this->mid; ?> = <?php echo (issetParam($this->row['IS_IGNORE_WFM_HISTORY']) == '1' ? 'true' : 'false'); ?>;
var fc_<?php echo $this->mid; ?> = <?php echo json_encode($this->row['dataViewLayoutTypes']['calendar']['fields']); ?>;

$(function() {

    Core.initInputType($(windowId_<?php echo $this->mid; ?>));
    
    if (typeof FullCalendar == 'undefined') {
        
        $.cachedScript('assets/core/js/plugins/ui/fullcalendar/3.10/fullcalendar.min.js').done(function() {
            initFullCalendar_<?php echo $this->mid; ?>();
        });
        
    } else {
        initFullCalendar_<?php echo $this->mid; ?>();
    }
    
    $dv_search_<?php echo $this->mid; ?>.on('click', 'button.dataview-default-filter-btn', function() {
        filterClick_<?php echo $this->mid; ?> = true;
        $('#objectdatagrid-<?php echo $this->mid; ?>').fullCalendar('refetchEvents');
    });
    
    <?php 
    if (issetParam($this->row['IS_ENTER_FILTER']) == '1') { 
    ?>
    $dv_search_<?php echo $this->mid; ?>.on('keydown', 'input[data-path]', function(e){
        var code = e.keyCode || e.which;

        if (code == '13') {
            $dv_search_<?php echo $this->mid; ?>.find('button.dataview-default-filter-btn').click();
        }
    });

    $dv_search_<?php echo $this->mid; ?>.on('change', 'select[data-path], input.popupInit', function(){
        $dv_search_<?php echo $this->mid; ?>.find('button.dataview-default-filter-btn').click();
    });        
    
    $dv_search_<?php echo $this->mid; ?>.on('click', '.bp-icon-selection > li', function(){
        setTimeout(function() {
            $dv_search_<?php echo $this->mid; ?>.find('button.dataview-default-filter-btn').click();
        }, 10);
    }); 
    <?php
    }
    ?>
    
    $dv_search_<?php echo $this->mid; ?>.on('click', 'button.dataview-default-filter-reset-btn', function() {
        
        var $this = $(this), $thisForm = $this.closest('form');
                
        $thisForm.find("input[type=text], input[type=hidden], textarea").not("input[name='inputMetaDataId'], select.right-radius-zero").val('');
        $thisForm.find("input[type=radio], input[type=checkbox]").removeAttr('checked');
        $thisForm.find("input[type=radio], input[type=checkbox]").closest('span.checked').removeClass('checked');
        $thisForm.find("select.select2").select2('val', '');
        $thisForm.find('.bp-icon-selection > li.active').removeClass('active');
        $thisForm.find('.btn.removebtn[data-lookupid]').hide();
        $thisForm.find('.btn[data-lookupid][data-choosetype][data-idfield][onclick]').text('..');
        
        filterClick_<?php echo $this->mid; ?> = true;
        
        $('#objectdatagrid-<?php echo $this->mid; ?>').fullCalendar('refetchEvents');
    });

    $.contextMenu({
        selector: '#objectdatagrid-<?php echo $this->mid; ?> .fc-view-container .fc-day-grid-event, #objectdatagrid-<?php echo $this->mid; ?> .fc-view-container .fc-time-grid-event, #objectdatagrid-<?php echo $this->mid; ?> .fc-view-container .fc-day',
        events: {
            show: function(opt) {
                var $this = opt.$trigger;
                var $parent = $this.closest('.not-datagrid');
                var $popover = $this.closest('.fc-popover');
                
                if ($popover.length) {
                    $parent = $parent.find('.fc-day-grid-container');
                    $parent.find('.paneldv-selected-row').removeClass('paneldv-selected-row');
                    $parent.find('a[data-rid="' + $this.data('rid') + '"]').addClass('paneldv-selected-row');
                } else {
                    $parent.find('.paneldv-selected-row').removeClass('paneldv-selected-row');
                    $this.addClass('paneldv-selected-row');
                }
            }
        },
        build: function($trigger, e) {
            
            var rows = $trigger.data('rowdata');
            var contextMenuData = {
                <?php 
                $commandContextArray = Arr::sortBy('ORDER_NUM', $this->dataViewProcessCommand['commandContext'], 'asc');
                $cmi = 1;
                foreach ($commandContextArray as $cm => $row) {

                    $contextMenuIcon = str_replace('fa-', '', $row['ICON_NAME']);

                    if (isset($row['STANDART_ACTION'])) {

                        if ($row['STANDART_ACTION'] == 'criteria') {

                            echo '"' . $cmi . '": {'
                            . 'name: "' . $this->lang->line($row['PROCESS_NAME']) . '", '
                            . 'icon: "' . $contextMenuIcon . '", ';

                            if (isset($row['CRITERIA']) && $row['CRITERIA'] != '') {
                                echo '_dvSimpleCriteria: "'.$row['CRITERIA'].'",';
                            }

                            echo 'callback: function(key, options) {'
                            . 'transferProcessCriteria(\'' . $this->mid . '\', \'' . $row['BATCH_NUMBER'] . '\', \'context\', options.$trigger, {callerType: \''.$this->metaDataCode.'\'});'
                            . '}'
                            . '},';

                        } elseif ($row['STANDART_ACTION'] == 'processCriteria') {

                            echo '"' . $cmi . '": {'
                            . 'name: "' . $this->lang->line($row['PROCESS_NAME']) . '", '
                            . 'icon: "' . $contextMenuIcon . '", ';

                            if (isset($row['CRITERIA']) && $row['CRITERIA'] != '') {
                                echo '_dvSimpleCriteria: "'.$row['CRITERIA'].'",';
                            }

                            echo 'callback: function(key, options) {';

                            if ($row['ADVANCED_CRITERIA'] != '') {
                                echo '_dvAdvancedCriteria = "'.$row['ADVANCED_CRITERIA'].'";';
                            }

                            echo 'transferProcessAction(\'' . $row['PASSWORD_PATH'] . '\', \'' . $this->mid . '\', \'' . $row['PROCESS_META_DATA_ID'] . '\', \'' . $row['META_TYPE_ID'] . '\', \'processCriteria\', options.$trigger, {callerType: \''.$this->metaDataCode.'\'}, \'\');'
                            . '}'
                            . '},';

                        } else {

                            echo '"' . $cmi. '": {'
                            . 'name: "' . $this->lang->line($row['PROCESS_NAME']) . '", '
                            . 'icon: "' . $contextMenuIcon . '", ';

                            if (isset($row['CRITERIA']) && $row['CRITERIA'] != '') {
                                echo '_dvSimpleCriteria: "'.$row['CRITERIA'].'",';
                            }

                            echo 'callback: function(key, options) {'
                            . 'transferProcessAction(\'\', \'' . $this->mid . '\', \'' . $row['STANDART_ACTION'] . '\', \'' . Mdmetadata::$businessProcessMetaTypeId . '\', \'grid\', options.$trigger, {callerType: \''.$this->metaDataCode.'\'}, \'\');'
                            . '}'
                            . '},';
                        }

                    } else {

                        echo '"' . $cmi. '": {'
                            . 'name: "' . $this->lang->line($row['PROCESS_NAME']) . '", '
                            . 'icon: "' . $contextMenuIcon . '", ';

                            if (isset($row['CRITERIA']) && $row['CRITERIA'] != '') {
                                echo '_dvSimpleCriteria: "'.$row['CRITERIA'].'",';
                            }

                            echo 'callback: function(key, options) {'
                            . 'transferProcessAction(\'' . $row['PASSWORD_PATH'] . '\', \'' . $this->mid . '\', \'' . $row['PROCESS_META_DATA_ID'] . '\', \'' . $row['META_TYPE_ID'] . '\', \'grid\', options.$trigger, {callerType: \''.$this->metaDataCode.'\'}, \'\');'
                            . '}'
                            . '},';
                    }
                    $cmi++;
                }
                ?>
            };
            
            $.each(contextMenuData, function ($indexCn, $contextR) {
                if (typeof $contextR['_dvSimpleCriteria'] !== 'undefined' && $contextR['_dvSimpleCriteria']) {
                    var evalcriteria = $contextR['_dvSimpleCriteria'].toLowerCase();

                    if (evalcriteria.indexOf('#') > -1) {
                        var criteriaSplit = evalcriteria.split('#');
                        evalcriteria = trim(criteriaSplit[0]);
                    }

                    $.each(rows, function(index, row) {
                        if (evalcriteria.indexOf(index) > -1) {
                            row = (row === null) ? '' : row.toLowerCase();
                            var regex = new RegExp('\\b' + index + '\\b', 'g');
                            evalcriteria = evalcriteria.replace(regex, "'" + row.toString() + "'");
                        }
                    });

                    try {
                        if (!eval(evalcriteria)) {
                            ticket = false;
                            delete contextMenuData[$indexCn];
                        }
                    } catch (err) {
                        delete contextMenuData[$indexCn];
                        console.log(evalcriteria);
                    }
                }
            });
            
            <?php
            if (isset($this->dataViewWorkFlowBtn) && $this->dataViewWorkFlowBtn == true) { 
            ?>
            
            contextMenuData['sep1'] = "---------";
            
            $.ajax({
                type: 'post',
                url: 'mdobject/getWorkflowNextStatus',
                data: {metaDataId: '<?php echo $this->mid ?>', dataRow: rows},
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.status === 'success' && response.datastatus && response.data) {
                        
                        var rowId = '', realWfmName = '', advancedCriteria = '', wfmIcon = '';
                        
                        if (typeof rows.id !== 'undefined') {
                            rowId = rows.id;
                        }
                        
                        $.each(response.data, function (i, v) {
                            
                            if (typeof v.wfmstatusname != 'undefined' && typeof v.processname != 'undefined' && v.processname != '') {
                                v.wfmstatusname = plang.get(v.processname);
                            }
                            
                            if (v.wfmstatusicon) {
                                wfmIcon = '<i class="fa '+v.wfmstatusicon+'" style="color: '+v.wfmstatuscolor+'"></i> ';
                            }
                            
                            if (typeof v.usedescriptionwindow != 'undefined' && !v.usedescriptionwindow && typeof v.wfmuseprocesswindow != 'undefined' && !v.wfmuseprocesswindow) {
                                
                                contextMenuData[v.wfmstatusid] = {
                                    name: wfmIcon + v.wfmstatusname, 
                                    isHtmlName: true,  
                                    callback: function(key, options) {
                                        
                                        var $el = $('<span />', {text: v.wfmstatusname});
                                        
                                        if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                            $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                        }
                            
                                        changeWfmStatusId($el, v.wfmstatusid, '<?php echo $this->mid ?>', '<?php echo $this->refStructureId ?>', v.wfmstatuscolor, v.wfmstatusname, '', '', '');
                                    }
                                };
                                
                            } else {
                                if (typeof v.wfmstatusname != 'undefined' && v.wfmstatusname != '' && (v.wfmstatusprocessid == '' || v.wfmstatusprocessid == 'null' || v.wfmstatusprocessid == null)) {
                                    
                                    if (v.wfmisneedsign == '1') {
                                        
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon + v.wfmstatusname + ' <i class="fa fa-key"></i>', 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = $('<span />', {text: v.wfmstatusname});
                                                $el.attr('id', v.wfmstatusid);
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                        
                                                beforeSignChangeWfmStatusId($el, v.wfmstatusid, '<?php echo $this->mid ?>', '<?php echo $this->refStructureId ?>', v.wfmstatuscolor, v.wfmstatusname);
                                            }
                                        };
                                
                                    } else if (v.wfmisneedsign == '2') {
                                        
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon + v.wfmstatusname +' <i class="fa fa-key"></i>', 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = $('<span />', {text: v.wfmstatusname});
                                                $el.attr('id', v.wfmstatusid);
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                                
                                                beforeHardSignChangeWfmStatusId($el, v.wfmstatusid, '<?php echo $this->mid ?>', '<?php echo $this->refStructureId ?>', v.wfmstatuscolor, v.wfmstatusname);
                                            }
                                        };
                                        
                                    } else {
                                    
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon + v.wfmstatusname, 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = $('<span />', {text: v.wfmstatusname});
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                                
                                                changeWfmStatusId($el, v.wfmstatusid, '<?php echo $this->mid ?>', '<?php echo $this->refStructureId ?>', v.wfmstatuscolor, v.wfmstatusname);
                                            }
                                        };
                                        
                                    }
                                } else if (v.wfmstatusprocessid != '' && v.wfmstatusprocessid != 'null' && v.wfmstatusprocessid != null) {
                                
                                    var wfmStatusCode = ('wfmstatuscode' in Object(v)) ? v.wfmstatuscode : ''; 
                                    var metaTypeId = ('metatypeid' in Object(v)) ? v.metatypeid : '200101010000011';
                                    
                                    if (v.wfmisneedsign == '1') {
                                    
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon+v.wfmstatusname+' <i class="fa fa-key"></i>', 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = options.$trigger;
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                                
                                                transferProcessAction('signProcess', '<?php echo $this->mid ?>', v.wfmstatusprocessid, metaTypeId, 'toolbar', $el, {callerType: '<?php echo $this->metaDataCode; ?>', isWorkFlow: true, wfmStatusId: v.wfmstatusid, wfmStatusCode: wfmStatusCode}, 'dataViewId=<?php echo $this->mid ?>&refStructureId=<?php echo $this->refStructureId; ?>&statusId='+v.wfmstatusid+'&statusName='+v.wfmstatusname+'&statusColor='+v.wfmstatuscolor+'&rowId='+rowId);
                                            }
                                        };
                                        
                                    } else if (v.wfmisneedsign == '2') {
                                    
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon+v.wfmstatusname+' <i class="fa fa-key"></i>', 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = options.$trigger;
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                                
                                                transferProcessAction('hardSignProcess', '<?php echo $this->mid ?>', v.wfmstatusprocessid, metaTypeId, 'toolbar', $el, {callerType: '<?php echo $this->metaDataCode; ?>', isWorkFlow: true, wfmStatusId: +v.wfmstatusid, wfmStatusCode: wfmStatusCode}, 'dataViewId=<?php echo $this->mid ?>&refStructureId=<?php echo $this->refStructureId; ?>&statusId='+v.wfmstatusid+'&statusName='+v.wfmstatusname+'&statusColor='+v.wfmstatuscolor+'&rowId='+rowId);
                                            }
                                        };
                                        
                                    } else {
                                    
                                        contextMenuData[v.wfmstatusid] = {
                                            name: wfmIcon + v.wfmstatusname, 
                                            isHtmlName: true,  
                                            callback: function(key, options) {
                                                
                                                var $el = options.$trigger;
                                                
                                                if (typeof v.advancedCriteria !== "undefined" && v.advancedCriteria !== null) {
                                                    $el.attr('data-advanced-criteria', v.advancedCriteria.replace(/\"/g, ''));
                                                }
                                                
                                                transferProcessAction('', '<?php echo $this->mid ?>', v.wfmstatusprocessid, metaTypeId, 'toolbar', $el, {callerType: '<?php echo $this->metaDataCode; ?>', isWorkFlow: true, wfmStatusId: v.wfmstatusid, wfmStatusCode: wfmStatusCode}, 'dataViewId=<?php echo $this->mid ?>&refStructureId=<?php echo $this->refStructureId; ?>&statusId='+v.wfmstatusid+'&statusName='+v.wfmstatusname+'&statusColor='+v.wfmstatuscolor+'&rowId='+rowId);
                                            }
                                        };
                                        
                                    }
                                }    
                            }
                            
                        });
                    }
                }
            });
            
            if (!isIgnoreWfmHistory_<?php echo $this->mid; ?>) {
                
                contextMenuData['wfmHistory'] = {
                    name: plang.getDefault('wfm_log_history', 'Өөрчлөлтийн түүх харах'), 
                    isHtmlName: true,  
                    callback: function(key, options) {
                        seeWfmStatusForm(this, '<?php echo $this->mid ?>');
                    }
                };
            }
            <?php
            }
                
            $firstMetaId = isset($this->dataViewProcessCommand['commandAddMeta'][0]['PROCESS_META_DATA_ID']) ? $this->dataViewProcessCommand['commandAddMeta'][0]['PROCESS_META_DATA_ID'] : '';
            $firstMetaTypeId = isset($this->dataViewProcessCommand['commandAddMeta'][0]['META_TYPE_ID']) ? $this->dataViewProcessCommand['commandAddMeta'][0]['META_TYPE_ID'] : '';
            if ($firstMetaId) { 
            ?>
                            
                contextMenuData['0'] = {
                    name: plang.get('add_btn'), 
                    icon: 'plus',
                    isHtmlName: true,  
                    callback: function(key, options) {
                        _processAddonParam['addonJsonParam'] = JSON.stringify({"startDate":$(this).attr('data-date'),"endDate":$(this).attr('data-date')});
                        privateTransferProcessAction('<?php echo $this->mid; ?>', '<?php echo $firstMetaId; ?>', '<?php echo $firstMetaTypeId; ?>', 'toolbar', this, {callerType: '<?php echo $this->metaDataCode; ?>'}, $('div[id="objectdatagrid-<?php echo $this->mid; ?>"]'), false, undefined, undefined, undefined, undefined, undefined);
                    }
                };
                
            <?php } ?>
            
            var options =  {
                callback: function (key, opt) {
                    eval(key);
                },
                items: contextMenuData
            };
            
            return options;            
        }
    });    
    
});
        
function initFullCalendar_<?php echo $this->mid; ?>() {
    $('#objectdatagrid-<?php echo $this->mid; ?>').fullCalendar({
        <?php
        if (isset($this->callerType) || $refreshBtn) {
        ?>
        customButtons: {
            <?php
            if (isset($this->callerType) || $listDvBtn) {
            ?>
            listViewBtn: {
                bootstrapFontAwesome: 'fa-list', 
                click: function() {
                    dataViewer_<?php echo $this->mid; ?>(this, 'detail', '<?php echo $this->mid; ?>');
                }
            }, 
            <?php            
            }
            if ($refreshBtn) {
            ?>
            refreshBtn: {
                bootstrapFontAwesome: 'fa-refresh', 
                click: function() {
                    explorerRefresh_<?php echo $this->mid; ?>(this);
                }
            }, 
            <?php
            }
            ?>
        },
        <?php
        }
        ?>
        header: {
            left: '', //'prev,next today',
            center: 'prev title next today<?php echo ($refreshBtn ? ' refreshBtn' : ''); ?>',
            right: '<?php echo $headerRight; ?>'
        },
        defaultDate: '<?php echo Date::currentDate('Y-m-d'); ?>', 
        defaultView: '<?php echo ($defaultview_calendar) ? $defaultview_calendar : 'month' ?>',
        themeSystem: 'bootstrap4',
        timezone: 'Asia/Ulaanbaatar', 
        navLinks: true,
        height: 'auto', 
        contentHeight: 'auto', 
        editable: <?php echo ($eventDropProcessCode || $eventResizeProcessCode) ? 'true' : 'false'; ?>,
        eventLimit: true,
        displayEventTime: false,
        nextDayThreshold: '00:00:00', 
        minTime: '<?php echo $minTime; ?>', 
        maxTime: '<?php echo $maxTime; ?>', 
        fixedWeekCount: false, 
        aspectRatio: <?php echo $aspectRatio ? $aspectRatio : '1.35'; ?>, 
        slotDuration: '<?php echo $slotDuration ? $slotDuration : '00:30:00'; ?>', 
        views: {
            month: {eventLimit: 6, titleFormat: 'YYYY он MMMM'}, 
            basicWeek: {eventLimit: false}, 
            basicDay: {eventLimit: false}
        },
        events: function (start, end, timezone, callback) {
            
            var defaultCriteriaData = '';
            var $packageTab = $();
            
            <?php
            if (!isset($this->uriParams) || (isset($this->uriParams) && !$this->uriParams)) {
            ?>
            var $filterStartDate = $dv_search_<?php echo $this->mid; ?>.find('[data-path="filterStartDate"]');
            
            if ($filterStartDate.length && filterClick_<?php echo $this->mid; ?> == false) {
                var filterStartDate = moment(start).format('YYYY-MM-DD');
                var filterEndDate = moment(end).format('YYYY-MM-DD');

                $dv_search_<?php echo $this->mid; ?>.find('[data-path="filterStartDate"]').datepicker('update', filterStartDate);
                $dv_search_<?php echo $this->mid; ?>.find('[data-path="filterEndDate"]').datepicker('update', filterEndDate);
            } 
            
            defaultCriteriaData = $dv_search_<?php echo $this->mid; ?>.serialize();
            $packageTab = objectdatagrid_<?php echo $this->mid; ?>.closest('div.package-meta-tab');
            
            if ($filterStartDate.length == 0) {
                var filterStartDate = moment(start).format('YYYY-MM-DD');
                var filterEndDate = moment(end).format('YYYY-MM-DD');
                defaultCriteriaData += '&param[filterStartDate]='+filterStartDate+'&criteriaCondition[filterStartDate]==&param[filterEndDate]='+filterEndDate+'&criteriaCondition[filterEndDate]==';
            }
            <?php
            }
            ?>
            
            if ($packageTab.length) {
                var packageId = $packageTab.attr('data-realpack-id');
                var $form = $('#package-meta-' + packageId).find('form.package-criteria-form-' + packageId + '_<?php echo $this->mid; ?>');
                defaultCriteriaData = $form.serialize();
            }
            
            objectdatagrid_<?php echo $this->mid; ?>.find('[data-fca="1"]').remove();
            
            var postParams = {
                metaDataId: '<?php echo $this->mid; ?>',  
                defaultCriteriaData: defaultCriteriaData, 
                workSpaceId: '<?php echo $this->workSpaceId; ?>', 
                workSpaceParams: '<?php echo $this->workSpaceParams; ?>', 
                uriParams: '<?php echo $this->uriParams; ?>', 
                pagingWithoutAggregate: 1, 
                rows: 20000
            };
            
            if (typeof _isRunAfterProcessSave !== 'undefined') {
                delete postParams.isNotUseReport;

                if (_isRunAfterProcessSave) {
                    postParams.isNotUseReport = 1;
                    _isRunAfterProcessSave = false;
                }
            }
                
            $.ajax({
                type: 'post',
                url: 'mdobject/getDataViewMergeRows',
                data: postParams,
                success: function(response) {
                    
                    if (response.status == 'success') {
                        
                        <?php
                        if (isset($this->row['dataViewLayoutTypes']['calendar']['fields']['color'])) {
                            $colorField = 'color: event[fc_'.$this->mid.'.color],';
                        }
                        ?>
                        
                        callback(response.rows.map(function(event) {
                            
                            return {
                                id: event[fc_<?php echo $this->mid; ?>.id], 
                                title: event[fc_<?php echo $this->mid; ?>.title], 
                                start: event[fc_<?php echo $this->mid; ?>.start], 
                                end: event[fc_<?php echo $this->mid; ?>.end], 
                                <?php echo $colorField; ?> 
                                rowdata: event
                            };
                        }));
                        
                        <?php
                        if (isset($this->uriParams) && $this->uriParams) {
                        ?>
                        objectdatagrid_<?php echo $this->mid; ?>.fullCalendar('gotoDate', moment(response.rows[0][fc_<?php echo $this->mid; ?>.start], 'YYYY-MM-DD'));
                        <?php
                        }
                        ?>
                        
                    } else {
                        PNotify.removeAll();
                        new PNotify({
                            title: response.status,
                            text: response.message,
                            type: response.status,
                            sticker: false
                        });
                    }
                    
                    filterClick_<?php echo $this->mid; ?> == false;
                },
                error: function(response) {
                    console.log(response);
                }
            }); 
        },
        <?php
        if ($eventResizeProcessCode) {
        ?>
        eventResize: function(event, delta, revertFunc) {
        
            var paramData = [];
            paramData.push({
                fieldPath: 'id', 
                inputPath: 'id', 
                value: event.id
            }, {
                fieldPath: 'plannedenddate', 
                inputPath: 'plannedenddate', 
                value: moment(event.start).format('YYYY-MM-DD HH:mm:ss')
            }, {
                fieldPath: 'startDate', 
                inputPath: 'startDate', 
                value: moment(event.start).format('YYYY-MM-DD HH:mm:ss')
            });
            
            if (event.end) {
                paramData.push({
                    fieldPath: 'endDate', 
                    inputPath: 'endDate', 
                    value: moment(event.end).format('YYYY-MM-DD HH:mm:ss')
                });
            }
        
            $.ajax({
                type: 'post',
                url: 'mdwebservice/execProcess', 
                data: {processCode: '<?php echo $eventResizeProcessCode; ?>', paramData: paramData},
                dataType: 'json',
                async: false, 
                success: function(response) {
                    if (response.status != 'success') {
                        revertFunc();
                        PNotify.removeAll();
                        new PNotify({
                            title: response.status,
                            text: response.text,
                            type: response.status,
                            sticker: false, 
                            addclass: pnotifyPosition
                        });
                    } else {
                        explorerRefresh_<?php echo $this->mid; ?>(this);
                    }
                }
            });
        }, 
        <?php
        }
        if ($eventDropProcessCode) {
        ?>
        eventDrop: function(event, delta, revertFunc) {
            var paramData = [];
            var eventStartDate = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            
            paramData.push({
                fieldPath: 'id', 
                inputPath: 'id', 
                value: event.id
            }, {
                fieldPath: 'plannedenddate', 
                inputPath: 'plannedenddate', 
                value: eventStartDate
            }, {
                fieldPath: 'startDate', 
                inputPath: 'startDate', 
                value: eventStartDate
            });
            
            if (event.end) {
                var eventEndDate = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
                var modifyEndDate = bpDateTimeModify(eventEndDate, '-1 day');
                
                if (modifyEndDate == eventStartDate) {
                    eventEndDate = eventStartDate;
                }
                
                paramData.push({
                    fieldPath: 'endDate', 
                    inputPath: 'endDate', 
                    value: eventEndDate
                });
            } else {
                paramData.push({
                    fieldPath: 'endDate', 
                    inputPath: 'endDate', 
                    value: eventStartDate
                });
            }
        
            $.ajax({
                type: 'post',
                url: 'mdwebservice/execProcess', 
                data: {
                    processCode: '<?php echo $eventDropProcessCode; ?>', 
                    paramData: paramData
                },
                dataType: 'json',
                async: false, 
                success: function(response) {
                    if (response.status != 'success') {
                        revertFunc();
                        PNotify.removeAll();
                        new PNotify({
                            title: response.status,
                            text: response.text,
                            type: response.status,
                            sticker: false, 
                            addclass: pnotifyPosition
                        });
                    } else {
                        explorerRefresh_<?php echo $this->mid; ?>(this);
                    }
                }
            });
        },
        <?php
        }
        if (isset($this->dataViewProcessCommand['commandAddMeta']) && is_countable($this->dataViewProcessCommand['commandAddMeta'])) {
            
            $commandAddMeta = $this->dataViewProcessCommand['commandAddMeta'];
            $commandAddMetaCount = count($commandAddMeta);
            
            $firstMetaId = isset($commandAddMeta[0]['PROCESS_META_DATA_ID']) ? $commandAddMeta[0]['PROCESS_META_DATA_ID'] : '';
            $firstMetaTypeId = isset($commandAddMeta[0]['META_TYPE_ID']) ? $commandAddMeta[0]['META_TYPE_ID'] : '';
        ?>
        dayClick: function(date, jsEvent, view) {
            
            if (!doubleClick_<?php echo $this->mid; ?>) {
                
                doubleClick_<?php echo $this->mid; ?> = true;
                setTimeout(function() { doubleClick_<?php echo $this->mid; ?> = false; }, 250);
                
            } else {
                
                var dateGet = moment(date);
                var postDates = {
                    startDate: dateGet.format('YYYY-MM-DD'), 
                    endDate: dateGet.format('YYYY-MM-DD'), 
                    startTime: dateGet.format('HH:mm'), 
                    endTime: dateGet.add(30, 'minutes').format('HH:mm')
                };
                
                if (view.name == 'month') {
                    delete postDates.startTime;
                    delete postDates.endTime;
                }
                
                _processAddonParam['addonJsonParam'] = JSON.stringify(postDates);
                
                <?php if ($firstMetaId) { ?>
                    dvCalendarRunMeta_<?php echo $this->mid; ?>(this, '<?php echo $firstMetaId; ?>', '<?php echo $firstMetaTypeId; ?>');
                <?php } ?>
            }
        }, 
        <?php
        }
        ?>
        viewRender: function(view, element) {
            
            <?php
            if (isset($commandAddMeta)) {
            ?>
                var $parent = element.closest('.fc');
                var $headerToolbar = $parent.find('.fc-header-toolbar');
                var $toolbar = $headerToolbar.find('> .fc-left');

                $headerToolbar.find('.fc-left-bps').remove();

                <?php
                if ($commandAddMetaCount == 1) {
                ?>
                $toolbar.after('<div class="fc-left-bps float-left">'+
                    '<div class="btn-group">'+
                        '<button class="btn btn-secondary btn-sm" type="button" onclick="dvCalendarRunMeta_<?php echo $this->mid; ?>(this, \'<?php echo $firstMetaId; ?>\', \'<?php echo $firstMetaTypeId; ?>\');">'+plang.get('add_btn')+'</button>'+
                    '</div>'+
                '</div>');
                <?php
                } else {
                ?>
                $afterHtml = '';
                <?php
                    foreach ($commandAddMeta as $bpRow) {
                        if (!$bpRow['CRITERIA'] && !$bpRow['ADVANCED_CRITERIA']) { 
                ?>
                    $afterHtml += '<a href="javascript:;" class="dropdown-item" onclick="dvCalendarRunMeta_<?php echo $this->mid; ?>(this, \'<?php echo $bpRow['PROCESS_META_DATA_ID']; ?>\', \'<?php echo $bpRow['META_TYPE_ID']; ?>\');"><i class="icon-arrow-right5"></i> <?php echo $this->lang->line($bpRow['PROCESS_NAME']); ?></a>';
                <?php 
                        }
                    }
                ?>
                if ($afterHtml) {
                    $toolbar.after('<div class="fc-left-bps float-left">'+
                        '<div class="btn-group">'+
                            '<button type="button" class="btn btn-secondary btn-sm btn-icon dropdown-toggle" data-toggle="dropdown">'+
                                '<i class="icon-plus3"></i>'+
                            '</button>'+
                            '<div class="dropdown-menu dropdown-menu-left">'+
                                $afterHtml + 
                            '</div>'+
                        '</div>'+
                    '</div>');
                }
            <?php
                }
            }
            
            if ($todayFocus) {
            ?>           
                if (view.name == 'month') {
                    
                    var $today = objectdatagrid_<?php echo $this->mid; ?>.find('.fc-content-skeleton .fc-today');
                    
                    if ($today.length) {
                        var windowHeight = $(window).height() - 105;
                        var offsetTop = $today.offset().top - 105;
                        var dayHeight = Number(objectdatagrid_<?php echo $this->mid; ?>.find('.fc-day:eq(0)').height());

                        if ((windowHeight - offsetTop) < dayHeight) {
                            $('html, body').stop().animate({
                                scrollTop: offsetTop
                            }, '1000');
                        }
                    }
                }
            <?php
            }
            ?>
        },
        eventRender: function(event, element, view) {
            
            if (event.id) {
                
                element.find('.fc-title').html(html_entity_decode(event.title, 'ENT_QUOTES'));
                
                var rData = event.rowdata;
                element.attr({'data-rid': event.id, 'data-rowdata': JSON.stringify(rData)});
                
                <?php
                if ($eventPosition1) {
                ?>
                if (rData.hasOwnProperty('<?php echo $eventPosition1; ?>') && rData.<?php echo $eventPosition1; ?>) {
                    element.find('.fc-content').append('<div class="fc-event-pos-1">'+rData.<?php echo $eventPosition1; ?>+'</div>');
                }
                <?php
                }
                if ($eventPosition2) {
                ?>
                if (rData.hasOwnProperty('<?php echo $eventPosition2; ?>') && rData.<?php echo $eventPosition2; ?>) {
                    element.find('.fc-content').append('<div class="fc-event-pos-2">'+rData.<?php echo $eventPosition2; ?>+'</div>');
                }
                <?php    
                }
                if ($timeViewVerticalAlign == 'middle') {
                ?>
                                    
                var startMoment = moment(event.start);
                var endMoment = moment(event.end);
                var diffMinutes = endMoment.diff(startMoment, 'minutes');
                
                if (diffMinutes > 60) {
                    element.find('.fc-content').addClass('fc-content-middle');
                } else if (diffMinutes <= 30) {
                    element.addClass('fc-normal-lineheight');
                }
                <?php
                }
                if ($image1) {
                ?>
                                    
                if (rData.hasOwnProperty('<?php echo $image1; ?>')) {
                    element.find('.fc-title').prepend('<img src="api/image_thumbnail?width=36&src='+rData.<?php echo $image1; ?>+'" onerror="onFCUserImgError(this);" class="rounded-circle fc-image1"/>');
                } 
                
                <?php
                }
                if ($borderLeftColor) {
                ?>
                                    
                if (rData.hasOwnProperty('<?php echo $borderLeftColor; ?>')) {
                    element.css('border-left', '7px solid ' + rData['<?php echo $borderLeftColor; ?>']);
                }
                
                <?php
                }
                if ($borderRightColor) {
                ?>
                                    
                if (rData.hasOwnProperty('<?php echo $borderRightColor; ?>')) {
                    element.css('border-right', '7px solid ' + rData['<?php echo $borderRightColor; ?>']);
                }
                
                <?php
                }
                if ($isTooltipField) {
                    $tooltipPos6_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6_labelname']);
                    $tooltipPos6Desc_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos6Desc_labelname']);
                    $tooltipPos7_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos7_labelname']);
                    $tooltipPos8_labelname = issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos8_labelname']);
                ?>
                element.qtip({
                    content: {
                        text: function(event, api) {
                            
                            var rowData = api.elements.target.data('rowdata');
                            
                            var content = '<div class="card pb0 mb0 border-0 shadow-0">'+
                                '<div class="card-body">'+
                                    '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap">'+
                                        '<div>'+
                                            <?php
                                            if ($tooltipPos1) {
                                            ?>
                                            '<h6 class="text-primary mb-1" title="<?php echo $this->lang->line(issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['tooltipPos1_labelname'])); ?>">'+dvFieldValueShow(rowData.<?php echo $tooltipPos1; ?>)+'</h6>'+
                                            <?php
                                            }
                                            if ($tooltipPos2) {
                                            ?>
                                            '<p class="mb-2 font-weight-bold">'+dvFieldValueShow(rowData.<?php echo $tooltipPos2; ?>)+'</p>'+
                                            <?php
                                            }
                                            if ($tooltipPos3) {
                                            ?>
                                            '<p class="mb-2">'+dvFieldValueShow(rowData.<?php echo $tooltipPos3; ?>)+'</p>'+                            
                                            <?php
                                            }
                                            if ($tooltipImage1) {
                                            ?>
                                            '<img src="api/image_thumbnail?width=50&src='+rowData.<?php echo $tooltipImage1; ?>+'" onerror="onFCUserImgError(this);" class="rounded-circle border-gray border-1" width="50" height="50"/> '+
                                            <?php
                                            }
                                            if ($tooltipPos4) {
                                            ?>
                                            dvFieldValueShow(rowData.<?php echo $tooltipPos4; ?>)+
                                            <?php
                                            }
                                            ?>
                                            '</div>'+
                                            '<ul class="list list-unstyled mb-0 mt-3 mt-sm-0 ml-auto" style="min-width: 100px">'+
                                                <?php
                                                if ($tooltipPos5) {
                                                ?>
                                                '<li><span class="text-muted">'+moment(rowData.<?php echo $tooltipPos5; ?>).format('YYYY-MM-DD')+'</span></li>'+
                                                <?php
                                                }
                                                if ($tooltipPos8) {
                                                ?>
                                                '<li>'+
                                                    '<?php echo $tooltipPos8_labelname ? $this->lang->line($tooltipPos8_labelname).':<br />' : ''; ?><span class="badge bg-warning-400 ml-auto" style="background:#AAA;color:#FFF;">'+dvFieldValueShow(rowData.<?php echo $tooltipPos8; ?>)+'</span>'+
                                                '</li>'+
                                                <?php
                                                }
                                                if ($tooltipPos6) {
                                                ?>
                                                '<li>'+
                                                    '<?php echo $tooltipPos6_labelname ? $this->lang->line($tooltipPos6_labelname).':<br />' : ''; ?><span class="badge bg-warning-400 ml-auto" style="<?php echo $tooltipPos6TextColor ? 'color: '.$tooltipPos6TextColor.';' : ''; echo $tooltipPos6Color ? 'background-color: \'+rowData.'.$tooltipPos6Color.'+\'' : ''; ?>">'+dvFieldValueShow(rowData.<?php echo $tooltipPos6; ?>)+'</span>'+
                                                '</li>'+
                                                <?php
                                                }
                                                if ($tooltipPos7) {
                                                ?>
                                                '<li>'+
                                                    '<?php echo $tooltipPos7_labelname ? $this->lang->line($tooltipPos7_labelname).':<br />' : ''; ?><span class="badge bg-success-400 ml-auto" <?php echo $tooltipPos7Color ? 'style="background-color: \'+rowData.'.$tooltipPos7Color.'+\'"' : ''; ?>>'+dvFieldValueShow(rowData.<?php echo $tooltipPos7; ?>)+'</span>'+
                                                '</li>'+
                                                 <?php
                                                }
                                                if ($tooltipPos14) {
                                                ?>
                                                '<li>'+
                                                    '<?php echo $tooltipPos14_labelname ? $this->lang->line($tooltipPos14_labelname).':<br />' : ''; ?><span class="badge bg-success-400 ml-auto" <?php echo $tooltipPos14Color ? 'style="background-color: \'+rowData.'.$tooltipPos14Color.'+\'"' : ''; ?>>'+dvFieldValueShow(rowData.<?php echo $tooltipPos14; ?>)+'</span>'+
                                                '</li>'+
                                                 <?php
                                                }
                                                ?>
                                            '</ul>'+
                                        '</div>';
                                        <?php 
                                        if ($tooltipPos10 && $tooltipPos9) { 
                                        ?>
                                            content += '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap mt10 ">'+
                                                '<div class="pt10 w-100" style="border-top: 1px solid #e5e5e5;">'+
                                                    '<label class="w-100"><i class="fa fa-calendar"></i> <?php echo $tooltipPos9_labelname ? $tooltipPos9_labelname : ''; ?>: ' + dvFieldValueShow(rowData.<?php echo $tooltipPos9; ?>) + '</label>'+
                                                    '<label class="w-100"><i class="fa fa-calendar"></i> <?php echo $tooltipPos10_labelname ? $tooltipPos10_labelname : ''; ?>: ' + dvFieldValueShow(rowData.<?php echo $tooltipPos10; ?>) + '</label>'+
                                                '</div>'+
                                            '</div>';
                                        <?php 
                                        }
                                        if ($tooltipPos11 && $tooltipPos11_labelname) { 
                                        ?>
                                            if (typeof rowData.<?php echo $tooltipPos11; ?> !== 'undefined' && rowData.<?php echo $tooltipPos11; ?>) {
                                                content += '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap mt10 ">'+
                                                    '<div class="pt10 pb10 w-100" style="border-top: 1px solid #e5e5e5;"><?php echo $tooltipPos11_labelname; ?><hr class="mt0 mb5 border-0">'+
                                                        rowData.<?php echo $tooltipPos11; ?> +
                                                    '</div>'+
                                                '</div>';
                                            }
                                        <?php 
                                        }
                                        if ($tooltipPos12 && $tooltipPos12_labelname) { 
                                        ?>
                                            if (typeof rowData.<?php echo $tooltipPos12; ?> !== 'undefined' && rowData.<?php echo $tooltipPos12; ?>) {
                                                content += '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap mt10 ">'+
                                                    '<div class="pt10 pb10 w-100 pull-left" style="border-top: 1px solid #e5e5e5;"><?php echo $tooltipPos12_labelname; ?><hr class="mt0 mb5 border-0">'+
                                                        rowData.<?php echo $tooltipPos12; ?> +
                                                    '</div>'+ 
                                                '</div>';
                                            }
                                        <?php 
                                        }
                                        if ($tooltipPos13 && $tooltipPos13_labelname) { 
                                        ?>
                                            if (typeof rowData.<?php echo $tooltipPos13; ?> !== 'undefined' && rowData.<?php echo $tooltipPos13; ?>) {
                                            content += '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap mt10 ">'+
                                                    '<div class="pt10 pb10 w-100" style="border-top: 1px solid #e5e5e5;"><?php echo $tooltipPos13_labelname; ?><hr class="mt0 mb5 border-0">'+
                                                        rowData.<?php echo $tooltipPos13; ?> +
                                                    '</div>'+ 
                                                '</div>';
                                            }
                                        <?php 
                                        }
                                        if ($tooltipPos6Desc && $tooltipPos6Desc_labelname) { 
                                        ?>
                                            if (typeof rowData.<?php echo $tooltipPos6Desc; ?> !== 'undefined' && rowData.<?php echo $tooltipPos6Desc; ?>) {
                                            content += '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap mt10 ">'+
                                                    '<div class="pt10 pb10 w-100" style="border-top: 1px solid #e5e5e5;"><?php echo $tooltipPos6Desc_labelname; ?><hr class="mt0 mb5 border-0">'+
                                                        rowData.<?php echo $tooltipPos6Desc; ?> +
                                                    '</div>'+ 
                                                '</div>';
                                            }
                                        <?php 
                                        } 
                                        ?>
                                    content += '</div>'+
                                '</div>';
                            
                            return content;
                        }
                    },
                    position: {
                        effect: false,
                        my: 'bottom center',
                        at: 'top center',
                        viewport: $(window) 
                    },
                    show: {
                        effect: false, 
                        delay: 700
                    },
                    hide: {
                        effect: false, 
                        fixed: true,
                        delay: 70
                    }, 
                    style: {
                        classes: 'qtip-bootstrap',
                        width: 500, 
                        tip: {
                            width: 12,
                            height: 7
                        }
                    }
                });
                <?php
                }
                ?>
                                    
                element.off('dblclick').on('dblclick', function() {
                    var row = event.rowdata;
                    <?php if ($this->dataGridOptionData['DRILLDBLCLICKROW'] == 'true' && $this->dataGridOptionData['DRILL_CLICK_FNC']) {
                        echo $this->dataGridOptionData['DRILL_CLICK_FNC'];
                    } ?>
                    /* if ('<?php echo $this->mid; ?>' === '1625136903388818') {
                        drillDownTransferProcessAction('transferProcessAction', '1', '', '', '1625136903388818', '16255573575641', 'toolbar', '', this, {callerType: 'LMS_SUBJECT_CALENDAR_LIST', isDrillDown: true}, undefined, undefined, event.rowdata);
                    }
                    if ('<?php echo $this->mid; ?>' === '16252423945501') {
                        drillDownTransferProcessAction('transferProcessAction', '1', '', '', '16252423945501', '16255573575641', 'toolbar', '', this, {callerType: 'LMS_SUBJECT_CALENDAR_SESSION_LIST', isDrillDown: true}, undefined, undefined, event.rowdata);
                    } */
                });

                if ('<?php echo $this->mid; ?>' == '1596004315173' || '1596004373847' == '<?php echo $this->mid; ?>') {
                    element.off('click').on('click', function(e) {
                        runDrillDV_<?php echo $this->mid; ?>($(e)[0]['currentTarget']['dataset']['rowdata']);
                    });
                }
            }
        }, 
        eventAfterRender: function(event, element, view) {
            
            <?php
            if ($isAddonField) {
            ?>
                                
            if (!event.id) {
                
                var rowData = event.rowdata;
                var startDate = moment(event.start).format('YYYY-MM-DD');
                var prependHtml = '';
                
                <?php
                if ($topLeft1) {
                ?>
                if (rowData.<?php echo $topLeft1; ?> !== '' && rowData.<?php echo $topLeft1; ?> !== null) {
                    prependHtml += '<span class="badge badge-primary" data-fca="1" data-one="1" title="<?php echo $this->lang->line(issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft1_labelname'])); ?>">'+rowData.<?php echo $topLeft1; ?>+'</span>';
                }
                <?php
                }
                if ($topLeft2) {
                ?>

                if (rowData.<?php echo $topLeft2; ?> !== '' && rowData.<?php echo $topLeft2; ?> !== null) {
                    prependHtml += '<span class="badge badge-secondary" data-fca="1" data-one="1" title="<?php echo $this->lang->line(issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft2_labelname'])); ?>">'+rowData.<?php echo $topLeft2; ?>+'</span>';
                }
                
                <?php
                }
                if ($topLeft3) {
                ?>
                if (rowData.<?php echo $topLeft3; ?> !== '' && rowData.<?php echo $topLeft3; ?> !== null) {
                    prependHtml += '<span class="badge badge-info" data-fca="1" data-one="2" title="<?php echo $this->lang->line(issetParam($this->row['dataViewLayoutTypes']['calendar']['fields']['topLeft3_labelname'])); ?>">'+rowData.<?php echo $topLeft3; ?>+'</span>';
                }
                <?php
                }
                ?>     
                                    
                if (view.name == 'month') {
                    
                    var $dayCell = element.closest('.fc-row').find('td[data-date="'+startDate+'"].fc-day-top');
                    
                    $dayCell.find('[data-one="1"]').remove();
                    $dayCell.prepend(prependHtml);
                    
                } else if (view.name == 'basicWeek') {
                    
                    var d = new Date(startDate);
                    var n = d.getDay();
                    var i = (n == 0) ? 6 : n - 1;
                    var $dayCell = element.closest('.fc-content-skeleton').find('> table > tbody > tr:eq(0) > td:eq('+i+')');
                    
                    $dayCell.find('[data-one="1"]').remove();
                    $dayCell.prepend(prependHtml);
                    
                } else if (view.name == 'basicDay') {
                    
                    var $dayCell = element.closest('.fc-content-skeleton').find('> table > tbody > tr:eq(0) > td');
                    
                    $dayCell.find('[data-one="1"]').remove();
                    $dayCell.prepend(prependHtml);
                } 
                
                <?php
                if ($loopColorCircle) {
                ?>
                if (rowData.hasOwnProperty('<?php echo $loopColorCircle; ?>') 
                    && rowData.<?php echo $loopColorCircle; ?> !== '' 
                    && rowData.<?php echo $loopColorCircle; ?> !== null) {  
                
                    var loopColorCircle = rowData.<?php echo $loopColorCircle; ?>;
                    var loopColorCircleTitle = '';
                    
                    <?php
                    if ($loopColorCircleTitle) {
                    ?>
                        loopColorCircleTitle = rowData.hasOwnProperty('<?php echo $loopColorCircleTitle; ?>') ? rowData.<?php echo $loopColorCircleTitle; ?> : '';
                    <?php
                    }
                    ?>
                                            
                    var loopColorCircleHtml = '<span class="badge badge-mark ml5" data-title="'+loopColorCircleTitle+'" style="background-color: '+loopColorCircle+'; border-color: '+loopColorCircle+'"></span>';
                    
                    if (view.name == 'month') {
                       
                        var $badgeGroup = $dayCell.find('.span-group-control');
                        
                        if ($badgeGroup.length) {
                            $dayCell = $badgeGroup;
                        } else {
                            $dayCell.append('<div class="span-group" data-fca="1" date="'+startDate+'">'+
                                '<div class="span-group-control"></div>'+
                                '<span class="input-group-append">'+
                                    '<button class="btn btn-light" type="button">...</button>'+
                                '</span>'+
                            '</div>');
                            $dayCell = $dayCell.find('.span-group-control');
                            
                            fcLoopAddonFieldsQtip($dayCell.parent().find('.btn'));
                        }
                        
                        $dayCell.append(loopColorCircleHtml);
                        
                    } else {
                        
                        if ($dayCell) {
                            
                            var $badgeGroup = $dayCell.find('.span-group-control');
                            
                            if ($badgeGroup.length) {
                                $dayCell = $badgeGroup;
                            } else {
                                $dayCell.prepend('<div class="span-group" data-fca="1">'+
                                    '<div class="span-group-control"></div>'+
                                    '<span class="input-group-append">'+
                                        '<button class="btn btn-light" type="button">...</button>'+
                                    '</span>'+
                                '</div>');
                                $dayCell = $dayCell.find('.span-group-control');

                                fcLoopAddonFieldsQtip($dayCell.parent().find('.btn'));
                            }
                            
                            $dayCell.prepend(loopColorCircleHtml);
                        }
                    } 
                }
                <?php
                }
                ?> 

                element.remove();
            }
            <?php
            } 
            if ($timeViewVerticalAlign == 'middle') {
            ?>
                if (event.id && (view.name == 'agendaWeek' || view.name == 'agendaDay')) {
                    var startMoment = moment(event.start);
                    var endMoment = moment(event.end);
                    var diffMinutes = endMoment.diff(startMoment, 'minutes');
                    if (diffMinutes <= 20) {
                        var getTop = element.css('top');
                        element.css('top', parseFloat(getTop) - 7);
                    }
                }
            <?php 
            } 
            ?>
        }, 
        eventAfterAllRender: function(view) {
            
            if (view.name == 'basicDay' || view.name == 'agendaDay') {
                $('html,body').animate({
                    scrollTop: 0
                }, 'fast');
            }
            
            <?php
            if ($isAddonField) {
            ?>
            var $el = view.calendar.el;
            var filterStartDate = moment(view.start).format('YYYY-MM-DD');
            var filterEndDate = moment(view.end).format('YYYY-MM-DD');
            var now = new Date(filterEndDate);
            var d = new Date(filterStartDate)
            var daysOfYears = {};
            var array = $('#objectdatagrid-<?php echo $this->mid; ?>').fullCalendar('clientEvents');
            
            for (var a in array) {
                daysOfYears[moment(array[a]['start']).format('YYYY-MM-DD')] = 1;
            }
            
            for (d; d <= now; d.setDate(d.getDate() + 1)) {
                
                var checkDate = moment(d).format('YYYY-MM-DD');
                
                if (!daysOfYears.hasOwnProperty(checkDate)) {
                    $el.find('[data-date="'+checkDate+'"]').find('span.badge').remove();
                }
            }
            <?php
            }
            ?>
                            
            var $headContainer = $('#objectdatagrid-<?php echo $this->mid; ?> .fc-head');
            $headContainer.find('tr.cloned-head').remove();
            var $cloneHead = $('<tr class="fc-first fc-last cloned-head"></tr>').css({
                zIndex: 5,
                display: 'none'
            }).appendTo($headContainer);

            $('#objectdatagrid-<?php echo $this->mid; ?> .fc-head th').each(function() {
                var $this = $(this);
                var $cloned = $this.clone();
                $cloned.css('width', $this.width());
                $cloned.appendTo($cloneHead);
            });

            $(document).on('scroll', function() {
                var scroll = $(document).scrollTop() + 60;
                var show = $headContainer.offset().top + $cloneHead.outerHeight() < (scroll + 10);
                if (show) {
                    $cloneHead.css({
                        display: '', 
                        position: 'absolute',
                        top: scroll - $headContainer.offset().top
                    });
                } else {
                    $cloneHead.hide();		
                }
            });	
        }
    });
}
function dvCalendarRunMeta_<?php echo $this->mid; ?>(elem, processId, metaTypeId) {
    transferProcessAction('', '<?php echo $this->mid; ?>', processId, metaTypeId, 'toolbar', elem, {callerType: '<?php echo $this->metaDataCode; ?>'}, undefined, undefined, undefined, undefined, '');
}
function explorerRefresh_<?php echo $this->mid; ?>(elem) {
    $('#objectdatagrid-<?php echo $this->mid; ?>').fullCalendar('refetchEvents');
}
function dvRowSelector_<?php echo $this->mid ?>(e, type, isIgnoreAlert) {
    console.log('dvRowSelector_');
}
function onFCUserImgError(source) {
    source.src = "assets/custom/addon/admin/layout4/img/user.png";
    source.onerror = "";
    return true;
}
function fcLoopAddonFieldsQtip(element) {
    element.qtip({
        content: {
            text: function(event, api) {

                var elem = api.elements.target.closest('.span-group').find('span.badge-mark');
                var list = '';
                
                elem.each(function(){
                    var $this = $(this);
                    var loopColorCircle = $this.css('background-color');
                    list += '<li class="d-sm-flex align-items-sm-center"><span class="badge badge-mark mr10" style="background-color: '+loopColorCircle+'; border-color: '+loopColorCircle+'; width: 20px; min-width: 20px; height: 20px"></span> '+$this.attr('data-title')+'</li>';
                });

                var content = '<ul class="list list-unstyled mb-0">'+list+'</ul>';

                return content;
            }
        },
        position: {
            effect: false,
            my: 'bottom center',
            at: 'top center',
            viewport: $(window) 
        },
        show: {
            event: 'click',
            solo: true
        },
        hide: {
            event: 'click unfocus', 
            effect: false, 
            fixed: true,
            delay: 70
        }, 
        style: {
            classes: 'qtip-bootstrap',
            width: 330, 
            tip: {
                width: 12,
                height: 7
            }
        }
    });
}
function runDrillDV_<?php echo $this->mid; ?>(criteriadata){
    $.ajax({
        type: 'post',
        url: 'mdobject/dataview/1596179277179992/0/json',
        data: {uriParams: criteriadata},
        dataType: 'json',
        beforeSend: function() {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true
            });
        },
        success: function(dataHtml) {

            var $dialogName = 'dialog-drilldown-dataview-' + dataHtml.metaDataId;
            if (!$("#" + $dialogName).length) {
                $('<div id="' + $dialogName + '"></div>').appendTo('body');
            }
            var $dialog = $('#' + $dialogName);
            var heightDialog = 'auto';
            var widthDialog = 1200;
            
            $dialog.empty().append('<div class="col-md-12" id="object-value-list-'+dataHtml.metaDataId+'">' + dataHtml.Html + '</div>');
            $dialog.dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: dataHtml.Title,
                height: heightDialog,
                width: widthDialog,
                maxHeight: heightDialog,
                modal: true,
                position: { my: 'top', at: 'top+50' },
                /*open: function() {
                    setTimeout(function() {
                        $dialog.dialog("option", "position", {my: "center", at: "center", of: window});
                    }, 10);
                },*/ 
                close: function() {
                    $dialog.empty().dialog('destroy').remove();
                },
                buttons: [{
                    text: dataHtml.close_btn,
                    class: 'btn blue-madison btn-sm',
                    click: function() {
                        $dialog.dialog('close');
                    }
                }]
            }).dialogExtend({
                "closable": true,
                "maximizable": true,
                "minimizable": true,
                "collapsable": true,
                "dblclick": "maximize",
                "minimizeLocation": "left",
                "icons": {
                    "close": "ui-icon-circle-close",
                    "maximize": "ui-icon-extlink",
                    "minimize": "ui-icon-minus",
                    "collapse": "ui-icon-triangle-1-s",
                    "restore": "ui-icon-newwin"
                },
                maximize: function() {
                    $('#objectdatagrid-' + dataHtml.metaDataId).datagrid('resize');
                },
                restore: function() {
                    $('#objectdatagrid-' + dataHtml.metaDataId).datagrid('resize');
                }
            });

            Core.initAjax($dialog);
            $dialog.dialog('open');

            $dialog.find('.div-objectdatagrid-'+dataHtml.metaDataId).addClass('pl0 pr0');
            $dialog.find('#object-value-list-'+dataHtml.metaDataId).addClass('pl0 pr0');

            Core.unblockUI();
        },
        error: function() { alert('Error'); }
    });    
}
</script>