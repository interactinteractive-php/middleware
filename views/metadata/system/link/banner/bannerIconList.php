<?php
if ($this->processIconList) {
?>
<div class="row">
    <?php
        $path = 'photo';
        foreach ($this->processIconList as $icon) {
            
    ?>
    <div class="col-sm-4 col-md-2">
        <a 
            href="javascript:;" 
            class="thumbnail view-icon" 
            onclick="processIconSelect('<?php echo $icon['CONTENT_ID']; ?>', '<?php echo $icon['CONTENT_TYPE']; ?>', '<?php echo $icon['CONTENT_DATA']; ?>', '<?php echo $icon['CONTENT_NAME']; ?>');" 
            data-type="<?php echo $icon['CONTENT_TYPE']; ?>"
            data-name="<?php echo $icon['CONTENT_DATA']; ?>"
            style="height: 65px; width: 65px; overflow: hidden;">
            <img src="assets/core/global/img/process_content/<?php echo $icon['CONTENT_TYPE']; ?>/<?php echo $icon['CONTENT_DATA']; ?>" style="height: 65px; width: 65px; display: block;">
        </a>
    </div>
    <?php
        }
    ?>
</div>
<?php
} else {
    echo "No icon!";
}
?>