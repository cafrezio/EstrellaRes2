<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Evento</label>
                        <input class="form-control" value="{{ $lugar }}" disabled style="font-weight: bold;">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Fecha</label>
                        <input class="form-control" value="{{ $rend->fecha }}" disabled style="font-weight: bold;">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label>Recaudado</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input class="form-control" type="number"  value="{{ $recaudado }}" disabled style="font-weight: bold;">
                        </div>

                    </div> 
                </div>
            
                <div class="col">
                    <div class="form-group">
                        <label>Entregado a Cuenta</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input class="form-control" type="number" step="any"  value="{{ $rend->a_cuenta }}" wire:change="changeACuenta($event.target.value)">
                        </div>
                    </div> 
                </div>

                <div class="col">
                    <div class="form-group">
                        <label>A Rendir</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input class="form-control" type="number"  value="{{ $aRendir }}" disabled style="font-weight: bold;">
                        </div>
                    </div> 
                </div>

            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 15%">Categoria</th>
                        <th>Detalle</th>
                        <th style="width: 15%">Monto</th>
                        <th style="width: 10%"></th>
                    </tr>
                </thead>
                @foreach ($gastos as $gasto)
                    <tr>
                        <td>
                            <select class="form-control" name="categoria" wire:change="changeCategoria({{ $gasto->id }}, $event.target.value)">
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ ($gasto->categoriagasto_id == $categoria->id)? 'selected' : '' }}>
                                        {{ $categoria->categoria }} 
                                    </option>  
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control" wire:change="changeDetalle({{ $gasto->id }}, $event.target.value)" value="{{ $gasto->detalle }}">
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input class="form-control" type="number" step="any" wire:change="changeValor({{ $gasto->id }}, $event.target.value)" value="{{ $gasto->valor}}">
                            </div>
                        </td>
                        <td >
                            <a wire:click="deleteGasto({{ $gasto->id }})" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                @endforeach
                    <tr style="background-color: #9ebac7">
                        <td></td>
                        <td style="text-align: right; vertical-align:middle">
                            <h5><b>Total Gastos</b></h5>
                        </td>
                        <td >
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input class="form-control" type="number"  value="{{ $totgastos }}" disabled style="font-weight: bold;">
                            </div>
                        </td>
                        <td></td>
                    </tr>
            </table>
        </div>

            <div class="card-footer">
                <table class="table table-striped">           
                    <tr >
                        <td style="width: 15%">
                            <select class="form-control" name="categoria" wire:model='newCategoria'>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">
                                        {{ $categoria->categoria }} 
                                    </option>  
                                @endforeach
                            </select>
                            @error('newCategoria')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </td>
                        <td >
                            <div class="form-group">
                                <input class="form-control" wire:model='newDetalle'>
                            </div>
                        </td>
                        <td style="width: 15%">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input class="form-control" type="number" step="any" wire:model='newValor'>
                            </div>
                            @error('newValor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </td>
                        <td style="width: 10%">
                            <button class="btn btn-sm btn-success" wire:click="save">
                                Agregar
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        
    </div>    
</div>
