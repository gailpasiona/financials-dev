<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">{{{$data['title']}}}</h4>
      </div>
      <div class="modal-body">
                <div class="messages"></div>
                    <form class="form-horizontal" id="createform" role="form" method="POST" accept-charset="UTF-8">
                    <fieldset>
                        <div id="voucher_info" class="form-group">
                            <div class="col-md-12">
                                <!-- <div class="form-group row"> -->
                                  <!-- <label for="cost_dept" class="col-md-4 control-label">PO Number</label> -->
                                  <!-- <div class="col-md-6"> -->
                                    
                                     <input class="form-control" type="hidden" placeholder="PO Number" type="text" readonly="readonly" name="po_number" id="po_number" value="{{{$data['po_number']}}}">
                                    
                                  <!-- </div> -->
                                <!-- </div> -->

                                <div class="form-group row">
                                     <label for="cost_dept" class="col-md-4 control-label">Payee</label>
                                    <div class="col-md-6">
                                        
                                          <input class="form-control" readonly="readonly" placeholder="Requestor" type="text" name="payee" id="payee" value="{{{$data['payee']}}}">
                                        
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">PO Date</label>
                                    <div class="col-md-6">
                                       <input class="form-control" readonly="readonly" placeholder="Requestor" type="text" name="po_date" id="po_date" value="{{{$data['po_date']}}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount_request" class="col-md-4 control-label">Amount</label>
                                    <div class="col-md-6">
                                        <input class="form-control editable" readonly="readonly" placeholder="Amount Requested" type="text" name="amount" id="amount" value="{{{$data['amount']}}}">
                                      
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="date_needed" class="col-md-4 control-label">Date Needed</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['date_needed']))
                                        <input class="form-control datepicker" placeholder="Date Needed" type="text" readonly="readonly" name="date_needed" id="date_needed" value="{{{date("Y-m-d")}}}">
                                      @else
                                        <input class="form-control datepicker" placeholder="Date Needed" type="text" readonly="readonly" name="date_needed" id="date_needed" value="{{{$data['date_needed']}}}">
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="refno" class="col-md-4 control-label">Reference No</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['refno']))
                                        <input class="form-control editable" placeholder="Reference No" type="text" name="refno" id="refno" value="">
                                      @else
                                        <input class="form-control editable" placeholder="Reference No" type="text" name="refno" id="refno" value="{{{$data['refno']}}}">
                                      @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
     <div class="modal-footer">
          
            <div class="progress progress-striped f_bar">

                <div class="progress-bar bar f_bar" style="width: 0%;">
                     
                    <span class="prog_txt"></span>

                </div>

            </div>

          <button type="button" class="btn btn-default" id="dumer" data-dismiss="modal">Close</button>
           @if(isset($data['po_number']))
              <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Proceed</button>
           @else
              <button type="button" id="createBtn" class="btn btn-primary createBtn">Save</button>
           @endif

      </div>
    </div>
</div>

<script src="{{ URL::asset('js/dyn_fields.js')}}"></script>

    
    <script>
        
        $('.datepicker').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
        });
        
        $("#submitBtn").click(function(e){
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
         
          $("#createform :input").prop("readonly", true);
          $("#submitBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'AP/request',//'AP/approve/' + encodeURIComponent($("#po_number").val()),
            type: "PATCH",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input#refno").prop("readonly", false);
            $("#submitBtn").prop("disabled", false);
            $(".bar").css("width", "100%");
            $(".f_bar").removeClass( "active" );

            show_message(data);

          });
          request.fail(function( jqXHR, textStatus ) {
            $(".bar").css("width", "50%");
            console.log("Failed");
            console.log(textStatus);
          });
      });

        $("#createBtn").click(function(e){
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
         
          $("#createform :input").prop("readonly", true);
          $("#createBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'AP/save',
            type: "POST",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input.editable").prop("readonly", false);
            $("#createBtn").prop("disabled", false);
            $(".bar").css("width", "100%");
            $(".f_bar").removeClass( "active" );

            show_message(data);

          });
          request.fail(function( jqXHR, textStatus ) {
            $(".bar").css("width", "100%");
            console.log("Failed");
            console.log(textStatus);
            $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />System Error, please contact your System Administrator</div>');
          });
      });

      function show_message(data){
            $("div").removeClass("has-error");
            $( ".message_content" ).remove();//remove first if exists
             var prompt = "<br />";

             if(data.status == 'success_error'){
                $.each(data.message, function(key,value) {
                    prompt += value + "<br />";
                    $('.' + key).addClass("has-error");
                 });
                 $(".modal-dialog .messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Errors Occured!</strong> '+prompt+' </div>');
             }
             else if(data.status == 'success_failed'){
                  $(".modal-dialog .messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Failed!</strong> <br />'+data.message+' </div>');
             }
             else{
                  $(".messages").append('<div class="message_content alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />'+data.message+' </div>');
                  
                  $('#modal_form').modal('hide');
             }
             
      }

    </script>
    



