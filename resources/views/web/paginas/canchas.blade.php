@extends('web.layouts.app-web')

@section('content')
<style>
    :root{ --pya-green:#198754; }
    .muted{ color:rgba(17,24,39,.62); }
    .section-title{ font-weight:900; letter-spacing:-.01em; }

    .filter-card{ border-radius:16px; border:1px solid rgba(2,6,23,.07); box-shadow:0 8px 30px rgba(2,6,23,.07); background:#fff; }
    .search-field{ border-radius:14px; border:1px solid rgba(2,6,23,.10); background:#fff; padding:.55rem .8rem; height:52px; display:flex; gap:.55rem; align-items:center; }
    .search-ico{ color:rgba(17,24,39,.58); font-size:1rem; flex:0 0 auto; }
    .search-control{ border:0!important; padding:0!important; height:auto!important; box-shadow:none!important; background:transparent!important; font-weight:700; color:#111827; }
    .search-control::placeholder{ color:rgba(17,24,39,.55); font-weight:700; }

    .pitch-card{ border-radius:16px; overflow:hidden; border:1px solid rgba(2,6,23,.06); box-shadow:0 16px 44px rgba(2,6,23,.08); height:100%; background:#fff; }
    .pitch-thumb{ height:160px; background-size:cover; background-position:center; position:relative; }
    .badge-availability{ position:absolute; top:12px; left:12px; padding:.28rem .6rem; border-radius:999px; font-weight:900; font-size:.75rem; backdrop-filter:blur(6px); box-shadow:0 10px 20px rgba(0,0,0,.12); }
    .badge-ok{ background:rgba(25,135,84,.92); color:#fff; }
    .pitch-title{ font-weight:900; }
    .pitch-type{ color:var(--pya-green); font-weight:900; font-size:.82rem; }
    .price-stack{ text-align:right; line-height:1.05; }
    .price-stack .price{ font-weight:900; font-size:1.05rem; }
    .price-stack .unit{ color:rgba(17,24,39,.55); font-size:.74rem; font-weight:700; }
    .btn-availability{ height:34px; padding:.35rem 1rem; font-weight:900; border-radius:999px; }
</style>

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-end mb-3">
    <div>
        <h3 class="section-title mb-1">Canchas deportivas</h3>
        <div class="muted">{{ $canchas->count() }} cancha(s) encontrada(s)</div>
    </div>
    <a href="{{ route('web.paginas.inicio') }}" class="link-secondary text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Volver al inicio
    </a>
</div>

{{-- FILTROS --}}
<div class="filter-card p-3 p-lg-4 mb-4">
    <form class="row g-2 g-lg-3" method="GET" action="{{ route('web.paginas.canchas') }}" autocomplete="off">
        <div class="col-12 col-lg-4">
            <div class="search-field">
                <i class="bi bi-geo-alt search-ico"></i>
                <select class="form-select search-control" name="id_distrito">
                    <option value="">Todos los distritos</option>
                    @foreach($distritos as $d)
                        <option value="{{ $d->id }}" {{ request('id_distrito') == $d->id ? 'selected' : '' }}>
                            {{ $d->nombre }}
                        </option>
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
                        <option value="{{ $t->id }}" {{ request('id_tipo_cancha') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 col-lg-2">
            <button class="btn btn-success btn-pill w-100" style="height:52px" type="submit">
                <i class="bi bi-search me-2"></i>Filtrar
            </button>
        </div>
        @if(request('id_distrito') || request('id_tipo_cancha'))
        <div class="col-12 col-lg-2">
            <a href="{{ route('web.paginas.canchas') }}" class="btn btn-outline-secondary btn-pill w-100" style="height:52px">
                <i class="bi bi-x-circle me-2"></i>Limpiar
            </a>
        </div>
        @endif
    </form>
</div>

{{-- RESULTADOS --}}
@if($canchas->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-search text-muted d-block fs-1 mb-3"></i>
        <h5 class="fw-bold">No encontramos canchas</h5>
        <p class="muted">Intenta con otros filtros.</p>
        <a href="{{ route('web.paginas.canchas') }}" class="btn btn-success btn-pill">Ver todas</a>
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
                <div class="muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ optional(optional($cancha->complejo)->distrito)->nombre ?? 'Sin ubicación' }}
                    @if($cancha->complejo) — {{ $cancha->complejo->nombre }} @endif
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

@endsection
