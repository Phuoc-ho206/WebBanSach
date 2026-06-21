document.addEventListener('DOMContentLoaded', function () {
  const currentPage = window.location.pathname.split('/').pop();
  const menuLinks = document.querySelectorAll('.sidebar a');

  menuLinks.forEach(function (link) {
    const linkPage = link.getAttribute('href');

    if (linkPage === currentPage) {
      link.classList.add('active');
    }
  });

  const dangerButtons = document.querySelectorAll('.btn.danger');

  dangerButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      const isConfirmed = confirm('Bạn có chắc chắn muốn thực hiện thao tác này không?');

      if (!isConfirmed) {
        event.preventDefault();
      }
    });
  });
});