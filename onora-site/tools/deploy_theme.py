#!/usr/bin/env python3
"""Deploy theme/onora to the site and activate it.

WordPress has no REST endpoint for uploading themes, so this pushes a one-off snippet through the
Code Snippets plugin (must be installed + active), triggers it with one request, then deletes the snippet.
usage: deploy_theme.py [--no-activate]
"""
import base64, hashlib, json, os, sys, urllib.request
sys.path.insert(0, os.path.dirname(__file__))
from mcp import auth_header

SITE = "https://soharon.co.uk/onora/"
SRC = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "theme", "onora")


def req(method, path, body=None):
    r = urllib.request.Request(SITE + "wp-json/" + path, method=method,
                               data=json.dumps(body).encode() if body is not None else None,
                               headers={"Authorization": auth_header(), "Content-Type": "application/json"})
    return json.load(urllib.request.urlopen(r, timeout=120))


files = {}
for dp, _, fns in os.walk(SRC):
    for fn in fns:
        p = os.path.join(dp, fn)
        files[os.path.relpath(p, SRC)] = base64.b64encode(open(p, "rb").read()).decode()
digest = hashlib.sha1(json.dumps(files, sort_keys=True).encode()).hexdigest()
activate = "--no-activate" not in sys.argv

code = f"""
add_action( 'init', function () {{
	if ( get_option( 'onora_theme_deploy' ) === '{digest}' ) {{ return; }}
	$dir = WP_CONTENT_DIR . '/themes/onora';
	$files = json_decode( base64_decode( '{base64.b64encode(json.dumps(files).encode()).decode()}' ), true );
	foreach ( $files as $rel => $b64 ) {{
		$path = $dir . '/' . $rel;
		wp_mkdir_p( dirname( $path ) );
		file_put_contents( $path, base64_decode( $b64 ) );
	}}
	{"if ( get_stylesheet() !== 'onora' ) { switch_theme( 'onora' ); }" if activate else ''}
	update_option( 'onora_theme_deploy', '{digest}', false );
}}, 1 );
"""
snip = req("POST", "code-snippets/v1/snippets", {"name": "ONORA theme deploy (temporary)", "code": code, "scope": "global", "active": True})
sid = snip.get("id")
print("snippet", sid, snip.get("active"), snip.get("code_error"))
try:
    urllib.request.urlopen(urllib.request.Request(SITE + "?onora-deploy=" + digest[:8], headers={"Authorization": auth_header()}), timeout=120).read()
    themes = req("GET", "wp/v2/themes?status=active")
    print("active theme:", [t["stylesheet"] for t in themes])
finally:
    req("DELETE", f"code-snippets/v1/snippets/{sid}")
    print("snippet deleted")
