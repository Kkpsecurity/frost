{{-- Training Ranges Panel --}}
<div class="frost-secondary-bg py-5" id="ranges">
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="text-white mb-2">
                    <i class="fas fa-map-marked-alt me-2"></i>Training Ranges
                </h2>
                <p class="text-white-50 mb-0">{{ $ranges->total() }} authorized shooting ranges for your security
                    training</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Ranges List Column --}}
            <div class="col-lg-6">
                <div class="list-group list-group-flush">
                    @forelse ($ranges as $range)
                        <div class="list-group-item bg-dark border-secondary mb-3" data-range-id="{{ $range->id }}"
                            data-lat="{{ $range->latitude }}" data-lng="{{ $range->longitude }}"
                            data-city="{{ $range->city }}" style="cursor: pointer; transition: all 0.3s ease;">

                            <div class="d-flex align-items-start gap-3">
                                {{-- Icon --}}
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bullseye fa-2x text-danger"></i>
                                </div>

                                {{-- Content --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="text-white mb-0">{{ $range->name }}</h5>
                                        <button class="btn btn-sm btn-outline-light focus-range-btn"
                                            data-range-id="{{ $range->id }}" data-lat="{{ $range->latitude }}"
                                            data-lng="{{ $range->longitude }}" title="Show on map"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-map-pin"></i>
                                        </button>
                                    </div>

                                    <div class="mb-2">
                                        <span class="badge bg-primary me-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $range->city }}
                                        </span>
                                        @if ($range->appt_only)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-calendar-check me-1"></i>By Appointment
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-white-50 mb-2 small">
                                        <i class="fas fa-location-dot me-2"></i>{{ $range->address }}
                                    </p>

                                    @if ($range->inst_name)
                                        <p class="text-white-50 mb-2 small">
                                            <i class="fas fa-chalkboard-user me-2 text-info"></i>
                                            <strong>Instructor:</strong> {{ $range->inst_name }}
                                        </p>
                                    @endif

                                    <div class="d-flex flex-wrap gap-3 mb-2">
                                        <small class="text-success">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            <strong>${{ number_format($range->price, 2) }}</strong>
                                        </small>
                                        @if ($range->times)
                                            <small class="text-info">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $range->times }}
                                            </small>
                                        @endif
                                    </div>

                                    @if ($range->inst_phone || $range->inst_email)
                                        <div class="d-flex flex-wrap gap-3 mb-2">
                                            @if ($range->inst_phone)
                                                <small>
                                                    <a href="tel:{{ $range->inst_phone }}"
                                                        class="text-white-50 text-decoration-none">
                                                        <i
                                                            class="fas fa-phone me-1 text-success"></i>{{ $range->inst_phone }}
                                                    </a>
                                                </small>
                                            @endif
                                            @if ($range->inst_email)
                                                <small>
                                                    <a href="mailto:{{ $range->inst_email }}"
                                                        class="text-white-50 text-decoration-none">
                                                        <i
                                                            class="fas fa-envelope me-1 text-warning"></i>{{ $range->inst_email }}
                                                    </a>
                                                </small>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($range->range_html)
                                        <button class="btn btn-sm btn-outline-info mt-2" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#rangeDetails{{ $range->id }}"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-info-circle me-1"></i>More Details
                                        </button>

                                        <div class="collapse mt-2" id="rangeDetails{{ $range->id }}">
                                            <div class="alert alert-secondary mb-0">
                                                <div class="text-white-50 small" style="white-space: pre-line;">
                                                    {!! nl2br(e($range->range_html)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No ranges found. Please contact support.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($ranges->hasPages())
                    <div class="mt-4">
                        {{ $ranges->links() }}
                    </div>
                @endif
            </div>

            {{-- Map Column --}}
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary" style="position: sticky; top: 20px;">
                    <div class="card-header border-secondary d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-map me-2"></i>Range Locations
                        </h5>
                        <button class="btn btn-sm btn-outline-light" onclick="fitMapToMarkers()">
                            <i class="fas fa-compress-arrows-alt"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 70vh; min-height: 500px; width: 100%;"></div>
                    </div>
                    <div class="card-footer bg-dark border-secondary text-white-50 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Click markers to view details • Click list items to focus map
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* Ranges Panel Background */
    #ranges {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)),
            url('{{ asset('resources/assets/images/photo-calendar-page.webp') }}');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .list-group-item[data-range-id] {
        transition: all 0.3s ease;
        border-radius: 0.5rem !important;
    }

    .list-group-item[data-range-id]:hover {
        background: rgba(13, 110, 253, 0.15) !important;
        border-color: #0d6efd !important;
        transform: translateX(3px);
    }

    .list-group-item[data-range-id].active {
        border-color: #0d6efd !important;
        border-width: 2px !important;
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
        background: rgba(13, 110, 253, 0.2) !important;
    }

    .focus-range-btn {
        transition: transform 0.2s ease;
    }

    .focus-range-btn:hover {
        transform: scale(1.15);
    }

    a.text-white-50:hover {
        color: #fff !important;
    }
</style>

<script>
    let map;
    let markers = {};
    let infoWindows = {};
    const ranges = @json($ranges);

    function initMap() {
        const defaultCenter = {
            lat: 27.9944024,
            lng: -81.7602544
        };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: defaultCenter,
            styles: [{
                    elementType: "geometry",
                    stylers: [{
                        color: "#1a1a1a"
                    }]
                },
                {
                    elementType: "labels.text.fill",
                    stylers: [{
                        color: "#8a8a8a"
                    }]
                },
                {
                    elementType: "labels.text.stroke",
                    stylers: [{
                        color: "#1a1a1a"
                    }]
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{
                        color: "#2c2c2c"
                    }]
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{
                        color: "#000000"
                    }]
                }
            ]
        });

        // Create markers for each range
        ranges.forEach(range => {
            if (range.latitude && range.longitude) {
                const position = {
                    lat: parseFloat(range.latitude),
                    lng: parseFloat(range.longitude)
                };

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: range.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: "#dc3545",
                        fillOpacity: 1,
                        strokeColor: "#ffffff",
                        strokeWeight: 2
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                    <div style="padding: 10px; min-width: 200px;">
                        <h6 class="mb-2">${range.name}</h6>
                        <p class="mb-1 small text-muted">${range.city}</p>
                        ${range.price ? `<p class="mb-0 small"><strong>Price:</strong> $${parseFloat(range.price).toFixed(2)}</p>` : ''}
                        ${range.inst_name ? `<p class="mb-0 small"><strong>Instructor:</strong> ${range.inst_name}</p>` : ''}
                    </div>
                `
                });

                marker.addListener('click', function() {
                    closeAllInfoWindows();
                    infoWindow.open(map, marker);
                    highlightRangeItem(range.id);
                    scrollToRangeItem(range.id);
                });

                markers[range.id] = marker;
                infoWindows[range.id] = infoWindow;
            }
        });

        fitMapToMarkers();
    }

    function fitMapToMarkers() {
        const markerList = Object.values(markers);
        if (markerList.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markerList.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
        }
    }

    function closeAllInfoWindows() {
        Object.values(infoWindows).forEach(iw => iw.close());
    }

    function highlightRangeItem(rangeId) {
        document.querySelectorAll('.list-group-item[data-range-id]').forEach(item => {
            item.classList.remove('active');
        });
        const activeItem = document.querySelector(`.list-group-item[data-range-id="${rangeId}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }
    }

    function scrollToRangeItem(rangeId) {
        const item = document.querySelector(`.list-group-item[data-range-id="${rangeId}"]`);
        if (item) {
            item.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Focus range button handlers
        document.querySelectorAll('.focus-range-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const rangeId = parseInt(this.dataset.rangeId);
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);

                if (lat && lng && map && markers[rangeId]) {
                    map.panTo({
                        lat,
                        lng
                    });
                    map.setZoom(13);
                    closeAllInfoWindows();
                    infoWindows[rangeId].open(map, markers[rangeId]);
                    highlightRangeItem(rangeId);
                }
            });
        });

        // Range item click handlers
        document.querySelectorAll('.list-group-item[data-range-id]').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.closest('.focus-range-btn') || e.target.closest('button') || e
                    .target.closest('a')) {
                    return;
                }

                const rangeId = parseInt(this.dataset.rangeId);
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);

                if (lat && lng && map && markers[rangeId]) {
                    map.panTo({
                        lat,
                        lng
                    });
                    map.setZoom(13);
                    closeAllInfoWindows();
                    infoWindows[rangeId].open(map, markers[rangeId]);
                    highlightRangeItem(rangeId);
                }
            });
        });

        // City filter
        document.getElementById('cityFilter')?.addEventListener('change', function() {
            const selectedCity = this.value;
            document.querySelectorAll('.list-group-item[data-range-id]').forEach(item => {
                if (!selectedCity || item.dataset.city === selectedCity) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Upcoming filter
        document.getElementById('upcomingFilter')?.addEventListener('change', function() {
            const url = new URL(window.location);
            if (this.checked) {
                url.searchParams.set('upcoming', '1');
            } else {
                url.searchParams.delete('upcoming');
            }
            window.location.href = url.toString();
        });
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap"
    async defer></script>
