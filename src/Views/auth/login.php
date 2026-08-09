<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BRI School Management System - Login</title>
    <meta name="description" content="BRI International School Management System">
    <link rel="icon" href="/images/fevicon.png" type="image/png" />
    <link rel="stylesheet" href="/css/font-awesome.min.css" />
    <style>
        :root {
            --brand: #2f8a5b;
            --brand-dark: #1f6b45;
            --ink: #24303a;
            --muted: #6b7688;
            --line: #e4e8ec;
        }
        * { box-sizing: border-box; }
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 880px;
            min-height: 520px;
            display: flex;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(20, 40, 30, 0.15);
        }
        .auth-brand {
            flex: 1 1 45%;
            background: linear-gradient(160deg, var(--brand), var(--brand-dark));
            color: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .auth-brand .mark {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
        }
        .auth-brand h1 {
            font-size: 26px;
            line-height: 1.3;
            margin: 28px 0 8px;
        }
        .auth-brand p {
            font-size: 14px;
            opacity: 0.85;
            margin: 0;
            max-width: 320px;
            line-height: 1.6;
        }
        .auth-brand .roles {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .auth-brand .roles span {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
        }
        .auth-form {
            flex: 1 1 55%;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .auth-form h2 {
            margin: 0 0 6px;
            color: var(--ink);
            font-size: 22px;
        }
        .auth-form .subtitle {
            margin: 0 0 28px;
            color: var(--muted);
            font-size: 14px;
        }
        .alert-error {
            background: #fdeeee;
            color: #b13a3a;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .field-group {
            margin-bottom: 20px;
        }
        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .field-wrap {
            position: relative;
        }
        .field-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
        }
        .field-wrap input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 14px;
            color: var(--ink);
            background: #fafbfc;
            transition: border-color 0.15s, background 0.15s;
        }
        .field-wrap input:focus {
            outline: none;
            border-color: var(--brand);
            background: #fff;
        }
        .btn-signin {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.15s;
        }
        .btn-signin:hover {
            background: var(--brand-dark);
        }
        @media (max-width: 720px) {
            .auth-card { flex-direction: column; min-height: 0; }
            .auth-brand { padding: 32px 28px; }
            .auth-form { padding: 32px 28px; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">BRI</div>
            <div>
                <h1>BRI International School</h1>
                <p>Manage students, teachers, attendance, exams and fees in one place.</p>
            </div>
            <div class="roles">
                <span>Admin</span>
                <span>Teacher</span>
                <span>Bursar</span>
            </div>
        </div>
        <div class="auth-form">
            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to your management dashboard.</p>

            <?php if (!empty($error_message)): ?>
                <div class="alert-error">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="field-group">
                    <label for="username">Username</label>
                    <div class="field-wrap">
                        <i class="fa fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="username" autofocus>
                    </div>
                </div>
                <div class="field-group">
                    <label for="password">Password</label>
                    <div class="field-wrap">
                        <i class="fa fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn-signin">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
