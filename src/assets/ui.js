
(function(){
  const root = document.documentElement;
  const key = 'ams-theme';
  function apply(t){ if(!t){ root.removeAttribute('data-theme'); return; } root.setAttribute('data-theme', t); }
  const saved = localStorage.getItem(key) || ''; if(saved) apply(saved);
  window.toggleTheme = function(){
    const next = root.getAttribute('data-theme') === 'dark' ? '' : 'dark';
    apply(next); localStorage.setItem(key, next);
    const el = document.getElementById('themeToggle');
    if(el){ el.querySelector('.label').textContent = next==='dark' ? 'Dark' : 'Light'; el.querySelector('.ico').textContent = next==='dark' ? '🌙' : '☀️'; }
  };
  document.addEventListener('DOMContentLoaded', ()=>{
    const el = document.getElementById('themeToggle'); if(!el) return;
    const dark = root.getAttribute('data-theme') === 'dark';
    el.querySelector('.label').textContent = dark ? 'Dark' : 'Light';
    el.querySelector('.ico').textContent = dark ? '🌙' : '☀️';
  });
})();
