#!/bin/bash
# rebuild Brands page from scratch
cd /home/user/alkhoory/onora-site
S=/tmp/claude-0/-home-user-alkhoory/9d34961f-08e1-5caa-b0dd-f584185bc5cd/scratchpad
python3 $S/brands_gen.py >/dev/null
python3 tools/chrome.py 27 brands header --build
for f in sections/brands-0*.json; do echo "== $f"; python3 tools/mcp.py elementor-build-composition $f | python3 -c "
import json,sys;r=json.load(sys.stdin);print({k:r.get(k) for k in ('success','root_element_ids','isError','detail','error')}); [print(' W',w) for w in (r.get('warnings') or [])]"; done
python3 tools/chrome.py 27 brands footer --build
