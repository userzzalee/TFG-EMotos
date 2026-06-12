@extends('taller.layout')

@section('content')
<div class="max-w-2xl w-full">

    <h1 class="text-xl sm:text-2xl font-bold tracking-widest uppercase text-white mb-1">Nueva cita</h1>
    <p class="text-sm text-white/40 mb-6 sm:mb-8">Rellena el formulario y nos pondremos en contacto contigo.</p>

    <form action="{{ route('taller.guardar') }}" method="POST" enctype="multipart/form-data"
          class="flex flex-col gap-5 sm:gap-6">
        @csrf

        {{-- Marca / Modelo --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="field-label">Marca *</label>
                <input type="text" name="marca" value="{{ old('marca') }}" placeholder="Honda, KTM…"
                       class="field-input @error('marca') border-red-500 @enderror">
                @error('marca') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Modelo *</label>
                <input type="text" name="modelo" value="{{ old('modelo') }}" placeholder="CRF450R…"
                       class="field-input @error('modelo') border-red-500 @enderror">
                @error('modelo') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Matrícula --}}
        <div>
            <label class="field-label">Matrícula *</label>
            <input type="text" name="matricula" value="{{ old('matricula') }}" placeholder="1234 ABC"
                   class="field-input w-full sm:w-48 @error('matricula') border-red-500 @enderror">
            @error('matricula') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        {{-- Fecha y hora de la cita --}}
        <div x-data="agendaPicker(@js($agenda), '{{ old('fecha_cita') }}')">
            <label class="field-label">Fecha y hora de la cita *</label>
            <input type="hidden" name="fecha_cita" :value="seleccion">

            <template x-if="dias.length === 0">
                <p class="text-sm text-white/40 bg-[#111] border border-white/10 rounded-lg px-4 py-3">
                    No hay horas disponibles en los próximos días. Vuelve a intentarlo más tarde.
                </p>
            </template>

            <div x-show="dias.length > 0" x-cloak>
                <p class="text-[11px] text-white/30 uppercase tracking-wider mb-2">1 · Elige un día</p>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <template x-for="d in dias" :key="d.fecha">
                        <button type="button" @click="elegirDia(d)"
                                class="day-chip" :class="diaActivo === d.fecha ? 'day-chip-activo' : ''"
                                x-text="d.etiqueta"></button>
                    </template>
                </div>

                <template x-if="diaActivo">
                    <div class="mt-4">
                        <p class="text-[11px] text-white/30 uppercase tracking-wider mb-2">2 · Elige una hora</p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                            <template x-for="h in slots" :key="h.hora">
                                <button type="button" @click="!h.ocupado && elegirHora(h.hora)"
                                        :disabled="h.ocupado"
                                        class="slot-chip"
                                        :class="h.ocupado ? 'slot-chip-ocupado' : (horaActiva === h.hora ? 'slot-chip-activo' : '')"
                                        x-text="h.hora"></button>
                            </template>
                        </div>
                    </div>
                </template>

                <p class="text-sm mt-4" x-show="resumen" x-cloak>
                    <span class="text-white/40">Cita seleccionada:</span>
                    <span class="text-[#f0c36d] font-semibold" x-text="resumen"></span>
                </p>
            </div>

            @error('fecha_cita') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        {{-- Problema --}}
        <div>
            <label class="field-label">Descripción del problema *</label>
            <textarea name="problema" rows="4" placeholder="Describe el problema con detalle…"
                      class="field-input resize-none @error('problema') border-red-500 @enderror">{{ old('problema') }}</textarea>
            @error('problema') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        {{-- Comentarios --}}
        <div>
            <label class="field-label">Comentarios adicionales <span class="text-white/30">(opcional)</span></label>
            <textarea name="comentarios" rows="3" placeholder="Cualquier información extra que nos pueda ayudar…"
                      class="field-input resize-none">{{ old('comentarios') }}</textarea>
        </div>

        {{-- Fotos --}}
        <div>
            <label class="field-label">Fotos <span class="text-white/30">(opcional, máx. 5)</span></label>
            <label for="fotos"
                   class="flex flex-col items-center justify-center gap-2 border border-dashed border-white/20
                          rounded-lg h-28 cursor-pointer hover:border-[#f0c36d]/50 transition-colors">
                <svg class="w-7 h-7 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 16.5V19a1.5 1.5 0 001.5 1.5h15A1.5 1.5 0 0021 19v-2.5M12 3v12m0-12l-3.5 3.5M12 3l3.5 3.5"/>
                </svg>
                <span class="text-xs text-white/40 text-center px-4" id="foto-label">Arrastra o pulsa para subir fotos</span>
            </label>
            <input type="file" id="fotos" name="fotos[]" multiple accept="image/*" class="hidden"
                   onchange="document.getElementById('foto-label').textContent = this.files.length + ' archivo(s) seleccionado(s)'">
            @error('fotos.*') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="w-full sm:w-auto self-start px-8 py-3 bg-[#f0c36d] text-black text-sm font-bold uppercase tracking-widest
                       rounded hover:bg-[#e0b35d] active:scale-[0.98] transition-all">
            Solicitar cita
        </button>
    </form>
</div>

<style>
    .field-label { display:block; font-size:12px; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:6px; }
    .field-input { width:100%; background:#111; border:1px solid rgba(255,255,255,.15); border-radius:6px;
                   padding:10px 14px; color:#e5e7eb; font-size:14px; outline:none; transition:border .15s; }
    .field-input:focus { border-color:#f0c36d; }
    .field-error { margin-top:4px; font-size:12px; color:#f87171; }

    .day-chip { flex:0 0 auto; white-space:nowrap; padding:8px 14px; border-radius:8px;
                font-size:12px; color:#cbd5e1; background:#111; border:1px solid rgba(255,255,255,.12);
                cursor:pointer; transition:all .15s; }
    .day-chip:hover { border-color:rgba(240,195,109,.4); color:#fff; }
    .day-chip-activo { background:rgba(240,195,109,.15); border-color:#f0c36d; color:#f0c36d; font-weight:600; }

    .slot-chip { padding:8px 0; border-radius:8px; font-size:13px; color:#cbd5e1; background:#111;
                 border:1px solid rgba(255,255,255,.12); cursor:pointer; transition:all .15s; }
    .slot-chip:hover:not(:disabled) { border-color:rgba(240,195,109,.4); color:#fff; }
    .slot-chip-activo { background:#f0c36d; border-color:#f0c36d; color:#000; font-weight:700; }
    .slot-chip-ocupado { opacity:0.35; cursor:not-allowed; text-decoration:line-through; }
</style>
@endsection
