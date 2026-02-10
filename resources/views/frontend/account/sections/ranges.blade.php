{{-- Training Ranges Section --}}
<div class="ranges-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-map-marked-alt me-2"></i>Training Ranges
    </h3>

    @if (!empty($data['ranges']) && $data['ranges']->count() > 0)
        <div class="row">
            {{-- Map Column --}}
            <div class="col-lg-5 mb-4">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-header bg-secondary border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-map me-2"></i>Range Locations
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="rangesMap" style="height: 500px; width: 100%;">
                            {{-- Map will be rendered here --}}
                        </div>
                    </div>
                    <div class="card-footer bg-dark border-secondary text-white-50 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Click markers to view range details
                    </div>
                </div>
            </div>

            {{-- Ranges List Column --}}
            <div class="col-lg-7">
                <div class="list-group list-group-flush">
                    @foreach ($data['ranges'] as $range)
                        <div class="list-group-item bg-dark border-secondary mb-3 rounded range-item"
                            data-range-id="{{ $range->id }}" data-lat="{{ $range->latitude }}"
                            data-lng="{{ $range->longitude }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-2 text-white">
                                        <i class="fas fa-bullseye text-danger me-2"></i>
                                        {{ $range->name }}
                                        @if (!$range->is_active)
                                            <span class="badge bg-secondary ms-2">Inactive</span>
                                        @endif
                                    </h5>

                                    <div class="mb-2">
                                        <span class="text-white-50">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            {{ $range->city }}
                                        </span>
                                    </div>

                                    <div class="mb-2 text-white-50 small" style="white-space: pre-line;">
                                        {{ $range->address }}</div>

                                    @if ($range->inst_name)
                                        <div class="mb-2">
                                            <strong class="text-info">Instructor:</strong>
                                            <span class="text-white">{{ $range->inst_name }}</span>
                                        </div>
                                    @endif

                                    @if ($range->inst_phone)
                                        <div class="mb-2">
                                            <i class="fas fa-phone text-success me-2"></i>
                                            <a href="tel:{{ $range->inst_phone }}"
                                                class="text-white-50">{{ $range->inst_phone }}</a>
                                        </div>
                                    @endif

                                    @if ($range->inst_email)
                                        <div class="mb-2">
                                            <i class="fas fa-envelope text-warning me-2"></i>
                                            <a href="mailto:{{ $range->inst_email }}"
                                                class="text-white-50">{{ $range->inst_email }}</a>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-3 flex-wrap mt-3">
                                        <div>
                                            <strong class="text-success">Price:</strong>
                                            <span class="text-white">${{ number_format($range->price, 2) }}</span>
                                        </div>
                                        <div>
                                            <strong class="text-info">Times:</strong>
                                            <span class="text-white-50">{{ $range->times }}</span>
                                        </div>
                                        @if ($range->appt_only)
                                            <div>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-calendar-check me-1"></i>Appointment Only
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($range->range_html)
                                        <button class="btn btn-sm btn-outline-primary mt-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#rangeDetails{{ $range->id }}"
                                            aria-expanded="false">
                                            <i class="fas fa-info-circle me-1"></i>View Details
                                        </button>

                                        <div class="collapse mt-3" id="rangeDetails{{ $range->id }}">
                                            <div class="card bg-secondary border-0">
                                                <div class="card-body text-white-50 small"
                                                    style="white-space: pre-line;">
                                                    {!! nl2br(e($range->range_html)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button class="btn btn-outline-light btn-sm ms-3 focus-range-btn"
                                    data-range-id="{{ $range->id }}" data-lat="{{ $range->latitude }}"
                                    data-lng="{{ $range->longitude }}" title="Show on map">
                                    <i class="fas fa-map-marker-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Map Script --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize map (using Leaflet.js - free and open source)
                // You'll need to add Leaflet CSS/JS to your layout if not already included
                if (typeof L !== 'undefined') {
                    const map = L.map('rangesMap').setView([27.9944024, -81.7602544], 7); // Florida center

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    const markers = [];
                    const ranges = @json($data['ranges']);

                    // Add markers for each range
                    ranges.forEach(range => {
                        if (range.latitude && range.longitude) {
                            const marker = L.marker([range.latitude, range.longitude])
                                .addTo(map)
                                .bindPopup(`
                                    <strong>${range.name}</strong><br>
                                    ${range.city}<br>
                                    <strong>Price:</strong> $${parseFloat(range.price).toFixed(2)}<br>
                                    ${range.inst_name ? '<strong>Instructor:</strong> ' + range.inst_name : ''}
                                `);

                            markers[range.id] = marker;
                        }
                    });

                    // Focus on range when button clicked
                    document.querySelectorAll('.focus-range-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const lat = parseFloat(this.dataset.lat);
                            const lng = parseFloat(this.dataset.lng);
                            const rangeId = this.dataset.rangeId;

                            if (lat && lng) {
                                map.setView([lat, lng], 13);
                                if (markers[rangeId]) {
                                    markers[rangeId].openPopup();
                                }
                            }
                        });
                    });
                } else {
                    // Fallback if Leaflet not available
                    document.getElementById('rangesMap').innerHTML = `
                        <div class="d-flex align-items-center justify-content-center h-100 text-white-50">
                            <div class="text-center">
                                <i class="fas fa-map fa-3x mb-3"></i>
                                <p>Map requires Leaflet.js library</p>
                            </div>
                        </div>
                    `;
                }
            });
        </script>

        <style>
            .range-item {
                transition: all 0.3s ease;
            }

            .range-item:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                transform: translateX(5px);
            }

            .focus-range-btn {
                transition: all 0.2s ease;
            }

            .focus-range-btn:hover {
                transform: scale(1.1);
            }
        </style>
    @else
        <div class="alert alert-secondary">
            <i class="fas fa-info-circle me-2"></i>
            No training ranges available at this time.
        </div>
    @endif
</div>
