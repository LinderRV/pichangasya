@extends('web.layouts.app-web')

@section('content')
<style>
    :root{ --pya-green:#198754; --pya-dark:#0b1220; }
    .muted{ color:rgba(17,24,39,.62); }
    .section-title{ font-weight:900; letter-spacing:-.01em; }

    .hero-wrap{ position:relative; overflow:hidden; border-radius:18px; min-height:420px; background:var(--pya-dark); }
    .hero-bg{ position:absolute; inset:0; background-image:linear-gradient(90deg,rgba(11,18,32,.80) 0%,rgba(11,18,32,.42) 55%,rgba(11,18,32,.12) 100%),url('/images/1.jpg'); background-size:cover; background-position:center; transform:scale(1.03); filter:saturate(1.05); }
    .hero-content{ position:relative; z-index:1; padding:54px 44px 28px; }
    .hero-title{ font-weight:900; letter-spacing:-.02em; line-height:1.05; text-shadow:0 12px 30px rgba(0,0,0,.28); }
    .hero-sub{ max-width:520px; color:rgba(255,255,255,.88); font-weight:600; }
    .hero-actions .btn{ height:44px; display:inline-flex; align-items:center; gap:.5rem; }
    .btn-outline-hero{ color:rgba(255,255,255,.92); border-color:rgba(255,255,255,.40); background:rgba(0,0,0,.14); }
    .btn-outline-hero:hover{ color:#fff; border-color:rgba(255,255,255,.55); background:rgba(0,0,0,.18); }

    .search-card{ margin-top:-28px; border-radius:16px; box-shadow:0 18px 50px rgba(2,6,23,.12); border:1px solid rgba(2,6,23,.06); }
    .search-card .card-body{ background:#fff; border-radius:16px; }
    .search-field{ border-radius:14px; border:1px solid rgba(2,6,23,.10); background:#fff; padding:.55rem .8rem; height:54px; display:flex; gap:.55rem; align-items:center; }
    .search-ico{ color:rgba(17,24,39,.58); font-size:1rem; }
    .search-control{ border:0!important; padding:0!important; height:auto!important; box-shadow:none!important; background:transparent!important; font-weight:700; color:#111827; }
    .search-control::placeholder{ color:rgba(17,24,39,.55); font-weight:700; }

    .pitch-card{ border-radius:16px; overflow:hidden; border:1px solid rgba(2,6,23,.06); box-shadow:0 16px 44px rgba(2,6,23,.08); height:100%; background:#fff; }
    .pitch-thumb{ height:160px; background-size:cover; background-position:center; position:relative; }
    .badge-availability{ position:absolute; top:12px; left:12px; padding:.28rem .6rem; border-radius:999px; font-weight:900; font-size:.75rem; backdrop-filter:blur(6px); box-shadow:0 10px 20px rgba(0,0,0,.12); }
    .badge-ok{ background:rgba(25,135,84,.92); color:#fff; }
    .pitch-title{ font-weight:900; }
    .pitch-type{ color:var(--pya-green); font-weight:900; font-size:.82rem; }
    .pitch-loc{ font-size:.83rem; }
    .price-stack{ text-align:right; line-height:1.05; }
    .price-stack .price{ font-weight:900; font-size:1.05rem; }
    .price-stack .unit{ color:rgba(17,24,39,.55); font-size:.74rem; font-weight:700; }
    .btn-availability{ height:34px; padding:.35rem 1rem; font-weight:900; border-radius:999px; min-width:150px; }

    .feature-tile{ border-radius:16px; border:1px solid rgba(25,135,84,.18); background:rgba(25,135,84,.05); padding:14px; height:100%; }
    .feature-ico{ width:42px; height:42px; border-radius:14px; background:rgba(25,135,84,.12); color:var(--pya-green); display:flex; align-items:center; justify-content:center; flex:0 0 auto; font-size:1.1rem; }

    @media(max-width:991.98px){ .hero-content{ padding:32px 20px 20px; } .hero-wrap{ min-height:340px; } .search-card{ margin-top:-18px; } }
</style>

{{-- HERO --}}
<section class="hero-wrap">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="hero-content">
        <div class="row align-items-end g-3">
            <div class="col-12 col-lg-8">
                <h1 class="hero-title text-white display-5 mb-3">
                    Reserva tu cancha deportiva<br class="d-none d-md-block"> de forma rápida y segura
                </h1>
                <p class="hero-sub mb-4">Consulta disponibilidad en tiempo real y encuentra el espacio ideal para tu partido.</p>
                <div class="d-flex flex-wrap gap-2 hero-actions">
                    <a href="#canchas" class="btn btn-success btn-pill"><i class="bi bi-search me-2"></i>Ver canchas disponibles</a>
                    <a href="#como-funciona" class="btn btn-outline-hero btn-pill"><i class="bi bi-play-circle me-2"></i>Cómo funciona</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- BUSCADOR --}}
<div class="card search-card">
    <div class="card-body p-3 p-lg-4">
        <form class="row g-2 g-lg-3" method="GET" action="{{ route('web.paginas.canchas') }}" autocomplete="off">
            <div class="col-12 col-lg-4">
                <div class="search-field">
                    <i class="bi bi-geo-alt search-ico"></i>
                    <select class="form-select search-control" name="id_distrito">
                        <option value="">Todos los distritos</option>
                        @foreach($distritos as $d)
                            <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="search-field">
                    <i class="bi bi-dribbble search-ico"></i>
                    <select class="form-select search-control" name="id_tipo_cancha">
                        <option value="">Todas las canchas</option>
                        @foreach($tipoCanchas as $t)
                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-4 d-grid">
                <button class="btn btn-success btn-pill" style="height:54px" type="submit">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CANCHAS --}}
<section class="mt-4 mt-lg-5" id="canchas">
    <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
        <div>
            <h3 class="section-title mb-1">Canchas disponibles</h3>
            <div class="muted">Reserva tu espacio favorito hoy mismo.</div>
        </div>
        <a href="{{ route('web.paginas.canchas') }}" class="link-success fw-bold text-decoration-none">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>

    @if($canchas->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
            No hay canchas disponibles en este momento.
        </div>
    @else
    <div class="row g-3">
        @foreach($canchas as $cancha)
        <div class="col-12 col-md-6 col-xl-3">
            <div class="pitch-card">
                <div class="pitch-thumb" style="background-image:url('{{ $cancha->foto ? asset($cancha->foto) : asset('/images/1.jpg') }}');">
                    <span class="badge-availability badge-ok">Disponible</span>
                </div>
                <div class="p-3">
                    <div class="pitch-title">{{ $cancha->nombre }}</div>
                    <div class="pitch-type">{{ optional($cancha->tipoCancha)->nombre }}</div>
                    <div class="pitch-loc muted mt-1">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ optional(optional(optional($cancha->complejo)->distrito))->nombre ?? 'Sin ubicación' }}
                        @if($cancha->complejo)
                            — {{ $cancha->complejo->nombre }}
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div class="price-stack">
                            <div class="price">S/ {{ number_format($cancha->precio_hora, 0) }}</div>
                            <div class="unit">por hora</div>
                        </div>
                        <a class="btn btn-success btn-availability" href="{{ route('web.paginas.cancha', $cancha->id) }}">
                            Ver disponibilidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- CÓMO FUNCIONA --}}
<section class="mt-4 mt-lg-5" id="como-funciona">
    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-search"></i></div>
                    <div><div class="fw-bold">Busca tu cancha</div><div class="small muted">Filtra por ubicación y tipo de cancha.</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-calendar-check"></i></div>
                    <div><div class="fw-bold">Elige fecha y hora</div><div class="small muted">Consulta disponibilidad en tiempo real.</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-credit-card"></i></div>
                    <div><div class="fw-bold">Paga online</div><div class="small muted">Yape, Plin o tarjeta. Rápido y seguro.</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-check-circle"></i></div>
                    <div><div class="fw-bold">¡Listo!</div><div class="small muted">Recibe tu confirmación y ve a jugar.</div></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection
