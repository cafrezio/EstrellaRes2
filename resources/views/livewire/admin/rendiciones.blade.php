<div>
    @php
        setlocale(LC_TIME, "spanish");
    @endphp
    @if ($newRend)
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Lugar</th>
                            <th>Fecha</th>
                            <th ></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newRend as $new)
                            <tr>
                                <td>{{ $new->lugar }}</td>
                                <td>{{ utf8_encode(strftime("%a %d/%m/%Y", strtotime($new->fecha ))) }}</td>
                                <td width="10px">
                                    <a class="btn btn-info btn-sm" wire:click="save({{ $new->evento_id}} , {{strtotime($new->fecha )}})">
                                        Crear
                                    </a>
                                </td>

                                
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Lugar</th>
                        <th>Fecha</th>
                        <th>Cobrador</th>
                        <th>Recaudado</th>
                        <th>A Cuenta</th>
                        <th>Gastos</th>
                        <th>A Rendir</th>
                        <th ></th>
                        @can('admin.rendiciones.destroy')
                          <th ></th>  
                        @endcan
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventosRend as $evRend)
                        <tr>
                            <td>{{ $evRend->lugar }}</td>
                            <td>{{ utf8_encode(strftime("%a %d/%m/%Y", strtotime($evRend->fecha ))) }}</td>
                            <td>{{ $evRend->name }}</td>
                            <td>$ {{ number_format($evRend->recaudado) }}</td>
                            <td>$ {{ number_format($evRend->a_cuenta) }}</td>
                            <td>$ {{ number_format($evRend->gastos) }}</td>
                            <td>$ {{ number_format($evRend->a_rendir) }}</td>
                            <td width="10px">
                                <a class="btn btn-primary btn-sm" href="{{ route('admin.rendicion.edit', $evRend->rend_id) }}">Editar</a>
                            </td>
                            <td width="10px">
                                <a class="btn btn-secondary btn-sm" href="{{ route('rendicionprint', $evRend->rend_id) }}" target="_blank">Imprimir</a>
                            </td> 
                            @can('admin.rendiciones.destroy')
                                <td width="10px">
                                    <a wire:click="$emit('deleteRend', {{ $evRend->rend_id }})" class="btn btn-danger btn-sm">Eliminar</a>
                                </td> 
                            @endcan
                        </tr>    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @push('js')

    @endpush
</div>
