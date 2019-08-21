function pmConfirm(message, true_func, false_func) {
    var container = document.createElement('div');
    //template for modal window
    container.innerHTML += '<div class="modal fade custom-confirm">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-body" style="padding:30px 15px;">' +
        '<div>' + message + '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
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
    ok.onclick = function () {
        $(modal).modal("hide");
        $(modal).detach();
        true_func();
    }

    //get click Cancel button
    var cancel = modal.getElementsByTagName('button')[1];
    cancel.onclick = function () {
        false_func();
    }

    document.body.appendChild(modal);

    $(modal).modal();
}

function pmModal(url, id, title, buttons, onLoaded) {

    var container = document.createElement('div');
    //template for modal window
    container.innerHTML += '<div class="modal fade pm-modal" id="' + id + '">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '<h4 class="modal-title">' + title + '</h4>' +
        '</div>' +
        '<div class="modal-body"></div>' +
        '<div class="modal-footer"></div>' +
        '</div>' +
        '</div>' +
        '</div>';

    //'<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> OK</button>' +
    //'<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +

    var modal = container.firstChild;
    document.body.appendChild(modal);

    $(modal).on('hidden.bs.modal', function () {
        $(this).detach();
    });

    $.each(buttons, function () {
        $(modal).find(".modal-footer").append(this);
    });

    $.get(url, function (data) {
        $(modal).find(".modal-body").html(data);

        if (typeof (onLoaded) === "function") {
            onLoaded($("#" + id));

            $(modal).modal();
        }
    }, "text")
        .fail(function (response, textStatus, errorThrown) {
            notify("Error connecting to server", "danger");
        });
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}