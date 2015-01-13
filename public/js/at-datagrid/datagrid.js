var DataGrid = new Object({
    checkAll: function(sender) {
        $('.at-grid table').find('.grid-table-checker').attr('checked', sender.checked);
    },

    action: function(action, msg, callback) {
        var checkers = $('.at-grid table input[name="items[]"]:checked');

        if (checkers.length > 0) {
            msg = msg || '';

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
            alert('Check one or more rows');
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
    }
});