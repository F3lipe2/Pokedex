const contenedor = document.getElementById("contenedor-favoritos");
const contador   = document.getElementById("contador-favoritos");

function cargarFavoritos() {
    fetch("../api/listar_favoritos.php")
        .then(res => {
            if (res.status === 401) { window.location.href = 'login.php'; return; }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            contenedor.innerHTML = "";

            if (data.length === 0) {
                contador.textContent = "No tienes Pokémon guardados.";
                contenedor.innerHTML = `<p class="msg-vacio">Busca un Pokémon en la Pokédex y presiona el corazón ♡ para añadirlo aquí.</p>`;
                return;
            }

            contador.textContent = `${data.length} Pokémon guardado${data.length !== 1 ? "s" : ""}`;

            data.forEach(poke => {
                const card = document.createElement("div");
                card.className = "card-favorito";
                card.innerHTML = `
                    <button class="btn-eliminar" title="Quitar de favoritos">✕</button>
                    <img src="${poke.imagen}" alt="${poke.nombre}">
                    <h3>${poke.nombre}</h3>
                    <span class="num-pokemon">#${String(poke.pokemon_id).padStart(3, "0")}</span>
                `;

                // Botón eliminar
                card.querySelector(".btn-eliminar").addEventListener("click", (e) => {
                    e.stopPropagation();
                    eliminarFavorito(poke.pokemon_id, poke.nombre, poke.imagen, card);
                });

                contenedor.appendChild(card);
            });
        })
        .catch(() => {
            contador.textContent = "Error al cargar favoritos.";
            contenedor.innerHTML = `<p class="msg-vacio" style="color:#c0392b;">No se pudo conectar con la base de datos.</p>`;
        });
}

function eliminarFavorito(id, nombre, imagen, cardElement) {
    const formData = new FormData();
    formData.append("id",     id);
    formData.append("nombre", nombre);
    formData.append("imagen", imagen);

    fetch("../api/favoritos.php", { method: "POST", body: formData })
        .then(res => {
            if (res.status === 401) { window.location.href = 'login.php'; return; }
            return res.text();
        })
        .then(respuesta => {
            if (respuesta === "eliminado") {
                cardElement.style.transition = "opacity 0.3s, transform 0.3s";
                cardElement.style.opacity    = "0";
                cardElement.style.transform  = "scale(0.8)";
                setTimeout(() => {
                    cardElement.remove();
                    const restantes = contenedor.querySelectorAll(".card-favorito").length;
                    if (restantes === 0) {
                        contador.textContent = "No tienes Pokémon guardados.";
                        contenedor.innerHTML = `<p class="msg-vacio">Busca un Pokémon en la Pokédex y presiona el corazón ♡ para añadirlo aquí.</p>`;
                    } else {
                        contador.textContent = `${restantes} Pokémon guardado${restantes !== 1 ? "s" : ""}`;
                    }
                }, 300);
            }
        });
}

// Cargar al iniciar
cargarFavoritos();