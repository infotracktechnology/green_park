@props([
    'type' => 'text',
    'label' => '',
    'name' => '',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'size' => '3',
    'id' => null,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => 'on',
    'min' => null,
    'max' => null,
    'step' => null,

    'class' => '',
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div class="form-group col-md-{{ $size }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $inputId }}" 
        value="{{ old($name, $value) }}" 
        class="form-control form-control-sm {{ $class }} @error($name) is-invalid @enderror" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($step !== null) step="{{ $step }}" @endif
        aria-describedby="{{ $hasError ? $inputId.'Feedback' : '' }}"
    >
    
    @error($name)
        <div id="{{ $inputId }}Feedback" class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>