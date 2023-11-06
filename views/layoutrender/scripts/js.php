<script type="text/javascript">
var activeAjaxRequests = 0;
var $thisLayout = $('#layout-id-'+layoutLinkIdjs);
$thisLayout.attr('data-loaded-layoutdata', 'false');
$.ajaxSetup({
    global: false, 
    async: true, 
    type: 'post', 
    data: {nult: 1, filterJson: '<?php echo $this->filterParams; ?>'},
    beforeSend: function(jqXHR) {
        activeAjaxRequests++;
    },
    complete: function(jqXHR) {
        activeAjaxRequests--;
        setTimeout(function () {
            $thisLayout.attr('data-loaded-layoutdata', !activeAjaxRequests);
        }, 1000);
    }
});
</script>