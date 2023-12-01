var map, pointsLayer, polylinesLayer;
var atualizar_mapa = true, timeoutId;
var ultima_coordenada = [];
var placeholderData = {
    "type": "FeatureCollection",
    "features": []
};

var btnVerEmTempoReal = document.querySelector('.ver_em_tempo_real').parentElement;
    
// Define custom icons
const bolinha = L.icon({
    iconUrl: 'public/assets/css/images/bolinha.svg',
    iconSize: [8, 8],
    popupAnchor: [0, -8]
});

const bolinhaAzul = L.icon({
    fillColor: "#00ff78",
    color: "#000",
    iconUrl: 'public/assets/css/images/bolinha_azul.svg',
    iconSize: [16, 16],
    popupAnchor: [0, -8], 
    zIndexOffset: 1000
});

const bolinhaVerde = L.icon({
    iconUrl: 'public/assets/css/images/bolinha_verde.svg',
    iconSize: [16, 16],
    popupAnchor: [0, -8],
    zIndexOffset: 1000
});

//CASO DADOS DO GPS NÃO EXISTAM, INICIE O MAPA SEM PONTOS OU LINHAS
if (parseInt(dados_gps_quantidade) == 0) {
    map = L.map('map').setView([-22.9064, -47.0616], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 3,
        maxZoom: 19, // maxZoom máximo de 19
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);        

    ultima_coordenada['id'] = "null";

    atualizarCoordenadasPeriodicamente(true);
} else {
    ultima_coordenada['id'] = parseInt(dados_gps[dados_gps.length - 1]['id']);
    ultima_coordenada['latitude'] = parseFloat(dados_gps[dados_gps.length - 1]['latitude']);
    ultima_coordenada['longitude'] = parseFloat(dados_gps[dados_gps.length - 1]['longitude']);
    ultima_coordenada['velocidade'] = parseFloat(dados_gps[dados_gps.length - 1]['velocidade']);
    ultima_coordenada['inicio'] = parseInt(dados_gps[dados_gps.length - 1]['inicio']);
    ultima_coordenada['fim'] = parseInt(dados_gps[dados_gps.length - 1]['fim']);
    ultima_coordenada['data_bd'] = dados_gps[dados_gps.length - 1]['data_bd'];
    ultima_coordenada['data_formatada_utc'] = dados_gps[dados_gps.length - 1]['data_formatada_utc'];
    ultima_coordenada['data_formatada_local'] = dados_gps[dados_gps.length - 1]['data_formatada_local'];

    montarMapa();
}

async function atualizarCoordenadasPeriodicamente(remontar_mapa = false) {
    try {
        if (!atualizar_mapa) {
            clearTimeout(timeoutId);
            return;
        } 

        let resposta_fetch = await procurarNovasPosicoes();

        if (resposta_fetch.result.status) {
            if (remontar_mapa) {
                remontarMapa(resposta_fetch.result.coordenadas, true);
            }
            atualizarMapa(resposta_fetch.result);

            btnVerEmTempoReal.classList.remove('d-none');
            btnVerEmTempoReal.classList.add('d-block', 'd-md-flex');
        } 
    } catch (error) {
        console.error("Erro ao obter novas posições:", error);
    }

    // Agende a próxima execução após 5 segundos
    timeoutId = setTimeout(() => atualizarCoordenadasPeriodicamente(remontar_mapa), 5000);
}

function montarMapa()
{
    if (pointsLayer != undefined) {
        pointsLayer.clearLayers();
    }
    if (polylinesLayer != undefined) {
        polylinesLayer.clearLayers();
    }
    if (map) {
        // Se o mapa já existe, destrua-o
        map.remove();
    }

    btnVerEmTempoReal.classList.remove('d-none');
    btnVerEmTempoReal.classList.add('d-block', 'd-md-flex');

    atualizar_mapa = true;

    let tela_inicial_configuracoes_viagem = document.querySelector('.configuracoes .dispositivo_viagem .viagem');
    tela_inicial_configuracoes_viagem.textContent = ``;
    tela_inicial_configuracoes_viagem.classList.add('d-block');
    tela_inicial_configuracoes_viagem.classList.add('d-none');

    if (configuracao_fechar.classList.contains('d-block')) {
        configuracao_fechar.click();
    }

    map = L.map('map').setView([dados_gps[dados_gps.length - 1].latitude, dados_gps[dados_gps.length - 1].longitude], 18);

    //POSIÇÃO MÁXIMA PARA ARRASTAR
    let bounds = new L.LatLngBounds(new L.LatLng(85, -180), new L.LatLng(-85, 180));

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        center: bounds.getCenter(),
        minZoom: 3,
        maxZoom: 19, // maxZoom máximo de 19
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Create an empty GeoJSON layers for points and polylines
    pointsLayer = L.geoJSON().addTo(map);
    polylinesLayer = L.geoJSON().addTo(map);

    for (var i = 0; i < dados_gps.length; i++) {
        var item = dados_gps[i];

        // Create placeholder points
        let pointFeature = {
            "type": "Feature",
            "properties": {
                "popupContent": 
                    `<div>
                        ${item.data_formatada_utc} (UTC)
                        <br>
                        ${item.data_formatada_local} (LOCAL)
                    </div>
                    <div>
                        <b>Latitude: </b>${item.latitude}
                    </div>
                    <div>
                        <b>Longitude: </b>${item.longitude}
                    </div>
                    <div>
                        <b>Velocidade: </b>${item.velocidade} km/h
                    </div>`
                ,
                "id": parseInt(item.id),
                "posicao": i
            },
            "geometry": {
                "type": "Point",
                "coordinates": [item.longitude, item.latitude]
            }
        };
        placeholderData.features.push(pointFeature);

        pointFeature.properties['isCustomIcon'] = false;

        if (i === 0 || i === dados_gps.length - 1) {
            // Change the icon for points when i is 1 or 8
            pointFeature.properties['isCustomIcon'] = true;
        }

        // Create placeholder lines connecting points
        if (i > 0) {
            var lineFeature = {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [[dados_gps[i-1].longitude, dados_gps[i-1].latitude], [item.longitude, item.latitude]]
                }
            }
            placeholderData.features.push(lineFeature);
        }
    }

    // Display placeholder data with popups
    L.geoJSON(placeholderData, {
        pointToLayer: function (feature, latlng) {

            if (feature.properties['isCustomIcon'] && feature.properties['posicao'] === 0) {
                return L.marker(latlng, { icon: bolinhaAzul });
            } else if(feature.properties['isCustomIcon'] && feature.properties['posicao'] === dados_gps.length - 1) {
                return L.marker(latlng, { icon: bolinhaVerde });
            } else {
                return L.marker(latlng, { icon: bolinha });
            }
        },
        onEachFeature: function (feature, layer) {
            if (feature.properties && feature.properties.popupContent) {
                layer.bindPopup(feature.properties.popupContent);
            }
            if (feature.geometry.type === "Point") {
                pointsLayer.addLayer(layer);
            } else if (feature.geometry.type === "LineString") {
                polylinesLayer.addLayer(layer);
            }
        }
    });

    // Add the layers to the map
    pointsLayer.addTo(map);
    polylinesLayer.addTo(map);

    atualizar_mapa = true;
    clearTimeout(timeoutId);
    atualizarCoordenadasPeriodicamente();
}

function remontarMapa(coordenadas, habilitar_fetch = false)
{
    if (pointsLayer != undefined) {
        pointsLayer.clearLayers();
    }
    if (polylinesLayer != undefined) {
        polylinesLayer.clearLayers();
    }
    if (map) {
        // Se o mapa já existe, destrua-o
        map.remove();
    }

    map = L.map('map').setView([coordenadas[coordenadas.length - 1].latitude, coordenadas[coordenadas.length - 1].longitude], 18);

    //POSIÇÃO MÁXIMA PARA ARRASTAR
    let bounds = new L.LatLngBounds(new L.LatLng(85, -180), new L.LatLng(-85, 180));

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        center: bounds.getCenter(),
        minZoom: 3,
        maxZoom: 19, // maxZoom máximo de 19
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Create an empty GeoJSON layers for points and polylines
    pointsLayer = L.geoJSON().addTo(map);
    polylinesLayer = L.geoJSON().addTo(map);

    let placeholderData = {
        "type": "FeatureCollection",
        "features": []
    };

    for (let i = 0; i < coordenadas.length; i++) {
        var item = coordenadas[i];

        // Create placeholder points
        let pointFeature = {
            "type": "Feature",
            "properties": {
                "popupContent": 
                    `<div>
                        ${item.data_bd} (UTC)
                        <br>
                        ${item.data_formatada_local} (LOCAL)
                    </div>
                    <div>
                        <b>Latitude: </b>${item.latitude}
                    </div>
                    <div>
                        <b>Longitude: </b>${item.longitude}
                    </div>
                    <div>
                        <b>Velocidade: </b>${item.velocidade} km/h
                    </div>`
                ,
                "id": parseInt(item.id),
                "posicao": i
            },
            "geometry": {
                "type": "Point",
                "coordinates": [item.longitude, item.latitude]
            }
        };
        placeholderData.features.push(pointFeature);

        pointFeature.properties['isCustomIcon'] = false;

        if (i === 0 || i === coordenadas.length - 1) {
            // Change the icon for points when i is 1 or 8
            pointFeature.properties['isCustomIcon'] = true;
        }

        // Create placeholder lines connecting points
        if (i > 0) {
            let lineFeature = {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [[coordenadas[i-1].longitude, coordenadas[i-1].latitude], [item.longitude, item.latitude]]
                }
            }
            placeholderData.features.push(lineFeature);
        }
    }

    // Display placeholder data with popups
    L.geoJSON(placeholderData, {
        pointToLayer: function (feature, latlng) {
            if (feature.properties['isCustomIcon'] && feature.properties['posicao'] === 0) {
                return L.marker(latlng, { icon: bolinhaAzul });
            } else if(feature.properties['isCustomIcon'] && feature.properties['posicao'] === coordenadas.length - 1) {
                return L.marker(latlng, { icon: bolinhaVerde });
            } else {
                return L.marker(latlng, { icon: bolinha });
            }
        },
        onEachFeature: function (feature, layer) {
            if (feature.properties && feature.properties.popupContent) {
                layer.bindPopup(feature.properties.popupContent);
            }
            if (feature.geometry.type === "Point") {
                pointsLayer.addLayer(layer);
            } else if (feature.geometry.type === "LineString") {
                polylinesLayer.addLayer(layer);
            }
        }
    });

    // Add the layers to the map
    pointsLayer.addTo(map);
    polylinesLayer.addTo(map);

    atualizar_mapa = habilitar_fetch;

    return true;
}

function atualizarMapa(objeto) {
    if (!objeto.status) {
        return;
    }

    let coordenadas = objeto.coordenadas;
    let quantidade_pontos = 0;

    ultima_coordenada['id'] = parseInt(coordenadas[coordenadas.length - 1].id);
    ultima_coordenada['latitude'] = parseFloat(coordenadas[coordenadas.length - 1].latitude);
    ultima_coordenada['longitude'] = parseFloat(coordenadas[coordenadas.length - 1].longitude);
    ultima_coordenada['velocidade'] = parseFloat(coordenadas[coordenadas.length - 1].velocidade);
    ultima_coordenada['inicio'] = parseInt(coordenadas[coordenadas.length - 1].inicio);
    ultima_coordenada['fim'] = parseInt(coordenadas[coordenadas.length - 1].fim);
    ultima_coordenada['data_bd'] = coordenadas[coordenadas.length - 1].data_bd;
    ultima_coordenada['data_formatada_utc'] = coordenadas[coordenadas.length - 1].data_formatada_utc;
    ultima_coordenada['data_formatada_local'] = coordenadas[coordenadas.length - 1].data_formatada_local;

    // Remover a última polyline redundante
    if (pointsLayer != undefined) {
        pointsLayer.clearLayers();
    }
    if (polylinesLayer != undefined) {
        polylinesLayer.clearLayers();
    }
    polylinesLayer = L.geoJSON().addTo(map);

    //SETAR ÚLTIMO PONTO PARA PONTO PRETO ANTES DE ATUALIZAR O MAPA
    let allPoints = placeholderData.features
        .filter(feature => feature.geometry.type === "Point")

    if (allPoints.length > 2) {
        for (let i = 0; i < allPoints.length; i++) {
            if (i > 0 && i < allPoints.length) {
                allPoints[i].properties['isCustomIcon'] = false;
            }
        }
    }

    for (let i = 0; i < placeholderData.features.length; i++) {
        if (placeholderData.features[i].properties) {
            quantidade_pontos++;
        }
    }

    // Adicionar as coordenadas das bolinhas antigas
    let allCoordinates = placeholderData.features
        .filter(feature => feature.geometry.type === "Point")
        .map(feature => feature.geometry.coordinates);

    // Adicionar as coordenadas das bolinhas novas
    for (let i = 0; i < coordenadas.length; i++) {
        let item = coordenadas[i];
        allCoordinates.push([item.longitude, item.latitude]);
    }

    for (let i = 0; i < allCoordinates.length; i++) {
        if (i > 0) {
            let lineFeature = {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [[allCoordinates[i-1][0], allCoordinates[i-1][1]], [allCoordinates[i][0], allCoordinates[i][1]]]
                }
            }
            placeholderData.features.push(lineFeature);
        }
    }

    for (let i = 0; i < coordenadas.length; i++) {
        var item = coordenadas[i];

        // Create placeholder points
        let pointFeature = {
            "type": "Feature",
            "properties": {
                "popupContent": 
                    `<div>
                        ${item.data_bd} (UTC)
                        <br>
                        ${item.data_formatada_local} (LOCAL)
                    </div>
                    <div>
                        <b>Latitude: </b>${item.latitude}
                    </div>
                    <div>
                        <b>Longitude: </b>${item.longitude}
                    </div>
                    <div>
                        <b>Velocidade: </b>${item.velocidade} km/h
                    </div>`
                ,
                "id": parseInt(item.id),
                "posicao": quantidade_pontos + i
            },
            "geometry": {
                "type": "Point",
                "coordinates": [item.longitude, item.latitude]
            }
        };
        placeholderData.features.push(pointFeature);

        pointFeature.properties['isCustomIcon'] = false;

        if (quantidade_pontos + i === 0 || i === coordenadas.length - 1) {
            pointFeature.properties['isCustomIcon'] = true;
        }

        //ADICIONA OS DADOS ATUALIZADOS PARA dados_gps, QUE SERÃO MOSTRADOS QUANDO CLICAR NO BOTÃO "VER EM TEMPO REAL"
        // Verifica se dados_gps é undefined e inicializa se necessário
        if (!dados_gps) {
            dados_gps = [];
        }

        dados_gps[dados_gps.length + i] = {
            'id': parseInt(item.id),
            'latitude': parseFloat(item.latitude),
            'longitude': parseFloat(item.longitude),
            'velocidade': parseFloat(item.velocidade),
            'inicio': parseInt(item.inicio),
            'fim': parseInt(item.fim),
            'data_bd': item.data_bd,
            'data_formatada_utc': item.data_formatada_utc,
            'data_formatada_local': item.data_formatada_local
        };
    }

    // Display placeholder data with popups
    L.geoJSON(placeholderData, {
        pointToLayer: function (feature, latlng) {
            if (feature.properties['isCustomIcon'] && feature.properties['posicao'] === 0) {
                return L.marker(latlng, { icon: bolinhaAzul });
            } else if (feature.properties['isCustomIcon'] && feature.properties['posicao'] === (quantidade_pontos + coordenadas.length) - 1) {
                return L.marker(latlng, { icon: bolinhaVerde });
            } else {
                return L.marker(latlng, { icon: bolinha });
            }
        },
        onEachFeature: function (feature, layer) {
            if (feature.properties && feature.properties.popupContent) {
                layer.bindPopup(feature.properties.popupContent);
            }
            if (feature.geometry.type === "Point") {
                pointsLayer.addLayer(layer);
            } 
            else if (feature.geometry.type === "LineString") {
                polylinesLayer.addLayer(layer);
            }
        }
    });

    // Add the layers to the map
    pointsLayer.addTo(map);
    polylinesLayer.addTo(map);
}



async function procurarNovasPosicoes() {
    const endpoint = BASE + "action/json/atualizar-viagem-atual-mapa";

    try {
        // Retorna a promessa criada pela função fetch junto com o objeto resultante
        const response = await fetch(endpoint, {
            method: "POST",
            mode: "same-origin",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                "csrf_token": csrf_token,
                "ultimo_id_viagem_atual": ultima_coordenada['id']
            })            
        });

        const jsonResponse = await response.json();

        // Verifica o status da resposta
        if (jsonResponse.status === false) {
            return {
                promise: response,
                result: {
                    status: false
                }
            };
        }

        return {
            promise: response,
            result: {
                status: true,
                coordenadas: jsonResponse.coordenadas,
                novas_coordenadas: [jsonResponse.coordenadas[0].latitude, jsonResponse.coordenadas[0].longitude]
            }
        };
    } catch (error) {
        console.error("Erro ao processar a solicitação:", error);
        return {
            promise: Promise.reject(error), // Retorna uma promessa rejeitada em caso de erro
            result: {
                status: false,
                error: "Erro ao processar a solicitação"
            }
        };
    }
}