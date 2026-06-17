<?php foreach ($customers as $customer): ?>
    <p><?= $customer['name'] ?> - <?= $customer['ktp_number'] ?></p>
<?php endforeach; ?>