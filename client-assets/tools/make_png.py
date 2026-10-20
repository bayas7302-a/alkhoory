"""Normalise a car image to a transparent side-view PNG with a soft ground shadow."""
import sys
from PIL import Image, ImageDraw, ImageFilter
from collections import deque
src, out = sys.argv[1], sys.argv[2]
im = Image.open(src).convert('RGBA')
w, h = im.size
a = im.split()[3]
if a.getextrema()[0] == 255:  # opaque: flood-fill the flat background from the edges
    px = im.load(); bg = px[2, 2][:3]; tol = 10
    seen = bytearray(w * h); q = deque()
    for x in range(w): q.extend([(x, 0), (x, h - 1)])
    for y in range(h): q.extend([(0, y), (w - 1, y)])
    mask = Image.new('L', (w, h), 255); mp = mask.load()
    while q:
        x, y = q.popleft()
        if x < 0 or y < 0 or x >= w or y >= h or seen[y * w + x]: continue
        seen[y * w + x] = 1
        r, g, b, _ = px[x, y]
        if abs(r - bg[0]) <= tol and abs(g - bg[1]) <= tol and abs(b - bg[2]) <= tol:
            mp[x, y] = 0
            q.extend([(x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)])
    mask = mask.filter(ImageFilter.MinFilter(3)).filter(ImageFilter.GaussianBlur(0.7))
    im.putalpha(mask)
car = im.crop(im.split()[3].point(lambda v: 255 if v > 40 else 0).getbbox())
cw, ch = car.size
W = 1400; scale = (W * 0.84) / cw
car = car.resize((int(cw * scale), int(ch * scale)), Image.LANCZOS)
cw, ch = car.size
H = int(ch * 1.32)
canvas = Image.new('RGBA', (W, H), (0, 0, 0, 0))
x0 = (W - cw) // 2; y0 = int(H * 0.08)
sh = Image.new('L', (W, H), 0); d = ImageDraw.Draw(sh)
base = y0 + ch
d.ellipse([x0 + cw * 0.04, base - ch * 0.06, x0 + cw * 0.96, base + ch * 0.07], fill=150)
sh = sh.filter(ImageFilter.GaussianBlur(ch * 0.05))
shadow = Image.new('RGBA', (W, H), (10, 14, 22, 0)); shadow.putalpha(sh)
canvas.alpha_composite(shadow)
canvas.alpha_composite(car, (x0, y0))
canvas.save(out, optimize=True)
print(out, canvas.size)
