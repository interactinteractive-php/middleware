<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.'); 
$headerPathArray = $headerArray = $rowsArray = array();
$positionTimer = '';
$positionSetTimer = '';
$minusPx = '150';

if (issetParamArray($this->relationComponentsConfigData['header'])) {
    $headerPathArray = $this->relationComponentsConfigData['header'];
    $headerArray = Arr::groupByArrayOnlyRow($headerPathArray, 'trg_indicator_path', false);
    $positionTimer = issetParam($this->rowData[$headerArray['position-timer']['src_indicator_path']]);
    $positionSetTimer = issetParam($this->rowData[$headerArray['position-settimer']['src_indicator_path']]);
    
}

if (issetParamArray($this->relationComponentsConfigData['rows'])) {
    $rowsPathArray = $this->relationComponentsConfigData['rows'];
    $rowsArray = Arr::groupByArrayOnlyRow($rowsPathArray, 'trg_indicator_path', false);
}

?>
<div class="wg-form-paper <?php echo $this->uniqId ?> " id="mv-checklist-render<?php echo $this->uniqId ?>">
    <div class="wg-form d-flex">
        <div class="card card-side no-border px-0">
            <div class="card-body">
                <div class="d-flex justify-content-center px-2">
                    <?php 
                        $logoImage = 'assets/custom/img/new_veritech_black_logo.png';

                        if (isset($this->logoImage) && file_exists($this->logoImage)) {
                            $logoImage = $this->logoImage;
                        }
                    ?>
                    <img style="height: 30px;float: left;" class="mr-auto" src="<?php echo $logoImage ?>">
                    <p class="mb-0 mt-1 ml-2 headerTitle mr-auto"><?php echo $this->title ?></p>
                    <?php if (issetParam($rowsArray)) { ?>
                    <button type="button" class="btn btn-sm btn-circle mr-1 btn-success bp-btn-save bp-btn-finish<?php echo $this->uniqId ?> pull-right" onclick="saveQuestion_<?php echo $this->uniqId ?>(this)" style="display: none;">
                        <i class="icon-checkmark-circle2"></i> <?php echo Lang::line('finish_btn') ?>
                    </button>
                    <?php } ?>
                </div>
                <ul class="nav nav-tabs nav-tabs-bottom mt-3">
                    <li class="nav-item"><a href="#tab-info-section-<?php echo $this->uniqId ?>" class="nav-link active" data-toggle="tab"><?php echo Lang::line('LMS_EXAM_TITLE_001') ?> </a></li>
                    <li class="nav-item"><a href="#tab-question-section<?php echo $this->uniqId ?>" class="nav-link disabled" data-toggle="tab"><?php echo Lang::line('LMS_EXAM_TITLE_002') ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="tab-info-section-<?php echo $this->uniqId ?>">
                        <div class="kpi-ind-tmplt-section padding-content">
                            <div class="row m-0">
                                <?php 
                                if ($headerArray) {
                                    for ($i = 1; $i <= sizeOf($headerArray)/2; $i++) {
                                        if (issetParam($headerArray['position-'. $i .'-label'])) {
                                            ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo issetParam($headerArray['position-'. $i .'-label']['default_value']) ?>:</label>
                                                    <input type="text" readonly class="form-control" value="<?php echo is_array(issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']])) ? '' : issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']]); ?>">
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                }
                                ?>
                            </div>
                            <?php if (issetParam($rowsArray)) { ?>
                                <div class="row m-0 mt-4">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-circle btn-success bp-btn-start pull-right" onclick="startExam_<?php echo $this->uniqId ?>(this)">
                                        <?php echo Lang::line('LMS_EXAM_START') ?>
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-question-section<?php echo $this->uniqId ?>">
                        <form method="post" enctype="multipart/form-data" class="saveForm">
                            <div class="d-flex">
                                <div class="d-none">
                                    <?php 
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiMainIndicatorId'.Mdform::$mvPathSuffix, 'value' => $this->indicatorId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiStructureIndicatorId'.Mdform::$mvPathSuffix, 'value' => $this->structureIndicatorId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'uxFlowActionIndicatorId'.Mdform::$mvPathSuffix, 'value' => $this->uxFlowActionIndicatorId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'uxFlowIndicatorId'.Mdform::$mvPathSuffix, 'value' => $this->uxFlowIndicatorId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiTblId'.Mdform::$mvPathSuffix, 'value' => Mdform::$firstTplId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiTblIdField'.Mdform::$mvPathSuffix, 'value' => Mdform::$inputId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiDataTblName'.Mdform::$mvPathSuffix, 'value' => $this->dataTableName));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiNamePattern'.Mdform::$mvPathSuffix, 'value' => $this->namePattern));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiCrudIndicatorId'.Mdform::$mvPathSuffix, 'value' => $this->crudIndicatorId));
                                        echo Form::hidden(array('name' => Mdform::$mvPathPrefix.'kpiActionType'.Mdform::$mvPathSuffix, 'value' => $this->actionType));
                                        echo $this->standardHiddenFields;
                                        echo implode('', Mdform::$headerHiddenControl);
                                    ?>
                                    <?php foreach ($this->rowData as $key => $row) {
                                        if (!is_array($row)) { ?>
                                            <input type="text" name="mvParam[<?php echo $key; ?>]" value="<?php echo $row ?>" >
                                            <?php }
                                    } ?>
                                </div>
                                <div class="sidebar sidebar-light sidebar-secondary sidebar-expand-md pr-2" style="width:280px">
                                    <div class="sidebar-content">
                                        <div class="card">
                                            <div class="card-body mv-checklist-menu" >
                                                <ul class="nav nav-sidebar" data-nav-type="accordion">
                                                    <?php 
                                                    if (issetParam($rowsArray['position-1'])) {
                                                        $position1PathArr = explode('.', $rowsArray['position-1']['src_indicator_path']);
                                                        if (issetParamArray($this->rowData[$position1PathArr['0']])) {
                                                            
                                                            $position1GroupArray = Arr::groupByArrayOnlyRows($this->rowData[$position1PathArr['0']], $position1PathArr['1']);
                                                            foreach ($position1GroupArray as $position1Key => $position1Group) {
                                                                ?>
                                                                <li class="nav-item nav-item-submenu nav-group-sub-mv-opened">
                                                                    <a href="javascript:;" class="nav-link mv_checklist_02_groupname"><?php echo $position1Key; ?></a>
                                                                    <ul class="nav nav-group-sub">
                                                                    <?php 
                                                                    
                                                                    $position2PathArr = explode('.', $rowsArray['position-2']['src_indicator_path']);
                                                                    $position2GroupArray = Arr::groupByArrayOnlyRows($position1Group, $position2PathArr['1']);
                                                                    $index= $counter= 0;
                                                                    foreach ($position2GroupArray as $position2Key =>  $position2Grp) { 
                                                                        $position2Group = $position2Grp['0'];
                                                                        $tmparr = $hideTmparr = array();

                                                                        foreach ($position2Grp as $rKey => $rVal) {
                                                                            for ($c=3; $c<=10; $c++) {
                                                                                if (issetParam($rowsArray['position-' . $c]['src_indicator_path'])) {
                                                                                    $position2PathArr = explode('.', $rowsArray['position-' . $c]['src_indicator_path']);
                                                                                    if (issetParam($position2PathArr['1']))
                                                                                        $tmp['position' . $c] = issetParam($rVal[$position2PathArr['1']]);
                                                                                }
                                                                            }
                                                                            array_push($tmparr, $tmp);
                                                                            array_push($hideTmparr, $rVal);
                                                                        }
                                                                        
                                                                        $rowJson = htmlentities(json_encode($tmparr), ENT_QUOTES, 'UTF-8');
                                                                        $hideRowJson = htmlentities(json_encode($hideTmparr), ENT_QUOTES, 'UTF-8');
                                                                        ?>
                                                                        <li class="nav-item checklistmenu-item">
                                                                            <a href="javascript:;" class="mv_checklist_02_sub nav-link" onclick="checkMenuFnc<?php echo $this->uniqId ?>(this)" data-uniqid="<?php echo $this->uniqId; ?>" data-json="<?php echo $rowJson; ?>" data-paramhidden="<?php echo $hideRowJson; ?>" data-iscomment="1" data-stepid="<?php echo $this->uniqId . '_' . $index++; ?>">
                                                                                <i class="far fa-square"></i>
                                                                                <span class="pt1"><?php echo $position2Key; ?></span>
                                                                            </a>
                                                                        </li>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    </ul>
                                                                </li>
                                                                <?php 
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 main-content" style="background-color: #F9F9F9; width: 100vh !important;">
                                    <div class="mv-checklist-render-comment p-3">
                                        <div class="w-100 text-center">
                                            <img src="middleware/assets/img/process/background/watch.png" />
                                        </div>
                                    </div>
                                    <?php 
                                        if (issetParam($rowsArray['position-1'])) {
                                            $position1PathArr = explode('.', $rowsArray['position-1']['src_indicator_path']);
                                            if (issetParamArray($this->rowData[$position1PathArr['0']])) {
                                                
                                                $position1GroupArray = Arr::groupByArrayOnlyRows($this->rowData[$position1PathArr['0']], $position1PathArr['1']);
                                                foreach ($position1GroupArray as $position1Key => $position1Group) {

                                                    $position2PathArr = explode('.', $rowsArray['position-2']['src_indicator_path']);
                                                    $position2GroupArray = Arr::groupByArrayOnlyRows($position1Group, $position2PathArr['1']);
                                                    $index= $counter = 0;
                                                    $questionIndex = 1;
                                                    foreach ($position2GroupArray as $position2Key =>  $position2Grp) { 
                                                        $position2Group = $position2Grp['0'];
                                                        $tmparr = $hideTmparr = array();

                                                        foreach ($position2Grp as $rKey => $rVal) {
                                                            for ($c=3; $c<=10; $c++) {
                                                                if (issetParam($rowsArray['position-' . $c]['src_indicator_path'])) {
                                                                    $position2PathArr = explode('.', $rowsArray['position-' . $c]['src_indicator_path']);
                                                                    if (issetParam($position2PathArr['1']))
                                                                        $tmp['position' . $c] = issetParam($rVal[$position2PathArr['1']]);
                                                                }
                                                            }
                                                            array_push($tmparr, $tmp);
                                                            array_push($hideTmparr, $rVal);
                                                        }
                                                        
                                                        $rowJson = htmlentities(json_encode($tmparr), ENT_QUOTES, 'UTF-8');
                                                        $hideRowJson = htmlentities(json_encode($hideTmparr), ENT_QUOTES, 'UTF-8');
                                                        ?>
                                                        <div class="mv-checklist-render-comment p-3" data-stepkey="<?php echo $this->uniqId . '_' . $index; ?>" style="display: none">
                                                            <p class="question-txt"><?php echo $questionIndex++ . '. ' . checkDefaultVal($tmparr[0]['position3'], '...') ?> :</p>
                                                            <div class="row">
                                                                <div class="col-md-12 column-grap">
                                                                    <?php foreach ($tmparr as $i => $row) {
                                                                        ?>
                                                                        <button type="button" class="btn text-left answer-txt">
                                                                            <?php echo $row['position4'] ?>
                                                                            <div class="hide-param d-none">
                                                                                <input type="hidden" name="mvParam[<?php echo $position2PathArr['0'] . '.rowState' ?>][<?php echo $counter; ?>]" data-path="<?php echo $position2PathArr['0'] . '.rowState' ?>" data-col-path="rowState" value="add" >
                                                                                <input type="hidden" name="mvParam[<?php echo $position2PathArr['0'] . '.rowCount' ?>][<?php echo $counter; ?>]" data-path="<?php echo $position2PathArr['0'] . '.rowCount' ?>" data-col-path="rowCount" value="0" >
                                                                                <?php foreach ($hideTmparr[$i] as $key => $value) { ?>
                                                                                    <input type="hidden" name="mvParam[<?php echo $position2PathArr['0'] . '.' .$key ?>][<?php echo $counter; ?>]" data-path="<?php echo $position2PathArr['0'] . '.' .$key ?>" data-col-path="<?php echo $key ?>" value="<?php echo $value ?>" >
                                                                                <?php } ?>
                                                                            </div>
                                                                        </button>
                                                                    <?php 
                                                                        $counter++;
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        $index++;
                                                    }

                                                }

                                            }
                                        }
                                    ?>
                                </div>
                            </div>           
                        </form>
                        <?php if (issetParam($rowsArray)) { ?>
                            <div class="w-100 pull-left p-2 actions"> 
                                <button type="button" class="btn btn-sm btn-circle mr-1 btn-success bp-btn-next pull-right" onclick="nextQuestion_<?php echo $this->uniqId ?>(this)">
                                    <?php echo Lang::line('next_btn') ?> <i class="icon-arrow-right5"></i> 
                                </button>
                                <button type="button" class="btn btn-sm btn-circle mr-1 btn-success bp-btn-next pull-right" onclick="prevQuestion_<?php echo $this->uniqId ?>(this)">
                                    <i class="icon-arrow-left5"></i>  <?php echo Lang::line('prev_btn') ?>
                                </button>
                            </div> 
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php if (checkDefaultVal($positionTimer, '0') !== '0') { ?>
        <div class="timer-group<?php echo $this->uniqId ?> ">
            <div class="timer-group ml-3">
                <div class="timer hour d-none">
                    <div class="hand<?php echo $this->uniqId ?>"><span></span></div>
                    <div class="hand<?php echo $this->uniqId ?>"><span></span></div>
                </div>
                <div class="timer minute">
                    <div class="hand<?php echo $this->uniqId ?> not-start"><span></span></div>
                    <div class="hand<?php echo $this->uniqId ?> not-start"><span></span></div>
                </div>
                <!-- <div class="timer second">
                    <div class="hand"><span></span></div>
                    <div class="hand"><span></span></div>
                </div> -->
                <div class="face">
                    <h2>Хугацаа</h2>
                    <p id="lazy<?php echo $this->uniqId ?>">00:<?php echo checkDefaultVal($positionTimer, '0') < '10' ? '0' . checkDefaultVal($positionTimer, '0') : checkDefaultVal($positionTimer, '00')  ?>:00</p>  
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>

<style type="text/css">

    div[aria-describedby="dialog-widgetrender-<?php echo $this->mainIndicatorId ?>"] {
        .wg-form-paper {
            background-image: url('<?php echo checkDefaultVal($this->bgImage, 'middleware/assets/img/process/background/back.png') ?>');
            background-repeat: no-repeat;
            background-position: top center;
            background-attachment: fixed;
            background-color: #ededed;
            padding-top: 11px;
            background-size: cover;
            height: 100%;
            .wg-form {
                position: relative;
                width: 1200px;
                min-height: calc(100vh - 126px);
                margin-left: auto;
                margin-right: auto;
                .card-side  {
                    margin-top: 40px;
                    padding: 20px;
                    box-shadow: 0px 2px 6px 0 rgba(0,0,0,.5);
                    background: #FFF;
                    border-radius: 20px;
                    width: 1200px;

                    .nav-link.active {
                        color: #699BF7 !important;
                        font-size: 18px;
                    }

                    .done > .nav-link.active,
                    .done > .nav-link.active i {
                        color: #4CAF50 !important;
                    }
                    
                    .nav-link {
                        font-size: 18px;
                        font-weight: 500;
                        line-height: 21px;
                        letter-spacing: 0px;
                        text-align: left;
                        padding: 20px;
                    }
    
                    .nav-tabs-bottom .nav-link.active:before {
                        background-color: #699BF7 !important;
                    }
    
                    .headerTitle {
                        font-size: 24px;
                        font-weight: 700;
                        line-height: 28px;
                        letter-spacing: 0px;
                        text-align: left;
                    }
    
                    .nav-tabs-bottom {
                        box-shadow: 0px 5px 10px 0px #0000001A;
                    }
                    
                    .bp-btn-start,
                    .bp-btn-next,
                    .bp-btn-save {
                        color: #699BF7;
                        border-color: #699BF7;
                        background-color: #FFF !important;
                    }
                    
                    .bp-btn-start:hover,
                    .bp-btn-next:hover,
                    .bp-btn-save:hover {
                        color: #FFF !important;
                        border-color: #699BF7;
                        background-color: #699BF7 !important;
                    }

                    .bp-btn-start,
                    .bp-btn-next,
                    .bp-btn-save {
                        padding: 12px 15px 10px 15px;
                        border-radius: 20px;
                        gap: 5px;
                        background: #468CE2;
                        font-size: 14px;
                        line-height: 16px;
                        letter-spacing: 0em;
                        text-align: center;
                    }
                    
                    .form-control {
                        border-radius: 20px;
                        border: 0.5px;
                        padding: 11px 20px;
                        font-size: 16px;
                        font-weight: 600;
                        line-height: 24px;
                        letter-spacing: 0px;
                        text-align: left;
                        height: 46px !important;
                        color: #585858;
                    }
    
                    .form-label {
                        font-size: 16px;
                        font-weight: 400;
                        line-height: 24px;
                        letter-spacing: 0px;
                        text-align: left;
                    }
    
                    .padding-content {
                        padding: 24px 11px 0 11px;
                    }
    
                    .mv-checklist-menu {
                        overflow-y: auto;
                        overflow-x: hidden;

                        .nav-group-sub-mv-opened .nav-group-sub {
                            display: block;
                        }
                        .nav-sidebar .nav-item:not(.nav-item-header):last-child {
                            padding-bottom: 0 !important;
                        }
                        .nav-item-submenu.nav-group-sub-mv-opened>.nav-link:after {
                            -webkit-transform: rotate(90deg);
                            transform: rotate(90deg);
                        }
                        .nav-group-sub .nav-link {
                            padding-left: 20px;
                        }
                        .nav-item-submenu>.nav-link.mv_checklist_02_groupname:after {
                            margin-top: -6px;
                        }
                        .nav-link.mv_checklist_02_groupname {
                            font-size: 13px;
                            color: #333 !important;
                            font-weight: bold !important;
                            padding-top: 5px;
                            padding-bottom: 5px;
                            text-transform: none !important;
                        }    
                        .nav-link.mv_checklist_02_sub {
                            padding-top: 2px;
                            padding-bottom: 2px;
                            font-size: 12px;
                        }    

                        .nav-link.mv_checklist_02_sub i {
                            color: #1B84FF;
                            margin-top: 2px;
                            font-size: 18px;    
                            margin-right: 13px;
                        }    
                    }
                    
                    .main-content {
                        background-color: #F9F9F9;
                        overflow-y: auto;
                        overflow-x: hidden;
                    }

                    .mv-checklist-render-comment {

                        .question-txt {
                            font-size: 18px;
                            font-weight: 500;
                            line-height: 21px;
                            letter-spacing: 0px;
                            text-align: left;
                            color: #585858;
                            margin-bottom: 20px;
                        }
                        
                        .column-grap {
                            display: inline-block;
                            column-gap: 10px;
                            column-count: 2;
                        }

                        .answer-txt {
                            font-size: 12px;
                            font-weight: 400;
                            line-height: 14px;
                            letter-spacing: 0px;
                            text-align: left !important;
                            padding: 15px 25px 15px 25px !important;
                            border-radius: 50px !important;
                            gap: 10px !important;
                            margin-bottom: 15px;
                            background-color: #FFF !important;
                            border-color: #FFF;
                            color: #585858 !important;
                            width: 100%;
                        }

                        .answer-txt.active,
                        .answer-txt:hover {
                            color: #FFF !important;
                            background: linear-gradient(90deg, #468CE2 0%, rgba(70, 140, 226, 0.52) 100%);
                        }
                        
                    }
                }

                .position-timer {
                    margin-left: 10px;
                    margin-top: 10%;

                    .card-body {
                        padding: 10px;
                        border-radius: 20px;
                        border: none;
                        box-shadow: 0px 5px 10px 0px #0000001A;
                        
                        .timer {

                            font-size: 38px;
                            font-weight: 400;
                            line-height: 38px;
                            letter-spacing: 0px;
                            text-align: center;
                            color: #585858;

                            .num {
                                font-size: 38px;
                                font-weight: 600;
                                line-height: 38px;
                                letter-spacing: 0px;
                                text-align: center;
                                color: #699BF7;
                            }
                            .txt {
                                font-size: 16px;
                                font-weight: 700;
                                line-height: 14px;
                                letter-spacing: 0px;
                                text-align: center;
                                color: #699BF7;
                            }
                            
                            .all {
                                padding: 55px 30px;
                                border: 15px solid #2196F3;
                                border-radius: 50%;
                            }
                        }
                    }
                }
            }
        }

        #dialog-widgetrender-<?php echo $this->mainIndicatorId ?> {
            padding-left: 0;
            padding-right: 0;
        }

        .ui-dialog-titlebar {
            border: none;
            display: none !important;
        }

        .ui-dialog-buttonpane {
            display: none !important;
        }
    }

    <?php if (checkDefaultVal($positionTimer, '0') !== '0') { ?>
    .timer-group<?php echo $this->uniqId ?> {

        .timer-group {
            height: <?php echo 400-$minusPx  ?>px;
            width: <?php echo 400-$minusPx  ?>px;
            margin: 0 auto;
            position: relative;
        }
    
        .timer {
            border-radius: 50%;
            height: 100px;
            width: 100px;
            overflow: hidden;
            position: absolute;
        }
    
        .timer:after {
            background: #FFF;
            border-radius: 50%;
            content: "";
            display: block;
            position: absolute;

            height: 80px;
            width: 80px;
            left: 10px;
            top: 10px;
        }
    
        .timer .hand<?php echo $this->uniqId ?> {
            height: 100%;
            width: 50%;
            float: left;
            overflow: hidden;
            position: relative;
        }
    
        .timer .hand<?php echo $this->uniqId ?> span {
            border: 50px solid rgba(0, 255, 255, .4);
            border-bottom-color: transparent;
            border-left-color: transparent;
            border-radius: 50%;
            display: block;
            position: absolute;
            transform: rotate(225deg);

            height: 0;
            width: 0;
            right: 0;
            top: 0;
        }
    
        .timer .hand<?php echo $this->uniqId ?>:first-child {
            transform: rotate(180deg);
        }
    
        .timer .hand<?php echo $this->uniqId ?> span {
            animation-duration: 4s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }
    
        .timer .done<?php echo $this->uniqId ?>:first-child span,
        .timer .done<?php echo $this->uniqId ?>:last-child span {
            background: rgba(105, 155, 247, 1);
        }

        .timer .alert<?php echo $this->uniqId ?>:first-child span,
        .timer .alert<?php echo $this->uniqId ?>:last-child span {
            background: #FFE2DF !important;
            border-top-color: #F67162 !important;
            border-right-color: #F67162 !important;
        }

        .face.alert<?php echo $this->uniqId ?> p ,
        .face.alert<?php echo $this->uniqId ?> h2 {
            color: #F67162 !important; 
        }

        .timer .hand<?php echo $this->uniqId ?>:not(.done<?php echo $this->uniqId ?>, .not-start):first-child span {
            animation-name: spin1_<?php echo $this->uniqId ?>;
        }
    
        .timer .hand<?php echo $this->uniqId ?>:not(.done<?php echo $this->uniqId ?>, .not-start):last-child span {
            animation-name: spin2_<?php echo $this->uniqId ?>; 
        }
    
        .timer.hour {
            background: rgba(0, 0, 0, .3);
            left: 0;
            top: 0;
            height: <?php echo 400-$minusPx  ?>px;
            width: <?php echo 400-$minusPx  ?>px;
        }
    
        .timer.hour .hand<?php echo $this->uniqId ?> span {
            animation-duration: 3600s;
            border-top-color: rgba(105, 155, 247, 0.9);
            border-right-color: rgba(105, 155, 247, 0.9);
            border-width: <?php echo 200-$minusPx/2  ?>px;
        }
    
        .timer.hour:after {
            left: 20px;
            top: 20px;
            height: <?php echo 360-$minusPx  ?>px;
            width: <?php echo 360-$minusPx  ?>px;
        }
    
        .timer.minute {
            background: rgba(0, 0, 0, .2);
            left: 25px;
            top: 25px;
            height: <?php echo 350-$minusPx  ?>px;
            width: <?php echo 350-$minusPx  ?>px;
        }
    
        .timer.minute .hand<?php echo $this->uniqId ?> span {
            animation-duration: <?php echo checkDefaultVal($positionTimer, '1')*60 ?>s;
            border-top-color: rgba(105, 155, 247, 1);
            border-right-color: rgba(105, 155, 247, 1);
            border-width: <?php echo 175-$minusPx/2  ?>px;
        }
    
        .timer.minute:after {
            left: 20px;
            top: 20px;
            height: <?php echo 310-$minusPx  ?>px;
            width: <?php echo 310-$minusPx  ?>px;
        }
    
        .timer.second {
            background: rgba(0, 0, 0, .2);
            left: 50px;
            top: 50px;
            height: <?php echo 300-$minusPx  ?>px;
            width: <?php echo 300-$minusPx  ?>px;
        }
    
        .timer.second .hand<?php echo $this->uniqId ?> span {
            animation-duration: 1s;
            border-top-color: rgba(255, 255, 255, .15);
            border-right-color: rgba(255, 255, 255, .15);
            border-width: 150px;
        }
    
        .timer.second:after {
            left: 2px;
            top: 2px;
            height: <?php echo 296-$minusPx  ?>px;
            width: <?php echo 296-$minusPx  ?>px;
        }
    
        .face {
            /* background: rgba(0, 0, 0, .1); */
            background: rgb(255 254 254 / 10%);
            border-radius: 50%;
            padding: <?php echo 165-$minusPx  ?>px 40px 0;
            text-align: center;
            position: absolute;
            left: 52px;
            top: 52px;
            height: <?php echo 296-$minusPx  ?>px;
            width: <?php echo 296-$minusPx  ?>px;
        }
    
        .face h2 {
            font-weight: 300; 
            color: rgba(105, 155, 247, 1);
            position: absolute;
            width: 110px;
            left: 18px;
            top: 30px;
        }
    
        .face p {
            border-radius: 20px;
            font-weight: 400;
            position: absolute;
            color: rgba(105, 155, 247, 1);
            font-size: 24px;
            top: 60px;
            left: 20px;
            width: <?php echo 260-$minusPx  ?>px;
        }
    }
    
    @keyframes spin1_<?php echo $this->uniqId ?> {
        0% {
            transform: rotate(225deg);
        }
        50% {
            transform: rotate(225deg);
        }
        100% {
            transform: rotate(405deg);
        }
    }
    
    @keyframes spin2_<?php echo $this->uniqId ?> {
        0% {
            transform: rotate(225deg);
        }
        50% {
            transform: rotate(405deg);
        }
        100% {
            transform: rotate(405deg);
        }
    }
    <?php } ?>
</style>

<script type="text/javascript">
    var $checkList_<?php echo $this->uniqId; ?> = $('#mv-checklist-render<?php echo $this->uniqId ?>');
    var $checkListMenu_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.mv-checklist-menu');
    var $checkListContent_<?php echo $this->uniqId; ?> = $checkList_<?php echo $this->uniqId; ?>.find('.main-content');

    <?php if (checkDefaultVal($positionTimer, '0') !== '0') { ?>
        var defaults = {}, 
        one_second = 1000, 
        one_minute = one_second * 60, 
        one_hour = one_minute * 60, 
        one_day = one_hour * 24, 
        date = new Date (),
        minutes = '<?php echo checkDefaultVal($positionTimer, '0') ?>',
        finishDate = new Date(date.getTime() + minutes*60000);
        
        var requestAnimationFrame<?php echo $this->uniqId ?> = (function() {
            return window.requestAnimationFrame     || 
                window.webkitRequestAnimationFrame  || 
                window.mozRequestAnimationFrame     || 
                window.oRequestAnimationFrame       || 
                window.msRequestAnimationFrame      || 
                function(callback) {
                    window.setTimeout(callback, 1000 / 60);
                };
        }());
    
        function tick<?php echo $this->uniqId ?>() {
            var face = document.getElementById('lazy<?php echo $this->uniqId ?>');
            var now = new Date(), 
                elapsed = finishDate - now, 
                parts = [];
                
            if (elapsed <= 30000) {
                $('.hand<?php echo $this->uniqId ?>').addClass('alert<?php echo $this->uniqId ?>');
                $('.face').addClass('alert<?php echo $this->uniqId ?>');
            }
            if (elapsed <= 0) {
                $('.hand<?php echo $this->uniqId ?>').addClass('done<?php echo $this->uniqId ?>');
                
                $('.hand<?php echo $this->uniqId ?>').removeClass('alert<?php echo $this->uniqId ?>');
                $('.face').removeClass('alert<?php echo $this->uniqId ?>');

                $('.timer-group<?php echo $this->uniqId ?>').find('.face > h2').empty().append('');
                $('.bp-btn-save<?php echo $this->uniqId ?>').trigger('click');
                return false;
            }
            
            parts[0] = '' + Math.floor( elapsed / one_hour );
            parts[1] = '' + Math.floor( (elapsed % one_hour) / one_minute );
            parts[2] = '' + Math.floor( ( (elapsed % one_hour) % one_minute ) / one_second );

            parts[0] = (parts[0].length == 1) ? '0' + parts[0] : parts[0];
            parts[1] = (parts[1].length == 1) ? '0' + parts[1] : parts[1];
            parts[2] = (parts[2].length == 1) ? '0' + parts[2] : parts[2];
            face.innerText = parts.join(':');

            requestAnimationFrame<?php echo $this->uniqId ?>(tick<?php echo $this->uniqId ?>);
        }
    <?php } ?>

    $(function() { 

        $checkListMenu_<?php echo $this->uniqId; ?>.height($(window).height() - $checkListMenu_<?php echo $this->uniqId; ?>.offset().top - 390);
        $checkListContent_<?php echo $this->uniqId; ?>.height($(window).height() - $checkListMenu_<?php echo $this->uniqId; ?>.offset().top - 390);
        
        $checkListMenu_<?php echo $this->uniqId; ?>.on('click', 'a.nav-link:not(.disabled)', function() {
            var $this = $(this);
        
            $checkListMenu_<?php echo $this->uniqId; ?>.find('a.nav-link.active').removeClass('active');
            $this.addClass('active');
            
            var rowJson = $this.attr('data-json'), uniqId = $this.attr('data-uniqid'), indicatorId = $this.attr('data-indicatorid'), 
                isComment = $this.hasAttr('data-iscomment') ? $this.attr('data-iscomment') : 0;
            
            if (typeof rowJson === 'undefined') {
                if ($this.parent().hasClass('nav-group-sub-mv-opened')) {
                    $this.parent().removeClass('nav-group-sub-mv-opened');
                } else {
                    $this.parent().addClass('nav-group-sub-mv-opened');
                }

                return;
            }
            
            if (typeof rowJson !== 'object') {
                var jsonObj = JSON.parse(html_entity_decode(rowJson, 'ENT_QUOTES'));
            } else {
                var jsonObj = rowJson;
            }

        });

    });

    function startExam_<?php echo $this->uniqId ?>(element) {
        var $this = $(element),
            $parent = $this.closest('.<?php echo $this->uniqId ?>');
        $this.remove();
        $parent.find('a[href="#tab-question-section<?php echo $this->uniqId ?>"]').removeClass('disabled');
        $parent.find('a[href="#tab-question-section<?php echo $this->uniqId ?>"]').trigger('click');
        $parent.find('.bp-btn-finish<?php echo $this->uniqId ?>').show();

        <?php if (checkDefaultVal($positionTimer, '0') !== '0') { ?>
            date = new Date (),
            minutes = '<?php echo checkDefaultVal($positionTimer, '0') ?>',
            finishDate = new Date(date.getTime() + minutes*60000);
            
            tick<?php echo $this->uniqId ?>();
            $('.hand<?php echo $this->uniqId ?>').removeClass('not-start');
            /* $('.timer-group<?php echo $this->uniqId ?>').show(); */
        <?php } ?>
    } 

    function nextQuestion_<?php echo $this->uniqId ?>(element) {
        var $this = $(element),
            $parent = $this.closest('.<?php echo $this->uniqId ?>');
        
        if ($parent.find('.checklistmenu-item.selected').next().length > 0) {
            $parent.find('.checklistmenu-item.selected').next().find('a.mv_checklist_02_sub').trigger('click');
        }
    } 

    function prevQuestion_<?php echo $this->uniqId ?>(element) {
        var $this = $(element),
            $parent = $this.closest('.<?php echo $this->uniqId ?>');
        
        if ($parent.find('.checklistmenu-item.selected').prev().length > 0) {
            $parent.find('.checklistmenu-item.selected').prev().find('a.mv_checklist_02_sub').trigger('click');
        }

    } 

    function saveQuestion_<?php echo $this->uniqId ?>(element) {
        
        var confirmDialog = '#dialog-ntrservice-confirm';
        if (!$(confirmDialog).length) {
            $('<div id="' + confirmDialog.replace('#', '') + '"></div>').appendTo('body');
        }

        $(confirmDialog).empty().append('<p style="text-align: center; font-size: 17px;"><?php echo Lang::line('msg_save_confirm') ?></p>');
        $(confirmDialog).dialog({
            cache: false,
            resizable: false,
            bgiframe: true,
            autoOpen: false,
            title: 'Санамж',
            width: 450,
            height: "auto",
            modal: true,
            close: function() {
                $(confirmDialog).empty().dialog('destroy').remove();
            },
            buttons: [{
                    text: plang.get('yes_btn'),
                    class: 'btn green-meadow btn-sm',
                    click: function() {
                        $('.bp-btn-save<?php echo $this->uniqId ?>').trigger('click');
                        $(confirmDialog).empty().dialog('destroy').remove();
                    }
                },
                {
                    text: plang.get('no_btn'),
                    class: 'btn blue-madison btn-sm',
                    click: function() {
                        $(confirmDialog).empty().dialog('destroy').remove();
                    }
                }
            ]
        });

        $(confirmDialog).dialog('open');
    }

    $('a[href="#tab-question-section<?php echo $this->uniqId ?>"]').on('shown.bs.tab', function(e) {
        $checkList_<?php echo $this->uniqId; ?>.find('.mv_checklist_02_sub').first().trigger('click');
        /* $checkList_<?php echo $this->uniqId; ?>.find('.bp-btn-start').hide(); */
    });

    $('a[href="#tab-info-section-<?php echo $this->uniqId ?>"]').on('shown.bs.tab', function(e) {
        /* $('.timer-group<?php echo $this->uniqId ?>').hide(); */
    });

    $('body').on('click', '#tab-question-section<?php echo $this->uniqId ?> .answer-txt', function () {
        var _this = $(this),
            _selector = _this.find('.hide-param');
            _this.closest('.column-grap').find('input[data-path="C8.IS_CORRECT"]').val('0');
            _selector.find('input[data-path="C8.IS_CORRECT"]').val('1');

        _this.closest('.column-grap').find('.active').removeClass('active');
        _this.addClass('active');
        $('#tab-question-section<?php echo $this->uniqId ?> .checklistmenu-item.selected').find('i.fa-square').addClass('fa-check-square').removeClass('fa-square');
        $('#tab-question-section<?php echo $this->uniqId ?> .checklistmenu-item.selected').addClass('done');
    });

    function checkMenuFnc<?php echo $this->uniqId ?>(element) {
        var _this = $(element),
            stepKey = _this.attr('data-stepid'),
            rowJson = JSON.parse(_this.attr('data-json')),
            hideParams = JSON.parse(_this.attr('data-paramhidden')),
            $parentSelector = _this.closest('#mv-checklist-render<?php echo $this->uniqId ?>');
        
        $parentSelector.find('.checklistmenu-item.selected').removeClass('selected');
        _this.parent().addClass('selected');
        /* _this.find('i.fa-square').addClass('fa-check-square').removeClass('fa-square'); */

        $parentSelector.find('.main-content > .mv-checklist-render-comment').hide();
        $parentSelector.find('.main-content > .mv-checklist-render-comment[data-stepkey="'+ stepKey +'"]').show();
    };

</script>