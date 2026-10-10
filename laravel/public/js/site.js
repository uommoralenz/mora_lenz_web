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
      var menu = dropdown.querySelector(".navbar__dropdown-menu");
      var closeTimer;
      if (!trigger) return;

      var setOpen = function (open) {
        dropdown.setAttribute("data-open", open ? "true" : "false");
        trigger.setAttribute("aria-expanded", open ? "true" : "false");
      };

      var cancelClose = function () {
        window.clearTimeout(closeTimer);
      };

      var scheduleClose = function () {
        if (!window.matchMedia("(min-width: 1024px)").matches) return;
        cancelClose();
        closeTimer = window.setTimeout(function () {
          setOpen(false);
        }, 3000);
      };

      trigger.addEventListener("click", function (e) {
        e.stopPropagation();
        cancelClose();
        setOpen(dropdown.getAttribute("data-open") !== "true");
      });

      trigger.addEventListener("mouseenter", function () {
        cancelClose();
        if (window.matchMedia("(min-width: 1024px)").matches) setOpen(true);
      });

      trigger.addEventListener("mouseleave", scheduleClose);

      if (menu) {
        menu.addEventListener("mouseenter", cancelClose);
        menu.addEventListener("mouseleave", scheduleClose);
      }

      document.addEventListener("click", function (e) {
        if (!dropdown.contains(e.target)) {
          cancelClose();
          setOpen(false);
        }
      });

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          cancelClose();
          setOpen(false);
        }
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

  /* Gallery album previews rotate their photos; clicking opens the Facebook album. */
  function initGallerySlideshows() {
    var slideshows = document.querySelectorAll("[data-gallery-slideshow]");
    Array.prototype.forEach.call(slideshows, function (slideshow) {
      var slides = slideshow.querySelectorAll(".gallery-slideshow__slide");
      if (slides.length < 2) return;
      var index = 0;
      setInterval(function () {
        slides[index].classList.remove("is-active");
        index = (index + 1) % slides.length;
        slides[index].classList.add("is-active");
      }, 4000);
    });
  }

  /* ----------------------------------------------------------- lightbox */

  /* Photo viewer for the gallery page: arrows, keyboard, swipe, thumbnails. */
  function initLightbox() {
    var box = document.querySelector("[data-lightbox]");
    var openers = document.querySelectorAll("[data-lightbox-open]");
    if (!box || !openers.length) return;

    var img = box.querySelector("[data-lightbox-img]");
    var caption = box.querySelector("[data-lightbox-caption]");
    var title = box.querySelector("[data-lightbox-title]");
    var count = box.querySelector("[data-lightbox-count]");
    var strip = box.querySelector("[data-lightbox-strip]");
    var prev = box.querySelector("[data-lightbox-prev]");
    var next = box.querySelector("[data-lightbox-next]");

    var photos = [];
    var index = 0;
    var opener = null;

    function show(i) {
      if (!photos.length) return;
      index = (i + photos.length) % photos.length;

      var photo = photos[index];
      img.classList.add("is-loading");
      img.onload = function () {
        img.classList.remove("is-loading");
      };
      img.src = photo.src;
      img.alt = photo.caption || title.textContent;
      caption.textContent = photo.caption || "";
      count.textContent = index + 1 + " / " + photos.length;

      var single = photos.length < 2;
      prev.hidden = single;
      next.hidden = single;
      strip.hidden = single;

      Array.prototype.forEach.call(strip.children, function (thumb, n) {
        thumb.classList.toggle("is-active", n === index);
        if (n === index) {
          thumb.scrollIntoView({ block: "nearest", inline: "center" });
        }
      });

      // Warm the neighbours so arrows feel instant.
      [index + 1, index - 1].forEach(function (n) {
        var p = photos[(n + photos.length) % photos.length];
        if (p) new Image().src = p.src;
      });
    }

    function open(button) {
      try {
        photos = JSON.parse(button.getAttribute("data-photos") || "[]");
      } catch (e) {
        photos = [];
      }
      if (!photos.length) return;

      opener = button;
      title.textContent = button.getAttribute("data-title") || "";

      strip.innerHTML = "";
      photos.forEach(function (photo, n) {
        var thumb = document.createElement("button");
        thumb.type = "button";
        thumb.className = "lightbox__thumb";
        thumb.setAttribute("aria-label", "Photo " + (n + 1));
        var t = document.createElement("img");
        t.src = photo.src;
        t.alt = "";
        t.loading = "lazy";
        thumb.appendChild(t);
        thumb.addEventListener("click", function () {
          show(n);
        });
        strip.appendChild(thumb);
      });

      box.hidden = false;
      document.body.classList.add("has-lightbox");
      show(0);
      box.querySelector("[data-lightbox-close]").focus();
    }

    function close() {
      box.hidden = true;
      document.body.classList.remove("has-lightbox");
      img.removeAttribute("src");
      if (opener) opener.focus();
    }

    Array.prototype.forEach.call(openers, function (button) {
      button.addEventListener("click", function () {
        open(button);
      });
    });

    box.querySelector("[data-lightbox-close]").addEventListener("click", close);
    prev.addEventListener("click", function () {
      show(index - 1);
    });
    next.addEventListener("click", function () {
      show(index + 1);
    });

    // Clicking the dark area around the photo closes the viewer.
    box.addEventListener("click", function (e) {
      if (e.target === box || e.target.classList.contains("lightbox__stage") ||
          e.target.classList.contains("lightbox__figure")) {
        close();
      }
    });

    document.addEventListener("keydown", function (e) {
      if (box.hidden) return;
      if (e.key === "Escape") close();
      else if (e.key === "ArrowLeft") show(index - 1);
      else if (e.key === "ArrowRight") show(index + 1);
      else if (e.key === "Tab") {
        // Keep keyboard focus inside the viewer while it is open.
        var items = box.querySelectorAll("button:not([hidden])");
        if (!items.length) return;
        var first = items[0];
        var last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    });

    // Swipe left / right on touch screens.
    var startX = null;
    box.addEventListener("touchstart", function (e) {
      startX = e.touches[0].clientX;
    }, { passive: true });
    box.addEventListener("touchend", function (e) {
      if (startX === null) return;
      var dx = e.changedTouches[0].clientX - startX;
      startX = null;
      if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
    }, { passive: true });
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
      // Only lock the button once the browser has accepted every field. Doing
      // it unconditionally left the form stuck showing a disabled "Sending..."
      // whenever native validation refused the submit.
      if (form.checkValidity && !form.checkValidity()) return;

      var button = form.querySelector('button[type="submit"]');
      if (!button) return;
      button.disabled = true;
      button.textContent = "Sending...";
    });

    // Drop a server-side error outline as soon as that field is corrected.
    Array.prototype.forEach.call(form.querySelectorAll(".is-invalid"), function (field) {
      field.addEventListener("input", function () {
        field.classList.remove("is-invalid");
        field.removeAttribute("aria-invalid");
      });
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
    initGallerySlideshows();
    initLightbox();
    initReveal();
    initContactForm();
  });
})();
