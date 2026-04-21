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

    // Map link text → correct HVN path (Angular stores wrong actions in the DB)
    // Keys are matched as exact words only — 'creators' must not match 'Join as Creator'.
    var HVN_TEXT_MAP = {
        'community': '/community',
        'creators':  '/creators',
    };
    // Paths that must always be a hard browser navigation (Angular has no route for them)
    var HVN_PREFIXES = ['/community', '/creators', '/creator-signup'];

    function isHvnPath(path) {
        return HVN_PREFIXES.some(function(p) {
            return path === p || path.startsWith(p + '/');
        });
    }

    function hvnPathForText(text) {
        var t = (text || '').trim().toLowerCase();
        for (var key in HVN_TEXT_MAP) {
            // whole-word match: 'community' hits "Community" but not "Join as Creator"
            if (t === key || new RegExp('\\b' + key + '\\b').test(t)) {
                // reject if the text is clearly a CTA like "join as creators"
                if (t.indexOf('join') !== -1) continue;
                return HVN_TEXT_MAP[key];
            }
        }
        return null;
    }

    // Patch every nav <a> whose visible text matches an HVN label.
    // This fixes links that Angular rendered with the wrong href (e.g. /news).
    function patchNavLinks() {
        var links = document.querySelectorAll('nav a, .nav a, [class*="navbar"] a, [class*="header"] a, [class*="menu"] a');
        links.forEach(function(a) {
            var path = hvnPathForText(a.textContent);
            if (!path) return;
            if (a.getAttribute('href') !== path) {
                a.setAttribute('href', path);
            }
            if (!a.__hvnPatched) {
                a.__hvnPatched = true;
                a.addEventListener('click', function(e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    window.location.href = path;
                }, true);
            }
        });
    }

    // Run once Angular has rendered, then watch for re-renders
    setTimeout(patchNavLinks, 400);
    setTimeout(patchNavLinks, 1200);
    var navPatchTimer;
    var navObs = new MutationObserver(function() {
        clearTimeout(navPatchTimer);
        navPatchTimer = setTimeout(patchNavLinks, 150);
    });
    navObs.observe(document.body, { childList: true, subtree: true });

    // Intercept ALL link clicks (capture phase) — catches any remaining HVN hrefs
    document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;
        var href = a.getAttribute('href') || '';

        // Force hard navigation for HVN paths
        if (isHvnPath(href)) {
            e.stopImmediatePropagation();
            e.preventDefault();
            window.location.href = href;
            return;
        }

        // Force hard navigation if link text matches HVN page
        var hvnPath = hvnPathForText(a.textContent);
        if (hvnPath) {
            e.stopImmediatePropagation();
            e.preventDefault();
            window.location.href = hvnPath;
            return;
        }

        // Clean trailing %20 from other links
        if (!/(%20|\s)+$/.test(href)) return;
        var clean = cleanUrl(href);
        if (!clean || clean === href) return;
        e.stopImmediatePropagation();
        e.preventDefault();
        history.pushState(null, '', clean);
        window.dispatchEvent(new PopStateEvent('popstate', { state: history.state }));
    }, true);

    // Inject "Join as a Creator" inside Angular's auth-page sign-in/register form.
    // auth-page .info-row  = the "Don't have an account?" row inside the panel.
    // auth-page .auth-panel = the white card containing the form.
    (function injectCreatorLink() {
        var LINK_ID = 'hvn-creator-link';

        function inject() {
            if (document.getElementById(LINK_ID)) return;
            if (!document.querySelector('auth-page')) return;

            // Best spot: right after the info-row (sign-in↔register toggle) inside the panel
            var infoRow   = document.querySelector('auth-page .info-row');
            var authPanel = document.querySelector('auth-page .auth-panel');
            var target    = infoRow || authPanel || document.querySelector('auth-page-footer') || document.querySelector('auth-page');
            if (!target) return;

            var div = document.createElement('div');
            div.id = LINK_ID;
            div.style.cssText = [
                'margin-top:16px',
                'padding-top:16px',
                'border-top:1px solid rgba(255,255,255,0.08)',
                'text-align:center',
                'font-size:13px',
                'color:rgba(255,255,255,0.6)',
                'font-family:Roboto,sans-serif',
            ].join(';');
            div.innerHTML = 'Want to share your content? '
                + '<a href="/creator-signup" onclick="event.stopPropagation();window.location.href=\'/creator-signup\';return false;" '
                + 'style="color:#F65F54;font-weight:500;text-decoration:none;">Join as a Creator →</a>';

            // Insert after infoRow, or append to panel/page
            if (infoRow && infoRow.parentNode) {
                infoRow.parentNode.insertBefore(div, infoRow.nextSibling);
            } else {
                target.appendChild(div);
            }
        }

        function cleanup() {
            if (!document.querySelector('auth-page')) {
                var el = document.getElementById(LINK_ID);
                if (el) el.remove();
            }
        }

        var obs = new MutationObserver(function() { inject(); cleanup(); });
        obs.observe(document.body, { childList: true, subtree: true });
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
