import sys, statistics
from PIL import Image, ImageFilter, ImageDraw
src, out = sys.argv[1], sys.argv[2]
im = Image.open(src).convert('RGB'); px = im.load()
def lum(c): return 0.3*c[0]+0.59*c[1]+0.11*c[2]
RECTS = {'alkah': (70, 243, 337, 259), 'tayyeb': (621, 242, 891, 258), 'elite_urban': (621, 296, 906, 318)}
for name, (x0, y0, x1, y1) in RECTS.items():
    vals = [px[x, y] for x in range(x0, x1+1) for y in range(y0, y1+1)]
    dark = [c for c in vals if lum(c) < 110 and abs(c[0]-c[1]) < 25]
    col = tuple(int(statistics.median(c[i] for c in dark)) for i in range(3))
    import random
    random.seed(7)
    h = y1 - y0
    for y in range(y0, y1+1):
        t = (y - y0) / max(1, h)
        k = 1.10 - 0.22 * t                      # lighter at the top edge, darker at the bottom like a backlit fascia
        if y == y0: k = 1.35                      # thin highlight along the top edge
        for x in range(x0, x1+1):
            n = random.gauss(0, 2.2)
            px[x, y] = tuple(max(0, min(255, int(c * k + n))) for c in col)
    print(name, col)
# soften the fill edges slightly so the panels sit naturally in the JPEG
mask = Image.new('L', im.size, 0); md = ImageDraw.Draw(mask)
for (x0, y0, x1, y1) in RECTS.values(): md.rectangle([x0-1, y0-1, x1+1, y1+1], fill=255)
blur = im.filter(ImageFilter.GaussianBlur(0.8))
edge = mask.filter(ImageFilter.FIND_EDGES).filter(ImageFilter.MaxFilter(3))
im.paste(blur, (0, 0), edge)
im.save(out, quality=92)
