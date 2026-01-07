<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accedi - GymBruh</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Stile specifico per la pagina di login */
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .auth-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff6b00;
        }

        .auth-container input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .auth-container button {
            width: 100%;
            padding: 10px;
            background: #ff6b00;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        .auth-container button:hover {
            background: #e55a00;
        }

        .auth-container p {
            text-align: center;
            margin-top: 15px;
        }

        .auth-container a {
            color: #ff6b00;
            font-weight: bold;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
            display: none;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
            display: none;
        }
    </style>
</head>
<body>

<div class="auth-container">

    <h2>Accedi 🔐</h2>

    <!-- Messaggi di errore / successo -->
    <div id="error-msg" class="error"></div>
    <div id="success-msg" class="success">Registrazione ok! Ora accedi.</div>

    <form id="login-form" onsubmit="return false;">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">ACCEDI</button>
    </form>

    <p>Non sei iscritto? <a href="register_frontend.html">Registrati</a></p>
    <p><a href="../index.php">Torna alla Home</a></p>

</div>

<script>
    // Simulazione frontend-only
    const form = document.getElementById('login-form');
    form.addEventListener('submit', function() {
        const username = form.username.value.trim();
        const password = form.password.value.trim();
        const errorDiv = document.getElementById('error-msg');
        const successDiv = document.getElementById('success-msg');

        if (!username || !password) {
            errorDiv.textContent = "Compila tutti i campi!";
            errorDiv.style.display = "block";
            successDiv.style.display = "none";
        } else {
            errorDiv.style.display = "none";
            successDiv.style.display = "none";
            alert("Login simulato per: " + username);
            form.reset();
        }
    });
</script>

</body>
</html>
