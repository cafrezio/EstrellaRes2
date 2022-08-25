<div>
    @php
        setlocale(LC_TIME, "spanish");
    @endphp
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col">
                    <b><label for="evento">Seleccione el Evento</label></b>
                    <select class="form-control" name="evento" wire:model="eventoSel">
                        <option value="-1">--------</option>  
                        @foreach ($eventos as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->lugar }} </option>                                   
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <b><label for="fecha">Seleccione la fecha</label></b>
                    <select class="form-control" name="fecha" wire:model="fechaSel">
                        <option value="-1">--------</option>  
                            @foreach ($fechas as $fecha)
                            <option value="{{ $fecha->fecha }}">{{ utf8_encode(strftime("%A %d de %B", strtotime($fecha->fecha))) }}
                            </option> 
                        @endforeach
                    </select>
                    
                </div>
                <div class="col">
                    <span class="badge badge-secondary" style="width:100%"><h5>Reservas: <b> {{ $resTotal }} </b> / Asistencia: <b> {{ $asistTotal }} </h5></span>
                    <div class="progress" style="height:30px; margin-top:5px">
                        @if ($resTotal > 0)
                        <div class="progress-bar " style="width:{{ sprintf("%.0f%%", $asistTotal/$resTotal * 100) }} ;height:30px; background-color:#1ABC9C">{{ sprintf("%.0f%%", $asistTotal/$resTotal * 100) }}</div>
                        @endif
                        
                    </div> 
                </div>   
            </div>
        </form>
    </div> 
</div>