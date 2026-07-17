@extends('web.layouts.app-web')

@section('title', $cancha->nombre . ' | PichangasYa')
@section('meta_description', 'Consulta información, precio y horarios disponibles para reservar ' . $cancha->nombre . '.')

@section('content')
<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    :root{ --pya-green:#198754; }
    .muted{ color:rgba(17,24,39,.62); }

    .cancha-hero{ border-radius:20px; overflow:hidden; height:320px; background:#111; position:relative; }
    .cancha-hero img{ width:100%; height:100%; object-fit:cover; }
    .cancha-hero-grad{ position:absolute; inset:0; background:linear-gradient(to top,rgba(11,18,32,.7) 0%,rgba(0,0,0,0) 55%); }
    .cancha-hero-info{ position:absolute; bottom:0; left:0; right:0; padding:22px 22px; }

    .detail-card{ border-radius:16px; border:1px solid rgba(2,6,23,.07); box-shadow:0 16px 44px rgba(2,6,23,.07); background:#fff; }
    .section-label{ font-size:.78rem; font-weight:900; text-transform:uppercase; letter-spacing:.06em; color:rgba(17,24,39,.55); }

    .datepicker-wrap input[type="date"]{ border-radius:14px; border:1px solid rgba(2,6,23,.12); height:52px; padding:.55rem 1rem; font-weight:700; background:#fff; cursor:pointer; }
    .datepicker-wrap input[type="date"]:focus{ box-shadow:0 0 0 3px rgba(25,135,84,.18); border-color:var(--pya-green); outline:none; }

    .slots-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:.7rem; }
    .slot-btn{ border-radius:12px; border:2px solid rgba(25,135,84,.25); background:#fff; padding:.7rem .6rem; text-align:center; cursor:pointer; transition:all .15s; }
    .slot-btn:hover,.slot-btn.selected{ border-color:var(--pya-green); background:rgba(25,135,84,.06); }
    .slot-time{ font-weight:900; font-size:.95rem; }
    .slot-price{ font-size:.78rem; color:rgba(17,24,39,.62); font-weight:700; }

    .duracion-grid{ display:flex; flex-wrap:wrap; gap:.5rem; }
    .duracion-btn{ border-radius:999px; border:2px solid rgba(25,135,84,.25); background:#fff; padding:.5rem 1rem; font-weight:800; font-size:.85rem; cursor:pointer; transition:all .15s; }
    .duracion-btn:hover,.duracion-btn.selected{ border-color:var(--pya-green); background:var(--pya-green); color:#fff; }

    #slotsState{ min-height:100px; }
    .slots-loading{ display:flex; align-items:center; justify-content:center; gap:.6rem; color:rgba(17,24,39,.55); padding:2rem; }

    .badge-pill{ border-radius:999px; padding:.3rem .7rem; font-size:.8rem; font-weight:800; }
    .btn-pay{ background:linear-gradient(135deg,#1a6fc4,#1d4ed8); border:0; color:#fff; font-weight:900; height:48px; border-radius:999px; }
    .btn-pay:hover{ background:linear-gradient(135deg,#1559a3,#1741bc); color:#fff; }
    .btn-pay:disabled{ opacity:.65; }

    @media(max-width:767.98px){ .cancha-hero{ height:220px; } }
</style>

{{-- Flash de error --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-3" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- BREADCRUMB --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0" style="font-size:.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none link-success">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('web.paginas.canchas') }}" class="text-decoration-none link-success">Canchas</a></li>
        <li class="breadcrumb-item active">{{ $cancha->nombre }}</li>
    </ol>
</nav>

<div class="row g-4">

    {{-- COLUMNA IZQUIERDA --}}
    <div class="col-12 col-lg-8">

        {{-- HERO --}}
        <div class="cancha-hero mb-4">
            <img src="{{ $cancha->foto ? asset($cancha->foto) : asset('/images/1.jpg') }}" alt="{{ $cancha->nombre }}">
            <div class="cancha-hero-grad"></div>
            <div class="cancha-hero-info">
                <span class="badge bg-success badge-pill me-2">{{ optional($cancha->tipoCancha)->nombre }}</span>
                @if($cancha->capacidad)
                    <span class="badge bg-dark badge-pill"><i class="bi bi-people me-1"></i>{{ $cancha->capacidad }} jugadores</span>
                @endif
                <h1 class="text-white fw-bold mt-2 mb-0 h4">{{ $cancha->nombre }}</h1>
                <div class="text-white-50 small">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ optional(optional($cancha->complejo)->distrito)->nombre }}
                    @if($cancha->complejo) — {{ $cancha->complejo->nombre }} @endif
                </div>
            </div>
        </div>

        @if($cancha->descripcion)
        <div class="detail-card p-4 mb-4">
            <div class="section-label mb-2">Descripción</div>
            <p class="mb-0">{{ $cancha->descripcion }}</p>
        </div>
        @endif

        @if($cancha->complejo)
        <div class="detail-card p-4 mb-4">
            <div class="section-label mb-3">Información del establecimiento</div>
            <h2 class="h5 fw-bold mb-2">{{ $cancha->complejo->nombre }}</h2>
            @if($cancha->complejo->descripcion)
                <p class="text-muted">{{ $cancha->complejo->descripcion }}</p>
            @endif
            <div class="row g-3 small">
                @if($cancha->complejo->direccion)
                <div class="col-12 col-md-6">
                    <div class="d-flex gap-2"><i class="bi bi-geo-alt text-success"></i><span>{{ $cancha->complejo->direccion }}</span></div>
                </div>
                @endif
                @if($cancha->complejo->correo)
                <div class="col-12 col-md-6">
                    <div class="d-flex gap-2"><i class="bi bi-envelope text-success"></i><a class="link-success" href="mailto:{{ $cancha->complejo->correo }}">{{ $cancha->complejo->correo }}</a></div>
                </div>
                @endif
                @if($cancha->complejo->telefono)
                <div class="col-12 col-md-6">
                    <div class="d-flex gap-2"><i class="bi bi-telephone text-success"></i><span>{{ $cancha->complejo->telefono }}</span></div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="detail-card p-4 mb-4">
            <div class="section-label mb-3">Antes de reservar</div>
            <div class="row g-3 small">
                <div class="col-12 col-md-4"><i class="bi bi-check-circle text-success me-1"></i>La reserva se confirma después del pago.</div>
                <div class="col-12 col-md-4"><i class="bi bi-clock text-success me-1"></i>Llega con anticipación y presenta tu código.</div>
                <div class="col-12 col-md-4"><i class="bi bi-arrow-repeat text-success me-1"></i>Los cambios se coordinan con el complejo.</div>
            </div>
        </div>

        {{-- FECHA --}}
        <div class="detail-card p-4 mb-4">
            <div class="section-label mb-3">Selecciona una fecha</div>
            <div class="datepicker-wrap">
                <input type="date" id="fechaSeleccionada" class="form-control"
                    min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                    style="max-width:260px;">
            </div>
        </div>

        {{-- DURACIÓN --}}
        <div class="detail-card p-4 mb-4">
            <div class="section-label mb-3">¿Por cuánto tiempo?</div>
            <div class="duracion-grid" id="duracionGrid">
                @foreach($duraciones as $min)
                    <button type="button" class="duracion-btn @if($min === 60) selected @endif" data-minutos="{{ $min }}">
                        {{ $min % 60 === 0 ? intdiv($min, 60) . 'h' : intdiv($min, 60) . 'h' . ($min % 60) }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- SLOTS --}}
        <div class="detail-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-label">Horarios disponibles</div>
                <div id="fechaLabel" class="small fw-bold text-muted"></div>
            </div>
            <div id="slotsState">
                <div class="slots-loading"><span class="spinner-border spinner-border-sm"></span> Cargando horarios...</div>
            </div>
        </div>

    </div>

    {{-- COLUMNA DERECHA --}}
    <div class="col-12 col-lg-4">
        <div class="detail-card p-4 sticky-top" style="top:90px;">

            <div class="section-label mb-3">Resumen</div>

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="fw-bold">{{ $cancha->nombre }}</div>
                    <div class="small muted">{{ optional($cancha->tipoCancha)->nombre }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-success fs-5">S/ {{ number_format($cancha->precio_hora, 0) }}</div>
                    <div class="small muted">por hora</div>
                </div>
            </div>

            <div id="resumenSlot" class="d-none">
                <div class="border rounded-3 p-3 mb-3 bg-light">
                    <div class="small fw-bold text-muted mb-1">Slot seleccionado</div>
                    <div id="resumenFecha" class="fw-bold"></div>
                    <div id="resumenHorario" class="text-success fw-bold"></div>
                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                        <span class="fw-bold">Total a pagar:</span>
                        <span id="resumenTotal" class="fw-bold text-success fs-5"></span>
                    </div>
                </div>

                <button id="btnPagar" class="btn btn-pay w-100">
                    <i class="bi bi-credit-card me-2"></i>Pagar con tarjeta
                </button>
                <div class="text-center mt-2">
                    <small class="text-muted">Pago seguro via
                        <strong>Stripe</strong>
                        &nbsp;<i class="bi bi-shield-check text-success"></i>
                    </small>
                </div>
                <div id="errorPago" class="alert alert-danger d-none mt-2 py-2 small"></div>
            </div>

            <div id="resumenVacio" class="text-center py-3">
                <i class="bi bi-calendar3 text-muted d-block mb-2 fs-3"></i>
                <div class="small muted">Selecciona un horario para continuar.</div>
            </div>

            @if($cancha->complejo && $cancha->complejo->direccion)
            <hr>
            <div class="section-label mb-2">Dirección</div>
            <div class="small"><i class="bi bi-geo-alt me-1 text-success"></i>{{ $cancha->complejo->direccion }}</div>
            @endif

            @if($cancha->complejo && $cancha->complejo->telefono)
            <div class="mt-2">
                <a href="https://wa.me/51{{ preg_replace('/\D/', '', $cancha->complejo->telefono) }}" target="_blank" class="btn btn-outline-success w-100 btn-pill btn-sm">
                    <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
                </a>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- MODAL LOGIN --}}
<div class="modal fade" id="modalLogin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-lock-fill text-warning fs-2 mb-3 d-block"></i>
                <h6 class="fw-bold mb-2">Necesitas iniciar sesión</h6>
                <p class="small muted mb-3">Para reservar debes tener una cuenta en PichangasYa.</p>
                <div class="d-grid gap-2">
                    <a id="btnLoginRedir" href="/login" class="btn btn-success btn-pill">Iniciar sesión</a>
                    <a href="/register" class="btn btn-outline-secondary btn-pill">Crear cuenta</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
(function(){
    const isAuth       = {{ auth()->check() ? 'true' : 'false' }};
    const isCliente    = {{ auth()->check() && auth()->user()->esCliente() ? 'true' : 'false' }};
    const idCancha     = {{ $cancha->id }};
    const slotsUrl     = "{{ url('web/slots') }}";
    const sesionUrl    = "{{ route('cliente.stripe.sesion') }}";
    const csrfToken    = "{{ csrf_token() }}";

    let slotSeleccionado   = null;
    let duracionMinutos    = 60;

    const $fechaInput   = document.getElementById('fechaSeleccionada');
    const $duracionGrid = document.getElementById('duracionGrid');
    const $slotsState   = document.getElementById('slotsState');
    const $fechaLabel   = document.getElementById('fechaLabel');
    const $resumenSlot  = document.getElementById('resumenSlot');
    const $resumenVacio = document.getElementById('resumenVacio');
    const $btnPagar     = document.getElementById('btnPagar');
    const $errorPago    = document.getElementById('errorPago');

    function formatFecha(f){
        const [y,m,d] = f.split('-');
        const dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const dt = new Date(y, m-1, d);
        return `${dias[dt.getDay()]} ${d} ${meses[m-1]} ${y}`;
    }

    function cargarSlots(fecha){
        slotSeleccionado = null;
        mostrarResumenVacio();
        $fechaLabel.textContent = formatFecha(fecha);
        $slotsState.innerHTML = `<div class="slots-loading"><span class="spinner-border spinner-border-sm"></span> Cargando horarios...</div>`;

        fetch(`${slotsUrl}/${idCancha}/${fecha}?duracion=${duracionMinutos}`)
            .then(r => r.json())
            .then(res => {
                if(!res.status || !res.data || res.data.length === 0){
                    $slotsState.innerHTML = `<div class="text-center py-4 text-muted"><i class="bi bi-calendar-x d-block fs-2 mb-2"></i>No hay horarios disponibles para esta fecha.</div>`;
                    return;
                }
                let html = '<div class="slots-grid">';
                res.data.forEach(s => {
                    html += `<div class="slot-btn" data-inicio="${s.hora_inicio}" data-fin="${s.hora_fin}" data-total="${s.total}">
                                <div class="slot-time">${s.hora_inicio} – ${s.hora_fin}</div>
                                <div class="slot-price">S/ ${parseFloat(s.total).toFixed(0)}</div>
                             </div>`;
                });
                html += '</div>';
                $slotsState.innerHTML = html;

                $slotsState.querySelectorAll('.slot-btn').forEach(btn => {
                    btn.addEventListener('click', function(){
                        $slotsState.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        slotSeleccionado = {
                            fecha:       $fechaInput.value,
                            hora_inicio: this.dataset.inicio,
                            hora_fin:    this.dataset.fin,
                            total:       parseFloat(this.dataset.total)
                        };
                        mostrarResumenSlot(slotSeleccionado);
                    });
                });
            })
            .catch(() => {
                $slotsState.innerHTML = `<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle d-block fs-2 mb-2"></i>Error al cargar horarios.</div>`;
            });
    }

    function mostrarResumenSlot(s){
        document.getElementById('resumenFecha').textContent   = formatFecha(s.fecha);
        document.getElementById('resumenHorario').textContent = `${s.hora_inicio} – ${s.hora_fin}`;
        document.getElementById('resumenTotal').textContent   = `S/ ${s.total.toFixed(0)}`;
        $resumenSlot.classList.remove('d-none');
        $resumenVacio.classList.add('d-none');
        $errorPago.classList.add('d-none');
        $btnPagar.disabled = false;
        $btnPagar.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pagar con tarjeta';
    }

    function mostrarResumenVacio(){
        $resumenSlot.classList.add('d-none');
        $resumenVacio.classList.remove('d-none');
    }

    $btnPagar.addEventListener('click', function(){
        if(!slotSeleccionado) return;

        if(!isAuth){
            document.getElementById('btnLoginRedir').href = `/login?redirect=${encodeURIComponent(window.location.href)}`;
            new bootstrap.Modal(document.getElementById('modalLogin')).show();
            return;
        }

        if(!isCliente){
            $errorPago.textContent = 'Esta cuenta no puede reservar. Inicia sesión con una cuenta de cliente para pagar.';
            $errorPago.classList.remove('d-none');
            return;
        }

        $btnPagar.disabled = true;
        $btnPagar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirigiendo a Stripe...';
        $errorPago.classList.add('d-none');

        // Crea la Checkout Session en el backend y redirige a la página hospedada de Stripe
        fetch(sesionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                id_cancha:   idCancha,
                fecha:       slotSeleccionado.fecha,
                hora_inicio: slotSeleccionado.hora_inicio,
                hora_fin:    slotSeleccionado.hora_fin,
            }),
        })
        .then(r => r.json())
        .then(res => {
            if(!res.status){
                $errorPago.textContent = res.message || 'Error al iniciar el pago.';
                $errorPago.classList.remove('d-none');
                $btnPagar.disabled = false;
                $btnPagar.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pagar con tarjeta';
                return;
            }

            window.location.href = res.data.url;
        })
        .catch(function(){
            $errorPago.textContent = 'Error de conexión con la pasarela de pago.';
            $errorPago.classList.remove('d-none');
            $btnPagar.disabled = false;
            $btnPagar.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pagar con tarjeta';
        });
    });

    $fechaInput.addEventListener('change', function(){ cargarSlots(this.value); });

    $duracionGrid.querySelectorAll('.duracion-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            $duracionGrid.querySelectorAll('.duracion-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            duracionMinutos = parseInt(this.dataset.minutos, 10);
            cargarSlots($fechaInput.value);
        });
    });

    cargarSlots($fechaInput.value);
})();
</script>
@endsection
