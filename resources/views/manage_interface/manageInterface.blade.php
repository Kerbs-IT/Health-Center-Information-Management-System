<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite([
    'resources/css/app.css',
    'resources/css/nurse_dashboard.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/manageInterface.css',
    'resources/js/manageInterface/manageInterface.js'
    ])

    <div class="ms-0 ps-0 d-flex w-100">
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>

            <div class="flex-grow-1">
                @include('layout.header')

                <main class="mi-main">
                    {{-- Page heading --}}
                    <div class="mi-page-header">
                        <div class="mi-page-header__left">
                            <span class="mi-page-header__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M11 12a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                    <path
                                        d="M12 7a5 5 0 1 0 5 5h-5v-5" />
                                    <path d="M12 3a9 9 0 1 0 9 9" />
                                </svg>
                            </span>
                            <div>
                                <h1 class="mi-page-header__title">Manage Interface</h1>
                                <p class="mi-page-header__sub">Customize your health center portal's appearance</p>
                            </div>
                        </div>
                        <div class="mi-page-header__actions">
                            <button class="mi-btn mi-btn--ghost" type="button" id="discard-btn">Discard</button>
                            <button class="mi-btn mi-btn--primary" type="button" id="save-all-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Save changes
                            </button>
                        </div>
                    </div>

                    <div class="mi-grid">

                        {{-- ── 1. COLOR PALETTE ── --}}
                        <div class="mi-card">
                            <div class="mi-card__header">
                                <span class="mi-card__icon mi-card__icon--green">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" />
                                        <path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="mi-card__title">Color palette</h2>
                                    <p class="mi-card__sub">Brand colors used across your portal</p>
                                </div>
                            </div>

                            <form action="" method="post" id="color-pallete-form">
                                @method('PUT')
                                @csrf

                                <div class="mi-color-fields">
                                    <div class="mi-color-field">
                                        <label class="mi-label">Primary <span class="mi-label__hint">(background)</span></label>
                                        <div class="mi-color-row">
                                            <input type="color" class="mi-color-swatch" id="primary_color" name="primaryColor">
                                            <input type="text" class="mi-color-hex" id="primary_hex" value="#FFFFFF" maxlength="7" placeholder="#FFFFFF">
                                        </div>
                                    </div>

                                    <div class="mi-color-field">
                                        <label class="mi-label">Secondary <span class="mi-label__hint">(menubar)</span></label>
                                        <div class="mi-color-row">
                                            <input type="color" class="mi-color-swatch" id="secondary_color" name="secondaryColor">
                                            <input type="text" class="mi-color-hex" id="secondary_hex" value="" maxlength="7" placeholder="#000000">
                                        </div>
                                    </div>

                                    <div class="mi-color-field">
                                        <label class="mi-label">Tertiary <span class="mi-label__hint">(hover)</span></label>
                                        <div class="mi-color-row">
                                            <input type="color" class="mi-color-swatch" id="tertiary_color" name="tertiaryColor">
                                            <input type="text" class="mi-color-hex" id="tertiary_hex" value="#2E8B57" maxlength="7" placeholder="#2E8B57">
                                        </div>
                                    </div>
                                </div>

                                {{-- live preview strip --}}
                                <div class="mi-palette-preview" id="palette-preview">
                                    <div class="mi-palette-preview__bar" id="preview-bar"></div>
                                    <div class="mi-palette-preview__nav" id="preview-nav">
                                        <span class="mi-palette-preview__dot mi-palette-preview__dot--active"></span>
                                        <span class="mi-palette-preview__dot"></span>
                                        <span class="mi-palette-preview__dot"></span>
                                    </div>
                                    <div class="mi-palette-preview__foot">
                                        <span class="mi-palette-preview__hover" id="preview-hover"></span>
                                        <span class="mi-palette-preview__label">live preview</span>
                                    </div>
                                </div>

                            </form>
                        </div>

                        {{-- ── 2. LOGO ── --}}
                        <div class="mi-card">
                            <div class="mi-card__header">
                                <span class="mi-card__icon mi-card__icon--blue">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 8h.01" />
                                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" />
                                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="mi-card__title">Logo</h2>
                                    <p class="mi-card__sub">Your clinic or organization logo</p>
                                </div>
                            </div>

                            <div class="mi-logo-layout">
                                <div class="mi-logo-preview" id="logo-preview-box">
                                    @if(file_exists(public_path('images/hugoperez_logo.png')))
                                    <img src="{{ asset('images/hugoperez_logo.png') }}?v={{ filemtime(public_path('images/hugoperez_logo.png')) }}"
                                        alt="Current logo" class="mi-logo-preview__img" id="logo-preview-img">
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" style="color:#c4c8d0">
                                        <path d="M3 21l18 0" />
                                        <path d="M5 21v-14l8 -4l8 4v14" />
                                        <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                                    </svg>
                                    <span class="mi-logo-preview__label" id="logo-preview-label">No logo</span>
                                    @endif
                                </div>

                                <div class="mi-logo-upload-side">
                                    <label class="mi-dropzone" id="logo-dropzone" for="logo-file-input">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 9l5 -5l5 5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                        <span>Click or drag to upload new logo</span>
                                        <small>PNG, SVG, JPG · Max 2MB</small>
                                        <input type="file" id="logo-file-input" accept="image/*" style="display:none">
                                    </label>
                                    <div class="mi-row-btns">
                                        <button type="button" class="mi-btn-sm mi-btn-sm--danger" id="logo-remove-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            Remove logo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end .mi-grid --}}

                    {{-- ── 3. CAROUSEL ── --}}
                    <div class="mi-card mi-card--full">
                        <div class="mi-card__header">
                            <span class="mi-card__icon mi-card__icon--amber">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 5m0 1a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z" />
                                    <path d="M7 12l5 -5l5 5" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="mi-card__title">Homepage carousel</h2>
                                <p class="mi-card__sub">Slideshow images displayed on the homepage</p>
                            </div>
                            <div class="mi-card__header-actions">
                                <label class="mi-btn-sm" for="carousel-file-input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 9l5 -5l5 5" />
                                        <path d="M12 4l0 12" />
                                    </svg>
                                    Upload slide
                                    <input type="file" id="carousel-file-input" accept="image/*" multiple style="display:none">
                                </label>
                            </div>
                        </div>

                        <div class="mi-carousel-track" id="carousel-track">
                            {{-- Existing slides from DB --}}
                            @if(isset($carouselImages) && count($carouselImages))
                            @foreach($carouselImages as $index => $img)
                            <div class="mi-slide" data-id="{{ $img->id }}">
                                <img src="{{ asset($img->path) }}" alt="Slide {{ $index + 1 }}" class="mi-slide__img">
                                @if($index === 0)
                                <span class="mi-slide__badge">Main</span>
                                @endif
                                <div class="mi-slide__overlay">
                                    <button type="button" class="mi-slide__del" data-id="{{ $img->id }}" title="Remove slide">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6l-12 12" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <span class="mi-slide__label">Slide {{ $index + 1 }}</span>
                            </div>
                            @endforeach
                            @endif

                            {{-- Add new slot --}}
                            <label class="mi-slide mi-slide--add" for="carousel-file-input">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                <span>Add slide</span>
                            </label>
                        </div>

                        <p class="mi-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="vertical-align:-2px">
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 9h.01" />
                                <path d="M11 12h1l1 4h1" />
                            </svg>
                            Drag thumbnails to reorder · Recommended size 1200 × 500 px
                        </p>
                    </div>

                    {{-- ── 4. HEALTH WORKERS ── --}}
                    <div class="mi-card mi-card--full">
                        <div class="mi-card__header">
                            <span class="mi-card__icon mi-card__icon--coral">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M16 11h6m-3 -3v6" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="mi-card__title">Health workers — homepage photos</h2>
                                <p class="mi-card__sub">Separate from each worker's system profile picture</p>
                            </div>
                        </div>

                        <div class="mi-workers-list">
                            @forelse(isset($healthWorkers) ? $healthWorkers : [] as $worker)
                            <div class="mi-worker-row" data-worker-id="{{ $worker->id }}">
                                <div class="mi-worker-avatar"
                                    style="background:{{ $worker->avatar_bg ?? '#e6f4ec' }};color:{{ $worker->avatar_color ?? '#1a6b3c' }}">
                                    {{ strtoupper(substr($worker->first_name, 0, 1) . substr($worker->last_name, 0, 1)) }}
                                </div>
                                <div class="mi-worker-info">
                                    <span class="mi-worker-name">{{ $worker->first_name }} {{ $worker->last_name }}</span>
                                    <span class="mi-worker-role">{{ $worker->role ?? 'Health Worker' }}</span>
                                    <div class="mi-worker-photo-status">
                                        @if($worker->homepage_photo)
                                        <span class="mi-photo-badge mi-photo-badge--set">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                            Homepage photo set
                                        </span>
                                        @else
                                        <span class="mi-photo-badge mi-photo-badge--none">No homepage photo</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mi-worker-actions">
                                    @if($worker->homepage_photo)
                                    <label class="mi-btn-sm" for="worker-photo-{{ $worker->id }}" title="Replace">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                        </svg>
                                        Replace
                                    </label>
                                    <button type="button" class="mi-btn-sm mi-btn-sm--danger worker-photo-remove"
                                        data-worker-id="{{ $worker->id }}" title="Remove homepage photo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                    @else
                                    <label class="mi-btn-sm mi-btn-sm--upload" for="worker-photo-{{ $worker->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 9l5 -5l5 5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                        Upload photo
                                    </label>
                                    @endif
                                    <input type="file" id="worker-photo-{{ $worker->id }}"
                                        class="worker-photo-input" accept="image/*"
                                        data-worker-id="{{ $worker->id }}" style="display:none">
                                </div>
                            </div>
                            @empty
                            <div class="mi-empty">No health workers found.</div>
                            @endforelse
                        </div>

                        <div class="mi-notice">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;color:#2E8B57">
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 9h.01" />
                                <path d="M11 12h1l1 4h1" />
                            </svg>
                            <span>Homepage photos are only shown on the public-facing homepage. They are separate from each worker's system profile picture, which is managed under their own account.</span>
                        </div>
                    </div>

                    {{-- ── 5. ABOUT US IMAGE ── --}}
                    <div class="mi-card mi-card--full">
                        <div class="mi-card__header">
                            <span class="mi-card__icon mi-card__icon--teal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 21l18 0" />
                                    <path d="M5 21v-14l8 -4l8 4v14" />
                                    <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="mi-card__title">About us — homepage image</h2>
                                <p class="mi-card__sub">Banner shown in the About Us section of your homepage</p>
                            </div>
                        </div>

                        <div class="mi-about-layout">
                            <div class="mi-about-preview" id="about-preview-box">
                                @if(isset($aboutImage) && $aboutImage)
                                <img src="{{ asset('storage/' . $aboutImage) }}" alt="About Us banner"
                                    class="mi-about-preview__img" id="about-preview-img">
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" style="color:#9ca3af">
                                    <path d="M15 8h.01" />
                                    <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" />
                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                </svg>
                                <span id="about-preview-label" style="font-size:11px;color:#9ca3af">No image set</span>
                                @endif
                            </div>

                            <div class="mi-about-upload-side">
                                <label class="mi-dropzone" for="about-file-input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 9l5 -5l5 5" />
                                        <path d="M12 4l0 12" />
                                    </svg>
                                    <span>Click or drag to upload About Us image</span>
                                    <small>JPG, PNG, WebP · Max 5MB · Recommended 1200 × 600 px</small>
                                    <input type="file" id="about-file-input" accept="image/*" style="display:none">
                                </label>

                                <div class="mi-about-filename">
                                    <span class="mi-fname" id="about-fname">
                                        {{ isset($aboutImage) && $aboutImage ? basename($aboutImage) : 'No file selected' }}
                                    </span>
                                    <button type="button" class="mi-btn-sm mi-btn-sm--danger" id="about-remove-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </main>
            </div>
        </div>
    </div>

    @if(isset($isActive) && $isActive)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('manage-interface');
            if (con) con.classList.add('active');
        });
    </script>
    @endif

</body>

</html>