"""Region-growing background removal for studio car shots with a gradient backdrop + floor reflection.
usage: cutout_grow.py src out floor_y [tol]"""
import sys
from collections import deque
from PIL import Image, ImageFilter
src, out, floor_y = sys.argv[1], sys.argv[2], int(sys.argv[3]); tol = int(sys.argv[4]) if len(sys.argv) > 4 else 5
im = Image.open(src).convert('RGB'); w, h = im.size; px = im.load()
bgm = bytearray(w * h)
q = deque()
for x in range(w): q.append((x, 0, px[x, 0]))
for y in range(min(floor_y, h)): q.append((0, y, px[0, y])); q.append((w - 1, y, px[w - 1, y]))
def lowsat(c): return max(c) - min(c) < 18
while q:
    x, y, pc = q.popleft()
    if x < 0 or y < 0 or x >= w or y >= floor_y: continue
    i = y * w + x
    if bgm[i]: continue
    c = px[x, y]
    if not lowsat(c) or max(abs(c[0] - pc[0]), abs(c[1] - pc[1]), abs(c[2] - pc[2])) > tol: continue
    bgm[i] = 1
    for dx, dy in ((1, 0), (-1, 0), (0, 1), (0, -1)): q.append((x + dx, y + dy, c))
mask = Image.new('L', (w, h), 0); mp = mask.load()
for y in range(floor_y):
    for x in range(w):
        if not bgm[y * w + x]: mp[x, y] = 255
mask = mask.filter(ImageFilter.MedianFilter(3)).filter(ImageFilter.GaussianBlur(0.6))
o = im.convert('RGBA'); o.putalpha(mask); o.save(out)
print('done', out)
