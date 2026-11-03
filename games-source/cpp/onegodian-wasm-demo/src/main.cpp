#include <emscripten.h>
#include <cmath>
static double phase = 0.0;
static void frame() {
  phase += 0.018;
  EM_ASM({
    const c = document.getElementById('game');
    if (!c) return;
    const ctx = c.getContext('2d');
    ctx.fillStyle = '#071121'; ctx.fillRect(0, 0, c.width, c.height);
    ctx.fillStyle = '#d8b35a'; ctx.beginPath();
    ctx.arc(c.width / 2 + Math.sin($0) * 120, c.height / 2, 25, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#f5f1e8'; ctx.font = '22px sans-serif';
    ctx.fillText('OneGodian C++ / WebAssembly', 30, 42);
  }, phase);
}
int main() { emscripten_set_main_loop(frame, 60, 1); return 0; }
