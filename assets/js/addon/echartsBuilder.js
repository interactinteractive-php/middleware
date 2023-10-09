var isKpiIndicatorEchartsScript = true;
var googleMapActiveWindow = null;
var kpIndicatorChart = {};

var EchartBuilder = function() {

    var pushToAry = function (name, val) {
        var obj = {};
        obj[name] = val;
        
        return obj;
    }
     
    var recursivePushObj = function (tmpObj, tmp, value) {
        if (typeof tmp[tmpObj[0]] === 'undefined') {
            tmp[tmpObj[0]] = {};
        }
        if (tmpObj.length === 3 ) {
            if (typeof tmp[tmpObj[0]][tmpObj[1]] === 'undefined') {
                tmp[tmpObj[0]][tmpObj[1]] = {};
            }
            tmp[tmpObj[0]][tmpObj[1]][tmpObj[2]] = value;
        } else {
            tmp[tmpObj[0]][tmpObj[1]] = (tmpObj[1] === 'data') ? JSON.parse(value) : value;
        }
    
        return tmp;
    }
     
    var convertOptions = function  (chartConfig, option) {
        var tmp = {}
        $.each(chartConfig, function (index, row) {
            if (row) {
                if (index.indexOf('_') > -1) {
                    var tmpObj = index.split('_');
                    tmp = recursivePushObj(tmpObj, tmp, row);
                } else {
                    tmp[index] = row;
                }
    
                option = {
                    ...option,
                    ...tmp,
                }
            }
        });
    
        return option;
    }
     
    var chartRender = function (obj) {
         
        var elemId = obj.elemId;
        if (!elemId) return false;

        var $mainSelector = $('#' + elemId).closest('.theme-builder');
        var chartConfig = obj.chartConfig;
        var useData = obj.useData;
        var type = chartConfig.type;
        var chartName = (chartConfig.hasOwnProperty('chartName')) ? chartConfig.chartName : type + '_chart';
        var category = chartConfig.axisX;
        var value = chartConfig.axisY;
        var aggregate = chartConfig.aggregate;
        var columnsConfig = obj.columnsConfig;
        var data = obj.data;
        var isRunInterval = false;
        var isLineChartConfig = false;
        var kpiLayoutIndex = 0;
        obj.isLayoutBuilder = false;
    
        if (typeof $mainSelector.attr('data-kpi-layout') !== 'undefined' && $mainSelector.attr('data-kpi-layout') !== '0') {
            isLayoutBuilder = true;
            obj.isLayoutBuilder = true;
            var _parentSelector = $('#' + elemId).closest('.layout-builder-v0');
            kpiLayoutIndex = $mainSelector.attr('data-kpi-layout');
            elemId = _parentSelector.find('div.selected-box[data-kl-col="'+ kpiLayoutIndex +'"] [data-section-code="'+ kpiLayoutIndex +'"]').attr('id');
        }
    
        if (aggregate == 'COUNT' && (value == '' || value === null)) {
            value = 'COUNT_COL';
        }
                
        if (chartConfig.hasOwnProperty('lineChartConfig') && chartConfig.lineChartConfig) {
            isLineChartConfig = true;
        }
                
        if (obj.hasOwnProperty('isRunInterval') && obj.isRunInterval) {
    
            isRunInterval = true;
    
            if (kpIndicatorChart.hasOwnProperty(elemId)) {
                
                var prevChart = kpIndicatorChart[elemId];
                
                if (type == 'stacked_column') {
                    var stackedData = kpiDataMartStackedColumnData(data, category, value, chartConfig);
                    data = stackedData.data;
                }
                
                prevChart.data = data;
                return;
            }
        }
    
        var themeGroup;
    
        if ($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata')) {
            themeGroup = JSON.parse($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata'));
        }
        
        try {
            var chartDom = document.getElementById(elemId);
            var option;
            
        } catch (error) {
            console.log(error);
        }
        
        echarts.dispose(chartDom);
        var myChart = echarts.init(chartDom);
        var dataSet = data;
        var grid = {containLabel: true};
        option = {
            themeCode: chartConfig.themeCode,
        };
        option = convertOptions(chartConfig, option);
    
        var axisLabel = {},
            yAxis = {
                type: 'value',
            },
            xAxis = {
                type: 'category',
                label: axisLabel,
            };
        
        var seriesLabel = {position: 'middle',
        formatter: '{b}: {c}'};
    
        if (typeof chartConfig.axisLabelShow !== 'undefined') {
            axisLabel = {...axisLabel,
                show: chartConfig.axisLabelShow
            };
    
            seriesLabel = {...seriesLabel,
                show: chartConfig.axisLabelShow
            };
        }
        
        if (axisLabel.length > 0) {
            xAxis = {
                ...xAxis,
                label: axisLabel,
            }
        }
        
        if (typeof chartConfig.xyReverse !== 'undefined' && chartConfig.xyReverse) {
            var xAxisTmp = xAxis;
            xAxis = yAxis;
            yAxis = xAxisTmp;
        }
    
        var series = {
            data: dataSet,
            type: type,
            showBackground: true,
            backgroundStyle: {
                color: 'rgba(180, 180, 180, 0.2)'
            },
            label: seriesLabel,
        };

        switch (type) {
            case 'maps': 
                myChart.showLoading();
                myChart.hideLoading();
    
                /* $.getJSON(URL_APP + 'middleware/assets/js/addon/data.json', function (data) { */
                    function getAirportCoord(idx) {
                        console.log(data.airports[idx]);
                        if (data.airports[idx]) {
                            return [data.airports[idx][3], data.airports[idx][4]];
                        }
                    }
                    var routes = data.routes.map(function (airline) {
                        console.log(airline);
                        return [getAirportCoord(airline[1]), getAirportCoord(airline[2])];
                    });
                    
                    option = {
                        geo3D: {
                        map: 'world',
                        shading: 'realistic',
                        silent: true,
                        environment: '#333',
                        realisticMaterial: {
                            roughness: 0.8,
                            metalness: 0
                        },
                        postEffect: {
                            enable: true
                        },
                        groundPlane: {
                            show: false
                        },
                        light: {
                            main: {
                            intensity: 1,
                            alpha: 30
                            },
                            ambient: {
                            intensity: 0
                            }
                        },
                        viewControl: {
                            distance: 70,
                            alpha: 89,
                            panMouseButton: 'left',
                            rotateMouseButton: 'right'
                        },
                        itemStyle: {
                            color: '#000'
                        },
                        regionHeight: 0.5
                        },
                        series: [
                        {
                            type: 'lines3D',
                            coordinateSystem: 'geo3D',
                            effect: {
                            show: true,
                            trailWidth: 1,
                            trailOpacity: 0.5,
                            trailLength: 0.2,
                            constantSpeed: 5
                            },
                            blendMode: 'lighter',
                            lineStyle: {
                            width: 0.2,
                            opacity: 0.05
                            },
                            data: routes
                        }
                        ]
                    };
                    
                    option && myChart.setOption(option);
                    $mainSelector.find('.kpi-dm-chart-create').attr('data-config', JSON.stringify(option));
    
                    window.addEventListener("resize", myChart.resize);
                    window.addEventListener('keydown', function () {
                        myChart.dispatchAction({
                        type: 'lines3DToggleEffect',
                        seriesIndex: 0
                        });
                    });
                /* }); */
                return false;
            break;
            case 'card' :
            case 'card_vertical' :
                var $elem = $('#' + elemId);
                if ($elem.length) {
                    
                    var html = [], cardAmountValue = 0;
                    
                    if (data.hasOwnProperty(0)) {
                        var dataRow = data[0];
                        if (dataRow.hasOwnProperty(value)) {
                            cardAmountValue = number_format(dataRow[value], 2, '.', ',');
                        }
                    }
                    
                    if (isRunInterval) {
                        var $card = $elem.closest('.bl-section'), $amountValue = $card.find('.kpi-card-amount-value');
                        
                        if ($amountValue.length) {
                            $amountValue.text(cardAmountValue);
                            return;
                        }
                    }
                    
                    var labelText = chartConfig.labelText;
                    
                    if (columnsConfig.hasOwnProperty('labelText') && columnsConfig.labelText != '') {
                        labelText = columnsConfig.labelText;
                    }
    
                    var cardConfigStyle = 'style="';
    
                    if (chartConfig.bgColor != null && chartConfig.bgColor != '') {
                        cardConfigStyle += 'background-color: '+chartConfig.bgColor + '; ';
                    }
    
                    if (chartConfig.grid_width != null && chartConfig.grid_width) {
                        cardConfigStyle += 'min-width: ' + chartConfig.grid_width + 'px; max-width: max-content !important;';
                    }
    
                    if (chartConfig.grid_height != null && chartConfig.grid_height) {
                        cardConfigStyle += 'min-height: ' + chartConfig.grid_height + 'px; max-height: max-content !important;';
                    }
    
                    if (chartConfig.grid_borderradius != null && chartConfig.grid_borderradius) {
                        cardConfigStyle += 'border-radius: '+ chartConfig.grid_borderradius +'px !important; ';
                    }
    
                    if (chartConfig.grid_left != null && chartConfig.grid_left) {
                        cardConfigStyle += 'padding-left: '+ chartConfig.grid_left +'px !important; ';
                    }
    
                    if (chartConfig.grid_right != null && chartConfig.grid_right) {
                        cardConfigStyle += 'padding-right: '+ chartConfig.grid_right +'px !important; ';
                    }
    
                    if (chartConfig.grid_top != null && chartConfig.grid_top) {
                        cardConfigStyle += 'padding-top: '+ chartConfig.grid_top +'px !important; ';
                    }
    
                    if (chartConfig.grid_bottom != null && chartConfig.grid_bottom) {
                        cardConfigStyle += 'padding-bottom: '+ chartConfig.grid_bottom +'px !important; ';
                    }
    
                    if (chartConfig.grid_alignment != null && chartConfig.grid_alignment) {
                        cardConfigStyle += 'text-align: '+ chartConfig.grid_alignment +' !important; ';
                    }
    
                    cardConfigStyle += '"';
    
                    if (chartConfig.bgColor != null && chartConfig.bgColor != '') {
                        html.push('<div class="card card-body bg-blue-400 p-2 pl15 pl15" '+ cardConfigStyle +'>');
                    } else {
                        html.push('<div class="card card-body p-2 pl15 pl15" '+ cardConfigStyle +'>');
                    }
                    
                    if (type === 'card') {
                        html.push('<div class="media">');
                            html.push('<div class="media-body">');
                                html.push('<h3 class="mb-0 kpi-card-amount-value">'+cardAmountValue+'</h3>');
                                html.push('<span class="text-uppercase font-size-xs">'+labelText+'</span>');
                            html.push('</div>');
                            
                            if (chartConfig.iconName != null && chartConfig.iconName != '') {
                                
                                html.push('<div class="ml-3 align-self-center">');
                                
                                if (chartConfig.bgColor != null && chartConfig.bgColor != '') {
                                    html.push('<i class="far '+chartConfig.iconName+' opacity-75" style="font-size: 48px"></i>');
                                } else {
                                    html.push('<i class="far '+chartConfig.iconName+' text-indigo-400" style="font-size: 48px"></i>');
                                }
                                
                                html.push('</div>');
                            }
                        
                        html.push('</div>');
                    } else {
                        var iconTextStyle = 'style="',
                            footerTextStyle = 'style="',
                            headerTextStyle = 'style="';
    
                        /* icon text style begin */
                        if (chartConfig.iconFontSize != null && chartConfig.iconFontSize) {
                            iconTextStyle += 'font-size: '+ chartConfig.iconFontSize +'px; ';
                            
                        } else {
                            iconTextStyle += 'font-size: 48px;';
                        }
                        
                        if (chartConfig.iconLeftPadding != null && chartConfig.iconLeftPadding) {
                            iconTextStyle += 'padding-left: '+ chartConfig.iconLeftPadding +'px !important; ';
                        }
    
                        if (chartConfig.iconRightPadding != null && chartConfig.iconRightPadding) {
                            iconTextStyle += 'padding-right: '+ chartConfig.iconRightPadding +'px !important; ';
                        }
    
                        if (chartConfig.iconTopPadding != null && chartConfig.iconTopPadding) {
                            iconTextStyle += 'padding-top: '+ chartConfig.iconTopPadding +'px !important; ';
                        }
    
                        if (chartConfig.iconBottomPadding != null && chartConfig.iconBottomPadding) {
                            iconTextStyle += 'padding-bottom: '+ chartConfig.iconBottomPadding +'px !important; ';
                        }
    
                        if (chartConfig.iconAlignment != null && chartConfig.iconAlignment) {
                            iconTextStyle += 'text-align: '+ chartConfig.iconAlignment +' !important; ';
                        }
                        /* header text style end */
    
                        /* header text style begin */
                        if (chartConfig.headFontSize != null && chartConfig.headFontSize) {
                            headerTextStyle += 'font-size: '+ chartConfig.headFontSize +'px"';
                        }
                        
                        if (chartConfig.headLeftPadding != null && chartConfig.headLeftPadding) {
                            headerTextStyle += 'padding-left: '+ chartConfig.headLeftPadding +'px !important; ';
                        }
    
                        if (chartConfig.headRightPadding != null && chartConfig.headRightPadding) {
                            headerTextStyle += 'padding-right: '+ chartConfig.headRightPadding +'px !important; ';
                        }
    
                        if (chartConfig.headTopPadding != null && chartConfig.headTopPadding) {
                            headerTextStyle += 'padding-top: '+ chartConfig.headTopPadding +'px !important; ';
                        }
    
                        if (chartConfig.headBottomPadding != null && chartConfig.headBottomPadding) {
                            headerTextStyle += 'padding-bottom: '+ chartConfig.headBottomPadding +'px !important; ';
                        }
    
                        if (chartConfig.headAlignment != null && chartConfig.headAlignment) {
                            headerTextStyle += 'text-align: '+ chartConfig.headAlignment +' !important; ';
                        }
                        /* header text style end */
    
                        /* footer text style begin */
                        if (chartConfig.footFontSize != null && chartConfig.footFontSize) {
                            footerTextStyle += 'font-size: '+ chartConfig.footFontSize +'px"';
                        }
                        
                        if (chartConfig.footLeftPadding != null && chartConfig.footLeftPadding) {
                            footerTextStyle += 'padding-left: '+ chartConfig.footLeftPadding +'px !important; ';
                        }
    
                        if (chartConfig.footRightPadding != null && chartConfig.footRightPadding) {
                            footerTextStyle += 'padding-right: '+ chartConfig.footRightPadding +'px !important; ';
                        }
    
                        if (chartConfig.footTopPadding != null && chartConfig.footTopPadding) {
                            footerTextStyle += 'padding-top: '+ chartConfig.footTopPadding +'px !important; ';
                        }
    
                        if (chartConfig.footBottomPadding != null && chartConfig.footBottomPadding) {
                            footerTextStyle += 'padding-bottom: '+ chartConfig.footBottomPadding +'px !important; ';
                        }
    
                        if (chartConfig.footAlignment != null && chartConfig.footAlignment) {
                            footerTextStyle += 'text-align: '+ chartConfig.footAlignment +' !important; ';
                        }
                        /* footer text style end */
    
                        iconTextStyle += '"', footerTextStyle += '"', headerTextStyle += '"';
    
                        html.push('<div class="media">');
                            html.push('<div class="media-body" style="display: grid">');
                            
                                if (chartConfig.iconName != null && chartConfig.iconName != '') {
                                    
                                    html.push('<div class="align-self-center">');
                                    
                                    if (chartConfig.bgColor != null && chartConfig.bgColor != '') {
                                        html.push('<i class="far '+chartConfig.iconName+' opacity-75" '+ iconTextStyle +'></i>');
                                    } else {
                                        html.push('<i class="far '+chartConfig.iconName+' text-indigo-400" '+ iconTextStyle +'></i>');
                                    }
                                    
                                    html.push('</div>');
                                }
    
                                html.push('<span class="text-uppercase font-size-xs" '+ headerTextStyle +'>'+labelText+'</span>');
                                html.push('<h3 class="mb-0 kpi-card-amount-value" '+ footerTextStyle +'>'+cardAmountValue+'</h3>');
                            html.push('</div>');
                            
                        
                        html.push('</div>');
                    }
                    html.push('</div>');
                    
                    var cardHtml = html.join('');
                    
                    if ($elem.hasClass('card-body')) {
                        
                        var $card = $elem.closest('.card');
                        
                        $card.addClass('d-none');
                        $card.after(cardHtml);
                        
                    } else {
                        $elem.empty().append(cardHtml);
                    }
                }
                
                return false;
                break;
            case 'radar':
                option = {
                    ...option,
                    radar: {
                        /* indicator: [
                        { name: 'Sales', max: 6500 },
                        { name: 'Administration', max: 16000 },
                        { name: 'Information Technology', max: 30000 },
                        { name: 'Customer Support', max: 38000 },
                        { name: 'Development', max: 52000 },
                        { name: 'Marketing', max: 25000 }
                        ] */
                        indicator: dataSet
                    },
                    series: [
                        {
                        name: 'Budget vs spending',
                        type: 'radar',
                        data: dataSet
                        }
                    ]
                }
                break;
            case 'pie':
                option = {
                    ...option,
                    series:{
                        name: 'Access From',
                        type: type,
                        radius: '50%',
                        data: dataSet,
                        emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                        }
                    },
                }; 
                break;
            case 'barPolar':
                option = {
                    polar: {
                    radius: [30, '80%']
                    },
                    radiusAxis: {
                    max: 4
                    },
                    angleAxis: {
                    type: 'category',
                    data: obj.dataXaxis,
                    startAngle: 75
                    },
                    series: {
                        data: dataSet,
                        type: 'bar',
                        showBackground: true,
                        coordinateSystem: 'polar',
                        backgroundStyle: {
                            color: 'rgba(180, 180, 180, 0.2)'
                        },
                        label: {
                            show: true,
                            position: 'middle',
                            formatter: '{b}: {c}'
                        }
                    }
                };
                break;
            case 'stacked_column':
            case 'clustered_column':
                type = 'bar';
            case 'tree_circle' : 
                option = {
                    /* tooltip: {
                        trigger: 'item',
                        triggerOn: 'mousemove'
                    }, */
                    series: [
                    {
                        type: 'tree',
                        data: dataSet,
                        top: '18%',
                        bottom: '14%',
                        layout: 'radial',
                        symbol: 'emptyCircle',
                        symbolSize: 7,
                        initialTreeDepth: 3,
                        animationDurationUpdate: 750,
                        emphasis: {
                        focus: 'descendant'
                        }
                    }
                    ]
                };
                break;
            case 'tree' : 
                
                var legendData = [];
                var _seriesData = [];
                var plusPercent = 100/(dataSet.length);
                var bottomPer = 100; var topPer = 0;
                
                $.each(dataSet, function (_i, _r) {
                    bottomPer -= plusPercent;
                    legendData.push({
                        name: plang.get('tree_structure') + ': ' + _i,
                        icon: 'rectangle'
                    });
    
                    _seriesData.push({
                        type: 'tree',
                        name: 'tree-' + _i ,
                        data: [_r],
                        top: topPer + '%',
                        left: '10%',
                        bottom: bottomPer + '%',
                        right: '15%',
                        symbolSize: 7,
                        label: {
                            position: 'left',
                            verticalAlign: 'middle',
                            align: 'right'
                        },
                        leaves: {
                            label: {
                                position: 'right',
                                verticalAlign: 'middle',
                                align: 'left'
                            }
                        },
                        expandAndCollapse: true,
                        emphasis: {
                            focus: 'descendant'
                        },
                        animationDuration: 550,
                        animationDurationUpdate: 750,
                        triggerEvent: true
                    });
                    topPer += plusPercent;
                });
                option = {
                    /* tooltip: {
                        trigger: 'item',
                        triggerOn: 'mousemove'
                    }, */
                    legend: {
                        top: '2%',
                        left: '3%',
                        orient: 'vertical',
                        data: legendData,
                        borderColor: '#c23531'
                    },
                    series: _seriesData
                };
                
                break;
            default:
    
                if (typeof chartConfig.smooth !== 'undefined' && chartConfig.smooth) {
                    series = {...series,
                        smooth: true
                    };
                }
                xAxis = {
                    ...xAxis,
                    data: obj.dataXaxis,
                }
                option = {...option,
                    yAxis: yAxis,
                    xAxis: xAxis,
                    series: series,
                };
                break;
        }
    
        if (chartConfig.bgColor) {
            option = {
                ...option,
                backgroundColor: chartConfig.bgColor,
            };
        }

        /* myChart.on('finished', function () {
            console.log('ffinished');
        }); */
        
        option && myChart.setOption(option);
        var jsonMinif = JSON.stringify(option);
        $mainSelector.find('.kpi-dm-chart-create').attr('data-config', jsonMinif);
        if (type === 'tree' || type === 'tree_circle') {
            var subContent = $('#' + elemId).parent().find('.subcontent');
            subContent.hide();
            myChart.on('click', params => {
                var _descriptionContent = '';
                _descriptionContent += '<table class="table table-bordered" style="background: #FFF;">';
                    _descriptionContent += '<tbody>';
                    _descriptionContent += '<tr><td style="background-color: #f5f5f5; width: 180px" colspan="2"><button type="button" class="btn btn-light bg-gray bg-grey-c0 border-0 p-1 pl-2 pr-2 text-white subcontent-collapse-btn"><i class="far fa-arrow-alt-to-right"></i></button></td></tr>';
                    $.each(params.data, function (n, o) {
                        var nlower = n.toLowerCase();
                        if (/* typeof o !== 'object' &&  */nlower.indexOf('id') === -1 && nlower !== 'name' && nlower !== 'children' && nlower !== 'value') {
                            _descriptionContent += '<tr data-cell-path="'+ n +'">';
                                _descriptionContent += '<td style="background-color: #f5f5f5; width: 180px">';
                                    _descriptionContent += '<label required="">'+ n + ' <span class="label-colon">:</span></label>';
                                _descriptionContent += '</td>';
                                _descriptionContent += '<td>'+ ((typeof o !== 'object') ? o : '') + '</td>';
                            _descriptionContent += '</tr>';
                        }
                    });
                    
                    _descriptionContent += '</tbody>';
                _descriptionContent += '</table>';
    
                subContent.empty().append(_descriptionContent).promise().done(function () {
                    subContent.show();
                });
            });
        }
        
        $mainSelector.find('textarea[name="addintionalConfig"]').val(JSON.stringify(option));

        if (typeof obj.isLayoutBuilder !== 'undefined' && obj.isLayoutBuilder) {
            obj.chartConfig = option;
            $('#' + elemId).attr('data-config', JSON.stringify(obj));
        }
        window.addEventListener("resize", myChart.resize);
    }

    var chartBuilderStatic = function (obj) {
         
        var elemId = obj.elemId;
        console.log(elemId);
        if (!elemId) return false;

        var $mainSelector = $('#' + elemId).closest('.theme-builder');
        var chartConfig = obj;
        var useData = obj.useData;
        var type = chartConfig.type;
        var chartName = (chartConfig.hasOwnProperty('chartName')) ? chartConfig.chartName : type + '_chart';
        var category = chartConfig.axisX;
        var value = chartConfig.axisY;
        var aggregate = chartConfig.aggregate;
        var columnsConfig = obj.columnsConfig;
        var data = obj.data;
        var isRunInterval = false;
        var isLineChartConfig = false;
        var kpiLayoutIndex = 0;
        obj.isLayoutBuilder = false;
    
        if (chartConfig.hasOwnProperty('lineChartConfig') && chartConfig.lineChartConfig) {
            isLineChartConfig = true;
        }
                
        if (obj.hasOwnProperty('isRunInterval') && obj.isRunInterval) {
    
            isRunInterval = true;
    
            if (kpIndicatorChart.hasOwnProperty(elemId)) {
                
                var prevChart = kpIndicatorChart[elemId];
                
                if (type == 'stacked_column') {
                    var stackedData = kpiDataMartStackedColumnData(data, category, value, chartConfig);
                    data = stackedData.data;
                }
                
                prevChart.data = data;
                return;
            }
        }
    
        var themeGroup;
    
        if ($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata')) {
            themeGroup = JSON.parse($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata'));
        }
        
        try {
            var chartDom = document.getElementById(elemId);
            var option;
            
        } catch (error) {
            console.log(error);
        }
        
        echarts.dispose(chartDom);
        var myChart = echarts.init(chartDom);
        var dataSet = data;
        var grid = {containLabel: true};
        option = {
            themeCode: chartConfig.themeCode,
        };
        option = convertOptions(chartConfig, option);
    
        var axisLabel = {},
            yAxis = {
                type: 'value',
            },
            xAxis = {
                type: 'category',
                label: axisLabel,
            };
        
        var seriesLabel = {position: 'middle', formatter: '{b}: {c}'};
    
        if (typeof chartConfig.axisLabelShow !== 'undefined') {
            axisLabel = {...axisLabel,
                show: chartConfig.axisLabelShow
            };
    
            seriesLabel = {...seriesLabel,
                show: chartConfig.axisLabelShow
            };
        }
        
        if (axisLabel.length > 0) {
            xAxis = {
                ...xAxis,
                label: axisLabel,
            }
        }
        
        if (typeof chartConfig.xyReverse !== 'undefined' && chartConfig.xyReverse) {
            var xAxisTmp = xAxis;
            xAxis = yAxis;
            yAxis = xAxisTmp;
        }
    
        console.log(option);
        if (typeof option.series !== 'undefined') {
            var series = {
                type: type,
                ...option.series
            };
            delete option.series;
            option = {
                ...option,
                series,
            };
        }
        
        if (chartConfig.bgColor) {
            option = {
                ...option,
                backgroundColor: chartConfig.bgColor,
            };
        }

        console.log(option);
        option && myChart.setOption(option);
        $mainSelector.find('textarea[name="addintionalConfig"]').val(JSON.stringify(option));

        if (typeof obj.isLayoutBuilder !== 'undefined' && obj.isLayoutBuilder) {
            obj.chartConfig = option;
            $('#' + elemId).attr('data-config', JSON.stringify(obj));
        }
        window.addEventListener("resize", myChart.resize);
    }
    
    var chartConfigrationToggle = function ($mainSelector, _dataConfigStr) {
        $mainSelector.find('.chartTypesConfigration, .chartTypesConfigration .configration').hide();
        var _dataConfig = _dataConfigStr.split('##');
        $.each(_dataConfig, function (key, conf) {
            var __config = conf.replace('.', '_');
            $mainSelector.find('.configration.' + __config).show();
            $mainSelector.find('.chartTypesConfigration.conf_' + __config).show();
        });
    };
     
    var kpiDataMartEChartSetOption = function (obj) {
        var elemId = obj.elemId;
        var $mainSelector = $('#' + elemId).closest('.theme-builder');
        var chartConfig = obj.chartConfig;
        var useData = obj.useData;
        var type = chartConfig.type;
        var chartName = (chartConfig.hasOwnProperty('chartName')) ? chartConfig.chartName : type + '_chart';
        var category = chartConfig.axisX;
        var value = chartConfig.axisY;
        var aggregate = chartConfig.aggregate;
        var columnsConfig = obj.columnsConfig;
        var data = obj.data;
        var isRunInterval = false;
        var isLineChartConfig = false;
        /* var axisYdataType = columnsConfig[value]; */
        
        var isLayoutBuilder = false, kpiLayoutIndex = 0;
        if ($mainSelector.attr('data-kpi-layout') !== '0') {
            isLayoutBuilder = true;
            var _parentSelector = $('#' + elemId).closest('.layout-builder-v0');
            kpiLayoutIndex = $mainSelector.attr('data-kpi-layout');
            elemId = _parentSelector.find('div.selected-box[data-kl-col="'+ kpiLayoutIndex +'"] [data-section-code="'+ kpiLayoutIndex +'"]').attr('id');
        }
    
        if (aggregate == 'COUNT' && (value == '' || value === null)) {
            value = 'COUNT_COL';
        }
                
        if (chartConfig.hasOwnProperty('lineChartConfig') && chartConfig.lineChartConfig) {
            isLineChartConfig = true;
        }
                
        if (obj.hasOwnProperty('isRunInterval') && obj.isRunInterval) {
    
            isRunInterval = true;
    
            if (kpIndicatorChart.hasOwnProperty(elemId)) {
                
                var prevChart = kpIndicatorChart[elemId];
                
                if (type == 'stacked_column') {
                    var stackedData = kpiDataMartStackedColumnData(data, category, value, chartConfig);
                    data = stackedData.data;
                }
                
                prevChart.data = data;
                return;
            }
        }
    
        var themeGroup;
    
        if ($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata')) {
            themeGroup = JSON.parse($mainSelector.find('.theme-plan-group.selected').attr('data-rowdata'));
        }
    
        if (chartConfig.chartType === 'card_vertical' || chartConfig.chartType === 'card') {
            kpiDataMartChartRender(obj)
            return false;
        }
    
        try {
            var chartDom = document.getElementById(elemId);
            var option;
            echarts.dispose(chartDom);
            
            var myChart = echarts.init(chartDom);
    
            option = JSON.parse(chartConfig.buildCharConfig);
            option && myChart.setOption(option);
            
            var jsonMinif = JSON.stringify(option);
            $mainSelector.find('.kpi-dm-chart-create').attr('data-config', jsonMinif);
    
            if (typeof obj.isLayoutBuilder !== 'undefined' && obj.isLayoutBuilder) {
                /* if (typeof chartConfig.grid.height !== 'undefined') {
                    myChart.resize({
                        width: chartConfig.grid.width,
                        height: chartConfig.grid.height
                    });
                } */
            }
    
            window.addEventListener("resize", myChart.resize);
        } catch (error) {
            console.log(error);
        }
    
    };
     
    return {
        init: function(uniqId) {
            /* init('#layout-builder'+ uniqId); */
        },
        chartRender: function (obj) {
            chartRender(obj);
        },
        chartBuilderStatic: function (obj) {
            chartBuilderStatic(obj);
        },
        chartConfigrationToggle: function ($mainSelector, _dataConfigStr) {
            chartConfigrationToggle($mainSelector, _dataConfigStr);
        }
    };
} ();