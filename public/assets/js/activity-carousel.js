const motion = matchMedia('(prefers-reduced-motion: reduce)');
const requestDialog = document.querySelector('#activity-request-dialog');

for (const carousel of document.querySelectorAll('.activity-carousel')) {
    const track = carousel.querySelector('.activity-track');
    const arrows = carousel.querySelector('.activity-carousel-arrows');
    if (!arrows) continue;

    const previous = arrows.querySelector('[data-step="-1"]');
    const next = arrows.querySelector('[data-step="1"]');
    const cards = track.querySelectorAll('.activity-card');
    let timer;
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
        if (motion.matches || !visible || document.hidden || requestDialog?.open || end() <= 1 ||
            interacting || track.matches(':focus-within')) return;

        timer = setTimeout(() => {
            if (requestDialog?.open) return;
            scroll(1, true);
            schedule();
        }, 5000);
    };
    const update = () => {
        arrows.hidden = end() <= 1;
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft >= end() - 1;
    };

    for (const button of [previous, next]) {
        button.addEventListener('click', () => {
            scroll(Number(button.dataset.step));
            schedule();
        });
    }

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
    requestDialog?.addEventListener('close', schedule);
    motion.addEventListener('change', schedule);
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
