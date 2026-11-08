(function () {
  'use strict';

  var shell = document.querySelector('[data-shell]');

  /* ---------- Add sheet + FAB ---------- */
  var fab = document.querySelector('[data-fab]');
  var sheet = document.getElementById('add-sheet');

  function setSheet(open) {
    if (!shell || !sheet || !fab) return;
    shell.classList.toggle('is-sheet-open', open);
    fab.setAttribute('aria-expanded', String(open));
    sheet.setAttribute('aria-hidden', String(!open));
    sheet.inert = !open;
  }

  if (fab && sheet) {
    var pressTimer = null;
    var longPressed = false;

    fab.addEventListener('pointerdown', function () {
      longPressed = false;
      pressTimer = setTimeout(function () {
        longPressed = true;
        if (navigator.vibrate) navigator.vibrate(10);
        window.location.href = fab.dataset.quickUrl;
      }, 500);
    });
    ['pointerup', 'pointerleave', 'pointercancel'].forEach(function (type) {
      fab.addEventListener(type, function () { clearTimeout(pressTimer); });
    });
    fab.addEventListener('click', function () {
      if (longPressed) return;
      setSheet(!shell.classList.contains('is-sheet-open'));
    });
    fab.addEventListener('contextmenu', function (e) { e.preventDefault(); });

    document.querySelectorAll('[data-sheet-close]').forEach(function (el) {
      el.addEventListener('click', function () { setSheet(false); });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setSheet(false);
    });
    window.addEventListener('pageshow', function () { setSheet(false); });
  }

  /* ---------- Toast ---------- */
  var toast = document.querySelector('[data-toast]');
  if (toast) {
    setTimeout(function () {
      toast.classList.add('is-hiding');
      setTimeout(function () { toast.remove(); }, 300);
    }, 5000);
  }

  /* ---------- Big amount input: digits only, auto width ---------- */
  document.querySelectorAll('[data-amount-input]').forEach(function (input) {
    function fit() {
      input.value = input.value.replace(/\D+/g, '').replace(/^0+(?=\d)/, '');
      input.style.width = Math.max(2, input.value.length || 1) + 1 + 'ch';
      var form = input.form;
      if (form) form.dispatchEvent(new Event('saldo:change'));
    }
    input.addEventListener('input', fit);
    fit();
  });

  /* ---------- Submit is disabled until the form has an amount ---------- */
  document.querySelectorAll('form[data-requires-amount]').forEach(function (form) {
    var amount = form.querySelector('[data-amount-input]');
    var submit = form.querySelector('[type=submit][data-primary]');
    function sync() {
      if (submit && amount) submit.disabled = !(parseInt(amount.value, 10) > 0);
    }
    form.addEventListener('saldo:change', sync);
    sync();
  });

  /* ---------- Date chips ---------- */
  document.querySelectorAll('[data-date-pick]').forEach(function (box) {
    var input = box.querySelector('input[type=date]');
    var chips = box.querySelectorAll('[data-date]');
    var other = box.querySelector('[data-date-other]');
    var otherLabel = other ? other.querySelector('span') : null;

    function format(value) {
      var d = new Date(value + 'T00:00:00');
      return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', '');
    }
    function sync() {
      var matched = false;
      chips.forEach(function (chip) {
        var on = chip.dataset.date === input.value;
        chip.classList.toggle('is-active', on);
        matched = matched || on;
      });
      if (other) {
        other.classList.toggle('is-active', !matched);
        otherLabel.textContent = matched ? 'Другая дата' : format(input.value);
      }
    }
    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        input.value = chip.dataset.date;
        sync();
      });
    });
    input.addEventListener('change', sync);
    if (other && input.showPicker) {
      other.addEventListener('click', function (e) {
        e.preventDefault();
        try { input.showPicker(); } catch (err) { input.focus(); }
      });
    }
    sync();
  });

  /* ---------- Template chips prefill name + category ---------- */
  document.querySelectorAll('[data-template]').forEach(function (chip) {
    chip.addEventListener('click', function () {
      var form = chip.closest('form');
      var name = form.querySelector('[data-field=name]');
      var category = form.querySelector('[data-field=category]');
      var template = form.querySelector('[data-field=template]');
      if (name) name.value = chip.dataset.name;
      if (category) category.value = chip.dataset.category;
      if (template) template.value = chip.dataset.template;
      form.querySelectorAll('[data-template]').forEach(function (c) {
        c.classList.toggle('is-active', c === chip);
      });
      var amount = form.querySelector('[data-amount-input]');
      if (amount && !amount.value) amount.focus();
    });
  });

  /* ---------- Transfer: swap accounts ---------- */
  document.querySelectorAll('[data-swap]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      var from = form.querySelector('[data-field=from]');
      var to = form.querySelector('[data-field=to]');
      var tmp = from.value;
      from.value = to.value;
      to.value = tmp;
      from.dispatchEvent(new Event('change'));
      to.dispatchEvent(new Event('change'));
    });
  });

  /* ---------- Show remaining balance under account selects ---------- */
  document.querySelectorAll('select[data-balance-hint]').forEach(function (select) {
    var hint = document.getElementById(select.dataset.balanceHint);
    function sync() {
      var opt = select.options[select.selectedIndex];
      hint.textContent = opt && opt.dataset.balance ? 'на счёте ' + opt.dataset.balance : '';
    }
    select.addEventListener('change', sync);
    sync();
  });

  /* ---------- Password visibility ---------- */
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.togglePassword);
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });

  /* ---------- Service worker (static shell cache only) ---------- */
  if ('serviceWorker' in navigator && location.protocol !== 'file:') {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    });
  }
})();
