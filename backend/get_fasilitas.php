<?php
// backend/get_fasilitas.php
function get_semua_fasilitas(mysqli $conn): array {
    return $conn->query("SELECT id, nama, ikon FROM fasilitas ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
}
