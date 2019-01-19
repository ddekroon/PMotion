function customConfirm(message, true_func, false_func){
    var container = document.createElement('div');
    //template for modal window
    container.innerHTML += '<div class="modal fade custom-confirm">'+
                        '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                                '<div class="modal-body" style="padding:30px 15px;">' +
                                    '<div>' + message + '</div>' +
                                '</div>' +
                                '<div class="modal-footer">'+ 
                                    '<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> OK</button>' +
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

    //modal window
    var modal = container.firstChild;

    //get click OK button
    var ok = modal.getElementsByTagName('button')[0];
    ok.onclick = function() {
        $(modal).modal("hide");
        $(modal).detach();
        true_func();
    }

    //get click Cancel button
    var cancel = modal.getElementsByTagName('button')[1];
    cancel.onclick = function() {
        false_func();
    }

    document.body.appendChild(modal);

    $(modal).modal();
}