@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Segment</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.segments') }}"><div class="text-tiny">Segments</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.segments.update', $segment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <fieldset class="name">
                    <div class="body-title">Segment Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow @error('name') is-invalid @enderror"
                        type="text" name="name"
                        value="{{ old('name', $segment->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select name="is_active">
                            <option value="1" {{ old('is_active', $segment->is_active) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $segment->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Description</div>
                    <textarea class="flex-grow @error('description') is-invalid @enderror"
                        name="description" rows="4">{{ old('description', $segment->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
