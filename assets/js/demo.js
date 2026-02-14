(function () {
  const fallback = "../IMAGES/live/hero/hero-fallback.png";

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("img[data-fallback]").forEach((img) => {
      img.addEventListener("error", () => {
        if (img.dataset.failed === "1") {
          return;
        }
        img.dataset.failed = "1";
        img.src = img.getAttribute("data-fallback") || fallback;
      });
    });
  });
})();
