<article class="analytics-panel">
    <h3>{{ $title }}</h3>
    @if($items->isEmpty())
        <div class="analytics-empty">Aún no hay servicios contratados.</div>
    @else
        <table class="analytics-table">
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td><strong>{{ $item->titulo_servicio }}</strong></td>
                        <td>{{ $item->total }} contratos</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</article>
