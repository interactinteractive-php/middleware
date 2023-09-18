<?php
if ($this->getCountCardData) {
    
    $selectedIndex = null;
        
    if (strpos($this->selection, 'rowindex:') !== false) {
        $selectedIndex = str_replace('rowindex:', '', trim($this->selection));
        if ($selectedIndex) {
            $selectedIndex = $selectedIndex - 1;
        }
    }
        
    if ($this->theme == 'wfmstatus') {
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

            echo '<li><a href="javascript:;" class="'.$cssClass.'" onclick="dataViewFilterCardFieldPath_'.$this->metaDataId.'(\''.$this->fieldPath.'\', \''.$value.'\', this);" data-default-active="'.$activeTab.'">'.$title.' ('.$row['count'].')</a></li>';
            
            $total += $row['count'];
        }
        ?>
        <li><a href="javascript:;" class="wfm-status-done uppercase" onclick="dataViewFilterCardFieldPath_<?php echo $this->metaDataId; ?>('all', 'all', this);"><?php echo $this->lang->line('all') . ' ('.$total.')'; ?></a></li>
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