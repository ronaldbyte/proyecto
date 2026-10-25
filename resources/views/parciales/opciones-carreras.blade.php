@php
    $areasCatalogo = $areasCatalogo ?? $areas ?? collect();
    $seleccionada = $seleccionada ?? null;
    $incluirVacia = $incluirVacia ?? true;
    $textoVacio = $textoVacio ?? 'Seleccione la carrera';
@endphp
@if($incluirVacia)
    <option value="">{{ $textoVacio }}</option>
@endif
@foreach($areasCatalogo as $area)
    <optgroup label="{{ $area->nombre }}">
        @foreach($area->carreras as $carrera)
            <option
                value="{{ $carrera->id }}"
                data-area="{{ $area->id }}"
                @selected((string) $seleccionada === (string) $carrera->id)
            >{{ $carrera->nombre }}</option>
        @endforeach
    </optgroup>
@endforeach
