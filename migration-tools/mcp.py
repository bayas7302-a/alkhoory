"""Tiny MCP-over-HTTP client. Auth header taken from env WP_AUTH (never stored)."""
import json, os, sys, urllib.request
URL = "https://soharon.co.uk/ais/wp-json/elementor/mcp/"
SESS = os.path.join(os.path.dirname(__file__), ".session")
def _post(body, sid=None):
    h = {"Authorization": os.environ["WP_AUTH"], "Content-Type": "application/json", "Accept": "application/json, text/event-stream"}
    if sid: h["Mcp-Session-Id"] = sid
    req = urllib.request.Request(URL, json.dumps(body).encode(), h)
    with urllib.request.urlopen(req, timeout=300) as r:
        return r.headers.get("Mcp-Session-Id"), r.read().decode()
def session():
    if os.path.exists(SESS): return open(SESS).read().strip()
    sid, _ = _post({"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-06-18","capabilities":{},"clientInfo":{"name":"claude","version":"1"}}})
    _post({"jsonrpc":"2.0","method":"notifications/initialized"}, sid)
    open(SESS,"w").write(sid); return sid
def call(name, args):
    _, txt = _post({"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":name,"arguments":args}}, session())
    d = json.loads(txt)
    if "error" in d: return {"__error": d["error"]}
    c = d["result"]["content"][0]["text"]
    try: return json.loads(c)
    except Exception: return c
if __name__ == "__main__":
    args = json.loads(sys.argv[2]) if len(sys.argv) > 2 and sys.argv[2] else {}
    if len(sys.argv) > 3: args = json.load(open(sys.argv[3]))
    r = call(sys.argv[1], args)
    print(r if isinstance(r, str) else json.dumps(r, indent=1))
