<script type="text/javascript">
$.ajaxSetup({
    global: false, 
    async: true, 
    type: 'post', 
    data: {nult: 1, filterJson: '<?php echo $this->filterParams; ?>'}
});
</script>