<?php
if ($this->getCountCardData) {
    $colors = array('#FFCA57', '#FF9F7F', '#A4A9FF', '#F6B14A', '#FF948C', '#FFA8D2', '#78D3F4', '#73E9BE', '#D0A3FF', '#83E286', '#64DCD2', '#FFA486', '#FF8C66', '#FF97B0', '#82BDDB', '#9BA0F4', '#6AE8BA', '#FF93A0', '#8ACEF9', '#F9A771', '#F9948C', '#F58DB0', '#E295EF', '#B49AE4', '#9EA8E3', '#93CBF6', '#85D5F8', '#75E1EC', '#6CD4CC', '#7FE181', '#B7E382', '#FFD762', '#FFC671', '#FFAC94', '#CBA497', '#7DB7D4', '#F099A5', '#F39C8E', '#9BC2EF', '#8ACDD8');
    $selectedIndex = null;
        
    if (strpos($this->selection, 'rowindex:') !== false) {
        $selectedIndex = str_replace('rowindex:', '', trim($this->selection));
        if ($selectedIndex) {
            $selectedIndex = $selectedIndex - 1;
        }
    }

    if (issetParam($this->jsonConfig)) {
        $jsonArr = json_decode(str_replace("&quot;", "\"", $this->jsonConfig), true);
    }
        
    if ($this->theme == 'wfmstatus' || $this->theme == 'card') {
?>
<div class="wfm-status-step">
    <ul>
        <?php
        $total = 0;
        foreach ($this->getCountCardData as $k => $row) {

            $cssClass = 'wfm-status-done';

            $title = $activeTab = '';    

            if (isset($row[$this->fieldPath])) {
                $title = $row[$this->fieldPath];
                if ($this->getTypeCode == 'date') {
                    $title = Date::formatter($title, 'Y-m-d');
                } elseif ($this->getTypeCode == 'datetime') {
                    $title = Date::formatter($title, 'Y-m-d H:i:s');
                }
            }    

            $value = $title;

            if ($title == '') {
                $title = $this->lang->line('META_00098');
            } 
            
            if (!is_null($selectedIndex) && $k == $selectedIndex) {
                $activeTab = 'true';
            }

            if (issetParam($this->theme) === 'card' && issetParam($jsonArr['aggregateField']) && issetParam($jsonArr['aggregateFunction'])) {
                echo '<li>'
                    . '<a href="javascript:;" style="background: '. $colors[$k] .';"
                            class="'.$cssClass.' card-section" onclick="dataViewFilterCardFieldPath_'.$this->metaDataId.'(\''.$this->fieldPath.'\', \''.$value.'\', this);" data-default-active="'.$activeTab.'">'
                        . '<div class="d-flex">'
                            . '<span class="uppercase text-left text-two-line" style="width: 67% !important">'. $title . '</span>'
                            . '<div class="ml-1 pull-right">'
                                . '<span class="text-right w-100 pull-left"> '. Number::formatMoney($row['count']) .'</span>'
                                . ($jsonArr['aggregateLabelName'] ? '<span class="text-right w-100 pull-left" >'. $jsonArr['aggregateLabelName'] .'</span>'  : '')
                            . '</div>' 
                        . '</div>'
                    . '</a>'
                .'</li>';
            } else {
                
                echo '<li>'
                    . '<a href="javascript:;" class="'.$cssClass.'" onclick="dataViewFilterCardFieldPath_'.$this->metaDataId.'(\''.$this->fieldPath.'\', \''.$value.'\', this);" data-default-active="'.$activeTab.'">'
                        . $title.' ('.$row['count'].')' 
                    . '</a>'
                .'</li>';
            }
            
            $total += $row['count'];
        }

        if (issetParam($this->theme) === 'card' && issetParam($jsonArr['aggregateField']) && issetParam($jsonArr['aggregateFunction'])) {
            echo '<li>'
                . '<a href="javascript:;" style="background: '. $colors[$k+1] .';"
                        class="wfm-status-done card-section" onclick="dataViewFilterCardFieldPath_'.$this->metaDataId.'(\'all\', \'all\', this);">'
                    . '<div class="d-flex">'
                        . '<span class="uppercase text-left text-two-line" style="width: 67% !important">'. $this->lang->line('all') . '</span>'
                        . '<div class="ml-1 pull-right">'
                            . '<span class="text-right w-100 pull-left"> '. Number::formatMoney($total).'</span>'
                            . ($jsonArr['aggregateLabelName'] ? '<span class="text-right w-100 pull-left" >'. $jsonArr['aggregateLabelName'] .'</span>'  : '')
                        . '</div>' 
                    . '</div>'
                . '</a>'
            .'</li>';
        } else {
            ?>
            <li><a href="javascript:;" class="wfm-status-done uppercase" onclick="dataViewFilterCardFieldPath_<?php echo $this->metaDataId; ?>('all', 'all', this);"><?php echo $this->lang->line('all') . ' ('.$total.')'; ?></a></li>
        <?php 
        }
        ?>
        
    </ul>
</div>
<style type="text/css">
.wfm-status-step {
    text-align: left;
}
.wfm-status-step ul {
    list-style: none;
    display: inline-table;
    margin-bottom: 0;
    padding: 0;
}
.wfm-status-step ul li {
    display: inline;
}
.wfm-status-step .card-section {
    max-height: max-content;
    box-shadow: 0px 20px 27px 0px #0000000D;
    padding: 20px;
    width: 320px;
    min-height: 75px;
}
.wfm-status-step ul li a {
    display: inline-block;
    height: 26px;
    background-color: #eaedf4;
    text-align: center;
    padding: 2px 12px;
    position: relative;
    margin: 3px 4px 5px 0px;
    font-size: 13px;
    text-decoration: none;
    color: #515f77;
    border-radius: 17px;
    font-weight: 600;
    line-height: 22px;
}
.wfm-status-step ul li:first-child a {
    border-top-left-radius: 17px; 
    border-bottom-left-radius: 17px;
    border-top-right-radius: 17px; 
    border-bottom-right-radius: 17px;
}
.wfm-status-step ul li:first-child a:before {
    display: none; 
}
.wfm-status-step ul li:last-child a:after {
    display: none; 
}
.wfm-status-step ul li a.wfm-status-done:hover {
    background-color: #56bc91;
}
.wfm-status-step ul li a.wfm-status-done {
    color: #fff;
    background-color: #0ca766;
}
.wfm-status-step ul li a.wfm-status-current {
    color: #fff;
    background-color: #0070d2;
}
</style>
<?php
    } else {
?>
<div class="only-horizontal-scroll no-padding m-0">
    <ul class="grid list-view0">
        <?php
        foreach ($this->getCountCardData as $row) {
            
            $title = $activeTab = '';    
            
            if (isset($row[$this->fieldPath])) {
                $title = $row[$this->fieldPath];
                if ($this->getTypeCode == 'date') {
                    $title = Date::formatter($title, 'Y-m-d');
                } elseif ($this->getTypeCode == 'datetime') {
                    $title = Date::formatter($title, 'Y-m-d H:i:s');
                }
            }    
            
            $value = $title;
            
            if ($title == '') {
                $title = $this->lang->line('META_00098');
            } 
            
            if (!is_null($selectedIndex) && $k == $selectedIndex) {
                $activeTab = 'true';
            }
        ?>
        <li class="dir">	
            <figure class="directory">
                <a href="javascript:;" onclick="dataViewFilterCardFieldPath_<?php echo $this->metaDataId; ?>('<?php echo $this->fieldPath; ?>', '<?php echo $value; ?>', this);" class="folder-link" title="<?php echo $title; ?>">
                    <div class="img-precontainer">
                        <div class="img-container directory">
                            <span class="count"><?php echo $row['count']; ?></span>
                            <img class="directory-img" src="assets/core/global/img/meta/folder.png">
                        </div>
                    </div>
                    <div class="box">
                        <h4 class="ellipsis"><?php echo $title; ?></h4>
                    </div>
                </a>	
            </figure>
        </li>
        <?php
        }
        ?>
        <li class="dir">	
            <figure class="directory">
                <a href="javascript:;" onclick="dataViewFilterCardFieldPath_<?php echo $this->metaDataId; ?>('all', 'all', this);" class="folder-link" data-default-active="<?php echo $activeTab; ?>">
                    <div class="img-precontainer">
                        <div class="img-container directory">
                            <img class="directory-img" src="assets/core/global/img/meta/folder.png">
                        </div>
                    </div>
                    <div class="box">
                        <h4 class="ellipsis"><?php echo $this->lang->line('all'); ?></h4>
                    </div>
                </a>	
            </figure>
        </li>
    </ul>
</div>    
<?php
    }
}
?>