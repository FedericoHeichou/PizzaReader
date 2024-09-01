<template>
    <div id="modal-container" class="modal fade" tabindex="-1" style="display: none" aria-labelledby="modal-text-head" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modal-text-head">{{ popupData.header }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="modal-text-desc">{{ popupData.body }}</span>
                </div>
                <div class="modal-footer">
                    <span class="btn btn-success" data-bs-dismiss="modal">{{ popupData.button }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Popup",
    props : ["popupData"],
}
</script>
