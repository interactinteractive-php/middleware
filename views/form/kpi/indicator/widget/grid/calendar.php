<?php 
    $eventClickProcess = array();
    $eventSelectableProcessId = '0'; //checkDefaultVal($this->relationViewConfig['eventselectableprocessid'], '0');
?>
<div class="center-sidebar overflow-hidden content ">
    <div class="row">
        <?php
        if (!isset($this->isIgnoreFilter)) {
        ?>
        <div class="col-md-auto pl-0">
            <div class="kpidv-data-filter-col pr-1"></div>
        </div>
        <?php
        }
        ?>
        <div class="col right-sidebar-content-for-resize content-wrapper pl-0 pr-0 overflow-hidden mv-calendar-<?php echo $this->indicatorId; ?>" style="min-height: 250px;">
            <div class="row">
                <div class="col-md-12">
                    
                    <?php
                    if (!isset($this->isBasket) && Input::numeric('isIgnoreTitle') != '1') {
                    ?>
                    <div class="text-uppercase font-weight-bold mt-0 mb-2">
                        <?php echo $this->title; ?>
                    </div>
                    <?php
                    }
                    ?>
                    
                    <div class="table-toolbar d-none">
                        <div class="d-flex">
                            <div class="col p-0">
                                <div class="dv-process-buttons">
                                    <div class="btn-group btn-group-devided">
                                        <?php
                                        if ($this->isDataMart) {
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "generateKpiDataMart(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'generateDataMart', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Датамарт бэлдэх', true
                                            ); 
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "generateDataMartSqlView(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'generateDataMartSqlView', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> SQL харах', true
                                            );

                                        } elseif ($this->isRawDataMart) {
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "generateKpiRawDataMart(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'generateRawDataMart', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Датамарт бэлдэх', true
                                            ); 
                                            
                                        } elseif ($this->isCheckQuery) {
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "mvExecuteCheckQuery(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'executeCheckQuery', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Run check query', true
                                            ); 
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "mvExecuteFixQuery(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'executeFixQuery', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Run fix query', true
                                            );

                                        } else {
                                            
                                            $contextMenu = array();
                                            $createFcProcessEvent = $createFcProcessEventBtnName = "";
                                            
                                            foreach ($this->process as $process) {
                                                
                                                $srcIndicatorId = $process['structure_indicator_id'];
                                                $crudIndicatorId = issetParam($process['crud_indicator_id']);
                                                $isFillRelation = issetParam($process['is_fill_relation']);
                                                $isNormalRelation = issetParam($process['is_normal_relation']);
                                                $typeCode = $process['type_code'];
                                                $kpiTypeId = $process['kpi_type_id'];
                                                $buttonName = $className = $onClick = $description = $opt = '';
                                                if ($srcIndicatorId == $this->indicatorId) {
                                                    
                                                    if ($typeCode == 'create') {
                                                        
                                                        $labelName = $process['label_name'] == 'Нэмэх' ? $this->lang->line('add_btn') : $this->lang->line($process['label_name']);
                                                        $className = 'btn btn-success btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-plus"></i> '.$labelName;
                                                        $createFcProcessEventBtnName = $labelName;
                                                        if ($isNormalRelation) {
                                                            $createFcProcessEvent = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId});";
                                                        } else {
                                                            $createFcProcessEvent = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', false, undefined, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                        }
                                                        
                                                    } elseif ($typeCode == 'update') {
                                                        
                                                        $labelName = $process['label_name'] == 'Засах' ? $this->lang->line('edit_btn') : $this->lang->line($process['label_name']);
                                                        $isUpdate = true;
                                                        
                                                        if ($isFillRelation) {
                                                            $opt = ", {fillSelectedRow: true, mode: 'update'}, undefined, 'refetchEvents_". $this->indicatorId ."'";
                                                        } else {
                                                            $opt = ", undefined, undefined, 'refetchEvents_". $this->indicatorId ."'";
                                                        }
                                                        
                                                        $className = 'btn btn-warning btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-edit"></i> '.$labelName;
                                                        
                                                        if ($isNormalRelation) {
                                                            $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update'});";
                                                        } else {
                                                            $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true$opt);";
                                                        }
                                                        
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $labelName,
                                                            'onClick' => $onClick,
                                                            'actionName' => 'edit',
                                                            'iconName' => 'edit', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );
                                                        
                                                    } elseif ($typeCode == 'read') {
                                                        
                                                        $isUpdate = true;
                                                        $className = 'btn purple btn-circle btn-sm';
                                                        $buttonName = '<i class="fas fa-eye"></i> '.$this->lang->line('view_btn');
                                                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true, {mode: 'view'}, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $this->lang->line('view_btn'),
                                                            'onClick' => $onClick,
                                                            'actionName' => 'view',
                                                            'iconName' => 'eye', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );
                                                        
                                                    } elseif ($typeCode == 'delete') {
                                                        
                                                        $isDelete = true;
                                                        $className = 'btn btn-danger btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-trash"></i> '.$this->lang->line('delete_btn');
                                                        $onClick = "removeKpiIndicatorValue(this, '".$this->indicatorId."', 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $this->lang->line('delete_btn'),
                                                            'onClick' => $onClick,
                                                            'actionName' => 'delete',
                                                            'iconName' => 'trash', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );
                                                        
                                                    } elseif ($typeCode == 'config') {
                                                        
                                                        $isDelete = true;
                                                        $className = 'btn blue-steel btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-tools"></i> '.$this->lang->line('Config');
                                                        $onClick = "mapKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', 'config');";
                                                        
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $this->lang->line('Config'),
                                                            'onClick' => $onClick,
                                                            'actionName' => 'config',
                                                            'iconName' => 'tools', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );
                                                        
                                                    } elseif ($typeCode == '360') {
                                                        
                                                        $isDelete = true;
                                                        $className = 'btn blue-steel btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-tools"></i> '.$this->lang->line('360');
                                                        $onClick = "mapKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', '360');";
                                                        
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $this->lang->line('360'),
                                                            'onClick' => $onClick,
                                                            'actionName' => '360',
                                                            'iconName' => 'tools', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );
                                                        
                                                    } elseif ($typeCode == 'excel') {
                                                        
                                                        $className = 'btn green btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-file-excel"></i> '.$this->lang->line('pf_excel_import');
                                                        $onClick = "excelImportKpiIndicatorValue(this, '".$this->indicatorId."');";
                                                        
                                                    } elseif ($typeCode == 'excel_export_one_line') {
                                                        
                                                        $className = 'btn green btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-file-excel"></i> Эксель нэг мөрөөр татах';
                                                        $onClick = "exportExcelOneLineKpiIndicatorValue(this, '".$this->indicatorId."');";
                                                        
                                                    } elseif ($typeCode == 'export') {
                                                        
                                                        $isDelete = true;
                                                        $className = 'btn green btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-download"></i> '.$this->lang->line('excel_export_btn');
                                                        $onClick = "exportKpiIndicatorValue(this, '".$this->indicatorId."');";
                                                        
                                                    } elseif ($kpiTypeId == '1191') {
                                                        
                                                        $className = 'btn blue-steel btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-play"></i> ' . $this->lang->line($process['label_name'] ? $process['label_name'] : $process['name']);
                                                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$crudIndicatorId', false, {transferSelectedRow: true}, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                    } elseif ($kpiTypeId == '1080') {
                                                        
                                                        $className = 'btn blue-steel btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-play"></i> ' . $this->lang->line($process['label_name'] ? $process['label_name'] : $process['name']);
                                                        $onClick = "callWebServiceKpiIndicatorValue(this, '$crudIndicatorId');";
                                                    } 
                                                    
                                                } else {
                                                    
                                                    $description = $this->lang->line(issetParam($process['description']));
                                                    $processName = $this->lang->line(issetParam($process['label_name']));
                                                    $isDfillRelation = issetParam($process['is_dfill_relation']);
                                                    
                                                    if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                        $eventClickProcess['kpiTypeId'] =  $kpiTypeId;
                                                        $eventClickProcess['srcIndicatorId'] =  $srcIndicatorId;
                                                    }

                                                    if ($typeCode == 'create') {
                                                        
                                                        $className = 'btn btn-success btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-plus"></i> '.$processName;
                                                        
                                                        if ($isFillRelation) {
                                                            $opt = ', {fillSelectedRow: true, mode: \'create\'}';
                                                        } elseif ($isDfillRelation) {
                                                            $opt = ', {fillDynamicSelectedRow: true, mode: \'create\'}';
                                                        }
                                                        
                                                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', false$opt, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                    } elseif ($typeCode == 'update') {
                                                        
                                                        $className = 'btn btn-warning btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-edit"></i> '.$processName;
                                                        
                                                        if ($isFillRelation) {
                                                            $opt = ', {fillSelectedRow: true, mode: \'update\'}';
                                                            
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  '{fillSelectedRow: true, mode: \'update\'}';
                                                            }
                                                        } elseif ($isDfillRelation) {
                                                            $opt = ', {fillDynamicSelectedRow: true, mode: \'update\'}';
                                                            
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  '{fillDynamicSelectedRow: true, mode: \'update\'}';
                                                            }
                                                        } elseif ($opt === '') {
                                                            $opt = ',undefined';
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  'undefined';
                                                            }
                                                        }
                                                        
                                                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true$opt, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                       
                                                        $contextMenu[] = array(
                                                            'crudIndicatorId' => $crudIndicatorId, 
                                                            'labelName' => $processName,
                                                            'onClick' => $onClick,
                                                            'actionName' => 'edit',
                                                            'iconName' => 'edit', 
                                                            'data-actiontype' => $typeCode, 
                                                            'data-main-indicatorid' => $this->indicatorId, 
                                                            'data-structure-indicatorid' => $this->indicatorId, 
                                                            'data-crud-indicatorid' => $crudIndicatorId,
                                                            'data-mapid' => issetParam($process['map_id'])
                                                        );

                                                    } elseif ($typeCode == 'read') {
                                                        
                                                        $className = 'btn purple btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-eye"></i> '.$processName;
                                                        
                                                        if ($isFillRelation) {
                                                            $opt = ', {fillSelectedRow: true, mode: \'view\'}';
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  '{fillSelectedRow: true, mode: \'view\'}';
                                                            }
                                                        } elseif ($isDfillRelation) {
                                                            $opt = ', {fillDynamicSelectedRow: true, mode: \'view\'}';
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  '{fillDynamicSelectedRow: true, mode: \'view\'}';
                                                            }
                                                        } else {
                                                            $opt = ', {mode: \'view\'}';
                                                            if (issetParam($this->relationViewConfig['eventclick']) === $srcIndicatorId) {
                                                                $eventClickProcess['mode'] =  '{mode: \'view\'}';
                                                            }
                                                        }
                                                        
                                                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true$opt, undefined, 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                        
                                                    } elseif ($typeCode == 'delete') {
                                                        
                                                        $className = 'btn btn-danger btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-trash"></i> '.$processName;
                                                        $onClick = "removeKpiIndicatorValue(this, '$srcIndicatorId', 'refetchEvents_". $this->indicatorId ."');";
                                                        
                                                    } elseif ($typeCode == 'excel') {
                                                        
                                                        $className = 'btn green btn-circle btn-sm';
                                                        $buttonName = '<i class="far fa-file-excel"></i> '.$processName;
                                                        $onClick = "excelImportKpiIndicatorValue(this, '$srcIndicatorId');";
                                                    }
                                                }
                                                
                                                echo html_tag('a', 
                                                    array( 
                                                        'href' => 'javascript:;', 
                                                        'class' => $className, 
                                                        'data-qtip-title' => $description, 
                                                        'data-qtip-pos' => 'top', 
                                                        'onclick' => $onClick, 
                                                        'data-actiontype' => $typeCode, 
                                                        'data-main-indicatorid' => $this->indicatorId, 
                                                        'data-structure-indicatorid' => $this->indicatorId, 
                                                        'data-crud-indicatorid' => $crudIndicatorId,
                                                        'data-mapid' => issetParam($process['map_id'])
                                                    ), 
                                                    $buttonName, true
                                                );
                                            }
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn green btn-circle btn-sm', 
                                                    'onclick' => "callWebServiceKpiIndicatorValue(this, '".$this->indicatorId."');", 
                                                    'data-actiontype' => 'callwebservice', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                'Call service', $this->isCallWebService
                                            );
                                            
                                            if ($this->isPrint) {
                                                
                                                echo html_tag('a', 
                                                    array(
                                                        'class' => 'btn green btn-circle btn-sm', 
                                                        'onclick' => "reportTemplateKpiIndicatorValue(this, '".$this->indicatorId."');", 
                                                        'data-actiontype' => 'reporttemplate', 
                                                        'href' => 'javascript:;'
                                                    ), 
                                                    '<i class="far fa-print"></i> Хэвлэх'
                                                );
                                            }
                                            
                                            if ($this->isUseWorkflow) {
                                                
                                                echo '<div class="btn-group workflow-btn-group-'.$this->indicatorId.'">
                                                    <button type="button" class="btn btn-sm blue btn-circle dropdown-toggle workflow-btn-'.$this->indicatorId.'" data-toggle="dropdown"><i class="far fa-cogs"></i> '.$this->lang->line('change_workflow').'</button>
                                                    <ul class="dropdown-menu workflow-dropdown-'.$this->indicatorId.'" role="menu"></ul>
                                                </div>';
                                            }
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => "renderIframeIndicator(this);", 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> iFrame', false
                                            ); 
                                        }
                                        
                                        if (isset($this->isImportManage) && $this->isImportManage) {
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-info btn-circle btn-sm', 
                                                    'onclick' => 'mvImportManageFieldsConfig(this, \''.$this->indicatorId.'\', \''.$this->mainIndicatorId.'\');', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-cogs"></i> Талбарын тохиргоо', true
                                            ); 
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-warning btn-circle btn-sm', 
                                                    'onclick' => 'mvImportManageDataCheck(this, \''.$this->indicatorId.'\', \''.$this->mainIndicatorId.'\');', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-check"></i> Шалгах', true
                                            ); 
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => 'mvImportManageDataUpdate(this, \''.$this->indicatorId.'\', \''.$this->mainIndicatorId.'\');', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Update', true
                                            ); 
                                            
                                            echo html_tag('a', 
                                                array(
                                                    'class' => 'btn btn-success btn-circle btn-sm', 
                                                    'onclick' => 'mvImportManageDataCommit(this, \''.$this->indicatorId.'\', \''.$this->mainIndicatorId.'\');', 
                                                    'href' => 'javascript:;'
                                                ), 
                                                '<i class="far fa-database"></i> Commit', true
                                            ); 
                                        ?>
                                        <div class="mv-imp-manage-info d-inline-block ml-4"></div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php    
                            if (Input::numeric('isIgnoreRightTools') != 1) {
                            ?>
                            <div class="dv-right-tools-btn ml-2 text-right">
                                <div class="btn-group btn-group-devided">
                                    <?php if ($this->relationComponentsOther) { ?>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="icon-stack2"></i></button>
                                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(-75px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <?php echo Html::anchor(
                                                        'javascript:;', '<i class="far fa-list"></i> Жагсаалт', array(
                                                        'class' => 'dropdown-item',
                                                        'title' => 'Calendar',     
                                                        'onclick' => 'kpiIndicatorViewList_'.$this->indicatorId.'(this, \''.$this->indicatorId.'\');'
                                                    ), true  
                                                );  ?>
                                                <?php echo Html::anchor(
                                                        'javascript:;', '<i class="far fa-calendar"></i> Calendar', array(
                                                        'class' => 'dropdown-item',
                                                        'title' => 'Calendar',     
                                                        'onclick' => 'kpiIndicatorViewCalendar_'.$this->indicatorId.'(this, \''.$this->indicatorId.'\');'
                                                    ), true  
                                                );  ?>
                                            </div>
                                        </div>
                                    <?php }
                                    echo Html::anchor(
                                            'javascript:;', '<i class="far fa-file-excel"></i>', array(
                                            'class' => 'btn btn-secondary btn-circle btn-sm default',
                                            'title' => 'Excel гаргах',     
                                            'onclick' => 'excelExportKpiIndicatorValue(this, \''.$this->indicatorId.'\');'
                                        ), true  
                                    ); 
                                    
                                    echo Html::anchor(
                                            'javascript:;', '<i class="far fa-cube"></i>', array(
                                            'class' => 'btn btn-secondary btn-circle btn-sm default',
                                            'title' => 'Pivot view',     
                                            'onclick' => 'pivotKpiIndicatorValue(this, \''.$this->indicatorId.'\');'
                                        ), (defined('CONFIG_PIVOT_SERVICE_ADDRESS') && CONFIG_PIVOT_SERVICE_ADDRESS)  
                                    ); 
                                    
                                    echo Html::anchor(
                                            'javascript:;', '<i class="far fa-map-marker"></i>', array(
                                            'class' => 'btn btn-secondary btn-circle btn-sm default',
                                            'title' => 'Google map',
                                            'onclick' => 'googleMapKpiIndicatorValue(this, \''.$this->indicatorId.'\', \''.$this->coordinateField.'\');'
                                        ), $this->coordinateField ? true : false
                                    );
                                    ?>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col col-cus">
                    <div class="col jeasyuiTheme3 fc-calendar-<?php echo $this->indicatorId; ?>" id="fc-calendar-<?php echo $this->indicatorId; ?>"></div>
                    <div class="col-md-4 calendar-bp-layout" style="display: none" ></div>
                </div>    
            </div>    
        </div>     
    </div>    
</div>

<style type="text/css">

.kpidv-data-filter-col {
    width: 240px;
    border-right: 1px solid #ddd;
    overflow-x: hidden;
    overflow-y: auto;
}
.kpidv-data-filter-col .list-group {
    border: none;
    padding: 0;
}
.kpidv-data-filter-col .list-group-item {
    padding: 0.28rem 0;
}
.kpidv-data-filter-col .list-group-item.active {
    color: rgba(51,51,51,.85);
    background-color: rgba(93, 173, 226, 0.3);
    border-color: rgba(93, 173, 226, 0.3);
}
.mv-calendar-<?php echo $this->indicatorId; ?> {
    .fc {
        .fc-toolbar.fc-header-toolbar {
            margin: 0 0 1.5em 0;
        }
    }

    .col-cus {
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        background: #FFF;
        border-top-left-radius: 20px !important;
        border-bottom-left-radius: 20px !important;
        border-top-right-radius: 20px !important;
        border-bottom-right-radius: 20px !important;
        padding-right: 0;
    }

    .fc-toolbar h2 {
        line-height: 2em;
        font-size: 1.5em;
    }

    .fc table,
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #F3F4F6 !important;
    }

    .fa {
        font-family: "Font Awesome 5 Pro" !important;
        font-size: 0.89rem !important;
    }

    .sidebar-calendar {
        background: #F3F4F6 !important;
    }

    .dv-calendar-layout {
        background: #FFF !important;
        margin: 30px 0;
    }

    .calendar-bp-layout {
        margin-left: 0;
        background: #F9FDFF !important;
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
        padding: 30px;
    }

    .fc-button:hover {
        color: #FFF !important;
        background-color: #009EF7 !important;
        border: 2px solid #009EF7 !important;
    }

    .fc-button {
        background-color: #FFF !important;
        border: 2px solid #F5F6F7 !important;
        box-shadow: 0px 2px 5px 0px #26334D08 !important;
        border-radius: 100px;
        color: #6B7A99 !important;
    }

    .fc-today-button {
        background-color: #FFF !important;
        border: 2px solid #F5F6F7 !important;
        box-shadow: 0px 2px 5px 0px #26334D08 !important;
        border-radius: 100px;
        color: #6B7A99 !important;
    }

    .fc-button-group .fc-button:first-child {
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
    }

    .fc-button-group .fc-button:last-child {
        border-top-right-radius: 30px;
        border-bottom-right-radius: 30px;
    }

    .fc-button {
        font-size: 14px;
        line-height: 1.33;
        vertical-align: middle;
    }

    .fc-col-header-cell-cushion {
        color: #6B7A99;
        font-size: 16px;
        vertical-align: middle;
        font-weight: 700;
    }

    .fc th {
        height: 72px;
        vertical-align: middle;
        color: #6B7A99;
        font-weight: 700;
    }

    .fc .fc-daygrid-day-frame {
        min-height: 150px;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background-color: #009ef72e;
    }

    .fc-daygrid-day-number {
        color: #6B7A99 !important;
    }

    .fc-view-container .fc-view>table .fc-head tr:first-child>td, .fc-view-container .fc-view>table .fc-head tr:first-child>th {
        background: #FFF !important;
        padding-top: 26px;
        padding-bottom: 26px;
        font-weight: 700;
    }

    .fc-view {
        border: 1px solid #FFF;
    }

    .fc-head-container {
        padding: 0 !important;
    }

    .table-bordered td, .table-bordered th {
        border: 1px solid #F5F6F7;
    }
    
    .fc-day-grid-event {
        border: 1px solid #F00;
    }
    
    .fc-image1 {
        margin-right: 5px; 
        width: 18px; 
        height: 18px;
    }

    .fc .fc-timegrid-axis-cushion, .fc .fc-timegrid-slot-label-cushion {
        font-size: 16px;
        padding: 25px;
        color: #6B7A99;
        font-weight: 700;
    }

    .fc-daygrid-event-harness {
        display: none;
        .fc-daygrid-dot-event:hover {
            background: #00BCD4 !important;
            
        }
        .fc-daygrid-dot-event:hover,
        .fc-event-main {
            cursor: pointer;
        }
    }

    .fc-daygrid-day-frame {
        height: 100%;
    }

    .fc-highlight {
        background : #673ab7 !important;
    }

    .fc-timegrid-event-harness {
        .fc-event {
            background: #FFF !important;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            box-shadow: 0px 2px 4px 0px #00000040;
        }
        .fc-circle-event,
        .fc-event-main {
            color: #333;
        }
    }

    .fc .fc-daygrid-day-top {
        display: flex !important;
        flex-direction: row;
        width: 100%;
        .fc-daygrid-day-number {
            margin-left: auto;
        }
    }

    .fc-event-pos-2,
    .fc-event-pos-1
        {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
        overflow: hidden;
    }
    
    <?php if (issetParam($this->relationViewConfig['eventviewcounter']) !== '') { ?>
        .fc-daygrid-event-harness {
            position: unset !important; 
            width: 32px;
            height: 32px;
            float: left;
            margin-left: 5px;
            .fc-event {
                float: left;
                background: transparent;
                padding: 0;
                width: 100%;
                height: 100%;
                border-radius: 45px;
                border-color: transparent;
                
                .fc-circle-event {
                    border-radius: 45px;
                    width: 100%;
                    height: 100%;
                    text-align: center;
                    vertical-align: middle;
                    padding: 6px 0;
                }
            }
        }
    <?php } else { ?>
        .fc-daygrid-event-harness { 
            margin-bottom: 0.25rem;
        }
    <?php  } ?>
}

.fc-event-position-4,
.fc-event-position-5 {
    font-size: 10px;
    margin: 5px 0;
}

.qtip-fccalendar {
    border-radius: 10px;
    padding: 13px 16px;
    .tooltip-1 {
        font-size: 16px;
        font-weight: 700;
        line-height: 14px;
        letter-spacing: 0px;
        text-align: left;
        margin-bottom: 25px;
    }

    .tooltip-2,
    .tooltip-3,
    .tooltip-4,
    .tooltip-5 {
        font-size: 12px;
        font-weight: 400;
        line-height: 14px;
        letter-spacing: 0px;
        text-align: left;
    }
}

</style>
<?php 
$isTooltipField = false;
if (issetParam($this->relationViewConfig['tooltip-1']) || issetParam($this->relationViewConfig['tooltip-2']) || issetParam($this->relationViewConfig['tooltip-3'])) {
    $isTooltipField = true;
}

?>
<script type="text/javascript">
<?php

    $menuCallBack = $menuItems = '';
    if (!isset($this->isBasket) && !$this->isDataMart && isset($contextMenu) && $contextMenu) {
        
        
        
        foreach ($contextMenu as $menu) {

            $menu['onClick'] = str_replace('this', '$a', $menu['onClick']);
            /* $menu['onClick'] = str_replace('this', 'opt.$trigger', $menu['onClick']); */
            
            $menuCallBack .= 'if (key === \''.$menu['crudIndicatorId'].'_'.$menu['data-actiontype'].'\') { ';
                
                $menuCallBack .= 'var $a = $(\'<a />\'); ';
                $menuCallBack .= '$a.attr(\'data-actiontype\', \''.$menu['data-actiontype'].'\')';
                $menuCallBack .= '.attr(\'data-main-indicatorid\', \''.$menu['data-main-indicatorid'].'\')';
                $menuCallBack .= '.attr(\'data-structure-indicatorid\', \''.$menu['data-structure-indicatorid'].'\')';
                $menuCallBack .= '.attr(\'data-crud-indicatorid\', \''.$menu['data-crud-indicatorid'].'\')';
                $menuCallBack .= '.attr(\'data-mapid\', \''.$menu['data-mapid'].'\') ';
                $menuCallBack .= '.attr(\'data-rowdata\', $(opt.$trigger).attr(\'data-rowdata\')) ';
                $menuCallBack .= '.addClass(\'no-dataview\'); ';
                
                $menuCallBack .= $menu['onClick'];
            $menuCallBack .= '} ';
            
            $menuItems .= '"'.$menu['crudIndicatorId'].'_'.$menu['data-actiontype'].'": {name: \''.$menu['labelName'].'\', icon: \''.$menu['iconName'].'\'}, ';
        }
    }
    if ($menuCallBack) { ?>
    $.contextMenu({
        selector: '.fc-calendar-<?php echo $this->indicatorId; ?> .fc-day .fc-daygrid-event-harness > .fc-event, .fc-calendar-<?php echo $this->indicatorId; ?> .fc-timegrid-col .fc-timegrid-event-harness > .fc-timegrid-event',
        callback: function (key, opt) {
            <?php echo $menuCallBack; ?>
        },
        items: {
            <?php echo $menuItems; ?> 
        }
    });
<?php }  ?>
var dynamicHeight = 0;
var calendar<?php echo $this->indicatorId; ?>;
var filterStart<?php echo $this->indicatorId; ?>, filterEnd<?php echo $this->indicatorId; ?>;
var mainSelector_<?php echo $this->indicatorId; ?> = $('#object-value-list-<?php echo $this->indicatorId; ?>');
var mvCalendar_<?php echo $this->indicatorId; ?> = $('.mv-calendar-<?php echo $this->indicatorId; ?>');
var idField_<?php echo $this->indicatorId; ?> = '<?php echo $this->idField; ?>';

var isIgnoreWfmHistory_<?php echo $this->indicatorId; ?> = false;
var isGoogleMapView_<?php echo $this->indicatorId; ?> = false;
var isFilterShowData_<?php echo $this->indicatorId; ?> = <?php echo ($this->isFilterShowData == '1' ? 'true' : 'false'); ?>;
var indicatorName_<?php echo $this->indicatorId; ?> = '<?php echo $this->title; ?>';
var drillDownCriteria_<?php echo $this->indicatorId; ?> = '<?php echo $this->drillDownCriteria; ?>';

if (typeof isKpiIndicatorScript === 'undefined') {
    $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/indicator.js'); ?>');
}

$(function() {
    if (typeof isFullCalendarPlugin == 'undefined') {
        $.cachedScript('assets/core/js/plugins/ui/fullcalendar/version/6.1.10/fullcalendar.js').done(function() {
            initFullCalendar_<?php echo $this->indicatorId; ?>();
        });
    } else {
        initFullCalendar_<?php echo $this->indicatorId; ?>();
    }
    
    dynamicHeight = $(window).height() - 40;

    if (dynamicHeight < 230) {
        dynamicHeight = 350;
    }

    <?php if (Input::numeric('isDrilldown') == '1') { ?>
    dynamicHeight = dynamicHeight - 50;
    <?php } elseif ($dynamicHeight = Input::numeric('dynamicHeight')) { ?>
    dynamicHeight = <?php echo $dynamicHeight; ?>;
    <?php } ?>
});

<?php
if (!isset($this->isIgnoreFilter)) {
?>
filterKpiIndicatorValueForm(<?php echo $this->indicatorId; ?>);
<?php
}
?>

function filterKpiIndicatorValueForm(indicatorId) {
    var drillDownCriteria = window['drillDownCriteria_' + indicatorId];
    
    $.ajax({
        type: 'post',
        url: 'mdform/filterKpiIndicatorValueForm',
        data: {indicatorId: indicatorId, drillDownCriteria: drillDownCriteria},
        dataType: 'json',
        success: function(data) {
            
            var $filterCol = $('#object-value-list-' + indicatorId + ' .kpidv-data-filter-col');
            
            if (data.status == 'success' && data.html != '') {
                
                $filterCol.css('height', dynamicHeight + 47);
                
                $filterCol.append(data.html).promise().done(function() {
                    Core.initNumberInput($filterCol);
                    Core.initLongInput($filterCol);
                    Core.initDateInput($filterCol);
                    Core.initSelect2($filterCol);
                });
                
            } else {
                $filterCol.closest('.col-md-auto').remove();
                calendar<?php echo $this->indicatorId; ?>.updateSize();
                console.log(data);
            }
        }
    });
}

function initFullCalendar_<?php echo $this->indicatorId; ?>() {
    var calendarEl<?php echo $this->indicatorId; ?> = document.getElementById('fc-calendar-<?php echo $this->indicatorId; ?>');
    calendar<?php echo $this->indicatorId; ?> = new FullCalendar.Calendar(calendarEl<?php echo $this->indicatorId; ?>, {
        initialView: 'dayGridMonth',
        locale: 'mn',
        headerToolbar: {
            left: '<?php echo issetParam($createFcProcessEvent) ? 'createFcProcess' : '' ?>', 
            center: 'prev title next refreshBtn',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,dataViewList'
        },
        height: 'auto', 
        timeZone: 'local', 
        contentHeight: 'auto', 
        navLinks: false, // can click day/week names to navigate views
        selectable: <?php echo issetParam($eventSelectableProcessId) ? 'true' : 'false' ?>,
        selectMirror: false,
        nowIndicator: false,
        allDaySlot: false,
        dayMaxEventRows: true,
        editable: <?php echo (issetParam($eventDropProcessCode) || issetParam($eventResizeProcessCode)) ? 'true' : 'false'; ?>,
        themeSystem: 'bootstrap4',
        customButtons: {
            <?php if (issetParam($createFcProcessEvent)) { ?>
                createFcProcess: {
                    text: '<?php echo $createFcProcessEventBtnName ?>',
                    click: function() {
                        <?php echo $createFcProcessEvent; ?>
                    }
                },
            <?php } ?>
            refreshBtn: {
                icon: ' fa fa-refresh ', 
                click: function() {
                    calendar<?php echo $this->indicatorId; ?>.refetchEvents();
                }
            },
            dataViewList: {
                text: 'Жагсаалтаар', 
                icon: ' fa fa-list ',
                click: function() {
                    kpiIndicatorViewList_<?php echo $this->indicatorId ?> (this, '<?php echo $this->indicatorId; ?>')
                }
            }
        },
        slotDuration: '<?php echo checkDefaultVal($slotDuration, '01:00:00'); ?>', 
        slotMinTime: '<?php echo checkDefaultVal($minTime, '00:00:01'); ?>', 
        slotMaxTime: '<?php echo checkDefaultVal($maxTime, '23:59:59'); ?>', 
        views: {
            listWeek: {
                eventContent: function (info) {
                    console.log(info);
                }
            },
            dayGridMonth: { 
                dayMaxEventRows: <?php echo checkDefaultVal($this->relationViewConfig['daymaxeventrows'], '6'); ?>,
                titleFormat: { 
                    year: 'numeric', 
                    month: '2-digit'
                }
            },
            timeGridWeek: {
                titleFormat: { 
                    month: 'numeric', 
                    day: '2-digit' ,
                    omitCommas: true
                }
            },
            timeGridDay: {
                titleFormat: { 
                    year: 'numeric', 
                    month: 'numeric', 
                    day: '2-digit' ,
                }
            }
        },
        slotLabelFormat: {
            hour: '2-digit',
            hour12: false
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            var start = fetchInfo['start'];
            var startStr = fetchInfo['startStr'];
            var end = fetchInfo['end'];
            var endStr = fetchInfo['endStr'];
            var timeZone = fetchInfo['timeZone'];

            var defaultCriteriaData = '';
            var $packageTab = $();
            var $filterStartDate = $('#object-value-list-<?php echo $this->indicatorId; ?> .kpidv-data-filter-col').find('[data-path="filterStartDate"]');
            var filterData = {};
            if ($filterStartDate.length == 0) {
                var filterStartDate = moment(start).format('YYYY-MM-DD');
                var filterEndDate = moment(end).format('YYYY-MM-DD');
            }

            filterStart<?php echo $this->indicatorId; ?> = moment(start).format('YYYY-MM-DD');
            filterEnd<?php echo $this->indicatorId; ?> = moment(end).format('YYYY-MM-DD');

            var postParams = {
                indicatorId: '<?php echo $this->indicatorId; ?>',  
                postHiddenParams: '<?php echo issetParam($this->postHiddenParams); ?>', 
                hiddenParams: '<?php echo issetParam($this->hiddenParams); ?>', 
                filter: '<?php echo issetParam($this->filter); ?>'
            };

            $.ajax({
                type: 'post',
                url: 'mdform/indicatorDataGrid',
                data: postParams,
                success: function(responseData) {
                    var response = JSON.parse(responseData);
                    if (response.status == 'success') {
                        successCallback(response.rows.map(function(event) {
                            
                            return {
                                id: event['ID'], 
                                title: event['<?php echo issetParam($this->relationViewConfig['position-3']) ?>'], 
                                start: event['<?php echo issetParam($this->relationViewConfig['position-1']) ?>'], 
                                end: event['<?php echo issetParam($this->relationViewConfig['position-2']) ?>'], 
                                rowdata: event
                            };
                        }));
                        
                    } else {
                        PNotify.removeAll();
                        new PNotify({
                            title: response.status,
                            text: response.message,
                            type: response.status,
                            sticker: false
                        });
                    }

                },
                error: function(response) {
                    console.log(response);
                }
            }); 
        },
        eventResize: function(event, delta, revertFunc) {}, 
        eventDrop: function(event, delta, revertFunc) {},
        eventContent: function(arg, argFunc) {
            var event = arg.event;
            var element = $(arg.el);
            var eventHtml = '', eventStyle = '';
            var rData = arg.event.extendedProps.rowdata;  
            
            if (event.id) {
                <?php if (issetParam($this->relationViewConfig['eventviewcounter']) === '') { ?>
                    eventStyle += 'style="';
                    eventStyle += '"';
                    eventHtml += "<div id='"+ event.id +"' class='fc-circle-event' "+ eventStyle +">"; 
                        eventHtml += '<span class="fc-title">'; 
                            eventHtml += html_entity_decode(event.title, 'ENT_QUOTES');
                        eventHtml += '</span">'; 
                        <?php if (issetParam($this->relationViewConfig['position-4'])) { ?>
                            if (rData.hasOwnProperty('<?php echo $this->relationViewConfig['position-4']; ?>') && rData.<?php echo $this->relationViewConfig['position-4']; ?>) {
                                eventHtml += '<div class="fc-event-position-4 text-white">'+rData.<?php echo $this->relationViewConfig['position-4']; ?>+'</div>';
                            }
                        <?php } ?>
                        <?php if (issetParam($this->relationViewConfig['position-5'])) { ?>
                            if (rData.hasOwnProperty('<?php echo $this->relationViewConfig['position-5']; ?>') && rData.<?php echo $this->relationViewConfig['position-5']; ?>) {
                                eventHtml += '<div class="fc-event-position-5 text-white">'+rData.<?php echo $this->relationViewConfig['position-5']; ?>+'</div>';
                            }
                        <?php } ?>
                    eventHtml += '</div>';
                <?php } else { ?>
                    if (rData.hasOwnProperty('<?php echo $this->relationViewConfig['eventviewcounter']; ?>') && rData.<?php echo $this->relationViewConfig['eventviewcounter']; ?>) {
                        eventHtml += '<div class="fc-circle-event text-white">'+rData.<?php echo $this->relationViewConfig['eventviewcounter']; ?>+'</div>';
                    } else {
                        eventHtml += '<div class="fc-circle-event text-white">0</div>';
                    }
                <?php } ?>
            }
            return { html: eventHtml };
        }, 
        eventDidMount: function(arg) {
        
            var event = arg.event;
            var startDate = moment(event.start).format('YYYY-MM-DD');
            var element = $(arg.el);
            var eventHtml = '';
            var rData = arg.event.extendedProps.rowdata;  
            if (!event.id) return false;
            
            element.attr({'data-rid': event.id, 'data-rowdata': JSON.stringify(rData)});
            element.closest('.fc-daygrid-event-harness').show();
            var prependHtml = '';

            <?php if (issetParam($this->relationViewConfig['topleftposition-1'])) { ?>
                if (rData.<?php echo $this->relationViewConfig['topleftposition-1']; ?> !== '' && rData.<?php echo $this->relationViewConfig['topleftposition-1']; ?> !== null) {
                    prependHtml += '<span class="badge badge-primary" data-fca="1" data-one="1" title="<?php echo $this->lang->line(issetParam($this->relationViewConfig['topleftposition-1-label'])); ?>">'+rData.<?php echo $this->relationViewConfig['topleftposition-1']; ?>+'</span>';
                }
            <?php } ?>
            <?php if (issetParam($this->relationViewConfig['topleftposition-2'])) { ?>
                if (rData.<?php echo $this->relationViewConfig['topleftposition-2']; ?> !== '' && rData.<?php echo $this->relationViewConfig['topleftposition-2']; ?> !== null) {
                    prependHtml += '<span class="badge badge-secondary" data-fca="1" data-one="1" title="<?php echo $this->lang->line(issetParam($this->relationViewConfig['topleftposition-2-label'])); ?>">'+rData.<?php echo $this->relationViewConfig['topleftposition-2']; ?>+'</span>';
                }
            <?php } ?>
            
            if (arg.view.type == 'dayGridMonth') {
                <?php if (issetParam($this->relationViewConfig['eventcolor'])) { ?>
                    if ('<?php echo issetParam($this->relationViewConfig['eventcolor']); ?>' !== '' && rData.hasOwnProperty('<?php echo issetParam($this->relationViewConfig['eventcolor']); ?>') && rData.<?php echo issetParam($this->relationViewConfig['eventcolor']); ?>) {
                        var loopColorCircle = rData.<?php echo issetParam($this->relationViewConfig['eventcolor']); ?>;
                        element.closest('.fc-event').css('background-color', loopColorCircle); 
                        element.closest('.fc-event').css('border-color', loopColorCircle); 
                    } else {
                    }
                <?php } else { ?>
                    <?php if (issetParam($this->relationViewConfig['eventviewcounter']) !== '') { ?>
                        element.closest('.fc-event').css('background-color', '#00BCD4'); 
                        element.closest('.fc-event').css('border-color', '#00BCD4'); 
                    <?php } ?>
                <?php } ?>
                

                var $dayCell = element.closest('tr[role="row"]').find('td[data-date="'+startDate+'"] .fc-daygrid-day-top');
                
                $dayCell.find('[data-one="1"]').remove();
                $dayCell.prepend(prependHtml);
                    
            } else if (arg.view.type == 'basicWeek') {
                
                var d = new Date(startDate);
                var n = d.getDay();
                var i = (n == 0) ? 6 : n - 1;
                var $dayCell = element.closest('.fc-content-skeleton').find('> table > tbody > tr:eq(0) > td:eq('+i+')');
                
                $dayCell.find('[data-one="1"]').remove();
                $dayCell.prepend(prependHtml);
                
            } else if (arg.view.type == 'basicDay') {
                
                var $dayCell = element.closest('.fc-content-skeleton').find('> table > tbody > tr:eq(0) > td');
                
                $dayCell.find('[data-one="1"]').remove();
                $dayCell.prepend(prependHtml);
            }

            <?php if ($isTooltipField) { ?>
            
            element.qtip({
                content: {
                    text: function(event, api) {
                        var rowData = rData;
                        var tooltipLabel2 = '',
                            tooltipLabel3 = '',
                            tooltipLabel4 = '',
                            tooltipLabel5 = '';
                        <?php if (issetParam($this->relationViewConfig['tooltiplabel-2'])) { ?>
                            tooltipLabel2 = '<?php echo $this->relationViewConfig['tooltiplabel-2'] ?>'; 
                        <?php  } ?>
                        <?php if (issetParam($this->relationViewConfig['tooltiplabel-3'])) { ?>
                            tooltipLabel3 = '<?php echo $this->relationViewConfig['tooltiplabel-3'] ?>'; 
                        <?php  } ?>
                        <?php if (issetParam($this->relationViewConfig['tooltiplabel-4'])) { ?>
                            tooltipLabel4 = '<?php echo $this->relationViewConfig['tooltiplabel-4'] ?>'; 
                        <?php  } ?>
                        <?php if (issetParam($this->relationViewConfig['tooltiplabel-5'])) { ?>
                            tooltipLabel5 = '<?php echo $this->relationViewConfig['tooltiplabel-5'] ?>'; 
                        <?php  } ?>
                        var content = ' <div class="card pb0 mb0 border-0 shadow-0">'+
                                            '<div class="card-body">'+
                                                '<div class="d-sm-flex align-item-sm-center flex-sm-nowrap">'+
                                                    '<div class="w-100">'+
                                                        <?php
                                                        if (issetParam($this->relationViewConfig['tooltip-1'])) {
                                                        ?>
                                                        '<h6 class="tooltip-1 w-100 text-center" title="">'+dvFieldValueShow(rowData.<?php echo $this->relationViewConfig['tooltip-1']; ?>)+'</h6>'+
                                                        '<div class="row mb-2">'+
                                                            <?php
                                                            }
                                                            if (issetParam($this->relationViewConfig['tooltip-2'])) {
                                                            ?>
                                                            '<div class="col tooltip-2">'+ tooltipLabel2 +' '+dvFieldValueShow(rowData.<?php echo $this->relationViewConfig['tooltip-2']; ?>)+'</div>'+
                                                            <?php
                                                            }
                                                            if (issetParam($this->relationViewConfig['tooltip-3'])) {
                                                            ?>
                                                            '<div class="col tooltip-3">'+ tooltipLabel3 + ' '+dvFieldValueShow(rowData.<?php echo $this->relationViewConfig['tooltip-3']; ?>)+'</div>'+                            
                                                            <?php
                                                            }
                                                            ?>
                                                        '</div>'+
                                                        '<div class="row mb-2">'+
                                                            <?php
                                                            if (issetParam($this->relationViewConfig['tooltip-4'])) {
                                                            ?>
                                                            '<div class="col tooltip-4">'+ tooltipLabel4 +' '+dvFieldValueShow(rowData.<?php echo $this->relationViewConfig['tooltip-4']; ?>)+'</div>'+
                                                            <?php
                                                            }
                                                            ?>
                                                        '</div>'+
                                                        '<div class="row mb-2">'+
                                                            <?php
                                                            if (issetParam($this->relationViewConfig['tooltip-5'])) {
                                                            ?>
                                                            '<div class="col tooltip-5">'+ tooltipLabel5 +' '+dvFieldValueShow(rowData.<?php echo $this->relationViewConfig['tooltip-5']; ?>)+'</div>'+
                                                            <?php
                                                            }
                                                            ?>
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
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
                    classes: 'qtip-bootstrap qtip-fccalendar',
                    width: 500, 
                    tip: {
                        width: 12,
                        height: 7
                    }
                }
            });
            <?php } ?>
        },
        loading: function (isLoading) {
            /* console.log('isLoading : ' + isLoading); */
            
            mainSelector_<?php echo $this->indicatorId; ?>.find('.kpidv-data-filter-col input[data-path="filterStartDate"]').val(filterStart<?php echo $this->indicatorId; ?>);
            mainSelector_<?php echo $this->indicatorId; ?>.find('.kpidv-data-filter-col input[data-path="filterEndDate"]').val(filterEnd<?php echo $this->indicatorId; ?>);
        },
        eventClick: function(arg) {
            if (!$(arg.el).hasClass('no-dataview')) $(arg.el).addClass('no-dataview');
            <?php if (issetParam($this->relationViewConfig['eventclicksidebar']) !== '') { ?>
                manageKpiIndicatorValue(arg.el, '<?php echo $this->relationViewConfig['eventclicksidebar'] ?>', '<?php echo $this->indicatorId; ?>', true, {mode: 'view'}, 'indicatorSidebarView_<?php echo $this->indicatorId ?>', 'refetchEvents_<?php echo $this->indicatorId ?>');
                mvCalendar_<?php echo $this->indicatorId; ?>.find('.calendar-bp-layout').show();
            <?php }elseif (issetParam($this->relationViewConfig['eventclick']) !== '' && $eventClickProcess) { ?>
                manageKpiIndicatorValue(arg.el, '<?php echo $eventClickProcess['kpiTypeId']; ?>', '<?php echo $eventClickProcess['srcIndicatorId']; ?>', true, <?php echo $eventClickProcess['mode']; ?>, undefined, 'refetchEvents_<?php echo $this->indicatorId ?>', '<?php echo $this->indicatorId ?>');
            <?php } ?>
        },
        /* dateClick: function(arg) {
            var date = arg.date;
            var jsEvent = arg.jsEvent;
            var view = arg.view;
            console.log(view);
        },  */
        <?php if ($eventSelectableProcessId) { ?>
            select: function(arg) {
                var dateStartGet = moment(arg.start);
                var dateEndGet = moment(arg.end);
                var dateEndDay = arg.end.getDate()-1;
                
                var filterStartDate = dateStartGet.format('YYYY-MM-DD'); // HH:mm:ss
                var filterEndDate = dateEndGet.format('YYYY-MM-') + (dateEndDay < 10 ? '0' + dateEndDay : dateEndDay); // HH:mm:ss

                manageKpiIndicatorValue(arg.el, '<?php echo $eventClickProcess['kpiTypeId']; ?>', '<?php echo $eventClickProcess['srcIndicatorId']; ?>', true, <?php echo $eventClickProcess['mode']; ?>, undefined, 'refetchEvents_<?php echo $this->indicatorId ?>', '<?php echo $this->indicatorId ?>');
            },
        <?php } ?>
        progressiveEventRendering: function(event, element, view) {}
    });
    
    calendar<?php echo $this->indicatorId; ?>.render();

    setInterval(() => {
        /* calendar<?php echo $this->indicatorId; ?>.refetchEvents(); */
    }, <?php echo checkDefaultVal($fcRefreshTimer, '0.5') ?>*1000);
}

function indicatorSidebarView_<?php echo $this->indicatorId ?> (data) {
    mvCalendar_<?php echo $this->indicatorId; ?>.find('.calendar-bp-layout').empty().append('<form method="post" enctype="multipart/form-data">' + data.html + '</form>').promise().done(function () {
        calendar<?php echo $this->indicatorId; ?>.updateSize();
    });
}

function refetchEvents_<?php echo $this->indicatorId ?> () {
    calendar<?php echo $this->indicatorId; ?>.refetchEvents();
}

</script>