@extends('common::framework')

@section('body-end')
<script>
(function () {
    function cleanUrl(url) {
        if (!url) return url;
        return url.replace(/(%20|\s)+$/, '');
    }

    // Fix address bar on hard load
    var p = window.location.pathname;
    if (/(%20|\s)+$/.test(p)) {
        history.replaceState(null, '', cleanUrl(p) + window.location.search + window.location.hash);
    }

    // Intercept link clicks BEFORE Angular's router (capture phase)
    document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;
        var href = a.getAttribute('href') || '';
        if (!/(%20|\s)+$/.test(href)) return;
        var clean = cleanUrl(href);
        if (!clean || clean === href) return;
        e.stopImmediatePropagation();
        e.preventDefault();
        history.pushState(null, '', clean);
        window.dispatchEvent(new PopStateEvent('popstate', { state: history.state }));
    }, true);

    // Inject "Join as Creator" into Angular's auth-page (register tab)
    (function injectCreatorBanner() {
        var BANNER_ID = 'hvn-creator-cta';

        function inject() {
            // auth-page is Angular's component element for sign-in / register
            var authPage = document.querySelector('auth-page');
            if (!authPage || document.getElementById(BANNER_ID)) return;

            // Only inject on the register tab (page contains a password-confirm field,
            // or simply always inject since creators need to know about the option)
            var banner = document.createElement('div');
            banner.id = BANNER_ID;
            banner.style.cssText = [
                'position:fixed',
                'bottom:20px',
                'right:20px',
                'z-index:99999',
                'background:linear-gradient(135deg,#3d3580,#6c63ff)',
                'color:#fff',
                'padding:13px 18px',
                'border-radius:10px',
                'font-size:13px',
                'font-family:Roboto,sans-serif',
                'box-shadow:0 4px 20px rgba(108,99,255,.45)',
                'cursor:pointer',
                'text-decoration:none',
                'display:flex',
                'align-items:center',
                'gap:8px',
                'max-width:220px',
                'line-height:1.4',
            ].join(';');
            banner.innerHTML = '<span style="font-size:18px;">🎬</span><span><strong style="display:block;font-size:14px;margin-bottom:1px;">Join as Creator</strong>Upload content & get discovered</span>';
            banner.addEventListener('click', function() { window.location.href = '/creator-signup'; });

            document.body.appendChild(banner);
        }

        // Remove banner when auth-page closes
        function cleanup() {
            if (!document.querySelector('auth-page')) {
                var b = document.getElementById(BANNER_ID);
                if (b) b.remove();
            }
        }

        var obs = new MutationObserver(function() { inject(); cleanup(); });
        obs.observe(document.body, { childList: true, subtree: true });
        inject();
    }());
}());
</script>
@endsection

@section('angular-styles')
    {{--angular styles begin--}}
		<link rel="stylesheet" href="client/styles.dd30edb2e30333fe4043.css" media="print" onload="this.media='all'">
	{{--angular styles end--}}
@endsection

@section('angular-scripts')
    {{--angular scripts begin--}}
		<script src="client/runtime.da6032f6256ba37882c7.js" defer=""></script>
		<script src="client/polyfills.d433a9329e434544e226.js" defer=""></script>
		<script src="client/main.51d3ab87516a2e615d53.js" defer=""></script>
	{{--angular scripts end--}}
@endsection
