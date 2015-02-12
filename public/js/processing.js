var myApp;
myApp = myApp || (function () {
var pleaseWaitDiv = $('<div class="modal fade" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false"><div class="modal-dialog"><div class="modal-content"> <div class="modal-header"><strong>Processing Invoice, Please Wait...</strong></div><div class="modal-body"><div class="progress progress-striped active"><div class="progress-bar bar" style="width: 100%;"></div></div></div></div></div></div>');
return {
		showPleaseWait: function() {
			 pleaseWaitDiv.modal();
		},
		hidePleaseWait: function () {
			pleaseWaitDiv.modal('hide');
		},
};
})();