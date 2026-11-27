
<?php if (!$isLoggedIn): ?>

    <section>
        <h3>Log In</h3>

        <p>Log in to add, edit, and delete vehicles.</p>

        <!-- This div places the login form and demo credentials side by side. -->
        <div class="login-layout">

            <div class="login-form">
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
            </div>

            <!-- This div groups the demonstration login credentials beside the form. -->
            <div class="demo-credentials">
                <h4>Demo Login</h4>
                <p><strong>Username:</strong> web250</p>
                <p><strong>Password:</strong> 123!</p>
            </div>

        </div>
    </section>

<?php endif; ?>
