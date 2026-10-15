#!/bin/bash
cd /home/user/alkhoory/onora-site
python3 tools/chrome.py 28 products header --build
for f in sections/products-[0-9]*.json; do
  r=$(python3 tools/mcp.py elementor-build-composition $f)
  echo "$f $(echo "$r" | python3 -c 'import json,sys;r=json.load(sys.stdin);print(r.get("success"),r.get("root_element_ids"),json.dumps({k:r.get(k) for k in ("warnings","isError","detail") if k in r})[:800])')"
done
python3 tools/chrome.py 28 products footer --build
