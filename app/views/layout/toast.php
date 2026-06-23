<?php

if (empty($notification)) {
    return;
}

$isApproved = $notification['decision'] === 'approved';

$message = $isApproved
    ? 'Pengajuan kredit disetujui'
    : 'Pengajuan kredit ditolak';

$statusLabel = $isApproved ? 'Approved' : 'Rejected';
$statusClass = $isApproved ? 'approved' : 'rejected';
$leasingName = htmlspecialchars($notification['leasing_name'] ?? 'Leasing', ENT_QUOTES, 'UTF-8');

?>

<style>
body {
    background:
        linear-gradient(rgba(245, 246, 248, .78), rgba(245, 246, 248, .78)),
        radial-gradient(circle at top left, #d7dde5 0, transparent 32%),
        #eef1f4;
}

#toast {
    position: fixed;
    top: 34px;
    right: 42px;
    width: min(340px, calc(100vw - 32px));
    padding: 18px;
    background: #ffffff;
    border-radius: 28px;
    box-shadow: 0 28px 70px rgba(15, 23, 42, .2);
    z-index: 9999;
    font-family: Arial, sans-serif;
    color: #151515;
    transform: none;
    transform-origin: top right;
    transition: transform .22s ease, opacity .22s ease;
    touch-action: pan-y;
    user-select: none;
}

#toast.is-closing {
    opacity: 0;
    transform: translateX(120%);
}

.toast-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.toast-heading {
    font-size: 15px;
    font-weight: 700;
}

.toast-tabs {
    display: flex;
    gap: 8px;
    align-items: center;
}

.toast-pill {
    padding: 7px 12px;
    border: 0;
    border-radius: 999px;
    background: #f4f4f4;
    color: #111111;
    font-size: 11px;
    font-weight: 700;
}

#closeToast {
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 50%;
    background: #f4f4f4;
    color: #111111;
    font-size: 17px;
    line-height: 1;
    cursor: pointer;
}

.toast-list {
    display: grid;
    gap: 10px;
}

.toast-item {
    position: relative;
    display: grid;
    grid-template-columns: 42px 1fr;
    gap: 10px;
    min-height: 72px;
    padding: 12px 26px 12px 10px;
    background: #f7f7f7;
    border-radius: 18px;
}

.toast-item::after {
    content: "";
    position: absolute;
    top: 50%;
    right: 12px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ff3b1f;
    transform: translateY(-50%);
}

.toast-icon {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #ffffff;
}

.toast-icon-mark {
    position: relative;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #000000;
}

.toast-icon-mark.approved::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 7px;
    width: 10px;
    height: 6px;
    border-left: 3px solid #ffffff;
    border-bottom: 3px solid #ffffff;
    transform: rotate(-45deg);
}

.toast-icon-mark.rejected::before,
.toast-icon-mark.rejected::after {
    content: "";
    position: absolute;
    left: 8px;
    top: 13px;
    width: 13px;
    height: 3px;
    border-radius: 999px;
    background: #ffffff;
}

.toast-icon-mark.rejected::before {
    transform: rotate(45deg);
}

.toast-icon-mark.rejected::after {
    transform: rotate(-45deg);
}

.toast-content {
    min-width: 0;
}

.toast-title-row {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-bottom: 4px;
}

.toast-title {
    overflow: hidden;
    font-size: 13px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.toast-time {
    color: #b5b5b5;
    font-size: 10px;
    white-space: nowrap;
}

.toast-subtitle,
.toast-amount {
    overflow: hidden;
    color: #555555;
    font-size: 12px;
    line-height: 1.4;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 640px) {
    #toast {
        top: 18px;
        right: 16px;
        transform: none;
    }
}
</style>

<div id="toast" role="status" aria-live="polite">
    <div class="toast-header">
        <div class="toast-heading">Notifications</div>

        <div class="toast-tabs">
            <div class="toast-pill">All</div>
            <button id="closeToast" type="button" aria-label="Tutup notifikasi">x</button>
        </div>
    </div>

    <div class="toast-list">
        <div class="toast-item">
            <div class="toast-icon" aria-hidden="true">
                <div class="toast-icon-mark <?= $statusClass ?>"></div>
            </div>

            <div class="toast-content">
                <div class="toast-title-row">
                    <div class="toast-title"><?= $statusLabel ?></div>
                    <div class="toast-time">now</div>
                </div>

                <div class="toast-subtitle"><?= $message ?></div>
                <div class="toast-amount">Leasing: <?= $leasingName ?></div>
            </div>
        </div>

    </div>
</div>

<script>
const toast = document.getElementById('toast');
const closeToast = document.getElementById('closeToast');

function closeNotification() {
    if (!toast) {
        return;
    }

    toast.classList.add('is-closing');
    setTimeout(function() {
        toast.remove();
    }, 220);
}

if (closeToast) {
    closeToast.addEventListener('pointerdown', function(event) {
        event.stopPropagation();
    });

    closeToast.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        closeNotification();
    });
}

if (toast) {
    let startX = 0;
    let currentX = 0;
    let dragging = false;

    toast.addEventListener('pointerdown', function(event) {
        if (event.target.closest('#closeToast')) {
            return;
        }

        dragging = true;
        startX = event.clientX;
        currentX = event.clientX;
        toast.setPointerCapture(event.pointerId);
    });

    toast.addEventListener('pointermove', function(event) {
        if (!dragging) {
            return;
        }

        currentX = event.clientX;
        const diffX = currentX - startX;

        if (Math.abs(diffX) > 8) {
            toast.style.transform = 'translateX(' + diffX + 'px)';
        }
    });

    toast.addEventListener('pointerup', function() {
        if (!dragging) {
            return;
        }

        dragging = false;
        const diffX = currentX - startX;

        if (Math.abs(diffX) > 90) {
            closeNotification();
            return;
        }

        toast.style.transform = '';
    });
}
</script>
