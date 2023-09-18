<?php
if ($this->isAjax == false) {
?>
<div class="col-md-12">
    <div class="card light shadow card-multi-tab">
        <div class="card-header header-elements-inline tabbable-line">
            <ul class="nav nav-tabs card-multi-tab-navtabs">
                <li>
                    <a href="#app_tab_<?php echo $this->uniqId; ?>" class="active" data-toggle="tab"><i class="fa fa-caret-right"></i> <?php echo $this->title; ?><span><i class="fa fa-times-circle"></i></span></a>
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
var dynamicHeight = $(window).height() - kpiDMChart_<?php echo $this->uniqId; ?>.offset().top - 20;

if (dynamicHeight < 230) {
    dynamicHeight = 350;
}

if (kpiDMChart_<?php echo $this->uniqId; ?>.closest('.package-tab').length) {
    dynamicHeight = 'auto';
}

if (typeof isKpiIndicatorEchartsScript === 'undefined') {
    $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/echartsBuilder.js'); ?>').done(function() {      
        if (typeof isKpiIndicatorScript === 'undefined') {
            $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/indicator.js'); ?>').done(function() {
                kpiDataMartLoadChart_<?php echo $this->uniqId; ?>();
            });
        } else {
            kpiDataMartLoadChart_<?php echo $this->uniqId; ?>();
        }
    });
} else {
    if (typeof isKpiIndicatorScript === 'undefined') {
        $.cachedScript('<?php echo autoVersion('middleware/assets/js/addon/indicator.js'); ?>').done(function() {
            kpiDataMartLoadChart_<?php echo $this->uniqId; ?>();
        });
    } else {
        kpiDataMartLoadChart_<?php echo $this->uniqId; ?>();
    }
}


filterKpiIndicatorValueChartList(<?php echo $this->uniqId; ?>, <?php echo $this->indicatorId; ?>);

function filterKpiIndicatorValueChartList(uniqId, indicatorId) {
    $.ajax({
        type: 'post',
        url: 'mdform/filterKpiIndicatorValueForm',
        data: {uniqId: uniqId, indicatorId: indicatorId, isChartList: 1},
        dataType: 'json',
        success: function(data) {
            var $filterCol = $('#kpi-datamart-chart-' + uniqId + ' .kpidv-data-filter-col');
            
            if (data.status == 'success' && data.html != '') {
                
                $filterCol.css('height', dynamicHeight - 20);
                
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
function filterKpiIndicatorValueChartListLoad(elem) {
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
        
    var $div = $('#kpi-datamart-chart-<?php echo $this->uniqId; ?>'), 
        $col = $div.find('.kpidv-data-filter-col'), 
        $charts = $div.find('.kpidm-chart-list-div');   
    
    var getFilterData = getKpiIndicatorFilterData('', $col);
    var filterData = getFilterData.filterData;
    
    if ($charts.length) {
        
        $charts.each(function() {
            var $this = $(this), chartId = $this.attr('id'), 
                scriptJson = $this.next('script[data-id="'+chartId+'"]').text(), 
                jsonObj = JSON.parse(scriptJson), chartConfig = jsonObj.chartConfig, 
                loopFilterData = filterData;
            
            if (jsonObj.hasOwnProperty('chartFilterCriteria')) {
                var chartFilterCriteria = JSON.parse(html_entity_decode(jsonObj.chartFilterCriteria, 'ENT_QUOTES'));
                
                if (Object.keys(chartFilterCriteria).length > 0 && Object.keys(loopFilterData).length > 0) {
                    for (var f in chartFilterCriteria) {
                        if (loopFilterData && !loopFilterData.hasOwnProperty(f)) {
                            loopFilterData[f] = chartFilterCriteria[f];
                        }
                    }
                } else {
                    loopFilterData = chartFilterCriteria;
                }
            }
                    
            $.ajax({
                type: 'post',
                url: 'mdform/filterKpiIndicatorValueChart',
                data: {
                    indicatorId: '<?php echo $this->indicatorId; ?>', 
                    chartConfig: chartConfig, 
                    filterData: loopFilterData
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        if (typeof chartConfig.mainType !== 'undefined' && chartConfig.mainType === 'echart') {
                            kpiDataMartEChartBuildRender({
                                elemId: chartId, 
                                chartConfig: chartConfig, 
                                data: data.data, 
                                columnsConfig: data.columnsConfig
                            });
                        } else {
                            kpiDataMartChartRender({
                                elemId: chartId, 
                                chartConfig: chartConfig, 
                                data: data.data, 
                                columnsConfig: data.columnsConfig
                            });
                        }

                    } else {
                        console.log(data);
                    }
                }
            });
        });
    }
}
</script>