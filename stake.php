<?php
session_start();
require 'config.php';
require 'functions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensaje = ''; // Variable para almacenar el mensaje

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id']; // Obtener el ID del usuario autenticado desde la sesión
    $monto = $_POST['amount'];
    $sistema = $_POST['sistema']; // Obtener el sistema desde el formulario
    $estado = 'pendiente';
    $fecha = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO depositos (usuario_id, monto, sistema, estado, fecha) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $monto, $sistema, $estado, $fecha]);

    // Asignar mensaje de depósito pendiente de aprobación
    $mensaje = 'Depósito pendiente de aprobación';
}
?>

<?php include './partials/layouts/layoutTop.php'; ?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-medium mb-0">Stake</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                    </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Stake</li>
        </ul>
    </div>

    <div class="mb-24">
        <h2></h2>
        <h3></h3>

        <!-- Botón para ver el historial de depósitos -->
        <div class="d-flex justify-content-end mb-3">
            <a href="historial.php" class="btn btn-primary btn-sm">Historial de Depósitos y Retiros</a>
        </div>
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="form-container">
                <h6>Depositar con Criptomonedas</h6>
                <?php if ($mensaje): ?>
                    <div class="alert alert-info"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="amount">Monto:</label>
                        <input type="number" id="amount" name="amount" min="0" max="1000000000" required>
                    </div>     
                    <div class="form-group">
                        <label for="sistema">¿Dónde quiere invertir?</label>
                        <select id="sistema" name="sistema" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="validator_node">Validator Node</option>
                            <option value="trading_ia">Trading IA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="crypto">Moneda:</label>
                        <select id="crypto" name="crypto" required>
                            <option value="" selected disabled>Seleccione una moneda</option>
                            <option value="USDT">USDT</option>
                            <option value="USDC">USDC</option>
                        </select>
                    </div>
                    <div class="form-group" id="network-group">
                        <label for="network">Red:</label>
                        <select id="network" name="network" required>
                            <option value="" selected disabled>Seleccione una red</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="wallet">Dirección de la Wallet:</label>
                        <div class="input-group">
                            <input type="text" id="wallet" name="wallet" readonly>
                            <span class="copy-icon" id="copy-wallet" title="Copiar">
                                <iconify-icon icon="mdi:content-copy"></iconify-icon>
                            </span>
                        </div>
                    </div>
                    <div id="copy-confirmation" class="copy-confirmation" style="display: none;">
                        <iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Dirección copiada al portapapeles
                    </div>
                    <div class="form-group" id="qr-group" style="display:none;">
                        <label for="qr">Código QR:</label>
                        <img id="qr" class="qr-image" src="" alt="QR Wallet">
                    </div>
                    <div class="note" id="note" style="display:none;">
                        <strong>Nota:</strong> <span id="note-content"></span>
                    </div>
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <button type="submit" id="submit-button" disabled>Depositar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>

<style>
    .form-container {
        max-width: 700px;
        margin: auto;
        padding: 20px;
        border: 1px solid var(--border-color, #ccc);
        border-radius: 10px;
        background-color:rgb(250, 250, 250);
    }

    .form-container h6 {
        text-align: center;
        font-size: 1.25rem;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group .input-group {
        display: flex;
        align-items: center;
        position: relative;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid var(--input-border-color, #ccc);
        border-radius: 5px;
        background-color: #273142;
        color: var(--input-text-color, #fff);
    }

    .form-group input[readonly] {
        background-color:rgb(243, 244, 245);
    }

    .copy-icon {
        position: absolute;
        right: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 8px;
        background-color: transparent;
        color: var(--button-text-color, #fff);
    }

    .form-group button {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: var(--button-background-color, #007bff);
        color: var(--button-text-color, #fff);
        cursor: pointer;
    }

    .form-group button:disabled {
        background-color: var(--button-disabled-background-color, #6c757d);
        cursor: not-allowed;
    }

    .note {
        background-color: #fdd;
        padding: 10px;
        border: 1px solid #f00;
        border-radius: 5px;
        margin-top: 15px;
        color: #fff;
        font-size: 0.875rem;
        white-space: normal;
        word-wrap: break-word;
    }

    .copy-confirmation {
        display: flex;
        align-items: center;
        margin-top: 10px;
        color: green;
        font-size: 0.9rem;
    }

    .qr-image {
        width: 100px;
        height: 100px;
    }

    @media (prefers-color-scheme: dark) {
        body {
            --background-color:rgb(255, 255, 255);
            --text-color: #ffffff;
            --border-color: #333;
            --input-border-color: #555;
            --button-background-color: #007bff;
            --button-text-color: #ffffff;
            --button-disabled-background-color:rgb(3, 39, 220);
        }
        .form-container {
            background-color: #1B2431;
        }
        .form-group input,
        .form-group select,
        .form-group input[readonly] {
            background-color: #273142;
        }
        .copy-icon {
            color: var(--button-text-color, #fff);
        }
        .note {
            color: #000;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const cryptoSelect = document.getElementById('crypto');
        const networkGroup = document.getElementById('network-group');
        const networkSelect = document.getElementById('network');
        const walletInput = document.getElementById('wallet');
        const qrGroup = document.getElementById('qr-group');
        const qrImage = document.getElementById('qr');
        const submitButton = document.getElementById('submit-button');
        const copyIcon = document.getElementById('copy-wallet');
        const note = document.getElementById('note');
        const noteContent = document.getElementById('note-content');
        const copyConfirmation = document.getElementById('copy-confirmation');

        const walletAddresses = {
            USDT: {
                TRC20: "TRTjQjApsqsALfpwCMp1683jQ3Bja4oC1C"
            },
            USDC: {
                "Base Mainnet": "0x540fab6011a089a9c2eea379d69aebf5910773c5"
            }
        };

        const notes = {
            USDT: {
                TRC20: "Solo puedes añadir USDT a esta dirección de la red Tron (TRC20). Otros métodos pueden hacer que no se abone."
            },
            USDC: {
                "Base Mainnet": "Solo puedes añadir USDC a esta dirección de la red Base Mainnet. Otros métodos pueden hacer que no se abone."
            }
        };

        const qrImages = {
            USDT: {
                TRC20: "assets/images/qrusdt.png"
            },
            USDC: {
                "Base Mainnet": "assets/images/qrusdc.png"
            }
        };

        amountInput.addEventListener('input', function() {
            if (amountInput.value !== '') {
                cryptoSelect.disabled = false;
            } else {
                cryptoSelect.disabled = true;
                cryptoSelect.value = '';
                networkSelect.disabled = true;
                networkSelect.value = '';
                walletInput.value = '';
                qrImage.src = '';
                submitButton.disabled = true;
                copyIcon.style.display = 'none';
                copyConfirmation.style.display = 'none';
                note.style.display = 'none';
                qrGroup.style.display = 'none';
                noteContent.textContent = '';
            }
        });

        cryptoSelect.addEventListener('change', function() {
            networkSelect.disabled = true;
            networkSelect.innerHTML = '<option value="" selected disabled>Seleccione una red</option>';
            walletInput.value = '';
            qrImage.src = '';
            submitButton.disabled = true;
            copyIcon.style.display = 'none';
            copyConfirmation.style.display = 'none';
            note.style.display = 'none';
            qrGroup.style.display = 'none';
            noteContent.textContent = '';

            if (cryptoSelect.value === 'USDT') {
                networkSelect.innerHTML += '<option value="TRC20">TRC20</option>';
            } else if (cryptoSelect.value === 'USDC') {
                networkSelect.innerHTML += '<option value="Base Mainnet">Base Mainnet</option>';
            }
            networkSelect.disabled = false;
            networkGroup.style.display = 'block';
        });

        networkSelect.addEventListener('change', function() {
            if (networkSelect.value !== '') {
                const selectedCrypto = cryptoSelect.value;
                const selectedNetwork = networkSelect.value;
                const walletAddress = walletAddresses[selectedCrypto][selectedNetwork];
                const qrSrc = qrImages[selectedCrypto][selectedNetwork];
                walletInput.value = walletAddress;
                qrImage.src = qrSrc;
                submitButton.disabled = false;
                copyIcon.style.display = 'inline';
                note.style.display = 'block';
                qrGroup.style.display = 'block';
                noteContent.textContent = notes[selectedCrypto][selectedNetwork];
            } else {
                walletInput.value = '';
                qrImage.src = '';
                submitButton.disabled = true;
                copyIcon.style.display = 'none';
                copyConfirmation.style.display = 'none';
                note.style.display = 'none';
                qrGroup.style.display = 'none';
                noteContent.textContent = '';
            }
        });

        copyIcon.addEventListener('click', function() {
            const walletInput = document.getElementById('wallet');
            walletInput.select();
            document.execCommand('copy');
            copyConfirmation.style.display = 'flex';
            setTimeout(() => {
                copyConfirmation.style.display = 'none';
            }, 2000);
        });
    });
</script>