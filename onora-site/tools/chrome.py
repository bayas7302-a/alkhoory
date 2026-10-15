#!/usr/bin/env python3
"""Generate the shared ONORA header + footer build-composition payloads.

usage: chrome.py POST_ID ACTIVE PART [--build]
  ACTIVE = home|about|brands|products|contact   PART = header|footer
Writes sections/<ACTIVE>-<PART>.json; with --build also posts it.
`header --build` REPLACES the whole page (mode replace_children on the document) so run it first;
then append the page sections in order, then run `footer --build` last.
"""
import json, os, sys, subprocess

BASE = "https://soharon.co.uk/onora/"
NAV = [("home", "Home", BASE), ("about", "About Us", BASE + "about-us/"), ("brands", "Brands", BASE + "brands/"),
       ("products", "Products", BASE + "products/"), ("contact", "Contact Us", BASE + "contact-us/")]
LOGO_NAVY, LOGO_WHITE = 35, 36
GOLD = "linear-gradient(90deg, #A5740B 0%, #DEAF57 25%, #F1C96A 50%, #BE8826 75%, #A5740B 100%)"
HERE = os.path.dirname(os.path.abspath(__file__))
COLLECTIONS = ["Chinaware", "Cutlery", "Glassware", "Buffet", "Bar Items", "Table Lamps"]


def link(url, blank=False):
    return {"destination": url, "isTargetBlank": blank, "tag": "a"}


def header(post_id, active):
    x, cfg, st, cl = [], {}, {}, {}
    x.append('<e-flexbox configuration-id="Header">')
    st["Header"] = "flex-direction: column; padding: 0px; gap: 0px; position: sticky; top: 0px; z-index: 50; background: #FFFFFF;"
    # utility bar
    x.append('<e-flexbox configuration-id="Utility bar"><e-flexbox configuration-id="Utility contacts">')
    st["Utility bar"] = ("background: var(--on-navy-deep); padding: 11px 100px; justify-content: space-between; align-items: center; gap: 24px;"
                         " @media(--tablet){ padding: 10px 40px; } @media(--mobile){ padding: 9px 20px; justify-content: center; }")
    st["Utility contacts"] = "padding: 0px; width: auto; gap: 32px; @media(--mobile){ gap: 16px; }"
    util = [("Util phone", "+971 50 203 8005", "tel:+971502038005"), ("Util email", "jason@onorahs.com", "mailto:jason@onorahs.com"),
            ("Util location", "Karama, Dubai — UAE", None)]
    for cid, txt, url in util:
        x.append(f'<e-paragraph configuration-id="{cid}"></e-paragraph>')
        cfg[cid] = {"paragraph": txt, **({"link": link(url)} if url else {})}
        st[cid] = "font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 0.3px; color: rgba(255,255,255,0.78);"
    st["Util location"] += " @media(--mobile){ display: none; }"
    x.append('</e-flexbox><e-paragraph configuration-id="Util tagline"></e-paragraph></e-flexbox>')
    cfg["Util tagline"] = {"paragraph": "Premium supplies for hotels, restaurants &amp; cafés"}
    st["Util tagline"] = ("font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 0.4px; color: var(--on-gold-soft);"
                          " @media(--mobile){ display: none; }")
    # main nav
    x.append('<e-flexbox configuration-id="Navigation"><e-image configuration-id="Logo"></e-image><e-flexbox configuration-id="Menu">')
    st["Navigation"] = ("min-height: 96px; padding: 12px 100px; justify-content: space-between; align-items: center; gap: 24px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #E6E0D6; background: #FFFFFF;"
                        " @media(--tablet){ padding: 14px 40px; flex-wrap: wrap; } @media(--mobile){ padding: 14px 20px; flex-wrap: wrap; }")
    cfg["Logo"] = {"image": {"src": {"id": LOGO_NAVY}, "size": "full"}, "link": link(BASE)}
    st["Logo"] = "width: 172px; height: auto; @media(--mobile){ width: 140px; }"
    st["Menu"] = ("padding: 0px; width: auto; gap: 44px; align-items: flex-start;"
                  " @media(--tablet){ order: 3; width: 100%; justify-content: center; gap: 28px; } @media(--mobile){ order: 3; width: 100%; justify-content: space-between; gap: 6px; }")
    for key, label, url in NAV:
        on = key == active
        cid = f"Nav {label}"
        x.append(f'<e-flexbox configuration-id="{cid}"><e-paragraph configuration-id="{cid} label"></e-paragraph><e-div-block configuration-id="{cid} bar"></e-div-block></e-flexbox>')
        st[cid] = "flex-direction: column; align-items: center; gap: 8px; padding: 0px; width: auto;"
        cfg[cid] = {"link": link(url)}
        cfg[f"{cid} label"] = {"paragraph": label}
        st[f"{cid} label"] = (f"font-family: var(--on-sans); font-weight: {600 if on else 500}; font-size: 13px; letter-spacing: 1.6px; text-transform: uppercase;"
                              f" color: {'#0B2F62' if on else '#16213A'}; transition: color 0.3s; &:hover{{ color: #C9962E; }}"
                              " @media(--mobile){ font-size: 10px; letter-spacing: 0.6px; }")
        st[f"{cid} bar"] = f"width: 22px; min-width: 22px; height: 2px; padding: 0px; background: {GOLD if on else 'transparent'};"
    x.append('</e-flexbox><e-button configuration-id="Header CTA"></e-button></e-flexbox></e-flexbox>')
    cfg["Header CTA"] = {"text": "Request a quote&nbsp;&nbsp;→", "link": link(BASE + "contact-us/")}
    cl["Header CTA"] = ["on-btn-navy"]
    st["Header CTA"] = "@media(--mobile){ padding: 12px 16px; font-size: 10px; letter-spacing: 1.5px; }"
    return {"post_id": post_id, "xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


def footer(post_id):
    x, cfg, st, cl = [], {}, {}, {}
    p_style = "font-family: var(--on-sans); font-weight: 400; font-size: 14px; line-height: 22px; color: rgba(255,255,255,0.75); transition: color 0.3s; &:hover{ color: #E2BB6C; }"
    x.append('<e-flexbox configuration-id="Footer"><e-flexbox configuration-id="Footer top"><e-flexbox configuration-id="Footer brand">'
             '<e-image configuration-id="Footer logo"></e-image><e-paragraph configuration-id="Footer about"></e-paragraph>'
             '<e-flexbox configuration-id="Newsletter"><e-paragraph configuration-id="Newsletter input"></e-paragraph><e-button configuration-id="Newsletter subscribe"></e-button></e-flexbox>'
             '<e-paragraph configuration-id="Newsletter note"></e-paragraph></e-flexbox><e-flexbox configuration-id="Footer columns">')
    st["Footer"] = ("flex-direction: column; gap: 72px; padding: 96px 100px 36px 100px; background: var(--on-navy-deep);"
                    " @media(--tablet){ padding: 72px 40px 32px 40px; gap: 56px; } @media(--mobile){ padding: 56px 20px 28px 20px; gap: 44px; }")
    st["Footer top"] = "padding: 0px; justify-content: space-between; align-items: flex-start; gap: 48px; @media(--tablet){ flex-direction: column; } @media(--mobile){ flex-direction: column; }"
    st["Footer brand"] = "flex-direction: column; gap: 26px; padding: 0px; width: 360px; max-width: 100%; align-items: flex-start;"
    cfg["Footer logo"] = {"image": {"src": {"id": LOGO_WHITE}, "size": "full"}, "link": link(BASE)}
    st["Footer logo"] = "width: 238px; height: auto;"
    cfg["Footer about"] = {"paragraph": "ONORA Hotel Supplies brings world-class tableware, buffet, bar and service equipment to hotels, restaurants and cafés across the UAE."}
    st["Footer about"] = "font-family: var(--on-sans); font-weight: 400; font-size: 14px; line-height: 24px; color: rgba(255,255,255,0.68);"
    st["Newsletter"] = "padding: 0px; gap: 0px; align-items: stretch; width: 100%;"
    cfg["Newsletter input"] = {"paragraph": "Your email address", "link": link("mailto:jason@onorahs.com?subject=Newsletter%20signup")}
    st["Newsletter input"] = "flex-grow: 1; padding: 16px 18px; border: 1px solid rgba(255,255,255,0.25); font-family: var(--on-sans); font-size: 13px; color: rgba(255,255,255,0.5); line-height: 1.2;"
    cfg["Newsletter subscribe"] = {"text": "Subscribe", "link": link("mailto:jason@onorahs.com?subject=Newsletter%20signup")}
    st["Newsletter subscribe"] = f"background: {GOLD}; color: #071F44; font-family: var(--on-sans); font-weight: 600; font-size: 11px; letter-spacing: 1.8px; text-transform: uppercase; padding: 16px 22px; border-radius: 0px; line-height: 1.2;"
    cfg["Newsletter note"] = {"paragraph": "New collections and insider offers — straight to your inbox."}
    st["Newsletter note"] = "font-family: var(--on-sans); font-size: 12px; color: rgba(255,255,255,0.5);"
    st["Footer columns"] = "padding: 0px; width: auto; gap: 64px; align-items: flex-start; @media(--tablet){ gap: 48px; } @media(--mobile){ flex-wrap: wrap; gap: 36px; }"
    cols = [("Explore", [(l, u) for _, l, u in NAV], "180px"),
            ("Collections", [(c, BASE + "products/") for c in COLLECTIONS], "180px"),
            ("Get in touch", [("+971 50 203 8005", "tel:+971502038005"), ("jason@onorahs.com", "mailto:jason@onorahs.com"),
                              ("onorahs.com", "https://onorahs.com"), ("Office M-A98, Hamra Building, Karama, Dubai — UAE", None)], "240px")]
    for title, items, w in cols:
        c = f"Col {title}"
        x.append(f'<e-flexbox configuration-id="{c}"><e-paragraph configuration-id="{c} title"></e-paragraph>')
        st[c] = f"flex-direction: column; gap: 16px; padding: 0px; width: {w}; max-width: 100%;"
        cfg[f"{c} title"] = {"paragraph": title}
        st[f"{c} title"] = "font-family: var(--on-sans); font-weight: 600; font-size: 12px; letter-spacing: 2.5px; text-transform: uppercase; color: var(--on-gold-soft); margin-bottom: 4px;"
        for i, (label, url) in enumerate(items):
            cid = f"{c} {i+1}"
            x.append(f'<e-paragraph configuration-id="{cid}"></e-paragraph>')
            cfg[cid] = {"paragraph": label, **({"link": link(url)} if url else {})}
            st[cid] = p_style
        x.append('</e-flexbox>')
    x.append('</e-flexbox></e-flexbox><e-flexbox configuration-id="Footer bottom"><e-paragraph configuration-id="Copyright"></e-paragraph>'
             '<e-flexbox configuration-id="Socials">')
    st["Footer bottom"] = ("padding: 28px 0px 0px 0px; border-width: 1px 0px 0px 0px; border-style: solid; border-color: rgba(255,255,255,0.12); justify-content: space-between; align-items: center; gap: 20px;"
                           " @media(--mobile){ flex-direction: column; align-items: flex-start; }")
    cfg["Copyright"] = {"paragraph": "© 2026 ONORA Hotel Supplies. All rights reserved."}
    st["Copyright"] = "font-family: var(--on-sans); font-size: 12px; color: rgba(255,255,255,0.55);"
    st["Socials"] = "padding: 0px; width: auto; gap: 28px; align-items: center;"
    sm = "font-family: var(--on-sans); font-weight: 500; font-size: 12px; letter-spacing: 0.5px; color: rgba(255,255,255,0.7); &:hover{ color: #E2BB6C; }"
    for i, (n, u) in enumerate([("Instagram", "https://www.instagram.com/"), ("LinkedIn", "https://www.linkedin.com/"), ("Facebook", "https://www.facebook.com/")]):
        if i:
            x.append(f'<e-paragraph configuration-id="Social dot {i}"></e-paragraph>')
            cfg[f"Social dot {i}"] = {"paragraph": "·"}
            st[f"Social dot {i}"] = "font-family: var(--on-sans); font-size: 12px; color: rgba(255,255,255,0.7);"
        x.append(f'<e-paragraph configuration-id="Social {n}"></e-paragraph>')
        cfg[f"Social {n}"] = {"paragraph": n, "link": link(u, True)}
        st[f"Social {n}"] = sm
    x.append('</e-flexbox><e-flexbox configuration-id="Legal"><e-paragraph configuration-id="Privacy"></e-paragraph><e-paragraph configuration-id="Legal dot"></e-paragraph><e-paragraph configuration-id="Terms"></e-paragraph></e-flexbox></e-flexbox></e-flexbox>')
    st["Legal"] = "padding: 0px; width: auto; gap: 12px; align-items: center;"
    lg = "font-family: var(--on-sans); font-size: 12px; color: rgba(255,255,255,0.55); &:hover{ color: #E2BB6C; }"
    cfg["Privacy"] = {"paragraph": "Privacy Policy", "link": link(BASE + "privacy-policy/")}; st["Privacy"] = lg
    cfg["Legal dot"] = {"paragraph": "·"}; st["Legal dot"] = lg
    cfg["Terms"] = {"paragraph": "Terms of Use", "link": link(BASE + "contact-us/")}; st["Terms"] = lg
    return {"post_id": post_id, "xml_structure": "".join(x), "element_config": cfg, "style": st, "classes": cl}


if __name__ == "__main__":
    pid, active, which = int(sys.argv[1]), sys.argv[2], sys.argv[3]
    out = os.path.join(HERE, "..", "sections")
    payload = header(pid, active) if which == "header" else footer(pid)
    if which == "header":
        payload.update(parent_id="document", mode="replace_children")
    for part, payload in ((which, payload),):
        f = os.path.join(out, f"{active}-{part}.json")
        json.dump(payload, open(f, "w"), indent=1, ensure_ascii=False)
        if "--build" in sys.argv:
            r = json.loads(subprocess.run([os.path.join(HERE, "mcp.py"), "elementor-build-composition", f], capture_output=True, text=True).stdout)
            print(part, json.dumps({k: r.get(k) for k in ("success", "root_element_ids", "warnings", "isError", "detail", "error") if k in r})[:1500])
