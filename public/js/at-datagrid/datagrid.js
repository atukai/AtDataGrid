var DataGrid = new Object({
    checkAll: function(sender) {
        $('.atf-grid table').find('.grid-table-checker').attr('checked', sender.checked);
    },

    action: function(action, msg, callback) {
        var checkers = $('.atf-grid table input[name="items[]"]:checked');

        if (checkers.length > 0) {
            msg = msg ? msg : '';

            if (msg != '' && !confirm(msg)) {
                $('#datagrid-list-form').get(0).reset();
                return false;
            } else {
                var form = $('#datagrid-list-form');
                if (typeof callback != 'undefined') {
                    callback(form, action, msg);
                } else {
                    $('#datagrid-list-form-action').val(action);
                    form.submit();
                }
            }
        } else {
            alert('Выберите одну или несколько строк.');
        }
    },

    ajaxAction: function(url, callback) {
        $.ajax({
            type: 'GET',
            url: url,
            cache: true,
            dataType: 'html',
            success: callback
        });
    },

    confirmAction: function(sender, message) {
        message = message || "Are you sure?";
        return confirm(message)
    },

    loadDataPanel: function(url, panel) {
        $.ajax({
            type: 'GET',
            url: url,
            cache: true,
            data: 'panel=' + panel,
            dataType: 'html',
            success: function (result) {
                $('#' + panel).html(result);
            },
            error: function(){
                $('#' + panel).html('Oops! Error occured.');
            }
        });
    }
});