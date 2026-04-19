@extends('common::framework')

@section('body-end')
<script>
(function () {
    function cleanUrl(url) {
        if (!url) return url;
        return url.replace(/(%20)+$/, '').replace(/\s+$/, '');
    }
    var _push = history.pushState.bind(history);
    var _replace = history.replaceState.bind(history);
    history.pushState = function (s, t, url) { return _push(s, t, cleanUrl(url)); };
    history.replaceState = function (s, t, url) { return _replace(s, t, cleanUrl(url)); };
    var p = window.location.pathname;
    if (/(%20)+$/.test(p) || /\s+$/.test(p)) {
        history.replaceState(null, '', cleanUrl(p) + window.location.search + window.location.hash);
    }
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
