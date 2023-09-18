var isKpiAddonScript = true;
var kpiDataMartArrowStyle = 'Flowchart'; /*[Straight, Flowchart, Bezier, StateMachine]*/
var kpiIndicatorAttrs = [];
var kpiDataMartConnectConfig = {
    connector: [kpiDataMartArrowStyle, {stub: [10, 20], gap: 10, cornerRadius: 5, alwaysRespectStubs: true}], 
    paintStyle: {strokeStyle: "#5c96bc", lineWidth: 2, outlineColor: "#fff", outlineWidth: 2, radius: 5},
    hoverPaintStyle: {fillStyle: "#77ca00", strokeStyle: "#77ca00", lineWidth: 5},
    dragOptions: {cursor: 'pointer'}
};

function kpiExport(elem, processMetaDataId, dataViewId, paramData) {
    
    Core.blockUI({boxed: true, message: 'Exporting...'});
    
    if (paramData.hasOwnProperty('objectCode')) {
        var objectCode = paramData.objectCode;
        var postData = paramData;
    } else {
        var postData = paramDataToObject(paramData);
        var objectCode = postData.hasOwnProperty('objectCode') ? postData.objectCode : '';
    }
    
    var selectedRows = getDataViewSelectedRows(dataViewId);
    postData['selectedRows'] = selectedRows;
    
    if (objectCode == 'kpiindicatorall') {
        
        var dialogName = '#dialog-kpiindicatorall-export';
        if (!$(dialogName).length) {
            $('<div id="' + dialogName.replace('#', '') + '"></div>').appendTo('body');
        }
        var $dialog = $(dialogName), form = [];

        form.push('<form method="post" enctype="multipart/form-data">');
            form.push('<div class="col-md-12 xs-form">');

                form.push('<div class="form-group row mt10 mb-2">');
                    form.push('<label class="col-form-label col-md-5 text-right">Өгөгдөлтэй хамт татах эсэх:</label>');
                    form.push('<div class="col-md-7 pl0">');
                        form.push('<input type="checkbox" name="isConfirmData">');
                    form.push('</div>');
                form.push('</div>');

            form.push('</div>');
        form.push('</form>');

        $dialog.html(form.join(''));
        $dialog.dialog({
            cache: false,
            resizable: true,
            bgiframe: true,
            autoOpen: false,
            title: 'Data export', 
            width: 450,
            height: 'auto',
            modal: true,
            close: function () {
                $dialog.empty().dialog('destroy').remove();
            },
            buttons: [
                {text: plang.get('export_btn'), class: 'btn green-meadow btn-sm', click: function() {

                    PNotify.removeAll();
                    Core.blockUI({boxed: true, message: 'Exporting...'});
                    
                    postData.isConfirmedData = 1;
                    
                    if ($dialog.find('input[name="isConfirmData"]').is(':checked')) {
                        postData.dataTableName = ['KPI_INDICATOR'];
                    } 
                    
                    $dialog.dialog('close');
                    
                    $.fileDownload(URL_APP + 'mdupgrade/exportKpiIndicator', {
                        httpMethod: 'POST', 
                        data: postData
                    }).done(function(){
                        PNotify.removeAll();
                        new PNotify({
                            title: 'Success',
                            text: 'Амжилттай татагдлаа',
                            type: 'success',
                            sticker: false,
                            hide: true,
                            addclass: pnotifyPosition
                        });
                        Core.unblockUI();
                    }).fail(function (msg, url) {
                        PNotify.removeAll();
                        new PNotify({
                            title: 'Error',
                            text: msg, 
                            type: 'error',
                            sticker: false, 
                            addclass: pnotifyPosition
                        });
                        Core.unblockUI();
                    });  
                }},
                {text: plang.get('close_btn'), class: 'btn blue-madison btn-sm', click: function () {
                    $dialog.dialog('close');
                }}
            ]
        });
        Core.initUniform($dialog);
        $dialog.dialog('open');
        Core.unblockUI();
        
    } else if (objectCode == 'kpiindicator' && !postData.hasOwnProperty('dataTableName')) {
        
        $.fileDownload(URL_APP + 'mdupgrade/exportKpiIndicator', {
            httpMethod: 'POST', 
            data: postData, 
        }).done(function(){
            PNotify.removeAll();
            new PNotify({
                title: 'Success',
                text: 'Амжилттай татагдлаа',
                type: 'success',
                sticker: false,
                hide: true,
                addclass: pnotifyPosition
            });
            Core.unblockUI();
        }).fail(function (msg, url) {
            
            PNotify.removeAll();
                
            if (msg.indexOf('confirmDataExport') !== -1) {

                var dataCountArr = msg.split('|'), dataCount = dataCountArr[1];
                var dialogName = '#dialog-kpiindicatorvalue-export';
                if (!$(dialogName).length) {
                    $('<div id="' + dialogName.replace('#', '') + '"></div>').appendTo('body');
                }
                var $dialog = $(dialogName), form = [];

                form.push('<form method="post" enctype="multipart/form-data">');
                    form.push('<div class="col-md-12 xs-form">');

                        form.push('<div class="form-group row mt10">');
                            form.push('<label class="col-form-label col-md-5 text-right">Өгөгдлийн тоо:</label>');
                            form.push('<div class="col-md-7 font-weight-bold">');
                                form.push(dataCount);
                            form.push('</div>');
                        form.push('</div>');

                        form.push('<div class="form-group row mt10 mb-2">');
                            form.push('<label class="col-form-label col-md-5 text-right">Өгөгдөлтэй хамт татах эсэх:</label>');
                            form.push('<div class="col-md-7 pl0">');
                                form.push('<input type="checkbox" name="isConfirmData">');
                            form.push('</div>');
                        form.push('</div>');

                    form.push('</div>');
                form.push('</form>');

                $dialog.html(form.join(''));
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: 'Data export', 
                    width: 450,
                    height: 'auto',
                    modal: true,
                    buttons: [
                        {text: plang.get('export_btn'), class: 'btn green-meadow btn-sm', click: function() {

                            PNotify.removeAll();
                            $dialog.dialog('close');

                            if ($dialog.find('input[name="isConfirmData"]').is(':checked')) {
                                postData.dataTableName = ['KPI_INDICATOR'];
                            } else {
                                postData.dataTableName = '';
                            }

                            kpiExport(elem, processMetaDataId, dataViewId, postData);
                        }},
                        {text: plang.get('close_btn'), class: 'btn blue-madison btn-sm', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                Core.initUniform($dialog);
                $dialog.dialog('open');

            } else {
                new PNotify({
                    title: 'Error',
                    text: msg, 
                    type: 'error',
                    sticker: false, 
                    addclass: pnotifyPosition
                });
            }
            Core.unblockUI();
        });     
        
    } else {
        
        $.fileDownload(URL_APP + 'mdupgrade/exportObject', {
            httpMethod: 'POST', 
            data: postData, 
        }).done(function(){
            PNotify.removeAll();
            new PNotify({
                title: 'Success',
                text: 'Амжилттай татагдлаа',
                type: 'success',
                sticker: false,
                hide: true,
                addclass: pnotifyPosition
            });
            Core.unblockUI();
        }).fail(function (msg, url) {
            PNotify.removeAll();
            new PNotify({
                title: 'Error',
                text: msg, 
                type: 'error',
                sticker: false, 
                addclass: pnotifyPosition
            });
            Core.unblockUI();
        });   
    }
}
function kpiImport(elem, dataViewId) {
    var $dialogName = 'dialog-object-import';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    var $dialog = $('#' + $dialogName);
    
    $.ajax({
        type: 'post',
        url: 'mdupgrade/importMeta',
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            PNotify.removeAll();

            $dialog.empty().append(data.html);
            $dialog.dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: 'PHP Object импорт',
                width: 700,
                height: 'auto',
                modal: true,
                close: function () {
                    $dialog.empty().dialog('destroy').remove();
                },
                buttons: [{
                    text: data.import_btn, class: 'btn btn-sm green', click: function () {
                        $("#newImportForm").validate({errorPlacement: function () {}});
                        
                        if ($("#newImportForm").valid()) {
                            $('#newImportForm').ajaxSubmit({
                                type: 'post',
                                url: 'mdupgrade/importMetaFile',
                                dataType: 'json',
                                beforeSend: function () {
                                    Core.blockUI({message: 'Түр хүлээнэ үү...', boxed: true});
                                },
                                success: function (data) {

                                    PNotify.removeAll();
                                    new PNotify({
                                        title: data.status,
                                        text: data.message,
                                        type: data.status,
                                        sticker: false, 
                                        hide: true,  
                                        addclass: pnotifyPosition,
                                        delay: 1000000000
                                    });

                                    if (data.status == 'success') {
                                        $dialog.dialog('close');
                                        dataViewReload(dataViewId);
                                    } 

                                    Core.unblockUI();
                                }
                            });
                        }
                    }},
                    {text: data.close_btn, class: 'btn btn-sm blue-hoki', click: function () {
                        $dialog.dialog('close');
                    }}
                ]
            });
            $dialog.dialog('open');
            
            Core.unblockUI();
        },
        error: function () { alert('Error'); }
    });
}

function kpiDmMartRelationTreeChartInit(elem, id) {
    var $dialogName = 'dialog-kpirelation-treechart';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    var $dialog = $('#' + $dialogName);
    
    if ($("link[href='middleware/assets/css/addon/d3treegraph.css?v=4']").length == 0) {
        $('head').append('<link rel="stylesheet" type="text/css" href="middleware/assets/css/addon/d3treegraph.css?v=4"/>');
    }
    
    $dialog.empty().append('<div id="kpi-tree-container"></div>');
    $dialog.dialog({
        cache: false,
        resizable: true,
        bgiframe: true,
        autoOpen: false,
        dialogClass: 'no-padding-dialog',
        title: plang.get('kpi_treegraph_view'),
        width: $(window).width() - 10,
        height: $(window).height() - 10,
        modal: true, 
        open: function() {
            kpiTreeChartInit(id);
        }, 
        close: function() {
            $dialog.empty().dialog('destroy').remove();
        },
        buttons: [
            {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function () {
                $dialog.dialog('close');
            }}
        ]
    });
    $dialog.dialog('open');
}

function kpiTreeChartInit(recordId) {
    
    $.ajax({
        type: 'post',
        url: 'mdform/kpiDmMartTreeGraph',
        data: {recordId: recordId}, 
        dataType: 'json',
        success: function (treeData) {

            // Calculate total nodes, max label length
            var totalNodes = 0;
            var maxLabelLength = 0;
            // variables for drag/drop
            var selectedNode = null;
            var draggingNode = null;
            // panning variables
            var panSpeed = 200;
            var panBoundary = 20; // Within 20px from edges will pan when dragging.
            // Misc. variables
            var i = 0;
            var duration = 750;
            var root;

            // size of the diagram
            var viewerWidth = $(window).width() - 10;
            var viewerHeight = $(window).height() - 88;
            
            var $treeContainer = $('#kpi-tree-container');
            if ($treeContainer.hasAttr('data-width')) {
                viewerWidth = $treeContainer.attr('data-width');
            }
            
            var tree = d3.layout.tree().size([viewerHeight, viewerWidth]);

            // define a d3 diagonal projection for use by the node paths later on.
            var diagonal = d3.svg.diagonal().projection(function(d) {
                return [d.y, d.x];
            });

            // A recursive helper function for performing some setup by walking through all nodes

            function visit(parent, visitFn, childrenFn) {
                if (!parent) return;

                visitFn(parent);

                var children = childrenFn(parent);
                if (children) {
                    var count = children.length;
                    for (var i = 0; i < count; i++) {
                        visit(children[i], visitFn, childrenFn);
                    }
                }
            }

            // Call visit function to establish maxLabelLength
            visit(treeData, function(d) {
                totalNodes++;
                maxLabelLength = Math.max(d.name.length, maxLabelLength);

            }, function(d) {
                return d.children && d.children.length > 0 ? d.children : null;
            });

            // TODO: Pan function, can be better implemented.

            function pan(domNode, direction) {
                var speed = panSpeed;
                if (panTimer) {
                    clearTimeout(panTimer);
                    translateCoords = d3.transform(svgGroup.attr("transform"));
                    if (direction == 'left' || direction == 'right') {
                        translateX = direction == 'left' ? translateCoords.translate[0] + speed : translateCoords.translate[0] - speed;
                        translateY = translateCoords.translate[1];
                    } else if (direction == 'up' || direction == 'down') {
                        translateX = translateCoords.translate[0];
                        translateY = direction == 'up' ? translateCoords.translate[1] + speed : translateCoords.translate[1] - speed;
                    }
                    scaleX = translateCoords.scale[0];
                    scaleY = translateCoords.scale[1];
                    scale = zoomListener.scale();
                    svgGroup.transition().attr("transform", "translate(" + translateX + "," + translateY + ")scale(" + scale + ")");
                    d3.select(domNode).select('g.node').attr("transform", "translate(" + translateX + "," + translateY + ")");
                    zoomListener.scale(zoomListener.scale());
                    zoomListener.translate([translateX, translateY]);
                    panTimer = setTimeout(function() {
                        pan(domNode, speed, direction);
                    }, 50);
                }
            }

            // Define the zoom function for the zoomable tree

            function zoom() {
                svgGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
            }

            // define the zoomListener which calls the zoom function on the "zoom" event constrained within the scaleExtents
            var zoomListener = d3.behavior.zoom().scaleExtent([0.1, 3]).on("zoom", zoom);

            // define the baseSvg, attaching a class for styling and the zoomListener
            var baseSvg = d3.select("#kpi-tree-container").append("svg")
                .attr("width", viewerWidth)
                .attr("height", viewerHeight)
                .attr("class", "overlay")
                .call(zoomListener);

            var overCircle = function(d) {
                selectedNode = d;
                updateTempConnector();
            };
            var outCircle = function(d) {
                selectedNode = null;
                updateTempConnector();
            };

            // Function to update the temporary connector indicating dragging affiliation
            var updateTempConnector = function() {
                var data = [];
                if (draggingNode !== null && selectedNode !== null) {
                    // have to flip the source coordinates since we did this for the existing connectors on the original tree
                    data = [{
                        source: {
                            x: selectedNode.y0,
                            y: selectedNode.x0
                        },
                        target: {
                            x: draggingNode.y0,
                            y: draggingNode.x0
                        }
                    }];
                }
                var link = svgGroup.selectAll(".templink").data(data);

                link.enter().append("path")
                    .attr("class", "templink")
                    .attr("d", d3.svg.diagonal())
                    .attr('pointer-events', 'none');

                link.attr("d", d3.svg.diagonal());

                link.exit().remove();
            };

            // Function to center node when clicked/dropped so node doesn't get lost when collapsing/moving with large amount of children.

            function centerNode(source) {
                scale = zoomListener.scale();
                x = -source.y0;
                y = -source.x0;
                x = x * scale + viewerWidth / 2;
                y = y * scale + viewerHeight / 2;
                d3.select('g').transition()
                    .duration(duration)
                    .attr("transform", "translate(" + x + "," + y + ")scale(" + scale + ")");
                zoomListener.scale(scale);
                zoomListener.translate([x, y]);
            }

            // Helper functions for collapsing and expanding nodes.

            function collapse(d) {
                if (d.children) {
                    d._children = d.children;
                    d._children.forEach(collapse);
                    d.children = null;
                } else if (d.childcount) {
                    d._childcount = d.childcount;
                    d.childcount = 0;
                }
            }

            // Toggle children function

            function toggleChildren(d) {

                if (d.children) {

                    d._children = d.children;
                    d.children = null;

                } else if (!d.children && !d._children) {

                    var postData = {
                        id: d.rid, 
                        templateId: d.templateId, 
                        relatedTemplateId: d.relatedTemplateId, 
                        relatedId: d.relatedId, 
                        arrowTemplateId: d.arrowTemplateId, 
                        ignoreId: recordId 
                    };

                    if (postData.relatedId && postData.relatedTemplateId) {

                        postData.id = postData.relatedId;
                        postData.templateId = postData.relatedTemplateId;

                        delete postData.relatedId;
                        delete postData.relatedTemplateId;

                    } else if (postData.id && postData.templateId) {

                        delete postData.relatedId;
                    }

                    $.ajax({
                        type: 'post',
                        url: 'mdform/kpiDmMartTreeGraph',
                        data: postData, 
                        dataType: 'json',
                        async: false, 
                        success: function (childObject) {
                            if (Object.keys(childObject).length) {
                                d.children = childObject;
                                d._childcount = 0;
                                d.childcount = 1;
                            }
                        }
                    });

                } else {

                    d.children = d._children;
                    d._children = null;
                }

                return d;
            }

            // Toggle children on click.

            function click(d) {
                if (d3.event.defaultPrevented) return; // click suppressed
                d = toggleChildren(d);
                update(d);
                centerNode(d);
            }

            function update(source) {
                // Compute the new height, function counts total children of root node and sets tree height accordingly.
                // This prevents the layout looking squashed when new nodes are made visible or looking sparse when nodes are removed
                // This makes the layout more consistent.
                var levelWidth = [1];
                var childCount = function(level, n) {

                    if (n.children && n.children.length > 0) {
                        if (levelWidth.length <= level + 1) levelWidth.push(0);

                        levelWidth[level + 1] += n.children.length;
                        n.children.forEach(function(d) {
                            childCount(level + 1, d);
                        });
                    }
                };
                childCount(0, root);
                var newHeight = d3.max(levelWidth) * 40; // 25 pixels per line  
                tree = tree.size([newHeight, viewerWidth]);

                // Compute the new tree layout.
                var nodes = tree.nodes(root).reverse(),
                    links = tree.links(nodes);

                // Set widths between levels based on maxLabelLength.
                nodes.forEach(function(d) {
                    //d.y = (d.depth * (maxLabelLength * 10)); //maxLabelLength * 10px
                    // alternatively to keep a fixed scale one can set a fixed depth per level
                    // Normalize for fixed-depth by commenting out below line
                    d.y = (d.depth * 250); //500px per level.
                });

                // Update the nodes
                node = svgGroup.selectAll("g.node").data(nodes, function(d) {
                    return d.id || (d.id = ++i);
                });

                // Enter any new nodes at the parent's previous position.
                var nodeEnter = node.enter().append("g")
                    .attr("class", "node")
                    .attr("transform", function(d) {
                        return "translate(" + source.y0 + "," + source.x0 + ")";
                    })
                    .on('click', click);

                nodeEnter.append("circle")
                        .attr('class', 'nodeCircle')
                        .attr("r", 0)
                        .style("fill", function(d) {
                            return d._children ? "lightsteelblue" : "#fff";
                        });

                /*nodeEnter.append("image")
                .attr("xlink:href", 'assets/custom/img/veritech_white.png')
                .attr("x", "-18px")
                .attr("y", "-20px")
                .attr("width", "40px")
                .attr("height", "40px");    */

                nodeEnter.append("text")
                    .attr("x", function(d) {
                        return d.children || d._children ? -15 : 15;
                    })
                    .attr("dy", ".35em")
                    .attr('class', 'nodeText')
                    .attr("text-anchor", function(d) {
                        return d.children || d._children ? "end" : "start";
                    })
                    .html(function(d) {
                        return d.name;
                    })
                    .style("fill-opacity", 0);

                nodeEnter.append('svg:foreignObject')
                    .attr("width", 20)
                    .attr("height", 20)
                    .attr("x", "-8px")
                    .attr("y", "-10px")
                    .html(function(d) {
                        return d.icon ? '<i class="'+d.icon+'"></i>' : '';
                    });

                // phantom node to give us mouseover in a radius around it
                nodeEnter.append("circle")
                    .attr('class', 'ghostCircle')
                    .attr("r", 30)
                    .attr("opacity", 0.2) // change this to zero to hide the target area
                    .style("fill", "red")
                    .attr('pointer-events', 'mouseover')
                    .on("mouseover", function(node) {
                        overCircle(node);
                    })
                    .on("mouseout", function(node) {
                        outCircle(node);
                    });

                // Update the text to reflect whether node has children or not.
                node.select('text.nodeText')
                    .attr("x", function(d) {
                        //return d.children || d._children || d._childcount ? -20 : 20;
                        return d.depth ? 20 : -20;
                    })
                    .attr("text-anchor", function(d) {
                        //return d.children || d._children || d._childcount ? "end" : "start";
                        return d.depth ? 'start' : 'end';
                    })
                    .html(function(d) {
                        return d.name;
                    }).call(wrap, 100)
                    .append("svg:title").html(function(d) { return d.name; });

                // Change the circle fill depending on whether it has children and is collapsed
                node.select("circle.nodeCircle")
                    .attr("r", 15)
                    .style("stroke", function(d) {
                        if (d.color) {
                            return d.color;
                        }
                    })
                    .style("fill", function(d) {
                        return d._children || d._childcount ? "lightsteelblue" : "#fff";
                    });

                // Transition nodes to their new position.
                var nodeUpdate = node.transition()
                    .duration(duration)
                    .attr("transform", function(d) {
                        return "translate(" + d.y + "," + d.x + ")";
                    });

                // Fade the text in
                nodeUpdate.select("text.nodeText").style("fill-opacity", 1);

                // Transition exiting nodes to the parent's new position.
                var nodeExit = node.exit().transition()
                    .duration(duration)
                    .attr("transform", function(d) {
                        return "translate(" + source.y + "," + source.x + ")";
                    })
                    .remove();

                nodeExit.select("circle").attr("r", 0);

                nodeExit.select("text.nodeText").style("fill-opacity", 0);

                // Update the links
                var link = svgGroup.selectAll("path.link").data(links, function(d) {
                    return d.target.id;
                });

                // Enter any new links at the parent's previous position.
                link.enter().insert("path", "g")
                    .attr("class", "link")
                    .attr("d", function(d) {
                        var o = {
                            x: source.x0,
                            y: source.y0
                        };
                        return diagonal({
                            source: o,
                            target: o
                        });
                    });

                // Transition links to their new position.
                link.transition()
                    .duration(duration)
                    .attr("d", diagonal);

                // Transition exiting nodes to the parent's new position.
                link.exit().transition()
                    .duration(duration)
                    .attr("d", function(d) {
                        var o = {
                            x: source.x,
                            y: source.y
                        };
                        return diagonal({
                            source: o,
                            target: o
                        });
                    })
                    .remove();

                // Stash the old positions for transition.
                nodes.forEach(function(d) {
                    d.x0 = d.x;
                    d.y0 = d.y;
                });
            }

            // Append a group which holds all nodes and which the zoom Listener can act upon.
            var svgGroup = baseSvg.append("g");

            // Define the root
            root = treeData;
            root.x0 = viewerHeight / 2;
            root.y0 = 0;

            // Layout the tree initially and center on the root node.

            root.children.forEach(collapse);

            update(root);
            centerNode(root);
        }
    });
}

function wrap(text, width) {
    text.each(function () {

        var text = d3.select(this);
        
        if (text.node().getComputedTextLength() <= width) {
            return;
        }
        
        text.attr("dy", '0em').attr('y', '-0.2em');
        
        var words = text.text().split(/\s+/).reverse(),
            word,
            line = [],
            x = text.attr("x"),
            y = text.attr("y"),
            dy = 0, //parseFloat(text.attr("dy")),
            tspan = text.text(null)
                    .append("tspan")
                    .attr("x", x)
                    .attr("y", y)
                    .attr("dy", dy + "em"), n = 0;
        
        while (word = words.pop()) {
            
            line.push(word);
            tspan.text(line.join(" "));
            
            if (tspan.node().getComputedTextLength() > width) {
                
                n++;
                if (n > 1) { break; }
                
                line.pop();
                tspan.text(line.join(" "));
                line = [word];
                
                var lh = 0;
                if (n > 0) { lh = 1.1; }
                
                tspan = text.append("tspan")
                    .attr("x", x)
                    .attr("y", y)
                    .attr("dy", lh + "em")
                    .text(word);
            }
        }
    });
}

function kpiDmMartRelationTreeChartRenderInit(elem, width, id) {
    
    if ($("link[href='middleware/assets/css/addon/d3treegraph.css?v=4']").length == 0) {
        $('head').append('<link rel="stylesheet" type="text/css" href="middleware/assets/css/addon/d3treegraph.css?v=4"/>');
    }
    
    elem.empty().append('<div id="kpi-tree-container" data-width="'+width+'"></div>');
    kpiTreeChartInit(id);
}

function kpiIndicatorBpRun(elem, processMetaDataId, dataViewId, paramData) {
    var $dialogName = 'dialog-kpiindicatorbprun';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    var $dialog = $('#' + $dialogName);
    var paramObj = paramDataToObject(paramData);
    
    $.ajax({
        type: 'post',
        url: 'mdform/kpiIndicatorBpRunForm',
        data: paramObj, 
        beforeSend: function () {
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            PNotify.removeAll();

            $dialog.empty().append(data);
            $dialog.dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: 'KPI Indicator - Run process',
                width: 700,
                height: 'auto',
                modal: true,
                close: function () {
                    $dialog.empty().dialog('destroy').remove();
                },
                buttons: [{
                    text: plang.get('setting_run'), class: 'btn btn-sm green', click: function () {
                        var $form = $('#kpiIndicatorBpRunForm');
                        $form.validate({errorPlacement: function () {}});
                        
                        if ($form.valid()) {
                            
                            var selectedRows = getDataViewSelectedRows(paramObj.dataViewId), indicatorIds = '-';
                            
                            Core.blockUI({message: 'Loading... (<span id="kpi-indctr-bprun-count">1</span> / '+selectedRows.length+')', boxed: true});
                            
                            for (var s in selectedRows) {
                                indicatorIds += selectedRows[s][paramObj.id] + '-';
                            }
                            
                            var obj = {
                                fiscalPeriodId: bpGetLookupFieldValue($form, elem, 'fiscalPeriodId', 'id'), 
                                startDate: bpGetLookupFieldValue($form, elem, 'fiscalPeriodId', 'startdate'), 
                                endDate: bpGetLookupFieldValue($form, elem, 'fiscalPeriodId', 'enddate'), 
                                runBpDvId: paramObj.runBpDvId, 
                                doneWfmStatusId: paramObj.doneWfmStatusId, 
                                newWfmStatusId: paramObj.newWfmStatusId
                            };
                            
                            kpiIndicatorBpRunLoop(indicatorIds, 1, obj);
                        }
                    }},
                    {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function () {
                        $dialog.dialog('close');
                    }}
                ]
            });
            $dialog.dialog('open');
            
            Core.unblockUI();
        },
        error: function () { alert('Error'); }
    });
}
function kpiIndicatorBpRunLoop(indicatorIds, depth, obj) {
    var idsMatches = indicatorIds.match(/\-(.*?)\-/);

    if (idsMatches) {
        var indicatorId = idsMatches[1];
        
        if (indicatorId) {
            
            obj.indicatorId = indicatorId;
            
            $.ajax({
                type: 'post',
                url: 'mdform/kpiIndicatorBpRun',
                data: obj,
                dataType: 'json',
                success: function(data) {

                    if (data.status == 'success') {
                        
                        depth = depth + 1;
                        $('#kpi-indctr-bprun-count').text(depth);
                        
                        kpiIndicatorBpRunLoop(indicatorIds.replace('-' + indicatorId, ''), depth, obj);
                        
                    } else {
                        PNotify.removeAll();
                        new PNotify({
                            title: data.status,
                            text: data.message,
                            type: data.status,
                            sticker: false, 
                            hide: true,  
                            addclass: pnotifyPosition,
                            delay: 1000000000
                        });
                        Core.unblockUI();
                    }
                }
            });
        }
    } else {
        PNotify.removeAll();
        new PNotify({
            title: 'Success',
            text: 'Success',
            type: 'success',
            sticker: false, 
            hide: true,  
            addclass: pnotifyPosition,
            delay: 1000000000
        });
        Core.unblockUI();
        
        $('#dialog-kpiindicatorbprun').dialog('close');
    }
}

function kpiIndicatorTemplateConfig(elem, processMetaDataId, dataViewId, selectedRow) {
    if (typeof selectedRow == 'undefined' || (typeof selectedRow != 'undefined' && selectedRow.length == 0)) {
        alert(plang.get('msg_pls_list_select'));
        return;
    }
    
    $.ajax({
        type: 'post',
        url: 'mdform/kpiIndicatorTemplateConfig',
        data: {selectedRow: selectedRow}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            PNotify.removeAll();
            
            if (data.status == 'success') {
                
                var $dialogName = 'dialog-kpiindicatortemplateconfig';
                if (!$("#" + $dialogName).length) {
                    $('<div id="' + $dialogName + '"></div>').appendTo('body');
                }
                var $dialog = $('#' + $dialogName);
    
                $dialog.empty().append(data.html);
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: 'KPI Indicator template config',
                    width: 1300,
                    height: 'auto',
                    modal: true,
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: [
                        {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                $dialog.dialog('open');
            
            } else {
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    sticker: false, 
                    addclass: pnotifyPosition
                });
            }
            
            Core.unblockUI();
        },
        error: function () { alert('Error'); Core.unblockUI(); }
    });
}

function kpiDataMartRelationConfig(elem, processMetaDataId, dataViewId, selectedRow, paramData) {
    var id = selectedRow.id;
    var $dialogName = 'dialog-dmart-relationconfig';
    if (!$("#" + $dialogName).length) { $('<div id="' + $dialogName + '"></div>').appendTo('body'); }
    var $dialog = $('#' + $dialogName);
    
    kpiIndicatorAttrs = [];
    
    $.ajax({
        type: 'post',
        url: 'mdform/kpiDataMartRelationConfig',
        data: {id: id}, 
        dataType: 'json', 
        beforeSend: function(){
            if (!$("link[href='assets/custom/addon/plugins/jsplumb/css/style.v3.css']").length){
                $("head").append('<link rel="stylesheet" type="text/css" href="assets/custom/addon/plugins/jsplumb/css/style.v3.css"/>');
            }
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function (data) {
            
            if (data.status == 'success') {
                
                $dialog.dialog({
                    cache: false,
                    resizable: false,
                    bgiframe: true,
                    autoOpen: false,
                    title: plang.get('dmart_relation_title'),
                    width: $(window).width(),
                    height: $(window).height(),
                    modal: false,
                    open: function() {

                        disableScrolling();

                        $dialog.empty().append(data.html).promise().done(function() {

                            var setHeight = $(window).height() - 190;
                            var $editor = $('#datamart-editor');
                            
                            $('.heigh-editor').css({'height': setHeight, 'max-height': setHeight});
                            $editor.css({'height': setHeight - 2, 'max-height': setHeight - 2});

                            setKpiDataMartVisualObjects($editor, data.objects, data.objects.graphjson, false);
                            
                            Core.unblockUI();
                        });
                    }, 
                    close: function() {
                        enableScrolling();
                    }, 
                    buttons: [
                        {text: plang.get('save_btn'), class: 'btn btn-sm green bp-btn-save', click: function() {
                            saveKpiDataMartRelationConfig(elem, $dialog);
                        }},
                        {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki', click: function() {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                $dialog.dialog('open');
            
            } else {
                
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false
                });
                Core.unblockUI();
            }
        }
    });
}
function saveKpiDataMartRelationConfig(elem, $dialog) {
    
    Core.blockUI({message: 'Saving...', boxed: true});
                            
    var positions = [], objects = [], connections = [], columns = [], criterias = [], checkObjects = [],   
        getConnections = jsPlumb.getConnections(), $editor = $('#datamart-editor'), 
        $columns = $('.kpi-datamart-columns-config > tbody > tr'), 
        $criterias = $('.kpi-datamart-criterias-config > tbody > tr'), 
        $objects = $editor.find('.wfposition'), 
        mainIndicatorId = $('input[data-kpidatamart-id="1"]').val();

    $objects.each(function () {

        var $elem = $(this), objId = $elem.attr('id'), indicatorId = $elem.attr('data-indicatorid');

        positions.push({
            id: objId,
            top: $elem.css('top'),
            left: $elem.css('left')
        });
        
        if (!checkObjects.hasOwnProperty(indicatorId)) {
            
            objects.push({
                trgIndicatorId: indicatorId
            });
            
            checkObjects[indicatorId] = 1;
        }
    });
    
    if (getConnections.length) {

        $.each(getConnections, function (idx, connection) {

            var sourceId = connection.sourceId, targetId = connection.targetId;
            var $linkInput = $editor.find('textarea[name="'+sourceId+'_'+targetId+'"]');
            var srcIndicatorId = $linkInput.attr('data-src-indicatorid');
            var srcAliasName = $linkInput.attr('data-src-aliasname');
            var trgIndicatorId = $linkInput.attr('data-trg-indicatorid');
            var trgAliasName = $linkInput.attr('data-trg-aliasname');
            var joinType = $linkInput.attr('data-jointype');
            var relationOrder = $linkInput.attr('data-relation-order');
            var subDtls = JSON.parse($linkInput.val());
            var defaultValue = '';

            connections.push({
                mainIndicatorId: mainIndicatorId, 
                srcIndicatorId: srcIndicatorId, 
                srcAliasName: srcAliasName, 
                trgIndicatorId: trgIndicatorId, 
                trgAliasName: trgAliasName, 
                joinType: joinType,
                joinOrderNumber: relationOrder,
                defaultValue: defaultValue, 
                KPI_DATAMODEL_MAP_KEY_DTL: subDtls
            });
        });
        
    } else if (objects.length == 1) {
        
        var srcIndicatorId = $objects.attr('data-indicatorid');
        var srcAliasName = $objects.find('.bp-code').text();
        
        connections.push({
            mainIndicatorId: mainIndicatorId, 
            srcIndicatorId: srcIndicatorId, 
            srcAliasName: srcAliasName, 
            trgIndicatorId: '', 
            trgAliasName: '', 
            joinType: '',
            joinOrderNumber: '',
            defaultValue: ''
        });
    }
    
    if ($columns.length) {
        $columns.each(function() {
            
            var $row = $(this);
            var id = $row.find('input[data-field-name="id"]').val();
            var trg_indicator_id = $row.find('input[data-field-name="trg_indicator_id"]').val();
            var trg_indicator_map_id = $row.find('input[data-field-name="trg_indicator_map_id"]').val();
            var trg_alias_name = $row.find('input[data-field-name="trg_alias_name"]').val();
            
            var $aggregate = $row.find('select[data-field-name="aggregate"]');
            var $expression = $row.find('input[data-field-name="expression"]');
            var expression = '', aggregate = '';
            
            if ($aggregate.length) {
                aggregate = $aggregate.val();
            }
            
            if ($expression.length) {
                expression = $expression.val();
            }
            
            columns.push({
                id: id, 
                mainIndicatorId: mainIndicatorId, 
                trgIndicatorId: trg_indicator_id, 
                expressionString: expression, 
                trgIndicatorMapId: trg_indicator_map_id, 
                trgAliasName: trg_alias_name, 
                aggregateFunction: aggregate
            });
        });
    }
    
    if ($criterias.length) {
        $criterias.each(function() {
            
            var $row = $(this);
            var id = $row.find('input[data-field-name="id"]').val();
            var criteria_alias_name = $row.find('input[data-field-name="criteria_alias_name"]').val();
            var criteria_indicator_id = $row.find('input[data-field-name="criteria_indicator_id"]').val();
            var criteriaCriteria = ($row.find('input[data-field-name="criteriaCriteria"]').val()).trim();
            
            if (criteria_alias_name != '' && criteria_indicator_id != '' && criteriaCriteria != '') {
                
                criterias.push({
                    id: id, 
                    mainIndicatorId: mainIndicatorId, 
                    indicatorId: criteria_indicator_id, 
                    aliasName: criteria_alias_name, 
                    criteria: criteriaCriteria
                });
            }
        });
    }

    var postData = {
        id: mainIndicatorId,
        graphJson: JSON.stringify(positions), 
        objects: objects, 
        connections: connections, 
        columns: columns, 
        criterias: criterias
    };

    $.ajax({
        type: 'post',
        url: 'mdform/saveKpiDataMartRelationConfig',
        data: postData, 
        dataType: 'json', 
        success: function (data) {

            PNotify.removeAll();
            new PNotify({
                title: data.status,
                text: data.message,
                type: data.status,
                addclass: pnotifyPosition,
                sticker: false
            });
            
            if (data.status == 'success') {
                $dialog.dialog('close');
            }

            Core.unblockUI();
        }
    });
}
function kpiDataMartAddObject(elem) {
    dataViewSelectableGrid('nullmeta', '0', '16511984441409', 'multi', 'nullmeta', elem, 'kpiDataMartFillEditor');
}
function kpiDataMartFillEditor(metaDataCode, processMetaDataId, chooseType, elem, rows, paramRealPath, lookupMetaDataId, isMetaGroup) {
    var $editor = $('#datamart-editor');
    var wfIconClass = 'wfIconRectangle',
        wfIconType = 'rectangle',
        wfIconWidth = 160,
        wfIconHeight = 70,
        bpOrder = 0,
        wfIconAddPositionTop = 20,
        wfIconAddPostionLeft = 20;
    
    for (var k in rows) {
        
        var row = rows[k];
        bpOrder = parseInt(bpOrder) + 1;
        
        var aliasname = 'T' + (Number($editor.find('.wfposition').length) + 1);
        var tempWidth = (parseInt($editor.width()) - 470) - parseInt(wfIconAddPostionLeft);

        if (parseInt(tempWidth) < 0) {
            wfIconAddPostionLeft = 20;
            wfIconAddPositionTop = wfIconAddPositionTop + 120;
        }

        var wfIconArray = {
            id: row.id + '_' + aliasname, 
            code: aliasname, 
            title: row.name,
            indicatorId: row.id, 
            type: wfIconType,
            class: wfIconClass,
            positionTop: wfIconAddPositionTop,
            positionLeft: wfIconAddPostionLeft,
            width: wfIconWidth, 
            height: wfIconHeight, 
            colorCode: ''
        };

        $editor.append(setBoxKpiDataMartRelation(wfIconArray));
        wfIconAddPostionLeft = wfIconAddPostionLeft + 180;

        /*jsPlumb.detachEveryConnection();*/

        var $lastBox = $editor.find('.wfposition:last');

        setVisualKpiDataMartRelation($lastBox);
        kpiDataMartBoxDraggable($lastBox);
    }
    
    if ($editor.find('.wfposition').length == 1) {
        setKpiDataMartAliasCombo($editor);
    }
}
function setKpiDataMartVisualObjects($editor, objects, graphJson, isReadonly) {
    
    jsPlumb.detachEveryConnection();
    
    if (objects.hasOwnProperty('dtls') && objects.dtls && Object.keys(objects.dtls).length) {
        
        var graphObj = [], connections = [], isSavedPosition = false, 
            wfIconClass = 'wfIconRectangle', wfIconType = 'rectangle', 
            wfIconWidth = 160, wfIconHeight = 70, 
            wfIconAddPositionTop = 20, wfIconAddPostionLeft = 40, 
            templateList = objects.dtls;
    
        if (graphJson) {
            
            var graphObjs = JSON.parse(html_entity_decode(graphJson, "ENT_QUOTES"));

            for (var g in graphObjs) {
                graphObj[graphObjs[g]['id']] = {top: graphObjs[g]['top'], left: graphObjs[g]['left']};
            }
            
            isSavedPosition = true;
        }
        
        for (var k in templateList) {
            
            var row = templateList[k];
            
            if (row.typeid == '102') {
                
                connections.push(row);
                
            } else {
                
                var id = row.id + '_' + row.aliasname;
                
                if (!isSavedPosition || (isSavedPosition && typeof graphObj[id] === 'undefined')) {
                    
                    var tempWidth = (parseInt($editor.width()) - 470) - parseInt(wfIconAddPostionLeft);

                    if (parseInt(tempWidth) < 0) {
                        wfIconAddPositionTop = wfIconAddPositionTop + 120;
                        wfIconAddPostionLeft = 40;
                    }
                    
                } else {
                    wfIconAddPositionTop = (graphObj[id]['top']).replace('px', '');
                    wfIconAddPostionLeft = (graphObj[id]['left']).replace('px', '');
                }
                
                var wfIconArray = {
                    id: id,
                    code: row.aliasname, 
                    title: row.name,
                    indicatorId: row.id, 
                    type: wfIconType,
                    class: wfIconClass,
                    positionTop: wfIconAddPositionTop,
                    positionLeft: wfIconAddPostionLeft,
                    width: wfIconWidth,
                    height: wfIconHeight, 
                    colorCode: '', 
                    isReadonly: isReadonly
                };

                $editor.append(setBoxKpiDataMartRelation(wfIconArray));
                
                if (!isSavedPosition) {
                    wfIconAddPostionLeft = wfIconAddPostionLeft + 200;
                }

                var $lastBox = $editor.find('.wfposition:last');

                setVisualKpiDataMartRelation($lastBox);
                
                if (!isReadonly) {
                    kpiDataMartBoxDraggable($lastBox);
                }
            }
        }
        
        if (connections) {
            
            for (var c in connections) {
                
                var cRow = connections[c];
                var srcId = cRow.srcindicatorid + '_' + cRow.srcaliasname;
                var trgId = cRow.trgindicatorid + '_' + cRow.trgaliasname;
                
                if ($editor.find('#' + srcId).length && $editor.find('#' + trgId).length) {
                    
                    var connectObj = {source: srcId, target: trgId};
                    
                    if (cRow.name != '' && cRow.name != null) {
                        connectObj.overlays = [['Label', {label: cRow.name}]];
                    }
                    
                    jsPlumb.connect(connectObj, kpiDataMartConnectConfig);
                    
                    var inputAttr = 'name="'+srcId+'_'+trgId+'" '+
                        'data-name="'+cRow.name+'" '+
                        'data-jointype="'+cRow.jointype+'" '+
                        'data-relation-order="'+((cRow.hasOwnProperty('joinordernumber') && cRow.joinordernumber != null && cRow.joinordernumber != '') ? cRow.joinordernumber : '')+'" '+
                        'data-sourceid="'+srcId+'" '+
                        'data-targetid="'+trgId+'" '+
                        'data-src-aliasname="'+cRow.srcaliasname+'" '+
                        'data-trg-aliasname="'+cRow.trgaliasname+'" '+
                        'data-src-indicatorid="'+cRow.srcindicatorid+'" '+
                        'data-trg-indicatorid="'+cRow.trgindicatorid+'"';
                    
                    var relationDtlIds = cRow.relationdtlid;
                    
                    if (relationDtlIds.length) {
                        for (var r in relationDtlIds) {
                            relationDtlIds[r]['id'] = '';
                        }
                    }

                    $editor.append('<textarea class="d-none" '+inputAttr+'>'+JSON.stringify(relationDtlIds)+'</textarea>');
                }
            }
        }
        
        setKpiDataMartAliasCombo($editor);
    }
    
    return;
}

function setBoxKpiDataMartRelation(elem) {
    var _left = elem.positionLeft;
    var _top = elem.positionTop;
    var html = []; 
    
    html.push('<div id="' + elem.id + '" ' +
            'data-indicatorid="' + elem.indicatorId + '" ' +
            'class="wfposition wfdmart ' + elem.type + ' wfdmcolor-' + elem.colorCode + ' wfisreadonly-'+elem.isReadonly+'" ' +
            'style="top: ' + _top + 'px; left: ' + _left + 'px;">');
    
        html.push('<div class="wfIcon ' + elem.class + '" data-type="' + elem.type + '" ' +
                'data-top="' + elem.positionTop + '" data-left="' + elem.positionLeft + '" ' +
                'data-class="' + elem.class + '" data-title="' + elem.title + '">');

        html.push('<span class="iconText">');

        if (elem.type == 'rectangle') {
            html.push('<div class="bp-code">' + elem.code + '</div>');
            html.push('<div class="bp-name">' + elem.title + '</div>');
        }

        html.push('</span>');
        
        html.push('<div class="connect"></div>');
        html.push('</div>');
    html.push('</div>');
    
    return html.join('');
}

function setVisualKpiDataMartRelation(elem) {
    
    jsPlumb.importDefaults({
        ConnectionsDetachable: false,
        ReattachConnections: false,
        connector: [kpiDataMartArrowStyle, {stub: [10, 20], gap: 10, cornerRadius: 5, alwaysRespectStubs: true}],
        ConnectionOverlays: [["Arrow", {location: 0.99, width: 12, length: 10, foldback: 1}]],
        Endpoint: ["Dot", {radius: 6}]
    });

    jsPlumb.makeSource(elem, {
        filter: ".connect",
        anchor: "Continuous",
        isSource: true,
        isTarget: false,
        reattach: true,
        maxConnections: 99,
        connector: [kpiDataMartArrowStyle, {stub: [10, 20], gap: 10, cornerRadius: 1, alwaysRespectStubs: true}],
        connectorPaintStyle: {
            strokeStyle: "green",
            lineWidth: 2
        },
        connectorHoverPaintStyle: {
            strokeStyle: "#77ca00",
            outlineColor: "#77ca00",
            outlineWidth: 5
        },
        connectorStyle: {
            strokeStyle: "#5c96bc",
            lineWidth: 2,
            outlineColor: "#fff",
            outlineWidth: 2
        },
        paintStyle: {fillStyle: "transparent"},
        hoverPaintStyle: {fillStyle: "transparent", lineWidth: 5},
        Endpoint: ["Dot", {radius: 1}]
    });
    jsPlumb.makeTarget(elem, {
        isSource: false,
        isTarget: true,
        reattach: true,
        setDragAllowedWhenFull: true,
        dropOptions: {hoverClass: "dragHover"},
        anchor: "Continuous",
        connectorHoverPaintStyle: {
            strokeStyle: "#77ca00",
            outlineColor: "#77ca00",
            outlineWidth: 5
        },
        paintStyle: {fillStyle: "transparent"},
        hoverPaintStyle: {fillStyle: "#77ca00", strokeStyle: "#77ca00", lineWidth: 7}
    });
}

function kpiDataMartBoxDraggable(elem) {
    jsPlumb.draggable(elem, {
        containment: '#datamart-editor', 
        stop: function () {
            setBoxAttrKpiDataMartRelation($(this));
        }
    });
}
function setBoxAttrKpiDataMartRelation(elem) {
    elem.find('.wfIcon').attr({'data-top': elem.position().top, 'data-left': elem.position().left});
}

function kpiDataMartNewRelationConnect(info, data, $editor) {
    
    var sourceAttrs = data.sourceAttrs;
    var targetAttrs = data.targetAttrs;
    
    if (sourceAttrs.length == 0 || targetAttrs.length == 0) {
        PNotify.removeAll();
        new PNotify({
            title: 'Info',
            text: 'Темплейтийн талбарууд ирсэнгүй!',
            type: 'info',
            addclass: pnotifyPosition,
            sticker: false
        });
    }
    
    var $dialogName = 'dialog-dmart-relationconnect';
    if (!$("#" + $dialogName).length) { $('<div id="' + $dialogName + '"></div>').appendTo('body'); }
    var $dialog = $('#' + $dialogName);
    var $src = $editor.find('#' + info.sourceId);
    var $trg = $editor.find('#' + info.targetId);
    var sourceRows = '', targetRows = '', relationAttrs = '', sourceComboOption = '', 
        targetComboOption = '', relationOrder = '', joinType = '';
    var isEdit = false;
    var operatorComboOption = '<option value="=">=</option>'+
        '<option value="<"><</option>'+
        '<option value="<">></option>'+
        '<option value="<="><=</option>'+
        '<option value=">=">>=</option>';
    var joinTypeComboOption = '<option value="INNER">INNER</option>'+
        '<option value="LEFT">LEFT</option>';    
    
    for (var s in sourceAttrs) {
        
        if (sourceAttrs[s]['parentid']) {
            
            sourceRows += '<tr data-id="'+sourceAttrs[s]['id']+'" data-attr-row="source" class="cursor-pointer">'+
                '<td><a href="javascript:;" class="dmart-attrconnect-check" data-info="source"><i class="icon-circle text-success font-size-18"></i></a></td>'+    
                '<td>'+sourceAttrs[s]['columnname']+'</td>'+
                '<td>'+sourceAttrs[s]['labelname']+'</td>'+
                '<td>'+sourceAttrs[s]['showtype']+'</td>'+
            '</tr>';
    
            sourceComboOption += '<option value="'+sourceAttrs[s]['id']+'">'+sourceAttrs[s]['columnname']+' - '+sourceAttrs[s]['labelname']+'</option>';
        }
    }
    
    for (var t in targetAttrs) {
        
        if (targetAttrs[t]['parentid']) {
            
            targetRows += '<tr data-id="'+targetAttrs[t]['id']+'" data-attr-row="target" class="cursor-pointer">'+
                '<td><a href="javascript:;" class="dmart-attrconnect-check" data-info="target"><i class="icon-circle text-success font-size-18"></i></a></td>'+        
                '<td>'+targetAttrs[t]['columnname']+'</td>'+
                '<td>'+targetAttrs[t]['labelname']+'</td>'+
                '<td>'+targetAttrs[t]['showtype']+'</td>'+
            '</tr>';
    
            targetComboOption += '<option value="'+targetAttrs[t]['id']+'">'+targetAttrs[t]['columnname']+' - '+targetAttrs[t]['labelname']+'</option>';
        }
    }
    
    if (data.hasOwnProperty('isEdit') && data.isEdit) {
        
        var $linkInput = $editor.find('textarea[name="'+info.sourceId+'_'+info.targetId+'"]');
        var linkInputJsonStr = $linkInput.val();
        var relationObj = JSON.parse(linkInputJsonStr);
        
        joinType = $linkInput.attr('data-jointype');
        relationOrder = $linkInput.attr('data-relation-order');
                
        if (relationObj) {
                    
            for (var r in relationObj) {

                var relationRow = relationObj[r];
                
                relationRow.sourceComboOption = sourceComboOption;
                relationRow.targetComboOption = targetComboOption;
                relationRow.operatorComboOption = operatorComboOption;
                
                relationAttrs += setKpiDataMartRelationAttrs(relationRow);
            }
            
            isEdit = true;
        }
    }
    
    var html = 
            '<div data-relation-header="1">'+
                '<div class="row">'+
                    '<div class="col text-center">'+
                        '<a href="mdform/indicatorList/'+$src.attr('data-indicatorid')+'" target="_blank" class="font-weight-bold">' + $src.find('.wfIcon').attr('data-title') + '</a>' + 
                    '</div>'+
                    '<div class="col-md-auto" style="width: 50px; padding: 0">'+
                        '<input class="form-control form-control-sm font-weight-bold text-center longInit" data-relation-type="order" value="'+relationOrder+'"/>'+
                    '</div>'+
                    '<div class="col-md-auto" style="width: 100px; padding: 0">'+
                        '<select class="form-control form-control-sm" data-relation-type="jointype">'+joinTypeComboOption.replace('value="'+joinType+'"', 'value="'+joinType+'" selected')+'</select>'+
                    '</div>'+
                    '<div class="col text-center">'+
                        '<a href="mdform/indicatorList/'+$trg.attr('data-indicatorid')+'" target="_blank" class="font-weight-bold">' + $trg.find('.wfIcon').attr('data-title') + '</a>' + 
                    '</div>'+
                '</div>'+

                '<div class="row">'+
                    '<div class="col-md-12">'+
                        '<table class="table table-hover mt-2 mb-4" data-relation-tbl-list="1">'+
                            '<tbody>'+relationAttrs+'</tbody>'+
                        '</table>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            
            '<div class="row">'+
                '<div class="col" data-attr-tbl="1">'+
                    '<table class="table table-hover table-bordered">'+
                        '<thead>'+
                            '<tr>'+
                                '<th style="width: 30px"></th>'+
                                '<th>Код</th>'+
                                '<th>Нэр</th>'+
                                '<th>Төрөл</th>'+
                            '</tr>'+
                        '</thead>'+
                        '<tbody>'+sourceRows+'</tbody>'+
                    '</table>'+
                    '<input type="hidden" name="connectSourceFieldId" data-name=""/>'+
                '</div>'+
                '<div class="col-md-auto" style="width: 35px; padding: 0">'+
                    '<button type="button" class="btn btn-sm green" data-relation-type="add"><i class="far fa-arrow-up"></i></button>'+
                '</div>'+
                '<div class="col" data-attr-tbl="1">'+
                    '<table class="table table-hover table-bordered">'+
                        '<thead>'+
                            '<tr>'+
                                '<th style="width: 30px"></th>'+
                                '<th>Код</th>'+
                                '<th>Нэр</th>'+
                                '<th>Төрөл</th>'+
                            '</tr>'+
                        '</thead>'+
                        '<tbody>'+targetRows+'</tbody>'+
                    '</table>'+
                    '<input type="hidden" name="connectTargetFieldId" data-name=""/>'+
                '</div>'+
            '</div>';
    
    $dialog.html(html);
    $dialog.dialog({
        cache: false,
        resizable: false,
        bgiframe: true,
        autoOpen: false,
        title: 'Connect',
        width: 900,
        height: 'auto',
        modal: true,
        open: function () {
            Core.initLongInput($dialog);
            Core.initSelect2($dialog);
            setKpiDataMartAttrsMaxHeight($dialog);
            Core.unblockUI();
        },
        close: function () {
            PNotify.removeAll();
            $dialog.dialog('destroy').remove();
        },
        buttons: [
            {text: plang.get('save_btn'), class: 'btn btn-sm green-meadow bp-btn-save', click: function() {
                
                PNotify.removeAll();
                
                var $relationRows = $dialog.find('table[data-relation-tbl-list] > tbody > tr');
                
                if ($relationRows.length) {
                    
                    var relationAttrs = [];
                    
                    $relationRows.each(function() {
                        var $thisRow = $(this);
                        var relationAttr = {
                            srcindicatormapid: $thisRow.find('select[data-relation-type="src"]').val(), 
                            trgindicatormapid: $thisRow.find('select[data-relation-type="trg"]').val(), 
                            operatorname: $thisRow.find('select[data-relation-type="operator"]').val()
                        };
                        relationAttrs.push(relationAttr);
                    });
                    
                    var relationAttrsJsonStr = JSON.stringify(relationAttrs);
                    var relationJoinType = $dialog.find('select[data-relation-type="jointype"]').val();
                    var relationOrder = $dialog.find('input[data-relation-type="order"]').val();
                    
                    if (isEdit) {
                        
                        var $linkInput = $editor.find('textarea[name="'+info.sourceId+'_'+info.targetId+'"]');
                        
                        $linkInput.val(relationAttrsJsonStr);
                        $linkInput.attr({'data-jointype': relationJoinType, 'data-relation-order': relationOrder});
                        
                        $dialog.dialog('close'); 
                        
                    } else {
                    
                        var $sourceIndicatorElem = $editor.find('#' + info.sourceId);
                        var $targetIndicatorElem = $editor.find('#' + info.targetId);
                        var sourceIndicatorId = $sourceIndicatorElem.attr('data-indicatorid');
                        var targetIndicatorId = $targetIndicatorElem.attr('data-indicatorid');
                        var srcAliasName = $sourceIndicatorElem.find('.bp-code').text();
                        var trgAliasName = $targetIndicatorElem.find('.bp-code').text();

                        var inputAttr = 'name="'+info.sourceId+'_'+info.targetId+'" '+
                            'data-name="" '+
                            'data-jointype="'+relationJoinType+'" ' + 
                            'data-relation-order="'+relationOrder+'" ' + 
                            'data-sourceid="'+info.sourceId+'" '+
                            'data-targetid="'+info.targetId+'" '+
                            'data-src-aliasname="'+srcAliasName+'" '+
                            'data-trg-aliasname="'+trgAliasName+'" '+
                            'data-src-indicatorid="'+sourceIndicatorId+'" '+
                            'data-trg-indicatorid="'+targetIndicatorId+'"';

                        $editor.append('<textarea class="d-none" '+inputAttr+'>'+relationAttrsJsonStr+'</textarea>');

                        jsPlumb.connect({
                            source: info.sourceId,
                            target: info.targetId
                        }, kpiDataMartConnectConfig);

                        $dialog.dialog('close'); 

                        setTimeout(function() {
                            setKpiDataMartAliasCombo($editor);
                        }, 1);
                    }
                    
                } else {
                    new PNotify({
                        title: 'Info',
                        text: 'Та талбарыг сонгоно уу!',
                        type: 'info',
                        addclass: pnotifyPosition,
                        sticker: false
                    });
                }
            }},
            {text: plang.get('close_btn'), class: 'btn btn-sm blue-hoki bp-btn-close', click: function() { 
                $dialog.dialog('close'); 
            }}
        ]
    });
    $dialog.dialog('open');
    
    $dialog.on('click', 'button[data-relation-type="add"]', function() {
        var $connectSourceFieldId = $('input[name="connectSourceFieldId"]');
        var $connectTargetFieldId = $('input[name="connectTargetFieldId"]');
        var connectSourceFieldId = $connectSourceFieldId.val();
        var connectTargetFieldId = $connectTargetFieldId.val();
        
        PNotify.removeAll();
        
        if (connectSourceFieldId == '' || connectTargetFieldId == '') {
            new PNotify({
                title: 'Info',
                text: 'Та талбарыг сонгоно уу!',
                type: 'info',
                sticker: false,
                hide: true,
                addclass: pnotifyPosition
            });
        } else {
            
            var $tbl = $('table[data-relation-tbl-list="1"]');
            var relationRow = {
                srcindicatormapid: connectSourceFieldId, 
                trgindicatormapid: connectTargetFieldId, 
                operatorname: '=', 
                sourceComboOption: sourceComboOption, 
                targetComboOption: targetComboOption, 
                operatorComboOption: operatorComboOption
            };
            var relationAttrs = setKpiDataMartRelationAttrs(relationRow);
            
            $tbl.find('> tbody').append(relationAttrs);
            
            Core.initSelect2($tbl.find('> tbody > tr:last'));
            
            setKpiDataMartAttrsMaxHeight($dialog);
        }
    });
    
    $dialog.on('click', 'button[data-relation-type="remove"]', function() {
        $(this).closest('tr').remove();
        setKpiDataMartAttrsMaxHeight($dialog);
    });
    
    $dialog.on('click', 'tr[data-attr-row]', function() {
        var $row = $(this), info = $row.attr('data-attr-row'), $icon = $row.find('i');
        
        if ($icon.hasClass('icon-circle')) {
            
            var $tbody = $row.closest('tbody');
            var labelName = $row.find('td:eq(2)').text();
            
            $tbody.find('.icon-checkmark-circle2').removeClass('icon-checkmark-circle2').addClass('icon-circle');
            $icon.removeClass('icon-circle').addClass('icon-checkmark-circle2');
            
            $tbody.find('.table-info').removeClass('table-info');
            $row.addClass('table-info');
            
            if (info == 'source') {
                $('input[name="connectSourceFieldId"]').val($row.attr('data-id')).attr('data-name', labelName);
            } else {
                $('input[name="connectTargetFieldId"]').val($row.attr('data-id')).attr('data-name', labelName);
            }
            
        } else {
            
            $icon.removeClass('icon-checkmark-circle2').addClass('icon-circle');
            $row.removeClass('table-info');
            
            if (info == 'source') {
                $('input[name="connectSourceFieldId"]').val('').attr('data-name', '');
            } else {
                $('input[name="connectTargetFieldId"]').val('').attr('data-name', '');
            }
        }
    });
}
function setKpiDataMartAttrsMaxHeight($dialog) {
    var $panel = $dialog.find('[data-attr-tbl="1"]');
    if ($panel.length) {
        var headerHeight = $dialog.find('[data-relation-header="1"]').outerHeight(true);
        $panel.css({'overflow': 'auto', 'max-height': $(window).height() - headerHeight - 110});
    }
}

function setKpiDataMartRelationAttrs(relationRow) {
    var sourceComboOption = relationRow.sourceComboOption;
    var targetComboOption = relationRow.targetComboOption;
    var operatorComboOption = relationRow.operatorComboOption;
    
    var relationAttrs = '<tr>';
        relationAttrs += '<td style="width: 408px;padding: 3px 0;"><select class="form-control form-control-sm select2" data-relation-type="src">'+sourceComboOption.replace('value="'+relationRow.srcindicatormapid+'"', 'value="'+relationRow.srcindicatormapid+'" selected')+'</select></td>';
        relationAttrs += '<td style="width: 55px; text-align: center;padding: 3px 0;"><select class="form-control form-control-sm" data-relation-type="operator">'+operatorComboOption.replace('value="'+relationRow.operatorname+'"', 'value="'+relationRow.operatorname+'" selected')+'</select></td>';
        relationAttrs += '<td style="padding: 3px 0;">';
            relationAttrs += '<select class="form-control form-control-sm select2" data-relation-type="trg" style="float: left;width: 360px;margin-top: 1px;">'+targetComboOption.replace('value="'+relationRow.trgindicatormapid+'"', 'value="'+relationRow.trgindicatormapid+'" selected')+'</select>';
            relationAttrs += '<button type="button" class="btn btn-sm red float-right" data-relation-type="remove"><i class="far fa-trash"></i></button>';
        relationAttrs += '</td>';
    relationAttrs += '</tr>';
    
    return relationAttrs;
}

function getKpiIndicatorAttrs(indicatorId) {
    var trgComboAttrs = [];
                    
    trgComboAttrs.push('<option value="">- '+plang.get('select_btn')+' -</option>');

    $.ajax({
        type: 'post',
        url: 'mdform/getKpiIndicatorAttrs',
        data: {indicatorId: indicatorId}, 
        dataType: 'json', 
        async: false, 
        success: function(data) {

            $.each(data, function(i, value) {
                if (data[i]['parentid'] != '' && data[i]['parentid'] != null) {
                    trgComboAttrs.push('<option value="'+data[i]['id']+'">' + data[i]['columnname'] + ' - ' + data[i]['labelname'] + '</option>');
                }
            });
        }
    });
    
    return trgComboAttrs.join('');
}
function setKpiDataMartAliasCombo($editor) {
    
    var $objects = $editor.find('.wfposition');
    var $columnsTbl = $('.kpi-datamart-columns-config > tbody > tr');
    var comboDatas = [];
    
    comboDatas.push('<option value="">- '+plang.get('select_btn')+' -</option>');
    
    $objects.each(function() {
        var $this = $(this);
        var indicatorId = $this.attr('data-indicatorid');
        var aliasName = $this.find('.bp-code').text();
        var indicatorName = $this.find('.bp-name').text();
        
        comboDatas.push('<option value="'+aliasName+'_'+indicatorId+'">' + aliasName + ' - ' + indicatorName + '</option>');
        
        if (!kpiIndicatorAttrs.hasOwnProperty(indicatorId)) {
            kpiIndicatorAttrs[indicatorId] = getKpiIndicatorAttrs(indicatorId);
        }
    });
    
    var comboOptions = comboDatas.join('');
    
    $columnsTbl.each(function() {
        var $row = $(this);
        var $aliasCombo = $row.find('[data-field-name="aliasName"]');
        var trg_indicator_id = $row.find('[data-field-name="trg_indicator_id"]').val();
        var trg_alias_name = $row.find('[data-field-name="trg_alias_name"]').val();
        
        $aliasCombo.empty().append(comboOptions);
        
        if (trg_indicator_id != '' && trg_alias_name != '') {
            
            var $selectedOption = $aliasCombo.find('option[value="'+trg_alias_name+'_'+trg_indicator_id+'"]');
            
            if ($selectedOption.length) {
                
                $selectedOption.attr('selected', 'selected');
                
                var $trgColumnNameCombo = $row.find('[data-field-name="trgColumnName"]');
                var trg_indicator_map_id = $row.find('[data-field-name="trg_indicator_map_id"]').val();

                $trgColumnNameCombo.empty().append(kpiIndicatorAttrs[trg_indicator_id]);

                if (trg_indicator_map_id != '') {
                    $trgColumnNameCombo.find('option[value="'+trg_indicator_map_id+'"]').attr('selected', 'selected');
                }
                
            } else {
                $row.find('[data-field-name="trg_alias_name"]').val('');
                $row.find('[data-field-name="trg_indicator_id"]').val('');
                $row.find('[data-field-name="trg_indicator_map_id"]').val('');
                $row.find('[data-field-name="trgColumnName"]').val('');
                $row.find('[data-field-name="trgColumnName"]').find('option:gt(0)').remove();
            }
        }
    });
    
    setKpiDataMartCriteriaAliasCombo($editor, comboDatas);
    
    return;
}

function setKpiDataMartCriteriaAliasCombo(editor, comboDatas) {
    
    if (typeof editor == 'undefined') {
        
        var $editor = $('#datamart-editor');
        var $objects = $editor.find('.wfposition');
        var comboDatas = [];

        comboDatas.push('<option value="">- '+plang.get('select_btn')+' -</option>');

        $objects.each(function() {
            var $this = $(this);
            var indicatorId = $this.attr('data-indicatorid');
            var aliasName = $this.find('.bp-code').text();
            var indicatorName = $this.find('.bp-name').text();

            comboDatas.push('<option value="'+aliasName+'_'+indicatorId+'">' + aliasName + ' - ' + indicatorName + '</option>');

            if (!kpiIndicatorAttrs.hasOwnProperty(indicatorId)) {
                kpiIndicatorAttrs[indicatorId] = getKpiIndicatorAttrs(indicatorId);
            }
        });
    } 
    
    var comboOptions = comboDatas.join('');
    var $criteriasTbl = $('.kpi-datamart-criterias-config > tbody > tr');
    
    $criteriasTbl.each(function() {
        var $row = $(this);
        var $criteriaAliasNameCombo = $row.find('[data-field-name="criteriaAliasName"]');
        var criteria_alias_name = $row.find('[data-field-name="criteria_alias_name"]').val();
        var criteria_indicator_id = $row.find('[data-field-name="criteria_indicator_id"]').val();
        
        $criteriaAliasNameCombo.empty().append(comboOptions);
        
        if (criteria_alias_name != '' && criteria_indicator_id != '') {
            
            var $selectedOption = $criteriaAliasNameCombo.find('option[value="'+criteria_alias_name+'_'+criteria_indicator_id+'"]');
            
            if ($selectedOption.length) {
                
                var $criteriaColumnNameCombo = $row.find('[data-field-name="criteriaColumnName"]');
                
                $selectedOption.attr('selected', 'selected');
                $criteriaColumnNameCombo.empty().append(kpiIndicatorAttrs[criteria_indicator_id]);
                
            } else {
                $row.find('[data-field-name="criteria_alias_name"]').val('');
                $row.find('[data-field-name="criteria_indicator_id"]').val('');
            }
        }
    });
}

function editKpiDataMartConnection(connection) {
    var $editor = $('#datamart-editor');
    var sourceIndicatorId = $editor.find('#' + connection.sourceId).attr('data-indicatorid');
    var targetIndicatorId = $editor.find('#' + connection.targetId).attr('data-indicatorid');

    $.ajax({
        type: 'post',
        url: 'mdform/getKpiDataMartObjectRelation',
        data: {sourceIndicatorId: sourceIndicatorId, targetIndicatorId: targetIndicatorId}, 
        dataType: 'json', 
        beforeSend: function() {
            Core.blockUI({message: 'Loading...', boxed: true});
        },
        success: function(data) {
            data.isEdit = true;
            kpiDataMartNewRelationConnect(connection, data, $editor);
        }
    });
}

$(function() {
    
    if (typeof jsPlumb != 'undefined') { 
    
    jsPlumb.bind('beforeDrop', function(info) {
        
        var result = false;
        var $editor = $('#datamart-editor');
        var sourceIndicatorId = $editor.find('#' + info.sourceId).attr('data-indicatorid');
        var targetIndicatorId = $editor.find('#' + info.targetId).attr('data-indicatorid');
        
        $.ajax({
            type: 'post',
            url: 'mdform/getKpiDataMartObjectRelation',
            data: {sourceIndicatorId: sourceIndicatorId, targetIndicatorId: targetIndicatorId}, 
            dataType: 'json', 
            async: false, 
            success: function (data) {
                
                kpiDataMartNewRelationConnect(info, data, $editor);
            }
        });
        
        return result;
    });
    
    jsPlumb.bind('dblclick', function(connection, originalEvent) {
        editKpiDataMartConnection(connection);
    });
    
    jsPlumb.bind('contextmenu', function(connection, originalEvent) {
        
        $.contextMenu('destroy', '#kpiDataMartVisualConfigForm ._jsPlumb_connector');
        
        $.contextMenu({
            selector: '#kpiDataMartVisualConfigForm ._jsPlumb_connector',
            callback: function (key, opt) {

                if (key == 'editConnect') { 
                    
                    editKpiDataMartConnection(connection);
                    
                } else if (key == 'removeConnect') {
                    
                    var dialogName = '#dialog-kpidmart-obj-confirm';
                    if (!$(dialogName).length) {
                        $('<div id="' + dialogName.replace('#', '') + '"></div>').appendTo('body');
                    }
                    var $dialog = $(dialogName);

                    $dialog.html(plang.get('msg_delete_confirm'));
                    $dialog.dialog({
                        cache: false,
                        resizable: true,
                        bgiframe: true,
                        autoOpen: false,
                        title: plang.get('msg_title_confirm'), 
                        width: 300,
                        height: 'auto',
                        modal: true,
                        buttons: [
                            {text: plang.get('yes_btn'), class: 'btn green-meadow btn-sm', click: function() {

                                var $linkInput = $('#datamart-editor').find('textarea[name="'+connection.sourceId+'_'+connection.targetId+'"]');

                                if ($linkInput.length) {
                                    $linkInput.remove();
                                }

                                jsPlumb.select({source: connection.sourceId, target: connection.targetId}).detach();

                                $dialog.dialog('close');
                            }},
                            {text: plang.get('no_btn'), class: 'btn blue-madison btn-sm', click: function () {
                                $dialog.dialog('close');
                            }}
                        ]
                    });
                    $dialog.dialog('open');
                }
            },
            items: {
                "editConnect": {name: plang.get('edit_btn'), icon: "edit"}, 
                "removeConnect": {name: plang.get('delete_btn'), icon: "trash"}
            }
        });
    });
    
    }
    
    $.contextMenu({
        selector: '#kpiDataMartVisualConfigForm .wfdmart:not(.wfisreadonly-true)',
        callback: function (key, opt) {
            
            if (key == 'removeObj') {
                
                var $elem = $(this);
                var dialogName = '#dialog-kpidmart-obj-confirm';
                if (!$(dialogName).length) {
                    $('<div id="' + dialogName.replace('#', '') + '"></div>').appendTo('body');
                }
                var $dialog = $(dialogName);
                
                $dialog.html(plang.get('msg_delete_confirm'));
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: plang.get('msg_title_confirm'), 
                    width: 300,
                    height: 'auto',
                    modal: true,
                    buttons: [
                        {text: plang.get('yes_btn'), class: 'btn green-meadow btn-sm', click: function() {
                                
                            var $editor = $('#datamart-editor');
                            var objId   = $elem.attr('id');
                            
                            $editor.find('textarea[data-sourceid="'+objId+'"], textarea[data-targetid="'+objId+'"]').each(function() {
                                
                                var $thisObj = $(this);
                                $thisObj.remove();
                            });
                            
                            jsPlumb.detach(objId);
                            jsPlumb.remove(objId);
                            
                            $dialog.dialog('close');
                            
                            var $editor = $('#datamart-editor');
                            
                            setTimeout(function() {
                                setKpiDataMartAliasCombo($editor);
                            }, 1);
                        }},
                        {text: plang.get('no_btn'), class: 'btn blue-madison btn-sm', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                $dialog.dialog('open');
            }
        },
        items: {
            "removeObj": {name: plang.get('delete_btn'), icon: "trash"}
        }
    });
    
    $(document.body).on('change', 'select[data-field-name="aliasName"]', function() {
        var $this = $(this), 
            thisVal = $this.val(), 
            $row = $this.closest('tr'), 
            $trg_alias_name = $row.find('[data-field-name="trg_alias_name"]'), 
            $trg_indicator_id = $row.find('[data-field-name="trg_indicator_id"]'), 
            $trgColumnName = $row.find('[data-field-name="trgColumnName"]');
            
        if (thisVal != '') {
            
            var thisValArr = thisVal.split('_');
            var aliasName = thisValArr[0];
            var indicatorId = thisValArr[1];
            
            $trg_alias_name.val(aliasName);
            $trg_indicator_id.val(indicatorId);
            
            $trgColumnName.empty().append(getKpiIndicatorAttrs(indicatorId));
            
        } else {
            
            $trg_alias_name.val('');
            $trg_indicator_id.val('');
            $trgColumnName.val('');
            $trgColumnName.find('option:gt(0)').remove();
        }
    });
    
    $(document.body).on('change', 'select[data-field-name="trgColumnName"]', function() {
        var $this = $(this), 
            thisVal = $this.val(), 
            $row = $this.closest('tr'), 
            $trg_indicator_map_id = $row.find('[data-field-name="trg_indicator_map_id"]');
        
        if (thisVal != '') {
            $trg_indicator_map_id.val(thisVal);
        } else {
            $trg_indicator_map_id.val('');
        }
    });
    
    $(document.body).on('change', 'select[data-field-name="criteriaAliasName"]', function() {
        var $this = $(this), 
            thisVal = $this.val(), 
            $row = $this.closest('tr'), 
            $criteria_alias_name = $row.find('[data-field-name="criteria_alias_name"]'), 
            $criteria_indicator_id = $row.find('[data-field-name="criteria_indicator_id"]'),
            $criteriaColumnName = $row.find('[data-field-name="criteriaColumnName"]');
            
        if (thisVal != '') {
            
            var thisValArr = thisVal.split('_');
            var aliasName = thisValArr[0];
            var indicatorId = thisValArr[1];
            
            $criteria_alias_name.val(aliasName);
            $criteria_indicator_id.val(indicatorId);
            
            $criteriaColumnName.empty().append(getKpiIndicatorAttrs(indicatorId));
            
        } else {
            
            $criteria_alias_name.val('');
            $criteria_indicator_id.val('');
            $criteriaColumnName.val('');
            $criteriaColumnName.find('option:gt(0)').remove();
        }
    });
    
    $(document.body).on('click', '.kpi-datamart-criterias-remove', function() {
        var $this = $(this), 
            $tbody = $this.closest('tbody');
            
        $this.closest('tr').remove();
        
        var $rows = $tbody.find('> tr');
        
        $rows.each(function(i) {
            $(this).find('td:eq(0)').text((i + 1) + '.');
        });
    });
    
    $(document.body).on('click', '.kpi-datamart-criterias-addrow', function() {
        var $this = $(this), 
            $table = $this.closest('table'),
            $tbody = $table.find('> tbody'),
            html = [], 
            select_btn = plang.get('select_btn');
            
        html.push('<tr>');
            html.push('<td></td>');
            html.push('<td>');
                html.push('<input type="hidden" data-field-name="id">');
                html.push('<input type="hidden" data-field-name="criteria_alias_name">');
                html.push('<input type="hidden" data-field-name="criteria_indicator_id">');
                html.push('<select class="form-control form-control-sm" data-field-name="criteriaAliasName" data-placeholder="- '+select_btn+' -">');
                html.push('</select>');
            html.push('</td>');
            html.push('<td>');
                html.push('<select class="form-control form-control-sm" data-field-name="criteriaColumnName" data-placeholder="- '+select_btn+' -">');
                    html.push('<option value="">- '+select_btn+' -</option>');
                html.push('</select>');
            html.push('</td>');
            html.push('<td>');
                html.push('<input type="text" data-field-name="criteriaCriteria" class="form-control form-control-sm">');
            html.push('</td>');
            html.push('<td class="text-center">');
                html.push('<a href="javascript:;" class="btn red btn-xs kpi-datamart-criterias-remove" title="'+plang.get('delete_btn')+'">');
                    html.push('<i class="far fa-trash"></i>');
                html.push('</a>');
            html.push('</td>');
        html.push('</tr>');
        
        $tbody.append(html.join(''));
        
        setKpiDataMartCriteriaAliasCombo();
        
        var $rows = $tbody.find('> tr');
        
        $rows.each(function(i) {
            $(this).find('td:eq(0)').text((i + 1) + '.');
        });
    });
    
});