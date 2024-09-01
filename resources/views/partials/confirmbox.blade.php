<div id="modal-container" class="modal fade" tabindex="-1" aria-labelledby="modal-text-head" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-text-head">Warning!</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="modal-text-desc"></span>
            </div>
            <div class="modal-footer">
                <a href="#" id="modal-btn-no" class="btn btn-danger" data-bs-dismiss="modal">No</a>
                <a href="#" id="modal-btn-yes" class="btn btn-success" data-form-submit="true">Yes</a>
            </div>
        </div>
    </div>
</div>
<div id="info-container" class="modal fade" tabindex="-1" aria-labelledby="info-text-head" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="info-text-head">Warning!</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="info-text-desc"></span>
            </div>
            <div class="modal-footer">
                <a href="#" id="info-btn-yes" class="btn btn-success" data-bs-dismiss="modal">Ok</a>
            </div>
        </div>
    </div>
</div>
<script>
    function alertbox(text) {
        const infoText = document.getElementById('info-text-desc');
        infoText.textContent = text;
        const modal = new bootstrap.Modal(document.getElementById('info-container'));
        modal.show();
    }
    const modalCallback = function (event) {
        const button = event.relatedTarget;
        const description = button.getAttribute('data-description');
        const formId = button.getAttribute('data-form');
        const modalDescription = confirmBox.querySelector('.modal-body span');
        if (formId) {
            const modalYes = confirmBox.querySelector('a[data-form-submit="true"]');
            modalYes.addEventListener('click', function () {
                document.getElementById(formId).submit();
            });
        }
        modalDescription.textContent = description;
    };
    var confirmBox = document.getElementById('modal-container');
    confirmBox.addEventListener('show.bs.modal', modalCallback);
</script>
