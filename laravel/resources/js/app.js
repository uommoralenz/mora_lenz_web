const root = document.documentElement;

function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'light') {
        root.classList.remove('dark');
        return;
    }

    if (savedTheme === 'dark' || prefersDark) {
        root.classList.add('dark');
    }
}

function initializeNavigation() {
    const nav = document.querySelector('[data-navbar]');
    if (!nav) {
        return;
    }

    const updateNavState = () => {
        nav.classList.toggle('nav-scrolled', window.scrollY > 20);
    };

    updateNavState();
    window.addEventListener('scroll', updateNavState, { passive: true });

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const isDark = root.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    });

    const menuButton = nav.querySelector('[data-menu-button]');
    menuButton?.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('is-menu-open');
        menuButton.setAttribute('aria-expanded', String(isOpen));
    });

    nav.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
        const button = dropdown.querySelector('[data-dropdown-button]');

        dropdown.addEventListener('mouseenter', () => dropdown.classList.add('is-open'));
        dropdown.addEventListener('mouseleave', () => dropdown.classList.remove('is-open'));
        button?.addEventListener('click', () => dropdown.classList.toggle('is-open'));
    });

    nav.querySelectorAll('[data-mobile-dropdown-button]').forEach((button) => {
        button.addEventListener('click', () => {
            button.parentElement?.classList.toggle('is-open');
        });
    });

    document.querySelectorAll('[data-scroll-link]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');

            if (!href?.startsWith('#')) {
                return;
            }

            const target = document.querySelector(href);
            if (!target) {
                return;
            }

            event.preventDefault();
            const navHeight = 80;
            const top = target.getBoundingClientRect().top + window.scrollY - navHeight;

            window.scrollTo({ top, behavior: 'smooth' });
            nav.classList.remove('is-menu-open');
            menuButton?.setAttribute('aria-expanded', 'false');
            nav.querySelectorAll('.is-open').forEach((item) => item.classList.remove('is-open'));
        });
    });
}

function initializeCountdowns() {
    const countdowns = document.querySelectorAll('[data-countdown]');

    if (countdowns.length === 0) {
        return;
    }

    const update = () => {
        countdowns.forEach((countdown) => {
            const daysElement = countdown.querySelector('[data-countdown-days]');
            const labelElement = countdown.querySelector('[data-countdown-label]');
            const eventDate = new Date(countdown.dataset.eventDate);
            const difference = eventDate.getTime() - Date.now();

            if (!daysElement || !labelElement) {
                return;
            }

            if (difference <= 0) {
                daysElement.textContent = '0';
                labelElement.textContent = 'STARTED';
                return;
            }

            daysElement.textContent = String(Math.floor(difference / (1000 * 60 * 60 * 24)));
            labelElement.textContent = 'DAYS';
        });
    };

    update();
    window.setInterval(update, 1000);
}

function getSlidesPerView() {
    if (window.matchMedia('(min-width: 1024px)').matches) {
        return 3;
    }

    if (window.matchMedia('(min-width: 640px)').matches) {
        return 2;
    }

    return 1;
}

function initializeCarousel(rootElement, options = {}) {
    const track = rootElement.querySelector(options.trackSelector);
    const prev = rootElement.querySelector(options.prevSelector);
    const next = rootElement.querySelector(options.nextSelector);
    const dots = rootElement.querySelector(options.dotsSelector);
    const slides = Array.from(track?.children ?? []);
    let index = 0;
    let intervalId;

    if (!track || slides.length === 0) {
        return;
    }

    const getMaxIndex = () => Math.max(0, slides.length - getSlidesPerView());

    const renderDots = () => {
        if (!dots) {
            return;
        }

        dots.innerHTML = '';
        for (let i = 0; i <= getMaxIndex(); i += 1) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = i === index
                ? 'h-2 w-8 rounded-full bg-cyan-400 transition-all duration-300'
                : 'h-2 w-2 rounded-full bg-zinc-600 transition-all duration-300 hover:bg-zinc-500';
            button.setAttribute('aria-label', `Go to slide ${i + 1}`);
            button.addEventListener('click', () => {
                index = i;
                update();
                restart();
            });
            dots.appendChild(button);
        }
    };

    const update = () => {
        const perView = getSlidesPerView();
        index = Math.min(index, getMaxIndex());
        track.style.transform = `translateX(-${index * (100 / perView)}%)`;
        renderDots();
    };

    const advance = (direction) => {
        const maxIndex = getMaxIndex();
        index = maxIndex === 0 ? 0 : (index + direction + maxIndex + 1) % (maxIndex + 1);
        update();
    };

    const restart = () => {
        if (!options.autoplay) {
            return;
        }

        window.clearInterval(intervalId);
        intervalId = window.setInterval(() => advance(1), options.autoplay);
    };

    prev?.addEventListener('click', () => {
        advance(-1);
        restart();
    });
    next?.addEventListener('click', () => {
        advance(1);
        restart();
    });
    window.addEventListener('resize', update);

    update();
    restart();
}

function initializeCarousels() {
    document.querySelectorAll('[data-gallery-carousel]').forEach((carousel) => {
        initializeCarousel(carousel, {
            trackSelector: '[data-gallery-track]',
            dotsSelector: '[data-gallery-dots]',
            autoplay: 3000,
        });
    });

    document.querySelectorAll('[data-past-carousel]').forEach((carousel) => {
        initializeCarousel(carousel, {
            trackSelector: '[data-past-track]',
            prevSelector: '[data-past-prev]',
            nextSelector: '[data-past-next]',
            dotsSelector: '[data-past-dots]',
            autoplay: 4000,
        });
    });
}

initializeTheme();

document.addEventListener('DOMContentLoaded', () => {
    initializeNavigation();
    initializeCountdowns();
    initializeCarousels();
});

