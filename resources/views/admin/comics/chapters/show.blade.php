@extends('admin.comics.information', ['fields' => \App\Models\Chapter::getFormFields(), 'is_chapter' => true])
@section('card-title', 'Information about this chapter')
@section('reader_url', asset(substr($chapter->url, 1)))
@section('destroy-message', 'Do you want to delete this chapter and its relative pages?')
@section('form-action', route('admin.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]))
@section('list-title', 'Pages')
@section('list')
    <form id="fileupload" method="POST" enctype="multipart/form-data"
          action="{{ route('admin.comics.chapters.pages.store', ['comic' => $comic->slug, 'chapter' => $chapter->id]) }}">
    @csrf
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="fas fa-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple accept=".jpg, .jpeg, .png, .gif, .webp"/>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="fas fa-upload"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="fas fa-ban"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="fas fa-trash"></i>
                    <span>Delete selected</span>
                </button>
                <input type="checkbox" class="toggle"/>
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success progress-bar-striped" style="width: 0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-hover">
            <tbody class="files"></tbody>
        </table>
    </form>
@endsection
@section('script')
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
          <tr class="template-upload fade{%=o.options.loadImageFileTypes.test(file.type)?' image':''%} item">
              <td class="td-preview">
                  <div class="preview"></div>
              </td>
              <td>
                  <h5 class="name filter">{%=file.name%}</h5>
                  <strong class="error text-danger"></strong>
              </td>
              <td>
                  <p class="size">Processing...</p>
                  <div class="progress active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:0%;"></div></div>
              </td>
              <td class="td-buttons">
                  {% if (!o.options.autoUpload && o.options.edit && o.options.loadImageFileTypes.test(file.type)) { %}
                    <button class="btn btn-success edit" data-index="{%=i%}" disabled>
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </button>
                  {% } %}
                  {% if (!i && !o.options.autoUpload) { %}
                      <button class="btn btn-primary start" disabled>
                          <i class="fas fa-upload"></i>
                          <span>Start</span>
                      </button>
                  {% } %}
                  {% if (!i) { %}
                      <button class="btn btn-warning cancel">
                          <i class="fas fa-ban"></i>
                          <span>Cancel</span>
                      </button>
                  {% } %}
              </td>
          </tr>
      {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
          <tr class="template-download fade{%=file.thumbnailUrl?' image':''%} item">
              <td class="td-preview">
                  <div class="preview">
                      {% if (file.thumbnailUrl) { %}
                          <a href="{%=file.url%}" target="_blank" title="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                      {% } %}
                  </div>
              </td>
              <td>
                  <h5 class="name">
                      {% if (file.url) { %}
                          <a href="{%=file.url%}" target="_blank" class="filter" title="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                      {% } else { %}
                          <span class="filter">{%=file.name%}</span>
                      {% } %}
                  </h5>
                  {% if (file.error) { %}
                      <div><span class="label label-danger upload-error">Error</span> {%=file.error%}</div>
                  {% } %}
              </td>
              <td>
                  <span class="size">{%=o.formatFileSize(file.size)%}</span>
              </td>
              <td class="td-buttons pe-4">
                  {% if (file.deleteUrl) { %}
                      <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                          <i class="fas fa-trash"></i>
                          <span>Delete</span>
                      </button>
                      <input type="checkbox" name="delete" value="1" class="toggle">
                  {% } else { %}
                      <button class="btn btn-warning cancel">
                          <i class="fas fa-ban"></i>
                          <span>Cancel</span>
                      </button>
                  {% } %}
              </td>
          </tr>
      {% } %}
    </script>
    <script type="text/javascript">
        let old_files = @json($pages);
        window.onbeforeunload = function() {
            if ($('.upload-error').length) {
                alertbox('Some uploads failed, do you want really quit?');
                return 'Some uploads failed, do you want really quit?';
            }
        }
    </script>
@endsection
