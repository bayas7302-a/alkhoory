#!/bin/bash
cd /home/user/alkhoory/onora-site
python3 tools/chrome.py 29 contact header --build
for f in sections/contact-0*.json; do echo "== $f"; python3 tools/mcp.py elementor-build-composition $f | python3 -c "import json,sys;r=json.load(sys.stdin);print({k:r.get(k) for k in ('success','root_element_ids','warnings','detail')})"; done
python3 tools/chrome.py 29 contact footer --build
