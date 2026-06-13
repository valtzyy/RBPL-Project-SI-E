<main style="max-width: 420px; margin: 64px auto; font-family: Arial, sans-serif;">
    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <div style="padding: 10px; margin-bottom: 16px; background: #fee2e2; color: #991b1b;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div style="margin-bottom: 12px;">
            <label for="identity">Username atau Email</label><br>
            <input
                type="text"
                id="identity"
                name="identity"
                required
                autocomplete="username"
                style="width: 100%; padding: 8px;"
            >
        </div>

        <div style="margin-bottom: 16px;">
            <label for="password">Password</label><br>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                style="width: 100%; padding: 8px;"
            >
        </div>

        <button type="submit" style="padding: 10px 16px;">Masuk</button>
    </form>
</main>
