document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("imageModal");
    console.log("HI");
    console.log(modal);
    const modalImage = document.getElementById("modalImage");

    // Додаємо обробку кліків для кожної картинки
    document.querySelectorAll(".clickable-image").forEach(img => {
        img.addEventListener("click", function () {
            modal.style.display = "flex"; // Показуємо модальне вікно
            modalImage.src = this.src; // Підставляємо джерело картинки
        });
    });

    // Закриваємо модальне вікно при кліку на нього
    modal.addEventListener("click", function () {
        modal.style.display = "none"; // Ховаємо модальне вікно
    });
});