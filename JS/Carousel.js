document.addEventListener('DOMContentLoaded', function() {
    const prevButton = document.querySelector('.carousel-prev');
    const nextButton = document.querySelector('.carousel-next');
    const pedidosContainer = document.querySelector('.pedidos-abiertos');

    const cardWidth = document.querySelector('.pedido-card').offsetWidth + 10; // 10px es el espacio entre las tarjetas

    prevButton.addEventListener('click', function() {
        pedidosContainer.scrollBy({
            left: -cardWidth * 3, // Desplaza 3 tarjetas hacia la izquierda
            behavior: 'smooth'
        });
    });

    nextButton.addEventListener('click', function() {
        pedidosContainer.scrollBy({
            left: cardWidth * 3, // Desplaza 3 tarjetas hacia la derecha
            behavior: 'smooth'
        });
    });
});
