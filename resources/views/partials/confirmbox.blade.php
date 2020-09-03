<div id="modal-container" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-text-head">Warning!</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <span id="modal-text-desc"></span>
            </div>
            <div class="modal-footer">
                <a href="#" id="modal-btn-no" class="btn btn-danger">No</a>
                <a href="#" id="modal-btn-yes" class="btn btn-success">Yes</a>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmbox(text, form_id) {
        event.preventDefault();
        let modalContainer = $("#modal-container");
        modalContainer.modal({show: true, closeOnEscape: true, backdrop: 'static', keyboard: true});
        modalContainer.find("#modal-text-desc").html(text);
        modalContainer.find("#modal-btn-no").click(function () {
            modalContainer.modal('hide');
            return false;
        });
        modalContainer.find("#modal-btn-yes").click(function () {
            modalContainer.modal('hide');
            $('#' + form_id).submit();
            return false;
        });
    }
</script>
