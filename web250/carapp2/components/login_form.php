
<?php if (!$isLoggedIn): ?>

    <section>
        <h3>Log In</h3>

        <p>Log in to add, edit, and delete vehicles.</p>

        <form method="post" action="scripts/login.php">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= escapeValue($csrfToken) ?>">

            <label for="username">Username:</label>
            <input id="username"
                   name="username"
                   type="text"
                   autocomplete="username"
                   required>

            <label for="password">Password:</label>
            <input id="password"
                   name="password"
                   type="password"
                   autocomplete="current-password"
                   required>

            <button type="submit" name="login">Log In</button>

        </form>
    </section>

<?php endif; ?>

