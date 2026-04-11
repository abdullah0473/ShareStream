$(document).ready(function () {

  /* =========================
     COMMON HELPERS
  ========================= */
  function showError(el, msg) {
    el.next('.error-text').remove();
    el.after('<div class="error-text">' + msg + '</div>');
  }

  function clearError(el) {
    el.next('.error-text').remove();
  }

  /* =========================
     REGISTER FORM  
  ========================= */
  if ($('#registerForm').length) {

    $('#registerForm').attr('novalidate', 'novalidate');

    $('#registerForm input').on('input', function () {
      clearError($(this));
    });

    $('#registerForm').on('submit', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();

      let valid = true;

      const name     = $('#full_name');
      const email    = $('#email');
      const phone    = $('#phone');
      const city     = $('#city');
      const password = $('#password');
      const confirm  = $('#confirm_password');

      if (name.val().trim().length < 3) {
        showError(name, 'Full name must be at least 3 characters');
        valid = false;
      }

      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(email.val().trim())) {
        showError(email, 'Please enter a valid email address');
        valid = false;
      }

      if (phone.val().trim() !== '') {
        const phoneRe = /^[0-9+\s]{8,15}$/;
        if (!phoneRe.test(phone.val().trim())) {
          showError(phone, 'Please enter a valid phone number');
          valid = false;
        }
      }

      if (city.val().trim() !== '' && city.val().trim().length < 2) {
        showError(city, 'City name is too short');
        valid = false;
      }

      if (password.val().length < 6) {
        showError(password, 'Password must be at least 6 characters');
        valid = false;
      }

      if (confirm.val().trim() === '') {
        showError(confirm, 'Please confirm your password');
        valid = false;
      } else if (password.val() !== confirm.val()) {
        showError(confirm, 'Passwords do not match');
        valid = false;
      }

      if (!valid) return false;

      e.currentTarget.submit();
    });
  }

  /* =========================
     CREATE IDEA FORM
  ========================= */
  if ($('#createIdeaForm').length) {

    $('#createIdeaForm').attr('novalidate', 'novalidate');

    $('#createIdeaForm input, #createIdeaForm textarea, #createIdeaForm select')
      .on('input change', function () {
        clearError($(this));
      });

    $('#createIdeaForm').on('submit', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();

      let valid = true;

      const category    = $('#category_id');
      const title       = $('#title');
      const summary     = $('#summary');
      const description = $('#description');
      const image       = $('#cover_image');

      if (category.val() === '') {
        showError(category, 'Please select a category');
        valid = false;
      }

      if (title.val().trim().length < 5) {
        showError(title, 'Title must be at least 5 characters');
        valid = false;
      }

      if (summary.val().trim().length < 10) {
        showError(summary, 'Summary must be at least 10 characters');
        valid = false;
      }

      if (description.val().trim().length < 20) {
        showError(description, 'Description must be at least 20 characters');
        valid = false;
      }

      if (image.val() !== '') {
        const allowed = ['jpg', 'jpeg', 'png', 'webp'];
        const ext = image.val().split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
          showError(image, 'Allowed image types: jpg, png, webp');
          valid = false;
        }
      }

      if (!valid) return false;

      e.currentTarget.submit();
    });
  }

});
