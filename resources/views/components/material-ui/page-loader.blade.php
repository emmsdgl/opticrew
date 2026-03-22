{{-- Drop this component at the top of any <body> to show a loader on every page load/reload --}}
<div id="page-loader" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;transition:opacity 0.3s;"
     class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-md">
    <x-material-ui.lottie-loader :size="120" text="Loading" />
</div>

@once
<script>
(function() {
    var loaded = false;

    function hideLoader() {
        if (loaded) return;
        loaded = true;
        var loader = document.getElementById('page-loader');
        if (!loader) return;
        loader.style.opacity = '0';
        setTimeout(function() { loader.remove(); }, 300);
    }

    var hasSkeletons = false;

    window.addEventListener('skeletons-done', function() {
        hasSkeletons = true;
        hideLoader();
    });

    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!hasSkeletons) {
                hideLoader();
            }
        }, 400);
    });

    // Safety: if load event already fired (e.g., script ran late), hide after a timeout
    if (document.readyState === 'complete') {
        setTimeout(function() {
            if (!hasSkeletons) hideLoader();
        }, 500);
    }
})();
</script>
@endonce
