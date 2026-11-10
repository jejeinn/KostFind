// js/script.js
document.getElementById('navToggle')?.addEventListener('click', () => {
  document.getElementById('navMenu').classList.toggle('open');
});
// Tombol cepat anggaran di halaman cari
document.querySelectorAll('.chips [data-set]').forEach(b => {
  b.addEventListener('click', () => {
    document.getElementById('harga_max').value = b.dataset.set;
  });
});
