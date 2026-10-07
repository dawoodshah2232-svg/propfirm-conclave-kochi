// PropFirm Conclave Admin — shared behaviours
(function () {
  'use strict';

  // Confirm destructive actions
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm') || 'Are you sure?')) e.preventDefault();
    });
  });

  // Drag-to-reorder lists: [data-sortable] container of [data-id] items.
  // Posts the new order (array of ids) to the form's data-save-url.
  document.querySelectorAll('[data-sortable]').forEach(function (list) {
    var saveUrl = list.getAttribute('data-save-url');
    var dragEl = null;

    function items() { return Array.prototype.slice.call(list.querySelectorAll('[data-id]')); }

    items().forEach(function (it) {
      it.setAttribute('draggable', 'true');
      it.addEventListener('dragstart', function () { dragEl = it; it.classList.add('dragging'); });
      it.addEventListener('dragend', function () {
        it.classList.remove('dragging'); dragEl = null; persist();
      });
      it.addEventListener('dragover', function (e) {
        e.preventDefault();
        if (!dragEl || dragEl === it) return;
        var r = it.getBoundingClientRect();
        var after = (e.clientY - r.top) > r.height / 2;
        list.insertBefore(dragEl, after ? it.nextSibling : it);
      });
    });

    var note = null;
    function persist() {
      if (!saveUrl) return;
      var order = items().map(function (it) { return it.getAttribute('data-id'); });
      var fd = new FormData();
      fd.append('order', JSON.stringify(order));
      var csrf = document.querySelector('input[name="csrf"]');
      if (csrf) fd.append('csrf', csrf.value);
      fetch(saveUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) { toast(j.ok ? 'Order saved.' : 'Save failed.'); })
        .catch(function () { toast('Save failed.'); });
    }

    function toast(msg) {
      if (note) note.remove();
      note = document.createElement('div');
      note.className = 'alert alert-ok';
      note.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99;margin:0';
      note.textContent = msg;
      document.body.appendChild(note);
      setTimeout(function () { if (note) note.remove(); }, 2200);
    }
  });

  // Live image preview on file inputs
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var img = document.getElementById(inp.getAttribute('data-preview'));
      if (img && inp.files[0]) img.src = URL.createObjectURL(inp.files[0]);
    });
  });
})();
