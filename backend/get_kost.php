<?php
// backend/get_kost.php - pengambilan data satu kost (prepared statement)
function get_kost(mysqli $conn, int $id): ?array {
    $st = $conn->prepare("SELECT * FROM kost WHERE id = ? AND status = 'aktif'");
    $st->bind_param('i', $id);
    $st->execute();
    return $st->get_result()->fetch_assoc() ?: null;
}
function get_fasilitas_kost(mysqli $conn, int $id): array {
    $st = $conn->prepare("SELECT f.nama, f.ikon FROM kost_fasilitas kf JOIN fasilitas f ON f.id = kf.fasilitas_id WHERE kf.kost_id = ? ORDER BY f.nama");
    $st->bind_param('i', $id);
    $st->execute();
    return $st->get_result()->fetch_all(MYSQLI_ASSOC);
}
// Nilai tiap kriteria + label jawaban Google Form-nya
function get_penilaian_kost(mysqli $conn, int $id): array {
    $st = $conn->prepare("SELECT k.kelompok, k.nama, o.label FROM kriteria k
        LEFT JOIN nilai_kost n ON n.kriteria_id = k.id AND n.kost_id = ?
        LEFT JOIN opsi_nilai o ON o.kriteria_id = k.id AND o.skor = n.nilai
        WHERE k.kolom_kost IS NULL ORDER BY k.id");
    $st->bind_param('i', $id);
    $st->execute();
    $out = [];
    foreach ($st->get_result() as $r) $out[$r['kelompok']][] = $r;
    return $out;
}
