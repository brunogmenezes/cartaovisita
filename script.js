/* =============================================
   DRA. BARBARA FERNANDES – JAVASCRIPT
   Features: WhatsApp lead modal, click tracking,
             ripple effect, scroll animations
   ============================================= */

const BASE = typeof APP_BASE !== 'undefined' ? APP_BASE : '/drabarbarafernandes';

// ---- Ripple effect ----
document.querySelectorAll('.cta-button, .action-card, .featured-card').forEach(el => {
  el.addEventListener('click', function (e) {
    const ripple = document.createElement('span');
    const rect   = this.getBoundingClientRect();
    const size   = Math.max(rect.width, rect.height) * 2;
    ripple.className = 'ripple';
    ripple.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size/2}px;top:${e.clientY - rect.top - size/2}px`;
    this.appendChild(ripple);
    ripple.addEventListener('animationend', () => ripple.remove());
  });
});

// ---- Track click (for non-whatsapp buttons) ----
function trackClick(eventType) {
  navigator.sendBeacon(BASE + '/api/track.php', JSON.stringify({ type: eventType }));
}

// ---- Modal WhatsApp ----
const overlay    = document.getElementById('modal-overlay');
const closeBtn   = document.getElementById('modal-close');
const form       = document.getElementById('lead-form');
const nameInput  = document.getElementById('lead-name');
const phoneInput = document.getElementById('lead-phone');
const submitBtn  = document.getElementById('modal-submit-btn');
const errName    = document.getElementById('err-name');
const errPhone   = document.getElementById('err-phone');
const submitText = document.getElementById('submit-text');
const submitLoad = document.getElementById('submit-loading');
const waBtn      = document.getElementById('btn-whatsapp');

function openModal() {
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
  setTimeout(() => nameInput?.focus(), 400);
}

function closeModal() {
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

if (waBtn) {
  waBtn.addEventListener('click', openModal);
}

if (closeBtn) {
  closeBtn.addEventListener('click', closeModal);
}

if (overlay) {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModal();
});

// Phone mask
if (phoneInput) {
  phoneInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 6) v = `(${v.slice(0,2)}) ${v.slice(2,7)}-${v.slice(7)}`;
    else if (v.length > 2) v = `(${v.slice(0,2)}) ${v.slice(2)}`;
    else if (v.length > 0) v = `(${v}`;
    this.value = v;
  });
}

function validateForm() {
  let valid = true;

  const nameVal = nameInput?.value.trim() ?? '';
  if (nameVal.length < 2) {
    errName.textContent = 'Por favor, informe seu nome completo.';
    nameInput.classList.add('invalid');
    valid = false;
  } else {
    errName.textContent = '';
    nameInput.classList.remove('invalid');
  }

  const phoneVal = phoneInput?.value.replace(/\D/g, '') ?? '';
  if (phoneVal.length < 8) {
    errPhone.textContent = 'Por favor, informe um telefone válido.';
    phoneInput.classList.add('invalid');
    valid = false;
  } else {
    errPhone.textContent = '';
    phoneInput.classList.remove('invalid');
  }

  return valid;
}

if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validateForm()) return;

    // Loading state
    submitBtn.disabled  = true;
    submitText.style.display = 'none';
    submitLoad.style.display = 'inline';

    try {
      const res  = await fetch(BASE + '/api/save-lead.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({
          name:  nameInput.value.trim(),
          phone: phoneInput.value.trim(),
        }),
      });

      const data = await res.json();

      if (data.ok && data.whatsapp_url) {
        closeModal();
        form.reset();
        // Small delay so modal closes before opening WhatsApp
        setTimeout(() => { window.open(data.whatsapp_url, '_blank'); }, 250);
      } else {
        showToast('❌ ' + (data.error || 'Erro ao processar. Tente novamente.'));
      }
    } catch (err) {
      showToast('❌ Erro de conexão. Tente novamente.');
    } finally {
      submitBtn.disabled  = false;
      submitText.style.display = 'inline';
      submitLoad.style.display = 'none';
    }
  });
}

// ---- Toast ----
function showToast(message) {
  const existing = document.getElementById('toast-notif');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'toast-notif';
  toast.textContent = message;
  Object.assign(toast.style, {
    position:      'fixed',
    bottom:        '32px',
    left:          '50%',
    transform:     'translateX(-50%) translateY(20px)',
    background:    'linear-gradient(135deg, #2d2235, #4a3860)',
    color:         '#fff',
    padding:       '13px 26px',
    borderRadius:  '100px',
    fontFamily:    "'Nunito', sans-serif",
    fontWeight:    '700',
    fontSize:      '0.88rem',
    boxShadow:     '0 8px 30px rgba(0,0,0,0.22)',
    zIndex:        '9999',
    opacity:       '0',
    transition:    'all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
    whiteSpace:    'nowrap',
    pointerEvents: 'none',
  });
  document.body.appendChild(toast);
  requestAnimationFrame(() => requestAnimationFrame(() => {
    toast.style.opacity   = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
  }));
  setTimeout(() => {
    toast.style.opacity   = '0';
    toast.style.transform = 'translateX(-50%) translateY(20px)';
    setTimeout(() => toast.remove(), 350);
  }, 3200);
}

// ---- Intersection observer: stagger scroll animations ----
const io = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity   = '1';
      entry.target.style.transform = 'translateY(0)';
      io.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.section-block').forEach((el, i) => {
  el.style.opacity   = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = `opacity 0.5s ease ${i * 0.12}s, transform 0.5s cubic-bezier(0.34,1.56,0.64,1) ${i * 0.12}s`;
  io.observe(el);
});

// ---- Chip pulse on click ----
document.querySelectorAll('.chip').forEach(chip => {
  chip.addEventListener('click', function () {
    this.style.transform = 'scale(0.92)';
    setTimeout(() => { this.style.transform = ''; }, 150);
  });
});
