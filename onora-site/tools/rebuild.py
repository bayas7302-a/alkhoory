#!/usr/bin/env python3
"""Rebuild page(s) from sections/<page>-NN-*.json, publish, and use the theme template.

usage: rebuild.py home|about|brands|products|contact [...]
The header and footer come from the ONORA theme (theme/onora), not from Elementor.
"""
import glob, json, os, sys
sys.path.insert(0, os.path.dirname(__file__))
from mcp import call

POSTS = {"home": 25, "about": 26, "brands": 27, "products": 28, "contact": 29}
SEC = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "sections")

for page in sys.argv[1:]:
    pid = POSTS[page]
    for i, f in enumerate(sorted(glob.glob(os.path.join(SEC, f"{page}-[0-9][0-9]-*.json")))):
        p = json.load(open(f))
        p.update(post_id=pid, parent_id="document", mode="replace_children" if i == 0 else "append")
        p.pop("dry_run", None)
        r = call("elementor-build-composition", p)
        print(page, os.path.basename(f), r.get("success"), r.get("root_element_ids"), r.get("warnings") or "", r.get("detail") or r.get("error") or "")
    # publish, then a settings save so Elementor regenerates the page CSS (stale otherwise)
    print(page, "publish", call("elementor-publish-document", {"post_id": pid}).get("status"))
    print(page, "template", call("elementor-update-page-settings", {"post_id": pid, "settings": {"template": "default"}}).get("success"))
