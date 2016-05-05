var DataGrid = {

    init: function () {
        $('#at-datagrid-check-all').click(function(e) {
            that = this;
            $('.grid-table-checker').each(function() {
                this.checked = that.checked;
            });
        });
    },

    confirmAction: function(element, message) {
        event = event || window.event;
        event.preventDefault();

        message = message || "Are you sure?";
        var location = $(element).attr('href') || '';

        bootbox.confirm(message, function(result) {
            if (result) {
                window.location.replace(location);
            }
        });
    }
};

$(document).ready(function () {
    DataGrid.init();
});