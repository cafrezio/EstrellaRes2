<div>
    @php
        setlocale(LC_TIME, "spanish");
    @endphp
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Lugar</th>
                    <th>Fecha Ini</th>
                    <th>Fecha Fin</th>
                    <th>Activo</th>
                    <th ></th>
                    @can('admin.eventos.edit')
                        <th ></th>
                    @endcan
                    @can('admin.eventos.destroy')
                        <th ></th>
                    @endcan
                    <th ></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($eventos as $evento)
                    <tr
                    @if ($evento->activo)
                        style="font-weight: bold;"
                    @endif 
                        style="color: gray;"
                    >
                        <td>{{ $evento->id }}</td>
                        <td>{{ $evento->lugar }}</td>
                        <td>{{ utf8_encode(strftime("%a %d/%m/%Y", strtotime($evento->inicio ))) }}</td>
                        <td>{{ utf8_encode(strftime("%a %d/%m/%Y", strtotime($evento->final ))) }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mySwitch" name="darkmode" value="yes"
                                @if ($evento->activo)
                                    checked
                                @endif                                
                                readonly="readonly">
                            </div>
                        </td>
                        <td width="10px">
                            <a class="btn btn-info btn-sm" href="{{ route('admin.eventos.show', $evento->id) }}">Funciones</a>
                        </td>

                        @can('admin.eventos.edit')
                            <td width="10px">
                                <a class="btn btn-primary btn-sm" href="{{ route('admin.eventos.edit', $evento->id) }}">Editar</a>
                            </td>
                        @endcan


                        @can('admin.eventos.destroy')
                            <td width="10px">
                                <a wire:click="$emit('deleteEvent', {{ $evento->id }})" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        @endcan
                        <td>
                            <a class="btn btn-secondary btn-sm" href="{{ route('eventoprint', $evento->id) }}" target="_blank">Imprimir Reservas</a>
                        </td>
                    </tr>    
                @endforeach
            </tbody>
        </table>
    </div>

    @push('js')

    @endpush
</div>
