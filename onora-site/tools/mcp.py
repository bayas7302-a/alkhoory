#!/usr/bin/env python3
"""Tiny JSON-RPC client for the Elementor MCP endpoint on soharon.co.uk/onora.

usage: mcp.py TOOL_NAME ARGS.json|'{"inline":"json"}'
Credentials come from the ONORA_AUTH env var ("user:app-password") or ~/.claude.json.
"""
import base64, json, os, sys, urllib.request

URL = "https://soharon.co.uk/onora/wp-json/elementor/mcp/"
SID_FILE = os.path.join(os.path.expanduser("~"), ".onora_mcp_sid")


def auth_header():
    if os.environ.get("ONORA_AUTH"):
        return "Basic " + base64.b64encode(os.environ["ONORA_AUTH"].encode()).decode()
    cfg = json.load(open(os.path.expanduser("~/.claude.json")))
    return cfg["mcpServers"]["my-blog-elementor"]["headers"]["Authorization"]


def _post(payload, sid=None):
    h = {"Authorization": auth_header(), "Content-Type": "application/json",
         "Accept": "application/json, text/event-stream"}
    if sid:
        h["Mcp-Session-Id"] = sid
    req = urllib.request.Request(URL, data=json.dumps(payload).encode(), headers=h, method="POST")
    with urllib.request.urlopen(req, timeout=300) as r:
        body = r.read().decode()
        return r.headers.get("Mcp-Session-Id"), body


def session():
    if os.path.exists(SID_FILE):
        return open(SID_FILE).read().strip()
    sid, _ = _post({"jsonrpc": "2.0", "id": 1, "method": "initialize", "params": {
        "protocolVersion": "2025-03-26", "capabilities": {}, "clientInfo": {"name": "onora", "version": "1"}}})
    _post({"jsonrpc": "2.0", "method": "notifications/initialized"}, sid)
    open(SID_FILE, "w").write(sid)
    return sid


def call(name, args):
    for attempt in range(2):
        try:
            _, body = _post({"jsonrpc": "2.0", "id": 2, "method": "tools/call",
                             "params": {"name": name, "arguments": args}}, session())
            break
        except urllib.error.HTTPError as e:
            if attempt == 0 and e.code in (400, 404):
                os.path.exists(SID_FILE) and os.remove(SID_FILE)
                continue
            raise
    if body.startswith("event:") or body.startswith("data:"):
        body = [l[5:] for l in body.splitlines() if l.startswith("data:")][-1]
    res = json.loads(body)
    if "error" in res:
        return {"error": res["error"]}
    r = res["result"]
    if r.get("structuredContent") is not None:
        out = r["structuredContent"]
    else:
        txt = r["content"][0]["text"]
        try:
            out = json.loads(txt)
        except ValueError:
            out = txt
    if r.get("isError"):
        return {"isError": True, "detail": out}
    return out


if __name__ == "__main__":
    a = sys.argv[2] if len(sys.argv) > 2 else "{}"
    args = json.load(open(a)) if os.path.exists(a) else json.loads(a)
    print(json.dumps(call(sys.argv[1], args), indent=1, ensure_ascii=False))
