<div class="col-md-3">
    <div class="list-group metaProcessTypeList">
        <?php
        if ($this->processType) {
            foreach ($this->processType as $k => $row) {
                ?>
                <a href="javascript:;" class="list-group-item-action" id="<?php echo $row['CONTENT_TYPE_ID']; ?>" data-default="<?php echo (empty($k) ? '1' : '');?>">
                    <?php echo $row['CONTENT_TYPE_NAME']; ?> 
                    <span class="badge badge-warning"><?php echo $row['COUNT_ICON']; ?></span>
                </a>
                <?php
            }
        }
        ?>
    </div>
</div>
<div class="col-md-9">
    <div class="scroller" id="processIconRenderDiv" style="height: 380px;" data-handle-color="#637283" data-always-visible="1" data-rail-visible="1" data-handle-size="8px">

    </div>
</div>

<input type="hidden" name="contentId" id="contentId">
<input type="hidden" name="contentType" id="contentType">
<input type="hidden" name="contentData" id="contentData">
<input type="hidden" name="contentName" id="contentName">
<input type="hidden" name="rowId" id="rowId" value="0">
<script type="text/javascript">
    $(function () {
        $.contextMenu({
            selector: ".view-icon",
            callback: function (key, opt) {
                if (key === 'view-big') {
                    viewBig(this);
                }
            }, items: {
                "view-big": {name: "Томоор харах", icon: "eye"}
            }
        });
        $("a.list-group-item-action.active").trigger("click");
        $(".metaProcessTypeList").on("click", "a.list-group-item-action", function () {
            var _this = $(this);
            var processTypeId = _this.attr("id");
            $(".metaProcessTypeList a.list-group-item-action").removeClass("active");
            _this.addClass("active");
            renderProcessIcon(processTypeId);
        });

        $(".metaProcessTypeList a.list-group-item-action").each(function () {
            var _this = $(this);
            var isDefault = _this.attr("data-default");
            if (isDefault === "1") {
                $(".metaProcessTypeList a.list-group-item-action").removeClass("active");
                _this.addClass("active");
                renderProcessIcon(_this.attr("id"));
                return;
            }
        });
    });

    function renderProcessIcon(processTypeId) {
        $.ajax({
            type: 'post',
            url: 'mdmeta/processIconList',
            data: {processTypeId: processTypeId},
            beforeSend: function () {
                Core.blockUI();
            },
            success: function (data) {
                $("#processIconRenderDiv").empty().html(data);
                Core.unblockUI();
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            Core.initAjax();
        });
    }
    function processIconSelect(contentId, contentType, contentData, contentName) {
        $("#contentId").val(contentId);
        $("#contentType").val(contentType);
        $("#contentData").val(contentData);
        $("#contentName").val(contentName);
    }
    function viewBig(elem) {
        var _this = elem;
        var dataType = _this.attr('data-type');
        var dataName = _this.attr('data-name');
        var dialogName = '#view-big-dialog';
        if (!$(dialogName).length) {
            $('<div id="' + dialogName.replace('#', '') + '"></div>').appendTo('body');
        }
        $(dialogName).html('<img src="assets/core/global/img/process_content/'+dataType+'/'+dataName+'">');
        $(dialogName).dialog({
            cache: false,
            resizable: true,
            bgiframe: true,
            autoOpen: false,
            title: 'Томоор харах',
            width: 'auto',
            height: 'auto',
            modal: true,
            buttons: [
                {text: '<?php echo $this->lang->line('META_00033'); ?>', class: 'btn blue-madison btn-sm', click: function () {
                    $(dialogName).dialog('close');
                }}
            ]
        });
        $(dialogName).dialog('open');
    }
</script>