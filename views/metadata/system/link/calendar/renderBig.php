<div id="calendar-<?php echo $this->metaDataId; ?>" class="has-toolbar"></div>

<script type="text/javascript">
$(function(){
    if (!jQuery().fullCalendar) {
        return;
    }
    
    var bigCalIdName = $('#calendar-<?php echo $this->metaDataId; ?>');
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    var h = {};

    if (Core.isRTL()) {
        if (bigCalIdName.parents(".card").width() <= 720) {
            bigCalIdName.addClass("mobile");
            h = {
                right: 'title, prev, next',
                center: '',
                left: 'agendaDay, agendaWeek, month, today'
            };
        } else {
            bigCalIdName.removeClass("mobile");
            h = {
                right: 'title',
                center: '',
                left: 'agendaDay, agendaWeek, month, today, prev,next'
            };
        }
    } else {
        if (bigCalIdName.parents(".card").width() <= 720) {
            bigCalIdName.addClass("mobile");
            h = {
                left: 'title, prev, next',
                center: '',
                right: 'today,month,agendaWeek,agendaDay'
            };
        } else {
            bigCalIdName.removeClass("mobile");
            h = {
                left: 'title',
                center: '',
                right: 'prev,next,today,month,agendaWeek,agendaDay'
            };
        }
    }

    bigCalIdName.fullCalendar('destroy');
    bigCalIdName.fullCalendar({
        header: h,
        defaultView: 'month', 
        slotMinutes: 15,
        editable: true,
        droppable: false,
        height: 550
    });
});    
</script>