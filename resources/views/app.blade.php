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

    // Inject "Join as Creator" banner into Angular register form
    (function injectCreatorLink() {
        var injected = false;
        function tryInject() {
            if (injected) return;
            // Angular register forms typically have a submit button with type=submit
            // or a form with name/email inputs. Look for registration-related panels.
            var forms = document.querySelectorAll('auth-page, register-page, [class*="register"], [class*="auth-page"]');
            if (!forms.length) {
                forms = document.querySelectorAll('form');
            }
            forms.forEach(function(form) {
                if (form.querySelector('.hvn-creator-inject')) return;
                // Only inject if form has a username or name field (registration form)
                var hasNameField = form.querySelector('input[name="name"], input[placeholder*="name" i], input[placeholder*="username" i]');
                if (!hasNameField) return;
                var banner = document.createElement('div');
                banner.className = 'hvn-creator-inject';
                banner.style.cssText = 'margin-top:16px;padding:14px 16px;background:#1a1a2e;border:1px solid #3d3580;border-radius:8px;text-align:center;font-size:13px;color:#aaa;';
                banner.innerHTML = 'Want to share your content? <a href="/creator-signup" style="color:#6c63ff;font-weight:500;text-decoration:none;">Join as a Creator →</a>';
                form.appendChild(banner);
                injected = true;
            });
        }
        var obs = new MutationObserver(function() { tryInject(); });
        obs.observe(document.body, { childList: true, subtree: true });
        // Also try immediately after Angular boots
        window.addEventListener('load', function() { setTimeout(tryInject, 800); });
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
