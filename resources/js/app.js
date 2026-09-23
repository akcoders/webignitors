import * as bootstrap from 'bootstrap';

const root = document.documentElement;
const nav = document.querySelector('.site-nav');
const progressBar = document.querySelector('.scroll-progress span');
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(pointer: fine)').matches;
const motionEnabled = !reduceMotion;

root.classList.add('has-js');
root.classList.toggle('has-fine-pointer', finePointer);

document.querySelectorAll('.page-hero .container').forEach((element) => {
    element.dataset.parallax = '-0.025';
});

document.querySelectorAll('.story-orb').forEach((element) => {
    element.dataset.parallax = '0.08';
});

const parallaxItems = [...document.querySelectorAll('[data-parallax]')];
let scrollTicking = false;

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const renderScroll = () => {
    const scrollTop = window.scrollY;
    const scrollRange = document.documentElement.scrollHeight - window.innerHeight;
    const progress = scrollRange > 0 ? scrollTop / scrollRange : 0;

    nav?.classList.toggle('is-scrolled', scrollTop > 24);

    if (progressBar) {
        progressBar.style.transform = `scaleX(${progress})`;
    }

    if (motionEnabled && window.innerWidth > 767) {
        parallaxItems.forEach((element) => {
            const speed = Number.parseFloat(element.dataset.parallax || '0');
            const rect = element.getBoundingClientRect();

            if (rect.bottom > -200 && rect.top < window.innerHeight + 200) {
                const centerOffset = rect.top + rect.height / 2 - window.innerHeight / 2;
                const distance = clamp(centerOffset * speed, -140, 140);
                element.style.setProperty('--parallax-y', `${distance}px`);
            }
        });
    }

    root.style.setProperty('--scroll-depth', `${scrollTop * 0.025}px`);
    scrollTicking = false;
};

const requestScrollRender = () => {
    if (!scrollTicking) {
        requestAnimationFrame(renderScroll);
        scrollTicking = true;
    }
};

renderScroll();
window.addEventListener('scroll', requestScrollRender, { passive: true });
window.addEventListener('resize', requestScrollRender, { passive: true });

const revealItems = document.querySelectorAll('.reveal');
revealItems.forEach((item, index) => {
    item.style.setProperty('--reveal-delay', `${(index % 4) * 70}ms`);
});

if ('IntersectionObserver' in window && motionEnabled) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.08,
    });

    revealItems.forEach((item) => revealObserver.observe(item));
} else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
}

if (finePointer && motionEnabled) {
    const cursorCore = document.querySelector('.cursor-core');
    const cursorAura = document.querySelector('.cursor-aura');
    let pointerX = window.innerWidth / 2;
    let pointerY = window.innerHeight / 2;
    let auraX = pointerX;
    let auraY = pointerY;

    window.addEventListener('pointermove', (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;
        root.style.setProperty('--pointer-x', `${pointerX}px`);
        root.style.setProperty('--pointer-y', `${pointerY}px`);

        if (cursorCore) {
            cursorCore.style.transform = `translate3d(${pointerX}px, ${pointerY}px, 0)`;
        }
    }, { passive: true });

    const animateAura = () => {
        auraX += (pointerX - auraX) * 0.14;
        auraY += (pointerY - auraY) * 0.14;

        if (cursorAura) {
            cursorAura.style.transform = `translate3d(${auraX}px, ${auraY}px, 0)`;
        }

        requestAnimationFrame(animateAura);
    };

    animateAura();

    document.querySelectorAll('a, button, input, select, textarea').forEach((element) => {
        element.addEventListener('pointerenter', () => root.classList.add('is-hovering'));
        element.addEventListener('pointerleave', () => root.classList.remove('is-hovering'));
    });

    const tiltCards = document.querySelectorAll('[data-tilt], .bento-card, .project-card, .value-card, .deliverable-card');
    tiltCards.forEach((card) => {
        card.classList.add('tilt-surface');

        card.addEventListener('pointermove', (event) => {
            const bounds = card.getBoundingClientRect();
            const x = (event.clientX - bounds.left) / bounds.width;
            const y = (event.clientY - bounds.top) / bounds.height;

            card.style.setProperty('--tilt-x', `${(0.5 - y) * 7}deg`);
            card.style.setProperty('--tilt-y', `${(x - 0.5) * 8}deg`);
            card.style.setProperty('--card-x', `${x * 100}%`);
            card.style.setProperty('--card-y', `${y * 100}%`);
        });

        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--tilt-x', '0deg');
            card.style.setProperty('--tilt-y', '0deg');
        });
    });

    document.querySelectorAll('[data-magnetic], .site-nav .btn').forEach((element) => {
        element.addEventListener('pointermove', (event) => {
            const bounds = element.getBoundingClientRect();
            const x = event.clientX - bounds.left - bounds.width / 2;
            const y = event.clientY - bounds.top - bounds.height / 2;
            element.style.setProperty('--mag-x', `${x * 0.16}px`);
            element.style.setProperty('--mag-y', `${y * 0.16}px`);
        });

        element.addEventListener('pointerleave', () => {
            element.style.setProperty('--mag-x', '0px');
            element.style.setProperty('--mag-y', '0px');
        });
    });
}

document.querySelectorAll('.navbar-collapse .nav-link:not(.dropdown-toggle), .navbar-collapse .dropdown-item').forEach((link) => {
    link.addEventListener('click', () => {
        const menu = document.querySelector('.navbar-collapse.show');
        if (menu && bootstrap.Collapse) {
            bootstrap.Collapse.getOrCreateInstance(menu).hide();
        }
    });
});

const message = document.querySelector('#message');
const count = document.querySelector('[data-message-count]');

if (message && count) {
    const updateCount = () => {
        count.textContent = `${message.value.length} / 3000`;
    };

    updateCount();
    message.addEventListener('input', updateCount);
}

const inquiryForm = document.querySelector('[data-inquiry-form]');
if (inquiryForm) {
    inquiryForm.addEventListener('submit', () => {
        const button = inquiryForm.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Sending brief…';
        }
    });
}

document.querySelectorAll('.audit-url-form, .dashboard-audit-form').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Starting analysis…';
        }
    });
});

document.querySelectorAll('.auth-form-panel form').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Please wait…';
        }
    });
});

document.querySelectorAll('[data-copy-url]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyUrl);
            const original = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check2"></i>';
            window.setTimeout(() => {
                button.innerHTML = original;
            }, 1600);
        } catch {
            window.prompt('Copy this link:', button.dataset.copyUrl);
        }
    });
});

const blogEditor = document.querySelector('.blog-editor');

if (blogEditor) {
    const title = blogEditor.querySelector('#title');
    const slug = blogEditor.querySelector('[data-slug-target]');
    const metaTitle = blogEditor.querySelector('#meta_title');
    const excerpt = blogEditor.querySelector('#excerpt');
    const metaDescription = blogEditor.querySelector('#meta_description');
    const previewTitle = blogEditor.querySelector('[data-seo-title]');
    const previewDescription = blogEditor.querySelector('[data-seo-description]');
    const previewSlug = blogEditor.querySelector('[data-seo-slug]');
    const content = blogEditor.querySelector('[data-markdown-content]');
    let slugWasEdited = Boolean(slug?.value);

    const toSlug = (value) => value
        .toLowerCase()
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 190);

    const refreshPreview = () => {
        if (previewTitle) previewTitle.textContent = metaTitle?.value || title?.value || 'Your article title';
        if (previewDescription) previewDescription.textContent = metaDescription?.value || excerpt?.value || 'Your meta description will appear here. Make the value of the article clear before someone clicks.';
        if (previewSlug) previewSlug.textContent = slug?.value || 'article-url';
    };

    title?.addEventListener('input', () => {
        if (!slugWasEdited && slug) slug.value = toSlug(title.value);
        refreshPreview();
    });
    slug?.addEventListener('input', () => {
        slugWasEdited = true;
        slug.value = toSlug(slug.value);
        refreshPreview();
    });
    [metaTitle, excerpt, metaDescription].forEach((field) => field?.addEventListener('input', refreshPreview));

    blogEditor.querySelectorAll('[data-count-input]').forEach((field) => {
        const counter = blogEditor.querySelector('[data-count-for="' + field.id + '"]');
        const refreshCount = () => {
            if (counter) counter.textContent = field.value.length;
        };
        field.addEventListener('input', refreshCount);
        refreshCount();
    });

    const wordCounter = blogEditor.querySelector('[data-word-count]');
    const refreshWords = () => {
        if (!wordCounter || !content) return;
        wordCounter.textContent = (content.value.trim().match(/\b[\p{L}\p{N}'’-]+\b/gu) || []).length;
    };
    content?.addEventListener('input', refreshWords);
    refreshWords();
    refreshPreview();
}

const reportProgress = document.querySelector('[data-report-status-url]');

if (reportProgress) {
    const stage = reportProgress.querySelector('[data-report-stage]');
    const percentage = reportProgress.querySelector('[data-report-percent]');
    const progress = reportProgress.querySelector('[data-report-progress]');
    let consecutiveFailures = 0;

    const refreshReportStatus = async () => {
        try {
            const response = await fetch(reportProgress.dataset.reportStatusUrl, {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Report status request failed with ${response.status}`);
            }

            const result = await response.json();
            consecutiveFailures = 0;

            if (stage && result.stage) {
                stage.textContent = result.stage;
            }

            const value = Math.max(0, Math.min(100, Number(result.progress) || 0));
            if (percentage) {
                percentage.textContent = `${value}%`;
            }
            if (progress) {
                progress.style.width = `${value}%`;
            }

            if (result.completed || result.failed) {
                window.location.reload();
                return;
            }

            window.setTimeout(refreshReportStatus, document.hidden ? 10000 : 4000);
        } catch {
            consecutiveFailures += 1;
            window.setTimeout(refreshReportStatus, Math.min(30000, 4000 * consecutiveFailures));
        }
    };

    window.setTimeout(refreshReportStatus, 1500);
}
