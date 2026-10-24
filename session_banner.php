<?php
// Shown on every protected page.
?>

<div class="session-user-banner" aria-label="Signed-in user">
    <?php if (!empty($_SESSION['user_image']) && is_file(__DIR__ . '/' . $_SESSION['user_image'])): ?>
        <img src="<?= htmlspecialchars($_SESSION['user_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile image" style="width:38px;height:38px;object-fit:cover;border-radius:50%;margin-right:10px;vertical-align:middle;">
    <?php else: ?>
        <span style="display:inline-grid;place-items:center;width:38px;height:38px;border-radius:50%;background:#eef5ff;color:#2563eb;margin-right:10px;"><i class="fa-solid fa-user"></i></span>
    <?php endif; ?>
    <span>
        Signed in as
        <strong><?= htmlspecialchars($session_display_name, ENT_QUOTES, 'UTF-8') ?></strong>
        (<?= htmlspecialchars(ucfirst($session_role), ENT_QUOTES, 'UTF-8') ?>)
    </span>
    <a href="logout.php">Log out</a>
</div>

<script>
(function () {
    const role = <?= json_encode($session_role) ?>;

    const permissions = {
        admin: ['*'],

        registrar: [
            'subjects.php',
            'subjects_edit.php',
            'term.php',
            'term_edit.php',
            'instructor.php',
            'schoolyear.php',
            'schoolyear_edit.php'
        ],

        dean: [
            'subjects.php',
            'subjects_edit.php',
            'section.php',
            'section_edit.php',
            'instructor.php',
            'yearlevel.php',
            'yearlevel_edit.php'
        ],

        instructor: [
            'subjects.php',
            'subjects_edit.php',
            'section.php',
            'section_edit.php'
        ],

        student: [
            'subjects.php'
        ]
    };

    const allowed = permissions[role] || [];

    function pageAllowed(page) {
        if (role === 'admin') {
            return true;
        }

        return allowed.indexOf(page.toLowerCase()) !== -1;
    }

    function cleanPage(href) {
        if (
            !href ||
            href.indexOf('://') !== -1 ||
            href.indexOf('#') === 0
        ) {
            return '';
        }

        return href
            .split('/')
            .pop()
            .split('?')[0]
            .toLowerCase();
    }

    function applyRoleMenu() {

        // Hide menu items the current user is not allowed to access
        document.querySelectorAll('.menu a').forEach(function (link) {

            const page = cleanPage(link.getAttribute('href'));

            if (page && !pageAllowed(page)) {
                link.style.display = 'none';
            }
        });

        // Add Instructor menu item for authorized roles
        if (
            role !== 'admin' &&
            ['registrar', 'dean', 'instructor'].indexOf(role) !== -1
        ) {

            const menu = document.querySelector('.menu');

            if (
                menu &&
                !menu.querySelector('[data-instructor-menu]')
            ) {

                const a = document.createElement('a');

                a.href = 'instructor.php';
                a.setAttribute('data-instructor-menu', '1');

                a.innerHTML =
                    '<i class="fa-solid fa-chalkboard-user"></i> Instructor';

                menu.appendChild(a);
            }
        }
    }

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            applyRoleMenu
        );

    } else {

        applyRoleMenu();

    }

})();
</script>