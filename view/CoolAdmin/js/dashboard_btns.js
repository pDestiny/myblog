(function() {
    const $dashboardEditBtn = $('.dashboard-edit-btn');
    const $dashboardDeleteBtn = $('.dashboard-delete-btn');

    $dashboardEditBtn.on('click', function() {
        const dashboardId = $(this).data("dashboardId");

        location.href = '/controller/admin/editArticlePage.php?dashboard_id=' + dashboardId;
    });

    $dashboardDeleteBtn.on('click', function() {

      $('.delete-btn').data('id', $(this).data('dashboard-id')).data('type', "dashboard");

    });
})();