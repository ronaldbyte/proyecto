@if ($paginator->hasPages())
    <nav>
        @if ($paginator->onFirstPage())
            <span class="boton boton-suave">Anterior</span>
        @else
            <a class="boton boton-suave" href="{{ $paginator->previousPageUrl() }}">Anterior</a>
        @endif

        <span style="padding:8px 10px;">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="boton boton-suave" href="{{ $paginator->nextPageUrl() }}">Siguiente</a>
        @else
            <span class="boton boton-suave">Siguiente</span>
        @endif
    </nav>
@endif
