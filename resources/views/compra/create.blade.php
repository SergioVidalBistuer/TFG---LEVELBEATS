<form action="{{ route('compra.save') }}" method="POST">
    @csrf
    <label>Cliente:</label>
    <input type="text" name="cliente">
    <h3>Frutas</h3>
    @foreach($frutas as $fruta)
        <div>
            {{ $fruta->nombre }}
            <input type="number"
                   name="frutas[{{ $fruta->id }}]"

                   min="0"
                   value="0">
        </div>
    @endforeach
    <button type="submit">Guardar</button>
</form>
