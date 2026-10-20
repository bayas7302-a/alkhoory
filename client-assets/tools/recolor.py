"""recolor.py base.png out_prefix hue_center hue_width name=#hex ... — repaint saturated body paint, keep shading."""
import sys, colorsys
import numpy as np
from PIL import Image
src, prefix, hc, hw = sys.argv[1], sys.argv[2], float(sys.argv[3]), float(sys.argv[4])
im = np.asarray(Image.open(src).convert('RGBA')).astype(np.float32) / 255.0
rgb, a = im[..., :3], im[..., 3]
mx, mn = rgb.max(-1), rgb.min(-1); v = mx; s = np.where(mx > 0, (mx - mn) / np.maximum(mx, 1e-6), 0)
r, g, b = rgb[..., 0], rgb[..., 1], rgb[..., 2]
hue = np.zeros_like(mx); d = np.maximum(mx - mn, 1e-6)
hue = np.where(mx == r, ((g - b) / d) % 6, np.where(mx == g, (b - r) / d + 2, (r - g) / d + 4)) * 60
dh = np.abs(((hue - hc) + 180) % 360 - 180)
paint = (s > 0.22) & (dh < hw) & (a > 0.1)
# soft mask edge by saturation ramp
w = np.clip((s - 0.18) / 0.12, 0, 1) * np.clip((hw - dh) / 10, 0, 1) * paint
vm = np.median(v[paint]); sm = np.median(s[paint])
for spec in sys.argv[5:]:
    name, hexc = spec.split('=')
    t = np.array([int(hexc[i:i + 2], 16) for i in (1, 3, 5)], np.float32) / 255
    th, ts, tv = colorsys.rgb_to_hsv(*t)
    rel = v / vm                                   # shading relative to median paint brightness
    nv = np.clip(tv * rel, 0, 1)
    # highlights brighter than the target can show: push towards white instead of clipping colour
    over = np.clip(tv * rel - 1, 0, 1)
    ns = np.clip(ts * (s / max(sm, 1e-3)) * (1 - over), 0, 1)
    if ts < 0.12: ns = np.clip(s * 0.08, 0, ts + 0.04)   # neutral paints: keep a hint of the original sheen only
    hh = np.full_like(v, th)
    # hsv -> rgb vectorised
    i = np.floor(hh * 6).astype(int) % 6; f = hh * 6 - np.floor(hh * 6)
    p = nv * (1 - ns); q = nv * (1 - f * ns); tt = nv * (1 - (1 - f) * ns)
    choices = [np.stack(x, -1) for x in ((nv, tt, p), (q, nv, p), (p, nv, tt), (p, q, nv), (tt, p, nv), (nv, p, q))]
    new = np.zeros_like(rgb)
    for k in range(6): new = np.where((i == k)[..., None], choices[k], new)
    out = rgb * (1 - w[..., None]) + new * w[..., None]
    o = np.concatenate([out, a[..., None]], -1)
    Image.fromarray((o * 255).astype(np.uint8)).save(f'{prefix}-{name}.png', optimize=True)
    print(prefix, name)
