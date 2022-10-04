<div>
    @php
        setlocale(LC_TIME, "spanish");
    @endphp
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-3">
                    <b><label for="evento">Seleccione el Evento</label></b>
                    <select class="form-control" name="evento" wire:model="eventoSel">
                        <option value="-1">--------</option>  
                        @foreach ($eventos as $evento)
                            <option value="{{ $evento->id }}">
                                {{ $evento->lugar . " - " . utf8_encode(strftime("%d/%m/%Y", strtotime($evento->inicio )))}} 
                                @if ($evento->activo)
                                      (ACTIVO)
                                @endif
                            </option>                                   
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <b><label for="fecha">Seleccione la fecha</label></b>
                    <select class="form-control" name="fecha" wire:model="fechaSel">
                        <option value="-1">--------</option>  
                            @foreach ($fechas as $fecha)
                            <option value="{{ $fecha->fecha }}">{{ utf8_encode(strftime("%A %d de %B", strtotime($fecha->fecha))) }}
                            </option> 
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-md-3">
                    <span class="badge badge-secondary" style="width:100%"><h5>Reservas: <b> {{ $resTotal }} </b> / Asistencia: <b> {{ $asistTotal }} </b></h5></span>
                    <div class="progress" style="height:30px; margin-top:5px">
                        @if ($resTotal > 0)
                        <div class="progress-bar " style="width:{{ sprintf("%.0f%%", $asistTotal/$resTotal * 100) }} ;height:30px; background-color:#1ABC9C">{{ sprintf("%.0f%%", $asistTotal/$resTotal * 100) }}</div>
                        @endif
                    </div> 
                    
                </div>  
                <div class="col-md-3">
                    <span class="badge badge-info" style="width:100%"><h5>Total Evento: {{ '$ ' . number_format($totEvento) }} </h5></span>
                    <div  style="height:30px; margin-top:5px">
                        <h5><span class="badge badge-success" style="width:49%">Entradas: {{number_format($totAsistEvento) }}</span>
                            <span class="badge badge-success" style="width:49%">Promedio: 
                                @if ($totAsistEvento)
                                    {{ '$' . number_format($totEvento / $totAsistEvento) }}
                                @else
                                    $ 0    
                                @endif
                            </span>
                        </h5>
                    </div>
                </div> 
            </div>
        </form>
    </div> 
</div>