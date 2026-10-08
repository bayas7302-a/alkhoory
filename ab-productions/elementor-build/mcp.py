import json, sys, urllib.request, os

# Credentials are never stored in the repo: export ABP_MCP_AUTH as
# base64("username:application password") before running the scripts.
URL = os.environ.get('ABP_MCP_URL', 'https://soharon.co.uk/ab-production/wp-json/elementor/mcp/')
H = {'Authorization': 'Basic ' + os.environ.get('ABP_MCP_AUTH', ''),
     'Content-Type': 'application/json', 'Accept': 'application/json, text/event-stream'}
SF = os.path.join(os.path.dirname(os.path.abspath(__file__)), '.sid')


def call(method, params=None, i=1):
    h = dict(H)
    if os.path.exists(SF):
        h['Mcp-Session-Id'] = open(SF).read().strip()
    r = urllib.request.Request(URL, json.dumps({"jsonrpc": "2.0", "id": i, "method": method, "params": params or {}}).encode(), h)
    with urllib.request.urlopen(r, timeout=300) as resp:
        sid = resp.headers.get('mcp-session-id')
        if sid:
            open(SF, 'w').write(sid)
        return json.loads(resp.read())


def init():
    if os.path.exists(SF):
        os.remove(SF)
    call('initialize', {"protocolVersion": "2025-03-26", "capabilities": {}, "clientInfo": {"name": "cc", "version": "1"}})


def tool(name, args):
    if not os.path.exists(SF):
        init()
    try:
        return call('tools/call', {"name": name, "arguments": args})
    except urllib.error.HTTPError as e:
        init()
        return call('tools/call', {"name": name, "arguments": args})


def text(name, args):
    r = tool(name, args)
    if 'error' in r:
        return 'ERROR ' + json.dumps(r['error'])
    c = r['result'].get('content', [])
    out = ''.join(x.get('text', '') for x in c)
    if r['result'].get('isError'):
        out = 'ISERROR ' + out
    return out


if __name__ == '__main__':
    if sys.argv[1] == 'list':
        init()
        print(json.dumps(call('tools/list'), indent=1))
    else:
        a = {}
        if len(sys.argv) > 2:
            a = json.load(open(sys.argv[2][1:])) if sys.argv[2].startswith('@') else json.loads(sys.argv[2])
        print(text(sys.argv[1], a))
