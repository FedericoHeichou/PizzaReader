<template>
    <div id="modal-container" class="modal fade" tabindex="-1" role="dialog" aria-modal="true" style="display: none">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modal-text-head">{{ popupData.header }}</h3>
                    <button @click="closeFunction" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <span id="modal-text-desc">{{ popupData.body }}</span>
                </div>
                <div class="modal-footer">
                    <span @click="closeFunction" class="btn btn-success">{{ popupData.button }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Popup",
    props : ["popupData"],
    methods : {
        closeFunction(){
            $('#modal-container').modal('hide');
        }
    },
}
</script>
