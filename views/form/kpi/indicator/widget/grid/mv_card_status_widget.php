<style type="text/css">
.mv_card_status_widget {
    display: inline-block;
    width: 90px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    -ms-border-radius: 4px;
    -o-border-radius: 4px;
    border-radius: 4px;
}

.mv_card_status_widget:hover .no-dataview {
    display: block !important;
}
.mv_card_status_widget {
    .card {
       margin-bottom: 0;
        border-radius: 10px !important;
        padding: 0 !important;
    }
    .card-body .card-img {
        border-radius: 0;
        border-bottom: 1px #eee solid;
    }
}

.mv_card_status_widget h5 {
    display: block;
    padding: 0 10px;
    font-size: 12px;
    color: #333;
    line-height: 20px;
    text-align: center;
    overflow: hidden;
}

.mv_card_status_widget_main {
    gap: 20px;
    display: inline-flex;
    flex-wrap: wrap;
    justify-content: center;
    padding: 20px 0;

    .mv_card_status_widget.active > .card,
    .mv_card_status_widget:hover > .card {
        border-color: #2196f3;
    }

    .left-sidewidget {
        /* gap: 16px;
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 280px; */
        width: 270px;
        max-height: max-content;
        min-height: max-content;
        min-width: 200px;
    }

    .position-1 {
        margin: 0;
        max-height: 47px;
        margin-bottom: 16px;
        .badge {
            font-size: 20px;
            color: #282A30;
            background: #F9F8F9;
            border-radius: 4px;
            padding: 12px;
            
        }
    }

    .position-2 {
        font-size: 16px; 
        color: #282A30;
        margin: 0;
        max-height: 47px;
        margin-bottom: 16px;
    }
    .position-3 {
        font-size: 14px;
        color: #707579;
        margin: 0;
        max-height: 47px;
        margin-bottom: 16px;
    }
    .position-4 {
        font-size: 14px;
        margin: 0;
        max-height: 47px;
    }

    .right-sidewidget {
        height: 180px !important;
    }
}

</style>

<div class="dv-process-buttons mt-2 ml-2">
    <div class="btn-group btn-group-devided">
    <?php 
    $contextMenu = array();
    $deleteClickAction = '';
    $deleteCrudId = '';
    $mapId = '';
    $buttonClickAction = '';
    foreach ($this->process as $process) {

        $srcIndicatorId = $process['structure_indicator_id'];
        $crudIndicatorId = issetParam($process['crud_indicator_id']);
        $isFillRelation = issetParam($process['is_fill_relation']);
        $isNormalRelation = issetParam($process['is_normal_relation']);
        $typeCode = $process['type_code'];
        $kpiTypeId = $process['kpi_type_id'];
        $buttonName = $className = $onClick = $description = '';
        $opt = ', undefined';

        if ($srcIndicatorId == $this->indicatorId) {

            if ($typeCode == 'create') {

                $mapId = issetParam($process['map_id']);
                $labelName = $process['label_name'] == 'Нэмэх' ? $this->lang->line('add_btn') : $this->lang->line($process['label_name']);
                $className = 'btn btn-success btn-circle btn-sm';
                $buttonName = '<i class="far fa-plus"></i> '.$labelName;
                
                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'create\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'create\'}';
                }
                
                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', false$opt, undefined, 'mvWidgetFileViewCreateCallback');";
                }

            } elseif ($typeCode == 'update') {

                $labelName = $process['label_name'] == 'Засах' ? $this->lang->line('edit_btn') : $this->lang->line($process['label_name']);
                $isUpdate = true;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'update\'}';
                } 

                $className = 'btn btn-warning btn-circle btn-sm';
                $buttonName = '<i class="far fa-edit"></i> '.$labelName;

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update'});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true, undefined, undefined, 'mvWidgetFileViewCreateCallback');";
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
                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true, {mode: 'view'});";

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
                $onClick = "removeKpiIndicatorValue(this, '".$this->indicatorId."', 'mvWidgetFileViewDeleteCallback');";
                $deleteClickAction = $onClick;
                $deleteCrudId = $process['crud_indicator_id'];

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
                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$crudIndicatorId', false, {transferSelectedRow: true});";

            } elseif ($kpiTypeId == '1080') {

                $className = 'btn blue-steel btn-circle btn-sm';
                $buttonName = '<i class="far fa-play"></i> ' . $this->lang->line($process['label_name'] ? $process['label_name'] : $process['name']);
                $onClick = "callWebServiceKpiIndicatorValue(this, '$crudIndicatorId');";
            } 

        } else {

            $description = $this->lang->line(issetParam($process['description']));
            $processName = $this->lang->line(issetParam($process['label_name']));
            $isDfillRelation = issetParam($process['is_dfill_relation']);

            if ($typeCode == 'create') {

                $className = 'btn btn-success btn-circle btn-sm';
                $buttonName = '<i class="far fa-plus"></i> '.$processName;
                $mapId = issetParam($process['map_id']);

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'create\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'create\'}';
                }

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$srcIndicatorId."', false$opt, undefined, 'mvWidgetFileViewCreateCallback');";
                }

            } elseif ($typeCode == 'update') {

                $className = 'btn btn-warning btn-circle btn-sm';
                $buttonName = '<i class="far fa-edit"></i> '.$processName;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'update\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'update\'}';
                }

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update'});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true, undefined, undefined, 'mvWidgetFileViewCreateCallback');";
                }

            } elseif ($typeCode == 'read') {

                $className = 'btn purple btn-circle btn-sm';
                $buttonName = '<i class="far fa-eye"></i> '.$processName;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'view\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'view\'}';
                } else {
                    $opt = ', {mode: \'view\'}';
                }

                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true$opt);";

            } elseif ($typeCode == 'delete') {

                $className = 'btn btn-danger btn-circle btn-sm';
                $buttonName = '<i class="far fa-trash"></i> '.$processName;
                $onClick = "removeKpiIndicatorValue(this, '$srcIndicatorId', 'mvWidgetFileViewDeleteCallback');";
                $deleteClickAction = $onClick;
                
                $deleteCrudId = $process['crud_indicator_id'];

            } elseif ($typeCode == 'excel') {

                $className = 'btn green btn-circle btn-sm';
                $buttonName = '<i class="far fa-file-excel"></i> '.$processName;
                $onClick = "excelImportKpiIndicatorValue(this, '$srcIndicatorId');";
            }

            if (issetParam($this->relationViewConfig['position-indicator-1']) == $srcIndicatorId) {
                $buttonClickAction = $onClick;
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
    } ?>
    </div>        
</div>        
        
<div class="mv_card_status_widget_main ">
    <?php                                            
    $dataResult = $this->response['rows'];
    foreach ($dataResult as $row) {
        $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');
    ?>
        <a href="javascript:;" style="width: 315px;" class="mv_card_status_widget no-dataview" data-rowdata="<?php echo $rowJson; ?>" data-clickprocess="<?php echo issetParam($this->relationViewConfig['position-indicator-1']) ?>">
            <div class="card">
                <div class="card-body p-2 d-flex">
                    <div class="left-sidewidget ">
                        <div class="w-100 pull-left position-1 ">
                            <span class="badge badge-warning badge-icon"><i class="<?php echo issetParam($row[$this->relationViewConfig['position-1']]) ?>"></i></span>
                        </div>
                        <p class="w-100 pull-left text-left text-one-line position-2">
                            <?php echo issetParam($row[$this->relationViewConfig['position-2']]) ?>
                        </p>
                        <p class="w-100 pull-left text-left text-three-line position-3">
                            <?php echo issetParam($row[$this->relationViewConfig['position-3']]) ?>
                        </p>
                        <p class="w-100 pull-left text-left text-three-line position-4">
                            <span class="badge badge-primary rounded-pill py-1 px-3" style="background: '<?php echo issetParam($this->relationViewConfig['position-5']) ? checkDefaultVal($row[$this->relationViewConfig['position-5']], '...') : ''; ?>'"><?php echo checkDefaultVal($row[$this->relationViewConfig['position-4']], '...') ?></span>
                        </p>
                    </div>
                    <div class="right-sidewidget pull-right ml-auto">
                        <button href="javascript:;" <?php echo ($buttonClickAction) ? 'onclick="' . $buttonClickAction . '"' : ''; ?> data-rowdata="<?php echo $rowJson; ?>"  class="btn btn-outline bg-indigo-400 text-indigo-400 btn-icon h-100 no-dataview"><i class="icon-arrow-right15"></i></button>
                    </div>
                </div>
            </div>
        </a>
    <?php
    } 
    ?>
</div>

<script type="text/javascript">
$('.mv_card_status_widget').click(function() {
    var $this = $(this);
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        return;
    }
    $this.closest('.mv_card_status_widget_main').find('.active').removeClass('active');
    $this.addClass('active');
}); 
function mvWidgetFileViewCreateCallback() {
    $('div[data-menu-id="164723019841110"]').remove();
    $('a[data-menu-id="164723019841110"]').trigger('click');
}    

function mvWidgetFileViewDeleteCallback(elem) {
    mvWidgetFileViewCreateCallback();
}
</script>