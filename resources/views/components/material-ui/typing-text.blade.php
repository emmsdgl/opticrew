{{--
    Typing Text Animation
    Add data-typing to any element to enable the effect.
    Optional attributes:
        data-typing-delay="0"       (start delay in seconds)
        data-typing-duration="1.5"  (total duration in seconds)

    Include this component once in a layout to enable globally.
--}}

<style>
    /* Hide text before JS wraps chars, preventing flash of unstyled text */
    [data-typing]:not([data-typing-init]) {
        visibility: hidden;
    }
    [data-typing][data-typing-init] {
        visibility: visible;
    }
    [data-typing] .typing-char-wrap {
        opacity: 0;
        display: inline;
        transition: opacity 0.3s ease-in-out;
    }
    [data-typing] .typing-char-el {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    [data-typing].typing-active .typing-char-wrap,
    [data-typing].typing-active .typing-char-el {
        opacity: 1;
    }
</style>

<script>
(function() {
    function initTyping() {
        const elements = document.querySelectorAll('[data-typing]');

        elements.forEach(el => {
            if (el.dataset.typingInit) return;
            el.dataset.typingInit = 'true';

            const delay = parseFloat(el.dataset.typingDelay || '0');
            const duration = parseFloat(el.dataset.typingDuration || '1.5');

            // Walk the DOM and wrap text nodes in spans, preserving child elements and layout
            function wrapTextNodes(node) {
                const children = Array.from(node.childNodes);
                children.forEach(child => {
                    if (child.nodeType === Node.TEXT_NODE) {
                        const text = child.textContent;
                        if (!text.trim() && !text.includes(' ')) return;
                        const fragment = document.createDocumentFragment();
                        text.split('').forEach(char => {
                            const span = document.createElement('span');
                            span.className = 'typing-char-wrap';
                            span.textContent = char;
                            fragment.appendChild(span);
                        });
                        child.replaceWith(fragment);
                    } else if (child.nodeType === Node.ELEMENT_NODE) {
                        const tag = child.tagName.toLowerCase();
                        if (tag === 'br' || tag === 'img') return;
                        // Skip flex/inline-flex containers — treat as one animated unit
                        // without overriding their display property
                        const display = getComputedStyle(child).display;
                        if (display.includes('flex') || display.includes('grid')) {
                            child.classList.add('typing-char-el');
                            return;
                        }
                        wrapTextNodes(child);
                    }
                });
            }

            wrapTextNodes(el);

            // Get all chars and compute stagger delays
            const chars = el.querySelectorAll('.typing-char-wrap, .typing-char-el');
            const charDelay = chars.length > 0 ? duration / chars.length : 0;
            chars.forEach((char, i) => {
                char.style.transitionDelay = (delay + i * charDelay) + 's';
            });

            // Force the browser to paint the initial opacity:0 state
            // before we start observing. Without this double-rAF, elements
            // already in the viewport get typing-active in the same frame
            // as the char wrapping, so no transition ever fires.
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    const observer = new IntersectionObserver(entries => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                el.classList.add('typing-active');
                                observer.unobserve(el);
                            }
                        });
                    }, { threshold: 0.2 });

                    observer.observe(el);
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTyping);
    } else {
        requestAnimationFrame(initTyping);
    }
})();
</script>
