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

    // Inject "Join as Creator" link directly inside Angular's auth-page form
    (function injectCreatorLink() {
        var LINK_ID = 'hvn-creator-link';

        function inject() {
            if (document.getElementById(LINK_ID)) return;

            // Try footer first, then fall back to auth-page itself
            var target = document.querySelector('auth-page-footer')
                      || document.querySelector('auth-page');
            if (!target) return;

            var wrap = document.createElement('div');
            wrap.id = LINK_ID;
            wrap.style.cssText = 'text-align:center;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.08);font-size:13px;';
            wrap.innerHTML = 'Want to share content? <a href="/creator-signup" style="color:#6c63ff;font-weight:500;text-decoration:none;">Join as a Creator →</a>';
            target.appendChild(wrap);
        }

        function cleanup() {
            if (!document.querySelector('auth-page')) {
                var el = document.getElementById(LINK_ID);
                if (el) el.remove();
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
