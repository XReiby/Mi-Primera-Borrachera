document.addEventListener('DOMContentLoaded', function () {
    const dropdownButton = document.querySelector('h3'); // Selector del elemento que activa el dropdown
    const dropdownContent = document.getElementById('dropdown-content');

    dropdownButton.addEventListener('click', function () {
        const isVisible = dropdownContent.style.display === 'block';
        dropdownContent.style.display = isVisible ? 'none' : 'block'; // Alternar visibilidad
    });

    // Cerrar el dropdown si se hace clic fuera de él
    window.addEventListener('click', function (event) {
        if (!dropdownButton.contains(event.target) && !dropdownContent.contains(event.target)) {
            dropdownContent.style.display = 'none';
        }
    });
});
