// Referencias al HTML
const inputPokemon     = document.getElementById('input-pokemon');
const btnBuscar        = document.getElementById('btn-buscar');

const msgCargando      = document.getElementById('msg-cargando');
const msgError         = document.getElementById('msg-error');

const panelTipo        = document.getElementById('panel-tipo');
const titulotipo       = document.getElementById('titulo-tipo');
const listaPorGen      = document.getElementById('lista-por-generacion');

const tarjeta          = document.getElementById('tarjeta');
const btnFavorito      = document.getElementById('btn-favorito');
const nombrePokemon    = document.getElementById('nombre-pokemon');
const numeroPokemon    = document.getElementById('numero-pokemon');
const spritePokemon    = document.getElementById('sprite-pokemon');
const tiposPokemon     = document.getElementById('tipos-pokemon');
const alturaPokemon    = document.getElementById('altura-pokemon');
const pesoPokemon      = document.getElementById('peso-pokemon');
const habilidadPokemon = document.getElementById('habilidad-pokemon');
const statsBody        = document.getElementById('stats-body');


// Configuración de tipos
const TIPOS = [
    { api: 'fire',     label: 'Fuego',     color: '#EE8130' },
    { api: 'water',    label: 'Agua',      color: '#6390F0' },
    { api: 'grass',    label: 'Planta',    color: '#7AC74C' },
    { api: 'electric', label: 'Eléctrico', color: '#F7D02C' },
    { api: 'ground',   label: 'Tierra',    color: '#E2BF65' },
    { api: 'rock',     label: 'Roca',      color: '#B6A136' },
    { api: 'ice',      label: 'Hielo',     color: '#96D9D6' },
    { api: 'psychic',  label: 'Psíquico',  color: '#F95587' },
    { api: 'ghost',    label: 'Fantasma',  color: '#735797' },
    { api: 'dragon',   label: 'Dragón',    color: '#6F35FC' },
    { api: 'dark',     label: 'Siniestro', color: '#705746' },
    { api: 'steel',    label: 'Acero',     color: '#B7B7CE' },
    { api: 'fairy',    label: 'Hada',      color: '#D685AD' },
    { api: 'poison',   label: 'Veneno',    color: '#A33EA1' },
    { api: 'fighting', label: 'Lucha',     color: '#C22E28' },
    { api: 'bug',      label: 'Bicho',     color: '#A6B91A' },
    { api: 'normal',   label: 'Normal',    color: '#A8A878' },
    { api: 'flying',   label: 'Volador',   color: '#A98FF3' },
];

// Etiquetas legibles para cada generación
const GENERACIONES = {
    'generation-i':    'Generación I — Kanto',
    'generation-ii':   'Generación II — Johto',
    'generation-iii':  'Generación III — Hoenn',
    'generation-iv':   'Generación IV — Sinnoh',
    'generation-v':    'Generación V — Teselia',
    'generation-vi':   'Generación VI — Kalos',
    'generation-vii':  'Generación VII — Alola',
    'generation-viii': 'Generación VIII — Galar',
    'generation-ix':   'Generación IX — Paldea',
};

// Orden correcto para mostrar generaciones
const ORDEN_GEN = [
    'generation-i', 'generation-ii', 'generation-iii',
    'generation-iv', 'generation-v', 'generation-vi',
    'generation-vii', 'generation-viii', 'generation-ix',
];


// Pintar botones de tipo al cargar
function pintarBotonesTipo() {
    const contenedor = document.getElementById('contenedor-tipos');
    contenedor.innerHTML = '';

    TIPOS.forEach(function(tipo) {
        const btn = document.createElement('button');
        btn.className        = 'chip-tipo';
        btn.textContent      = tipo.label;
        btn.style.background = tipo.color;
        btn.dataset.tipo     = tipo.api;

        btn.addEventListener('click', function() {
            // Marcar el activo visualmente
            document.querySelectorAll('.chip-tipo').forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');

            cargarPokemonPorTipo(tipo.api, tipo.label);
        });

        contenedor.appendChild(btn);
    });
}


// Cargar pokémon de un tipo y agrupar por gen
async function cargarPokemonPorTipo(tipoApi, tipoLabel) {

    // Ocultar tarjeta individual y mostrar cargando
    tarjeta.style.display    = 'none';
    panelTipo.style.display  = 'none';
    msgError.style.display   = 'none';
    msgCargando.style.display = 'block';

    try {
        // Paso 1: pedir la lista de Pokémon de ese tipo
        // La respuesta tiene: { pokemon: [ { pokemon: {name, url} }, ... ] }
        const resTipo = await fetch(`https://pokeapi.co/api/v2/type/${tipoApi}`);
        if (!resTipo.ok) throw new Error('No se pudo cargar el tipo.');
        const datosTipo = await resTipo.json();

        // Tomamos todos los pokémon del tipo (filtramos mega/formas especiales
        // que no tienen id numérico simple)
        const lista = datosTipo.pokemon
            .map(p => p.pokemon)
            .filter(p => {
                // La URL tiene la forma .../pokemon/25/ → extraemos el id
                const id = extraerIdDeUrl(p.url);
                // Solo Pokémon con id <= 10000 (excluye formas alternativas con id > 10000)
                return id !== null && id <= 10000;
            });

        // Paso 2: para cada Pokémon pedimos su especie para obtener la generación
        // Usamos Promise.all para hacerlo en paralelo y que sea rápido
        const promesas = lista.map(async function(p) {
            const id = extraerIdDeUrl(p.url);
            try {
                const resEsp = await fetch(`https://pokeapi.co/api/v2/pokemon-species/${id}/`);
                if (!resEsp.ok) return null;
                const esp = await resEsp.json();
                return {
                    id:         id,
                    nombre:     p.name,
                    generacion: esp.generation.name,   // ej: "generation-i"
                };
            } catch {
                return null; // Si falla uno, lo ignoramos
            }
        });

        const resultados = (await Promise.all(promesas)).filter(r => r !== null);

        // Paso 3: agrupar por generación
        const porGen = {};
        resultados.forEach(function(poke) {
            if (!porGen[poke.generacion]) {
                porGen[poke.generacion] = [];
            }
            porGen[poke.generacion].push(poke);
        });

        // Paso 4: mostrar
        mostrarPorGeneracion(porGen, tipoLabel, tipoApi);

    } catch (error) {
        msgCargando.style.display = 'none';
        msgError.style.display    = 'block';
        msgError.textContent      = error.message;
    }
}


// Mostrar los grupos en el HTML
function mostrarPorGeneracion(porGen, tipoLabel, tipoApi) {

    msgCargando.style.display = 'none';
    listaPorGen.innerHTML     = '';

    // Encontrar el color del tipo actual
    const tipoInfo = TIPOS.find(t => t.api === tipoApi);
    const colorTipo = tipoInfo ? tipoInfo.color : '#888';

    // Título del panel
    titulotipo.textContent = `Tipo: ${tipoLabel}`;
    titulotipo.style.color = colorTipo;

    // Recorremos en orden de generación
    ORDEN_GEN.forEach(function(genKey) {
        if (!porGen[genKey]) return; // Si no hay Pokémon de esa gen para este tipo, saltar

        const pokemones = porGen[genKey];

        // Sección de generación
        const seccion = document.createElement('div');
        seccion.className = 'seccion-gen';

        const titulo = document.createElement('h3');
        titulo.className   = 'titulo-gen';
        titulo.textContent = GENERACIONES[genKey] || genKey;
        titulo.style.borderColor = colorTipo;
        seccion.appendChild(titulo);

        const grid = document.createElement('div');
        grid.className = 'grid-pokemon';

        pokemones.forEach(function(poke) {
            const chip = document.createElement('button');
            chip.className    = 'chip-pokemon';
            chip.dataset.id   = poke.id;
            chip.dataset.nombre = poke.nombre;

            // Imagen pequeña (sprite oficial de frente)
            const img = document.createElement('img');
            img.src   = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${poke.id}.png`;
            img.alt   = poke.nombre;
            img.width = 60;

            const etiqueta = document.createElement('span');
            etiqueta.textContent = '#' + String(poke.id).padStart(3,'0') + ' ' + poke.nombre;

            chip.appendChild(img);
            chip.appendChild(etiqueta);

            chip.addEventListener('click', function() {
                buscarPokemonPorId(poke.id);
            });

            grid.appendChild(chip);
        });

        seccion.appendChild(grid);
        listaPorGen.appendChild(seccion);
    });

    panelTipo.style.display = 'block';
    // Hacer scroll suave al panel
    panelTipo.scrollIntoView({ behavior: 'smooth' });
}


// Buscar y mostrar tarjeta de detalle
async function buscarPokemon() {
    const nombre = inputPokemon.value.trim().toLowerCase();
    if (nombre === '') return;

    mostrarCargando();

    try {
        const respuesta = await fetch(`https://pokeapi.co/api/v2/pokemon/${nombre}`);
        if (!respuesta.ok) throw new Error('Pokémon no encontrado. Verifica el nombre o número.');
        const datos = await respuesta.json();
        mostrarTarjeta(datos);
    } catch (error) {
        msgCargando.style.display = 'none';
        msgError.style.display    = 'block';
        msgError.textContent      = error.message;
    }
}

async function buscarPokemonPorId(id) {
    mostrarCargando();
    try {
        const respuesta = await fetch(`https://pokeapi.co/api/v2/pokemon/${id}`);
        if (!respuesta.ok) throw new Error('No se pudo cargar el Pokémon.');
        const datos = await respuesta.json();
        mostrarTarjeta(datos);
    } catch (error) {
        msgCargando.style.display = 'none';
        msgError.style.display    = 'block';
        msgError.textContent      = error.message;
    }
}


// Rellenar la tarjeta con los datos
function mostrarTarjeta(datos) {

    // Número y nombre
    numeroPokemon.textContent = '#' + String(datos.id).padStart(3, '0');
    nombrePokemon.textContent = datos.name;

    // Sprite
    spritePokemon.src = datos.sprites.front_default;
    spritePokemon.alt = 'Sprite de ' + datos.name;

    // Tipos
    tiposPokemon.textContent = datos.types.map(t => t.type.name).join(' / ');

    // Altura y peso
    alturaPokemon.textContent = (datos.height / 10) + ' m';
    pesoPokemon.textContent   = (datos.weight / 10) + ' kg';

    // Habilidad principal (la primera no oculta)
    const hab = datos.abilities.find(a => !a.is_hidden);
    habilidadPokemon.textContent = hab ? hab.ability.name : '–';

    // Estadísticas
    statsBody.innerHTML = '';
    datos.stats.forEach(function(s) {
        const fila = document.createElement('tr');
        fila.innerHTML = `<td>${s.stat.name}</td><td>${s.base_stat}</td>`;
        statsBody.appendChild(fila);
    });

    // Reiniciar corazón
    verificarFavorito(datos.id);

    // Ocultar cargando y mostrar tarjeta
    msgCargando.style.display = 'none';
    tarjeta.style.display     = 'block';

    // Scroll a la tarjeta
    tarjeta.scrollIntoView({ behavior: 'smooth' });
}


// Utilidades

// Extrae el número del id desde una URL de la API
// Ej: "https://pokeapi.co/api/v2/pokemon/25/" → 25
function extraerIdDeUrl(url) {
    const partes = url.replace(/\/$/, '').split('/');
    const id = parseInt(partes[partes.length - 1], 10);
    return isNaN(id) ? null : id;
}

function mostrarCargando() {
    msgCargando.style.display = 'block';
    msgError.style.display    = 'none';
    tarjeta.style.display     = 'none';
}


// Eventos

btnBuscar.addEventListener('click', buscarPokemon);

inputPokemon.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') buscarPokemon();
});


btnFavorito.addEventListener('click', function () {

    const id = parseInt(numeroPokemon.textContent.replace('#', ''));
    const nombre = nombrePokemon.textContent;
    const imagen = spritePokemon.src;

    fetch("api/favoritos.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${id}&nombre=${encodeURIComponent(nombre)}&imagen=${encodeURIComponent(imagen)}`
    })
    .then(res => res.text())
    .then(data => {

        if (data.trim() === "guardado") {
            btnFavorito.textContent = '♥';
            btnFavorito.classList.add('activo-corazon');
        } else {
            btnFavorito.textContent = '♡';
            btnFavorito.classList.remove('activo-corazon');
        }

    });

});

function verificarFavorito(id){

    fetch(`api/verificar_favorito.php?id=${id}`)
    .then(res => res.text())
    .then(data => {

        if(data.trim() === "si"){
            btnFavorito.textContent = '♥';
            btnFavorito.classList.add('activo-corazon');
        }else{
            btnFavorito.textContent = '♡';
            btnFavorito.classList.remove('activo-corazon');
        }

    });

}


// Inicio: pintar botones de tipo
pintarBotonesTipo();
