<!DOCTYPE html>
<html>
<head>
    <title>Detail Dokumen</title>
</head>
<body>
    <h3>Preview Dokumen</h3>
    <img src="<?= htmlspecialchars($linkAman) ?>" alt="Dokumen Rahasia">
    <p>Public ID: <?= htmlspecialchars($publicId) ?></p>
    <p>Link sementara: <a href="<?= htmlspecialchars($linkAman) ?>" target="_blank"><?= htmlspecialchars($linkAman) ?></a></p>
    
</body>
</html>
