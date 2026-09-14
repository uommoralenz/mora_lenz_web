/* Mora Lenz — public site behaviour.
   Plain ES5-compatible JS, no framework, no build step. */
(function () {
  "use strict";

  /* ------------------------------------------------------------- navbar */

  function initNavbar() {
    var navbar = document.querySelector("[data-navbar]");
    if (!navbar) return;

    var onScroll = function () {
      navbar.classList.toggle("is-scrolled", window.scrollY > 10);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    // Mobile menu
    var toggle = navbar.querySelector("[data-navbar-toggle]");
    var panel = navbar.querySelector("[data-navbar-mobile]");

    if (toggle && panel) {
      toggle.addEventListener("click", function () {
        var open = panel.getAttribute("data-open") === "true";
        panel.setAttribute("data-open", open ? "false" : "true");
        toggle.setAttribute("aria-expanded", open ? "false" : "true");
      });

      // Close after tapping a link so in-page anchors are visible.
      panel.addEventListener("click", function (e) {
        if (e.target.closest("a")) {
          panel.setAttribute("data-open", "false");
          toggle.setAttribute("aria-expanded", "false");
        }
      });
    }

    // Services dropdown — click to open, so it works on touch devices too.
    var dropdowns = navbar.querySelectorAll("[data-dropdown]");

    Array.prototype.forEach.call(dropdowns, function (dropdown) {
      var trigger = dropdown.querySelector("[data-dropdown-trigger]");
      if (!trigger) return;

      var setOpen = function (open) {
        dropdown.setAttribute("data-open", open ? "true" : "false");
        trigger.setAttribute("aria-expanded", open ? "true" : "false");
      };

      trigger.addEventListener("click", function (e) {
        e.stopPropagation();
        setOpen(dropdown.getAttribute("data-open") !== "true");
      });

      dropdown.addEventListener("mouseenter", function () {
        if (window.matchMedia("(min-width: 1024px)").matches) setOpen(true);
      });

      dropdown.addEventListener("mouseleave", function () {
        if (window.matchMedia("(min-width: 1024px)").matches) setOpen(false);
      });

      document.addEventListener("click", function (e) {
        if (!dropdown.contains(e.target)) setOpen(false);
      });

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") setOpen(false);
      });
    });
  }

  /* ---------------------------------------------------------- countdown */

  function pad(n) {
    return n < 10 ? "0" + n : String(n);
  }

  function initCountdowns() {
    var nodes = document.querySelectorAll("[data-countdown]");
    if (!nodes.length) return;

    var timers = [];

    Array.prototype.forEach.call(nodes, function (node) {
      var target = new Date(node.getAttribute("data-countdown")).getTime();
      if (isNaN(target)) return;

      var slots = {
        days: node.querySelector('[data-unit="days"]'),
        hours: node.querySelector('[data-unit="hours"]'),
        minutes: node.querySelector('[data-unit="minutes"]'),
        seconds: node.querySelector('[data-unit="seconds"]'),
      };

      var tick = function () {
        var diff = target - Date.now();

        if (diff <= 0) {
          node.innerHTML =
            '<div class="countdown__expired">Event Started!</div>';
          return true; // done
        }

        var days = Math.floor(diff / 86400000);
        var hours = Math.floor((diff / 3600000) % 24);
        var minutes = Math.floor((diff / 60000) % 60);
        var seconds = Math.floor((diff / 1000) % 60);

        if (slots.days) slots.days.textContent = pad(days);
        if (slots.hours) slots.hours.textContent = pad(hours);
        if (slots.minutes) slots.minutes.textContent = pad(minutes);
        if (slots.seconds) slots.seconds.textContent = pad(seconds);

        // Colour shifts as the event gets close, matching the original.
        node.classList.toggle("is-urgent", days === 0 && hours < 24);
        node.classList.toggle("is-critical", days === 0 && hours < 1);

        return false;
      };

      if (tick()) return;

      timers.push(
        setInterval(function () {
          if (tick()) {
            timers.forEach(clearInterval);
          }
        }, 1000)
      );
    });
  }

  /* ----------------------------------------------------------- carousel */

  function initCarousels() {
    var carousels = document.querySelectorAll("[data-carousel]");

    Array.prototype.forEach.call(carousels, function (carousel) {
      var slides = carousel.querySelectorAll(".carousel__slide");
      if (slides.length <= 1) return;

      var index = 0;

      setInterval(function () {
        slides[index].classList.remove("is-active");
        index = (index + 1) % slides.length;
        slides[index].classList.add("is-active");
      }, 5000);
    });
  }

  /* --------------------------------------------------------- reveal-in */

  function initReveal() {
    var targets = document.querySelectorAll("[data-reveal]");
    if (!targets.length) return;

    // No IntersectionObserver (very old browser): just show everything.
    if (!("IntersectionObserver" in window)) {
      Array.prototype.forEach.call(targets, function (el) {
        el.style.opacity = "1";
        el.style.transform = "none";
      });
      return;
    }

    Array.prototype.forEach.call(targets, function (el, i) {
      el.style.opacity = "0";
      el.style.transform = "translateY(40px)";
      el.style.transition =
        "opacity .8s ease " +
        (i % 3) * 0.12 +
        "s, transform .8s ease " +
        (i % 3) * 0.12 +
        "s";
    });

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.style.opacity = "1";
          entry.target.style.transform = "none";
          observer.unobserve(entry.target);
        });
      },
      { rootMargin: "-60px" }
    );

    Array.prototype.forEach.call(targets, function (el) {
      observer.observe(el);
    });
  }

  /* ------------------------------------------------------ contact form */

  function initContactForm() {
    var form = document.querySelector("[data-contact-form]");
    if (!form) return;

    form.addEventListener("submit", function () {
      var button = form.querySelector('button[type="submit"]');
      if (!button) return;
      button.disabled = true;
      button.textContent = "Sending...";
    });

    // If the server reported a result, scroll it into view.
    var alertBox = document.querySelector("[data-contact-alert]");
    if (alertBox) {
      alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  function ready(fn) {
    if (document.readyState !== "loading") {
      fn();
    } else {
      document.addEventListener("DOMContentLoaded", fn);
    }
  }

  ready(function () {
    initNavbar();
    initCountdowns();
    initCarousels();
    initReveal();
    initContactForm();
  });
})();
