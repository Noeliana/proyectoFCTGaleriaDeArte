
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    const total = slides.length;

    function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
    });
}

    document.querySelector('.next')?.addEventListener('click', () => {
    current = (current + 1) % total;
    showSlide(current);
});

    document.querySelector('.prev')?.addEventListener('click', () => {
    current = (current - 1 + total) % total;
    showSlide(current);
});

    setInterval(() => {
    current = (current + 1) % total;
    showSlide(current);
}, 5000);
