#!/usr/bin/env python3
"""Download Figma MCP asset URLs and upload them to the WordPress media library.

usage: assets.py name=URL [name=URL ...]
Prints JSON {name: {id, url, w, h}}. SVGs are rasterised to 3x PNG (WP blocks SVG uploads).
Uploads are de-duplicated by content hash via assets/map.json, so repeated icons upload once.
"""
import sys, os, json, hashlib, subprocess, base64, io, fcntl
import urllib.request
from PIL import Image

ROOT = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
ADIR = os.path.join(ROOT, 'assets')
MAP = os.path.join(ADIR, 'map.json')
API = 'https://soharon.co.uk/yutong/wp-json/wp/v2/media'
# credential comes from the my-blog-elementor MCP entry, never from the repo
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
os.makedirs(ADIR, exist_ok=True)

def fetch(url):
    out = subprocess.run(['curl', '-sSL', '-m', '120', url], capture_output=True, check=True).stdout
    if not out:
        raise RuntimeError('empty download: ' + url)
    return out

def upload(data, fname, mime):
    out = subprocess.run(['curl', '-sS', '-m', '180', '-H', 'Authorization: ' + AUTH,
                          '-H', 'Content-Type: ' + mime,
                          '-H', 'Content-Disposition: attachment; filename="%s"' % fname,
                          '--data-binary', '@-', API], input=data, capture_output=True, check=True).stdout
    j = json.loads(out)
    if not j.get('id'):
        raise RuntimeError('upload failed: %s' % out[:300])
    return j

def main():
    res = {}
    lock = open(MAP + '.lock', 'w')
    fcntl.flock(lock, fcntl.LOCK_EX)
    m = json.load(open(MAP)) if os.path.exists(MAP) else {}
    for arg in sys.argv[1:]:
        name, url = arg.split('=', 1)
        data = fetch(url)
        is_svg = data.lstrip()[:5] in (b'<svg ', b'<?xml') or url.endswith('.svg')
        if is_svg:
            import cairosvg
            data = cairosvg.svg2png(bytestring=data, scale=3)
        if not is_svg and len(data) > 150_000:
            im = Image.open(io.BytesIO(data))
            if im.mode in ('RGBA', 'LA', 'P'):
                a = im.convert('RGBA').getchannel('A')
                opaque = a.getextrema()[0] == 255
            else:
                opaque = True
            if opaque:
                buf = io.BytesIO()
                im.convert('RGB').save(buf, 'JPEG', quality=85, optimize=True, progressive=True)
                data = buf.getvalue()
        h = hashlib.sha1(data).hexdigest()
        if h not in m:
            im = Image.open(io.BytesIO(data))
            fmt = (im.format or 'PNG').lower()
            ext = {'jpeg': 'jpg'}.get(fmt, fmt)
            w, hgt = im.size
            if is_svg:
                w, hgt = round(w / 3), round(hgt / 3)
            fname = 'yt-%s.%s' % (name, ext)
            open(os.path.join(ADIR, fname), 'wb').write(data)
            j = upload(data, fname, 'image/' + ('jpeg' if ext == 'jpg' else ext))
            m[h] = {'id': j['id'], 'url': j['source_url'], 'w': w, 'h': hgt, 'name': name}
            json.dump(m, open(MAP, 'w'), indent=1)
        names = m[h].setdefault('names', [m[h]['name']])
        if name not in names:
            names.append(name)
            json.dump(m, open(MAP, 'w'), indent=1)
        res[name] = m[h]
    print(json.dumps(res, indent=1))

main()
