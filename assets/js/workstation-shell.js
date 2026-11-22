document.addEventListener("DOMContentLoaded", function () {
  var body = document.body;
  var themeButton = document.querySelector(".theme-toggle");
  var layoutButton = document.querySelector(".layout-toggle");
  var menuButton = document.querySelector(".menu-toggle");
  var sidebar = document.querySelector(".sidebar");
  var clock = document.querySelector(".taskbar-clock");
  var date = document.querySelector(".taskbar-date");

  function applyTheme(theme) {
    var light = theme === "light";
    body.dataset.theme = light ? "light" : "dark";
    if (themeButton) {
      themeButton.setAttribute("aria-pressed", String(light));
      themeButton.title = light ? "Switch to dark theme" : "Switch to light theme";
      themeButton.innerHTML = light
        ? '<i class="bi bi-moon-stars"></i><span>DARK UI</span>'
        : '<i class="bi bi-sun"></i><span>LIGHT UI</span>';
    }
    var themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) { themeColor.content = light ? "#eef2ef" : "#101619"; }
  }

  function applyLayout(layout) {
    var optimized = layout !== "normal";
    body.dataset.layout = optimized ? "optimized" : "normal";
    if (layoutButton) {
      layoutButton.setAttribute("aria-pressed", String(optimized));
      layoutButton.title = optimized ? "Switch to normal sizing" : "Switch to optimized sizing";
      layoutButton.innerHTML = optimized
        ? '<i class="bi bi-aspect-ratio"></i><span>OPTIMIZED</span>'
        : '<i class="bi bi-aspect-ratio"></i><span>NORMAL</span>';
    }
      body.dispatchEvent(new CustomEvent("workstation:layoutchange", { detail: { layout: body.dataset.layout } }));
  }

  try {
    applyTheme(window.localStorage.getItem("myaghi-concept-theme") || body.dataset.theme || "dark");
    applyLayout(window.localStorage.getItem("myaghi-concept-layout") || body.dataset.layout || "optimized");
  } catch (error) {
    applyTheme(body.dataset.theme || "dark");
    applyLayout(body.dataset.layout || "optimized");
  }

  if (themeButton) {
    themeButton.addEventListener("click", function () {
      var next = body.dataset.theme === "light" ? "dark" : "light";
      applyTheme(next);
      try { window.localStorage.setItem("myaghi-concept-theme", next); } catch (error) {}
    });
  }

  if (layoutButton) {
    layoutButton.addEventListener("click", function () {
      var next = body.dataset.layout === "optimized" ? "normal" : "optimized";
      applyLayout(next);
      try { window.localStorage.setItem("myaghi-concept-layout", next); } catch (error) {}
    });
  }

  if (menuButton && sidebar) {
    function closeMenu() {
      sidebar.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
    }

    menuButton.addEventListener("click", function () {
      var open = sidebar.classList.toggle("is-open");
      menuButton.setAttribute("aria-expanded", String(open));
    });

    sidebar.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", closeMenu);
    });

    document.addEventListener("click", function (event) {
      if (sidebar.classList.contains("is-open") && !sidebar.contains(event.target) && !menuButton.contains(event.target)) {
        closeMenu();
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && sidebar.classList.contains("is-open")) {
        closeMenu();
        menuButton.focus();
      }
    });
  }

  function updateClock() {
    var now = new Date();
    if (clock) { clock.textContent = new Intl.DateTimeFormat(undefined, { hour: "2-digit", minute: "2-digit" }).format(now); }
    if (date) { date.textContent = new Intl.DateTimeFormat(undefined, { day: "2-digit", month: "short", year: "numeric" }).format(now); }
  }

  updateClock();
  window.setInterval(updateClock, 30000);
});
