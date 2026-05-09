@php
    $title = $territory->type === '1' ? 'Atlas' : $territory->displayName();
@endphp

<x-main-layout :title="$title" css="territory_view">
    @if ($hasMapData)
    <x-slot:js>
        <script src="{{ asset('js/d3.v4.min.js') }}"></script>
        <script defer src="{{ asset('js/alpine.min.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('territoryMap', (config) => ({
                    async init() {
                        if (!window.d3) {
                            return;
                        }

                        const [territories, territoryData, land] = await Promise.all([
                            fetch(config.territoriesUrl).then((response) => response.json()),
                            fetch(config.territoryDataUrl).then((response) => response.json()),
                            fetch(config.landUrl).then((response) => response.json()),
                        ]);

                        if (!territories.features.length) {
                            return;
                        }

                        this.draw(territories, territoryData, land);
                    },

                    draw(territories, territoryData, land) {
                        const width = 960;
                        const height = 500;
                        const projection = d3.geoOrthographic();
                        const path = d3.geoPath().projection(projection);
                        const tooltipData = {};

                        const svg = d3.select(this.$refs.map)
                            .append('svg')
                            .attr('viewBox', `0 0 ${width} ${height}`)
                            .attr('role', 'img');

                        projection.center(path.centroid(territories)).fitSize([width, height], territories);

                        svg.append('g')
                            .attr('id', 'landmass')
                            .selectAll('path')
                            .data(land.features)
                            .enter()
                            .append('path')
                            .attr('d', path)
                            .attr('class', 'map_landmass');

                        svg.append('g')
                            .attr('id', 'territories')
                            .selectAll('path')
                            .data(territories.features)
                            .enter()
                            .append('a')
                            .attr('xlink:href', (feature) => `${config.territoryBaseUrl}/${feature.properties.id}`)
                            .append('path')
                            .attr('d', path)
                            .attr('class', 'map_territory');

                        territoryData.features.forEach((feature) => {
                            if (!feature.properties.name) {
                                return;
                            }

                            const area = Number(feature.properties.area || 0);
                            const population = Number(feature.properties.population || 0);
                            const squareKm = area / 1000000;
                            const density = squareKm ? population / squareKm : 0;

                            tooltipData[feature.properties.id] = `
                                <img src="${config.imageBaseUrl}/${feature.properties.id}.png" alt="">
                                <p>${feature.properties.name}</p>
                                <small>
                                    ${squareKm.toLocaleString('de-DE', { maximumFractionDigits: 2 })} km&sup2;<br>
                                    ${population.toLocaleString('de-DE')} EW<br>
                                    ${density.toLocaleString('de-DE', { maximumFractionDigits: 2 })} EW/km&sup2;
                                </small>
                            `;
                        });

                        const tooltip = d3.select(this.$refs.tooltip);

                        svg.selectAll('.map_territory')
                            .on('mouseover', function (feature) {
                                tooltip.html(tooltipData[feature.properties.id] || '')
                                    .style('left', `${d3.event.pageX + 7}px`)
                                    .style('top', `${d3.event.pageY - 15}px`)
                                    .style('opacity', 1)
                                    .style('display', 'block');
                            })
                            .on('mouseout', function () {
                                tooltip.style('opacity', 0).style('display', 'none');
                            })
                            .on('mousemove', function () {
                                tooltip.style('left', `${d3.event.pageX + 17}px`)
                                    .style('top', `${d3.event.pageY - 15}px`);
                            });

                        const settlements = territoryData.features.filter((feature) => feature.geometry);

                        svg.append('g')
                            .attr('id', 'settlements')
                            .selectAll('path')
                            .data(settlements)
                            .enter()
                            .append('path')
                            .attr('d', path.pointRadius(2.5));

                        svg.append('g')
                            .attr('id', 'settlement_labels')
                            .selectAll('.place-label')
                            .data(settlements)
                            .enter()
                            .append('text')
                            .attr('class', (feature) => feature.properties.id === config.capitalId ? 'place-label capital' : 'place-label')
                            .attr('transform', (feature) => `translate(${projection(feature.geometry.coordinates)})`)
                            .attr('dx', '.35em')
                            .text((feature) => feature.properties.capital);

                        this.$refs.children.hidden = true;
                    },
                }));
            });
        </script>
    </x-slot:js>
    @endif

    <div
        id="territory_map"
        @if ($hasMapData)
        x-data="territoryMap(@js([
            'territoriesUrl' => route('territory.children_geojson', ['territory' => $territory->id]),
            'territoryDataUrl' => route('territory.settlements_geojson', ['territory' => $territory->id]),
            'landUrl' => route('territory.land_geojson'),
            'territoryBaseUrl' => url('/territory/view'),
            'imageBaseUrl' => asset('images/territory'),
            'capitalId' => $territory->settlement,
        ]))"
        @endif
    >
        @if ($hasMapData)
        <div x-ref="map"></div>
        <div class="tooltip" x-ref="tooltip"></div>
        @endif

        @if ($territory->children->isNotEmpty())
        <ol id="territory_children" @if ($hasMapData) x-ref="children" @endif>
            @include('territory._children', ['territories' => $territory->children])
        </ol>
        @endif
    </div>

    <svg height="0" style="width: 0; position: fixed;">
        <filter id="umrisse" color-interpolation-filters="sRGB">
            <feMorphology in="SourceGraphic" radius="5" operator="erode" result="result2" />
            <feComposite in="result2" in2="SourceGraphic" operator="xor" />
        </filter>
    </svg>

    <img src="{{ asset('images/territory/'.$territory->id.'.png') }}" alt="Wappen von {{ $territory->name }}">

    <ol id="territory_info">
        @if ($territory->ruler)
        <li id="territory_info_ruler">
            {{ $territory->rulerTitle() }}:
            <a href="{{ url('/') }}/user/character/{{ $territory->ruler->id }}">{{ $territory->ruler->name }}</a>
        </li>
        @endif
        <li>Größe: {{ number_format($territory->area / 1000000, 2, ',', '.') }} km&sup2;</li>
        <li>Einwohner: {{ number_format($territory->population, 0, ',', '.') }}</li>
        @if ($territory->populationDensity() !== null)
        <li>Bevölkerungsdichte: {{ number_format($territory->populationDensity(), 2, ',', '.') }} EW/km&sup2;</li>
        @endif
        @if ($territory->capital)
        <li>Hauptstadt: {{ $territory->capital->name }} ({{ number_format($territory->capital->population, 0, ',', '.') }} Einwohner)</li>
        @endif
    </ol>
</x-main-layout>
