#!/usr/bin/env python3
"""Send an elementor-build-composition payload file to the WP MCP server.

usage: send.py payload.json [--dry]
Payload = build-composition arguments (post_id, xml_structure, element_config, style, ...).
Prints warnings/errors and root ids; full response saved next to the payload as *.resp.json.
"""
import sys, json, subprocess, os
ROOT = os.environ.get('YT_WORK', '/tmp/claude-0/-home-user-alkhoory/69817a7b-90b7-525f-a45d-003b87e8786d/scratchpad')
p = sys.argv[1]
args = json.load(open(p))
if '--dry' in sys.argv:
    args['dry_run'] = True
body = json.dumps({'jsonrpc': '2.0', 'id': 1, 'method': 'tools/call',
                   'params': {'name': sys.argv[3] if len(sys.argv) > 3 and sys.argv[2] == '--tool' else 'elementor-build-composition', 'arguments': args}})
out = subprocess.run([os.path.join(ROOT, 'wpmcp.sh'), body], capture_output=True, text=True).stdout
try:
    j = json.loads(out)
except Exception:
    print('RAW:', out[:3000]); sys.exit(1)
r = j.get('result', {})
txt = r.get('content', [{}])[0].get('text', '')
if r.get('isError') or 'error' in j:
    print('ERROR:', txt or j.get('error')); sys.exit(1)
try:
    t = json.loads(txt)
except Exception:
    print(txt[:3000]); sys.exit(0)
open(p.replace('.json', '.resp.json'), 'w').write(json.dumps(t, indent=1))
print(json.dumps({k: t[k] for k in t if k in ('warnings', 'root_element_ids', 'errors', 'status', 'message', 'removed_element_ids')}, indent=1)[:4000])
