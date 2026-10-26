(function () {
    'use strict';

    var charts = [];

    function token(name, fallback) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
    }

    function alpha(hex, amount) {
        var value = hex.replace('#', '');
        if (value.length !== 6) {
            return hex;
        }
        var r = parseInt(value.substring(0, 2), 16);
        var g = parseInt(value.substring(2, 4), 16);
        var b = parseInt(value.substring(4, 6), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + amount + ')';
    }

    function drawCharts() {
        if (typeof Chart === 'undefined') {
            return;
        }

        charts.forEach(function (chart) { chart.destroy(); });
        charts = [];

        var brand = token('--brand', '#c90076');
        var second = token('--muted', '#6b6570');
        var line = token('--line', '#e5e1e7');

        Chart.defaults.color = second;
        Chart.defaults.borderColor = line;
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;

        document.querySelectorAll('canvas[data-chart]').forEach(function (canvas) {
            var config = JSON.parse(canvas.dataset.chart);
            var small = canvas.dataset.size === 'small';
            var hasSecondAxis = config.datasets.some(function (set) { return set.yAxisID === 'y1'; });

            var datasets = config.datasets.map(function (set, index) {
                var type = set.type || config.type;
                var color = index === 0 ? brand : second;
                return Object.assign({
                    borderColor: color,
                    backgroundColor: type === 'bar' ? alpha(color, index === 0 && config.type === 'bar' ? 0.4 : 0.3) : alpha(color, 0.1),
                    borderWidth: 2,
                    pointRadius: small ? 0 : 2,
                    pointBackgroundColor: color,
                    tension: 0.35,
                    fill: type !== 'bar' && index === 0,
                    spanGaps: true,
                    borderRadius: type === 'bar' ? 6 : 0,
                    order: index === 0 ? 1 : 2
                }, set);
            });

            var scales = {
                x: { display: !small, grid: { display: false }, ticks: { maxTicksLimit: 10 } },
                y: { display: !small, beginAtZero: true, grid: { color: line } }
            };

            if (config.max) {
                scales.y.max = config.max;
            }

            if (hasSecondAxis) {
                scales.y1 = { position: 'right', beginAtZero: true, grid: { display: false }, ticks: { precision: 0 } };
            }

            charts.push(new Chart(canvas, {
                type: config.type,
                data: { labels: config.labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: !small && datasets.length > 1 }, tooltip: { enabled: true } },
                    scales: scales
                }
            }));
        });
    }

    function themePickers() {
        var pickers = document.querySelectorAll('[data-theme-picker]');
        var csrf = document.querySelector('meta[name="csrf-token"]');

        pickers.forEach(function (picker) {
            var form = picker.querySelector('form');

            form.addEventListener('click', function (event) {
                var option = event.target.closest('button[name="theme"]');
                if (!option || !window.fetch) {
                    return;
                }
                event.preventDefault();

                var root = document.documentElement;
                root.setAttribute('data-theme', option.value);
                root.setAttribute('data-bs-theme', option.dataset.dark === 'true' ? 'dark' : 'light');

                document.querySelectorAll('button[name="theme"]').forEach(function (button) {
                    button.setAttribute('aria-pressed', button.value === option.value ? 'true' : 'false');
                });

                picker.open = false;
                drawCharts();

                var data = new FormData(form);
                data.append('theme', option.value);

                fetch(form.action, {
                    method: 'POST',
                    body: data,
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '' }
                });
            });
        });

        document.addEventListener('click', function (event) {
            pickers.forEach(function (picker) {
                if (picker.open && !picker.contains(event.target)) {
                    picker.open = false;
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }
            pickers.forEach(function (picker) {
                if (picker.open) {
                    picker.open = false;
                    picker.querySelector('summary').focus();
                }
            });
        });
    }

    function clickableRows() {
        document.querySelectorAll('tr[data-href]').forEach(function (row) {
            row.addEventListener('click', function (event) {
                if (event.target.closest('a, button, form')) {
                    return;
                }
                window.location = row.dataset.href;
            });
        });
    }

    function confirmForms() {
        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                }
            });
        });
    }

    function addMoreLists() {
        document.querySelectorAll('[data-add-more]').forEach(function (button) {
            button.addEventListener('click', function () {
                var list = document.getElementById(button.dataset.addMore);
                var first = list.querySelector('.input-group');
                var copy = first.cloneNode(true);
                copy.querySelector('input').value = '';
                list.appendChild(copy);
                copy.querySelector('input').focus();
            });
        });

        document.addEventListener('click', function (event) {
            var remove = event.target.closest('[data-remove-row]');
            if (!remove) {
                return;
            }
            var list = remove.closest('.add-more-list');
            var group = remove.closest('.input-group');
            if (list.querySelectorAll('.input-group').length > 1) {
                group.remove();
            } else {
                group.querySelector('input').value = '';
            }
        });
    }

    function colourPickers() {
        document.querySelectorAll('[data-colour-text]').forEach(function (text) {
            var picker = document.getElementById(text.dataset.colourText);
            picker.addEventListener('input', function () {
                text.value = picker.value;
            });
            text.addEventListener('input', function () {
                if (/^#[0-9a-fA-F]{6}$/.test(text.value)) {
                    picker.value = text.value;
                }
            });
        });
    }

    function logoPreview() {
        document.querySelectorAll('[data-logo-input]').forEach(function (input) {
            var target = document.getElementById(input.dataset.logoInput);
            input.addEventListener('change', function () {
                var file = input.files[0];
                if (!file) {
                    return;
                }
                var img = target.querySelector('img') || target.insertBefore(document.createElement('img'), target.firstChild);
                img.src = URL.createObjectURL(file);
            });
        });
    }

    function rangeForms() {
        document.querySelectorAll('.range-form select[name="range"]').forEach(function (select) {
            var form = select.form;
            var dates = form.querySelectorAll('input[type="date"]');
            function sync() {
                dates.forEach(function (input) { input.disabled = select.value !== ''; });
            }
            select.addEventListener('change', function () {
                sync();
                if (select.value !== '') {
                    form.submit();
                }
            });
            sync();
        });
    }

    function channelForms() {
        document.querySelectorAll('[data-channel-form]').forEach(function (form) {
            var subject = form.querySelector('[data-subject]');
            function sync() {
                var channel = form.querySelector('input[name="channel"]:checked');
                subject.hidden = !channel || channel.value !== 'email';
            }
            form.querySelectorAll('input[name="channel"]').forEach(function (radio) {
                radio.addEventListener('change', sync);
            });
            sync();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        drawCharts();
        themePickers();
        clickableRows();
        confirmForms();
        addMoreLists();
        colourPickers();
        logoPreview();
        rangeForms();
        channelForms();
    });
})();
