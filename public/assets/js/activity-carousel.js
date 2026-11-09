const motion = matchMedia('(prefers-reduced-motion: reduce)');

for (const carousel of document.querySelectorAll('.activity-carousel')) {
    const track = carousel.querySelector('.activity-track');
    const toolbar = carousel.querySelector('.activity-carousel-toolbar');
    if (!toolbar) continue;

    const previous = toolbar.querySelector('[data-step="-1"]');
    const next = toolbar.querySelector('[data-step="1"]');
    const autoplay = toolbar.querySelector('.activity-autoplay');
    const cards = track.querySelectorAll('.activity-card');
    let timer;
    let paused = motion.matches;
    let visible = false;
    let interacting = false;

    const end = () => track.scrollWidth - track.clientWidth;
    const scroll = (direction, wrap = false) => {
        const step = cards[1].offsetLeft - cards[0].offsetLeft;
        const left = wrap && track.scrollLeft >= end() - 1 ? 0 : track.scrollLeft + step * direction;
        track.scrollTo({ left, behavior: motion.matches ? 'instant' : 'smooth' });
    };
    const schedule = () => {
        clearTimeout(timer);
        if (paused || !visible || document.hidden || end() <= 1 ||
            interacting || track.matches(':focus-within')) return;

        timer = setTimeout(() => {
            scroll(1, true);
            schedule();
        }, 5000);
    };
    const update = () => {
        toolbar.hidden = end() <= 1;
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft >= end() - 1;
        autoplay.textContent = paused ? '▶' : 'Ⅱ';
        autoplay.setAttribute('aria-label', paused ? 'Reanudar desplazamiento automático' : 'Pausar desplazamiento automático');
    };

    toolbar.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (button === autoplay) {
            paused = !paused;
            update();
        } else if (button?.dataset.step) {
            scroll(Number(button.dataset.step));
        }
        schedule();
    });

    track.addEventListener('scroll', update, { passive: true });
    track.addEventListener('pointerenter', (event) => {
        if (event.pointerType !== 'touch') interacting = true;
        schedule();
    });
    track.addEventListener('pointerleave', () => {
        interacting = false;
        schedule();
    });
    track.addEventListener('touchstart', () => {
        interacting = true;
        schedule();
    }, { passive: true });
    for (const event of ['touchend', 'touchcancel']) {
        track.addEventListener(event, () => {
            interacting = false;
            schedule();
        }, { passive: true });
    }
    carousel.addEventListener('focusin', schedule);
    carousel.addEventListener('focusout', () => setTimeout(schedule, 0));
    document.addEventListener('visibilitychange', schedule);
    motion.addEventListener('change', () => {
        paused = motion.matches;
        update();
        schedule();
    });
    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        schedule();
    }).observe(track);
    new ResizeObserver(() => {
        update();
        schedule();
    }).observe(track);
    update();
}
