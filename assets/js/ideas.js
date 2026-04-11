document.addEventListener('DOMContentLoaded', () => {

  const categories = window.IDEA_CATEGORIES || [];
  const input = document.getElementById('categorySearch');
  const results = document.getElementById('categoryResults');
  const form = document.getElementById('ideaFilterForm');

  if (!input || !results) return;

  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    results.innerHTML = '';

    if (!q) {
      results.classList.add('d-none');
      return;
    }

    const matches = categories.filter(c =>
      c.name.toLowerCase().includes(q)
    );

    if (matches.length === 0) {
      results.classList.add('d-none');
      return;
    }

    matches.slice(0, 6).forEach(cat => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'list-group-item list-group-item-action';
      item.innerHTML = `
        <span style="color:${cat.color}">
          <i class="fa-solid ${cat.icon} me-2"></i>
          ${cat.name}
        </span>
      `;

      item.onclick = () => {
        // Prevent duplicates
        if ([...form.querySelectorAll('input[name="categories[]"]')]
            .some(el => el.value == cat.id)) {
          input.value = '';
          results.classList.add('d-none');
          return;
        }

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'categories[]';
        hidden.value = cat.id;

        form.appendChild(hidden);
        form.submit();
      };

      results.appendChild(item);
    });

    results.classList.remove('d-none');
  });

  document.addEventListener('click', (e) => {
    if (!results.contains(e.target) && e.target !== input) {
      results.classList.add('d-none');
    }
  });

});