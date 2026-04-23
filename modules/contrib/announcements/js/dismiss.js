(function (Drupal, once, cookies) {
  Drupal.behaviors.annoucementsDismiss = {
    attach: function (context, settings) {
      once('annoucementsDismiss', '.announcements-announcement.announcement-dismissible', context).forEach(function (announcement) {
        announcement_id = announcement.dataset.announcementId;
        announcement.querySelectorAll('.close').forEach((function (announcement, announcement_id) {return function (button) {
          button.addEventListener('click', function (event) {
            announcement.classList.toggle('announcement-dismissed');
            cookies.set('announcement-' + announcement_id + '-dismissed', announcement_id);
          });
        };})(announcement, announcement_id));

        if (cookies.get('announcement-' + announcement_id + '-dismissed')) {
          announcement.classList.toggle('announcement-dismissed');
        }
      });
    }
  };
})(Drupal, once, window.Cookies);
