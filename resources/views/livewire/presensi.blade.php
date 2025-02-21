<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-md">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p><strong>Nama Pegawai : </strong>{{ auth()->user()->name }}</p>
                        <p><strong>Kantor : </strong>{{ $schedule->office->name }}</p>
                        <p><strong>Shift : </strong>{{ $schedule->shift->name }} ({{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }})</p>
                        <p class="{{ $schedule->work_from_anywhere ? 'text-green-500' : 'text-blue-500' }}"><strong>Status: </strong>{{ $schedule->work_from_anywhere ? 'WFA' : 'WFO' }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Jam Masuk</h4>
                            <p>@if($attendance) {{ $attendance->start_time }} @else --:--:-- @endif</p> 
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Jam Pulang</h4>
                            <p>@if($attendance) {{ $attendance->end_time }} @else --:--:-- @endif</p> 
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Presensi Kehadiran</h2>
                    <div wire:ignore id="map" class="mb-4 rounded-lg border border-gray-300"></div>
                    <form wire:submit="store" class="row g-3" enctype="multipart/form-data">
                        <button type="button" id="tapLocation" class="px-4 py-2 bg-blue-500 text-white rounded">Tandai
                            Lokasi</button>
                        @if ($isInsideRadius)
                            <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded">Submit
                                Presensi</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map, lat, lng, component, locationMarker;
        const office = [{{ $schedule->office->latitude }}, {{ $schedule->office->longitude }}];
        const radius = {{ $schedule->office->radius }};
        document.getElementById('tapLocation').addEventListener('click', tapLocation);

        document.addEventListener('livewire:initialized', function() {
            component = @this;
            map = L.map('map').setView([{{ $schedule->office->latitude }},
                {{ $schedule->office->longitude }}
            ], 20);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const circle = L.circle(office, {
                color: 'green',
                fillColor: '#59cc28',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
        });

        function tapLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;

                    if (locationMarker) {
                        map.removeLayer(locationMarker);
                    }

                    locationMarker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);

                    if (isWithinLocation(lat, lng, office, radius)) {
                        component.set('isInsideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                    }
                })
            } else {
                alert('Can not get the location.');
            }
        }

        function isWithinLocation(lat, lng, center, radius) {
            const is_wfa = {{ $schedule->work_from_anywhere }};
            if(is_wfa) {
                return true
            }else{
                let distance = map.distance([lat, lng], center);
                return distance <= radius;
            }
        }
    </script>
</div>
