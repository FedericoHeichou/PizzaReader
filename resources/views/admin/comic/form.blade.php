@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-12">
                    <h3 class="mt-1 float-left">@yield('title')</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="@yield('action')" method="POST" enctype="multipart/form-data">
                @csrf
                @yield('method')

                @include('partials.form.input_text', ['field' => 'name', 'label' => 'Name', 'hint' => 'Insert comic\'s name', 'required' => 'required'])
                @include('partials.form.input_text', ['field' => 'stub', 'label' => 'URL slug', 'hint' => 'Automatically generated, use this if you want to have a custom URL slug'])
                @include('partials.form.checkbox', ['field' => 'hidden', 'label' => 'Hidden', 'hint' => 'Check to hide this comic', 'checked' => 'checked'])
                @include('partials.form.input_text', ['field' => 'author', 'label' => 'Author', 'hint' => 'Insert comic\'s author'])
                @include('partials.form.input_text', ['field' => 'artist', 'label' => 'Artist', 'hint' => 'Insert comic\'s artist'])
                @include('partials.form.input_text', ['field' => 'target', 'label' => 'Target', 'hint' => 'Insert comic\'s target [Example: Shonen, Seinen, Shojo, Kodomo, Josei]'])
                @include('partials.form.input_text', ['field' => 'status', 'label' => 'Status', 'hint' => 'Insert comic\'s status [Example: Airing, Finished]'])
                @include('partials.form.textarea', ['field' => 'description', 'label' => 'Description', 'hint' => 'Insert comic\'s description'])
                @include('partials.form.input_file', ['field' => 'thumbnail', 'label' => 'Thumbnail', 'hint' => 'Insert comic\'s thumbnail'])
                @include('partials.form.select', ['field' => 'comic_format', 'label' => 'Comic\'s format', 'hint' => 'Select comic\'s format', 'options' => App\ComicFormat::all(), 'required' => 'required'])
                @include('partials.form.checkbox', ['field' => 'adult', 'label' => 'Adult', 'hint' => 'Check to set this comic for adults only'])
                @include('partials.form.input_text', ['field' => 'custom_chapter', 'label' => 'Custom chapter title', 'hint' => 'Replace the default chapter with a custom format [Example: "{num}{sub}{ord} punch" returns "2nd punch"]'])

            </form>
        </div>
    </div>
@endsection
