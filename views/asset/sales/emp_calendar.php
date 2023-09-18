<div class="conf-room conf-room-<?php echo $this->uniqId; ?>">
    <div class="row p-3">
        <div class="col-2">
            <div class="mb-4">
                <h6 class="font-weight-bold">
                    Шүүлт
                    <button type="button" id="reset_search" class="btn btn-success " style="border: 1px solid #45b549;height: 24px;margin-bottom: 10px;float: right;border-radius: 3px;padding: 1px 19px;color: #000;background: none;"> Бүгд (<a id="countt">0</a>)</button>
                </h6>
                <div class="form-group">
                    <div class="input-group">
                        <div class="dateElement input-group d-flex" style="max-width: 100% !important;">
                            <span class="input-group-btn input-group-append input-group-prepend w-100">
                                <input id="startDateFilter" type="text" placeholder="Эхлэх огноо" class="dateInit calendar" value="<?php echo Date::beforeDate('Y-m-d', '-1 day'); ?>">
                                <span class="input-group-text" title="Эхлэх огноо сонгох" onclick="return false;">
                                    <i class="icon-calendar22"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="dateElement input-group d-flex" style="max-width: 100% !important;">
                            <span class="input-group-btn input-group-append w-100">
                                <input id="endDateFilter" type="text" placeholder="Дуусах огноо" class="dateInit calendar" value="<?php echo Date::currentDate('Y-m-d'); ?>">
                                <span class="input-group-text" title="Дуусах огноо сонгох" onclick="return false;">
                                    <i class="icon-calendar22"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                    <hr>
                <div class="form-group">
                    <?php if (isset($this->searchRoom) && $this->searchRoom) {
                        foreach ($this->searchRoom as $room) {
                        ?>
                        <div class="w-100">
                            <input type="radio" name="filterRoom" class="filterRoom" id="filterRoom<?php echo $room['id'] ?>" value="<?php echo $room['id'] ?>" /><label for="filterRoom<?php echo $room['id'] ?>" class="ml-1"> <?php echo $room['name']; ?></label>
                        </div>
                    <?php } 
                    } ?>
                </div>
                <!-- <hr>
                <div class="form-group">
                    <?php 
                    // if(isset($this->wfmStatusList) && $this->wfmStatusList ) { 
                            // foreach($this->wfmStatusList as $status) {
                        ?> 
                        <div class="w-100">
                            <input type="checkbox" name="wfmStatus[]" class="wfmStatus" id="wfmStatus<?php echo $status['id'] ?>" value="<?php echo $status['id'] ?>" /><label for="wfmStatus<?php echo $status['id'] ?>" class="ml-1"> <?php echo $status['wfmstatusname']; ?></label>
                        </div>
                        <?php //}
                    //} 
                    ?>
                </div> -->
                <hr>
                <button type="button" id="reset_search" title="Шүүлт цэвэрлэх" class="btn search-btn" style="background-color: #555; color: #FFF"><i class="icon-reload-alt"></i> Цэвэрлэх</button>
                <button type="button" class="btn btn-primary search-btn" onclick="reload('filter')"><i class="icon-search4 mr-1"></i> Шүүх</button>
            </div>
            <div class="d-none">
                <h6 class="font-weight-bold">Танхимууд</h6>
                <?php foreach ($this->searchRoom as $room) { ?>
                    <div class="inf-box" title="<?php echo $room['name'] ?>">
                        <span><i class="icon-circle2 mr-1" style="color:<?php echo $room['description'] ?>;"></i></span>
                        <span><?php echo $room['name'] ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-10">
            <div class="fullcalendar-external" id="calendar-<?php echo $this->uniqId; ?>"></div>
        </div>
    </div>
</div>

<style type="text/css">
    #calendar-<?php echo $this->uniqId; ?> .status-btn {
        padding: 0 !important;
    }
    
    .conf-room-<?php echo $this->uniqId; ?> .btn-success:hover {
        background: #b6deb7 !important;
        color: #FFF !important;
    }
    
    .ui-widget[aria-describedby="dialog-dataview-selectable-1565070936581248"] 
    {
        z-index: 1052 !important; 
    }
    .ui-widget-overlay[aria-describedby="dialog-dataview-selectable-1565070936581248"]
    {
        z-index: 1051 !important; 
    }
    #calendar-<?php echo $this->uniqId; ?> .spinner {
        width: initial !important;
        height: initial !important;
    }
    .conf-room-<?php echo $this->uniqId; ?> .fc-time-grid .fc-slats td{
        height: 3.1em;
    }
    .conf-room-<?php echo $this->uniqId; ?> {
        background: #FFF;
    }
    .conf-room-<?php echo $this->uniqId; ?> .dropdown-item.form-check .form-check-label {
        padding-left: 1rem;
    }
    .conf-room-<?php echo $this->uniqId; ?> .btn-group {
        /* width: 235px !important; */
    }
    .conf-room-<?php echo $this->uniqId; ?> .multiselect.btn-light,
    .conf-room-<?php echo $this->uniqId; ?> .input-group-text {
        /* border: 3px solid #FFD071; */
        border-radius: 0;
    }
    .conf-room-<?php echo $this->uniqId; ?> .multiselect.btn-light {
        border-left-width: 1px;
    }
    .conf-room-<?php echo $this->uniqId; ?> .input-group-text {
        /* border-right: 0; */
    }
    .conf-room-<?php echo $this->uniqId; ?> .btn-light:focus,
    .conf-room-<?php echo $this->uniqId; ?> .btn-light:hover,
    .conf-room-<?php echo $this->uniqId; ?> .btn-light:not([disabled]):not(.disabled).active,
    .conf-room-<?php echo $this->uniqId; ?> .btn-light:not([disabled]):not(.disabled):active,
    .conf-room-<?php echo $this->uniqId; ?> .show > .btn-light.dropdown-toggle {
        border-color: #FFD071;
    }
    .conf-room-<?php echo $this->uniqId; ?> .search-btn {
        height: 41px;
        /* border: 3px solid #FFD071; */
        border-left:0;
        /* border-radius: 0 3px 3px 0; */
    }
    .conf-room-<?php echo $this->uniqId; ?> input.calendar {
        height: 41px;
        /* border: 3px solid #FFD071; */
        border-right: 0;
        /*border-left-width: 1px;*/
        outline: 0;
        padding: 0px 10px;
    }
    .conf-room-<?php echo $this->uniqId; ?> .fc-center {
        margin-left: 260px !important;
    }
    .conf-room-<?php echo $this->uniqId; ?> .inf-box {
        margin-bottom: 8px;
    }
    .conf-room-<?php echo $this->uniqId; ?> .form-group {
        margin-bottom: 10px;
    }
    .fc-basic-view .fc-body .fc-row {
        /* height: 280px !important; */
    }
    .fc-row fc-week .fc-widget-content {
        /* height: 300px !important; */
    }
    .prtype {
        font-size: 11px !important;
        border: 1px solid #ab9d9d;
        padding: 4px 7px;
        margin-right: 3px;
        margin-top: 4px;
        margin-bottom: 4px;
        line-height: 26px;
    }
    .org-choice-d {
        word-break: break-word;
        margin: 0;
        padding: 5px;
        background: none;
        border: none;
    }
    .custom_border_bottom td
    {
        text-transform: initial;
    }
    .fc-more-popover {
        width: 260px;
    }
    .fc-more-popover .fc-event-container {
        max-height: 280px;
        overflow: auto;
    }    
</style>

<div id="viewinformation<?php echo $this->uniqId; ?>" class="modal fade" style="margin-top:200px;" tabindex="-1">
    <div class="modal-dialog modal-small">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-uppercase">Дэлгэрэнгүй харах</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Хаах</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var doubleClick_<?php echo $this->uniqId; ?> = false;
    
    today = yyyy + '-' + mm + '-' + dd;
    
    $(document).ready(function () {
        $.getScript(URL_APP + 'assets/custom/gov/multiselect.js').done(function() {
            BootstrapMultiselect.init();
        })
        Core.init();
        reload('');
    });
    
    $("body").on('click', '#reset_search', function () {
        $("#startDateFilter").val('');
        $("#endDateFilter").val('');
        
        $('.filterRoom').each(function (index, row) {$(row).removeAttr('checked'); $(row).parent().removeClass('checked');});
        $('.wfmStatus').each(function (index, row) {$(row).removeAttr('checked'); $(row).parent().removeClass('checked');});
        
        reload('filter');
    });

    function changeDateTimeFormat<?php echo $this->uniqId; ?>(date) {
        var yyyy = date.getFullYear();
        var MM = date.getMonth() + 1;
        var dd = date.getDate();

        if (MM < 10) {
            MM = '0' + MM
        }
        if (dd < 10) {
            dd = '0' + dd
        }
        /*date = MM + "/" + dd + "/" + yyyy;        */
        return yyyy + "-" + MM + "-" + dd;
    }
    
    function getmoreConference_<?php echo $this->uniqId ?>(id) {
        $.ajax({
            type: 'post',
            url: 'mdasset/getSalesDetail',
            data: {id: id},
            dataType: 'json',
            success: function ($response) {
                var data = $response.data;
                var $data = $response.cdata;
                var tastypes = '';
                
                if (typeof $data.oms_meta_dm_record_map !== 'undefined') {
                    $.each($data.oms_meta_dm_record_map, function(index, row) {
                        tastypes += row.taskname;
                        if (($data.oms_meta_dm_record_map).length > (index+1)) {
                            tastypes += ', ';
                        }
                    });
                }
                
                var $mhtml = '<tr class="custom_border_bottom">'
                        $mhtml += '</td>';
                    '</tr>';
                            
                
                
                    var dataHtml = '<div class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable " style="background-color: '+data.color+';border-color:'+data.color+';">' +
                                        '<div style="width: 100%;">' +
                                            '<table id="tableAgree" class="table text-white font-size-20" style="color: #607D8B !important;font-family: Roboto Condensed;text-transform: uppercase;border: none;">' +
                                                '<tbody>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" width="35%" style="color: '+data.textcolor+';">Нэр :</td> ' +
                                                        '<td class="" width="65%" style="color:'+data.textcolor+';">'+ (data.name ? data.name : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Төлөв :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ (data.wfmstatusname ? data.wfmstatusname : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Танхим :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ (data.confname ? data.confname : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Огноо :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ (data.startdate ? data.startdate : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Цаг :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ (data.conftime ? data.conftime : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Захиалсан ажилтан :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ (data.createdusername ? data.createdusername : '') +'</td>' +
                                                    '</tr>' +
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">'+ plang.get('TASK_TYPE_IDS') +' :</td>' +
                                                        '<td class="" style="color:'+data.textcolor+';">'+ tastypes +'</td>' +
                                                    '</tr>' + 
                                                    '<tr class="custom_border_bottom">' +
                                                        '<td class="text-right font-weight-bold" style="color: '+data.textcolor+';">Тайлбар :</td>' +
                                                        '<td class=""><p style="text-transform:none; text-align: justify; color:'+data.textcolor+'; margin: 0">'+ (data.desc ? data.desc : '') +'</p></td>' +
                                                    '</tr>' + $mhtml + 
                                                '</tbody>' +
                                            '</table>' +
                                        '</div>' +
                                    '</div>';
        
                var $dialogName = 'dialog-oms-more';
                $('<div class="modal pl0 fade modal-after-save-close" id="'+ $dialogName +'" tabindex="-1" role="dialog" aria-hidden="true">'+
                        '<div class="modal-dialog" style="width: 500px !important; margin-top: 10px;">'+
                            '<div class="modal-content modalcontent'+ $response.uniqId +'">'+
                                '<div class="modal-header"></div>'+
                                '<div class="modal-body">'+
                                    dataHtml +
                                '</div>'+
                                '<div class="modal-footer">'+
                                    '<button type="button" data-dismiss="modal" class="btn btn-primary close_modalbtn" dialog-name="'+ $dialogName +'">' + $response.close_btn + '</button>'+
                                '</div>' +
                            '</div>'+
                        '</div>'+
                    '</div>').appendTo('body');

                var $dialog   = $('#' + $dialogName);
                
                $dialog.modal({
                    show: false,
                    keyboard: false,
                    backdrop: 'static'
                });

                $dialog.on('shown.bs.modal', function () {
                    setTimeout(function() {
                        Core.unblockUI();
                    }, 10);
                });   

                $dialog.draggable({
                    handle: ".modal-header"
                });

                $dialog.modal('show');
                
            }
        });
    }
    
   
    function  deleteEvent<?php echo $this->uniqId ?> (id) {

        var $dialogName = 'dialog-member_' +id;
        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
        }
        var $dialog = $('#' + $dialogName);
        var fillDataParams = 'id='+id+'&defaultGetPf=1';

        $.ajax({
            type: 'post',
            url: 'mdwebservice/callMethodByMeta',
            data: {
                metaDataId: '1581386692497', 
                isDialog: true, 
                isSystemMeta: false, 
                fillDataParams: fillDataParams
            },
            dataType: 'json',
            beforeSend: function () {
                Core.blockUI({
                    message: 'Loading...', 
                    boxed: true
                });
            },
            success: function (data) {

                $dialog.empty().append(data.Html);

                var $processForm = $('#wsForm', '#' + $dialogName), 
                    processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                var buttons = [
                    {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                        if (window['processBeforeSave_'+processUniqId]($(e.target))) {     

                            $processForm.validate({ 
                                ignore: '', 
                                highlight: function(element) {
                                    $(element).addClass('error');
                                    $(element).parent().addClass('error');
                                    if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                        $processForm.find("div.tab-pane:hidden:has(.error)").each(function(index, tab){
                                            var tabId = $(tab).attr('id');
                                            $processForm.find('a[href="#'+tabId+'"]').tab('show');
                                        });
                                    }
                                },
                                unhighlight: function(element) {
                                    $(element).removeClass('error');
                                    $(element).parent().removeClass('error');
                                },
                                errorPlacement: function(){} 
                            });

                            var isValidPattern = initBusinessProcessMaskEvent($processForm);

                            if ($processForm.valid() && isValidPattern.length === 0) {
                                $processForm.ajaxSubmit({
                                    type: 'post',
                                    url: 'mdwebservice/runProcess',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            boxed: true, 
                                            message: 'Түр хүлээнэ үү'
                                        });
                                    },
                                    success: function (responseData) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: responseData.status,
                                            text: responseData.message,
                                            type: responseData.status, 
                                            sticker: false
                                        });

                                        if (responseData.status === 'success') {
                                            $dialog.dialog('close');
                                        } 
                                        Core.unblockUI();
                                    },
                                    error: function () {
                                        alert("Error");
                                    }
                                });
                            }
                        }    
                    }},
                    {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                        $dialog.dialog('close');

                    }}
                ];
                var dialogWidth = data.dialogWidth, dialogHeight = data.dialogHeight;

                if (data.isDialogSize === 'auto') {
                    dialogWidth = 1200;
                    dialogHeight = 'auto';
                }          

                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.Title,
                    width: dialogWidth,
                    height: dialogHeight,
                    modal: true,
                    closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape), 
                    close: function () {
                        reload('refetchEvents');
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: buttons
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                if (data.dialogSize === 'fullscreen') {
                    $dialog.dialogExtend("maximize");
                }

                setTimeout(function(){
                    $dialog.dialog('open');
                    Core.unblockUI();
                }, 1);
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            reload('refetchEvents');
            Core.initBPAjax($dialog);

        });
    }

    function reload(type) {
        if (typeof type !== 'undefined' && type == 'refetchEvents') {
            $('#calendar-<?php echo $this->uniqId; ?>').fullCalendar('refetchEvents');
            return;
        }
        
        if (type === 'filter') {
            $('#calendar-<?php echo $this->uniqId; ?>').fullCalendar('refetchEvents');
            
            return;
        }
        
        if (!$().fullCalendar || typeof Switchery == 'undefined' || !$().draggable) {
            console.warn('Warning - fullcalendar.min.js, switchery.min.js or jQuery UI is not loaded.');
            return;
        }

        blockContent_<?php echo $this->uniqId ?>('#calendar-<?php echo $this->uniqId; ?>');

        $('#calendar-<?php echo $this->uniqId; ?>').fullCalendar({
            buttonText: {
                prev: '<',
                next: '>'
            },            
            customButtons: {
                add_event: {
                    text: 'Нэмэх',
                    click: function () {
                        callSalesAddForm('');
                    }
                },
                edit_event: {
                    text: 'Засах',
                    click: function () {
                        callSalesAddForm('');
                    }
                }
            },
            header: {
                left: 'add_event',
                center: 'prev title next',
                right: 'month,agendaWeek,agendaDay,today'
            },
            header: {
                center: 'prev title next',
                right: 'listViewBtn,month,basicWeek,basicDay'
            },            
            height: 'auto',
            contentHeight: 'auto',
            defaultDate: moment('<?php echo Date::currentDate('Y-m-d'); ?>'),
            defaultView: 'month',
            timezone: 'Asia/Ulaanbaatar', 
            fixedWeekCount: false,
            weekends: false,
            displayEventTime: false,
            nextDayThreshold: '00:00:00',
            eventLimit: true, // for all non-TimeGrid view
            eventLimitText: plang.get('more'),
            selectable: true,
            views: {
                basicWeek: {eventLimit: 6}, 
                basicDay: {eventLimit: 6},        
                month: {
                    titleFormat: 'YYYY он MMMM',
                    eventLimit: 6
                }
            },
            events: function (start, end, timezone, callback) {
                var $events = <?php echo json_encode($this->data) ?>;
                var filterStartDate = '';
                var filterEndDate = '';
                var defaultCriteriaData = '';
                if (type === 'no=') {
                    filterStartDate = changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(start.unix() * 1000));
                    filterEndDate = changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(end.unix() * 1000));
                    defaultCriteriaData = 'param%5BfilterStartDate%5D=' + filterStartDate + '&criteriaCondition%5BfilterStartDate%5D=%3D&param%5BfilterEndDate%5D=' + filterEndDate + '&criteriaCondition%5BfilterEndDate%5D=%3D&criteriaTemplates=&criteriaTemplateName=&criteriaTemplateDescription=&inputMetaDataId=<?php echo $this->metaDataId ?>';
                } else {
                    filterStartDate = ($("#startDateFilter").val()) ? $("#startDateFilter").val() : changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(start.unix() * 1000));
                    filterEndDate = ($("#endDateFilter").val()) ? $("#endDateFilter").val() : changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(end.unix() * 1000));
                    defaultCriteriaData = 'param%5BfilterStartDate%5D=' + filterStartDate + '&criteriaCondition%5BfilterStartDate%5D=%3D&param%5BfilterEndDate%5D=' + filterEndDate + '&criteriaCondition%5BfilterEndDate%5D=%3D&criteriaTemplates=&criteriaTemplateName=&criteriaTemplateDescription=&inputMetaDataId=<?php echo $this->metaDataId ?>';
                    
                    //filtertemplateid search
                    var ticket = false;
                    var rooms = $('.filterRoom:checked').val();
                    
                    if (typeof rooms == 'string') {
                        ticket = true;
                        defaultCriteriaData += '&param%5Bfiltertemplateid%5D%5B%5D=' + rooms;
                    } else {
                        $(rooms).each(function (index, el) {
                            var roomId = el;
                            if (roomId) {
                                ticket = true;
                                defaultCriteriaData += '&param%5Bfiltertemplateid%5D%5B%5D=' + roomId;
                            }
                        });
                    }
                    
                    defaultCriteriaData += (ticket) ? '&criteriaCondition%5Bfiltertemplateid%5D=%3D' : '';
                    //filtertemplateid search
                    
                    //wfmstatusid search
                    var ticket1 = false;
                    $(".wfmStatus:checked").each(function (index, el) {
                        var statusId = $(el).val();
                        if (typeof statusId !== 'undefined' && statusId) {
                            ticket1 = true;
                            defaultCriteriaData += '&param%5Bfilterwfmstatusid%5D%5B%5D=' + statusId;
                        }
                    });
                    
                    defaultCriteriaData += (ticket1) ? '&criteriaCondition%5Bfilterwfmstatusid%5D=%3D' : '';
                    //wfmstatusid search
                }
                
                if (typeof param === "undefined") {
                    param = {};
                }

                var data = $.extend(param, {
                    metaDataId: '<?php echo $this->metaDataId ?>',
                    filterStartDate: filterStartDate,
                    filterEndDate: filterEndDate,
                    defaultCriteriaData: defaultCriteriaData
                });

                $.ajax({
                    url: 'mdobject/dataViewDataGrid/false',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function (data) {
                        $('#countt').html('0');
                        if (typeof data.rows !== "undefined") {
                            callback(data.rows);
                            $('#countt').html(data.total);
                        }
                        Core.unblockUI('#calendar-<?php echo $this->uniqId; ?>');
                    }
                });
            },
            eventAfterRender: function (event, element, view) {
                var eventHtml = '';
                var $style = $(element).attr('style');
                var $class = $(element).attr('class');
                var editButton = '';
                var deleteButton = '';
                var approveButton = '';
                var cancelButton = '';
                var eventj = event;
                var emptyTime = '';
                var emptyTimeWeek = '';
                
                $.each(eventj, function (index, row) {
                    if (typeof row !== 'string') {
                        delete eventj[index];
                    }
                   // console.log(row);
                });
                
                var ticketBtn<?php echo $this->uniqId; ?> = true;
                /*
                if ($('.conf-room-<?php echo $this->uniqId; ?>').find('.filterRoom:checked').val()) {
                    ticketBtn<?php echo $this->uniqId; ?> = true;
                }
                */
                if ((event.wfmstatusid === '1574913193491871' || event.wfmstatusid === '1574913204578107' || event.showeditbutton === '0') || !ticketBtn<?php echo $this->uniqId; ?>) {
                    editButton = '';
                } 
                else {
                    editButton = '<a href="javascript:;" title="Засах" class="btn w-100" onclick="edit<?php echo $this->uniqId ?>(' + event.id + ')">Засах</a>';
                    deleteButton = '<a href="javascript:;" title="Устгах" class="btn ml-1 w-100" onclick="deleteEvent<?php echo $this->uniqId ?>('+event.id+')">Устгах</a>';
                }
                
                if (event.showapprovebutton === '1' && ticketBtn<?php echo $this->uniqId; ?>) {
                    approveButton = '<a href="javascript:;" data-rowdata="'+ htmlentities(JSON.stringify(eventj), 'ENT_QUOTES', 'UTF-8') +'"  title="Зөвшөөрөх" class="btn mr1 w-100" onclick="transferProcessAction(\'\', \'1578475245215\', \'1579523005203\', \'200101010000011\', \'toolbar\', this, {callerType: \'OMS_MEETING_GENERAL_LIST\'}, undefined, undefined, '+ htmlentities(JSON.stringify(eventj), 'ENT_QUOTES', 'UTF-8') +', undefined, \'\');" data-actiontype="update" data-dvbtn-processcode="OMS_CONFERENCE_DV_008" data-ismain="0">Зөвшөөрөх</a>';
                } 
                else {
                    approveButton = '';
                }
                
                if (event.showcancelbutton === '1' && ticketBtn<?php echo $this->uniqId; ?>) {
                    cancelButton = '<a href="javascript:;" data-rowdata="'+ htmlentities(JSON.stringify(eventj), 'ENT_QUOTES', 'UTF-8') +'" title="Татгалзах" class="btn w-100" style="padding-right: 5px;" onclick="transferProcessAction(\'\', \'1578475245215\', \'1578976016438\', \'200101010000011\', \'toolbar\', this, {callerType: \'OMS_MEETING_GENERAL_LIST\'}, undefined, undefined, '+ htmlentities(JSON.stringify(eventj), 'ENT_QUOTES', 'UTF-8') +', undefined, \'\');" data-actiontype="update" data-dvbtn-processcode="OMS_CONFERENCE_APPROVE_DV_008" data-ismain="0">Татгалзах</a>';
                } 
                else {
                    cancelButton = '';
                }

                if (typeof event.id === 'undefined') {
                    emptyTime = 'onclick="callSalesAddForm(\'\',\''+event.startdate+'\',undefined,undefined,undefined,undefined,'+event.templateid+',\''+event.stime+'\',\''+event.etime+'\')"';             
                } 
                else {
                    emptyTime = 'id="id-' + event.id + '" onclick="getmoreConference_<?php echo $this->uniqId ?>(' + event.id + ');" ';
                }
                
                if (typeof event.id === 'undefined') {
                    emptyTimeWeek = 'callSalesAddForm(\'\',\''+event.startdate+'\',undefined,undefined,undefined,undefined,'+event.templateid+',\''+event.stime+'\',\''+event.etime+'\')';
                } 
                else {
                    emptyTimeWeek = 'getmoreConference_<?php echo $this->uniqId ?>(' + event.id + ');';
                }
                
                eventHtml = '<div '+emptyTime+' style="background-color:' + event.statuscolor  + ';border-color:' + event.statuscolor + ';">'
                                + '<a href="javascript:void(0);">'
                                + '<div class="fc-content d-flex flex-row align-items-center">'
                                + '<div class="mr-2">'
                                + '<button type="button" title="' + event.tooltiptext + '" class="btn btn-light btn-icon rounded-round status-btn" style="background-color: '+event.color+';">'
                                + '<i class="' + event.icon + '"></i>'
                                + '</button>'
                                + '</div>'
                                + '<div>'
                                + '<div class="fc-title " style="color:'+event.textcolor+'">' + event.confname + '</div>'
                                + '<div class="fc-title" style="color:'+event.textcolor+'">' + event.starttime + '</div>'
                                + '</a>'
                                + '</div>';

                $(element).empty().append(eventHtml).promise().done(function () {
                    $(this).closest('a').attr('style', 'background-color:' + event.statuscolor  + ' !important;border-color:' + event.statuscolor + ' !important;');
                });
                
                $(element).parent().addClass('fc-hrm-time-duration').attr('data-date', event.startdate);
                $(element).attr('data-eventid', event.id);
                
                if (editButton + deleteButton + approveButton + cancelButton) {
                    $(element).parent().find('[data-eventid="'+ event.id +'"]').each(function (index, row) {
                        $(row).qtip({
                            content: {
                                text: function(event1, api1) {
                                    var $html = '<table id="tableAgree" class="table text-white font-size-20" style="">'
                                                    + '<tbody>'
                                                        + '<tr class="custom_border_bottom">'
                                                            + '<td class="" style="color:#000;">'
                                                                + event.confname
                                                            + '</td>'
                                                        + '</tr>'
                                                        + '<tr class="custom_border_bottom">'
                                                            + '<td class="" style="color:#000;">'+ event.startdate +  ' ' + event.starttime +'</td>'
                                                        + '</tr>'
                                                    + '</tbody>'
                                                + '</table>';
                                        $html += '<hr style="margin-top: 5px; margin-bottom: 5px;"><div class="row">'
                                                    + (editButton ? '<div class="col pl0 pr0">' + editButton + '</div>' : '') 
                                                    + (deleteButton ? '<div class="col pl0 pr0">' + deleteButton + '</div>' : '') 
                                                    + (approveButton ? '<div class="col pl0 pr0">' + approveButton + '</div>' : '') 
                                                    + (cancelButton ? '<div class="col pl0 pr0">' + cancelButton + '</div>' : '')
                                                + '</div>';
                                    return $html; // Set some initial text
                                }
                            },
                            position:{
                                effect:!1,at:"center left",
                                my: "right center",
                            },
                            show:{effect:!1,delay:500},
                            hide:{effect:!1,fixed:!0,delay:70},
                            style:{classes:"qtip-bootstrap",width:250,tip:{width:12,height:7}}
                        });
                    });
                }

            },
            dayClick: function (date) {
            
                if (!doubleClick_<?php echo $this->uniqId; ?>) {

                    doubleClick_<?php echo $this->uniqId; ?> = true;
                    setTimeout(function() { doubleClick_<?php echo $this->uniqId; ?> = false; }, 250);

                } else {
                    
                    var $dateStr = changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(date.unix() * 1000));

                    if ($dateStr < today) {
                        PNotify.removeAll();
                        new PNotify({
                            title: '<?php echo Lang::line('warning') ?>',
                            text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST2') ?>',
                            type: 'warning',
                            sticker: false
                        });
                        return;
                    } 
                    callSalesAddForm('', $dateStr);
                    
                }
            },
            eventDrop: function (info, dd, dd1, dd3) {
                if (!confirm("Та өөрчлөхдөө итгэлтэй байна уу?")) {
                    var filterStartDate = changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(info.start.unix() * 1000));
                    var filterEndDate = changeDateTimeFormat<?php echo $this->uniqId; ?>(new Date(info.end.unix() * 1000));
                    $('#calendar-<?php echo $this->uniqId; ?>').fullCalendar('refetchEvents');
                }
            },
            windowResize: function(view) {
                //alert('The calendar has adjusted to a window resize');
            }
        });

        $('#external-events .fc-event').each(function () {
            $(this).css({'backgroundColor': $(this).data('statuscolor'), 'borderColor': $(this).data('statuscolor')});
            $(this).data('event', {
                title: $.trim($(this).html()),
                color: $(this).data('statuscolor'),
                stick: true
            });
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0
            });
        });
        
    }

    function blockContent_<?php echo $this->uniqId ?>(mainSelector) {
        $(mainSelector).block({
            message: '<i class="icon-spinner4 spinner"></i>',
            centerX: 0,
            centerY: 0,
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                width: 16,
                top: '15px',
                left: '',
                right: '15px',
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
        });
    }

    function edit<?php echo $this->uniqId ?>(id) {
        var $dialogName = 'dialog-member_' +id;
        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
        }
        var $dialog = $('#' + $dialogName);
        var fillDataParams = 'id='+id+'&defaultGetPf=1';

        $.ajax({
            type: 'post',
            url: 'mdwebservice/callMethodByMeta',
            data: {
                metaDataId: '1581386692394', 
                isDialog: true, 
                isSystemMeta: false, 
                fillDataParams: fillDataParams
            },
            dataType: 'json',
            beforeSend: function () {
                Core.blockUI({
                    message: 'Loading...', 
                    boxed: true
                });
            },
            success: function (data) {

                $dialog.empty().append(data.Html);

                var $processForm = $('#wsForm', '#' + $dialogName), 
                    processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                var buttons = [
                    {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                        if (window['processBeforeSave_'+processUniqId]($(e.target))) {     

                            $processForm.validate({ 
                                ignore: '', 
                                highlight: function(element) {
                                    $(element).addClass('error');
                                    $(element).parent().addClass('error');
                                    if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                        $processForm.find("div.tab-pane:hidden:has(.error)").each(function(index, tab){
                                            var tabId = $(tab).attr('id');
                                            $processForm.find('a[href="#'+tabId+'"]').tab('show');
                                        });
                                    }
                                },
                                unhighlight: function(element) {
                                    $(element).removeClass('error');
                                    $(element).parent().removeClass('error');
                                },
                                errorPlacement: function(){} 
                            });

                            var isValidPattern = initBusinessProcessMaskEvent($processForm);

                            if ($processForm.valid() && isValidPattern.length === 0) {
                                $processForm.ajaxSubmit({
                                    type: 'post',
                                    url: 'mdwebservice/runProcess',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            boxed: true, 
                                            message: 'Түр хүлээнэ үү'
                                        });
                                    },
                                    success: function (responseData) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: responseData.status,
                                            text: responseData.message,
                                            type: responseData.status, 
                                            sticker: false
                                        });

                                        if (responseData.status === 'success') {
                                            $dialog.dialog('close');
                                        } 
                                        Core.unblockUI();
                                    },
                                    error: function () {
                                        alert("Error");
                                    }
                                });
                            }
                        }    
                    }},
                    {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                        $dialog.dialog('close');
                        reload('refetchEvents');
                    }}
                ];
                var dialogWidth = data.dialogWidth, dialogHeight = data.dialogHeight;

                if (data.isDialogSize === 'auto') {
                    dialogWidth = 1200;
                    dialogHeight = 'auto';
                }          

                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.Title,
                    width: dialogWidth,
                    height: dialogHeight,
                    modal: true,
                    closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape), 
                    close: function () {
                        reload('refetchEvents');
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: buttons
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                if (data.dialogSize === 'fullscreen') {
                    $dialog.dialogExtend("maximize");
                }

                setTimeout(function(){
                    $dialog.dialog('open');
                    Core.unblockUI();
                }, 1);
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            reload('refetchEvents');
            Core.initBPAjax($dialog);

        });
    }
    
    $('body').on('click', '.close_modalbtn', function () {
        var $dialog = $('#' + $(this).attr('dialog-name'));
        $dialog.modal('hide');
        $('.modal-backdrop').remove();
        $dialog.remove();
        $('body').removeClass('modal-open');
    });
    
    $(document).ready( function( ) {
        
    } );

    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-day-grid-container .fc-day, #calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-day-grid-container .fc-day-top",
        build: function ($trigger, e) {
            var $this = $(e.currentTarget);
            var dateStr = $this.data('date');
            if(today <= dateStr) {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    }
                };
            } else {
                PNotify.removeAll();
                new PNotify({
                    title: '<?php echo Lang::line('warning') ?>',
                    text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                    type: 'warning',
                    sticker: false
                });
            }

            var options = {
                callback: function (key, opt) {
                },
                items: contextMenuData
            };

            return options;
        }
    });

    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-agendaWeek-view .fc-body .fc-content-skeleton tbody td",
        build: function ($trigger, e) {
            var $this = $(e.currentTarget);
            var dateStr = $this.closest('.fc-agendaWeek-view').find('.fc-day-header:eq(' + ($this.index()-1) + ')').data('date');
            
            if (today <= dateStr) {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    } 
                };
            } 
            else {
                PNotify.removeAll();
                new PNotify({
                    title: '<?php echo Lang::line('warning') ?>',
                    text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                    type: 'warning',
                    sticker: false
                });
            }

            var options = {
                callback: function (key, opt) {},
                items: contextMenuData
            };

            return options;
        }
    });

    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-agendaDay-view .fc-body .fc-content-skeleton tbody td",
        build: function ($trigger, e) {
            var $this = $(e.currentTarget);
            var dateStr = $this.closest('.fc-agendaDay-view').find('.fc-day-header:eq(' + ($this.index()-1) + ')').data('date');
            
            if (today <= dateStr) {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    } 
                };
            } 
            else {
                PNotify.removeAll();
                new PNotify({
                    title: '<?php echo Lang::line('warning') ?>',
                    text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                    type: 'warning',
                    sticker: false
                });
            }

            var options = {
                callback: function (key, opt) {},
                items: contextMenuData
            };

            return options;
        }
    });

    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-event, #calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-event-container .fc-event",
        build: function ($trigger, e) {
            var $this = $(e.currentTarget);
            var dateStr = $this.closest('.fc-hrm-time-duration').data('date');
            if (typeof $this.closest('.fc-hrm-time-duration').data('date') === 'undefined') {
                dateStr = $this.closest('.fc-hrm-time-duration').attr('data-date');
            }
            
            var idStr = $this.data('id'), wfmCode = $this.data('wfmcode');

            if (typeof dateStr === 'undefined') {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    }
                };
            }
            else {
                if (today <= dateStr) {
                    var contextMenuData = {
                        "create": {
                            name: "Нэмэх",
                            icon: "plus",
                            callback: function (key, options) {
                                callSalesAddForm('', dateStr);
                            }
                        }
                    };
                } else {
                    PNotify.removeAll();
                    new PNotify({
                        title: '<?php echo Lang::line('warning') ?>',
                        text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                        type: 'warning',
                        sticker: false
                    });
                }
            }

            var options = {
                callback: function (key, opt) {},
                items: contextMenuData
            };

            return options;
        }
    });
    
    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-event, #calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body  .fc-event .fc-highlight",
        build: function ($trigger, e) {
            var $this = $(e.currentTarget);
            var dateStr = $this.closest('.fc-hrm-time-duration').data('date');
            
            if (typeof $this.closest('.fc-hrm-time-duration').data('date') === 'undefined') {
                dateStr = $this.closest('.fc-hrm-time-duration').attr('data-date');
            }
            
            var idStr = $this.data('id'), wfmCode = $this.data('wfmcode');
            
            if (today <= dateStr) {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    }
                };
            } 
            else {
                PNotify.removeAll();
                new PNotify({
                    title: '<?php echo Lang::line('warning') ?>',
                    text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                    type: 'warning',
                    sticker: false
                });
            }

            var options = {
                callback: function (key, opt) {},
                items: contextMenuData
            };

            return options;
        }
    });
    
    $.contextMenu({
        selector: "#calendar-<?php echo $this->uniqId; ?> .fc-view-container .fc-body .fc-highlight-skeleton > table > tbody > tr > td",
        build: function ($trigger, e) {
            
            var $this = $(e.currentTarget);
            var dateStr = $this.closest('.fc-week').find('.fc-bg > table > tbody > tr > td:eq(' + $this.index() + ')').data('date');
            
            if (today <= dateStr) {
                var contextMenuData = {
                    "create": {
                        name: "Нэмэх",
                        icon: "plus",
                        callback: function (key, options) {
                            callSalesAddForm('', dateStr);
                        }
                    }
                };
            }
            else {
                PNotify.removeAll();
                new PNotify({
                    title: '<?php echo Lang::line('warning') ?>',
                    text: '<?php echo Lang::line('WRONG_DATE_GOVERMENT_POST') ?>',
                    type: 'warning',
                    sticker: false
                });
            }

            var options = {
                callback: function (key, opt) {},
                items: contextMenuData
            };

            return options;
        }
    });

    function callSalesAddForm(paramData, date, isedit, id, callback, param,templateId,stime,etime) {

        var $dialogName = 'dialog-member_' +id;
        if (!$('#' + $dialogName).length) {
            $('<div id="' + $dialogName + '" class="display-none"></div>').appendTo('body');
        }
        var $dialog = $('#' + $dialogName);
        var fillDataParams = 'subjectId='+id+'&defaultGetPf=1';

        $.ajax({
            type: 'post',
            url: 'mdwebservice/callMethodByMeta',
            data: {
                metaDataId: '1587109580755', 
                isDialog: true, 
                isSystemMeta: false, 
                // fillDataParams: fillDataParams
            },
            dataType: 'json',
            beforeSend: function () {
                Core.blockUI({
                    message: 'Loading...', 
                    boxed: true
                });
            },
            success: function (data) {

                $dialog.empty().append(data.Html);

                var $processForm = $('#wsForm', '#' + $dialogName), 
                    processUniqId = $processForm.parent().attr('data-bp-uniq-id');

                var buttons = [
                    {text: data.run_btn, class: 'btn green-meadow btn-sm bp-btn-save', click: function (e) {
                        if (window['processBeforeSave_'+processUniqId]($(e.target))) {     

                            $processForm.validate({ 
                                ignore: '', 
                                highlight: function(element) {
                                    $(element).addClass('error');
                                    $(element).parent().addClass('error');
                                    if ($processForm.find("div.tab-pane:hidden:has(.error)").length) {
                                        $processForm.find("div.tab-pane:hidden:has(.error)").each(function(index, tab){
                                            var tabId = $(tab).attr('id');
                                            $processForm.find('a[href="#'+tabId+'"]').tab('show');
                                        });
                                    }
                                },
                                unhighlight: function(element) {
                                    $(element).removeClass('error');
                                    $(element).parent().removeClass('error');
                                },
                                errorPlacement: function(){} 
                            });

                            var isValidPattern = initBusinessProcessMaskEvent($processForm);

                            if ($processForm.valid() && isValidPattern.length === 0) {
                                $processForm.ajaxSubmit({
                                    type: 'post',
                                    url: 'mdwebservice/runProcess',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            boxed: true, 
                                            message: 'Түр хүлээнэ үү'
                                        });
                                    },
                                    success: function (responseData) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: responseData.status,
                                            text: responseData.message,
                                            type: responseData.status, 
                                            sticker: false
                                        });

                                        if (responseData.status === 'success') {
                                            $dialog.dialog('close');
                                        } 
                                        Core.unblockUI();
                                    },
                                    error: function () {
                                        alert("Error");
                                    }
                                });
                            }
                        }    
                    }},
                    {text: data.close_btn, class: 'btn blue-madison btn-sm', click: function () {
                        $dialog.dialog('close');

                    }}
                ];
                var dialogWidth = data.dialogWidth, dialogHeight = data.dialogHeight;

                if (data.isDialogSize === 'auto') {
                    dialogWidth = 1200;
                    dialogHeight = 'auto';
                }          

                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.Title,
                    width: dialogWidth,
                    height: dialogHeight,
                    modal: true,
                    closeOnEscape: (typeof isCloseOnEscape == 'undefined' ? true : isCloseOnEscape), 
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                        reload('refetchEvents');
                    },
                    buttons: buttons
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                if (data.dialogSize === 'fullscreen') {
                    $dialog.dialogExtend("maximize");
                }

                setTimeout(function(){
                    $dialog.dialog('open');
                    Core.unblockUI();
                }, 1);
            },
            error: function () {
                alert("Error");
            }
        }).done(function () {
            Core.initBPAjax($dialog);
            reload('refetchEvents');
        });
    }
    
</script>

<style type="text/css">
    .conf-room-<?php echo $this->uniqId ?> .hover-action-btns .btn { 
        padding: 0px 4px;
        border-radius: 0;
    }
    .conf-room-<?php echo $this->uniqId ?> .hover-action-btns {
        position: relative;
        height: 15px;
        top: 3px;
        float: right;
        right: -9px;
        display: none;
    }
    .conf-room-<?php echo $this->uniqId ?> a.fc-event:hover .hover-action-btns,
    .conf-room-<?php echo $this->uniqId ?> div.fc-event:hover .hover-action-btns {
        display: block !important;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip {
        display:inline-block;
        position:relative;
        text-align:left;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip h5 {
        padding: 10px;
        border-bottom: 1px dashed #FFF;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip .tooltipright {
        min-width:293px;
        max-width:293px;
        top:50%;
        left:100%;
        margin-left:20px;
        transform:translate(0, -50%);
        padding:0;
        color:#EEEEEE;
        background-color:#444444;
        font-weight:normal;
        font-size:13px;
        border-radius:8px;
        position:absolute;
        z-index:99999999;
        box-sizing:border-box;
        box-shadow:0 1px 8px rgba(0,0,0,0.5);
        visibility:hidden; opacity:0; transition:opacity 0.8s;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip:hover .tooltipright {
        visibility:visible; opacity:1;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip .tooltipright label {
        width:400px;
        border-radius:8px 8px 0 0;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip .text-content {
        padding:10px 20px;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip .tooltipright i {
        position:absolute;
        top:50%;
        right:100%;
        margin-top:-12px;
        width:12px;
        height:24px;
        overflow:hidden;
    }
    .conf-room-<?php echo $this->uniqId ?> .data-tooltip .tooltipright i::after {
        content:'';
        position:absolute;
        width:12px;
        height:12px;
        left:0;
        top:50%;
        transform:translate(50%,-50%) rotate(-45deg);
        background-color:#444444;
        box-shadow:0 1px 8px rgba(0,0,0,0.5);
    }
    .custom_border_bottom {
        /*border-bottom: 1px solid red;*/
    }
    #tableAgree td, .table th {
        border:none;
    }
</style>