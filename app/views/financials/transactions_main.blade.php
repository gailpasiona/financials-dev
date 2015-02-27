@extends('templates.master')

@section('nav')
@include('templates.nav.nav_public',array('user' => $user))
@stop
@section('sub_nav')
@include('templates.nav.user_menu')
@stop


@section('content')
<div class="page">
  <div class="container">
      <div class="col-md-12">
        <div class="messages"></div>
          <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-folder-open-o fa-md"></i> <strong>Payables</strong></div>
           
                 <div class="panel-table"><table id="table-purchases" class="table"></table></div>
            
                 
                  <div class="panel-footer clearfix">
                    <div class="pull-left col-md-3">
                        <!-- <a class="btn btn-md btn-danger" href="{{{ action('Financials\Controllers\TransactionController@create') }}}" data-toggle="modal" data-target="#modal_form"><i><strong>      Create     </strong></i></a>&nbsp; -->
                    </div>

        <div class="col-md-9">
            <!-- <div class="controls">
            <select class="form-control" name="type" id="type">
                     <option value="">Default</option>
                     <option value="">All Purchases</option>
            </select>
        	<button type="button" class="btn btn-md btn-success" onclick="refreshTable();"><i class="fa fa-refresh fa-lg"></i>&nbsp;<strong>Refresh Table</strong></button>
        </div> -->
            <!-- <a href="{{ route('user_form')}}" class="btn btn-warning" data-toggle="modal" data-target="#users_modal">Create New</a> -->
            <form class="form-inline">
              <div class="control-group pull-right">
                <!-- <label class="control-label">Reload Table:</label> -->
                
                <div class="controls">
                  <select class="form-control" id="reload_type">
                            <option value="">For Invoicing </option>
                            <option value="all">w/ Posted Invoice </option>
                    </select>
                 <button type="button" class="btn btn-md btn-success" onclick="refreshTable();"><i class="fa fa-refresh fa-lg"></i>&nbsp;<strong>Refresh Table</strong></button>
                </div>
              </div>
            </form>
        </div>

    </div>
          
          </div>
      </div>

  </div>
  <div class="modal fade" data-backdrop="static" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
</div>
 @stop


@section('styles')
<link rel="stylesheet" href="{{ URL::asset('css/bootstrap-table.css')}}" />
<link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}" />
@stop
@section('scripts')
<script src="{{ URL::asset('js/bootstrap-table.js')}}"></script>
<script src="{{ URL::asset('js/processing.js')}}"></script>
<script src="{{ URL::asset('js/accounting.js')}}"></script>
<script src="{{ URL::asset('js/datepicker.js')}}"></script>
<script>

$('#table-purchases').bootstrapTable({
                        url: "AP/list",
                        queryParams: function query(params)
                                    {
                                        var q = {
                                            "limit": params.pageSize,
                                            "offset": params.pageSize * (params.pageNumber - 1),
                                            "search": params.searchText,
                                            //
                                            "type" : $("#reload_type").val(),
                                            //
                                            "name": params.sortName,
                                            "order": params.sortOrder
                                        };
                     
                                        return q;
                                    },
                        // showRefresh: true,
                        //showColumns: true,
                        // search: true,
                       //    showToggle: true,
                        pagination: true,
                        columns: [{
                            field: 'po_number',
                            title: 'PO Number',
                            align: 'left',
                            valign: 'center',
                            sortable: true,
                            //width: 200/4,
                        },{
                            field: 'po_date',
                            title: 'PO Date',
                            align: 'left',
                            valign: 'center',
                            sortable: true,
                            //width: 200/4,
                        }, {
                            field: 'po_total_amount',
                            title: 'Amount',
                            align: 'left',
                            valign: 'center',
                            formatter: amountFormatter,
                            //valign: 'bottom',
                            //width: 200/4,
                            sortable: true
                        }, {
                            field: 'supplier',
                            title: 'Supplier',
                            align: 'left',
                            valign: 'center',
                            //valign: 'bottom',
                            //width: 200/3,
                            formatter: json2string,
                            sortable: false
                        }, {
                            field: 'requestor',
                            title: 'Requestor',
                            align: 'left',
                            valign: 'center',
                            //valign: 'bottom',
                            //width: 200/3,
                            //formatter: json2string,
                            sortable: false
                        },{
                            field: 'id',
                            title: 'Action',
                            align: 'center',
                            valign: 'center',
                            //valign: 'bottom',
                            //width: 200/3,
                            formatter: useractionFormatter,
                            sortable: false
                        }]});
function json2string(obj){
    return obj.supplier_name;
}
function amountFormatter(value){
    return accounting.formatMoney(value,"Php ");
}
function useractionFormatter(value){
    // return '<button type="button" class="btn btn-sm btn-warning syncBtn" onclick="invoice_action('+value+');"><strong>Invoice</strong></button>';
    var url = 'AP/generate_invoice?reference=' + value;
    return '<a class="btn btn-sm btn-warning" href="'+ url +'" data-toggle="modal" data-target="#modal_form"><strong>Invoice</strong></a>';
}
function refreshTable(){
	$('#table-purchases').bootstrapTable('refresh',{});
}
function invoice_action(value){
    console.log(value);
   myApp.showPleaseWait();
    var request = $.ajax({
            url: "AP/invoice/generate",
            type: "POST",
            data: {reference: value}
            // dataType: "json"
    });
            
    request.done(function(data){
        console.log(data);
        myApp.hidePleaseWait();
        $('.table').bootstrapTable('refresh');
    // $('#table-records').bootstrapTable('refresh',{url: 'bp_records'});

    });
    request.fail(function(jqXHR, textStatus){
        myApp.hidePleaseWait();
    });
}

$('.modal').on('hidden.bs.modal', function () {
 $(this).removeData('bs.modal');
  $('.table').bootstrapTable('refresh');
  $(this).empty();
});
</script>
@stop
