/* Accessible settings tabs, bulk template controls, shortcode copy and live preview. */
document.addEventListener('DOMContentLoaded', function () {
  // Copy any shortcode from Template Management or Setup Documents.
  document.querySelectorAll('[data-copy-shortcode]').forEach(function (button) {
    button.addEventListener('click', function () {
      var value = button.getAttribute('data-copy-shortcode') || '';
      var status = button.parentElement ? button.parentElement.querySelector('.dm-roi-copy-status') : null;
      function show(message) { if (status) status.textContent = message; }
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(value).then(function () { show('Copied!'); }).catch(function () { fallback(); });
      } else { fallback(); }
      function fallback() {
        var field = document.createElement('textarea'); field.value = value; field.setAttribute('readonly', ''); field.style.position = 'absolute'; field.style.left = '-9999px';
        document.body.appendChild(field); field.select();
        var ok = false; try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(field); show(ok ? 'Copied!' : 'Select and copy the shortcode manually.');
      }
    });
  });
  document.querySelectorAll('.dm-roi-settings-wrap').forEach(function (root) {
    var tabs = Array.from(root.querySelectorAll('.dm-roi-tab'));
    var panels = Array.from(root.querySelectorAll('[data-panel-content]'));
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var target = tab.getAttribute('data-panel');
        tabs.forEach(function (item) {
          var active = item === tab;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach(function (panel) {
          var active = panel.getAttribute('data-panel-content') === target;
          panel.classList.toggle('is-active', active);
          panel.hidden = !active;
        });
      });
    });
    root.querySelectorAll('[data-template-bulk]').forEach(function (button) {
      button.addEventListener('click', function () {
        var enabled = button.getAttribute('data-template-bulk') === 'enable';
        root.querySelectorAll('.dm-roi-template-toggle input[type="checkbox"]').forEach(function (input) { input.checked = enabled; });
      });
    });
  });
  (function () {
    var copy = document.getElementById('dm-roi-copy');
    if (!copy) return;
    copy.addEventListener('click', function () {
      var field = document.getElementById('dm-roi-shortcode');
      var status = document.getElementById('dm-roi-copy-status');
      field.select(); field.setSelectionRange(0, 99999);
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(field.value).then(function () { status.textContent = 'Copied!'; }).catch(function () { status.textContent = 'Please copy the selected shortcode.'; });
      } else { status.textContent = document.execCommand('copy') ? 'Copied!' : 'Please copy the selected shortcode.'; }
    });
  }());
  (function () {
    var primary = document.getElementById('dm-primary'), card = document.getElementById('dm-card'), title = document.getElementById('dm-title'), buttonText = document.getElementById('dm-button-text'), radius = document.getElementById('dm-radius'), previewTitle = document.getElementById('dm-roi-preview-title'), previewButton = document.getElementById('dm-roi-preview-button'), preview = document.getElementById('dm-roi-preview');
    if (!primary || !card || !title || !buttonText || !radius || !previewTitle || !previewButton || !preview) return;
    // Update the preview elements directly. This avoids relying on CSSOM access
    // to the enqueued stylesheet, which can fail when assets are combined or restricted.
    function safeColor(value, fallback) {
      return /^#[0-9a-f]{6}$/i.test(value) ? value : fallback;
    }
    function update() {
      var primaryColor = safeColor(primary.value, '#b20d55');
      var cardColor = safeColor(card.value, '#ffffff');
      var buttonRadius = radius.value === 'pill' ? '999px' : (radius.value === 'square' ? '3px' : '8px');
      previewTitle.textContent = title.value || 'Digital Marketing ROI Calculator';
      previewButton.textContent = buttonText.value || 'Calculate My ROI';
      preview.style.backgroundColor = cardColor;
      previewTitle.style.color = primaryColor;
      previewButton.style.backgroundColor = primaryColor;
      previewButton.style.borderColor = primaryColor;
      previewButton.style.borderRadius = buttonRadius;
    }
    [primary, card, title, buttonText, radius].forEach(function (field) { field.addEventListener('input', update); });
    update();
  }());
});
