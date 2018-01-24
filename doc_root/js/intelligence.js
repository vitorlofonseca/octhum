
/**
 * Load selects with database data
 *
 */
function loadSelects(){

    //INTELLIGENCE CATEGORY
    if(document.getElementById("selectIntelligenceCategory")) {

        $("#selectIntelligenceCategory").append("<option value='0' disabled selected>Category</option>");

        $.get("http://localhost:8000/api/v1/intelligenceCategory", function (data) {
            jQuery.each(data, function () {
                $("#selectIntelligenceCategory").append('<option value=' + this["category"] + '>' + this["category"] + '</option>');
            });
        });

    }

}


/**
 * Load intelligences table
 *
 */
function loadIntelligences(){

    //INTELLIGENCE CATEGORY
    if($("tbodyIntelligences")) {

        $.get("http://localhost:8000/api/v1/intelligence/user/1", function (data) {

            //empty array
            if(data.length == 0){

                var line = "<tr class='odd gradeX' align='center'>" +
                                "<td colspan='3'>" +
                                    "There is no intelligences" +
                                "</td>" +
                            "</tr>";

                $("#tbodyIntelligences").html(line);

            } else {

                $("#tbodyIntelligences").html("");

                jQuery.each(data, function () {

                    var line = "<tr class='odd gradeX'>" +
                        "<td>" + this["name"] + "</td>" +
                        "<td>" + this["description"] + "</td>" +
                        "<td>" +
                        "<nobr>" +
                        "<button type='button' class='btn btn-outline btn-info' data-toggle='modal' data-target='#intelligenceModalUse' onclick='useIntelligence(" + this["id"] + ");'>Use</button>&nbsp;" +
                        "<button type='button' class='btn btn-outline btn-warning' data-toggle='modal' data-target='#modalIntelligenceMangmnt' onclick='modalIntelligenceMangmnt(" + this["id"] + ");'>Modify</button>&nbsp;" +
                        "<button type='button' class='btn btn-outline btn-danger' onclick='deleteIntelligence(" + this['id'] + ");'>Delete</button>" +
                        "</nobr>" +
                        "</td>" +
                        "</tr>";

                    $("#tbodyIntelligences").append(line);

                });

            }

        });

    }

}

//input number of each intelligence
var countInputs=0;

function useIntelligence(id){

    //getting the intelligence
    $.get("http://localhost:8000/api/v1/intelligence/"+id, function (data) {

        $("#inputVariables").html("");
        $("#intelligenceIdUse").val(id);
        $("#modalUseTitle").html("<span data-toggle='tooltip' data-html='true' data-placement='bottom' title='<nobr>Inclusion Date: " + formatDate(data["created_at"]) + "</nobr><br><nobr>Modification Date: " + formatDate(data["updated_at"]) + "</nobr>'>Intelligence #" + id + " - "+data["name"] + "</span>");
        $('[data-toggle="tooltip"]').tooltip();

        countInputs = 0;

        jQuery.each(data["mlp"]["mlp_variable"], function () {

            var tr = "<tr><td style='padding-bottom: 10px;'><input type='text' class='form-control' id='input_"+countInputs+"' name='neuron_"+this["id"]+"' placeholder='"+this["name"]+" value'></td></tr>";

            $("#inputVariables").append(tr);

            countInputs++;
        });

    });

}

/**
 * create / read / update an intelligence
 *
 */
function modalIntelligenceMangmnt(id){

    //read /modifying an intelligence
    if(id){

        //getting the intelligence
        $.get("http://localhost:8000/api/v1/intelligence/"+id, function (data) {

            $("#titleManagerIntelligence").html("<span data-toggle='tooltip' data-html='true' data-placement='bottom' title='<nobr>Inclusion Date: " + formatDate(data["created_at"]) + "</nobr><br><nobr>Modification Date: " + formatDate(data["updated_at"]) + "</nobr>'>Intelligence #" + id + " - "+data["name"] + "</span>");
            $('[data-toggle="tooltip"]').tooltip();
            $("#intelligenceName").val(data["name"]);
            $("#intelligenceId").val(id);
            $("#textAreaIntelligenceDescription").html(data["description"]);
            $("#selectIntelligenceCategory").val(data["category"]["category"]);

            //cleaning the classifications
            $("#tableClassifications").html("");

            $("#tableClassifications").append("<tr id='tr_classification_1'>"+
                                                    "<td>"+
                                                        "<input type='text' class='form-control' placeholder='1º Classification' id='classification_1' name='classification_1'>"+
                                                    "</td>"+
                                                    "<td align='right' valign='center'>"+
                                                        "<a href='javascript:void(0);' onclick='addClassification();' id='btn_add_classification'>"+
                                                            "<span style='color:green;' class='glyphicon glyphicon-plus'></span>"+
                                                        "</a>"+
                                                    "</td>"+
                                                "</tr>");

            $("#selectIntelligenceCategory").prop('disabled', true);
            $("#divFile").hide();

        });

    }

    //creating a new intelligence
    else {

        $("#selectIntelligenceCategory").prop('disabled', false);
        $("#titleManagerIntelligence").html("New Intelligence");
        $("#intelligenceName").val("");
        $("#textAreaIntelligenceDescription").html("");
        $("#selectIntelligenceCategory").val(0);
        $("#intelligenceId").val(0);
        $("#intelligenceDataInc").html("");

        $("#divClassifications").hide();
        $("#divFile").show();
    }

}


/**
 * save intelligence in database
 *
 */
function saveIntelligence(){

    //modification
    if($("#intelligenceId").val() != 0){

        var id = $("#intelligenceId").val();

        var intelligenceName = $("#intelligenceName").val();
        var intelligenceDescription = $("#textAreaIntelligenceDescription").val();
        var email = 'admin@admin.com';

        $.ajax({
            url: 'http://localhost:8000/api/v1/intelligence/'+id+'?name='+intelligenceName+'&description='+intelligenceDescription+'&userEmail='+email,
            type: 'post',
            data: {_method: 'put'},
            success: function(data) {

                loadIntelligences();

                showMessage(data, "success");

                $('#modalIntelligenceMangmnt').modal('toggle');
            },
            error: function(data){

                showMessage(data.responseJSON["returnMsg"], "error");

                $('#modalIntelligenceMangmnt').modal('toggle');
            }
        });

    }

    //creation
    else {

        var intelligenceName = $("#intelligenceName").val();

        if($("#selectIntelligenceCategory").val()) {
            var category = $("#selectIntelligenceCategory").val().toLowerCase();
        }

        if($("#selectIntelligenceCategory").val()) {
            var dataType = 1;
            //var dataType = $('input[name=radioDataTypeIntelligence]:checked', '#dataTypesIntelligence').val();
        }

        var intelligenceDescription = $("#textAreaIntelligenceDescription").val();
        var email = 'admin@admin.com';

        var fd = new FormData();
        fd.append('fileTraining', $('#file')[0].files[0]);

        //hidden the popup
        $('#modalIntelligenceMangmnt').modal('toggle');

        //loading screen
        //http://carlosbonetti.github.io/jquery-loading/
        $('body').loading({
            stoppable: true,
            message: 'Learning...',
            onStart: function(loading) {
                loading.overlay.slideDown(400);
            },
            onStop: function(loading) {
                loading.overlay.slideUp(400);
            }
        });


        $.ajax({
            url: 'http://localhost:8000/api/v1/intelligence?name='+intelligenceName+'&category='+category+'&description='+intelligenceDescription+'&userEmail='+email+'&dataType='+dataType,
            type: 'post',
            data: fd,
            processData: false,
            contentType: false,
            success: function(data) {

                //closing loading screen
                //http://carlosbonetti.github.io/jquery-loading/
                $('body').loading('stop');

                loadIntelligences();

                showMessage(data, "success");

                //console.log(data);

            },
            error: function(data){

                //closing loading screen
                //http://carlosbonetti.github.io/jquery-loading/
                $('body').loading('stop');

                showMessage(data.responseJSON["returnMsg"], "error");

                //console.log(data);
            }
        });


    }

}


/**
 * Delete intelligence
 *
 * @param id
 */
function deleteIntelligence(id){

    $.ajax({
        url: 'http://localhost:8000/api/v1/intelligence/'+id,
        type: 'post',
        data: {_method: 'delete'},
        success: function(data) {

            loadIntelligences();

            showMessage(data, "success");
        },
        error: function(data){

            console.log();
            showMessage(data.responseJSON["returnMsg"], "error");
        }
    });

}

function fileSelectedToIntelligence(){
    $("#modalDataType").hide();
    $("#modalInstructions").animate({width:'toggle'},350);
    $("#modalInstructions").show();

    $('#btnPrev').css("visibility", "visible");
    $('#btnOpenFile').css("visibility", "visible");
}


$(document).ready(function(){

    $("#btnPrev").click(function(){
        $("#modalInstructions").hide();
        $("#modalDataType").animate({width:'toggle'},350);
        $("#modalDataType").show();

        $('#btnPrev').css("visibility", "hidden");
        $('#btnOpenFile').css("visibility", "hidden");

    });

    $("#file").change(function(){
        $('#modalFile').modal('toggle');
    });

    $('#test').click(function(){
        $("body").loading();
    });

});

/**
 * Load file types to user select in intelligence creation
 *
 */
function loadDataTypes(){

    $("#dataTypesIntelligence").html("");

    //getting the intelligence
    $.get("http://localhost:8000/api/v1/intelligenceDataType/", function (data) {

        jQuery.each(data, function () {

            $("#dataTypesIntelligence").append("<div class='radio'><label><input type='radio' onclick='fileSelectedToIntelligence();' name='radioDataTypeIntelligence' value='"+this["id"]+"' id='rbDataType'>"+this["type"]+"</label></div>");

        });

    });
}


/**
 * Process inputs that user give to intelligence
 *
 */
function processInputs(){

    var strInputs = "";

    for(var i=0 ; i<countInputs ; i++){

        //concat & to explode in api
        if(i != 0){
            strInputs += "-";
        }

        var input = $("#input_"+i).val().replace(".", ",");

        strInputs += input;
    }

    var intelligenceId = $('#intelligenceIdUse').val();

    //getting the inputs
    $.get("http://localhost:8000/api/v1/processInputs/"+strInputs+"/intelligenceId/"+intelligenceId, function (data) {
        $("#classification").html(data);
    });
}