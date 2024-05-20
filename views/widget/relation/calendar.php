<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.'); ?> 
<?php 
$tmparr = $hideTmparr = array();
$headerPathArray = $rowsArray = $position1GroupArray = array();
if (issetParamArray($this->relationComponentsConfigData['header'])) {
    $headerPathArray = $this->relationComponentsConfigData['header'];
    $headerArray = Arr::groupByArrayOnlyRow($headerPathArray, 'trg_indicator_path', false);
}

if (issetParamArray($this->relationComponentsConfigData['rows'])) {
    $rowsPathArray = $this->relationComponentsConfigData['rows'];
    $rowsArray = Arr::groupByArrayOnlyRow($rowsPathArray, 'trg_indicator_path', false);
    
    $position1PathArr = explode('.', $rowsArray['position-1']['src_indicator_path']);
    $position1GroupArray = Arr::groupByArrayOnlyRows($this->rowData[$position1PathArr['0']], $position1PathArr['1']);
    $index= $counter= 0;
    foreach ($position1GroupArray as $position1Key =>  $position1Grp) { 
        $position1Group = $position1Grp['0'];
        $tmparr = $hideTmparr = array();
        foreach ($position1Grp as $rKey => $rVal) {
            
            for ($c=1; $c<=10; $c++) {
                if (issetParam($rowsArray['position-' . $c]['src_indicator_path'])) {
                    $position1PathArr = explode('.', $rowsArray['position-' . $c]['src_indicator_path']);
                    if (issetParam($position1PathArr['1'])) {
                        $tmp['position' . $c] = issetParam($rVal[$position1PathArr['1']]);
                    }
                }
            }
            
            $tmp['position-recordid'] = $tmp['position-starttime'] = $tmp['position-endtime'] = '';

            if (issetParam($rVal['START_TIME'])) {
                $parsed = explode(':', $rVal['START_TIME']);
                $timeSecond = $parsed['0'] * 3600 + $parsed['1'] * 60 + $parsed['2'];    
                $tmp['position-starttime'] = $timeSecond;
            }

            if (issetParam($rVal['CONTENT_ID'])) {
                $tmp['position-recordid'] = $rVal['CONTENT_ID'];
            }

            if (issetParam($rVal['END_TIME'])) {
                $parsed = explode(':', $rVal['END_TIME']);
                $timeSecond = $parsed['0'] * 3600 + $parsed['1'] * 60 + $parsed['2'];    

                $tmp['position-endtime'] = $timeSecond;
            }
            
            array_push($tmparr, $tmp);
            array_push($hideTmparr, $rVal);
        }
    }
}
?>
<div class="wg-form-paper <?php echo $this->uniqId ?> " id="mv-checklist-render<?php echo $this->uniqId ?>">
    <div class="card p-3 h-100 bl-sectioncode1-card">
        <div class="card-header">
            <h6 class="card-title"><?php echo $this->title; ?></h6>
        </div>
        <div class="card-body" data-section-code="1">
            <div class="row">
                <?php 
                if ($headerArray) {
                    for ($i = 1; $i <= sizeOf($headerArray)/2; $i++) {
                        if (issetParam($headerArray['position-'. $i .'-label'])) {
                            ?>
                            <div class="col-md-12" style="-ms-flex: 0 0 450px;flex: 0 0 450px;max-width: 450px;">
                                <div class="form-group row align-items-center">
                                    <label class="col-form-label col-md-3 pr-0 line-height-normal text-left" style=""><?php echo issetParam($headerArray['position-'. $i .'-label']['default_value']) ?> <span class="label-colon">:</span>
                                    </label>
                                    <div class="col-md-9 col-form-control bp-header-param">
                                        <div style="margin-right: auto;">
                                            <span><?php echo is_array(issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']])) ? '' : issetParam($this->rowData[$headerArray['position-'. $i]['src_indicator_path']]); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php echo issetParam($this->uniqCss) ?>
<?php echo issetParam($this->uniqJs) ?>