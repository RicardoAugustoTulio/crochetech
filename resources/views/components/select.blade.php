@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-rose-600 focus:ring-rose-600 rounded-md shadow-sm text-lg min-h-12']) !!}>
    {{ $slot }}
</select>
