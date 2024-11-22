document.addEventListener("DOMContentLoaded", function () {
    const toggleTitle = document.getElementById("toggle-title");
    const dropdownContent = document.getElementById("dropdown-content");

    // Abrir y cerrar al hacer clic en el título
    toggleTitle.addEventListener("click", function () {
        if (dropdownContent.style.display === "none" || dropdownContent.style.display === "") {
            dropdownContent.style.display = "block";
        } else {
            dropdownContent.style.display = "none";
        }
    });

    // Cerrar la lista cuando el cursor salga del área del menú desplegable
    dropdownContent.addEventListener("mouseleave", function () {
        dropdownContent.style.display = "none";
    });
});
