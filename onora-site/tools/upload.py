#!/usr/bin/env python3
"""Upload local image files to the ONORA WordPress media library.

usage: upload.py FILE [FILE...]   -> prints JSON lines {file, id, url, width, height}
Re-uploading a file whose name is already in tools/media.json returns the cached entry.
"""
import json, mimetypes, os, sys, urllib.request, fcntl
sys.path.insert(0, os.path.dirname(__file__))
from mcp import auth_header

API = "https://soharon.co.uk/onora/wp-json/wp/v2/media"
CACHE = os.path.join(os.path.dirname(__file__), "media.json")


def load():
    return json.load(open(CACHE)) if os.path.exists(CACHE) else {}


def upload(path):
    name = os.path.basename(path)
    with open(CACHE + ".lock", "w") as lk:
        fcntl.flock(lk, fcntl.LOCK_EX)
        cache = load()
        if name in cache:
            return cache[name]
        mime = mimetypes.guess_type(name)[0] or "application/octet-stream"
        req = urllib.request.Request(API, data=open(path, "rb").read(), method="POST", headers={
            "Authorization": auth_header(), "Content-Type": mime,
            "Content-Disposition": f'attachment; filename="{name}"'})
        d = json.load(urllib.request.urlopen(req, timeout=300))
        md = d.get("media_details") or {}
        entry = {"id": d["id"], "url": d["source_url"], "width": md.get("width"), "height": md.get("height")}
        cache = load(); cache[name] = entry
        json.dump(cache, open(CACHE, "w"), indent=1, sort_keys=True)
        return entry


if __name__ == "__main__":
    for f in sys.argv[1:]:
        print(json.dumps({"file": f, **upload(f)}))
