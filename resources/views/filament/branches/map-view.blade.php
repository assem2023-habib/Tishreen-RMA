<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echo-js@latest/dist/echo.min.js"></script>

<div x-data="mapComponent()" x-init="initMap()">
    <div x-ref="mapContainer" id="branches-map" style="height: 500px; width: 100%;"></div>
</div>
<script>
    function mapComponent() {
        return {
            branches: @json($branchesForMap),
            centerLatitude: {{ $centerLatitude ?? 33.5138 }},
            centerLongitude: {{ $centerLongitude ?? 36.2765 }},
            zoomLevel: {{ $zoomLevel ?? 6 }},
            selectField: '',
            initMap() {
                // حدد نوع الحقل (من أو إلى) بناءً على data-branch-select
                this.selectField = this.$root.getAttribute('data-branch-select') || '';
                const map = L.map(this.$refs.mapContainer).setView(
                    [this.centerLatitude, this.centerLongitude],
                    this.zoomLevel
                );
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                this.branches.forEach(branch => {
                    const lat = parseFloat(branch.lat);
                    const lng = parseFloat(branch.lng);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const marker = L.marker([lat, lng])
                            .addTo(map)
                            .bindPopup(`<b>${branch.name ?? 'name'}</b><br>${branch.email ?? ''}`);
                        marker.on('click', () => {
                            // إرسال id الفرع إلى الفورم
                            if (window.livewire) {
                                window.livewire.emit('setBranchRouteField', this.selectField, branch
                                    .id);
                            } else {
                                // fallback: dispatch event
                                window.dispatchEvent(new CustomEvent('branch-route-select', {
                                    detail: {
                                        field: this.selectField,
                                        value: branch.id
                                    }
                                }));
                            }
                        });
                    }
                });
                setTimeout(() => map.invalidateSize(), 150);
            }
        }
    }

    // function calculateDistance(lat1, lon1, lat2, lon2) {
    //     const R = 6371; // نصف قطر الأرض بالكيلومتر
    //     const dLat = (lat2 - lat1) * Math.PI / 180;
    //     const dLon = (lon2 - lon1) * Math.PI / 180;
    //     const a =
    //         Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    //         Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    //         Math.sin(dLon / 2) * Math.sin(dLon / 2);
    //     const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    //     return (R * c).toFixed(2); // المسافة بالكيلومتر
    // }

    // // بعد اختيار الفرعين
    // const distance = calculateDistance(branch1Lat, branch1Lng, branch2Lat, branch2Lng);

    // // إرسال القيمة لحقل distance_per_kilo في Filament
    // window.dispatchEvent(new CustomEvent('filament-form-set', {
    //     detail: {
    //         field: 'distance_per_kilo',
    //         value: distance
    //     }
    // }));
</script>

<style>
    #branches-map,
    .leaflet-container {
        min-height: 500px;
        min-width: 100%;
        height: 500px !important;
        width: 100% !important;
    }
</style>
