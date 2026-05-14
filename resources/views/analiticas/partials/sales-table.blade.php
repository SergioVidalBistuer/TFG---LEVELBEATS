<article class="analytics-panel">
    <h3>{{ $title }}</h3>
    @if($items->isEmpty())
        <div class="analytics-empty">Todavía no hay ventas registradas.</div>
    @else
        <table class="analytics-table">
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nombre }}</strong>
                            <small>{{ ucfirst($item->tipo) }} · {{ $item->comprador ?? 'Comprador' }}</small>
                        </td>
                        <td>{{ number_format((float) $item->importe, 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</article>
