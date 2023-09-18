<?php
if ($this->isAjax == false) {
?>
<div class="col-md-12">
    <div class="card light shadow card-multi-tab">
        <div class="card-header header-elements-inline tabbable-line">
            <ul class="nav nav-tabs card-multi-tab-navtabs">
                <li>
                    <a href="#app_tab_<?php echo $this->uniqId; ?>" class="active" data-toggle="tab">
                        <i class="fa fa-caret-right"></i> <?php echo $this->title; ?><span><i class="fa fa-times-circle"></i></span>
                    </a>
                </li>
            </ul>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="fullscreen"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content card-multi-tab-content">
                <div class="tab-pane active" id="app_tab_<?php echo $this->uniqId; ?>">
                    <?php echo $this->renderChart; ?> 
                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    echo $this->renderChart;    
}
?>

<script type="text/javascript">
var kpiDMChart_<?php echo $this->uniqId; ?> = $('#kpi-datamart-chart-<?php echo $this->uniqId; ?>');
var kpiChartObj_<?php echo $this->uniqId; ?> = {};
var kpiChartOption_<?php echo $this->uniqId; ?> = {};

var dynamicHeight = $(window).height() - kpiDMChart_<?php echo $this->uniqId; ?>.offset().top - 20;

if (dynamicHeight < 230) {
    dynamicHeight = 350;
}

if (kpiDMChart_<?php echo $this->uniqId; ?>.closest('.package-tab').length) {
    dynamicHeight = 'auto';
}

if (typeof isKpiIndicatorScript === 'undefined') {
    $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/indicator.js'); ?>');
}

if (typeof isKpiIndicatorEchartsScript === 'undefined') {
    $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/echartsBuilder.js'); ?>');
}

filterKpiIndicatorValueChart(<?php echo $this->uniqId; ?>, <?php echo $this->indicatorId; ?>);

function filterKpiIndicatorValueChart(uniqId, indicatorId) {
    $.ajax({
        type: 'post',
        url: 'mdform/filterKpiIndicatorValueForm',
        data: {uniqId: uniqId, indicatorId: indicatorId},
        dataType: 'json',
        success: function(data) {
            var $filterCol = $('#kpi-datamart-chart-' + uniqId + ' .kpidv-data-filter-col');
            
            if (data.status == 'success' && data.html != '') {
                $filterCol.css('height', dynamicHeight - 20);
                $filterCol.css('minHeight', '100%');
                $filterCol.append(data.html).promise().done(function() {
                    Core.initNumberInput($filterCol);
                    Core.initLongInput($filterCol);
                    Core.initDateInput($filterCol);
                });
            } else {
                $filterCol.closest('.col-md-auto').remove();
            }
        }
    });
}

function filterKpiIndicatorValueChartLoad(elem) {
    var $this = $(elem), 
        $parent = $this.closest('.list-group'), 
        uniqId = $parent.attr('data-uniqid');
    
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        $this.find('i').removeClass('fas fa-check-square').addClass('far fa-square');
    } else {
        $this.addClass('active');
        $this.find('i').removeClass('far fa-square').addClass('fas fa-check-square');
    }
    
    window['kpiDataMartLoadChart_' + uniqId]();
}

function kpiDataMartLoadChart_<?php echo $this->uniqId; ?>() {
    
    var _chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"]');
        kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartType"]').val(_chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-value'));
        
    var chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartType"]').val(),
        chartMainType = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartMainType"]').val(),
        chartCategory = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategory"]').val(), 
        chartCategoryGroup = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategoryGroup"]').val(), 
        chartValue = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValue"]').val(), 
        chartAggregate = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartAggregate"]').val();
    
    if (isArray(chartValue)) {
        chartValue = chartValue.join(',');
    }
    
    if (chartType != '' 
        && (
            ((chartType != 'card' && chartType != 'card_vertical') && chartCategory != '') || (chartType == 'card' || chartType == 'card_vertical')
        ) 
        && (chartValue != '' || (chartValue == '' && chartAggregate == 'COUNT') || (chartType === 'tree'|| chartType === 'tree_circle')) 
        ) {
            
    
        if (chartType == 'stacked_column' && chartCategoryGroup == '') {
            return false;
        }
        
        var chartValueSortType = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValueSortType"]').val(), 
            chartRowNum = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartRowNum"]').val(),
            chartLabelText = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartLabelText"]').val(),
            chartBgColor = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartBgColor"]').val(), 
            chartIconName = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartIconName"]').val(), 
            chartLineColumn = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartLineChartColumn"]').val(), 
            chartLineAggregate = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartLineChartAggregate"]').val();
        
        kpiDMChart_<?php echo $this->uniqId; ?>.find('option[data-type]').hide();
        kpiDMChart_<?php echo $this->uniqId; ?>.find('option[data-type="'+  chartMainType +'"]').show();

        kpiDMChart_<?php echo $this->uniqId; ?>.find('div.echart').hide();
        if (chartMainType === 'echart') {
            kpiDMChart_<?php echo $this->uniqId; ?>.find('div.echart').show();
            kpiDMChart_<?php echo $this->uniqId; ?>.find('.chartTypesConfigration, .chartTypesConfigration .configration').hide();
            
            if (typeof _chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-config') !== 'undefined') {
                var _dataConfigStr = _chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-config');

                if (_dataConfigStr && typeof window['chartConfigrationToggle'] !== 'undefined') {
                    chartConfigrationToggle(kpiDMChart_<?php echo $this->uniqId; ?>, _dataConfigStr);
                }
            }
        }
        
        var chartConfig = {
            mainType: chartMainType, 
            type: chartType, 
            axisX: chartCategory, 
            axisXGroup: chartCategoryGroup, 
            axisY: chartValue, 
            aggregate: chartAggregate, 
            axisYSortType: chartValueSortType, 
            rowNum: chartRowNum, 
            labelText: chartLabelText, 
            bgColor: chartBgColor, 
            iconName: chartIconName
        };    

        <?php if (issetParam($this->isBuild) === '1') { ?>
            if (chartType !== 'card' && chartType != 'card_vertical') {
                var themeGroup = JSON.parse(kpiDMChart_<?php echo $this->uniqId; ?>.find('.theme-plan-group.selected').attr('data-rowdata'));
                chartConfig['bgColor'] = themeGroup['bgColor'];
                chartConfig['color'] = themeGroup['themeColor'];
                
            }

            kpiDMChart_<?php echo $this->uniqId; ?>.find('.configration input, .configration select').each(function (iConfig, rConfig) {
                var $rConfig = $(rConfig),
                    _tagName = $rConfig.prop('tagName').toLowerCase();
                    _attrType = $rConfig.attr('type');
                    _code = $rConfig.attr('id');
                
                if (_attrType === 'checkbox') {
                    chartConfig[_code] = $rConfig.is(':checked');
                } else {
                    chartConfig[_code] = $rConfig.val();
                }
            });
            
        <?php } ?>
        
        if (chartLineColumn != '' && chartLineAggregate != '') {
            chartConfig.lineChartConfig = {column: chartLineColumn, aggregate: chartLineAggregate};
        }
        
        if (chartType == 'maps') {
            var chartMapCountry = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartMapCountry"]').val();
            if (chartMapCountry != '') {
                chartConfig.mapsChartConfig = {country: chartMapCountry};
            } else {
                return false;
            }
        }
        
        var filterData = getChartFilterData_<?php echo $this->uniqId; ?>();   
        
        $.ajax({
            type: 'post',
            url: 'mdform/filterKpiIndicatorValueChart',
            data: {
                indicatorId: '<?php echo $this->indicatorId; ?>', 
                chartConfig: chartConfig, 
                filterData: filterData,
                isBuild: '<?php echo issetDefaultVal($this->isBuild, '0') ?>'
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    
                    $('#kpi-datamart-chart-render-<?php echo $this->uniqId; ?>').empty().append('').promise().done(function () {

                        if (chartMainType === 'echart') {
                            kpiChartObj_<?php echo $this->uniqId; ?> = {
                                elemId: 'kpi-datamart-chart-render-<?php echo $this->uniqId; ?>', 
                                chartConfig: chartConfig, 
                                data: data.data, 
                                dataXaxis: data.dataXaxis, 
                                columnsConfig: data.columnsConfig,
                                useData: '1',
                            }
                            kpiDataMartEChartBuildRender(kpiChartObj_<?php echo $this->uniqId; ?>);
    
                        } else {
                            kpiDataMartChartRender({
                                elemId: 'kpi-datamart-chart-render-<?php echo $this->uniqId; ?>', 
                                chartConfig: chartConfig, 
                                data: data.data, 
                                columnsConfig: data.columnsConfig
                            });
                        }
                    });
                } else {
                    console.log(data);
                }
            }
        });
    }
}
function getChartFilterData_<?php echo $this->uniqId; ?>() {
    var $col = $('#kpi-datamart-chart-<?php echo $this->uniqId; ?>').find('.kpidv-data-filter-col');
    
    var getFilterData = getKpiIndicatorFilterData('', $col);
    var filterData = getFilterData.filterData;
    
    return filterData;
}

$(function() {
    
    $.cachedScript('assets/custom/addon/plugins/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js?v=1').done(function() {
        $("head").append('<link rel="stylesheet" type="text/css" href="assets/custom/addon/plugins/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css"/>');
        
        kpiDMChart_<?php echo $this->uniqId; ?>.find('button[role="iconpicker"]').iconpicker({
            arrowPrevIconClass: 'fa fa-arrow-left',
            arrowNextIconClass: 'fa fa-arrow-right'
        });
        
        kpiDMChart_<?php echo $this->uniqId; ?>.on('change', '#menu-iconpicker', function(e){ 
            if (e.icon === 'empty' || e.icon === 'fa-empty') {
                $("input[name='kpiDMChartIconName']").val('').trigger('change');
            } else {
                $("input[name='kpiDMChartIconName']").val(e.icon).trigger('change');
            }
        });
    });

    kpiDMChart_<?php echo $this->uniqId; ?>.on('change', 'select[name="kpiDMChartType"], select[name="kpiDMChartAggregate"], select[name="kpiDMChartCategory"], select[name="kpiDMChartValue"], select[name="kpiDMChartCategoryGroup"], select[name="kpiDMChartValueSortType"], input[name="kpiDMChartRowNum"], input[name="kpiDMChartLabelText"], select[name="kpiDMChartBgColor"], input[name="kpiDMChartIconName"], select[name="kpiDMChartLineChartColumn"], select[name="kpiDMChartLineChartAggregate"], select[name="kpiDMChartMapCountry"]', function() {
        
        var $this = $(this), name = $this.attr('name');
        var _chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"]');
        var $chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartType"]');
        var $chartMainType = $('input[name="kpiDMChartMainType"]');
        var $chartValue = $('select[name="kpiDMChartValue"]');
        var $chartParent = $chartValue.parent();
        
        kpiDMChart_<?php echo $this->uniqId; ?>.find('div.echart').hide();
        /* if ($chartMainType.val() ==='echart') {
            kpiDMChart_<?php echo $this->uniqId; ?>.find('div.echart').show();
        } */
        
        kpiDMChart_<?php echo $this->uniqId; ?>.find('option[data-type]').hide();
        kpiDMChart_<?php echo $this->uniqId; ?>.find('option[data-type="'+  $chartMainType.val() +'"]').show();

        $chartType.val(_chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-value'));
        var _chartMainType = _chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-maintype');
        if (_chartMainType) {
            $chartMainType.val(_chartMainType);
        } else {
            $chartMainType.val('amchart');
        }
        
        if (name == 'kpiDMChartType') {
            
            /* var chartTypeVal = _chartType.val(); */
            var chartTypeVal = $chartType.val();
            
            var $kpiDMChartCategoryGroupRow = $('.kpiDMChartCategoryGroup-row');
            var $kpiDMChartCategoryRow = $('.kpiDMChartCategory-row');
            var $kpiDMChartLabelTextRow = $('.kpiDMChartLabelText-row');
            var $kpiDMChartBgColorRow = $('.kpiDMChartBgColor-row');
            var $kpiDMChartIconNameRow = $('.kpiDMChartIconName-row');
            var $kpiDMChartLineChartColumnRow = $('.kpiDMChartLineChartColumn-row');
            var $kpiDMChartLineChartAggregateRow = $('.kpiDMChartLineChartAggregate-row');
            var $kpiDMChartMapCountryRow = $('.kpiDMChartMapCountry-row');
            
            $chartValue.find('option[value=""]').remove();
            
            $kpiDMChartCategoryGroupRow.addClass('d-none');
            $kpiDMChartCategoryRow.removeClass('d-none');
            
            $kpiDMChartLabelTextRow.addClass('d-none');
            $kpiDMChartBgColorRow.addClass('d-none');
            $kpiDMChartIconNameRow.addClass('d-none');
            $kpiDMChartLineChartColumnRow.addClass('d-none');
            $kpiDMChartLineChartAggregateRow.addClass('d-none');
            $kpiDMChartMapCountryRow.addClass('d-none');
            
            if (chartTypeVal == 'clustered_column') {
                
                if (!$chartValue.hasAttr('multiple')) {
                    $chartValue.prop('multiple', true);
                }
            
            } else if (chartTypeVal == 'stacked_column') {
                
                $kpiDMChartCategoryGroupRow.removeClass('d-none');
                
                $chartValue.prepend('<option value="">- '+plang.get('select_btn')+' -</option>');
                $chartValue.prop('multiple', false);
                
            } else if (chartTypeVal == 'card' || chartTypeVal == 'card_vertical') { 
                
                $kpiDMChartCategoryRow.addClass('d-none');
                $kpiDMChartLabelTextRow.removeClass('d-none');
                $kpiDMChartBgColorRow.removeClass('d-none');
                $kpiDMChartIconNameRow.removeClass('d-none');
            
                $chartValue.prepend('<option value="">- '+plang.get('select_btn')+' -</option>');
                $chartValue.prop('multiple', false);
                
            } else if (chartTypeVal == 'maps') { 
                
                $kpiDMChartMapCountryRow.removeClass('d-none');
                
            } else {
                
                $chartValue.prepend('<option value="">- '+plang.get('select_btn')+' -</option>');
                $chartValue.prop('multiple', false);
            }
            
            if (chartTypeVal == 'clustered_column' || chartTypeVal == 'column') {
                
                $kpiDMChartLineChartColumnRow.removeClass('d-none');
                $kpiDMChartLineChartAggregateRow.removeClass('d-none');
            }

            try {
                $chartValue.select2('destroy');
            } catch (error) {
                console.log(error);                
            }
            Core.initSelect2($chartParent);
            
        } else if (name == 'kpiDMChartValue') {
            
            var valueShowType = $chartValue.find('option:selected').attr('data-showtype');
            
            if (valueShowType == 'text') {
                $('select[name="kpiDMChartAggregate"]').val('COUNT');
            }
        }
        
        kpiDataMartLoadChart_<?php echo $this->uniqId; ?>();
    });
    
    kpiDMChart_<?php echo $this->uniqId; ?>.on('click', '.kpi-dm-chart-create', function() {
        
        var _chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"]');
        kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartType"]').val(_chartType.find('option[value="'+ _chartType.val() +'"]').attr('data-value'));

        var chartType = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartType"]').val(),
            chartMainType = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartMainType"]').val(),
            chartAggregate = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartAggregate"]').val(),
            chartCategory = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategory"]').val(), 
            chartCategoryGroup = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategoryGroup"]').val(), 
            chartValue = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValue"]').val(), 
            chartMapCountry = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartMapCountry"]').val(),
            $this = $(this);
        
        if (isArray(chartValue)) {
            chartValue = chartValue.join(',');
        }
        
        kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"], select[name="kpiDMChartAggregate"], select[name="kpiDMChartCategory"], select[name="kpiDMChartValue"], select[name="kpiDMChartCategoryGroup"], select[name="kpiDMChartMapCountry"]').removeClass('error');
        console.log(chartType);
        if (chartType == '' || ((chartType != 'card' && chartType != 'card_vertical') && chartCategory == '') || (chartValue == '' && chartAggregate != 'COUNT' && chartType != 'tree' && chartType != 'tree_circle') || chartAggregate == '') {
            
            if (chartType == '') {
                kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"]').addClass('error');
            }
            
            if ((chartType != 'card' && chartType != 'card_vertical') && chartCategory == '') {
                kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategory"]').addClass('error');
            }
            
            if (chartValue == '') {
                kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValue"]').addClass('error');
            }
            
            if (chartAggregate == '' && chartAggregate != 'COUNT') {
                kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartAggregate"]').addClass('error');
            }
            
            return false;
        }
        
        if (chartType == 'stacked_column' && chartCategoryGroup == '') {
            kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartCategoryGroup"]').addClass('error');
            return false;
        }
        
        if (chartType == 'maps' && chartMapCountry == '') {
            kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartMapCountry"]').addClass('error');
            return false;
        }

        var html = '<form>'+
            '<div class="col-md-12 xs-form">'+ 
                '<div class="form-group row">'+
                    '<label class="col-form-label col-md-2 text-right pr0" for="dmart_label_name"><span class="required">*</span>Чартын нэр:</label>'+
                    '<div class="col-md-10">'+
                        '<input type="text" name="chartTitle" class="form-control form-control-sm" placeholder="Чартын нэр" required="required" value="<?php echo issetParam($this->chartName); ?>"/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</form>';
        
        var $dialogName = 'dialog-kpidmart-createchart';
        if (!$("#" + $dialogName).length) { $('<div id="' + $dialogName + '"></div>').appendTo('body'); }
        var $dialog = $('#' + $dialogName);
    
        $dialog.html(html);
        $dialog.dialog({
            cache: false,
            resizable: false,
            bgiframe: true,
            autoOpen: false,
            title: '<?php echo isset($this->chartIndicatorId) ? 'Чарт засах' : 'Чарт үүсгэх'; ?>',
            width: 600,
            height: 'auto',
            modal: true,
            close: function () {
                $dialog.dialog('destroy').remove();
            },
            buttons: [
                {text: plang.get('save_btn'), class: 'btn btn-sm green-meadow', click: function() {

                    PNotify.removeAll();
                    
                    var $form = $dialog.find('form');
                    $form.validate({errorPlacement: function () {}});

                    if ($form.valid()) {
                        
                        $form.ajaxSubmit({
                            type: 'post',
                            url: 'mdform/createKpiDmChart',
                            dataType: 'json',
                            beforeSubmit: function(formData, jqForm, options) {
                                
                                var chartValueSortType = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValueSortType"]').val(), 
                                    chartRowNum = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartRowNum"]').val(), 
                                    chartLabelText = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartLabelText"]').val(),
                                    chartBgColor = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartBgColor"]').val(), 
                                    chartIconName = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartIconName"]').val();
                                
                                formData.push(
                                    {name: 'indicatorId', value: '<?php echo $this->indicatorId; ?>'}, 
                                    {name: 'chartType', value: chartType}, 
                                    {name: 'chartMainType', value: chartMainType}, 
                                    {name: 'chartCategory', value: chartCategory}, 
                                    {name: 'chartCategoryGroup', value: chartCategoryGroup}, 
                                    {name: 'chartValue', value: chartValue}, 
                                    {name: 'chartAggregate', value: chartAggregate}, 
                                    {name: 'chartValueSortType', value: chartValueSortType}, 
                                    {name: 'chartRowNum', value: chartRowNum}, 
                                    {name: 'chartLabelText', value: chartLabelText}, 
                                    {name: 'chartBgColor', value: chartBgColor}, 
                                    {name: 'chartIconName', value: chartIconName}, 
                                    {name: 'chartMapCountry', value: chartMapCountry},
                                    );
                                <?php if (issetParam($this->isBuild) === '1') { ?>
                                    formData.push({name: 'buildCharConfig', value: $this.attr('data-config')});
                                    kpiDMChart_<?php echo $this->uniqId; ?>.find('.chartTypesConfigration input, .chartTypesConfigration select').each(function (i, r) {
                                        
                                        var _selectedAttr= $(r), _tagName = _selectedAttr.prop('tagName').toLowerCase();
                                        if (_selectedAttr.attr('type') === 'checkbox') {
                                            if (_selectedAttr.is(':checked')) {
                                                formData.push({name: _selectedAttr.attr('name'), value: '1'});
                                            }
                                        } else {
                                            formData.push({name: _selectedAttr.attr('name'), value: _selectedAttr.val()});
                                        }
                                        
                                    });
                                <?php } ?>
                                

                                if (chartType == 'clustered_column' || chartType == 'column') {
                                    
                                    var chartLineColumn = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartLineChartColumn"]').val(), 
                                        chartLineAggregate = kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartLineChartAggregate"]').val();
                            
                                    if (chartLineColumn != '' && chartLineAggregate != '') {
                                        formData.push({name: 'chartLineColumn', value: chartLineColumn});
                                        formData.push({name: 'chartLineAggregate', value: chartLineAggregate});
                                    }
                                }
                        
                                var isFilterSave = kpiDMChart_<?php echo $this->uniqId; ?>.find('input[name="kpiDMChartIsFilterSave"]').is(':checked');
                                
                                if (isFilterSave) {
                                    var filterData = getChartFilterData_<?php echo $this->uniqId; ?>();   
                                    formData.push({name: 'chartFilterCriteria', value: JSON.stringify(filterData)});
                                }
                                
                                <?php
                                if (isset($this->chartIndicatorId)) {
                                ?>
                                    formData.push({name: 'chartIndicatorId', value: '<?php echo $this->chartIndicatorId; ?>'});
                                <?php
                                }
                                ?>
                            },
                            beforeSend: function () {
                                Core.blockUI({message: 'Loading...', boxed: true});
                            },
                            success: function (data) {

                                new PNotify({
                                    title: data.status,
                                    text: data.message,
                                    type: data.status,
                                    sticker: false, 
                                    hide: true,  
                                    addclass: pnotifyPosition
                                });

                                if (data.status == 'success') {
                                    $dialog.dialog('close');
                                } 

                                Core.unblockUI();
                            }
                        });
                    }
                }},
                {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function() { 
                    $dialog.dialog('close'); 
                }}
            ]
        });
        $dialog.dialog('open');
    });
    
    kpiDMChart_<?php echo $this->uniqId; ?>.on('change', 'select[name="kpiDMChartBgColor"]', function() {
        var $this = $(this);
        
        if ($this.val() != '') {
            $this.css('background-color', $this.val());
        } else {
            $this.css('background-color', '');
        }
    });

    <?php
    if (issetParam($this->graphJsonConfig)) {
    ?>
    kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartType"]').trigger('change');
    
    /*if (kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValue"]') != '' 
        && kpiDMChart_<?php echo $this->uniqId; ?>.find('select[name="kpiDMChartValue"]').find('option:selected').attr('data-showtype') == 'text') {
        
        $('select[name="kpiDMChartAggregate"]').val('COUNT');
    }*/

    <?php
    }
    ?>

});

    <?php if (issetParam($this->isBuild) === '1') { ?>
    
    kpiDMChart_<?php echo $this->uniqId; ?>.on('click', '.theme-plan-group', function () {
        var $this = $(this),
            themeGroup = JSON.parse($this.attr('data-rowdata')),
            $form = $this.closest('form');
            $form.find('.theme-plan-group').removeClass('selected');
            $this.addClass('selected');
        /* 
        if (typeof kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'] ==='undefined') {
            kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'] = {};
        }
        */
        kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig']['themeCode'] = themeGroup.code;
        kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig']['bgColor'] = themeGroup.bgColor;
        kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig']['color'] = themeGroup.themeColor;

        kpiDataMartEChartBuildRender(kpiChartObj_<?php echo $this->uniqId; ?>);
    });
    
    kpiDMChart_<?php echo $this->uniqId; ?>.on('change', '.configration input, .configration select', function () {
        var $this = $(this),
            _tagName = $this.prop('tagName').toLowerCase();
            _attrType = $this.attr('type');
            _code = $this.attr('id');

        if (typeof kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'] ==='undefined') {
            kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'] = {};
        }
        
        if (_attrType === 'checkbox') {
            kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'][_code] = $this.is(':checked');
        } else {
            kpiChartObj_<?php echo $this->uniqId; ?>['chartConfig'][_code] = $this.val();
        }
        kpiDataMartEChartBuildRender(kpiChartObj_<?php echo $this->uniqId; ?>);
    });
    

    <?php } ?>

</script>