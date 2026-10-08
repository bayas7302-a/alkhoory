import re,collections
def parse_rules(html):
    """element-id -> list of (media, suffix, {prop:val})"""
    rules=collections.defaultdict(list)
    def walk(css,media):
        i=0
        while i<len(css):
            b=css.find('{',i)
            if b<0: break
            sel=css[i:b].strip()
            if sel.startswith('@'):
                depth=1;j=b+1
                while depth and j<len(css):
                    if css[j]=='{':depth+=1
                    elif css[j]=='}':depth-=1
                    j+=1
                if sel.startswith('@media'): walk(css[b+1:j-1],re.sub(r'\s','',sel))
                i=j
            else:
                e=css.find('}',b); decl=css[b+1:e]; i=e+1
                d={}
                for p in decl.split(';'):
                    if ':' in p:
                        k,v=p.split(':',1); d[k.strip()]=v.strip()
                for s in sel.split(','):
                    m=re.search(r'\.elementor-element-([0-9a-f]{6,8})((?:[^ ]*)?)(.*)$',s.strip())
                    if m: rules[m.group(1)].append((media,(m.group(2)+m.group(3)).strip(),d))
    for st in re.findall(r'<style[^>]*>(.*?)</style>',html,re.S): walk(st,'')
    return rules
