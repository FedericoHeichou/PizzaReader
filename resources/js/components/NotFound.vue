<template>
    <div class="py-sm-4 text-center">
        <div class="display-3 mt-5">Page not found.</div>
        <div class="fw-bold mb-2">
            The page you are trying to get doesn't exist.<br>
            If you clicked a link it is expired (for example the chapter has been removed).<br>
        </div>
        <img class="img-thumbnail" style="max-width:100%;" :src="reader.BASE_URL + 'img/404.gif'">
    </div>
</template>

<script>
export default {
    name: "NotFound",
    props: {
        reader: {
            type: Object,
            required: true
        }
    }
}
</script>
