<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Graph Distances and Paths</title>
    <meta name="description" content="Interactive diagram showing all distances from a node, and highlighting all paths between two nodes." />
    <meta charset="UTF-8">
</head>
<body onload="init()">
<div id="sample">
    <div id="myDiagramDiv" style="border: solid 1px black; background: white; width: 100%; height: 700px"></div>
    <table>
        <tr>
            <td>
                <form id="createForm">
                    <fieldset>
                        <legend>Create Node:</legend>
                        <input type="text" name="name" placeholder="Node name">
                        <button type="submit">Create</button>
                    </fieldset>
                </form>
            </td>
            <td>
                <form id="updateForm">
                    <fieldset>
                        <legend>Update Node:</legend>
                        <select id="updateSelection">
                            <option>Select Node</option>
                        </select>
                        <br/>
                        <br/>
                        <input type="text" name="name" placeholder="Node name">
                        <button type="submit">Update</button>
                    </fieldset>
                </form>
            </td>
            <td>
                <form id="connectForm">
                    <fieldset>
                        <legend>Connect Node/Find path:</legend>
                        <select id="fromSelection" name="from_node">
                            <option>From Node</option>
                        </select>
                        <select id="toSelection" name="to_node">
                            <option>To Node</option>
                        </select>
                        <button type="submit" id="connectSubmit">Connect</button>
                        <button type="submit" id="pathSubmit">Find path</button>
                    </fieldset>
                </form>
            </td>
            <td>
                <form id="deleteForm">
                    <fieldset>
                        <legend>Delete Node:</legend>
                        <select id="deleteSelection">
                            <option>Select Node</option>
                        </select>
                        <button type="submit">Delete</button>
                    </fieldset>
                </form>
            </td>
        </tr>
    </table>

    <p>Click on a node to show distances from that node to each other node.
    Click on a second node to show a shortest path from the first node to the second node.
        (Note that there might not be any path between the nodes.)</p>
    <p>Clicking on a third node will de-select the first two.</p>
    <p>
        Here is a list of all paths between the first and second selected nodes.
        Select a path to highlight it in the diagram.
    </p>
    <select id="myPaths" style="min-width:200px" size="10"></select>
</div>
</body>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="https://gojs.net/latest/release/go.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.6.0/socket.io.min.js"></script>
<script src="/js/go.js"></script>
<script>
    var socket = io('http://localhost:3000');

    // Real time update catcher
    $(function () {
        socket.on('graph-channel', function(data){
            console.log(data);

            if (data.event === 'nodeCreated') {
                nodeCreated(data.node);
            } else if (data.event === 'nodeUpdated') {
                nodeUpdated(data.node);
            } else if (data.event === 'nodeDeleted') {
                nodeDeleted(data.node.id);
            } else if (data.event === 'nodeConnected') {
                nodeConnected(data.fromNode, data.toNode);
            }
        }.bind(this));
    });

    function generateGraph() {

        $.ajax({
            type: 'GET',
            url: "/api/nodes",
            async: false,
            dataType: "json",
            success: function (data) {
                arr = data.data // loop through results array

                $.each( arr, function( key, value ) {
                    //console.log( key + ": " + value.name );
                    cn = value.neighbours;
                    fromIndex = findIndex(value.id);
                    if (!fromIndex) {
                        nodePushToArray(value);
                        fromIndex = findIndex(value.id);
                    }


                    if (typeof cn.data !='undefined') {
                        $.each(cn.data, function (k, v) {
                            //console.log( k + ": " + v.name );
                            toIndex = findIndex(v.id);
                            if (!toIndex) {
                                nodePushToArray(v);

                                // edgeCreated(fromIndex, findIndex(v.id));
                                edgeCreated(value.id, v.id);
                            } else {
                                //edgeCreated(fromIndex, toIndex);
                                edgeCreated(value.id, v.id);
                            }

                        });
                    }
                });

            }
        });

        //console.log(nodeDataArray);
        //console.log(linkDataArray);

        myDiagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);

        $.each( nodeDataArray, function( key, value ) {
            populateSelection(value);
        });
    }

    function findPaths(fromNode, toNode) {
        node1 = myDiagram.findNodeForKey(fromNode);
        node2 = myDiagram.findNodeForKey(toNode);
        highlightShortestPath(node1, node2);
        listAllPaths(node1, node2);
    }

    function populateSelection(node) {
        //console.log(node);
        $('#fromSelection').append('<option value="'+node.id+'">'+node.name+'</option>');
        $('#toSelection').append('<option value="'+node.id+'">'+node.name+'</option>');
        $('#deleteSelection').append('<option value="'+node.id+'">'+node.name+'</option>');
        $('#updateSelection').append('<option value="'+node.id+'">'+node.name+'</option>');
    }

    function findIndex(value) {
        index = $.map(nodeDataArray, function(obj, index) {
            if(obj.id == value) {
                return index;
            }
        });

        if (typeof index[0] == 'undefined') {
            return false;
        }

        return index[0];
    }

    function containsObject(obj, list) {
        var i;
        for (i = 0; i < list.length; i++) {
            if (list[i].id === obj.id) {
                return true;
            }
        }

        return false;
    }

    function nodePushToArray(node) {
        //console.log(index, node);
        nodeDataArray.push({
            key: node.id,
            id: node.id,
            name: node.name,
            text: node.name,
            color: go.Brush.randomColor(128, 240)
        });
        index = index + 1;
    }

    function nodeCreated(node) {
        myDiagram.model.addNodeData({
            key: node.id,
            id: node.id,
            name: node.name,
            text: node.name,
            color: go.Brush.randomColor(128, 240)
        });

        populateSelection(node);
    }

    function nodeUpdated(nodeI) {
        var node = myDiagram.findNodeForKey(nodeI.id);
        if (node !== null) {
            myDiagram.startTransaction();
            myDiagram.model.setDataProperty(node.data, "text", nodeI.name);
            myDiagram.commitTransaction("updated node");
        }
        $("select option[value='"+node.id+"']").text(nodeI.name);
    }

    function nodeDeleted(nodeId) {
        // var node = myDiagram.findNodeForKey(findIndex(nodeId));
        var node = myDiagram.findNodeForKey(nodeId);
        if (node !== null) {
            myDiagram.startTransaction();
            myDiagram.remove(node);
            myDiagram.commitTransaction("deleted node");
        }
        $("select option[value='"+nodeId+"']").remove();
    }

    function nodeConnected(fromNode, toNode) {
        drawEdge(fromNode.id, toNode.id);
        findPaths(fromNode.id, toNode.id);
    }

    function edgeCreated(fromIndex, toIndex) {
        linkDataArray.push({
            from: fromIndex,
            to: toIndex,
            color: go.Brush.randomColor(0, 127)
        });
    }

    function drawEdge(fromIndex, toIndex) {
        myDiagram.model.addLinkData({from: fromIndex, to: toIndex, color: go.Brush.randomColor(0, 127)})
    }

    function errorHandeling(jqXHR, exception) {
        var msg = '';
        if (jqXHR.status == 422) {
            var errors = jqXHR.responseJSON;
            $.each(errors , function (index, value){
                console.log(index + ':' + value);
                msg += value + '\n';
            });

        } else {
            msg = jqXHR.responseText;
        }
        swal("Errors!", msg, "warning");
        //console.log(msg);
    }

    $('#createForm').submit(function(e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            url: '/api/nodes',
            type: 'post',
            data: form.serialize(),
            cache: false,
            success: function(response) {
                swal('Success!', response.message, 'success');
                form[0].reset();
            },
            error: function(jqXHR, exception) {
                errorHandeling(jqXHR, exception);
            }
        });
    });

    $('#connectSubmit').click(function (e) {
        e.preventDefault();
        var form = $('#connectForm');

        $.ajax({
            url: '/api/nodes/connect',
            type: 'post',
            data: form.serialize(),
            cache: false,
            success: function(response) {

            },
            error: function(jqXHR, exception) {
                errorHandeling(jqXHR, exception);
            }
        });
    });

    $('#pathSubmit').click(function(e) {
        e.preventDefault();

        var form = $('#connectForm');

        $.ajax({
            url: '/api/nodes/paths',
            type: 'get',
            data: form.serialize(),
            cache: false,
            success: function(response) {
                console.log(response);
                findPaths($('select[name=from_node]').val(), $('select[name=to_node]').val())
            },
            error: function(jqXHR, exception) {
                errorHandeling(jqXHR, exception);
            }
        });
    });

    $('#deleteForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: '/api/nodes/' + $('#deleteSelection').val(),
            type: 'delete',
            cache: false,
            success: function(response) {
                swal('Success!', response.message, 'success');
            },
            error: function(jqXHR, exception) {
                errorHandeling(jqXHR, exception);
            }
        });
    });

    $('#updateForm').submit(function(e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            url: '/api/nodes/' + $('#updateSelection').val(),
            type: 'put',
            data: form.serialize(),
            cache: false,
            success: function(response) {
                swal('Success!', response.message, 'success');
                form[0].reset();
            },
            error: function(jqXHR, exception) {
                errorHandeling(jqXHR, exception);
            }
        });
    });

    $('#updateSelection').on('change', function() {
        var thisvalue = $(this).find("option:selected").text();
        $(this).parent().find($('input[type=text]')).val(thisvalue);
    })
</script>
</html>