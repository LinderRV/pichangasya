@extends('web.layouts.app-web')

@section('content')
<style>
    :root{
        --pya-green: #198754;
        --pya-dark: #0b1220;
    }

    .muted{ color: rgba(17,24,39,.62); }
    .section-title{ font-weight: 900; letter-spacing: -.01em; }

    /* HERO  */
    .hero-wrap{
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        min-height: 420px;
        background: var(--pya-dark);
    }
    .hero-bg{
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(90deg, rgba(11,18,32,.80) 0%, rgba(11,18,32,.42) 55%, rgba(11,18,32,.12) 100%),
            url('/images/1.jpg');
        background-size: cover;
        background-position: center;
        transform: scale(1.03);
        filter: saturate(1.05);
    }
    .hero-content{ position: relative; z-index: 1; padding: 54px 44px 28px; }
    .hero-title{ font-weight: 900; letter-spacing: -.02em; line-height: 1.05; text-shadow: 0 12px 30px rgba(0,0,0,.28); }
    .hero-sub{ max-width: 520px; color: rgba(255,255,255,.88); font-weight: 600; }

    .hero-actions .btn{
        height: 44px;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }
    .btn-outline-hero{
        color: rgba(255,255,255,.92);
        border-color: rgba(255,255,255,.40);
        background: rgba(0,0,0,.14);
    }
    .btn-outline-hero:hover{
        color: #fff;
        border-color: rgba(255,255,255,.55);
        background: rgba(0,0,0,.18);
    }

    /* Search bar */
    .search-card{
        margin-top: -28px;
        border-radius: 16px;
        box-shadow: 0 18px 50px rgba(2,6,23,.12);
        border: 1px solid rgba(2,6,23,.06);
    }
    .search-card .card-body{ background: #fff; border-radius: 16px; }
    .search-grid{ align-items: center; }
    .search-field{
        border-radius: 14px;
        border: 1px solid rgba(2,6,23,.10);
        background: #fff;
        padding: .55rem .8rem;
        height: 54px;
        display: flex;
        gap: .55rem;
        align-items: center;
    }
    .search-field[data-sep="true"]{ position: relative; }
    .search-field[data-sep="true"]:after{
        content: "";
        position: absolute;
        right: 10px;
        top: 12px;
        bottom: 12px;
        width: 1px;
        background: rgba(2,6,23,.08);
        display: none;
    }
    .search-ico{ color: rgba(17,24,39,.58); font-size: 1rem; }
    .search-control{
        border: 0 !important;
        padding: 0 !important;
        height: auto !important;
        box-shadow: none !important;
        background: transparent !important;
        font-weight: 700;
        color: #111827;
    }
    .search-control:focus{ outline: none; }

    .search-control::-webkit-calendar-picker-indicator{ opacity: .6; }
    .search-control::placeholder{ color: rgba(17,24,39,.55); font-weight: 700; }

    /* Cards  */
    .pitch-card{
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(2,6,23,.06);
        box-shadow: 0 16px 44px rgba(2,6,23,.08);
        height: 100%;
        background: #fff;
    }
    .pitch-thumb{ height: 160px; background-size: cover; background-position: center; position: relative; }
    .badge-availability{
        position: absolute;
        top: 12px;
        left: 12px;
        padding: .28rem .6rem;
        border-radius: 999px;
        font-weight: 900;
        font-size: .75rem;
        backdrop-filter: blur(6px);
        box-shadow: 0 10px 20px rgba(0,0,0,.12);
    }
    .badge-ok{ background: rgba(25,135,84,.92); color:#fff; }
    .badge-low{ background: rgba(255,140,0,.92); color:#fff; }
    .fav-btn{
        position: absolute;
        top: 10px;
        right: 10px;
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(2,6,23,.10);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(17,24,39,.82);
    }
    .fav-btn:hover{ background: #fff; }
    .pitch-meta{ display: grid; gap: .15rem; }

    .pitch-title{ font-weight: 900; }
    .pitch-type{ color: var(--pya-green); font-weight: 900; font-size: .82rem; }
    .pitch-loc{ font-size: .83rem; }
    .price-stack{ text-align: right; line-height: 1.05; }
    .price-stack .price{ font-weight: 900; font-size: 1.05rem; }
    .price-stack .unit{ color: rgba(17,24,39,.55); font-size: .74rem; font-weight: 700; }
    .btn-availability{
        height: 34px;
        padding: .35rem 1rem;
        font-weight: 900;
        border-radius: 999px;
        min-width: 150px;
    }

    .feature-tile{
        border-radius: 16px;
        border: 1px solid rgba(25,135,84,.18);
    background: rgba(25,135,84,.05);
        padding: 14px 14px;
        height: 100%;
    }
    .feature-ico{
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: rgba(25,135,84,.12);
        color: var(--pya-green);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 1.1rem;
    }

    @media (max-width: 991.98px){
        .hero-content{ padding: 32px 20px 20px; }
        .hero-wrap{ min-height: 340px; }
        .search-card{ margin-top: -18px; }
        .search-field{ height: 52px; }
    }

    @media (min-width: 992px){
        .search-field[data-sep="true"]:after{ display: block; }
    }
</style>

<section class="hero-wrap">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="hero-content">
        <div class="row align-items-end g-3">
            <div class="col-12 col-lg-8">
                <h1 class="hero-title text-white display-5 mb-3">
                    Reserva tu cancha deportiva<br class="d-none d-md-block"> de forma rápida y segura
                </h1>
                <p class="hero-sub mb-4">
                    Consulta disponibilidad en tiempo real y encuentra el espacio ideal para tu partido.
                </p>

                <div class="d-flex flex-wrap gap-2 hero-actions">
                    <a href="#canchas" class="btn btn-success btn-pill">
                        <i class="bi bi-search me-2"></i>Ver canchas disponibles
                    </a>
                    <a href="#como-funciona" class="btn btn-outline-hero btn-pill">
                        <i class="bi bi-play-circle me-2"></i>Cómo funciona
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="card search-card">
    <div class="card-body p-3 p-lg-4">
        <form class="row g-2 g-lg-3 search-grid" id="searchForm" autocomplete="off" method="GET" action="#">
            <div class="col-12 col-lg-3">
                <div class="search-field" data-sep="true">
                    <i class="bi bi-geo-alt search-ico" aria-hidden="true"></i>
                    <select class="form-select search-control" name="distrito" aria-label="Ubicación">
                        <option value="" selected>Selecciona distrito</option>
                        <option>San Juan de Lurigancho</option>
                        <option>San Miguel</option>
                        <option>Ate Vitarte</option>
                        <option>Surco</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="search-field" data-sep="true">
                    <i class="bi bi-dribbble search-ico" aria-hidden="true"></i>
                    <select class="form-select search-control" name="tipo" aria-label="Tipo de cancha">
                        <option value="" selected>Todas las canchas</option>
                        <option value="futbol-7">Fútbol 7</option>
                        <option value="futbol-11">Fútbol 11</option>
                        <option value="voley">Vóley</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <div class="search-field" data-sep="true">
                    <i class="bi bi-calendar3 search-ico" aria-hidden="true"></i>
                    <input class="form-control search-control" type="date" name="fecha" aria-label="Fecha" />
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <div class="search-field">
                    <i class="bi bi-clock search-ico" aria-hidden="true"></i>
                    <input class="form-control search-control" type="time" name="hora" aria-label="Hora" />
                </div>
            </div>
            <div class="col-12 col-lg-2 d-grid">
                <button class="btn btn-success btn-pill" style="height:54px" type="submit" aria-label="Buscar">
                    <i class="bi bi-search me-2" aria-hidden="true"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<section class="mt-4 mt-lg-5" id="canchas">
    <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
        <div>
            <h3 class="section-title mb-1">Canchas destacadas</h3>
            <div class="muted">Algunas opciones populares para reservar hoy.</div>
        </div>
        <a href="#" class="link-success fw-bold text-decoration-none">Ver todas las canchas <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="pitch-card bg-white">
                <div class="pitch-thumb" style="background-image:url('/images/1.jpg');">
                    <span class="badge-availability badge-ok">Disponible</span>
                    <button class="fav-btn" type="button" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                </div>
                <div class="p-3">
                    <div class="pitch-meta">
                        <div class="pitch-title">Cancha Los Campeones</div>
                        <div class="pitch-type">Fútbol 7</div>
                        <div class="pitch-loc muted"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>San Juan de Lurigancho</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div class="price-stack">
                            <div class="price">S/ 80</div>
                            <div class="unit">por hora</div>
                        </div>
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <a class="btn btn-success btn-availability" href="#">Ver disponibilidad</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="pitch-card bg-white">
                <div class="pitch-thumb" style="background-image:url('/images/1.jpg');">
                    <span class="badge-availability badge-low">Pocos horarios</span>
                    <button class="fav-btn" type="button" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                </div>
                <div class="p-3">
                    <div class="pitch-meta">
                        <div class="pitch-title">Sport Center San Miguel</div>
                        <div class="pitch-type">Fútbol 7</div>
                        <div class="pitch-loc muted"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>San Miguel</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div class="price-stack">
                            <div class="price">S/ 75</div>
                            <div class="unit">por hora</div>
                        </div>
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <a class="btn btn-success btn-availability" href="#">Ver disponibilidad</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="pitch-card bg-white">
                <div class="pitch-thumb" style="background-image:url('/images/1.jpg');">
                    <span class="badge-availability badge-ok">Disponible</span>
                    <button class="fav-btn" type="button" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                </div>
                <div class="p-3">
                    <div class="pitch-meta">
                        <div class="pitch-title">La Canchita Fútbol 11</div>
                        <div class="pitch-type">Fútbol 11</div>
                        <div class="pitch-loc muted"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Ate Vitarte</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div class="price-stack">
                            <div class="price">S/ 120</div>
                            <div class="unit">por hora</div>
                        </div>
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <a class="btn btn-success btn-availability" href="#">Ver disponibilidad</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="pitch-card bg-white">
                <div class="pitch-thumb" style="background-image:url('/images/1.jpg');">
                    <span class="badge-availability badge-ok">Disponible</span>
                    <button class="fav-btn" type="button" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
                </div>
                <div class="p-3">
                    <div class="pitch-meta">
                        <div class="pitch-title">Vóley Stars</div>
                        <div class="pitch-type">Vóley</div>
                        <div class="pitch-loc muted"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Surco</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div class="price-stack">
                            <div class="price">S/ 60</div>
                            <div class="unit">por hora</div>
                        </div>
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <a class="btn btn-success btn-availability" href="#">Ver disponibilidad</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-4 mt-lg-5" id="como-funciona">
    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="fw-bold">Disponibilidad en tiempo real</div>
                        <div class="small muted">Consulta horarios disponibles al instante y evita cruces.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <div class="fw-bold">Reservas rápidas y seguras</div>
                        <div class="small muted">Reserva tu cancha en pocos pasos y con total confianza.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-credit-card"></i></div>
                    <div>
                        <div class="fw-bold">Pagos fáciles</div>
                        <div class="small muted">Paga online de manera segura y sin complicaciones.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="feature-tile">
                <div class="d-flex gap-3">
                    <div class="feature-ico"><i class="bi bi-phone"></i></div>
                    <div>
                        <div class="fw-bold">Desde cualquier dispositivo</div>
                        <div class="small muted">Accede desde tu celular, tablet o computadora.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection