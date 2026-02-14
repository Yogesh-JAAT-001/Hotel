(function () {
  const fallbackImage = "IMAGES/live/hero/hero-fallback.png";

  function setCurrentYear() {
    const el = document.getElementById("currentYear");
    if (el) {
      el.textContent = String(new Date().getFullYear());
    }
  }

  function wireImageFallbacks() {
    const images = document.querySelectorAll("img[data-fallback]");
    images.forEach((img) => {
      img.addEventListener("error", function onError() {
        if (img.dataset.failed === "1") {
          return;
        }
        img.dataset.failed = "1";
        img.src = img.getAttribute("data-fallback") || fallbackImage;
      });
    });
  }

  function smoothAnchorScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", (event) => {
        const href = anchor.getAttribute("href");
        if (!href || href.length < 2) {
          return;
        }
        const target = document.querySelector(href);
        if (!target) {
          return;
        }
        event.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    setCurrentYear();
    wireImageFallbacks();
    smoothAnchorScroll();
  });
})();
