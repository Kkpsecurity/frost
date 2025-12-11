$(function() {
    $('#type_id').on('change', function() {
        $('#status-form').submit();
    });    
   
    $('input[name="password"]').val('');
    $('input[name="password_confirmation"]').val('');
    
});

$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
            'MM/DD/YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    // Opens the delete modal
    $('.delete-btn').click(function() {        
        var modalId = $(this).data('modal-id');
        var recordId = $(this).data('record-id');
        $('#delete-record-id').val(recordId); // set the record ID value
        $('#' + modalId).modal('show');
    });

    // Submits the delete form when the "Delete" button is clicked
    $('button#delete-record-btn').on('click', function(event) {
        event.preventDefault();
        $(document).find('form#form-delete-record').submit();
    });

    
});