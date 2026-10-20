
<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM materi WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: materi_index.php");
        exit;
    }

    echo "Materi gagal dihapus.";
}
?>
