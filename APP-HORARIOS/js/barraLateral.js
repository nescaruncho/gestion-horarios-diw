document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.querySelector('.toggle-btn');
    const sidebar = document.querySelector('.sidebar');
    const tablaContainer = document.querySelector('.tabla-container');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        tablaContainer.classList.toggle('shifted');
    });
    

    document.addEventListener('click', function(e) {
        if(!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('active');
            tablaContainer.classList.remove('shifted');
        }
    });
});
document.getElementById('toggle-btn').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const containerTable = document.getElementById('contenedor-tabla');
    sidebar.classList.toggle('collapsed');
    containerTable.classList.toggle('collapsed'); 
});


function showTooltip(event) {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('collapsed')) {
        const icon = event.currentTarget.querySelector('.icon');
        const text = event.currentTarget.querySelector('.text').textContent;


        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;


        const iconRect = icon.getBoundingClientRect();
        tooltip.style.position = 'absolute';
        tooltip.style.left = `${iconRect.right + 10}px`;
        tooltip.style.top = `${iconRect.top-5}px`;


        document.body.appendChild(tooltip);
    }
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

const menuItems = document.querySelectorAll('.menu-item');
menuItems.forEach(item => {
    item.addEventListener('mouseenter', showTooltip);
    item.addEventListener('mouseleave', hideTooltip);
});