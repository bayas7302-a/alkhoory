"""Minimal client for the Elementor MCP endpoint (JSON-RPC over HTTP).

Credentials come from the environment, never from this file:
  WP_URL           e.g. https://soharon.co.uk/alkhoory
  WP_USER          WordPress username
  WP_APP_PASSWORD  application password (Users → Profile → Application Passwords)
"""
import base64
import json
import os
import urllib.request

_URL = os.environ.get("WP_URL", "https://soharon.co.uk/alkhoory").rstrip("/") + "/wp-json/elementor/mcp/"
_AUTH = base64.b64encode(f"{os.environ['WP_USER']}:{os.environ['WP_APP_PASSWORD']}".encode()).decode()
_session = None


def _post(payload, session=None):
    headers = {"Authorization": "Basic " + _AUTH, "Content-Type": "application/json", "Accept": "application/json, text/event-stream"}
    if session:
        headers["Mcp-Session-Id"] = session
    req = urllib.request.Request(_URL, data=json.dumps(payload).encode(), headers=headers, method="POST")
    with urllib.request.urlopen(req, timeout=300) as resp:
        body = resp.read().decode()
        return resp.headers.get("Mcp-Session-Id"), (json.loads(body) if body else None)


def _ensure_session():
    global _session
    if _session is None:
        _session, _ = _post({"jsonrpc": "2.0", "id": 1, "method": "initialize", "params": {"protocolVersion": "2025-06-18", "capabilities": {}, "clientInfo": {"name": "alkhoory-build", "version": "1"}}})
        _post({"jsonrpc": "2.0", "method": "notifications/initialized"}, _session)
    return _session


def call(name, args):
    """Call an Elementor MCP tool and return its text result."""
    _, d = _post({"jsonrpc": "2.0", "id": 2, "method": "tools/call", "params": {"name": name, "arguments": args}}, _ensure_session())
    if "result" in d:
        return d["result"].get("content", [{}])[0].get("text", "")
    return json.dumps(d)
