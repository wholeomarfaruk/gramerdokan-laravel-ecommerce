@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit Campaign Landing Page</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Campaigns</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit Campaign landing page</div>
                    </li>
                </ul>
            </div>
            <!-- form-edit -->

            <form action="{{ route('admin.campaigns.landingpage.update', $campaign->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @foreach ($page->edit_sections as $sectionKey => $section)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ $section->label ?? ucfirst($sectionKey) }}</h5>
                        </div>

                        <div class="card-body">

                            {{-- ONLY loop if fields exist --}}
                            @if (isset($section->fields) && count((array) $section->fields) > 0)
                                @foreach ($section->fields as $key => $field)
                                {{-- @dd($field->key) --}}
                                    @if (isset($field->fields) && count((array) $field->fields) > 0)
                                     {{-- if child found --}}
                                        @foreach ($field->fields as $fieldKey => $subField)


                                            @php
                                                $subValue = isset($subField->key)
                                                    ? getSectionValue($page->sections, $subField->key)
                                                    : '';
                                            @endphp
                                           <x-landing-page-section :field="$subField" :value="$subValue" />
                                        @endforeach
                                    @else
                                        @php
                                            $value = isset($field->key)
                                                ? getSectionValue($page->sections, $field->key)
                                                : '';
                                        @endphp

                                        <div class="mb-3">
                                            <label class="form-label">{{ $field->label ?? $key }}</label>

                                            @if ($field->type === 'text')
                                                <input type="text" name="fields[{{ $field->key }} ]" class="form-control"
                                                    value="{{ old('fields.' . $field->key, $value) }}"
                                                    placeholder="{{ $field->placeholder ?? '' }}">
                                            @endif
                                            {{-- @dd($fieldKey) --}}

                                            @if ($field->type === 'textarea')
                                                <textarea name="fields[{{ $field->key }} ]" class="form-control" rows="3">{{ old('fields.' . $field->key, $value) }}</textarea>
                                            @endif

                                        </div>
                                    @endif
                                @endforeach
                            @endif



                        </div>
                    </div>
                @endforeach





                <button type="submit" class="btn btn-success">Save Changes</button>
            </form>
        </div>



        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
    </div>
    <!-- content area end -->
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#myFile').on('change', function() {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imgpreview').show();
                    $('#imgpreview img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
            $('#gFile').on('change', function() {

                var photos = this.files;
                if (photos.length > 0) {
                    $('#galPreview item').remove();
                }
                $.each(photos, function(i, photo) {
                    $('#galPreview').prepend('<div class="item"><img src="' + URL.createObjectURL(
                        photo) + '"></div>');

                })

            });
        })

        function stringtoSlug(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
            str = str.toLowerCase();
            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes



            $('#slug_input').val(str);

        }
    </script>
    <script src="https://cdn.tiny.cloud/1/hkkbs6irhd8pjbxo4xgcyy5o1lvtjcx4p843koiprxzql6dh/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>

    <script>
        tinymce.init({
            selector: '#editor',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',


        });
    </script>
    <script>
        let sizeId = 0;

        function addSize(value = "") {
            sizeId++;
            const container = document.getElementById("sizes-container");

            const div = document.createElement("div");

            div.classList.add('size-row', 'mb-5', 'd-flex', 'justify-content-between', 'align-items-center');
            div.setAttribute("data-id", sizeId);

            div.innerHTML = `
        <input type="text" name="sizes[size][]" value="${value}" placeholder="Enter size">
        <input type="text" name="sizes[qty][]" value="${value}" placeholder="Enter Quantity">

        <button type="button" onclick="deleteSize(${sizeId})">   <i class="icon-trash-2 text-danger"></i></button>
      `;
            container.appendChild(div);
        }

        function deleteSize(id) {
            const row = document.querySelector(`.size-row[data-id='${id}']`);
            if (row) row.remove();
        }

        function editSize(id) {
            const row = document.querySelector(`.size-row[data-id='${id}'] input`);
            if (row) {
                const newValue = prompt("Edit size:", row.value);
                if (newValue !== null) row.value = newValue;
            }
        }
    </script>
@endpush
