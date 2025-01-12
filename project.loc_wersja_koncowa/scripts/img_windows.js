document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("imageModal");
    console.log("HI");
    console.log(modal);
    const modalImage = document.getElementById("modalImage");

    document.querySelectorAll(".clickable-image").forEach(img => {
        img.addEventListener("click", function () {
            modal.style.display = "flex";
            modalImage.src = this.src;
        });
    });

    modal.addEventListener("click", function () {
        modal.style.display = "none";
    });
});