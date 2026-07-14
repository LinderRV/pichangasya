@extends('web.layouts.app-web')

@section('link')
@unless(config('niubiz.simulado'))
<script src="{{ config('niubiz.js_url') }}" data-client="true"></script>
@endunless
@endsection

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
    .niubiz-logo{ height:18px; vertical-align:middle; }
    .btn-pay{ background:linear-gradient(135deg,#1a6fc4,#1d4ed8); border:0; color:#fff; font-weight:900; height:48px; border-radius:999px; }
    .btn-pay:hover{ background:linear-gradient(135deg,#1559a3,#1741bc); color:#fff; }
    .btn-pay:disabled{ opacity:.65; }

    @media(max-width:767.98px){ .cancha-hero{ height:220px; } }

    /* Modal de pago con tarjeta — estilo del widget Niubiz */
    .niubiz-modal{ border-radius:14px; overflow:hidden; background:#f5f5f5; }
    .niubiz-header{ display:flex; align-items:center; justify-content:space-between; padding:14px 18px; }
    .niubiz-lang{ font-size:.72rem; font-weight:700; letter-spacing:.03em; color:rgba(17,24,39,.55); }
    .niubiz-lang strong{ color:#111; }
    .niubiz-close{ width:30px; height:30px; border-radius:50%; border:0; background:#e5e5e5; color:#555; display:flex; align-items:center; justify-content:center; font-size:.85rem; }
    .niubiz-close:hover{ background:#d9d9d9; }
    .niubiz-body{ background:#fff; margin:0 10px 10px; border-radius:12px; padding:22px 20px; }
    .niubiz-titulo{ font-weight:800; font-size:.95rem; color:#111; margin-bottom:14px; }
    .niubiz-metodo{ display:flex; align-items:flex-start; gap:12px; border:1.5px solid var(--pya-green); border-radius:10px; padding:14px; margin-bottom:18px; cursor:default; }
    .niubiz-metodo input{ display:none; }
    .niubiz-metodo-radio{ width:18px; height:18px; border-radius:50%; border:2px solid var(--pya-green); flex:0 0 auto; margin-top:2px; position:relative; }
    .niubiz-metodo-radio::after{ content:''; position:absolute; inset:3px; border-radius:50%; background:var(--pya-green); }
    .niubiz-metodo-info{ display:flex; flex-direction:column; gap:2px; }
    .niubiz-metodo-nombre{ font-weight:800; font-size:.9rem; color:#111; }
    .niubiz-metodo-sub{ font-size:.78rem; color:rgba(17,24,39,.55); margin-bottom:8px; }
    .niubiz-brands{ display:flex; flex-wrap:wrap; gap:6px; }
    .niubiz-brands.detectando .brand{ opacity:.3; transition:opacity .15s; }
    .niubiz-brands.detectando .brand.activa{ opacity:1; box-shadow:0 0 0 1.5px var(--pya-green); }
    .brand{ height:22px; display:flex; align-items:center; justify-content:center; padding:0 8px; border-radius:4px; font-size:.62rem; font-weight:900; letter-spacing:.02em; border:1px solid rgba(2,6,23,.08); background:#fff; }
    .brand-visa{ color:#1a1f71; font-style:italic; }
    .brand-mc{ padding:0 6px; gap:-6px; }
    .brand-mc i{ width:13px; height:13px; border-radius:50%; display:inline-block; }
    .brand-mc i:first-child{ background:#eb001b; margin-right:-5px; }
    .brand-mc i:last-child{ background:#f79e1b; opacity:.9; }
    .brand-diners{ color:#004a97; }
    .brand-amex{ color:#016fd0; }
    .brand-unionpay{ background:linear-gradient(90deg,#e21836 0%,#e21836 33%,#00447c 33%,#00447c 66%,#00954c 66%); color:#fff; }
    .niubiz-total{ display:flex; justify-content:space-between; align-items:center; font-size:.85rem; color:rgba(17,24,39,.6); margin-bottom:16px; }
    .niubiz-total strong{ color:var(--pya-green); font-size:1.05rem; }
    .niubiz-btn-verde{ background:var(--pya-green); border:0; color:#fff; font-weight:800; height:46px; border-radius:8px; }
    .niubiz-btn-verde:hover{ background:#157347; color:#fff; }
    .niubiz-volver{ border:0; background:none; padding:0; margin-bottom:14px; color:rgba(17,24,39,.6); font-size:.82rem; font-weight:700; }
    .niubiz-label{ font-size:.78rem; font-weight:700; color:rgba(17,24,39,.7); margin-bottom:4px; display:block; }
    .niubiz-input{ border-radius:8px; border:1.5px solid rgba(2,6,23,.15); height:44px; }
    .niubiz-input:focus{ border-color:var(--pya-green); box-shadow:0 0 0 3px rgba(25,135,84,.15); }
    .niubiz-footer{ text-align:center; font-size:.72rem; color:rgba(17,24,39,.45); padding:10px 0 16px; }
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
        <li class="breadcrumb-item"><a href="{{ route('web.paginas.inicio') }}" class="text-decoration-none link-success">Inicio</a></li>
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
                        <strong>Niubiz</strong>
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

{{-- MODAL PAGO CON TARJETA (estilo widget Niubiz) --}}
<div class="modal fade" id="modalPagoTarjeta" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg niubiz-modal">
            <div class="niubiz-header">
                <span class="niubiz-lang">ENG&nbsp;&nbsp;<strong>ESP</strong></span>
                <button type="button" class="niubiz-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="niubiz-body">
                @php
                    $brandsHtml = '
                        <span class="brand brand-visa" data-brand="visa">VISA</span>
                        <span class="brand brand-mc" data-brand="mc"><i></i><i></i></span>
                        <span class="brand brand-diners" data-brand="diners">Diners Club</span>
                        <span class="brand brand-amex" data-brand="amex">AMEX</span>
                        <span class="brand brand-unionpay" data-brand="unionpay">UnionPay</span>
                    ';
                @endphp

                {{-- PASO 1: elegir medio de pago --}}
                <div id="pagoPasoMetodo">
                    <p class="niubiz-titulo">Elige un medio de pago</p>

                    <div class="niubiz-metodo">
                        <span class="niubiz-metodo-radio"></span>
                        <span class="niubiz-metodo-info">
                            <span class="niubiz-metodo-nombre">Tarjeta de crédito y débito</span>
                            <span class="niubiz-metodo-sub">Realiza tu pago en cuotas o directo</span>
                            <span class="niubiz-brands">{!! $brandsHtml !!}</span>
                        </span>
                    </div>

                    <div class="niubiz-total">
                        <span>Total a pagar</span>
                        <strong id="modalPagoTotal"></strong>
                    </div>

                    <button type="button" id="btnContinuarMetodo" class="btn niubiz-btn-verde w-100">Continuar</button>
                </div>

                {{-- PASO 2: datos de la tarjeta --}}
                <div id="pagoPasoTarjeta" class="d-none">
                    <button type="button" id="btnVolverMetodo" class="niubiz-volver"><i class="bi bi-arrow-left me-1"></i>Volver</button>

                    <p class="niubiz-titulo">Ingresa los datos de tu tarjeta</p>
                    <div class="niubiz-brands mb-3">{!! $brandsHtml !!}</div>

                    <div class="mb-2">
                        <label class="niubiz-label">Número de tarjeta</label>
                        <input type="text" id="inputNumeroTarjeta" class="form-control niubiz-input" placeholder="0000 0000 0000 0000" maxlength="19" inputmode="numeric" autocomplete="cc-number">
                    </div>
                    <div class="mb-2">
                        <label class="niubiz-label">Nombre del titular</label>
                        <input type="text" id="inputNombreTitular" class="form-control niubiz-input" placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="niubiz-label">Vencimiento</label>
                            <input type="text" id="inputVencimiento" class="form-control niubiz-input" placeholder="MM/AA" maxlength="5" inputmode="numeric" autocomplete="cc-exp">
                        </div>
                        <div class="col-5">
                            <label class="niubiz-label">CVV</label>
                            <input type="text" id="inputCvv" class="form-control niubiz-input" placeholder="123" maxlength="3" inputmode="numeric" autocomplete="cc-csc">
                        </div>
                    </div>

                    <div id="errorPagoModal" class="alert alert-danger d-none py-2 small mb-3"></div>

                    <button type="button" id="btnConfirmarPagoTarjeta" class="btn niubiz-btn-verde w-100">
                        <i class="bi bi-lock-fill me-2"></i>Pagar
                    </button>
                </div>
            </div>

            <div class="niubiz-footer"><i class="bi bi-shield-lock me-1"></i>Powered by <strong>niubiz</strong></div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
(function(){
    const isAuth       = {{ auth()->check() ? 'true' : 'false' }};
    const idCancha     = {{ $cancha->id }};
    const slotsUrl     = "{{ url('web/slots') }}";
    const sesionUrl    = "{{ route('cliente.niubiz.sesion') }}";
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

    let niubizActionUrl = null;

    const $modalPagoEl        = document.getElementById('modalPagoTarjeta');
    const modalPago            = $modalPagoEl ? new bootstrap.Modal($modalPagoEl) : null;
    const $modalPagoTotal      = document.getElementById('modalPagoTotal');
    const $pagoPasoMetodo      = document.getElementById('pagoPasoMetodo');
    const $pagoPasoTarjeta     = document.getElementById('pagoPasoTarjeta');
    const $btnContinuarMetodo  = document.getElementById('btnContinuarMetodo');
    const $btnVolverMetodo     = document.getElementById('btnVolverMetodo');
    const $inputNumeroTarjeta  = document.getElementById('inputNumeroTarjeta');
    const $inputNombreTitular  = document.getElementById('inputNombreTitular');
    const $inputVencimiento    = document.getElementById('inputVencimiento');
    const $inputCvv            = document.getElementById('inputCvv');
    const $errorPagoModal      = document.getElementById('errorPagoModal');
    const $btnConfirmarPago    = document.getElementById('btnConfirmarPagoTarjeta');
    const $brandsPaso2         = document.querySelector('#pagoPasoTarjeta .niubiz-brands');

    function mostrarPasoMetodo(){
        $pagoPasoTarjeta.classList.add('d-none');
        $pagoPasoMetodo.classList.remove('d-none');
    }
    function mostrarPasoTarjeta(){
        $pagoPasoMetodo.classList.add('d-none');
        $pagoPasoTarjeta.classList.remove('d-none');
    }

    $btnContinuarMetodo && $btnContinuarMetodo.addEventListener('click', mostrarPasoTarjeta);
    $btnVolverMetodo && $btnVolverMetodo.addEventListener('click', mostrarPasoMetodo);

    function detectarMarca(numero){
        if(/^4/.test(numero))                     return 'visa';
        if(/^(5[1-5]|2[2-7])/.test(numero))       return 'mc';
        if(/^3[47]/.test(numero))                 return 'amex';
        if(/^3(?:0[0-5]|[68])/.test(numero))      return 'diners';
        if(/^62/.test(numero))                    return 'unionpay';
        return null;
    }

    $inputNumeroTarjeta && $inputNumeroTarjeta.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,16).replace(/(\d{4})(?=\d)/g,'$1 ');

        const numero = this.value.replace(/\s/g,'');
        const marca  = numero ? detectarMarca(numero) : null;

        if(!$brandsPaso2) return;
        $brandsPaso2.classList.toggle('detectando', !!numero);
        $brandsPaso2.querySelectorAll('.brand').forEach(function(el){
            el.classList.toggle('activa', el.dataset.brand === marca);
        });
    });
    $inputVencimiento && $inputVencimiento.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,4).replace(/(\d{2})(?=\d)/,'$1/');
    });
    $inputCvv && $inputCvv.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,3);
    });

    function resetBotonConfirmarPago(){
        $btnConfirmarPago.disabled = false;
        $btnConfirmarPago.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Pagar';
    }

    $btnConfirmarPago && $btnConfirmarPago.addEventListener('click', function(){
        $errorPagoModal.classList.add('d-none');

        if(!$inputNombreTitular.value.trim()){
            $errorPagoModal.textContent = 'Ingresa el nombre del titular.';
            $errorPagoModal.classList.remove('d-none');
            return;
        }

        $btnConfirmarPago.disabled = true;
        $btnConfirmarPago.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

        fetch(niubizActionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                numero_tarjeta: $inputNumeroTarjeta.value,
                nombre_titular: $inputNombreTitular.value,
                vencimiento:    $inputVencimiento.value,
                cvv:            $inputCvv.value,
            }),
        })
        .then(r => r.json())
        .then(res => {
            if(!res.status){
                $errorPagoModal.textContent = res.message || 'No se pudo procesar el pago.';
                $errorPagoModal.classList.remove('d-none');
                resetBotonConfirmarPago();
                return;
            }
            window.location.href = res.data.redirect;
        })
        .catch(function(){
            $errorPagoModal.textContent = 'Error de conexión. Intenta de nuevo.';
            $errorPagoModal.classList.remove('d-none');
            resetBotonConfirmarPago();
        });
    });

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

        $btnPagar.disabled = true;
        $btnPagar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparando pago...';
        $errorPago.classList.add('d-none');

        // Paso 1: Crear sesión Niubiz en el backend
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

            const { session_key, purchase_number, amount, merchant_id, action_url, simulado } = res.data;

            $btnPagar.disabled = false;
            $btnPagar.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pagar con tarjeta';

            if(simulado){
                // Paso 2 (simulado): abrir modal propio con el estilo del widget Niubiz
                niubizActionUrl = action_url;
                $errorPagoModal.classList.add('d-none');
                $inputNumeroTarjeta.value = '';
                $inputNombreTitular.value = '';
                $inputVencimiento.value   = '';
                $inputCvv.value           = '';
                $brandsPaso2 && $brandsPaso2.classList.remove('detectando');
                $modalPagoTotal.textContent = `S/ ${amount}`;
                resetBotonConfirmarPago();
                mostrarPasoMetodo();
                modalPago.show();
                return;
            }

            // Paso 2: Abrir checkout de Niubiz
            VisanetCheckout.configure({
                sessiontoken:   session_key,
                channel:        'web',
                merchantid:     merchant_id,
                purchasenumber: purchase_number,
                amount:         amount,
                currency:       'PEN',
                action:         action_url,
                timeouturl:     window.location.href,
                complete: function(params){
                    // Callback cuando Niubiz termina (algunos browsers lo disparan antes del redirect)
                    console.log('Niubiz complete:', params);
                },
            });

            VisanetCheckout.open();
        })
        .catch(err => {
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
