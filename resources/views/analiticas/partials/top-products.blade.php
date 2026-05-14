<article class="analytics-panel">
    <h3>{{ $title }}</h3>
    @if($items->isEmpty())
        <div class="analytics-empty">Aún no hay datos suficientes.</div>
    @else
        <table class="analytics-table">
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nombre }}</strong>
                            <small>{{ ucfirst($item->tipo) }}</small>
                        </td>
                        <td>{{ $item->total }} ventas</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</article>
