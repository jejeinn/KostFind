<?php
// backend/proses_saw.php - filter preferensi + perhitungan SAW (dinamis dari database)

function baca_filter(array $in): array {
    $tipe_ok = ['Khusus Perempuan', 'Khusus Laki-Laki', 'Campuran'];
    $f = ['harga_max' => max(0, (int)($in['harga_max'] ?? 0)),
          'jarak_max' => max(0, (int)($in['jarak_max'] ?? 0)),
          'tipe' => in_array($in['tipe'] ?? '', $tipe_ok, true) ? $in['tipe'] : '',
          'min' => [], 'fas' => array_values(array_unique(array_filter(array_map('intval', (array)($in['fas'] ?? [])), fn($v) => $v > 0)))];
    foreach ((array)($in['min'] ?? []) as $kid => $skor) {
        if (ctype_digit((string)$kid) && ctype_digit((string)$skor) && (int)$skor > 0)
            $f['min'][(int)$kid] = (int)$skor;
    }
    return $f;
}

function saw_hitung(mysqli $conn, array $f): array {
    // 1. Kriteria & bobot
    $krit = $conn->query("SELECT id, kode, nama, tipe, bobot, kolom_kost FROM kriteria ORDER BY id")->fetch_all(MYSQLI_ASSOC);
    $total_bobot = array_sum(array_column($krit, 'bobot')) ?: 1;

    // 2. Alternatif = kost aktif yang lolos preferensi user
    $sql = "SELECT k.* FROM kost k WHERE k.status = 'aktif'";
    $types = ''; $p = [];
    if ($f['harga_max'] > 0) { $sql .= " AND k.harga_bulan <= ?"; $types .= 'i'; $p[] = $f['harga_max']; }
    if ($f['jarak_max'] > 0) { $sql .= " AND k.jarak_meter <= ?"; $types .= 'i'; $p[] = $f['jarak_max']; }
    if ($f['tipe'] !== '')   { $sql .= " AND k.tipe_kost = ?";    $types .= 's'; $p[] = $f['tipe']; }
    foreach ($f['min'] as $kid => $skor) {
        $sql .= " AND EXISTS (SELECT 1 FROM nilai_kost n WHERE n.kost_id = k.id AND n.kriteria_id = ? AND n.nilai >= ?)";
        $types .= 'ii'; $p[] = $kid; $p[] = $skor;
    }
    foreach ($f['fas'] as $fid) {
        $sql .= " AND EXISTS (SELECT 1 FROM kost_fasilitas kf WHERE kf.kost_id = k.id AND kf.fasilitas_id = ?)";
        $types .= 'i'; $p[] = $fid;
    }
    $st = $conn->prepare($sql);
    if ($p) $st->bind_param($types, ...$p);
    $st->execute();
    $alt = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    if (!$alt) return ['alternatif' => [], 'kriteria' => $krit];

    // 3. Matriks keputusan X[kost_id][kriteria_id]
    $ids = array_map('intval', array_column($alt, 'id'));
    $X = [];
    $rs = $conn->query("SELECT kost_id, kriteria_id, nilai FROM nilai_kost WHERE kost_id IN (" . implode(',', $ids) . ")");
    foreach ($rs as $r) $X[$r['kost_id']][$r['kriteria_id']] = (float)$r['nilai'];
    foreach ($alt as $a)
        foreach ($krit as $k)
            if ($k['kolom_kost']) $X[$a['id']][$k['id']] = (float)$a[$k['kolom_kost']];

    // 4-5. Normalisasi: Cost = Min/Xij ; Benefit = Xij/Max
    $V = []; $R = [];
    foreach ($alt as $a) $V[$a['id']] = 0.0;
    foreach ($krit as $k) {
        $kol = array_map(fn($a) => $X[$a['id']][$k['id']] ?? 0.0, $alt);
        $max = max($kol);
        $pos = array_filter($kol, fn($v) => $v > 0);
        $min = $pos ? min($pos) : 0;
        foreach ($alt as $a) {
            $x = $X[$a['id']][$k['id']] ?? 0.0;
            if ($k['tipe'] === 'cost') $r = $x > 0 ? $min / $x : 0;
            else                       $r = $max > 0 ? $x / $max : 0;
            $R[$a['id']][$k['id']] = $r;
            // 6. Vi = sum(Wj * Rij)
            $V[$a['id']] += ($k['bobot'] / $total_bobot) * $r;
        }
    }

    // 7. Peringkat
    foreach ($alt as &$a) $a['skor'] = $V[$a['id']];
    unset($a);
    usort($alt, fn($a, $b) => $b['skor'] <=> $a['skor']);
    foreach ($alt as $i => &$a) $a['peringkat'] = $i + 1;
    unset($a);
    return ['alternatif' => $alt, 'kriteria' => $krit, 'matriks' => $X, 'R' => $R];
}
