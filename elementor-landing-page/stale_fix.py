# Inline the Elementor local CSS rules that WP Rocket's stale minified copy is missing.
import re, json, subprocess, sys
import os
AUTH="Authorization: Basic "+os.environ["WP_BASIC_AUTH"]  # base64 of "user:application-password"
def sh(c): return subprocess.run(['bash','-c',c],capture_output=True,text=True).stdout
page=sh('curl -sS -A "Mozilla/5.0 Chrome/126" "https://soharon.com/heelee-landing-page/"')
m=re.search(r"href='(https://soharon.com/wp-content/cache/min/[^']*local-24951-frontend-desktop\.css[^']*)'",page)
fresh=sh('curl -sS "https://soharon.com/wp-content/uploads/elementor/css/local-24951-frontend-desktop.css?nc=$RANDOM"')
if not m:
    print('no minified copy served; nothing to patch'); css=''
else:
    stale=sh('curl -sS "%s"'%m.group(1))
    def norm(b):
        b=re.sub(r'\s+','',b).rstrip(';'); b=re.sub(r'(?<![\d.])0px','0',b); return set(b.split(';'))
    def rules(t): return {re.sub(r'\s+',' ',a).strip():b for a,b in re.findall(r'([^{}]+)\{([^{}]*)\}',t)}
    S=rules(stale); F=rules(fresh)
    css=''.join('%s{%s}'%(k,v) for k,v in F.items() if k not in S or norm(S[k])!=norm(v))
    # re-apply tablet/mobile rules after the patch so the breakpoints still win (same specificity, later wins)
    if css:
        for bp in ('tablet','mobile'):
            css+=sh('curl -sS "https://soharon.com/wp-content/uploads/elementor/css/local-24951-frontend-%s.css?nc=$RANDOM"'%bp)
    print('stale min file:',m.group(1).split('?')[1],'patch bytes',len(css))
d=json.loads(json.loads(sh('curl -sS -f -H "%s" "https://soharon.com/wp-json/wp/v2/pages/24951?context=edit&_fields=meta"'%AUTH))['meta']['_elementor_data'])
w=d[0]['elements'][0]; assert w.get('widgetType')=='html'
h=re.sub(r'<style id="hl-stale-css">.*?</style>','',w['settings']['html'],flags=re.S)
if css: h=h.replace('<style id="hl-page-fixes">','<style id="hl-stale-css">'+css+'</style><style id="hl-page-fixes">',1)
w['settings']['html']=h
json.dump({"meta":{"_elementor_data":json.dumps(d)}},open('body_stale.json','w'))
print(sh('curl -sS -f -X POST -H "%s" -H "Content-Type: application/json" "https://soharon.com/wp-json/wp/v2/pages/24951" --data-binary @body_stale.json -o /dev/null -w "stale-css write %%{http_code}"'%AUTH))
