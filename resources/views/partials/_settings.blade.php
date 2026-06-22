{{--
    Global Settings (Dark Mode + Font Size)
    =======================================
    Include partial ini di setiap layout root sebelum </body>.

    Bahasa sekarang memakai Laravel Localization penuh:
    - Locale dibaca dari session oleh LocaleMiddleware.
    - Perubahan bahasa dikirim ke route language.switch.

    Partial ini hanya mempertahankan preferensi visual legacy:
    localStorage key darkMode, fontSize, emailNotif.
--}}

@once
<link rel="stylesheet" href="{{ asset('assets/css/theme-dark.css') }}" id="tp-theme-css">

<style id="tp-global-settings-css">
body.font-small  { font-size: 0.92em; }
body.font-medium { font-size: 1em; }
body.font-large  { font-size: 1.10em; }
</style>
@endonce

@once
<script id="tp-global-settings-js">
(function () {
    'use strict';

    var VALID_FONT_SIZES = ['small', 'medium', 'large'];

    function normalizeFontSize(size) {
        return VALID_FONT_SIZES.indexOf(size) !== -1 ? size : 'medium';
    }

    var SETTINGS = {
        darkMode: localStorage.getItem('darkMode') === 'true',
        fontSize: normalizeFontSize(localStorage.getItem('fontSize')),
        emailNotif: localStorage.getItem('emailNotif') !== 'false'
    };

    function applyVisualSettings() {
        var html = document.documentElement;
        var body = document.body;
        if (!body) return;

        body.classList.toggle('dark-mode', SETTINGS.darkMode);

        html.classList.remove('tp-font-small', 'tp-font-medium', 'tp-font-large');

        body.classList.remove('font-small', 'font-medium', 'font-large');
        body.classList.add('font-' + SETTINGS.fontSize);
    }

    if (document.body) {
        applyVisualSettings();
    } else {
        document.addEventListener('DOMContentLoaded', applyVisualSettings, { once: true });
    }

    window.addEventListener('storage', function (e) {
        if (!e.key) return;

        if (e.key === 'darkMode' || e.key === 'fontSize') {
            SETTINGS.darkMode = localStorage.getItem('darkMode') === 'true';
            SETTINGS.fontSize = normalizeFontSize(localStorage.getItem('fontSize'));
            applyVisualSettings();
        }
    });

    window.TPSettings = {
        get: function (key) {
            return SETTINGS[key];
        },
        set: function (key, value) {
            if (key === 'fontSize') {
                value = normalizeFontSize(value);
            }

            SETTINGS[key] = value;
            localStorage.setItem(key, typeof value === 'boolean' ? (value ? 'true' : 'false') : value);
            applyVisualSettings();
        },
        reapply: applyVisualSettings
    };
}());
</script>
@endonce
