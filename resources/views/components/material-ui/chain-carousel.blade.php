@props([
    'items' => [],
    'scrollSpeedMs' => 1800,
    'visibleItemCount' => 7,
    'containerClass' => '',
])

@php
    $id = 'chain-' . uniqid();
@endphp

<div id="{{ $id }}" class="w-full overflow-hidden {{ $containerClass }}">
    <div class="flex flex-col xl:flex-row max-w-7xl mx-auto px-4 md:px-8 gap-8 xl:gap-12 justify-center items-center">

        {{-- Left Carousel (hidden on mobile) --}}
        <div id="{{ $id }}-left" class="relative w-full max-w-md xl:max-w-2xl h-[420px] items-center justify-center hidden xl:flex xl:-left-14 overflow-hidden">
            <div class="absolute inset-0 z-10 pointer-events-none">
                <div class="absolute top-0 h-1/4 w-full bg-gradient-to-b from-white dark:from-gray-900 to-transparent"></div>
                <div class="absolute bottom-0 h-1/4 w-full bg-gradient-to-t from-white dark:from-gray-900 to-transparent"></div>
            </div>
            <div id="{{ $id }}-left-items" class="relative w-full h-full"></div>
        </div>

        {{-- Center Column --}}
        <div class="flex flex-col text-center gap-0 w-full items-center">
            {{-- Dynamic content (fades on change) - fixed height prevents layout shift --}}
            <div id="{{ $id }}-dynamic" class="flex flex-col items-center gap-3 w-full h-[280px] justify-center overflow-hidden">
                <div id="{{ $id }}-icon" class="p-3 rounded-full shadow-lg shadow-blue-500/25" style="background: linear-gradient(135deg, #3b82f6, #6366f1, #8b5cf6);">
                </div>
                <h3 id="{{ $id }}-name" class="text-base xl:text-xl font-bold text-gray-900 dark:text-white mt-2"></h3>
                <p id="{{ $id }}-price" class="text-base xl:text-xl font-bold text-blue-600 dark:text-blue-400"></p>
                <p id="{{ $id }}-details" class="text-xs xl:text-sm text-gray-500 dark:text-gray-400 leading-relaxed w-full"></p>
                <div id="{{ $id }}-badges" class="flex flex-wrap gap-2 mt-2 justify-center"></div>
            </div>

            {{-- Search Bar (completely outside fade container) --}}
            <div id="{{ $id }}-searchbar" class="my-12 relative w-full">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
                    <input id="{{ $id }}-search" type="text" placeholder="Search a cleaning service..."
                           class="w-full pl-11 pr-10 py-3 text-sm rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-colors duration-200" />
                    <button id="{{ $id }}-search-clear" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="{{ $id }}-dropdown" class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 z-20 max-h-60 overflow-y-auto shadow-xl hidden"></div>
            </div>
        </div>

        {{-- Right Carousel --}}
        <div id="{{ $id }}-right" class="relative w-full max-w-md xl:max-w-2xl h-[640px] hidden xl:flex items-center justify-center xl:-right-14 overflow-hidden">
            <div class="absolute inset-0 z-10 pointer-events-none">
                <div class="absolute top-0 h-1/4 w-full bg-gradient-to-b from-white dark:from-gray-900 to-transparent"></div>
                <div class="absolute bottom-0 h-1/4 w-full bg-gradient-to-t from-white dark:from-gray-900 to-transparent"></div>
            </div>
            <div id="{{ $id }}-right-items" class="relative w-full h-full"></div>
        </div>

    </div>
</div>

<script>
(function() {
    const cfg = {
        id: '{{ $id }}',
        items: @json($items),
        speed: {{ $scrollSpeedMs }},
        visible: {{ $visibleItemCount % 2 === 0 ? $visibleItemCount + 1 : $visibleItemCount }},
    };

    function init() {
        const n = cfg.items.length;
        if (n === 0) return;

        let currentIndex = 0;
        let isPaused = false;
        let scrollTimeout = null;

        const leftContainer = document.getElementById(cfg.id + '-left-items');
        const rightContainer = document.getElementById(cfg.id + '-right-items');
        const iconEl = document.getElementById(cfg.id + '-icon');
        const nameEl = document.getElementById(cfg.id + '-name');
        const priceEl = document.getElementById(cfg.id + '-price');
        const detailsEl = document.getElementById(cfg.id + '-details');
        const badgesEl = document.getElementById(cfg.id + '-badges');
        const searchInput = document.getElementById(cfg.id + '-search');
        const searchClear = document.getElementById(cfg.id + '-search-clear');
        const dropdown = document.getElementById(cfg.id + '-dropdown');

        // Pause on hover
        const leftWrap = document.getElementById(cfg.id + '-left');
        const rightWrap = document.getElementById(cfg.id + '-right');
        [leftWrap, rightWrap].forEach(el => {
            if (!el) return;
            el.addEventListener('mouseenter', () => isPaused = true);
            el.addEventListener('mouseleave', () => isPaused = false);
        });

        // Search functionality
        function showDropdown(filtered) {
            dropdown.innerHTML = '';
            if (filtered.length === 0) {
                dropdown.classList.add('hidden');
                return;
            }
            filtered.forEach(item => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-150 rounded-lg m-1';
                row.innerHTML = `
                    <div class="w-6 h-6 flex-shrink-0 text-blue-600 dark:text-blue-400">${item.icon || ''}</div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">${item.name}</span>
                    <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">${item.price || ''}</span>
                `;
                row.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    const idx = cfg.items.findIndex(c => c.name === item.name);
                    if (idx !== -1) {
                        currentIndex = idx;
                        isPaused = true;
                        render();
                    }
                    searchInput.value = item.name;
                    searchClear.classList.remove('hidden');
                    dropdown.classList.add('hidden');
                });
                dropdown.appendChild(row);
            });
            dropdown.classList.remove('hidden');
        }

        searchInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            searchClear.classList.toggle('hidden', !val);
            if (!val) {
                dropdown.classList.add('hidden');
                isPaused = false;
                return;
            }
            isPaused = true;
            const filtered = cfg.items.filter(item => item.name.toLowerCase().includes(val));
            showDropdown(filtered);
        });

        searchInput.addEventListener('focus', () => {
            const val = searchInput.value.toLowerCase();
            if (val) {
                isPaused = true;
                const filtered = cfg.items.filter(item => item.name.toLowerCase().includes(val));
                showDropdown(filtered);
            }
        });

        searchInput.addEventListener('blur', () => {
            setTimeout(() => dropdown.classList.add('hidden'), 200);
        });

        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            searchClear.classList.add('hidden');
            dropdown.classList.add('hidden');
            isPaused = false;
        });

        // Pause on scroll
        window.addEventListener('scroll', () => {
            isPaused = true;
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => isPaused = false, 600);
        }, { passive: true });

        function getVisibleItems() {
            const half = Math.floor(cfg.visible / 2);
            const result = [];
            for (let i = -half; i <= half; i++) {
                let idx = ((currentIndex + i) % n + n) % n;
                result.push({ ...cfg.items[idx], dist: i, origIdx: idx });
            }
            return result;
        }

        function createCard(item, side) {
            const dist = Math.abs(item.dist);
            const opacity = Math.max(0, 1 - dist / 3.5);
            const scale = Math.max(0.6, 1 - dist * 0.08);
            const yOffset = item.dist * 80;
            const xOffset = side === 'left' ? -dist * 40 : dist * 40;

            const card = document.createElement('div');
            card.className = 'absolute flex items-center gap-4 px-5 py-3 transition-all duration-500 ease-in-out';
            card.style.cssText = `
                left: 50%; top: 50%;
                transform: translate(-50%, -50%) translateY(${yOffset}px) translateX(${xOffset}px) scale(${scale});
                opacity: ${opacity};
                ${side === 'left' ? 'flex-direction: row-reverse;' : ''}
            `;

            const iconWrap = document.createElement('div');
            iconWrap.className = 'rounded-full p-2.5 flex-shrink-0 bg-blue-600 dark:bg-blue-500 border border-blue-500/30 dark:border-blue-400/30';
            iconWrap.innerHTML = `<div class="w-7 h-7 flex items-center justify-center text-white">${item.icon || ''}</div>`;

            const textWrap = document.createElement('div');
            textWrap.className = `flex flex-col ${side === 'left' ? 'text-right' : 'text-left'}`;
            textWrap.innerHTML = `
                <span class="text-sm lg:text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">${item.name}</span>
                <span class="text-xs lg:text-sm text-gray-500 dark:text-gray-400">${item.price || ''}</span>
            `;

            card.appendChild(iconWrap);
            card.appendChild(textWrap);
            return card;
        }

        const dynamicEl = document.getElementById(cfg.id + '-dynamic');
        dynamicEl.style.transition = 'opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

        function updateCenter() {
            const item = cfg.items[currentIndex];

            // Smooth fade out
            dynamicEl.style.opacity = '0';
            dynamicEl.style.transform = 'translateY(6px)';

            setTimeout(() => {
                iconEl.innerHTML = `<div class="w-8 h-8 flex items-center justify-center text-white">${item.icon || ''}</div>`;
                nameEl.textContent = item.name;
                priceEl.textContent = item.price || '';
                detailsEl.textContent = item.description || '';

                // Badges (max 4, auto-width pills)
                badgesEl.innerHTML = '';
                if (item.badges && item.badges.length) {
                    item.badges.slice(0, 4).forEach(badge => {
                        const span = document.createElement('span');
                        span.className = 'px-4 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 whitespace-nowrap';
                        span.textContent = badge;
                        badgesEl.appendChild(span);
                    });
                }

                // Brief pause at invisible state, then smooth fade in
                requestAnimationFrame(() => {
                    dynamicEl.style.opacity = '1';
                    dynamicEl.style.transform = 'translateY(0)';
                });
            }, 400);
        }

        function render() {
            const items = getVisibleItems();

            // Clear and rebuild
            if (leftContainer) leftContainer.innerHTML = '';
            if (rightContainer) rightContainer.innerHTML = '';

            items.forEach(item => {
                if (leftContainer) leftContainer.appendChild(createCard(item, 'left'));
                if (rightContainer) rightContainer.appendChild(createCard(item, 'right'));
            });

            updateCenter();
        }

        // Initial render
        render();

        // Intersection Observer - only animate when visible
        let isInView = false;
        const observer = new IntersectionObserver(entries => {
            isInView = entries[0].isIntersecting;
        }, { rootMargin: '-80px 0px' });
        observer.observe(document.getElementById(cfg.id));

        // Auto-scroll
        setInterval(() => {
            if (isPaused || !isInView) return;
            currentIndex = (currentIndex + 1) % n;
            render();
        }, cfg.speed);

        // Slide-in animation on scroll
        const leftWrapEl = document.getElementById(cfg.id + '-left');
        const rightWrapEl = document.getElementById(cfg.id + '-right');
        if (leftWrapEl) {
            leftWrapEl.style.transform = 'translateX(-60px)';
            leftWrapEl.style.opacity = '0';
            leftWrapEl.style.transition = 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s ease';
        }
        if (rightWrapEl) {
            rightWrapEl.style.transform = 'translateX(60px)';
            rightWrapEl.style.opacity = '0';
            rightWrapEl.style.transition = 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s ease';
        }

        const entryObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.transform = 'translateX(0)';
                    entry.target.style.opacity = '1';
                    entryObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: '-50px 0px' });

        if (leftWrapEl) entryObserver.observe(leftWrapEl);
        if (rightWrapEl) entryObserver.observe(rightWrapEl);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        requestAnimationFrame(init);
    }
})();
</script>
