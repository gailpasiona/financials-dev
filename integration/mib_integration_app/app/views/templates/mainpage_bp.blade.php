@extends('main.master')
@section('title')
Sync MIB Suppliers
@stop
@section('content')
	<div class='col-md-10 col-md-offset-1 box'>
		<div class="box-header">
			<h3 class="col-md-12 text-center">Sync Suppliers</h3> <br />
		</div>
		<div id="records_area">
				<table id="table-records"></table>
		</div>
	</div>

@stop
@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-table.css')}}" />
@stop
@section('scripts')
    <script src="{{ URL::asset('js/bootstrap-table.js')}}"></script>
    <script src="{{ URL::asset('js/processing.js')}}"></script>
    <script>
    	$('#table-records').bootstrapTable({
    		url: 'bp_records',
            showRefresh: true,
            showColumns: true,
            search: true,
            pagination: true,
            columns: [
            			{field: 'supplier_name',
                            title: 'Supplier Name',
                            align: 'center',
                            valign: 'center',
                            sortable: true,
                            width: 200/4
                        }, {
                            field: 'address',
                            title: 'Address',
                            align: 'center',
                            valign: 'center',
                            width: 200/4,
                            sortable: true
                        },{
                            field: 'id',
                            title: 'Action',
                            align: 'center',
                            formatter: urlFormatter,
                            valign: 'center',
                            width: 200/4,
                            sortable: true
                        }]
    	});

		function urlFormatter(value){
            // return '<a class="sync_btn" href="more_info/'+ value +'" data-toggle="modal" data-target="#info_modal" data-tooltip="tooltip" data-placement="top" title="Proceed RFP"><i class="fa fa-refresh fa-lg"></i></a>';
            return '<button type="button" class="btn btn-xs btn-warning syncBtn" onclick="syncnow('+value+');">Sync</button>';
        }

        function formatSupplier(value){
        	return value.bp_name;
        }

        function syncnow(value){
        	console.log(value);
        	myApp.showPleaseWait();

            var request = $.ajax({
                url: "bp/sync",
                type: "POST",
                data: {bp_id: value},
                dataType: "json"
            });
            
            request.done(function(data){
                myApp.hidePleaseWait();
                $('#table-records').bootstrapTable('refresh',{url: 'bp_records'});

            });
            request.fail(function(jqXHR, textStatus){
                myApp.hidePleaseWait();
            });

        }

        

    </script>
@stop