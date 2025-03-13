document.addEventListener("DOMContentLoaded", function () {
    const contenedorTabla = document.getElementById("contenedor-tabla");
    const ultimaTabla = localStorage.getItem("ultimaTabla") || "ciclos"; 
    mostrarTabla(ultimaTabla);

    const enlaces = document.querySelectorAll(".opcion");
    const sidebar = document.getElementById('sidebar');
    const containerTable = document.getElementById('contenedor-tabla');

    sidebar.classList.add('collapsed');
    containerTable.classList.add('collapsed'); 
    sidebar.classList.remove('active');
    containerTable.classList.remove('shifted');
    
   
    enlaces.forEach((enlace) => {
        enlace.addEventListener("click", function (e) {
            e.preventDefault(); 

            sidebar.classList.add('collapsed');
            containerTable.classList.add('collapsed'); 
            sidebar.classList.remove('active');
            containerTable.classList.remove('shifted');
            
            const tablaId = this.getAttribute("data-tabla");

            ocultarTablaActual(() => {
                mostrarTabla(tablaId);
                localStorage.setItem("ultimaTabla", tablaId);
            });
        });
    });

    
    function mostrarTabla(tablaId) {
        
        const template = document.getElementById(`template-${tablaId}`);
        if (template) {
            const tabla = document.importNode(template.content, true);
            contenedorTabla.appendChild(tabla);

            setTimeout(() => {
                const tablaElement = document.getElementById(tablaId);
                if (tablaElement) {
                    tablaElement.classList.add("tabla-visible");
                }
            }, 10); 
        }
    }

    
    function ocultarTablaActual(callback) {
        const tablaActual = contenedorTabla.querySelector(".divTabla");
        if (tablaActual) {
            
            tablaActual.classList.remove("tabla-visible");
            setTimeout(() => {                
                contenedorTabla.removeChild(tablaActual);
                if (callback) callback();
            }, 500); 
        } else if (callback) {
            callback();
        }
    }
});