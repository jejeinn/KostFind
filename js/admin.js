// js/admin.js
document.querySelectorAll('form[data-confirm]').forEach(f =>
  f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));
const inputs = document.querySelectorAll('input.bobot');
if (inputs.length) {
  const out = document.getElementById('totalBobot'), btn = document.getElementById('btnBobot');
  const hitung = () => {
    const t = [...inputs].reduce((s, i) => s + (parseFloat(i.value) || 0), 0);
    out.textContent = t.toFixed(2);
    const ok = Math.abs(t - 100) < 0.005;
    out.style.color = ok ? '#15803d' : '#b91c1c'; btn.disabled = !ok;
  };
  inputs.forEach(i => i.addEventListener('input', hitung)); hitung();
}
