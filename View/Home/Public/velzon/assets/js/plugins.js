(function () {

  function loadScript(src) {
    if (document.querySelector(`script[src="${src}"]`)) return;

    const s = document.createElement("script");
    s.src = src;
    s.defer = true;
    document.head.appendChild(s);
  }

  const needsToast = document.querySelectorAll("[toast-list]").length > 0;
  const needsChoices = document.querySelectorAll("[data-choices]").length > 0;
  const needsDate = document.querySelectorAll("[data-provider]").length > 0;

  if (needsToast) {
    loadScript("https://cdn.jsdelivr.net/npm/toastify-js");
  }

  if (needsChoices) {
    loadScript("<?php echo URL ?>View/Home/Public/velzon/assets/libs/choices.js/public/assets/scripts/choices.min.js");
  }

  if (needsDate) {
    loadScript("<?php echo URL ?>View/Home/Public/velzon/assets/libs/flatpickr/flatpickr.min.js");
  }

})();
