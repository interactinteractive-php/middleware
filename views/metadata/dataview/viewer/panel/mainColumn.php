<ul class="nav nav-sidebar<?php echo (issetParam($this->isPanelDvChangeTreeIcon) == 1) ? ' panel-dv-tree-icon' : ''; ?>" data-nav-type="accordion" data-part="dv-twocol-first-list">
    <?php
    if ($this->mainColumnData) {
        
        if (!isset($this->mainColumnData['status'])) {
                
            foreach ($this->mainColumnData as $row) {
                
                $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');

                $subMenu = $subMenuOpen = $icon = $menuSelected = '';

                if ($iconName = issetParam($row['icon'])) {
                    $icon = '<i class="'.$iconName.' font-weight-bold" style="color: '.issetParam($row['color']).';"></i> ';
                }

                if (issetParam($row['childrecordcount'])) {
                    $subMenu = ' nav-item-submenu';
                }
                
                if (issetParam($row['childs'])) {
                    $subMenuOpen = ' nav-item-open';
                    $menuSelected = ' dv-twocol-f-selected';
                }
                
                if (issetParam($row['_clickrow']) == '1') {
                    $subMenuOpen .= ' nav-item-menu-click';
                }
    ?>
    <li class="nav-item<?php echo $subMenu . $subMenuOpen; ?>" style="<?php echo issetParam($row['style']) ?>">
        <a href="javascript:void(0);" class="nav-link font-weight-bold<?php echo $menuSelected; ?>" data-id="<?php echo $row[$this->idField]; ?>" data-listmetadataid="<?php echo $row['metadataid']; ?>" data-listmetadatacriteria="<?php echo issetParam($row['listmetadatacriteria']); ?>" data-metatypeid="<?php echo issetParam($row['metatypeid']); ?>" data-rowdata="<?php echo $rowJson; ?>">
            <?php echo $icon; ?>
            <span><?php echo $row[$this->nameField]; ?></span>
        </a>
        
        <?php echo Mdobject::dvPanelMainMenuRender($row, $this->idField, $this->nameField); ?>
    </li>
    <?php
            }
        } else {
            echo html_tag('div', array('class' => 'alert alert-info'), $this->mainColumnData['message']);
        }
    }
    ?>
</ul>