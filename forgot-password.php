<?php
include './partials/head.php'; // Incluye la cabecera HTML
require 'config.php'; // Archivo de configuración de la base de datos
require 'functions.php'; // Archivo con funciones auxiliares

$mensaje = 'h';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verificar si el correo electrónico existe en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $stmt->execute([':correo' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Generar un token único
        $token = bin2hex(random_bytes(50));

        // Guardar el token en la base de datos con una fecha de expiración
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())");
        $stmt->execute([':email' => $email, ':token' => $token]);

        // Enviar el correo electrónico con el enlace de restablecimiento
        $resetLink = "http://yourwebsite.com/reset-password.php?token=$token";

        $to = $email;
        $subject = "Password Reset Request";
        $message = "Click on the following link to reset your password: $resetLink";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($to, $subject, $message, $headers)) {
            $mensaje = "<div class='alert alert-success'>Check your email for the reset link.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Failed to send email.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Email address not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<?php include './partials/head.php' ?>

<body>
    <section class="auth forgot-password-page bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="assets/images/auth/forgot-pass-img.png" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <h4 class="mb-12">Forgot Password</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter the email address associated with your account and we will send you a link to reset your password.</p>
                </div>
                <?php echo $mensaje; ?>
                <form action="forgot-password.php" method="POST">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter Email" required>
                    </div>
                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Continue</button>

                    <div class="text-center">
                        <a href="sign-in.php" class="text-primary-600 fw-bold mt-24">Back to Sign In</a>
                    </div>

                    <div class="mt-120 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="sign-in.php" class="text-primary-600 fw-semibold">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-body p-40 text-center">
                    <div class="mb-32">
                        <img src="assets/images/auth/envelop-icon.png" alt="">
                    </div>
                    <h6 class="mb-12">Verify your Email</h6>
                    <p class="text-secondary-light text-sm mb-0">Thank you, check your email for instructions to reset your password</p>
                    <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32" data-bs-dismiss="modal">Skip</button>
                    <div class="mt-32 text-sm">
                        <p class="mb-0">Didn’t receive an email? <a href="resend.php" class="text-primary-600 fw-semibold">Resend</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include './partials/scripts.php' ?>

</body>
</html>