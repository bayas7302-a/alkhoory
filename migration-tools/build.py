"""Usage: build.py <page-name> <source-page-id> <target-post-id>  (converts and pushes via MCP)"""
import json, sys, os
sys.path.insert(0, os.path.dirname(__file__))
import convert
from convert import convert_page, svg_hash
from mcp import call
PAGES = "home:24 about-us:99 community:271 contact-us:357 locations:209 offshore-virtual-staff:1338 recruitment-and-migration:1155 solutions:172 strategic-labour-hire:1311".split()
for p in PAGES:
    for sv in json.load(open(f'computed/{p.split(":")[0]}.json'))['svgs']:
        if sv['w']:
            convert.SVG_SIZES.setdefault(svg_hash(sv['html']), (sv['w'], sv['h']))
name, src, target = sys.argv[1], sys.argv[2], int(sys.argv[3])
secs, ctx = convert_page(name, src, '.')
print('missing svgs:', set(ctx.missing_svgs))
json.dump(secs, open(f'out/{name}.json', 'w'), indent=1)
for i, s in enumerate(secs):
    args = {'post_id': target, 'parent_id': 'document', 'mode': 'replace_children' if i == 0 else 'append', **s}
    r = call('elementor-build-composition', args)
    ok = isinstance(r, dict) and r.get('success')
    print(i, 'ok' if ok else 'FAIL', (r.get('warnings') if isinstance(r, dict) else r) if not ok or (isinstance(r, dict) and r.get('warnings')) else '')
    if not ok:
        print(json.dumps(r)[:2000]); sys.exit(1)
