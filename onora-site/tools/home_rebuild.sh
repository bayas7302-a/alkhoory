#!/bin/bash
cd /home/user/alkhoory/onora-site
python3 tools/chrome.py 25 home header --build
for f in sections/home-0*.json; do
  echo -n "$f "; python3 tools/mcp.py elementor-build-composition $f | python3 -c "import json,sys;r=json.load(sys.stdin);print(r.get('success'),r.get('root_element_ids'),r.get('warnings'),r.get('error'))"
done
python3 tools/chrome.py 25 home footer --build
python3 tools/mcp.py elementor-publish-document '{"post_id":25}' | head -c 80; echo
python3 tools/mcp.py elementor-update-page-settings '{"post_id":25,"settings":{"template":"elementor_canvas"}}' | head -c 30; echo
