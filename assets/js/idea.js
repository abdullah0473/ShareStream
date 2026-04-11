document.addEventListener('DOMContentLoaded', () => {

  /* =====================
     VOTE
  ===================== */
  const voteBtn = document.getElementById('voteBtn');
  const voteCount = document.getElementById('voteCount');

  if (voteBtn && IS_LOGGED_IN) {
    voteBtn.onclick = () => {
      fetch('../ajax/vote.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({idea_id: IDEA_ID})
      })
      .then(res => res.json())
      .then(d => {
        if (d.success) voteCount.textContent = d.votes;
      });
    };
  }

  /* =====================
     COMMENT
  ===================== */
  const commentForm = document.getElementById('commentForm');
  if (commentForm) {
    commentForm.onsubmit = e => {
      e.preventDefault();
      const body = commentForm.body.value.trim();
      if (!body) return;

      fetch('../ajax/comment.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({idea_id: IDEA_ID, body})
      })
      .then(res => res.text())
      .then(html => {
        document.getElementById('comments').insertAdjacentHTML('afterbegin', html);
        commentForm.reset();
      });
    };
  }

  /* =====================
     BOOKMARK
  ===================== */
  const bookmarkBtn = document.getElementById('bookmarkBtn');
  if (bookmarkBtn) {
    bookmarkBtn.onclick = () => {
      fetch('../ajax/bookmark.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({idea_id: IDEA_ID})
      })
      .then(res => res.json())
      .then(d => bookmarkBtn.textContent = d.bookmarked ? '★ Bookmarked' : '☆ Bookmark');
    };
  }

  /* =====================
     REPORT
  ===================== */
  const reportForm = document.getElementById('reportForm');
  if (reportForm) {
    reportForm.onsubmit = e => {
      e.preventDefault();
      fetch('../ajax/report.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({idea_id: IDEA_ID, reason: reportForm.reason.value})
      })
      .then(() => location.reload());
    };
  }

});