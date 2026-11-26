document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(open));
    });
    nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
      nav.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    }));
  }

  document.querySelectorAll('.flash').forEach(message => {
    window.setTimeout(() => {
      message.classList.add('flash-hide');
      window.setTimeout(() => message.remove(), 450);
    }, 4200);
  });

  if (window.JCLOTHES_ALERT) {
    window.setTimeout(() => {
      if (window.Swal) {
        const alertOptions = {
          title: window.JCLOTHES_ALERT.title || 'J Clothes',
          text: window.JCLOTHES_ALERT.message,
          icon: window.JCLOTHES_ALERT.type === 'error' ? 'error' : 'success',
          confirmButtonColor: '#19372c'
        };
        if (window.JCLOTHES_ALERT.undoProductId) {
          alertOptions.showDenyButton = true;
          alertOptions.denyButtonText = 'Undo';
          alertOptions.denyButtonColor = '#a66e4d';
        }
        Swal.fire(alertOptions).then(result => {
          if (result.isDenied) {
            const form = document.querySelector('.undo-flash-form');
            if (form) form.submit();
          }
        });
      }
    }, 100);
  }
});
