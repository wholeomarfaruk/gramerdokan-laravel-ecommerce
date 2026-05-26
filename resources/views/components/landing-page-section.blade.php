@props(['field', 'value'])
<div class="mb-3">
    <label class="form-label">{{ $field->label ?? '' }}</label>

    @if ($field->type === 'text')
        <input type="text" name="fields[{{ $field->key }} ]" class="form-control" value="{{ old('fields.' . $field->key, $value) }}"
            placeholder="{{ $field->placeholder ?? '' }}">
    @endif
    {{-- @dd($fieldKey) --}}

    @if ($field->type === 'textarea')
        <textarea name="fields[{{ $field->key }} ]" class="form-control" rows="3">{{ old('fields.' . $field->key, $value) }}</textarea>
    @endif

</div>
